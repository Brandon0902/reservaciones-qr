<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();

            $table->longText('qr_payload');        // JSON con datos del boleto
            // Estados: issued | used | void
            $table->string('status', 50)->default('issued');

            $table->dateTime('issued_at');
            $table->dateTime('used_at')->nullable();

            $table->unsignedInteger('id_mesa')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
