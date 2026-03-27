<?php

namespace Database\Seeders;

use App\Models\Persediaan;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class PersediaanSeeder extends Seeder
{
    public function run(): void
    {
        Produk::all()->each(function (Produk $produk) {
            Persediaan::factory(rand(1, 3))->create([
                'id_produk' => $produk->id,
                'unit' => $produk->unit_kecil,
            ]);
        });
    }
}
