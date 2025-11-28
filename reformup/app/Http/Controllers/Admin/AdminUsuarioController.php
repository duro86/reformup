<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Oficio;
use App\Models\Perfil_Profesional;
use Spatie\Permission\Traits\HasRoles;
use App\Mail\ProfesionalValidado;
use App\Mail\ProfesionalSuspendido;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;
use Exception;
use App\Http\Controllers\Traits\FiltroRangoFechas;


class AdminUsuarioController extends Controller
{
    use FiltroRangoFechas;

    /**
     * Listar y buscar todos los usuarios
     */
    public function listarUsuarios(Request $request)
    {
        $q = $request->input('q'); // texto de búsqueda

        $query = User::query();

        // --- Filtro por texto ---
        if ($q) {
            $qLike = '%' . $q . '%';

            $query->where(function ($sub) use ($qLike) {
                $sub->where('nombre', 'like', $qLike)
                    ->orWhere('apellidos', 'like', $qLike)
                    ->orWhere('email', 'like', $qLike)
                    ->orWhere('telefono', 'like', $qLike);
            });
        }

        // --- Filtro por rango de fechas (usando created_at del usuario) ---
        $this->aplicarFiltroRangoFechas($query, $request, 'created_at');

        // --- Orden + paginación ---
        $usuarios = $query
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString(); // mantiene q, fecha_desde, fecha_hasta

        return view('layouts.admin.usuarios.usuarios', compact('usuarios', 'q'));
    }

    // Exportar todos los usuarios a PDF
    public function exportarUsuariosPdf()
    {
        // Sacamos TODOS los usuarios, sin paginación
        $usuarios = User::orderBy('created_at', 'asc')->get();

        // Cargamos la vista específica para PDF
        $pdf = Pdf::loadView('layouts.admin.usuarios.pdf.usuarios_pdf', [
            'usuarios' => $usuarios,
        ])->setPaper('a4', 'portrait');

        $fileName = 'usuarios-' . now()->format('Ymd-His') . '.pdf';

        //return $pdf->download($fileName); //descargar
        // Abrir en el navegador:
        return $pdf->stream($fileName);
    }

    /**
     * Exporta pdf de búsqueda o página seleccionada
     */
    public function exportarUsuariosPaginaPdf(Request $request)
    {
        $pagina    = (int) $request->input('page', 1);
        $porPagina = 5;
        $busqueda  = $request->input('q');

        $query = User::query();

        if ($busqueda) {
            $like = '%' . $busqueda . '%';
            $query->where(function ($q) use ($like) {
                $q->where('nombre', 'like', $like)
                    ->orWhere('apellidos', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('telefono', 'like', $like);
            });
        }

        //  Mismo filtro de fechas que en el listado
        $this->aplicarFiltroRangoFechas($query, $request, 'created_at');

        $paginator = $query
            ->orderByDesc('created_at')
            ->paginate($porPagina, ['*'], 'page', $pagina);

        $usuarios = $paginator->items();

        $pdf = Pdf::loadView('layouts.admin.usuarios.pdf.usuarios_pdf_pagina', [
            'usuarios' => $usuarios,
            'page'     => $pagina,
            'busqueda' => $busqueda,
        ])->setPaper('a4', 'portrait');

        $fileName = 'usuarios-pagina-' . $pagina . '-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->stream($fileName);
    }


    /**
     * Mostrar formulario para crear nuevo usuario desde el panel admin
     */
    public function mostrarFormAdminUsuarioNuevo()
    {
        return view('layouts.admin.registro_cliente'); // Vista con formulario para crear usuario siendo admin
    }

    /**
     * Crear nuevo usuario desde el panel admin
     */
    public function crearUsuarioNuevo(Request $request)
    {
        // Validación de datos
        $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'telefono' => ['required', 'regex:/^[6789]\d{8}$/', 'unique:users,telefono'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['required', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'cp' => ['nullable', 'regex:/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'] // 2MB máximo
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 50 caracteres.',

            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.string' => 'Los apellidos deben ser un texto válido.',
            'apellidos.max' => 'Los apellidos no pueden superar los 100 caracteres.',

            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debes introducir un correo válido.',
            'email.unique' => 'El email ya está registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono no tiene un formato válido.',
            'telefono.unique' => 'El teléfono ya está registrado.',

            'ciudad.string' => 'La ciudad debe ser un texto válido.',
            'ciudad.max' => 'La ciudad no puede superar los 100 caracteres.',

            'provincia.required' => 'La provincia es obligatoria.',
            'provincia.string'   => 'La provincia debe ser un texto válido.',
            'provincia.max'      => 'La provincia no puede superar los 100 caracteres.',

            'direccion.string' => 'La dirección debe ser un texto válido.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',

            'cp.regex' => 'El código postal no tiene un formato válido.',

            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF o SVG.',
            'avatar.max' => 'La imagen no debe superar los 2MB.'
        ]);

        // --- Manejo imagen avatar al CREAR usuario ---
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {

            $dir  = 'imagenes/avatarUser/' . now()->format('Ymd');
            $ext  = $request->file('avatar')->getClientOriginalExtension();
            $base = pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $file = $safe . '-' . Str::random(8) . '.' . $ext;

            // Creamos el directorio si no existe
            Storage::disk('public')->makeDirectory($dir);

            // Guardamos el archivo en storage/app/public/...
            $request->file('avatar')->storeAs($dir, $file, 'public');

            // Guardamos solo la ruta relativa en BD
            $avatarPath = $dir . '/' . $file;
        } else {
            // Si no sube nada, dejamos nulo
            $avatarPath = null;
        }

        // Insertamos en la tabla users y asignamos el rol de cliente
        $user = User::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'telefono' => $request->telefono,
            'ciudad' => $request->ciudad,
            'provincia' => $request->provincia,
            'cp' => $request->cp,
            'direccion' => $request->direccion,
            'avatar' => $avatarPath
        ]);


