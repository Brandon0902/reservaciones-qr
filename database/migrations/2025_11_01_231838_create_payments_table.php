<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();

            $table->string('method', 50);              // e.g. transfer
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('MXN');

            // Estados: pending | approved | rejected
            $table->string('status', 50)->default('pending');

            $table->dateTime('payment_due_at')->nullable();
            $table->string('txn_ref', 120)->nullable();

            // Comprobante (ruta en storage/app/public/receipts)
            $table->string('receipt_ref', 255)->nullable();
            $table->dateTime('receipt_uploaded_at')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();

            $table->dateTime('paid_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
