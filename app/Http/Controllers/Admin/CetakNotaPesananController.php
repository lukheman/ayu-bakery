<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakNotaPesananController extends Controller
{
    public function __invoke(int $id)
    {
        $pesanan = Pesanan::with(['reseller', 'itemPesanan.produk', 'transaksi.kurir'])
            ->findOrFail($id);

        // Generate QR code matrix menggunakan BaconQrCode encoder langsung
        // Pendekatan ini menghasilkan HTML table yang pasti kompatibel dengan DomPDF
        $qrMatrix = $this->generateQrMatrix($pesanan->kode_konfirmasi ?? 'N/A');

        $pdf = Pdf::loadView('pdf.nota-pesanan', [
            'pesanan' => $pesanan,
            'qrMatrix' => $qrMatrix,
        ])->setPaper([0, 0, 226.77, 750]); // Lebar kertas thermal 80mm

        return $pdf->stream('Nota_Pesanan_'.str_pad($pesanan->id, 5, '0', STR_PAD_LEFT).'.pdf');
    }

    /**
     * Generate QR code sebagai array matrix (2D array of boolean).
     *
     * @return array<int, array<int, bool>>
     */
    private function generateQrMatrix(string $content): array
    {
        $encoder = Encoder::encode(
            $content,
            ErrorCorrectionLevel::H
        );

        $byteMatrix = $encoder->getMatrix();
        $width = $byteMatrix->getWidth();
        $height = $byteMatrix->getHeight();

        $matrix = [];
        for ($y = 0; $y < $height; $y++) {
            $row = [];
            for ($x = 0; $x < $width; $x++) {
                $row[] = $byteMatrix->get($x, $y) === 1;
            }
            $matrix[] = $row;
        }

        return $matrix;
    }
}
