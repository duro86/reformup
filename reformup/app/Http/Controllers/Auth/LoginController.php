<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Perfil_Profesional;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;   // <-- importa el trait



class LoginController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function mostrarLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa el login del usuario.
     */
    public function login(Request $request)
    {
        // 1) Validación básica
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

        if (Auth::attempt($credenciales, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 2) Si es admin, se va al panel de admin sí o sí
            if ($user->hasRole('admin')) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('success', 'Bienvenido/a al panel de administración');
            }

            // 3) Si tiene rol profesional, revisamos si tiene perfil profesional
            if ($user->hasRole('profesional')) {
                $perfilProfesional = $user->perfil_Profesional()->first();

                if (!$perfilProfesional) {
                    // Tiene rol profesional pero no perfil empresa
                    return redirect()
                        ->route('usuario.dashboard')
                        ->with('warning', 'Tienes el rol de profesional pero aún no has completado tu perfil de empresa. Complétalo para poder usar el panel de profesional.');
                }

                // Ojo: aun así lo mantenemos entrando por usuario.dashboard
                // Desde allí le pones un botón "Ir a mi panel profesional"
                return redirect()
                    ->route('usuario.dashboard')
                    ->with('success', 'Bienvenido/a. Tienes acceso como profesional desde tu panel.');
            }

            // 4) Por defecto, cualquier usuario normal → dashboard usuario
            if ($user->hasRole('usuario')) {
                return redirect()
                    ->route('usuario.dashboard')
                    ->with('success', 'Bienvenido/a');
            }

            // 5) Fallback muy raro
            return redirect()
                ->route('home')
                ->with('error', 'Error en el inicio de sesión.');
        }

        // 6) Credenciales incorrectas
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


    /**
     * Cierra la sesión del usuario autenticado.
     */
    public function logout(Request $request)
    {
        Auth::logout();                    // Cierra la sesión del usuario autenticado: borra la información de autenticación.
        $request->session()->invalidate(); // Invalida la sesión actual, borrando todos los datos de sesión.
        $request->session()->regenerateToken(); // Genera un nuevo token CSRF para evitar ataques de falsificación de solicitudes.

        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    }
}
