<?php

namespace Database\Factories;

use App\Models\MovingAverage;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovingAverage>
 */
class MovingAverageFactory extends Factory
{
    protected $model = MovingAverage::class;

    public function definition(): array
    {
        $rataPenjualan = fake()->randomFloat(2, 5, 100);

        return [
            'id_produk' => Produk::factory(),
            'periode' => fake()->randomElement([3, 5, 7]),
            'rata_penjualan' => $rataPenjualan,
            'rekomendasi_produksi' => intval(ceil($rataPenjualan * 1.2)),
            'tgl_hitung' => fake()->dateTimeBetween('-7 days', 'now'),
            'created_at' => now(),
        ];
    }
}
