<?php

namespace App\Livewire\AdminToko;

use App\Models\Produk;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Manajemen Produk - Ayu Bakery')]
class ProdukManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public string $nama_produk = '';
    public string $kode_produk = '';
    public string $varian_rasa = '';
    public int $harga_jual = 0;
    public int $harga_jual_satuan = 0;
    public string $unit_besar = '';
    public string $unit_kecil = '';
    public int $tingkat_konversi = 1;
    public string $deskripsi = '';
    public $gambar;
    public ?string $currentGambar = null;

    // State
    public ?int $editingProdukId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingProdukId = null;

    protected function rules(): array
    {
        $rules = [
            'nama_produk' => ['required', 'string', 'max:255'],
            'kode_produk' => ['required', 'string', 'max:50'],
            'varian_rasa' => ['nullable', 'string', 'max:255'],
            'harga_jual' => ['required', 'integer', 'min:0'],
            'harga_jual_satuan' => ['required', 'integer', 'min:0'],
            'unit_besar' => ['nullable', 'string', 'max:50'],
            'unit_kecil' => ['nullable', 'string', 'max:50'],
            'tingkat_konversi' => ['required', 'integer', 'min:1'],
            'gambar' => ['nullable', 'image', 'max:2048'],
        ];

        if ($this->editingProdukId) {
            $rules['kode_produk'][] = 'unique:produk,kode_produk,' . $this->editingProdukId;
        } else {
            $rules['kode_produk'][] = 'unique:produk,kode_produk';
        }

        return $rules;
    }

    protected $messages = [
        'nama_produk.required' => 'Nama produk harus diisi.',
        'kode_produk.required' => 'Kode produk harus diisi.',
        'kode_produk.unique' => 'Kode produk sudah digunakan.',
        'harga_beli.required' => 'Harga beli harus diisi.',
        'harga_jual.required' => 'Harga jual harus diisi.',
        'harga_jual_satuan.required' => 'Harga jual satuan harus diisi.',
        'gambar.image' => 'File harus berupa gambar.',
        'gambar.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGambar(): void
    {
        $this->validateOnly('gambar');
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingProdukId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $produkId): void
    {
        $produk = Produk::findOrFail($produkId);
        $this->editingProdukId = $produkId;
        $this->nama_produk = $produk->nama_produk;
        $this->kode_produk = $produk->kode_produk;
        $this->varian_rasa = $produk->varian_rasa ?? '';
        $this->harga_jual = $produk->harga_jual;
        $this->harga_jual_satuan = $produk->harga_jual_satuan;
        $this->unit_besar = $produk->unit_besar ?? '';
        $this->unit_kecil = $produk->unit_kecil ?? '';
        $this->tingkat_konversi = $produk->tingkat_konversi;
        $this->deskripsi = $produk->deskripsi ?? '';
        $this->gambar = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'nama_produk' => $validated['nama_produk'],
            'kode_produk' => $validated['kode_produk'],
            'varian_rasa' => $validated['varian_rasa'] ?: null,
            'harga_jual' => $validated['harga_jual'],
            'harga_jual_satuan' => $validated['harga_jual_satuan'],
            'unit_besar' => $validated['unit_besar'] ?: null,
            'unit_kecil' => $validated['unit_kecil'] ?: null,
            'tingkat_konversi' => $validated['tingkat_konversi'],
            'deskripsi' => $validated['deskripsi'] ?: null,
        ];

        // Handle image upload
        if ($this->gambar) {
            // Delete old image if editing
            if ($this->editingProdukId && $this->currentGambar && Storage::disk('public')->exists($this->currentGambar)) {
                Storage::disk('public')->delete($this->currentGambar);
            }
            $data['gambar'] = $this->gambar->store('produk', 'public');
        }

        if ($this->editingProdukId) {
            $produk = Produk::findOrFail($this->editingProdukId);
            $produk->update($data);
            session()->flash('success', 'Produk berhasil diperbarui.');
        } else {
            Produk::create($data);
            session()->flash('success', 'Produk berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function removeImage(): void
    {
        if ($this->editingProdukId && $this->currentGambar) {
            $produk = Produk::findOrFail($this->editingProdukId);
            if (Storage::disk('public')->exists($this->currentGambar)) {
                Storage::disk('public')->delete($this->currentGambar);
            }
            $produk->update(['gambar' => null]);
            $this->currentGambar = null;
        }
        $this->gambar = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $produkId): void
    {
        $this->deletingProdukId = $produkId;
        $this->showDeleteModal = true;
    }

    public function deleteProduk(): void
    {
        if ($this->deletingProdukId) {
            $produk = Produk::find($this->deletingProdukId);
            if ($produk) {
                if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                    Storage::disk('public')->delete($produk->gambar);
                }
                $produk->delete();
            }
            session()->flash('success', 'Produk berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingProdukId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingProdukId = null;
    }

    protected function resetForm(): void
    {
        $this->nama_produk = '';
        $this->kode_produk = '';
        $this->varian_rasa = '';
        $this->harga_jual = 0;
        $this->harga_jual_satuan = 0;
        $this->unit_besar = '';
        $this->unit_kecil = '';
        $this->tingkat_konversi = 1;
        $this->deskripsi = '';
        $this->gambar = null;
        $this->currentGambar = null;
        $this->editingProdukId = null;
    }

    public function render()
    {
        $produks = Produk::query()
            ->when($this->search, function ($query) {
                $query->where('nama_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('varian_rasa', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin-toko.produk-management', [
            'produks' => $produks,
        ]);
    }
}
