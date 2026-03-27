<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'id_pesanan',
        'id_kasir',
        'id_kurir',
        'metode_pembayaran',
        'bukti_pembayaran',
        'total_bayar',
        'status_pembayaran',
        'status_pengiriman',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Kasir::class, 'id_kasir');
    }

    public function kurir(): BelongsTo
    {
        return $this->belongsTo(Kurir::class, 'id_kurir');
    }

    public function laporanPenjualan(): HasMany
    {
        return $this->hasMany(LaporanPenjualan::class, 'id_transaksi');
    }
}
