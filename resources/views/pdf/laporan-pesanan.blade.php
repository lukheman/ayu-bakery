<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pesanan - Ayu Bakery</title>
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

        .period {
            text-align: center;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 16px;
        }

        table.stats {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 4px 0;
        }

        table.stats td {
            text-align: center;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 800;
        }

        .stat-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .order-group {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .order-header {
            background: #f1f5f9;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            border-bottom: 1px solid #6366f1;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-top: none;
        }

        table.items th {
            background: #f8fafc;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        table.items td {
            padding: 5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 700;
        }

        .text-muted {
            color: #94a3b8;
        }

        .text-primary {
            color: #6366f1;
        }

        .badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-diproses {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .badge-selesai {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-dibatalkan {
            background: #fee2e2;
            color: #dc2626;
        }

        .summary {
            margin-top: 16px;
            padding: 12px;
            border: 2px solid #6366f1;
            border-radius: 6px;
        }

        .summary table {
            width: 100%;
            font-size: 11px;
        }

        .summary td {
            padding: 3px 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🎂 Ayu Bakery</h1>
        <h2>Laporan Pesanan</h2>
        <div class="date">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} —
        {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
    </div>

    {{-- Statistics --}}
    <table class="stats">
        <tr>
            <td>
                <div class="stat-value text-primary">{{ number_format($totalPesanan) }}</div>
                <div class="stat-label">Total</div>
            </td>
            <td>
                <div class="stat-value" style="color: #d97706;">{{ number_format($totalPending) }}</div>
                <div class="stat-label">Pending</div>
            </td>
            <td>
                <div class="stat-value" style="color: #4f46e5;">{{ number_format($totalDiproses) }}</div>
                <div class="stat-label">Diproses</div>
            </td>
            <td>
                <div class="stat-value" style="color: #16a34a;">{{ number_format($totalSelesai) }}</div>
                <div class="stat-label">Selesai</div>
            </td>
            <td>
                <div class="stat-value" style="color: #dc2626;">{{ number_format($totalDibatalkan) }}</div>
                <div class="stat-label">Batal</div>
            </td>
            <td>
                <div class="stat-value" style="color: #1e293b; font-size: 12px;">Rp
                    {{ number_format($totalNilai, 0, ',', '.') }}</div>
                <div class="stat-label">Total Nilai</div>
            </td>
        </tr>
    </table>

    {{-- Orders --}}
    @foreach($pesanans as $pesanan)
        @php
            $statusEnum = \App\Enums\StatusPesanan::tryFrom($pesanan->status);
            $nilai = $pesanan->transaksi?->total_bayar ?? $pesanan->itemPesanan->sum('subtotal');
        @endphp
        <div class="order-group">
            <div class="order-header">
                <table style="width: 100%;">
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">#{{ $pesanan->id }}</span>
                            <span class="text-muted"
                                style="margin-left: 8px;">{{ $pesanan->created_at->format('d/m/Y H:i') }}</span>
                            <span style="margin-left: 8px;">{{ $pesanan->reseller?->nama ?? '-' }}</span>
                        </td>
                        <td class="text-right">
                            @if($statusEnum)
                                <span class="badge badge-{{ $statusEnum->value }}">{{ $statusEnum->label() }}</span>
                            @endif
                            <span class="fw-bold" style="margin-left: 8px;">Rp
                                {{ number_format($nilai, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <table class="items">
                <thead>
                    <tr>
                        <th style="text-align: left;">Produk</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Unit</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesanan->itemPesanan as $item)
                        <tr>
                            <td>{{ $item->produk?->nama_produk ?? '-' }}</td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td class="text-center">{{ $item->unit ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-right fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    @if($pesanans->isEmpty())
        <div style="text-align: center; padding: 30px; color: #94a3b8;">
            Tidak ada data pesanan pada periode ini.
        </div>
    @endif

    {{-- Summary --}}
    @if($pesanans->isNotEmpty())
        <div class="summary">
            <table>
                <tr>
                    <td class="fw-bold">Total Pesanan</td>
                    <td class="text-right fw-bold">{{ number_format($totalPesanan) }} pesanan</td>
                </tr>
                <tr>
                    <td class="fw-bold">Total Nilai</td>
                    <td class="text-right fw-bold" style="font-size: 14px; color: #6366f1;">Rp
                        {{ number_format($totalNilai, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Selesai</td>
                    <td class="text-right">{{ $totalSelesai }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Diproses</td>
                    <td class="text-right">{{ $totalDiproses }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Pending</td>
                    <td class="text-right">{{ $totalPending }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Dibatalkan</td>
                    <td class="text-right">{{ $totalDibatalkan }}</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh sistem Ayu Bakery.
    </div>
</body>

</html>