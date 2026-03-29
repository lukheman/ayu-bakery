<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PenjualanKasir;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CetakStrukController extends Controller
{
    public function __invoke(string $nomor_struk)
    {
        $penjualan = PenjualanKasir::with(['items', 'kasir'])
            ->where('nomor_struk', $nomor_struk)
            ->firstOrFail();

        // Lebar kertas thermal 80mm ~ 226.77 pt
        // Panjang diset auto (tinggi cukup panjang agar konten tidak terpotong)
        $pdf = Pdf::loadView('pdf.struk-kasir', compact('penjualan'))
            ->setPaper(array(0, 0, 226.77, 600));

        return $pdf->stream('Struk_' . $penjualan->nomor_struk . '.pdf');
    }
}
