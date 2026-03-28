<div style="height: 100%; display: flex; flex-direction: column; padding: 1.5rem 2rem;">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 style="font-weight: 800; color: var(--pos-text); margin: 0;">Riwayat Penjualan</h4>
            <p style="color: var(--pos-text-muted); margin: 0; font-size: 0.85rem;">Daftar transaksi penjualan langsung
            </p>
        </div>

        <div class="d-flex gap-3">
            {{-- Stats --}}
            <div
                style="background: var(--pos-surface); border: 1px solid var(--pos-border); border-radius: 12px; padding: 0.75rem 1.25rem; text-align: center;">
                <div style="font-size: 0.7rem; color: var(--pos-text-muted); font-weight: 600;">TRANSAKSI HARI INI</div>
                <div style="font-weight: 800; font-size: 1.2rem; color: var(--pos-accent);">{{ $jumlahTransaksi }}</div>
            </div>
            <div
                style="background: var(--pos-surface); border: 1px solid var(--pos-border); border-radius: 12px; padding: 0.75rem 1.25rem; text-align: center;">
                <div style="font-size: 0.7rem; color: var(--pos-text-muted); font-weight: 600;">TOTAL HARI INI</div>
                <div style="font-weight: 800; font-size: 1.2rem; color: var(--pos-success);">Rp
                    {{ number_format($totalHariIni, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="d-flex gap-2 mb-3">
        <div class="input-group" style="width: 280px;">
            <span class="input-group-text"
                style="background: var(--pos-surface-2); border-color: var(--pos-border); color: var(--pos-text-muted);">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                placeholder="Cari nomor struk..." style="font-size: 0.9rem;">
        </div>
        <input type="date" wire:model.live="filterTanggal" class="form-control"
            style="width: 180px; font-size: 0.9rem;">
    </div>

    {{-- Table --}}
    <div
        style="flex: 1; overflow-y: auto; background: var(--pos-surface); border-radius: 12px; border: 1px solid var(--pos-border);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--pos-border);">
                    <th
                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; color: var(--pos-text-muted); font-weight: 600; text-transform: uppercase;">
                        No. Struk</th>
                    <th
                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; color: var(--pos-text-muted); font-weight: 600; text-transform: uppercase;">
                        Waktu</th>
                    <th
                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; color: var(--pos-text-muted); font-weight: 600; text-transform: uppercase;">
                        Items</th>
                    <th
                        style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; color: var(--pos-text-muted); font-weight: 600; text-transform: uppercase;">
                        Metode</th>
                    <th
                        style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; color: var(--pos-text-muted); font-weight: 600; text-transform: uppercase;">
                        Total</th>
                    <th
                        style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; color: var(--pos-text-muted); font-weight: 600; text-transform: uppercase;">
                        Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualans as $penjualan)
                    <tr style="border-bottom: 1px solid rgba(71,85,105,0.3);" wire:key="penjualan-{{ $penjualan->id }}">
                        <td style="padding: 0.65rem 1rem;">
                            <span
                                style="font-weight: 700; color: var(--pos-primary-light); font-family: 'JetBrains Mono', monospace; font-size: 0.85rem;">{{ $penjualan->nomor_struk }}</span>
                        </td>
                        <td style="padding: 0.65rem 1rem; font-size: 0.85rem; color: var(--pos-text-secondary);">
                            {{ $penjualan->created_at->format('H:i') }}
                        </td>
                        <td style="padding: 0.65rem 1rem; font-size: 0.85rem; color: var(--pos-text-secondary);">
                            {{ $penjualan->items->count() }} produk
                        </td>
                        <td style="padding: 0.65rem 1rem;">
                            @php $metode = $penjualan->metode_pembayaran; @endphp
                            <span
                                style="font-size: 0.75rem; padding: 3px 8px; border-radius: 50px; font-weight: 600;
                                        background: {{ $metode->value === 'tunai' ? 'rgba(16,185,129,0.15)' : 'rgba(99,102,241,0.15)' }};
                                        color: {{ $metode->value === 'tunai' ? 'var(--pos-success)' : 'var(--pos-primary-light)' }};">
                                {{ $metode->label() }}
                            </span>
                        </td>
                        <td
                            style="padding: 0.65rem 1rem; text-align: right; font-weight: 700; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; color: var(--pos-text);">
                            Rp {{ number_format($penjualan->total, 0, ',', '.') }}
                        </td>
                        <td style="padding: 0.65rem 1rem; text-align: center;">
                            <button wire:click="openDetail({{ $penjualan->id }})"
                                style="background: var(--pos-surface-2); border: 1px solid var(--pos-border); border-radius: 6px; color: var(--pos-text-secondary); padding: 4px 10px; font-size: 0.8rem; cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 3rem 1rem; text-align: center;">
                            <i class="fas fa-receipt" style="font-size: 2rem; color: var(--pos-text-muted);"></i>
                            <p style="color: var(--pos-text-muted); margin: 0.5rem 0 0;">Belum ada transaksi</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($penjualans->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $penjualans->links() }}
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($showDetailModal && $detailPenjualan)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1050; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.2s;"
            wire:click.self="closeDetail">
            <div style="background: white; border-radius: 16px; width: 360px; overflow: hidden;" wire:click.stop>
                <div style="color: #1e293b; padding: 1.5rem;">
                    <div class="text-center mb-3">
                        <div style="font-weight: 800; font-size: 1.1rem;">🎂 Ayu Bakery</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">Struk Pembayaran</div>
                        <hr style="border-style: dashed; border-color: #e2e8f0; margin: 0.75rem 0;">
                    </div>

                    <div style="font-size: 0.8rem; margin-bottom: 0.75rem;">
                        <div class="d-flex justify-content-between"><span style="color: #64748b;">No. Struk</span><span
                                style="font-weight: 600;">{{ $detailPenjualan->nomor_struk }}</span></div>
                        <div class="d-flex justify-content-between"><span
                                style="color: #64748b;">Tanggal</span><span>{{ $detailPenjualan->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between"><span
                                style="color: #64748b;">Kasir</span><span>{{ $detailPenjualan->kasir->nama }}</span></div>
                    </div>

                    <hr style="border-style: dashed; border-color: #e2e8f0; margin: 0.5rem 0;">

                    @foreach($detailPenjualan->items as $item)
                        <div style="font-size: 0.8rem; margin-bottom: 6px;">
                            <div style="font-weight: 600; color: #1e293b;">{{ $item->nama_produk }}</div>
                            <div class="d-flex justify-content-between" style="color: #64748b;">
                                <span>{{ $item->jumlah }} x Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                <span style="color: #1e293b; font-weight: 600;">Rp
                                    {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach

                    <hr style="border-style: dashed; border-color: #e2e8f0; margin: 0.5rem 0;">

                    <div style="font-size: 0.85rem;">
                        <div class="d-flex justify-content-between mb-1"><span style="font-weight: 700;">TOTAL</span><span
                                style="font-weight: 800;">Rp
                                {{ number_format($detailPenjualan->total, 0, ',', '.') }}</span></div>
                        <div class="d-flex justify-content-between mb-1"><span style="color: #64748b;">Bayar</span><span>Rp
                                {{ number_format($detailPenjualan->bayar, 0, ',', '.') }}</span></div>
                        <div class="d-flex justify-content-between"><span style="color: #64748b;">Kembalian</span><span
                                style="font-weight: 700; color: #10b981;">Rp
                                {{ number_format($detailPenjualan->kembalian, 0, ',', '.') }}</span></div>
                    </div>
                </div>

                <div style="padding: 0.75rem 1.5rem 1.25rem; display: flex; gap: 0.5rem;">
                    <button onclick="window.print()"
                        style="flex: 1; padding: 0.5rem; background: #6366f1; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                    <button wire:click="closeDetail"
                        style="flex: 1; padding: 0.5rem; background: #f1f5f9; color: #1e293b; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>