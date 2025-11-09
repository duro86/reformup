<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;

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