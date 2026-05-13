<?php

namespace App\Imports;

use App\Enums\MetodePembayaran;
use App\Models\ItemPenjualan;
use App\Models\Kasir;
use App\Models\PenjualanKasir;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PenjualanImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $penjualans = [];

        // Get a default kasir id
        $kasir = Kasir::first();
        $idKasir = $kasir ? $kasir->id : null;

        foreach ($rows as $row) {
            $namaProduk = $row['nama_produk'] ?? null;
            if (!$namaProduk) {
                continue;
            }

            // Create or Find Product
            $produk = Produk::firstOrCreate(
                ['nama_produk' => $namaProduk],
                [
                    'kode_produk' => 'PRD-' . strtoupper(substr(md5($namaProduk . time() . rand()), 0, 5)),
                    'harga_jual' => $row['harga'] ?? 0,
                    'unit_kecil' => 'pcs',
                ]
            );

            $nomorStruk = $row['nomor_struk'] ?? 'IMP-' . strtoupper(substr(md5(time() . rand()), 0, 6));
            
            try {
                // Handle different date formats or use now
                $tanggal = !empty($row['tanggal']) ? Carbon::parse($row['tanggal']) : now();
            } catch (\Exception $e) {
                $tanggal = now();
            }

            if (!isset($penjualans[$nomorStruk])) {
                $penjualan = PenjualanKasir::where('nomor_struk', $nomorStruk)->first();
                
                if (!$penjualan) {
                    // Coba tentukan metode pembayaran yang valid
                    $metodePembayaran = MetodePembayaran::TUNAI->value;
                    if (!empty($row['metode_pembayaran'])) {
                        foreach (MetodePembayaran::cases() as $metode) {
                            if (strtolower($metode->value) === strtolower($row['metode_pembayaran'])) {
                                $metodePembayaran = $metode->value;
                                break;
                            }
                        }
                    }

                    $penjualan = PenjualanKasir::create([
                        'nomor_struk' => $nomorStruk,
                        'tanggal' => $tanggal,
                        'id_kasir' => $idKasir, // Requires valid id_kasir
                        'metode_pembayaran' => $metodePembayaran,
                        'total' => 0,
                        'bayar' => 0,
                        'kembalian' => 0,
                    ]);
                }
                
                $penjualans[$nomorStruk] = $penjualan;
            }

            $penjualan = $penjualans[$nomorStruk];
            
            $harga = $row['harga'] ?? $produk->harga_jual;
            $jumlah = $row['jumlah'] ?? 1;
            $subtotal = $row['subtotal'] ?? ($harga * $jumlah);

            ItemPenjualan::create([
                'id_penjualan' => $penjualan->id,
                'id_produk' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'harga' => $harga,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
            ]);

            // Update Total
            $penjualan->total += $subtotal;
            $penjualan->bayar = $penjualan->total;
            $penjualan->save();
        }
    }
}
