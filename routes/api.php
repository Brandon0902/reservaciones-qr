<?php

// routes/api.php

use App\Http\Controllers\Api\Auth\ApiAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login',  [ApiAuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me',      [ApiAuthController::class, 'me'])->middleware('auth:sanctum');
});

// Ejemplo de ruta protegida SOLO para validators:
Route::middleware(['auth:sanctum', 'ability:validator'])->group(function () {
    Route::get('/validator/ping', fn() => response()->json(['ok' => true]));
});
