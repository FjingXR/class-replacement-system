<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Class Replacement System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            var saved = localStorage.getItem('theme');
            if (saved === 'light') {
                document.documentElement.className = 'light';
            }
        })();
    </script>
    <link rel="stylesheet" href="/css/theme.css">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--color-bg);
            color: var(--color-on-bg);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background var(--transition), color var(--transition);
        }

        .top-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            height: 56px;
            display: flex;
            align-items: center;
            background: var(--color-primary-container);
            padding: 0 20px;
            gap: 0;
        }

        .top-logo {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            cursor: pointer;
            margin-right: 24px;
        }
        .top-logo img {
            height: 48px;
            width: auto;
            display: block;
        }

        .nav-items {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex: 1;
            height: 100%;
        }
        .nav-item {
            height: 100%;
            display: flex;
            align-items: center;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--color-on-primary-container);
            text-decoration: none;
            position: relative;
            cursor: pointer;
            transition: background 0.15s, opacity 0.15s;
            white-space: nowrap;
            opacity: 0.75;
            border-radius: 0;
        }
        .nav-item:hover {
            opacity: 1;
            background: rgba(7,27,51,0.06);
        }
        .nav-item.active {
            opacity: 1;
            font-weight: 600;
            background: var(--color-tertiary-container);
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            padding-left: 20px;
            height: 100%;
        }

        .theme-toggle {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            transition: background 0.15s, transform 0.15s, opacity 0.15s;
            flex-shrink: 0;
        }
        .theme-toggle:hover {
            opacity: 0.85;
            transform: scale(1.08);
        }
        .theme-toggle:active {
            transform: scale(0.95);
        }

        .notif-btn {
            position: relative;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, opacity 0.15s;
            flex-shrink: 0;
        }
        .notif-btn:hover {
            opacity: 0.85;
        }
        .notif-badge {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: var(--color-error);
            color: var(--color-on-error);
            font-size: 9px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 2px var(--color-primary-container);
        }

        .user-panel {
            display: flex;
            align-items: center;
            gap: 2px;
            background: var(--color-secondary-container);
            border-radius: 50px;
            padding: 3px 4px 3px 8px;
            border: 1px solid rgba(12,51,33,0.12);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--color-on-secondary-container);
            user-select: none;
        }
        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--color-on-secondary-container);
            color: var(--color-secondary-container);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }
        .user-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .user-name {
            font-size: 12px;
            font-weight: 600;
        }
        .user-role {
            font-size: 10px;
            opacity: 0.7;
        }

        .logout-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--color-on-secondary-container);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .logout-btn:hover {
            background: rgba(12,51,33,0.1);
            color: var(--color-error);
        }

        .app-container {
            width: 100%;
            max-width: 100%;
            padding: 16px 24px;
            padding-top: 72px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .app-container { padding: 10px 12px; padding-top: 66px; }
            .top-logo { margin-right: 12px; }
            .nav-items { gap: 2px; }
            .nav-item { padding: 0 8px; font-size: 12px; }
            .user-info { display: none; }
        }

        @yield('page-styles')
    </style>
</head>
<body>

    @include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? ''])

    <div class="app-container">
        @yield('content')
    </div>

    <script>
        function updateIcon(isDark) {
            const icon = document.getElementById('theme-icon');
            if (!icon) return;
            icon.innerHTML = isDark
                ? '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>'
                : '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
        }

        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            html.classList.toggle('light');
            html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
            updateIcon(!isDark);
        }

        function navigateHome() {
            window.location.href = '/';
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateIcon(document.documentElement.classList.contains('dark'));
        });

        @yield('page-scripts')
    </script>
</body>
</html>
