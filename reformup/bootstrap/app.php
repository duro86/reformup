<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// IMPORTAMOS MIDDLEWARE:
use App\Http\Middleware\VerificarRolORedireccionar;
use App\Http\Middleware\CheckAuthRedirect;
use App\Http\Middleware\TienePerfilProfesional;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ALIAS PERSONALIZADO
        $middleware->alias([
            'rol.redirigir' => VerificarRolORedireccionar::class,
            'auth.redirect' => CheckAuthRedirect::class,
            'tiene.perfil.profesional' => TienePerfilProfesional::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Excepciones personalizadas
        $exceptions->render(function (PostTooLargeException $e, Request $request): Response {
            return back()
                ->withInput()
                ->withErrors(['avatar' => 'El archivo es demasiado grande.'])
                ->with('error', 'El archivo o los datos enviados son demasiado grandes. Prueba con un archivo más pequeño.');
        });
    })->create();
