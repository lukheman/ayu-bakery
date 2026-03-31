<?php

namespace App\Livewire\PemilikToko;

use App\Enums\StatusPesanan;
use App\Models\ItemPenjualan;
use App\Models\ItemPesanan;
use App\Models\MovingAverage;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Prediksi Penjualan (Moving Average) - Ayu Bakery')]
#[Layout('layouts.app')]
class PrediksiPenjualan extends Component
{
    public int $jumlahPeriode = 4;
    public string $search = '';

    public function updatedJumlahPeriode(): void
    {
        if ($this->jumlahPeriode < 2) {
            $this->jumlahPeriode = 2;
        }
        if ($this->jumlahPeriode > 12) {
            $this->jumlahPeriode = 12;
        }
    }

    /**
     * Hitung penjualan per produk per minggu.
     * Menggabungkan data dari item_penjualan (kasir) dan item_pesanan (reseller selesai).
     */
    private function hitungDataMingguan(): Collection
    {
        $n = $this->jumlahPeriode;

        // Tentukan rentang waktu: N minggu terakhir dari hari ini
        $endDate = Carbon::now()->endOfWeek();
        $startDate = Carbon::now()->subWeeks($n)->startOfWeek();

        // Ambil semua produk
        $produks = Produk::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('nama_produk', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_produk', 'like', '%' . $this->search . '%')
                        ->orWhere('varian_rasa', 'like', '%' . $this->search . '%');
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
            $yearWeekNum = intval($currentWeek->isoFormat('GGGG') . str_pad($currentWeek->isoFormat('WW'), 2, '0', STR_PAD_LEFT));
            $weeks[] = [
                'label' => 'Mg ' . ($i + 1),
                'range' => $currentWeek->format('d/m') . ' - ' . $currentWeek->copy()->endOfWeek()->format('d/m'),
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

            $ma = $n > 0 ? round($totalQty / $n, 2) : 0;
            $rekomendasiProduksi = (int) ceil($ma);

            $results->push([
                'produk' => $produk,
                'weekly' => $weeklyData,
                'total' => $totalQty,
                'ma' => $ma,
                'rekomendasi' => $rekomendasiProduksi,
            ]);
        }

        return $results;
    }

    public function simpanPrediksi()
    {
        $data = $this->hitungDataMingguan();

        foreach ($data as $item) {
            MovingAverage::updateOrCreate(
                [
                    'id_produk' => $item['produk']->id,
                    'periode' => $this->jumlahPeriode,
                    'tgl_hitung' => now()->format('Y-m-d'),
                ],
                [
                    'rata_penjualan' => $item['ma'],
                    'rekomendasi_produksi' => $item['rekomendasi'],
                    'created_at' => now(),
                ]
            );
        }

        session()->flash('message', 'Prediksi berhasil disimpan ke database!');
    }

    public function downloadPdf()
    {
        $data = $this->hitungDataMingguan();

        $n = $this->jumlahPeriode;
        $endDate = Carbon::now()->endOfWeek();
        $startDate = Carbon::now()->subWeeks($n)->startOfWeek();

        // Generate weeks for PDF header
        $weeks = [];
        $currentWeek = $startDate->copy();
        for ($i = 0; $i < $n; $i++) {
            $weeks[] = [
                'label' => 'Mg ' . ($i + 1),
                'range' => $currentWeek->format('d/m') . ' - ' . $currentWeek->copy()->endOfWeek()->format('d/m'),
            ];
            $currentWeek->addWeek();
        }

        $pdf = Pdf::loadView('pdf.prediksi-penjualan', [
            'data' => $data,
            'weeks' => $weeks,
            'jumlahPeriode' => $this->jumlahPeriode,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'prediksi-penjualan-ma-' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $data = $this->hitungDataMingguan();

        $n = $this->jumlahPeriode;
        $endDate = Carbon::now()->endOfWeek();
        $startDate = Carbon::now()->subWeeks($n)->startOfWeek();

        // Generate weeks for view header
        $weeks = [];
        $currentWeek = $startDate->copy();
        for ($i = 0; $i < $n; $i++) {
            $weeks[] = [
                'label' => 'Mg ' . ($i + 1),
                'range' => $currentWeek->format('d/m') . ' - ' . $currentWeek->copy()->endOfWeek()->format('d/m'),
            ];
            $currentWeek->addWeek();
        }

        // Stats
        $totalProduk = $data->count();
        $avgPrediksi = $data->count() > 0 ? round($data->avg('ma'), 2) : 0;
        $totalRekomendasi = $data->sum('rekomendasi');

        return view('livewire.pemilik-toko.prediksi-penjualan', [
            'data' => $data,
            'weeks' => $weeks,
            'totalProduk' => $totalProduk,
            'avgPrediksi' => $avgPrediksi,
            'totalRekomendasi' => $totalRekomendasi,
        ]);
    }
}
