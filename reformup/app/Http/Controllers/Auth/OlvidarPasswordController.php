<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class OlvidarPasswordController extends Controller
{
    // Muestra el formulario donde el usuario pone su email
    public function mostrarLinkRequestForm()
    {
        return view('auth.olvidar_password');
    }

    // Envía el email con el enlace de reseteo
    public function enviarResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Debes introducir un correo válido.',
        ]);

        // Laravel busca el usuario y genera el token
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Te hemos enviado un correo con instrucciones para restablecer tu contraseña.');
        }

        // Si el correo no existe en la BD
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'No encontramos ningún usuario con ese correo.');
    }
}

