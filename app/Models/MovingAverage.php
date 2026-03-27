<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovingAverage extends Model
{
    use HasFactory;

    protected $table = 'moving_average';

    public $timestamps = false;

    protected $fillable = [
        'id_produk',
        'periode',
        'rata_penjualan',
        'rekomendasi_produksi',
        'tgl_hitung',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'tgl_hitung' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
