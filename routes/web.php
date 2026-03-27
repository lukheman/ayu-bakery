<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\Profile;
use App\Livewire\Admin\ComponentDocs;
use App\Livewire\AdminToko\ProdukManagement;
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
    Route::get('/profile', Profile::class)->name('admintoko.profile');
    Route::get('/produk', ProdukManagement::class)->name('admintoko.produk');
    Route::post('/logout', [LogoutController::class, '__invoke'])->name('logout');
});
