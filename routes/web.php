<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/* ===== Controllers ===== */
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExtraServiceController;
use App\Http\Controllers\Admin\PaymentApprovalController;

use App\Http\Controllers\Client\ReservationController;
use App\Http\Controllers\Client\MyReservationsController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\TicketController;

/* ===== Público ===== */
Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/qr-test', function () {
    $data = ['hello'=>'world','time'=>now()->toIso8601String()];
    $svg  = QrCode::format('svg')->size(400)->margin(1)->generate(json_encode($data));
    Storage::disk('tickets')->put('test.svg', $svg);
    return redirect()->to(Storage::disk('tickets')->url('test.svg'));
});

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
    Route::prefix('client')
        ->name('client.')
        ->middleware('force.payment.proof')
        ->group(function () {

        Route::get('/dashboard', fn () => view('client.dashboard'))->name('dashboard');

        // === Mis reservaciones & boletos (AGRUPADOS por mesa)
        Route::get('/reservations', [MyReservationsController::class, 'index'])
            ->name('reservations.my');

        // ⬅️ ÚNICA ruta válida para ver boletos de una reservación
        Route::get('/reservations/{reservation}/tickets', [MyReservationsController::class, 'tickets'])
            ->name('reservations.tickets');

        // === Crear / ver una reservación
        Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations',        [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');

        // JSON para datepicker
        Route::get('/reservations/booked-dates', [ReservationController::class, 'bookedDates'])
            ->name('reservations.booked-dates');

        // === Boletos individuales (ver/descargar)
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');

        // === Pago (comprobante del cliente)
        Route::get('/reservations/{reservation}/payment-proof',   [PaymentController::class, 'create'])
            ->name('payments.proof');
        Route::post('/reservations/{reservation}/payment-proof',  [PaymentController::class, 'store'])
            ->name('payments.proof.store');
        Route::get('/reservations/{reservation}/payment-confirmation', [PaymentController::class, 'confirmation'])
            ->name('payments.confirmation');
    });

    /* ===== Admin ===== */
    Route::prefix('admin')->name('admin.')->middleware('admin.only')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Usuarios
        Route::resource('users', UserController::class)->except(['show']);

        // Pagos
        Route::get('/payments', [PaymentApprovalController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject',  [PaymentApprovalController::class, 'reject'])->name('payments.reject');
        Route::post('/payments/{payment}/refund',  [PaymentApprovalController::class, 'refund'])->name('payments.refund');
        Route::patch('/payments/{payment}/status', [PaymentApprovalController::class, 'updateStatus'])->name('payments.update-status');

        // Servicios extra
        Route::get('/extra-services',                 [ExtraServiceController::class, 'index'])->name('extra-services.index');
        Route::get('/extra-services/create',          [ExtraServiceController::class, 'create'])->name('extra-services.create');
        Route::post('/extra-services',                [ExtraServiceController::class, 'store'])->name('extra-services.store');
        Route::get('/extra-services/{servicio}/edit', [ExtraServiceController::class, 'edit'])->name('extra-services.edit');
        Route::put('/extra-services/{servicio}',      [ExtraServiceController::class, 'update'])->name('extra-services.update');
        Route::delete('/extra-services/{servicio}',   [ExtraServiceController::class, 'destroy'])->name('extra-services.destroy');
    });
});

/* ===== Auth (Breeze) ===== */
require __DIR__.'/auth.php';
