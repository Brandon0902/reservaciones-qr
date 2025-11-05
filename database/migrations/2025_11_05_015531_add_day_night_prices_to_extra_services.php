<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('extra_services', function (Blueprint $table) {
            $table->decimal('day_price', 10, 2)->default(0)->after('description');
            $table->decimal('night_price', 10, 2)->default(0)->after('day_price');
            $table->dropColumn('price'); // si quieres dejar de usar el campo viejo
        });
    }

    public function down(): void
    {
        Schema::table('extra_services', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0);
            $table->dropColumn(['day_price', 'night_price']);
        });
    }
};
