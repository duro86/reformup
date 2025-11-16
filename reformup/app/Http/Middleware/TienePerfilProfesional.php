<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Perfil_Profesional; 
use Spatie\Permission\Models\Role;

class TienePerfilProfesional
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si no está logueado por si acaso
        if (!$user) {
            return redirect()->route('login');
        }

        // Si no tiene rol profesional, fuera
        if (!$user->hasRole('profesional')) {
            abort(403, 'No tienes permisos de profesional.');
        }

        // Si no tiene perfil_profesional asociado, mandamos a completar perfil
        $perfilProfesional = $user->perfil_Profesional()->first(); // relación hasOne en User

        if (!$perfilProfesional) {
            return redirect()
                ->route('usuario.dashboard') // tu rVamos al perfil de usuario
                ->with('warning', 'Debes completar tu perfil profesional antes de acceder al panel de profesional.');
        }

        return $next($request);
    }
}
