<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeranjangBelanja extends Model
{
    use HasFactory;

    protected $table = 'keranjang_belanja';

    protected $fillable = [
        'id_reseller',
    ];

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class, 'id_reseller');
    }

    public function itemKeranjang(): HasMany
    {
        return $this->hasMany(ItemKeranjang::class, 'id_keranjang');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_keranjang');
    }
}
