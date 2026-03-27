<?php

namespace Database\Factories;

use App\Models\MutasiStok;
use App\Models\Persediaan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MutasiStok>
 */
class MutasiStokFactory extends Factory
{
    protected $model = MutasiStok::class;

    public function definition(): array
    {
        return [
            'id_produk' => Produk::factory(),
            'id_persediaan' => Persediaan::factory(),
            'jumlah' => fake()->numberBetween(1, 50),
            'unit' => fake()->randomElement(['Pcs', 'Dus', 'Box']),
            'jenis' => fake()->randomElement(['masuk', 'keluar']),
            'keterangan' => fake()->randomElement(['Produksi harian', 'Penjualan', 'Retur', 'Kadaluarsa', 'Penyesuaian stok']),
            'tanggal' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
