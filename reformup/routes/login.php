<?php
// ---- RUTAS WEB ----
use Illuminate\Support\Facades\Route;
//Autenticación
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AuthProController;
use App\Http\Controllers\Auth\LoginController;
//Password
use App\Http\Controllers\Auth\OlvidarPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

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