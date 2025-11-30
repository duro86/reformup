<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Perfil_Profesional;
use Illuminate\Http\Request;

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
}
