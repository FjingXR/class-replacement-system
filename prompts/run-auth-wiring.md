# /sdd-propose prompt — Auth Wiring

> Paste this into a fresh forked session to run /sdd-propose for logout, session timeout,
> login redirect, and user profile display. The agent auto-loads AGENTS.md → reads CodingMAIN.md.

---

```
/sdd-propose

Implement logout, session timeout, post-login redirect, and user profile display for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §5 Domain Model, §6 RBAC, §7 FR/NFR, §10 Coding Conventions).
- ../final/FR&NFR.md — find every FR/NFR that touches auth/session.
- app/Models/User.php — User model with student()/lecturer() relationships and loginId(), isStudent(), isLecturer() helpers.
- app/Models/Student.php — Student model (student_id, name, cohort relationship).
- app/Models/Lecturer.php — Lecturer model (staff_id, name, department relationship).
- app/Livewire/Actions/Logout.php — already implemented (calls Auth::logout, invalidates session, regenerates CSRF token, redirects to /).
- app/Providers/FortifyServiceProvider.php — custom authenticateUsing callback (queries Student/Lecturer by login_id, checks Hash::check); Fortify::loginView redirects to /login/student; configureRateLimiting set to 5/min.
- config/fortify.php — Fortify config; 'home' => '/dashboard' (needs role-based redirect — see below).
- config/session.php — session lifetime config; look for the block comment marked [SESSION TIMEOUT] (line ~36). Currently 'lifetime' => (int) env('SESSION_LIFETIME', 1) — set to 1 min for testing. NFR 2.4 says 30 min for production.
- .env — SESSION_LIFETIME=1 (set for testing).
- routes/web.php — current routes (login routes, UI mock routes, dashboard with auth middleware).
- resources/views/partials/ui-nav-bar.blade.php — nav bar with dummy logout button (onclick="alert('Logout')" — needs real form) AND no user profile display (needs name/role info from database).
- resources/views/dashboard.blade.php — already has POST logout form (reference pattern).
- resources/views/layouts/app/sidebar.blade.php — already has POST logout form (reference pattern).
- resources/views/components/desktop-user-menu.blade.php — already has POST logout form (reference pattern).
- resources/views/pages/auth/verify-email.blade.php — already has POST logout form (reference pattern).

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply:
   - FR 1.1 login (exists — login pages work, route /login/student and /login/staff, Fortify authenticateUsing queries DB). Login is fully wired to database: Student::where('student_id', $loginId) or Lecturer::where('staff_id', $loginId), then Hash::check($password, $user->password).
   - NFR 2.4 session timeout — config/session.php already set to 1 min (look for [SESSION TIMEOUT] comment). .env SESSION_LIFETIME=1. For production: change to 30. Comment already in code for easy finding.
   - Role-based session lifetime: students are view-only (low risk) → 30-day session for better UX. Staff can approve/reject (higher risk) → 30 min session for security. Implementation: override session lifetime per-role in FortifyServiceProvider or middleware (Fortify `home` is a single value, so session lifetime override may need similar approach).
   - Post-login redirect: role-based — student → /student-my-timetable-ui (page does not exist yet, will be created in a separate SDD; for now, redirect will 404 until that page is built), staff → /my-timetable-ui. Fortify 'home' config is a single value, so the redirect needs to be overridden per-role.
   - Nav bar user profile: show Auth::user() name, role, and student_id/staff_id from related model.
   - [A1] Remember me: "Remember me" checkbox on both login forms. Fortify has built-in `remember` feature — just needs checkbox + `'remember' => $request->boolean('remember')` in auth attempt. Frontend design is TBD — use a simple placeholder checkbox for now.
   - [A3] Session expiry countdown: Blade partial + JS timer that shows "Session expires in X min" banner near timeout, with extend/Logout buttons. Auto-logouts when timer hits 0. Frontend design is TBD — use a simple placeholder banner for now.
   - [B1] Active session indicator: nav bar shows green dot or "Session active" text when logged in. Frontend design is TBD — use a simple placeholder for now.
   - Flag any other FR/NFR that touches auth/session.
   - Staff login lockout: after 3 consecutive failed logins with same staff ID → lock for 10 min. After unlock, 3 fresh attempts → lock again. Show hint: "Forgot password? Reset at TARUMT intranet." Cache-based (no migration). Staff-only — skip for students.
2. Confirm the implementation approach:
   - POST /logout route: already registered by Fortify — no route file changes needed.
   - Logout action: app/Livewire/Actions/Logout.php already does everything. No changes needed.
   - What's missing: (a) nav bar logout button is dummy alert → replace with real POST form; (b) nav bar has no user profile display → add name/role from Auth::user(); (c) session timeout already at 1 min; (d) post-login redirect needs role-based logic; (e) A1 remember me checkbox on login forms; (f) A3 session countdown partial + JS; (g) B1 session indicator in nav bar.
   - Post-login redirect approach: Fortify 'home' is '/dashboard'. Options: (a) change Fortify home to a route that redirects by role, (b) override in FortifyServiceProvider using Fortify::redirectUsing(), (c) add middleware. Discuss which is cleanest.
   - User profile in nav bar: use Auth::user() to get name, role, and related student_id/staff_id. The User model has: $user->name, $user->role, $user->student?->student_id, $user->lecturer?->staff_id. Display these in the top-right area of the nav bar.
   - A1 remember me: Fortify already supports this. Just add checkbox to login forms + ensure auth attempt passes 'remember'. No backend changes needed beyond the checkbox.
   - A3 session countdown: needs a JS timer that periodically checks session time remaining (can use a lightweight AJAX call or estimate client-side based on SESSION_LIFETIME). Shows banner when < X min remaining. Design is placeholder.
   - B1 session indicator: just a visual element in nav bar. Can check auth()->check() to show/hide. Design is placeholder.
   - C4 auto-logout (optional): JS tracks mouse/keyboard activity. If idle for X min → warning modal → auto-logout at 0. Complements A3 session countdown. ~3-4 hours effort. Do you want to include it?
3. Flag any auth/security concerns:
   - Logout must be POST only (CSRF protection) — already handled by Fortify route.
   - Session invalidation: already handled in Logout action.
   - Does the nav bar button need role-based visibility? (only show when logged in — check auth()->check() in blade).
   - User profile display must handle both student and lecturer roles (different fields: student_id vs staff_id).
   - Student redirect to /student-my-timetable-ui will 404 until that page is built — is that acceptable for now?
   - A3 session countdown: if using client-side estimation, clock drift could cause early/late logout. Consider a server-side check endpoint for accuracy (or accept imprecision for this FYP).
4. Wait for my OK on (1), (2), and (3) before writing the SDD proposal/design/tasks.

Feature to implement
- Name: Logout + Session Timeout + Login Redirect + User Panel + Remember Me + Session Countdown + Session Indicator + Staff Lockout + (Optional) Auto-Logout
- FR/NFR refs: NFR 2.4 (session timeout — 1 min for testing, 30 min prod), implicit FR 1.1 (login exists → logout + redirect must work)
- What changes:
  - resources/views/partials/ui-nav-bar.blade.php — FIVE changes (user panel already exists at lines 29–45 with hardcoded data):
    (a) Replace hardcoded .user-avatar "KL" with dynamic initials from Auth::user()->name (first letter of first name + first letter of last name, e.g. "Kylian Mbappe" → "KM", "Poong Foo Jing" → "PJ"). No PFP column in users table — always use initials.
    (b) Replace hardcoded .user-name "Kylian Mbappe" with Auth::user()->name
    (c) Replace hardcoded .user-role "Lecturer" with Auth::user()->role (and show student_id/staff_id below it)
    (d) Replace hardcoded .logout-btn onclick="alert('Logout')" with real <form method="POST" action="{{ route('logout') }}"> + @csrf + <button type="submit">
    (e) [B1] Add active session indicator (green dot or "Session active" text) near user panel. Design is placeholder — will be refined later.
    (f) Wrap user panel + logout in @auth / @guest guards if not already done (show login link when guest, profile when auth)
  - resources/views/auth/login-student.blade.php — [A1] Add "Remember me" checkbox below password field. Design is placeholder — will be refined later.
  - resources/views/auth/login-staff.blade.php — [A1] Same "Remember me" checkbox.
  - NEW: resources/views/partials/ui-session-countdown.blade.php — [A3] New Blade partial for session expiry countdown banner/modal. Shows "Session expires in X min. Still here?" with extend/Logout buttons. Auto-hides when not near expiry. Design is placeholder — will be refined later.
  - NEW: public/js/session-countdown.js — [A3] JS timer that checks session lifetime periodically, shows countdown banner when near expiry, auto-submits logout when timer hits 0.
  - (Optional) C4: NEW public/js/auto-logout.js — JS that tracks mousemove/keydown/click activity. If idle for X min → show warning modal → auto-submit logout at 0. Can be combined with A3 session countdown or kept separate. Only if user wants it.
  - Staff login lockout (cache-based):
    - app/Providers/FortifyServiceProvider.php — in authenticateUsing, before returning $user: check Cache::get('login_lockout:{staff_id}'). If locked → return null + set error message "Account locked. Try again in X min. Forgot password? Reset at TARUMT intranet."
    - app/Providers/FortifyServiceProvider.php — on failed login (when authenticateUsing returns null for staff): increment Cache::get('login_fail:{staff_id}'). If count >= 3 → Cache::put('login_lockout:{staff_id}', true, 10 minutes) + Cache::forget('login_fail:{staff_id}').
    - app/Providers/FortifyServiceProvider.php — on successful login for staff: Cache::forget('login_fail:{staff_id}') + Cache::forget('login_lockout:{staff_id}').
    - resources/views/auth/login-staff.blade.php — display lockout error message (already handles $errors bag). The hint text "Forgot password? Reset at TARUMT intranet" will appear in the error message.
    - Students: NO lockout (view-only, low risk). Only apply to login_type === 'staff'.
  - config/fortify.php — change 'home' redirect logic to be role-based: student → /student-my-timetable-ui, staff → /my-timetable-ui. (Note: /student-my-timetable-ui does not exist yet — will 404 until that SDD is applied.)
  - config/session.php — already set to 1 min (look for [SESSION TIMEOUT] comment). This is the DEFAULT. Role-based override will be applied per-role (see "What to add" below).
  - .env — SESSION_LIFETIME=1 already set. No change needed — already done.
- What already exists (do NOT re-implement):
  - resources/views/partials/ui-nav-bar.blade.php — user panel HTML already exists at lines 29–45 (.user-panel > .user-profile > .user-avatar + .user-info > .user-name + .user-role, and .logout-btn). Data is HARDCODED. Just replace with dynamic data from Auth::user().
  - app/Livewire/Actions/Logout.php — full logout implementation already works
  - POST /logout route — provided by Fortify (no route changes needed)
  - dashboard.blade.php, sidebar.blade.php, desktop-user-menu.blade.php, verify-email.blade.php — all already have working logout forms (use as reference for nav bar pattern)
  - Login pages and routes — already work (/login/student, /login/staff), fully wired to database via FortifyServiceProvider authenticateUsing
  - User model — has student()/lecturer() relationships, loginId(), isStudent(), isLecturer() helpers. No PFP/avatar column in users table — always use dynamic initials from name.
  - config/session.php — SESSION_LIFETIME already set to 1 min (look for [SESSION TIMEOUT] block comment at line ~36)
  - .env — SESSION_LIFETIME=1 already set
  - Fortify — has built-in `remember` feature for A1 (just needs checkbox in login form + `'remember' => true` in auth attempt). Also has rate limiting in configureRateLimiting() (per-IP, 5/min) — this is SEPARATE from the new per-user lockout.
- What to add:
  - Wire user panel: replace 4 hardcoded values (avatar initials, name, role, logout button) with dynamic data from Auth::user()
  - Add student_id/staff_id display below role in user panel
  - Implement role-based post-login redirect (student → /student-my-timetable-ui, staff → /my-timetable-ui)
  - Implement role-based session lifetime:
    - Students (view-only, low risk): 30-day session (43200 min) — better UX, they just view timetables
    - Staff (can approve/reject, higher risk): 30-min session (or 1 min for testing) — tighter security
    - Implementation options: (a) override in login controller/session middleware per-role, (b) set session lifetime in LoginController after auth, (c) use a middleware that checks role and adjusts session lifetime. Discuss which is cleanest.
  - [A1] Add "Remember me" checkbox to both login forms + pass 'remember' => true to Fortify auth attempt
  - [A3] Create session countdown Blade partial + JS timer — shows banner when session is near expiry, auto-logouts on timeout
  - [B1] Add active session indicator to nav bar (green dot / "Session active" text)
  - (Optional) C4: Auto-logout on JS inactivity — track activity, warn at X min idle, auto-logout at 0
  - Staff login lockout: cache-based consecutive failure tracking (key: `login_fail:{staff_id}`), lockout after 3 failures (key: `login_lockout:{staff_id}`, TTL 10 min), clear on success, hint about TARUMT intranet password reset

Backend conventions: follow CodingMAIN.md §10 exactly. No new Actions needed (Logout action already exists). Config + Fortify changes only. Run composer run lint:check + composer run types:check after apply. Commit prefix: feat:.

What to reuse:
  - resources/views/partials/ui-nav-bar.blade.php — user panel HTML already exists (lines 29–45), just replace hardcoded data
  - app/Livewire/Actions/Logout.php — no changes needed, just call from user panel form
  - Fortify POST /logout route — already registered, no changes needed
  - Fortify 'remember' feature — built-in, just needs checkbox in login form
  - Existing logout form pattern from dashboard.blade.php / sidebar.blade.php as reference for user panel HTML
  - User model relationships (student/lecturer) for profile display
  - [SESSION TIMEOUT] comment in config/session.php as a landmark for finding session config

Changelog (generate BEFORE the proposal, keep updating as you build): create `page-changelogs/auth-wiring-changelog.md` now (even if only header + empty Files Changed). Follow the exact format of `page-changelogs/my-timetable-changelog.md`: `# Changelog — Logout + Session Timeout + Login Redirect + User Profile` → `## Files Changed` → one `### \`<file path>\`` per changed file → per-file table `| Timestamp | Location | Change | Detail |`. Log every touched file (nav bar, config/fortify.php). Note: config/session.php and .env are already changed (no need to log those). Use server-local ISO-ish timestamps.

