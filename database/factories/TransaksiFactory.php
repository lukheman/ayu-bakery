<?php

namespace Database\Factories;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPengiriman;
use App\Models\Kasir;
use App\Models\Kurir;
use App\Models\Pesanan;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaksi>
 */
class TransaksiFactory extends Factory
{
    protected $model = Transaksi::class;

    public function definition(): array
    {
        return [
            'id_pesanan' => Pesanan::factory(),
            'id_kasir' => Kasir::factory(),
            'id_kurir' => Kurir::factory(),
            'metode_pembayaran' => fake()->randomElement(MetodePembayaran::values()),
            'bukti_pembayaran' => null,
            'total_bayar' => fake()->numberBetween(50000, 500000),
            'status_pembayaran' => fake()->randomElement(StatusPembayaran::values()),
            'status_pengiriman' => fake()->randomElement(StatusPengiriman::values()),
            'tanggal' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
