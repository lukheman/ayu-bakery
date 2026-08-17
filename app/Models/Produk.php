<?php

namespace App\Models;

use Carbon\Carbon;
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
        'unit',
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
        return $this->total_stok.' '.($this->unit ?? 'pcs');
    }

    public function getNearestExpiryAttribute(): ?array
    {
        $persediaan = $this->relationLoaded('persediaan')
            ? $this->persediaan->whereNotNull('tgl_exp')->sortBy('tgl_exp')->first()
            : $this->persediaan()->whereNotNull('tgl_exp')->orderBy('tgl_exp')->first();

        if (! $persediaan?->tgl_exp) {
            return null;
        }

        $tglExp = Carbon::parse($persediaan->tgl_exp)->startOfDay();
        $sisaHari = now()->startOfDay()->diffInDays($tglExp, false);

        return [
            'date' => $tglExp->format('d/m/Y'),
            'sisa_hari' => $sisaHari,
        ];
    }
}
