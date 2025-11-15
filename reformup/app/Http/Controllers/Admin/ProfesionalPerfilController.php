<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perfil_Profesional;

class ProfesionalPerfilController extends Controller
{
    /**
     * Listado de perfiles profesionales
     */
    public function listarProfesionales()
    {
        // Cargamos también el usuario y sus roles
        $profesionales = Perfil_Profesional::with(['user' => function ($q) {
            $q->with('roles'); // para Spatie
        }])->paginate(5);

        return view('layouts.admin.profesionales.profesionales', compact('profesionales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $perfil = Perfil_Profesional::with('user')->findOrFail($id);

        // Puedes devolver el objeto entero (Laravel lo serializa a JSON)
        return response()->json($perfil);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function eliminar($id)
    {
        $perfil = Perfil_Profesional::with('user')->findOrFail($id);

    // Si quieres hacer algo especial si NO tiene usuario, lo detectas aquí:
    if (! $perfil->user) {
        //
    }

    $perfil->delete(); // o soft delete

    return redirect()
        ->route('admin.profesionales')
        ->with('success', 'Perfil profesional eliminado correctamente.');
    }
}
