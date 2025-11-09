<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Profesional\ProfesionalDashboardController;
use App\Http\Controllers\Usuario\UsuarioDashboardController;

// Página de inicio
Route::get('/', function () {
    return view('home');
})->name('home');


// Registro de clientes (usuario normal)
Route::get('/registrar/cliente', [AuthController::class, 'mostrarFormCliente'])->name('registrar.cliente');
Route::post('/registrar/cliente', [AuthController::class, 'registrarCliente'])->name('registrar.cliente');

// Registro de profesional: muestra opciones (por ejemplo, tipo de cuenta)
Route::get('/registrar/profesional', [AuthProController::class, 'mostrarOpcionesPro'])->name('registrar.profesional');

// Registro de profesional (nuevo profesional individual)
Route::get('/registro/profesional/nuevo', [AuthProController::class, 'mostrarFromProNuevo'])->name('registro.pro.form');
Route::post('/registrar/profesional', [AuthProController::class, 'registrarClientePro'])->name('registrar.profesional');

// Registro de empresa profesional
Route::get('/registro/profesional/empresa', [AuthProController::class, 'mostrarFromProEmpresa'])->name('registro.pro.empresa');
Route::post('/registro/profesional/empresa', [AuthProController::class, 'registrarEmpresa'])->name('registrar.empresa');


// Validar usuario (ejemplo: email o usuario tras registro antes de login)
Route::get('/validarUsuario', [AuthProController::class, 'mostrarValidarUsuario'])->name('validar.usuario');
Route::post('/validarUsuario', [AuthProController::class, 'validarUsuario'])->name('validar.usuario.post');

//Login 
// Mostrar formulario login
Route::get('/login', [LoginController::class, 'mostrarLoginForm'])->name('login');

// Procesar login
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Salir / logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- ADMIN ---

//Crear otro admin
// Ruta para crear el admin (solo usar una vez y luego eliminar o proteger)
/*Route::post('/crear-admin', [AuthController::class, 'registrarAdmin'])
    ->name('crear.admin');*/

// Dashboard admin
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // aquí irán más rutas: usuarios, reportes, etc.
    });

// --- PROFESIONAL ---
Route::middleware(['auth', 'role:profesional'])
    ->prefix('profesional')->name('profesional.')
    ->group(function () {
        Route::get('/dashboard', [ProfesionalDashboardController::class, 'index'])->name('dashboard');
        // solicitudes, presupuestos, trabajos, perfil, etc.
    });

// --- USUARIO ---
Route::middleware(['auth', 'role:usuario'])
    ->prefix('usuario')->name('usuario.')
    ->group(function () {
        Route::get('/dashboard', [UsuarioDashboardController::class, 'index'])->name('dashboard');
        // mis solicitudes, mis reseñas, datos de cuenta, etc.
    });

Route::middleware(['auth', 'rol.redirigir:admin'])->get('/admin/prueba', [AdminDashboardController::class, 'prueba'])
    ->name('admin.prueba');
