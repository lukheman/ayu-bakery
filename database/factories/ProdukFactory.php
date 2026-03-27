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
            ['nama' => 'Roti Tawar', 'rasa' => 'Original', 'unit_besar' => 'Dus', 'unit_kecil' => 'Pcs', 'konversi' => 12],
            ['nama' => 'Roti Cokelat', 'rasa' => 'Cokelat', 'unit_besar' => 'Dus', 'unit_kecil' => 'Pcs', 'konversi' => 10],
            ['nama' => 'Roti Keju', 'rasa' => 'Keju', 'unit_besar' => 'Dus', 'unit_kecil' => 'Pcs', 'konversi' => 10],
            ['nama' => 'Donat Gula', 'rasa' => 'Gula', 'unit_besar' => 'Box', 'unit_kecil' => 'Pcs', 'konversi' => 6],
            ['nama' => 'Donat Cokelat', 'rasa' => 'Cokelat', 'unit_besar' => 'Box', 'unit_kecil' => 'Pcs', 'konversi' => 6],
            ['nama' => 'Kue Lapis', 'rasa' => 'Original', 'unit_besar' => 'Loyang', 'unit_kecil' => 'Potong', 'konversi' => 8],
            ['nama' => 'Brownies', 'rasa' => 'Cokelat', 'unit_besar' => 'Loyang', 'unit_kecil' => 'Potong', 'konversi' => 8],
            ['nama' => 'Nastar', 'rasa' => 'Nanas', 'unit_besar' => 'Toples', 'unit_kecil' => 'Pcs', 'konversi' => 30],
            ['nama' => 'Kastengel', 'rasa' => 'Keju', 'unit_besar' => 'Toples', 'unit_kecil' => 'Pcs', 'konversi' => 30],
            ['nama' => 'Bolu Pandan', 'rasa' => 'Pandan', 'unit_besar' => 'Loyang', 'unit_kecil' => 'Potong', 'konversi' => 10],
        ];

        $index = self::$counter % count($produkList);
        self::$counter++;
        $produk = $produkList[$index];

        $hargaBeli = fake()->numberBetween(15000, 50000);
        $hargaJual = $hargaBeli + fake()->numberBetween(5000, 20000);

        return [
            'nama_produk' => $produk['nama'],
            'kode_produk' => 'PRD-' . str_pad(self::$counter, 4, '0', STR_PAD_LEFT),
            'varian_rasa' => $produk['rasa'],
            'harga_jual' => $hargaJual,
            'harga_jual_satuan' => intval($hargaJual / $produk['konversi']),
            'unit_besar' => $produk['unit_besar'],
            'unit_kecil' => $produk['unit_kecil'],
            'tingkat_konversi' => $produk['konversi'],
            'deskripsi' => 'Produk ' . $produk['nama'] . ' rasa ' . $produk['rasa'] . ' dari Ayu Bakery',
            'gambar' => null,
        ];
    }
}
