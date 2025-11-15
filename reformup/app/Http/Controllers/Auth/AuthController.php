<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

//Controlador para la autenticación usando Laravel Sanctum
class AuthController extends Controller
{
    public function mostrarFormCliente()
    {
        return view('auth.registro_cliente');
    }

    public function registrarCliente(Request $request)
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
        return redirect()->route('home')->with('success', 'Registro completado correctamente');
    }

    /* public function registrarAdmin(Request $request)--Crear 1 solo admin
    {
        // Validación de datos (similar al cliente, agregar o quitar campos según necesidad)
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
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048']
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

        // Manejo imagen avatar igual que en registrarCliente
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $image = $request->file('avatar');
            $path = 'img/avatarUser/' . date('Ymd') . '/';
            $filename = time() . '_' . $image->getClientOriginalName();

            Storage::disk('public')->makeDirectory($path);
            $image->storeAs($path, $filename, 'public');
            $avatarPath = 'storage/' . $path . $filename;
        } else {
            $avatarPath = 'storage/img/admin/avatar_admin.png';
        }

        // Crear usuario y asignar rol admin
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

        $user->assignRole('admin'); // Asignar rol admin

        return redirect()->route('home')->with('success', 'Usuario administrador registrado correctamente.');
    }*/
}
