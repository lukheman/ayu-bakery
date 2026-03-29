<?php

namespace App\Livewire\PemilikToko;

use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Laporan Pesanan - Ayu Bakery')]
#[Layout('layouts.app')]
class LaporanPesanan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $tanggalDari = '';
    public string $tanggalSampai = '';

    public function mount(): void
    {
        $this->tanggalDari = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSampai = now()->format('Y-m-d');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedFilterStatus(): void
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

    private function baseQuery()
    {
        return Pesanan::query()
            ->with(['reseller', 'itemPesanan.produk', 'transaksi'])
            ->when($this->tanggalDari, fn($q) => $q->whereDate('created_at', '>=', $this->tanggalDari))
            ->when($this->tanggalSampai, fn($q) => $q->whereDate('created_at', '<=', $this->tanggalSampai))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('reseller', fn($r) => $r->where('nama', 'like', '%' . $this->search . '%'));
                });
            })
            ->orderByDesc('created_at');
    }

    public function downloadPdf()
    {
        $pesanans = $this->baseQuery()->get();

        $stats = $this->calculateStats($pesanans);

        $pdf = Pdf::loadView('pdf.laporan-pesanan', [
            'pesanans' => $pesanans,
            ...$stats,
            'tanggalDari' => $this->tanggalDari,
            'tanggalSampai' => $this->tanggalSampai,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-pesanan-' . now()->format('Y-m-d') . '.pdf');
    }

    private function calculateStats($data = null)
    {
        if ($data) {
            $totalPesanan = $data->count();
            $totalPending = $data->where('status', StatusPesanan::PENDING->value)->count();
            $totalDiproses = $data->where('status', StatusPesanan::DIPROSES->value)->count();
            $totalSelesai = $data->where('status', StatusPesanan::SELESAI->value)->count();
            $totalDibatalkan = $data->where('status', StatusPesanan::DIBATALKAN->value)->count();
            $totalNilai = $data->sum(fn($p) => $p->transaksi?->total_bayar ?? $p->itemPesanan->sum('subtotal'));
        } else {
            $query = fn() => Pesanan::query()
                ->when($this->tanggalDari, fn($q) => $q->whereDate('created_at', '>=', $this->tanggalDari))
                ->when($this->tanggalSampai, fn($q) => $q->whereDate('created_at', '<=', $this->tanggalSampai))
                ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                ->when($this->search, function ($q) {
                    $q->where(function ($query) {
                        $query->where('id', 'like', '%' . $this->search . '%')
                            ->orWhereHas('reseller', fn($r) => $r->where('nama', 'like', '%' . $this->search . '%'));
                    });
                });

            $totalPesanan = $query()->count();
            $totalPending = $query()->where('status', StatusPesanan::PENDING->value)->count();
            $totalDiproses = $query()->where('status', StatusPesanan::DIPROSES->value)->count();
            $totalSelesai = $query()->where('status', StatusPesanan::SELESAI->value)->count();
            $totalDibatalkan = $query()->where('status', StatusPesanan::DIBATALKAN->value)->count();
            $totalNilai = 0;
            $query()->with(['transaksi', 'itemPesanan'])->chunk(100, function ($chunk) use (&$totalNilai) {
                $totalNilai += $chunk->sum(fn($p) => $p->transaksi?->total_bayar ?? $p->itemPesanan->sum('subtotal'));
            });
        }

        return compact('totalPesanan', 'totalPending', 'totalDiproses', 'totalSelesai', 'totalDibatalkan', 'totalNilai');
    }

    public function render()
    {
        $pesanans = $this->baseQuery()->paginate(15);
        $stats = $this->calculateStats();

        return view('livewire.pemilik-toko.laporan-pesanan', [
            'pesanans' => $pesanans,
            ...$stats,
        ]);
    }
}
