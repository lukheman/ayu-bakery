<?php

namespace App\Exports;

use App\Models\ItemPenjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenjualanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tanggalDari;
    protected $tanggalSampai;

    public function __construct($tanggalDari, $tanggalSampai)
    {
        $this->tanggalDari = $tanggalDari;
        $this->tanggalSampai = $tanggalSampai;
    }

    public function collection()
    {
        return ItemPenjualan::with(['penjualan', 'produk', 'penjualan.kasir'])
            ->whereHas('penjualan', function ($query) {
                if ($this->tanggalDari) {
                    $query->whereDate('tanggal', '>=', $this->tanggalDari);
                }
                if ($this->tanggalSampai) {
                    $query->whereDate('tanggal', '<=', $this->tanggalSampai);
                }
            })
            ->get();
    }

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
            'Subtotal'
        ];
    }

    public function map($item): array
    {
        $metodeRaw = $item->penjualan->metode_pembayaran;
        $metode = $metodeRaw instanceof \App\Enums\MetodePembayaran ? $metodeRaw->value : $metodeRaw;

        return [
            $item->penjualan->tanggal ? $item->penjualan->tanggal->format('Y-m-d') : '',
            $item->penjualan->nomor_struk,
            $item->penjualan->kasir ? $item->penjualan->kasir->nama : '-',
            $metode,
            $item->nama_produk,
            $item->harga,
            $item->jumlah,
            $item->subtotal,
        ];
    }
}
