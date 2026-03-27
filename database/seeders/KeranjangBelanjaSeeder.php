<?php

namespace Database\Seeders;

use App\Models\ItemKeranjang;
use App\Models\KeranjangBelanja;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class KeranjangBelanjaSeeder extends Seeder
{
    public function run(): void
    {
        // Note: KeranjangBelanja records are already created in ResellerSeeder
        // This seeder adds items to existing carts
        $produkIds = Produk::pluck('id')->toArray();

        KeranjangBelanja::all()->take(3)->each(function (KeranjangBelanja $keranjang) use ($produkIds) {
            $selectedProduk = fake()->randomElements($produkIds, rand(1, 3));
            foreach ($selectedProduk as $produkId) {
                ItemKeranjang::factory()->create([
                    'id_keranjang' => $keranjang->id,
                    'id_produk' => $produkId,
                ]);
            }
        });
    }
}
