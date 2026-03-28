<?php

namespace App\Livewire\Kasir;

use App\Enums\JenisMutasi;
use App\Enums\MetodePembayaran;
use App\Models\ItemPenjualan;
use App\Models\MutasiStok;
use App\Models\PenjualanKasir;
use App\Models\Persediaan;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Point of Sale - Ayu Bakery')]
#[Layout('layouts.kasir')]
class PointOfSale extends Component
{
    public string $search = '';
    public array $cart = [];
    public string $metodePembayaran = 'tunai';
    public int $bayar = 0;

    public bool $showReceipt = false;
    public ?array $receiptData = null;

    /**
     * Add an item to the cart.
     *
     * @param int $produkId
     * @param string $unit  'besar' or 'kecil'
     */
    public function addToCart(int $produkId, string $unit = 'besar'): void
    {
        $produk = Produk::find($produkId);
        if (!$produk)
            return;

        // If product has no unit_kecil, always treat as 'besar'
        if (!$produk->unit_kecil) {
            $unit = 'besar';
        }

        // Cart key distinguishes same product in different units
        $key = $produkId . '_' . $unit;

        $konversi = (int) ($produk->tingkat_konversi ?: 1);

        if ($unit === 'kecil') {
            $harga = (int) $produk->harga_jual_satuan;
            $unitLabel = $produk->unit_kecil ?? 'pcs';
            $qtyKonversi = 1; // 1 unit kecil = 1 unit kecil
        } else {
            $harga = (int) $produk->harga_jual;
            $unitLabel = $produk->unit_besar ?? 'unit';
            $qtyKonversi = $konversi; // 1 unit besar = N unit kecil
        }

        if (isset($this->cart[$key])) {
            $this->cart[$key]['jumlah']++;
            $this->cart[$key]['qty_unit_kecil'] = $this->cart[$key]['jumlah'] * $qtyKonversi;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['jumlah'] * $harga;
        } else {
            $this->cart[$key] = [
                'id_produk' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'varian' => $produk->varian_rasa,
                'unit' => $unit,
                'unit_label' => $unitLabel,
                'qty_konversi' => $qtyKonversi,
                'harga' => $harga,
                'jumlah' => 1,
                'qty_unit_kecil' => $qtyKonversi,
                'subtotal' => $harga,
            ];
        }
    }

    public function updateQty(string $key, int $qty): void
    {
        if ($qty <= 0) {
            unset($this->cart[$key]);
            return;
        }
        if (isset($this->cart[$key])) {
            $this->cart[$key]['jumlah'] = $qty;
            $this->cart[$key]['qty_unit_kecil'] = $qty * $this->cart[$key]['qty_konversi'];
            $this->cart[$key]['subtotal'] = $qty * $this->cart[$key]['harga'];
        }
    }

