<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Prediksi Penjualan (Moving Average) - Ayu Bakery</title>
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

        table.main td.rekomendasi {
            background: #ddd;
            font-weight: 700;
            color: #111;
        }

        table.main th.th-ma {
            background: #ddd;
        }

        table.main th.th-rek {
            background: #ccc;
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
        <h2>Prediksi Penjualan — Metode Moving Average</h2>
        <div class="date">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    {{-- Info --}}
    <div class="info">
        Periode analisis: <strong>{{ $jumlahPeriode }} minggu terakhir</strong>
        ({{ $startDate->format('d/m/Y') }} — {{ $endDate->format('d/m/Y') }})
    </div>

    {{-- Formula --}}
    <div class="formula-box">
        <strong>Rumus:</strong> MA = (X₁ + X₂ + ... + Xₙ) / N &nbsp;&mdash;&nbsp;
        X = qty penjualan per minggu, N = {{ $jumlahPeriode }} periode.
        Hasil MA = prediksi penjualan minggu berikutnya. Rekomendasi produksi = pembulatan ke atas dari MA.
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
                <th class="th-ma">MA</th>
                <th class="th-rek">Rekomendasi</th>
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
                    <td class="ma">{{ number_format($item['ma'], 1) }}</td>
                    <td class="rekomendasi">{{ $item['rekomendasi'] }} {{ $item['produk']->unit_kecil ?? 'pcs' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($weeks) + 3 }}" style="text-align: center; padding: 20px; color: #94a3b8;">
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
                    MA = ({{ implode(' + ', $item['weekly']) }}) / {{ $jumlahPeriode }} = <strong
                        style="color: #111;">{{ number_format($item['ma'], 2) }}</strong>
                    → Rekomendasi: <strong style="color: #111;">{{ $item['rekomendasi'] }}</strong>
                    {{ $item['produk']->unit_kecil ?? 'pcs' }}
                </div>
            @endforeach
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Laporan prediksi ini digenerate secara otomatis oleh sistem Ayu Bakery menggunakan metode Moving Average.
        Hasil prediksi bersifat peramalan dan dapat berbeda dengan penjualan aktual.
    </div>
</body>

</html>