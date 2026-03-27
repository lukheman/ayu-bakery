<?php

namespace Database\Factories;

use App\Enums\StatusPesanan;
use App\Models\KeranjangBelanja;
use App\Models\Pesanan;
use App\Models\Reseller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pesanan>
 */
class PesananFactory extends Factory
{
    protected $model = Pesanan::class;

    public function definition(): array
    {
        return [
            'id_reseller' => Reseller::factory(),
            'id_keranjang' => KeranjangBelanja::factory(),
            'status' => fake()->randomElement(StatusPesanan::values()),
            'catatan' => fake()->optional(0.5)->sentence(),
        ];
    }
}
