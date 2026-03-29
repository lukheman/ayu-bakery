<?php

namespace App\Livewire\Admin;

use App\Models\AdminToko;
use App\Models\PemilikToko;
use App\Models\Kasir;
use App\Models\Kurir;
use App\Models\Reseller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('User Management')]
class UserManagement extends Component
{
    use WithPagination;

    #[Url(as: 'role')]
    public string $role = 'admin_toko';

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public string $nama = '';
    public string $email = '';
    public string $no_hp = '';
    public string $alamat = '';
    public string $password = '';
    public string $password_confirmation = '';

    // State
    public ?int $editingUserId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingUserId = null;

    protected array $models = [
        'admin_toko' => AdminToko::class,
        'pemilik_toko' => PemilikToko::class,
        'kasir' => Kasir::class,
        'kurir' => Kurir::class,
        'reseller' => Reseller::class,
    ];

    public function updatedRole(): void
    {
        $this->resetPage();
        $this->resetSearch();
    }

    public function resetSearch(): void
    {
        $this->search = '';
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];

        if (in_array($this->role, ['admin_toko', 'reseller'])) {
            $rules['alamat'] = ['nullable', 'string'];
        }

        $table = $this->role; // admin_toko, pemilik_toko, kasir, kurir, reseller

        if ($this->editingUserId) {
            $rules['email'][] = "unique:{$table},email," . $this->editingUserId;
            if ($this->password) {
                $rules['password'] = ['confirmed', Password::defaults()];
            }
        } else {
            $rules['email'][] = "unique:{$table},email";
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingUserId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $modelClass = $this->models[$this->role];
        $user = $modelClass::findOrFail($userId);

        $this->editingUserId = $userId;
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->no_hp = $user->no_hp ?? '';

        if (in_array($this->role, ['admin_toko', 'reseller'])) {
            $this->alamat = $user->alamat ?? '';
        } else {
            $this->alamat = '';
        }

        $this->password = '';
        $this->password_confirmation = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();
        $modelClass = $this->models[$this->role];

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? null,
        ];

        if (in_array($this->role, ['admin_toko', 'reseller'])) {
            $data['alamat'] = $validated['alamat'] ?? null;
        }

        if ($this->editingUserId) {
            $user = $modelClass::findOrFail($this->editingUserId);

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
            session()->flash('success', 'Pengguna berhasil diperbarui.');
        } else {
            $data['password'] = Hash::make($this->password);
            $modelClass::create($data);
            session()->flash('success', 'Pengguna berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $userId): void
    {
        $this->deletingUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        if ($this->deletingUserId) {
            $modelClass = $this->models[$this->role];
            $modelClass::destroy($this->deletingUserId);
            session()->flash('success', 'Pengguna berhasil dihapus.');
        }

        $this->showDeleteModal = false;
        $this->deletingUserId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingUserId = null;
    }

    protected function resetForm(): void
    {
        $this->nama = '';
        $this->email = '';
        $this->no_hp = '';
        $this->alamat = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->editingUserId = null;
    }

    public function render()
    {
        $modelClass = $this->models[$this->role];

        $users = $modelClass::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}
