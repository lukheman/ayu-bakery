<?php

namespace Database\Factories;

use App\Enums\StatusExp;
use App\Models\Persediaan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Persediaan>
 */
class PersediaanFactory extends Factory
{
    protected $model = Persediaan::class;

    public function definition(): array
    {
        $tglProduksi = fake()->dateTimeBetween('-30 days', 'now');
        $tglExp = fake()->dateTimeBetween('+7 days', '+90 days');
        $sisaHari = now()->diffInDays($tglExp, false);

        return [
            'id_produk' => Produk::factory(),
            'jumlah' => fake()->numberBetween(10, 200),
            'tgl_produksi' => $tglProduksi,
            'tgl_exp' => $tglExp,
            'sisa_hari' => max(0, $sisaHari),
            'status_exp' => $sisaHari > 14 ? StatusExp::AMAN : ($sisaHari > 3 ? StatusExp::HAMPIR_EXP : StatusExp::EXPIRED),
        ];
    }
}
