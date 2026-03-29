<div>
    {{-- Print Styles --}}
    <style>
        @media print {

            .sidebar,
            .topbar,
            .no-print,
            .mobile-toggle {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .content-area {
                padding: 0 !important;
            }

            .modern-card {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                break-inside: avoid;
            }

            .stat-card {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
            }

            .table-modern tbody tr {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
            }

            body {
                background: white !important;
                color: #1e293b !important;
            }

            .print-header {
                display: block !important;
            }
        }

        .print-header {
            display: none;
        }

        .produk-group {
            margin-bottom: 1.5rem;
        }

        .produk-header {
            background: var(--bg-tertiary);
            padding: 1rem 1.25rem;
            border-radius: 12px 12px 0 0;
            border: 1px solid var(--border-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .batch-table {
            width: 100%;
            border-collapse: collapse;
        }

        .batch-table th {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.65rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .batch-table td {
            padding: 0.65rem 1rem;
            font-size: 0.85rem;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-light);
        }

        .batch-table tr:last-child td {
            border-bottom: none;
        }

        .batch-table-wrapper {
            border: 1px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        }
    </style>

    {{-- Print-only header --}}
    <div class="print-header text-center mb-4">
        <h3 style="font-weight: 800;">🎂 Ayu Bakery</h3>
        <h5 style="color: #64748b;">Laporan Persediaan Produk</h5>
        <p style="color: #94a3b8; font-size: 0.85rem;">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <hr>
    </div>

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                <i class="fas fa-file-alt me-2" style="color: var(--primary-color);"></i>Laporan Persediaan
            </h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                Ringkasan stok dan detail persediaan per produk
            </p>
        </div>
        <button wire:click="downloadPdf" class="btn btn-modern btn-primary-modern"
            style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-pdf"></i> Cetak PDF
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: var(--primary-color);">
                    <i class="fas fa-box"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">{{ $totalProduk }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Produk</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: var(--success-color);">
                <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: var(--success-color);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($totalStok) }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Stok (unit kecil)
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: var(--warning-color);">
                <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: var(--warning-color);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">{{ $totalHampirExp }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Batch Hampir Expired</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card" style="--accent-color: var(--danger-color);">
                <div class="stat-icon" style="background: rgba(239,68,68,0.1); color: var(--danger-color);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">{{ $totalExpired }}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Batch Expired</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="modern-card mb-4 no-print" style="padding: 1rem 1.25rem;">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk / kode produk / varian...">
                </div>
            </div>
            <div class="col-md-4">
                <select wire:model.live="filterStatus" class="form-select"
                    style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StatusExp::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 text-end">
                <span style="font-size: 0.8rem; color: var(--text-muted);">
                    {{ $produks->count() }} produk
                </span>
            </div>
        </div>
    </div>

    {{-- Product Groups --}}
    @forelse($produks as $produk)
        @if($produk->persediaan->count() > 0)
            <div class="produk-group">
                {{-- Product Header --}}
                <div class="produk-header">

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">
                                {{ $produk->nama_produk }}
                            </span>
                            @if($produk->varian_rasa)
                                <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 8px;">
                                    — {{ $produk->varian_rasa }}
                                </span>
                            @endif
                            @if($produk->kode_produk)
                                <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 8px;">
                                    <i class="fas fa-barcode me-1"></i>{{ $produk->kode_produk }}
                                </span>
                            @endif
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--primary-color);">
                                <i class="fas fa-cubes me-1"></i>Stok: {{ $produk->stok_text }}
                            </div>
                            @if($produk->unit_besar && $produk->tingkat_konversi > 1)
                                <div style="font-size: 0.7rem; color: var(--text-muted);">
                                    1 {{ $produk->unit_besar }} = {{ $produk->tingkat_konversi }} {{ $produk->unit_kecil ?? 'pcs' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Batch Table --}}
                <div class="batch-table-wrapper">
                    <table class="batch-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Jumlah</th>
                                <th>Tgl Produksi</th>
                                <th>Tgl Expire</th>
                                <th>Sisa Hari</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produk->persediaan as $index => $batch)
                                <tr>
                                    <td style="color: var(--text-muted);">{{ $index + 1 }}</td>
                                    <td>
                                        <span style="font-weight: 600;">{{ $batch->jumlah }}</span>
                                        <span
                                            style="font-size: 0.75rem; color: var(--text-muted);">{{ $produk->unit_kecil ?? 'pcs' }}</span>
                                    </td>
                                    <td>
                                        @if($batch->tgl_produksi)
                                            {{ $batch->tgl_produksi->format('d/m/Y') }}
                                        @else
                                            <span style="color: var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($batch->tgl_exp)
                                            {{ $batch->tgl_exp->format('d/m/Y') }}
                                        @else
                                            <span style="color: var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($batch->sisa_hari !== null)
                                            <span
                                                style="font-weight: 600; {{ $batch->sisa_hari <= 3 ? 'color: var(--danger-color);' : ($batch->sisa_hari <= 14 ? 'color: var(--warning-color);' : 'color: var(--success-color);') }}">
                                                {{ $batch->sisa_hari }} hari
                                            </span>
                                        @else
                                            <span style="color: var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusEnum = $batch->status_exp ? \App\Enums\StatusExp::tryFrom($batch->status_exp) : null;
                                        @endphp
                                        @if($statusEnum)
                                            <span class="badge-modern"
                                                style="background: rgba({{ $statusEnum->color() === 'success' ? '16,185,129' : ($statusEnum->color() === 'warning' ? '245,158,11' : '239,68,68') }}, 0.12);
                                                                                       color: var(--{{ $statusEnum->color() }}-color); font-size: 0.72rem;">
                                                <i class="{{ $statusEnum->icon() }}"></i>
                                                {{ $statusEnum->label() }}
                                            </span>
                                        @else
                                            <span style="color: var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @empty
        <div class="modern-card text-center py-5">
            <i class="fas fa-box-open" style="font-size: 3rem; color: var(--text-muted);"></i>
            <p class="mt-3" style="color: var(--text-muted); font-size: 1rem;">Tidak ada data persediaan ditemukan</p>
        </div>
    @endforelse

    {{-- Empty state when filter results in no batches --}}
    @if($produks->count() > 0 && $produks->every(fn($p) => $p->persediaan->count() === 0))
        <div class="modern-card text-center py-5">
            <i class="fas fa-filter" style="font-size: 3rem; color: var(--text-muted);"></i>
            <p class="mt-3" style="color: var(--text-muted); font-size: 1rem;">Tidak ada batch persediaan yang sesuai filter
            </p>
        </div>
    @endif
</div>
