<?php

// app/Http/Controllers/Admin/AdminComentarioController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\ComentarioPublicadoMailable;
use App\Mail\Admin\ComentarioRechazadoMailable;

class AdminComentarioController extends Controller
{
    /**
     * Listado de TODOS los comentarios (sin filtro)
     */
    public function index()
    {   
        // Lista Comentarios
        $comentarios = Comentario::with([
            'trabajo.presupuesto.solicitud.cliente',
            'trabajo.presupuesto.profesional',
        ])
            ->orderByDesc('fecha')
            ->paginate(5);

        return view('layouts.admin.comentarios.index', compact('comentarios'));
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
     * Toggle publicar / despublicar desde el switch.
     */
    public function togglePublicado(Request $request, Comentario $comentario)
    {

        // Cragamos relaciones
        $comentario->load('cliente', 'trabajo.presupuesto.solicitud');

        // Si ya está publicado+visible → lo pasamos a pendiente y no visible
        if ($comentario->estado === 'publicado' && $comentario->visible) {
            $comentario->estado  = 'pendiente';
            $comentario->visible = false;
            $comentario->save();

            return back()->with('success', 'Comentario despublicado.');
        }

        // Si no lo está → lo publicamos y marcamos visible
        $comentario->estado  = 'publicado';
        $comentario->visible = true;
        $comentario->save();

        $cliente   = $comentario->cliente;
        $trabajo   = $comentario->trabajo;
        $perfilPro = $trabajo?->presupuesto?->profesional;

        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new ComentarioPublicadoMailable($comentario, $cliente, $trabajo, $perfilPro)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'No se pudo enviar el mail al cliente.');
            }
        }

        return back()->with('success', 'Comentario publicado correctamente.');
    }

    /**
     * Rechazar / banear comentario: estado=rechazado, visible=false + email al cliente.
     */
    public function rechazar(Request $request, Comentario $comentario)
    {
        $this->authorize('admin');

        $comentario->load('cliente', 'trabajo.presupuesto.solicitud');

        $comentario->estado  = 'rechazado';
        $comentario->visible = false;
        $comentario->save();

        $cliente   = $comentario->cliente;
        $trabajo   = $comentario->trabajo;
        $perfilPro = $trabajo?->presupuesto?->profesional;

        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new ComentarioRechazadoMailable($comentario, $cliente, $trabajo, $perfilPro)
                );
            } catch (\Throwable $e) {
                // Log si quieres
            }
        }

        return back()->with('success', 'Comentario rechazado y usuario notificado.');
    }

    /**
     * Editar comentario (formulario).
     */
    public function editar(Comentario $comentario)
    {
        return view('layouts.admin.comentarios.edit', compact('comentario'));
    }

    /**
     * Guardar edición de comentario.
     */
    public function actualizar(Request $request, Comentario $comentario)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'opinion'    => 'nullable|string|max:2000',
        ]);

        $comentario->puntuacion = $validated['puntuacion'];
        $comentario->opinion    = $validated['opinion'] ?? null;
        // no tocamos estado/visible aquí; eso va con switch / rechazar
        $comentario->save();

        return redirect()
            ->route('admin.comentarios.index')
            ->with('success', 'Comentario actualizado correctamente.');
    }
}
