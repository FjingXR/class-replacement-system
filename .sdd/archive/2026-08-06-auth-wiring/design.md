# Design: Auth Wiring

## Architecture

No new migrations or models. Changes span existing files + 3 new files:

| File | Change type |
|------|------------|
| `app/Providers/FortifyServiceProvider.php` | Modify — redirect, session lifetime, lockout |
| `bootstrap/app.php` | Modify — register new middleware |
| `app/Http/Middleware/EnsureSessionLifetime.php` | **NEW** — role-based session expiry |
| `resources/views/partials/ui-nav-bar.blade.php` | Modify — user panel, logout, indicator |
| `resources/views/auth/login-student.blade.php` | Modify — remember me checkbox |
| `resources/views/auth/login-staff.blade.php` | Modify — remember me checkbox, lockout error |
| `resources/views/partials/ui-session-countdown.blade.php` | **NEW** — countdown banner |
| `public/js/session-countdown.js` | **NEW** — countdown timer |
| `public/js/auto-logout.js` | **NEW** — staff idle tracker |
| `resources/views/partials/ui-lockout-countdown.blade.php` | **NEW** — lockout countdown banner |
| `public/js/lockout-countdown.js` | **NEW** — lockout countdown timer |
| `config/fortify.php` | No changes (redirectUsing overrides home) |

## Key decisions

### 1. User panel wiring + logout form

Replace hardcoded values in `ui-nav-bar.blade.php` (desktop lines 37–53, mobile drawer lines 83–99):

```blade
@auth
<div class="user-panel">
    <div class="user-profile">
        <div class="user-avatar">{{ Auth::user()->initials() }}</div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-id">{{ Auth::user()->loginId() }}</span>
            <span class="user-role">{{ ucfirst(Auth::user()->role) }}</span>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="logout-btn" type="submit" aria-label="Logout">
            {{-- existing SVG icon --}}
        </button>
    </form>
</div>
@endauth

@guest
<a href="/login/student" class="login-link">Login</a>
@endguest
```

Same pattern for mobile nav drawer. Uses existing `Auth::user()->initials()` and `Auth::user()->loginId()` model methods — no custom helpers needed.

### 2. Role-based post-login redirect

Use `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()`:

```php
Fortify::redirectUsing(function () {
    $user = Auth::user();
    if ($user->isStudent()) return '/student-my-timetable-ui';
    if ($user->isLecturer()) return '/my-timetable-ui';
    return config('fortify.home'); // fallback for unknown roles
});
```

Falls back to `config('fortify.home')` (`/dashboard`) if role is undefined.

### 3. Role-based session lifetime

**Problem:** `config('session.lifetime')` is read by Laravel's session driver for garbage collection. It acts as a hard ceiling — a session cannot outlive it regardless of middleware.

**Solution:** Set `config/session.php` `lifetime` to `43200` (30 days — the maximum role lifetime). This removes the ceiling. Then enforce per-role expiry via middleware.

- `config/session.php`: change `lifetime` from `1` to `43200`. Add comment: `[SESSION LIFETIME] Hard ceiling — 30 days (max role lifetime). Per-role expiry enforced by EnsureSessionLifetime middleware.`
- On login (inside `authenticateUsing`), store the intended lifetime:
  ```php
  $minutes = $user->isStudent() ? 43200 : 30; // 30 days or 30 min (production)
  // Testing mode: change staff to 1 min. Comment: // Production: 30 | Testing: 1
  session(['role_lifetime' => $minutes]);
  ```
- Create `app/Http/Middleware/EnsureSessionLifetime.php`:
  - On every request, updates a custom session timestamp: `session(['_auth_last_activity' => now()->timestamp])`
  - Reads `session('role_lifetime')` (fallback: `config('session.lifetime')`)
  - Reads `session('_auth_last_activity')` — the timestamp set by this middleware on previous requests (first request after login will be null → use login timestamp)
  - Compares `now()->timestamp - session('_auth_last_activity')` against `session('role_lifetime') * 60`
  - If expired: flush session, redirect to `/login` with error "Session expired. Please log in again."
  - On first request after login (`_auth_last_activity` is null): sets it to `now()->timestamp` (session just started, not expired)
