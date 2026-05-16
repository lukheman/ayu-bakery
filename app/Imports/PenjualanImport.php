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
        // Get a default kasir id
        $kasir = Kasir::first();
        $idKasir = $kasir ? $kasir->id : null;

        foreach ($rows as $row) {
            $tanggalRaw = $row['tanggal'] ?? null;
            if (!$tanggalRaw) {
                continue;
            }

            try {
                // Try to parse the date. If it's a numeric value from Excel, we convert it.
                // Otherwise, let Carbon try to parse the string.
                if (is_numeric($tanggalRaw)) {
                    $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalRaw);
                    $tanggal = Carbon::instance($tanggal);
                } else {
                    // Try to handle m/d/y or d/m/y correctly if possible, default Carbon parse is usually smart
                    $tanggal = Carbon::parse($tanggalRaw);
                }
            } catch (\Exception $e) {
                $tanggal = now();
            }

            // Create a unique struk number for this date and import
            $nomorStruk = 'IMP-' . $tanggal->format('Ymd') . '-' . strtoupper(substr(md5(time() . rand()), 0, 4));

            $penjualan = PenjualanKasir::create([
                'nomor_struk' => $nomorStruk,
                'tanggal' => $tanggal,
                'id_kasir' => $idKasir, // Requires valid id_kasir
                'metode_pembayaran' => MetodePembayaran::TUNAI->value,
                'total' => 0,
                'bayar' => 0,
                'kembalian' => 0,
            ]);

            $totalPenjualan = 0;

            // Iterate over all columns in the row
            foreach ($row as $key => $value) {
                // Skip the date column or empty values
                if ($key === 'tanggal' || empty($value) || !is_numeric($value)) {
                    continue;
                }

                $jumlah = (int) $value;
                if ($jumlah <= 0) {
                    continue;
                }

                // Extract product name from column name (e.g., 'produksi_roti_boy' -> 'Roti Boy')
                $namaProdukStr = str_replace(['produksi_', '_'], ['', ' '], $key);
                $namaProduk = ucwords($namaProdukStr);

                // Create or Find Product
                $produk = Produk::firstOrCreate(
                    ['nama_produk' => $namaProduk],
                    [
                        'kode_produk' => 'PRD-' . strtoupper(substr(md5($namaProduk . time() . rand()), 0, 5)),
                        'harga_jual' => 10000, // Default price
                        'unit_kecil' => 'pcs',
                    ]
                );

                $harga = $produk->harga_jual;
                $subtotal = $harga * $jumlah;

                ItemPenjualan::create([
                    'id_penjualan' => $penjualan->id,
                    'id_produk' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => $harga,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ]);

                $totalPenjualan += $subtotal;
            }

            // If no items were created, we can delete the empty transaction
            if ($totalPenjualan == 0) {
                $penjualan->delete();
                continue;
            }

            // Update Total
            $penjualan->total = $totalPenjualan;
            $penjualan->bayar = $totalPenjualan;
            $penjualan->save();
        }
    }
}
