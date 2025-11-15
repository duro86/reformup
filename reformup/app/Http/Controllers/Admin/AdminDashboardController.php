<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Spatie\Permission\Traits\HasRoles;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminDashboardController extends Controller
{
    use HasRoles;
    // Método para mostrar el dashboard admin
    public function index()
    {
        return view('layouts.admin.dashboard_admin');
    }

    // Método para listar usuarios (ejemplo adicional)
    public function listarUsuarios()
    {
        $usuarios = User::paginate(5); // todos los campos paginados
        return view('layouts.admin.usuarios', compact('usuarios'));
    }

    //  Mostrar formulario para crear nuevo usuario desde el panel admin
    public function mostrarFormAdminUsuarioNuevo()
    {
        return view('layouts.admin.registro_cliente'); // Vista con formulario para crear usuario siendo admin
    }

    // Crear nuevo usuario desde el panel admin
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
            'provincia' => ['nullable', 'string', 'max:100'],
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

            'provincia.string' => 'La provincia debe ser un texto válido.',
            'provincia.max' => 'La provincia no puede superar los 100 caracteres.',

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
            // Si no sube nada, asignamos el avatar por defecto
            $avatarPath = 'imagenes/avatarUser/avatar_default.png';
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

    // Ver detalles de un usuario
    public function show(User $usuario)
    {
        // Axios envía la cabecera X-Requested-With: XMLHttpRequest porque Laravel la configura en resources/js/bootstrap.js, así que request()->ajax() será true y devolverá JSON.
        if (request()->ajax()) {
            return response()->json($usuario);
        }
        return view('layouts.admin.usuarios.show', compact('usuario'));
    }

    // Mostrar formulario de edición
    public function editarUsuario($id)
    {
        $usuario = User::findOrFail($id);

        // Roles disponibles para mostrar en el formulario
        $allRoles = Role::whereIn('name', ['usuario', 'profesional', 'admin'])
            ->pluck('name')
            ->toArray();

        $currentRoles = $usuario->getRoleNames()->toArray();

        return view('layouts.admin.editar_usuario', compact('usuario', 'allRoles', 'currentRoles'));
    }

    // Actualizar usuario
    public function actualizarUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // Roles válidos sacados de la BD (limitados a los que quieres manejar desde el panel)
        $validRoles = Role::whereIn('name', ['usuario', 'profesional', 'admin'])
            ->pluck('name')
            ->toArray(); // ['usuario','profesional','admin']

        // Validación (email y teléfono únicos, ignorando al propio usuario)
        $request->validate([
            'nombre'     => ['required', 'string', 'max:50'],
            'apellidos'  => ['required', 'string', 'max:100'],
            'email'      => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            // password opcional: solo si se rellena
            'password'   => ['nullable', 'confirmed', 'min:6'],
            'telefono'   => [
                'required',
                'regex:/^[6789]\d{8}$/',
                Rule::unique('users', 'telefono')->ignore($usuario->id),
            ],
            'roles'   => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::in($validRoles)],
            'ciudad'     => ['nullable', 'string', 'max:100'],
            'provincia'  => ['nullable', 'string', 'max:100'],
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
            'roles.required'     => 'Debes seleccionar al menos un rol.',
            'roles.min'          => 'Debes seleccionar al menos un rol.',
            'roles.*.in'         => 'Alguno de los roles seleccionados no es válido.',
        ]);

        // Roles seleccionados desde el formulario
        $rolesSeleccionados = $request->roles ?? [];

        // --- REGLAS DE NEGOCIO SOBRE ROLES ---

        // 1) El usuario debe tener SIEMPRE el rol "usuario"
        if (! in_array('usuario', $rolesSeleccionados)) {
            return back()
                ->with([
                    'error' => 'El usuario debe tener siempre el rol básico de usuario. ' .
                        'Si quieres eliminar al usuario, hazlo desde la opción de eliminar, no quitándole el rol. O si no tiene perfil de usuario, deberias registrarlo primero.',
                ])
                ->withInput();
        }

        // 2) No puede quitar "profesional" si tiene perfil_profesional
        $teniaProfesional = $usuario->hasRole('profesional');
        $quitandoProfesional = $teniaProfesional && ! in_array('profesional', $rolesSeleccionados);
        $tienePerfilProfesional = $usuario->perfil_Profesional()->exists();

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

            // opcional: borrar avatar anterior si no es el default
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
            // si no sube nada, dejamos el que ya tenía
            $avatarPath = $usuario->avatar;
        }

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

        // Si el admin cambia la contraseña
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password);
        }

        // Guardamos Usuario
        $usuario->save();

        // --- Gestionar roles: esto borra los anteriores y deja solo los seleccionados ---
        $usuario->syncRoles($rolesSeleccionados);

        return redirect()
            ->route('admin.usuarios')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /* public function update(Request $request, User $usuario)
    {
        // validar y actualizar
        $usuario->update($request->all());
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }*/

    // Eliminar usuario desde el panel admin
    public function eliminarUsuario($id)
    {
        $usuario = User::findOrFail($id);

        // Si tiene perfil profesional, lo eliminamos también
        // Usa el nombre correcto de la relación:
        // perfilProfesional()  o  perfil_Profesional()
        if ($usuario->perfilProfesional) {
            $usuario->perfilProfesional->delete();  // o ->forceDelete() si no usas SoftDeletes
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
