<?php

namespace App\Livewire\Reseller;

use App\Enums\StatusPesanan;
use App\Models\ItemKeranjang;
use App\Models\ItemPesanan;
use App\Models\KeranjangBelanja;
use App\Models\Pesanan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Keranjang Belanja - Ayu Bakery')]
#[Layout('layouts.reseller')]
class Keranjang extends Component
{
    public string $catatan = '';
    public bool $showCheckoutModal = false;
    public array $selectedItems = [];

    public function getKeranjang(): ?KeranjangBelanja
    {
        $reseller = auth('reseller')->user();
        if (!$reseller) {
            return null;
        }

        return KeranjangBelanja::where('id_reseller', $reseller->id)
            ->with('itemKeranjang.produk')
            ->first();
    }

    public function toggleSelectAll(): void
    {
        $keranjang = $this->getKeranjang();
        if (!$keranjang)
            return;

        $allIds = $keranjang->itemKeranjang->pluck('id')->toArray();

        if (count($this->selectedItems) === count($allIds)) {
            $this->selectedItems = [];
        } else {
            $this->selectedItems = $allIds;
        }
    }

    public function updateQty(int $itemId, int $jumlah): void
    {
        $item = ItemKeranjang::find($itemId);
        if ($item && $jumlah > 0) {
            $item->update(['jumlah' => $jumlah]);
        }
    }

    public function incrementQty(int $itemId): void
    {
        $item = ItemKeranjang::find($itemId);
        if ($item) {
            $item->update(['jumlah' => $item->jumlah + 1]);
        }
    }

    public function decrementQty(int $itemId): void
    {
        $item = ItemKeranjang::find($itemId);
        if ($item && $item->jumlah > 1) {
            $item->update(['jumlah' => $item->jumlah - 1]);
        }
    }

    public function removeItem(int $itemId): void
    {
        ItemKeranjang::destroy($itemId);
        $this->selectedItems = array_values(array_diff($this->selectedItems, [$itemId]));
        session()->flash('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function openCheckoutModal(): void
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Pilih minimal 1 produk untuk checkout.');
            return;
        }
        $this->showCheckoutModal = true;
    }

    public function closeCheckoutModal(): void
    {
        $this->showCheckoutModal = false;
    }

    public function checkout(): void
    {
        $reseller = auth('reseller')->user();
        if (!$reseller) {
            return;
        }

        if (empty($this->selectedItems)) {
            session()->flash('error', 'Pilih minimal 1 produk untuk checkout.');
            return;
        }

        $keranjang = $this->getKeranjang();
        if (!$keranjang) {
            session()->flash('error', 'Keranjang belanja tidak ditemukan.');
            return;
        }

        // Create pesanan
        $pesanan = Pesanan::create([
            'id_reseller' => $reseller->id,
            'id_keranjang' => $keranjang->id,
            'status' => StatusPesanan::PENDING,
            'catatan' => $this->catatan ?: null,
        ]);

        // Snapshot selected items as ItemPesanan
        $selectedCartItems = ItemKeranjang::whereIn('id', $this->selectedItems)
            ->where('id_keranjang', $keranjang->id)
            ->with('produk')
            ->get();

        foreach ($selectedCartItems as $cartItem) {
            ItemPesanan::create([
                'id_pesanan' => $pesanan->id,
                'id_produk' => $cartItem->id_produk,
                'jumlah' => $cartItem->jumlah,
                'unit' => $cartItem->produk->unit_kecil,
                'harga_satuan' => $cartItem->produk->harga_jual_satuan ?? 0,
                'subtotal' => $cartItem->jumlah * ($cartItem->produk->harga_jual_satuan ?? 0),
                'created_at' => now(),
            ]);
        }

        // Delete the checked-out items from the cart
        ItemKeranjang::whereIn('id', $this->selectedItems)
            ->where('id_keranjang', $keranjang->id)
            ->delete();

        $this->selectedItems = [];
        $this->catatan = '';
        $this->showCheckoutModal = false;
        session()->flash('success', 'Pesanan berhasil dibuat! Item yang dipilih telah dihapus dari keranjang.');
    }

    public function render()
    {
        $keranjang = $this->getKeranjang();
        $items = $keranjang ? $keranjang->itemKeranjang->load('produk') : collect();

        // Selected items summary
        $selectedItemModels = $items->whereIn('id', $this->selectedItems);
        $selectedTotal = $selectedItemModels->sum(fn($item) => $item->jumlah * ($item->produk->harga_jual_satuan ?? 0));
        $selectedCount = $selectedItemModels->count();

        $grandTotal = $items->sum(fn($item) => $item->jumlah * ($item->produk->harga_jual_satuan ?? 0));

        return view('livewire.reseller.keranjang', [
            'items' => $items,
            'grandTotal' => $grandTotal,
            'selectedTotal' => $selectedTotal,
            'selectedCount' => $selectedCount,
        ]);
    }
}
