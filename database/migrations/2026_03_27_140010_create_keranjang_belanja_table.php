<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('keranjang_belanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_reseller')->constrained('reseller')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang_belanja');
    }
};
