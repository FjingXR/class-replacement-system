# Design: Login Pages OOP Refactor

## Technical Approach

Create a shared login layout (`layouts/login-template.blade.php`) that both login pages extend. The layout contains all shared HTML shell, CSS (~350 lines), and JS (~30 lines), parameterized via Blade variables. Each login page only provides its unique content via `@section`.

## Architecture Decisions

### 1. Layout Structure

**Decision:** Use `layouts/login-template.blade.php` as the base layout with named `@yield` sections and Blade variable parameters.

```blade
{{-- layouts/login-template.blade.php --}}
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
        {{-- ALL shared login CSS (~350 lines) --}}
        {{-- Uses $btnColorToken, $btnColorOnToken, $btnHoverDark, $btnHoverLight, $spinnerColorToken --}}
    </style>
    @yield('page-styles')
</head>
<body>
    <div class="bg-layer" style="background: url('{{ $bgImage }}') center/cover no-repeat; filter: saturate(0.9) brightness(0.45);"></div>
    
    @yield('content')
    
    <script>
        {{-- Shared validation function --}}
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
```

### 2. Blade Variable Parameters

**Decision:** Pass 12 parameters via `@extends` to fully parameterize the layout.

| Variable | Type | Staff Value | Student Value | Usage |
|----------|------|-------------|---------------|-------|
| `$title` | string | `"Staff"` | `"Student"` | `<title>`, `.page-title` |
| `$bgImage` | string | `"/images/staff-login-bg.jpg"` | `"/images/student-login-bg.jpg"` | `.bg-layer` background |
| `$btnColorToken` | string | `"--color-secondary"` | `"--color-primary"` | `.login-btn` background |
| `$btnColorOnToken` | string | `"--color-on-secondary"` | `"--color-on-primary"` | `.login-btn` color |
| `$btnHoverDark` | string | `"#88dbb3"` | `"#154d94"` | `.login-btn:hover` dark mode |
| `$btnHoverLight` | string | `"#2db876"` | `"#154d94"` | `.login-btn:hover` light mode |
| `$spinnerColorToken` | string | `"--color-on-secondary"` | `"--color-on-primary"` | `.spinner` border-top-color |
| `$idLabel` | string | `"Staff ID"` | `"Student ID"` | Label text |
| `$idPlaceholder` | string | `"e.g. 6767"` | `"e.g. 25RSD0001"` | Input placeholder |
| `$idRegex` | string | `"^\\d+$"` | `"^\\d{2}[A-Za-z]{3}\\d{4}$"` | JS validation |
| `$formatHint` | string | `"Numeric staff ID only"` | `"Format: 2 digits + 3 letters + 4 digits"` | Hint text |
| `$loginType` | string | `"staff"` | `"student"` | Hidden input value |
| `$roleSwitchHtml` | string | `"Student? <a href=\"...\">Student Login</a>"` | `"Staff? <a href=\"...\">Staff Login</a>"` | Role switch link |

### 3. CSS Parameterization

**Decision:** Use CSS custom properties set via inline `<style>` in the layout, parameterized by Blade variables.

```css
/* In layouts/login-template.blade.php <style> */
:root {
    --login-btn-bg: var({{ $btnColorToken }});
    --login-btn-color: var({{ $btnColorOnToken }});
    --login-btn-hover-dark: {{ $btnHoverDark }};
    --login-btn-hover-light: {{ $btnHoverLight }};
    --login-spinner-color: var({{ $spinnerColorToken }});
}

.login-btn {
    background: var(--login-btn-bg);
    color: var(--login-btn-color);
}

.login-btn:hover:not(:disabled) {
    background: var(--login-btn-hover-dark);
}

.light .login-btn:hover:not(:disabled) {
    background: var(--login-btn-hover-light);
}

.login-btn:disabled {
    background: var(--color-outline);
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.spinner {
    border-top-color: var(--login-spinner-color);
}
```

### 4. Child Page Structure

**Decision:** Each login page only provides the form content and any page-specific CSS overrides.