- Register in `bootstrap/app.php`:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->alias(['role' => CheckRole::class, 'pl' => CheckPl::class]);
      $middleware->append(EnsureSessionLifetime::class);
  })
  ```
- **NFR 2.4 deviation:** Student 30-day lifetime is a deliberate deviation from NFR 2.4's blanket "30 min". Justification: students are view-only, low risk. Tracked in this design doc under "NFR overrides" section below.

### 4. Staff login lockout

All lockout logic guarded by `if ($loginType === 'staff') { ... }` — no lockout runs for students.

**Error message delivery:** When locked out, throw `Illuminate\Validation\ValidationException` with the custom message. Fortify catches this and displays it in the login form's `$errors` bag (which both login views already render).

```php
if ($loginType === 'staff') {
    $lockout = Cache::get("login_lockout:{$loginId}");
    if ($lockout) {
        throw ValidationException::withMessages([
            'login_id' => "Account locked. Try again in {$lockout['minutes']} min. Forgot password? Reset at TARUMT intranet.",
        ]);
    }
}
```

On failed auth (null return, staff only, staff ID exists in DB):
```php
$failKey = "login_fail:{$loginId}";
$attempts = Cache::get($failKey, 0) + 1;
Cache::put($failKey, $attempts, 600); // 10 min TTL
if ($attempts >= 3) {
    Cache::put("login_lockout:{$loginId}", ['minutes' => 10], 600);
    Cache::forget($failKey);
}
```

On successful auth (staff):
```php
Cache::forget("login_fail:{$loginId}");
Cache::forget("login_lockout:{$loginId}");
```

**Coexistence with Fortify rate limiter:** The existing `configureRateLimiting()` (5/min per IP+login_id) is KEPT. Cache lockout is per-user-ID, Fortify throttle is per-IP — they layer.

### 5. Session countdown (A3)

**Data contract:** The Blade partial renders a container div with data attributes:

```blade
<div class="session-countdown"
     data-lifetime="{{ session('role_lifetime', config('session.lifetime')) }}"
     data-last-activity="{{ session('_auth_last_activity', now()->timestamp) }}"
     style="display: none;">
    <span class="countdown-text">Session expires in <span id="countdown-min">--</span> min</span>
    <button onclick="extendSession()" class="btn-extend">Still here?</button>
    <form id="countdown-logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
        @csrf
    </form>
</div>
```

**JS (`session-countdown.js`):**
- Reads `data-lifetime` and `data-last-activity` from container
- Computes `remaining = (lastActivity + lifetime*60) - Date.now()/1000`
- When remaining < 120s: show banner, update countdown text every second
- "Still here?" button: `location.reload()` (refreshes `_auth_last_activity` via EnsureSessionLifetime middleware)
- When remaining <= 0: auto-submit `#countdown-logout-form`

**Inclusion:** `@include('partials.ui-session-countdown')` in `resources/views/layouts/ui-template.blade.php` (the shared layout), inside `@auth` block, after the nav bar. Ensures it renders for all authenticated users on every page, after session is started.

**No new route needed** — extend is just a page reload. The `/logout` route already exists via Fortify.

### 6. Auto-logout — staff only (C4)

**Idle tracking:** JS tracks `mousemove`, `keydown`, `click` on `document`. Resets a debounce timer on each event.

**Timing:** At 25 min idle → show warning modal ("You've been idle for 25 min. Session expires in 5 min."). At 30 min → auto-submit logout form. Aligned with staff session lifetime.

**Multi-tab coordination:** Use `BroadcastChannel('idle-sync')` to broadcast activity across tabs. Fallback: `localStorage` event (set a key on activity, listen for `storage` event in other tabs). If neither is available, each tab tracks independently.

**Loading:** Only inject `<script src="/js/auto-logout.js">` when `auth()->user()->isLecturer()`. Place the script tag and hidden logout form in `resources/views/partials/ui-nav-bar.blade.php` (included on every page via the layout), after the user panel section.

**Logout form:** Hidden form in the nav bar that JS auto-submits:
```html
<form id="auto-logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
</form>
```

### 7. Remember me (A1)

Add before the submit button in both login forms:
```html
<label class="remember-me">
    <input type="checkbox" name="remember" value="1"> Remember me
</label>
```

No PHP changes — Fortify's `AttemptToAuthenticate` checks `$request->boolean('remember')` automatically.

### 8. Remember me help text

