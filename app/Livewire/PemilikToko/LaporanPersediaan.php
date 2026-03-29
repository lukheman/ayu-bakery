<?php

namespace App\Livewire\PemilikToko;

use App\Enums\StatusExp;
use App\Models\Persediaan;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Persediaan - Ayu Bakery')]
#[Layout('layouts.app')]
class LaporanPersediaan extends Component
{
    public string $search = '';
    public string $filterStatus = '';

    public function downloadPdf()
    {
        $produks = Produk::query()
            ->withSum('persediaan', 'jumlah')
            ->with([
                'persediaan' => function ($query) {
                    $query->orderBy('tgl_exp', 'asc');
                }
            ])
            ->orderBy('nama_produk')
            ->get();

        $totalProduk = Produk::count();
        $totalStok = Persediaan::sum('jumlah');
        $totalHampirExp = Persediaan::where('status_exp', StatusExp::HAMPIR_EXP->value)->where('jumlah', '>', 0)->count();
        $totalExpired = Persediaan::where('status_exp', StatusExp::EXPIRED->value)->where('jumlah', '>', 0)->count();

        $pdf = Pdf::loadView('pdf.laporan-persediaan', [
            'produks' => $produks,
            'totalProduk' => $totalProduk,
            'totalStok' => $totalStok,
            'totalHampirExp' => $totalHampirExp,
            'totalExpired' => $totalExpired,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-persediaan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $produks = Produk::query()
            ->withSum('persediaan', 'jumlah')
            ->with([
                'persediaan' => function ($query) {
                    if ($this->filterStatus) {
                        $query->where('status_exp', $this->filterStatus);
                    }
                    $query->orderBy('tgl_exp', 'asc');
                }
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama_produk', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_produk', 'like', '%' . $this->search . '%')
                        ->orWhere('varian_rasa', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->whereHas('persediaan', function ($q) {
                    $q->where('status_exp', $this->filterStatus);
                });
            })
            ->orderBy('nama_produk')
            ->get();

        // Statistics
        $totalProduk = Produk::count();
        $totalStok = Persediaan::sum('jumlah');
        $totalHampirExp = Persediaan::where('status_exp', StatusExp::HAMPIR_EXP->value)->where('jumlah', '>', 0)->count();
        $totalExpired = Persediaan::where('status_exp', StatusExp::EXPIRED->value)->where('jumlah', '>', 0)->count();

        return view('livewire.pemilik-toko.laporan-persediaan', [
            'produks' => $produks,
            'totalProduk' => $totalProduk,
            'totalStok' => $totalStok,
            'totalHampirExp' => $totalHampirExp,
            'totalExpired' => $totalExpired,
        ]);
    }
}
