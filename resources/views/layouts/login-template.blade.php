<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} Login — Class Replacement System</title>
    <script>
        (function() {
            var saved = localStorage.getItem('theme');
            if (saved === 'light') {
                document.documentElement.className = 'light';
            }
        })();
    </script>
    <script src="/js/ui-common.js"></script>
    <link rel="stylesheet" href="/css/theme.css">
    <style>
        :root {
            --input-bg: color-mix(in srgb, var(--color-surface-variant) 30%, transparent);
            --input-border: var(--color-outline);
            --card-bg: color-mix(in srgb, var(--color-surface) 65%, transparent);
            --card-border: var(--color-outline);
            --glass-blur: blur(28px);
            --shadow-card: var(--shadow-lg);
            --login-btn-bg: var({{ $btnColorToken }});
            --login-btn-color: var({{ $btnColorOnToken }});
            --login-btn-hover-dark: {{ $btnHoverDark }};
            --login-btn-hover-light: {{ $btnHoverLight }};
            --login-spinner-color: var({{ $spinnerColorToken }});
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
            background: url('{{ $bgImage }}') center/cover no-repeat;
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
            border-radius: 12px;
            border: 1px solid var(--card-border);
            background: var(--card-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
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
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--card-border);
            border-radius: 24px;
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

        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            animation: fadeSlideUp 0.6s ease 0.15s both;
        }

        .logo-img {
            display: block;
            width: 100%;
            max-width: 280px;
            height: auto;
            animation: fadeSlideUp 0.5s ease 0.1s both;
        }

        .page-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--color-on-surface);
            margin-bottom: 24px;
            animation: fadeSlideUp 0.6s ease 0.2s both;
        }

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
            border-radius: 14px;
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

        .login-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 14px;
            background: var(--login-btn-bg);
            color: var(--login-btn-color);
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
        .login-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(151, 230, 194, 0.2);
            background: var(--login-btn-hover-dark);
        }
        .light .login-btn:hover:not(:disabled) { box-shadow: 0 8px 25px rgba(46, 194, 126, 0.2); background: var(--login-btn-hover-light); }
        .login-btn:active:not(:disabled) { transform: translateY(0) scale(0.99); }
        .login-btn:disabled { background: var(--color-outline); opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }

        .login-btn .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid transparent;
            border-top-color: var(--login-spinner-color);
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

        .role-switch {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: var(--color-on-surface-variant);
            animation: fadeSlideUp 0.6s ease 0.55s both;
        }
        .role-switch a {
            color: var(--color-primary);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        .role-switch a:hover { opacity: 0.75; }

        .error-msg {
            background: color-mix(in srgb, var(--color-error) 15%, transparent);
            border: 1px solid var(--color-error);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
            color: var(--color-error);
            animation: fadeSlideUp 0.4s ease both;
        }

        .format-hint {
            font-size: 12px;
            color: var(--color-on-surface-variant);
            margin-top: 4px;
            padding-left: 2px;
            opacity: 0.6;
        }

        @media (max-width: 480px) {
            .login-card { padding: 32px 24px 28px; margin: 12px; border-radius: 20px; max-width: 100%; }
            .logo-img { max-width: 200px; }
            .login-btn { padding: 14px; }
            .theme-toggle { top: 16px; right: 16px; width: 36px; height: 36px; }
        }
        @media (max-width: 380px) {
            .login-card { padding: 28px 20px 24px; border-radius: 16px; }
        }
    </style>
    @yield('page-styles')
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

        <div class="page-title">{{ $title }} Login</div>

        <form method="POST" action="/login">
            @csrf
            <input type="hidden" name="login_type" value="{{ $loginType }}">

            @if ($errors->any())
                <div class="error-msg">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="error-msg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="input-group">
                <label for="login_id">{{ $idLabel }}</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input type="text" id="login_id" name="login_id" placeholder="{{ $idPlaceholder }}" value="{{ old('login_id') }}" autocomplete="username" spellcheck="false" required oninput="validateLogin()">
                </div>
                <div class="format-hint">{{ $formatHint }}</div>
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
                    <input type="password" id="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password" required oninput="validateLogin()">
                    <button class="toggle-pw" type="button" onclick="togglePassword()" aria-label="Toggle password visibility">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button class="login-btn" type="submit" id="loginBtn" disabled>
                <span class="spinner"></span>
                <span class="btn-text">Log in</span>
                <svg class="btn-arrow" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </form>

        <div class="role-switch">{!! $roleSwitchHtml !!}</div>
    </div>

    <script>
        const idRegex = @json($idRegex);

        function validateLogin() {
            const loginId = document.getElementById('login_id').value.trim();
            const password = document.getElementById('password').value;
            const btn = document.getElementById('loginBtn');

            const idValid = idRegex.test(loginId);
            const pwValid = password.length > 0;

            btn.disabled = !(idValid && pwValid);
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateIcon(document.documentElement.classList.contains('dark'));
            document.getElementById('login_id').focus();
            validateLogin();
        });

        document.querySelectorAll('.login-btn').forEach(btn => {
            btn.addEventListener('mousedown', e => ripple(e, btn));
        });
    </script>
    @yield('page-scripts')
</body>
</html>
