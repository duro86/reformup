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
Route::post('/registrar/profesional', [AuthProController::class, 'registrarProfesional'])->name('registrar.profesional');
