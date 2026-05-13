<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Persediaan - Ayu Bakery</title>
    <style>
        @@page { size: A4; margin: 2cm; }
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
            color: #111;
            margin-bottom: 2px;
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

        .produk-group {
            margin: 20px;
            page-break-inside: avoid;
        }

        .produk-header {
            background: #eee;
            padding: 8px 12px;
            border: 1px solid #aaa;
            border-bottom: 2px solid #111;
        }

        .produk-name {
            font-size: 12px;
            font-weight: 700;
            color: #111;
        }

        .produk-meta {
            font-size: 9px;
            color: #555;
        }

        .produk-stok {
            font-size: 11px;
            font-weight: 700;
            color: #111;
        }

        .order-group {
            margin: 20px;
            page-break-inside: avoid;
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

        table.main tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 2px 6px;
            font-size: 9px;
            font-weight: 700;
            border: 1px solid #555;
            color: #111;
        }

        .text-success { color: #111; font-weight: 600; }
        .text-warning { color: #333; }
        .text-danger  { color: #111; font-weight: 700; }
        .text-muted   { color: #555; }
        .fw-bold { font-weight: 700; }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #555;
            border-top: 1px solid #aaa;
            padding-top: 10px;
            margin-right: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #555;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>Ayu Bakery</h1>
        <h2>Laporan Persediaan Produk</h2>
    </div>

    {{-- Product Groups --}}
    @forelse($produks as $produk)
        @if($produk->persediaan->count() > 0)
            <div class="produk-group">
                <div class="produk-header">
                    <table class="main">
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

                <table class="main">
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
                                        <span>
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
                                            {{ $statusEnum->label() }}
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
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
