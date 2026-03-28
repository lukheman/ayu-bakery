<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Pesanan Saya</h3>
            <p class="mb-0" style="color: var(--text-secondary);">Riwayat dan status pesanan Anda</p>
        </div>

        {{-- Status Filter --}}
        <div class="d-flex align-items-center gap-2">
            <select wire:model.live="filterStatus" class="form-select"
                style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.6rem 1rem; min-width: 180px;">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert d-flex align-items-center gap-2 mb-4"
            style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; color: var(--success-color); padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Orders List --}}
    <div class="d-flex flex-column gap-3">
        @forelse ($pesanans as $pesanan)
            @php
                $statusEnum = \App\Enums\StatusPesanan::tryFrom($pesanan->status);
                $items = $pesanan->itemPesanan ?? collect();
                $total = $items->sum('subtotal');
            @endphp
            <div class="modern-card" wire:key="pesanan-{{ $pesanan->id }}" style="padding: 1.25rem 1.5rem;">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    {{-- Order Info --}}
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span style="font-weight: 700; color: var(--text-primary); font-size: 1rem;">
                                #{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            @if($statusEnum)
                                <span style="font-size: 0.75rem; padding: 4px 10px; border-radius: 50px; font-weight: 600;
                                    background: rgba({{ $statusEnum->color() === 'warning' ? '245,158,11' : ($statusEnum->color() === 'primary' ? '99,102,241' : ($statusEnum->color() === 'success' ? '16,185,129' : '239,68,68')) }}, 0.1);
                                    color: var(--{{ $statusEnum->color() }}-color);">
                                    <i class="{{ $statusEnum->icon() }}"></i>
                                    {{ $statusEnum->label() }}
                                </span>
                            @endif
                        </div>
                        <div style="color: var(--text-muted); font-size: 0.85rem;">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $pesanan->created_at->format('d M Y, H:i') }}
                        </div>
                        <div class="mt-1" style="color: var(--text-secondary); font-size: 0.85rem;">
                            {{ $items->count() }} produk
                            @if($pesanan->catatan)
                                · <i class="fas fa-sticky-note"></i> {{ Str::limit($pesanan->catatan, 40) }}
                            @endif
                        </div>
                    </div>

                    {{-- Total & Actions --}}
                    <div class="text-end">
                        <div style="font-weight: 700; color: var(--primary-color); font-size: 1.1rem;">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </div>
                        <button wire:click="openDetail({{ $pesanan->id }})" class="btn btn-sm mt-2"
                            style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.4rem 1rem; font-size: 0.8rem;">
                            <i class="fas fa-eye me-1"></i> Detail
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="modern-card text-center py-5">
                <i class="fas fa-receipt mb-3" style="font-size: 3rem; color: var(--text-muted);"></i>
                <h5 style="color: var(--text-primary); font-weight: 600;">Belum Ada Pesanan</h5>
                <p style="color: var(--text-secondary);">Anda belum memiliki pesanan. Mulai belanja di katalog!</p>
                <a href="{{ route('reseller.katalog') }}" class="btn mt-2"
                    style="background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.7rem 2rem;">
                    <i class="fas fa-store me-1"></i> Lihat Katalog
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($pesanans->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $pesanans->links() }}
        </div>
    @endif

    {{-- Detail Modal --}}
    @if ($showDetailModal && $detailPesanan)
        @php
            $detailItems = $detailPesanan->itemPesanan ?? collect();
            $detailTotal = $detailItems->sum('subtotal');
            $detailStatus = \App\Enums\StatusPesanan::tryFrom($detailPesanan->status);
        @endphp
        <div class="modal-backdrop-custom" wire:click.self="closeDetail">
            <div class="modal-content-custom" wire:click.stop style="max-width: 550px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        Detail Pesanan #{{ str_pad($detailPesanan->id, 5, '0', STR_PAD_LEFT) }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeDetail">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Status Badge --}}
                <div class="mb-3">
                    @if($detailStatus)
                        <span style="font-size: 0.8rem; padding: 5px 12px; border-radius: 50px; font-weight: 600;
                            background: rgba({{ $detailStatus->color() === 'warning' ? '245,158,11' : ($detailStatus->color() === 'primary' ? '99,102,241' : ($detailStatus->color() === 'success' ? '16,185,129' : '239,68,68')) }}, 0.1);
                            color: var(--{{ $detailStatus->color() }}-color);">
                            <i class="{{ $detailStatus->icon() }}"></i>
                            {{ $detailStatus->label() }}
                        </span>
                    @endif
                    <span class="ms-2" style="color: var(--text-muted); font-size: 0.85rem;">
                        {{ $detailPesanan->created_at->format('d M Y, H:i') }}
                    </span>
                </div>

                {{-- Kode Konfirmasi (untuk QR) --}}
                @if($detailPesanan->kode_konfirmasi && $detailPesanan->status !== 'dibatalkan' && $detailPesanan->status !== 'selesai')
                    <div class="text-center mb-3" style="padding: 1rem; background: rgba(99,102,241,0.05); border: 1px dashed var(--primary-color); border-radius: 12px;">
                        <small style="color: var(--text-muted); display: block; margin-bottom: 4px;">Kode Konfirmasi Pengiriman</small>
                        <div style="font-weight: 800; font-size: 1.5rem; color: var(--primary-color); letter-spacing: 5px;">{{ $detailPesanan->kode_konfirmasi }}</div>
                        <small style="color: var(--text-muted); margin-top: 4px; display: block;">Berikan kode ini kepada kurir saat menerima barang</small>
                    </div>
                @endif

                {{-- Items --}}
                <div class="mb-3">
                    @foreach($detailItems as $item)
                        <div class="d-flex align-items-center gap-3 py-2" style="border-bottom: 1px solid var(--border-light);">
                            @if($item->produk->gambar)
                                <img src="{{ Storage::url($item->produk->gambar) }}" alt="{{ $item->produk->nama_produk }}"
                                    style="width: 44px; height: 44px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                            @else
                                <div style="width: 44px; height: 44px; border-radius: 8px; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-birthday-cake" style="color: var(--text-muted);"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="color: var(--text-primary); font-size: 0.9rem;">{{ $item->produk->nama_produk }}</div>
                                <small style="color: var(--text-muted);">{{ $item->jumlah }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                            </div>
                            <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Catatan --}}
                @if($detailPesanan->catatan)
                    <div class="mb-3 p-3" style="background: var(--bg-tertiary); border-radius: 10px;">
                        <small style="color: var(--text-muted); font-weight: 600;">Catatan:</small>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">{{ $detailPesanan->catatan }}</div>
                    </div>
                @endif

                {{-- Total --}}
                <div class="d-flex justify-content-between py-3" style="border-top: 2px solid var(--border-color);">
                    <span style="font-weight: 600; color: var(--text-primary); font-size: 1.05rem;">Total</span>
                    <span style="font-weight: 700; color: var(--primary-color); font-size: 1.05rem;">
                        Rp {{ number_format($detailTotal, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Cancel Action --}}
                @if($detailPesanan->status === 'pending')
                    <div class="d-flex justify-content-end mt-3">
                        <button wire:click="cancelPesanan({{ $detailPesanan->id }})" class="btn"
                            style="background: rgba(239,68,68,0.1); color: var(--danger-color); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; font-weight: 600; padding: 0.6rem 1.25rem; font-size: 0.9rem;"
                            wire:confirm="Apakah Anda yakin ingin membatalkan pesanan ini?">
                            <i class="fas fa-ban me-1"></i> Batalkan Pesanan
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
