<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthProController extends Controller
{
    public function mostrarOpcionesPro()
    {
        return view('auth.registro_pro');
    }

    public function registrarProfesional(Request $request)
    {
        $mode = $request->input('mode');

        if ($mode === 'existing') {
            // Validar solo email y campos empresa
            $request->validate([
                'email' => ['required', 'email', 'exists:users,email'],
                'empresa' => ['required', 'string', 'max:255'],
                // Añade las validaciones de otros campos empresa aquí
            ]);

            $user = User::where('email', $request->email)->first();

            // Crear empresa ligada a este usuario
            /*Empresa::create([
                'user_id' => $user->id,
                'nombre' => $request->empresa,
                // Completar otros campos empresa
            ]);*/

            // Opcional: asignar rol profesional si usas Spatie
            $user->assignRole('profesional');

            return redirect()->route('home')->with('success', 'Empresa creada y ligada a tu usuario.');
        }

        else {
            // Validar datos completos usuario y empresa
            $request->validate([
                'nombre' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'empresa' => ['required', 'string', 'max:255'],
                // Más validaciones empresa y usuario
            ]);

            // Crear usuario
            $user = User::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                // Otros campos usuario
            ]);

            // Asignar rol profesional si usas Spatie
            $user->assignRole('profesional');

            return redirect()->route('home')->with('success', 'Registro completado correctamente.');
        }
    }
}


