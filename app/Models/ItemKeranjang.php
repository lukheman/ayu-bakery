<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemKeranjang extends Model
{
    use HasFactory;

    protected $table = 'item_keranjang';

    public $timestamps = false;

    protected $fillable = [
        'id_keranjang',
        'id_produk',
        'jumlah',
        'unit',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function keranjangBelanja(): BelongsTo
    {
        return $this->belongsTo(KeranjangBelanja::class, 'id_keranjang');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
