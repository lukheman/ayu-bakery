<?php

use App\Enums\MetodePembayaran;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualan_kasir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kasir')->constrained('kasir')->cascadeOnDelete();
            $table->string('nomor_struk')->unique();
            $table->integer('total')->default(0);
            $table->integer('bayar')->default(0);
            $table->integer('kembalian')->default(0);
            $table->enum('metode_pembayaran', MetodePembayaran::values())->default(MetodePembayaran::TUNAI->value);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_kasir');
    }
};
