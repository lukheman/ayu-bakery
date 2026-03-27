<div>
    {{-- Page Header --}}
    <x-page-header title="Manajemen Produk" subtitle="Kelola semua data produk">
        <x-slot:actions>
            <x-button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Produk
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-alert variant="success" title="Berhasil!" class="mb-4">
            {{ session('success') }}
        </x-alert>
    @endif

    @if (session('error'))
        <x-alert variant="danger" title="Error!" class="mb-4">
            {{ session('error') }}
        </x-alert>
    @endif

    {{-- Produk Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Semua Produk</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari produk..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Produk Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Varian</th>
                        <th>Harga Jual</th>
                        <th>Unit</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produks as $produk)
                        <tr wire:key="produk-{{ $produk->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($produk->gambar)
                                        <img src="{{ Storage::url($produk->gambar) }}"
                                            alt="{{ $produk->nama_produk }}"
                                            style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--bg-secondary); display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-box" style="color: var(--text-muted);"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold" style="color: var(--text-primary);">{{ $produk->nama_produk }}</div>
                                        <small class="text-muted">{{ $produk->kode_produk }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">{{ $produk->varian_rasa ?? '-' }}</td>
                            <td>
                                <div style="color: var(--text-primary); font-weight: 600;">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div style="color: var(--text-secondary);">{{ $produk->unit_besar ?? '-' }}</div>
                                <small class="text-muted">{{ $produk->unit_kecil ?? '-' }} ({{ $produk->tingkat_konversi }}x)</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-action-button variant="edit" wire:click="openEditModal({{ $produk->id }})"
                                        title="Edit produk" />
                                    <x-action-button variant="delete" wire:click="confirmDelete({{ $produk->id }})"
                                        title="Hapus produk" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-box-open mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada produk ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($produks->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $produks->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 700px;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingProdukId ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk <span style="color: var(--danger-color);">*</span></label>
                                <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" id="nama_produk"
                                    wire:model="nama_produk" placeholder="Masukkan nama produk">
                                @error('nama_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="kode_produk" class="form-label">Kode Produk <span style="color: var(--danger-color);">*</span></label>
                                <input type="text" class="form-control @error('kode_produk') is-invalid @enderror" id="kode_produk"
                                    wire:model="kode_produk" placeholder="Contoh: PRD-001">
                                @error('kode_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="varian_rasa" class="form-label">Varian Rasa</label>
                                <input type="text" class="form-control @error('varian_rasa') is-invalid @enderror" id="varian_rasa"
                                    wire:model="varian_rasa" placeholder="Contoh: Coklat, Keju">
                                @error('varian_rasa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                                    wire:model="deskripsi" rows="3" placeholder="Deskripsi produk"></textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="harga_beli" class="form-label">Harga Beli <span style="color: var(--danger-color);">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">Rp</span>
                                        <input type="number" class="form-control @error('harga_beli') is-invalid @enderror" id="harga_beli"
                                            wire:model="harga_beli" min="0">
                                    </div>
                                    @error('harga_beli')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="harga_jual" class="form-label">Harga Jual <span style="color: var(--danger-color);">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">Rp</span>
                                        <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" id="harga_jual"
                                            wire:model="harga_jual" min="0">
                                    </div>
                                    @error('harga_jual')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="harga_jual_satuan" class="form-label">Harga Jual Satuan <span style="color: var(--danger-color);">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">Rp</span>
                                    <input type="number" class="form-control @error('harga_jual_satuan') is-invalid @enderror" id="harga_jual_satuan"
                                        wire:model="harga_jual_satuan" min="0">
                                </div>
                                @error('harga_jual_satuan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="unit_besar" class="form-label">Unit Besar</label>
                                    <input type="text" class="form-control @error('unit_besar') is-invalid @enderror" id="unit_besar"
                                        wire:model="unit_besar" placeholder="Contoh: Box">
                                    @error('unit_besar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="unit_kecil" class="form-label">Unit Kecil</label>
                                    <input type="text" class="form-control @error('unit_kecil') is-invalid @enderror" id="unit_kecil"
                                        wire:model="unit_kecil" placeholder="Contoh: Pcs">
                                    @error('unit_kecil')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="tingkat_konversi" class="form-label">Konversi <span style="color: var(--danger-color);">*</span></label>
                                    <input type="number" class="form-control @error('tingkat_konversi') is-invalid @enderror" id="tingkat_konversi"
                                        wire:model="tingkat_konversi" min="1">
                                    @error('tingkat_konversi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Image Upload --}}
                    <div class="mb-4">
                        <label class="form-label">Gambar Produk</label>
                        <div class="d-flex align-items-start gap-3">
                            {{-- Preview --}}
                            @if ($gambar)
                                <img src="{{ $gambar->temporaryUrl() }}" alt="Preview"
                                    style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid var(--border-color);">
                            @elseif ($currentGambar)
                                <div style="position: relative;">
                                    <img src="{{ Storage::url($currentGambar) }}" alt="Current"
                                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid var(--border-color);">
                                    <button type="button" wire:click="removeImage"
                                        style="position: absolute; top: -8px; right: -8px; background: var(--danger-color); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                            <div style="flex: 1;">
                                <input type="file" class="form-control @error('gambar') is-invalid @enderror"
                                    wire:model="gambar" accept="image/*">
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maks. 2MB. Format: JPG, PNG, WEBP</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary">
                            {{ $editingProdukId ? 'Perbarui Produk' : 'Simpan Produk' }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-confirm-modal
        :show="$showDeleteModal"
        title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteProduk"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Produk
        </x-slot:confirmButton>
    </x-confirm-modal>
</div>
