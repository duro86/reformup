<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrabajoCanceladoMailable;

class UsuarioTrabajoController extends Controller
{
    /**
     * Listado de trabajos del usuario (cliente).
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();
        $estado  = $request->query('estado'); // previsto, en_curso, finalizado, cancelado o null

        $trabajos = Trabajo::with([
            'presupuesto.solicitud',
            'presupuesto.solicitud.cliente',
            'presupuesto.profesional',
            'comentarios', // ya que lo usas en la vista
        ])
            ->whereHas('presupuesto.solicitud', function ($q) use ($usuario) {
                $q->where('cliente_id', $usuario->id);
            })
            ->when($estado, function ($q) use ($estado) {
                $q->where('estado', $estado);
            })
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        return view('layouts.usuario.trabajos.index', [
            'trabajos' => $trabajos,
            'usuario'  => $usuario,
            'estado'   => $estado,
            'estados'  => Trabajo::ESTADOS,
        ]);
    }


    /**
     * Muestra un trabajo concreto del usuario.
     */
    public function mostrar(Trabajo $trabajo)
    {
        $this->autorizarUsuario($trabajo);

        // Cargamos todas las relaciones necesarias
        $trabajo->load([
            'presupuesto.solicitud.cliente',   // cliente = user
            'presupuesto.profesional',        // Perfil_Profesional
        ]);

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;
        $cliente     = $solicitud?->cliente;          // User
        $perfilPro   = $presupuesto?->profesional;    // Perfil_Profesional

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

        // Por si alguien entra por URL normal (no Vue)
        return view('layouts.usuario.trabajos.mostrar', compact('trabajo'));
    }



    /**
     * Usuario cancela un trabajo que aún no ha comenzado.
     */
    public function cancelar(Request $request, Trabajo $trabajo)
    {
        $user = Auth::user();

        // Comprueba que el trabajo pertenece al cliente logueado
        $this->autorizarUsuario($trabajo);

        // Solo se puede cancelar si está previsto y no ha comenzado
        if ($trabajo->estado !== 'previsto' || !is_null($trabajo->fecha_ini)) {
            return back()->with('error', 'Solo puedes cancelar trabajos que aún no han comenzado.');
        }

        $validated = $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        $motivo = $validated['motivo'] ?? null;

        // 1) Cambiamos estado del trabajo
        $trabajo->estado = 'cancelado';
        $trabajo->save();

        // 2) Presupuesto y profesional
        $presupuesto = $trabajo->presupuesto;
        $perfil      = $presupuesto?->profesional;   // Perfil_Profesional (pro_id)

        // IMPORTANTE: aquí usamos un estado que exista en la BBDD, por ejemplo 'rechazado'
        if ($presupuesto) {
            $presupuesto->estado = 'rechazado';   // o el que tengas definido en el ENUM
            $presupuesto->save();
        }

        // 3) Enviar email a la empresa si tiene email_empresa
        if ($perfil && $perfil->email_empresa) {
            try {
                Mail::to($perfil->email_empresa)->send(
                    new TrabajoCanceladoMailable($trabajo, $presupuesto, $perfil, $user, $motivo)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'No se ha podido cancelar el trabajo.');
            }
        }

        return back()->with('success', 'Has cancelado el trabajo correctamente.');
    }






    /**
     * Método privado para asegurar que el trabajo pertenece al usuario logueado.
     */
    private function autorizarUsuario(Trabajo $trabajo)
    {
        $usuarioId = Auth::id();

        $solicitud = $trabajo->presupuesto
            ? $trabajo->presupuesto->solicitud
            : null;

        if (!$solicitud || $solicitud->cliente_id !== $usuarioId) {
            return back()->with('error', 'nO TIENES ACCESO A ESTA SECCIÓN.');
        }
    }
}
