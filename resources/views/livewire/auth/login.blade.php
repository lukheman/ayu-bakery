<div class="login-container">
    <div class="login-card">
        <!-- Brand Logo -->
        <div class="brand-logo">
            <div class="icon-wrapper">
                <i class="fas fa-bread-slice"></i>
            </div>
            <h1>Selamat Datang</h1>
            <p>Masuk ke sistem Ayu Bakery</p>
        </div>

        <!-- Login Form -->
        <form wire:submit="submit">
            <!-- Role Selector -->
            <div class="form-floating position-relative mb-3">
                <i class="fas fa-user-tag input-icon"></i>
                <select wire:model="role" class="form-control @error('role') is-invalid @enderror" id="role"
                    style="padding-left: 2.5rem;">
                    <option value="">-- Pilih Role --</option>
                    @foreach($this->roleOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label for="role">Role</label>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Email Field -->
            <div class="form-floating position-relative">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror"
                    id="email" placeholder="Email Address" autofocus>
                <label for="email">Email Address</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-floating position-relative">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" wire:model="password"
                    class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password">
                <label for="password">Password</label>
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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

            <!-- Remember Me & Forgot Password -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" wire:model="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
                <span wire:loading.remove>Sign In <i class="fas fa-arrow-right"></i></span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin me-2"></i> Signing in...
                </span>
            </button>
        </form>

        <!-- Divider -->
        <div class="divider">
            <span>or continue with</span>
        </div>


        <!-- Sign Up Link -->
        <div class="signup-link">
            Don't have an account? <a href="{{ route('register') }}">Create Account</a>
        </div>
    </div>
</div>