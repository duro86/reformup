<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfesionalComentarioController extends Controller
{
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

        // Solo comentarios de trabajos suyos + publicados + visibles
        $comentarios = Comentario::with([
            'trabajo.presupuesto.solicitud.cliente',
            'trabajo.presupuesto.profesional',
        ])
            ->whereHas('trabajo.presupuesto', function ($q) use ($perfil) {
                $q->where('pro_id', $perfil->id);
            })
            ->where('estado', 'publicado')
            ->where('visible', true)
            ->orderByDesc('fecha')
            ->paginate(6);

        // Para la vista (el filtro realmente solo tiene sentido en "publicados")
        $estado  = null;
        $estados = [
            'publicado' => 'Publicados',
        ];

        return view('layouts.profesional.comentarios.index', compact(
            'comentarios',
            'estado',
            'estados',
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
