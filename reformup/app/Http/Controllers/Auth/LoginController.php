<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;   // <-- importa el trait

class LoginController extends Controller
{
    public function mostrarLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validación
        $credenciales = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Debes ingresar un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $remember = $request->boolean('remember', false);

        // Happy path: credenciales correctas
        if (Auth::attempt($credenciales, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirección por rol (Spatie)
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Bienvenido/a al panel de administración');
            }

            if ($user->hasRole('profesional')) {
                return redirect()->route('profesional.dashboard')
                    ->with('success', 'Bienvenido/a a tu panel de profesional');
            }

            // Rol por defecto: usuario
            return redirect()->route('usuario.dashboard')
                ->with('success', 'Bienvenido/a');
        }

        // 3) Error: diferenciamos “no existe” vs “contraseña incorrecta”
        $userExiste = User::where('email', $request->email)->exists();

        if (!$userExiste) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'El usuario no está registrado');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'La contraseña es incorrecta');
    }

    public function logout(Request $request)
    {
        Auth::logout();                    // Cierra la sesión del usuario autenticado: borra la información de autenticación.
        $request->session()->invalidate(); // Invalida la sesión actual, borrando todos los datos de sesión.
        $request->session()->regenerateToken(); // Genera un nuevo token CSRF para evitar ataques de falsificación de solicitudes.

        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    }
}
