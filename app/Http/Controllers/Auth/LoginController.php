<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected array $guardMap = [
        'admin_toko' => 'admin_toko',
        'pemilik_toko' => 'pemilik_toko',
        'kasir' => 'kasir',
        'reseller' => 'reseller',
        'kurir' => 'kurir',
    ];

    public function showLoginForm()
    {
        $roleOptions = [
            'admin_toko' => 'Admin Toko',
            'pemilik_toko' => 'Pemilik Toko',
            'kasir' => 'Kasir',
            'reseller' => 'Reseller',
            'kurir' => 'Kurir',
        ];
        return view('auth.login', ['type' => 'auth', 'roleOptions' => $roleOptions]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'role' => ['required', 'in:' . implode(',', array_keys($this->guardMap))],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'role.required' => 'Silakan pilih role Anda.',
            'role.in' => 'Role yang dipilih tidak valid.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $guard = $this->guardMap[$request->role];
        $remember = $request->has('remember');

        if (
            Auth::guard($guard)->attempt(
                $request->only('email', 'password'),
                $remember
            )
        ) {
            $request->session()->regenerate();

            return match ($request->role) {
                'reseller' => redirect()->route('reseller.katalog'),
                'kurir' => redirect()->route('kurir.pesanan'),
                'kasir' => redirect()->route('kasir.pos'),
                default => redirect()->route('admintoko.produk'),
            };
        }

        // Cek penyebab gagal login
        $userExists = Auth::guard($guard)->getProvider()->retrieveByCredentials(['email' => $request->email]);
        
        if (!$userExists) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->withInput($request->except('password'));
        } else {
            return back()->withErrors(['password' => 'Password yang Anda masukkan salah.'])->withInput($request->except('password'));
        }
    }
}
