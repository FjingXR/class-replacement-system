# /sdd-propose prompt — Logout + Session Timeout + Login Redirect + User Profile

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
   - Post-login redirect: role-based — student → /student-my-timetable-ui (page does not exist yet, will be created in a separate SDD; for now, redirect will 404 until that page is built), staff → /my-timetable-ui. Fortify 'home' config is a single value, so the redirect needs to be overridden per-role. Suggested approach: override in FortifyServiceProvider boot() or use middleware.
   - Nav bar user profile: show Auth::user() name, role, and student_id/staff_id from related model.
   - Flag any other FR/NFR that touches auth/session.
2. Confirm the implementation approach:
   - POST /logout route: already registered by Fortify — no route file changes needed.
   - Logout action: app/Livewire/Actions/Logout.php already does everything. No changes needed.
   - What's missing: (a) nav bar logout button is dummy alert → replace with real POST form; (b) nav bar has no user profile display → add name/role from Auth::user(); (c) session timeout already at 1 min (config + .env already changed); (d) post-login redirect needs role-based logic — student → /student-my-timetable-ui (not yet built), staff → /my-timetable-ui.
   - Post-login redirect approach: Fortify 'home' is '/dashboard'. Options: (a) change Fortify home to a route that redirects by role, (b) override in FortifyServiceProvider using Fortify::redirectUsing(), (c) add middleware. Discuss which is cleanest.
   - User profile in nav bar: use Auth::user() to get name, role, and related student_id/staff_id. The User model has: $user->name, $user->role, $user->student?->student_id, $user->lecturer?->staff_id. Display these in the top-right area of the nav bar.
3. Flag any auth/security concerns:
   - Logout must be POST only (CSRF protection) — already handled by Fortify route.
   - Session invalidation: already handled in Logout action.
   - Does the nav bar button need role-based visibility? (only show when logged in — check auth()->check() in blade).
   - User profile display must handle both student and lecturer roles (different fields: student_id vs staff_id).
   - Student redirect to /student-my-timetable-ui will 404 until that page is built — is that acceptable for now?
4. Wait for my OK on (1), (2), and (3) before writing the SDD proposal/design/tasks.

Feature to implement
- Name: Logout + Session Timeout + Login Redirect + User Profile
- FR/NFR refs: NFR 2.4 (session timeout — 1 min for testing, 30 min prod), implicit FR 1.1 (login exists → logout + redirect must work)
- What changes:
  - resources/views/partials/ui-nav-bar.blade.php — THREE changes:
    (a) Replace dummy alert logout button with real POST form to route('logout'), matching pattern in dashboard.blade.php
    (b) Add user profile display in top-right area: show Auth::user()->name, Auth::user()->role, and Auth::user()->student?->student_id OR Auth::user()->lecturer?->staff_id (depending on role)
    (c) Wrap logout + profile in @auth / @guest guards if not already done
  - config/fortify.php — change 'home' redirect logic to be role-based: student → /student-my-timetable-ui, staff → /my-timetable-ui. (Note: /student-my-timetable-ui does not exist yet — will 404 until that SDD is applied.)
  - config/session.php — already set to 1 min (look for [SESSION TIMEOUT] comment). No change needed — already done.
  - .env — SESSION_LIFETIME=1 already set. No change needed — already done.
- What already exists (do NOT re-implement):
  - app/Livewire/Actions/Logout.php — full logout implementation already works
  - POST /logout route — provided by Fortify (no route changes needed)
  - dashboard.blade.php, sidebar.blade.php, desktop-user-menu.blade.php, verify-email.blade.php — all already have working logout forms (use as reference for nav bar pattern)
  - Login pages and routes — already work (/login/student, /login/staff), fully wired to database via FortifyServiceProvider authenticateUsing
  - User model — has student()/lecturer() relationships, loginId(), isStudent(), isLecturer() helpers
  - config/session.php — SESSION_LIFETIME already set to 1 min (look for [SESSION TIMEOUT] block comment at line ~36)
  - .env — SESSION_LIFETIME=1 already set
- What to add:
  - Wire nav bar logout button: replace <button class="logout-btn" onclick="alert('Logout')"> with <form method="POST" action="{{ route('logout') }}"> + @csrf + <button type="submit">Logout</button>
  - Add user profile to nav bar: top-right area showing name, role, student_id/staff_id from Auth::user() and its relationships
  - Implement role-based post-login redirect (student → /student-my-timetable-ui, staff → /my-timetable-ui)

Backend conventions: follow CodingMAIN.md §10 exactly. No new Actions needed (Logout action already exists). Config + Fortify changes only. Run composer run lint:check + composer run types:check after apply. Commit prefix: feat:.

What to reuse:
  - app/Livewire/Actions/Logout.php — no changes needed, just call from nav bar form
  - Fortify POST /logout route — already registered, no changes needed
  - Existing logout form pattern from dashboard.blade.php / sidebar.blade.php as reference for nav bar HTML
  - User model relationships (student/lecturer) for profile display
  - [SESSION TIMEOUT] comment in config/session.php as a landmark for finding session config

Changelog (generate BEFORE the proposal, keep updating as you build): create `page-changelogs/logout-session-timeout-changelog.md` now (even if only header + empty Files Changed). Follow the exact format of `page-changelogs/my-timetable-changelog.md`: `# Changelog — Logout + Session Timeout + Login Redirect + User Profile` → `## Files Changed` → one `### \`<file path>\`` per changed file → per-file table `| Timestamp | Location | Change | Detail |`. Log every touched file (nav bar, config/fortify.php). Note: config/session.php and .env are already changed (no need to log those). Use server-local ISO-ish timestamps.

Deliverables: .sdd/changes/logout-session-timeout/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/), updated resources/views/partials/ui-nav-bar.blade.php (logout form + user profile), config/fortify.php change (role-based redirect), page-changelogs/logout-session-timeout-changelog.md (created now, filled as you build). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: feat:.

Constraints: no new migrations (no DB changes), no new models, no new Livewire components, no new composer dependencies, no new frontend UI mock pages. /student-my-timetable-ui will 404 until that SDD is applied (acceptable — that page is next in queue).
```

---

## What's already changed (before this SDD)

These files were already modified outside of any SDD — no need to include them as deliverables:
- `config/session.php` — `lifetime` changed to `1`, `[SESSION TIMEOUT]` block comment added at line ~36
- `.env` — `SESSION_LIFETIME=1`

## What the agent will need to figure out

1. **Role-based post-login redirect** — Fortify `'home'` is a single string. The agent needs to override it per-role. Best approach: use `Fortify::redirectUsing()` in `FortifyServiceProvider::boot()` to return different URLs based on `Auth::user()->role`.
2. **Nav bar user profile layout** — read the full nav bar HTML, find the top-right area, add user info container with name + role + ID.
3. **@auth/@guest guards** — check if nav bar already has these; wrap profile + logout accordingly.
4. **Student vs Lecturer profile fields** — use `@if(auth()->user()->isStudent())` to show `student_id` vs `staff_id`.
