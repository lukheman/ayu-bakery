<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('kode_produk')->unique();
            $table->string('varian_rasa')->nullable();
            $table->integer('harga_jual')->default(0);
            $table->integer('harga_jual_satuan')->default(0);
            $table->string('unit_besar')->nullable();
            $table->string('unit_kecil')->nullable();
            $table->integer('tingkat_konversi')->default(1);
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
