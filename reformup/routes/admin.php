<?php

use Illuminate\Support\Facades\Route;
//Autenticación
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;

//Admin
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUsuarioController;
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
Route::middleware(['rol.redirigir:admin'])->prefix('admin')
    ->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard'); //Mostrar dashboard

        // ----- EVENTOS USUARIOS CON ADMIN -----
        Route::get('/usuarios', [AdminUsuarioController::class, 'listarUsuarios'])
            ->name('usuarios');
        Route::get('/usuarios/{usuario}', [AdminUsuarioController::class, 'mostrar'])
            ->name('usuarios.ver');
        Route::get('/usuarios/{id}/editar', [AdminUsuarioController::class, 'editarUsuario'])
            ->name('usuarios.editar');
        Route::put('/usuarios/{id}', [AdminUsuarioController::class, 'actualizarUsuario'])
            ->name('usuarios.actualizar');
        Route::delete('/usuarios/{id}', [AdminUsuarioController::class, 'eliminarUsuario'])
            ->name('usuarios.eliminar');
        Route::get('/usuarios/export/pdf', [AdminUsuarioController::class, 'exportarUsuariosPdf'])
            ->name('usuarios.exportar.pdf');
        Route::get('/admin/usuarios/exportar_pagina', [AdminUsuarioController::class, 'exportarUsuariosPaginaPdf'])
            ->name('usuarios.exportarPaginaPdf');


        // Registrar un cliente siendo Admin
        Route::get('/registrar/cliente', [AdminUsuarioController::class, 'mostrarFormAdminUsuarioNuevo'])->name('form.registrar.cliente');
        Route::post('/registrar/cliente', [AdminUsuarioController::class, 'crearUsuarioNuevo'])->name('registrar.cliente');

        // ----- EVENTOS PROFESIONALES CON ADMIN -----
        Route::get('/profesionales/profesionales', [ProfesionalPerfilController::class, 'listarProfesionales'])
            ->name('profesionales');
        Route::get('/profesionales/exportar-todos-pdf', [ProfesionalPerfilController::class, 'exportarProfesionalesPdf'])
            ->name('profesionales.exportar_todos_pdf');

        Route::get('/profesionales/exportar-pdf', [ProfesionalPerfilController::class, 'exportarProfesionalesPaginaPdf'])
            ->name('profesionales.exportar_pdf_pagina');

        Route::get('/profesionales/{profesional}', [ProfesionalPerfilController::class, 'show'])
            ->name('profesionales.ver');

        Route::patch('/profesionales/{perfil}/toggle-visible', [ProfesionalPerfilController::class, 'toggleVisible'])
            ->name('profesionales.toggle_visible');

        Route::get('/profesionales/{id}/editar', [ProfesionalPerfilController::class, 'editarProfesional'])
            ->name('profesionales.editar');

        Route::put('/profesionales/{id}', [ProfesionalPerfilController::class, 'actualizarProfesional'])
            ->name('profesionales.actualizar');

        Route::delete('/profesionales/{id}', [ProfesionalPerfilController::class, 'eliminarProfesional'])
            ->name('profesionales.eliminar');


        // ----- REGISTRAR PROFESIONAL SIENDO ADMIN -----
        Route::get('/registrar/profesional', [ProfesionalPerfilController::class, 'mostrarFormAdminProNuevo'])->name('form.registrar.profesional');
        Route::post('/registrar/profesional', [ProfesionalPerfilController::class, 'crearProNuevo'])->name('registrar.profesional');

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


        // ----- COMENTARIOS -----

        // ----- LISTADO COMENTARIOS (ADMIN) -----
        Route::get('/comentarios', [AdminComentarioController::class, 'index'])
            ->name('comentarios');

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

        // ----- SOLICITUDES (ADMIN) -----
        Route::get('/solicitudes', [AdminSolicitudController::class, 'index'])
            ->name('solicitudes');

        Route::get('/solicitudes/crear', [AdminSolicitudController::class, 'crear'])
            ->name('solicitudes.crear');

        Route::post('/solicitudes', [AdminSolicitudController::class, 'guardar'])
            ->name('solicitudes.guardar');

        Route::get('/solicitudes/{solicitud}', [AdminSolicitudController::class, 'mostrar'])
            ->name('solicitudes.mostrar');

        Route::patch('/solicitudes/{solicitud}/cancelar', [AdminSolicitudController::class, 'cancelar'])
            ->name('solicitudes.cancelar');

        Route::get('/solicitudes/{solicitud}/editar', [AdminSolicitudController::class, 'editar'])
            ->name('solicitudes.editar');

        Route::put('/solicitudes/{solicitud}', [AdminSolicitudController::class, 'actualizar'])
            ->name('solicitudes.actualizar');

        // Eliminar SOLICITUD por parte del admin
        Route::delete(
            '/solicitudes/{solicitud}/eliminar',
            [AdminSolicitudController::class, 'eliminarSolicitudAdmin']
        )->name('solicitudes.eliminar_admin');

        // -----PRESUPUESTOS (ADMIN)
        Route::get('/presupuestos', [AdminPresupuestoController::class, 'index'])
            ->name('presupuestos');

        // --Seleccionar solicitud para crear un nuevo presupuesto (ADMIN)--
        Route::get('/presupuestos/seleccionar-solicitud', [AdminPresupuestoController::class,     'seleccionarSolicitudParaNuevo',])
            ->name('presupuestos.seleccionar_solicitud');

        Route::get('/presupuestos/crear/{solicitud}', [AdminPresupuestoController::class, 'crearDesdeSolicitud'])
            ->name('presupuestos.crear');
        Route::post('/presupuestos/{solicitud}', [AdminPresupuestoController::class, 'guardarDesdeSolicitud'])
            ->name('presupuestos.guardar');

        Route::get('/presupuestos/{presupuesto}', [AdminPresupuestoController::class, 'mostrar'])
            ->name('presupuestos.mostrar');


        Route::patch('/presupuestos/{presupuesto}/cancelar', [AdminPresupuestoController::class, 'cancelar'])
            ->name('presupuestos.cancelar');

        // EDITAR presupuesto (formulario)
        Route::get('/presupuestos/{presupuesto}/editar', [AdminPresupuestoController::class, 'editar'])
            ->name('presupuestos.editar');

        // ACTUALIZAR presupuesto (POST/PUT)
        Route::put('/presupuestos/{presupuesto}', [AdminPresupuestoController::class, 'actualizar'])
            ->name('presupuestos.actualizar');

        // Eliminar presupuesto
        Route::delete('/presupuestos/{presupuesto}/eliminar', [
            AdminPresupuestoController::class,
            'eliminarPresuAdmin',
        ])->name('presupuestos.eliminar_admin');

        // ----- LISTADO TRABAJOS (ADMIN) -----
        Route::get('/trabajos', [AdminTrabajoController::class, 'index'])
            ->name('trabajos');

        Route::get('/trabajos/exportar-excel', [AdminTrabajoController::class, 'exportarTrabajosExcel'])
            ->name('trabajos.exportar_excel');

        // MOSTRAR (para modal)
        Route::get('/trabajos/{trabajo}', [AdminTrabajoController::class, 'mostrar'])
            ->name('trabajos.mostrar');

        // Editar trabajo (formulario)
        Route::get('/trabajos/{trabajo}/editar', [AdminTrabajoController::class, 'editar'])
            ->name('trabajos.editar');

        // Actualizar trabajo
        Route::put('/trabajos/{trabajo}', [AdminTrabajoController::class, 'actualizar'])
            ->name('trabajos.actualizar');

        // Cancelar trabajo
        Route::patch('/trabajos/{trabajo}/cancelar', [AdminTrabajoController::class, 'cancelar'])
            ->name('trabajos.cancelar');

        Route::delete(
            '/trabajos/{trabajo}/eliminar',
            [AdminTrabajoController::class, 'eliminarTrabajoAdmin']
        )
            ->name('trabajos.eliminar_admin');
    });
