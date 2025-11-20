<?php

use Illuminate\Support\Facades\Route;
//Autenticación
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;

//Admin
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSolicitudController;
use App\Http\Controllers\Admin\AdminPresupuestoController;
use App\Http\Controllers\Admin\AdminTrabajoController;
use App\Http\Controllers\Admin\AdminComentarioController;
use App\Http\Controllers\Admin\ProfesionalPerfilController;
// --- ADMIN ---

//Crear otro admin
// Ruta para crear el admin (solo usar una vez y luego eliminar o proteger)
/*Route::post('/crear-admin', [AuthController::class, 'registrarAdmin'])
    ->name('crear.admin');*/

// Dashboard admin con middleware personalizado
Route::middleware(['auth', 'rol.redirigir:admin'])->prefix('admin')
    ->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); //Mostrar dashboard

        // ----- EVENTOS USUARIOS CON ADMIN -----
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

        // ----- EVENTOS PROFESIONALES CON ADMIN -----
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

        // ----- REGISTRAR PROFESIONAL SIENDO ADMIN -----
        Route::get('/registrar/profesional', [ProfesionalPerfilController::class, 'mostrarFormAdminProNuevo'])->name('admin.form.registrar.profesional');
        Route::post('/registrar/profesional', [ProfesionalPerfilController::class, 'crearProNuevo'])->name('admin.registrar.profesional');

        // ----- PERFIL -----
        Route::get('/perfil', [AdminDashboardController::class, 'mostrarPerfil'])
            ->name('perfil');

        Route::put('/perfil', [AdminDashboardController::class, 'actualizarPerfil'])
            ->name('perfil.actualizar');

        // ----- LISTADO SOLICITUDES (ADMIN) -----
        Route::get('/solicitudes', [AdminSolicitudController::class, 'index'])
            ->name('solicitudes');

        // ----- LISTADO PRESUPUESTOS (ADMIN) -----
        Route::get('/presupuestos', [AdminPresupuestoController::class, 'index'])
            ->name('presupuestos');

        // ----- LISTADO TRABAJOS (ADMIN) -----
        Route::get('/trabajos', [AdminTrabajoController::class, 'index'])
            ->name('trabajos');

        // ----- LISTADO COMENTARIOS (ADMIN) -----
        Route::get('/comentarios', [AdminComentarioController::class, 'index'])
            ->name('comentarios.index');

        Route::get('/comentarios/{comentario}', [AdminComentarioController::class, 'mostrar'])
            ->name('comentarios.mostrar');

        Route::patch('/comentarios/{comentario}/toggle-publicado', [AdminComentarioController::class, 'togglePublicado'])
            ->name('comentarios.toggle_publicado');

        Route::patch('/comentarios/{comentario}/rechazar', [AdminComentarioController::class, 'rechazar'])
            ->name('comentarios.rechazar');

        Route::get('/comentarios/{comentario}/editar', [AdminComentarioController::class, 'editar'])
            ->name('comentarios.editar');

        Route::put('/comentarios/{comentario}', [AdminComentarioController::class, 'actualizar'])
            ->name('comentarios.actualizar');
    });