Below the remember me checkbox in each login form, add context-aware help text:

**Student login (`login-student.blade.php`):**
```html
<p class="remember-hint">Keep me logged in for 30 days</p>
```

**Staff login (`login-staff.blade.php`):**
```html
<p class="remember-hint">Keep me logged in for 30 minutes</p>
```

CSS in `theme.css`:
```css
.remember-hint {
    font-size: 0.75rem;
    color: var(--color-muted);
    margin-top: 0.25rem;
}
```

### 9. Lockout error with countdown (Feature 11)

**Blade partial (`ui-lockout-countdown.blade.php`):**
```blade
@props(['expiresAt'])
<div class="lockout-countdown" data-expires="{{ $expiresAt }}" style="display: none;">
    <div class="lockout-banner">
        <svg class="icon-warning"><!-- warning icon --></svg>
        <span class="lockout-text">Account locked. Try again in <span class="lockout-timer">--:--</span>.</span>
    </div>
    <p class="lockout-hint">Forgot password? Reset at <a href="https://intranet.tarumt.edu.my" target="_blank">TARUMT intranet</a>.</p>
</div>
```

**JS (`lockout-countdown.js`):**
- Reads `data-expires` (Unix timestamp)
- Computes remaining = `expires - Date.now()/1000`
- Updates `.lockout-timer` every second: `{min} min {sec} sec`
- When remaining <= 0: hide `.lockout-countdown`, re-enable login button

**Backend:** In `FortifyServiceProvider`, when throwing `ValidationException` for lockout, also flash the lockout expiry:
```php
session(['lockout_expires' => now()->addMinutes(10)->timestamp]);
throw ValidationException::withMessages([...]);
```

**In `login-staff.blade.php`:**
```blade
@if(session('lockout_expires'))
    @include('partials.ui-lockout-countdown', ['expiresAt' => session('lockout_expires')])
@endif
```

### 10. Session expiry modal at 60s (Feature 12)

Extends `session-countdown.js`. At 60s remaining, show a blocking modal:

**Modal markup in `ui-session-countdown.blade.php`:**
```blade
<div class="session-modal-overlay" id="sessionModal" style="display: none;">
    <div class="session-modal">
        <h3>Session Expiring Soon</h3>
        <p>Your session expires in <span id="modal-countdown">60</span> seconds.</p>
        <div class="session-modal-actions">
            <button onclick="location.reload()" class="btn-primary">Stay logged in</button>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-secondary">Logout</button>
            </form>
        </div>
    </div>
</div>
```

**JS logic in `session-countdown.js`:**
- At 120s: show banner (existing)
- At 60s: show modal overlay, disable page interaction
- "Stay logged in" → `location.reload()` (refreshes `_auth_last_activity`)
- At 0s: auto-submit logout form (existing)

CSS: `.session-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; }`

### 11. Session indicator tooltip (Feature 13)

**In `ui-nav-bar.blade.php`:**
```blade
@auth
<span class="session-dot" title="Session active"></span>
@endauth
```

Change the existing `title="Session active"` — this was already in the SDD but now explicitly called out as a distinct feature. No additional CSS needed.

### 12. Session indicator (B1)

`@auth` block in nav bar near user panel:
```blade
@auth
<span class="session-dot" title="Session active"></span>
@endauth
```

CSS: `.session-dot { background: var(--color-secondary); border-radius: 50%; width: 8px; height: 8px; display: inline-block; }`

## NFR overrides

| NFR | Override | Justification | Approved by |
|-----|----------|--------------|-------------|
| NFR 2.4 (30 min session) | Student sessions: 30 days | Students are view-only, low risk, better UX | User decision 2026-08-03 |

## Frontend approach

All placeholder UI must reuse existing frontend patterns from the codebase:
- **CSS tokens** from `public/css/theme.css` — use `var(--color-*)` tokens, never hardcode hex
- **JS helpers** from `public/js/ui-common.js` — reuse existing functions where applicable
- **Blade patterns** from existing partials — follow the `@extends`/`@include` OOP structure
- **Component style** — match the look of existing UI elements (buttons, badges, modals) for consistency

The frontend SDD later will polish these placeholders (responsive, animation, accessibility). For now, make them **functional and visually consistent** with the existing design system.

## Promoted to shared

None — all changes are in existing files or new standalone files.
