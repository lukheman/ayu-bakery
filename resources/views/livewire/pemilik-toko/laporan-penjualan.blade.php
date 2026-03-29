<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                <i class="fas fa-chart-bar me-2" style="color: var(--primary-color);"></i>Laporan Penjualan
            </h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                Rekapitulasi penjualan kasir dan reseller
            </p>
        </div>
        <button wire:click="downloadPdf" class="btn btn-modern btn-primary-modern"
            style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-pdf"></i> Cetak Semua PDF
        </button>
    </div>

    {{-- Unified Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: var(--success-color);">
                <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: var(--success-color);">
                    <i class="fas fa-wallet"></i>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">Rp
                    {{ number_format($stats['totalPendapatan'], 0, ',', '.') }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Pendapatan Semua</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: var(--primary-color);">
                    <i class="fas fa-receipt"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($stats['totalTransaksi']) }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Transaksi
                    (Struk+Pesanan)</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: #f59e0b;">
                <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                    <i class="fas fa-store"></i>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">Rp
                    {{ number_format($stats['pendapatanKasir'], 0, ',', '.') }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Pendapatan Kasir</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: #0ea5e9;">
                <div class="stat-icon" style="background: rgba(14,165,233,0.1); color: #0ea5e9;">
                    <i class="fas fa-users"></i>
                </div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">Rp
                    {{ number_format($stats['pendapatanReseller'], 0, ',', '.') }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Pendapatan Reseller</div>
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
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Metode</label>
                <select wire:model.live="filterMetode" class="form-select"
                    style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                    <option value="">Semua</option>
                    @foreach(\App\Enums\MetodePembayaran::cases() as $metode)
                        <option value="{{ $metode->value }}">{{ $metode->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Cari</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="{{ $activeTab === 'kasir' ? 'No. struk / nama kasir...' : 'ID pesanan / nama reseller...' }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4 px-1" style="border-bottom: 2px solid var(--border-color); border-radius: 0;">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'kasir' ? 'active fw-bold' : '' }}"
                style="cursor:pointer; font-size: 0.95rem; border: none; border-bottom: 3px solid {{ $activeTab === 'kasir' ? 'var(--primary-color)' : 'transparent' }}; background: transparent; color: {{ $activeTab === 'kasir' ? 'var(--primary-color)' : 'var(--text-muted)' }}; padding: 0.75rem 1.5rem;"
                wire:click="setTab('kasir')">
                <i class="fas fa-store me-2"></i> Penjualan Kasir
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'reseller' ? 'active fw-bold' : '' }}"
                style="cursor:pointer; font-size: 0.95rem; border: none; border-bottom: 3px solid {{ $activeTab === 'reseller' ? 'var(--primary-color)' : 'transparent' }}; background: transparent; color: {{ $activeTab === 'reseller' ? 'var(--primary-color)' : 'var(--text-muted)' }}; padding: 0.75rem 1.5rem;"
                wire:click="setTab('reseller')">
                <i class="fas fa-users me-2"></i> Penjualan Reseller
            </a>
        </li>
    </ul>

    {{-- Transactions Table --}}
    <div class="modern-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table table-modern mb-0" style="border-spacing: 0; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            {{ $activeTab === 'kasir' ? 'No. Struk' : 'ID Pesanan' }}
                        </th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            Tanggal</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            {{ $activeTab === 'kasir' ? 'Kasir' : 'Pelanggan/Reseller' }}
                        </th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            Metode</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color);">
                            Item</th>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right;">
                            Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $penjualan)
                        @php
                            $isKasir = $activeTab === 'kasir';
                            $idAtauStruk = $isKasir ? $penjualan->nomor_struk : '#' . $penjualan->id_pesanan;
                            $tanggal = $isKasir ? $penjualan->tanggal : $penjualan->tanggal;
                            $aktor = $isKasir ? ($penjualan->kasir?->nama ?? '-') : ($penjualan->pesanan?->reseller?->nama ?? '-');
                            
                            $metodeRaw = $penjualan->metode_pembayaran;
                            $metode = $metodeRaw instanceof \App\Enums\MetodePembayaran ? $metodeRaw : \App\Enums\MetodePembayaran::tryFrom($metodeRaw);
                            
                            $metodeLabel = $metode ? $metode->label() : ($isKasir ? $metodeRaw->label() : $metodeRaw);
                            $metodeColor = $metode ? $metode->color() : ($isKasir ? $metodeRaw->color() : 'primary');
                            $metodeIcon = $metode ? $metode->icon() : ($isKasir ? $metodeRaw->icon() : 'fas fa-money-bill');
                            $itemCount = $isKasir ? $penjualan->items->where('jumlah', '>', 0)->count() : $penjualan->pesanan?->itemPesanan->count();
                            $items = $isKasir ? $penjualan->items->where('jumlah', '>', 0) : $penjualan->pesanan?->itemPesanan;
                            $total = $isKasir ? $penjualan->total : $penjualan->total_bayar;
                        @endphp

                        <tr wire:key="row-{{ $penjualan->id }}"
                            style="border-bottom: 1px solid var(--border-light); cursor: pointer;"
                            onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'table-row' : 'none'">
                            <td style="padding: 0.85rem 1rem; vertical-align: middle;">
                                <span
                                    style="font-weight: 600; font-size: 0.85rem; color: var(--primary-color);">{{ $idAtauStruk }}</span>
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.85rem;">
                                {{ $tanggal->format('d/m/Y') }}
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.85rem;">
                                {{ $aktor }}
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle;">
                                <span class="badge-modern"
                                    style="background: rgba({{ $metodeColor === 'success' ? '16,185,129' : '99,102,241' }}, 0.12); color: var(--{{ $metodeColor }}-color); font-size: 0.72rem;">
                                    <i class="{{ $metodeIcon }}"></i>
                                    {{ $metodeLabel }}
                                </span>
                            </td>
                            <td
                                style="padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.8rem; color: var(--text-muted);">
                                {{ $itemCount }} produk
                            </td>
                            <td style="padding: 0.85rem 1rem; vertical-align: middle; text-align: right;">
                                <span style="font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">Rp
                                    {{ number_format($total, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        {{-- Expandable detail row --}}
                        <tr style="display: none; background: var(--bg-tertiary);">
                            <td colspan="6" style="padding: 0.75rem 1.5rem 1rem;">
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
                                                style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">
                                                Harga</th>
                                            <th
                                                style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">
                                                Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($items)
                                            @foreach($items as $item)
                                                <tr>
                                                    <td style="font-size: 0.8rem; padding: 4px 8px; color: var(--text-primary);">
                                                        {{ $isKasir ? $item->nama_produk : ($item->produk?->nama_produk ?? '-') }}
                                                    </td>
                                                    <td
                                                        style="font-size: 0.8rem; padding: 4px 8px; text-align: center; color: var(--text-primary);">
                                                        {{ $item->jumlah }} {{ !$isKasir ? $item->unit : '' }}</td>
                                                    <td
                                                        style="font-size: 0.8rem; padding: 4px 8px; text-align: right; color: var(--text-muted);">
                                                        Rp
                                                        {{ number_format($isKasir ? $item->harga : $item->harga_satuan, 0, ',', '.') }}
                                                    </td>
                                                    <td
                                                        style="font-size: 0.8rem; padding: 4px 8px; text-align: right; font-weight: 600; color: var(--text-primary);">
                                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                @if($isKasir)
                                    <div
                                        style="margin-top: 6px; padding-top: 6px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; font-size: 0.8rem;">
                                        <span style="color: var(--text-muted);">Bayar: Rp
                                            {{ number_format($penjualan->bayar, 0, ',', '.') }} &nbsp;|&nbsp; Kembalian: Rp
                                            {{ number_format($penjualan->kembalian, 0, ',', '.') }}</span>
                                    </div>
                                @elseif($penjualan->bukti_pembayaran)
                                    <div
                                        style="margin-top: 6px; padding-top: 6px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; font-size: 0.8rem;">
                                        <a href="{{ Storage::url($penjualan->bukti_pembayaran) }}" target="_blank"
                                            style="color: var(--primary-color); text-decoration: none;"><i
                                                class="fas fa-image me-1"></i> Lihat Bukti Transfer</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem 1rem;">
                                <i class="fas fa-receipt" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                                <p class="mt-2" style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">Tidak
                                    ada data penjualan {{ $activeTab }} ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($penjualans->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $penjualans->links() }}
        </div>
    @endif
</div>