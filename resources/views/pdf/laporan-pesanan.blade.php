<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pesanan - Ayu Bakery</title>
    <style>
        @page { size: A4; margin: 2cm; }
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

        .order-header {
            background: #eee;
            padding: 6px 10px;
            border: 1px solid #aaa;
            border-bottom: 2px solid #111;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #aaa;
        }

        table.items th {
            background: #eee;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            color: #111;
            padding: 5px 8px;
            border-bottom: 1px solid #aaa;
            border-right: 1px solid #ccc;
        }

        table.items td {
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

        .badge {
            padding: 2px 6px;
            font-size: 9px;
            font-weight: 700;
            border: 1px solid #555;
            color: #111;
        }

        .badge-pending { border-color: #888; }
        .badge-diproses { border-color: #888; }
        .badge-selesai { border-color: #333; font-weight: 700; }
        .badge-dibatalkan { border-color: #888; color: #555; }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #555;
            border-top: 1px solid #aaa;
            padding-top: 10px;
            margin-right: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Ayu Bakery</h1>
        <h2>Laporan Pesanan</h2>
    </div>

    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} —
        {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
    </div>


    {{-- Orders --}}
    @foreach($pesanans as $pesanan)
        @php
            $statusEnum = \App\Enums\StatusPesanan::tryFrom($pesanan->status);
            $nilai = $pesanan->transaksi?->total_bayar ?? $pesanan->itemPesanan->sum('subtotal');
        @endphp
        <div class="order-group">
            <div class="order-header">
                <table>
                    <tr>
                        <td>
                            <span class="fw-bold">#{{ $pesanan->id }}</span>
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

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
