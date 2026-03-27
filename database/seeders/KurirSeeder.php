<?php

namespace Database\Seeders;

use App\Models\Kurir;
use Illuminate\Database\Seeder;

class KurirSeeder extends Seeder
{
    public function run(): void
    {
        Kurir::factory()->create([
            'nama' => 'Kurir Utama',
            'email' => 'kurir@gmail.com',
            'password' => 'password123',
        ]);

        Kurir::factory(2)->create();
    }
}
