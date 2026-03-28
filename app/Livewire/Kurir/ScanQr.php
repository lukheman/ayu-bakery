<?php

namespace App\Livewire\Kurir;

use App\Enums\StatusPengiriman;
use App\Enums\StatusPesanan;
use App\Models\Transaksi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Scan QR Konfirmasi - Kurir')]
#[Layout('layouts.kurir')]
class ScanQr extends Component
{
    public string $kodeKonfirmasi = '';
    public bool $scanResult = false;
    public string $resultMessage = '';
    public string $resultType = '';
    public ?array $pesananInfo = null;

    public function confirmDelivery(): void
    {
        if (empty($this->kodeKonfirmasi)) {
            $this->resultType = 'error';
            $this->resultMessage = 'Kode konfirmasi tidak boleh kosong.';
            $this->scanResult = true;
            return;
        }

        $kurir = auth('kurir')->user();

        $transaksi = Transaksi::where('id_kurir', $kurir->id)
            ->whereHas('pesanan', function ($q) {
                $q->where('kode_konfirmasi', $this->kodeKonfirmasi)
                    ->where('status', StatusPesanan::DIPROSES->value);
            })
            ->with(['pesanan.reseller', 'pesanan.itemPesanan.produk'])
            ->first();

        if (!$transaksi) {
            $this->resultType = 'error';
            $this->resultMessage = 'Pesanan tidak ditemukan, kode tidak valid, atau pesanan bukan milik Anda.';
            $this->scanResult = true;
            return;
        }

        $transaksi->update(['status_pengiriman' => StatusPengiriman::DITERIMA->value]);
        $transaksi->pesanan->update(['status' => StatusPesanan::SELESAI]);

        $this->pesananInfo = [
            'id' => $transaksi->pesanan->id,
            'reseller' => $transaksi->pesanan->reseller->nama,
            'total' => $transaksi->pesanan->itemPesanan->sum('subtotal'),
            'items' => $transaksi->pesanan->itemPesanan->count(),
        ];

        $this->resultType = 'success';
        $this->resultMessage = 'Pesanan berhasil dikonfirmasi! Status: Diterima / Selesai.';
        $this->scanResult = true;
        $this->kodeKonfirmasi = '';
    }

    public function resetScan(): void
    {
        $this->reset(['kodeKonfirmasi', 'scanResult', 'resultMessage', 'resultType', 'pesananInfo']);
    }

    public function render()
    {
        return view('livewire.kurir.scan-qr');
    }
}
