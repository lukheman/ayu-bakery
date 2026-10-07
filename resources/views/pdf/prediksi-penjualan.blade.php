<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Prediksi Penjualan (Weighted Moving Average) - Ayu Bakery</title>
    <style>
        @@page { size: A4; margin: 2cm; }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
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

        .info {
            text-align: center;
            font-size: 11px;
            color: #333;
            margin-bottom: 12px;
        }

        .info strong {
            color: #111;
        }

        .formula-box {
            background: #f5f5f5;
            border: 1px solid #aaa;
            padding: 10px 16px;
            margin-bottom: 16px;
            font-size: 10px;
        }

        .formula-box strong {
            color: #111;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.main th {
            background: #eee;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            color: #111;
            padding: 6px 8px;
            text-align: center;
            border: 1px solid #aaa;
        }

        table.main td {
            padding: 6px 8px;
            font-size: 9px;
            border: 1px solid #ccc;
            text-align: center;
        }

        table.main td.produk {
            text-align: left;
            font-weight: 600;
        }

        table.main td.ma {
            background: #eee;
            font-weight: 700;
            color: #111;
        }

        table.main th.th-ma {
            background: #ddd;
        }

        table.main td.mse {
            background: #fdf3e3;
            font-weight: 700;
            color: #111;
        }

        table.main th.th-mse {
            background: #f5e3c2;
        }

        table.main td.mad {
            background: #f3e2e2;
            font-weight: 700;
            color: #111;
        }

        table.main th.th-mad {
            background: #e8c9c9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #555;
            border-top: 1px solid #aaa;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>🎂 Ayu Bakery</h1>
        <h2>Prediksi Penjualan — Metode Weighted Moving Average</h2>
        <div class="date">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    {{-- Info --}}
    <div class="info">
        Periode analisis: <strong>{{ $jumlahPeriode }} minggu terakhir</strong>
        ({{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }})
        @if(!empty($isManual ?? false))
            <br><span style="font-size: 10px;">Periode pilihan manual (Dari–Sampai Minggu).</span>
        @elseif(!empty($isFallback ?? false))
            <br><span style="font-size: 10px;">Tidak ada penjualan pada minggu-minggu terakhir, sehingga
                ditampilkan {{ $jumlahPeriode }} minggu dengan penjualan terbanyak.</span>
        @endif
    </div>

    {{-- Formula --}}
    <div class="formula-box">
        <strong>Rumus:</strong> WMA = (1·X₁ + 2·X₂ + ... + N·Xₙ) / (1 + 2 + ... + N) &nbsp;&mdash;&nbsp;
        X = qty penjualan per minggu, N = {{ $jumlahPeriode }} periode (minggu terbaru bobot terbesar).
        Hasil WMA = prediksi penjualan minggu berikutnya.
        <br>
        <strong>Akurasi (evaluasi ramalan satu-langkah ke depan — tiap minggu diramal dari minggu-minggu sebelumnya):</strong>
        MAD = Σ|Xₜ − Fₜ| / n &nbsp;·&nbsp;
        MSE = Σ(Xₜ − Fₜ)² / n &nbsp;·&nbsp;
        MAPE = Σ(|Xₜ − Fₜ| / Xₜ × 100%) / n &nbsp;&mdash;&nbsp;
        semakin kecil nilainya, semakin akurat hasil peramalan.
    </div>

    {{-- Table --}}
    <table class="main">
        <thead>
            <tr>
                <th style="text-align: left; min-width: 120px;">Produk</th>
                @foreach($weeks as $week)
                    <th>{{ $week['label'] }}<br><span style="font-weight: 400; font-size: 7px;">{{ $week['range'] }}</span>
                    </th>
                @endforeach
                <th class="th-ma">WMA</th>
                <th class="th-mad">MAD</th>
                <th class="th-mse">MSE</th>
                <th class="th-mse">MAPE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td class="produk">
                        {{ $item['produk']->nama_produk }}
                        @if($item['produk']->varian_rasa)
                            <br><span
                                style="font-weight: 400; color: #94a3b8; font-size: 8px;">{{ $item['produk']->varian_rasa }}</span>
                        @endif
                    </td>
                    @foreach($item['weekly'] as $qty)
                        <td>{{ $qty }}</td>
                    @endforeach
                    <td class="ma">{{ number_format($item['wma'], 1) }}</td>
                    <td class="mad">{{ $item['mad'] === null ? '-' : number_format($item['mad'], 2) }}</td>
                    <td class="mse">{{ $item['mse'] === null ? '-' : number_format($item['mse'], 2) }}</td>
                    <td class="mse">{{ $item['mape'] === null ? '-' : number_format($item['mape'], 2).'%' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($weeks) + 5 }}" style="text-align: center; padding: 20px; color: #94a3b8;">
                        Tidak ada data produk.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Calculation Detail Section --}}
    @if($data->count() > 0)
        <div style="margin-top: 16px; font-size: 9px; color: #555;">
            <strong style="color: #111;">Detail Perhitungan:</strong>
            @foreach($data as $item)
                <div style="margin-top: 4px;">
                    <strong>{{ $item['produk']->nama_produk }}:</strong>
                    WMA = <strong
                        style="color: #111;">{{ number_format($item['wma'], 2) }}</strong>
                    &nbsp;|&nbsp; MAD = <strong
                        style="color: #111;">{{ $item['mad'] === null ? '-' : number_format($item['mad'], 2) }}</strong>
                    &nbsp;|&nbsp; MSE = <strong
                        style="color: #111;">{{ $item['mse'] === null ? '-' : number_format($item['mse'], 2) }}</strong>
                    &nbsp;|&nbsp; MAPE = <strong
                        style="color: #111;">{{ $item['mape'] === null ? '-' : number_format($item['mape'], 2).'%' }}</strong>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Laporan prediksi ini digenerate secara otomatis oleh sistem Ayu Bakery menggunakan metode Weighted Moving Average.
        Hasil prediksi bersifat peramalan dan dapat berbeda dengan penjualan aktual.
    </div>
</body>

</html>
