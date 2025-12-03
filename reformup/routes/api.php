<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\ProfesionalApiController;

Route::get('/profesionales', [ProfesionalApiController::class, 'index'])->name('api.profesionales.index');;

Route::get('/profesionales/{perfil}', [ProfesionalApiController::class, 'mostrar'])
    ->name('api.profesionales.mostrar');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profesional/trabajos', [ProfesionalApiController::class, 'misTrabajos']);
});