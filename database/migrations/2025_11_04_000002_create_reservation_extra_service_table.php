<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservation_extra_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('extra_service_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            // guardamos el precio aplicado por pieza y el total por si cambian tarifas en el futuro
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('reservation_extra_service');
    }
};
