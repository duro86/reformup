<?php

namespace App\Http\Controllers\Profesional;

use App\Http\Controllers\Controller;
use App\Models\Oficio;
use App\Models\Perfil_Profesional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class ProfesionalDashboardController extends Controller
{
    /**
     * Panel Dashboard profesionales, controlamos si tiene perfil profesional o no
     */

    public function index()
    {
        $user = Auth::user();
        $isProfesional = $user->hasRole('profesional');
        $perfil_profesional = $user->perfil_Profesional;

        // Si NO tiene perfil profesional, redirigimos al panel de usuario
        if (! $perfil_profesional) {
            return redirect()
                ->route('usuario.dashboard')
                ->with('error', 'No tienes un perfil profesional creado. Accede a tu panel de usuario o crea primero tu perfil profesional.');
        }

        // Si tiene perfil profesional, mostramos el dashboard pro
        return view('layouts.profesional.dashboard_profesional', compact('user', 'perfil_profesional'));
    }

    /**
     * Mostrar formulario de perfil profesional (para el propio profesional)
     */
    public function mostrarPerfil()
    {
        $user   = Auth::user();
        $perfil_profesional = $user->perfil_Profesional;

        // Si por lo que sea tiene rol profesional pero aún no tiene perfil
        if (!$perfil_profesional) {
            // Podrías redirigir a tu flujo de creación de perfil
            return redirect()
                ->route('registrar.profesional.opciones')
                ->with('warning', 'Primero debes crear tu perfil profesional.');
        }

        // Oficios disponibles
        $oficios = Oficio::orderBy('nombre')->get(['id', 'nombre', 'slug']);

        // IDs de oficios seleccionados
        $oficiosSeleccionados = $perfil_profesional->oficios->pluck('id')->toArray();

        return view('layouts.profesional.perfil.perfil_profesional', compact('perfil_profesional', 'oficios', 'oficiosSeleccionados'));
    }

    /**
     * Actualizar perfil profesional (desde el propio panel profesional)
     */
    public function actualizarPerfil(Request $request)
    {
        $user   = Auth::user();
        $perfil_profesional = $user->perfil_Profesional;

        if (!$perfil_profesional) {
            return redirect()
                ->route('registrar.profesional.opciones')
                ->with('warning', 'Primero debes crear tu perfil profesional.');
        }

        // VALIDACIÓN (basada en la del admin, pero sin puntuación, trabajos, etc.)
        $validated = $request->validate([
            'empresa' => ['required', 'string', 'max:255'],

            'cif' => [
                'required',
                'string',
                'max:15',
                'regex:/^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/',
                Rule::unique('perfiles_profesionales', 'cif')->ignore($perfil_profesional->id),
            ],

            'email_empresa' => [
                'required',
                'email',
                'email:rfc,dns',
                Rule::unique('perfiles_profesionales', 'email_empresa')->ignore($perfil_profesional->id),
            ],

            'telefono_empresa' => [
                'required',
                'regex:/^(\\+34|0034|34)?[ -]*([6|7|8|9])[ -]*([0-9][ -]*){8}$/',
                Rule::unique('perfiles_profesionales', 'telefono_empresa')->ignore($perfil_profesional->id),
            ],

            // Ojo: en el form usas ciudad_empresa / provincia_empresa / direccion_empresa
            'ciudad'    => ['nullable', 'string', 'max:120'],
            'provincia' => ['required', 'string', 'max:120'],
            'direccion_empresa' => ['nullable', 'string', 'max:255'],

            'web' => ['nullable', 'url', 'max:255'],

            'bio' => ['nullable', 'string', 'max:500'],

            'avatar_profesional' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],

            'oficios'   => ['required', 'array', 'min:1'],
            'oficios.*' => ['exists:oficios,id'],
        ], [
            'empresa.required' => 'El nombre de la empresa es obligatorio.',
            'provincia.required' => 'La provincia de la empresa es obligatoria.',
            'provincia.string'   => 'La provincia debe ser texto válido.',
            'ciudad.string'     => 'La ciudad debe ser texto válido.',

            'cif.required' => 'El CIF es obligatorio.',
            'cif.regex'    => 'El CIF no tiene un formato válido.',
            'cif.unique'   => 'Este CIF ya está registrado.',

            'email_empresa.required' => 'El email de la empresa es obligatorio.',
            'email_empresa.email'    => 'Debes introducir un correo empresarial válido.',
            'email_empresa.unique'   => 'Este email de empresa ya está registrado.',

            'telefono_empresa.required' => 'El teléfono de la empresa es obligatorio.',
            'telefono_empresa.regex'    => 'El teléfono de la empresa no tiene el formato correcto.',
            'telefono_empresa.unique'   => 'Este teléfono de empresa ya está registrado.',

            'web.url'  => 'Debes introducir una URL válida para la web.',
            'web.max'  => 'La URL es demasiado larga.',

            'bio.string' => 'La biografía debe ser texto válido.',
            'bio.max'    => 'La biografía es demasiado larga.',

            'avatar_profesional.image' => 'El archivo debe ser una imagen.',
            'avatar_profesional.mimes' => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF, SVG o WEBP.',
            'avatar_profesional.max'   => 'La imagen no debe superar los 2MB.',

            'oficios.required' => 'Debes seleccionar al menos un oficio.',
            'oficios.*.exists' => 'Alguno de los oficios seleccionados no es válido.',
        ]);

        // GESTIÓN AVATAR (mismo patrón que en admin, pero sin pisar si no hay nuevo)
        $avatarPath = $perfil_profesional->avatar; // por defecto, mantenemos el actual

        if ($request->hasFile('avatar_profesional') && $request->file('avatar_profesional')->isValid()) {
            // Borramos anterior si existía
            if ($perfil_profesional->avatar) {
                Storage::disk('public')->delete($perfil_profesional->avatar);
            }

            $dir  = 'imagenes/avatarProfesional/' . now()->format('Ymd');
            $ext  = $request->file('avatar_profesional')->getClientOriginalExtension();
            $base = pathinfo($request->file('avatar_profesional')->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $file = $safe . '-' . Str::random(8) . '.' . $ext;

            Storage::disk('public')->makeDirectory($dir);
            $request->file('avatar_profesional')->storeAs($dir, $file, 'public');

            $avatarPath = $dir . '/' . $file; // solo cambiamos si ha subido uno nuevo
        }

        try {
            // Asignamos datos al modelo
            $perfil_profesional->empresa          = $validated['empresa'];
            $perfil_profesional->cif              = $validated['cif'];
            $perfil_profesional->email_empresa    = $validated['email_empresa'];
            $perfil_profesional->telefono_empresa = $validated['telefono_empresa'];

            $perfil_profesional->ciudad      = $validated['ciudad']            ?? null;
            $perfil_profesional->provincia   = $validated['provincia']         ?? null;
            $perfil_profesional->dir_empresa = $validated['direccion_empresa'] ?? null;

            $perfil_profesional->web   = $validated['web'] ?? null;
            $perfil_profesional->bio   = $validated['bio'] ?? null;
            $perfil_profesional->avatar = $avatarPath; // mantiene el anterior si no se ha tocado

            $perfil_profesional->save();

            // Oficios (relación many-to-many)
            $perfil_profesional->oficios()->sync($validated['oficios'] ?? []);

            return redirect()
                ->route('profesional.perfil')
                ->with('success', 'Tu perfil profesional se ha actualizado correctamente.');
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un problema al guardar los datos. Inténtalo de nuevo.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Ha ocurrido un error inesperado.');
        }
    }
}
