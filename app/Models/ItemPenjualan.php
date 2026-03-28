<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPenjualan extends Model
{
    use HasFactory;

    protected $table = 'item_penjualan';

    protected $fillable = [
        'id_penjualan',
        'id_produk',
        'id_persediaan',
        'nama_produk',
        'harga',
        'jumlah',
        'subtotal',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(PenjualanKasir::class, 'id_penjualan');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function persediaan(): BelongsTo
    {
        return $this->belongsTo(Persediaan::class, 'id_persediaan');
    }
}
