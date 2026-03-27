<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PemilikToko extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pemilik_toko';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
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
}
