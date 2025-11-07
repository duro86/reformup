<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

use App\Http\Controllers\Auth\AuthController;

Route::get('/registrar/cliente', [AuthController::class, 'mostrarFormCliente'])->name('registrar.cliente');
Route::post('/registrar/cliente', [AuthController::class, 'registrarCliente'])->name('registrar.cliente');

