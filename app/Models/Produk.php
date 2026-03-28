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

    public function getTotalStokAttribute()
    {
        return $this->persediaan_sum_jumlah ?? 0;
    }

    public function getStokTextAttribute()
    {
        $totalStok = $this->total_stok;
        $konversi = max(1, $this->tingkat_konversi ?? 1);
        $stokBesar = floor($totalStok / $konversi);
        $stokKecil = $totalStok % $konversi;

        $stokText = '';
        if ($this->unit_besar && $konversi > 1) {
            if ($stokBesar > 0)
                $stokText .= $stokBesar . ' ' . $this->unit_besar . ' ';
            if ($stokKecil > 0)
                $stokText .= $stokKecil . ' ' . ($this->unit_kecil ?? 'pcs');
            if ($stokBesar == 0 && $stokKecil == 0)
                $stokText = '0 ' . ($this->unit_kecil ?? 'pcs');
        } else {
            $stokText = $totalStok . ' ' . ($this->unit_kecil ?? 'pcs');
        }

        return trim($stokText);
    }
}
