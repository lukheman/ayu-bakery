@props([
    'title' => 'Ayu Bakery - Reseller',
    'brandName' => 'Ayu Bakery',
    'brandIcon' => 'fas fa-birthday-cake'
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-7.2.0-web/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @livewireStyles
    <style>
        :root {
            --primary-color: #e11d48;
            --primary-dark: #be123c;
            --primary-light: #fb7185;
            --secondary-color: #f59e0b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);

            --bg-primary: #f1f5f9;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --input-bg: #ffffff;
            --hover-bg: #f8fafc;
        }

        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --border-light: #475569;
            --input-bg: #1e293b;
            --hover-bg: #334155;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.3), 0 1px 2px rgba(0,0,0,0.4);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Navbar */
        .reseller-navbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .reseller-navbar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .reseller-navbar .brand i { font-size: 1.5rem; }

        .reseller-navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .reseller-navbar .nav-link-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .reseller-navbar .nav-link-item:hover {
            background: var(--hover-bg);
            color: var(--primary-color);
        }

        .reseller-navbar .nav-link-item.active {
            background: var(--primary-color);
            color: white;
        }

        .cart-badge {
            background: var(--danger-color);
            color: white;
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 50px;
            font-weight: 700;
            position: absolute;
            top: -4px;
            right: -8px;
        }

        /* Theme Toggle */
        .theme-toggle {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-toggle:hover {
            background: var(--hover-bg);
            color: var(--primary-color);
        }

        .theme-toggle i { font-size: 1.15rem; }

        /* Content */
        .reseller-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Reusable Card */
        .modern-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border-light);
        }

        /* Form */
        .form-label {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            background: var(--input-bg);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1);
        }

        .form-control::placeholder { color: var(--text-muted); }

        .input-group-text {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        /* Modal */
        .modal-backdrop-custom {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); z-index: 1050;
            display: flex; align-items: center; justify-content: center;
        }

        .modal-content-custom {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
        }

        .modal-header-custom {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title-custom {
            font-size: 1.25rem; font-weight: 600; color: var(--text-primary);
        }

        .modal-close-btn {
            background: transparent; border: none; color: var(--text-secondary);
            font-size: 1.5rem; cursor: pointer; padding: 0; line-height: 1;
        }

        .modal-close-btn:hover { color: var(--danger-color); }

        .text-muted { color: var(--text-muted) !important; }

        /* Pagination */
        .pagination {
            --bs-pagination-bg: var(--bg-secondary);
            --bs-pagination-color: var(--text-primary);
            --bs-pagination-border-color: var(--border-color);
            --bs-pagination-hover-bg: var(--hover-bg);
            --bs-pagination-hover-color: var(--primary-color);
            --bs-pagination-active-bg: var(--primary-color);
            --bs-pagination-active-border-color: var(--primary-color);
            --bs-pagination-disabled-bg: var(--bg-tertiary);
            --bs-pagination-disabled-color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .reseller-content { padding: 1rem; }
            .reseller-navbar { padding: 0.75rem 1rem; }
        }
    </style>
</head>
<body>
    @php
        $reseller = auth('reseller')->user();
        $cartCount = 0;
        if ($reseller) {
            $cart = $reseller->keranjangBelanja;
            $cartCount = $cart ? $cart->itemKeranjang()->count() : 0;
        }
    @endphp

    {{-- Navbar --}}
    <nav class="reseller-navbar">
        <a href="{{ route('reseller.katalog') }}" class="brand">
            <i class="{{ $brandIcon }}"></i>
            <span>{{ $brandName }}</span>
        </a>
        <div class="nav-links">
            <a href="{{ route('reseller.katalog') }}" class="nav-link-item {{ request()->routeIs('reseller.katalog') ? 'active' : '' }}">
                <i class="fas fa-store"></i> Katalog
            </a>
            <a href="{{ route('reseller.keranjang') }}" class="nav-link-item {{ request()->routeIs('reseller.keranjang') ? 'active' : '' }}" style="position: relative;">
                <i class="fas fa-shopping-cart"></i> Keranjang
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="{{ route('reseller.pesanan') }}" class="nav-link-item {{ request()->routeIs('reseller.pesanan') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> Pesanan
            </a>
            <a href="{{ route('reseller.profil') }}" class="nav-link-item {{ request()->routeIs('reseller.profil') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i> Profil
            </a>

            <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="nav-link-item" style="border:none; background:none; cursor:pointer;" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </nav>

    {{-- Content --}}
    <div class="reseller-content">
        {{ $slot }}
    </div>

    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (prefersDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
            updateThemeIcon();
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) { themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon'; }
        }

        initTheme();
    </script>
    @livewireScripts
</body>
</html>
