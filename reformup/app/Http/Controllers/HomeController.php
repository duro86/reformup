<?php

namespace App\Http\Controllers;

use App\Models\Perfil_Profesional;
use App\Models\Comentario;

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

        // Últimos 9 comentarios publicados
        $comentarios = Comentario::query()
            ->where('estado', 'publicado')
            ->where('visible', true)
            ->with(['cliente']) // relación belongsTo User (cliente_id)
            ->orderByDesc('created_at')
            ->take(9)
            ->get();

        // Agrupamos en “slides” de 3 para el carrusel
        $slides = $comentarios->chunk(3);

        return view('home', compact('profesionalesDestacados', 'comentarios', 'slides'));
    }

    public function profesionalesBuscador()
    {
        // La lógica de datos viene desde la API vía Vue,
        // así que aquí solo devolvemos la vista
        return view('layouts.profesionales.index');
    }
}
