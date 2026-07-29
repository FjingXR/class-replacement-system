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
        @yield('page-styles')
    </style>
</head>
<body>

    @if(!isset($hideNav) || !$hideNav)
        @include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? ''])
    @endif


    <div class="app-container">
        @yield('content')
    </div>

    <script src="/js/ui-common.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateIcon(document.documentElement.classList.contains('dark'));
        });

        @yield('page-scripts')
    </script>
</body>
</html>
