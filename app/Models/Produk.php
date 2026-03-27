<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'kode_produk',
        'varian_rasa',
        'harga_jual',
        'harga_jual_satuan',
        'unit_besar',
        'unit_kecil',
        'tingkat_konversi',
        'deskripsi',
        'gambar',
    ];

    public function persediaan(): HasMany
    {
        return $this->hasMany(Persediaan::class, 'id_produk');
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'id_produk');
    }

    public function movingAverage(): HasMany
    {
        return $this->hasMany(MovingAverage::class, 'id_produk');
    }

    public function itemKeranjang(): HasMany
    {
        return $this->hasMany(ItemKeranjang::class, 'id_produk');
    }

    public function itemPesanan(): HasMany
    {
        return $this->hasMany(ItemPesanan::class, 'id_produk');
    }
}
