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

        $estados = [null => 'Todas'] + Trabajo::ESTADOS;

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
        // Aquí tiene sentido usar 'fecha_ini' como referencia del trabajo
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha_ini');

        $trabajos = $query
            ->orderByDesc('fecha_ini')
            ->paginate(5)
            ->withQueryString();         // mantiene q, estado, fecha_desde, fecha_hasta

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
        // Valores antiguos
        $oldEstado   = $trabajo->estado;
        $oldDirObra  = $trabajo->dir_obra;
        $oldFechaIni = $trabajo->fecha_ini;
        $oldFechaFin = $trabajo->fecha_fin;

        // Validación
        $validated = $request->validate(
            [
                'estado'    => 'required|in:previsto,en_curso,finalizado,cancelado',
                'dir_obra'  => 'nullable|string|max:255',
                'fecha_ini' => 'nullable|date',
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

        // Actualizar trabajo
        $trabajo->estado   = $validated['estado'];
        $trabajo->dir_obra = $validated['dir_obra'] ?? null;
        $trabajo->fecha_ini = $validated['fecha_ini'] ?? null;
        $trabajo->fecha_fin = $validated['fecha_fin'] ?? null;
        $trabajo->save();

        // --- REGLAS DE NEGOCIO LIGADAS AL ESTADO ---

        $presupuesto = $trabajo->presupuesto;          // relación trabajo -> presupuesto
        $solicitud   = $presupuesto?->solicitud;       // relación presupuesto -> solicitud

        if ($presupuesto) {
            switch ($trabajo->estado) {

                // 1) Si pongo el trabajo EN CURSO → presupuesto pasa a ACEPTADO
                case 'en_curso':
                    if ($presupuesto->estado !== 'aceptado') {
                        $presupuesto->estado = 'aceptado';
                        $presupuesto->save();
                    }
                    break;

                // 2) Si pongo FINALIZADO:
                case 'finalizado':
                    if (is_null($trabajo->fecha_ini)) {
                        // Finalizado sin haber empezado realmente → consideramos el presupuesto como RECHAZADO
                        if ($presupuesto->estado !== 'rechazado') {
                            $presupuesto->estado = 'rechazado';
                            $presupuesto->save();
                        }
                    } else {
                        // Finalizado habiendo empezado → nos aseguramos de que el presupuesto quede como ACEPTADO
                        if ($presupuesto->estado !== 'aceptado') {
                            $presupuesto->estado = 'aceptado';
                            $presupuesto->save();
                        }
                    }
                    break;

                // 3) Si cancelo el trabajo → presupuesto RECHAZADO
                case 'cancelado':
                    if ($presupuesto->estado !== 'rechazado') {
                        $presupuesto->estado = 'rechazado';
                        $presupuesto->save();
                    }

                    // opcional: cancelar también la solicitud si quieres
                    if ($solicitud && $solicitud->estado !== 'cancelada') {
                        $solicitud->estado = 'cancelada';
                        $solicitud->save();
                    }

                    break;

                // 4) PREVISTO → no tocamos nada especial
                case 'previsto':
                default:
                    // no hacemos nada
                    break;
            }
        }

        // Cargar relaciones para el email
        $trabajo->load('presupuesto.solicitud.cliente', 'presupuesto.solicitud.profesional');

        $presupuesto = $trabajo->presupuesto;          // usa presu_id internamente
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $solicitud?->profesional;

        try {
            // Cliente
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

            // Profesional
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
