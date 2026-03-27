<?php

namespace App\Livewire\Auth;

use App\Models\Reseller;
use App\Models\KeranjangBelanja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Register - Ayu Bakery')]
class Register extends Component
{
    #[Rule(['required', 'string', 'max:255'])]
    public string $nama = '';

    #[Rule(['required', 'email', 'max:255', 'unique:reseller,email'])]
    public string $email = '';

    public string $password = '';
    public string $password_confirmation = '';

    public string $no_hp = '';
    public string $alamat = '';

    public bool $agree_terms = false;

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:reseller,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'agree_terms' => ['accepted'],
        ];
    }

    protected $messages = [
        'agree_terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
    ];

    public function submit()
    {
        $validated = $this->validate();

        $reseller = Reseller::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        // Create shopping cart for the new reseller
        KeranjangBelanja::create([
            'id_reseller' => $reseller->id,
        ]);

        Auth::guard('reseller')->login($reseller);

        session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layoutData(['type' => 'auth']);
    }
}
