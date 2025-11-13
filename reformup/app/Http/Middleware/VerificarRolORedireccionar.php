<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // 
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;   // <-- importa el trait

class VerificarRolORedireccionar
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            // Si no está autenticado, redirige a login con mensaje de error
            return redirect()->route('login')
                ->with('error', 'Inicia sesión para continuar.');
        }

        // Verifica si el usuario tiene alguno de los roles permitidos
        if (!Auth::user()->hasAnyRole($roles)) {
            // Si no tiene el rol, redirige a la página principal con mensaje de error
            return redirect()->route('home')
                ->with('error', 'No tienes permisos para acceder a esa sección.');
        }

        // Si pasa las dos verificaciones, permite que la solicitud continúe
        return $next($request);
    }
}
