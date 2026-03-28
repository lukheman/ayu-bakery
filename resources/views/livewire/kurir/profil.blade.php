<div>
    @php
        $kurir = auth('kurir')->user();
        $initials = collect(explode(' ', $kurir?->nama ?? ''))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
    @endphp

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1" style="font-weight: 700; color: var(--text-primary);">Profil Saya</h3>
            <p class="mb-0" style="color: var(--text-secondary);">Kelola informasi akun Anda</p>
        </div>
        <span
            style="font-size: 0.8rem; padding: 5px 14px; border-radius: 50px; background: rgba(99,102,241,0.1); color: var(--primary-color); font-weight: 600;">
            <i class="fas fa-motorcycle me-1"></i> Kurir
        </span>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert d-flex align-items-center gap-2 mb-4"
            style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); border-radius: 12px; color: var(--success-color); padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- Left: Profile Card --}}
        <div class="col-lg-4">
            <div class="modern-card text-center">
                <div class="position-relative d-inline-block mb-3">
                    @if($currentAvatar)
                        <img src="{{ Storage::url($currentAvatar) }}" alt="Avatar" class="rounded-circle"
                            style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--primary-color);">
                    @else
                        <div
                            style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; margin: 0 auto;">
                            {{ $initials }}
                        </div>
                    @endif
                </div>

                <h4 style="color: var(--text-primary); font-weight: 600;">{{ $kurir?->nama }}</h4>
                <p class="text-muted mb-1">{{ $kurir?->email }}</p>
                @if($kurir?->no_hp)
                    <p class="text-muted mb-0" style="font-size: 0.9rem;"><i
                            class="fas fa-phone me-1"></i>{{ $kurir->no_hp }}</p>
                @endif

                <hr style="border-color: var(--border-color); margin: 1.5rem 0;">

                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size: 0.85rem;">Bergabung</span>
                        <span
                            style="color: var(--text-primary); font-size: 0.85rem;">{{ $kurir?->created_at?->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted" style="font-size: 0.85rem;">Terakhir diperbarui</span>
                        <span
                            style="color: var(--text-primary); font-size: 0.85rem;">{{ $kurir?->updated_at?->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Edit Forms --}}
        <div class="col-lg-8">
            {{-- Avatar Upload --}}
            <div class="modern-card mb-4">
                <h6 class="mb-3" style="color: var(--text-primary); font-weight: 600;">
                    <i class="fas fa-camera me-2" style="color: var(--secondary-color);"></i>Foto Profil
                </h6>

                <div class="d-flex align-items-center gap-4">
                    <div>
                        @if($avatar)
                            <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="rounded-circle"
                                style="width: 72px; height: 72px; object-fit: cover; border: 3px solid var(--primary-color);">
                        @elseif($currentAvatar)
                            <img src="{{ Storage::url($currentAvatar) }}" alt="Avatar" class="rounded-circle"
                                style="width: 72px; height: 72px; object-fit: cover; border: 3px solid var(--border-color);">
                        @else
                            <div
                                style="width: 72px; height: 72px; border-radius: 50%; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 700; color: var(--text-muted);">
                                {{ $initials }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-grow-1">
                        <input type="file" wire:model="avatar" id="avatar-upload" class="d-none" accept="image/*">
                        <div class="d-flex gap-2 flex-wrap">
                            <label for="avatar-upload" class="btn"
                                style="background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 500; padding: 0.5rem 1rem; font-size: 0.85rem; cursor: pointer;">
                                <i class="fas fa-upload me-1"></i>
                                <span wire:loading.remove wire:target="avatar">Pilih Foto</span>
                                <span wire:loading wire:target="avatar">Mengupload...</span>
                            </label>

                            @if($avatar)
                                <button type="button" wire:click="uploadAvatar" class="btn"
                                    style="background: var(--success-color); color: white; border: none; border-radius: 8px; font-weight: 500; padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    <i class="fas fa-check me-1"></i>Simpan
                                </button>
                                <button type="button" wire:click="$set('avatar', null)" class="btn"
                                    style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 500; padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    <i class="fas fa-times me-1"></i>Batal
                                </button>
                            @endif

                            @if($currentAvatar && !$avatar)
                                <button type="button" wire:click="removeAvatar" class="btn"
                                    style="background: rgba(239,68,68,0.1); color: var(--danger-color); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px; font-weight: 500; padding: 0.5rem 1rem; font-size: 0.85rem;"
                                    wire:confirm="Hapus foto profil?">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            @endif
                        </div>

                        @error('avatar')
                            <div class="text-danger mt-2" style="font-size: 0.8rem;">{{ $message }}</div>
                        @enderror

                        <p class="text-muted mb-0 mt-2" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle me-1"></i> JPG, PNG, GIF. Maks 2MB.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Profile Info --}}
            <div class="modern-card mb-4">
                <h6 class="mb-3" style="color: var(--text-primary); font-weight: 600;">
                    <i class="fas fa-user-edit me-2" style="color: var(--primary-color);"></i>Informasi Profil
                </h6>

                <form wire:submit="updateProfile">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Lengkap <span
                                    style="color: var(--danger-color);">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                                wire:model="nama" placeholder="Masukkan nama lengkap">
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span
                                    style="color: var(--danger-color);">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                wire:model="email" placeholder="Masukkan email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp"
                                wire:model="no_hp" placeholder="08xxxxxxxxxx">
                            @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn"
                            style="background: var(--primary-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.6rem 1.5rem;">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password --}}
            <div class="modern-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        <i class="fas fa-lock me-2" style="color: var(--warning-color);"></i>Ubah Password
                    </h6>
                    <button type="button" wire:click="togglePasswordSection" class="btn btn-sm"
                        style="background: {{ $showPasswordSection ? 'rgba(239,68,68,0.1)' : 'var(--bg-tertiary)' }}; color: {{ $showPasswordSection ? 'var(--danger-color)' : 'var(--text-secondary)' }}; border: 1px solid {{ $showPasswordSection ? 'rgba(239,68,68,0.3)' : 'var(--border-color)' }}; border-radius: 8px; font-weight: 500; padding: 0.4rem 1rem; font-size: 0.8rem;">
                        {{ $showPasswordSection ? 'Batal' : 'Ubah Password' }}
                    </button>
                </div>

                @if($showPasswordSection)
                    <form wire:submit="updatePassword">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label">Password Saat Ini <span
                                        style="color: var(--danger-color);">*</span></label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" wire:model="current_password"
                                    placeholder="Masukkan password saat ini">
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password Baru <span
                                        style="color: var(--danger-color);">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" wire:model="password" placeholder="Masukkan password baru">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span
                                        style="color: var(--danger-color);">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    wire:model="password_confirmation" placeholder="Konfirmasi password baru">
                            </div>
                        </div>

                        <div class="p-3 mt-3"
                            style="background: rgba(14,165,233,0.08); border-radius: 10px; border: 1px solid rgba(14,165,233,0.2);">
                            <small style="color: var(--secondary-color);">
                                <i class="fas fa-info-circle me-1"></i>
                                Password harus minimal 8 karakter.
                            </small>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn"
                                style="background: var(--warning-color); color: white; border: none; border-radius: 8px; font-weight: 600; padding: 0.6rem 1.5rem;">
                                <i class="fas fa-key me-1"></i> Perbarui Password
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">
                        <i class="fas fa-shield-alt me-2"></i>
                        Klik tombol "Ubah Password" untuk memperbarui password Anda.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>