<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 style="font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                <i class="fas fa-chart-line me-2" style="color: var(--primary-color);"></i>Prediksi Penjualan
            </h4>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">
                Peramalan penjualan menggunakan metode Weighted Moving Average
            </p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('admintoko.penjualan') }}" class="btn btn-modern"
                style="background: #f59e0b; color: white; border: none; display: flex; align-items: center; gap: 8px; border-radius: 8px; padding: 0.5rem 1rem; text-decoration: none;">
                <i class="fas fa-file-import"></i> Kelola Data Penjualan
            </a>
            <button wire:click="simpanPrediksi" class="btn btn-modern"
                style="background: var(--success-color); color: white; border: none; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-save"></i> Simpan Prediksi
            </button>
            <button wire:click="downloadPdf" class="btn btn-modern btn-primary-modern"
                style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </button>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-modern mb-4"
            style="background: rgba(16,185,129,0.1); color: var(--success-color); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px; padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stat-card" style="--accent-color: var(--primary-color);">
                <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: var(--primary-color);">
                    <i class="fas fa-box"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ $totalProduk }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Total Produk Dianalisis
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card" style="--accent-color: #f59e0b;">
                <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                    <i class="fas fa-calculator"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ number_format($avgPrediksi, 1) }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Rata-rata Prediksi (WMA)
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="stat-card" style="--accent-color: #ef4444;">
                <div class="stat-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">
                    {{ $avgMad === null ? '-' : number_format($avgMad, 2) }}
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 500;">Rata-rata MAD (Akurasi)
                </div>
            </div>
        </div>
    </div>

    {{-- Info Card: Rumus --}}
    <div class="modern-card mb-4"
        style="padding: 1rem 1.25rem; background: rgba(99,102,241,0.05); border: 1px solid rgba(99,102,241,0.15);">
        <div style="display: flex; align-items: start; gap: 12px;">
            <div
                style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99,102,241,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-info-circle" style="color: var(--primary-color); font-size: 1rem;"></i>
            </div>
            <div>
                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary); margin-bottom: 4px;">
                    Rumus Weighted Moving Average</div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                    <strong>WMA = (1·X₁ + 2·X₂ + ... + N·Xₙ) / (1 + 2 + ... + N)</strong> — di mana
                    <strong>X</strong> = data penjualan per minggu, <strong>N</strong> = jumlah periode. Minggu terbaru
                    diberi bobot terbesar. Hasil WMA digunakan sebagai prediksi penjualan
                    untuk periode berikutnya.
                </div>
                <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px;">
                    <strong>MAD = Σ|X<sub>t</sub> − F<sub>t</sub>| / n</strong> ·
                    <strong>MSE = Σ(X<sub>t</sub> − F<sub>t</sub>)² / n</strong> ·
                    <strong>MAPE = Σ(|X<sub>t</sub> − F<sub>t</sub>| / X<sub>t</sub> × 100%) / n</strong> — mengukur akurasi
                    ramalan satu-langkah ke depan: tiap minggu t diramal (F<sub>t</sub>) dari minggu-minggu
                    sebelumnya, lalu dibandingkan dengan aktualnya (X<sub>t</sub>). Ramalan minggu berikutnya
                    tidak ikut karena aktualnya belum ada.
                    Semakin kecil nilainya, semakin akurat hasil peramalan.
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="modern-card mb-4" style="padding: 1rem 1.25rem;">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Jumlah
                    Periode (N)</label>
                <input type="number" class="form-control" wire:model.live.debounce.500ms="jumlahPeriode" min="2"
                    max="12" placeholder="4" title="N minggu terakhir (otomatis)">
            </div>
            <div class="col-md-4">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Dari
                    Minggu</label>
                <select class="form-select" wire:model.live="mingguDari"
                    style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                    <option value="">Otomatis</option>
                    @foreach ($mingguTersedia as $m)
                        <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Sampai
                    Minggu</label>
                <select class="form-select" wire:model.live="mingguSampai"
                    style="background: var(--input-bg); border-color: var(--border-color); color: var(--text-primary); border-radius: 8px; padding: 0.75rem 1rem;">
                    <option value="">Otomatis</option>
                    @foreach ($mingguTersedia as $m)
                        <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                @if ($isManual)
                    <button class="btn btn-modern w-100"
                        style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.65rem 1rem;"
                        wire:click="resetPeriode" title="Kembali ke periode otomatis">
                        <i class="fas fa-rotate-left me-1"></i> Otomatis
                    </button>
                @else
                    <div
                        style="font-size: 0.75rem; color: var(--text-muted); padding: 0.65rem 0.25rem;">
                        <i class="fas fa-circle-info me-1"></i>Mode otomatis
                    </div>
                @endif
            </div>
        </div>
        <div class="row g-2 align-items-end mt-2">
            <div class="col-md-8">
                <label class="form-label"
                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Cari
                    Produk</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="Nama produk, kode, atau varian...">
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div
                    style="font-size: 0.78rem; color: var(--text-muted); background: var(--bg-tertiary); padding: 0.65rem 1rem; border-radius: 8px; width: 100%;">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Data {{ $nAktif }} minggu:
                    {{ $startDate->format('d/m/Y') }} –
                    {{ $startDate->copy()->addWeeks($nAktif)->subDay()->format('d/m/Y') }}
                    @if ($isManual)
                        <span style="font-weight: 700;">(manual)</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Prediksi --}}
    <div class="modern-card mb-4" style="padding: 1.25rem;">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <i class="fas fa-chart-bar me-2" style="color: var(--primary-color);"></i>Grafik Prediksi Penjualan
            </h5>
            <select class="form-select" style="max-width: 280px;" wire:model.live="chartProdukId">
                <option value="">Semua Produk</option>
                @foreach ($data as $item)
                    <option value="{{ $item['produk']->id }}">{{ $item['produk']->nama_produk }}{{ $item['produk']->varian_rasa ? ' - '.$item['produk']->varian_rasa : '' }}</option>
                @endforeach
            </select>
        </div>
        <div wire:ignore>
            <div style="position: relative; height: 320px;">
                <canvas id="grafikPrediksi"></canvas>
            </div>
        </div>
        <script type="application/json" id="grafikPrediksiData">@json($chartData)</script>
        @if ($isFallback)
            <div class="alert alert-modern mt-3 mb-0"
                style="background: rgba(99,102,241,0.08); color: var(--text-primary); border: 1px solid rgba(99,102,241,0.25); border-radius: 12px; padding: 0.85rem 1.1rem; font-size: 0.82rem;">
                <i class="fas fa-history me-2" style="color: var(--primary-color);"></i>
                Tidak ada penjualan pada {{ $jumlahPeriode }} minggu terakhir, jadi grafik menampilkan
                {{ $jumlahPeriode }} minggu dengan penjualan terbanyak
                ({{ $startDate->format('d/m/Y') }} – {{ $endDate->format('d/m/Y') }}).
            </div>
        @elseif ($totalTerjualPeriode <= 0)
            <div class="alert alert-modern mt-3 mb-0"
                style="background: rgba(245,158,11,0.1); color: var(--text-primary); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 0.85rem 1.1rem; font-size: 0.82rem;">
                <i class="fas fa-info-circle me-2" style="color: #f59e0b;"></i>
                Belum ada data penjualan sama sekali. Grafik terlihat datar; import data penjualan
                terlebih dahulu.
            </div>
        @endif
    </div>

    <script src="{{ asset('assets/chartjs/chart.umd.min.js') }}"></script>
    @script
    <script>
        function buildAllConfig(payload) {
            return {
                type: 'bar',
                data: {
                    labels: payload.labels,
                    datasets: [
                        { label: 'Total aktual', data: payload.total, backgroundColor: 'rgba(99,102,241,0.7)', borderRadius: 6 },
                        { label: 'WMA (prediksi)', data: payload.wma, backgroundColor: 'rgba(245,158,11,0.85)', borderRadius: 6 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Qty' } },
                        x: { ticks: { maxRotation: 45 } },
                    },
                },
            };
        }

        function buildDetailConfig(payload) {
            return {
                type: 'bar',
                data: {
                    labels: payload.labels,
                    datasets: [
                        { type: 'bar', label: 'Aktual (' + payload.unit + ')', data: payload.aktual, backgroundColor: 'rgba(99,102,241,0.7)', borderRadius: 6 },
                        { type: 'line', label: 'WMA / Prediksi (' + payload.wmaValue + ')', data: payload.wma, borderColor: '#f59e0b', backgroundColor: '#f59e0b', tension: 0.3, pointRadius: 4 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Qty (' + payload.unit + ')' } } },
                    plugins: { title: { display: true, text: payload.title } },
                },
            };
        }

        function renderPrediksiChart() {
            var el = document.getElementById('grafikPrediksi');
            var dataEl = document.getElementById('grafikPrediksiData');
            if (!el || !dataEl || typeof Chart === 'undefined') return;
            var payload;
            try {
                payload = JSON.parse(dataEl.textContent);
            } catch (e) {
                return;
            }
            if (!payload || !payload.labels || payload.labels.length === 0) return;
            var existing = (typeof Chart.getChart === 'function') ? Chart.getChart(el) : window.grafikPrediksi;
            if (existing) existing.destroy();
            window.grafikPrediksi = new Chart(el, payload.mode === 'detail' ? buildDetailConfig(payload) : buildAllConfig(payload));
        }

        function registerPrediksiChartHook() {
            if (window.Livewire && !window._prediksiChartHookRegistered) {
                window._prediksiChartHookRegistered = true;
                Livewire.hook('morph.updated', function () { renderPrediksiChart(); });
            }
        }

        renderPrediksiChart();
        registerPrediksiChartHook();
        document.addEventListener('livewire:init', registerPrediksiChartHook);
    </script>
    @endscript

    {{-- Results Table --}}
    <div class="modern-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table table-modern mb-0" style="border-spacing: 0; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th
                            style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); white-space: nowrap;">
                            Produk
                        </th>
                        @foreach ($weeks as $week)
                            <th
                                style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap;">
                                {{ $week['label'] }}
                                <div style="font-size: 0.6rem; color: var(--text-muted); font-weight: 400;">
                                    {{ $week['range'] }}
                                </div>
                            </th>
                        @endforeach
                        <th
                            style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap; background: rgba(99,102,241,0.05);">
                            WMA
                        </th>
                        <th
                            style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap; background: rgba(239,68,68,0.05);">
                            MAD
                        </th>
                        <th
                            style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap; background: rgba(245,158,11,0.05);">
                            MSE
                        </th>
                        <th
                            style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: center; white-space: nowrap; background: rgba(245,158,11,0.05);">
                            MAPE
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr wire:key="row-{{ $item['produk']->id }}"
                            style="border-bottom: 1px solid var(--border-light); cursor: pointer;"
                            onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'table-row' : 'none'">
                            <td style="padding: 0.85rem 1rem; vertical-align: middle;">
                                <div style="font-weight: 600; font-size: 0.85rem; color: var(--text-primary);">
                                    {{ $item['produk']->nama_produk }}
                                </div>
                                @if ($item['produk']->varian_rasa)
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">
                                        {{ $item['produk']->varian_rasa }}
                                    </div>
                                @endif
                            </td>
                            @foreach ($item['weekly'] as $qty)
                                <td
                                    style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; font-size: 0.85rem; color: var(--text-primary);">
                                    {{ $qty }}
                                </td>
                            @endforeach
                            <td
                                style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; background: rgba(99,102,241,0.05);">
                                <span
                                    style="font-weight: 700; font-size: 0.9rem; color: var(--primary-color);">{{ number_format($item['wma'], 1) }}</span>
                            </td>
                            <td
                                style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; background: rgba(239,68,68,0.05);">
                                <span
                                    style="font-weight: 700; font-size: 0.9rem; color: #ef4444;">{{ $item['mad'] === null ? '-' : number_format($item['mad'], 2) }}</span>
                            </td>
                            <td
                                style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; background: rgba(245,158,11,0.05);">
                                <span
                                    style="font-weight: 700; font-size: 0.9rem; color: #f59e0b;">{{ $item['mse'] === null ? '-' : number_format($item['mse'], 2) }}</span>
                            </td>
                            <td
                                style="padding: 0.85rem 0.75rem; text-align: center; vertical-align: middle; background: rgba(245,158,11,0.05);">
                                <span
                                    style="font-weight: 700; font-size: 0.9rem; color: #f59e0b;">{{ $item['mape'] === null ? '-' : number_format($item['mape'], 2).'%' }}</span>
                            </td>
                        </tr>
                        {{-- Expandable detail row --}}
                        <tr style="display: none; background: var(--bg-tertiary);">
                            <td colspan="{{ count($weeks) + 5 }}" style="padding: 0.75rem 1.5rem 1rem;">
                                <div
                                    style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 6px;">
                                    <i class="fas fa-calculator me-1"></i> Detail Perhitungan Weighted Moving Average
                                </div>
                                <div
                                    style="background: var(--bg-secondary); border-radius: 8px; padding: 1rem; border: 1px solid var(--border-color);">
                                    <div style="font-size: 0.82rem; color: var(--text-primary); margin-bottom: 8px;">
                                        <strong>{{ $item['produk']->nama_produk }}</strong> — data penjualan
                                        {{ $jumlahPeriode }} minggu terakhir:
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 6px;">
                                        @foreach ($item['weekly'] as $i => $qty)
                                            Minggu {{ $i + 1 }} = <strong>{{ $qty }}</strong>
                                            {{ $item['produk']->unit ?? 'pcs' }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </div>
                                    @php
                                        $wmaTerms = [];
                                        $wmaBobot = 0;
                                        foreach ($item['weekly'] as $idx => $qty) {
                                            $b = $idx + 1;
                                            $wmaTerms[] = $b.'·'.$qty;
                                            $wmaBobot += $b;
                                        }
                                        $wmaFmt = number_format($item['wma'], 2);
                                    @endphp
                                    <div
                                        style="font-size: 0.85rem; color: var(--primary-color); font-weight: 600; padding: 8px 12px; background: rgba(99,102,241,0.06); border-radius: 6px; display: inline-block;">
                                        WMA = ({{ implode(' + ', $wmaTerms) }}) / {{ $wmaBobot }} =
                                        <strong>{{ $wmaFmt }}</strong>
                                    </div>
                                    <div style="margin-top: 10px; font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase;">
                                        Evaluasi ramalan satu-langkah ke depan (F<sub>t</sub> dari minggu-minggu sebelumnya)
                                    </div>
                                    @if (empty($item['eval']))
                                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">
                                            Hanya 1 minggu data — belum ada pasangan ramalan-aktual untuk dievaluasi.
                                        </div>
                                    @else
                                        <table style="width: 100%; border-collapse: collapse; margin-top: 6px;">
                                            <thead>
                                                <tr>
                                                    <th style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: center; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">Minggu</th>
                                                    <th style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">Aktual (X)</th>
                                                    <th style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">Ramalan (F)</th>
                                                    <th style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">|Error|</th>
                                                    <th style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">Error²</th>
                                                    <th style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; text-align: right; padding: 4px 8px; border-bottom: 1px solid var(--border-color);">|Error|/X</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item['eval'] as $t => $e)
                                                    <tr>
                                                        <td style="font-size: 0.8rem; padding: 4px 8px; text-align: center;">{{ $t + 2 }}</td>
                                                        <td style="font-size: 0.8rem; padding: 4px 8px; text-align: right;">{{ $e['x'] }}</td>
                                                        <td style="font-size: 0.8rem; padding: 4px 8px; text-align: right;">{{ number_format($e['f'], 2) }}</td>
                                                        <td style="font-size: 0.8rem; padding: 4px 8px; text-align: right;">{{ number_format(abs($e['x'] - $e['f']), 2) }}</td>
                                                        <td style="font-size: 0.8rem; padding: 4px 8px; text-align: right;">{{ number_format((abs($e['x'] - $e['f'])) ** 2, 2) }}</td>
                                                        <td style="font-size: 0.8rem; padding: 4px 8px; text-align: right;">{{ $e['x'] != 0 ? number_format(abs($e['x'] - $e['f']) / $e['x'] * 100, 2).'%' : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div style="font-size: 0.82rem; color: var(--text-primary); margin-top: 6px;">
                                            MAD = <strong>{{ $item['mad'] === null ? '-' : number_format($item['mad'], 2) }}</strong>
                                            &nbsp;|&nbsp; MSE = <strong>{{ $item['mse'] === null ? '-' : number_format($item['mse'], 2) }}</strong>
                                            &nbsp;|&nbsp; MAPE = <strong>{{ $item['mape'] === null ? '-' : number_format($item['mape'], 2).'%' }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($weeks) + 5 }}" style="text-align: center; padding: 3rem 1rem;">
                                <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                                <p class="mt-2" style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">Tidak
                                    ada
                                    data produk ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
