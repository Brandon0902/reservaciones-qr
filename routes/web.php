<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/* ===== Controllers ===== */
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentApprovalController;
use App\Http\Controllers\Client\ReservationController;
use App\Http\Controllers\Admin\DashboardController;

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

    /* ===== Admin (ahora con middleware propio 'admin.only') ===== */
    Route::prefix('admin')->name('admin.')->middleware('admin.only')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // CRUD de Usuarios
        Route::resource('users', UserController::class)->except(['show']);

        // Pagos: bandeja y acciones
        Route::get('/payments', [PaymentApprovalController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject',  [PaymentApprovalController::class, 'reject'])->name('payments.reject');
    });

    /* ===== Ruta de diagnóstico (temporal) ===== */
    Route::get('/debug/me', function () {
        $u = auth()->user();
        return [
            'email'         => $u?->email,
            'role_attr'     => $u?->getAttributes()['role'] ?? null,
            'role_cast'     => $u?->role instanceof \App\Enums\UserRole ? $u->role->value : $u?->role,
            'is_admin_eval' => $u && (
                ($u->role instanceof \App\Enums\UserRole && $u->role === \App\Enums\UserRole::ADMIN)
                || ($u->role === 'admin')
            ),
            'db'            => DB::connection()->getDatabaseName(),
        ];
    })->name('debug.me');
});

/* ===== Auth (Breeze) ===== */
require __DIR__ . '/auth.php';
