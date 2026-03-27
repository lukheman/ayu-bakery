<?php

namespace Database\Seeders;

use App\Models\MovingAverage;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class MovingAverageSeeder extends Seeder
{
    public function run(): void
    {
        Produk::all()->each(function (Produk $produk) {
            MovingAverage::factory()->create([
                'id_produk' => $produk->id,
            ]);
        });
    }
}
