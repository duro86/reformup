<?php
// ---- RUTAS WEB ----
use Illuminate\Support\Facades\Route;
//Autenticación
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;
USE App\Http\Controllers\HomeController;

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


/*Route::middleware(['auth', 'rol.redirigir:admin'])->get('/admin/prueba', [AdminDashboardController::class, 'prueba'])
    ->name('admin.prueba');*/
