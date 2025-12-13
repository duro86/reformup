<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Solicitud;
use App\Models\Presupuesto;
use App\Models\Trabajo;
use App\Models\Comentario;
use Illuminate\Support\Facades\Mail;
use Mews\Purifier\Facades\Purifier;
use App\Mail\ContactoWebMailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

//Controlador para la autenticación usando Laravel Sanctum
class AuthController extends Controller
{
    public function mostrarFormCliente()
    {
        return view('auth.registro_cliente');
    }

    public function registrarCliente(Request $request)
    {

        // Validación de datos
        $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'telefono' => ['required', 'regex:/^[6789]\d{8}$/', 'unique:users,telefono'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['required', 'string', 'max:100'],
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

            'provincia.required' => 'La provincia es obligatoria.',
            'provincia.string'   => 'La provincia debe ser un texto válido.',
            'provincia.max'      => 'La provincia no puede superar los 100 caracteres.',

            'direccion.string' => 'La dirección debe ser un texto válido.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',

            'cp.regex' => 'El código postal no tiene un formato válido.',

            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'Sólo se permiten archivos JPG, PNG, JPEG, GIF o SVG.',
            'avatar.max' => 'La imagen no debe superar los 2MB.'
        ]);

        // --- Manejo imagen avatar al CREAR usuario ---
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


        $user->assignRole('usuario'); // Usando Spatie asigando el rol de usuario

        // Logueamos al usuario
        Auth::login($user);

        // Por defecto, fijamos modo usuario
        session(['modo_panel' => 'usuario']);

        // REDIRECCIÓN INTELLIGENTE:
        // - si hay url.intended (por ejemplo, venir de "Contratar esta empresa") → irá ahí
        // - si no, irá al dashboard de usuario
        return redirect()
            ->intended(route('usuario.dashboard'))
            ->with('success', 'Registro completado correctamente.');
    }

    /* public function registrarAdmin(Request $request)--Crear 1 solo admin
    {
        // Validación de datos (similar al cliente, agregar o quitar campos según necesidad)
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
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048']
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

        // Manejo imagen avatar igual que en registrarCliente
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $image = $request->file('avatar');
            $path = 'img/avatarUser/' . date('Ymd') . '/';
            $filename = time() . '_' . $image->getClientOriginalName();

            Storage::disk('public')->makeDirectory($path);
            $image->storeAs($path, $filename, 'public');
            $avatarPath = 'storage/' . $path . $filename;
        } else {
            $avatarPath = 'storage/img/admin/avatar_admin.png';
        }

        // Crear usuario y asignar rol admin
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

        $user->assignRole('admin'); // Asignar rol admin

        return redirect()->route('home')->with('success', 'Usuario administrador registrado correctamente.');
    }*/

    /**
     * Paso a paso, indicar a los usuarios el funcionamiento de la web
     */
    public function pasoAPaso()
    {
        // Traemos los estados

        $estadosSolicitud   = Solicitud::ESTADOS;
        $estadosPresupuesto = Presupuesto::ESTADOS;
        $estadosTrabajo     = Trabajo::ESTADOS;
        $estadosComentario  = Comentario::ESTADOS;

        return view('layouts.public.paso_a_paso', compact(
            'estadosSolicitud',
            'estadosPresupuesto',
            'estadosTrabajo',
            'estadosComentario'
        ));
    }

    /**
     * Mostrar pagina contacto sobre nosotros
     */
    public function contacto()
    {
        return view('layouts.public.contacto');
    }

    /**
     * Recibir email de informacion de sobre nosotros
     */
    public function contactoEnviar(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nombre'     => 'required|string|max:255',
                'email'      => 'required|email',
                'asunto'     => 'required|string|max:255',
                'mensaje'    => 'required|string|max:2000',
                'privacidad' => 'accepted',
            ],
            [
                'nombre.required'     => 'Por favor, indica tu nombre.',
                'email.required'      => 'El email es obligatorio.',
                'email.email'         => 'El formato de email no es válido.',
                'asunto.required'     => 'Indica un asunto para tu mensaje.',
                'mensaje.required'    => 'El mensaje no puede estar vacío.',
                'privacidad.accepted' => 'Debes aceptar la política de privacidad.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#contacto-form')
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Revisa el formulario: hay campos incorrectos o incompletos.');
        }

        $validated = $validator->validated();

        $nombre  = strip_tags($validated['nombre']);
        $asunto  = strip_tags($validated['asunto']);
        $email   = $validated['email'];
        $mensajeLimpio = Purifier::clean($validated['mensaje'], 'solicitud');

        try {
            Mail::to('admin@reformup.es')->send(new ContactoWebMailable(
                $nombre,
                $email,
                $asunto,
                $mensajeLimpio
            ));

            return redirect()->to(url()->previous() . '#contacto-form')
                ->with('success', 'Tu mensaje se ha enviado correctamente.');
        } catch (\Throwable $e) {
            return redirect()->to(url()->previous() . '#contacto-form')
                ->withInput()
                ->with('error', 'Ha ocurrido un error al enviar el mensaje. Inténtalo de nuevo más tarde.');
        }
    }

    public function modoUsuario()
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole('usuario')) {
            return redirect()->route('home')
                ->with('error', 'Esta cuenta no tiene panel de usuario.');
        }

        session(['modo_panel' => 'usuario']);

        return redirect()->route('usuario.dashboard');
    }

    public function modoProfesional()
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole('profesional')) {
            return redirect()->route('home')
                ->with('error', 'Esta cuenta no tiene panel profesional.');
        }

        session(['modo_panel' => 'profesional']);

        return redirect()->route('profesional.dashboard');
    }
}
