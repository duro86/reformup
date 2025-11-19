<?php

// app/Http/Controllers/Usuario/UsuarioComentarioController.php
namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use App\Models\User;
use App\Models\Comentario;
use App\Mail\Admin\ComentarioPendienteMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioComentarioController extends Controller
{

    /**
     * Listado de comentarios del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        $comentarios = Comentario::with([
            'trabajo.presupuesto.solicitud',
            'trabajo.presupuesto.profesional',
        ])
            ->where('cliente_id', $user->id)
            ->orderByDesc('fecha')
            ->paginate(5);

        return view('layouts.usuario.comentarios.index', compact('comentarios', 'user'));
    }

    /**
     * Formulario para dejar comentario de un trabajo finalizado.
     */
    /**
     * Formulario para dejar comentario de un trabajo finalizado.
     */
    public function crear(Trabajo $trabajo)
    {
        $user = Auth::user();

        // Cargamos presupuesto y solicitud del trabajo
        $trabajo->load('presupuesto.solicitud');

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;

        // El trabajo debe ser suyo y debe haber solicitado el trabajo y presupuesto asociado
        if (! $solicitud || $solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes valorar trabajos de otros usuarios.');
        }

        // Solo trabajos finalizados
        if ($trabajo->estado !== 'finalizado') {
            return back()->with('error', 'Solo puedes comentar trabajos finalizados.');
        }

        // Evitar duplicados
        $yaComentado = Comentario::where('trabajo_id', $trabajo->id)
            ->where('cliente_id', $user->id)
            ->exists();

        if ($yaComentado) {
            return back()->with('error', 'Ya has dejado un comentario para este trabajo.');
        }

        return view('layouts.usuario.comentarios.crear', compact('trabajo'));
    }

    /**
     * Guardar comentario.
     */
    public function guardar(Request $request, Trabajo $trabajo)
    {
        $user = Auth::user();

        // Cargar presupuesto y solicitud
        $trabajo->load('presupuesto.solicitud');

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;

        // 1) El trabajo debe ser suyo
        if (! $solicitud || $solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes valorar trabajos de otros usuarios.');
        }

        // 2) Solo trabajos finalizados
        if ($trabajo->estado !== 'finalizado') {
            return back()->with('error', 'Solo puedes comentar trabajos finalizados.');
        }

        // 3) Evitar duplicados
        $yaComentado = Comentario::where('trabajo_id', $trabajo->id)
            ->where('cliente_id', $user->id)
            ->exists();

        if ($yaComentado) {
            return back()->with('error', 'Ya has dejado un comentario para este trabajo.');
        }

        // 4) Validación
        $validated = $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'opinion'    => 'nullable|string|max:2000',
        ]);

        // 5) Crear comentario
        $comentario = Comentario::create([
            'trabajo_id' => $trabajo->id,
            'cliente_id' => $user->id,
            'puntuacion' => $validated['puntuacion'],
            'opinion'    => $validated['opinion'] ?? null,
            'estado'     => 'pendiente',
            'visible'    => false,
            'fecha'      => now(),
        ]);

        // Cargamos profesional (perfil) por si lo quieres usar en el correo
        $profesional = $presupuesto?->profesional ?? null;

        // 6) Enviar mail a todos los admins avisando del nuevo comentario pendiente
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            if (! $admin->email) {
                continue;
            }

            try {
                Mail::to($admin->email)->send(
                    new ComentarioPendienteMailable($comentario, $trabajo, $user, $profesional)
                );
            } catch (\Throwable $e) {
                // Redirigimos con error:
                return back()->with('error', 'Fallo al notificar al administrador sobre el nuevo comentario.');
                // \Log::error('Error enviando mail comentario pendiente: '.$e->getMessage());
            }
        }

        return redirect()
            ->route('usuario.trabajos.index')
            ->with('success', 'Tu comentario se ha enviado y está pendiente de revisión por el administrador.');
    }

    /**
     * Formulario para editar un comentario propio.
     */
    public function editar(Comentario $comentario)
    {
        $user = Auth::user();

        // Solo su propio comentario
        if ($comentario->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes editar los comentarios de otros usuarios.');
        }

        // Solo permitir edición si está pendiente o rechazado
        if (! in_array($comentario->estado, ['pendiente', 'rechazado'])) {
            return redirect()
                ->route('usuario.comentarios.index')
                ->with('error', 'Solo puedes editar comentarios pendientes o rechazados.');
        }

        // Cargar relaciones necesarias
        $comentario->load('trabajo.presupuesto.solicitud');

        return view('layouts.usuario.comentarios.editar', compact('comentario'));
    }

    /**
     * Actualizar comentario propio.
     * Al editar, lo volvemos a poner en "pendiente" y oculto.
     */
    public function actualizar(Request $request, Comentario $comentario)
    {
        $user = Auth::user();

        // 1) El comentario debe ser suyo
        if ($comentario->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes editar los comentarios de otros usuarios.');
        }

        // 2) Solo se puede editar si está pendiente o rechazado
        if (! in_array($comentario->estado, ['pendiente', 'rechazado'])) {
            return redirect()
                ->route('usuario.comentarios.index')
                ->with('error', 'Solo puedes editar comentarios pendientes o rechazados.');
        }

        // 3) Validación
        $validated = $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'opinion'    => 'nullable|string|max:2000',
        ]);

        // 4) Actualizar comentario
        $comentario->puntuacion = $validated['puntuacion'];
        $comentario->opinion    = $validated['opinion'] ?? null;
        $comentario->estado     = 'pendiente';  // vuelve a revisión
        $comentario->visible    = false;
        $comentario->fecha      = now();
        $comentario->save();

        // 5) Cargamos el trabajo y el profesional para el correo
        $trabajo = $comentario->trabajo; // relación comentario -> trabajo

        $presupuesto  = null;
        $profesional  = null;

        // Cargamos relaciones necesarias
        if ($trabajo) {
            $trabajo->load('presupuesto.profesional', 'presupuesto.solicitud');
            $presupuesto = $trabajo->presupuesto;
            $profesional = $presupuesto?->profesional;
        }

        // 6) Enviar mail a todos los admins avisando de comentario editado y pendiente
        $admins = User::role('admin')->get();

        // Enviar email a todos los admins avisando del nuevo comentario pendiente
        foreach ($admins as $admin) {
            if (! $admin->email) {
                continue;
            }

            try {
                Mail::to($admin->email)->send(
                    new ComentarioPendienteMailable($comentario, $trabajo, $user, $profesional)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'Fallo al notificar al administrador sobre el comentario editado.');
            }
        }

        return redirect()
            ->route('usuario.comentarios.index')
            ->with('success', 'Tu comentario se ha actualizado y está pendiente de revisión.');
    }
}
