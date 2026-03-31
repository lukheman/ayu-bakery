<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                <i class="fas fa-chart-line me-2" style="color: var(--primary-color);"></i>Prediksi Penjualan
            </h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                Peramalan penjualan menggunakan metode Moving Average
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button wire:click="simpanPrediksi" class="btn btn-modern"
                style="background: var(--success-color); color: white; border: none; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-save"></i> Simpan Prediksi
            </button>
            <button wire:click="downloadPdf" class="btn btn-modern btn-primary-modern"
                style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </button>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-modern mb-4"
            style="background: rgba(16,185,129,0.1); color: var(--success-color); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px; padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: var(--primary-color);">
                    <i class="fas fa-box"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ $totalProduk }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Produk Dianalisis
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card" style="--accent-color: #f59e0b;">
                <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($avgPrediksi, 1) }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Rata-rata Prediksi (MA)
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card" style="--accent-color: var(--success-color);">
                <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: var(--success-color);">
                    <i class="fas fa-industry"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($totalRekomendasi) }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Rekomendasi Produksi
                </div>
            </div>
        </div>
    </div>

    {{-- Info Card: Rumus --}}
    <div class="modern-card mb-4"
        style="padding: 1rem 1.25rem; background: rgba(99,102,241,0.05); border: 1px solid rgba(99,102,241,0.15);">
        <div style="display: flex; align-items: start; gap: 12px;">
            <div
                style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99,102,241,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 1rem;"></i>
            </div>
            <div>
                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); margin-bottom: 4px;">
                    Rumus Moving Average</div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                    <strong>MA = (X₁ + X₂ + ... + Xₙ) / N</strong> — di mana <strong>X</strong> = data penjualan per
                    minggu, <strong>N</strong> = jumlah periode. Hasil MA digunakan sebagai prediksi dan rekomendasi
                    produksi untuk periode berikutnya.
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="modern-card mb-4" style="padding: 1rem 1.25rem;">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Jumlah
                    Periode (N Minggu)</label>
                <input type="number" class="form-control" wire:model.live.debounce.500ms="jumlahPeriode" min="2"
                    max="12" placeholder="4">
            </div>
            <div class="col-md-5">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Cari
                    Produk</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="Nama produk, kode, atau varian...">
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div
                    style="font-size: 0.78rem; color: var(--text-muted); background: var(--bg-tertiary); padding: 0.65rem 1rem; border-radius: 8px; width: 100%;">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Data {{ $jumlahPeriode }} minggu terakhir
                </div>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="modern-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table table-modern mb-0" style="border-spacing: 0; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); white-space: nowrap;">
                            Produk
                        </th>
                        @foreach ($weeks as $week)
                            <th
                                style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap;">
                                {{ $week['label'] }}
                                <div style="font-size: 0.6rem; color: var(--text-muted); font-weight: 400;">
                                    {{ $week['range'] }}
                                </div>
                            </th>
                        @endforeach
                        <th
                            style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap; background: rgba(99,102,241,0.05);">
                            MA
                        </th>
                        <th
                            style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap; background: rgba(16,185,129,0.05);">
                            Rekomendasi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr wire:key="row-{{ $item['produk']->id }}"
                            style="border-bottom: 1px solid var(--border-light); cursor: pointer;"
                            onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'table-row' : 'none'">
                            <td style="padding: 0.85rem 1rem; vertical-align: middle;">
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">
                                    {{ $item['produk']->nama_produk }}
                                </div>
                                @if ($item['produk']->varian_rasa)
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">
                                        {{ $item['produk']->varian_rasa }}
                                    </div>
                                @endif
                            </td>
                            @foreach ($item['weekly'] as $qty)
                                <td
                                    style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; font-size: 0.85rem; color: var(--text-primary);">
                                    {{ $qty }}
                                </td>
                            @endforeach
                            <td
                                style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; background: rgba(99,102,241,0.05);">
                                <span
                                    style="font-weight: 700; font-size: 0.9rem; color: var(--primary-color);">{{ number_format($item['ma'], 1) }}</span>
                            </td>
                            <td
                                style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; background: rgba(16,185,129,0.05);">
                                <span class="badge-modern"
                                    style="background: rgba(16,185,129,0.12); color: var(--success-color); font-size: 0.8rem; font-weight: 700;">
                                    {{ $item['rekomendasi'] }} {{ $item['produk']->unit_kecil ?? 'pcs' }}
                                </span>
                            </td>
                        </tr>
                        {{-- Expandable detail row --}}
                        <tr style="display: none; background: var(--bg-tertiary);">
                            <td colspan="{{ count($weeks) + 3 }}" style="padding: 0.75rem 1.5rem 1rem;">
                                <div
                                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 6px;">
                                    <i class="fas fa-calculator me-1"></i> Detail Perhitungan Moving Average
                                </div>
                                <div
                                    style="background: var(--bg-secondary); border-radius: 8px; padding: 1rem; border: 1px solid var(--border-color);">
                                    <div style="font-size: 0.82rem; color: var(--text-primary); margin-bottom: 8px;">
                                        <strong>{{ $item['produk']->nama_produk }}</strong> — data penjualan
                                        {{ $jumlahPeriode }} minggu terakhir:
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 6px;">
                                        @foreach ($item['weekly'] as $i => $qty)
                                            Minggu {{ $i + 1 }} = <strong>{{ $qty }}</strong>
                                            {{ $item['produk']->unit_kecil ?? 'pcs' }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </div>
                                    <div
                                        style="font-size: 0.85rem; color: var(--primary-color); font-weight: 600; padding: 8px 12px; background: rgba(99,102,241,0.06); border-radius: 6px; display: inline-block;">
                                        MA = ({{ implode(' + ', $item['weekly']) }}) / {{ $jumlahPeriode }} =
                                        <strong>{{ number_format($item['ma'], 2) }}</strong>
                                    </div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 6px;">
                                        <i class="fas fa-arrow-right me-1"></i> Rekomendasi produksi:
                                        <strong style="color: var(--success-color);">{{ $item['rekomendasi'] }}</strong>
                                        {{ $item['produk']->unit_kecil ?? 'pcs' }} (pembulatan ke atas dari MA)
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($weeks) + 3 }}" style="text-align: center; padding: 3rem 1rem;">
                                <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                                <p class="mt-2" style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">Tidak
                                    ada
                                    data produk ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>