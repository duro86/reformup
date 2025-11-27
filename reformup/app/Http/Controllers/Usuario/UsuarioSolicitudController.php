<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\Perfil_Profesional;
use App\Models\Oficio;
use Mews\Purifier\Facades\Purifier;

class UsuarioSolicitudController extends Controller
{
    /**
     * Listado de solicitudes del cliente, con filtro por estado.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para ver tus solicitudes.');
        }

        $estado = $request->query('estado');             // abierta / en_revision / cerrada / cancelada / null
        $q      = trim((string) $request->query('q'));   // texto buscador

        // Base: solo solicitudes de ESTE cliente
        $query = Solicitud::with(['profesional'])
            ->where('cliente_id', $user->id);

        // Filtro por estado
        if (! empty($estado)) {
            $query->where('estado', $estado);
        }

        // Filtro por buscador
        if ($q !== '') {
            $qLike = '%' . $q . '%';

            $query->where(function ($sub) use ($qLike) {
                $sub->where('titulo', 'like', $qLike)
                    ->orWhere('ciudad', 'like', $qLike)
                    ->orWhere('provincia', 'like', $qLike)
                    ->orWhere('estado', 'like', $qLike)
                    ->orWhereHas('profesional', function ($q2) use ($qLike) {
                        $q2->where('empresa', 'like', $qLike)
                            ->orWhere('email_empresa', 'like', $qLike);
                    });
            });
        }

        $solicitudes = $query
            ->orderByDesc('fecha')
            ->paginate(5)
            ->withQueryString();

        return view('layouts.usuario.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'estado'      => $estado,
            'q'           => $q,
            'estados'     => Solicitud::ESTADOS,
        ]);
    }

    /**
     * Ver detalles de una solicitud del usuario (JSON para Vue o vista normal)
     */
    public function mostrar(Solicitud $solicitud)
    {
        $userId = Auth::id();

        // 1) No autenticado
        if (is_null($userId)) {
            if (request()->ajax() || request()->wantsJson()) {
                abort(401, 'No autenticado.');
            }

            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión para ver tus solicitudes.');
        }

        // 2) La solicitud NO es suya
        if ($solicitud->cliente_id !== $userId) {
            if (request()->ajax() || request()->wantsJson()) {
                abort(403, 'No puedes acceder a esta solicitud.');
            }

            return back()->with('error', 'No puedes acceder a esta solicitud.');
        }

        // 3) Cargar relaciones necesarias
        //    (asumiendo que en el modelo tienes:
        //     cliente(), profesional(), presupuestos() y en Presupuesto -> trabajo())
        $solicitud->load([
            'cliente',
            'profesional',
            'presupuestos.trabajo',
        ]);

        $cliente   = $solicitud->cliente;
        $perfilPro = $solicitud->profesional;

        // Escogemos un presupuesto asociado "principal"
        $presupuestoAsociado = $solicitud->presupuestos->first();
        $trabajoAsociado     = $presupuestoAsociado?->trabajo;

        // 4) Petición JSON (Axios desde el modal)
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'id'              => $solicitud->id,
                'titulo'          => $solicitud->titulo,
                'descripcion'     => $solicitud->descripcion,
                'estado'          => $solicitud->estado,
                'ciudad'          => $solicitud->ciudad,
                'provincia'       => $solicitud->provincia,
                'presupuesto_max' => $solicitud->presupuesto_max,

                //Fechas
                'fecha'           => $solicitud->fecha
                    ? $solicitud->fecha->format('d/m/Y H:i')
                    : null,

                'cliente' => $cliente ? [
                    'nombre'    => $cliente->nombre ?? $cliente->name ?? null,
                    'apellidos' => $cliente->apellidos ?? null,
                    'email'     => $cliente->email,
                    'telefono'  => $cliente->telefono ?? null,
                ] : null,

                'profesional' => $perfilPro ? [
                    'empresa'          => $perfilPro->empresa,
                    'email_empresa'    => $perfilPro->email_empresa,
                    'telefono_empresa' => $perfilPro->telefono_empresa,
                    'ciudad'           => $perfilPro->ciudad,
                    'provincia'        => $perfilPro->provincia,
                ] : null,

                'presupuesto' => $presupuestoAsociado ? [
                    'id'     => $presupuestoAsociado->id,
                    'estado' => $presupuestoAsociado->estado ?? null,
                    'total'  => $presupuestoAsociado->total ?? null,
                ] : null,

                'trabajo' => $trabajoAsociado ? [
                    'id'        => $trabajoAsociado->id,
                    'estado'    => $trabajoAsociado->estado ?? null,
                    'fecha_ini' => $trabajoAsociado->fecha_ini,
                    'fecha_fin' => $trabajoAsociado->fecha_fin,
                    'dir_obra'  => $trabajoAsociado->dir_obra,
                ] : null,
            ]);
        }

        // 5) Si entra sin AJAX, devuelves otra vista (si quieres tenerla)
        return view('layouts.usuario.solicitudes.mostrar', compact('solicitud'));
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

        // Filtro oficios (uno o varios, tipo OR)
        if (!empty($oficiosSeleccionados)) {
            $ids = array_filter($oficiosSeleccionados);
            $query->whereHas('oficios', function ($q) use ($ids) {
                $q->whereIn('oficios.id', $ids);
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

        // Validamos
        $validated = $request->validate(
            [
                'pro_id'          => ['required', 'integer', 'exists:perfiles_profesionales,id'],
                'titulo'          => ['required', 'string', 'max:160'],
                'descripcion'     => ['required', 'string'],
                'ciudad'          => ['required', 'string', 'max:120'],
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

        // Limpiamos con el purifier
        $descripcion = $request->input('descripcion');
        $descripcion_limpia = Purifier::clean($descripcion, 'solicitud');

        // Creamos la solicitud: el estado por defecto en BBDD ya es 'abierta'
        Solicitud::create([
            'pro_id'         => $validated['pro_id'],  // Asociada al profesional seleccionado
            'cliente_id'      => $user->id,
            'titulo'          => $validated['titulo'],
            'descripcion'     => $descripcion_limpia,
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
            ->with('success', 'La solicitud ' . $solicitud->titulo . ' se ha eliminado correctamente.');
    }
}
