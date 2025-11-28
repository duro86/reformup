<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\SolicitudCanceladaClienteMailable;
use App\Mail\Admin\SolicitudCanceladaProfesionalMailable;
use App\Mail\Admin\SolicitudModificadaPorAdminMailable;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Traits\FiltroRangoFechas;

class AdminSolicitudController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de todas las solicitudes (admin).
     */
    public function index(Request $request)
    {
        $q = $request->input('q');

        $query = Solicitud::with(['cliente', 'profesional']);

        // --- Filtro por texto (tu lógica actual) ---
        if ($q) {
            $qLike = '%' . $q . '%';

            $query->where(function ($sub) use ($qLike) {
                $sub->where('titulo', 'like', $qLike)
                    ->orWhere('ciudad', 'like', $qLike)
                    ->orWhere('provincia', 'like', $qLike)
                    ->orWhere('estado', 'like', $qLike)
                    ->orWhereHas('cliente', function ($q2) use ($qLike) {
                        $q2->where('nombre', 'like', $qLike)
                            ->orWhere('apellidos', 'like', $qLike)
                            ->orWhere('email', 'like', $qLike);
                    })
                    ->orWhereHas('profesional', function ($q3) use ($qLike) {
                        $q3->where('empresa', 'like', $qLike)
                            ->orWhere('email_empresa', 'like', $qLike);
                    });
            });
        }

        // --- Filtro por rango de fechas (reutilizable) ---
        // Aquí usamos la columna fecha de la solicitud
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');
        

        // --- Orden y paginación ---
        $solicitudes = $query
            ->orderByDesc('fecha')
            ->paginate(6)
            ->withQueryString(); // conserva ?q, ?fecha_desde, ?fecha_hasta

        return view('layouts.admin.solicitudes.index', compact('solicitudes', 'q'));
    }


    /**
     * Mostrar una solicitud mediante una ventana modal
     */
    public function mostrar(Solicitud $solicitud)
    {
        // Cargar relaciones necesarias
        $solicitud->load([
            'cliente',
            'profesional',
            // añadimos:
            'presupuestos.trabajo',
        ]);

        $cliente   = $solicitud->cliente;
        $perfilPro = $solicitud->profesional;

        // Escogemos un presupuesto “asociado principal”
        // (ajusta la lógica si usas campo estado = 'aceptado', etc.)
        $presupuestoAsociado = $solicitud->presupuestos->first();
        $trabajoAsociado     = $presupuestoAsociado?->trabajo;

        if (request()->wantsJson()) {
            return response()->json([
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
                'created_at'      => $solicitud->created_at
                    ? $solicitud->created_at->format('d/m/Y H:i')
                    : null,
                'updated_at'      => $solicitud->updated_at
                    ? $solicitud->updated_at->format('d/m/Y H:i')
                    : null,

                'cliente' => $cliente ? [
                    'nombre'    => $cliente->nombre ?? $cliente->name ?? null,
                    'apellidos' => $cliente->apellidos ?? null,
                    'email'     => $cliente->email,
                    'telefono'  => $cliente->telefono ?? null,
                ] : null,

                'profesional' => $perfilPro ? [
                    'empresa'          => $perfilPro->empresa,
                    'email_empresa'    => $perfilPro->email_empresa,
                    'telefono_empresa' => $perfilPro->telefono_empresa,
                    'ciudad'           => $perfilPro->ciudad,
                    'provincia'        => $perfilPro->provincia,
                ] : null,

                // 🔹 Presupuesto asociado (si hay)
                'presupuesto' => $presupuestoAsociado ? [
                    'id'     => $presupuestoAsociado->id,
                    'estado' => $presupuestoAsociado->estado ?? null,
                    'total'  => $presupuestoAsociado->total ?? null,
                ] : null,

                // 🔹 Trabajo asociado (si hay)
                'trabajo' => $trabajoAsociado ? [
                    'id'        => $trabajoAsociado->id,
                    'estado'    => $trabajoAsociado->estado ?? null,
                    'fecha_ini' => $trabajoAsociado->fecha_ini
                        ? $trabajoAsociado->fecha_ini->format('d/m/Y H:i')
                        : null,
                    'fecha_fin' => $trabajoAsociado->fecha_fin
                        ? $trabajoAsociado->fecha_fin->format('d/m/Y H:i')
                        : null,
                ] : null,
            ]);
        }

        return view('layouts.admin.solicitudes.mostrar', compact('solicitud'));
    }

    public function editar(Solicitud $solicitud)
    {
        // No dejamos editar si ya está cerrada o cancelada
        if (in_array($solicitud->estado, ['cerrada', 'cancelada'])) {
            return redirect()
                ->route('admin.solicitudes')
                ->with('error', 'No puedes editar una solicitud cerrada o cancelada.');
        }

        // Cargamos cliente / profesional por si quieres mostrarlos
        $solicitud->load('cliente', 'profesional');

        $estados = Solicitud::ESTADOS; // ['abierta' => 'Abiertas', ...]

        return view('layouts.admin.solicitudes.editar', compact('solicitud', 'estados'));
    }

    /**
     * Actualizar o Modificar una solicitud por parte de admin
     */
    public function actualizar(Request $request, Solicitud $solicitud)
    {
        // Bloqueamos edición de cerradas / canceladas
        if (in_array($solicitud->estado, ['cerrada', 'cancelada'])) {
            return redirect()
                ->route('admin.solicitudes')
                ->with('error', 'No puedes editar una solicitud cerrada o cancelada.');
        }

        $validated = $request->validate([
            'titulo'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'ciudad'          => 'nullable|string|max:255',
            'provincia'       => 'nullable|string|max:255',
            'presupuesto_max' => 'nullable|numeric|min:0',
            'estado'          => 'required|in:abierta,en_revision,cerrada,cancelada',
        ]);

        $oldEstado = $solicitud->estado;
        $newEstado = $validated['estado'];

        // Cargamos relaciones para ver si hay presupuesto / trabajo asociados
        $solicitud->load('presupuestos.trabajo', 'cliente', 'presupuestos.profesional');

        // “Presupuesto principal” = primero (ajusta si tienes campo específico)
        $presupuesto = $solicitud->presupuestos->first();
        $trabajo     = $presupuesto?->trabajo;

        // Referencias comunes
        $cliente   = $solicitud->cliente;
        $perfilPro = $presupuesto?->profesional;

        /**
         * 1) Si el admin ha cambiado el estado a cancelada → lógica de cancelación completa
         */
        if ($newEstado === 'cancelada' && $oldEstado !== 'cancelada') {

            // Cancelamos solicitud
            $solicitud->fill($validated);
            $solicitud->estado = 'cancelada';
            $solicitud->save();

            // Presupuesto asociado → rechazado
            if ($presupuesto) {
                $presupuesto->estado = 'rechazado';
                $presupuesto->save();
            }

            // Trabajo asociado → cancelado (por si acaso lo hubiera)
            if ($trabajo) {
                $trabajo->estado = 'cancelado';
                $trabajo->save();
            }

            // 4) Email al cliente
            if ($cliente && $cliente->email) {
                try {
                    Mail::to($cliente->email)->send(
                        new SolicitudModificadaPorAdminMailable(
                            $solicitud,
                            $cliente,
                            $presupuesto,
                            $trabajo,
                            $perfilPro,
                            $oldEstado,
                            $newEstado
                        )
                    );
                } catch (\Throwable $e) {
                    return redirect()
                        ->route('admin.solicitudes')
                        ->with('error', 'La solicitud se ha cancelado, pero no se ha podido enviar el correo al usuario.');
                }
            }

            // 5) Email al profesional (si lo hay)
            if ($perfilPro && $perfilPro->email_empresa) {
                try {
                    Mail::to($perfilPro->email_empresa)->send(
                        new SolicitudModificadaPorAdminMailable(
                            $solicitud,
                            null,          // cliente null → correo adaptado para el profesional
                            $presupuesto,
                            $trabajo,
                            $perfilPro,
                            $oldEstado,
                            $newEstado
                        )
                    );
                } catch (\Throwable $e) {
                    return redirect()
                        ->route('admin.solicitudes')
                        ->with('error', 'La solicitud se ha cancelado, pero no se ha podido enviar el correo al profesional.');
                }
            }

            return redirect()
                ->route('admin.solicitudes')
                ->with('success', 'Solicitud cancelada. Presupuesto y trabajo asociados se han actualizado.');
        }

        /**
         * 2) Cualquier otra edición “normal”
         */
        $solicitud->fill($validated);
        $solicitud->save();

        // En ediciones normales también avisamos a cliente y profesional (si existen)
        try {
            // Email al cliente
            if ($cliente && $cliente->email) {
                Mail::to($cliente->email)->send(
                    new SolicitudModificadaPorAdminMailable(
                        $solicitud,
                        $cliente,
                        $presupuesto,
                        $trabajo,
                        $perfilPro
                    )
                );
            }

            // Email al profesional
            if ($perfilPro && $perfilPro->email_empresa) {
                Mail::to($perfilPro->email_empresa)->send(
                    new SolicitudModificadaPorAdminMailable(
                        $solicitud,
                        null,       // cliente null
                        $presupuesto,
                        $trabajo,
                        $perfilPro
                    )
                );
            }
        } catch (\Throwable $e) {
            // Si el correo falla, no rompemos la edición, solo avisamos
            return redirect()
                ->route('admin.solicitudes')
                ->with('error', 'La solicitud se ha actualizado, pero ha fallado el envío de los correos.');
        }

        return redirect()
            ->route('admin.solicitudes')
            ->with('success', 'Solicitud actualizada correctamente y correos enviados.');
    }


    public function cancelar(Request $request, Solicitud $solicitud)
    {
        $solicitud->load('cliente', 'profesional', 'presupuestos.trabajo');

        // 1) Cancelar solicitud
        $solicitud->estado = 'cancelada';
        $solicitud->save();

        // 2) Presupuesto asociado
        $presupuesto = $solicitud->presupuesto;
        if ($presupuesto) {
            $presupuesto->estado = 'rechazado';
            $presupuesto->save();
        }

        // 3) Trabajo asociado (por si acaso)
        $trabajo = $solicitud->trabajo;
        if ($trabajo) {
            $trabajo->estado = 'cancelado';
            $trabajo->save();
        }

        $cliente   = $solicitud->cliente;
        $perfilPro = $solicitud?->profesional;

        // 4) Email al cliente
        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new SolicitudCanceladaClienteMailable(
                        $solicitud,
                        $cliente,
                        $perfilPro,
                        $presupuesto,
                        $trabajo
                    )
                );
            } catch (\Throwable $e) {
                // El comentario ya está guardado; avisamos del fallo de correo
                return redirect()
                    ->route('admin.solicitudes')
                    ->with('error', 'La solicitud se ha actualizado, pero no se ha podido enviar el correo al usuario.');
            }
        }

        // 5) Email al profesional (si lo había)
        if ($perfilPro && $perfilPro->email_empresa) {
            try {
                Mail::to($perfilPro->email_empresa)->send(
                    new SolicitudCanceladaProfesionalMailable(
                        $solicitud,
                        $perfilPro,
                        $cliente,
                        $presupuesto,
                        $trabajo
                    )
                );
            } catch (\Throwable $e) {
                // El comentario ya está guardado; avisamos del fallo de correo
                return redirect()
                    ->route('admin.solicitudes')
                    ->with('error', 'La solicitud se ha actualizado, pero no se ha podido enviar el correo al profesional.');
            }
        }

        return redirect()
            ->route('admin.solicitudes')
            ->with('success', 'Solicitud cancelada. Cliente y profesional han sido notificados.');
    }

    /**
     * Crear una solicitud nueva por parte del admin
     */
    public function crear()
    {
        // Clientes = usuarios con rol "cliente"
        $clientes = User::role('usuario')   // <- scope de Spatie
            ->orderBy('nombre')            // o 'name' según tu modelo
            ->get();

        // Profesionales = perfiles cuyo usuario tiene rol "profesional"
        $profesionales = Perfil_Profesional::with('user')
            ->whereHas('user', function ($q) {
                $q->role('profesional');    // <- scope de Spatie
            })
            ->orderBy('empresa')
            ->get();

        return view('layouts.admin.solicitudes.crear', [
            'clientes'      => $clientes,
            'profesionales' => $profesionales,
        ]);
    }

    /**
     * Guardar datos al crear
     */
    public function guardar(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'cliente_id'      => ['required', 'integer', 'exists:users,id'],
                'pro_id'          => ['required', 'integer', 'exists:perfiles_profesionales,id'],
                'titulo'          => ['required', 'string', 'max:160'],
                'descripcion'     => ['required', 'string'],
                'ciudad'          => ['required', 'string', 'max:120'],
                'provincia'       => ['nullable', 'string', 'max:120'],
                'dir_cliente'     => ['nullable', 'string', 'max:255'],
                'presupuesto_max' => ['nullable', 'numeric', 'min:0'],
            ],
            [
                // Cliente
                'cliente_id.required' => 'Debes seleccionar un cliente para esta solicitud.',
                'cliente_id.integer'  => 'El identificador del cliente no es válido.',
                'cliente_id.exists'   => 'El cliente seleccionado no existe.',

                // Profesional (perfiles_profesionales)
                'pro_id.required' => 'Debes seleccionar un profesional para esta solicitud.',
                'pro_id.integer'  => 'El identificador del profesional no es válido.',
                'pro_id.exists'   => 'El profesional seleccionado no existe.',

                // Campos de la solicitud
                'titulo.required'      => 'El título es obligatorio.',
                'titulo.max'           => 'El título no puede tener más de 160 caracteres.',
                'descripcion.required' => 'La descripción de la solicitud es obligatoria.',
                'ciudad.required'      => 'La ciudad es obligatoria.',
                'ciudad.max'           => 'La ciudad no puede tener más de 120 caracteres.',
                'provincia.max'        => 'La provincia no puede tener más de 120 caracteres.',
                'dir_cliente.max'      => 'La dirección no puede tener más de 255 caracteres.',
                'presupuesto_max.numeric' => 'El presupuesto máximo debe ser un número.',
                'presupuesto_max.min'     => 'El presupuesto máximo no puede ser negativo.',
            ]
        );

        // 🔎 Validaciones extra uniendo lógica de perfiles_profesionales y roles
        $validator->after(function ($validator) use ($request) {

            $clienteId = $request->input('cliente_id');
            $proId     = $request->input('pro_id');

            if (!$proId) {
                return;
            }

            // Cargamos el perfil profesional con su usuario
            $perfilPro = Perfil_Profesional::with('user')->find($proId);

            if (!$perfilPro) {
                // Por si acaso, aunque el exists ya debería haber saltado
                $validator->errors()->add('pro_id', 'El profesional seleccionado no existe en el sistema.');
                return;
            }

            // Que tenga usuario asociado
            if (!$perfilPro->user) {
                $validator->errors()->add('pro_id', 'El profesional seleccionado no tiene un usuario asociado válido.');
                return;
            }

            // Que el usuario tenga rol profesional (Spatie)
            if (!$perfilPro->user->hasRole('profesional')) {
                $validator->errors()->add('pro_id', 'El profesional seleccionado no tiene el rol de profesional.');
            }

            // Que cliente y profesional no sean el mismo usuario
            if ($clienteId && $perfilPro->user_id == $clienteId) {
                $validator->errors()->add('pro_id', 'El cliente y el profesional no pueden ser el mismo usuario.');
            }
        });

        // 👇 Aquí es donde metemos el back() + SweetAlert
        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Revisa los campos marcados en rojo.');
        }

        // Si todo va bien, ya tienes datos validados
        $validated = $validator->validated();

        // Aquí creamos la solicitud:

        $solicitud = new Solicitud();
        $solicitud->cliente_id      = $validated['cliente_id'];
        $solicitud->pro_id          = $validated['pro_id'];
        $solicitud->titulo          = $validated['titulo'];
        $solicitud->descripcion     = $validated['descripcion'];
        $solicitud->ciudad          = $validated['ciudad'];
        $solicitud->provincia       = $validated['provincia'] ?? null;
        $solicitud->dir_cliente     = $validated['dir_cliente'] ?? null;
        $solicitud->presupuesto_max = $validated['presupuesto_max'] ?? null;
        $solicitud->estado          = 'abierta';
        $solicitud->save();


        return redirect()
            ->route('admin.solicitudes')
            ->with('success', 'Solicitud creada correctamente.');
    }
}
