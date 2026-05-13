<?php

namespace Database\Seeders;

use App\Models\Reseller;
use App\Models\KeranjangBelanja;
use Illuminate\Database\Seeder;

class ResellerSeeder extends Seeder
{
    public function run(): void
    {

        Reseller::factory()->create([
            'nama' => 'Reseller 1',
            'email' => 'reseller1@gmail.com',
            'password' => 'password123',
        ]);

        Reseller::factory()->create([
            'nama' => 'Reseller 2',
            'email' => 'reseller2@gmail.com',
            'password' => 'password123',
        ]);

        $resellers = Reseller::all();

        // Create a shopping cart for each reseller
        $resellers->each(function (Reseller $reseller) {
            KeranjangBelanja::factory()->create([
                'id_reseller' => $reseller->id,
            ]);
        });
    }
}
