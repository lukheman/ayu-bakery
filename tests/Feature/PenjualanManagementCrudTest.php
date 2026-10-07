<?php

namespace Tests\Feature;

use App\Livewire\AdminToko\PenjualanManagement;
use App\Models\ItemPenjualan;
use App\Models\Kasir;
use App\Models\PenjualanKasir;
use App\Models\Produk;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanManagementCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_crud_data_penjualan(): void
    {
        Kasir::factory()->create();
        $produks = Produk::factory()->count(2)->create();
        $this->assertCount(2, $produks);

        // READ: halaman render + data tampil
        Livewire::test(PenjualanManagement::class)
            ->assertStatus(200)
            ->assertSee('Penjualan Per Produk');

        // CREATE baris 1 (tanpa pilih kasir: otomatis kasir pertama)
        Livewire::test(PenjualanManagement::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('tanggal', '2026-02-01')
            ->set('metode_pembayaran', 'tunai')
            ->set('id_produk', $produks[0]->id)
            ->set('harga', 10000)
            ->set('jumlah', 5)
            ->call('save')
            ->assertHasNoErrors();

        // CREATE baris 2 tanggal sama -> menempel ke transaksi yang sama
        Livewire::test(PenjualanManagement::class)
            ->call('openCreateModal')
            ->set('tanggal', '2026-02-01')
            ->set('id_produk', $produks[1]->id)
            ->set('harga', 12000)
            ->set('jumlah', 3)
            ->call('save')
            ->assertHasNoErrors();

        // whereDate: kolom date di sqlite tersimpan sebagai datetime string.
        $this->assertEquals(1, PenjualanKasir::whereDate('tanggal', '2026-02-01')->count());
        $penjualan = PenjualanKasir::whereDate('tanggal', '2026-02-01')->first();
        $this->assertStringStartsWith('STR-20260201-', $penjualan->nomor_struk);
        $this->assertEquals(5 * 10000 + 3 * 12000, $penjualan->total);
        $this->assertEquals(2, $penjualan->items()->count());

        $baris1 = ItemPenjualan::where('id_penjualan', $penjualan->id)->orderBy('id')->first();

        // Validasi: jumlah minimal 1
        Livewire::test(PenjualanManagement::class)
            ->call('openCreateModal')
            ->set('tanggal', '2026-02-02')
            ->set('id_produk', $produks[0]->id)
            ->set('jumlah', 0)
            ->call('save')
            ->assertHasErrors(['jumlah']);

        // UPDATE: ubah qty baris 1 dari 5 -> 7, total induk ikut berubah
        Livewire::test(PenjualanManagement::class)
            ->call('openEditModal', $baris1->id)
            ->assertSet('editingItemId', $baris1->id)
            ->set('jumlah', 7)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(7 * 10000 + 3 * 12000, $penjualan->refresh()->total);

        // PINDAH TANGGAL: baris 1 ke 2026-02-05 -> transaksi baru dibuat
        Livewire::test(PenjualanManagement::class)
            ->call('openEditModal', $baris1->id)
            ->set('tanggal', '2026-02-05')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(1, PenjualanKasir::whereDate('tanggal', '2026-02-01')->count());
        $this->assertEquals(3 * 12000, PenjualanKasir::whereDate('tanggal', '2026-02-01')->first()->total);
        $transaksiBaru = PenjualanKasir::whereDate('tanggal', '2026-02-05')->first();
        $this->assertNotNull($transaksiBaru);
        $this->assertEquals(7 * 10000, $transaksiBaru->total);

        // DELETE per baris: hapus semua baris -> transaksi kosong ikut terhapus
        foreach (ItemPenjualan::pluck('id')->all() as $itemId) {
            Livewire::test(PenjualanManagement::class)
                ->call('confirmDelete', $itemId)
                ->call('deleteItem')
                ->assertHasNoErrors();
        }

        $this->assertEquals(0, ItemPenjualan::count());
        $this->assertEquals(0, PenjualanKasir::count());
    }

    public function test_import_excel_dari_menu_data_penjualan(): void
    {
        Kasir::factory()->create();
        $produks = Produk::factory()->count(2)->create();

        // Modal import terbuka/tutup + validasi file wajib.
        Livewire::test(PenjualanManagement::class)
            ->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->call('importExcel')
            ->assertHasErrors(['fileExcel'])
            ->call('closeImportModal')
            ->assertSet('showImportModal', false);

        // Unduh template: header format lebar.
        $export = new \App\Exports\PenjualanTemplateExport;
        $headings = $export->headings();
        $this->assertEquals('Tanggal', $headings[0]);
        $this->assertContains('Terjual '.$produks[0]->nama_produk, $headings);
        $this->assertContains('Sisa Akhir', $headings);

        // Import format lebar: hanya kolom Terjual yang masuk penjualan.
        $baris = collect([
            'tanggal' => '2026-03-01',
            'sisa_hari_sebelumnya' => 0,
            'total_terjual' => 130,
            'sisa_akhir' => 20,
        ]);
        foreach ($produks as $i => $produk) {
            $slug = strtolower(str_replace(' ', '_', $produk->nama_produk));
            $baris['produksi_'.$slug] = $i === 0 ? 100 : 50;
            $baris['terjual_'.$slug] = $i === 0 ? 90 : 40;
        }

        (new \App\Imports\PenjualanImport)->collection(collect([$baris]));

        $penjualan = PenjualanKasir::whereDate('tanggal', '2026-03-01')->latest('id')->first();
        $this->assertNotNull($penjualan);
        $this->assertEquals([40, 90], $penjualan->items()->orderBy('jumlah')->pluck('jumlah')->all());
    }
}
