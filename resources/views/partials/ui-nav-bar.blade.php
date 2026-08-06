@php
  $items = $navItems ?? [
    ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard'],
    ['key'=>'my-timetable','label'=>'My Timetable','href'=>'/my-timetable-ui'],
    ['key'=>'cohort-timetables','label'=>'Cohort Timetables','href'=>'/cohort-timetable-ui'],
    ['key'=>'replacement-arrangement','label'=>'Replacement Arrangement','href'=>'/replacement-home-ui'],
    ['key'=>'request-approval','label'=>'Request Approval','href'=>'/request-approval-ui','badge'=>true],
    ['key'=>'replacement-history','label'=>'Replacement History','href'=>'/my-request-history-ui'],
  ];
@endphp

<div class="top-bar">
    <div class="top-logo" onclick="navigateHome()">
        <img src="/images/logo_banner.png" alt="TAR UMT">
    </div>

    <div class="nav-items">
        @foreach ($items as $it)
        <a class="nav-item {{ $activeNav === $it['key'] ? 'active' : '' }}" href="{{ $it['href'] }}">{{ $it['label'] }}@if($it['badge'] ?? false)<span class="nav-badge" id="navPendingBadge"></span>@endif</a>
        @endforeach
    </div>

    <div class="top-right">
        <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
            <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </button>

        <button class="notif-btn" onclick="alert('Notifications panel')" aria-label="Notifications">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="notif-badge" id="notifBadge">{{ $notifCount ?? 3 }}</span>
        </button>

        <div class="user-panel">
            <div class="user-profile">
                <div class="user-avatar">KL</div>
                <div class="user-info">
                    <span class="user-name">Kylian Mbappe</span>
                    <span class="user-id">25SMR10186</span>
                    <span class="user-role">Lecturer</span>
                </div>
            </div>

            <button class="logout-btn" onclick="alert('Logout')" aria-label="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </button>
        </div>

        <button class="nav-hamburger" id="navHamburger" aria-label="Open navigation menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>
</div>

<div class="nav-drawer-overlay" id="navDrawerOverlay"></div>

<div class="nav-drawer" id="navDrawer">
    <div class="nav-drawer-header">
        <span class="nav-drawer-title">Navigation</span>
        <button class="nav-drawer-close" id="navDrawerClose" aria-label="Close navigation menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <div class="nav-drawer-items">
        @foreach ($items as $it)
        <a class="nav-drawer-item {{ $activeNav === $it['key'] ? 'active' : '' }}" href="{{ $it['href'] }}">{{ $it['label'] }}</a>
        @endforeach
    </div>
    <div class="nav-drawer-user">
        <div class="nav-drawer-user-info">
            <div class="nav-drawer-user-avatar">KL</div>
            <div class="nav-drawer-user-details">
                <span class="nav-drawer-user-name">Kylian Mbappe</span>
                <span class="nav-drawer-user-id">25SMR10186</span>
                <span class="nav-drawer-user-role">Lecturer</span>
            </div>
        </div>
        <button class="nav-drawer-logout" onclick="alert('Logout')" aria-label="Logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
        </button>
    </div>
</div>
