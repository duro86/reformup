<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrabajoCanceladoMailable;
use App\Http\Controllers\Traits\FiltroRangoFechas;


class UsuarioTrabajoController extends Controller
{
    use FiltroRangoFechas;

    /**
     * Listado de trabajos del usuario (cliente).
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();

        if (! $usuario) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para ver tus trabajos.');
        }

        $estado = $request->query('estado');            // previsto, en_curso, finalizado, cancelado o null
        $q      = trim((string) $request->query('q'));  // texto de búsqueda

        // Base: trabajos del cliente autenticado
        $query = Trabajo::with([
            'presupuesto.solicitud',
            'presupuesto.solicitud.cliente',
            'presupuesto.profesional',
            'comentarios',
        ])
            ->whereHas('presupuesto.solicitud', function ($sub) use ($usuario) {
                $sub->where('cliente_id', $usuario->id);
            });

        // Filtro por estado
        if (! empty($estado)) {
            $query->where('estado', $estado);
        }

        // Filtro por buscador
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                // Por campos de la solicitud asociada
                $sub->whereHas('presupuesto.solicitud', function ($q2) use ($like) {
                    $q2->where('titulo', 'like', $like)
                        ->orWhere('ciudad', 'like', $like)
                        ->orWhere('provincia', 'like', $like)
                        ->orWhere('estado', 'like', $like);
                })
                    // Por profesional (empresa / email)
                    ->orWhereHas('presupuesto.profesional', function ($q3) use ($like) {
                        $q3->where('empresa', 'like', $like)
                            ->orWhere('email_empresa', 'like', $like);
                    })
                    // Por estado del trabajo
                    ->orWhere('estado', 'like', $like)
                    // Por dirección de obra
                    ->orWhere('dir_obra', 'like', $like)
                    // Por total del presupuesto (convertido a texto)
                    ->orWhereHas('presupuesto', function ($q4) use ($like) {
                        $q4->whereRaw('CAST(total AS CHAR) LIKE ?', [$like]);
                    });
            });
        }

        // Filtro por rango de fechas del trabajo
        // Filtrar por fecha_ini (inicio del trabajo)
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha_ini');

        $trabajos = $query
            ->orderByDesc('created_at')   // Listado general por creacion
            ->paginate(5)
            ->withQueryString();

        // Añadir "ref_cliente" a cada trabajo (número correlativo del cliente)
        $trabajos->getCollection()->transform(function ($trabajo, $index) use ($trabajos) {
            $trabajo->ref_cliente = $trabajos->total() - ($trabajos->firstItem() + $index) + 1;
            return $trabajo;
        });

        return view('layouts.usuario.trabajos.index', [
            'trabajos' => $trabajos,
            'usuario'  => $usuario,
            'estado'   => $estado,
            'q'        => $q,
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
                    'fecha' => $solicitud->fecha->format('d/m/Y H:i'),
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

        // 2) Presupuesto, profesional y solicitud
        $presupuesto = $trabajo->presupuesto;
        $perfil      = $presupuesto?->profesional;   // Perfil_Profesional
        $solicitud   = $presupuesto?->solicitud;

        // Presupuesto → rechazado
        if ($presupuesto) {
            $presupuesto->estado = 'rechazado';
            $presupuesto->save();
        }

        // Solicitud → cancelada (tu criterio para “cerrar todo el hilo”)
        if ($solicitud) {
            $solicitud->estado = 'cancelada';
            $solicitud->save();
        }

        // 3) Intentar enviar email al profesional (si tiene email)
        if ($perfil && $perfil->email_empresa) {
            try {
                Mail::to($perfil->email_empresa)->send(
                    new TrabajoCanceladoMailable(
                        $trabajo,
                        $presupuesto,
                        $perfil,
                        $user,
                        $motivo
                    )
                );

                return back()->with(
                    'success',
                    'Has cancelado el trabajo correctamente. El profesional ha sido avisado por correo.'
                );
            } catch (\Throwable $e) {

                \Log::error('Error enviando correo de trabajo cancelado por el cliente', [
                    'trabajo_id'     => $trabajo->id,
                    'presupuesto_id' => $presupuesto?->id,
                    'solicitud_id'   => $solicitud?->id,
                    'profesional_id' => $perfil?->id,
                    'error'          => $e->getMessage(),
                ]);

                return back()->with(
                    'warning',
                    'Has cancelado el trabajo correctamente, pero no se ha podido enviar el correo al profesional.'
                );
            }
        }

        // Si no hay email de empresa, solo cancelamos y ya está
        return back()->with(
            'success',
            'Has cancelado el trabajo correctamente. El profesional no tiene email de empresa configurado.'
        );
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
            return back()->with('error', 'No tienes acceso a esta sección');
        }
    }
}
