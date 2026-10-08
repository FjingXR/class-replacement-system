<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Class Replacement System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function() {
            var saved = localStorage.getItem('theme');
            if (saved && ['dark', 'light'].includes(saved)) {
                document.documentElement.className = saved;
            }
            // Default is 'dark' from HTML
        })();
    </script>
    {{-- Anti-FOUC guard: applies until theme.css loads. Extensions that force layout
         before stylesheets arrive (e.g. MetaMask) would otherwise paint the unstyled
         nav drawer fully visible at the left edge, then slide it away once CSS lands.
         theme.css's own `.nav-drawer { display: flex }` overrides this guard on load. --}}
    <style>
        html.dark  { background: #0D1B2A; }
        html.light { background: #F0F3F7; }
        .nav-drawer { display: none; }
    </style>
    <link rel="stylesheet" href="/css/theme.css">
    <style>
        @yield('page-styles')
    </style>
</head>
<body data-page="{{ $pageKey ?? '' }}">

    @if(!isset($hideNav) || !$hideNav)
        @include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? '', 'navItems' => $navItems ?? null])
        {{-- LOGOUT MODAL DISABLED — logout now submits the form directly (see ui-nav-bar)
        @include('partials.ui-logout-modal')
        --}}
    @endif


    <div class="app-container">
        @yield('content')
    </div>

    <script src="/js/mock-data.js?v=3"></script>
    <script src="/js/ui-common.js?v=7"></script>
    {{-- R-3 (round-3): role-aware logo target — each page declares its home;
         navigateHome() reads this, falling back to the welcome view --}}
    <script>window.PAGE_HOME = '{{ $homeUrl ?? '/' }}';</script>
    {{-- LOGOUT MODAL DISABLED
    <script src="/js/logout-modal.js"></script>
    --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var pageKey = document.body.dataset.page;
            if (pageKey) initScrollRestore(pageKey);
            updateIcon(document.documentElement.classList.contains('dark')); initMobileNav();
            if (typeof updateNavBadge === 'function') updateNavBadge();
            if (typeof initDataTipTooltips === 'function') initDataTipTooltips();
        });

        @yield('page-scripts')
    </script>

    <!-- Toast/Undo Bar (shared) -->
    <div class="toast-bar" id="toastBar">
        <div class="toast-content">
            <span class="toast-message"></span>
            <span class="toast-details"></span>
        </div>
        <div class="toast-actions">
            <a class="toast-link" href="#" style="display:none"></a>
            <button class="toast-undo" style="display:none">Undo</button>
            <button class="toast-close" onclick="toast.close()">✕</button>
        </div>
    </div>
</body>
</html>
