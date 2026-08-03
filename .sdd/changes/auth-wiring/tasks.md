# Tasks: Auth Wiring

## Task 1: User panel wiring + logout form
- [ ] 1.1 Read `ui-nav-bar.blade.php` — desktop user panel (lines 37–53) and mobile nav drawer (lines 83–99)
- [ ] 1.2 Desktop: Replace hardcoded `.user-avatar` "KL" with `{{ Auth::user()->initials() }}`
- [ ] 1.3 Desktop: Replace hardcoded `.user-name` "Kylian Mbappe" with `{{ Auth::user()->name }}`
- [ ] 1.4 Desktop: Replace hardcoded `.user-id` "25SMR10186" with `{{ Auth::user()->loginId() }}`
- [ ] 1.5 Desktop: Replace hardcoded `.user-role` "Lecturer" with `{{ ucfirst(Auth::user()->role) }}`
- [ ] 1.6 Desktop: Replace `onclick="alert('Logout')"` with `<form method="POST" action="{{ route('logout') }}">` + `@csrf` + `<button type="submit">` (keep existing SVG icon)
- [ ] 1.7 Desktop: Wrap entire `.user-panel` in `@auth` block; add `@guest` fallback with login link
- [ ] 1.8 Mobile nav drawer: Apply same dynamic data + logout form + @auth/@guest guards (lines 83–99)

## Task 2: Role-based post-login redirect
- [ ] 2.1 Add `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()` — student → `/student-my-timetable-ui`, staff → `/my-timetable-ui`, fallback → `config('fortify.home')`

## Task 3: Role-based session lifetime
- [ ] 3.1 Change `config/session.php` `lifetime` from `1` to `43200` — add `[SESSION LIFETIME]` comment landmark explaining this is the hard ceiling, per-role enforcement via middleware
- [ ] 3.2 In `FortifyServiceProvider::authenticateUsing()`, after returning `$user`: `session(['role_lifetime' => $user->isStudent() ? 43200 : 30])` — add comment `// Production: 30 | Testing: 1` for staff value
- [ ] 3.3 Create `app/Http/Middleware/EnsureSessionLifetime.php` — on each request: read `session('_auth_last_activity')` (null on first request → set to now). If non-null and expired (`now - last > role_lifetime * 60`) → flush session + redirect to `/login`. Otherwise update `_auth_last_activity` to `now()->timestamp`.
- [ ] 3.4 Register middleware in `bootstrap/app.php` via `->withMiddleware(fn($m) => $m->append(EnsureSessionLifetime::class))`

## Task 4: A1 Remember me
- [ ] 4.1 Add `<label class="remember-me"><input type="checkbox" name="remember" value="1"> Remember me</label>` to `login-student.blade.php` (between password field and login button)
- [ ] 4.2 Add same checkbox to `login-staff.blade.php`

## Task 5: Staff login lockout
- [ ] 5.1 In `authenticateUsing()`, before DB query, guarded by `if ($loginType === 'staff')`: check `Cache::get("login_lockout:{$loginId}")` — if locked, throw `ValidationException::withMessages(['login_id' => "Account locked. Try again in 10 min. Forgot password? Reset at TARUMT intranet."])`
- [ ] 5.2 On failed auth (null return, staff only, lecturer exists in DB): increment `Cache::get("login_fail:{$loginId}")`, if >= 3 → set `Cache::put("login_lockout:{$loginId}", ['minutes' => 10], 600)` and forget fail key
- [ ] 5.3 On successful auth (staff): `Cache::forget("login_fail:{$loginId}")` + `Cache::forget("login_lockout:{$loginId}")`

## Task 6: A3 Session countdown
- [ ] 6.1 Create `resources/views/partials/ui-session-countdown.blade.php` — container div with `data-lifetime` and `data-last-activity` attributes, countdown text, "Still here?" button, hidden logout form
- [ ] 6.2 Create `public/js/session-countdown.js` — read data attributes, compute remaining time, show banner at < 120s, countdown text update, "Still here?" → `location.reload()`, at 0 → auto-submit logout form
- [ ] 6.3 Include partial in `resources/views/layouts/ui-template.blade.php` inside `@auth` block, after the nav bar include, before `@yield('content')`

## Task 7: B1 Session indicator
- [ ] 7.1 Add `<span class="session-dot" title="Session active"></span>` in `ui-nav-bar.blade.php` near user panel, inside `@auth` block
- [ ] 7.2 Add CSS: `.session-dot { background: var(--color-secondary); border-radius: 50%; width: 8px; height: 8px; display: inline-block; }` in `theme.css`

## Task 8: C4 Auto-logout (staff only)
- [ ] 8.1 Create `public/js/auto-logout.js` — track mousemove/keydown/click, 25 min idle → warning modal, 30 min → auto-submit logout form. Multi-tab: BroadcastChannel named `'idle-sync'` with `localStorage` fallback (set `idle-activity-ts` key, listen for `storage` event).
- [ ] 8.2 Add `<script src="/js/auto-logout.js">` + hidden logout form in `ui-nav-bar.blade.php`, guarded by `@if(auth()->check() && auth()->user()->isLecturer())`

## Task 9: Lint + verify
- [ ] 9.1 Run `composer run lint:check`
- [ ] 9.2 Run `composer run types:check`
- [ ] 9.3 Confirm no new failures
