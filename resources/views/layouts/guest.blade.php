@props([
    'title' => 'Ayu Bakery - Toko Roti & Kue',
    'description' => 'Ayu Bakery menyediakan aneka roti dan kue berkualitas dengan bahan pilihan dan harga terjangkau',
    'type' => 'guest',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description }}">
    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome-free-7.2.0-web/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css')}}">

    <style>
        :root {
            --primary-color: #e11d48;
            --primary-dark: #be123c;
            --primary-light: #fb7185;
            --secondary-color: #f59e0b;
            --accent-color: #7c3aed;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --bg-light: #fffbeb;
            --bg-white: #ffffff;
            --bg-warm: #fff7ed;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-white);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* ===== NAVBAR ===== */
        .site-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .site-navbar-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0.875rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .site-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--primary-color);
            font-size: 1.4rem;
            font-weight: 700;
        }

        .site-brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .site-nav {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .site-nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .site-nav-link:hover {
            color: var(--primary-color);
        }

        .site-navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-nav-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-nav-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-nav-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.25);
        }

        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(225, 29, 72, 0.35);
            color: white;
        }

        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .site-nav { display: none; }
            .mobile-menu-btn { display: block; }
            .site-navbar-actions .btn-nav-outline { display: none; }
        }

        /* ===== SHARED ===== */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section {
            padding: 5rem 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(225, 29, 72, 0.35);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.05rem;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: #1e293b;
            color: #cbd5e1;
            padding: 4rem 0 2rem;
        }

        .footer-container {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 3rem;
        }

        .footer-brand-name {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-decoration: none;
        }

        .footer-brand p {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .footer-column h4 {
            color: white;
            font-weight: 600;
            margin-bottom: 1.25rem;
            font-size: 1rem;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
        }

        .footer-column li {
            margin-bottom: 0.75rem;
        }

        .footer-column a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--primary-light);
        }

        .footer-bottom {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-bottom p {
            color: #64748b;
            font-size: 0.85rem;
        }

        .footer-social {
            display: flex;
            gap: 0.75rem;
        }

        .footer-social a {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .footer-social a:hover {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }

        /* ===== AUTH PAGE STYLES ===== */
        .auth-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 30%, #fecdd3 60%, #fda4af 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .bg-shapes {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden; z-index: 0;
        }

        .bg-shapes .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(225, 29, 72, 0.06);
            animation: authFloat 20s infinite ease-in-out;
        }

        .bg-shapes .shape:nth-child(1) { width: 500px; height: 500px; top: -150px; left: -150px; }
        .bg-shapes .shape:nth-child(2) { width: 350px; height: 350px; bottom: -100px; right: -80px; animation-delay: -7s; }
        .bg-shapes .shape:nth-child(3) { width: 250px; height: 250px; top: 40%; left: 60%; animation-delay: -14s; background: rgba(245, 158, 11, 0.06); }

        @keyframes authFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            33% { transform: translateY(-25px) rotate(8deg); }
            66% { transform: translateY(15px) rotate(-4deg); }
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.12);
            padding: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            animation: authSlideUp 0.5s ease-out;
        }

        @keyframes authSlideUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-brand .icon-wrapper {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 8px 25px rgba(225, 29, 72, 0.3);
        }

        .auth-brand .icon-wrapper i {
            font-size: 2rem;
            color: white;
        }

        .auth-brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.35rem;
        }

        .auth-brand p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--primary-color);
        }

        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            height: 50px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0 1rem 0 2.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #fafafa;
            color: var(--text-primary);
        }

        .input-wrapper input:focus,
        .input-wrapper select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.08);
            background: white;
        }

        .input-wrapper input.is-invalid,
        .input-wrapper select.is-invalid {
            border-color: var(--danger-color);
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.35rem;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.1);
            border-color: var(--primary-color);
        }

        .form-check-label {
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-auth {
            width: 100%;
            height: 52px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.5s ease;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(225, 29, 72, 0.35);
        }

        .btn-auth:hover::before { left: 100%; }
        .btn-auth:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .auth-footer-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .auth-footer-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer-link a:hover {
            text-decoration: underline;
        }

        /* Role selector tabs */
        .role-selector {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
            background: #f1f5f9;
            padding: 0.3rem;
            border-radius: 12px;
        }

        .role-option {
            flex: 1;
            padding: 0.65rem 1rem;
            border-radius: 10px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .role-option.active {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .role-option:hover:not(.active) {
            color: var(--text-primary);
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }
            .auth-brand .icon-wrapper {
                width: 60px; height: 60px;
            }
            .auth-brand .icon-wrapper i {
                font-size: 1.75rem;
            }
            .auth-brand h1 {
                font-size: 1.3rem;
            }
        }

        /* Autofill styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #fafafa inset;
            -webkit-text-fill-color: var(--text-primary);
        }
    </style>
    {{ $styles ?? '' }}
</head>

<body>
    {{-- Navbar (always visible) --}}
    <nav class="site-navbar">
        <div class="site-navbar-container">
            <a href="/" class="site-brand">
                <div class="site-brand-icon">
                    <i class="fas fa-birthday-cake"></i>
                </div>
                <span>Ayu Bakery</span>
            </a>

            <ul class="site-nav">
                <li><a href="/#tentang" class="site-nav-link">Tentang</a></li>
                <li><a href="/#keunggulan" class="site-nav-link">Keunggulan</a></li>
                <li><a href="/#bergabung" class="site-nav-link">Bergabung</a></li>
            </ul>

            <div class="site-navbar-actions">
                <a href="{{ route('login') }}" class="btn-nav btn-nav-outline">Masuk</a>
                <a href="{{ route('register') }}" class="btn-nav btn-nav-primary">Daftar</a>
                <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
            </div>
        </div>
    </nav>

    @if($type === 'auth')
        <section class="auth-section" style="padding-top: calc(73px + 2rem);">
            <div class="bg-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            {{ $slot }}
        </section>
    @else
        <main>
            {{ $slot }}
        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-container">
                    <div class="footer-brand">
                        <div class="footer-brand-name">
                            <div class="site-brand-icon" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            Ayu Bakery
                        </div>
                        <p>Menyediakan aneka roti dan kue berkualitas dengan bahan pilihan terbaik. Melayani pembelian langsung, pesanan reseller, dan pengiriman ke seluruh area.</p>
                    </div>

                    <div class="footer-column">
                        <h4>Menu</h4>
                        <ul>
                            <li><a href="/#tentang">Tentang Kami</a></li>
                            <li><a href="/#keunggulan">Keunggulan</a></li>
                            <li><a href="/#bergabung">Bergabung</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Layanan</h4>
                        <ul>
                            <li><a href="{{ route('register') }}">Daftar Reseller</a></li>
                            <li><a href="{{ route('register') }}">Daftar Kurir</a></li>
                            <li><a href="{{ route('login') }}">Login</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Kontak</h4>
                        <ul>
                            <li><i class="fas fa-map-marker-alt" style="width: 16px; color: var(--primary-light);"></i> Jl. Contoh No. 123</li>
                            <li><i class="fas fa-phone" style="width: 16px; color: var(--primary-light);"></i> 0812-3456-7890</li>
                            <li><i class="fas fa-envelope" style="width: 16px; color: var(--primary-light);"></i> info@ayubakery.com</li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} Ayu Bakery. All rights reserved.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </footer>
    @endif

    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
    @livewireStyles
    @livewireScripts
    {{ $scripts ?? '' }}
</body>

</html>
