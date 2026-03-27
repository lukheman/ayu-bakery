<?php

namespace Database\Seeders;

use App\Models\MutasiStok;
use App\Models\Persediaan;
use Illuminate\Database\Seeder;

class MutasiStokSeeder extends Seeder
{
    public function run(): void
    {
        Persediaan::all()->each(function (Persediaan $persediaan) {
            MutasiStok::factory(rand(1, 3))->create([
                'id_produk' => $persediaan->id_produk,
                'id_persediaan' => $persediaan->id,
                'unit' => $persediaan->unit,
            ]);
        });
    }
}