    public function incrementQty(string $key): void
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['jumlah']++;
            $this->cart[$key]['qty_unit_kecil'] = $this->cart[$key]['jumlah'] * $this->cart[$key]['qty_konversi'];
            $this->cart[$key]['subtotal'] = $this->cart[$key]['jumlah'] * $this->cart[$key]['harga'];
        }
    }

    public function decrementQty(string $key): void
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['jumlah']--;
            if ($this->cart[$key]['jumlah'] <= 0) {
                unset($this->cart[$key]);
            } else {
                $this->cart[$key]['qty_unit_kecil'] = $this->cart[$key]['jumlah'] * $this->cart[$key]['qty_konversi'];
                $this->cart[$key]['subtotal'] = $this->cart[$key]['jumlah'] * $this->cart[$key]['harga'];
            }
        }
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->bayar = 0;
    }

    public function getGrandTotalProperty(): int
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getKembalianProperty(): int
    {
        return max(0, $this->bayar - $this->grandTotal);
    }

    public function getCartCountProperty(): int
    {
        return collect($this->cart)->sum('jumlah');
    }

    public function processPayment(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        if ($this->bayar < $this->grandTotal) {
            session()->flash('error', 'Jumlah bayar kurang dari total!');
            return;
        }

        try {
            DB::transaction(function () {
                $kasir = auth('kasir')->user();

                // Generate nomor struk
                $today = now()->format('Ymd');
                $lastStruk = PenjualanKasir::where('nomor_struk', 'like', "STR-{$today}-%")
                    ->orderByDesc('id')
                    ->first();
                $sequence = 1;
                if ($lastStruk) {
                    $lastNum = (int) substr($lastStruk->nomor_struk, -4);
                    $sequence = $lastNum + 1;
                }
                $nomorStruk = "STR-{$today}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                $penjualan = PenjualanKasir::create([
                    'id_kasir' => $kasir->id,
                    'nomor_struk' => $nomorStruk,
                    'total' => $this->grandTotal,
                    'bayar' => $this->bayar,
                    'kembalian' => $this->kembalian,
                    'metode_pembayaran' => $this->metodePembayaran,
                    'tanggal' => now(),
                ]);

                // Process each cart item with FEFO
                // Stock is always tracked in unit kecil; qty_unit_kecil is the deduction amount.
                foreach ($this->cart as $item) {
                    $remainingQty = $item['qty_unit_kecil']; // unit kecil to deduct

                    $availableStocks = Persediaan::where('id_produk', $item['id_produk'])
                        ->where('jumlah', '>', 0)
                        ->orderBy('tgl_exp', 'asc')
                        ->lockForUpdate()
                        ->get();

                    $totalAvailable = $availableStocks->sum('jumlah');
                    if ($totalAvailable < $remainingQty) {
                        $unitLabel = $item['unit_label'];
                        throw new \Exception(
                            "Stok '{$item['nama_produk']}' tidak mencukupi! " .
                            "(Dibutuhkan: {$remainingQty} unit kecil, Tersedia: {$totalAvailable} unit kecil)"
                        );
                    }

                    $isFirst = true;
                    foreach ($availableStocks as $stock) {
                        if ($remainingQty <= 0)
                            break;

                        $take = min($remainingQty, $stock->jumlah);

                        $stock->jumlah -= $take;
                        $stock->save();

                        MutasiStok::create([
                            'id_produk' => $item['id_produk'],
                            'id_persediaan' => $stock->id,
                            'jumlah' => $take,
                            'unit' => $item['unit_label'],
                            'jenis' => JenisMutasi::KELUAR,
                            'keterangan' => 'POS ' . $nomorStruk . ' (' . $item['unit'] . ')',
                            'tanggal' => now(),
                        ]);

                        // For ItemPenjualan we record in the display unit (what customer ordered)
                        $displayQty = $isFirst
                            ? (int) ceil($take / $item['qty_konversi'])
                            : (int) ceil($take / $item['qty_konversi']);

                        ItemPenjualan::create([
                            'id_penjualan' => $penjualan->id,
                            'id_produk' => $item['id_produk'],
                            'id_persediaan' => $stock->id,
                            'nama_produk' => $item['nama_produk'],
                            'harga' => $item['harga'],
                            'jumlah' => $isFirst ? $item['jumlah'] : 0, // full qty on first batch row
                            'subtotal' => $isFirst ? $item['subtotal'] : 0,
                        ]);

                        $isFirst = false;
                        $remainingQty -= $take;
                    }
                }

                // Build receipt data
                $this->receiptData = [
                    'nomor_struk' => $nomorStruk,
                    'tanggal' => now()->format('d/m/Y H:i'),
                    'kasir' => $kasir->nama,
                    'metode' => MetodePembayaran::from($this->metodePembayaran)->label(),
                    'items' => array_values($this->cart),
                    'total' => $this->grandTotal,
                    'bayar' => $this->bayar,
                    'kembalian' => $this->kembalian,
                ];
            });

            $this->showReceipt = true;
            $this->cart = [];
            $this->bayar = 0;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function closeReceipt(): void
    {
        $this->showReceipt = false;
        $this->receiptData = null;
    }

    public function render()
    {
        $produks = Produk::query()
            ->withSum('persediaan', 'jumlah')
            ->when($this->search, function ($query) {
                $query->where('nama_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('varian_rasa', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama_produk')
            ->get();

        return view('livewire.kasir.point-of-sale', [
            'produks' => $produks,
        ]);
    }
}
