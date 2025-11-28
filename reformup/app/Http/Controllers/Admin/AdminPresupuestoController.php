<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presupuesto;
use App\Models\Solicitud;
use App\Mail\Admin\PresupuestoCanceladoPorAdminMailable;
use App\Mail\Admin\PresupuestoModificadoPorAdminMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Traits\FiltroRangoFechas;


class AdminPresupuestoController extends Controller
{
    use FiltroRangoFechas;

    /**
     * Listado presupuesto + información adicional solicitud
     */
    public function index(Request $request)
    {
        $estado = $request->query('estado');
        $q      = $request->query('q');

        $estados = [
            null         => 'Todos',
            'enviado'    => 'Enviados',
            'aceptado'   => 'Aceptados',
            'rechazado'  => 'Rechazados',
            'cancelado'  => 'Cancelados',
            'caducado'   => 'Caducados',
        ];

        // Empezamos la consulta base
        $query = Presupuesto::with(['solicitud.cliente', 'profesional']);

        // ----- Filtro por estado -----
        if ($estado) {
            $query->where('estado', $estado);
        }

        // ----- Filtro por búsqueda libre (tu lógica tal cual) -----
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub
                    // Buscar por id exacto si escribe un número
                    ->orWhere('id', $q)
                    // Por total aproximado (si metiera un número con coma o punto)
                    ->orWhere('total', 'like', '%' . str_replace(',', '.', $q) . '%')
                    // Por datos de la solicitud
                    ->orWhereHas('solicitud', function ($q2) use ($q) {
                        $q2->where('titulo', 'like', "%{$q}%")
                            ->orWhere('ciudad', 'like', "%{$q}%")
                            ->orWhere('provincia', 'like', "%{$q}%");
                    })
                    // Por cliente
                    ->orWhereHas('solicitud.cliente', function ($q3) use ($q) {
                        $q3->where('nombre', 'like', "%{$q}%")
                            ->orWhere('apellidos', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    })
                    // Por profesional
                    ->orWhereHas('profesional', function ($q4) use ($q) {
                        $q4->where('empresa', 'like', "%{$q}%")
                            ->orWhere('email_empresa', 'like', "%{$q}%")
                            ->orWhere('ciudad', 'like', "%{$q}%")
                            ->orWhere('provincia', 'like', "%{$q}%");
                    });
            });
        }

        // ----- Filtro por rango de fechas (usando el trait) -----
        // Aquí puedes usar 'created_at' o 'fecha', según lo que te interese
        $this->aplicarFiltroRangoFechas($query, $request, 'created_at');
        // Si prefieres por la columna 'fecha' del presupuesto:
        // $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

        // ----- Paginación -----
        $presupuestos = $query
            ->orderByDesc('created_at')   // o 'fecha' si has usado 'fecha' arriba
            ->paginate(6)
            ->withQueryString();          // conserva q, estado, fecha_desde, fecha_hasta

        return view('layouts.admin.presupuestos.index', [
            'presupuestos' => $presupuestos,
            'estados'      => $estados,
            'estado'       => $estado,
        ]);
    }

    /**
     * Mostrar los datos del presupuesto
     */
    public function mostrar(Presupuesto $presupuesto)
    {
        // Cargamos todo lo que necesitamos para el modal
        $presupuesto->load([
            'solicitud.cliente',
            'solicitud.profesional',
        ]);

        $solicitud = $presupuesto->solicitud;
        $cliente   = $solicitud?->cliente;
        $pro       = $solicitud?->profesional;

        if (request()->wantsJson()) {
            return response()->json([
                'id'        => $presupuesto->id,
                'estado'    => $presupuesto->estado,
                'total'     => $presupuesto->total,
                'notas'     => $presupuesto->notas,
                'fecha'     => $presupuesto->fecha
                    ? $presupuesto->fecha->format('d/m/Y H:i')
                    : null,
                'created_at' => $presupuesto->created_at
                    ? $presupuesto->created_at->format('d/m/Y H:i')
                    : null,
                'updated_at' => $presupuesto->updated_at
                    ? $presupuesto->updated_at->format('d/m/Y H:i')
                    : null,

                // PDF
                'docu_pdf' => $presupuesto->docu_pdf
                    ? asset('storage/' . $presupuesto->docu_pdf)
                    : null,

                // Solicitud asociada
                'solicitud' => $solicitud ? [
                    'id'              => $solicitud->id,
                    'titulo'          => $solicitud->titulo,
                    'descripcion'     => $solicitud->descripcion,
                    'estado'          => $solicitud->estado,
                    'ciudad'          => $solicitud->ciudad,
                    'provincia'       => $solicitud->provincia,
                    'presupuesto_max' => $solicitud->presupuesto_max,
                    'fecha'           => $solicitud->fecha
                        ? $solicitud->fecha->format('d/m/Y H:i')
                        : null,
                ] : null,

                // Cliente
                'cliente' => $cliente ? [
                    'nombre'    => $cliente->nombre ?? $cliente->name ?? null,
                    'apellidos' => $cliente->apellidos ?? null,
                    'email'     => $cliente->email,
                    'telefono'  => $cliente->telefono ?? null,
                ] : null,

                // Profesional
                'profesional' => $pro ? [
                    'empresa'          => $pro->empresa,
                    'email_empresa'    => $pro->email_empresa,
                    'telefono_empresa' => $pro->telefono_empresa,
                    'ciudad'           => $pro->ciudad,
                    'provincia'        => $pro->provincia,
                ] : null,
            ]);
        }

        // Si algún día quieres una vista "normal":
        return view('layouts.admin.presupuestos.mostrar', compact('presupuesto'));
    }

    /**
     * Seleccioonar solicitud para nuevo si hay solicitud abietas
     */
    public function seleccionarSolicitudParaNuevo()
    {
        // Solicitudes abiertas o en revisión
        // que NO tengan NINGÚN presupuesto en estado distinto de 'rechazado'
        $solicitudes = Solicitud::query()
            ->whereIn('estado', ['abierta', 'en_revision'])
            ->whereDoesntHave('presupuestos', function ($q) {
                $q->where('estado', '!=', 'rechazado');
            })
            ->with(['cliente', 'profesional'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('layouts.admin.presupuestos.seleccionar_solicitud', [
            'solicitudes' => $solicitudes,
        ]);
    }

    /**
     * Formulario para crear un presupuesto (ADMIN) a partir de una solicitud concreta.
     */
    public function crearDesdeSolicitud(Solicitud $solicitud)
    {
        // Solo permitir abierta / en revisión
        if (! in_array($solicitud->estado, ['abierta', 'en_revision'])) {
            return redirect()
                ->route('admin.solicitudes')
                ->with('error', 'Sólo puedes presupuestar solicitudes abiertas o en revisión.');
        }

        // Debe tener profesional asignado
        if (! $solicitud->pro_id || ! $solicitud->profesional) {
            return redirect()
                ->route('admin.solicitudes', $solicitud)
                ->with('error', 'La solicitud no tiene un profesional asignado.');
        }

        $solicitud->load('cliente', 'profesional');

        return view('layouts.admin.presupuestos.crear_desde_solicitud', [
            'solicitud' => $solicitud,
        ]);
    }

    /**
     * Guardar presupuesto creado por ADMIN desde una solicitud.
     */
    public function guardarDesdeSolicitud(Request $request, Solicitud $solicitud)
    {
        // Misma protección básica que en crear
        if (! in_array($solicitud->estado, ['abierta', 'en_revision'])) {
            return redirect()
                ->route('admin.solicitudes')
                ->with('error', 'Sólo puedes presupuestar solicitudes abiertas o en revisión.');
        }

        if (! $solicitud->pro_id || ! $solicitud->profesional) {
            return redirect()
                ->route('admin.solicitudes.mostrar', $solicitud)
                ->with('error', 'La solicitud no tiene un profesional asignado.');
        }

        $modo = $request->input('modo', 'lineas'); // 'archivo' | 'lineas'

        $rutaPdf = null;
        $total   = 0;
        $notas   = $request->input('notas');

        if ($modo === 'archivo') {
            // ======= MODO 1: subir PDF/Word ya hecho =======
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
            // ======= MODO 2: líneas -> generamos PDF =======
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

            // Generar PDF desde la vista (la misma que el profesional)
            $pdf = Pdf::loadView('layouts.profesional.presupuestos.pdf.presupuesto', [
                'profesional'        => $solicitud->profesional, // aquí usamos el profesional de la solicitud
                'solicitud'          => $solicitud,
                'lineas'             => $lineas,
                'subtotal'           => $subtotal,
                'ivaPorcentaje'      => $ivaPorcentaje,
                'ivaImporte'         => $ivaImporte,
                'total'              => $total,
                'notas'              => $notas,
                'presupuestoNumero'  => null,
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
                'pro_id'       => $solicitud->pro_id,   //pro_id del profesional de la solicitud
                'solicitud_id' => $solicitud->id,
                'total'        => $total,
                'notas'        => $notas,
                'estado'       => 'enviado',
                'docu_pdf'     => $rutaPdf,
                'fecha'        => now(),
            ]);

            // Igual que en profesional: si estaba abierta → en_revision
            if ($solicitud->estado === 'abierta') {
                $solicitud->estado = 'en_revision';
                $solicitud->save();
            }

            return redirect()
                ->route('admin.presupuestos')
                ->with('success', 'Presupuesto creado correctamente desde la solicitud.');
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un error al guardar el presupuesto.');
        }
    }


    /**
     * Cancelar presupuesto por parte del admin
     * 
     */
    public function cancelar(Request $request, Presupuesto $presupuesto)
    {
        // Sólo permitimos cancelar si está ENVIADO (igual que el profesional)
        if ($presupuesto->estado !== 'enviado') {
            return back()->with('error', 'Solo puedes cancelar presupuestos en estado ENVIADO.');
        }

        // Marcamos el presupuesto como RECHAZADO
        $presupuesto->estado = 'rechazado';
        $presupuesto->save();

        // Revisamos la solicitud asociada
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

        // Emails a cliente y profesional
        $solicitud->loadMissing('cliente', 'profesional');
        $cliente   = $solicitud->cliente;
        $perfilPro = $solicitud->profesional;

        try {
            // Email al cliente
            if ($cliente && $cliente->email) {
                Mail::to($cliente->email)->send(
                    new PresupuestoCanceladoPorAdminMailable(
                        $presupuesto,
                        $solicitud,
                        $cliente,
                        $perfilPro,
                        false // esProfesional = false
                    )
                );
            }

            // Email al profesional
            if ($perfilPro && $perfilPro->email_empresa) {
                Mail::to($perfilPro->email_empresa)->send(
                    new PresupuestoCanceladoPorAdminMailable(
                        $presupuesto,
                        $solicitud,
                        null,       // cliente null
                        $perfilPro,
                        true        // esProfesional = true
                    )
                );
            }
        } catch (\Throwable $e) {
            // No deshacemos la cancelación, solo avisamos
            return back()->with('error', 'El presupuesto se ha cancelado, pero ha fallado el envío de los correos.');
        }

        return back()->with('success', 'Presupuesto cancelado correctamente. Cliente y profesional han sido notificados.');
    }

    /**
     * Editar presupuesto por parte del admin
     */
    public function editar(Presupuesto $presupuesto)
    {
        // Cargamos relaciones para contexto en la vista
        $presupuesto->load('solicitud.cliente', 'solicitud.profesional');

        return view('layouts.admin.presupuestos.editar', compact('presupuesto'));
    }

    /**
     * Actualizar y validar información para actualizar presupuesto
     */
    public function actualizar(Request $request, Presupuesto $presupuesto)
    {
        // Validación
        $validated = $request->validate([
            'total'  => 'required|numeric|min:0',
            'estado' => 'required|in:enviado,aceptado,rechazado,caducado',
            'notas'  => 'nullable|string|max:2000',
        ], [
            'total.required' => 'El importe total es obligatorio.',
            'total.numeric'  => 'El importe total debe ser un número.',
            'total.min'      => 'El importe total no puede ser negativo.',

            'estado.required' => 'Debes indicar el estado del presupuesto.',
            'estado.in'       => 'El estado seleccionado no es válido.',
        ]);

        // Guardamos datos antiguos por si los usamos en el mail
        $oldTotal  = $presupuesto->total;
        $oldNotas  = $presupuesto->notas;
        $oldEstado = $presupuesto->estado;

        // Actualizar presupuesto
        $presupuesto->total  = $validated['total'];
        $presupuesto->estado = $validated['estado'];
        $presupuesto->notas  = $validated['notas'] ?? null;
        // Si quieres, puedes guardar fecha de última revisión:
        $presupuesto->fecha  = now(); // si tienes este campo
        $presupuesto->save();

        // Cargar relaciones para el email
        $presupuesto->load('solicitud.cliente', 'solicitud.profesional');

        $solicitud = $presupuesto->solicitud;
        $cliente   = $solicitud?->cliente;
        $perfilPro = $solicitud?->profesional;

        // Enviar mail al cliente y al profesional avisando de la modificación
        try {

            //  Email al cliente
            if ($cliente && $cliente->email) {
                Mail::to($cliente->email)->send(
                    new PresupuestoModificadoPorAdminMailable(
                        presupuesto: $presupuesto,
                        solicitud: $solicitud,
                        cliente: $cliente,
                        perfilPro: $perfilPro,
                        isProfesional: false,
                        oldTotal: $oldTotal,
                        oldNotas: $oldNotas,
                        oldEstado: $oldEstado,
                    )
                );
            }

            //  Email al profesional
            if ($perfilPro && $perfilPro->email_empresa) {
                Mail::to($perfilPro->email_empresa)->send(
                    new PresupuestoModificadoPorAdminMailable(
                        presupuesto: $presupuesto,
                        solicitud: $solicitud,
                        cliente: $cliente,
                        perfilPro: $perfilPro,
                        isProfesional: true,
                        oldTotal: $oldTotal,
                        oldNotas: $oldNotas,
                        oldEstado: $oldEstado,
                    )
                );
            }
        } catch (\Throwable $e) {
            // El presupuesto ya está guardado; avisamos del fallo de correo
            return redirect()
                ->route('admin.presupuestos')
                ->with('error', 'El presupuesto se ha actualizado, pero no se ha podido enviar el correo a cliente y/o profesional.');
        }

        return redirect()
            ->route('admin.presupuestos')
            ->with('success', 'Presupuesto actualizado correctamente. Cliente y profesional han sido notificados de la modificación.');
    }

    /**
     * Eliminar presupuesto por parte del admin
     * 
     */

    public function eliminarPresuAdmin(Presupuesto $presupuesto)
    {
        // Cargamos relaciones necesarias (si no las tienes ya eager-loaded en el index)
        $presupuesto->load(['solicitud.cliente', 'trabajo', 'profesional']);

        $solicitud = $presupuesto->solicitud;
        $trabajo   = $presupuesto->trabajo;
        $cliente   = $solicitud?->cliente;
        $pro       = $presupuesto->profesional;

        // CASO 1: ENVIADO → aquí NO se elimina, para eso ya tienes cancelar
        if ($presupuesto->estado === 'enviado') {
            return back()->with(
                'error',
                'Este presupuesto está ENVIADO. Primero debes cancelarlo con el botón de "Cancelar", no eliminarlo.'
            );
        }

        // CASO 2: ACEPTADO → sólo si el trabajo NO ha empezado (fecha_ini = null)
        if ($presupuesto->estado === 'aceptado') {

            if (! $trabajo) {
                return back()->with(
                    'error',
                    'Este presupuesto está aceptado pero no tiene trabajo asociado. Revisa los datos antes de eliminar.'
                );
            }

            if (! is_null($trabajo->fecha_ini)) {
                return back()->with(
                    'error',
                    'El trabajo asociado ya tiene fecha de inicio. No se puede cancelar/eliminar este presupuesto.'
                );
            }

            // 1) Marcamos presupuesto como CANCELADO (no lo borramos físicamente)
            $presupuesto->estado = 'cancelado';
            $presupuesto->save();

            // 2) Cancelamos el trabajo asociado
            $trabajo->estado = 'cancelado';
            $trabajo->save();

            // 3) La solicitud vuelve a EN_REVISIÓN para poder enviar un nuevo presupuesto
            if ($solicitud) {
                $solicitud->estado = 'en_revision';
                $solicitud->save();
            }

            // 4) Avisos por email (si quieres)
            /*
        try {
            if ($cliente && $cliente->email) {
                Mail::to($cliente->email)->queue(new PresupuestoCanceladoPorAdmin($presupuesto, 'cliente'));
            }

            if ($pro && $pro->email_empresa) {
                Mail::to($pro->email_empresa)->queue(new PresupuestoCanceladoPorAdmin($presupuesto, 'profesional'));
            }
        } catch (\Throwable $e) {
            // loguear si quieres, pero sin romper el flujo
        }
        */

            return back()->with(
                'success',
                'Presupuesto aceptado cancelado correctamente. Se ha cancelado también el trabajo y la solicitud ha pasado a "en revisión".'
            );
        }

        // CASO 3: RECHAZADO → se puede ELIMINAR y la solicitud queda CERRADA
        if ($presupuesto->estado === 'rechazado') {

            // Opcional: borrar el PDF del disco si existe
            if ($presupuesto->docu_pdf) {
                // Ajusta 'public' por 'private' si lo guardas en otro disk
                Storage::disk('public')->delete($presupuesto->docu_pdf);
            }

            // 1) Eliminamos el presupuesto
            $presupuesto->delete();

            // 2) La solicitud asociada queda CERRADA (si existe)
            if ($solicitud) {
                $solicitud->estado = 'cerrada';
                $solicitud->save();
            }

            return back()->with(
                'success',
                'Presupuesto rechazado eliminado correctamente. La solicitud asociada ha quedado cerrada.'
            );
        }

        // Otros estados → de momento no permitimos esta operación
        return back()->with(
            'error',
            'No se puede eliminar este presupuesto en su estado actual.'
        );
    }
}
