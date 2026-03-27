<?php

namespace Database\Factories;

use App\Models\AdminToko;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminToko>
 */
class AdminTokoFactory extends Factory
{
    protected $model = AdminToko::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'no_hp' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'foto' => null,
        ];
    }
}
