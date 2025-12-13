<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\TrabajoModificadoPorAdminMailable;
use App\Mail\Admin\TrabajoCanceladoPorAdminMailable;
use App\Http\Controllers\Traits\FiltroRangoFechas;
use App\Exports\TrabajosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class AdminTrabajoController extends Controller
{
    /**
     * Listado de trabajos (ADMIN)
     * Filtros por estado: previsto, en_curso, finalizado, cancelado
     */
    use FiltroRangoFechas;

    public function index(Request $request)
    {
        // Filtros
        $estado = $request->query('estado');           // previsto, en_curso, finalizado, cancelado o null
        $q      = trim((string) $request->query('q')); // texto buscador

        $estados = Trabajo::ESTADOS;

        $query = Trabajo::with([
            'presupuesto.solicitud.cliente',
            'presupuesto.solicitud.profesional',
            'presupuesto.profesional',
        ]);

        // Filtro por estado (si viene)
        if ($estado !== null && $estado !== '') {
            if (array_key_exists($estado, Trabajo::ESTADOS)) {
                $query->where('estado', $estado);
            }
        }

        // Filtro por buscador de texto
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                // Buscar por título / ciudad / provincia de la solicitud
                $sub->whereHas('presupuesto.solicitud', function ($q2) use ($like) {
                    $q2->where('titulo', 'like', $like)
                        ->orWhere('ciudad', 'like', $like)
                        ->orWhere('provincia', 'like', $like);
                })
                    // Buscar por datos del cliente
                    ->orWhereHas('presupuesto.solicitud.cliente', function ($q3) use ($like) {
                        $q3->where('nombre', 'like', $like)
                            ->orWhere('apellidos', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })
                    // Buscar por profesional del presupuesto
                    ->orWhereHas('presupuesto.profesional', function ($q4) use ($like) {
                        $q4->where('empresa', 'like', $like)
                            ->orWhere('email_empresa', 'like', $like)
                            ->orWhere('ciudad', 'like', $like)
                            ->orWhere('provincia', 'like', $like);
                    })
                    // Profesional de la solicitud (si lo usas)
                    ->orWhereHas('presupuesto.solicitud.profesional', function ($q5) use ($like) {
                        $q5->where('empresa', 'like', $like)
                            ->orWhere('email_empresa', 'like', $like)
                            ->orWhere('ciudad', 'like', $like)
                            ->orWhere('provincia', 'like', $like);
                    })
                    // Buscar por estado del propio trabajo
                    ->orWhere('estado', 'like', $like);
            });
        }

        // Filtro por rango de fechas (reutilizable)
        // Aquí 'fecha_ini' 
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha_ini');

        $trabajos = $query
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();       // mantiene q, estado, fecha_desde, fecha_hasta

        return view('layouts.admin.trabajos.index', [
            'trabajos' => $trabajos,
            'estado'   => $estado,
            'estados'   => $estados,
            'q'        => $q,
        ]);
    }

    /**
     * Mostrar trabajos en ventana modal
     */
    public function mostrar(Trabajo $trabajo)
    {
        // Cargamos todo lo necesario para el modal
        $trabajo->load([
            'presupuesto.solicitud.cliente',
            'presupuesto.solicitud.profesional',
        ]);

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $pro         = $solicitud?->profesional;

        if (request()->wantsJson()) {
            return response()->json([
                'id'        => $trabajo->id,
                'estado'    => $trabajo->estado,
                'descripcion' => $trabajo->descripcion ?? null,
                'dir_obra' => $trabajo->dir_obra ?? null,
                'fecha_ini' => $trabajo->fecha_ini
                    ? $trabajo->fecha_ini->format('d/m/Y H:i')
                    : null,
                'fecha_fin' => $trabajo->fecha_fin
                    ? $trabajo->fecha_fin->format('d/m/Y H:i')
                    : null,
                'created_at' => $trabajo->created_at
                    ? $trabajo->created_at->format('d/m/Y H:i')
                    : null,
                'updated_at' => $trabajo->updated_at
                    ? $trabajo->updated_at->format('d/m/Y H:i')
                    : null,

                // Presupuesto asociado
                'presupuesto' => $presupuesto ? [
                    'id'     => $presupuesto->id,
                    'estado' => $presupuesto->estado,
                    'total'  => $presupuesto->total,
                ] : null,

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

        // Si algún día quieres una vista normal:
        return view('layouts.admin.trabajos.mostrar', compact('trabajo'));
    }

    /**
     * Formulario de edición de un trabajo (ADMIN)
     */
    public function editar(Trabajo $trabajo)
    {
        // Cargamos contexto para mostrar arriba en la vista
        $trabajo->load([
            'presupuesto.solicitud.cliente',
            'presupuesto.solicitud.profesional',
        ]);

        return view('layouts.admin.trabajos.editar', [
            'trabajo' => $trabajo,
        ]);
    }

    /**
     * Actualizar trabajo (ADMIN)
     */
    public function actualizar(Request $request, Trabajo $trabajo)
    {
        // Valores antiguos (para email y reglas)
        $oldEstado   = $trabajo->estado;
        $oldDirObra  = $trabajo->dir_obra;
        $oldFechaIni = $trabajo->fecha_ini;
        $oldFechaFin = $trabajo->fecha_fin;

        // Validación (permitimos los 4 estados)
        $validated = $request->validate(
            [
                'estado'    => 'required|in:previsto,en_curso,finalizado,cancelado',
                'dir_obra'  => 'nullable|string|max:255',
                'fecha_ini' => 'nullable|date',                      // NO required_if, lo controlamos por lógica
                'fecha_fin' => 'nullable|date|after_or_equal:fecha_ini',
            ],
            [
                'estado.required' => 'Debes indicar el estado del trabajo.',
                'estado.in'       => 'El estado seleccionado no es válido.',

                'dir_obra.max'    => 'La dirección de la obra no puede tener más de 255 caracteres.',

                'fecha_ini.date'  => 'La fecha de inicio no tiene un formato válido.',
                'fecha_fin.date'  => 'La fecha de fin no tiene un formato válido.',
                'fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
            ]
        );

        $nuevoEstado = $validated['estado'];

        // Relacionamos ya presupuesto y solicitud para las reglas
        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;

        // 1) No permitir volver a "previsto" si ya ha empezado alguna vez
        if (
            $nuevoEstado === 'previsto'
            && (
                !is_null($oldFechaIni) ||
                in_array($oldEstado, ['en_curso', 'finalizado'])
            )
        ) {
            return back()
                ->withInput()
                ->with('error', 'No puedes volver a poner el trabajo en "previsto" porque ya ha comenzado.');
        }

        // 2) Si queremos ponerlo EN CURSO:
        //    - Debe existir presupuesto
        //    - El presupuesto debe estar "aceptado"
        //    - Debe haber fecha_ini (en el request o ya en BD)
        if ($nuevoEstado === 'en_curso') {

            if (!$presupuesto) {
                return back()
                    ->withInput()
                    ->with('error', 'No puedes poner el trabajo "en curso" porque no tiene presupuesto asociado.');
            }

            if ($presupuesto->estado !== 'aceptado') {
                return back()
                    ->withInput()
                    ->with('error', 'Solo puedes poner el trabajo "en curso" si el presupuesto está aceptado.');
            }

            // Obligamos a que haya fecha_ini (en request o ya guardada)
            $fechaIniForm = $validated['fecha_ini'] ?? null;
            if (is_null($fechaIniForm) && is_null($oldFechaIni)) {
                return back()
                    ->withInput()
                    ->with('error', 'Para iniciar el trabajo debes indicar una fecha de inicio.');
            }
        }

        // 3) Si lo ponemos FINALIZADO desde previsto/en_curso:
        //    - Si no hay fecha_ini, se la ponemos nosotros
        //    - Si no hay fecha_fin, también
        //    (esto lo hacemos justo después al asignar campos)

        // --- ACTUALIZAR CAMPOS DEL TRABAJO ---

        $trabajo->estado   = $nuevoEstado;
        $trabajo->dir_obra = $validated['dir_obra'] ?? null;

        // Fechas según el nuevo estado
        if ($nuevoEstado === 'en_curso') {
            // Si viene del formulario, usamos esa; si no, mantenemos la antigua o ponemos ahora
            if (!empty($validated['fecha_ini'])) {
                $trabajo->fecha_ini = $validated['fecha_ini'];
            } elseif (is_null($trabajo->fecha_ini)) {
                $trabajo->fecha_ini = now();
            }

            // No tocamos fecha_fin aquí
            if (!empty($validated['fecha_fin'])) {
                $trabajo->fecha_fin = $validated['fecha_fin'];
            }
        } elseif ($nuevoEstado === 'finalizado') {

            // Inicio: si viene del form, esa; si no hay ninguna, ahora
            if (!empty($validated['fecha_ini'])) {
                $trabajo->fecha_ini = $validated['fecha_ini'];
            } elseif (is_null($trabajo->fecha_ini)) {
                $trabajo->fecha_ini = now();
            }

            // Fin: si viene del form, esa; si no, ahora
            if (!empty($validated['fecha_fin'])) {
                $trabajo->fecha_fin = $validated['fecha_fin'];
            } else {
                $trabajo->fecha_fin = now();
            }
        } else {
            // previsto o cancelado → usamos lo que venga del form (si viene)
            $trabajo->fecha_ini = $validated['fecha_ini'] ?? $trabajo->fecha_ini;
            $trabajo->fecha_fin = $validated['fecha_fin'] ?? $trabajo->fecha_fin;
        }

        $trabajo->save();

        // --- REGLAS PARA PRESUPUESTO / SOLICITUD ---

        if ($presupuesto) {
            switch ($nuevoEstado) {
                case 'en_curso':
                    // aseguramos que siga aceptado
                    if ($presupuesto->estado !== 'aceptado') {
                        $presupuesto->estado = 'aceptado';
                        $presupuesto->save();
                    }
                    break;

                case 'finalizado':
                    // trabajo terminado ⇒ presupuesto aceptado
                    if ($presupuesto->estado !== 'aceptado') {
                        $presupuesto->estado = 'aceptado';
                        $presupuesto->save();
                    }

                    if ($solicitud && $solicitud->estado !== 'cerrada') {
                        $solicitud->estado = 'cerrada';
                        $solicitud->save();
                    }
                    break;

                case 'cancelado':
                    // Cancelado ⇒ presupuesto rechazado y solicitud cancelada
                    if ($presupuesto->estado !== 'rechazado') {
                        $presupuesto->estado = 'rechazado';
                        $presupuesto->save();
                    }

                    if ($solicitud && $solicitud->estado !== 'cancelada') {
                        $solicitud->estado = 'cancelada';
                        $solicitud->save();
                    }
                    break;

                case 'previsto':
                default:
                    // no tocamos nada
                    break;
            }
        }

        // --- EMAILS ---

        $trabajo->load('presupuesto.solicitud.cliente', 'presupuesto.solicitud.profesional');

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $solicitud?->profesional;

        try {
            if ($cliente && $cliente->email) {
                Mail::to($cliente->email)->send(
                    new TrabajoModificadoPorAdminMailable(
                        $trabajo,
                        $cliente,
                        $perfilPro,
                        $presupuesto,
                        $solicitud,
                        $oldEstado,
                        $oldDirObra,
                        $oldFechaIni,
                        $oldFechaFin,
                        false // paraProfesional
                    )
                );
            }

            if ($perfilPro && $perfilPro->email_empresa) {
                Mail::to($perfilPro->email_empresa)->send(
                    new TrabajoModificadoPorAdminMailable(
                        $trabajo,
                        $cliente,
                        $perfilPro,
                        $presupuesto,
                        $solicitud,
                        $oldEstado,
                        $oldDirObra,
                        $oldFechaIni,
                        $oldFechaFin,
                        true // paraProfesional
                    )
                );
            }
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.trabajos')
                ->with('error', 'El trabajo se ha actualizado, pero no se han podido enviar los correos de aviso.');
        }

        return redirect()
            ->route('admin.trabajos')
            ->with('success', 'Trabajo actualizado correctamente. Se ha notificado al cliente y al profesional.');
    }

    /**
     * Cancelar trabajo (ADMIN)
     */
    public function cancelar(Trabajo $trabajo)
    {
        // Solo tiene sentido cancelar si no está ya cancelado ni finalizado
        if (in_array($trabajo->estado, ['cancelado', 'finalizado'])) {
            return back()->with('error', 'No puedes cancelar un trabajo que ya está finalizado o cancelado.');
        }

        // Guardamos estado / fechas anteriores por si quieres usarlas en el mail
        $oldEstado   = $trabajo->estado;
        $oldFechaIni = $trabajo->fecha_ini;
        $oldFechaFin = $trabajo->fecha_fin;

        // Cambiamos estado a cancelado
        $trabajo->estado = 'cancelado';

        // Opcional: si no tiene fecha_fin, podemos marcarla ahora
        if (! $trabajo->fecha_fin) {
            $trabajo->fecha_fin = now();
        }

        $trabajo->save();

        // Cargamos relaciones para el mail
        $trabajo->load('presupuesto.solicitud.cliente', 'presupuesto.solicitud.profesional');

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $solicitud?->profesional;

        try {
            // Enviar correo al cliente
            if ($cliente && $cliente->email) {
                Mail::to($cliente->email)->send(
                    new TrabajoCanceladoPorAdminMailable(
                        $trabajo,
                        $cliente,
                        $perfilPro,
                        $presupuesto,
                        $solicitud,
                        $oldEstado,
                        $oldFechaIni,
                        $oldFechaFin,
                        false // paraProfesional = false
                    )
                );
            }

            // Enviar correo al profesional
            if ($perfilPro && $perfilPro->email_empresa) {
                Mail::to($perfilPro->email_empresa)->send(
                    new TrabajoCanceladoPorAdminMailable(
                        $trabajo,
                        $cliente,
                        $perfilPro,
                        $presupuesto,
                        $solicitud,
                        $oldEstado,
                        $oldFechaIni,
                        $oldFechaFin,
                        true // paraProfesional = true
                    )
                );
            }
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                'El trabajo se ha cancelado, pero ha habido un problema al enviar los correos de aviso.'
            );
        }

        return back()->with('success', 'Trabajo cancelado correctamente. Cliente y profesional han sido notificados.');
    }

    /**
     * Eliminar trabajo (ADMIN)
     */
    public function eliminarTrabajoAdmin(Trabajo $trabajo)
    {
        // Cargamos relaciones
        $trabajo->load([
            'presupuesto.solicitud.cliente',
            'presupuesto.profesional',
        ]);

        // Guardamos los datots
        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $presupuesto?->profesional;

        $oldEstado   = $trabajo->estado;
        $oldFechaIni = $trabajo->fecha_ini;
        $oldFechaFin = $trabajo->fecha_fin;

        //Cambiamos los estados del presupuesto y de la solicitud
        try {
            if ($presupuesto) {
                $presupuesto->estado = 'rechazado';
                $presupuesto->save();
            }

            if ($solicitud) {
                $solicitud->estado = 'cancelada';
                $solicitud->save();
            }

            // Marcamos el trabajo como cancelado para el estado actual del email
            $trabajo->estado = 'cancelado';

            // Enviamos email a cada 1
            try {
                // Email al cliente
                if ($cliente && $cliente->email) {
                    Mail::to($cliente->email)->send(
                        new TrabajoCanceladoPorAdminMailable(
                            $trabajo,
                            $cliente,
                            $perfilPro,
                            $presupuesto,
                            $solicitud,
                            $oldEstado,
                            $oldFechaIni,
                            $oldFechaFin,
                            false, // paraProfesional
                            true   // esEliminacion condicin
                        )
                    );
                }

                // Email al profesional
                if ($perfilPro && $perfilPro->email_empresa) {
                    Mail::to($perfilPro->email_empresa)->send(
                        new TrabajoCanceladoPorAdminMailable(
                            $trabajo,
                            $cliente,
                            $perfilPro,
                            $presupuesto,
                            $solicitud,
                            $oldEstado,
                            $oldFechaIni,
                            $oldFechaFin,
                            true,  // paraProfesional
                            true   // esEliminacion condicion
                        )
                    );
                }
            } catch (\Throwable $e) {
                return back()->with(
                    'error',
                    'El trabajo se ha eliminado, pero ha fallado el envío de los correos.'
                );
            }

            // Finalmente borramos el trabajo
            $trabajo->delete();

            return back()->with(
                'success',
                'Trabajo eliminado correctamente. El presupuesto se ha marcado como rechazado y la solicitud como cancelada.'
            );
        } catch (\Throwable $e) {
            return back()->with(
                'error',
                'Error al eliminar el trabajo. Revisa los datos o consulta el log.'
            );
        }
    }

    /**
     * Exportar trabajos en excel para admin
     */
    public function exportarTrabajosExcel(Request $request)
    {
        $user = Auth::user();

        // Por si acaso alguien llega aquí sin pasar bien el middleware
        if (!$user) {
            abort(403, 'No tienes permiso para exportar trabajos.');
        }

        $fileName = 'trabajos-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new TrabajosExport($request),
            $fileName
        );
    }
}
