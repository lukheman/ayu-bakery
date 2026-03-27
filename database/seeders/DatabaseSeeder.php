<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
                // 1. Independent user tables
            AdminTokoSeeder::class,
            PemilikTokoSeeder::class,
            KasirSeeder::class,
            KurirSeeder::class,

                // 2. Reseller + shopping carts
            ResellerSeeder::class,

                // 3. Products
            ProdukSeeder::class,

                // 4. Inventory
            PersediaanSeeder::class,
            MutasiStokSeeder::class,
            MovingAverageSeeder::class,

                // 5. Cart items
            KeranjangBelanjaSeeder::class,

                // 6. Orders → Items → Transactions → Reports
            PesananSeeder::class,
        ]);
    }
}
