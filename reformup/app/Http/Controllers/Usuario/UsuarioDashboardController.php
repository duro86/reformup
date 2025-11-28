<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use App\Models\Perfil_Profesional;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Oficio;
use Exception;

class UsuarioDashboardController extends Controller
{
    // Método para mostrar el dashboard admin
    public function index()
    {
        $user = Auth::user();

        $isProfesional = $user->hasRole('profesional');
        $perfilProfesional = $user->perfil_Profesional; // o ->perfil_Profesional()->first();

        return view('layouts.usuario.dashboard_usuario', [
            'user'              => $user,
            'isProfesional'     => $isProfesional,
            'perfilProfesional' => $perfilProfesional,
        ]);
    }

    // Mostrar perfil del usuario logueado
    public function mostrarPerfil()
    {
        $usuario = Auth::user();
        $roles   = $usuario->getRoleNames(); // colección

        return view('layouts.usuario.perfil.perfil_usuario', compact(
            'usuario',
            'roles'
        ));
    }

    /**
     * Actualizar el perfil del usuario logueado
     */
    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user();

        // --- VALIDACIÓN ---
        $rules = [
            'nombre'     => ['required', 'string', 'max:50'],
            'apellidos'  => ['required', 'string', 'max:100'],
            'email'      => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'current_password' => ['nullable', 'required_with:password'],
            'password'         => ['nullable', 'confirmed', 'min:6'],
            'telefono'   => [
                'required',
                'regex:/^[6789]\d{8}$/',
                Rule::unique('users', 'telefono')->ignore($usuario->id),
            ],
            'ciudad'     => ['nullable', 'string', 'max:100'],
            'provincia'  => ['required', 'string', 'max:100'],
            'cp'         => ['nullable', 'regex:/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/'],
            'direccion'  => ['nullable', 'string', 'max:255'],
            'avatar'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],

            // Gestión de contraseña:
            'password'          => ['nullable', 'confirmed', 'min:6'],
            // Solo exigimos current_password si quiere cambiar la contraseña
            'current_password'  => ['nullable'],
            'current_password.required_with' => 'Debes indicar tu contraseña actual para cambiarla.',
            'password.confirmed'             => 'La confirmación de la contraseña no coincide.',
            'password.min'                   => 'La nueva contraseña debe tener al menos 6 caracteres.',
        ];

        $messages = [
            'nombre.required'    => 'El nombre es obligatorio.',
            'nombre.max'         => 'El nombre no puede tener más de 50 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.max'      => 'Los apellidos no pueden tener más de 100 caracteres.',
            'email.required'     => 'El email es obligatorio.',
            'email.email'        => 'Debes introducir un correo válido.',
            'email.unique'       => 'El email ya está registrado.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'telefono.regex'     => 'El teléfono no tiene un formato válido.',
            'telefono.unique'    => 'El teléfono ya está registrado.',
            'cp.regex'           => 'El código postal no tiene un formato válido.',
            'avatar.image'       => 'El archivo debe ser una imagen.',
            'avatar.mimes'       => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF, SVG o WEBP.',
            'avatar.max'         => 'La imagen no debe superar los 2MB.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'ciudad.string'     => 'La ciudad debe ser texto válido.',
            'provincia.required' => 'La provincia de la empresa es obligatoria.',
            'provincia.string'   => 'La provincia debe ser texto válido.',
        ];

        $validated = $request->validate($rules, $messages);

        // --- Comprobación de contraseña actual si quiere cambiarla ---
        if ($request->filled('password')) {
            if (! $request->filled('current_password')) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'Debes introducir tu contraseña actual.']);
            }

            if (! Hash::check($request->current_password, $usuario->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
        }


        // --- Manejo imagen avatar al usuario ---
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {

            $dir  = 'imagenes/avatarUser/' . now()->format('Ymd');
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


        try {
            // --- ACTUALIZAR DATOS ---
            $usuario->nombre    = $request->nombre;
            $usuario->apellidos = $request->apellidos;
            $usuario->email     = $request->email;
            $usuario->telefono  = $request->telefono;
            $usuario->ciudad    = $request->ciudad;
            $usuario->provincia = $request->provincia;
            $usuario->cp        = $request->cp;
            $usuario->direccion = $request->direccion;
            $usuario->avatar    = $avatarPath;

            if ($request->filled('password')) {
                $usuario->password = bcrypt($request->password);
            }

            $usuario->save();

            return redirect()
                ->route('usuario.perfil')
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
