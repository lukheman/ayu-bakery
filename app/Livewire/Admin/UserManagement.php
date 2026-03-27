<?php

namespace App\Livewire\Admin;

use App\Models\AdminToko;
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

    // Search
    #[Url(as: 'q')]
    public string $search = '';

    // Form fields
    public string $nama = '';
    public string $email = '';
    public string $no_hp = '';
    public string $password = '';
    public string $password_confirmation = '';

    // State
    public ?int $editingUserId = null;
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingUserId = null;

    protected function rules(): array
    {
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];

        if ($this->editingUserId) {
            $rules['email'][] = 'unique:admin_toko,email,' . $this->editingUserId;
            if ($this->password) {
                $rules['password'] = ['confirmed', Password::defaults()];
            }
        } else {
            $rules['email'][] = 'unique:admin_toko,email';
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingUserId = null;
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = AdminToko::findOrFail($userId);
        $this->editingUserId = $userId;
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->no_hp = $user->no_hp ?? '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingUserId) {
            $user = AdminToko::findOrFail($this->editingUserId);
            $user->nama = $validated['nama'];
            $user->email = $validated['email'];
            $user->no_hp = $validated['no_hp'] ?? null;

            if (!empty($this->password)) {
                $user->password = Hash::make($this->password);
            }

            $user->save();
            session()->flash('success', 'Admin berhasil diperbarui.');
        } else {
            AdminToko::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);
            session()->flash('success', 'Admin berhasil ditambahkan.');
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
            AdminToko::destroy($this->deletingUserId);
            session()->flash('success', 'Admin berhasil dihapus.');
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
        $this->password = '';
        $this->password_confirmation = '';
        $this->editingUserId = null;
    }

    public function render()
    {
        $users = AdminToko::query()
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
