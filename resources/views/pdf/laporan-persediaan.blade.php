<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Persediaan - Ayu Bakery</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #6366f1;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .header h2 {
            font-size: 13px;
            font-weight: 600;
            color: #6366f1;
            margin-bottom: 4px;
        }

        .header .date {
            font-size: 10px;
            color: #94a3b8;
        }

        /* Stats row */
        .stats-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .stat-box .value {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
        }

        .stat-box .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Product group */
        .produk-group {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .produk-header {
            background: #f1f5f9;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-bottom: 2px solid #6366f1;
        }

        .produk-name {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
        }

        .produk-meta {
            font-size: 9px;
            color: #64748b;
        }

        .produk-stok {
            font-size: 11px;
            font-weight: 700;
            color: #6366f1;
        }

        /* Batch table */
        table.batch-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-top: none;
        }

        table.batch-table th {
            background: #f8fafc;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            padding: 6px 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        table.batch-table td {
            padding: 6px 10px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        table.batch-table tr:last-child td {
            border-bottom: none;
        }

        /* Status badges */
        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        .badge-aman {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-hampir_exp {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-expired {
            background: #fee2e2;
            color: #dc2626;
        }

        .text-success {
            color: #16a34a;
        }

        .text-warning {
            color: #d97706;
        }

        .text-danger {
            color: #dc2626;
        }

        .text-muted {
            color: #94a3b8;
        }

        .fw-bold {
            font-weight: 700;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>🎂 Ayu Bakery</h1>
        <h2>Laporan Persediaan Produk</h2>
        <div class="date">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    {{-- Statistics --}}
    <table style="width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 6px 0;">
        <tr>
            <td style="text-align: center; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; width: 25%;">
                <div style="font-size: 18px; font-weight: 800; color: #6366f1;">{{ $totalProduk }}</div>
                <div style="font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Produk
                </div>
            </td>
            <td style="text-align: center; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; width: 25%;">
                <div style="font-size: 18px; font-weight: 800; color: #10b981;">{{ number_format($totalStok) }}</div>
                <div style="font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Stok
                </div>
            </td>
            <td style="text-align: center; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; width: 25%;">
                <div style="font-size: 18px; font-weight: 800; color: #f59e0b;">{{ $totalHampirExp }}</div>
                <div style="font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 600;">Hampir Expired
                </div>
            </td>
            <td style="text-align: center; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; width: 25%;">
                <div style="font-size: 18px; font-weight: 800; color: #ef4444;">{{ $totalExpired }}</div>
                <div style="font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 600;">Batch Expired
                </div>
            </td>
        </tr>
    </table>

    {{-- Product Groups --}}
    @forelse($produks as $produk)
        @if($produk->persediaan->count() > 0)
            <div class="produk-group">
                <div class="produk-header">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <span class="produk-name">{{ $produk->nama_produk }}</span>
                                @if($produk->varian_rasa)
                                    <span class="produk-meta"> — {{ $produk->varian_rasa }}</span>
                                @endif
                                @if($produk->kode_produk)
                                    <span class="produk-meta" style="margin-left: 6px;">[{{ $produk->kode_produk }}]</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <span class="produk-stok">Stok: {{ $produk->stok_text }}</span>
                                @if($produk->unit_besar && $produk->tingkat_konversi > 1)
                                    <br><span class="produk-meta">1 {{ $produk->unit_besar }} = {{ $produk->tingkat_konversi }}
                                        {{ $produk->unit_kecil ?? 'pcs' }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <table class="batch-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
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
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ $batch->jumlah }}</span>
                                    <span class="text-muted">{{ $produk->unit_kecil ?? 'pcs' }}</span>
                                </td>
                                <td>
                                    @if($batch->tgl_produksi)
                                        {{ $batch->tgl_produksi->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($batch->tgl_exp)
                                        {{ $batch->tgl_exp->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($batch->sisa_hari !== null)
                                        <span
                                            class="{{ $batch->sisa_hari <= 3 ? 'text-danger fw-bold' : ($batch->sisa_hari <= 14 ? 'text-warning fw-bold' : 'text-success') }}">
                                            {{ $batch->sisa_hari }} hari
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusEnum = $batch->status_exp ? \App\Enums\StatusExp::tryFrom($batch->status_exp) : null;
                                    @endphp
                                    @if($statusEnum)
                                        <span class="badge badge-{{ $statusEnum->value }}">
                                            {{ $statusEnum->label() }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @empty
        <div class="empty-state">
            <p>Tidak ada data persediaan.</p>
        </div>
    @endforelse

    {{-- Footer --}}
    <div class="footer">
        Laporan ini digenerate secara otomatis oleh sistem Ayu Bakery.
    </div>
</body>

</html>