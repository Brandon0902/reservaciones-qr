<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ApiAuthController;
use App\Http\Controllers\Api\Tickets\TicketScanController;

/*
|--------------------------------------------------------------------------
| Auth (Sanctum personal tokens)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // Login (solo validadores; la lógica está en el controlador)
    Route::post('login',  [ApiAuthController::class, 'login'])
        ->middleware('throttle:10,1');

    // Requieren token válido
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::get('me',      [ApiAuthController::class, 'me']);
    });
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas para VALIDATOR
| - auth:sanctum  -> requiere Bearer token
| - ability:validator -> token debe tener esta ability
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'ability:validator'])
    ->prefix('validator')
    ->group(function () {
        // Health/ping
        Route::get('ping', fn () => response()->json(['ok' => true]));

        // Escaneo de boletos por token (QR)
        Route::post('tickets/scan', [TicketScanController::class, 'scan'])
            ->middleware('throttle:60,1');
    });
