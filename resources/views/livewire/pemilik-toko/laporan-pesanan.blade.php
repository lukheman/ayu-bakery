<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                <i class="fas fa-clipboard-list me-2" style="color: var(--primary-color);"></i>Laporan Pesanan
            </h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                Ringkasan pesanan reseller
            </p>
        </div>
        <button wire:click="downloadPdf" class="btn btn-modern btn-primary-modern"
            style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-pdf"></i> Cetak PDF
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card" style="--accent-color: var(--primary-color); padding: 1.25rem;">
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($totalPesanan) }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Total Pesanan</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card" style="--accent-color: var(--warning-color); padding: 1.25rem;">
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--warning-color);">
                    {{ number_format($totalPending) }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Pending</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card" style="--accent-color: var(--primary-color); padding: 1.25rem;">
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color);">
                    {{ number_format($totalDiproses) }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Diproses</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card" style="--accent-color: var(--success-color); padding: 1.25rem;">
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--success-color);">
                    {{ number_format($totalSelesai) }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Selesai</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card" style="--accent-color: var(--danger-color); padding: 1.25rem;">
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--danger-color);">
                    {{ number_format($totalDibatalkan) }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Dibatalkan</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card" style="--accent-color: var(--success-color); padding: 1.25rem;">
                <div style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">Rp
                    {{ number_format($totalNilai, 0, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Total Nilai</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="modern-card mb-4" style="padding: 1rem 1.25rem;">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Dari
                    Tanggal</label>
                <input type="date" class="form-control" wire:model.live="tanggalDari">
            </div>
            <div class="col-md-3">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Sampai
                    Tanggal</label>
                <input type="date" class="form-control" wire:model.live="tanggalSampai">
            </div>
            <div class="col-md-2">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Status</label>
                <select wire:model.live="filterStatus" class="form-select"
                    style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                    <option value="">Semua</option>
                    @foreach(\App\Enums\StatusPesanan::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="ID pesanan / nama reseller...">
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="modern-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table mb-0" style="border-collapse: collapse;">
                <thead>
                    <tr>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            ID</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            Tanggal</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            Reseller</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            Status</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            Item</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right;">
                            Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $pesanan)
                        @php
                            $statusEnum = \App\Enums\StatusPesanan::tryFrom($pesanan->status);
                            $nilai = $pesanan->transaksi?->total_bayar ?? $pesanan->itemPesanan->sum('subtotal');
                            $colorMap = ['warning' => '245,158,11', 'primary' => '99,102,241', 'success' => '16,185,129', 'danger' => '239,68,68'];
                        @endphp
                        <tr wire:key="pesanan-{{ $pesanan->id }}"
                            style="border-bottom: 1px solid var(--border-light); cursor: pointer;"
                            onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'table-row' : 'none'">
                            <td style="padding: 0.85rem 1rem; vertical-align: middle;">
                                <span
                                    style="font-weight: 600; font-size: 0.85rem; color: var(--primary-color);">#{{ $pesanan->id }}</span>
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.85rem;">
                                {{ $pesanan->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.85rem;">
                                {{ $pesanan->reseller?->nama ?? '-' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle;">
                                @if($statusEnum)
                                    <span class="badge-modern"
                                        style="background: rgba({{ $colorMap[$statusEnum->color()] ?? '99,102,241' }}, 0.12); color: var(--{{ $statusEnum->color() }}-color); font-size: 0.72rem;">
                                        <i class="{{ $statusEnum->icon() }}"></i>
                                        {{ $statusEnum->label() }}
                                    </span>
                                @endif
                            </td>
                            <td
                                style="padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.8rem; color: var(--text-muted);">
                                {{ $pesanan->itemPesanan->count() }} produk
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle; text-align: right;">
                                <span style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">Rp
                                    {{ number_format($nilai, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        {{-- Expandable detail --}}
                        <tr style="display: none; background: var(--bg-tertiary);">
                            <td colspan="6" style="padding: 0.75rem 1.5rem 1rem;">
                                @if($pesanan->catatan)
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px;">
                                        <i class="fas fa-sticky-note me-1"></i> {{ $pesanan->catatan }}
                                    </div>
                                @endif
                                <div
                                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 6px;">
                                    Detail Item
                                </div>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th
                                                style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: left; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">
                                                Produk</th>
                                            <th
                                                style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: center; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">
                                                Qty</th>
                                            <th
                                                style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: center; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">
                                                Unit</th>
                                            <th
                                                style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">
                                                Harga</th>
                                            <th
                                                style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">
                                                Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pesanan->itemPesanan as $item)
                                            <tr>
                                                <td style="font-size: 0.8rem; padding: 4px 8px; color: var(--text-primary);">
                                                    {{ $item->produk?->nama_produk ?? '-' }}</td>
                                                <td style="font-size: 0.8rem; padding: 4px 8px; text-align: center;">
                                                    {{ $item->jumlah }}</td>
                                                <td
                                                    style="font-size: 0.8rem; padding: 4px 8px; text-align: center; color: var(--text-muted);">
                                                    {{ $item->unit ?? '-' }}</td>
                                                <td
                                                    style="font-size: 0.8rem; padding: 4px 8px; text-align: right; color: var(--text-muted);">
                                                    Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                                <td
                                                    style="font-size: 0.8rem; padding: 4px 8px; text-align: right; font-weight: 600; color: var(--text-primary);">
                                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($pesanan->transaksi)
                                    <div
                                        style="margin-top: 6px; padding-top: 6px; border-top: 1px solid var(--border-color); font-size: 0.8rem; color: var(--text-muted);">
                                        Pembayaran: {{ $pesanan->transaksi->status_pembayaran }}
                                        &nbsp;|&nbsp; Pengiriman: {{ $pesanan->transaksi->status_pengiriman ?? '-' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem 1rem;">
                                <i class="fas fa-clipboard-list" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                                <p class="mt-2" style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">Tidak
                                    ada data pesanan ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($pesanans->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $pesanans->links() }}
        </div>
    @endif
</div>