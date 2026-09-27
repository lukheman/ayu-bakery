<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moving_average', function (Blueprint $table) {
            $table->float('mad')->default(0)->after('rata_penjualan');
        });
    }

    public function down(): void
    {
        Schema::table('moving_average', function (Blueprint $table) {
            $table->dropColumn('mad');
        });
    }
};
