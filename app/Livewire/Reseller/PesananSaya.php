<?php

namespace App\Livewire\Reseller;

use App\Enums\StatusPesanan;
use App\Models\Pesanan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pesanan Saya - Ayu Bakery')]
#[Layout('layouts.reseller')]
class PesananSaya extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $filterStatus = '';

    public ?int $detailPesananId = null;
    public bool $showDetailModal = false;

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

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

    public function cancelPesanan(int $pesananId): void
    {
        $reseller = auth('reseller')->user();
        $pesanan = Pesanan::where('id', $pesananId)
            ->where('id_reseller', $reseller->id)
            ->where('status', StatusPesanan::PENDING->value)
            ->first();

        if ($pesanan) {
            $pesanan->update(['status' => StatusPesanan::DIBATALKAN]);
            session()->flash('success', 'Pesanan berhasil dibatalkan.');
        }

        $this->closeDetail();
    }

    public function render()
    {
        $reseller = auth('reseller')->user();

        $pesanans = Pesanan::where('id_reseller', $reseller->id)
            ->with(['itemPesanan.produk'])
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $detailPesanan = null;
        if ($this->detailPesananId) {
            $detailPesanan = Pesanan::where('id', $this->detailPesananId)
                ->where('id_reseller', $reseller->id)
                ->with(['itemPesanan.produk'])
                ->first();
        }

        return view('livewire.reseller.pesanan-saya', [
            'pesanans' => $pesanans,
            'detailPesanan' => $detailPesanan,
            'statusOptions' => StatusPesanan::cases(),
        ]);
    }
}
