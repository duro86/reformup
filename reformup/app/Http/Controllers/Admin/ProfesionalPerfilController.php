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
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Http\Controllers\Traits\FiltroRangoFechas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProfesionalPerfilController extends Controller
{
    use FiltroRangoFechas;
    /**
     * Listado de perfiles profesionales
     */
    public function listarProfesionales(Request $request)
    {
        $q = $request->input('q'); // texto de búsqueda

        // Empezamos la query cargando también el user relacionado
        $query = Perfil_Profesional::with('user');

        if ($q) {
            $qLike = '%' . $q . '%';

            $query->where(function ($sub) use ($qLike) {
                // Campos del perfil profesional
                $sub->where('empresa', 'like', $qLike)
                    ->orWhere('cif', 'like', $qLike)
                    ->orWhere('email_empresa', 'like', $qLike)
                    ->orWhere('telefono_empresa', 'like', $qLike)
                    ->orWhere('dir_empresa', 'like', $qLike);
            })
                // Campos del usuario asociado (nombre, apellidos, email…)
                ->orWhereHas('user', function ($qUser) use ($qLike) {
                    $qUser->where('nombre', 'like', $qLike)
                        ->orWhere('apellidos', 'like', $qLike)
                        ->orWhere('email', 'like', $qLike)
                        ->orWhere('telefono', 'like', $qLike);
                });
        }

        // 🔹 Filtro por rango de fechas (alta del perfil profesional)
        $this->aplicarFiltroRangoFechas($query, $request, 'created_at');

        $profesionales = $query
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString(); // mantiene q, fecha_desde, fecha_hasta en la paginación

        return view('layouts.admin.profesionales.profesionales', compact('profesionales', 'q'));
    }

    /**
     * Exportar TODOS los profesionales a PDF 
     * */
    public function exportarProfesionalesPdf()
    {
        // Sacamos TODOS los profesionales, con su usuario asociado
        $profesionales = Perfil_Profesional::with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Cargamos la vista específica para PDF
        $pdf = Pdf::loadView('layouts.admin.profesionales.pdf.profesionales_pdf', [
            'profesionales' => $profesionales,
        ])->setPaper('a4', 'landscape'); // landscape porque hay muchas columnas

        $fileName = 'profesionales-' . now()->format('Ymd-His') . '.pdf';

        // return $pdf->download($fileName); // si quisieras descargar
        return $pdf->stream($fileName); // abrir en el navegador
    }

    /**
     * Exporta a PDF la página actual de profesionales (con la misma búsqueda)
     */
    public function exportarProfesionalesPaginaPdf(Request $request)
    {
        $pagina    = (int) $request->input('page', 1);
        $porPagina = 10; // o 5, lo que uses
        $busqueda  = $request->input('q');

        $query = Perfil_Profesional::with('user');

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('empresa', 'like', '%' . $busqueda . '%')
                    ->orWhere('cif', 'like', '%' . $busqueda . '%')
                    ->orWhere('email_empresa', 'like', '%' . $busqueda . '%')
                    ->orWhere('telefono_empresa', 'like', '%' . $busqueda . '%')
                    ->orWhereHas('user', function ($q2) use ($busqueda) {
                        $q2->where('nombre', 'like', '%' . $busqueda . '%')
                            ->orWhere('apellidos', 'like', '%' . $busqueda . '%')
                            ->orWhere('email', 'like', '%' . $busqueda . '%');
                    });
            });
        }

        // Mismo filtro de fechas que el listado
        $this->aplicarFiltroRangoFechas($query, $request, 'created_at');

        $paginator = $query
            ->orderByDesc('created_at')
            ->paginate($porPagina, ['*'], 'page', $pagina);

        $profesionales = $paginator->items();

        $pdf = Pdf::loadView('layouts.admin.profesionales.pdf.profesionales_pdf_pagina', [
            'profesionales' => $profesionales,
            'page'          => $pagina,
            'busqueda'      => $busqueda,
        ])->setPaper('a4', 'landscape');

        $fileName = 'profesionales-pagina-' . $pagina . '-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->stream($fileName);
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
            'provincia'  => ['required', 'string', 'max:120'],
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
            'provincia.required' => 'La provincia de la empresa es obligatoria.',
            'provincia.string'   => 'La provincia debe ser texto válido.',
            'provincia.max'      => 'La provincia no puede superar los 120 caracteres.',
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
            'avatar.uploaded'    => 'La subida de la imagen ha fallado. Por favor, inténtalo de nuevo.',

            'oficios.required' => 'Debes seleccionar al menos un oficio.',
        ]);

        // --- Manejo imagen avatar al CREAR usuario ---
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {

            $dir  = 'imagenes/avatarPro/' . now()->format('Ymd');
            $ext  = $request->file('avatar')->getClientOriginalExtension();
            $base = pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $file = $safe . '-' . Str::random(8) . '.' . $ext;

            // Creamos el directorio si no existe
            Storage::disk('public')->makeDirectory($dir);

            // Guardamos el archivo en storage/app/public/...
            $request->file('avatar')->storeAs($dir, $file, 'public');

            // Guardamos solo la ruta relativa en BD
            $avatarPath = $dir . '/' . $file;
        } else {
            // Si no sube nada, dejamos nulo
            $avatarPath = null;
        }


        // Página actual (por defecto 1)
        $paginaActual = $request->input('page', 1);

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
                ->route('admin.profesionales', ['page' => $paginaActual])
                ->with('success', 'Perfil profesional actualizado correctamente');
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

    /**
     * Elimina solo el perfil profesional (no el usuario)
     */
    public function eliminarProfesional(Perfil_Profesional $perfil)
    {
        try {
            DB::beginTransaction();

            $user = $perfil->user; // puede ser null si algo raro

            // 1) Borrar avatar del perfil profesional si no es el genérico
            if ($perfil->avatar && $perfil->avatar !== 'img/avatarPro/avatarHombrePro.png') {
                // Si tu avatar es ruta de storage (ej: "avatars/xx.png")
                if (Storage::disk('public')->exists($perfil->avatar)) {
                    Storage::disk('public')->delete($perfil->avatar);
                }
            }

            // 2) Borrar el perfil profesional
            $perfil->delete(); 


            // 3) Quitar rol profesional al usuario (si existe)
            if ($user) {
                if ($user->hasRole('profesional')) {
                    $user->removeRole('profesional');
                }

                $user->unsetRelation('perfil_Profesional');
            }

            DB::commit();

            return redirect()
                ->route('admin.profesionales')
                ->with('success', 'Perfil profesional eliminado correctamente. El usuario sigue existiendo pero sin perfil profesional.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error eliminando perfil profesional', [
                'perfil_id' => $perfil->id,
                'user_id'   => $perfil->user_id ?? null,
                'avatar'    => $perfil->avatar ?? null,
                'error'     => $e->getMessage(),
            ]);

            return redirect()
                ->route('admin.profesionales')
                ->with('error', 'No se ha podido eliminar el perfil profesional. Revisa dependencias (trabajos/presupuestos/solicitudes) o mira el log.');
        }
    }
}
