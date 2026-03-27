<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persediaan extends Model
{
    use HasFactory;

    protected $table = 'persediaan';

    protected $fillable = [
        'id_produk',
        'jumlah',
        'unit',
        'tgl_produksi',
        'tgl_exp',
        'sisa_hari',
        'status_exp',
    ];

    protected function casts(): array
    {
        return [
            'tgl_produksi' => 'date',
            'tgl_exp' => 'date',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'id_persediaan');
    }

    public function itemPesanan(): HasMany
    {
        return $this->hasMany(ItemPesanan::class, 'id_persediaan');
    }
}
