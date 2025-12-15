<?php

// app/Http/Controllers/Usuario/UsuarioComentarioController.php
namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use App\Models\User;
use App\Models\Perfil_Profesional;
use App\Models\Comentario;
use App\Mail\Admin\ComentarioPendienteMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Traits\FiltroRangoFechas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class UsuarioComentarioController extends Controller
{

    use FiltroRangoFechas;
    /**
     * Listado de comentarios del usuario.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()
                ->route('login')
                ->with('error', 'Debes iniciar sesión para ver tus comentarios.');
        }

        // Estados posibles para el select / filtros
        // Estados disponibles desde el modelo (sin "Todos")
        $estados = Comentario::ESTADOS;

        // Parámetros de filtro
        $q             = trim((string) $request->query('q'));
        $estado        = $request->query('estado');          // pendiente | publicado | rechazado | null
        $puntuacionMin = $request->query('puntuacion_min');
        $puntuacionMax = $request->query('puntuacion_max');

        // Base: comentarios de este cliente
        $query = Comentario::with([
            'trabajo.presupuesto.solicitud',
            'trabajo.presupuesto.profesional',
        ])
            ->where('cliente_id', $user->id);

        //  Filtro por estado (solo si es uno válido)
        if ($estado !== null && $estado !== '') {
            if (array_key_exists($estado, $estados)) {
                $query->where('estado', $estado);
            }
        }

        //  Buscador de texto
        if ($q !== '') {
            $like = '%' . $q . '%';

            $query->where(function ($sub) use ($like) {
                $sub
                    // Título de la solicitud
                    ->whereHas('trabajo.presupuesto.solicitud', function ($qSol) use ($like) {
                        $qSol->where('titulo', 'like', $like);
                    })
                    // Profesional (empresa / email)
                    ->orWhereHas('trabajo.presupuesto.profesional', function ($qPro) use ($like) {
                        $qPro->where('empresa', 'like', $like)
                            ->orWhere('email_empresa', 'like', $like);
                    })
                    // Opinión
                    ->orWhere('opinion', 'like', $like)
                    // Estado (por si el usuario escribe "publicado" en el buscador)
                    ->orWhere('estado', 'like', $like);
            });
        }

        //  Filtro por puntuación mínima / máxima
        $pMin = null;
        $pMax = null;

        if ($puntuacionMin !== null && $puntuacionMin !== '') {
            $pMin = (int) $puntuacionMin;
            if ($pMin < 1) $pMin = 1;
            if ($pMin > 5) $pMin = 5;
        }

        if ($puntuacionMax !== null && $puntuacionMax !== '') {
            $pMax = (int) $puntuacionMax;
            if ($pMax < 1) $pMax = 1;
            if ($pMax > 5) $pMax = 5;
        }

        // Si ambos están definidos y el min > max, los intercambiamos
        if ($pMin !== null && $pMax !== null && $pMin > $pMax) {
            [$pMin, $pMax] = [$pMax, $pMin];
        }

        if ($pMin !== null) {
            $query->where('puntuacion', '>=', $pMin);
        }

        if ($pMax !== null) {
            $query->where('puntuacion', '<=', $pMax);
        }

        //  Filtro por rango de fechas (columna 'fecha' del comentario)
        $this->aplicarFiltroRangoFechas($query, $request, 'fecha');

        $comentarios = $query
            ->orderByDesc('fecha')     // si a veces es null, puedes cambiar a created_at
            ->paginate(5)
            ->withQueryString();

        //  Ref correlativa por cliente 
        $total = $comentarios->total();       // total comentarios del usuario
        $first = $comentarios->firstItem();   // índice del primero en la página (1-based)

        foreach ($comentarios as $i => $comentario) {
            // Ejemplo: si hay 23 comentarios, el más nuevo será Ref 23, luego 22...
            $comentario->ref_cliente = $total - ($first + $i) + 1;
        }

        return view('layouts.usuario.comentarios.index', [
            'comentarios'   => $comentarios,
            'user'          => $user,
            'q'             => $q,
            'estado'        => $estado,
            'estados'       => $estados,
            'puntuacionMin' => $puntuacionMin,
            'puntuacionMax' => $puntuacionMax,
        ]);
    }

    /**
     * Formulario para dejar comentario de un trabajo finalizado.
     */
    public function crear(Trabajo $trabajo)
    {
        $user = Auth::user();

        // Cargamos presupuesto y solicitud del trabajo
        $trabajo->load('presupuesto.solicitud');

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;

        // El trabajo debe ser suyo y debe haber solicitado el trabajo y presupuesto asociado
        if (! $solicitud || $solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes valorar trabajos de otros usuarios.');
        }

        // Solo trabajos finalizados
        if ($trabajo->estado !== 'finalizado') {
            return back()->with('error', 'Solo puedes comentar trabajos finalizados.');
        }

        // Evitar duplicados
        $yaComentado = Comentario::where('trabajo_id', $trabajo->id)
            ->where('cliente_id', $user->id)
            ->exists();

        if ($yaComentado) {
            return back()->with('error', 'Ya has dejado un comentario para este trabajo.');
        }

        return view('layouts.usuario.comentarios.crear', compact('trabajo'));
    }

    /**
     * Guardar comentario.
     */
    public function guardar(Request $request, Trabajo $trabajo)
    {
        $user = Auth::user();

        // Cargar presupuesto y solicitud
        $trabajo->load('presupuesto.solicitud');

        $presupuesto = $trabajo->presupuesto;
        $solicitud   = $presupuesto?->solicitud;

        // 1) El trabajo debe ser suyo
        if (! $solicitud || $solicitud->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes valorar trabajos de otros usuarios.');
        }

        // 2) Solo trabajos finalizados
        if ($trabajo->estado !== 'finalizado') {
            return back()->with('error', 'Solo puedes comentar trabajos finalizados.');
        }

        // 3) Evitar duplicados
        $yaComentado = Comentario::where('trabajo_id', $trabajo->id)
            ->where('cliente_id', $user->id)
            ->exists();

        if ($yaComentado) {
            return back()->with('error', 'Ya has dejado un comentario para este trabajo.');
        }

        // 4) Validación (puntuación, opinión y hasta 3 imágenes)
        $validated = $request->validate(
            [
                'puntuacion'   => 'required|integer|min:1|max:5',
                'opinion'      => 'nullable|string|max:200',

                // imágenes opcionales, máximo 3
                'imagenes'     => 'nullable|array|max:3',
                'imagenes.*'   => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            ],
            [
                'imagenes.array'   => 'El formato de las imágenes no es válido.',
                'imagenes.max'     => 'Solo puedes subir hasta 3 imágenes por comentario.',
                'opinion.max'     => 'Maximo 200 caracteres en la opinión',
                'imagenes.*.image' => 'Cada archivo debe ser una imagen.',
                'imagenes.*.mimes' => 'Formatos permitidos: JPG, PNG o WEBP.',
                'imagenes.*.max'   => 'Cada imagen no puede superar los 2MB.',
            ]
        );

        // Limpiamos con el purifier
        $opinion = $validated['opinion'] ?? null;

        $opinion_limpia = $opinion
            ? Purifier::clean($opinion, 'solicitud')
            : null;

        try {
            // 5) Crear comentario
            $comentario = Comentario::create([
                'trabajo_id' => $trabajo->id,
                'cliente_id' => $user->id,
                'puntuacion' => $validated['puntuacion'],
                'opinion'    => $opinion_limpia,
                'estado'     => 'pendiente',
                'visible'    => false,
                'fecha'      => now(),
            ]);

            // 6) Guardar imágenes (si las hay)
            if ($request->hasFile('imagenes')) {
                $orden = 1;

                foreach ($request->file('imagenes') as $imagen) {
                    if (! $imagen || ! $imagen->isValid()) {
                        continue;
                    }

                    // carpeta por comentario: public/comentarios/{id}
                    $dir  = 'comentarios/' . $comentario->id;
                    $ruta = $imagen->store($dir, 'public');

                    $comentario->imagenes()->create([
                        'ruta'  => $ruta,
                        'orden' => $orden++,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // \Log::error('Error creando comentario: '.$e->getMessage());
            return back()->with('error', 'Fallo al crear su comentario, póngase en contacto con el administrador');
        }

        // Cargamos profesional (perfil) por si lo quieres usar en el correo
        $profesional = $presupuesto?->profesional ?? null;

        // 7) Enviar mail a todos los admins avisando del nuevo comentario pendiente
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            if (! $admin->email) {
                continue;
            }

            try {
                Mail::to($admin->email)->send(
                    new ComentarioPendienteMailable($comentario, $trabajo, $user, $profesional)
                );
            } catch (\Throwable $e) {
                return back()->with('error', 'Fallo al notificar al administrador sobre el nuevo comentario.');
            }
        }

        return redirect()
            ->route('usuario.trabajos.index')
            ->with('success', 'Tu comentario se ha enviado y está pendiente de revisión por el administrador.');
    }


    /**
     * Formulario para editar un comentario propio.
     */
    public function editar(Request $request, Comentario $comentario)
    {
        $user = Auth::user();

        // Solo su propio comentario
        if ($comentario->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes editar los comentarios de otros usuarios.');
        }

        // Solo permitir edición si está pendiente o rechazado
        if (! in_array($comentario->estado, ['pendiente', 'rechazado'])) {
            return redirect()
                ->route('usuario.comentarios.index')
                ->with('error', 'Solo puedes editar comentarios pendientes o rechazados.');
        }

        // Cargar relaciones necesarias (añadimos imagenes)
        $comentario->load('trabajo.presupuesto.solicitud', 'imagenes');

        // Correlativo SOLO de este cliente para mostrar en la vista
        $idsOrdenados = Comentario::where('cliente_id', $user->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->pluck('id');

        $pos = $idsOrdenados->search($comentario->id);
        $refCliente = $pos === false ? $comentario->id : ($idsOrdenados->count() - $pos);

        return view('layouts.usuario.comentarios.editar', compact('comentario', 'refCliente'));
    }


    /**
     * Actualizar comentario propio.
     * Al editar, lo volvemos a poner en "pendiente" y oculto.
     */
    public function actualizar(Request $request, Comentario $comentario)
    {
        $user = Auth::user();

        // 1) El comentario debe ser suyo
        if ($comentario->cliente_id !== $user->id) {
            return back()->with('error', 'No puedes editar los comentarios de otros usuarios.');
        }

        // 2) Solo se puede editar si está pendiente o rechazado
        if (! in_array($comentario->estado, ['pendiente', 'rechazado'])) {
            return redirect()
                ->route('usuario.comentarios.index')
                ->with('error', 'Solo puedes editar comentarios pendientes o rechazados.');
        }

        // 3) Validación (igual que en guardar)
        $validated = $request->validate(
            [
                'puntuacion'   => 'required|integer|min:1|max:5',
                'opinion'      => 'nullable|string|max:2000',

                'imagenes'     => 'nullable|array|max:3',
                'imagenes.*'   => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            ],
            [
                'imagenes.array'   => 'El formato de las imágenes no es válido.',
                'imagenes.max'     => 'Solo puedes subir hasta 3 imágenes por comentario.',
                'imagenes.*.image' => 'Cada archivo debe ser una imagen.',
                'imagenes.*.mimes' => 'Formatos permitidos: JPG, PNG o WEBP.',
                'imagenes.*.max'   => 'Cada imagen no puede superar los 2MB.',
            ]
        );

        // 4) Limpiamos la opinión con Purifier (igual que en guardar)
        $opinion = $validated['opinion'] ?? null;

        $opinion_limpia = $opinion
            ? Purifier::clean($opinion, 'solicitud')
            : null;

        try {
            // 5) Actualizar comentario
            $comentario->puntuacion = $validated['puntuacion'];
            $comentario->opinion    = $opinion_limpia;
            $comentario->estado     = 'pendiente';  // vuelve a revisión
            $comentario->visible    = false;
            $comentario->fecha      = now();
            $comentario->save();

            // 6) Si el usuario ha subido nuevas imágenes, reemplazamos las anteriores
            if ($request->hasFile('imagenes')) {
                // 6.1) Borrar ficheros anteriores del disco
                foreach ($comentario->imagenes as $img) {
                    if ($img->ruta && Storage::disk('public')->exists($img->ruta)) {
                        Storage::disk('public')->delete($img->ruta);
                    }
                }

                // 6.2) Borrar registros de BD
                $comentario->imagenes()->delete();

                // 6.3) Guardar las nuevas
                $orden = 1;

                foreach ($request->file('imagenes') as $imagen) {
                    if (! $imagen || ! $imagen->isValid()) {
                        continue;
                    }

                    $dir  = 'comentarios/' . $comentario->id;
                    $ruta = $imagen->store($dir, 'public');

                    $comentario->imagenes()->create([
                        'ruta'  => $ruta,
                        'orden' => $orden++,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // \Log::error('Error actualizando comentario: '.$e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al actualizar tu comentario.');
        }

        // 7) Cargamos el trabajo y el profesional para el correo
        $trabajo = $comentario->trabajo;

        $presupuesto = null;
        $profesional = null;

        if ($trabajo) {
            $trabajo->load('presupuesto.profesional', 'presupuesto.solicitud');
            $presupuesto = $trabajo->presupuesto;
            $profesional = $presupuesto?->profesional;
            $solicitud   = $presupuesto?->solicitud;
        }

        // 8) Notificar a admins de que hay comentario editado pendiente
        $admins = User::role('admin')->get();
        
        foreach ($admins as $admin) {
            if (! $admin->email) {
                continue;
            }
            
            try {
                Mail::to($admin->email)->send(
                    new ComentarioPendienteMailable($comentario, $trabajo, $user, $profesional, $solicitud)
                );
            } catch (\Throwable $e) {
                return redirect()
            ->route('usuario.comentarios.index')
            ->with('error', 'El email no se ha podido enviar al administrador, intentaremos solucionarlo lo antes posible. Revisaremos su comentario.');
            }
        }

        return redirect()
            ->route('usuario.comentarios.index')
            ->with('success', 'Tu comentario se ha actualizado y está pendiente de revisión.');
    }

    public function showJson(Comentario $comentario)
    {
        $user = Auth::user();

        abort_if(!$user || $comentario->cliente_id !== $user->id, 403);

        $comentario->load([
            'trabajo.presupuesto.solicitud:id,titulo,ciudad',
            'trabajo.presupuesto.profesional:id,empresa',
            'imagenes:id,comentario_id,ruta,orden',
        ]);

        return response()->json([
            'id'         => $comentario->id,
            'trabajo_id' => $comentario->trabajo_id,
            'estado'     => $comentario->estado,
            'estado_label' => ucfirst($comentario->estado),
            'visible'    => (bool) $comentario->visible,
            'puntuacion' => (int) $comentario->puntuacion,
            'opinion'    => $comentario->opinion, // puede ser null, Vue lo maneja
            'titulo'     => $comentario->trabajo?->presupuesto?->solicitud?->titulo,
            'ciudad'     => $comentario->trabajo?->presupuesto?->solicitud?->ciudad,
            'profesional' => [
                'empresa' => $comentario->trabajo?->presupuesto?->profesional?->empresa,
            ],
            'imagenes' => $comentario->imagenes->map(fn($img) => [
                'url' => Storage::url($img->ruta),
                'orden' => $img->orden,
            ])->values(),
        ]);
    }
}
