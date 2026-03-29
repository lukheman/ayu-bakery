<div>
    {{-- Page Header --}}
    @php
        $rolesLabel = [
            'admin_toko' => 'Admin Toko',
            'pemilik_toko' => 'Pemilik Toko',
            'kasir' => 'Kasir',
            'kurir' => 'Kurir',
            'reseller' => 'Reseller'
        ];
        $currentRoleLabel = $rolesLabel[$role] ?? 'User';
    @endphp
    <x-page-header title="Manajemen Pengguna" subtitle="Kelola seluruh pengguna dalam sistem">
        <x-slot:actions>
            <x-button variant="primary" icon="fas fa-plus" wire:click="openCreateModal">
                Tambah {{ $currentRoleLabel }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-alert variant="success" title="Success!" class="mb-4">
            {{ session('success') }}
        </x-alert>
    @endif

    @if (session('error'))
        <x-alert variant="danger" title="Error!" class="mb-4">
            {{ session('error') }}
        </x-alert>
    @endif

    {{-- Users Table Card --}}
    <div class="modern-card">
        {{-- Tabs --}}
        <div class="d-flex gap-2 mb-4 overflow-auto pb-2" style="border-bottom: 1px solid var(--border-color);">
            @foreach($rolesLabel as $key => $label)
                <button wire:click="$set('role', '{{ $key }}')"
                    class="btn {{ $role === $key ? 'btn-primary-modern' : 'btn-modern' }}"
                    style="{{ $role !== $key ? 'color: var(--text-secondary); background: transparent;' : '' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Search and Filters --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">Daftar {{ $currentRoleLabel }}</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text" style="background: var(--input-bg); border-color: var(--border-color);">
                    <i class="fas fa-search" style="color: var(--text-muted);"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari pengguna..."
                    wire:model.live.debounce.300ms="search" style="border-left: none;">
            </div>
        </div>

        {{-- Users Table --}}
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Dibuat Pada</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="user-avatar text-uppercase">{{ substr($user->nama, 0, 2) }}</div>
                                    <div>
                                        <div class="fw-semibold" style="color: var(--text-primary);">{{ $user->nama }}</div>
                                        <small class="text-muted">ID: {{ $user->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                            <td style="color: var(--text-secondary);">{{ $user->no_hp ?? '-' }}</td>
                            <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="action-btn action-btn-edit" wire:click="openEditModal({{ $user->id }})"
                                        title="Edit pengguna">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn action-btn-delete" wire:click="confirmDelete({{ $user->id }})"
                                        title="Hapus pengguna">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Tidak ada pengguna ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="d-flex justify-content-end mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="modal-backdrop-custom" wire:click.self="closeModal">
            <div class="modal-content-custom" wire:click.stop>
                <div class="modal-header-custom">
                    <h5 class="modal-title-custom">
                        {{ $editingUserId ? 'Edit ' . $currentRoleLabel : 'Tambah ' . $currentRoleLabel }}
                    </h5>
                    <button type="button" class="modal-close-btn" wire:click="closeModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit="save">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span
                                style="color: var(--danger-color);">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                            wire:model="nama" placeholder="Masukkan nama lengkap">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span
                                style="color: var(--danger-color);">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            wire:model="email" placeholder="Masukkan alamat email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp"
                            wire:model="no_hp" placeholder="Masukkan nomor HP">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(in_array($role, ['admin_toko', 'reseller']))
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" wire:model="alamat"
                                placeholder="Masukkan alamat lengkap (opsional)" rows="2"></textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                            @if (!$editingUserId)
                                <span style="color: var(--danger-color);">*</span>
                            @else
                                <small class="text-muted">(kosongkan jika tidak ingin diubah)</small>
                            @endif
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            wire:model="password"
                            placeholder="{{ $editingUserId ? 'Masukkan password baru' : 'Masukkan password' }}">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            wire:model="password_confirmation" placeholder="Konfirmasi password">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <x-button type="button" variant="outline" wire:click="closeModal">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary">
                            {{ $editingUserId ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <x-confirm-modal :show="$showDeleteModal" title="Konfirmasi Hapus"
        message="Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan."
        on-confirm="deleteUser" on-cancel="cancelDelete" variant="danger" icon="fas fa-exclamation-triangle">
        <x-slot:confirmButton>
            <i class="fas fa-trash-alt me-2"></i>Hapus
        </x-slot:confirmButton>
    </x-confirm-modal>
</div>