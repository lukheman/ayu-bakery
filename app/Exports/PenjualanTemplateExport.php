<?php

namespace App\Exports;

use App\Models\Kasir;
use App\Models\Produk;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenjualanTemplateExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'Tanggal',
            'Nomor Struk',
            'Kasir',
            'Metode Pembayaran',
            'Nama Produk',
            'Harga',
            'Jumlah',
            'Subtotal',
        ];
    }

    public function collection(): Collection
    {
        $produks = Produk::orderBy('nama_produk')->take(2)->get();

        if ($produks->isEmpty()) {
            $produks = collect([
                (object) ['nama_produk' => 'Roti Coklat', 'harga_jual' => 10000],
                (object) ['nama_produk' => 'Roti Keju', 'harga_jual' => 12000],
            ]);
        }

        $namaKasir = Kasir::first()?->nama ?? 'Kasir 1';
        $tanggal = now()->format('Y-m-d');

        $rows = collect();
        foreach ($produks as $i => $produk) {
            $jumlah = $i === 0 ? 10 : 5;
            $rows->push([
                $tanggal,
                'STR-'.now()->format('Ymd').'-00'.($i + 1),
                $namaKasir,
                'tunai',
                $produk->nama_produk,
                $produk->harga_jual,
                $jumlah,
                $produk->harga_jual * $jumlah,
            ]);
        }

        return $rows;
    }
}
