<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_keranjang')->constrained('keranjang_belanja')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->string('unit')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_keranjang');
    }
};
