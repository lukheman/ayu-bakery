<?php

namespace Database\Factories;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produk>
 */
class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    private static int $counter = 0;

    public function definition(): array
    {
        $produkList = [
            ['nama' => 'Roti Tawar', 'rasa' => 'Original', 'unit' => 'Pcs'],
            ['nama' => 'Roti Cokelat', 'rasa' => 'Cokelat', 'unit' => 'Pcs'],
            ['nama' => 'Roti Keju', 'rasa' => 'Keju', 'unit' => 'Pcs'],
            ['nama' => 'Donat Gula', 'rasa' => 'Gula', 'unit' => 'Pcs'],
            ['nama' => 'Donat Cokelat', 'rasa' => 'Cokelat', 'unit' => 'Pcs'],
            ['nama' => 'Kue Lapis', 'rasa' => 'Original', 'unit' => 'Potong'],
            ['nama' => 'Brownies', 'rasa' => 'Cokelat', 'unit' => 'Potong'],
            ['nama' => 'Nastar', 'rasa' => 'Nanas', 'unit' => 'Pcs'],
            ['nama' => 'Kastengel', 'rasa' => 'Keju', 'unit' => 'Pcs'],
            ['nama' => 'Bolu Pandan', 'rasa' => 'Pandan', 'unit' => 'Potong'],
        ];

        $index = self::$counter % count($produkList);
        self::$counter++;
        $produk = $produkList[$index];

        $hargaBeli = fake()->numberBetween(15000, 50000);
        $hargaJual = $hargaBeli + fake()->numberBetween(5000, 20000);

        return [
            'nama_produk' => $produk['nama'],
            'kode_produk' => 'PRD-'.str_pad(self::$counter, 4, '0', STR_PAD_LEFT),
            'varian_rasa' => $produk['rasa'],
            'harga_jual' => $hargaJual,
            'harga_jual_satuan' => $hargaJual,
            'unit' => $produk['unit'],
            'deskripsi' => 'Produk '.$produk['nama'].' rasa '.$produk['rasa'].' dari Ayu Bakery',
            'gambar' => null,
        ];
    }
}
