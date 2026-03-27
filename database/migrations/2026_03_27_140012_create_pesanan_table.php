<?php

use App\Enums\StatusPesanan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_reseller')->constrained('reseller')->cascadeOnDelete();
            $table->foreignId('id_keranjang')->constrained('keranjang_belanja')->cascadeOnDelete();
            $table->enum('status', StatusPesanan::values())->default(StatusPesanan::PENDING->value);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
