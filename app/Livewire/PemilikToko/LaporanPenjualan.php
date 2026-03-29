<?php

namespace App\Livewire\PemilikToko;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Models\PenjualanKasir;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Laporan Penjualan - Ayu Bakery')]
#[Layout('layouts.app')]
class LaporanPenjualan extends Component
{
    use WithPagination;

    public string $activeTab = 'kasir'; // 'kasir' | 'reseller'

    public string $search = '';
    public string $filterMetode = '';
    public string $tanggalDari = '';
    public string $tanggalSampai = '';

    public function mount(): void
    {
        $this->tanggalDari = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSampai = now()->format('Y-m-d');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedFilterMetode(): void
    {
        $this->resetPage();
    }
    public function updatedTanggalDari(): void
    {
        $this->resetPage();
    }
    public function updatedTanggalSampai(): void
    {
        $this->resetPage();
    }

    private function queryKasir()
    {
        return PenjualanKasir::query()
            ->with(['kasir', 'items'])
            ->when($this->tanggalDari, fn($q) => $q->whereDate('tanggal', '>=', $this->tanggalDari))
            ->when($this->tanggalSampai, fn($q) => $q->whereDate('tanggal', '<=', $this->tanggalSampai))
            ->when($this->filterMetode, fn($q) => $q->where('metode_pembayaran', $this->filterMetode))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('nomor_struk', 'like', '%' . $this->search . '%')
                        ->orWhereHas('kasir', fn($k) => $k->where('nama', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id');
    }

    private function queryReseller()
    {
        return Transaksi::query()
            ->with(['pesanan.reseller', 'pesanan.itemPesanan', 'kasir'])
            ->whereHas('pesanan', fn($q) => $q->where('status', StatusPesanan::SELESAI->value))
            ->when($this->tanggalDari, fn($q) => $q->whereDate('tanggal', '>=', $this->tanggalDari))
            ->when($this->tanggalSampai, fn($q) => $q->whereDate('tanggal', '<=', $this->tanggalSampai))
            ->when($this->filterMetode, fn($q) => $q->where('metode_pembayaran', $this->filterMetode))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('pesanan', fn($p) => $p->where('id', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('pesanan.reseller', fn($r) => $r->where('nama', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id');
    }

    private function getStats()
    {
        $kasirTotal = $this->queryKasir()->sum('total');
        $kasirCount = $this->queryKasir()->count();

        $resellerTotal = $this->queryReseller()->sum('total_bayar');
        $resellerCount = $this->queryReseller()->count();

        return [
            'totalPendapatan' => $kasirTotal + $resellerTotal,
            'totalTransaksi' => $kasirCount + $resellerCount,
            'pendapatanKasir' => $kasirTotal,
            'pendapatanReseller' => $resellerTotal,
        ];
    }

    public function downloadPdf()
    {
        $dataKasir = $this->queryKasir()->get();
        $dataReseller = $this->queryReseller()->get();
        $stats = $this->getStats();

        $pdf = Pdf::loadView('pdf.laporan-penjualan', [
            'dataKasir' => $dataKasir,
            'dataReseller' => $dataReseller,
            'stats' => $stats,
            'tanggalDari' => $this->tanggalDari,
            'tanggalSampai' => $this->tanggalSampai,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-penjualan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $stats = $this->getStats();

        $penjualans = $this->activeTab === 'kasir'
            ? $this->queryKasir()->paginate(15)
            : $this->queryReseller()->paginate(15);

        return view('livewire.pemilik-toko.laporan-penjualan', [
            'penjualans' => $penjualans,
            'stats' => $stats,
        ]);
    }
}
