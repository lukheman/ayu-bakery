<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penjualan')->constrained('penjualan_kasir')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('id_persediaan')->nullable()->constrained('persediaan')->nullOnDelete();
            $table->string('nama_produk');
            $table->integer('harga');
            $table->integer('jumlah');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_penjualan');
    }
};
