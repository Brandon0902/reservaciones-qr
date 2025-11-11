<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/** Aliases de middleware de rutas (tuyos) */
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\ForcePendingPaymentProof;
use App\Http\Middleware\EnsureRole;

/** Aliases de middleware de Sanctum para abilities */
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias de middleware de ruta
        $middleware->alias([
            'admin.only'            => AdminOnly::class,
            'force.payment.proof'   => ForcePendingPaymentProof::class,
            'role'                  => EnsureRole::class,          // e.g. role:validator

            // Sanctum abilities
            'abilities'             => CheckAbilities::class,      // requiere TODAS las abilities listadas
            'ability'               => CheckForAnyAbility::class,  // requiere ALGUNA de las abilities listadas
        ]);

        // Si quisieras inyectar algún middleware a un grupo:
        // $middleware->appendToGroup('web', ['force.payment.proof']);

        // Nota: Para tokens personales de Sanctum, NO es necesario EnsureFrontendRequestsAreStateful.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
