<?php

namespace App\Livewire\PemilikToko;

use App\Enums\StatusPesanan;
use App\Models\ItemPenjualan;
use App\Models\ItemPesanan;
use App\Models\MovingAverage;
use App\Models\PenjualanKasir;
use App\Models\Produk;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Prediksi Penjualan (Weighted Moving Average) - Ayu Bakery')]
#[Layout('layouts.app')]
class PrediksiPenjualan extends Component
{
    public string $search = '';

    /** Pilihan manual: minggu awal & akhir (format Y-m-d, Senin). Kosong = otomatis. */
    public ?string $mingguDari = null;

    public ?string $mingguSampai = null;

    public $chartProdukId = null;

    /** Produk yang ditampilkan pada tabel prediksi per minggu. */
    public $tabelProdukId = null;

    public function resetPeriode(): void
    {
        $this->reset(['mingguDari', 'mingguSampai']);
    }

    /**
     * Kunci minggu ISO (YYYYWW) yang sama dengan YEARWEEK(tanggal, 1) di MySQL.
     */
    private function kunciMinggu(Carbon $tanggal): int
    {
        return intval($tanggal->isoFormat('GGGG').str_pad($tanggal->isoFormat('WW'), 2, '0', STR_PAD_LEFT));
    }

    /**
     * Batas tanggal penjualan kasir (min/max). Bila $idProduk diisi,
     * hanya transaksi yang memuat produk tersebut.
     *
     * @return array{0: mixed, 1: mixed}
     */
    private function batasKasir(?int $idProduk = null): array
    {
        $q = PenjualanKasir::query();
        if ($idProduk) {
            $q->whereHas('items', fn ($qq) => $qq->where('id_produk', $idProduk));
        }

        return [$q->min('tanggal'), $q->max('tanggal')];
    }

    /**
     * Batas tanggal penjualan reseller selesai (min/max). Bila $idProduk
     * diisi, hanya pesanan yang memuat produk tersebut.
     *
     * @return array{0: mixed, 1: mixed}
     */
    private function batasReseller(?int $idProduk = null): array
    {
        $q = Transaksi::query()
            ->whereHas('pesanan', fn ($qq) => $qq->where('status', StatusPesanan::SELESAI->value));
        if ($idProduk) {
            $q->whereHas('pesanan.itemPesanan', fn ($qq) => $qq->where('id_produk', $idProduk));
        }

        return [$q->min('tanggal'), $q->max('tanggal')];
    }

    /**
     * Rentang primer: SELURUH riwayat penjualan — mulai minggu penjualan
     * paling lama sampai minggu penjualan terbaru (jangkar). Bila produk
     * dipilih, rentang mengikuti riwayat produk tersebut sehingga tabel
     * langsung bersih tanpa harus memilih manual. Tidak ada batasan N
     * minggu agar semua data ikut dihitung.
     *
     * @return array{0: Carbon, 1: Carbon} [startDate, endDate]
     */
    private function rentangPrimer(?int $idProduk = null): array
    {
        [$terlamaKasir, $terakhirKasir] = $this->batasKasir($idProduk);
        [$terlamaReseller, $terakhirReseller] = $this->batasReseller($idProduk);

        // Produk tanpa penjualan sama sekali: pakai rentang global.
        if (! $terlamaKasir && ! $terlamaReseller && $idProduk) {
            return $this->rentangPrimer(null);
        }

        $kandidatAwal = collect([$terlamaKasir, $terlamaReseller])->filter();

        // Belum ada data sama sekali: tampilkan 4 minggu terakhir yang kosong.
        if ($kandidatAwal->isEmpty()) {
            $acuan = Carbon::now();
            $n = 4;

            return [$acuan->copy()->subWeeks($n)->startOfWeek(), $acuan->copy()->endOfWeek()];
        }

        $awalRaw = collect([$terlamaKasir, $terlamaReseller])
            ->filter()
            ->map(fn ($t) => substr((string) $t, 0, 10))
            ->min();
        $awal = Carbon::parse($awalRaw);

        $jangkar = null;
        foreach (collect([$terakhirKasir, $terakhirReseller])->filter() as $tanggalRaw) {
            $tanggal = Carbon::parse($tanggalRaw);
            if (! $jangkar || $tanggal->gt($jangkar)) {
                $jangkar = $tanggal;
            }
        }

        $acuan = ($jangkar && $jangkar->lessThan(Carbon::now())) ? $jangkar : Carbon::now();

        return [$awal->copy()->startOfWeek(), $acuan->copy()->endOfWeek()];
    }

