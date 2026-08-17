<div>
    {{-- Page Header --}}
    <x-page-header title="Manajemen Persediaan" subtitle="Kelola data stok dan persediaan produk">
        <x-slot:actions>
            <x-button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah Persediaan
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-alert variant="success" title="Berhasil!" class="mb-4">
            {{ session('success') }}
        </x-alert>
    @endif

    {{-- Persediaan Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Data Persediaan</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari nama atau kode produk..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Persediaan Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Tgl Produksi</th>
                        <th>Tgl Expired</th>
                        <th>Status</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($persediaans as $persediaan)
                        <tr wire:key="persediaan-{{ $persediaan->id }}">
                            <td>
                                <div class="fw-semibold" style="color: var(--text-primary);">
                                    {{ $persediaan->produk->nama_produk }}
                                </div>
                                <small class="text-muted">{{ $persediaan->produk->kode_produk }}</small>
                            </td>
                            <td>
                                <div style="color: var(--text-primary); font-weight: 600; font-size: 1.1rem;">
                                    {{ $persediaan->jumlah }} {{ $persediaan->produk->unit ?? '' }}
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">
                                {{ $persediaan->tgl_produksi ? $persediaan->tgl_produksi->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                @if($persediaan->tgl_exp)
                                    <div style="color: var(--text-primary);">
                                        {{ $persediaan->tgl_exp->format('d/m/Y') }}
                                    </div>
                                    <small class="text-muted">Sisa {{ $persediaan->sisa_hari }} hari</small>
                                @else
                                    <span style="color: var(--text-secondary);">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusEnum = \App\Enums\StatusExp::tryFrom($persediaan->status_exp);
                                @endphp
                                @if($statusEnum)
                                    <span
                                        class="badge-modern bg-{{ $statusEnum->color() }}-subtle text-{{ $statusEnum->color() }}">
                                        <i class="{{ $statusEnum->icon() }}"></i>
                                        {{ $statusEnum->label() }}
                                    </span>
                                @else
                                    <span class="badge-modern bg-secondary-subtle text-secondary">
                                        <i class="fas fa-minus"></i>
                                        -
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-action-button variant="edit" wire:click="openEditModal({{ $persediaan->id }})"
                                        title="Edit persediaan" />
                                    <x-action-button variant="delete" wire:click="confirmDelete({{ $persediaan->id }})"
                                        title="Hapus persediaan" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-boxes mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data persediaan ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($persediaans->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $persediaans->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 600px;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingPersediaanId ? 'Edit Persediaan' : 'Tambah Persediaan Baru' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="id_produk" class="form-label">Produk <span
                                style="color: var(--danger-color);">*</span></label>
                        <select class="form-select @error('id_produk') is-invalid @enderror" id="id_produk"
                            wire:model.live="id_produk"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->kode_produk }} - {{ $produk->nama_produk }}
                                    ({{ $produk->varian_rasa }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="jumlah" class="form-label">Jumlah <span style="color: var(--danger-color);">*</span></label>
                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah"
                                wire:model="jumlah" min="0">
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="tgl_produksi" class="form-label">Tanggal Produksi</label>
                            <input type="date" class="form-control @error('tgl_produksi') is-invalid @enderror"
                                id="tgl_produksi" wire:model="tgl_produksi">
                            @error('tgl_produksi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="tgl_exp" class="form-label">Tanggal Expired</label>
                            <input type="date" class="form-control @error('tgl_exp') is-invalid @enderror" id="tgl_exp"
                                wire:model="tgl_exp">
                            @error('tgl_exp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary">
                            {{ $editingPersediaanId ? 'Perbarui Data' : 'Simpan Data' }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-confirm-modal :show="$showDeleteModal" title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus data persediaan ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deletePersediaan" on-cancel="cancelDelete" variant="danger" icon="fas fa-exclamation-triangle">
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Persediaan
        </x-slot:confirmButton>
    </x-confirm-modal>
</div>
