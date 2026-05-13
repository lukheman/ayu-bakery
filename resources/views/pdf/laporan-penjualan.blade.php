<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan (Kasir + Reseller) - Ayu Bakery</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #111;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 2px;
            color: #111;
        }

        .header h2 {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .header .date {
            font-size: 10px;
            color: #555;
        }

        .period {
            text-align: left;
            font-size: 12px;
            color: #333;
            margin-bottom: 16px;
            margin-left: 20px;
        }

        .order-group {
            margin: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #111;
            padding-bottom: 4px;
            margin-bottom: 12px;
            clear: both;
            margin-left: 20px;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #aaa;
        }

        table.main th {
            background: #eee;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            color: #111;
            padding: 5px 8px;
            border-bottom: 1px solid #aaa;
            border-right: 1px solid #ccc;
        }

        table.main td {
            padding: 5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #ccc;
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
            color: #555;
        }

        .text-primary {
            color: #111;
        }

        .text-success {
            color: #111;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #555;
            border-top: 1px solid #aaa;
            padding-top: 10px;
            margin-right: 20px;
        }

        .page-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>Ayu Bakery</h1>
        <h2>Laporan Penjualan (Rekapitulasi Total)</h2>
    </div>

    {{-- Period --}}
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} —
        {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
    </div>


    {{-- Kasir Section --}}
    <div class="section-title">A. Penjualan Kasir</div>
    @if($dataKasir->isEmpty())
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 10px;">Tidak ada penjualan kasir pada
            periode ini.</div>
    @else
        <div class="order-group">

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
        </div>
    @endif

    {{-- Reseller Section --}}
    <div style="margin-top: 24px;"></div>
    <div class="section-title">B. Penjualan Reseller (Selesai/Dibayar)</div>
    @if($dataReseller->isEmpty())
        <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 10px;">Tidak ada penjualan reseller yang
            selesai pada periode ini.</div>
    @else
        <div class="order-group">

        <table class="main">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Tanggal Transaksi</th>
                    <th>Reseller</th>
                    <th>Metode & Status</th>
                    <th>Total Bayar</th>
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
                        <td>{{ $metodeReseller ? $metodeReseller->label() : $reseller->metode_pembayaran }}
                            ({{ $reseller->status_pembayaran }})</td>
                        <td class="text-right fw-bold">Rp {{ number_format($reseller->total_bayar, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
