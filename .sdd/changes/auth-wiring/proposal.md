# Proposal: Auth Wiring

## Why

The current nav bar (`ui-nav-bar.blade.php`) has hardcoded user data ("Kylian Mbappe", "Lecturer", "KL") and a dummy logout button (`onclick="alert('Logout')"`). Students have no dedicated post-login landing page. Session timeout is a flat 1 min for all roles. There is no "remember me" option, no session expiry warning, and no brute-force protection on staff accounts.

This SDD wires up the auth infrastructure that the rest of the app depends on: real logout, real user display, role-based redirects, role-based session lifetimes, and security features (lockout, countdown, auto-logout).

## Scope

### In scope

1. **User panel wiring** — Replace 4 hardcoded values in `ui-nav-bar.blade.php` (avatar initials, name, role, logout button) with dynamic data from `Auth::user()`. Add student_id/staff_id below role. Apply same changes to nav drawer (lines 83–99). Wrap in `@auth`/`@guest` guards.
2. **Logout form** — Replace `onclick="alert('Logout')"` with `<form method="POST" action="{{ route('logout') }}">` + `@csrf` + `<button type="submit">`. Both desktop user panel and mobile nav drawer.
3. **Role-based post-login redirect** — Override Fortify `home` via `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()`: student → `/student-my-timetable-ui` (route exists in `routes/web.php`, template at `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`), staff → `/my-timetable-ui`.
4. **Role-based session lifetime** — Students: 30 days (43200 min). Staff: 30 min (or 1 min for testing). Implementation: on login, store `session(['role_lifetime' => $minutes])` into session data. Write a middleware `EnsureSessionLifetime` that reads `session('role_lifetime')` and compares `now - last_activity` against it, instead of relying on `config('session.lifetime')` (which is process-global and resets on each request). The `config/session.php` `lifetime` value becomes the fallback for sessions without a stored lifetime. *(Note: student 30-day lifetime is a deliberate deviation from NFR 2.4's blanket "30 min" — students are view-only, low risk.)*
5. **A1 Remember me** — Add checkbox to both login forms (`login-student.blade.php`, `login-staff.blade.php`). Fortify built-in `remember` feature handles the rest.
6. **A3 Session expiry countdown** — New Blade partial `ui-session-countdown.blade.php` + `public/js/session-countdown.js`. Shows banner when session is near expiry. Placeholder design.
7. **B1 Active session indicator** — Green dot in nav bar near user panel when `auth()->check()`. Placeholder design.
8. **Staff login lockout** — Cache-based. 3 consecutive failed logins with same staff ID → lock for 10 min. After unlock, 3 fresh attempts → lock again. Hint: "Forgot password? Reset at TARUMT intranet." **Staff-only — students exempt.** All lockout logic is guarded by `if ($loginType === 'staff') { ... }` — no cache checks, increments, or lockouts run for student logins. **Hook point:** inside `FortifyServiceProvider::authenticateUsing()`, BEFORE the DB query — check `Cache::get("login_lockout:{$loginId}")` first. If locked, return null immediately (no DB query). On failed auth (null return), increment `Cache::get("login_fail:{$loginId}")`; if count >= 3, set `Cache::put("login_lockout:{$loginId}", true, 10 minutes)`. On successful auth, clear both cache keys. Failed-attempt counting only increments when the staff ID actually exists in the database (avoid locking out non-existent IDs). **Relationship with existing rate limiter:** the existing `configureRateLimiting()` (5/min per IP+login_id) is KEPT as a separate layer. The cache-based lockout is per-user-ID (not per-IP), so both coexist: Fortify's rate limiter throttles rapid attempts from any source, while the cache lockout blocks a specific staff account after 3 consecutive failures. An attacker hitting the rate limiter gets slowed; an attacker targeting a specific staff ID gets locked out after 3.
9. **C4 Auto-logout (staff only)** — JS tracks mousemove/keydown/click. If staff idle for 25 min → warning modal → auto-submit logout at 30 min (aligned with staff session lifetime). Students are exempt (30-day session, no need).

10. **Remember me help text** — Context-aware help text below "Remember me" checkbox: *"Keep me logged in for 30 days"* (student) or *"Keep me logged in for 30 minutes"* (staff). Both login forms.
11. **Lockout error with countdown** — When staff is locked out, show a red error banner with live countdown timer: *"Account locked. Try again in {X} min {Y} sec. Forgot password? Reset at TARUMT intranet."* New Blade partial `ui-lockout-countdown.blade.php` + `public/js/lockout-countdown.js`. Staff login form only.
12. **Session expiry modal at 60s** — At 60s remaining, show a blocking modal overlay requiring user to click "Stay logged in" or "Logout". Banner at 120s stays as advance warning. Extends `session-countdown.js`.
13. **Session indicator tooltip** — Add `title="Session active"` on the B1 green dot for hover context. 1-line change in nav bar.

### Out of scope

- Frontend design refinement for A1/A3/B1/C4 (placeholder only — separate frontend SDD later)
- Password change, 2FA, session management, login audit trail (skipped per user decision)
- New migrations, models, Livewire components, or composer dependencies

## FR/NFR traceability

| Ref | Feature | This SDD |
|-----|---------|----------|
| FR 1.1 | Login | Exists — no changes to login logic |
| FR 4.13 | RBAC 3 roles | Auth infrastructure supports role-based routing + display |
| NFR 2.4 | Session timeout | Role-based: student 30d (deliberate deviation — view-only), staff 30min (1min testing) |
| NFR 2.5 | CSRF on POST | Logout uses POST + @csrf (feature 2) |
| NFR 3.4 | Role-appropriate redirect | Covered by feature 3 (post-login redirect) |
| — | Logout | Wire nav bar button to existing Fortify route |
| — | Post-login redirect | Role-based via Fortify::redirectUsing |
| — | Staff lockout | Cache-based, 3 fails → 10 min lock, coexists with Fortify rate limiter |
| NFR 3.3 | Remember me help text | Context-aware text explains what "remember me" does per role |
| NFR 3.3, 2.4 | Lockout countdown | Live countdown timer on lockout error banner |
| NFR 3.3, 2.4 | Session expiry modal | Blocking modal at 60s forces user attention before auto-logout |
| NFR 3.3 | Session indicator tooltip | Hover tooltip clarifies green dot meaning |
