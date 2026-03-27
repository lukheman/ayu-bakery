<?php

use App\Enums\JenisMutasi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('id_persediaan')->constrained('persediaan')->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->string('unit')->nullable();
            $table->enum('jenis', JenisMutasi::values());
            $table->string('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');
    }
};
