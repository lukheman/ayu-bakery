<?php

namespace Database\Seeders;

use App\Enums\StatusPembayaran;
use App\Enums\StatusPengiriman;
use App\Enums\StatusPesanan;
use App\Models\ItemPesanan;
use App\Models\KeranjangBelanja;
use App\Models\Kasir;
use App\Models\Kurir;
use App\Models\Pesanan;
use App\Models\Persediaan;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\LaporanPenjualan;
use Illuminate\Database\Seeder;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        $kasirIds = Kasir::pluck('id')->toArray();
        $kurirIds = Kurir::pluck('id')->toArray();
        $produkList = Produk::all();
        $persediaanMap = Persediaan::all()->groupBy('id_produk');

        // Create 5 orders from the first 5 resellers
        KeranjangBelanja::all()->each(function (KeranjangBelanja $keranjang) use ($kasirIds, $kurirIds, $produkList, $persediaanMap) {
            $pesanan = Pesanan::factory()->create([
                'id_reseller' => $keranjang->id_reseller,
                'id_keranjang' => $keranjang->id,
                'status' => fake()->randomElement([StatusPesanan::DIPROSES, StatusPesanan::SELESAI]),
            ]);

            // Create 1-3 order items
            $totalBayar = 0;
            $totalTerjual = 0;
            $selectedProduk = $produkList->random(rand(1, 3));

            foreach ($selectedProduk as $produk) {
                $jumlah = fake()->numberBetween(1, 5);
                $hargaSatuan = $produk->harga_jual_satuan;
                $subtotal = $jumlah * $hargaSatuan;
                $totalBayar += $subtotal;
                $totalTerjual += $jumlah;

                $persediaan = $persediaanMap->get($produk->id)?->first();

                ItemPesanan::factory()->create([
                    'id_pesanan' => $pesanan->id,
                    'id_produk' => $produk->id,
                    'id_persediaan' => $persediaan?->id,
                    'jumlah' => $jumlah,
                    'unit' => $produk->unit_kecil,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                    'tgl_exp' => $persediaan?->tgl_exp,
                ]);
            }

            // Create transaction
            $transaksi = Transaksi::factory()->create([
                'id_pesanan' => $pesanan->id,
                'id_kasir' => fake()->randomElement($kasirIds),
                'id_kurir' => fake()->randomElement($kurirIds),
                'total_bayar' => $totalBayar,
                'status_pembayaran' => StatusPembayaran::SUDAH_BAYAR,
                'status_pengiriman' => StatusPengiriman::DITERIMA,
                'tanggal' => now()->subDays(rand(1, 30)),
            ]);

            // Create sales report
            LaporanPenjualan::factory()->create([
                'id_transaksi' => $transaksi->id,
                'tanggal' => $transaksi->tanggal,
                'total_pendapatan' => $totalBayar,
                'jumlah_terjual' => $totalTerjual,
            ]);
        });
    }
}
