<?php

namespace Database\Factories;

use App\Models\ItemPesanan;
use App\Models\Persediaan;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemPesanan>
 */
class ItemPesananFactory extends Factory
{
    protected $model = ItemPesanan::class;

    public function definition(): array
    {
        $jumlah = fake()->numberBetween(1, 10);
        $hargaSatuan = fake()->numberBetween(5000, 50000);

        return [
            'id_pesanan' => Pesanan::factory(),
            'id_produk' => Produk::factory(),
            'id_persediaan' => Persediaan::factory(),
            'jumlah' => $jumlah,
            'unit' => fake()->randomElement(['Pcs', 'Dus', 'Box']),
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $jumlah * $hargaSatuan,
            'tgl_exp' => fake()->dateTimeBetween('+7 days', '+90 days'),
            'created_at' => now(),
        ];
    }
}
