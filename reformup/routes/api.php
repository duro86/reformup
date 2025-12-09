<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\ProfesionalApiController;
use App\Http\Controllers\Api\OficioController;
use Illuminate\Http\Request;


Route::get('/profesionales', [ProfesionalApiController::class, 'index'])->name('api.profesionales.index');

Route::get('/profesionales/{perfil}', [ProfesionalApiController::class, 'mostrar'])
    ->name('api.profesionales.mostrar');

Route::get('/profesional/trabajos', [ProfesionalApiController::class, 'misTrabajos'])
    ->middleware('auth:sanctum');