    /**
     * Daftar seluruh minggu kalender dari penjualan paling lama sampai
     * paling baru, untuk opsi pilihan manual Dari/Sampai Minggu.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function getDaftarMingguTersediaProperty(): array
    {
        [$minKasir, $maxKasir] = $this->batasKasir();
        [$minReseller, $maxReseller] = $this->batasReseller();
        $batas = collect([$minKasir, $minReseller, $maxKasir, $maxReseller])->filter();

        if ($batas->isEmpty()) {
            return [];
        }

        $daftar = [];
        $w = Carbon::parse($batas->min())->startOfWeek();
        $akhir = Carbon::parse($batas->max())->startOfWeek();
        while ($w->lessThanOrEqualTo($akhir)) {
            $daftar[] = [
                'value' => $w->format('Y-m-d'),
                'label' => $w->format('d/m/Y').' – '.$w->copy()->endOfWeek()->format('d/m/Y'),
            ];
            $w->addWeek();
        }

        return $daftar;
    }

    /**
     * Rentang manual dari pilihan Dari/Sampai Minggu.
     * Bila terbalik, otomatis dibalik. Tanpa batasan jumlah minggu.
     *
     * @return array{0: Carbon, 1: int} [startDate, jumlahMinggu]
     */
    private function rentangManual(): array
    {
        $dari = Carbon::parse($this->mingguDari)->startOfWeek();
        $sampai = Carbon::parse($this->mingguSampai)->startOfWeek();
        if ($dari->gt($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [$dari, $dari->diffInWeeks($sampai) + 1];
    }

    /**
     * Hitung analisis lengkap: data mingguan + label minggu + rentang.
     * Pengecekan memakai total per BUCKET (bukan total mentah rentang) agar
     * konsisten dengan yang tampil di grafik/tabel. Bila jendela primer
     * kosong namun masih ada data lama, otomatis mundur ke N minggu
     * berurutan dengan penjualan terbanyak.
     *
     * @return array{data: Collection, weeks: array, start: Carbon, end: Carbon, n: int, fallback: bool, manual: bool}
     */
    private function dataAnalisis(): array
    {
        // Mode manual: kedua minggu dipilih -> pakai persis rentang itu.
        if ($this->mingguDari && $this->mingguSampai) {
            [$start, $n] = $this->rentangManual();
            $end = $start->copy()->addWeeks($n - 1)->endOfWeek();

            return [
                'data' => $this->hitungDataMingguan($start, $end, $n),
                'weeks' => $this->daftarMinggu($start, $n),
                'start' => $start,
                'end' => $end,
                'n' => $n,
                'fallback' => false,
                'manual' => true,
            ];
        }

        // Mode otomatis mengikuti produk terpilih: rentang = riwayat
        // penjualan produk tersebut, sehingga tabel langsung bersih.
        $produkId = $this->tabelProdukId ? (int) $this->tabelProdukId : null;
        if ($produkId && ! Produk::where('id', $produkId)->exists()) {
            $produkId = null;
        }
        if (! $produkId) {
            $produkId = Produk::orderBy('nama_produk')->value('id');
        }

        [$start, $end] = $this->rentangPrimer($produkId);
        // N = seluruh minggu dalam rentang (tanpa batasan).
        $n = $start->copy()->diffInWeeks($end->copy()->startOfWeek()) + 1;
        $data = $this->hitungDataMingguan($start, $end, $n);

        if ($data->sum('total') > 0 || ! $this->adaDataLebihLama($start)) {
            return [
                'data' => $data,
                'weeks' => $this->daftarMinggu($start, $n),
                'start' => $start,
                'end' => $end,
                'n' => $n,
                'fallback' => false,
                'manual' => false,
            ];
        }

        $fb = $this->jendelaDataTerbanyak($n);
        if ($fb) {
            return [
                'data' => $this->hitungDataMingguan($fb[0], $fb[1], $n),
                'weeks' => $this->daftarMinggu($fb[0], $n),
                'start' => $fb[0],
                'end' => $fb[1],
                'n' => $n,
                'fallback' => true,
                'manual' => false,
            ];
        }

        return [
            'data' => $data,
            'weeks' => $this->daftarMinggu($start, $n),
            'start' => $start,
            'end' => $end,
            'n' => $n,
            'fallback' => false,
            'manual' => false,
        ];
    }

    /**
     * Apakah masih ada data penjualan yang lebih lama dari batas awal?
     */
    private function adaDataLebihLama(Carbon $start): bool
    {
        if (PenjualanKasir::where('tanggal', '<', $start)->exists()) {
            return true;
        }

        return Transaksi::query()
            ->whereHas('pesanan', fn ($q) => $q->where('status', StatusPesanan::SELESAI->value))
            ->where('tanggal', '<', $start)
            ->exists();
    }

    /**
     * Cari N minggu kalender berurutan dengan total penjualan terbanyak
     * di seluruh riwayat (kasir + reseller selesai). Seri dimenangkan oleh
     * periode paling akhir. Mengembalikan null bila tidak ada data.
     *
     * @return array{0: Carbon, 1: Carbon}|null [startDate, endDate]
     */
    private function jendelaDataTerbanyak(int $n): ?array
    {
        [$kasirMin, $kasirMax] = $this->batasKasir();
        [$resellerMin, $resellerMax] = $this->batasReseller();

        $batas = collect([$kasirMin, $resellerMin, $kasirMax, $resellerMax])->filter();
        if ($batas->isEmpty()) {
            return null;
        }

        $min = Carbon::parse($batas->min())->startOfWeek();
        $max = Carbon::parse($batas->max())->startOfWeek();

        $qtyKasir = ItemPenjualan::query()
            ->join('penjualan_kasir', 'item_penjualan.id_penjualan', '=', 'penjualan_kasir.id')
            ->selectRaw('YEARWEEK(penjualan_kasir.tanggal, 1) as yw, SUM(item_penjualan.jumlah) as qty')
            ->groupBy('yw')
            ->pluck('qty', 'yw');
        $qtyReseller = ItemPesanan::query()
            ->join('pesanan', 'item_pesanan.id_pesanan', '=', 'pesanan.id')
            ->join('transaksi', 'pesanan.id', '=', 'transaksi.id_pesanan')
            ->where('pesanan.status', StatusPesanan::SELESAI->value)
            ->selectRaw('YEARWEEK(transaksi.tanggal, 1) as yw, SUM(item_pesanan.jumlah) as qty')
            ->groupBy('yw')
            ->pluck('qty', 'yw');

        // Susun qty per minggu kalender berurutan dari minggu terlama.
        $mingguan = [];
        $w = $min->copy();
        while ($w->lessThanOrEqualTo($max)) {
            $key = (string) $this->kunciMinggu($w);
            $mingguan[] = [
                'start' => $w->copy(),
                'qty' => (int) ($qtyKasir[$key] ?? 0) + (int) ($qtyReseller[$key] ?? 0),
            ];
            $w->addWeek();
        }

        // Sliding window N minggu: total terbesar, seri pilih yang terbaru.
        $terbaik = null;
        $totalTerbaik = 0;
        $jumlah = count($mingguan);
        for ($i = 0; $i <= $jumlah - $n; $i++) {
            $total = 0;
            for ($j = 0; $j < $n; $j++) {
                $total += $mingguan[$i + $j]['qty'];
            }
            if ($total > 0 && $total >= $totalTerbaik) {
                $totalTerbaik = $total;
                $terbaik = $i;
            }
        }

        // Riwayat lebih pendek dari N minggu: tampilkan sejak minggu pertama
        // (kekurangannya nol) selama ada penjualan.
        if ($terbaik === null && $jumlah > 0 && array_sum(array_column($mingguan, 'qty')) > 0) {
            $terbaik = 0;
        }

        if ($terbaik === null) {
            return null;
        }

        $mulai = $mingguan[$terbaik]['start'];
        $akhir = $mulai->copy()->addWeeks($n - 1)->endOfWeek();

        return [$mulai, $akhir];
    }

    /**
     * Rumus WMA tertulis untuk ramalan minggu ke-(t+1),
     * dihitung dari 3 minggu sebelumnya (urutan terbaru dulu,
     * seperti: ((X × 3) + (X × 2) + (X × 1)) / 6).
     */
    private function rumusWma(array $weekly, int $t): string
    {
        $fmt = fn ($v) => number_format($v, 0, ',', '.');

        return '(('.$fmt($weekly[$t - 1]).' × 3) + ('.$fmt($weekly[$t - 2]).' × 2) + ('.$fmt($weekly[$t - 3]).' × 1)) / 6';
    }

    /**
     * Susun tabel prediksi per minggu untuk satu produk:
     * Aktual (Xt), Prediksi/Forecast (Ft), Error, |Error|, Error², %Error.
     * Tiga minggu pertama tidak punya ramalan (butuh 3 minggu sebelumnya).
     */
    private function tabelPrediksi(array $item, array $weeks): array
    {
        $weekly = $item['weekly'];
        $rows = [];

        foreach ($weekly as $i => $qty) {
            $eval = $i >= 3 ? ($item['eval'][$i - 3] ?? null) : null;
            $error = $eval ? $qty - $eval['f'] : null;
            $rows[] = [
                'label' => $weeks[$i]['label'] ?? ('Mg '.($i + 1)),
                'range' => $weeks[$i]['range'] ?? '',
                'aktual' => $qty,
                'prediksi' => $eval ? round($eval['f'], 2) : null,
                'rumus' => $eval ? $this->rumusWma($weekly, $i) : null,
                'error' => $error === null ? null : round($error, 2),
                'abs' => $error === null ? null : round(abs($error), 2),
                'sq' => $error === null ? null : round($error ** 2, 2),
                'pct' => ($error === null || $qty == 0) ? null : round(abs($error) / $qty * 100, 2),
            ];
        }

        $n = count($weekly);

        return [
            'produk' => $item['produk'],
            'rows' => $rows,
            'mad' => $item['mad'],
            'mse' => $item['mse'],
            'mape' => $item['mape'],
            'wma' => $item['wma'],
            'rumusBerikutnya' => $n >= 3 ? $this->rumusWma($weekly, $n) : null,
            'totalAktual' => array_sum($weekly),
        ];
    }

    /**
     * Label minggu untuk header tabel/grafik/PDF.
     */
    private function daftarMinggu(Carbon $startDate, int $n): array
    {
        $weeks = [];
        $currentWeek = $startDate->copy();
        for ($i = 0; $i < $n; $i++) {
            $weeks[] = [
                'label' => 'Mg '.($i + 1),
                'range' => $currentWeek->format('d/m').' - '.$currentWeek->copy()->endOfWeek()->format('d/m'),
            ];
            $currentWeek->addWeek();
        }

        return $weeks;
    }

    /**
     * Hitung penjualan per produk per minggu pada rentang tertentu.
     * Menggabungkan data dari item_penjualan (kasir) dan item_pesanan (reseller selesai).
     * $n = jumlah minggu (bobot WMA 1..N, minggu terbaru bobot terbesar).
     */
    private function hitungDataMingguan(Carbon $startDate, Carbon $endDate, int $n): Collection
    {

        // Ambil semua produk
        $produks = Produk::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('nama_produk', 'like', '%'.$this->search.'%')
                        ->orWhere('kode_produk', 'like', '%'.$this->search.'%')
                        ->orWhere('varian_rasa', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('nama_produk')
            ->get();

        // Penjualan kasir per produk per minggu
        $kasirSales = ItemPenjualan::query()
            ->join('penjualan_kasir', 'item_penjualan.id_penjualan', '=', 'penjualan_kasir.id')
            ->whereBetween('penjualan_kasir.tanggal', [$startDate, $endDate])
            ->selectRaw('item_penjualan.id_produk, YEARWEEK(penjualan_kasir.tanggal, 1) as minggu, SUM(item_penjualan.jumlah) as total_qty')
            ->groupBy('item_penjualan.id_produk', 'minggu')
            ->get()
            ->groupBy('id_produk');

        // Penjualan reseller per produk per minggu (hanya pesanan SELESAI)
        $resellerSales = ItemPesanan::query()
            ->join('pesanan', 'item_pesanan.id_pesanan', '=', 'pesanan.id')
            ->join('transaksi', 'pesanan.id', '=', 'transaksi.id_pesanan')
            ->where('pesanan.status', StatusPesanan::SELESAI->value)
            ->whereBetween('transaksi.tanggal', [$startDate, $endDate])
            ->selectRaw('item_pesanan.id_produk, YEARWEEK(transaksi.tanggal, 1) as minggu, SUM(item_pesanan.jumlah) as total_qty')
            ->groupBy('item_pesanan.id_produk', 'minggu')
            ->get()
            ->groupBy('id_produk');

        // Generate daftar minggu
        $weeks = [];
        $currentWeek = $startDate->copy();
        for ($i = 0; $i < $n; $i++) {
            $yearWeek = $currentWeek->format('oW'); // ISO year + week number
            $yearWeekNum = intval($currentWeek->isoFormat('GGGG').str_pad($currentWeek->isoFormat('WW'), 2, '0', STR_PAD_LEFT));
            $weeks[] = [
                'label' => 'Mg '.($i + 1),
                'range' => $currentWeek->format('d/m').' - '.$currentWeek->copy()->endOfWeek()->format('d/m'),
                'yearweek' => $yearWeekNum,
            ];
            $currentWeek->addWeek();
        }

        // Build data per produk
        $results = collect();

        foreach ($produks as $produk) {
            $weeklyData = [];
            $totalQty = 0;

            foreach ($weeks as $week) {
                $kasirQty = 0;
                $resellerQty = 0;

                if (isset($kasirSales[$produk->id])) {
                    $found = $kasirSales[$produk->id]->firstWhere('minggu', $week['yearweek']);
                    if ($found) {
                        $kasirQty = (int) $found->total_qty;
                    }
                }

                if (isset($resellerSales[$produk->id])) {
                    $found = $resellerSales[$produk->id]->firstWhere('minggu', $week['yearweek']);
                    if ($found) {
                        $resellerQty = (int) $found->total_qty;
                    }
                }

                $qty = $kasirQty + $resellerQty;
                $weeklyData[] = $qty;
                $totalQty += $qty;
            }

            // WMA orde 3 tetap: ramalan memakai 3 minggu sebelumnya
            // dengan bobot 1 (terlama), 2, 3 (terbaru):
            // F = (1·X_{t-3} + 2·X_{t-2} + 3·X_{t-1}) / 6.
            // Bila data kurang dari 3 minggu, pakai semua yang ada (bobot 1..k).
            $orde = min(3, $n);
            $totalBobot = 0;
            $totalBerbobot = 0;
            for ($j = $n - $orde; $j < $n; $j++) {
                $bobot = $j - ($n - $orde) + 1;
                $totalBobot += $bobot;
                $totalBerbobot += $bobot * $weeklyData[$j];
            }
            $wma = $totalBobot > 0 ? round($totalBerbobot / $totalBobot, 2) : 0;
            $rekomendasiProduksi = (int) ceil($wma);

            // Metrik akurasi (MAD/MSE/MAPE) memakai evaluasi ramalan
            // satu-langkah ke depan (in-sample) dengan WMA orde 3:
            // untuk tiap minggu t = 4..N, ramalan F_t dihitung dari
            // 3 minggu sebelumnya, lalu dibandingkan dengan aktual X_t
            // yang sudah ada. Tiga minggu pertama tidak bisa diramal ("-").
            // Ramalan minggu ke-(N+1) tidak ikut karena aktualnya belum ada.
            // MAD  = rata-rata |X - F| (semakin kecil semakin akurat)
            // MSE  = rata-rata (X - F)^2 (menghukum error besar)
            // MAPE = rata-rata |X - F| / X * 100% (X = 0 dilewati agar tidak bagi nol)
            $eval = [];
            for ($t = 3; $t < $n; $t++) {
                $eval[] = [
                    'x' => $weeklyData[$t],
                    'f' => ($weeklyData[$t - 3] + 2 * $weeklyData[$t - 2] + 3 * $weeklyData[$t - 1]) / 6,
                ];
            }

            $m = count($eval);
            $mad = null;
            $mse = null;
            $mape = null;
            if ($m > 0) {
                $totalDeviasi = 0;
                $totalKuadrat = 0;
                $totalPersen = 0;
                $jumlahPersen = 0;
                foreach ($eval as $e) {
                    $selisih = abs($e['x'] - $e['f']);
                    $totalDeviasi += $selisih;
                    $totalKuadrat += $selisih ** 2;
                    if ($e['x'] != 0) {
                        $totalPersen += ($selisih / $e['x']) * 100;
                        $jumlahPersen++;
                    }
                }
                $mad = round($totalDeviasi / $m, 2);
                $mse = round($totalKuadrat / $m, 2);
                $mape = $jumlahPersen > 0 ? round($totalPersen / $jumlahPersen, 2) : null;
            }

            $results->push([
                'produk' => $produk,
                'weekly' => $weeklyData,
                'total' => $totalQty,
                'wma' => $wma,
                'mad' => $mad,
                'mse' => $mse,
                'mape' => $mape,
                'eval' => $eval,
                'rekomendasi' => $rekomendasiProduksi,
            ]);
        }

        return $results;
    }

    /**
     * Susun payload grafik: mode 'all' (perbandingan MA semua produk)
     * atau mode 'detail' (tren mingguan + garis MA satu produk).
     */
    private function buildChartData(Collection $data, array $weeks): array
    {
        $selectedId = $this->chartProdukId ? (int) $this->chartProdukId : null;
        $item = $selectedId ? $data->firstWhere(fn ($i) => $i['produk']->id === $selectedId) : null;

        if ($item) {
            $labels = array_map(fn ($w) => $w['label'], $weeks);
            $labels[] = 'Prediksi';
            $weekly = array_values($item['weekly']);

            return [
                'mode' => 'detail',
                'title' => $item['produk']->nama_produk,
                'unit' => $item['produk']->unit ?? 'pcs',
                'labels' => $labels,
                'aktual' => array_merge($weekly, [null]),
                'wma' => array_fill(0, count($labels), $item['wma']),
                'wmaValue' => $item['wma'],
            ];
        }

        return [
            'mode' => 'all',
            'labels' => $data->map(fn ($i) => $i['produk']->nama_produk)->values()->all(),
            'total' => $data->map(fn ($i) => $i['total'])->values()->all(),
            'wma' => $data->map(fn ($i) => $i['wma'])->values()->all(),
            'rekomendasi' => $data->map(fn ($i) => $i['rekomendasi'])->values()->all(),
        ];
    }

    public function simpanPrediksi()
    {
        $analisis = $this->dataAnalisis();
        $data = $analisis['data'];
        $orde = min(3, $analisis['n']);

        foreach ($data as $item) {
            MovingAverage::updateOrCreate(
                [
                    'id_produk' => $item['produk']->id,
                    'periode' => $orde,
                    'tgl_hitung' => now()->format('Y-m-d'),
                ],
                [
                    'rata_penjualan' => $item['wma'],
                    'mad' => $item['mad'] ?? 0,
                    'mse' => $item['mse'] ?? 0,
                    'mape' => $item['mape'] ?? 0,
                    'rekomendasi_produksi' => $item['rekomendasi'],
                    'created_at' => now(),
                ]
            );
        }

        session()->flash('message', 'Prediksi berhasil disimpan ke database!');
    }

    public function downloadPdf()
    {
        $analisis = $this->dataAnalisis();
        $data = $analisis['data'];
        $weeks = $analisis['weeks'];
        $startDate = $analisis['start'];
        $endDate = $analisis['end'];

        $tabelItemPdf = $data->firstWhere(fn ($i) => $i['produk']->id === (int) $this->tabelProdukId) ?? $data->first();

        $pdf = Pdf::loadView('pdf.prediksi-penjualan', [
            'data' => $data,
            'weeks' => $weeks,
            'jumlahPeriode' => $analisis['n'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isFallback' => $analisis['fallback'],
            'isManual' => $analisis['manual'],
            'tabel' => $tabelItemPdf ? $this->tabelPrediksi($tabelItemPdf, $weeks) : null,
            'nextMinggu' => [
                'label' => 'Mg '.($analisis['n'] + 1),
                'range' => $startDate->copy()->addWeeks($analisis['n'])->format('d/m').' - '.$startDate->copy()->addWeeks($analisis['n'])->endOfWeek()->format('d/m'),
            ],
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'prediksi-penjualan-wma-'.now()->format('Y-m-d').'.pdf');
    }

    public function render()
    {
        $analisis = $this->dataAnalisis();
        $data = $analisis['data'];
        $weeks = $analisis['weeks'];
        $startDate = $analisis['start'];
        $endDate = $analisis['end'];

        // Stats
        $totalProduk = $data->count();
        $avgPrediksi = $data->count() > 0 ? round($data->avg('wma'), 2) : 0;
        $madValues = $data->whereNotNull('mad');
        $avgMad = $madValues->count() > 0 ? round($madValues->avg('mad'), 2) : null;
        $totalTerjualPeriode = $data->sum('total');

        // Produk default untuk tabel per minggu = produk pertama yang valid.
        if ($data->isNotEmpty()
            && ! $data->firstWhere(fn ($i) => $i['produk']->id === (int) $this->tabelProdukId)) {
            $this->tabelProdukId = $data->first()['produk']->id;
        }
        $tabelItem = $data->firstWhere(fn ($i) => $i['produk']->id === (int) $this->tabelProdukId);
        $tabel = $tabelItem ? $this->tabelPrediksi($tabelItem, $weeks) : null;

        // Label minggu ke-(N+1) untuk baris ramalan berikutnya.
        $awalBerikutnya = $startDate->copy()->addWeeks($analisis['n']);
        $nextMinggu = [
            'label' => 'Mg '.($analisis['n'] + 1),
            'range' => $awalBerikutnya->format('d/m').' - '.$awalBerikutnya->copy()->endOfWeek()->format('d/m'),
        ];

        return view('livewire.pemilik-toko.prediksi-penjualan', [
            'data' => $data,
            'weeks' => $weeks,
            'totalProduk' => $totalProduk,
            'avgPrediksi' => $avgPrediksi,
            'avgMad' => $avgMad,
            'totalTerjualPeriode' => $totalTerjualPeriode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'nAktif' => $analisis['n'],
            'isFallback' => $analisis['fallback'],
            'isManual' => $analisis['manual'],
            'mingguTersedia' => $this->daftarMingguTersedia,
            'tabel' => $tabel,
            'nextMinggu' => $nextMinggu,
            'chartData' => $this->buildChartData($data, $weeks),
        ]);
    }
}
