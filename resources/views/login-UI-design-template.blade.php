<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Class Replacement System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1A5FB4', hover: '#154d94', light: '#8DB5E6', container: '#A7C3E6', 'on-container': '#071B33' },
                        secondary: { DEFAULT: '#2EC27E', hover: '#26a86d', light: '#97E6C2', container: '#AEE6CC', 'on-container': '#0C3321' },
                        tertiary: { DEFAULT: '#E7EB63', light: '#E3E6AA', container: '#E4E6BB', 'on-container': '#323315' },
                        error: { DEFAULT: '#B3261E', light: '#E69490', container: '#E6ACA9' },
                        surface: { DEFAULT: '#313233', light: '#fbfcfc', variant: '#545c66', 'variant-light': '#d9dfe6' },
                        outline: { DEFAULT: '#9fa8b3', light: '#7f8b99' },
                    }
                }
            }
        }
    </script>
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
        :root {
            --input-bg: color-mix(in srgb, var(--color-surface-variant) 30%, transparent);
            --input-border: var(--color-outline);
            --card-bg: color-mix(in srgb, var(--color-surface) 65%, transparent);
            --card-border: var(--color-outline);
            --shadow-card: var(--shadow-lg);
        }
        .light {
            --input-bg: color-mix(in srgb, var(--color-surface-variant) 40%, transparent);
            --input-border: var(--color-outline);
            --card-bg: color-mix(in srgb, var(--color-surface) 60%, transparent);
            --card-border: var(--color-outline);
            --shadow-card: 0 24px 80px rgba(0,0,0,0.08);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: var(--color-bg);
            color: var(--color-on-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .bg-layer {
            position: fixed;
            inset: 0;
            background: url('/images/login-bg.jpg') center/cover no-repeat;
            filter: saturate(0.9) brightness(0.45);
            transition: filter 0.6s ease;
            will-change: transform;
            animation: bgZoom 20s ease-in-out infinite alternate;
        }
        .light .bg-layer { filter: saturate(1.05) brightness(0.65) contrast(1.05); }

        @keyframes bgZoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.08); }
        }

        .bg-overlay {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% 50%, rgba(0,0,0,0.25) 0%, transparent 100%),
                radial-gradient(ellipse 40% 40% at 20% 20%, rgba(141, 181, 230, 0.06) 0%, transparent 100%),
                radial-gradient(ellipse 40% 40% at 80% 80%, rgba(151, 230, 194, 0.06) 0%, transparent 100%);
            pointer-events: none;
        }

        .theme-toggle {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 20;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            color: var(--color-on-surface-variant);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: background 0.25s, transform 0.2s, border-color 0.25s;
            animation: fadeSlideDown 0.6s ease 0.2s both;
        }
        .theme-toggle:hover { transform: scale(1.08); background: var(--input-bg); border-color: var(--color-outline); }
        .theme-toggle:active { transform: scale(0.95); }

        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            margin: 24px;
            padding: 48px 44px 40px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            transition: background 0.4s ease, border-color 0.4s ease, box-shadow 0.4s ease;
            animation: fadeSlideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeSlideUp {
            0% { opacity: 0; transform: translateY(30px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes fadeSlideDown {
            0% { opacity: 0; transform: translateY(-16px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            0% { opacity: 0; transform: scale(0.85); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
            animation: fadeSlideUp 0.6s ease 0.15s both;
        }

        .logo-img {
            display: block;
            width: 100%;
            max-width: 280px;
            height: auto;
            animation: fadeSlideUp 0.5s ease 0.1s both;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-xl);
            background: var(--color-primary);
            color: var(--color-on-primary);
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            box-shadow: 0 4px 16px rgba(141, 181, 230, 0.25);
            transition: background 0.3s, color 0.3s, box-shadow 0.3s;
            animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s both;
            position: relative;
        }
        .logo-icon::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: var(--radius-xl);
            border: 1px solid rgba(141, 181, 230, 0.15);
        }
        .light .logo-icon {
            box-shadow: 0 4px 16px rgba(26, 95, 180, 0.2);
        }
        .light .logo-icon::after { border-color: rgba(26, 95, 180, 0.1); }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }
        .input-group:nth-child(1) { animation: fadeSlideUp 0.6s ease 0.25s both; }
        .input-group:nth-child(2) { animation: fadeSlideUp 0.6s ease 0.32s both; }

        .input-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-on-surface-variant);
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: color 0.3s;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: var(--radius-lg);
            transition: border-color 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }
        .input-wrapper:hover { background: rgba(84, 92, 102, 0.18); }
        .light .input-wrapper:hover { background: rgba(217, 223, 230, 0.35); }
        .input-wrapper:focus-within {
            border-color: var(--color-primary);
            background: var(--input-bg);
            box-shadow: 0 0 0 3px rgba(141, 181, 230, 0.1);
        }
        .light .input-wrapper:focus-within { box-shadow: 0 0 0 3px rgba(26, 95, 180, 0.08); }

        .input-icon {
            padding: 0 0 0 14px;
            color: var(--color-on-surface-variant);
            font-size: 18px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
            opacity: 0.5;
            transition: opacity 0.3s, color 0.3s;
        }
        .input-wrapper:focus-within .input-icon { opacity: 0.8; color: var(--color-primary); }

        .input-wrapper input {
            width: 100%;
            padding: 16px 14px;
            background: transparent;
            border: none;
            outline: none;
            color: var(--color-on-surface);
            font-size: 15px;
            font-family: inherit;
            font-weight: 400;
            transition: color 0.3s;
        }
        .input-wrapper input::placeholder { color: rgba(212, 220, 230, 0.3); transition: color 0.3s; }
        .light .input-wrapper input::placeholder { color: rgba(84, 92, 102, 0.3); }
        .input-wrapper input:focus::placeholder { color: transparent; }

        .toggle-pw {
            padding: 0 14px 0 0;
            background: none;
            border: none;
            color: var(--color-on-surface-variant);
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 18px;
            opacity: 0.35;
            transition: opacity 0.25s, color 0.25s;
            flex-shrink: 0;
        }
        .toggle-pw:hover { opacity: 0.7; }
        .input-wrapper:focus-within .toggle-pw { opacity: 0.6; }

        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 24px 0 28px;
            animation: fadeSlideUp 0.6s ease 0.4s both;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            font-size: 14px;
            color: var(--color-on-surface-variant);
            user-select: none;
            transition: color 0.2s;
        }
        .remember-me:hover { color: var(--color-on-surface); }

        .remember-me input { display: none; }

        .check-box {
            width: 18px;
            height: 18px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--color-outline);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.15s ease;
            flex-shrink: 0;
            position: relative;
        }
        .remember-me:hover .check-box { transform: scale(1.05); }
        .remember-me input:checked + .check-box {
            background: var(--color-primary);
            border-color: var(--color-primary);
        }
        .check-box svg {
            width: 11px;
            height: 11px;
            opacity: 0;
            transition: opacity 0.15s ease, transform 0.2s ease;
            transform: scale(0.5);
        }
        .remember-me input:checked + .check-box svg { opacity: 1; transform: scale(1); }

        .forgot-link {
            font-size: 14px;
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
            position: relative;
        }
        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--color-primary);
            transition: width 0.25s ease;
        }
        .forgot-link:hover::after { width: 100%; }
        .forgot-link:hover { opacity: 0.85; }

        .login-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: var(--radius-md);
            background: var(--color-primary);
            color: var(--color-on-primary);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.15s ease, box-shadow 0.3s ease, background 0.25s ease, opacity 0.2s;
            font-family: inherit;
            position: relative;
            overflow: hidden;
            animation: fadeSlideUp 0.6s ease 0.48s both;
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(151, 230, 194, 0.2);
            background: #88dbb3;
        }
        .light .login-btn:hover { box-shadow: 0 8px 25px rgba(46, 194, 126, 0.2); background: #2db876; }
        .login-btn:active { transform: translateY(0) scale(0.99); }
        .login-btn:disabled { opacity: 0.5; cursor: var(--cursor-cancel); transform: none; box-shadow: none; }

        .login-btn .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid transparent;
            border-top-color: var(--color-on-primary);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }
        .login-btn.loading .spinner { display: block; }
        .login-btn.loading .btn-text { display: none; }
        .login-btn.loading .btn-arrow { display: none; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            transform: scale(0);
            animation: rippleAnim 0.5s ease-out;
            pointer-events: none;
        }
        .light .ripple { background: rgba(255,255,255,0.35); }
        @keyframes rippleAnim {
            to { transform: scale(6); opacity: 0; }
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0 0;
            color: var(--color-on-surface-variant);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.4;
            animation: fadeSlideUp 0.6s ease 0.55s both;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--input-border);
        }

        .register-link {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
            color: var(--color-on-surface-variant);
            animation: fadeSlideUp 0.6s ease 0.62s both;
        }
        .register-link a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        .register-link a:hover { opacity: 0.75; }

        @media (max-width: 480px) {
            .login-card { padding: 32px 24px 28px; margin: 12px; border-radius: var(--radius-lg); max-width: 100%; }
            .logo-img { max-width: 200px; }
            .login-btn { padding: 14px; }
            .theme-toggle { top: 16px; right: 16px; width: 36px; height: 36px; }
        }
        @media (max-width: 380px) {
            .login-card { padding: 28px 20px 24px; border-radius: var(--radius-xl); }
            .options-row { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
    </style>
</head>
<body>
    <div class="bg-layer"></div>
    <div class="bg-overlay"></div>

    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme" title="Toggle theme">
        <svg id="theme-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>

    <div class="login-card">
        <div class="logo-area">
            <img src="/images/logo_banner.png" alt="TAR UMT" class="logo-img">
        </div>

        <form id="loginForm" onsubmit="handleSubmit(event)">
            <div class="input-group">
                <label for="credential">Staff ID / Student ID / Email</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input type="text" id="credential" placeholder="e.g. 25SMR10178" autocomplete="username" spellcheck="false">
                </div>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input type="password" id="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password">
                    <button class="toggle-pw" type="button" onclick="togglePassword()" aria-label="Toggle password visibility">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="options-row">
                <label class="remember-me">
                    <input type="checkbox" checked>
                    <span class="check-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </span>
                    Remember me
                </label>
                {{-- <a href="#" class="forgot-link" onclick="return false;">Forgot password?</a> --}}
            </div>

            <button class="login-btn" type="submit" id="loginBtn">
                <span class="spinner"></span>
                <span class="btn-text">Log in</span>
                <svg class="btn-arrow" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const pw = document.getElementById('password');
            const eye = document.getElementById('eye-icon');
            const isHidden = pw.type === 'password';

            pw.type = isHidden ? 'text' : 'password';
            eye.innerHTML = isHidden
                ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>'
                : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }

        function updateIcon(isDark) {
            const icon = document.getElementById('theme-icon');
            if (!icon) return;
            icon.innerHTML = isDark
                ? '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>'
                : '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
        }

        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            const isDark = html.classList.contains('dark');

            html.classList.toggle('light');
            html.classList.toggle('dark');

            localStorage.setItem('theme', isDark ? 'light' : 'dark');

            updateIcon(!isDark);
        }

        function ripple(e, btn) {
            const rect = btn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            const el = document.createElement('span');
            el.className = 'ripple';
            el.style.width = el.style.height = size + 'px';
            el.style.left = x + 'px';
            el.style.top = y + 'px';
            btn.appendChild(el);
            setTimeout(() => el.remove(), 500);
        }

        function handleSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            if (btn.classList.contains('loading')) return;
            btn.classList.add('loading');
            setTimeout(() => btn.classList.remove('loading'), 2000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateIcon(document.documentElement.classList.contains('dark'));
            document.getElementById('credential').focus();
        });

        document.querySelectorAll('.login-btn').forEach(btn => {
            btn.addEventListener('mousedown', e => ripple(e, btn));
        });
    </script>
</body>
</html>
