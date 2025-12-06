<?php

namespace App\Http\Controllers;

use App\Models\Perfil_Profesional;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Top 8 profesionales visibles, ordenados por puntuación y nombre
        $profesionalesDestacados = Perfil_Profesional::query()
            ->where('visible', true)
            ->orderByDesc('puntuacion_media')
            ->orderBy('empresa')
            ->with('oficios') //relación muchos-a-muchos
            ->take(8)
            ->get();

        // Últimos 6 comentarios publicados (con cliente + profesional)
        $comentarios = Comentario::query()
            ->where('estado', 'publicado')
            ->where('visible', true)
            ->with([
                'cliente',                             // cliente que comenta
                'trabajo.presupuesto.profesional',     // profesional al que va ligado el trabajo
            ])
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        // Cada "slide" tendrá hasta 2 comentarios
        $slides = $comentarios->chunk(2);

        return view('home', compact('profesionalesDestacados', 'comentarios', 'slides'));
    }

    public function profesionalesBuscador()
    {
        // La lógica de datos viene desde la API vía Vue,
        // así que aquí solo devolvemos la vista
        return view('layouts.profesionales.index');
    }

    
}