        $user->assignRole('usuario'); // Usando Spatie asigando el rol de usuario

        // Volver a la página de inicio con un mensaje de éxito
        return redirect()->route('admin.usuarios')->with('success', 'Usuario: ' . $user->nombre . ' ' . $user->apellidos . '  creado correctamente');
    }

    /**
     * Ver detalles de un usuario
     */
    public function mostrar(User $usuario)
    {
        // Axios envía la cabecera X-Requested-With: XMLHttpRequest porque Laravel la configura en resources/js/bootstrap.js, así que request()->ajax() será true y devolverá JSON.
        if (request()->ajax()) {
            return response()->json($usuario);
        }
        return view('layouts.admin.usuarios.mostrar', compact('usuario'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function editarUsuario($id)
    {
        $usuario = User::findOrFail($id);

        // Roles disponibles para mostrar en el formulario
        $allRoles = Role::whereIn('name', ['usuario', 'profesional', 'admin'])
            ->pluck('name')
            ->toArray();

        $currentRoles = $usuario->getRoleNames()->toArray();

        return view('layouts.admin.usuarios.editar_usuario', compact('usuario', 'allRoles', 'currentRoles'));
    }

    /**
     * Actualizar usuario
     */
    // 
    public function actualizarUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // Roles que SÍ se pueden modificar desde el panel
        $validRoles = Role::whereIn('name', ['profesional', 'admin'])
            ->pluck('name')
            ->toArray(); // ['profesional','admin']

        // Validación
        $request->validate([
            'nombre'     => ['required', 'string', 'max:50'],
            'apellidos'  => ['required', 'string', 'max:100'],
            'email'      => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'password'   => ['nullable', 'confirmed', 'min:6'],
            'telefono'   => [
                'required',
                'regex:/^[6789]\d{8}$/',
                Rule::unique('users', 'telefono')->ignore($usuario->id),
            ],
            // OJO: ahora 'roles' puede venir vacío (solo tendrá 'usuario' fijo)
            'roles'   => ['nullable', 'array'],
            'roles.*' => ['string', Rule::in($validRoles)],
            'ciudad'     => ['nullable', 'string', 'max:100'],
            'provincia' => ['required', 'string', 'max:100'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'cp'         => ['nullable', 'regex:/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/'],
            'avatar'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'email.required'     => 'El email es obligatorio.',
            'email.email'        => 'Debes introducir un correo válido.',
            'email.unique'       => 'El email ya está registrado.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'telefono.regex'     => 'El teléfono no tiene un formato válido.',
            'telefono.unique'    => 'El teléfono ya está registrado.',
            'cp.regex'           => 'El código postal no tiene un formato válido.',
            'avatar.image'       => 'El archivo debe ser una imagen.',
            'avatar.mimes'       => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF o SVG.',
            'avatar.max'         => 'La imagen no debe superar los 2MB.',
            'avatar.uploaded'    => 'La subida de la imagen ha fallado. Por favor, inténtalo de nuevo.',
            'provincia.required' => 'La provincia es obligatoria.',
            'provincia.string'   => 'La provincia debe ser un texto válido.',
            'provincia.max'      => 'La provincia no puede superar los 100 caracteres.',

            // Mensajes de roles (si quieres alguno, aunque ahora es nullable)
            'roles.*.in'         => 'Alguno de los roles seleccionados no es válido.',
        ]);

        // Roles seleccionados desde el formulario (solo admin/profesional)
        $rolesSeleccionados = $request->roles ?? [];

        // Nos aseguramos de que solo haya roles válidos (por si viene algo raro)
        $rolesSeleccionados = array_values(
            array_intersect($rolesSeleccionados, $validRoles)
        );

        // --- REGLAS DE NEGOCIO SOBRE ROLES ---

        // 1) NO controlamos 'usuario' aquí: SIEMPRE lo tendrá por código (no desde el form)

        // 2) No puede quitar "profesional" si tiene perfil_profesional
        $teniaProfesional        = $usuario->hasRole('profesional');
        $quitandoProfesional     = $teniaProfesional && ! in_array('profesional', $rolesSeleccionados);
        $tienePerfilProfesional  = $usuario->perfil_Profesional()->exists();

        if ($quitandoProfesional && $tienePerfilProfesional) {
            return back()
                ->with([
                    'error' => 'No puedes quitar el rol de profesional porque este usuario tiene un perfil profesional/empresa registrado. ' .
                        'Elimina o desactiva primero el perfil profesional para evitar incoherencias.',
                ])
                ->withInput();
        }

        // --- Procesar avatar (si sube uno nuevo) ---
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {

            if ($usuario->avatar && $usuario->avatar !== 'imagenes/avatarUser/avatar_default.png') {
                Storage::disk('public')->delete($usuario->avatar);
            }

            $dir  = 'imagenes/avatarUser/' . now()->format('Ymd');
            $ext  = $request->file('avatar')->getClientOriginalExtension();
            $base = pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $file = $safe . '-' . Str::random(8) . '.' . $ext;

            Storage::disk('public')->makeDirectory($dir);
            $request->file('avatar')->storeAs($dir, $file, 'public');

            $avatarPath = $dir . '/' . $file;
        } else {
            $avatarPath = $usuario->avatar;
        }

        //Manejo de errores
        try {

            // Guardamos la pagina actual de la paginacion
            $paginaActual = $request->input('page', 1);

            // --- Actualizar campos "normales" ---
            $usuario->nombre    = $request->nombre;
            $usuario->apellidos = $request->apellidos;
            $usuario->email     = $request->email;
            $usuario->telefono  = $request->telefono;
            $usuario->ciudad    = $request->ciudad;
            $usuario->provincia = $request->provincia;
            $usuario->cp        = $request->cp;
            $usuario->direccion = $request->direccion;
            $usuario->avatar    = $avatarPath;

            if ($request->filled('password')) {
                $usuario->password = bcrypt($request->password);
            }

            $usuario->save();

            // --- Gestionar roles ---

            // Siempre queremos que tenga el rol 'usuario' aunque no venga en el form
            $rolesFinales = array_unique(array_merge(['usuario'], $rolesSeleccionados));

            // Sobrescribimos roles con: usuario + (admin/profesional según el form)
            $usuario->syncRoles($rolesFinales);

            return redirect()
                ->route('admin.usuarios', ['page' => $paginaActual])
                ->with('success', 'Usuario actualizado correctamente');
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un problema al guardar los datos. Inténtalo de nuevo.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un error inesperado.');
        }
    }


    /**
     * Eliminar usuario desde el panel admin
     */
    public function eliminarUsuario($id)
    {
        $usuario = User::findOrFail($id);

        // Si tiene perfil profesional, lo eliminamos también
        // Usamos el nombre correcto de la relación:
        // perfilProfesional()  o  perfil_Profesional()
        if ($usuario->perfilProfesional) {
            $usuario->perfilProfesional->delete();  // o ->forceDelete() 
        }

        // Si quieres borrar también el avatar físico
        if ($usuario->avatar && $usuario->avatar !== 'imagenes/avatarUser/avatar_default.png') {
            Storage::disk('public')->delete($usuario->avatar);
        }

        // Ahora sí borramos el usuario
        $usuario->delete(); // o ->forceDelete()

        return redirect()
            ->route('admin.usuarios')
            ->with('success', 'Usuario eliminado correctamente (y su perfil profesional, si tenía).');
    }
}
