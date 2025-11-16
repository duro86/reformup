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

                $perfilProfesional = $user->perfil_Profesional()->first(); // null si no existe

                if (!$perfilProfesional) {
                    // OPCIÓN 1: Mantener rol pero forzar completar perfil
                    return redirect()
                        ->route('usuario.dashboard')  // Vamos a usuario dashboard
                        ->with('warning', 'Tienes el rol de profesional pero aún no has completado tu perfil de empresa. Complétalo para poder usar el panel de profesional.');

                    // OPCIÓN 2: Quitarle el rol directamente (yo no lo haría aquí, pero se podría):
                    /*
                    $user->removeRole('profesional');

                    return redirect()
                        ->route('usuario.dashboard')
                        ->with('warning', 'Tu rol de profesional se ha desactivado porque no tienes perfil de empresa. Contacta con soporte o vuelve a registrarte como profesional.');
                    */
                }

                return redirect()->route('profesional.dashboard')
                    ->with('success', 'Bienvenido/a a tu panel de profesional');
            }

            // Por defecto, usuarios normales
            if ($user->hasRole('usuario')) {
                return redirect()->route('usuario.dashboard')
                    ->with('success', 'Bienvenido/a');
            }

            // Fallback raro: si por lo que sea no tiene ninguno
            return redirect()->route('home')->with('error', 'error en el Inicio de Sesión');;
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
