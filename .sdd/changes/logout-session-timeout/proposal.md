# Proposal: Logout + Session Timeout + Login Redirect + User Panel

## Why

The current nav bar (`ui-nav-bar.blade.php`) has hardcoded user data ("Kylian Mbappe", "Lecturer", "KL") and a dummy logout button (`onclick="alert('Logout')"`). Students have no dedicated post-login landing page. Session timeout is a flat 1 min for all roles. There is no "remember me" option, no session expiry warning, and no brute-force protection on staff accounts.

This SDD wires up the auth infrastructure that the rest of the app depends on: real logout, real user display, role-based redirects, role-based session lifetimes, and security features (lockout, countdown, auto-logout).

## Scope

### In scope

1. **User panel wiring** — Replace 4 hardcoded values in `ui-nav-bar.blade.php` (avatar initials, name, role, logout button) with dynamic data from `Auth::user()`. Add student_id/staff_id below role. Apply same changes to nav drawer (lines 83–99). Wrap in `@auth`/`@guest` guards.
2. **Logout form** — Replace `onclick="alert('Logout')"` with `<form method="POST" action="{{ route('logout') }}">` + `@csrf` + `<button type="submit">`. Both desktop user panel and mobile nav drawer.
3. **Role-based post-login redirect** — Override Fortify `home` via `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()`: student → `/student-my-timetable-ui`, staff → `/my-timetable-ui`.
4. **Role-based session lifetime** — Students: 30 days (43200 min). Staff: 30 min (or 1 min for testing). Set `config('session.lifetime')` per-role after login.
5. **A1 Remember me** — Add checkbox to both login forms (`login-student.blade.php`, `login-staff.blade.php`). Fortify built-in `remember` feature handles the rest.
6. **A3 Session expiry countdown** — New Blade partial `ui-session-countdown.blade.php` + `public/js/session-countdown.js`. Shows banner when session is near expiry. Placeholder design.
7. **B1 Active session indicator** — Green dot in nav bar near user panel when `auth()->check()`. Placeholder design.
8. **Staff login lockout** — Cache-based. 3 consecutive failed logins with same staff ID → lock for 10 min. After unlock, 3 fresh attempts → lock again. Hint: "Forgot password? Reset at TARUMT intranet." Staff-only — students exempt.
9. **C4 Auto-logout (staff only)** — JS tracks mousemove/keydown/click. If staff idle for X min → warning modal → auto-submit logout. Students are exempt (30-day session, no need).

### Out of scope

- Frontend design refinement for A1/A3/B1/C4 (placeholder only — separate frontend SDD later)
- Password change, 2FA, session management, login audit trail (skipped per user decision)
- New migrations, models, Livewire components, or composer dependencies

## FR/NFR traceability

| Ref | Feature | This SDD |
|-----|---------|----------|
| FR 1.1 | Login | Exists — no changes to login logic |
| NFR 2.4 | Session timeout | Role-based: student 30d, staff 30min (1min testing) |
| — | Logout | Wire nav bar button to existing Fortify route |
| — | Post-login redirect | Role-based via Fortify::redirectUsing |
| — | Staff lockout | Cache-based, 3 fails → 10 min lock |
