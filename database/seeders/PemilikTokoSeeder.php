<?php

namespace Database\Seeders;

use App\Models\PemilikToko;
use Illuminate\Database\Seeder;

class PemilikTokoSeeder extends Seeder
{
    public function run(): void
    {
        PemilikToko::factory()->create([
            'nama' => 'Ayu Pemilik',
            'email' => 'pemiliktoko@gmail.com',
            'password' => 'password123',
        ]);
    }
}
