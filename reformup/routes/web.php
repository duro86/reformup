<?php
// ---- RUTAS WEB ----
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

//Profesional
use App\Http\Controllers\Profesional\ProfesionalDashboardController;
use App\Http\Controllers\Profesional\ProfesionalSolicitudController;
use App\Http\Controllers\Profesional\ProfesionalPresupuestoController;
use App\Http\Controllers\Profesional\ProfesionalTrabajoController;
//Usuario
use App\Http\Controllers\Usuario\UsuarioDashboardController;
use App\Http\Controllers\Usuario\UsuarioSolicitudController;
use App\Http\Controllers\Usuario\UsuarioPresupuestoController;
use App\Http\Controllers\Usuario\UsuarioTrabajoController;
use App\Http\Controllers\Usuario\UsuarioComentarioController;
//Password
use App\Http\Controllers\Auth\OlvidarPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


// ----- PAGINA INICIO (LANDING PAGE)-----
Route::get('/', function () {
    return view('home');
})->name('home');

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
    ->name('registro.pro.empresa')
    ->middleware('auth.redirect'); // Middleware personalizado por si no esta logueado el usuario
Route::post('/registro/profesional/empresa', [AuthProController::class, 'registrarEmpresa'])->name('registrar.empresa');

// -----  LOGIN -----
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

        Route::patch('/comentarios/{comentario}/publicar', [AdminComentarioController::class, 'publicar'])
            ->name('comentarios.publicar');

        Route::patch('/comentarios/{comentario}/rechazar', [AdminComentarioController::class, 'rechazar'])
            ->name('comentarios.rechazar');

        // Valorar profesional (visible/no visible)
        Route::post('/profesionales/{id}/toggle-visible', [AdminDashboardController::class, 'toggleVisible'])
            ->name('profesionales.toggleVisible');
    });


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

/*Route::middleware(['auth', 'rol.redirigir:admin'])->get('/admin/prueba', [AdminDashboardController::class, 'prueba'])
    ->name('admin.prueba');*/
