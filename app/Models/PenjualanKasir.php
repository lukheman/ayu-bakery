<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenjualanKasir extends Model
{
    use HasFactory;

    protected $table = 'penjualan_kasir';

    protected $fillable = [
        'id_kasir',
        'nomor_struk',
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'metode_pembayaran' => \App\Enums\MetodePembayaran::class,
        ];
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(Kasir::class, 'id_kasir');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPenjualan::class, 'id_penjualan');
    }
}
