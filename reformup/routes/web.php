<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::get('/registrar/cliente', [AuthController::class, 'mostrarFormCliente'])->name('registrar.cliente');
Route::post('/registrar/cliente', [AuthController::class, 'registrarCliente'])->name('registrar.cliente');

Route::get('/registrar/profesional', [AuthProController::class, 'mostrarOpcionesPro'])->name('registrar.profesional');

Route::get('/registro/profesional/nuevo', [AuthProController::class, 'mostrarFromProNuevo'])->name('registro.pro.form');
Route::post('/registrar/profesional', [AuthProController::class, 'registrarClientePro'])->name('registrar.profesional');

Route::get('/registro/profesional/empresa', [AuthProController::class, 'mostrarFromProEmpresa'])->name('registro.pro.empresa');
Route::post('/registro/profesional/empresa', [AuthProController::class, 'registrarEmpresa'])->name('registrar.empresa');

Route::get('/validarUsuario', [AuthProController::class, 'mostrarValidarUsuario'])->name('validar.usuario');
Route::post('/validarUsuario', [AuthProController::class, 'validarUsuario'])->name('validar.usuario.post');

//Login 
// Mostrar formulario login
Route::get('/login', [LoginController::class, 'mostrarLoginForm'])->name('login');

// Procesar login
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Salir / logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');