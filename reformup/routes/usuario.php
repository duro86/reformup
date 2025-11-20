<?php
// ---- RUTAS WEB ----
use Illuminate\Support\Facades\Route;
//Autenticación
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;

//Usuario
use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Usuario\UsuarioSolicitudController;
use App\Http\Controllers\Usuario\UsuarioPresupuestoController;
use App\Http\Controllers\Usuario\UsuarioTrabajoController;
use App\Http\Controllers\Usuario\UsuarioComentarioController;

// --- USUARIO ---
Route::middleware(['auth', 'rol.redirigir:usuario'])
    ->prefix('usuario')->name('usuario.')
    ->group(function () {
        // Usuario dashboard
        Route::get('/dashboard', [UsuarioDashboardController::class, 'index'])->name('dashboard');

        // PERFIL USUARIO
        Route::get('/perfil', [UsuarioDashboardController::class, 'mostrarPerfil'])->name('perfil');
        Route::put('/perfil', [UsuarioDashboardController::class, 'actualizarPerfil'])->name('perfil.actualizar');

        // REGISTRA EMPRESA (usuario normal a profesional empresa)
        Route::get('/registrar/profesional/opciones', [AuthProController::class, 'mostrarFormProEmpresa'])
            ->name('registrar.profesional.opciones');


        // ----- SOLICITUDES -----
        // LISTADO de solicitudes del cliente
        Route::get('/solicitudes', [UsuarioSolicitudController::class, 'index'])
            ->name('solicitudes.index');

        // Formulario nueva solicitud
        Route::get('/solicitudes/seleccionar_profesional', [UsuarioSolicitudController::class, 'seleccionarProfesional'])
            ->name('solicitudes.seleccionar_profesional');

        // Guardar nueva solicitud
        // PASO 2: formulario de solicitud con un profesional concreto
        Route::get('/solicitudes/crear/profesional/{pro}', [UsuarioSolicitudController::class, 'crearConProfesional'])
            ->name('solicitudes.crear_con_profesional');

        // Guardar solicitud
        Route::post('/solicitudes', [UsuarioSolicitudController::class, 'guardar'])
            ->name('solicitudes.guardar');

        // Eliminar una solicitud del cliente
        Route::delete('/solicitudes/{solicitud}', [UsuarioSolicitudController::class, 'eliminar'])
            ->name('solicitudes.eliminar');


        // ----- PRESUPUESTOS -----
        // LISTADO de PRESUPUESTOS del cliente
        Route::get('/presupuestos', [UsuarioPresupuestoController::class, 'index'])
            ->name('presupuestos.index');

        // Aceptar presupuesto
        Route::patch('/presupuestos/{presupuesto}/aceptar', [UsuarioPresupuestoController::class, 'aceptar'])
            ->name('presupuestos.aceptar');

        // Rechazar presupuesto
        Route::patch('/presupuestos/{presupuesto}/rechazar', [UsuarioPresupuestoController::class, 'rechazar'])
            ->name('presupuestos.rechazar');


        // ----- TRABAJOS -----
        // LISTADO de TRABAJOS del cliente
        Route::get('/trabajos',  [UsuarioTrabajoController::class, 'index'])
            ->name('trabajos.index');

        // Datos del trabajo (JSON para modal o vista normal)
        Route::get('trabajos/{trabajo}', [UsuarioTrabajoController::class, 'mostrar'])->name('trabajos.mostrar');

        // Cancelar trabajo por parte del cliente(usuario)
        Route::patch('trabajos/{trabajo}/cancelar', [UsuarioTrabajoController::class, 'cancelar'])
            ->name('trabajos.cancelar');


        // ----- COMENTARIOS -----
        // LISTADO de comentarios del cliente
        Route::get('/comentario', [UsuarioComentarioController::class, 'index'])
            ->name('comentarios.index');

        // FORMULARIO nuevo comentario sobre un trabajo
        Route::get('/comentarios/trabajo/{trabajo}', [UsuarioComentarioController::class, 'crear'])
            ->name('comentarios.crear');

        // GUARDAR nuevo comentario
        Route::post('/comentarios/trabajo/{trabajo}', [UsuarioComentarioController::class, 'guardar'])
            ->name('comentarios.guardar');

        // EDITAR comentario propio
        Route::get('/{comentario}/editar', [UsuarioComentarioController::class, 'editar'])
            ->name('comentarios.editar');

        // ACTUALIZAR comentario propio
        Route::put('/{comentario}', [UsuarioComentarioController::class, 'actualizar'])
            ->name('comentarios.actualizar');
    });
