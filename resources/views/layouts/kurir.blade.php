@props([
    'title' => 'Ayu Bakery - Kurir',
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
            --input-bg: #f8fafc;
            --hover-bg: rgba(225,29,72,0.04);
        }

        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border-color: #334155;
            --border-light: #1e293b;
            --input-bg: #0f172a;
            --hover-bg: rgba(225,29,72,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .top-navbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .brand-text {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
            text-decoration: none;
        }

        .nav-link-item {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .nav-link-item:hover, .nav-link-item.active {
            color: var(--primary-color);
            background: var(--hover-bg);
        }

        .main-content {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .modern-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }

        .form-control, .form-select {
            background: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 0.6rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(225,29,72,0.15);
            background: var(--input-bg);
            color: var(--text-primary);
        }

        .modal-backdrop-custom {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1050;
            display: flex; align-items: center; justify-content: center;
            animation: fadeIn 0.2s ease-out;
        }
        .modal-content-custom {
            background: var(--bg-secondary); border-radius: 16px;
            padding: 2rem; width: 100%; max-width: 500px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .modal-header-custom {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;
        }
        .modal-title-custom { font-weight: 700; color: var(--text-primary); font-size: 1.1rem; margin: 0; }
        .modal-close-btn {
            background: var(--bg-tertiary); border: none; width: 32px; height: 32px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            color: var(--text-muted); cursor: pointer; transition: all 0.2s;
        }

        .theme-toggle {
            background: var(--bg-tertiary); border: 1px solid var(--border-color);
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary); cursor: pointer; transition: all 0.2s;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        @media (max-width: 768px) {
            .main-content { padding: 1rem; }
        }
    </style>
</head>
<body>
    {{-- Navbar --}}
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center" style="max-width: 900px; margin: 0 auto;">
            <div class="d-flex align-items-center gap-3">
                <span class="brand-text"><i class="{{ $brandIcon }} me-1"></i> {{ $brandName }}</span>
                <span style="color: var(--text-muted); font-size: 0.8rem; padding: 3px 8px; background: rgba(225,29,72,0.1); border-radius: 50px; font-weight: 600;">Kurir</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('kurir.pesanan') }}" class="nav-link-item {{ request()->routeIs('kurir.pesanan') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Pesanan
                </a>
                <a href="{{ route('kurir.scan') }}" class="nav-link-item {{ request()->routeIs('kurir.scan') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i> Scan QR
                </a>
                <a href="{{ route('kurir.profil') }}" class="nav-link-item {{ request()->routeIs('kurir.profil') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> Profil
                </a>

                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                    <i id="theme-icon" class="fas fa-moon"></i>
                </button>

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link-item" style="border: none; background: none; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="main-content">
        {{ $slot }}
    </div>

    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @livewireScripts
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            document.getElementById('theme-icon').className = next === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
        (function() {
            const saved = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('theme-icon').className = saved === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            });
        })();
    </script>
</body>
</html>
