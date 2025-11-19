<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Presupuesto;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioPresupuestoController extends Controller
{
    /**
     * Listado de presupuestos del cliente logueado.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $estado = $request->query('estado'); // enviado, aceptado, rechazado, caducado, null

        $presupuestos = Presupuesto::with(['solicitud.profesional'])
            ->whereHas('solicitud', function ($q) use ($user) {
                $q->where('cliente_id', $user->id);
            })
            ->when($estado, function ($q) use ($estado) {
                $q->where('estado', $estado);
            })
            ->orderByDesc('fecha')
            ->paginate(5)
            ->withQueryString();

        return view('layouts.usuario.presupuestos.index', [
            'presupuestos' => $presupuestos,
            'estado'       => $estado,
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

        // Dirección de la obra, viene del SweetAlert si no exite en la solicuitud
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

        // Transacción para asegurar consistencia
        try {
            DB::transaction(function () use ($presupuesto, $solicitud, $data) {

                // 1) Marcar presupuesto como aceptado
                $presupuesto->estado = 'aceptado';
                $presupuesto->fecha  = now();
                $presupuesto->save();

                // 2) Marcar la solicitud como cerrada y rechazar otros presupuestos enviados
                if ($solicitud) {
                    $solicitud->estado = 'cerrada';
                    $solicitud->save();

                    // Bucamos otros presupuestos enviados y los rechazamos
                    Presupuesto::where('solicitud_id', $solicitud->id)
                        ->where('id', '!=', $presupuesto->id)
                        ->where('estado', 'enviado')
                        ->update(['estado' => 'rechazado']);
                }

                // 3) Elegir la dirección de obra:
                //    - Si hay dir_cliente en la solicitud ⇒ usamos esa.
                //    - Si no hay  usamos la que venía del formulario (SweetAlert).
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

            // Si va todo bien, redirigimos con éxito
            return redirect()
                ->route('usuario.presupuestos.index')
                ->with('success', 'Has aceptado el presupuesto y se ha creado el trabajo.');
        } catch (\Throwable $e) {
            // \Log::error($e->getMessage());
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

        try {
            $presupuesto->estado = 'rechazado';
            $presupuesto->fecha  = now();
            $presupuesto->save();

            return back()->with('success', 'Has rechazado el presupuesto.');
        } catch (\Throwable $e) {
            // \Log::error($e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al rechazar el presupuesto.');
        }
    }
}
