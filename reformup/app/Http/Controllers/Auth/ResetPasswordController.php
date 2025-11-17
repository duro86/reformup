<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    // Muestra el formulario de "nueva contraseña"
    public function mostrarResetForm(Request $request, $token)
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // Procesa el cambio de contraseña
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'password.required'  => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // Laravel se encarga de validar el token, el email, etc.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('success', 'Tu contraseña se ha restablecido correctamente. Ya puedes iniciar sesión.');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'El enlace no es válido o ha caducado. Solicita uno nuevo.');
    }
}