```blade
{{-- auth/login-staff.blade.php --}}
@extends('layouts.login-template', [
    'title' => 'Staff',
    'bgImage' => '/images/staff-login-bg.jpg',
    'btnColorToken' => '--color-secondary',
    'btnColorOnToken' => '--color-on-secondary',
    'btnHoverDark' => '#88dbb3',
    'btnHoverLight' => '#2db876',
    'spinnerColorToken' => '--color-on-secondary',
    'idLabel' => 'Staff ID',
    'idPlaceholder' => 'e.g. 6767',
    'idRegex' => '^\d+$',
    'formatHint' => 'Numeric staff ID only',
    'loginType' => 'staff',
    'roleSwitchHtml' => 'Student? <a href="' . route('login.student') . '">Student Login</a>',
])

@section('content')
    <div class="login-card">
        <div class="page-title">Staff Login</div>
        <form method="POST" action="{{ route('login.staff.post') }}">
            @csrf
            <input type="hidden" name="login_type" value="{{ $loginType }}">
            
            <div class="input-group">
                <label for="login_id">{{ $idLabel }}</label>
                <input type="text" id="login_id" name="login_id" 
                       placeholder="{{ $idPlaceholder }}" 
                       value="{{ old('login_id') }}" 
                       autocomplete="username" spellcheck="false" 
                       required oninput="validateLogin()">
                <div class="format-hint">{{ $formatHint }}</div>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" 
                           placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" 
                           autocomplete="current-password" 
                           required oninput="validateLogin()">
                    <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">
                        <svg>...</svg>
                    </button>
                </div>
            </div>
            
            <button class="login-btn" type="submit" id="loginBtn" disabled>
                <span class="spinner"></span>
                <span class="btn-text">Log in</span>
                <svg class="btn-arrow">...</svg>
            </button>
        </form>
        
        <div class="role-switch">{!! $roleSwitchHtml !!}</div>
    </div>
@endsection
```

### 5. Validation Function

**Decision:** Single `validateLogin()` function in the layout, using the `$idRegex` parameter.

```javascript
// In layouts/login-template.blade.php <script>
const idRegex = @json($idRegex);

function validateLogin() {
    const loginId = document.getElementById('login_id').value.trim();
    const password = document.getElementById('password').value;
    const btn = document.getElementById('loginBtn');
    
    const idValid = idRegex.test(loginId);
    const pwValid = password.length > 0;
    
    btn.disabled = !(idValid && pwValid);
}
```

## Dependencies

- `public/css/theme.css` — shared CSS tokens (loaded via layout)
- `public/js/ui-common.js` — shared JS functions: `updateIcon`, `ripple` (loaded via layout)
- `layouts/ui-template.blade.php` — NOT used (login pages have different structure)
- No new PHP/migrations/Livewire components

## File Changes

| File | Change |
|------|--------|
| `resources/views/layouts/login-template.blade.php` | **Create** — shared login layout (~200 lines) |
| `resources/views/auth/login-staff.blade.php` | **Modify** — refactor from 442 lines to ~80 lines (form only) |
| `resources/views/auth/login-student.blade.php` | **Modify** — refactor from 442 lines to ~80 lines (form only) |

## Known Residuals

- Button hover hex colors (`#88dbb3`, `#154d94`, etc.) are hardcoded — these are brand colors specific to staff/student themes and don't have CSS token equivalents. This is intentional.
- Background images (`staff-login-bg.jpg`, `student-login-bg.jpg`) are referenced by path — if images are renamed, both layout params must be updated.
- Button disabled style normalized to student style (`background: var(--color-outline); opacity: 0.4`) for both pages — staff previously used `opacity: 0.3` without background. Minor visual change, acceptable for consistency.
- Form action `/login` has no POST route — kept as-is for mock phase, ready for future backend wiring.
- `togglePassword()`, `ripple()`, `updateIcon()` confirmed as shared functions in `ui-common.js` — no changes needed.
