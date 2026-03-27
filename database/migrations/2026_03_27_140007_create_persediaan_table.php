<?php

use App\Enums\StatusExp;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('persediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('produk')->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->string('unit')->nullable();
            $table->date('tgl_produksi')->nullable();
            $table->date('tgl_exp')->nullable();
            $table->integer('sisa_hari')->default(0);
            $table->enum('status_exp', StatusExp::values())->default(StatusExp::AMAN->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persediaan');
    }
};
