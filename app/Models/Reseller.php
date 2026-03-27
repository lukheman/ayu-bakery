<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reseller extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'reseller';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function keranjangBelanja(): HasOne
    {
        return $this->hasOne(KeranjangBelanja::class, 'id_reseller');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_reseller');
    }
}
