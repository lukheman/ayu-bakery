<div>
    {{-- Page Header --}}
    <x-page-header title="Data Penjualan" subtitle="Kelola data penjualan per produk (tambah, ubah, hapus, import)">
        <x-slot:actions>
            <div class="d-flex gap-2">
                <x-button variant="danger" icon="fas fa-trash-alt" wire:click="openDeleteAllModal">
                    Hapus Semua
                </x-button>
                <x-button variant="warning" icon="fas fa-file-import" wire:click="openImportModal">
                    Import Data Penjualan
                </x-button>
                <x-button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                    Tambah Data
                </x-button>
            </div>
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

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-4">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: var(--primary-color);">
                    <i class="fas fa-receipt"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($totalBaris) }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Baris (sesuai filter)</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4">
            <div class="stat-card" style="--accent-color: #f59e0b;">
                <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                    <i class="fas fa-box"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($totalQty) }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Qty Terjual (sesuai filter)</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4">
            <div class="stat-card" style="--accent-color: var(--success-color);">
                <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: var(--success-color);">
                    <i class="fas fa-wallet"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">Rp
                    {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Pendapatan (sesuai filter)</div>
            </div>
        </div>
    </div>

    {{-- Penjualan Table Card --}}
    <div class="modern-card">
        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Penjualan Per Produk</h5>
            <div class="d-flex gap-2 flex-wrap">
                <input type="date" class="form-control" style="max-width: 170px;" wire:model.live="tanggalDari" title="Dari tanggal">
                <input type="date" class="form-control" style="max-width: 170px;" wire:model.live="tanggalSampai" title="Sampai tanggal">
                <div class="input-group" style="max-width: 280px;">
                    <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                        <i class="fas fa-search" style="color: var(--text-muted);"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Cari produk / no. struk..."
                        wire:model.live.debounce.300ms="search" style="border-left: none;">
                </div>
            </div>
        </div>

        {{-- Penjualan Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Struk</th>
                        <th>Produk</th>
                        <th style="text-align: right;">Harga</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Subtotal</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr wire:key="row-{{ $row->id }}">
                            <td style="font-size: 0.85rem;">{{ $row->penjualan->tanggal->format('d/m/Y') }}</td>
                            <td>
                                <span style="font-weight: 600; font-size: 0.8rem; color: var(--primary-color);">{{ $row->penjualan->nomor_struk }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">{{ $row->nama_produk }}</div>
                            </td>
                            <td style="text-align: right; font-size: 0.85rem; color: var(--text-muted);">Rp {{ number_format($row->harga, 0, ',', '.') }}</td>
                            <td style="text-align: center; font-size: 0.85rem; font-weight: 600;">{{ $row->jumlah }}</td>
                            <td style="text-align: right;">
                                <span style="font-weight: 700; font-size: 0.9rem;">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <x-action-button variant="edit" wire:click="openEditModal({{ $row->id }})"
                                        title="Edit baris penjualan" />
                                    <x-action-button variant="delete" wire:click="confirmDelete({{ $row->id }})"
                                        title="Hapus baris penjualan" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-receipt mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada data penjualan ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($rows->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $rows->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingItemId ? 'Edit Baris Penjualan' : 'Tambah Baris Penjualan' }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span style="color: var(--danger-color);">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                wire:model="tanggal">
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="metode_pembayaran" class="form-label">Metode <span style="color: var(--danger-color);">*</span></label>
                            <select class="form-select @error('metode_pembayaran') is-invalid @enderror" id="metode_pembayaran" wire:model="metode_pembayaran"
                                style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                                @foreach (\App\Enums\MetodePembayaran::cases() as $metode)
                                    <option value="{{ $metode->value }}">{{ $metode->label() }}</option>
                                @endforeach
                            </select>
                            @error('metode_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Berlaku untuk transaksi baru pada tanggal tersebut.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="id_produk" class="form-label">Produk <span style="color: var(--danger-color);">*</span></label>
                        <select class="form-select @error('id_produk') is-invalid @enderror" id="id_produk" wire:model.live="id_produk"
                            style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produks as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}{{ $produk->varian_rasa ? ' - ' . $produk->varian_rasa : '' }} (Rp {{ number_format($produk->harga_jual, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        @error('id_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label">Harga <span style="color: var(--danger-color);">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">Rp</span>
                                <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga"
                                    wire:model.live="harga" min="0">
                            </div>
                            @error('harga')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="jumlah" class="form-label">Jumlah Terjual <span style="color: var(--danger-color);">*</span></label>
                            <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah"
                                wire:model.live="jumlah" min="1">
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-1 mb-3">
                        <div style="font-size: 1rem; font-weight: 700;">
                            Subtotal: <span style="color: var(--primary-color);">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary">
                            {{ $editingItemId ? 'Perbarui Data' : 'Simpan Data' }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Import Modal --}}
    @if ($showImportModal)
        <div class="modal-backdrop-custom" wire:click.self="closeImportModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 500px;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">Import Data Penjualan</h5>
                    <button type="button" class="modal-close-btn" wire:click="closeImportModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form wire:submit="importExcel">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0" style="font-weight: 600; color: var(--text-primary);">Pilih File Excel / CSV</label>
                            <button type="button" class="btn btn-sm"
                                style="background: rgba(99,102,241,0.1); color: var(--primary-color); border: 1px solid rgba(99,102,241,0.3); border-radius: 8px; font-weight: 600; font-size: 0.8rem;"
                                wire:click="downloadTemplate">
                                <i class="fas fa-download me-1"></i> Unduh Template
                            </button>
                        </div>
                        <input type="file" class="form-control"
                               wire:model="fileExcel" accept=".xlsx,.xls,.csv" required
                               style="background: var(--input-bg); border-color: var(--border-color); padding: 0.5rem;">

                        <div class="form-text mt-2" style="font-size: 0.8rem; color: var(--text-muted);">
                            Format kolom (baris pertama): <strong>Tanggal, Produksi {Nama Produk}, Terjual {Nama Produk}, ..., Sisa Hari Sebelumnya, Total Terjual, Sisa Akhir</strong> — satu baris per tanggal.
                            Hanya kolom <strong>Terjual *</strong> yang diimport sebagai penjualan; kolom Produksi/Sisa/Total hanya info rekap dan diabaikan. Unduh template untuk contoh yang sudah sesuai.
                        </div>
                        <div class="form-text mt-2" style="font-size: 0.8rem; color: #f59e0b;">
                            <i class="fas fa-info-circle me-1"></i> Produk yang belum ada di database akan ditambahkan secara otomatis berdasarkan <strong>Nama Produk</strong>.
                        </div>

                        @error('fileExcel')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div wire:loading wire:target="fileExcel" class="mt-2 text-primary small" style="font-size: 0.85rem;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Mengunggah file...
                        </div>
                        <div wire:loading wire:target="importExcel" class="mt-2 text-primary small" style="font-size: 0.85rem;">
                            <i class="fas fa-spinner fa-spin me-1"></i> Memproses data...
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="button" variant="outline" wire:click="closeImportModal">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" wire:loading.attr="disabled">
                            Import Data
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete All Confirmation Modal --}}
    @if ($showDeleteAllModal)
        <div class="modal-backdrop-custom" wire:click.self="closeDeleteAllModal">
            <div class="modal-content-custom" wire:click.stop style="max-width: 500px; border-top: 4px solid var(--danger-color);">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom" style="color: var(--danger-color);">
                        <i class="fas fa-exclamation-triangle me-2"></i>Hapus Semua Data?
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeDeleteAllModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-secondary);">
                    Akan menghapus <strong>{{ number_format($totalBaris) }} baris</strong> data penjualan
                    yang tampil pada filter saat ini (termasuk transaksi yang kehabisan item).
                    Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                </p>
                <div class="mb-3">
                    <label for="confirmText" class="form-label">Ketik <strong>HAPUS</strong> untuk mengonfirmasi</label>
                    <input type="text" class="form-control @error('confirmText') is-invalid @enderror" id="confirmText"
                        wire:model="confirmText" placeholder="HAPUS" autocomplete="off">
                    @error('confirmText')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <x-button type="button" variant="outline" wire:click="closeDeleteAllModal">
                        Batal
                    </x-button>
                    <x-button type="button" variant="danger" wire:click="deleteAll">
                        <i class="fas fa-trash-alt me-2"></i>Ya, Hapus Semua
                    </x-button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-confirm-modal
        :show="$showDeleteModal"
        title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus baris penjualan produk ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteItem"
        on-cancel="cancelDelete"
        variant="danger"
        icon="fas fa-exclamation-triangle"
    >
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus Data
        </x-slot:confirmButton>
    </x-confirm-modal>
</div>
