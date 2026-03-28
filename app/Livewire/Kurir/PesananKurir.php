<?php

namespace App\Livewire\Kurir;

use App\Enums\StatusPengiriman;
use App\Enums\StatusPesanan;
use App\Models\Transaksi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Pesanan Saya - Kurir')]
#[Layout('layouts.kurir')]
class PesananKurir extends Component
{
    #[Url(as: 'status')]
    public string $filterStatus = '';

    public ?int $detailPesananId = null;
    public bool $showDetailModal = false;

    public function openDetail(int $pesananId): void
    {
        $this->detailPesananId = $pesananId;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailPesananId = null;
    }

    public function updateStatusPengiriman(int $transaksiId, string $status): void
    {
        $kurir = auth('kurir')->user();
        $transaksi = Transaksi::where('id', $transaksiId)
            ->where('id_kurir', $kurir->id)
            ->first();

        if (!$transaksi) {
            session()->flash('error', 'Transaksi tidak ditemukan.');
            return;
        }

        $transaksi->update(['status_pengiriman' => $status]);

        if ($status === StatusPengiriman::DITERIMA->value) {
            $transaksi->pesanan->update(['status' => StatusPesanan::SELESAI]);
        }

        session()->flash('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function render()
    {
        $kurir = auth('kurir')->user();

        $transaksis = Transaksi::where('id_kurir', $kurir->id)
            ->with(['pesanan.reseller', 'pesanan.itemPesanan.produk'])
            ->when($this->filterStatus, function ($query) {
                $query->where('status_pengiriman', $this->filterStatus);
            })
            ->latest()
            ->get();

        $detailTransaksi = null;
        if ($this->detailPesananId) {
            $detailTransaksi = Transaksi::where('id_kurir', $kurir->id)
                ->whereHas('pesanan', fn($q) => $q->where('id', $this->detailPesananId))
                ->with(['pesanan.reseller', 'pesanan.itemPesanan.produk'])
                ->first();
        }

        return view('livewire.kurir.pesanan-kurir', [
            'transaksis' => $transaksis,
            'detailTransaksi' => $detailTransaksi,
            'statusPengirimanOptions' => StatusPengiriman::cases(),
        ]);
    }
}
