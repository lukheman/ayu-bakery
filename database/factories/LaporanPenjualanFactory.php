<?php

namespace Database\Factories;

use App\Models\LaporanPenjualan;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaporanPenjualan>
 */
class LaporanPenjualanFactory extends Factory
{
    protected $model = LaporanPenjualan::class;

    public function definition(): array
    {
        return [
            'id_transaksi' => Transaksi::factory(),
            'tanggal' => fake()->dateTimeBetween('-30 days', 'now'),
            'total_pendapatan' => fake()->numberBetween(50000, 500000),
            'jumlah_terjual' => fake()->numberBetween(5, 100),
            'created_at' => now(),
        ];
    }
}
