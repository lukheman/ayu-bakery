<?php

namespace App\Livewire\Reseller;

use App\Enums\StatusPesanan;
use App\Models\ItemKeranjang;
use App\Models\ItemPesanan;
use App\Models\KeranjangBelanja;
use App\Models\Pesanan;
use App\Models\Produk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Katalog Produk - Ayu Bakery')]
#[Layout('layouts.reseller')]
class Katalog extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public int $qty = 1;
    public ?int $selectedProdukId = null;
    public bool $showCartModal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCartModal(int $produkId): void
    {
        $this->selectedProdukId = $produkId;
        $this->qty = 1;
        $this->showCartModal = true;
    }

    public function closeCartModal(): void
    {
        $this->showCartModal = false;
        $this->selectedProdukId = null;
        $this->qty = 1;
    }

    public function addToCart(): void
    {
        $reseller = auth('reseller')->user();

        if (!$reseller || !$this->selectedProdukId) {
            return;
        }

        $produk = Produk::withSum('persediaan', 'jumlah')->find($this->selectedProdukId);
        if (!$produk) {
            return;
        }

        $stokTersedia = $produk->total_stok;

        $this->validate([
            'qty' => ['required', 'integer', 'min:1', "max:{$stokTersedia}"],
        ], [
            'qty.max' => "Jumlah melebihi stok yang tersedia ({$stokTersedia}).",
        ]);

        // Get or create keranjang
        $keranjang = KeranjangBelanja::firstOrCreate(
            ['id_reseller' => $reseller->id]
        );

        // Check if product already in cart
        $existingItem = ItemKeranjang::where('id_keranjang', $keranjang->id)
            ->where('id_produk', $produk->id)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'jumlah' => $existingItem->jumlah + max(1, $this->qty),
            ]);
        } else {
            ItemKeranjang::create([
                'id_keranjang' => $keranjang->id,
                'id_produk' => $produk->id,
                'jumlah' => max(1, $this->qty),
                'unit' => $produk->unit_kecil,
                'created_at' => now(),
            ]);
        }

        $this->closeCartModal();
        session()->flash('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function directOrder(): void
    {
        $reseller = auth('reseller')->user();

        if (!$reseller || !$this->selectedProdukId) {
            return;
        }

        $produk = Produk::withSum('persediaan', 'jumlah')->find($this->selectedProdukId);
        if (!$produk) {
            return;
        }

        $stokTersedia = $produk->total_stok;

        $this->validate([
            'qty' => ['required', 'integer', 'min:1', "max:{$stokTersedia}"],
        ], [
            'qty.max' => "Jumlah melebihi stok yang tersedia ({$stokTersedia}).",
        ]);

        $qty = max(1, $this->qty);
        $hargaSatuan = $produk->harga_jual_satuan ?? 0;

        // Get or create keranjang for FK
        $keranjang = KeranjangBelanja::firstOrCreate(
            ['id_reseller' => $reseller->id]
        );

        // Create pesanan
        $pesanan = Pesanan::create([
            'id_reseller' => $reseller->id,
            'id_keranjang' => $keranjang->id,
            'status' => StatusPesanan::PENDING,
        ]);

        // Create item pesanan
        ItemPesanan::create([
            'id_pesanan' => $pesanan->id,
            'id_produk' => $produk->id,
            'jumlah' => $qty,
            'unit' => $produk->unit_kecil,
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $qty * $hargaSatuan,
            'created_at' => now(),
        ]);

        $this->closeCartModal();
        session()->flash('success', 'Pesanan langsung berhasil dibuat! Cek halaman Pesanan Saya.');
    }

    public function render()
    {
        $produks = Produk::query()
            ->withSum('persediaan', 'jumlah')
            ->when($this->search, function ($query) {
                $query->where('nama_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('varian_rasa', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_produk', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama_produk', 'asc')
            ->paginate(12);

        return view('livewire.reseller.katalog', [
            'produks' => $produks,
        ]);
    }
}
