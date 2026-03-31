@props([
    'title' => 'Ayu Bakery - Kasir',
])

<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-7.2.0-web/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        :root {
            --pos-primary: #e11d48;
            --pos-primary-light: #fb7185;
            --pos-success: #10b981;
            --pos-warning: #f59e0b;
            --pos-danger: #ef4444;
            --pos-accent: #f59e0b;

            /* Light theme (default override to dark for POS) */
            --pos-bg: #f1f5f9;
            --pos-surface: #ffffff;
            --pos-surface-2: #f8fafc;
            --pos-border: #e2e8f0;
            --pos-text: #1e293b;
            --pos-text-secondary: #64748b;
            --pos-text-muted: #94a3b8;
        }

        [data-theme="dark"] {
            --pos-bg: #0f172a;
            --pos-surface: #1e293b;
            --pos-surface-2: #334155;
            --pos-border: #475569;
            --pos-text: #f1f5f9;
            --pos-text-secondary: #94a3b8;
            --pos-text-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--pos-bg);
            color: var(--pos-text);
            height: 100vh;
            overflow: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .pos-topbar {
            background: var(--pos-surface);
            border-bottom: 1px solid var(--pos-border);
            padding: 0.5rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 56px;
        }

        .pos-brand {
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--pos-primary-light);
        }

        .pos-topbar-link {
            color: var(--pos-text-secondary);
            text-decoration: none;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.15s;
        }
        .pos-topbar-link:hover, .pos-topbar-link.active {
            color: var(--pos-text);
            background: var(--pos-surface-2);
        }

        .theme-toggle {
            background: transparent;
            border: none;
            color: var(--pos-text-secondary);
            cursor: pointer;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .theme-toggle:hover {
            background: var(--pos-surface-2);
            color: var(--pos-primary-light);
        }
        .theme-toggle i { font-size: 1rem; }

        .pos-main {
            height: calc(100vh - 56px);
            overflow: hidden;
        }

        .form-control, .form-select {
            background: var(--pos-surface-2);
            border-color: var(--pos-border);
            color: var(--pos-text);
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--pos-primary);
            box-shadow: 0 0 0 3px rgba(225,29,72,0.2);
            background: var(--pos-surface-2);
            color: var(--pos-text);
        }
        .form-control::placeholder { color: var(--pos-text-muted); }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--pos-surface-2); border-radius: 3px; }
    </style>
</head>
<body>
    <nav class="pos-topbar">
        <div class="d-flex align-items-center gap-3">
            <span class="pos-brand"><i class="fas fa-birthday-cake me-1"></i> Ayu Bakery</span>
            <span style="color: var(--pos-text-muted); font-size: 0.75rem; padding: 2px 8px; background: rgba(225,29,72,0.15); border-radius: 50px; font-weight: 600; color: var(--pos-primary-light);">POS</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('kasir.pos') }}" class="pos-topbar-link {{ request()->routeIs('kasir.pos') ? 'active' : '' }}">
                <i class="fas fa-cash-register me-1"></i> Kasir
            </a>
            <a href="{{ route('kasir.riwayat') }}" class="pos-topbar-link {{ request()->routeIs('kasir.riwayat') ? 'active' : '' }}">
                <i class="fas fa-history me-1"></i> Riwayat
            </a>
            <a href="{{ route('kasir.profil') }}" class="pos-topbar-link {{ request()->routeIs('kasir.profil') ? 'active' : '' }}">
                <i class="fas fa-user-circle me-1"></i> Profil
            </a>
            <div style="width: 1px; height: 24px; background: var(--pos-border); margin: 0 0.25rem;"></div>
            <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                <i id="theme-icon" class="fas fa-sun"></i>
            </button>
            <div style="width: 1px; height: 24px; background: var(--pos-border); margin: 0 0.25rem;"></div>
            <span class="pos-topbar-link" style="cursor: default; color: var(--pos-text-muted);">
                <i class="fas fa-user me-1"></i> {{ auth('kasir')->user()?->nama }}
            </span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="pos-topbar-link" style="border: none; background: none; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </nav>

    <div class="pos-main">
        {{ $slot }}
    </div>

    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function initTheme() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeIcon();
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const icon = document.getElementById('theme-icon');
            if (icon) icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }

        initTheme();
    </script>
    @livewireScripts
</body>
</html>
