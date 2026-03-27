<?php

namespace Database\Factories;

use App\Models\Kasir;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kasir>
 */
class KasirFactory extends Factory
{
    protected $model = Kasir::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'no_hp' => fake()->phoneNumber(),
            'foto' => null,
        ];
    }
}
