<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

//Controlador para la autenticación usando Laravel Sanctum
class AuthController extends Controller
{
    public function mostrarFormCliente()
    {
        return view('auth.registro_cliente');
    }

    public function registrarCliente(Request $request)
    {

        // Manejo imagen avatar
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $image = $request->file('avatar');
            $path = 'img/avatarUser/' . date('Ymd') . '/';
            $filename = time() . '_' . $image->getClientOriginalName();

            // Crear carpeta si no existe
            Storage::disk('public')->makeDirectory($path);

            // Guardar archivo
            $image->storeAs($path, $filename, 'public');

            $avatarPath = 'storage/' . $path . $filename;
        } else {
            // Imagen por defecto
            $avatarPath = 'storage/img/avatarUser/avatar_default.png';
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
}
