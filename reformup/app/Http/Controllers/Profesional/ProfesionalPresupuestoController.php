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

class ProfesionalPresupuestoController extends Controller
{
    /**
     * Listado de presupuestos del profesional logueado.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            abort(403, 'No tienes perfil profesional.');
        }

        $estado = $request->query('estado'); // enviado, aceptado, rechazado, caducado, null

        $presupuestos = Presupuesto::with(['solicitud.cliente'])
            ->where('pro_id', $perfil->id)
            ->when($estado, function ($q) use ($estado) {
                $q->where('estado', $estado);
            })
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('layouts.profesional.presupuestos.index', [
            'presupuestos' => $presupuestos,
            'estado'       => $estado,
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
            abort(403, 'No tienes perfil profesional.');
        }

        // Seguridad: la solicitud debe pertenecer a este profesional
        if ($solicitud->pro_id !== $perfil->id) {
            abort(403, 'No puedes presupuestar solicitudes de otros profesionales.');
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

            $file = $request->file('docu_pdf');
            $dir  = 'presupuestos/documentos/' . now()->format('Ymd');

            $ext  = $file->getClientOriginalExtension();
            $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $name = $safe . '-' . Str::random(8) . '.' . $ext;

            Storage::disk('public')->makeDirectory($dir);
            $file->storeAs($dir, $name, 'public');

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
                'notas'         => $notas,
                'presupuestoNumero' => null, // si luego quieres numerarlos
            ]);

            $dir  = 'presupuestos/generados/' . now()->format('Ymd');
            $safe = Str::slug('presupuesto-solicitud-' . $solicitud->id);
            $name = $safe . '-' . Str::random(8) . '.pdf';

            Storage::disk('public')->makeDirectory($dir);
            Storage::disk('public')->put($dir . '/' . $name, $pdf->output());

            $rutaPdf = $dir . '/' . $name;
        }

        // ==== Guarda el presupuesto en BBDD ====
        try {
            Presupuesto::create([
                'pro_id'       => $perfil->id,
                'solicitud_id' => $solicitud->id,
                'total'        => $total,
                'notas'        => $notas,
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
}
