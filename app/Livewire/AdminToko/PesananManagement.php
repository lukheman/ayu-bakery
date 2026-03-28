<?php

namespace App\Livewire\AdminToko;

use App\Enums\StatusPembayaran;
use App\Enums\StatusPengiriman;
use App\Enums\StatusPesanan;
use App\Models\Kurir;
use App\Models\MutasiStok;
use App\Models\Persediaan;
use App\Models\Pesanan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Pesanan - Ayu Bakery')]
#[Layout('layouts.app')]
class PesananManagement extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $filterStatus = '';

    public bool $showDeliveryModal = false;
    public ?int $selectedPesananId = null;
    public ?int $selectedKurirId = null;
    public string $selectedStatusPengiriman = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function acceptPesanan(int $id): void
    {
        $pesanan = Pesanan::with('itemPesanan.produk')->find($id);

        if (!$pesanan || $pesanan->status !== StatusPesanan::PENDING->value) {
            session()->flash('error', 'Pesanan tidak dapat diproses.');
            return;
        }

        try {
            DB::transaction(function () use ($pesanan) {
                // Implementasi FEFO
                $items = $pesanan->itemPesanan;

                foreach ($items as $item) {
                    $remainingQty = $item->jumlah;

                    $availableStocks = Persediaan::where('id_produk', $item->id_produk)
                        ->where('jumlah', '>', 0)
                        ->orderBy('tgl_exp', 'asc')
                        ->lockForUpdate() // Mencegah race condition
                        ->get();

                    $totalAvailable = $availableStocks->sum('jumlah');
                    if ($totalAvailable < $remainingQty) {
                        throw new \Exception("Stok produk '{$item->produk->nama_produk}' tidak mencukupi. (Sisa: {$totalAvailable}, Diminta: {$remainingQty})");
                    }

                    $isFirst = true;
                    $originalCreatedAt = $item->created_at;

                    foreach ($availableStocks as $stock) {
                        if ($remainingQty <= 0)
                            break;

                        $take = min($remainingQty, $stock->jumlah);

                        // Kurangi stok persediaan
                        $stock->jumlah -= $take;
                        $stock->save();

                        // Catat mutasi stok
                        MutasiStok::create([
                            'id_produk' => $item->id_produk,
                            'id_persediaan' => $stock->id,
                            'jumlah' => $take,
                            'unit' => $item->unit,
                            'jenis' => \App\Enums\JenisMutasi::KELUAR,
                            'keterangan' => 'Pesanan #' . str_pad($pesanan->id, 5, '0', STR_PAD_LEFT),
                            'tanggal' => now(),
                        ]);

                        if ($isFirst) {
                            // Update item pesanan pertama dengan persediaan yang spesifik
                            $item->update([
                                'id_persediaan' => $stock->id,
                                'jumlah' => $take,
                                'subtotal' => $take * $item->harga_satuan,
                                'tgl_exp' => $stock->tgl_exp,
                            ]);
                            $isFirst = false;
                        } else {
                            // Jika persediaan berbeda, buat item pesanan baru (split)
                            \App\Models\ItemPesanan::create([
                                'id_pesanan' => $pesanan->id,
                                'id_produk' => $item->id_produk,
                                'id_persediaan' => $stock->id,
                                'jumlah' => $take,
                                'unit' => $item->unit,
                                'harga_satuan' => $item->harga_satuan,
                                'subtotal' => $take * $item->harga_satuan,
                                'tgl_exp' => $stock->tgl_exp,
                                'created_at' => $originalCreatedAt,
                            ]);
                        }

                        $remainingQty -= $take;
                    }
                }

                $pesanan->update([
                    'status' => StatusPesanan::DIPROSES,
                    'kode_konfirmasi' => strtoupper(Str::random(8)),
                ]);

                $totalBayar = $pesanan->itemPesanan()->sum('subtotal');

                Transaksi::updateOrCreate(
                    ['id_pesanan' => $pesanan->id],
                    [
                        'total_bayar' => $totalBayar,
                        'status_pembayaran' => StatusPembayaran::BELUM_BAYAR,
                        'status_pengiriman' => StatusPengiriman::MENUNGGU,
                        'tanggal' => now(),
                    ]
                );
            });

            session()->flash('success', 'Pesanan berhasil diterima dan stok telah diperbarui (metode FEFO).');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function rejectPesanan(int $id): void
    {
        $pesanan = Pesanan::find($id);

        if (!$pesanan || $pesanan->status !== StatusPesanan::PENDING->value) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan.');
            return;
        }

        $pesanan->update(['status' => StatusPesanan::DIBATALKAN]);
        session()->flash('success', 'Pesanan berhasil ditolak / dibatalkan.');
    }

    public function openDeliveryModal(int $id): void
    {
        $pesanan = Pesanan::with('transaksi')->find($id);

        if (!$pesanan || $pesanan->status !== StatusPesanan::DIPROSES->value) {
            session()->flash('error', 'Hanya pesanan yang sedang diproses yang dapat diatur pengirimannya.');
            return;
        }

        $this->selectedPesananId = $id;
        $this->selectedKurirId = $pesanan->transaksi?->id_kurir;
        $this->selectedStatusPengiriman = $pesanan->transaksi?->status_pengiriman ?? StatusPengiriman::MENUNGGU->value;
        $this->showDeliveryModal = true;
    }

    public function closeDeliveryModal(): void
    {
        $this->showDeliveryModal = false;
        $this->reset(['selectedPesananId', 'selectedKurirId', 'selectedStatusPengiriman']);
    }

    public function updateDelivery(): void
    {
        $this->validate([
            'selectedStatusPengiriman' => ['required', 'string'],
        ]);

        $pesanan = Pesanan::with('transaksi')->find($this->selectedPesananId);

        if (!$pesanan || !$pesanan->transaksi) {
            return;
        }

        $pesanan->transaksi->update([
            'id_kurir' => $this->selectedKurirId ?: null,
            'status_pengiriman' => $this->selectedStatusPengiriman,
        ]);

        if ($this->selectedStatusPengiriman === StatusPengiriman::DITERIMA->value) {
            $pesanan->update(['status' => StatusPesanan::SELESAI]);
        }

        $this->closeDeliveryModal();
        session()->flash('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function render()
    {
        $pesanans = Pesanan::query()
            ->with(['reseller', 'itemPesanan.produk', 'transaksi.kurir'])
            ->when($this->search, function ($query) {
                $query->whereHas('reseller', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                })->orWhere('id', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin-toko.pesanan-management', [
            'pesanans' => $pesanans,
            'statusOptions' => StatusPesanan::cases(),
            'statusPengirimanOptions' => StatusPengiriman::cases(),
            'kurirs' => Kurir::all(),
        ]);
    }
}
