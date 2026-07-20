<div class="top-bar">
    <div class="top-logo" onclick="navigateHome()">
        <img src="/images/logo_banner.png" alt="TAR UMT">
    </div>

    <div class="nav-items">
        <a class="nav-item {{ $activeNav === 'dashboard' ? 'active' : '' }}" href="/dashboard">Dashboard</a>
        <a class="nav-item {{ $activeNav === 'my-timetable' ? 'active' : '' }}" href="/my-timetable-ui">My Timetable</a>
        <a class="nav-item {{ $activeNav === 'cohort-timetables' ? 'active' : '' }}" href="#">Cohort Timetables</a>
        <a class="nav-item {{ $activeNav === 'replacement-arrangement' ? 'active' : '' }}" href="/replacement-arrangement">Replacement Arrangement</a>
        <a class="nav-item {{ $activeNav === 'replacement-history' ? 'active' : '' }}" href="/my-request-history-ui">Replacement History</a>
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
            <span class="notif-badge">3</span>
        </button>

        <div class="user-panel">
            <div class="user-profile">
                <div class="user-avatar">KL</div>
                <div class="user-info">
                    <span class="user-name">Kylian Mbappe</span>
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
    </div>
</div>
