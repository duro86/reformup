<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Spatie\Permission\Traits\HasRoles;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    use HasRoles;
    // Método para mostrar el dashboard admin
    public function index()
    {
        return view('layouts.admin.dashboard_admin');
    }

    // Método para listar usuarios (ejemplo adicional)
    public function listarUsuarios()
    {
        $usuarios = User::paginate(5); // todos los campos paginados
        return view('layouts.admin.usuarios', compact('usuarios'));
    }

    public function show(User $usuario)
    {
        return view('admin.usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        // validar y actualizar
        $usuario->update($request->all());
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
