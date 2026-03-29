<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk {{ $penjualan->nomor_struk }}</title>
    <style>
        @page {
            margin: 10px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mb-1 {
            margin-bottom: 5px;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .w-100 {
            width: 100%;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-details {
            color: #333;
        }

        .header-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-subtitle {
            font-size: 10px;
            color: #555;
        }

        .row-space-between {
            width: 100%;
        }

        .row-space-between td:last-child {
            text-align: right;
        }

        .summary-box {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="text-center mb-2">
        <div class="header-title">Ayu Bakery</div>
        <div class="header-subtitle">Struk Pembayaran</div>
        <hr>
    </div>

    <table class="row-space-between mb-2">
        <tr>
            <td>No. Struk</td>
            <td class="fw-bold">{{ $penjualan->nomor_struk }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ $penjualan->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>{{ $penjualan->kasir->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pembayaran</td>
            <td>{{ $penjualan->metode_pembayaran->label() ?? strtoupper($penjualan->metode_pembayaran) }}</td>
        </tr>
    </table>

    <hr>

    <div class="mb-2">
        @foreach($penjualan->items as $item)
            <div class="mb-1">
                <div class="item-name">{{ $item->nama_produk }}</div>
                <table class="row-space-between item-details">
                    <tr>
                        <td>{{ $item->jumlah }} × Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        @endforeach
    </div>

    <hr>

    <div class="summary-box">
        <table class="row-space-between">
            <tr>
                <td class="fw-bold">TOTAL</td>
                <td class="fw-bold" style="font-size: 12px;">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td>Rp {{ number_format($penjualan->bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="fw-bold">Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <hr>

    <div class="text-center mt-2" style="font-size: 9px; color: #555;">
        Terima kasih atas kunjungan Anda!<br>
        — Ayu Bakery —
    </div>
</body>

</html>