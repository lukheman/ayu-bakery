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
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PenjualanImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        // Format baris: Tanggal, Nomor Struk, Kasir, Metode Pembayaran,
        // Nama Produk, Harga, Jumlah, Subtotal
        if ($rows->first()->has('nama_produk')) {
            $this->importFormatBaris($rows);

            return;
        }

        // Format lebar (lama): tanggal + satu kolom produksi_<nama> per produk
        $this->importFormatLebar($rows);
    }

    private function importFormatBaris(Collection $rows): void
    {
        $defaultKasir = Kasir::first();

        // Kelompokkan baris per nomor struk (satu struk = satu transaksi)
        $grup = [];
        foreach ($rows as $row) {
            $namaProduk = trim((string) ($row['nama_produk'] ?? ''));
            $jumlah = (int) ($row['jumlah'] ?? 0);
            if ($namaProduk === '' || $jumlah <= 0) {
                continue;
            }

            $nomorStruk = trim((string) ($row['nomor_struk'] ?? ''));
            $key = $nomorStruk !== '' ? 'STRUK:'.$nomorStruk : 'TANGGAL:'.(string) ($row['tanggal'] ?? '');
            $grup[$key][] = $row;
        }

        foreach ($grup as $baris) {
            $pertama = $baris[0];
            $tanggal = $this->parseTanggal($pertama['tanggal'] ?? null);

            $nomorStruk = trim((string) ($pertama['nomor_struk'] ?? ''));
            if ($nomorStruk === '' || PenjualanKasir::where('nomor_struk', $nomorStruk)->exists()) {
                $nomorStruk = 'IMP-'.$tanggal->format('Ymd').'-'.strtoupper(substr(md5(microtime().rand()), 0, 4));
            }

            $penjualan = PenjualanKasir::create([
                'nomor_struk' => $nomorStruk,
                'tanggal' => $tanggal,
                'id_kasir' => $this->resolveKasirId($pertama['kasir'] ?? null, $defaultKasir),
                'metode_pembayaran' => $this->resolveMetode($pertama['metode_pembayaran'] ?? null),
                'total' => 0,
                'bayar' => 0,
                'kembalian' => 0,
            ]);

            $totalPenjualan = 0;

            foreach ($baris as $row) {
                $produk = $this->findOrCreateProduk(trim((string) $row['nama_produk']));
                $jumlah = (int) $row['jumlah'];

                $harga = is_numeric($row['harga'] ?? null) ? (int) $row['harga'] : $produk->harga_jual;
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

            if ($totalPenjualan == 0) {
                $penjualan->delete();

                continue;
            }

            $penjualan->total = $totalPenjualan;
            $penjualan->bayar = $totalPenjualan;
            $penjualan->save();
        }
    }

    private function importFormatLebar(Collection $rows): void
    {
        // Get a default kasir id
        $kasir = Kasir::first();
        $idKasir = $kasir ? $kasir->id : null;

        foreach ($rows as $row) {
            $tanggalRaw = $row['tanggal'] ?? null;
            if (! $tanggalRaw) {
                continue;
            }

            $tanggal = $this->parseTanggal($tanggalRaw);

            // Create a unique struk number for this date and import
            $nomorStruk = 'IMP-'.$tanggal->format('Ymd').'-'.strtoupper(substr(md5(time().rand()), 0, 4));

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
                if ($key === 'tanggal' || empty($value) || ! is_numeric($value)) {
                    continue;
                }

                $jumlah = (int) $value;
                if ($jumlah <= 0) {
                    continue;
                }

                // Extract product name from column name (e.g., 'produksi_roti_boy' -> 'Roti Boy')
                $namaProdukStr = str_replace(['produksi_', '_'], ['', ' '], $key);
                $namaProduk = ucwords($namaProdukStr);

                $produk = $this->findOrCreateProduk($namaProduk);

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

    private function parseTanggal(mixed $tanggalRaw): Carbon
    {
        try {
            // Try to parse the date. If it's a numeric value from Excel, we convert it.
            // Otherwise, let Carbon try to parse the string.
            if (is_numeric($tanggalRaw)) {
                $tanggal = Date::excelToDateTimeObject($tanggalRaw);

                return Carbon::instance($tanggal);
            }

            // Try to handle m/d/y or d/m/y correctly if possible, default Carbon parse is usually smart
            return Carbon::parse($tanggalRaw);
        } catch (\Exception $e) {
            return now();
        }
    }

    private function findOrCreateProduk(string $namaProduk): Produk
    {
        return Produk::firstOrCreate(
            ['nama_produk' => $namaProduk],
            [
                'kode_produk' => 'PRD-'.strtoupper(substr(md5($namaProduk.time().rand()), 0, 5)),
                'harga_jual' => 10000, // Default price
                'unit' => 'pcs',
            ]
        );
    }

    private function resolveKasirId(mixed $namaKasir, ?Kasir $defaultKasir): ?int
    {
        $namaKasir = trim((string) ($namaKasir ?? ''));
        if ($namaKasir !== '') {
            $kasir = Kasir::where('nama', 'like', '%'.$namaKasir.'%')->first();
            if ($kasir) {
                return $kasir->id;
            }
        }

        return $defaultKasir?->id;
    }

    private function resolveMetode(mixed $metode): string
    {
        $metode = strtolower(trim((string) ($metode ?? '')));
        if (in_array($metode, MetodePembayaran::values(), true)) {
            return $metode;
        }

        return MetodePembayaran::TUNAI->value;
    }
}
