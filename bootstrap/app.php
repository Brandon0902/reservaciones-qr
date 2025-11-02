<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/** Aliases de middleware de rutas */
use App\Http\Middleware\AdminOnly;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias de middleware de ruta (para usar 'admin.only' en routes)
        $middleware->alias([
            'admin.only' => AdminOnly::class,
        ]);

        // Si quisieras inyectarlo a algún grupo (no necesario por ahora):
        // $middleware->appendToGroup('web', []);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
