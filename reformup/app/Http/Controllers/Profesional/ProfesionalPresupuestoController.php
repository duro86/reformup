<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Presupuesto;
use App\Models\Solicitud;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Http\Controllers\Traits\FiltroRangoFechas;


class ProfesionalPresupuestoController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de presupuestos del profesional logueado.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()
                ->back()
                ->with('error', 'No tienes permisos para esta sección');
        }

        $estado = $request->query('estado');             // enviado, aceptado, rechazado, caducado, null
        $q      = trim((string) $request->query('q'));   // texto buscador

        // Estados desde el modelo (sin "Todos")
        $estados = Presupuesto::ESTADOS;

        $query = Presupuesto::with(['solicitud.cliente'])
            ->where('pro_id', $perfil->id);

        // Filtro por estado SOLO si es válido
        if ($estado !== null && $estado !== '') {
            if (array_key_exists($estado, Presupuesto::ESTADOS)) {
                $query->where('estado', $estado);
            }
        }

        // Buscador
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                $sub
                    // Por solicitud (título, ciudad, provincia)
                    ->whereHas('solicitud', function ($q2) use ($like) {
                        $q2->where('titulo', 'like', $like)
                            ->orWhere('ciudad', 'like', $like)
                            ->orWhere('provincia', 'like', $like);
                    })
                    // Por cliente
                    ->orWhereHas('solicitud.cliente', function ($q3) use ($like) {
                        $q3->where('nombre', 'like', $like)
                            ->orWhere('apellidos', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })
                    // Por estado
                    ->orWhere('estado', 'like', $like)
                    // Por total (cast a texto)
                    ->orWhereRaw('CAST(total AS CHAR) LIKE ?', [$like]);
            });
        }

        // Filtro por rango de fechas: usamos 'fecha'
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

        $presupuestos = $query
            ->orderByDesc('fecha')   // o created_at 
            ->paginate(6)
            ->withQueryString();

        return view('layouts.profesional.presupuestos.index', [
            'presupuestos' => $presupuestos,
            'estado'       => $estado,
            'q'            => $q,
            'estados'      => $estados,
            'perfil'       => $perfil,
        ]);
    }


    /**
     * Formulario para crear un presupuesto a partir de una solicitud concreta.
     */
    public function crearFromSolicitud(Solicitud $solicitud)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()
                ->route('home')
                ->with('error', 'No tienes perfil de profesional.');
        }

        // Seguridad: la solicitud debe pertenecer a este profesional
        if ($solicitud->pro_id !== $perfil->id) {
            return redirect()
                ->back()
                ->with('error', 'No puedes presupuestar solicitudes de otros profesionales.');
        }

        // Podrías limitar sólo a estado abierta/en_revision si quieres
        if (! in_array($solicitud->estado, ['abierta', 'en_revision'])) {
            return redirect()
                ->route('profesional.solicitudes.index')
                ->with('error', 'Sólo puedes presupuestar solicitudes abiertas o en revisión.');
        }

        return view('layouts.profesional.presupuestos.crear', [
            'solicitud' => $solicitud,
        ]);
    }

    /**
     * Guardar presupuesto para la solicitud (modo: docu_pdf o líneas).
     */
    public function guardarFromSolicitud(Request $request, Solicitud $solicitud)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()->route('home')
                ->with('error', 'Debes tener un perfil profesional para acceder a esta sección.');
        }

        if ($solicitud->pro_id !== $perfil->id) {
            return redirect()->route('home')
                ->with('error', 'No puedes presupuestar solicitudes de otros profesionales.');
        }

        // ¿Qué modo usamos? archivo existente o líneas
        $modo = $request->input('modo', 'lineas'); // 'archivo' | 'lineas'

        $rutaPdf = null;
        $total   = 0;
        $notas   = $request->input('notas');

        if ($modo === 'archivo') {
            // ======= MODO 1: el profesional sube un PDF/Word ya hecho =======
            $validated = $request->validate([
                'docu_pdf' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'total'    => ['required', 'numeric', 'min:0'],
                'notas'    => ['nullable', 'string', 'max:2000'],
            ], [
                'docu_pdf.required' => 'Debes adjuntar un documento de presupuesto.',
                'docu_pdf.mimes'    => 'El documento debe ser PDF o Word.',
                'docu_pdf.max'      => 'El archivo no puede superar los 5 MB.',
                'total.required'    => 'El importe total es obligatorio.',
                'total.numeric'     => 'El importe debe ser un número.',
                'total.min'         => 'El importe no puede ser negativo.',
            ]);

            $total = $validated['total'];
            $notas = $validated['notas'] ?? null;

            // Eliminar etiquetas por si acaso (no hace falta Purifier)
            $notas_limpias = $notas ? strip_tags($notas) : null;

            $file = $request->file('docu_pdf'); // Cogemos el archivo del formulario
            $dir  = 'presupuestos/documentos/' . now()->format('Ymd'); // Ponemos ficha al documento(carpeta)

            // Cogemos el nombresin la extension 
            $ext  = $file->getClientOriginalExtension();
            $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base); // Version segura quitando paramentros raros
            $name = $safe . '-' . Str::random(8) . '.' . $ext;
            // Añade un código aleatorio de 8 caracteres (Str::random(8))

            // Creamos el directorio en el disco privado (si no existe)
            Storage::disk('private')->makeDirectory($dir);

            // Guardamos el archivo en el disco privado
            $file->storeAs($dir, $name, 'private');

            // Deifnimos ruta
            $rutaPdf = $dir . '/' . $name;
        } else {
            // ======= MODO 2: líneas -> generamos el PDF =======
            $validated = $request->validate([
                'concepto'           => ['required', 'array', 'min:1'],
                'concepto.*'         => ['nullable', 'string', 'max:255'],
                'cantidad'           => ['required', 'array'],
                'cantidad.*'         => ['nullable', 'numeric', 'min:0'],
                'precio_unitario'    => ['required', 'array'],
                'precio_unitario.*'  => ['nullable', 'numeric', 'min:0'],
                'notas'              => ['nullable', 'string', 'max:2000'],
            ], [
                'concepto.required' => 'Debes añadir al menos una línea de presupuesto.',
                'concepto.array'    => 'Formato de líneas no válido.',

                'cantidad.array'        => 'Formato de cantidades no válido.',
                'precio_unitario.array' => 'Formato de precios no válido.',
            ]);

            $notas         = $validated['notas'] ?? null;
            $subtotal      = 0;
            $lineasValidas = 0;
            $lineas        = [];

            $conceptos  = $validated['concepto'];
            $cantidades = $validated['cantidad'];
            $precios    = $validated['precio_unitario'];

            // Eliminar etiquetas por si acaso (no hace falta Purifier)
            $notas_limpias = $notas ? strip_tags($notas) : null;

            foreach ($conceptos as $i => $concepto) {
                $concepto = trim($concepto ?? '');
                $cantidad = $cantidades[$i] ?? null;
                $precio   = $precios[$i] ?? null;

                if ($concepto !== '' && $cantidad !== null && $precio !== null && $cantidad > 0 && $precio >= 0) {

                    $importeLinea = $cantidad * $precio;
                    $subtotal    += $importeLinea;
                    $lineasValidas++;

                    $lineas[] = [
                        'concepto' => $concepto,
                        'cantidad' => $cantidad,
                        'precio'   => $precio,
                        'importe'  => $importeLinea,
                    ];
                }
            }

            if ($lineasValidas === 0) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'concepto' => 'Debes añadir al menos una línea de presupuesto con datos válidos.',
                    ]);
            }

            // IVA
            $ivaPorcentaje = 21;
            $ivaImporte    = $subtotal * $ivaPorcentaje / 100;
            $total         = $subtotal + $ivaImporte;

            // === Generar PDF desde la vista ===
            $pdf = Pdf::loadView('layouts.profesional.presupuestos.pdf.presupuesto', [
                'profesional'   => $perfil,
                'solicitud'     => $solicitud,
                'lineas'        => $lineas,
                'subtotal'      => $subtotal,
                'ivaPorcentaje' => $ivaPorcentaje,
                'ivaImporte'    => $ivaImporte,
                'total'         => $total,
                'notas'         => $notas_limpias,
                'presupuestoNumero' => null, // si luego quieres numerarlos
            ]);

            // ==== Guardar el PDF generado en el disco "private" con nombre "bonito" ====
            $dir = 'presupuestos/generados/' . now()->format('Ymd');

            // Base del nombre: por ejemplo "presupuesto-solicitud-23-mi-empresa"
            $base = 'presupuesto-solicitud-' . $solicitud->id;
            if (!empty($perfil->empresa)) {
                $base .= '-' . $perfil->empresa;
            }

            // Lo limpiamos para que sea seguro como nombre de fichero
            $safe = Str::slug($base);

            // Extensión fija porque siempre es PDF
            $name = $safe . '-' . Str::random(8) . '.pdf';

            // Creamos directorio (si no existe) y guardamos
            Storage::disk('private')->makeDirectory($dir);
            Storage::disk('private')->put($dir . '/' . $name, $pdf->output());

            // Ruta que guardarás en la BD
            $rutaPdf = $dir . '/' . $name;
        }

        // ==== Guarda el presupuesto en BBDD ====
        try {
            Presupuesto::create([
                'pro_id'       => $perfil->id,
                'solicitud_id' => $solicitud->id,
                'total'        => $total,
                'notas'        => $notas_limpias,
                'estado'       => 'enviado',
                'docu_pdf'     => $rutaPdf,
                'fecha'        => now(),
            ]);

            if ($solicitud->estado === 'abierta') {
                $solicitud->estado = 'en_revision';
                $solicitud->save();
            }

            return redirect()
                ->route('profesional.presupuestos.index')
                ->with('success', 'Presupuesto creado correctamente.');
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un error al guardar el presupuesto.');
        }
    }

    /**
     * El profesional cancela (rechaza) un presupuesto que está ENVIADO.
     */
    public function cancelar(Request $request, Presupuesto $presupuesto)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()
                ->route('home')
                ->with('error', 'Debes tener un perfil profesional para acceder a esta sección.');
        }

        // Seguridad: el presupuesto tiene que ser suyo
        if ($presupuesto->pro_id !== $perfil->id) {
            return redirect()
                ->route('profesional.presupuestos.index')
                ->with('error', 'No puedes modificar presupuestos de otros profesionales.');
        }

        // Solo permitimos cancelar si está ENVIADO
        if ($presupuesto->estado !== 'enviado') {
            return back()->with('error', 'Solo puedes cancelar presupuestos en estado ENVIADO.');
        }

        // Marcamos el presupuesto como RECHAZADO
        $presupuesto->estado = 'rechazado';
        $presupuesto->save();

        // Opcional: revisar la solicitud asociada
        $solicitud = $presupuesto->solicitud;

        if ($solicitud && $solicitud->estado === 'en_revision') {
            // ¿Hay algún otro presupuesto aún enviado o aceptado?
            $hayOtroActivo = $solicitud->presupuestos()
                ->whereIn('estado', ['enviado', 'aceptado'])
                ->where('id', '!=', $presupuesto->id)
                ->exists();

            // Si no, devolvemos la solicitud a "abierta"
            if (! $hayOtroActivo) {
                $solicitud->estado = 'abierta';
                $solicitud->save();
            }
        }

        return back()->with('success', 'Presupuesto cancelado (marcado como rechazado) correctamente.');
    }
}
