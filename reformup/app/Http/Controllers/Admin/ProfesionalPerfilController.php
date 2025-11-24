<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perfil_Profesional;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\PerfilProfesionalOcultoMailable;
use App\Mail\Admin\PerfilProfesionalPublicadoMailable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Oficio;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Exception;

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
        }])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('layouts.admin.profesionales.profesionales', compact('profesionales'));
    }

    /**
     * Publicar / despublicar perfil profesional.
     */
    public function toggleVisible(Request $request, Perfil_Profesional $perfil)
    {
        // Cargamos el usuario asociado (dueño de la cuenta)
        $perfil->load('user');
        $user = $perfil->user;

        // Si no hay usuario asociado, igualmente dejamos hacer el toggle,
        // pero obviamente sin correo.
        $emailDestino = $perfil->email_empresa ?? $user?->email;

        // CASO 1: YA ESTÁ visible → lo ocultamos
        if ($perfil->visible) {
            $perfil->visible = false;
            $perfil->save();

            if ($emailDestino) {
                try {
                    Mail::to($emailDestino)->send(
                        new PerfilProfesionalOcultoMailable($perfil, $user)
                    );
                } catch (\Throwable $e) {
                    return back()->with(
                    'error',
                    'El perfil se ha ocultado, pero el correo ha fallado: '
                );
                }
            }


            return back()->with('success', 'Perfil profesional despublicado correctamente.');
        }

        // CASO 2: NO está visible → lo publicamos (dar de alta en plataforma)
        $perfil->visible = true;
        $perfil->save();

        if ($emailDestino) {
            try {
                Mail::to($emailDestino)->send(
                    new PerfilProfesionalPublicadoMailable($perfil, $user)
                );
            } catch (\Throwable $e) {
                return back()->with(
                    'error',
                    'El perfil se ha publicado, pero el correo ha fallado: '
                );
            }
        }


        return back()->with('success', 'Perfil profesional publicado correctamente.');
    }

    /**
     * Mostrar perfil profesional en formato JSON para vue.js
     */
    public function show($id)
    {
        $perfil = Perfil_Profesional::with('user', 'oficios')->findOrFail($id);

        // Puedes devolver el objeto entero (Laravel lo serializa a JSON)
        return response()->json($perfil);
    }

    /**
     * Mostrar formulario para editar el perfil profesional
     */
    public function editarProfesional($id)
    {

        $perfil = Perfil_Profesional::with('user')->findOrFail($id);

        // Traer todos los oficios para el selector
        $oficios = Oficio::orderBy('nombre')->get(['id', 'nombre', 'slug']);

        // Los IDs de los oficios actuales asignados al profesional (si tienes relación many-to-many)
        $oficiosSeleccionados = $perfil->oficios->pluck('id')->toArray();

        return view('layouts.admin.profesionales.editar_profesional', compact('perfil', 'oficios', 'oficiosSeleccionados'));
    }

    /**
     * Actualizar perfil profesional con datos del formulario
     */
    public function actualizarProfesional(Request $request, $id)
    {

        $perfil = Perfil_Profesional::findOrFail($id);

        // VALIDACIÓN
        $request->validate([
            'empresa' => ['required', 'string', 'max:255'],

            'cif' => [
                'required',
                'string',
                'max:15',
                'regex:/^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/',
                Rule::unique('perfiles_profesionales', 'cif')->ignore($perfil->id),
            ],

            'email_empresa' => [
                'required',
                'email',
                'email:rfc,dns',
                Rule::unique('perfiles_profesionales', 'email_empresa')->ignore($perfil->id),
            ],

            'bio' => ['nullable', 'string', 'max:500'],

            'web' => ['nullable', 'url', 'max:255'],

            'telefono_empresa' => [
                'required',
                'regex:/^(\\+34|0034|34)?[ -]*([6|7|8|9])[ -]*([0-9][ -]*){8}$/',
                Rule::unique('perfiles_profesionales', 'telefono_empresa')->ignore($perfil->id),
            ],

            'ciudad'     => ['nullable', 'string', 'max:120'],
            'provincia'  => ['nullable', 'string', 'max:120'],
            'dir_empresa' => ['nullable', 'string', 'max:255'],

            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],

            'puntuacion_media'    => ['nullable', 'numeric', 'min:0', 'max:5'],
            'trabajos_realizados' => ['nullable', 'integer', 'min:0'],

            'visible' => ['required', 'in:0,1'],

            //Oficios en edición:
            'oficios'   => ['required', 'array', 'min:1'],
            'oficios.*' => ['exists:oficios,id'],
        ], [
            'empresa.required' => 'El nombre de la empresa es obligatorio.',
            'empresa.string'   => 'El nombre de la empresa debe ser texto válido.',

            'cif.required' => 'El CIF es obligatorio.',
            'cif.string'   => 'El CIF debe ser texto válido.',
            'cif.regex'    => 'El CIF no tiene un formato válido.',
            'cif.unique'   => 'Este CIF ya está registrado.',

            'email_empresa.required' => 'El email de la empresa es obligatorio.',
            'email_empresa.email'    => 'Debes introducir un correo empresarial válido.',
            'email_empresa.unique'   => 'Este email de empresa ya está registrado.',

            'telefono_empresa.required' => 'El teléfono de la empresa es obligatorio.',
            'telefono_empresa.regex'    => 'El teléfono de la empresa no tiene el formato correcto.',
            'telefono_empresa.unique'   => 'Este teléfono de empresa ya está registrado.',

            'ciudad.string'     => 'La ciudad debe ser texto válido.',
            'provincia.string'  => 'La provincia debe ser texto válido.',
            'dir_empresa.string' => 'La dirección de la empresa debe ser texto válido.',

            'web.url'  => 'Debes introducir una URL válida para la web.',
            'web.max'  => 'La URL es demasiado larga.',

            'bio.string' => 'La biografía debe ser texto válido.',
            'bio.max'    => 'La biografía es demasiado larga.',

            'puntuacion_media.numeric' => 'La puntuación debe ser un número válido.',
            'puntuacion_media.min'     => 'La puntuación mínima es 0.',
            'puntuacion_media.max'     => 'La puntuación máxima es 5.',

            'trabajos_realizados.integer' => 'Los trabajos realizados deben ser un número entero.',
            'trabajos_realizados.min'     => 'Los trabajos realizados no pueden ser negativos.',

            'visible.required' => 'Debes indicar si el perfil está visible.',
            'visible.in'       => 'El campo visible debe ser Sí o No.',

            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF, SVG o WEBP.',
            'avatar.max'   => 'La imagen no debe superar los 2MB.',

            'oficios.required' => 'Debes seleccionar al menos un oficio.',
        ]);

        // AVATAR
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {

            if ($perfil->avatar) {
                Storage::disk('public')->delete($perfil->avatar);
            }

            $dir  = 'imagenes/avatarProfesional/' . now()->format('Ymd');
            $ext  = $request->file('avatar')->getClientOriginalExtension();
            $base = pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $file = $safe . '-' . Str::random(8) . '.' . $ext;

            Storage::disk('public')->makeDirectory($dir);
            $request->file('avatar')->storeAs($dir, $file, 'public');

            $avatarPath = $dir . '/' . $file;
        } else {
            $avatarPath = $perfil->avatar;
        }

        //Manejo de errores
        try {
            // ACTUALIZAR CAMPOS
            $perfil->empresa             = $request->empresa;
            $perfil->cif                 = $request->cif;
            $perfil->email_empresa       = $request->email_empresa;
            $perfil->telefono_empresa    = $request->telefono_empresa;
            $perfil->ciudad              = $request->ciudad;
            $perfil->provincia           = $request->provincia;
            $perfil->dir_empresa         = $request->dir_empresa;
            $perfil->web                 = $request->web;
            $perfil->bio                 = $request->bio;
            $perfil->puntuacion_media    = $request->puntuacion_media;
            $perfil->trabajos_realizados = $request->trabajos_realizados;
            $perfil->visible             = $request->visible;
            $perfil->avatar              = $avatarPath;

            $perfil->save();

            // Si usas relación muchos-a-muchos oficios:
            $perfil->oficios()->sync($request->oficios ?? []);

            return redirect()
                ->route('admin.profesionales')
                ->with('success', 'Perfil profesional actualizado correctamente');
        } catch (QueryException $e) {
            // Aquí podrías loguear el error técnico
            // Log::error($e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un problema al guardar los datos. Inténtalo de nuevo.');
        } catch (\Throwable $e) {
            // Para cualquier otra excepción inesperada
            // Log::error($e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un error inesperado.');
        }
    }

    /**
     * Elimina solo el perfil profesional (no el usuario)
     */
    public function eliminarProfesional($id)
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
