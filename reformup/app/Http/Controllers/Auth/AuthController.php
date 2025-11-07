<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

//Controlador para la autenticación usando Laravel Sanctum
class AuthController extends Controller
{
    public function mostrarFormCliente()
    {
        return view('auth.registro_cliente');
    }

    public function registrarCliente(Request $request)
    {

        // Validación de los datos del formulario de registro
        $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:6'],
            'telefono' => ['required', 'regex:/^[6789]\d{8}$/'],
            'ciudad' => ['nullable','string', 'max:100'],
            'provincia' => ['nullable','string', 'max:100'],
            'direccion' => ['nullable','string', 'max:255'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres',
            'apellidos.required' => 'Los apellidos son obligatorios',
            'apellidos.max' => 'Los apellidos no pueden tener más de 100 caracteres',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El formato del correo electrónico no es válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.regex' => 'El formato del teléfono no es válido (debe ser un número español)',
            'ciudad.max' => 'La ciudad no puede tener más de 100 caracteres',
            'provincia.max' => 'La provincia no puede tener más de 100 caracteres',
            'cp' => ['regex:/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/'], // Códigos postales españoles
            'cp.regex' => 'El código postal no es válido (debe ser un código postal español)',
            'direccion.max' => 'La dirección no puede tener más de 255 caracteres',
            'avatar.max' => 'La ruta del avatar no puede tener más de 255 caracteres',
        ]);


        // Gestion Avatar imagen
        if ($request->hasFile('avatar')) {
            $avatarName = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $request->file('avatar')->move(public_path('img/avatarUser'), $avatarName);
            $avatarPath = 'img/avatarUser/' . $avatarName;
        } else {
            $avatarPath = 'img/avatarUser/avatar_default.webp';
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
            'avatar' => $request->avatarPath
        ]);


        $user->assignRole('cliente'); // Usando Spatie asigando el rol de cliente

        // Volver a la página de inicio con un mensaje de éxito
        return redirect()->route('home')->with('success', 'Registro completado correctamente');
    }
}
