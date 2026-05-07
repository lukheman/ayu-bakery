@props([
    'userName' => 'John Doe',
    'userRole' => 'Administrator',
    'userInitials' => null,
    'notificationCount' => 0,
    'showLogout' => true,
    'showThemeToggle' => true
])

@php
    // Generate initials from user name if not provided
    $initials = $userInitials;
    if (!$initials && $userName) {
        $words = explode(' ', $userName);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        $initials = substr($initials, 0, 2);
    }
@endphp

<div class="topbar">
    <div class="d-flex align-items-center">
        <button class="btn btn-link mobile-toggle me-3" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div class="d-flex align-items-center gap-3">
        @if($showThemeToggle)
            <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
        @endif
        <a href="{{ route('admintoko.profile') }}" class="d-flex align-items-center gap-2 text-decoration-none" title="Go to Profile">
            @php
                $authUserForAvatar = null;
                foreach (['admin_toko', 'pemilik_toko', 'kasir', 'reseller', 'kurir'] as $g) {
                    if (Auth::guard($g)->check()) { $authUserForAvatar = Auth::guard($g)->user(); break; }
                }
            @endphp
            @if($authUserForAvatar?->foto)
                <img src="{{ Storage::url($authUserForAvatar->foto) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
            @else
                <div class="user-avatar">{{ $initials }}</div>
            @endif
            <div class="d-none d-md-block">
                <div class="fw-semibold user-name">{{ $userName }}</div>
                <small class="user-role">{{ $userRole }}</small>
            </div>
        </a>
        @if($showLogout)
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link" title="Logout" style="color: var(--text-secondary);">
                    <i class="fas fa-sign-out-alt" style="font-size: 1.25rem;"></i>
                </button>
            </form>
        @endif
        {{ $actions ?? '' }}
    </div>
</div>
