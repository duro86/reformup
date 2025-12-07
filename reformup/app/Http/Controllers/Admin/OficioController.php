<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Oficio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OficioController extends Controller
{
    /**
     * Listado de oficios + formulario de alta rápida.
     */
    public function index()
    {
        $oficios = Oficio::orderBy('nombre')->paginate(5);

        return view('layouts.admin.oficios.index', [
            'oficios' => $oficios,
        ]);
    }

    /**
     * Guardar un nuevo oficio.
     */
    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:oficios,nombre',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre del oficio es obligatorio.',
            'nombre.unique'   => 'Ya existe un oficio con ese nombre.',
        ]);

        $slug = Str::slug($validated['nombre']);

        if (Oficio::where('slug', $slug)->exists()) {
            $slug .= '-' . uniqid();
        }

        Oficio::create([
            'nombre'      => $validated['nombre'],
            'slug'        => $slug,
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return redirect()
            ->route('admin.oficios')
            ->with('success', 'Oficio creado correctamente.');
    }

    /**
     * Formulario de edición de un oficio.
     */
    public function editar(Oficio $oficio)
    {
        return view('layouts.admin.oficios.editar', compact('oficio'));
    }

    /**
     * Actualizar un oficio existente.
     */
    public function actualizar(Request $request, Oficio $oficio)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:100|unique:oficios,nombre,' . $oficio->id,
            'descripcion' => 'nullable|string|max:500',
        ]);

        $oficio->nombre      = $validated['nombre'];
        $oficio->descripcion = $validated['descripcion'] ?? null;

        if ($oficio->isDirty('nombre')) {
            $slug = Str::slug($validated['nombre']);

            if (Oficio::where('slug', $slug)->where('id', '!=', $oficio->id)->exists()) {
                $slug .= '-' . uniqid();
            }

            $oficio->slug = $slug;
        }

        $oficio->save();

        return redirect()
            ->route('admin.oficios')
            ->with('success', 'Oficio actualizado correctamente.');
    }

    /**
     * Eliminar oficio (si no tiene profesionales asociados).
     */
    public function eliminar(Oficio $oficio)
    {
        if ($oficio->profesionales()->exists()) {
            return redirect()
                ->route('admin.oficios')
                ->with('error', 'No puedes eliminar un oficio que tiene profesionales asociados.');
        }

        $oficio->delete();

        return redirect()
            ->route('admin.oficios')
            ->with('success', 'Oficio eliminado correctamente.');
    }
}
