# Design: Logout + Session Timeout + Login Redirect + User Panel

## Architecture

No new classes, migrations, or components. All changes are in existing files:
- `app/Providers/FortifyServiceProvider.php` — role-based redirect, session lifetime, staff lockout
- `resources/views/partials/ui-nav-bar.blade.php` — user panel wiring, logout form, session indicator
- `resources/views/auth/login-student.blade.php` — remember me checkbox
- `resources/views/auth/login-staff.blade.php` — remember me checkbox, lockout error display
- `resources/views/partials/ui-session-countdown.blade.php` — NEW, session countdown banner
- `public/js/session-countdown.js` — NEW, countdown timer logic
- `public/js/auto-logout.js` — NEW, staff-only inactivity tracker
- `config/fortify.php` — no changes (redirectUsing overrides home)

## Key decisions

### Role-based post-login redirect
Use `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()`. This overrides the `config/fortify.php` `home` value per-request. No middleware needed.

### Role-based session lifetime
Set `config('session.lifetime')` after successful authentication. In `Fortify::authenticateUsing()`, after returning `$user`, the session lifetime is already set by the time the response is generated. However, `authenticateUsing` returns the user — it doesn't have access to the response. Better approach: use a `Login` event listener or set it in middleware.

**Chosen approach:** Use `Fortify::authenticateUsing()` to return the user, then use an `after` middleware or `Event::listen(LoginSuccessful::class)` to set `config('session.lifetime')` based on role. Alternatively, set it directly in `authenticateUsing` before returning — but `config()` changes at runtime are per-request and don't persist to the session.

**Simplest approach:** In `FortifyServiceProvider::boot()`, listen for the `LoginSuccessful` event and set `config('session.lifetime')` there. Or: override in `authenticateUsing` by setting `Session::put('login_lifetime', ...)` and use middleware to enforce.

**Final chosen approach:** Store the intended lifetime in the session on login (`Session::put('lifetime_minutes', $minutes)`), and use a middleware or `config/session.php` lifetime override. Since `config('session.lifetime')` is read once at session creation, the cleanest way is to set it **before** the session is created — in the login request. Use `Fortify::authenticateUsing()` to set `config(['session.lifetime' => $minutes])` before returning the user. This works because the session hasn't been started yet at that point.

### Staff login lockout
In `FortifyServiceProvider::authenticateUsing()`:
1. If `login_type === 'staff'`, check `Cache::get("login_lockout:{$loginId}")`. If locked, return null with error message.
2. On failed auth (return null): increment `Cache::get("login_fail:{$loginId}")`. If count >= 3, set `Cache::put("login_lockout:{$loginId}", true, 10 minutes)`.
3. On successful auth: forget both cache keys.

### Session countdown
Client-side estimation: JS reads a `data-lifetime` attribute (set from `config('session.lifetime')` via Blade) and counts down. Shows banner when < 2 min remaining. Auto-submits logout form at 0.

### Auto-logout (staff only)
JS tracks `mousemove`, `keydown`, `click` on `document`. Resets idle timer on activity. At 5 min idle → show warning modal. At 6 min → auto-submit logout. Only loaded when `auth()->user()->isLecturer()`.

### User panel initials
Dynamic from `Auth::user()->name`: split by space, take first letter of first + last name. `str_word_count()` + array access. e.g. "Poong Foo Jing" → "PJ", "Kylian Mbappe" → "KM".

## Promoted to shared

None — all changes are in existing files or new standalone files.
