<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/** Aliases de middleware de rutas */
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\ForcePendingPaymentProof; // <- Importante

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias de middleware de ruta
        $middleware->alias([
            'admin.only'            => AdminOnly::class,
            'force.payment.proof'   => ForcePendingPaymentProof::class, // <- aquí registramos tu middleware
        ]);

        // Si quisieras inyectarlo a algún grupo globalmente:
        // $middleware->appendToGroup('web', ['force.payment.proof']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
