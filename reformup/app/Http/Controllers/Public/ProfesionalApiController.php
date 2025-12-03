<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Perfil_Profesional;
use Illuminate\Http\Request;
use App\Models\Trabajo;

class ProfesionalApiController extends Controller
{
    /**
     * Listado público de profesionales con filtros.
     * Endpoint: GET /api/profesionales
     */
    public function index(Request $request)
    {

        //dd('api profesionales OK');
        $query = Perfil_Profesional::query()
            ->where('visible', 1);

        // Filtros
        if ($empresa = $request->get('empresa')) {
            $query->where('empresa', 'like', '%' . $empresa . '%');
        }

        if ($ciudad = $request->get('ciudad')) {
            $query->where('ciudad', 'like', '%' . $ciudad . '%');
        }

        if ($provincia = $request->get('provincia')) {
            $query->where('provincia', 'like', '%' . $provincia . '%');
        }

        if (!is_null($request->get('min_rating'))) {
            $minRating = (float) $request->get('min_rating');
            $query->whereNotNull('puntuacion_media')
                ->where('puntuacion_media', '>=', $minRating);
        }

        // Paginación
        $perPage = (int) $request->get('per_page', 8);
        $perPage = max(1, min($perPage, 24)); // por si acaso no se nos va de madre

        $paginator = $query
            ->orderByDesc('puntuacion_media')
            ->orderBy('empresa')
            ->paginate($perPage);

        // Devolvemos solo los campos que necesitas
        $data = $paginator->getCollection()->transform(function ($perfil) {
            return [
                'id'               => $perfil->id,
                'empresa'          => $perfil->empresa,
                'ciudad'           => $perfil->ciudad,
                'provincia'        => $perfil->provincia,
                'email_empresa'    => $perfil->email_empresa,
                'telefono_empresa' => $perfil->telefono_empresa,
                'web'              => $perfil->web,
                'puntuacion_media' => $perfil->puntuacion_media,
                'avatar'           => $perfil->avatar,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    /**
     * Detalle de un profesional (por si lo quieres consultar por API).
     * Endpoint: GET /api/profesionales/{perfil}
     */
    public function mostrar(Perfil_Profesional $perfil)
    {
        if (! $perfil->visible) {
            return redirect()
                ->back()
                ->with('error', 'Perfil del profesional no visible en la plataforma.');
        }

        return response()->json($perfil);
    }

    /**
     * Devuelve en JSON los trabajos del profesional autenticado.
     * Endpoint: GET /api/profesional/trabajos
     */
    public function misTrabajos(Request $request)
    {
        $user = $request->user(); // usuario autenticado por Sanctum

        // 1) Comprobar que el usuario tiene rol profesional
        if (! $user->hasRole('profesional')) {
            return response()->json([
                'message' => 'No tienes rol profesional.',
            ], 403);
        }

        // 2) Comprobar que tiene perfil profesional asociado
        $perfil = $user->perfil_Profesional;
        if (! $perfil) {
            return response()->json([
                'message' => 'No tienes un perfil profesional asociado.',
            ], 404);
        }

        // 3) Cargar trabajos asociados a ese profesional
        $trabajos = Trabajo::with([
            'presupuesto.solicitud.cliente',
            'presupuesto.profesional',
        ])
            ->whereHas('presupuesto', function ($q) use ($perfil) {
                $q->where('pro_id', $perfil->id);
            })
            ->orderByDesc('created_at')
            ->get();

        // 4) Transformar los datos a un formato JSON más compacto
        $data = $trabajos->map(function (Trabajo $trabajo) {
            $presu     = $trabajo->presupuesto;
            $solicitud = $presu?->solicitud;
            $cliente   = $solicitud?->cliente;

            return [
                'id'        => $trabajo->id,
                'estado'    => $trabajo->estado,
                'fecha_ini' => optional($trabajo->fecha_ini)->format('Y-m-d'),
                'fecha_fin' => optional($trabajo->fecha_fin)->format('Y-m-d'),
                'dir_obra'  => $trabajo->dir_obra,
                'presupuesto' => [
                    'id'    => $presu?->id,
                    'total' => $presu?->total,
                ],
                'solicitud'   => [
                    'id'       => $solicitud?->id,
                    'titulo'   => $solicitud?->titulo,
                    'ciudad'   => $solicitud?->ciudad,
                    'provincia' => $solicitud?->provincia,
                ],
                'cliente'     => [
                    'id'        => $cliente?->id,
                    'nombre'    => $cliente?->nombre ?? $cliente?->name,
                    'apellidos' => $cliente?->apellidos,
                    'email'     => $cliente?->email,
                    'telefono'  => $cliente?->telefono,
                ],
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }
}
