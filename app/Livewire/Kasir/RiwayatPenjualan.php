<?php

namespace App\Livewire\Kasir;

use App\Models\PenjualanKasir;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Riwayat Penjualan - Ayu Bakery')]
#[Layout('layouts.kasir')]
class RiwayatPenjualan extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'tgl')]
    public string $filterTanggal = '';

    public bool $showDetailModal = false;
    public ?int $detailId = null;

    public function mount(): void
    {
        $this->filterTanggal = now()->format('Y-m-d');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTanggal(): void
    {
        $this->resetPage();
    }

    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailId = null;
    }

    public function render()
    {
        $kasir = auth('kasir')->user();

        $penjualans = PenjualanKasir::where('id_kasir', $kasir->id)
            ->with('items')
            ->when($this->search, function ($query) {
                $query->where('nomor_struk', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterTanggal, function ($query) {
                $query->whereDate('tanggal', $this->filterTanggal);
            })
            ->latest()
            ->paginate(15);

        $detailPenjualan = null;
        if ($this->detailId) {
            $detailPenjualan = PenjualanKasir::where('id_kasir', $kasir->id)
                ->where('id', $this->detailId)
                ->with('items.produk')
                ->first();
        }

        $totalHariIni = PenjualanKasir::where('id_kasir', $kasir->id)
            ->whereDate('tanggal', now())
            ->sum('total');

        $jumlahTransaksi = PenjualanKasir::where('id_kasir', $kasir->id)
            ->whereDate('tanggal', now())
            ->count();

        return view('livewire.kasir.riwayat-penjualan', [
            'penjualans' => $penjualans,
            'detailPenjualan' => $detailPenjualan,
            'totalHariIni' => $totalHariIni,
            'jumlahTransaksi' => $jumlahTransaksi,
        ]);
    }
}
