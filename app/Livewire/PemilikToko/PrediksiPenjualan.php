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
    public int $jumlahPeriode = 4;

    public string $search = '';

    /** Pilihan manual: minggu awal & akhir (format Y-m-d, Senin). Kosong = otomatis. */
    public ?string $mingguDari = null;

    public ?string $mingguSampai = null;

    public $chartProdukId = null;

    public function updatedJumlahPeriode(): void
    {
        if ($this->jumlahPeriode < 2) {
            $this->jumlahPeriode = 2;
        }
        if ($this->jumlahPeriode > 12) {
            $this->jumlahPeriode = 12;
        }
        // Jumlah periode = mode otomatis N minggu terakhir.
        $this->reset(['mingguDari', 'mingguSampai']);
    }

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
     * Rentang primer: N minggu dijangkarkan ke tanggal penjualan terbaru
     * (kasir / reseller selesai). Tanpa penjangkaran, bila data terakhir
     * lebih lama dari hari ini (mis. data impor Januari dibuka kembali
     * berbulan-bulan kemudian), seluruh grafik dan tabel berisi nol dan
     * grafik terlihat kosong.
     *
     * @return array{0: Carbon, 1: Carbon} [startDate, endDate]
     */
    private function rentangPrimer(): array
    {
        $n = $this->jumlahPeriode;

        $kandidat = [];
        $terakhirKasir = PenjualanKasir::max('tanggal');
        if ($terakhirKasir) {
            $kandidat[] = Carbon::parse($terakhirKasir);
        }
        $terakhirReseller = Transaksi::query()
            ->whereHas('pesanan', fn ($q) => $q->where('status', StatusPesanan::SELESAI->value))
            ->max('tanggal');
        if ($terakhirReseller) {
            $kandidat[] = Carbon::parse($terakhirReseller);
        }

        $jangkar = null;
        foreach ($kandidat as $tanggal) {
            if (! $jangkar || $tanggal->gt($jangkar)) {
                $jangkar = $tanggal;
            }
        }

        $acuan = ($jangkar && $jangkar->lessThan(Carbon::now())) ? $jangkar : Carbon::now();

        return [$acuan->copy()->subWeeks($n)->startOfWeek(), $acuan->copy()->endOfWeek()];
    }

    /**
     * Daftar seluruh minggu kalender dari penjualan paling lama sampai
     * paling baru, untuk opsi pilihan manual Dari/Sampai Minggu.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function getDaftarMingguTersediaProperty(): array
    {
        $batas = collect([
            PenjualanKasir::min('tanggal'),
            Transaksi::query()
                ->whereHas('pesanan', fn ($q) => $q->where('status', StatusPesanan::SELESAI->value))
                ->min('tanggal'),
            PenjualanKasir::max('tanggal'),
            Transaksi::query()
                ->whereHas('pesanan', fn ($q) => $q->where('status', StatusPesanan::SELESAI->value))
                ->max('tanggal'),
        ])->filter();

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
     * Bila terbalik, otomatis dibalik. Maksimal 12 minggu (dipotong dari depan).
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

        $n = $dari->diffInWeeks($sampai) + 1;
        if ($n > 12) {
            $n = 12;
            $dari = $sampai->copy()->subWeeks($n - 1);
        }

        return [$dari, $n];
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

        $n = $this->jumlahPeriode;

        [$start, $end] = $this->rentangPrimer();
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
        $kasirMin = PenjualanKasir::min('tanggal');
        $resellerMin = Transaksi::query()
            ->whereHas('pesanan', fn ($q) => $q->where('status', StatusPesanan::SELESAI->value))
            ->min('tanggal');
        $kasirMax = PenjualanKasir::max('tanggal');
        $resellerMax = Transaksi::query()
            ->whereHas('pesanan', fn ($q) => $q->where('status', StatusPesanan::SELESAI->value))
            ->max('tanggal');

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

            // WMA (Weighted Moving Average): minggu terbaru diberi bobot terbesar.
            // Bobot linear 1..N (X1 tertua bobot 1, XN terbaru bobot N).
            $totalBobot = 0;
            $totalBerbobot = 0;
            foreach ($weeklyData as $i => $qty) {
                $bobot = $i + 1;
                $totalBobot += $bobot;
                $totalBerbobot += $bobot * $qty;
            }
            $wma = $totalBobot > 0 ? round($totalBerbobot / $totalBobot, 2) : 0;
            $rekomendasiProduksi = (int) ceil($wma);

            // Metrik akurasi memakai WMA sebagai nilai prediksi (F):
            // MAD  = rata-rata |X - F| (semakin kecil semakin akurat)
            // MSE  = rata-rata (X - F)^2 (menghukum error besar)
            // MAPE = rata-rata |X - F| / X * 100% (X = 0 dilewati agar tidak bagi nol)
            $totalDeviasi = 0;
            $totalKuadrat = 0;
            $totalPersen = 0;
            $jumlahPersen = 0;
            foreach ($weeklyData as $qty) {
                $selisih = abs($qty - $wma);
                $totalDeviasi += $selisih;
                $totalKuadrat += $selisih ** 2;
                if ($qty != 0) {
                    $totalPersen += ($selisih / $qty) * 100;
                    $jumlahPersen++;
                }
            }
            $mad = $n > 0 ? round($totalDeviasi / $n, 2) : 0;
            $mse = $n > 0 ? round($totalKuadrat / $n, 2) : 0;
            $mape = $jumlahPersen > 0 ? round($totalPersen / $jumlahPersen, 2) : 0;

            $results->push([
                'produk' => $produk,
                'weekly' => $weeklyData,
                'total' => $totalQty,
                'wma' => $wma,
                'mad' => $mad,
                'mse' => $mse,
                'mape' => $mape,
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

        foreach ($data as $item) {
            MovingAverage::updateOrCreate(
                [
                    'id_produk' => $item['produk']->id,
                    'periode' => $analisis['n'],
                    'tgl_hitung' => now()->format('Y-m-d'),
                ],
                [
                    'rata_penjualan' => $item['wma'],
                    'mad' => $item['mad'],
                    'mse' => $item['mse'],
                    'mape' => $item['mape'],
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

        $pdf = Pdf::loadView('pdf.prediksi-penjualan', [
            'data' => $data,
            'weeks' => $weeks,
            'jumlahPeriode' => $analisis['n'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isFallback' => $analisis['fallback'],
            'isManual' => $analisis['manual'],
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
        $avgMad = $data->count() > 0 ? round($data->avg('mad'), 2) : 0;
        $totalRekomendasi = $data->sum('rekomendasi');
        $totalTerjualPeriode = $data->sum('total');

        return view('livewire.pemilik-toko.prediksi-penjualan', [
            'data' => $data,
            'weeks' => $weeks,
            'totalProduk' => $totalProduk,
            'avgPrediksi' => $avgPrediksi,
            'avgMad' => $avgMad,
            'totalRekomendasi' => $totalRekomendasi,
            'totalTerjualPeriode' => $totalTerjualPeriode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'nAktif' => $analisis['n'],
            'isFallback' => $analisis['fallback'],
            'isManual' => $analisis['manual'],
            'mingguTersedia' => $this->daftarMingguTersedia,
            'chartData' => $this->buildChartData($data, $weeks),
        ]);
    }
}
