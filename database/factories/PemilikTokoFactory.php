<?php

namespace Database\Factories;

use App\Models\PemilikToko;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PemilikToko>
 */
class PemilikTokoFactory extends Factory
{
    protected $model = PemilikToko::class;

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
