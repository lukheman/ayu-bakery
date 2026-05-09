<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CetakNotaPesananController extends Controller
{
    public function __invoke(int $id)
    {
        $pesanan = Pesanan::with(['reseller', 'itemPesanan.produk', 'transaksi.kurir'])
            ->findOrFail($id);

        // Generate QR code as SVG, lalu konversi ke base64 data URI agar kompatibel dengan DomPDF
        $qrCodeSvg = QrCode::format('svg')
            ->size(150)
            ->errorCorrection('H')
            ->generate($pesanan->kode_konfirmasi ?? 'N/A');

        $qrCodeBase64 = 'data:image/svg+xml;base64,'.base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.nota-pesanan', [
            'pesanan' => $pesanan,
            'qrCodeBase64' => $qrCodeBase64,
        ])->setPaper([0, 0, 226.77, 750]); // Lebar kertas thermal 80mm

        return $pdf->stream('Nota_Pesanan_'.str_pad($pesanan->id, 5, '0', STR_PAD_LEFT).'.pdf');
    }
}

