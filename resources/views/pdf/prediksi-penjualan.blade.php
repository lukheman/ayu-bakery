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
        @if(!empty($isManual ?? false))
            Periode analisis: <strong>pilihan manual ({{ $jumlahPeriode }} minggu)</strong>
        @else
            Periode analisis: <strong>seluruh data penjualan ({{ $jumlahPeriode }} minggu)</strong>
        @endif
        ({{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }})
        @if(!empty($isFallback ?? false))
            <br><span style="font-size: 10px;">Tidak ada penjualan pada minggu-minggu terakhir, sehingga
                ditampilkan {{ $jumlahPeriode }} minggu dengan penjualan terbanyak.</span>
        @endif
    </div>

    {{-- Formula --}}
    <div class="formula-box">
        <strong>Rumus:</strong> WMA = (1·X<sub>t-3</sub> + 2·X<sub>t-2</sub> + 3·X<sub>t-1</sub>) / 6 &nbsp;&mdash;&nbsp;
        X = qty penjualan per minggu. Tiap minggu diramal dari <strong>3 minggu sebelumnya</strong>
        (terbaru bobot 3). Tiga minggu pertama tidak bisa diramal.
        Hasil WMA = prediksi penjualan minggu berikutnya.
        <br>
        <strong>Akurasi (evaluasi ramalan satu-langkah ke depan):</strong>
        MAD = Σ|Xₜ − Fₜ| / n &nbsp;·&nbsp;
        MSE = Σ(Xₜ − Fₜ)² / n &nbsp;·&nbsp;
        MAPE = Σ(|Xₜ − Fₜ| / Xₜ × 100%) / n &nbsp;&mdash;&nbsp;
        semakin kecil nilainya, semakin akurat hasil peramalan.
    </div>

    {{-- Table per minggu untuk produk terpilih --}}
    @if($tabel)
        <div style="font-size: 12px; font-weight: 800; color: #111; margin-bottom: 8px;">
            {{ $tabel['produk']->nama_produk }}
            @if($tabel['produk']->varian_rasa)
                <span style="font-weight: 400;">— {{ $tabel['produk']->varian_rasa }}</span>
            @endif
        </div>
        <table class="main">
            <thead>
                <tr>
                    <th style="text-align: left;">Periode</th>
                    <th>Aktual (Xt)</th>
                    <th>WMA (St)</th>
                    <th>Error</th>
                    <th>MAD</th>
                    <th>MSE</th>
                    <th>MAPE (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tabel['rows'] as $r)
                    <tr>
                        <td style="text-align: left; font-weight: 600;">{{ $r['label'] }}<br><span
                                style="font-weight: 400; font-size: 7px;">{{ $r['range'] }}</span></td>
                        <td>{{ number_format($r['aktual'], 0, ',', '.') }}</td>
                        <td>
                            @if($r['prediksi'] === null)
                                -
                            @else
                                <strong>{{ number_format($r['prediksi'], 2, ',', '.') }}</strong>
                            @endif
                        </td>
                        <td>{{ $r['error'] === null ? '-' : number_format($r['error'], 2, ',', '.') }}</td>
                        <td>{{ $r['abs'] === null ? '-' : number_format($r['abs'], 2, ',', '.') }}</td>
                        <td>{{ $r['sq'] === null ? '-' : number_format($r['sq'], 2, ',', '.') }}</td>
                        <td>{{ $r['pct'] === null ? '-' : number_format($r['pct'], 2, ',', '.').'%' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: 700;">Rata-rata error</td>
                    <td style="font-weight: 700;">{{ $tabel['mad'] === null ? '-' : number_format($tabel['mad'], 2, ',', '.') }}</td>
                    <td style="font-weight: 700;">{{ $tabel['mse'] === null ? '-' : number_format($tabel['mse'], 2, ',', '.') }}</td>
                    <td style="font-weight: 700;">{{ $tabel['mape'] === null ? '-' : number_format($tabel['mape'], 2, ',', '.').'%' }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 20px; color: #94a3b8;">Tidak ada data produk.</div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Laporan prediksi ini digenerate secara otomatis oleh sistem Ayu Bakery menggunakan metode Weighted Moving Average.
        Hasil prediksi bersifat peramalan dan dapat berbeda dengan penjualan aktual.
    </div>
</body>

</html>
