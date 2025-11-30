<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TienePerfilProfesional
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user(); // ya validado por 'rol.redirigir:profesional'

        // Si no tiene perfil_profesional asociado, mandamos a completar el perfil
        $perfilProfesional = $user->perfil_Profesional()->first(); // hasOne en User

        if (! $perfilProfesional) {
            return redirect()
                ->route('profesional.perfil') // lo mandamos a la página para completarlo
                ->with('warning', 'Debes completar tu perfil profesional antes de acceder al panel de profesional.');
        }

        return $next($request);
    }
}
