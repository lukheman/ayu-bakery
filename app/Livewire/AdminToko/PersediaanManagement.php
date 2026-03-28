<?php

namespace App\Livewire\AdminToko;

use App\Enums\StatusExp;
use App\Models\Persediaan;
use App\Models\Produk;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Persediaan - Ayu Bakery')]
class PersediaanManagement extends Component
{
    use WithPagination;

    public string $search = '';

    // Form fields
    public ?int $id_produk = null;
    public int $jumlah = 0;
    public ?string $tgl_produksi = null;
    public ?string $tgl_exp = null;
    public ?Produk $editingProduk = null;

    // State
    public ?int $editingPersediaanId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingPersediaanId = null;

    protected function rules(): array
    {
        return [
            'id_produk' => ['required', 'exists:produk,id'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'tgl_produksi' => ['nullable', 'date'],
            'tgl_exp' => ['nullable', 'date', 'after_or_equal:tgl_produksi'],
        ];
    }

    protected $messages = [
        'id_produk.required' => 'Produk harus dipilih.',
        'id_produk.exists' => 'Produk tidak valid.',
        'jumlah.required' => 'Jumlah persediaan harus diisi.',
        'tgl_exp.after_or_equal' => 'Tanggal expire harus setelah atau sama dengan tanggal produksi.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedIdProduk($value): void
    {
        $this->editingProduk = $value ? Produk::find($value) : null;
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingPersediaanId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $persediaanId): void
    {
        $persediaan = Persediaan::with('produk')->findOrFail($persediaanId);
        $this->editingPersediaanId = $persediaanId;
        $this->editingProduk = $persediaan->produk;
        $this->id_produk = $persediaan->id_produk;
        $this->jumlah = $persediaan->jumlah;
        $this->tgl_produksi = $persediaan->tgl_produksi ? Carbon::parse($persediaan->tgl_produksi)->format('Y-m-d') : null;
        $this->tgl_exp = $persediaan->tgl_exp ? Carbon::parse($persediaan->tgl_exp)->format('Y-m-d') : null;
        $this->showModal = true;
    }

    public function calculateSisaHariAndStatus(?string $tglExpStr): array
    {
        if (!$tglExpStr) {
            return ['sisa_hari' => 0, 'status_exp' => StatusExp::AMAN->value];
        }

        $tglExp = Carbon::parse($tglExpStr)->startOfDay();
        $now = Carbon::now()->startOfDay();

        $sisaHari = max(0, $now->diffInDays($tglExp, false));
        $sisaHariRounded = (int) ceil($sisaHari);

        if ($sisaHariRounded > 14) {
            $status = StatusExp::AMAN->value;
        } elseif ($sisaHariRounded > 3) {
            $status = StatusExp::HAMPIR_EXP->value;
        } else {
            $status = StatusExp::EXPIRED->value;
        }

        return ['sisa_hari' => $sisaHariRounded, 'status_exp' => $status];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $calc = $this->calculateSisaHariAndStatus($validated['tgl_exp']);

        $data = [
            'id_produk' => $validated['id_produk'],
            'jumlah' => $validated['jumlah'],
            'tgl_produksi' => $validated['tgl_produksi'] ?: null,
            'tgl_exp' => $validated['tgl_exp'] ?: null,
            'sisa_hari' => $calc['sisa_hari'],
            'status_exp' => $calc['status_exp'],
        ];

        if ($this->editingPersediaanId) {
            $persediaan = Persediaan::findOrFail($this->editingPersediaanId);
            $persediaan->update($data);
            session()->flash('success', 'Data persediaan berhasil diperbarui.');
        } else {
            Persediaan::create($data);
            session()->flash('success', 'Data persediaan berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $persediaanId): void
    {
        $this->deletingPersediaanId = $persediaanId;
        $this->showDeleteModal = true;
    }

    public function deletePersediaan(): void
    {
        if ($this->deletingPersediaanId) {
            $persediaan = Persediaan::find($this->deletingPersediaanId);
            if ($persediaan) {
                $persediaan->delete();
            }
            session()->flash('success', 'Data persediaan berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingPersediaanId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingPersediaanId = null;
    }

    protected function resetForm(): void
    {
        $this->id_produk = null;
        $this->jumlah = 0;
        $this->tgl_produksi = null;
        $this->tgl_exp = null;
        $this->editingPersediaanId = null;
        $this->editingProduk = null;
    }

    public function render()
    {
        $persediaans = Persediaan::query()
            ->with('produk')
            ->when($this->search, function ($query) {
                $query->whereHas('produk', function ($q) {
                    $q->where('nama_produk', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_produk', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $produks = Produk::orderBy('kode_produk', 'asc')->get();

        return view('livewire.admin-toko.persediaan-management', [
            'persediaans' => $persediaans,
            'produks' => $produks
        ]);
    }
}
