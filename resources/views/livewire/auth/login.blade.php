<div class="auth-container">
    <div class="auth-card">
        {{-- Brand --}}
        <div class="auth-brand">
            <div class="icon-wrapper">
                <i class="fas fa-birthday-cake"></i>
            </div>
            <h1>Selamat Datang</h1>
            <p>Masuk ke sistem Ayu Bakery</p>
        </div>

        {{-- Login Form --}}
        <form wire:submit="submit">
            {{-- Role Selector --}}
            <div class="form-group">
                <label>Login Sebagai</label>
                <div class="input-wrapper">
                    <i class="fas fa-user-tag input-icon"></i>
                    <select wire:model="role" class="@error('role') is-invalid @enderror" id="role">
                        <option value="">-- Pilih Role --</option>
                        @foreach($this->roleOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" wire:model="email" class="@error('email') is-invalid @enderror" id="email"
                        placeholder="Masukkan email Anda" autofocus>
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" wire:model="password" class="@error('password') is-invalid @enderror"
                        id="password" placeholder="Masukkan password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-auth" wire:loading.attr="disabled">
                <span wire:loading.remove>Masuk <i class="fas fa-arrow-right"></i></span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin me-2"></i> Memproses...
                </span>
            </button>
        </form>

        {{-- Register Link --}}
        <div class="auth-footer-link">
            Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>