Deliverables: .sdd/changes/auth-wiring/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/), updated resources/views/partials/ui-nav-bar.blade.php (user panel wired + logout form + session indicator), updated resources/views/auth/login-student.blade.php + login-staff.blade.php (remember me checkbox + lockout error display), NEW resources/views/partials/ui-session-countdown.blade.php (placeholder), NEW public/js/session-countdown.js (placeholder), (optional) NEW public/js/auto-logout.js, updated app/Providers/FortifyServiceProvider.php (role-based redirect + staff lockout logic), config/fortify.php change (role-based redirect), session lifetime override logic (role-based: student 30 days, staff 30 min), page-changelogs/auth-wiring-changelog.md (created now, filled as you build). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: feat:.

Constraints: no new migrations (no DB changes), no new models, no new Livewire components, no new composer dependencies, no new frontend UI mock pages. A1/A3/B1/C4 frontend designs are PLACEHOLDER — will be refined in a separate frontend SDD later. /student-my-timetable-ui will 404 until that SDD is applied (acceptable — that page is next in queue). Session lifetime for students = 30 days (43200 min), staff = 30 min (or 1 min for testing).
```

---

## What's already changed (before this SDD)

These files were already modified outside of any SDD — no need to include them as deliverables:
- `config/session.php` — `lifetime` changed to `1`, `[SESSION TIMEOUT]` block comment added at line ~36
- `.env` — `SESSION_LIFETIME=1`

## What the agent will need to figure out

1. **Role-based post-login redirect** — Fortify `'home'` is a single string. The agent needs to override it per-role. Best approach: use `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()` to return different URLs based on `Auth::user()->role`.
2. **Role-based session lifetime** — Students: 30 days (43200 min), Staff: 30 min (or 1 min testing). Options: (a) set `config('session.lifetime')` after login based on role, (b) use middleware that adjusts lifetime per-request, (c) store lifetime in a custom column on users table (requires migration — avoid). Discuss with user which approach is cleanest.
3. **User panel wiring** — the `.user-panel` HTML already exists (lines 29–45 of ui-nav-bar.blade.php). It has hardcoded: avatar "KL", name "Kylian Mbappe", role "Lecturer", and a dummy logout button. Replace all 4 with dynamic data from `Auth::user()`. Add student_id/staff_id below role.
4. **@auth/@guest guards** — check if nav bar already has these; wrap user panel + logout + session indicator accordingly.
5. **Student vs Lecturer profile fields** — use `@if(auth()->user()->isStudent())` to show `student_id` vs `staff_id`.
6. **A1 Remember me** — add checkbox to both login forms. Ensure the form POST includes `remember` field. Fortify handles the rest.
7. **A3 Session countdown** — decide: client-side timer (estimate based on SESSION_LIFETIME) or server-side AJAX endpoint (accurate but more work). For FYP, client-side estimation is fine. Create the Blade partial + JS file.
8. **B1 Session indicator** — simple: check `auth()->check()` and show a green dot or text. Place near user panel in nav bar.
9. **C4 Auto-logout (optional)** — if user wants it: track `mousemove`/`keydown`/`click` on `document`, reset idle timer on activity. At X min idle → show warning modal. At 0 → submit logout form. Can share the warning modal with A3 or keep separate. ~3-4 hours effort.
10. **Staff login lockout** — cache keys: `login_fail:{staff_id}` (counter), `login_lockout:{staff_id}` (boolean, TTL 10 min). Check lockout in authenticateUsing before querying DB. Increment fail count on failed auth. Clear both on successful auth. Only for `login_type === 'staff'` — students are exempt. Hint text: "Forgot password? Reset at TARUMT intranet."
