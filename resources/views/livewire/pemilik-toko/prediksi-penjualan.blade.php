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
                    <strong>WMA = (1·X<sub>t-3</sub> + 2·X<sub>t-2</sub> + 3·X<sub>t-1</sub>) / 6</strong> — di mana
                    <strong>X</strong> = data penjualan per minggu. Ramalan tiap minggu dihitung dari
                    <strong>3 minggu sebelumnya</strong> (terbaru bobot 3). Hasil WMA digunakan sebagai
                    prediksi penjualan untuk minggu berikutnya.
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
            <div class="col-md-5">
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
            <div class="col-md-5">
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
                    @else
                        <span style="font-weight: 700;">(seluruh data)</span>
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
                Tidak ada penjualan pada {{ $nAktif }} minggu terakhir, jadi grafik menampilkan
                {{ $nAktif }} minggu dengan penjualan terbanyak
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

    {{-- Results Table Per Minggu --}}
    <div class="modern-card" style="padding: 1.25rem;">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                <i class="fas fa-table me-2" style="color: var(--primary-color);"></i>Hasil Prediksi Per Minggu
            </h5>
            <select class="form-select" style="max-width: 280px;" wire:model.live="tabelProdukId">
                @foreach ($data as $item)
                    <option value="{{ $item['produk']->id }}">{{ $item['produk']->nama_produk }}{{ $item['produk']->varian_rasa ? ' - '.$item['produk']->varian_rasa : '' }}</option>
                @endforeach
            </select>
        </div>
        @if ($tabel)
            <div style="overflow-x: auto;">
                <table class="table table-modern mb-0" style="border-spacing: 0; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); white-space: nowrap;">Tanggal</th>
                            <th style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right; white-space: nowrap;">Aktual (Xt)</th>
                            <th style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right; white-space: nowrap;">WMA (St)</th>
                            <th style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right; white-space: nowrap;">Error</th>
                            <th style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right; white-space: nowrap;">MAD</th>
                            <th style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right; white-space: nowrap;">MSE</th>
                            <th style="padding: 0.85rem 0.75rem; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: var(--text-secondary); border-bottom: 2px solid var(--border-color); text-align: right; white-space: nowrap;">MAPE (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tabel['rows'] as $r)
                            <tr wire:key="pred-{{ $tabel['produk']->id }}-{{ $r['label'] }}" style="border-bottom: 1px solid var(--border-light);">
                                <td style="padding: 0.85rem 1rem; vertical-align: middle;">
                                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">{{ $r['label'] }}</div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $r['range'] }}</div>
                                </td>
                                <td style="padding: 0.85rem 0.75rem; text-align: right; vertical-align: middle; font-weight: 700; font-size: 0.9rem; color: var(--success-color);">{{ number_format($r['aktual'], 0, ',', '.') }}</td>
                                <td style="padding: 0.85rem 0.75rem; text-align: right; vertical-align: middle;">
                                    @if ($r['prediksi'] === null)
                                        <span style="color: var(--text-muted);">-</span>
                                    @else
                                        <span class="badge-modern" style="background: rgba(245,158,11,0.9); color: white; font-size: 0.85rem; font-weight: 700;">{{ number_format($r['prediksi'], 2, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td style="padding: 0.85rem 0.75rem; text-align: right; vertical-align: middle; font-size: 0.85rem;">{{ $r['error'] === null ? '-' : number_format($r['error'], 2, ',', '.') }}</td>
                                <td style="padding: 0.85rem 0.75rem; text-align: right; vertical-align: middle; font-size: 0.85rem;">{{ $r['abs'] === null ? '-' : number_format($r['abs'], 2, ',', '.') }}</td>
                                <td style="padding: 0.85rem 0.75rem; text-align: right; vertical-align: middle; font-size: 0.85rem;">{{ $r['sq'] === null ? '-' : number_format($r['sq'], 2, ',', '.') }}</td>
                                <td style="padding: 0.85rem 0.75rem; text-align: right; vertical-align: middle; font-size: 0.85rem; font-weight: 700;">
                                    @if ($r['pct'] === null)
                                        <span style="color: var(--text-muted);">-</span>
                                    @else
                                        <span style="color: {{ $r['pct'] < 10 ? 'var(--success-color)' : ($r['pct'] <= 20 ? '#f59e0b' : '#ef4444') }};">{{ number_format($r['pct'], 2, ',', '.') }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        {{-- Baris ringkasan akurasi --}}
                        <tr style="background: var(--bg-tertiary); font-weight: 700;">
                            <td colspan="4" style="padding: 0.85rem 1rem; font-size: 0.82rem; text-transform: uppercase; color: var(--text-secondary);">Rata-rata error (MAD / MSE / MAPE)</td>
                            <td style="padding: 0.85rem 0.75rem; text-align: right; font-size: 0.9rem;">{{ $tabel['mad'] === null ? '-' : number_format($tabel['mad'], 2, ',', '.') }}</td>
                            <td style="padding: 0.85rem 0.75rem; text-align: right; font-size: 0.9rem;">{{ $tabel['mse'] === null ? '-' : number_format($tabel['mse'], 2, ',', '.') }}</td>
                            <td style="padding: 0.85rem 0.75rem; text-align: right; font-size: 0.9rem;">{{ $tabel['mape'] === null ? '-' : number_format($tabel['mape'], 2, ',', '.').'%' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 3rem 1rem;">
                <i class="fas fa-chart-line" style="font-size: 2.5rem; color: var(--text-muted);"></i>
                <p class="mt-2" style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0;">Tidak ada data produk ditemukan</p>
            </div>
        @endif
    </div>

</div>


</div>
