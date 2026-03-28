<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Login - Ayu Bakery')]
class Login extends Component
{
    #[Rule(['required'])]
    public string $role = '';

    #[Rule(['required', 'email'])]
    public string $email = '';

    #[Rule([])]
    public string $password = '';

    public bool $remember = false;

    /**
     * Map role to guard name
     */
    protected array $guardMap = [
        'admin_toko' => 'admin_toko',
        'pemilik_toko' => 'pemilik_toko',
        'kasir' => 'kasir',
        'reseller' => 'reseller',
        'kurir' => 'kurir',
    ];

    /**
     * Role labels for the dropdown
     */
    public function getRoleOptionsProperty(): array
    {
        return [
            'admin_toko' => 'Admin Toko',
            'pemilik_toko' => 'Pemilik Toko',
            'kasir' => 'Kasir',
            'reseller' => 'Reseller',
            'kurir' => 'Kurir',
        ];
    }

    public function submit()
    {
        $this->validate([
            'role' => ['required', 'in:' . implode(',', array_keys($this->guardMap))],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $guard = $this->guardMap[$this->role];

        if (
            Auth::guard($guard)->attempt(
                ['email' => $this->email, 'password' => $this->password],
                $this->remember
            )
        ) {
            session()->regenerate();

            return match ($this->role) {
                'reseller' => redirect()->to(route('reseller.katalog')),
                'kurir' => redirect()->to(route('kurir.pesanan')),
                'kasir' => redirect()->to(route('kasir.pos')),
                default => redirect()->to(route('admintoko.produk')),
            };
        }

        $this->addError('email', __('auth.failed'));
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layoutData(['type' => 'auth']);
    }
}
