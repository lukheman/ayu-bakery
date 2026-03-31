<div class="auth-container" style="max-width: 800px;">
    <div class="auth-card">
        {{-- Brand --}}
        <div class="auth-brand">
            <div class="icon-wrapper">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <h1>Daftar Akun</h1>
            <p>Bergabung bersama Ayu Bakery</p>
        </div>

        {{-- Role Selector --}}
        <div class="role-selector">
            <button type="button" wire:click="setRole('reseller')"
                class="role-option {{ $role === 'reseller' ? 'active' : '' }}">
                <i class="fas fa-users"></i> Reseller
            </button>
            <button type="button" wire:click="setRole('kurir')"
                class="role-option {{ $role === 'kurir' ? 'active' : '' }}">
                <i class="fas fa-motorcycle"></i> Kurir
            </button>
        </div>

        {{-- Role Description --}}
        <div
            style="background: {{ $role === 'reseller' ? '#fff1f2' : '#eff6ff' }}; padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; display: flex; align-items: start; gap: 10px; border: 1px solid {{ $role === 'reseller' ? 'rgba(225,29,72,0.1)' : 'rgba(59,130,246,0.1)' }};">
            <i class="fas {{ $role === 'reseller' ? 'fa-info-circle' : 'fa-info-circle' }}"
                style="color: {{ $role === 'reseller' ? '#e11d48' : '#3b82f6' }}; margin-top: 2px;"></i>
            <span style="font-size: 0.82rem; color: #64748b; line-height: 1.5;">
                @if($role === 'reseller')
                    Daftar sebagai <strong>Reseller</strong> untuk membeli produk dengan harga khusus dan menjualnya
                    kembali.
                @else
                    Daftar sebagai <strong>Kurir</strong> untuk membantu mengantarkan pesanan kepada pelanggan.
                @endif
            </span>
        </div>

        {{-- Register Form --}}
        <form wire:submit="submit">
            <div class="row">
                <div class="col-md-6">
                    {{-- Nama --}}
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" wire:model="nama" class="@error('nama') is-invalid @enderror" id="nama"
                                placeholder="Masukkan nama lengkap" autofocus>
                        </div>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" wire:model="email" class="@error('email') is-invalid @enderror"
                                id="email" placeholder="Masukkan email Anda">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- No HP --}}
                    <div class="form-group">
                        <label>No. HP <span style="color: #94a3b8; font-weight: 400;">(opsional)</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" wire:model="no_hp" class="@error('no_hp') is-invalid @enderror"
                                id="no_hp" placeholder="Contoh: 08123456789">
                        </div>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    {{-- Alamat (hanya untuk Reseller) --}}
                    @if($role === 'reseller')
                        <div class="form-group">
                            <label>Alamat <span style="color: #94a3b8; font-weight: 400;">(opsional)</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" wire:model="alamat" class="@error('alamat') is-invalid @enderror"
                                    id="alamat" placeholder="Alamat lengkap Anda">
                            </div>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    {{-- Password --}}
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" wire:model="password" class="@error('password') is-invalid @enderror"
                                id="password" placeholder="Minimal 8 karakter">
                            <button type="button" class="password-toggle" onclick="togglePwd('password', 'pwdIcon1')">
                                <i class="fas fa-eye" id="pwdIcon1"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" wire:model="password_confirmation" id="password_confirmation"
                                placeholder="Ulangi password">
                            <button type="button" class="password-toggle"
                                onclick="togglePwd('password_confirmation', 'pwdIcon2')">
                                <i class="fas fa-eye" id="pwdIcon2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terms --}}
            <div class="form-check" style="margin-bottom: 1.25rem;">
                <input class="form-check-input @error('agree_terms') is-invalid @enderror" type="checkbox"
                    wire:model="agree_terms" id="agree_terms">
                <label class="form-check-label" for="agree_terms">
                    Saya menyetujui <a href="#" style="color: #e11d48; text-decoration: none; font-weight: 600;">Syarat
                        & Ketentuan</a>
                </label>
                @error('agree_terms')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-auth" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    Daftar sebagai {{ $role === 'reseller' ? 'Reseller' : 'Kurir' }}
                    <i class="fas fa-arrow-right"></i>
                </span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin me-2"></i> Mendaftar...
                </span>
            </button>
        </form>

        {{-- Login Link --}}
        <div class="auth-footer-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</div>

<script>
    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>