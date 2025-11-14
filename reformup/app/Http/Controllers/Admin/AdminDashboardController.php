<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Spatie\Permission\Traits\HasRoles;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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

    public function mostrarFormAdminUsuarioNuevo()
    {
        return view('layouts.admin.registro_cliente'); // Vista con formulario para crear usuario siendo admin
    }

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

        // Manejo imagen avatar
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $dir = 'imagenes/avatarUser/' . now()->format('Ymd');
            $ext = $request->file('avatar')->getClientOriginalExtension();
            $base = pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $file = $safe . '-' . Str::random(8) . '.' . $ext;

            Storage::disk('public')->makeDirectory($dir);
            $request->file('avatar')->storeAs($dir, $file, 'public');

            $avatarPath = $dir . '/' . $file;                                     // guarda solo la ruta relativa
        } else {
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

    public function show(User $usuario)
    {   
        // Axios envía la cabecera X-Requested-With: XMLHttpRequest porque Laravel la configura en resources/js/bootstrap.js, así que request()->ajax() será true y devolverá JSON.
        if (request()->ajax()) {
            return response()->json($usuario);
        }
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        // validar y actualizar
        $usuario->update($request->all());
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
