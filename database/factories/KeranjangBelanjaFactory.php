<?php

namespace Database\Factories;

use App\Models\KeranjangBelanja;
use App\Models\Reseller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KeranjangBelanja>
 */
class KeranjangBelanjaFactory extends Factory
{
    protected $model = KeranjangBelanja::class;

    public function definition(): array
    {
        return [
            'id_reseller' => Reseller::factory(),
        ];
    }
}
