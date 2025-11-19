<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfesionalSolicitudController extends Controller
{
    /**
     * Listado de solicitudes recibidas por el profesional logueado.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional; // relación 1:1

        if (! $perfil) {
            abort(403, 'No tienes perfil profesional asociado.');
        }

        $estado = $request->query('estado'); // null | abierta | en_revision | cerrada | cancelada

        $query = Solicitud::with(['cliente', 'profesional'])
            ->where('pro_id', $perfil->id)
            ->orderByDesc('fecha');

        if ($estado) {
            $query->where('estado', $estado);
        }

        $solicitudes = $query->paginate(10)->withQueryString();

        return view('layouts.profesional.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'estado'      => $estado,
            'perfil'      => $perfil,
        ]);
    }

    /**
     * Detalle de una solicitud (para modal Vue o vista normal).
     */
    public function mostrar(Solicitud $solicitud)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil || $solicitud->pro_id !== $perfil->id) {
            abort(403, 'No tienes acceso a esta solicitud.');
        }

        $solicitud->load(['cliente', 'profesional']);

        // Si la petición pide JSON (Vue modal)
        if (request()->wantsJson()) {
            return response()->json([
                'id'              => $solicitud->id,
                'titulo'          => $solicitud->titulo,
                'descripcion'     => $solicitud->descripcion,
                'ciudad'          => $solicitud->ciudad,
                'provincia'       => $solicitud->provincia,
                'dir_empresa'     => $solicitud->dir_empresa,
                'estado'          => $solicitud->estado,
                'presupuesto_max' => $solicitud->presupuesto_max,
                'fecha'           => optional($solicitud->fecha)->format('d/m/Y H:i'),
                'cliente'         => [
                    'nombre'    => $solicitud->cliente?->nombre,
                    'apellidos' => $solicitud->cliente?->apellidos,
                    'email'     => $solicitud->cliente?->email,
                    'telefono'  => $solicitud->cliente?->telefono,
                ],
            ]);
        }

        // Fallback por si quieres una vista normal
        return view('layouts.profesional.solicitudes.show', compact('solicitud'));
    }

    /**
     * Cancelar una solicitud (cambiar estado a cancelada).
     */
    public function cancelar(Solicitud $solicitud)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil || $solicitud->pro_id !== $perfil->id) {
            return back()->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        if (! in_array($solicitud->estado, ['abierta', 'en_revision'])) {
            return back()->with('error', 'Solo puedes cancelar solicitudes abiertas o en revisión.');
        }

        $solicitud->estado = 'cancelada';
        $solicitud->save();

        return back()->with('success', 'La solicitud se ha cancelado correctamente.');
    }
}
