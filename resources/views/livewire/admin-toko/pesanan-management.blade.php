<div>
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-1" style="color: var(--text-primary); font-weight: 700;">Manajemen Pesanan</h2>
            <p class="mb-0" style="color: var(--text-secondary);">Kelola penerimaan pesanan dan status pengiriman Reseller</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert d-flex align-items-center gap-2 mb-4" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; color: var(--success-color); padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert d-flex align-items-center gap-2 mb-4" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; color: var(--danger-color); padding: 1rem 1.25rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="modern-card">
        {{-- Toolbar --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div class="d-flex gap-2">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text bg-transparent" style="border-color: var(--border-color);"><i class="fas fa-search" style="color: var(--text-muted);"></i></span>
                    <input type="text" class="form-control" placeholder="Cari Reseller / ID..." wire:model.live.debounce.300ms="search" style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <select class="form-select" wire:model.live="filterStatus" style="width: 180px; background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StatusPesanan::cases() as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Reseller</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Pengiriman</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanans as $pesanan)
                        @php
                            $statusEnum = \App\Enums\StatusPesanan::tryFrom($pesanan->status);
                            $totalItem = $pesanan->itemPesanan->sum('jumlah');
                            $totalBayar = $pesanan->itemPesanan->sum('subtotal');
                            $transaksi = $pesanan->transaksi;
                        @endphp
                        <tr>
                            <td><span style="font-weight: 600; color: var(--primary-color);">#{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $pesanan->reseller->nama }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $pesanan->reseller->no_hp }}</div>
                            </td>
                            <td>{{ $pesanan->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $totalItem }} pcs</td>
                            <td class="fw-semibold">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge" style="background: rgba(var(--bs-{{ $statusEnum->color() }}-rgb), 0.1); color: var(--{{ $statusEnum->color() }}-color); padding: 0.5em 0.8em; border-radius: 6px; font-weight: 600;">
                                    <i class="{{ $statusEnum->icon() }} me-1"></i> {{ $statusEnum->label() }}
                                </span>
                            </td>
                            <td>
                                @if($transaksi && $pesanan->status !== \App\Enums\StatusPesanan::PENDING->value && $pesanan->status !== \App\Enums\StatusPesanan::DIBATALKAN->value)
                                    @php
                                        $pengirimanEnum = \App\Enums\StatusPengiriman::tryFrom($transaksi->status_pengiriman);
                                    @endphp
                                    <div style="font-size: 0.85rem;">
                                        <span style="color: var(--{{ $pengirimanEnum->color() }}-color); font-weight: 600;">
                                            <i class="{{ $pengirimanEnum->icon() }} me-1"></i> {{ $pengirimanEnum->label() }}
                                        </span>
                                        @if($transaksi->kurir)
                                            <div style="color: var(--text-secondary); margin-top: 2px;"><i class="fas fa-motorcycle me-1 text-muted"></i>{{ $transaksi->kurir->nama }}</div>
                                        @else
                                            <div style="color: var(--text-secondary); margin-top: 2px;">Kurir belum diset</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if ($pesanan->status === \App\Enums\StatusPesanan::PENDING->value)
                                        <button wire:click="rejectPesanan({{ $pesanan->id }})" class="btn btn-sm btn-action" style="background: rgba(239,68,68,0.1); color: var(--danger-color);" title="Tolak Pesanan">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                        <button wire:click="acceptPesanan({{ $pesanan->id }})" class="btn btn-sm btn-action" style="background: rgba(16,185,129,0.1); color: var(--success-color);" title="Terima Pesanan">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                    @elseif ($pesanan->status === \App\Enums\StatusPesanan::DIPROSES->value)
                                        <button wire:click="openDeliveryModal({{ $pesanan->id }})" class="btn btn-sm btn-action" style="background: rgba(99,102,241,0.1); color: var(--primary-color);" title="Atur Pengiriman">
                                            <i class="fas fa-truck"></i> Pengiriman
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="fas fa-inbox fa-3x"></i></div>
                                <h6 style="color: var(--text-primary);">Tidak ada pesanan ditemukan</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pesanans->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $pesanans->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Atur Pengiriman --}}
    @if($showDeliveryModal)
        <div class="modal-backdrop-custom d-flex align-items-center justify-content-center" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; animation: fadeIn 0.2s ease-out;" wire:click.self="closeDeliveryModal">
            <div class="modal-content-custom" style="background: var(--bg-secondary); border-radius: 16px; padding: 2rem; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);" wire:click.stop>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0" style="font-weight: 700; color: var(--text-primary);">Atur Pengiriman</h5>
                    <button class="btn" wire:click="closeDeliveryModal" style="background: transparent; border: none; color: var(--text-muted); padding: 0;">
                        <i class="fas fa-times" style="font-size: 1.2rem;"></i>
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: var(--text-primary);">Kurir</label>
                    <select class="form-select" wire:model="selectedKurirId" style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Pilih Kurir...</option>
                        @foreach(\App\Models\Kurir::all() as $kur)
                            <option value="{{ $kur->id }}">{{ $kur->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-weight: 600; color: var(--text-primary);">Status Pengiriman</label>
                    <select class="form-select" wire:model="selectedStatusPengiriman" style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary);">
                        @foreach(\App\Enums\StatusPengiriman::cases() as $sp)
                            <option value="{{ $sp->value }}">{{ $sp->label() }}</option>
                        @endforeach
                    </select>
                    @if($selectedStatusPengiriman === \App\Enums\StatusPengiriman::DITERIMA->value)
                        <div class="form-text text-success mt-2"><i class="fas fa-info-circle me-1"></i> Menyimpan status ini akan merubah pesanan menjadi "Selesai".</div>
                    @endif
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button class="btn" wire:click="closeDeliveryModal" style="background: var(--bg-tertiary); color: var(--text-secondary); font-weight: 600;">Batal</button>
                    <button class="btn" wire:click="updateDelivery" style="background: var(--primary-color); color: white; font-weight: 600;">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    @endif
</div>
