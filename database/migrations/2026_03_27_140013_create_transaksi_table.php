<?php

use App\Enums\MetodePembayaran;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPengiriman;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('id_kasir')->nullable()->constrained('kasir')->nullOnDelete();
            $table->foreignId('id_kurir')->nullable()->constrained('kurir')->nullOnDelete();
            $table->enum('metode_pembayaran', MetodePembayaran::values())->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->integer('total_bayar')->default(0);
            $table->enum('status_pembayaran', StatusPembayaran::values())->default(StatusPembayaran::BELUM_BAYAR->value);
            $table->enum('status_pengiriman', StatusPengiriman::values())->default(StatusPengiriman::MENUNGGU->value);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
