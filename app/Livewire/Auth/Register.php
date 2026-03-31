<?php

namespace App\Livewire\Auth;

use App\Models\Kurir;
use App\Models\Reseller;
use App\Models\KeranjangBelanja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Daftar - Ayu Bakery')]
class Register extends Component
{
    public string $role = 'reseller'; // 'reseller' | 'kurir'

    public string $nama = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $no_hp = '';
    public string $alamat = '';

    public bool $agree_terms = false;

    protected function rules(): array
    {
        $emailTable = $this->role === 'kurir' ? 'kurir' : 'reseller';

        return [
            'role' => ['required', 'in:reseller,kurir'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:' . $emailTable . ',email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => $this->role === 'reseller'
                ? ['nullable', 'string', 'max:500']
                : ['nullable', 'string', 'max:500'],
            'agree_terms' => ['accepted'],
        ];
    }

    protected $messages = [
        'agree_terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        'email.unique' => 'Email sudah terdaftar.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function updatedRole(): void
    {
        $this->resetValidation();
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
        $this->resetValidation();
    }

    public function submit()
    {
        $validated = $this->validate();

        if ($this->role === 'kurir') {
            $user = Kurir::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'no_hp' => $validated['no_hp'] ?? null,
            ]);

            Auth::guard('kurir')->login($user);
            session()->regenerate();

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
        session()->regenerate();

        return redirect()->route('reseller.katalog');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layoutData(['type' => 'auth']);
    }
}
