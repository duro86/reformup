<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FiltroRangoFechas;


class ProfesionalSolicitudController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de solicitudes recibidas por el profesional logueado.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()
                ->route('home')
                ->with('error', 'No puedes acceder a las solicitudes sin un perfil profesional.');
        }

        $estado = $request->query('estado');             // abierta / en_revision / cerrada / cancelada / null
        $q      = trim((string) $request->query('q'));   // texto buscador

        // Estados disponibles desde el modelo (sin "Todas")
        $estados = Solicitud::ESTADOS;

        // Base: SOLO solicitudes dirigidas a este profesional
        $query = Solicitud::with(['cliente'])
            ->where('pro_id', $perfil->id);

        // Filtro por estado (solo si es válido)
        if ($estado !== null && $estado !== '') {
            if (array_key_exists($estado, Solicitud::ESTADOS)) {
                $query->where('estado', $estado);
            }
        }

        // Filtro por buscador
        if ($q !== '') {
            $qLike = '%' . $q . '%';

            $query->where(function ($sub) use ($qLike) {
                $sub->where('titulo', 'like', $qLike)
                    ->orWhere('ciudad', 'like', $qLike)
                    ->orWhere('provincia', 'like', $qLike)
                    ->orWhere('estado', 'like', $qLike)
                    ->orWhereHas('cliente', function ($q2) use ($qLike) {
                        $q2->where('nombre', 'like', $qLike)
                            ->orWhere('apellidos', 'like', $qLike)
                            ->orWhere('email', 'like', $qLike);
                    });
            });
        }

        // Filtro por rango de fechas (columna fecha de la solicitud)
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

        $solicitudes = $query
            ->orderByDesc('fecha')
            ->paginate(5)
            ->withQueryString();

        return view('layouts.profesional.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'estado'      => $estado,
            'q'           => $q,
            'estados'     => $estados,   // sin null, solo los del modelo
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
