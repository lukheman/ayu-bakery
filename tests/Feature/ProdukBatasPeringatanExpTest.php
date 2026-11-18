<?php

use App\Enums\StatusExp;
use App\Livewire\AdminToko\PersediaanManagement;
use App\Livewire\AdminToko\ProdukManagement;
use App\Models\Persediaan;
use App\Models\Produk;
use Livewire\Livewire;

test('produk menyimpan batas peringatan expired yang diatur pengguna', function () {
    Livewire::test(ProdukManagement::class)
        ->call('openCreateModal')
        ->set('nama_produk', 'Pizza')
        ->set('kode_produk', 'PRD-PIZZA')
        ->set('harga_jual', 50000)
        ->set('unit', 'Pcs')
        ->set('batas_peringatan_exp', 1)
        ->call('save')
        ->assertHasNoErrors();

    expect(Produk::where('kode_produk', 'PRD-PIZZA')->first()->batas_peringatan_exp)->toBe(1);
});

test('status persediaan mengikuti batas peringatan masing masing produk', function (int $batas, int $sisaHari, StatusExp $expected) {
    $produk = Produk::factory()->create(['batas_peringatan_exp' => $batas]);

    Livewire::test(PersediaanManagement::class)
        ->call('openCreateModal')
        ->set('id_produk', $produk->id)
        ->set('jumlah', 10)
        ->set('tgl_produksi', now()->subDays(5)->toDateString())
        ->set('tgl_exp', now()->addDays($sisaHari)->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    expect(Persediaan::where('id_produk', $produk->id)->first()->status_exp)->toBe($expected);
})->with([
    'pizza peringatan 1 hari' => [1, 1, StatusExp::HAMPIR_EXP],
    'roti peringatan 3 hari' => [3, 3, StatusExp::HAMPIR_EXP],
    'roti masih aman' => [3, 4, StatusExp::AMAN],
]);

test('mengubah batas peringatan produk menyinkronkan status persediaan', function () {
    $produk = Produk::factory()->create(['batas_peringatan_exp' => 1]);
    $persediaan = Persediaan::factory()->create([
        'id_produk' => $produk->id,
        'tgl_exp' => now()->addDays(2)->toDateString(),
        'sisa_hari' => 2,
        'status_exp' => StatusExp::AMAN,
    ]);

    Livewire::test(ProdukManagement::class)
        ->call('openEditModal', $produk->id)
        ->set('batas_peringatan_exp', 3)
        ->call('save')
        ->assertHasNoErrors();

    expect($persediaan->refresh()->status_exp)->toBe(StatusExp::HAMPIR_EXP);
});
