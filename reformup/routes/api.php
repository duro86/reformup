<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\ProfesionalApiController;
use App\Http\Controllers\Api\ProfesionalTrabajosController;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API PÚBLICA
|--------------------------------------------------------------------------
*/

Route::get('/profesionales', [ProfesionalApiController::class, 'index'])->name('api.profesionales.index');

Route::get('/profesionales/{perfil}', [ProfesionalApiController::class, 'mostrar'])
    ->name('api.profesionales.mostrar');

/*
|--------------------------------------------------------------------------
| API PRIVADA (PROFESIONAL)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get(
    '/profesional/trabajos',
    [ProfesionalTrabajosController::class, 'index']
);


