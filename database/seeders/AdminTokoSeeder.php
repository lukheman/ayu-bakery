<?php

namespace Database\Seeders;

use App\Models\AdminToko;
use Illuminate\Database\Seeder;

class AdminTokoSeeder extends Seeder
{
    public function run(): void
    {
        AdminToko::factory()->create([
            'nama' => 'Admin Ayu Bakery',
            'email' => 'admintoko@gmail.com',
            'password' => 'password123',
        ]);

        AdminToko::factory(2)->create();
    }
}
