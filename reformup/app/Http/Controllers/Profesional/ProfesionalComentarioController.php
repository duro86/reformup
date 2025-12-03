<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FiltroRangoFechas;


class ProfesionalComentarioController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de comentarios que afectan a este profesional.
     * SOLO muestra comentarios publicados y visibles.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()
                ->route('home')
                ->with('error', 'No tienes permiso para acceder a esta zona.');
        }

        // Texto de búsqueda
        $q = trim((string) $request->query('q'));

        // Puntuación mínima (1-5) en string desde la request
        $puntuacionMin = $request->query('puntuacion_min');

        // Base: solo comentarios de este profesional, publicados y visibles
        $query = Comentario::with([
            'trabajo.presupuesto.solicitud.cliente',
            'trabajo.presupuesto.profesional',
        ])
            ->whereHas('trabajo.presupuesto', function ($qRel) use ($perfil) {
                $qRel->where('pro_id', $perfil->id);
            })
            ->where('estado', 'publicado')
            ->where('visible', true);

        // 🔍 Buscador por texto
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                // Título de la solicitud
                $sub->whereHas('trabajo.presupuesto.solicitud', function ($qSol) use ($like) {
                    $qSol->where('titulo', 'like', $like);
                })
                    // Cliente
                    ->orWhereHas('trabajo.presupuesto.solicitud.cliente', function ($qCli) use ($like) {
                        $qCli->where('nombre', 'like', $like)
                            ->orWhere('apellidos', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })
                    // Profesional 
                    ->orWhereHas('trabajo.presupuesto.profesional', function ($qPro) use ($like) {
                        $qPro->where('empresa', 'like', $like);
                    })
                    // Opinión
                    ->orWhere('opinion', 'like', $like);
            });
        }

        //  Filtro por puntuación mínima (validando rango 1–5)
        if ($puntuacionMin !== null && $puntuacionMin !== '') {
            $pMin = (int) $puntuacionMin;

            if ($pMin >= 1 && $pMin <= 5) {
                $query->where('puntuacion', '>=', $pMin);
            }
        }

        //  Filtro por rango de fechas (usamos la columna fecha del comentario)
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

        $comentarios = $query
            ->orderByDesc('fecha')
            ->paginate(6)
            ->withQueryString(); // mantiene q + fechas + puntuación en la paginación

        // Para mantener la firma de otras vistas (aunque aquí no filtramos por estado)
        $estado  = null;
        $estados = [];

        return view('layouts.profesional.comentarios.index', compact(
            'comentarios',
            'estado',
            'estados',
            'q',
            'puntuacionMin'
        ));
    }


    /**
     * Mostrar comentario concreto al profesional (modal JSON o vista normal).
     * Solo permite ver comentarios suyos, publicados y visibles.
     */
    public function mostrar(Comentario $comentario)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()
                ->route('home')
                ->with('error', 'No tienes permiso para acceder a esta zona.');
        }

        // Cargamos relaciones necesarias
        $comentario->load([
            'trabajo.presupuesto.solicitud.cliente',
            'trabajo.presupuesto.profesional',
        ]);

        $trabajo     = $comentario->trabajo;
        $presupuesto = $trabajo?->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $presupuesto?->profesional;

        // 1) Seguridad: que el trabajo/presupuesto sea de este profesional
        if (! $perfilPro || $perfilPro->id !== $perfil->id) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }

            return redirect()
                ->back()
                ->with('error', 'No puedes ver los comentarios de otros profesionales.');
        }

        // 2) Solo comentarios publicados + visibles
        if (! $comentario->visible || $comentario->estado !== 'publicado') {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Este comentario no está disponible.'], 404);
            }

            return redirect()
                ->back()
                ->with('error', 'Este comentario no está disponible.');
        }

        // 3) Respuesta JSON para el modal Vue
        if (request()->wantsJson()) {
            return response()->json([
                'id'         => $comentario->id,
                'trabajo_id' => $trabajo?->id,
                'titulo'     => $solicitud?->titulo,
                'ciudad'     => $solicitud?->ciudad,

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
                'estado_label' => $comentario->estado
                    ? ucfirst($comentario->estado)
                    : null,
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

        // 4) Fallback a vista normal (solo si en algún momento la usas)
        return view('layouts.profesional.comentarios.mostrar', compact('comentario'));
    }
}
