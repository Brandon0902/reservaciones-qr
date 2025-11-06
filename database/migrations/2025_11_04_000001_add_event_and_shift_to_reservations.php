<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('event_name', 120)->after('user_id');
            // shift: day | night
            $table->string('shift', 10)->after('date'); 
            $table->unsignedBigInteger('extra_service_id')->nullable()->change(); // por compatibilidad
        });
    }
    public function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['event_name','shift']);
            // no revertimos nullable() porque puede haber datos
        });
    }
};
