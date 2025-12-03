<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrabajoCanceladoPorProfesionalMailable;
use App\Mail\TrabajoIniciadoMailable;
use App\Mail\TrabajoFinalizadoMailable;
use App\Http\Controllers\Traits\FiltroRangoFechas;


class ProfesionalTrabajoController extends Controller
{
    use FiltroRangoFechas;


    /**
     * Asegura que el trabajo pertenece al profesional logueado.
     */
    private function autorizarProfesional(Trabajo $trabajo)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_profesional ?? null;

        $presupuesto = $trabajo->presupuesto;

        if (! $perfil || ! $presupuesto || $presupuesto->pro_id !== $perfil->id) {
            return redirect()->route('home')
                ->with('error', 'No puedes acceder a esta sección, ese trabajo no pertenece a tus registros.');
        }
    }

    /**
     * Listado de trabajos del profesional.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil) {
            return redirect()
                ->back()
                ->with('error', 'No tienes permisos para esta sección');
        }

        // Estado del trabajo: previsto / en_curso / finalizado / cancelado / null
        $estado = $request->query('estado');
        $q      = trim((string) $request->query('q'));   // texto buscador

        // Estados disponibles desde el modelo (sin "Todos")
        $estados = Trabajo::ESTADOS;

        // Base: SOLO trabajos de este profesional

        $query = Trabajo::with(['presupuesto.solicitud.cliente'])
            ->whereHas('presupuesto', function ($qPro) use ($perfil) {
                $qPro->where('pro_id', $perfil->id);
            });

        // Filtro por estado (solo si es válido)
        if ($estado !== null && $estado !== '') {
            if (array_key_exists($estado, Trabajo::ESTADOS)) {
                $query->where('estado', $estado);
            }
        }

        // Filtro por buscador
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                // Por título / ciudad / provincia (desde la solicitud del presupuesto)
                $sub->whereHas('presupuesto.solicitud', function ($q2) use ($like) {
                    $q2->where('titulo', 'like', $like)
                        ->orWhere('ciudad', 'like', $like)
                        ->orWhere('provincia', 'like', $like);
                })
                    // Por cliente
                    ->orWhereHas('presupuesto.solicitud.cliente', function ($q3) use ($like) {
                        $q3->where('nombre', 'like', $like)
                            ->orWhere('apellidos', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })
                    // Por estado del trabajo
                    ->orWhere('estado', 'like', $like)
                    // Por importe (total del presupuesto asociado, casteado a texto)
                    ->orWhereHas('presupuesto', function ($q4) use ($like) {
                        $q4->whereRaw('CAST(total AS CHAR) LIKE ?', [$like]);
                    });
            });
        }

        // Filtro por rango de fechas (creación del trabajo)
        $this->aplicarFiltroRangoFechas($query, $request, 'created_at');

        $trabajos = $query
            ->orderByDesc('created_at')   // o 'fecha_ini' 
            ->paginate(6)
            ->withQueryString();

        return view('layouts.profesional.trabajos.index', [
            'trabajos' => $trabajos,
            'estado'   => $estado,
            'q'        => $q,
            'estados'  => $estados,
            'perfil'   => $perfil,
        ]);
    }


    /**
     * Detalle de un trabajo para el profesional (JSON para modal o vista normal).
     */
    public function mostrar(Trabajo $trabajo)
    {
        $this->autorizarProfesional($trabajo);

        // Cargamos todas las relaciones necesarias
        $trabajo->load([
            'presupuesto.solicitud.cliente',
            'presupuesto.profesional',
        ]);

        // Datos relacionados , guardamos en variables para facilitar acceso
        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $presupuesto?->profesional;

        // Respuesta JSON para modal
        if (request()->wantsJson()) {
            return response()->json([
                'id'        => $trabajo->id,
                'presu_id'  => $trabajo->presu_id,
                'fecha_ini' => $trabajo->fecha_ini
                    ? $trabajo->fecha_ini->format('d/m/Y H:i')
                    : null,
                'fecha_fin' => $trabajo->fecha_fin
                    ? $trabajo->fecha_fin->format('d/m/Y H:i')
                    : null,
                'estado'    => $trabajo->estado,
                'dir_obra'  => $trabajo->dir_obra,

                'presupuesto' => $presupuesto ? [
                    'id'      => $presupuesto->id,
                    // AJUSTA ESTE CAMPO al que tengas de "nombre del presupuesto":
                    'nombre'  => $presupuesto->nombre ?? $presupuesto->titulo ?? null,
                    'total'   => $presupuesto->total,
                    'notas'   => $presupuesto->notas ?? null,
                    'profesional' => $perfilPro ? [
                        'empresa'          => $perfilPro->empresa,
                        'email_empresa'    => $perfilPro->email_empresa,
                        'telefono_empresa' => $perfilPro->telefono_empresa,
                        'ciudad'           => $perfilPro->ciudad,
                        'provincia'        => $perfilPro->provincia,
                    ] : null,
                ] : null,

                'cliente' => $cliente ? [
                    'nombre'    => $cliente->nombre ?? $cliente->name ?? null,
                    'apellidos' => $cliente->apellidos ?? null,
                    'email'     => $cliente->email,
                    'telefono'     => $cliente->telefono,
                ] : null,

                'solicitud' => $solicitud ? [
                    'id'     => $solicitud->id,
                    'titulo' => $solicitud->titulo,
                    'ciudad' => $solicitud->ciudad,
                    'presupuesto_max' => $solicitud->presupuesto_max,
                ] : null,
            ]);
        }

        // mostrar vista normal
        return view('layouts.profesional.trabajos.mostrar', compact('trabajo'));
    }


    /**
     * El profesional marca el trabajo como "en curso" (empieza) y avisa al cliente.
     */
    public function empezar(Trabajo $trabajo)
    {
        $this->autorizarProfesional($trabajo);

        if ($trabajo->estado !== 'previsto' || !is_null($trabajo->fecha_ini)) {
            return back()->with('error', 'Solo puedes empezar trabajos en estado previsto y sin fecha de inicio.');
        }

        $trabajo->fecha_ini = now();
        $trabajo->estado    = 'en_curso';
        $trabajo->save();

        // Avisar al cliente
        $presupuesto = $trabajo->presupuesto; // Presupuesto
        $solicitud   = $presupuesto?->solicitud; // Solicitud
        $cliente     = $solicitud?->cliente; // Usuario
        $perfilPro   = $presupuesto?->profesional; // Perfil_Profesional

        // Enviar email al cliente
        if ($cliente && $cliente->email && $perfilPro) {
            try {
                Mail::to($cliente->email)->send(
                    new TrabajoIniciadoMailable($trabajo, $presupuesto, $cliente, $perfilPro)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'No se ha podido empezar el trabajo.');
            }
        }

        return back()->with('success', 'Has marcado el trabajo como en curso. El cliente ha sido notificado.');
    }

    /**
     * El profesional marca el trabajo como finalizado y avisa al cliente.
     */
    public function finalizar(Trabajo $trabajo)
    {
        $this->autorizarProfesional($trabajo);

        if ($trabajo->estado !== 'en_curso') {
            return back()->with('error', 'Solo puedes finalizar trabajos que estén en curso.');
        }

        if (is_null($trabajo->fecha_ini)) {
            $trabajo->fecha_ini = now(); // por seguridad
        }

        $trabajo->fecha_fin = now();
        $trabajo->estado    = 'finalizado';
        $trabajo->save();


        // Avisar al cliente
        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $presupuesto?->profesional;

        // Incrementar trabajos_realizados del perfil profesional
        if ($perfilPro) {
            $perfilPro->increment('trabajos_realizados');
        }

        // Enviar email al cliente
        if ($cliente && $cliente->email && $perfilPro) {
            try {
                Mail::to($cliente->email)->send(
                    new TrabajoFinalizadoMailable($trabajo, $presupuesto, $cliente, $perfilPro)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'No se ha enviar el correo a los implicaods.');
            }
        }

        if ($trabajo->presupuesto && $trabajo->presupuesto->profesional) {
            $perfilPro = $trabajo->presupuesto->profesional;
            $perfilPro->increment('trabajos_realizados'); // Sumamos en trabajos realizados
        }

        return back()->with('success', 'Has marcado el trabajo como finalizado. El cliente ha sido notificado.');
    }


    /**
     * El profesional cancela un trabajo que aún no ha comenzado.
     * Envía un correo al cliente avisando.
     */
    public function cancelar(Request $request, Trabajo $trabajo)
    {
        $this->autorizarProfesional($trabajo);

        // Controlamos que el trabajo está en estado previsto y no ha comenzado
        if ($trabajo->estado !== 'previsto' || !is_null($trabajo->fecha_ini)) {
            return back()->with('error', 'Solo puedes cancelar trabajos que aún no han comenzado.');
        }

        // Validamos motivo opcional
        $validated = $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        // Datos necesarios para el email
        $motivo      = $validated['motivo'] ?? null;
        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;
        $perfilPro   = $presupuesto?->profesional;

        // Cambiamos estado del trabajo
        $trabajo->estado = 'cancelado';
        $trabajo->save();

        // Opcional: marcar presupuesto como "rechazado" (o el estado que uses)
        if ($presupuesto) {
            $presupuesto->estado = 'rechazado'; // usa uno que exista en tu ENUM
            $presupuesto->save();
        }

        // Enviar email al cliente controlando los posibles errores
        if ($cliente && $cliente->email && $perfilPro) {
            try {
                // Enviamos el email
                Mail::to($cliente->email)->send(
                    new TrabajoCanceladoPorProfesionalMailable($trabajo, $presupuesto, $cliente, $motivo)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'No se ha podido cancelar el trabajo.');
            }
        }

        // Respuesta de éxito
        return back()->with('success', 'Has cancelado el trabajo correctamente. El cliente ha sido notificado.');
    }

    /**
     * General token para consulta datos apì
     */
    public function generarTokenApi()
    {
        $user = Auth::user();

        if (! $user->hasRole('profesional')) {
            abort(403);
        }

        // Opcional: eliminar tokens antiguos con este nombre
        $user->tokens()->where('name', 'token-movil-profesional')->delete();

        // Crear un nuevo token
        $token = $user->createToken('token-movil-profesional')->plainTextToken;

        // Devolver el token a la vista mediante mensaje flash
        return back()->with('token_api', $token);
    }
}
