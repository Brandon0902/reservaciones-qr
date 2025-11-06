// database/migrations/2025_11_06_053100_add_token_to_tickets_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'token')) {
                $table->uuid('token')->nullable()->after('reservation_id')->unique();
            }
        });
    }

    public function down(): void {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'token')) {
                $table->dropUnique(['token']);
                $table->dropColumn('token');
            }
        });
    }
};
