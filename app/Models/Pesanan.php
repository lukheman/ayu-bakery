<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'id_reseller',
        'id_keranjang',
        'status',
        'catatan',
        'kode_konfirmasi',
    ];

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class, 'id_reseller');
    }

    public function keranjangBelanja(): BelongsTo
    {
        return $this->belongsTo(KeranjangBelanja::class, 'id_keranjang');
    }

    public function itemPesanan(): HasMany
    {
        return $this->hasMany(ItemPesanan::class, 'id_pesanan');
    }

    public function transaksi(): HasOne
    {
        return $this->hasOne(Transaksi::class, 'id_pesanan');
    }
}
