<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/** Aliases de middleware de rutas */
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\ForcePendingPaymentProof;
use App\Http\Middleware\EnsureRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
        // Si tienes api separada explícitamente no es necesario pasarla aquí;
        // Laravel ya carga routes/api.php por defecto.
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias de middleware de ruta
        $middleware->alias([
            'admin.only'            => AdminOnly::class,
            'force.payment.proof'   => ForcePendingPaymentProof::class,
            'role'                  => EnsureRole::class, // <- para exigir rol (e.g. role:validator)
        ]);

        // Si quisieras inyectarlo a algún grupo globalmente:
        // $middleware->appendToGroup('web', ['force.payment.proof']);
        // Para tokens personales de Sanctum, NO necesitas agregar EnsureFrontendRequestsAreStateful.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
