<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Profile - Ayu Bakery')]
class Profile extends Component
{
    use WithFileUploads;

    public string $nama = '';
    public string $email = '';
    public string $no_hp = '';
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public $avatar;
    public ?string $currentAvatar = null;

    public bool $showPasswordSection = false;

    protected function getUser()
    {
        // Try each guard to find the logged-in user
        $guards = ['admin_toko', 'pemilik_toko', 'kasir', 'reseller', 'kurir'];
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }
        return null;
    }

    protected function getGuardName(): ?string
    {
        $guards = ['admin_toko', 'pemilik_toko', 'kasir', 'reseller', 'kurir'];
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $guard;
            }
        }
        return null;
    }

    public function mount(): void
    {
        $user = $this->getUser();
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->no_hp = $user->no_hp ?? '';
        $this->currentAvatar = $user->foto;
    }

    protected function rules(): array
    {
        $user = $this->getUser();
        $table = $user->getTable();

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:' . $table . ',email,' . $user->id],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];

        if ($this->showPasswordSection && $this->password) {
            $rules['current_password'] = ['required'];
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    protected $messages = [
        'current_password.required' => 'Password saat ini harus diisi.',
        'avatar.image' => 'File harus berupa gambar.',
        'avatar.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    public function togglePasswordSection(): void
    {
        $this->showPasswordSection = !$this->showPasswordSection;
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation(['current_password', 'password', 'password_confirmation']);
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => ['image', 'max:2048'],
        ]);
    }

    public function uploadAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $this->getUser();

        // Delete old avatar if exists
        if ($user->foto && Storage::exists($user->foto)) {
            Storage::delete($user->foto);
        }

        // Store new avatar
        $path = $this->avatar->store('avatars', 'public');

        $user->foto = $path;
        $user->save();

        $this->currentAvatar = $path;
        $this->avatar = null;

        session()->flash('success', 'Foto profil berhasil diperbarui.');
    }

    public function removeAvatar(): void
    {
        $user = $this->getUser();

        if ($user->foto && Storage::exists($user->foto)) {
            Storage::delete($user->foto);
        }

        $user->foto = null;
        $user->save();

        $this->currentAvatar = null;

        session()->flash('success', 'Foto profil berhasil dihapus.');
    }

    public function updateProfile(): void
    {
        $validated = $this->validate();

        $user = $this->getUser();
        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'] ?? null;
        $user->save();

        session()->flash('success', 'Profile berhasil diperbarui.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $this->getUser();

        // Verify current password manually
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->showPasswordSection = false;

        session()->flash('success', 'Password berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}
