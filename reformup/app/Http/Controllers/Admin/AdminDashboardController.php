<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Oficio;
use App\Models\Perfil_Profesional;
use Spatie\Permission\Traits\HasRoles;
use App\Mail\ProfesionalValidado;
use App\Mail\ProfesionalSuspendido;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;
use Exception;

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
        $usuarios = User::orderBy('created_at', 'desc')->paginate(5); // todos los campos paginados
        return view('layouts.admin.usuarios.usuarios', compact('usuarios'));
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

    //Boton para activar/desactivar la visibilidad de un profesional
    public function toggleVisible($id)
    {
        $perfil = Perfil_Profesional::findOrFail($id);
        $user   = $perfil->user;

        // Volteamos el estado
        $perfil->visible = !$perfil->visible;
        $perfil->save();

        // Enviar email según el nuevo estado
        if ($perfil->visible) {
            // Ahora es VISIBLE → validar profesional
            if ($user) {
                Mail::to($user->email)->send(new ProfesionalValidado($perfil, $user));
            }

            return back()->with('success', 'El profesional ha sido validado y ahora es visible.');
        } else {
            // Ahora NO es visible → suspensión
            if ($user) {
                Mail::to($user->email)->send(new ProfesionalSuspendido($perfil, $user));
            }

            return back()->with('warning', 'El profesional ha sido ocultado y su cuenta suspendida.');
        }
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

        return view('layouts.admin.usuarios.editar_usuario', compact('usuario', 'allRoles', 'currentRoles'));
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

    // Perfil Usuario Logueado (admin, usuario, profesional...)
    public function mostrarPerfil()
    {
        $usuario = Auth::user();
        $roles   = $usuario->getRoleNames();

        $oficios = Oficio::orderBy('nombre')->get();

        $perfilProfesional = $usuario->perfil_Profesional()
            ->with('oficios')
            ->first();

        $oficiosSeleccionados = $perfilProfesional
            ? $perfilProfesional->oficios->pluck('id')->toArray()
            : [];

        // Roles disponibles para checkboxes
        $allRoles = Role::whereIn('name', ['usuario', 'profesional', 'admin'])
            ->pluck('name')
            ->toArray();

        $currentRoles = $usuario->getRoleNames()->toArray();

        return view('layouts.admin.perfil.perfil', compact(
            'usuario',
            'roles',
            'perfilProfesional',
            'oficios',
            'oficiosSeleccionados',
            'allRoles',
            'currentRoles'
        ));
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();
        $rolesActuales    = $usuario->getRoleNames();
        $perfilProfesional = $usuario->perfil_Profesional()->first(); // puede ser null

        // ---------- 1) VALIDACIÓN DINÁMICA ----------
        $rules = [
            // Bloque USUARIO (siempre activo, porque tu admin también es user)
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
            'ciudad'     => ['nullable', 'string', 'max:100'],
            'provincia'  => ['nullable', 'string', 'max:100'],
            'cp'         => ['nullable', 'regex:/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'avatar'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];

        $messages = [
            'nombre.required'    => 'El nombre es obligatorio.',
            'nombre.string'    => 'El formato no es válido.',
            'nombre.max'    => 'El nombre no puede tener mas de 50 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max' => 'Los apellidos no pueden tener mas de 100 caracteres.',
            'apellidos.string' => 'El formato no es válido.',
            'email.required'     => 'El email es obligatorio.',
            'email.email'        => 'Debes introducir un correo válido.',
            'email.unique'       => 'El email ya está registrado.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'telefono.regex'     => 'El teléfono no tiene un formato válido.',
            'telefono.unique'    => 'El teléfono ya está registrado.',
            'ciudad.max'    => 'El nombre de la ciudad no puede superar los 100 caracteres.',
            'ciudad.string'    => 'El formato no es válido.',
            'provincia.max'    => 'El nombre de la provincia no puede superar los 100 caracteres.',
            'provincia.string'    => 'El formato no es válido.',
            'direccion.max'    => 'El nombre de la dirección no puede superar los 255 caracteres.',
            'direccion.string'    => 'El formato no es válido.',
            'cp.regex'           => 'El código postal no tiene un formato válido.',
            'avatar.image'       => 'El archivo debe ser una imagen.',
            'avatar.mimes'       => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF o WEBP.',
            'avatar.max'         => 'La imagen no debe superar los 2MB.',
        ];

        // Si tiene rol profesional Y perfil profesional, añadimos reglas de profesional
        if ($rolesActuales->contains('profesional') && $perfilProfesional) {
            $rules = array_merge($rules, [
                'empresa' => ['required', 'string', 'max:255'],
                'cif' => [
                    'required',
                    'string',
                    'max:15',
                    'regex:/^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/',
                    Rule::unique('perfiles_profesionales', 'cif')->ignore($perfilProfesional->id),
                ],
                'email_empresa' => [
                    'required',
                    'email',
                    'email:rfc,dns',
                    Rule::unique('perfiles_profesionales', 'email_empresa')->ignore($perfilProfesional->id),
                ],
                'telefono_empresa' => [
                    'required',
                    'regex:/^(\\+34|0034|34)?[ -]*([6|7|8|9])[ -]*([0-9][ -]*){8}$/',
                    Rule::unique('perfiles_profesionales', 'telefono_empresa')->ignore($perfilProfesional->id),
                ],
                'ciudad_empresa'    => ['nullable', 'string', 'max:120'],
                'provincia_empresa' => ['nullable', 'string', 'max:120'],
                'direccion_empresa' => ['nullable', 'string', 'max:255'],
                'web'               => ['nullable', 'url', 'max:255'],
                'bio'               => ['nullable', 'string', 'max:500'],
                'avatar_profesional' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
                'visible'           => ['required', 'in:0,1'],
                'oficios'           => ['required', 'array', 'min:1'],
                'oficios.*'         => ['exists:oficios,id'],
            ]);

            $messages = array_merge($messages, [
                'empresa.required' => 'El nombre de la empresa es obligatorio.',
                'cif.required'     => 'El CIF es obligatorio.',
                'cif.regex'        => 'El CIF no tiene un formato válido.',
                'cif.unique'       => 'Este CIF ya está registrado.',
                'email_empresa.required' => 'El email de la empresa es obligatorio.',
                'email_empresa.email'    => 'Debes introducir un correo empresarial válido.',
                'email_empresa.unique'   => 'Este email de empresa ya está registrado.',
                'telefono_empresa.required' => 'El teléfono de la empresa es obligatorio.',
                'telefono_empresa.regex'    => 'El teléfono de la empresa no tiene el formato correcto.',
                'telefono_empresa.unique'   => 'Este teléfono de empresa ya está registrado.',
                'ciudad_empresa.string'     => 'La ciudad de empresa debe ser texto válido.',
                'provincia_empresa.string'  => 'La provincia de empresa debe ser texto válido.',
                'direccion_empresa.string'  => 'La dirección de empresa debe ser texto válido.',
                'web.url'   => 'Debes introducir una URL válida para la web.',
                'web.max'   => 'La URL es demasiado larga.',
                'bio.string' => 'La biografía debe ser texto válido.',
                'bio.max'    => 'La biografía es demasiado larga.',
                'avatar_profesional.image' => 'El archivo de avatar profesional debe ser una imagen.',
                'avatar_profesional.mimes' => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF, SVG o WEBP.',
                'avatar_profesional.max'   => 'La imagen profesional no debe superar los 2MB.',
                'visible.required' => 'Debes indicar si el perfil profesional está visible.',
                'visible.in'       => 'El campo visible debe ser Sí o No.',
                'oficios.required' => 'Debes seleccionar al menos un oficio.',
            ]);
        }

        // --- Reglas de ROLES (opcional, sólo si vienen en el formulario) ---
        $validRoleNames = Role::whereIn('name', ['usuario', 'profesional', 'admin'])
            ->pluck('name')
            ->toArray();

        $rules['roles'] = ['nullable', 'array', 'min:1'];
        $rules['roles.*'] = ['string', Rule::in($validRoleNames)];

        $messages['roles.array'] = 'El formato de los roles no es válido.';
        $messages['roles.min']   = 'Debes tener al menos un rol asignado.';
        $messages['roles.*.in']  = 'Alguno de los roles seleccionados no es válido.';

        $validated = $request->validate($rules, $messages);

        // ---------- 2) AVATAR USUARIO ----------
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

            $avatarUserPath = $dir . '/' . $file;
        } else {
            $avatarUserPath = $usuario->avatar;
        }

        try {
            // ---------- 3) ACTUALIZAR USUARIO ----------
            $usuario->nombre    = $request->nombre;
            $usuario->apellidos = $request->apellidos;
            $usuario->email     = $request->email;
            $usuario->telefono  = $request->telefono;
            $usuario->ciudad    = $request->ciudad;
            $usuario->provincia = $request->provincia;
            $usuario->cp        = $request->cp;
            $usuario->direccion = $request->direccion;
            $usuario->avatar    = $avatarUserPath;

            if ($request->filled('password')) {
                $usuario->password = bcrypt($request->password);
            }

            $usuario->save();

            // ---------- 4) ACTUALIZAR PROFESIONAL (si aplica) ----------
            if ($rolesActuales->contains('profesional') && $perfilProfesional) {

                // Avatar profesional
                if ($request->hasFile('avatar_profesional') && $request->file('avatar_profesional')->isValid()) {

                    if ($perfilProfesional->avatar) {
                        Storage::disk('public')->delete($perfilProfesional->avatar);
                    }

                    $dirPro  = 'imagenes/avatarProfesional/' . now()->format('Ymd');
                    $extPro  = $request->file('avatar_profesional')->getClientOriginalExtension();
                    $basePro = pathinfo($request->file('avatar_profesional')->getClientOriginalName(), PATHINFO_FILENAME);
                    $safePro = Str::slug($basePro);
                    $filePro = $safePro . '-' . Str::random(8) . '.' . $extPro;

                    Storage::disk('public')->makeDirectory($dirPro);
                    $request->file('avatar_profesional')->storeAs($dirPro, $filePro, 'public');

                    $avatarProPath = $dirPro . '/' . $filePro;
                } else {
                    $avatarProPath = $perfilProfesional->avatar;
                }

                // Guardar datos del perfil profesional
                $perfilProfesional->empresa          = $request->empresa;
                $perfilProfesional->cif              = $request->cif;
                $perfilProfesional->email_empresa    = $request->email_empresa;
                $perfilProfesional->telefono_empresa = $request->telefono_empresa;
                $perfilProfesional->ciudad           = $request->ciudad_empresa;
                $perfilProfesional->provincia        = $request->provincia_empresa;
                $perfilProfesional->dir_empresa      = $request->direccion_empresa;
                $perfilProfesional->web              = $request->web;
                $perfilProfesional->bio              = $request->bio;
                $perfilProfesional->visible          = $request->visible;
                $perfilProfesional->avatar           = $avatarProPath;

                $perfilProfesional->save();

                // relación muchos-a-muchos oficios
                $perfilProfesional->oficios()->sync($request->oficios ?? []);
            }

            // ---------- 5) Sincronizar ROLES (si el formulario los envía) ----------
            if ($request->has('roles')) {
                $nuevosRoles = $request->input('roles', []);

                // Seguridad: NO permitir que un admin se quite su propio rol admin
                if ($rolesActuales->contains('admin') && !in_array('admin', $nuevosRoles)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'roles' => 'No puedes quitarte tu propio rol de administrador.',
                        ]);
                }

                // Sempre tenga al menos 'usuario'
                if (!in_array('usuario', $nuevosRoles)) {
                    $nuevosRoles[] = 'usuario';
                }

                // Guardar los nuevos roles
                $usuario->syncRoles($nuevosRoles);
            }

            // Si vamos bien, redirigimos con éxito
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Tu perfil se ha actualizado correctamente.');
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
}
