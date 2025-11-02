<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('headcount');

            // Estados: pending | paid | cancelled (luego usaremos Enums en el Modelo)
            $table->string('status', 50)->default('pending');
            $table->dateTime('hold_expires_at')->nullable();

            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('balance_amount', 10, 2);

            $table->foreignId('extra_service_id')->nullable()->constrained('extra_services')->nullOnDelete();

            $table->string('source', 50)->nullable(); // web/app/phone
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};
