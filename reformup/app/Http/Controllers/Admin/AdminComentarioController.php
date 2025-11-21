<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\ComentarioPublicadoMailable;
use App\Mail\Admin\ComentarioRechazadoMailable;
use App\Mail\Admin\ComentarioOcultadoMailable;

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
     * Guardar edición de comentario.
     */
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

        // Guardamos datos antiguos por si quieres usarlos en el mail
        $oldOpinion    = $comentario->opinion;
        $oldPuntuacion = $comentario->puntuacion;

        // Actualizar comentario
        $comentario->puntuacion = $validated['puntuacion'];
        $comentario->opinion    = $validated['opinion'] ?? null;
        // mantenemos estado y visible como están
        $comentario->fecha      = now(); // fecha de última revisión
        $comentario->save();

        // Cargar relaciones para el email
        $comentario->load('trabajo.presupuesto.solicitud.cliente', 'trabajo.presupuesto.profesional');

        $trabajo   = $comentario->trabajo;
        $presu     = $trabajo?->presupuesto;
        $solicitud = $presu?->solicitud;
        $cliente   = $solicitud?->cliente;
        $perfilPro = $presu?->profesional;

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
                        $oldPuntuacion
                    )
                );
            } catch (\Throwable $e) {
                // El comentario ya está guardado; avisamos del fallo de correo
                return redirect()
                    ->route('admin.comentarios')
                    ->with('error', 'El comentario se ha actualizado, pero no se ha podido enviar el correo al usuario.');
            }
        }

        return redirect()
            ->route('admin.comentarios')
            ->with('success', 'Comentario actualizado correctamente. El usuario ha sido notificado de la modificación.');
    }
}
