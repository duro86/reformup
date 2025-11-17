<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\Perfil_Profesional;
use App\Models\Oficio;


class UsuarioSolicitudController extends Controller
{
    /**
     * Listado de solicitudes del cliente, con filtro por estado.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $estado = $request->query('estado'); // puede venir null

        $query = Solicitud::with('profesional')   // 👈 importante
            ->where('cliente_id', $user->id)
            ->orderByDesc('fecha');

        if ($estado) {
            $query->where('estado', $estado);
        }

        $solicitudes = $query->paginate(10)->withQueryString();

        return view('layouts.usuario.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'estado'      => $estado,
        ]);
    }

    /**
     * Paso 1: mostrar listado/buscador de profesionales
     */
    public function seleccionarProfesional(Request $request)
    {
        $user = Auth::user();

        // Filtros que lleguen por GET
        $ciudad   = $request->get('ciudad');
        $provincia = $request->get('provincia');
        $oficiosSeleccionados = $request->get('oficios', []); // array de IDs

        // Query base con Oficios y si tiene el perfil visible por Admin
        $query = Perfil_Profesional::query()
            ->with('oficios')
            ->where('visible', 1);

        // No mostrar el propio perfil si el usuario también es profesional
        if ($user && $user->perfil_Profesional) {
            $query->where('id', '!=', $user->perfil_Profesional->id);
        }

        // Filtro ciudad
        if ($ciudad) {
            $query->where('ciudad', 'like', '%' . $ciudad . '%');
        }

        // Filtro provincia
        if ($provincia) {
            $query->where('provincia', 'like', '%' . $provincia . '%');
        }

        // Filtro oficios (uno o varios)
        if (!empty($oficiosSeleccionados)) {
            $query->whereHas('oficios', function ($q) use ($oficiosSeleccionados) {
                $q->whereIn('oficio_id', (array)$oficiosSeleccionados);
            });
        }

        // Paginación
        $profesionales = $query->paginate(5)->withQueryString();

        // Para el selector de oficios
        $oficios = Oficio::orderBy('nombre')->get();

        return view('layouts.usuario.solicitudes.seleccionar_profesional', [
            'profesionales'       => $profesionales,
            'oficios'             => $oficios,
            'ciudad'              => $ciudad,
            'provincia'           => $provincia,
            'oficiosSeleccionados' => $oficiosSeleccionados,
        ]);
    }

    /**
     * Paso 2: mostrar formulario de nueva solicitud para un profesional concreto
     */
    public function crearConProfesional(Perfil_Profesional $pro)
    {
        // Formulario de nueva solicitud dirigido a un profesional concreto
        return view('layouts.usuario.solicitudes.crear_solicitud', [
            'profesional' => $pro,
        ]);
    }

    /**
     * Guardar nueva solicitud de un cliente.
     */
    public function guardar(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate(
            [
                'pro_id'          => ['required', 'integer', 'exists:perfiles_profesionales,id'],
                'titulo'          => ['required', 'string', 'max:160'],
                'descripcion'     => ['required', 'string'],
                'ciudad'          => ['required', 'nullable', 'string', 'max:120'],
                'provincia'       => ['nullable', 'string', 'max:120'],
                'dir_cliente'     => ['nullable', 'string', 'max:255'],
                'presupuesto_max' => ['nullable', 'numeric', 'min:0'],
            ],
            [
                'pro_id.required' => 'Debes seleccionar un profesional para esta solicitud.',
                'pro_id.integer'  => 'El identificador del profesional no es válido.',
                'pro_id.exists'   => 'El profesional seleccionado no existe.',
                'titulo.required'      => 'El título es obligatorio.',
                'titulo.max'           => 'El título no puede tener más de 160 caracteres.',
                'dir_cliente.max'           => 'La dirección no puede tener más de 255 caracteres.',
                'ciudad.required'      => 'La ciudad es obligatoria.',
                'descripcion.required' => 'La descripción de la solicitud es obligatoria.',
                'presupuesto_max.numeric' => 'El presupuesto máximo debe ser un número.',
                'presupuesto_max.min'     => 'El presupuesto máximo no puede ser negativo.',
            ]
        );

        // Creamos la solicitud: el estado por defecto en BBDD ya es 'abierta'
        Solicitud::create([
            'pro_id'         => $validated['pro_id'],  // Asociada al profesional seleccionado
            'cliente_id'      => $user->id,
            'titulo'          => $validated['titulo'],
            'descripcion'     => $validated['descripcion'],
            'ciudad'          => $validated['ciudad'],
            'provincia'       => $validated['provincia'] ?? null,
            'dir_cliente'     => $validated['dir_cliente'] ?? null,
            'estado'          => 'abierta',        // por si acaso, luego cambiamos cuando haya un presupuesto
            'presupuesto_max' => $validated['presupuesto_max'] ?? null,
            'fecha'           => now(),
        ]);

        return redirect()
            ->route('usuario.solicitudes.index')
            ->with('success', 'Tu solicitud se ha creado correctamente.');
    }

    /** Eliminar una Solicitud si esta abierta(sin revision) */
    public function eliminar(Solicitud $solicitud)
    {
        $user = Auth::user();

        // 1) Seguridad: solo puede borrar sus propias solicitudes
        if ($solicitud->cliente_id !== $user->id) {
            abort(403, 'No puedes eliminar esta solicitud.');
        }

        // 2) Regla de negocio: solo si está ABIERTA
        if ($solicitud->estado !== 'abierta') {
            return back()->with('error', 'Solo puedes eliminar solicitudes en estado "abierta".');
        }

        // 3) Soft delete (ya usas SoftDeletes en el modelo)
        $solicitud->delete();

        // Si quieres mantener el filtro actual (estado) en la redirección:
        $estado = request('estado');

        return redirect()
            ->route('usuario.solicitudes.index', $estado ? ['estado' => $estado] : [])
            ->with('success', 'La solicitud '.$solicitud->titulo.' se ha eliminado correctamente.');
    }
}
