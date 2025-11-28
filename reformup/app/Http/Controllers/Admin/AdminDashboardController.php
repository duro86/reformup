<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Oficio;
use App\Models\Perfil_Profesional;
use Spatie\Permission\Traits\HasRoles;
use App\Mail\ProfesionalValidado;
use App\Mail\ProfesionalSuspendido;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDashboardController extends Controller
{
    use HasRoles;
    // Método para mostrar el dashboard admin
    public function index()
    {
        return view('layouts.admin.dashboard_admin');
    }

    /**
     * Perfil Usuario Logueado (admin, usuario, profesional...)
     */
    // 
    public function mostrarPerfil()
    {
        $usuario = Auth::user();
        $roles   = $usuario->getRoleNames();

        $oficios = Oficio::orderBy('nombre')->get();

        $perfilProfesional = $usuario->perfil_Profesional()
            ->with('oficios')
            ->first();

        $oficiosSeleccionados = $perfilProfesional
            ? $perfilProfesional->oficios->pluck('id')->toArray()
            : [];

        // Roles disponibles para checkboxes
        $allRoles = Role::whereIn('name', ['usuario', 'profesional', 'admin'])
            ->pluck('name')
            ->toArray();

        $currentRoles = $usuario->getRoleNames()->toArray();

        return view('layouts.admin.perfil.perfil', compact(
            'usuario',
            'roles',
            'perfilProfesional',
            'oficios',
            'oficiosSeleccionados',
            'allRoles',
            'currentRoles'
        ));
    }

    /**
     * Actualizar perfiles
     */
    public function actualizarPerfil(Request $request)
    {
        $usuario          = Auth::user();
        $rolesActuales    = $usuario->getRoleNames();
        $perfilProfesional = $usuario->perfil_Profesional()->first(); // puede ser null

        // ---------- 1) VALIDACIÓN DINÁMICA ----------
        // Reglas BLOQUE USUARIO
        $rules = [
            'nombre'     => ['required', 'string', 'max:50'],
            'apellidos'  => ['required', 'string', 'max:100'],
            'email'      => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'password'   => ['nullable', 'confirmed', 'min:6'],
            'telefono'   => [
                'required',
                'regex:/^[6789]\d{8}$/',
                Rule::unique('users', 'telefono')->ignore($usuario->id),
            ],
            'ciudad_user'    => ['nullable', 'string', 'max:100'],
            'provincia_user' => ['required', 'string', 'max:100'],
            'cp'         => ['nullable', 'regex:/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'avatar'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];

        $messages = [
            'nombre.required'    => 'El nombre es obligatorio.',
            'nombre.string'      => 'El formato del nombre no es válido.',
            'nombre.max'         => 'El nombre no puede tener más de 50 caracteres.',

            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.string'   => 'El formato de los apellidos no es válido.',
            'apellidos.max'      => 'Los apellidos no pueden tener más de 100 caracteres.',

            'email.required'     => 'El email es obligatorio.',
            'email.email'        => 'Debes introducir un correo válido.',
            'email.unique'       => 'El email ya está registrado.',

            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',

            'telefono.required'  => 'El teléfono es obligatorio.',
            'telefono.regex'     => 'El teléfono no tiene un formato válido.',
            'telefono.unique'    => 'El teléfono ya está registrado.',

            'ciudad_user.string' => 'La ciudad debe ser texto válido.',
            'ciudad_user.max'    => 'La ciudad no puede superar los 100 caracteres.',

            'provincia_user.required' => 'La provincia es obligatoria.',
            'provincia_user.string'   => 'La provincia debe ser texto válido.',

            'direccion.string'   => 'La dirección debe ser texto válido.',
            'direccion.max'      => 'La dirección no puede superar los 255 caracteres.',

            'cp.regex'           => 'El código postal no tiene un formato válido.',

            'avatar.image'       => 'El archivo debe ser una imagen.',
            'avatar.mimes'       => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF o WEBP.',
            'avatar.max'         => 'La imagen no debe superar los 2MB.',
        ];

        // Si tiene rol profesional Y perfil profesional, añadimos reglas de profesional
        if ($rolesActuales->contains('profesional') && $perfilProfesional) {
            $rules = array_merge($rules, [
                'empresa' => ['required', 'string', 'max:255'],
                'cif' => [
                    'required',
                    'string',
                    'max:15',
                    'regex:/^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/',
                    Rule::unique('perfiles_profesionales', 'cif')->ignore($perfilProfesional->id),
                ],
                'email_empresa' => [
                    'required',
                    'email',
                    'email:rfc,dns',
                    Rule::unique('perfiles_profesionales', 'email_empresa')->ignore($perfilProfesional->id),
                ],
                'telefono_empresa' => [
                    'required',
                    'regex:/^(\\+34|0034|34)?[ -]*([6|7|8|9])[ -]*([0-9][ -]*){8}$/',
                    Rule::unique('perfiles_profesionales', 'telefono_empresa')->ignore($perfilProfesional->id),
                ],
                // OJO: aquí ya son los del perfil profesional
                'ciudad'            => ['nullable', 'string', 'max:120'],
                'provincia'         => ['required', 'string', 'max:120'],
                'direccion_empresa' => ['nullable', 'string', 'max:255'],
                'web'               => ['nullable', 'url', 'max:255'],
                'bio'               => ['nullable', 'string', 'max:500'],
                'avatar_profesional' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
                'visible'           => ['required', 'in:0,1'],
                'oficios'           => ['required', 'array', 'min:1'],
                'oficios.*'         => ['exists:oficios,id'],
            ]);

            $messages = array_merge($messages, [
                'empresa.required' => 'El nombre de la empresa es obligatorio.',

                'cif.required'     => 'El CIF es obligatorio.',
                'cif.regex'        => 'El CIF no tiene un formato válido.',
                'cif.unique'       => 'Este CIF ya está registrado.',

                'email_empresa.required' => 'El email de la empresa es obligatorio.',
                'email_empresa.email'    => 'Debes introducir un correo empresarial válido.',
                'email_empresa.unique'   => 'Este email de empresa ya está registrado.',

                'telefono_empresa.required' => 'El teléfono de la empresa es obligatorio.',
                'telefono_empresa.regex'    => 'El teléfono de la empresa no tiene el formato correcto.',
                'telefono_empresa.unique'   => 'Este teléfono de empresa ya está registrado.',

                'provincia.required' => 'La provincia de la empresa es obligatoria.',
                'provincia.string'   => 'La provincia de la empresa debe ser texto válido.',
                'ciudad.string'      => 'La ciudad de la empresa debe ser texto válido.',

                'web.url'   => 'Debes introducir una URL válida para la web.',
                'web.max'   => 'La URL es demasiado larga.',

                'bio.string' => 'La biografía debe ser texto válido.',
                'bio.max'    => 'La biografía es demasiado larga.',

                'avatar_profesional.image' => 'El archivo de avatar profesional debe ser una imagen.',
                'avatar_profesional.mimes' => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF, SVG o WEBP.',
                'avatar_profesional.max'   => 'La imagen profesional no debe superar los 2MB.',

                'visible.required' => 'Debes indicar si el perfil profesional está visible.',
                'visible.in'       => 'El campo visible debe ser Sí o No.',

                'oficios.required' => 'Debes seleccionar al menos un oficio.',
            ]);
        }

        // --- Reglas de ROLES (opcional, sólo si vienen en el formulario) ---
        $validRoleNames = Role::whereIn('name', ['usuario', 'profesional', 'admin'])
            ->pluck('name')
            ->toArray();

        $rules['roles']   = ['nullable', 'array', 'min:1'];
        $rules['roles.*'] = ['string', Rule::in($validRoleNames)];

        $messages['roles.array'] = 'El formato de los roles no es válido.';
        $messages['roles.min']   = 'Debes tener al menos un rol asignado.';
        $messages['roles.*.in']  = 'Alguno de los roles seleccionados no es válido.';

        $validated = $request->validate($rules, $messages);

        // ---------- 2) AVATAR USUARIO ----------
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {

            if ($usuario->avatar && $usuario->avatar !== 'imagenes/avatarUser/avatar_default.png') {
                Storage::disk('public')->delete($usuario->avatar);
            }

            $dir  = 'imagenes/avatarUser/' . now()->format('Ymd');
            $ext  = $request->file('avatar')->getClientOriginalExtension();
            $base = pathinfo($request->file('avatar')->getClientOriginalName(), PATHINFO_FILENAME);
            $safe = Str::slug($base);
            $file = $safe . '-' . Str::random(8) . '.' . $ext;

            Storage::disk('public')->makeDirectory($dir);
            $request->file('avatar')->storeAs($dir, $file, 'public');

            $avatarUserPath = $dir . '/' . $file;
        } else {
            $avatarUserPath = $usuario->avatar;
        }

        try {
            // ---------- 3) ACTUALIZAR USUARIO ----------
            $usuario->nombre    = $validated['nombre'];
            $usuario->apellidos = $validated['apellidos'];
            $usuario->email     = $validated['email'];
            $usuario->telefono  = $validated['telefono'];
            $usuario->ciudad    = $validated['ciudad_user']    ?? null;
            $usuario->provincia = $validated['provincia_user'] ?? null;
            $usuario->cp        = $validated['cp']             ?? null;
            $usuario->direccion = $validated['direccion']      ?? null;
            $usuario->avatar    = $avatarUserPath;

            if (!empty($validated['password'])) {
                $usuario->password = bcrypt($validated['password']);
            }

            $usuario->save();

            // ---------- 4) ACTUALIZAR PROFESIONAL (si aplica) ----------
            if ($rolesActuales->contains('profesional') && $perfilProfesional) {

                // Avatar profesional
                if ($request->hasFile('avatar_profesional') && $request->file('avatar_profesional')->isValid()) {

                    if ($perfilProfesional->avatar) {
                        Storage::disk('public')->delete($perfilProfesional->avatar);
                    }

                    $dirPro  = 'imagenes/avatarProfesional/' . now()->format('Ymd');
                    $extPro  = $request->file('avatar_profesional')->getClientOriginalExtension();
                    $basePro = pathinfo($request->file('avatar_profesional')->getClientOriginalName(), PATHINFO_FILENAME);
                    $safePro = Str::slug($basePro);
                    $filePro = $safePro . '-' . Str::random(8) . '.' . $extPro;

                    Storage::disk('public')->makeDirectory($dirPro);
                    $request->file('avatar_profesional')->storeAs($dirPro, $filePro, 'public');

                    $avatarProPath = $dirPro . '/' . $filePro;
                } else {
                    $avatarProPath = $perfilProfesional->avatar;
                }

                // Guardar datos del perfil profesional
                $perfilProfesional->empresa          = $validated['empresa'];
                $perfilProfesional->cif              = $validated['cif'];
                $perfilProfesional->email_empresa    = $validated['email_empresa'];
                $perfilProfesional->telefono_empresa = $validated['telefono_empresa'];
                $perfilProfesional->ciudad           = $validated['ciudad']            ?? null;       // del bloque profesional
                $perfilProfesional->provincia        = $validated['provincia']         ?? null;
                $perfilProfesional->dir_empresa      = $validated['direccion_empresa'] ?? null;
                $perfilProfesional->web              = $validated['web']               ?? null;
                $perfilProfesional->bio              = $validated['bio']               ?? null;
                $perfilProfesional->visible          = $validated['visible'];
                $perfilProfesional->avatar           = $avatarProPath;

                $perfilProfesional->save();

                // relación muchos-a-muchos oficios
                $perfilProfesional->oficios()->sync($validated['oficios'] ?? []);
            }

            // ---------- 5) Sincronizar ROLES (si el formulario los envía) ----------
            if ($request->has('roles')) {
                $nuevosRoles = $request->input('roles', []);

                // 5.1 No permitir que un admin se quite su propio rol admin
                if ($rolesActuales->contains('admin') && !in_array('admin', $nuevosRoles)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'roles' => 'No puedes quitarte tu propio rol de administrador.',
                        ]);
                }

                // 5.2 No permitir quitar rol profesional si tiene perfil profesional asociado
                if (
                    $rolesActuales->contains('profesional')   // tenía rol profesional
                    && $perfilProfesional                     // tiene perfil en la tabla perfiles_profesionales
                    && !in_array('profesional', $nuevosRoles) // y ahora intentan quitarlo
                ) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'roles' => 'No puedes quitar el rol profesional mientras exista un perfil profesional asociado. ' .
                                'Elimina o desactiva primero el perfil de empresa.',
                        ]);
                }

                // 5.3 Siempre debe tener al menos 'usuario'
                if (!in_array('usuario', $nuevosRoles)) {
                    $nuevosRoles[] = 'usuario';
                }

                // 5.4 Guardar los nuevos roles
                $usuario->syncRoles($nuevosRoles);
            }

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Tu perfil se ha actualizado correctamente.');
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
