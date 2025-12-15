<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Comentario;
use App\Models\Perfil_Profesional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\ComentarioPublicadoMailable;
use App\Mail\Admin\ComentarioRechazadoMailable;
use App\Mail\Admin\ComentarioOcultadoMailable;
use App\Mail\Admin\ComentarioModificadoPorAdminMailable;
use App\Http\Controllers\Traits\FiltroRangoFechas;
use Mews\Purifier\Facades\Purifier;



class AdminComentarioController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de TODOS los comentarios (sin filtro)
     */
    public function index(Request $request)
    {
        $estado        = $request->query('estado');          // null | pendiente | publicado | rechazado
        $q             = trim((string) $request->query('q')); // texto buscador
        $puntuacionMin = $request->query('puntuacion_min');   // 1–5 o null

        // Estados: usamos la constante del modelo 
        $estados = Comentario::ESTADOS;

        $query = Comentario::with([
            'trabajo.presupuesto.solicitud.cliente',
            'trabajo.presupuesto.profesional',
        ]);

        // --- Filtro por estado (solo si es válido) ---
        if ($estado !== null && $estado !== '') {
            if (array_key_exists($estado, Comentario::ESTADOS)) {
                $query->where('estado', $estado);
            }
        }

        // --- Filtro por texto ---
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {

                // Título de la solicitud
                $sub->whereHas('trabajo.presupuesto.solicitud', function ($qSol) use ($like) {
                    $qSol->where('titulo', 'like', $like);
                })

                    // Cliente: nombre, apellidos, email
                    ->orWhereHas('trabajo.presupuesto.solicitud.cliente', function ($qCli) use ($like) {
                        $qCli->where('nombre', 'like', $like)
                            ->orWhere('apellidos', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })

                    // Profesional: empresa, email empresa
                    ->orWhereHas('trabajo.presupuesto.profesional', function ($qPro) use ($like) {
                        $qPro->where('empresa', 'like', $like)
                            ->orWhere('email_empresa', 'like', $like);
                    })

                    // Opinión del comentario (texto libre)
                    ->orWhere('opinion', 'like', $like)

                    // Estado del comentario (por si escribes "pendiente", etc.)
                    ->orWhere('estado', 'like', $like);
            });
        }

        // --- Filtro por puntuación mínima ---
        if ($puntuacionMin !== null && $puntuacionMin !== '') {
            $query->where('puntuacion', '>=', (int) $puntuacionMin);
        }

        // --- Filtro por rango de fechas (columna fecha del comentario) ---
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

        // --- Orden y paginación ---
        $comentarios = $query
            ->orderByDesc('fecha')
            ->paginate(5)
            ->withQueryString(); // conserva q, estado, fecha_desde, fecha_hasta, puntuacion_min

        return view('layouts.admin.comentarios.index', [
            'comentarios'    => $comentarios,  
            'q'              => $q,
            'estado'         => $estado,
            'estados'        => $estados,
            'puntuacionMin'  => $puntuacionMin,
        ]);
    }

    /**
     * Detalle de un comentario para el modal Vue (JSON) o vista normal.
     */
    public function mostrar(Comentario $comentario)
    {
        // Cargamos relaciones
        $comentario->load([
            'trabajo.presupuesto.solicitud.cliente',
            'trabajo.presupuesto.profesional',
        ]);

        // Guardamos en variables
        $trabajo     = $comentario->trabajo;
        $presupuesto = $trabajo?->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $presupuesto?->profesional;

        // Ventana Modal
        if (request()->wantsJson()) {
            return response()->json([
                'id'         => $comentario->id,
                'trabajo_id' => $trabajo?->id,
                'titulo'     => $solicitud?->titulo,
                'ciudad'     => $solicitud?->ciudad,
                'profesional' => $perfilPro ? [
                    'empresa'  => $perfilPro->empresa,
                    'ciudad'   => $perfilPro->ciudad,
                    'provincia' => $perfilPro->provincia,
                ] : null,

                'total'      => $presupuesto?->total,
                'fecha_ini'  => $trabajo?->fecha_ini
                    ? $trabajo->fecha_ini->format('d/m/Y H:i')
                    : null,
                'fecha_fin'  => $trabajo?->fecha_fin
                    ? $trabajo->fecha_fin->format('d/m/Y H:i')
                    : null,
                'dir_obra'   => $trabajo?->dir_obra,

                'puntuacion'   => $comentario->puntuacion,
                'opinion'      => $comentario->opinion,
                'visible'      => (bool) $comentario->visible,
                'estado'       => $comentario->estado,
                'estado_label' => ucfirst($comentario->estado),
                'fecha'        => $comentario->fecha
                    ? $comentario->fecha->format('d/m/Y H:i')
                    : null,

                'cliente' => $cliente ? [
                    'nombre'    => $cliente->nombre ?? $cliente->name ?? null,
                    'apellidos' => $cliente->apellidos ?? null,
                    'email'     => $cliente->email,
                ] : null,
            ]);
        }

        return view('layouts.admin.comentarios.mostrar', compact('comentario'));
    }

    /**
     * Toggle publicar comentario / despublicar desde el switch.
     */
    public function togglePublicado(Request $request, Comentario $comentario)
    {
        // Cargamos relaciones necesarias
        $comentario->load([
            'cliente',
            'trabajo.presupuesto.solicitud',
            'trabajo.presupuesto.profesional',
        ]);

        $cliente     = $comentario->cliente;
        $trabajo     = $comentario->trabajo;
        $presupuesto = $trabajo?->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $perfilPro   = $presupuesto?->profesional;

        // CASO 1: ya está publicado y visible → lo ocultamos
        if ($comentario->estado === 'publicado' && $comentario->visible) {
            $comentario->estado  = 'pendiente'; // o 'rechazado' si quieres algo más duro
            $comentario->visible = false;
            $comentario->save();

            // Recalcular media tras OCULTAR
            $this->recalcularPuntuacionPerfil($perfilPro);

            if ($cliente && $cliente->email) {
                try {
                    Mail::to($cliente->email)->send(
                        new ComentarioOcultadoMailable(
                            $comentario,
                            $cliente,
                            $trabajo,
                            $perfilPro,
                            $solicitud
                        )
                    );
                } catch (\Throwable $e) {
                    return back()->with(
                        'error',
                        'El comentario se ha ocultado, pero no se ha podido enviar el correo al usuario.'
                    );
                }
            }

            return back()->with('success', 'Comentario ocultado correctamente.');
        }

        // CASO 2: no está publicado+visible → lo publicamos
        $comentario->estado  = 'publicado';
        $comentario->visible = true;
        $comentario->save();

        //  Recalcular media tras PUBLICAR
        $this->recalcularPuntuacionPerfil($perfilPro);

        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new ComentarioPublicadoMailable(
                        $comentario,
                        $cliente,
                        $trabajo,
                        $perfilPro,
                        $solicitud
                    )
                );
            } catch (\Throwable $e) {
                return back()->with(
                    'error',
                    'El comentario se ha publicado, pero no se ha podido enviar el correo al usuario.'
                );
            }
        }

        return back()->with('success', 'Comentario publicado correctamente.');
    }


    /**
     * Rechazar / banear comentario: estado=rechazado, visible=false + email al cliente.
     */
    public function rechazar(Request $request, Comentario $comentario)
    {
        // Cargamos todas las relaciones que necesitamos para el correo
        $comentario->load([
            'cliente',
            'trabajo.presupuesto.solicitud',
            'trabajo.presupuesto.profesional',
        ]);

        // Marcamos el comentario como rechazado y no visible
        $comentario->estado  = 'rechazado';
        $comentario->visible = false;
        $comentario->fecha   = now();
        $comentario->save();

        $cliente     = $comentario->cliente;
        $trabajo     = $comentario->trabajo;
        $presupuesto = $trabajo?->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $perfilPro   = $presupuesto?->profesional;

        // Enviar correo al usuario avisando del rechazo
        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new ComentarioRechazadoMailable(
                        $comentario,
                        $cliente,
                        $trabajo,
                        $perfilPro,
                        $solicitud
                    )
                );
            } catch (\Throwable $e) {
                // El comentario ya está rechazado; solo informamos del fallo de correo
                return back()->with(
                    'error',
                    'El comentario se ha rechazado, pero no se ha podido enviar el correo al usuario.'
                );
            }
        }

        return back()->with('success', 'Comentario rechazado y usuario notificado.');
    }

    /**
     * Editar comentario (formulario).
     */
    public function editar(Comentario $comentario)
    {
        // Cargamos relaciones para contexto en la vista
        $comentario->load('trabajo.presupuesto.solicitud.cliente', 'trabajo.presupuesto.profesional');
        return view('layouts.admin.comentarios.editar', compact('comentario'));
    }

    /**
     * Actualizar comentario como ADMIN.
     * No cambia estado ni visible: solo contenido y puntuación.
     * Envía email informando al usuario.
     */
    public function actualizar(Request $request, Comentario $comentario)
    {
        // Validación
        $validated = $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'opinion'    => 'nullable|string|max:2000',
        ]);

        // Guardamos datos antiguos por si los usamos en el mail
        $oldOpinion    = $comentario->opinion;
        $oldPuntuacion = $comentario->puntuacion;

        // Limpiar opinión con Purifier (perfil "solicitud" como en el resto)
        $opinion = $validated['opinion'];

        $opinion_limpia = $opinion
            ? Purifier::clean($opinion, 'solicitud')
            : null;

        // Actualizar comentario
        $comentario->puntuacion = $validated['puntuacion'];
        $comentario->opinion    = $opinion_limpia;
        // mantenemos estado y visible como están
        $comentario->fecha      = now(); // fecha de última revisión
        $comentario->save();

        // Cargar relaciones para el email
        $comentario->load(
            'trabajo.presupuesto.solicitud.cliente',
            'trabajo.presupuesto.profesional'
        );

        $trabajo   = $comentario->trabajo;
        $presu     = $trabajo?->presupuesto;
        $solicitud = $presu?->solicitud;
        $cliente   = $solicitud?->cliente;
        $perfilPro = $presu?->profesional;

        //  Recalcular media tras PUBLICAR
        $this->recalcularPuntuacionPerfil($perfilPro);

        // Enviar mail al usuario avisando de la modificación
        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new ComentarioModificadoPorAdminMailable(
                        $comentario,
                        $cliente,
                        $trabajo,
                        $perfilPro,
                        $oldOpinion,
                        $oldPuntuacion,
                        $solicitud
                    )
                );
            } catch (\Throwable $e) {
                return redirect()
                    ->route('admin.comentarios')
                    ->with('error', 'El comentario se ha actualizado, pero no se ha podido enviar el correo al usuario.');
            }
        }

        return redirect()
            ->route('admin.comentarios')
            ->with('success', 'Comentario actualizado correctamente. El usuario ha sido notificado de la modificación.');
    }

    /**
     * Recalcular la puntuación media de un perfil profesional
     * en base a TODOS los comentarios publicados y visibles
     * que tenga asociados.
     */
    private function recalcularPuntuacionPerfil(?Perfil_Profesional $perfilPro): void
    {
        if (! $perfilPro) {
            return;
        }

        // Filtramos por estado
        $media = Comentario::where('estado', 'publicado')
            ->where('visible', true)
            ->whereHas('trabajo.presupuesto', function ($q) use ($perfilPro) {
                // Definimos relaciones
                $q->where('pro_id', $perfilPro->id);
            })
            ->avg('puntuacion'); // Media aritmetica de los comentarios del profesional

        $perfilPro->puntuacion_media = $media ?? 0;
        $perfilPro->save();
    }
}
