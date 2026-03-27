<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('id_persediaan')->nullable()->constrained('persediaan')->nullOnDelete();
            $table->integer('jumlah')->default(1);
            $table->string('unit')->nullable();
            $table->integer('harga_satuan')->default(0);
            $table->integer('subtotal')->default(0);
            $table->date('tgl_exp')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_pesanan');
    }
};
