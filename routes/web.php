<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\Profile;
use App\Livewire\Admin\ComponentDocs;
use App\Livewire\AdminToko\PersediaanManagement;
use App\Livewire\AdminToko\PesananManagement;
use App\Livewire\AdminToko\ProdukManagement;
use App\Livewire\Reseller\Katalog;
use App\Livewire\Reseller\Keranjang;
use App\Livewire\Reseller\PesananSaya;
use App\Livewire\Reseller\Profil as ResellerProfil;
use App\Livewire\Kurir\PesananKurir;
use App\Livewire\Kurir\ScanQr;
use App\Livewire\Kurir\Profil as KurirProfil;
use App\Livewire\Kasir\PointOfSale;
use App\Livewire\Kasir\RiwayatPenjualan;
use App\Livewire\Kasir\Profil as KasirProfil;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Guest\LandingPage;
use App\Http\Controllers\Admin\LogoutController;

// Guest Routes
Route::get('/', LandingPage::class)->name('home');

// Auth Routes
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::prefix('admintoko')->middleware('auth:admin_toko,pemilik_toko,kasir,reseller,kurir')->group(function () {
    Route::get('/users', UserManagement::class)->name('admintoko.users');
    Route::get('/produk', ProdukManagement::class)->name('admintoko.produk');
    Route::get('/persediaan', PersediaanManagement::class)->name('admintoko.persediaan');
    Route::get('/pesanan', PesananManagement::class)->name('admintoko.pesanan');
    Route::get('/profile', \App\Livewire\Admin\Profile::class)->name('admintoko.profile');
    Route::post('/logout', [LogoutController::class, '__invoke'])->name('logout');
});

Route::prefix('reseller')->middleware('auth:reseller')->group(function () {
    Route::get('/katalog', Katalog::class)->name('reseller.katalog');
    Route::get('/keranjang', Keranjang::class)->name('reseller.keranjang');
    Route::get('/pesanan', PesananSaya::class)->name('reseller.pesanan');
    Route::get('/profil', ResellerProfil::class)->name('reseller.profil');
});

Route::prefix('kurir')->middleware('auth:kurir')->group(function () {
    Route::get('/pesanan', PesananKurir::class)->name('kurir.pesanan');
    Route::get('/scan', ScanQr::class)->name('kurir.scan');
    Route::get('/profil', KurirProfil::class)->name('kurir.profil');
});

Route::prefix('kasir')->middleware('auth:kasir')->group(function () {
    Route::get('/pos', PointOfSale::class)->name('kasir.pos');
    Route::get('/riwayat', RiwayatPenjualan::class)->name('kasir.riwayat');
    Route::get('/profil', KasirProfil::class)->name('kasir.profil');
});
