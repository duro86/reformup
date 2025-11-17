<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Profesional\ProfesionalDashboardController;
use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Admin\ProfesionalPerfilController;
use App\Http\Controllers\Auth\OlvidarPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Página de inicio
Route::get('/', function () {
    return view('home');
})->name('home');


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
    ->name('registro.pro.empresa')
    ->middleware('auth.redirect'); // Middleware personalizado por si no esta logueado el usuario
Route::post('/registro/profesional/empresa', [AuthProController::class, 'registrarEmpresa'])->name('registrar.empresa');

// ---  LOGIN ---
// Mostrar formulario login
Route::get('/login', [LoginController::class, 'mostrarLoginForm'])->name('login');
// Solicitar enlace de reseteo
Route::get('/olvidar_password', [OlvidarPasswordController::class, 'mostrarLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/olvidar_password', [OlvidarPasswordController::class, 'enviarResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');

// Formulario para poner nueva contraseña
Route::get('/reset_password/{token}', [ResetPasswordController::class, 'mostrarResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

// Procesar login
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Salir / logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout'])->name('logoutget'); //Pruebas


// --- ADMIN ---

//Crear otro admin
// Ruta para crear el admin (solo usar una vez y luego eliminar o proteger)
/*Route::post('/crear-admin', [AuthController::class, 'registrarAdmin'])
    ->name('crear.admin');*/

// Dashboard admin con middleware personalizado
Route::middleware(['auth', 'rol.redirigir:admin'])->prefix('admin')
    ->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); //Mostrar dashboard

        // Eventos usuarios
        Route::get('/usuarios', [AdminDashboardController::class, 'listarUsuarios'])
            ->name('usuarios');
        Route::get('/usuarios/{usuario}', [AdminDashboardController::class, 'show'])
            ->name('admin.usuarios.ver');
        Route::get('/usuarios/{id}/editar', [AdminDashboardController::class, 'editarUsuario'])
            ->name('usuarios.editar');
        Route::put('/usuarios/{id}', [AdminDashboardController::class, 'actualizarUsuario'])
            ->name('usuarios.actualizar');
        Route::delete('/usuarios/{id}', [AdminDashboardController::class, 'eliminarUsuario'])
            ->name('usuarios.eliminar');

        // Registrar un cliente siendo Admin
        Route::get('/registrar/cliente', [AdminDashboardController::class, 'mostrarFormAdminUsuarioNuevo'])->name('admin.form.registrar.cliente');
        Route::post('/registrar/cliente', [AdminDashboardController::class, 'crearUsuarioNuevo'])->name('admin.registrar.cliente');

        // Eventos usuarios profesionales
        Route::get('/profesionales/profesionales', [ProfesionalPerfilController::class, 'listarProfesionales'])
            ->name('profesionales');

        Route::get('/profesionales/{profesional}', [ProfesionalPerfilController::class, 'show'])
            ->name('admin.profesionales.ver');

        Route::get('/profesionales/{id}/editar', [ProfesionalPerfilController::class, 'editarProfesional'])
            ->name('profesionales.editar');

        Route::put('/profesionales/{id}', [ProfesionalPerfilController::class, 'actualizarProfesional'])
            ->name('profesionales.actualizar');

        Route::delete('/profesionales/{id}', [ProfesionalPerfilController::class, 'eliminarProfesional'])
            ->name('profesionales.eliminar');

        // Registrar un profesional siendo Admin
        Route::get('/registrar/profesional', [ProfesionalPerfilController::class, 'mostrarFormAdminProNuevo'])->name('admin.form.registrar.profesional');
        Route::post('/registrar/profesional', [ProfesionalPerfilController::class, 'crearProNuevo'])->name('admin.registrar.profesional');

        // PERFIL
        Route::get('/perfil', [AdminDashboardController::class, 'mostrarPerfil'])
            ->name('perfil');

        Route::put('/perfil', [AdminDashboardController::class, 'actualizarPerfil'])
            ->name('perfil.actualizar');
    });


// --- PROFESIONAL ---
Route::middleware(['auth', 'rol.redirigir:profesional'])
    ->prefix('profesional')->name('profesional.')
    ->group(function () {
        Route::get('/dashboard', [ProfesionalDashboardController::class, 'index'])
            ->name('dashboard');

        // PERFIL PROFESIONAL (ver/editar)
        Route::get('/perfil', [ProfesionalDashboardController::class, 'mostrarPerfil'])
            ->name('perfil');

        Route::put('/perfil', [ProfesionalDashboardController::class, 'actualizarPerfil'])
            ->name('perfil.actualizar');
    });

// --- USUARIO ---
Route::middleware(['auth', 'rol.redirigir:usuario'])
    ->prefix('usuario')->name('usuario.')
    ->group(function () {
        // Usuario dashboard
        Route::get('/dashboard', [UsuarioDashboardController::class, 'index'])->name('dashboard');

        // PERFIL USUARIO
        Route::get('/perfil', [UsuarioDashboardController::class, 'mostrarPerfil'])->name('perfil');
        Route::put('/perfil', [UsuarioDashboardController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    });

/*Route::middleware(['auth', 'rol.redirigir:admin'])->get('/admin/prueba', [AdminDashboardController::class, 'prueba'])
    ->name('admin.prueba');*/
