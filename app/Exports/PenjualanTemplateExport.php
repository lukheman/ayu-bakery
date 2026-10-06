<?php

namespace App\Exports;

use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenjualanTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Format template lebar (wide) seperti rekap harian:
     * Tanggal | Produksi {P1} | Terjual {P1} | Produksi {P2} | Terjual {P2} | ...
     *         | Sisa Hari Sebelumnya | Total Terjual | Sisa Akhir
     *
     * - Kolom "Terjual *" yang dibaca sebagai data penjualan saat import.
     * - Kolom "Produksi *", "Sisa *", "Total *" hanya info/rekap, diabaikan saat import.
     */
    public function produkList(): array
    {
        $dariDb = Produk::orderBy('nama_produk')->pluck('nama_produk')->filter()->values()->all();

        if (! empty($dariDb)) {
            return $dariDb;
        }

        return ['Roti Boy', 'Pizza', 'Roti Burger', 'Roti Coklat'];
    }

    public function headings(): array
    {
        $headings = ['Tanggal'];

        foreach ($this->produkList() as $nama) {
            $headings[] = 'Produksi '.$nama;
            $headings[] = 'Terjual '.$nama;
        }

        $headings[] = 'Sisa Hari Sebelumnya';
        $headings[] = 'Total Terjual';
        $headings[] = 'Sisa Akhir';

        return $headings;
    }

    public function collection(): Collection
    {
        $produks = $this->produkList();

        // Contoh isi mengikuti pola data Januari milik user.
        // Sisa Akhir = Σ(Produksi − Terjual) hari itu.
        // Sisa Hari Sebelumnya = Sisa Akhir hari sebelumnya (baris pertama = 0).
        $contoh = $this->contohBaris($produks);

        $rows = collect();
        $sisaSebelumnya = 0;

        foreach ($contoh as $i => $baris) {
            $tanggal = Carbon::now()->subDays(count($contoh) - 1 - $i)->format('Y-m-d');
            $row = [$tanggal];

            $totalTerjual = 0;
            $sisaAkhir = 0;

            foreach ($produks as $index => $nama) {
                $produksi = $baris[$index]['produksi'] ?? 0;
                $terjual = $baris[$index]['terjual'] ?? 0;
                $row[] = $produksi;
                $row[] = $terjual;
                $totalTerjual += $terjual;
                $sisaAkhir += ($produksi - $terjual);
            }

            $row[] = $sisaSebelumnya;
            $row[] = $totalTerjual;
            $row[] = $sisaAkhir;

            $sisaSebelumnya = $sisaAkhir;
            $rows->push($row);
        }

        return $rows;
    }

    /**
     * Contoh angka produksi & terjual per produk.
     * Untuk 4 produk default dipakai angka persis seperti data user (3 hari pertama).
     */
    private function contohBaris(array $produks): array
    {
        if (count($produks) === 4) {
            return [
                [
                    ['produksi' => 270, 'terjual' => 248],
                    ['produksi' => 200, 'terjual' => 184],
                    ['produksi' => 250, 'terjual' => 230],
                    ['produksi' => 180, 'terjual' => 166],
                ],
                [
                    ['produksi' => 260, 'terjual' => 236],
                    ['produksi' => 190, 'terjual' => 173],
                    ['produksi' => 240, 'terjual' => 218],
                    ['produksi' => 170, 'terjual' => 155],
                ],
                [
                    ['produksi' => 250, 'terjual' => 227],
                    ['produksi' => 180, 'terjual' => 164],
                    ['produksi' => 230, 'terjual' => 209],
                    ['produksi' => 160, 'terjual' => 145],
                ],
            ];
        }

        // Produk dinamis: buat pola contoh generik.
        $hasil = [];
        foreach ([0, 1, 2] as $hari) {
            $baris = [];
            foreach ($produks as $index => $nama) {
                $produksi = 200 + ($index * 20) + ($hari * 10);
                $terjual = $produksi - (15 + $index + $hari);
                $baris[] = ['produksi' => $produksi, 'terjual' => $terjual];
            }
            $hasil[] = $baris;
        }

        return $hasil;
    }
}
