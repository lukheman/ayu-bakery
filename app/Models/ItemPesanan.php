<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPesanan extends Model
{
    use HasFactory;

    protected $table = 'item_pesanan';

    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'id_persediaan',
        'jumlah',
        'unit',
        'harga_satuan',
        'subtotal',
        'tgl_exp',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'tgl_exp' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
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
