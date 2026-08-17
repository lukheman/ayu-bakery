<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE pesanan MODIFY status ENUM('pending', 'diterima', 'dipacking', 'diantar', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE pesanan MODIFY status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'pending'");
    }
};
