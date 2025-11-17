<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfesionalDashboardController extends Controller
{
    // Panel de control para profesionales
    public function index()
    {
        $user   = Auth::user();
        $perfil = $user->perfil_Profesional()->first();

        return view('layouts.profesional.dashboard_profesional', compact('user', 'perfil'));
    }

    //Mostrar configuracion del perfil profesional
}
