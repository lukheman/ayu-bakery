<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan (Kasir + Reseller) - Ayu Bakery</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }

        .header { text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #6366f1; }
        .header h1 { font-size: 18px; font-weight: 800; margin-bottom: 2px; }
        .header h2 { font-size: 13px; font-weight: 600; color: #6366f1; margin-bottom: 4px; }
        .header .date { font-size: 10px; color: #94a3b8; }

        .period { text-align: center; font-size: 11px; color: #64748b; margin-bottom: 16px; font-weight: bold; }

        table.stats { width: 100%; margin-bottom: 24px; border-collapse: separate; border-spacing: 6px 0; }
        table.stats td { text-align: center; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; width: 25%; }
        .stat-value { font-size: 14px; font-weight: 800; }
        .stat-label { font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-top: 4px; }

        .section-title { font-size: 12px; font-weight: 800; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 12px; clear: both; }

        table.main { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.main th { background: #f8fafc; font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        table.main td { padding: 7px 10px; font-size: 10px; border-bottom: 1px solid #f1f5f9; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .text-muted { color: #94a3b8; }
        .text-primary { color: #6366f1; }
        .text-success { color: #16a34a; }

        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }

        .page-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>🎂 Ayu Bakery</h1>
        <h2>Laporan Penjualan (Rekapitulasi Total)</h2>
        <div class="date">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    {{-- Period --}}
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
    </div>

    {{-- Statistics --}}
    <table class="stats">
        <tr>
            <td>
                <div class="stat-value text-success">Rp {{ number_format($stats['totalPendapatan'], 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan (Kasir+Reseller)</div>
            </td>
            <td>
                <div class="stat-value text-primary">{{ number_format($stats['totalTransaksi']) }}</div>
                <div class="stat-label">Total Transaksi</div>
            </td>
            <td>
                <div class="stat-value" style="color: #f59e0b;">Rp {{ number_format($stats['pendapatanKasir'], 0, ',', '.') }}</div>
                <div class="stat-label">Pendapatan Kasir</div>
            </td>
            <td>
                <div class="stat-value" style="color: #0ea5e9;">Rp {{ number_format($stats['pendapatanReseller'], 0, ',', '.') }}</div>
                <div class="stat-label">Pendapatan Reseller</div>
            </td>
        </tr>
    </table>

    {{-- Kasir Section --}}
    <div class="section-title">A. Penjualan Kasir</div>
    @if($dataKasir->isEmpty())
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 10px;">Tidak ada penjualan kasir pada periode ini.</div>
    @else
        <table class="main">
            <thead>
                <tr>
                    <th>No. Struk</th>
                    <th>Tanggal</th>
                    <th>Kasir</th>
                    <th>Metode</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataKasir as $kasir)
                    @php
                        $metodeKasir = $kasir->metode_pembayaran instanceof \App\Enums\MetodePembayaran ? $kasir->metode_pembayaran : \App\Enums\MetodePembayaran::tryFrom($kasir->metode_pembayaran);
                    @endphp
                    <tr>
                        <td class="fw-bold">{{ $kasir->nomor_struk }}</td>
                        <td>{{ $kasir->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $kasir->kasir?->nama ?? '-' }}</td>
                        <td>{{ $metodeKasir ? $metodeKasir->label() : $kasir->metode_pembayaran }}</td>
                        <td class="text-right fw-bold">Rp {{ number_format($kasir->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Reseller Section --}}
    <div style="margin-top: 24px;"></div>
    <div class="section-title">B. Penjualan Reseller (Selesai/Dibayar)</div>
    @if($dataReseller->isEmpty())
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 10px;">Tidak ada penjualan reseller yang selesai pada periode ini.</div>
    @else
        <table class="main">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Tanggal Transaksi</th>
                    <th>Reseller</th>
                    <th>Metode & Status</th>
                    <th class="text-right">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataReseller as $reseller)
                    @php
                        $metodeReseller = $reseller->metode_pembayaran instanceof \App\Enums\MetodePembayaran ? $reseller->metode_pembayaran : \App\Enums\MetodePembayaran::tryFrom($reseller->metode_pembayaran);
                    @endphp
                    <tr>
                        <td class="fw-bold">#{{ $reseller->id_pesanan }}</td>
                        <td>{{ $reseller->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $reseller->pesanan?->reseller?->nama ?? '-' }}</td>
                        <td>{{ $metodeReseller ? $metodeReseller->label() : $reseller->metode_pembayaran }} ({{ $reseller->status_pembayaran }})</td>
                        <td class="text-right fw-bold">Rp {{ number_format($reseller->total_bayar, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Laporan ini digenerate secara otomatis oleh sistem Ayu Bakery. Rekapitulasi mengkompilasi transaksi kasir (offline) dan reseller (online/pesanan) yang valid.
    </div>
</body>
</html>