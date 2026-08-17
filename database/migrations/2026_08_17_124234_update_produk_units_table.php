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

        $columns = collect(DB::select('SHOW COLUMNS FROM produk'))->pluck('Field');

        if (! $columns->contains('unit_kecil')) {
            return;
        }

        DB::statement('ALTER TABLE produk CHANGE unit_kecil unit VARCHAR(255) NULL');
        DB::statement('ALTER TABLE produk DROP COLUMN unit_besar, DROP COLUMN tingkat_konversi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $columns = collect(DB::select('SHOW COLUMNS FROM produk'))->pluck('Field');

        if (! $columns->contains('unit')) {
            return;
        }

        DB::statement('ALTER TABLE produk CHANGE unit unit_kecil VARCHAR(255) NULL');
        DB::statement('ALTER TABLE produk ADD COLUMN unit_besar VARCHAR(255) NULL AFTER harga_jual_satuan');
        DB::statement('ALTER TABLE produk ADD COLUMN tingkat_konversi INT NOT NULL DEFAULT 1 AFTER unit_kecil');
    }
};
