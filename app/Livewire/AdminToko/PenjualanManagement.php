<?php

namespace App\Livewire\AdminToko;

use App\Enums\MetodePembayaran;
use App\Exports\PenjualanTemplateExport;
use App\Imports\PenjualanImport;
use App\Models\ItemPenjualan;
use App\Models\Kasir;
use App\Models\PenjualanKasir;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title('Data Penjualan - Ayu Bakery')]
class PenjualanManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Filters
    #[Url(as: 'q')]
    public string $search = '';

    public ?string $tanggalDari = null;

    public ?string $tanggalSampai = null;

    // Form fields (satu baris = satu produk terjual)
    public ?int $editingItemId = null;

    public ?string $tanggal = null;

    public string $metode_pembayaran = 'tunai';

    public $id_produk = null;

    public int $harga = 0;

    public int $jumlah = 1;

    // State
    public bool $showModal = false;

    public bool $showDeleteModal = false;

    public ?int $deletingItemId = null;

    // Hapus semua (sesuai filter aktif)
    public bool $showDeleteAllModal = false;

    public string $confirmText = '';

    // Import Excel
    public $fileExcel;

    public bool $showImportModal = false;

    protected function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'metode_pembayaran' => ['required', 'in:'.implode(',', MetodePembayaran::values())],
            'id_produk' => ['required', 'exists:produk,id'],
            'harga' => ['required', 'integer', 'min:0'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ];
    }

    protected $messages = [
        'tanggal.required' => 'Tanggal harus diisi.',
        'id_produk.required' => 'Produk harus dipilih.',
        'id_produk.exists' => 'Produk tidak valid.',
        'harga.min' => 'Harga tidak boleh negatif.',
        'jumlah.min' => 'Jumlah minimal 1.',
    ];

    public function updatedSearch(): void
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

    /**
     * Saat produk diganti, isi harga otomatis dari harga jual
     * produk (masih bisa diubah manual).
     */
    public function updatedIdProduk($value): void
    {
        $produk = Produk::find($value);
        if ($produk) {
            $this->harga = (int) $produk->harga_jual;
        }
    }

    public function getSubtotalProperty(): int
    {
        return (int) $this->harga * (int) $this->jumlah;
    }

    public function openCreateModal(): void
    {
        if (Kasir::count() === 0) {
            session()->flash('error', 'Belum ada data kasir. Tambahkan data kasir terlebih dahulu melalui menu Pengguna.');

            return;
        }
        if (Produk::count() === 0) {
            session()->flash('error', 'Belum ada data produk. Tambahkan data produk terlebih dahulu melalui menu Produk.');

            return;
        }

        $this->resetForm();
        $this->editingItemId = null;
        $this->tanggal = now()->format('Y-m-d');
        $this->metode_pembayaran = MetodePembayaran::TUNAI->value;
        $this->showModal = true;
    }

    public function openEditModal(int $itemId): void
    {
        $item = ItemPenjualan::with('penjualan')->findOrFail($itemId);

        $this->editingItemId = $item->id;
        $this->tanggal = Carbon::parse($item->penjualan->tanggal)->format('Y-m-d');
        $this->metode_pembayaran = $item->penjualan->metode_pembayaran instanceof MetodePembayaran
            ? $item->penjualan->metode_pembayaran->value
            : (string) $item->penjualan->metode_pembayaran;
        $this->id_produk = $item->id_produk;
        $this->harga = (int) $item->harga;
        $this->jumlah = (int) $item->jumlah;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Kolom id_kasir wajib diisi database, jadi pakai kasir pertama otomatis.
        $idKasir = Kasir::orderBy('id')->value('id');
        if (! $idKasir) {
            session()->flash('error', 'Belum ada data kasir. Tambahkan data kasir terlebih dahulu melalui menu Pengguna.');

            return;
        }

        DB::transaction(function () use ($validated, $idKasir) {
            $tanggal = Carbon::parse($validated['tanggal'])->format('Y-m-d');
            $produk = Produk::findOrFail($validated['id_produk']);
            $subtotal = (int) $validated['harga'] * (int) $validated['jumlah'];

            if ($this->editingItemId) {
                $item = ItemPenjualan::findOrFail($this->editingItemId);
                $oldParentId = $item->id_penjualan;

                $target = $this->findOrCreateTransaksi(
                    $tanggal,
                    $validated['metode_pembayaran']
                );

                $item->update([
                    'id_penjualan' => $target->id,
                    'id_produk' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => (int) $validated['harga'],
                    'jumlah' => (int) $validated['jumlah'],
                    'subtotal' => $subtotal,
                ]);

                // Hitung ulang transaksi lama & baru (transaksi kosong ikut terhapus).
                $this->refreshTotals($oldParentId);
                $this->refreshTotals($target->id);
            } else {
                $target = $this->findOrCreateTransaksi(
                    $tanggal,
                    $validated['metode_pembayaran']
                );

                ItemPenjualan::create([
                    'id_penjualan' => $target->id,
                    'id_produk' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga' => (int) $validated['harga'],
                    'jumlah' => (int) $validated['jumlah'],
                    'subtotal' => $subtotal,
                ]);

                $this->refreshTotals($target->id);
            }
        });

        session()->flash('success', $this->editingItemId ? 'Data penjualan berhasil diperbarui.' : 'Data penjualan berhasil ditambahkan.');
        $this->closeModal();
    }

    /**
     * Cari transaksi pada tanggal yang sama, atau buat baru bila belum ada.
     * Baris produk baru menempel ke transaksi harian tersebut.
     * Kolom kasir wajib diisi database sehingga memakai kasir pertama.
     */
    private function findOrCreateTransaksi(string $tanggal, string $metode): PenjualanKasir
    {
        $transaksi = PenjualanKasir::whereDate('tanggal', $tanggal)
            ->orderByDesc('id')
            ->first();

        if ($transaksi) {
            return $transaksi;
        }

        return PenjualanKasir::create([
            'nomor_struk' => $this->generateNomorStruk($tanggal),
            'tanggal' => $tanggal,
            'id_kasir' => Kasir::orderBy('id')->value('id'),
            'metode_pembayaran' => $metode,
            'total' => 0,
            'bayar' => 0,
            'kembalian' => 0,
        ]);
    }

    /**
     * Hitung ulang total transaksi dari itemnya. Transaksi yang sudah
     * tidak punya item ikut dihapus agar tidak ada struk kosong.
     */
    private function refreshTotals(int $idPenjualan): void
    {
        $penjualan = PenjualanKasir::find($idPenjualan);
        if (! $penjualan) {
            return;
        }

        if ($penjualan->items()->count() === 0) {
            $penjualan->delete();

            return;
        }

        $total = (int) $penjualan->items()->sum('subtotal');
        $penjualan->update([
            'total' => $total,
            'bayar' => $total,
            'kembalian' => 0,
        ]);
    }

    /**
     * Nomor struk mengikuti tanggal transaksi: STR-YYYYMMDD-NNNN.
     */
    private function generateNomorStruk(string $tanggal): string
    {
        $prefix = 'STR-'.Carbon::parse($tanggal)->format('Ymd').'-';

        $terakhir = PenjualanKasir::where('nomor_struk', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->first();

        $sequence = 1;
        if ($terakhir) {
            $sequence = (int) substr($terakhir->nomor_struk, -4) + 1;
        }

        return $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $itemId): void
    {
        $this->deletingItemId = $itemId;
        $this->showDeleteModal = true;
    }

    public function deleteItem(): void
    {
        if ($this->deletingItemId) {
            DB::transaction(function () {
                $item = ItemPenjualan::find($this->deletingItemId);
                if ($item) {
                    $parentId = $item->id_penjualan;
                    $item->delete();
                    $this->refreshTotals($parentId);
                }
            });
            session()->flash('success', 'Data penjualan berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingItemId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingItemId = null;
    }

    public function openDeleteAllModal(): void
    {
        if ((clone $this->baseQuery())->count() === 0) {
            session()->flash('error', 'Tidak ada data penjualan pada filter saat ini.');

            return;
        }

        $this->confirmText = '';
        $this->resetValidation();
        $this->showDeleteAllModal = true;
    }

    public function closeDeleteAllModal(): void
    {
        $this->showDeleteAllModal = false;
        $this->confirmText = '';
    }

    /**
     * Hapus seluruh baris produk yang tampil pada filter aktif.
     * Transaksi yang kehabisan item ikut terhapus otomatis.
     */
    public function deleteAll(): void
    {
        if (trim($this->confirmText) !== 'HAPUS') {
            $this->addError('confirmText', 'Ketik HAPUS (huruf kapital) untuk mengonfirmasi.');

            return;
        }

        $ids = (clone $this->baseQuery())->pluck('id')->all();

        DB::transaction(function () use ($ids) {
            $parentIds = ItemPenjualan::whereIn('id', $ids)->pluck('id_penjualan')->unique()->all();
            ItemPenjualan::whereIn('id', $ids)->delete();

            foreach ($parentIds as $parentId) {
                $this->refreshTotals($parentId);
            }
        });

        session()->flash('success', count($ids).' baris data penjualan berhasil dihapus.');
        $this->closeDeleteAllModal();
        $this->resetPage();
    }

    protected function resetForm(): void
    {
        $this->editingItemId = null;
        $this->tanggal = null;
        $this->metode_pembayaran = MetodePembayaran::TUNAI->value;
        $this->id_produk = null;
        $this->harga = 0;
        $this->jumlah = 1;
    }

    public function openImportModal(): void
    {
        $this->reset('fileExcel');
        $this->resetValidation();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->reset('fileExcel');
    }

    public function downloadTemplate()
    {
        return Excel::download(new PenjualanTemplateExport, 'template-import-penjualan.xlsx');
    }

    public function importExcel(): void
    {
        $this->validate([
            'fileExcel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new PenjualanImport, $this->fileExcel->getRealPath());
            session()->flash('success', 'Data penjualan berhasil diimport.');
            $this->closeImportModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat import: '.$e->getMessage());
        }
    }

    private function baseQuery()
    {
        return ItemPenjualan::query()
            ->with(['penjualan', 'produk'])
            ->whereHas('penjualan', function ($q) {
                $q->when($this->tanggalDari, fn ($qq) => $qq->whereDate('tanggal', '>=', $this->tanggalDari))
                    ->when($this->tanggalSampai, fn ($qq) => $qq->whereDate('tanggal', '<=', $this->tanggalSampai));
            })
            ->when($this->search, function ($q) {
                $keyword = '%'.$this->search.'%';
                $q->where(function ($query) use ($keyword) {
                    $query->where('nama_produk', 'like', $keyword)
                        ->orWhereHas('penjualan', fn ($penjualan) => $penjualan->where('nomor_struk', 'like', $keyword));
                });
            });
    }

    public function render()
    {
        $rows = (clone $this->baseQuery())
            ->join('penjualan_kasir', 'item_penjualan.id_penjualan', '=', 'penjualan_kasir.id')
            ->orderBy('penjualan_kasir.tanggal', 'desc')
            ->orderBy('item_penjualan.id', 'desc')
            ->select('item_penjualan.*')
            ->paginate(15);

        $totalBaris = (clone $this->baseQuery())->count();
        $totalQty = (clone $this->baseQuery())->sum('jumlah');
        $totalPendapatan = (clone $this->baseQuery())->sum('subtotal');

        return view('livewire.admin-toko.penjualan-management', [
            'rows' => $rows,
            'totalBaris' => $totalBaris,
            'totalQty' => (int) $totalQty,
            'totalPendapatan' => (int) $totalPendapatan,
            'produks' => Produk::orderBy('nama_produk')->get(),
        ]);
    }
}
