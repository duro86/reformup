<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Perfil_Profesional;
use App\Models\Oficio;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;   // <-- importa el trait


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthProController extends Controller
{
    public function mostrarOpcionesPro()
    {
        return view('auth.registro_pro');
    }

    public function mostrarFromProNuevo()
    {
        return view('auth.registro_pro_nuevo');
    }

    public function mostrarFromProEmpresa()
    {
        $userId = session('user_id'); // Obtener user_id de la sesión

        if (!$userId) {
            // Si no hay user_id en sesión, redirige a home con mensaje de error
            return redirect()->route('home')->with('error', 'Acceso no autorizado.');
        }

        $user = User::find($userId);
        if (!$user || !$user->hasRole('profesional')) {
            // Si el usuario no existe o no tiene rol profesional
            return redirect()->route('home')->with('error', 'Acceso no autorizado.');
        }

        // Usuario válido, continuar mostrando formulario
        $oficios = Oficio::orderBy('nombre')->get(['id', 'nombre', 'slug']);
        return view('auth.registro_pro_empresa', compact('userId', 'oficios'));
    }

    public function registrarClientePro(Request $request)
    {

        // Validación de datos
        $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'telefono' => ['required', 'regex:/^[6789]\d{8}$/', 'unique:users,telefono'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'cp' => ['nullable', 'regex:/^(?:0[1-9]|[1-4]\d|5[0-2])\d{3}$/'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'] // 2MB máximo
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 50 caracteres.',

            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.string' => 'Los apellidos deben ser un texto válido.',
            'apellidos.max' => 'Los apellidos no pueden superar los 100 caracteres.',

            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debes introducir un correo válido.',
            'email.unique' => 'El email ya está registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono no tiene un formato válido.',
            'telefono.unique' => 'El teléfono ya está registrado.',

            'ciudad.string' => 'La ciudad debe ser un texto válido.',
            'ciudad.max' => 'La ciudad no puede superar los 100 caracteres.',

            'provincia.string' => 'La provincia debe ser un texto válido.',
            'provincia.max' => 'La provincia no puede superar los 100 caracteres.',

            'direccion.string' => 'La dirección debe ser un texto válido.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',

            'cp.regex' => 'El código postal no tiene un formato válido.',

            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF o SVG.',
            'avatar.max' => 'La imagen no debe superar los 2MB.'
        ]);

        // Manejo imagen avatar
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $image = $request->file('avatar');
            $path = 'img/avatarUser/' . date('Ymd') . '/';
            $filename = time() . '_' . $image->getClientOriginalName();

            // Crear carpeta si no existe
            Storage::disk('public')->makeDirectory($path);

            // Guardar archivo
            $image->storeAs($path, $filename, 'public');

            $avatarPath = 'storage/' . $path . $filename;
        } else {
            // Imagen por defecto
            $avatarPath = 'storage/img/avatarUser/avatar_default.png';
        }

        // Insertamos en la tabla users y asignamos el rol de cliente
        $user = User::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'telefono' => $request->telefono,
            'ciudad' => $request->ciudad,
            'provincia' => $request->provincia,
            'cp' => $request->cp,
            'direccion' => $request->direccion,
            'avatar' => $avatarPath
        ]);


        $user->assignRole('profesional'); // Usando Spatie asigando el rol de usuario

        // Volver a la página de datos de la empresa con un mensaje de éxito
        session(['user_id' => $user->id]);
        return redirect()->route('registro.pro.empresa')->with('success', 'Registro completado correctamente, completa los datos de tu empresa');
    }

    public function registrarEmpresa(Request $request)
    {

        // Recupera el user_id de hidden o de la sesión
        $userId = $request->input('user_id', session('user_id'));

        // Si no tenemos user_id algo fue mal en el paso 1
        if (!$userId) {
            return redirect()
                ->route('registro.pro.form')
                ->with('error', 'No se encontró el usuario de la sesión. Repite el Paso 1.');
        }

        // Validación de datos
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'empresa' => ['required', 'string', 'max:255'],
            'cif' => ['required', 'string', 'max:15', 'unique:perfiles_profesionales,cif', 'regex:/^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/', 'unique:perfiles_profesionales,email_empresa'],
            'email_empresa' => ['required', 'email', 'email:rfc,dns', 'unique:perfiles_profesionales,email_empresa'],
            'bio' => ['nullable', 'string', 'max:500'],
            'web' => ['nullable', 'url', 'max:255'],
            'telefono_empresa' => ['required', 'regex:/^(\\+34|0034|34)?[ -]*([6|7|8|9])[ -]*([0-9][ -]*){8}$/', 'unique:perfiles_profesionales,telefono_empresa'], //Movil y fijo España
            'ciudad_empresa' => ['nullable', 'string', 'max:120'],
            'provincia_empresa' => ['nullable', 'string', 'max:120'],
            'direccion_empresa' => ['nullable', 'string', 'max:255'],
            'avatar_empresa' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'oficios' => ['required', 'array', 'min:1'],
            'oficios.*' => ['exists:oficios,id']
        ], [
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario no existe.',

            'empresa.required' => 'El nombre de la empresa es obligatorio.',
            'empresa.max' => 'El nombre de la empresa es demasiado largo.',

            'cif.required' => 'El CIF es obligatorio.',
            'cif.unique' => 'Este CIF ya está registrado.',
            'cif.regex' => 'El CIF no tiene un formato válido.',

            'email_empresa.required' => 'El email de la empresa es obligatorio.',
            'email_empresa.email' => 'El email de la empresa no es válido.',
            'email_empresa.unique' => 'Este email de empresa ya está registrado.',

            'bio.max' => 'La descripción es demasiado larga.',
            'web.url' => 'La web no es una URL válida.',
            'web.regex' => 'La web no tiene un formato válido.',

            'telefono_empresa.required' => 'El teléfono de la empresa es obligatorio.',
            'telefono_empresa.regex' => 'El teléfono de la empresa no tiene el formato correcto.',
            'telefono_empresa.unique' => 'Este teléfono de empresa ya está registrado.',

            'avatar_empresa.image' => 'El archivo debe ser una imagen.',
            'avatar_empresa.mimes' => 'Extensiones permitidas: jpeg, png, jpg, gif, svg, webp.',
            'avatar_empresa.max' => 'La imagen no debe superar los 2MB.',

            'oficios.required' => 'Debes seleccionar al menos un oficio.',
        ]);

        if ($request->hasFile('avatar_empresa') && $request->file('avatar_empresa')->isValid()) {
            $image = $request->file('avatar_empresa');
            $path = 'img/avatarEmpresa/' . date('Ymd') . '/';
            $filename = time() . '_' . $image->getClientOriginalName();

            Storage::disk('public')->makeDirectory($path);
            $image->storeAs($path, $filename, 'public');

            $avatarPath = 'storage/' . $path . $filename;
        } else {
            $avatarPath = 'storage/img/avatarEmpresa/avatar_default.png';
        }

        // Guardar empresa asociada a user_id
        $perfil = Perfil_Profesional::create([
            'user_id' => $request->user_id,
            'empresa' => $request->empresa,
            'cif' => $request->cif,
            'email_empresa' => $request->email_empresa,
            'bio' => $request->bio,
            'web' => $request->web,
            'telefono_empresa' => $request->telefono_empresa,
            'ciudad' => $request->ciudad_empresa,
            'provincia' => $request->provincia_empresa,
            'dir_empresa' => $request->direccion_empresa,
            'avatar' => $avatarPath,
        ]);

        // Sincronizar los oficios seleccionados
        $perfil->oficios()->sync($request->oficios);

        return redirect()->route('home')->with('success', 'Registro profesional completado correctamente. Ya puedes iniciar sesión.');
    }

    public function mostrarValidarUsuario()
    {
        return view('auth.validar_usuario');
    }

    public function validarUsuario(Request $request)
    {
        $creedenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        //Comprobar credenciales
        if (Auth::attempt($creedenciales)) {
            $request->session()->regenerate();

            $user = Auth::user(); //Datos del usuario autenticado

            if ($user->hasRole('profesional')) {
                $perfil = $user->perfil_Profesional; // Obtenemos el perfil profesional

                if ($perfil) {
                    // Si ya tiene empresa registrada, redirigimos con mensaje que incluye el nombre de la empresa
                    return redirect()->back()
                        ->with('info', 'Ya tienes una empresa registrada,' . $user->nombre . ': ' . $perfil->empresa);
                } else {
                    // Sin perfil profesional, asignamos user_id en sesión y dejamos registrar. esto no deberia pasar, lo gestionamos por si acaso
                    session(['user_id' => $user->id]);
                    return redirect()->route('registro.pro.empresa')->with('success', 'Usuario ' . $user->nombre . ' validado correctamente. Completa los datos de tu empresa.');
                }
            } elseif ($user->hasRole('usuario')) {
                // Sin perfil profesional, asignamos user_id en sesión y dejamos registrar
                session(['user_id' => $user->id]);
                return redirect()->route('registro.pro.empresa')->with('success', 'Usuario ' . $user->nombre . ' validado correctamente. Completa los datos de tu empresa.');
            }
        }

        // Si el Usuario existe
        $userExiste = User::where('email', $request->email)->exists();

        if (!$userExiste) {
            return redirect()->back()->with('error', 'El usuario no está registrado');
        } else {
            return redirect()->back()->with('error', 'La contraseña es incorrecta');
        }
    }
}
