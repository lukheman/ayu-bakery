<?php

namespace Database\Factories;

use App\Models\ItemKeranjang;
use App\Models\KeranjangBelanja;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemKeranjang>
 */
class ItemKeranjangFactory extends Factory
{
    protected $model = ItemKeranjang::class;

    public function definition(): array
    {
        return [
            'id_keranjang' => KeranjangBelanja::factory(),
            'id_produk' => Produk::factory(),
            'jumlah' => fake()->numberBetween(1, 10),
            'unit' => fake()->randomElement(['Pcs', 'Dus', 'Box']),
            'created_at' => now(),
        ];
    }
}
