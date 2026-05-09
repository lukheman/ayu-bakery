<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Pesanan #{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }}</title>
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

        .qr-section {
            text-align: center;
            margin: 12px 0 8px;
            padding: 10px 0;
        }

        .kode-konfirmasi {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-top: 5px;
            color: #333;
        }

        .qr-label {
            font-size: 8px;
            color: #777;
            margin-top: 4px;
        }

        /* QR Code HTML Table Styles */
        .qr-table {
            border-collapse: collapse;
            border-spacing: 0;
            margin: 0 auto;
            width: auto;
        }

        .qr-table td {
            padding: 0;
            margin: 0;
            width: 4px;
            height: 4px;
            min-width: 4px;
            min-height: 4px;
            line-height: 0;
            font-size: 0;
        }

        .qr-table td.qr-black {
            background-color: #000000;
        }

        .qr-table td.qr-white {
            background-color: #ffffff;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="text-center mb-2">
        <div class="header-title">Ayu Bakery</div>
        <div class="header-subtitle">Nota Pesanan Reseller</div>
        <hr>
    </div>

    <table class="row-space-between mb-2">
        <tr>
            <td>No. Pesanan</td>
            <td class="fw-bold">#{{ str_pad($pesanan->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ $pesanan->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="fw-bold">{{ ucfirst($pesanan->status) }}</td>
        </tr>
    </table>

    <hr>

    {{-- Info Reseller --}}
    <div class="mb-2">
        <div class="fw-bold mb-1">Penerima:</div>
        <table class="row-space-between">
            <tr>
                <td>Nama</td>
                <td>{{ $pesanan->reseller->nama }}</td>
            </tr>
            <tr>
                <td>No. HP</td>
                <td>{{ $pesanan->reseller->no_hp }}</td>
            </tr>
            @if($pesanan->reseller->alamat)
            <tr>
                <td>Alamat</td>
                <td>{{ $pesanan->reseller->alamat }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($pesanan->transaksi && $pesanan->transaksi->kurir)
    <div class="mb-1">
        <table class="row-space-between">
            <tr>
                <td>Kurir</td>
                <td>{{ $pesanan->transaksi->kurir->nama }}</td>
            </tr>
        </table>
    </div>
    @endif

    <hr>

    {{-- Item Pesanan --}}
    <div class="mb-2">
        @foreach($pesanan->itemPesanan as $item)
            <div class="mb-1">
                <div class="item-name">{{ $item->produk->nama_produk }}</div>
                <table class="row-space-between item-details">
                    <tr>
                        <td>{{ $item->jumlah }} {{ $item->unit ?? 'pcs' }} × Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($item->tgl_exp)
                    <tr>
                        <td colspan="2" style="font-size: 8px; color: #888;">Exp: {{ $item->tgl_exp->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        @endforeach
    </div>

    <hr>

    {{-- Total --}}
    <div class="summary-box">
        <table class="row-space-between">
            <tr>
                <td>Jumlah Item</td>
                <td>{{ $pesanan->itemPesanan->sum('jumlah') }} pcs</td>
            </tr>
            <tr>
                <td class="fw-bold" style="font-size: 12px; padding-top: 5px;">TOTAL</td>
                <td class="fw-bold" style="font-size: 12px; padding-top: 5px;">Rp {{ number_format($pesanan->itemPesanan->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <hr>

    {{-- QR Code Section - Rendered as HTML table for DomPDF compatibility --}}
    @if($pesanan->kode_konfirmasi && !empty($qrMatrix))
    <div class="qr-section">
        <div style="margin-bottom: 6px; font-size: 9px; color: #555; font-weight: bold;">
            SCAN QR UNTUK KONFIRMASI PENGIRIMAN
        </div>
        <table class="qr-table">
            @foreach($qrMatrix as $row)
            <tr>
                @foreach($row as $cell)
                <td class="{{ $cell ? 'qr-black' : 'qr-white' }}"></td>
                @endforeach
            </tr>
            @endforeach
        </table>
        <div class="kode-konfirmasi">{{ $pesanan->kode_konfirmasi }}</div>
        <div class="qr-label">Kode ini digunakan kurir untuk konfirmasi penerimaan</div>
    </div>
    @endif

    <hr>

    @if($pesanan->catatan)
    <div class="mb-2" style="font-size: 9px;">
        <div class="fw-bold">Catatan:</div>
        <div style="color: #555;">{{ $pesanan->catatan }}</div>
    </div>
    <hr>
    @endif

    <div class="text-center mt-2" style="font-size: 9px; color: #555;">
        Terima kasih telah memesan di Ayu Bakery!<br>
        Dicetak: {{ now()->format('d/m/Y H:i') }}<br>
        — Ayu Bakery —
    </div>
</body>

</html>
