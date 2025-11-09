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
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Inicia sesión para continuar.');
        }

        if (!Auth::user()->hasAnyRole($roles)) {
            return redirect()->route('home')
                ->with('error', 'No tienes permisos para acceder a esa sección.');
        }

        return $next($request);
    }
}
