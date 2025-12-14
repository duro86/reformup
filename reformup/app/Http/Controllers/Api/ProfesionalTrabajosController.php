<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use Illuminate\Http\Request;

class ProfesionalTrabajosController extends Controller
{
    /**
     * GET /api/profesional/trabajos
     * Devuelve trabajos del profesional autenticado (Sanctum).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->hasRole('profesional')) {
            return response()->json(['message' => 'No tienes rol profesional.'], 403);
        }

        $perfil = $user->perfil_Profesional;
        if (! $perfil) {
            return response()->json(['message' => 'No tienes un perfil profesional asociado.'], 409);
        }

        $trabajos = Trabajo::with([
            'presupuesto.solicitud.cliente',
            'presupuesto.profesional',
        ])
        ->whereHas('presupuesto', function ($q) use ($perfil) {
            $q->where('pro_id', $perfil->id);
        })
        ->orderByDesc('created_at')
        ->get();

        $data = $trabajos->map(function (Trabajo $trabajo) {
            $presu     = $trabajo->presupuesto;
            $solicitud = $presu?->solicitud;
            $cliente   = $solicitud?->cliente;

            return [
                'id'        => 'InfoTrabajo',
                'estado'    => $trabajo->estado,
                'fecha_ini' => optional($trabajo->fecha_ini)->format('Y-m-d'),
                'fecha_fin' => optional($trabajo->fecha_fin)->format('Y-m-d'),
                'dir_obra'  => $trabajo->dir_obra,
                'presupuesto' => [
                    'id'    => $presu?->id,
                    'total' => $presu?->total,
                ],
                'solicitud' => [
                    'id'        => 'InfoPresupuesto',
                    'titulo'    => $solicitud?->titulo,
                    'ciudad'    => $solicitud?->ciudad,
                    'provincia' => $solicitud?->provincia,
                ],
                'cliente' => [
                    'id'        => 'InfoCliente',
                    'nombre'    => $cliente?->nombre ?? $cliente?->name,
                    'apellidos' => $cliente?->apellidos,
                    'email'     => $cliente?->email,
                    'telefono'  => $cliente?->telefono,
                ],
            ];
        });

        return response()->json(['data' => $data]);
    }
}
