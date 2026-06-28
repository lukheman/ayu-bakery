<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kurir;
use App\Models\Reseller;
use App\Models\KeranjangBelanja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $role = $request->query('role', 'reseller');
        if (!in_array($role, ['reseller', 'kurir'])) {
            $role = 'reseller';
        }
        return view('auth.register', ['type' => 'auth', 'role' => $role]);
    }

    public function register(Request $request)
    {
        $role = $request->input('role');
        $emailTable = $role === 'kurir' ? 'kurir' : 'reseller';

        $validated = $request->validate([
            'role' => ['required', 'in:reseller,kurir'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:' . $emailTable . ',email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => $role === 'reseller'
                ? ['nullable', 'string', 'max:500']
                : ['nullable', 'string', 'max:500'],
            'agree_terms' => ['accepted'],
        ], [
            'nama.required' => 'Nama lengkap tidak boleh kosong.',
            'nama.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'agree_terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        if ($role === 'kurir') {
            $user = Kurir::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'no_hp' => $validated['no_hp'] ?? null,
            ]);

            Auth::guard('kurir')->login($user);
            $request->session()->regenerate();

            return redirect()->route('kurir.pesanan');
        }

        // Default: Reseller
        $user = Reseller::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        KeranjangBelanja::create([
            'id_reseller' => $user->id,
        ]);

        Auth::guard('reseller')->login($user);
        $request->session()->regenerate();

        return redirect()->route('reseller.katalog');
    }
}
