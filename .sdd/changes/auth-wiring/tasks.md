# Tasks: Auth Wiring

## Task 1: User panel wiring + logout form
- [ ] 1.1 Read `ui-nav-bar.blade.php` lines 37–53 (desktop) and 83–99 (mobile drawer)
- [ ] 1.2 Replace hardcoded `.user-avatar` "KL" with dynamic initials from `Auth::user()->name`
- [ ] 1.3 Replace hardcoded `.user-name` "Kylian Mbappe" with `Auth::user()->name`
- [ ] 1.4 Replace hardcoded `.user-id` "25SMR10186" with `Auth::user()->student?->student_id ?? Auth::user()->lecturer?->staff_id`
- [ ] 1.5 Replace hardcoded `.user-role` "Lecturer" with `Auth::user()->role`
- [ ] 1.6 Replace `onclick="alert('Logout')"` with `<form method="POST" action="{{ route('logout') }}">` + `@csrf` + `<button type="submit">`
- [ ] 1.7 Apply same changes to mobile nav drawer (lines 83–99)
- [ ] 1.8 Wrap user panel in `@auth` / `@guest` guards

## Task 2: Role-based post-login redirect
- [ ] 2.1 Add `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()` — student → `/student-my-timetable-ui`, staff → `/my-timetable-ui`

## Task 3: Role-based session lifetime
- [ ] 3.1 In `FortifyServiceProvider::authenticateUsing()`, set `config(['session.lifetime' => ...])` before returning `$user` — students: 43200 (30 days), staff: 1 (testing) / 30 (production)
- [ ] 3.2 Add `// [SESSION LIFETIME]` comment landmark near the change

## Task 4: A1 Remember me
- [ ] 4.1 Add "Remember me" checkbox to `login-student.blade.php` (between password field and login button)
- [ ] 4.2 Add "Remember me" checkbox to `login-staff.blade.php`
- [ ] 4.3 Ensure `authenticateUsing` passes `'remember'` to Fortify (check if needed or automatic)

## Task 5: Staff login lockout
- [ ] 5.1 In `authenticateUsing`, before DB query: check `Cache::get("login_lockout:{$loginId}")` for staff
- [ ] 5.2 On failed auth for staff: increment `Cache::get("login_fail:{$loginId}")`, set lockout if >= 3
- [ ] 5.3 On successful auth for staff: forget both cache keys
- [ ] 5.4 Lockout error message includes "Forgot password? Reset at TARUMT intranet"

## Task 6: A3 Session countdown
- [ ] 6.1 Create `resources/views/partials/ui-session-countdown.blade.php` — banner with countdown text + extend/Logout buttons
- [ ] 6.2 Create `public/js/session-countdown.js` — timer logic, shows banner near expiry, auto-logouts
- [ ] 6.3 Include partial in layout or nav bar

## Task 7: B1 Session indicator
- [ ] 7.1 Add green dot / "Session active" text in nav bar near user panel — `@auth` only

## Task 8: C4 Auto-logout (staff only)
- [ ] 8.1 Create `public/js/auto-logout.js` — tracks mousemove/keydown/click, warning at 5 min idle, logout at 6 min
- [ ] 8.2 Only load when `auth()->user()->isLecturer()`

## Task 9: Lint + verify
- [ ] 9.1 Run `composer run lint:check`
- [ ] 9.2 Run `composer run types:check`
- [ ] 9.3 Confirm no new failures
