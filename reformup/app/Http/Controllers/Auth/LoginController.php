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
     * Procesa el login del usuario. Validamos campos
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

        // 2) PRIMER INTENTO → login normal por users.email
        if (Auth::attempt($credenciales, $remember)) {

            $request->session()->regenerate();
            $user = Auth::user();

            return $this->redirigirSegunRol($user);
        }

        // 3) SEGUNDO INTENTO → login por email de EMPRESA
        $perfil = Perfil_Profesional::where('email_empresa', $request->email)->first();

        if ($perfil) {
            $user = $perfil->user;

            if ($user && Auth::attempt([
                'email' => $user->email,
                'password' => $request->password
            ], $remember)) {

                $request->session()->regenerate();

                return redirect()
                    ->route('profesional.dashboard')
                    ->with('success', 'Bienvenido/a a tu panel profesional');
            }

            // Existe el profesional pero la contraseña es incorrecta
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'La contraseña es incorrecta');
        }

        // 4) NO existe ni como usuario ni como profesional
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'No existe ningún usuario ni profesional con ese email');
    }

    private function redirigirSegunRol($user)
    {
        if ($user->hasRole('admin')) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Bienvenido/a al panel de administración');
        }

        if ($user->hasRole('profesional')) {

            $perfilProfesional = $user->perfil_Profesional()->first();

            if (!$perfilProfesional) {
                return redirect()
                    ->route('usuario.dashboard')
                    ->with('warning', 'Tienes el rol de profesional pero aún no has completado tu perfil de empresa.');
            }

            return redirect()
                ->route('usuario.dashboard')
                ->with('success', 'Bienvenido/a. Accede a tu panel profesional desde tu panel.');
        }

        if ($user->hasRole('usuario')) {
            return redirect()
                ->route('usuario.dashboard')
                ->with('success', 'Bienvenido/a');
        }

        return redirect()
            ->route('home')
            ->with('error', 'Error en el inicio de sesión.');
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
