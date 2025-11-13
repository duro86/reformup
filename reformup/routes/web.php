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

//Login 
// Mostrar formulario login
Route::get('/login', [LoginController::class, 'mostrarLoginForm'])->name('login');

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
Route::middleware(['auth', 'rol.redirigir:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); //Mostrar dashboard
    // Eventos usuarios
    Route::get('/usuarios', [AdminDashboardController::class, 'listarUsuarios'])->name('usuarios');
    Route::get('/usuarios/{id}', [AdminDashboardController::class, 'verUsuario'])->name('usuarios.ver');
    Route::get('/usuarios/{id}/editar', [AdminDashboardController::class, 'editarUsuario'])->name('usuarios.editar');
    Route::delete('/usuarios/{id}', [AdminDashboardController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
    Route::get('/registrar/cliente', [AdminDashboardController::class, 'mostrarFormAdminUsuarioNuevo'])->name('admin.form.registrar.cliente');
    Route::post('/registrar/cliente', [AdminDashboardController::class, 'crearUsuarioNuevo'])->name('admin.registrar.cliente');
});

// Rutas adicionales para la gestión de usuarios por parte del admin
//Route::get('/usuarios/{id}', [AdminDashboardController::class, 'verUsuario'])->name('admin.usuarios.ver');
//Route::get('/usuarios/{id}/editar', [AdminDashboardController::class, 'editarUsuario'])->name('admin.usuarios.editar');
//Route::delete('/usuarios/{id}', [AdminDashboardController::class, 'eliminarUsuario'])->name('admin.usuarios.eliminar');


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

/*Route::middleware(['auth', 'rol.redirigir:admin'])->get('/admin/prueba', [AdminDashboardController::class, 'prueba'])
    ->name('admin.prueba');*/
