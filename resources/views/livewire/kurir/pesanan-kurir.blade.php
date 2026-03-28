<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Pesanan Pengiriman</h3>
            <p class="mb-0" style="color: var(--text-secondary);">Daftar pesanan yang ditugaskan kepada Anda</p>
        </div>
        <select class="form-select" wire:model.live="filterStatus" style="width: 180px;">
            <option value="">Semua Status</option>
            @foreach($statusPengirimanOptions as $sp)
                <option value="{{ $sp->value }}">{{ $sp->label() }}</option>
            @endforeach
        </select>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert d-flex align-items-center gap-2 mb-4"
            style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; color: var(--success-color); padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert d-flex align-items-center gap-2 mb-4"
            style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; color: var(--danger-color); padding: 1rem 1.25rem;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @forelse ($transaksis as $transaksi)
        @php
            $pesanan = $transaksi->pesanan;
            $pengirimanEnum = \App\Enums\StatusPengiriman::tryFrom($transaksi->status_pengiriman);
            $statusEnum = \App\Enums\StatusPesanan::tryFrom($pesanan->status);
            $totalBayar = $pesanan->itemPesanan->sum('subtotal');
            $totalItem = $pesanan->itemPesanan->sum('jumlah');
        @endphp
        <div class="modern-card" wire:key="transaksi-{{ $transaksi->id }}">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span
                            style="font-weight: 700; color: var(--primary-color); font-size: 1rem;">#{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="badge"
                            style="background: rgba(var(--bs-{{ $pengirimanEnum->color() }}-rgb), 0.1); color: var(--{{ $pengirimanEnum->color() }}-color); padding: 0.4em 0.7em; border-radius: 6px; font-weight: 600; font-size: 0.75rem;">
                            <i class="{{ $pengirimanEnum->icon() }} me-1"></i> {{ $pengirimanEnum->label() }}
                        </span>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.85rem;">
                        <i class="fas fa-user me-1"></i> {{ $pesanan->reseller->nama }}
                        <span class="ms-2"><i class="fas fa-phone me-1"></i> {{ $pesanan->reseller->no_hp }}</span>
                    </div>
                    @if($pesanan->reseller->alamat)
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $pesanan->reseller->alamat }}
                        </div>
                    @endif
                </div>

                <div class="text-end">
                    <div style="font-weight: 700; color: var(--text-primary); font-size: 1.1rem;">Rp
                        {{ number_format($totalBayar, 0, ',', '.') }}</div>
                    <div style="color: var(--text-secondary); font-size: 0.85rem;">{{ $totalItem }} item</div>
                </div>
            </div>

            <hr style="border-color: var(--border-color); margin: 1rem 0;">

            <div class="d-flex justify-content-between align-items-center">
                <small style="color: var(--text-muted);">{{ $pesanan->created_at->format('d M Y H:i') }}</small>
                <div class="d-flex gap-2">
                    <button wire:click="openDetail({{ $pesanan->id }})" class="btn btn-sm"
                        style="background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.4rem 1rem; font-size: 0.8rem;">
                        <i class="fas fa-eye me-1"></i> Detail
                    </button>
                    @if($transaksi->status_pengiriman === \App\Enums\StatusPengiriman::MENUNGGU->value)
                        <button wire:click="updateStatusPengiriman({{ $transaksi->id }}, 'dikirim')" class="btn btn-sm"
                            style="background: rgba(99,102,241,0.1); color: var(--primary-color); border: none; border-radius: 8px; font-weight: 600; padding: 0.4rem 1rem; font-size: 0.8rem;">
                            <i class="fas fa-shipping-fast me-1"></i> Mulai Kirim
                        </button>
                    @elseif($transaksi->status_pengiriman === \App\Enums\StatusPengiriman::DIKIRIM->value)
                        <a href="{{ route('kurir.scan') }}" class="btn btn-sm"
                            style="background: rgba(16,185,129,0.1); color: var(--success-color); border: none; border-radius: 8px; font-weight: 600; padding: 0.4rem 1rem; font-size: 0.8rem;">
                            <i class="fas fa-qrcode me-1"></i> Scan Konfirmasi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="modern-card text-center py-5">
            <i class="fas fa-truck mb-3" style="font-size: 3rem; color: var(--text-muted);"></i>
            <h5 style="color: var(--text-primary); font-weight: 600;">Belum Ada Pesanan</h5>
            <p style="color: var(--text-secondary);">Pesanan yang ditugaskan kepada Anda akan muncul di sini.</p>
        </div>
    @endforelse

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailTransaksi)
        @php
            $dp = $detailTransaksi->pesanan;
            $dItems = $dp->itemPesanan;
            $dTotal = $dItems->sum('subtotal');
        @endphp
        <div class="modal-backdrop-custom" wire:click.self="closeDetail">
            <div class="modal-content-custom" wire:click.stop style="max-width: 520px;">
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">Detail Pesanan #{{ str_pad($dp->id, 5, '0', STR_PAD_LEFT) }}</h5>
                    <button type="button" class="modal-close-btn" wire:click="closeDetail"><i
                            class="fas fa-times"></i></button>
                </div>

                <div class="mb-3"
                    style="padding: 0.75rem; background: var(--bg-tertiary); border-radius: 10px; font-size: 0.9rem;">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: var(--text-secondary);">Reseller</span>
                        <span class="fw-semibold">{{ $dp->reseller->nama }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: var(--text-secondary);">No. HP</span>
                        <span>{{ $dp->reseller->no_hp }}</span>
                    </div>
                    @if($dp->reseller->alamat)
                        <div class="d-flex justify-content-between">
                            <span style="color: var(--text-secondary);">Alamat</span>
                            <span style="max-width: 250px; text-align: right;">{{ $dp->reseller->alamat }}</span>
                        </div>
                    @endif
                </div>

                @if($dp->kode_konfirmasi)
                    <div class="text-center mb-3"
                        style="padding: 0.75rem; background: rgba(99,102,241,0.05); border: 1px dashed var(--primary-color); border-radius: 10px;">
                        <small style="color: var(--text-muted);">Kode Konfirmasi</small>
                        <div style="font-weight: 800; font-size: 1.3rem; color: var(--primary-color); letter-spacing: 4px;">
                            {{ $dp->kode_konfirmasi }}</div>
                    </div>
                @endif

                <h6 class="mb-2" style="font-weight: 600; color: var(--text-primary);">Item Pesanan</h6>
                @foreach($dItems as $item)
                    <div class="d-flex justify-content-between align-items-center py-2"
                        style="border-bottom: 1px solid var(--border-light);">
                        <div>
                            <div class="fw-semibold" style="color: var(--text-primary); font-size: 0.9rem;">
                                {{ $item->produk->nama_produk }}</div>
                            <small style="color: var(--text-muted);">{{ $item->jumlah }} × Rp
                                {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                        </div>
                        <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-between mt-3 pt-2" style="border-top: 2px solid var(--border-color);">
                    <span style="font-weight: 700; font-size: 1.05rem;">Total</span>
                    <span style="font-weight: 700; color: var(--primary-color); font-size: 1.05rem;">Rp
                        {{ number_format($dTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif
</div>