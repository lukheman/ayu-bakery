<?php

namespace Database\Seeders;

use App\Models\Kasir;
use Illuminate\Database\Seeder;

class KasirSeeder extends Seeder
{
    public function run(): void
    {
        Kasir::factory()->create([
            'nama' => 'Kasir Utama',
            'email' => 'kasir@gmail.com',
            'password' => 'password123',
        ]);

        Kasir::factory(2)->create();
    }
}
