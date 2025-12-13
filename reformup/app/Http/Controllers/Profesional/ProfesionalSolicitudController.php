<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Traits\FiltroRangoFechas;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\SolicitudCanceladaClienteMailable;


class ProfesionalSolicitudController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de solicitudes recibidas por el profesional logueado.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional; // relación 1:1 con Perfil_Profesional

        // Si no tiene perfil profesional, fuera
        if (! $perfil) {
            return redirect()->route('home')
                ->with('error', 'No puedes acceder a las solicitudes sin un perfil profesional.');
        }

        // Filtros
        $estado = $request->query('estado');            // abierta / en_revision / cerrada / cancelada / null
        $q      = trim((string) $request->query('q'));  // texto buscador

        // Base: SOLO solicitudes dirigidas a este profesional
        $query = Solicitud::with(['cliente']) // al pro le interesa ver al cliente
            ->where('pro_id', $perfil->id);

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
                    ->orWhereHas('cliente', function ($q2) use ($qLike) {
                        $q2->where('nombre', 'like', $qLike)
                            ->orWhere('apellidos', 'like', $qLike)
                            ->orWhere('email', 'like', $qLike);
                    });
            });
        }

        // Filtro por rango de fechas (tiene sentido usar 'fecha' de la solicitud)
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');
        // Si alguna solicitud no tiene 'fecha' , usamos created_at:
        // $this->aplicarFiltroRangoFechas($query, $request, 'created_at');

        // Orden + paginación
        $solicitudes = $query
            ->orderByDesc('fecha')   // o 'created_at' 
            ->paginate(5)
            ->withQueryString();

        // Ref correlativa (visible solo para ESE profesional)
        $solicitudes->getCollection()->transform(function ($s, $i) use ($solicitudes) {
            $s->ref_pro = $solicitudes->total() - ($solicitudes->firstItem() + $i) + 1;
            return $s;
        });

        return view('layouts.profesional.solicitudes.index', [
            'solicitudes' => $solicitudes,
            'estado'      => $estado,
            'q'           => $q,
            'estados'     => Solicitud::ESTADOS ?? [], // por si usamos la constante
            'perfil'      => $perfil,
        ]);
    }


    /**
     * Detalle de una solicitud (para modal Vue o vista normal).
     */
    public function mostrar(Solicitud $solicitud)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        if (! $perfil || $solicitud->pro_id !== $perfil->id) {
            abort(403, 'No tienes acceso a esta solicitud.');
        }

        // Cargamos también presupuestos y su trabajo
        $solicitud->load([
            'cliente',
            'profesional',
            'presupuestos.trabajo',
        ]);

        // Escoger el presupuesto principal: el último por id
        $presupuestoPrincipal = $solicitud->presupuestos
            ->sortByDesc('id')
            ->first();

        // $presupuestoPrincipal = $solicitud->presupuestos
        //     ->whereIn('estado', ['enviado', 'aceptado'])
        //     ->sortByDesc('id')
        //     ->first();

        $trabajoPrincipal = $presupuestoPrincipal?->trabajo;

        // Si la petición pide JSON (Vue modal)
        if (request()->wantsJson()) {
            return response()->json([
                'id'              => $solicitud->id,
                'titulo'          => $solicitud->titulo,
                'descripcion'     => $solicitud->descripcion,
                'ciudad'          => $solicitud->ciudad,
                'provincia'       => $solicitud->provincia,
                'dir_empresa'     => $solicitud->dir_empresa,
                'estado'          => $solicitud->estado,
                'presupuesto_max' => $solicitud->presupuesto_max,
                'fecha'           => optional($solicitud->fecha)->format('d/m/Y H:i'),

                'cliente' => $solicitud->cliente ? [
                    'nombre'    => $solicitud->cliente->nombre,
                    'apellidos' => $solicitud->cliente->apellidos,
                    'email'     => $solicitud->cliente->email,
                    'telefono'  => $solicitud->cliente->telefono,
                ] : null,

                'profesional' => $solicitud->profesional ? [
                    'empresa'          => $solicitud->profesional->empresa,
                    'email_empresa'    => $solicitud->profesional->email_empresa,
                    'telefono_empresa' => $solicitud->profesional->telefono_empresa,
                    'ciudad'           => $solicitud->profesional->ciudad,
                    'provincia'        => $solicitud->profesional->provincia,
                ] : null,

                'presupuesto' => $presupuestoPrincipal ? [
                    'id'     => $presupuestoPrincipal->id,
                    'estado' => $presupuestoPrincipal->estado,
                    'total'  => $presupuestoPrincipal->total,
                ] : null,

                'trabajo' => $trabajoPrincipal ? [
                    'id'        => $trabajoPrincipal->id,
                    'estado'    => $trabajoPrincipal->estado,
                    'fecha_ini' => $trabajoPrincipal->fecha_ini->format('d/m/Y H:i'),
                    'fecha_fin' => $trabajoPrincipal->fecha_fin->format('d/m/Y H:i'),
                    'dir_obra'  => $trabajoPrincipal->dir_obra,
                ] : null,
            ]);
        }

        // Fallback por si quieres una vista normal
        return view('layouts.profesional.solicitudes.show', compact('solicitud'));
    }


    /**
     * Cancelar una solicitud (cambiar estado a cancelada).
     */
    public function cancelar(Solicitud $solicitud)
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional;

        // 1) Seguridad: que el profesional sea el dueño de la solicitud
        if (! $perfil || $solicitud->pro_id !== $perfil->id) {
            return back()->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // 2) Solo se pueden cancelar abiertas o en revisión
        if (! in_array($solicitud->estado, ['abierta', 'en_revision'], true)) {
            return back()->with('error', 'Solo puedes cancelar solicitudes abiertas o en revisión.');
        }

        // 3) Cambiamos estado a cancelada
        $solicitud->estado = 'cancelada';
        $solicitud->save();

        // 4) Cargamos relaciones para el correo
        $solicitud->load(['cliente', 'profesional', 'presupuestos.trabajo']);

        $cliente     = $solicitud->cliente;          // User
        $perfilPro   = $solicitud->profesional;      // Perfil_Profesional
        $presupuesto = $solicitud->presupuestos->first(); 
        $trabajo     = $presupuesto?->trabajo;

        // 5) Enviar correo al cliente (si tiene email)
        if ($cliente && $cliente->email) {
            try {
                Mail::to($cliente->email)->send(
                    new SolicitudCanceladaClienteMailable(
                        $solicitud,
                        $cliente,
                        $perfilPro,
                        $presupuesto,
                        $trabajo
                    )
                );
            } catch (\Throwable $e) {
                // Si el mail falla, la solicitud ya está cancelada igual
                return back()->with('error', 'La solicitud se ha cancelado, pero no se ha podido enviar el correo al cliente.');
            }
        }

        return back()->with('success', 'La solicitud se ha cancelado correctamente. El cliente ha sido notificado por correo.');
    }
}
