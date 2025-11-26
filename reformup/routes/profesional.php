<?php
// ---- RUTAS WEB ----
use Illuminate\Support\Facades\Route;
//Autenticación
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;
//Profesional
use App\Http\Controllers\Profesional\ProfesionalDashboardController;
use App\Http\Controllers\Profesional\ProfesionalSolicitudController;
use App\Http\Controllers\Profesional\ProfesionalPresupuestoController;
use App\Http\Controllers\Profesional\ProfesionalTrabajoController;
use App\Http\Controllers\Profesional\ProfesionalComentarioController;

// --- PROFESIONAL ---
Route::middleware(['auth', 'rol.redirigir:profesional'])
    ->prefix('profesional')->name('profesional.')
    ->group(function () {
        Route::get('/dashboard', [ProfesionalDashboardController::class, 'index'])
            ->name('dashboard');

        // --PERFIL PROFESIONAL (ver/editar)--
        Route::get('/perfil', [ProfesionalDashboardController::class, 'mostrarPerfil'])
            ->name('perfil');

        Route::put('/perfil', [ProfesionalDashboardController::class, 'actualizarPerfil'])
            ->name('perfil.actualizar');


        // ----- SOLICITUDES -----
        // --LISTADO SOLICITUDES que recibe el profesional--
        Route::get('/solicitudes', [ProfesionalSolicitudController::class, 'index'])
            ->name('solicitudes.index');

        // Detalle de una solicitud (para el modal Vue o vista normal)
        Route::get('/solicitudes/{solicitud}', [ProfesionalSolicitudController::class, 'mostrar'])
            ->name('solicitudes.mostrar');

        // Cancelar solicitud (cambia estado a cancelada)
        Route::patch('/solicitudes/{solicitud}/cancelar', [ProfesionalSolicitudController::class, 'cancelar'])
            ->name('solicitudes.cancelar');


        // ----- PRESUPUESTOS -----
        // --LISTADO de PRESUPUESTOS del profesional--
        Route::get('/presupuestos', [ProfesionalPresupuestoController::class, 'index'])
            ->name('presupuestos.index');

        // FORMULARIO de presupuesto desde una solicitud concreta
        Route::get('/presupuestos/solicitud/{solicitud}', [ProfesionalPresupuestoController::class, 'crearFromSolicitud'])
            ->name('presupuestos.crear_desde_solicitud');

        // GUARDAR presupuesto para una solicitud concreta
        Route::post('/presupuestos/solicitud/{solicitud}', [ProfesionalPresupuestoController::class, 'guardarFromSolicitud'])
            ->name('presupuestos.guardar_desde_solicitud');

        // NUEVA RUTA: ver PDF de un presupuesto concreto filtrando por id
        Route::get('/presupuestos/{presupuesto}/pdf', [ProfesionalPresupuestoController::class, 'verPdf'])
            ->name('presupuestos.ver_pdf');

        // Cancelar presupuesto
        Route::patch('/presupuestos/{presupuesto}/cancelar', [
            ProfesionalPresupuestoController::class,
            'cancelar'
        ])->name('presupuestos.cancelar');


        // ----- TRABAJOS -----
        // LISTADO de TRABAJOS del profesional
        Route::get('/trabajos',  [ProfesionalTrabajoController::class, 'index'])
            ->name('trabajos.index');

        // Datos del trabajo (JSON para modal o vista normal)
        Route::get('trabajos/{trabajo}', [ProfesionalTrabajoController::class, 'mostrar'])->name('trabajos.mostrar');

        // Cancelar trabajo por parte del profesional
        Route::patch('trabajos/{trabajo}/cancelar', [ProfesionalTrabajoController::class, 'cancelar'])
            ->name('trabajos.cancelar');
        // Empezar trabajo por parte del profesional
        Route::patch('trabajos/{trabajo}/empezar', [ProfesionalTrabajoController::class, 'empezar'])
            ->name('trabajos.empezar');
        // Finalizar trabajo por parte del profesional
        Route::patch('trabajos/{trabajo}/finalizar', [ProfesionalTrabajoController::class, 'finalizar'])
            ->name('trabajos.finalizar');

        // ----- COMENTARIOS -----
        // LISTADO de comentarios del profesional
        // Comentarios recibidos por el profesional
        Route::get('/comentarios', [ProfesionalComentarioController::class, 'index'])
            ->name('comentarios.index');
        Route::get('/comentarios/{comentario}', [ProfesionalComentarioController::class, 'mostrar'])
            ->name('comentarios.mostrar');
    });
