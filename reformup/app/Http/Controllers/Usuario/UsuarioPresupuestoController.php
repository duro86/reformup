<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Presupuesto;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\FiltroRangoFechas;
use Illuminate\Support\Facades\Mail;
use App\Mail\PresupuestoRechazadoPorClienteMailable;
use App\Mail\Usuario\AceptarPresupuesto;


class UsuarioPresupuestoController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de presupuestos del cliente logueado.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para ver tus presupuestos.');
        }

        $estado = $request->query('estado');           // enviado, aceptado, rechazado, caducado, null
        $q      = trim((string) $request->query('q')); // texto buscador

        $query = Presupuesto::with(['solicitud.profesional'])
            ->whereHas('solicitud', function ($sub) use ($user) {
                $sub->where('cliente_id', $user->id);
            });

        // Filtro por estado
        if (! empty($estado)) {
            $query->where('estado', $estado);
        }

        // Buscador libre
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                // Por datos de la solicitud (título, ciudad, provincia)
                $sub->whereHas('solicitud', function ($q2) use ($like) {
                    $q2->where('titulo', 'like', $like)
                        ->orWhere('ciudad', 'like', $like)
                        ->orWhere('provincia', 'like', $like);
                })
                    // Por profesional
                    ->orWhereHas('solicitud.profesional', function ($q3) use ($like) {
                        $q3->where('empresa', 'like', $like)
                            ->orWhere('email_empresa', 'like', $like);
                    })
                    // Por estado del presupuesto
                    ->orWhere('estado', 'like', $like)
                    // Por importe (como texto)
                    ->orWhereRaw('CAST(total AS CHAR) LIKE ?', [$like]);
            });
        }

        // Filtro por rango de fechas 
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

        $presupuestos = $query
            ->orderByDesc('fecha')
            ->paginate(5)
            ->withQueryString();

        return view('layouts.usuario.presupuestos.index', [
            'presupuestos' => $presupuestos,
            'estado'       => $estado,
            'q'            => $q,
            'estados'      => Presupuesto::ESTADOS,
        ]);
    }

    /**
     * Aceptar un presupuesto (cliente).
     */
    public function aceptar(Request $request, Presupuesto $presupuesto)
    {
        // Usuario logueado
        $user = Auth::user();

        // Seguridad: que el presupuesto sea del cliente logueado
        if (! $presupuesto->solicitud || $presupuesto->solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes gestionar presupuestos de otros clientes.');
        }

        // Sólo se pueden aceptar presupuestos en estado "enviado"
        if ($presupuesto->estado !== 'enviado') {
            return back()->with('error', 'Sólo puedes aceptar presupuestos en estado enviado.');
        }

        // Dirección de la obra, viene del SweetAlert si no existe en la solicitud
        $solicitud = $presupuesto->solicitud;

        // Si NO hay dir_cliente en la solicitud, entonces sí exigimos direccion_obra en la petición
        $rules    = [];
        $mensajes = [];

        if (! $solicitud || ! $solicitud->dir_cliente) {
            $rules['direccion_obra'] = ['required', 'string', 'max:255'];
            $mensajes = [
                'direccion_obra.required' => 'Debes indicar la dirección donde se realizará la obra.',
            ];
        }

        // Si $rules está vacío, no valida nada (y $data será [])
        $data = $rules
            ? $request->validate($rules, $mensajes)
            : [];

        try {
            // 1) Transacción para presupuesto + solicitud + trabajo
            DB::transaction(function () use ($presupuesto, $solicitud, $data) {

                // 1) Marcar presupuesto como aceptado
                $presupuesto->estado = 'aceptado';
                $presupuesto->fecha  = now();
                $presupuesto->save();

                // 2) Marcar la solicitud como cerrada y rechazar otros presupuestos enviados
                if ($solicitud) {
                    $solicitud->estado = 'cerrada';
                    $solicitud->save();

                    Presupuesto::where('solicitud_id', $solicitud->id)
                        ->where('id', '!=', $presupuesto->id)
                        ->where('estado', 'enviado')
                        ->update(['estado' => 'rechazado']);
                }

                // 3) Dirección de obra
                $dirObra = $solicitud && $solicitud->dir_cliente
                    ? $solicitud->dir_cliente
                    : ($data['direccion_obra'] ?? null);

                // 4) Crear el trabajo
                Trabajo::create([
                    'presu_id'   => $presupuesto->id,
                    'dir_obra'   => $dirObra,
                    'fecha_ini'  => null,
                    'fecha_fin'  => null,
                    'estado'     => 'previsto', // estado inicial a la espera del profesional
                ]);
            });

            // 2) Tras la transacción: recargar relaciones y enviar email al profesional
            $presupuesto->load('solicitud.cliente', 'solicitud.profesional');

            $solicitud = $presupuesto->solicitud;
            $cliente   = $solicitud?->cliente;
            $perfilPro = $solicitud?->profesional;

            if ($perfilPro && $perfilPro->email_empresa && $cliente) {
                try {
                    Mail::to($perfilPro->email_empresa)->send(
                        new AceptarPresupuesto($presupuesto, $solicitud, $cliente, $perfilPro)
                    );

                    return redirect()
                        ->route('usuario.presupuestos.index')
                        ->with('success', 'Has aceptado el presupuesto, se ha creado el trabajo y el profesional ha sido avisado por correo.');
                } catch (\Throwable $e) {
                    // Presupuesto aceptado y trabajo creado, pero fallo en el email
                    return redirect()
                        ->route('usuario.presupuestos.index')
                        ->with('warning', 'Has aceptado el presupuesto y se ha creado el trabajo, pero no se ha podido enviar el correo al profesional.');
                }
            }

            // Si no hay email de empresa, solo mensaje normal
            return redirect()
                ->route('usuario.presupuestos.index')
                ->with('success', 'Has aceptado el presupuesto y se ha creado el trabajo. El profesional no tiene email de empresa configurado.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ha ocurrido un error al aceptar el presupuesto.');
        }
    }


    /**
     * Rechazar un presupuesto (cliente).
     */
    public function rechazar(Request $request, Presupuesto $presupuesto)
    {
        $user = Auth::user();

        if (! $presupuesto->solicitud || $presupuesto->solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'Solo puedes rechazar presupuestos propios.');
        }

        if ($presupuesto->estado !== 'enviado') {
            return back()->with('error', 'Solo puedes rechazar presupuestos en estado enviado.');
        }

        // validamos motivo si existe
        $validated = $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        try {
            // Cambiar estado del presupuesto
            $presupuesto->estado = 'rechazado';
            $presupuesto->fecha  = now();
            $presupuesto->save();

            // Cambiar la solicitud a cancelada
            $solicitud = $presupuesto->solicitud;
            if ($solicitud) {
                $solicitud->estado = 'abierta';
                $solicitud->save();
            }

            // Enviar email al profesional
            $pro = $presupuesto->profesional;

            if ($pro && $pro->email_empresa) {
                Mail::to($pro->email_empresa)->send(
                    new PresupuestoRechazadoPorClienteMailable(
                        $presupuesto,
                        $user,
                        $validated['motivo'] ?? null,
                        $solicitud
                    )
                );
            }

            return back()->with('success', 'Has rechazado el presupuesto.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ha ocurrido un error al rechazar el presupuesto.');
        }
    }
}
