<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/* ===== Controllers ===== */
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentApprovalController;
use App\Http\Controllers\Client\ReservationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExtraServiceController;

/* ===== Público ===== */
Route::get('/', fn () => view('welcome'))->name('home');

/* ===== Dashboard genérico (opcional) ===== */
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth','verified'])
    ->name('dashboard');

/* ===== Autenticado ===== */
Route::middleware('auth')->group(function () {

    // Perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* ===== Cliente ===== */
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', fn () => view('client.dashboard'))->name('dashboard');

        // Reservas cliente
        Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations',        [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/receipt', [ReservationController::class, 'uploadReceipt'])
            ->name('reservations.upload-receipt');
    });

    /* ===== Admin (middleware propio 'admin.only') ===== */
    Route::prefix('admin')->name('admin.')->middleware('admin.only')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // CRUD de Usuarios
        Route::resource('users', UserController::class)->except(['show']);

        // Pagos: bandeja y acciones
        Route::get('/payments', [PaymentApprovalController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject',  [PaymentApprovalController::class, 'reject'])->name('payments.reject');

        // CRUD de Servicios Extras
        Route::get('/extra-services', [ExtraServiceController::class, 'index'])->name('extra-services.index');
        Route::get('/extra-services/create', [ExtraServiceController::class, 'create'])->name('extra-services.create');
        Route::post('/extra-services', [ExtraServiceController::class, 'store'])->name('extra-services.store');
        Route::get('/extra-services/{servicio}/edit', [ExtraServiceController::class, 'edit'])->name('extra-services.edit');
        Route::put('/extra-services/{servicio}', [ExtraServiceController::class, 'update'])->name('extra-services.update');
        Route::delete('/extra-services/{servicio}', [ExtraServiceController::class, 'destroy'])->name('extra-services.destroy');


    });
});

/* ===== Auth (Breeze) ===== */
require __DIR__ . '/auth.php';
