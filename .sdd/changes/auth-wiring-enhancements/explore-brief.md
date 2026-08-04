# Explore Brief: Auth Wiring Enhancements

## Summary

4 UX enhancements to the existing `auth-wiring` SDD, addressing missed features from FR/NFR review. All are frontend-only (Blade + JS + CSS), no new migrations or backend logic.

## Features

### 1. Remember me help text
- **What:** Context-aware help text below "Remember me" checkbox: *"Keep me logged in for 30 days"* (student) or *"Keep me logged in for 30 minutes"* (staff)
- **Where:** `login-student.blade.php`, `login-staff.blade.php`
- **NFR:** 3.3 (simple English), 3.4 (role-appropriate UX)

### 2. Lockout error with countdown
- **What:** Red error banner with live countdown timer when staff account is locked out: *"Account locked. Try again in {X} min {Y} sec. Forgot password? Reset at TARUMT intranet."*
- **Where:** `login-staff.blade.php` + new partial `ui-lockout-countdown.blade.php` + new JS `lockout-countdown.js`
- **Backend:** Pass lockout expiry timestamp via session/flash to view
- **NFR:** 3.3, 2.4

### 3. Session expiry modal at 60s
- **What:** Blocking modal overlay at 60s remaining (banner at 120s stays as advance warning). Modal requires user to click "Stay logged in" or "Logout".
- **Where:** Extend `session-countdown.js`, new modal markup in `ui-session-countdown.blade.php`
- **NFR:** 3.3, 2.4

### 4. Session indicator tooltip
- **What:** Add `title="Session active"` on the B1 green dot for hover context.
- **Where:** `ui-nav-bar.blade.php` (1-line change)
- **NFR:** 3.3

## Scope

- Frontend-only (Blade + JS + CSS)
- No new migrations, models, or composer dependencies
- Reuses existing patterns (theme.css tokens, ui-common.js helpers, modal patterns)
- Placeholder design (frontend SDD will polish later)

## Files to modify

| File | Change |
|------|--------|
| `resources/views/auth/login-student.blade.php` | Add remember me help text |
| `resources/views/auth/login-staff.blade.php` | Add remember me help text + lockout countdown |
| `resources/views/partials/ui-lockout-countdown.blade.php` | **NEW** — lockout countdown banner |
| `public/js/lockout-countdown.js` | **NEW** — lockout countdown timer |
| `resources/views/partials/ui-session-countdown.blade.php` | Add modal markup at 60s |
| `public/js/session-countdown.js` | Add modal show/hide logic at 60s |
| `resources/views/partials/ui-nav-bar.blade.php` | Add title attribute to session dot |
