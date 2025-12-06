<?php
// ---- RUTAS WEB ----
use Illuminate\Support\Facades\Route;
//Autenticación
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

//Rutas externas
require __DIR__ . '/admin.php';
require __DIR__ . '/profesional.php';
require __DIR__ . '/usuario.php';
require __DIR__ . '/login.php';


// ----- PÁGINA INICIO (LANDING PAGE) -----
Route::get('/', [HomeController::class, 'index'])->name('home');

// PÁGINA DE BUSCADOR DE PROFESIONALES (pública)
Route::get('/profesionales', [HomeController::class, 'profesionalesBuscador'])
    ->name('public.profesionales.index');

// ----  REGISTROS/invitados  ---
// Registro de clientes (usuario normal)
Route::get('/registrar/cliente', [AuthController::class, 'mostrarFormCliente'])->name('registrar.cliente');
Route::post('/registrar/cliente', [AuthController::class, 'registrarCliente'])->name('registrar.cliente.enviar');

// Registro de profesional: muestra opciones (por ejemplo, tipo de cuenta)
Route::get('/registrar/profesional/opciones', [AuthProController::class, 'mostrarOpcionesPro'])->name('registrar.profesional.opciones');

// Registro de profesional (nuevo profesional individual)
Route::get('/registro/profesional/nuevo', [AuthProController::class, 'mostrarFormProNuevo'])
    ->name('registro.pro.form');
Route::post('/registrar/profesional', [AuthProController::class, 'registrarClientePro'])
    ->name('registrar.profesional');

// Registro de empresa por parte de usuario (validacion)
Route::get('/validarUsuario', [AuthProController::class, 'mostrarValidarUsuario'])->name('validar.usuario');
Route::post('/validarUsuario', [AuthProController::class, 'validarUsuario'])->name('validar.usuario.post');

// Registro de empresa profesional(confirmado cuenta usuario)
Route::get('/registro/profesional/empresa', [AuthProController::class, 'mostrarFormProEmpresa'])
    ->name('registro.pro.empresa');

Route::post('/registro/profesional/empresa', [AuthProController::class, 'registrarEmpresa'])->name('registrar.empresa');

// Rutas comunes autenticadas para ver los pdf - seguridad
Route::middleware(['auth.redirect'])->group(function () {
    Route::get('/presupuestos/{presupuesto}/pdf', [AuthProController::class, 'verPdf'])
        ->name('presupuestos.ver_pdf');
});

// Ruta publica para ver el perfil de los profesionales
Route::get('/profesionales/{perfil}', [AuthProController::class, 'mostrar'])
    ->name('public.profesionales.mostrar');

// Ruta para contratar al profesional
Route::get('/profesionales/{perfil}/contratar', [AuthProController::class, 'contratar'])
    ->name('public.profesionales.contratar');

// Mostrar funcionamiento web
Route::get('/paso-a-paso', [AuthController::class, 'pasoAPaso'])
    ->name('public.paso_a_paso');

// Mostrar funcionamiento web
Route::get('/contacto', [AuthController::class, 'contacto'])
    ->name('public.contacto');
Route::post('/contacto', [AuthController::class, 'contactoEnviar'])->name('contacto.enviar');

//Politica de privacidad
Route::view('/politica-de-privacidad', 'legal.privacidad')->name('privacidad');

// Rutas para cambiar de modo
Route::get('/panel/modo/usuario', [AuthController::class, 'modoUsuario'])
    ->name('panel.modo.usuario');

Route::get('/panel/modo/profesional', [AuthController::class, 'modoProfesional'])
    ->name('panel.modo.profesional');

/*Route::middleware(['auth', 'rol.redirigir:admin'])->get('/admin/prueba', [AdminDashboardController::class, 'prueba'])
    ->name('admin.prueba');*/
