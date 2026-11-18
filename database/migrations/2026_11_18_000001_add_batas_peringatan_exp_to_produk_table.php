<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->unsignedInteger('batas_peringatan_exp')
                ->default(3)
                ->after('unit')
                ->comment('Jumlah hari sebelum expired untuk memunculkan peringatan');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('batas_peringatan_exp');
        });
    }
};
