<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use App\Models\Perfil_Profesional;
use App\Models\Oficio;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Traits\FiltroRangoFechas;
use Illuminate\Support\Facades\Mail;
use App\Mail\Usuario\SolicitudClienteAccionMailable;


class UsuarioSolicitudController extends Controller
{
    use FiltroRangoFechas;

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

        $estado = $request->query('estado');           // abierta / en_revision / cerrada / cancelada / null
        $q      = trim((string) $request->query('q')); // texto buscador

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

        // Filtro por rango de fechas (usamos la columna 'fecha' de la solicitud)
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

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
                return back()->with('error', 'No puedes acceder a esta solicitud, debes autenticarte');
            }

            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión para ver tus solicitudes.');
        }

        // 2) La solicitud NO es suya
        if ($solicitud->cliente_id !== $userId) {
            if (request()->ajax() || request()->wantsJson()) {
                return back()->with('No puedes acceder a esta solicitud.');
            }

            return back()->with('error', 'No puedes acceder a esta solicitud.');
        }

        // 3) Cargar relaciones necesarias
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
                'ciudad'          => ['nullable', 'string', 'max:120'],
                'provincia'       => ['required', 'string', 'max:120'],
                'dir_cliente'     => ['nullable', 'string', 'max:255'],
                'presupuesto_max' => ['nullable', 'numeric', 'min:0'],
            ],
            [
                'pro_id.required' => 'Debes seleccionar un profesional para esta solicitud.',
                'pro_id.integer'  => 'El identificador del profesional no es válido.',
                'pro_id.exists'   => 'El profesional seleccionado no existe.',
                'titulo.required'      => 'El título es obligatorio.',
                'titulo.max'           => 'El título no puede tener más de 160 caracteres.',
                'provincia.max'           => 'La provincia no puede tener más de 160 caracteres.',
                'ciudad.max'           => 'La ciudad no puede tener más de 160 caracteres.',
                'dir_cliente.max'           => 'La dirección no puede tener más de 255 caracteres.',
                'provincia.required'      => 'El municipio es obligatoria.',
                'descripcion.required' => 'La descripción de la solicitud es obligatoria.',
                'presupuesto_max.numeric' => 'El presupuesto máximo debe ser un número.',
                'presupuesto_max.min'     => 'El presupuesto máximo no puede ser negativo.',
            ]
        );

        // Limpiamos con el purifier
        $descripcion = $request->input('descripcion');
        $descripcion_limpia = Purifier::clean($descripcion, 'solicitud');

        // Creamos la solicitud: el estado por defecto en BBDD ya es 'abierta'
        try {
            Solicitud::create([
                'pro_id'         => $validated['pro_id'],  // Asociada al profesional seleccionado
                'cliente_id'      => $user->id,
                'titulo'          => $validated['titulo'],
                'descripcion'     => $descripcion_limpia,
                'ciudad'          => $validated['ciudad'],
                'provincia'       => $validated['provincia'] ?? null,
                'dir_cliente'     => $validated['dir_cliente'] ?? null,
                'estado'          => 'abierta',        // por defecto
                'presupuesto_max' => $validated['presupuesto_max'] ?? null,
                'fecha'           => now(),
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Ha ocurrido un error al crear la solicitud');
        }

        return redirect()
            ->route('usuario.solicitudes.index')
            ->with('success', 'Tu solicitud se ha creado correctamente.');
    }

    /**
     * Cancelar una solicitud del cliente (cambiar estado a "cancelada").
     */
    public function cancelar(Request $request, Solicitud $solicitud)
    {
        $user = Auth::user();

        if ($solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes eliminar una solicitud que no es tuya.');
        }

        if ($solicitud->estado !== 'abierta') {
            return back()->with('error', 'Solo puedes cancelar solicitudes en estado "abierta".');
        }

        // El motivo viene del input hidden que rellena el SweetAlert
        $motivo = trim((string) $request->input('motivo_cancelacion'));

        if ($motivo === '') {
            // Por seguridad, por si alguien salta el JS
            return back()->with('error', 'Debes indicar un motivo para cancelar la solicitud.');
        }

        try {
            $solicitud->estado = 'cancelada';
            $solicitud->save();

            // Enviar correo al profesional (si existe)
            $perfilPro = $solicitud->profesional; // Perfil_Profesional

            try {
                if ($perfilPro && $perfilPro->email_empresa) {
                    Mail::to($perfilPro->email_empresa)->send(
                        new SolicitudClienteAccionMailable(
                            $solicitud,
                            $user,
                            $perfilPro,
                            $motivo,
                            'cancelada'
                        )
                    );
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Se ha cancelado la solicitud, pero no se ha podido informar a los afectados');
            }

            $estado = $request->query('estado');

            return redirect()
                ->route('usuario.solicitudes.index', $estado ? ['estado' => $estado] : [])
                ->with('success', 'La solicitud se ha cancelado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ha ocurrido un error al cancelar la solicitud.');
        }
    }

    /** Eliminar una Solicitud si esta abierta(sin revision) */
    public function eliminar(Request $request, Solicitud $solicitud)
    {
        $user = Auth::user();

        if ($solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes eliminar una solicitud que no es tuya.');
        }

        if ($solicitud->estado !== 'abierta') {
            return back()->with('error', 'Solo puedes eliminar solicitudes en estado "abierta".');
        }

        $motivo = trim((string) $request->input('motivo_eliminacion'));

        $perfilPro = $solicitud->profesional;

        try {
            // Guardamos una copia antes del delete para el mail
            $solicitudClon = clone $solicitud;

            $solicitud->delete();

            // Email al profesional (si tiene email_empresa)
            try {
                if ($perfilPro && $perfilPro->email_empresa) {
                    Mail::to($perfilPro->email_empresa)->send(
                        new SolicitudClienteAccionMailable(
                            $solicitudClon,
                            $user,
                            $perfilPro,
                            $motivo !== '' ? $motivo : null,
                            'eliminada'
                        )
                    );
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Se ha borrado la solicitud, pero no se ha podido informar a los afectados.');
            }

            $estado = $request->query('estado');

            return redirect()
                ->route('usuario.solicitudes.index', $estado ? ['estado' => $estado] : [])
                ->with('success', 'La solicitud ' . ($solicitudClon->titulo ?? '') . ' se ha eliminado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ha ocurrido un error al eliminar la solicitud.');
        }
    }
}
