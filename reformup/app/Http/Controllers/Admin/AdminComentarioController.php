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
     * Listado de comentarios con filtro por estado.
     */
    public function index(Request $request)
    {
        // Filtro por estado
        $estado = $request->query('estado');

        // Consulta con filtros
        $comentarios = Comentario::with([
            'cliente',
            'trabajo.presupuesto.profesional',
            'trabajo.presupuesto.solicitud',
        ])
            ->when($estado, function ($q) use ($estado) {
                $q->where('estado', $estado);
            })
            ->orderByDesc('fecha')
            ->paginate(10);

        //Retorna la vista con los comentarios y el estado seleccionado
        return view('layouts.admin.comentarios.index', compact('comentarios', 'estado'));
    }

    /**
     * Publicar comentario por parte del Admin.
     */
    public function publicar(Comentario $comentario)
    {
        // Solo comentarios en estado pendiente
        if ($comentario->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes publicar comentarios en estado pendiente.');
        }

        // Actualiza el estado y la visibilidad
        $comentario->estado  = 'publicado';
        $comentario->visible = true;
        $comentario->save();

        // Enviar notificación al cliente
        $cliente   = $comentario->cliente;
        $trabajo   = $comentario->trabajo;
        $presupuesto = $trabajo?->presupuesto;
        $perfilPro = $presupuesto?->profesional;

        // Si el cliente tiene email, enviar notificación
        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new ComentarioPublicadoMailable($comentario, $cliente, $trabajo, $perfilPro)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'Fallo al publicar comentario.');
            }
        }

        return back()->with('success', 'Comentario publicado correctamente.');
    }

    /**
     * Rechazar comentario por parte del Admin.
     */
    public function rechazar(Comentario $comentario)
    {
        if ($comentario->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes rechazar comentarios en estado pendiente.');
        }

        $comentario->estado  = 'rechazado';
        $comentario->visible = false;
        $comentario->save();

        $cliente   = $comentario->cliente;
        $trabajo   = $comentario->trabajo;
        $presupuesto = $trabajo?->presupuesto;
        $perfilPro = $presupuesto?->profesional;

        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new ComentarioRechazadoMailable($comentario, $cliente, $trabajo, $perfilPro)
                );
            } catch (\Throwable $e) {
                // log opcional
            }
        }

        return back()->with('success', 'Comentario rechazado correctamente.');
    }
}
