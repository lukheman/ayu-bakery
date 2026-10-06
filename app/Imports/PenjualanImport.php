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
        // Format lebar (wide) seperti template rekap harian:
        // Tanggal | Produksi {P1} | Terjual {P1} | ... | Sisa Hari Sebelumnya | Total Terjual | Sisa Akhir
        //
        // - Kolom "Terjual *" = jumlah terjual -> diimport sebagai penjualan.
        // - Kolom "Produksi *", "Sisa *", "Total *" = info rekap -> diabaikan.
        // - Kompatibilitas mundur: file lama yang hanya punya "Produksi *"
        //   (tanpa "Terjual *") tetap dibaca sebagai jumlah terjual.
        $kasir = Kasir::first();
        $idKasir = $kasir ? $kasir->id : null;

        foreach ($rows as $row) {
            $tanggalRaw = $row['tanggal'] ?? null;
            if (! $tanggalRaw) {
                continue;
            }

            $tanggal = $this->parseTanggal($tanggalRaw);

            // Kumpulkan kolom terjual_* dulu, fallback ke produksi_* bila tidak ada.
            $kolomTerjual = [];
            $kolomProduksi = [];
            foreach ($row as $key => $value) {
                if (! is_string($key)) {
                    continue;
                }
                if (str_starts_with($key, 'terjual_')) {
                    $kolomTerjual[$key] = $value;
                } elseif (str_starts_with($key, 'produksi_')) {
                    $kolomProduksi[$key] = $value;
                }
            }

            $kolomDipakai = ! empty($kolomTerjual) ? $kolomTerjual : $kolomProduksi;
            $prefix = ! empty($kolomTerjual) ? 'terjual_' : 'produksi_';

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

            foreach ($kolomDipakai as $key => $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                if (! is_numeric($value)) {
                    continue;
                }

                $jumlah = (int) $value;
                if ($jumlah <= 0) {
                    continue;
                }

                // 'terjual_roti_boy' / 'produksi_roti_burger' -> 'Roti Boy' / 'Roti Burger'
                $namaProdukStr = substr($key, strlen($prefix));
                $namaProdukStr = str_replace('_', ' ', $namaProdukStr);
                $namaProduk = $this->resolveNamaProduk($namaProdukStr);

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

    /**
     * Samakan nama produk dari header kolom dengan data di database
     * (case-insensitive) agar "roti boy" ketemu "Roti Boy".
     * Kolom rekap seperti sisa_*, total_* tidak pernah sampai sini.
     */
    private function resolveNamaProduk(string $namaDariKolom): string
    {
        $namaDariKolom = trim($namaDariKolom);
        if ($namaDariKolom === '') {
            return $namaDariKolom;
        }

        $produk = Produk::whereRaw('LOWER(nama_produk) = ?', [strtolower($namaDariKolom)])->first();

        if ($produk) {
            return $produk->nama_produk;
        }

        return ucwords($namaDariKolom);
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
