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
- config/fortify.php — Fortify config; 'home' => '/dashboard' (needs to change to '/my-timetable-ui'); POST /logout route provided by Fortify.
- config/session.php — session lifetime config (currently 'lifetime' => (int) env('SESSION_LIFETIME', 120)).
- .env — SESSION_LIFETIME=120 (needs to change to 1 for testing).
- routes/web.php — current routes (login routes, UI mock routes, dashboard with auth middleware).
- resources/views/partials/ui-nav-bar.blade.php — nav bar with dummy logout button (onclick="alert('Logout')" — needs real form) AND no user profile display (needs name/role info from database).
- resources/views/dashboard.blade.php — already has POST logout form (reference pattern).
- resources/views/layouts/app/sidebar.blade.php — already has POST logout form (reference pattern).
- resources/views/components/desktop-user-menu.blade.php — already has POST logout form (reference pattern).
- resources/views/pages/auth/verify-email.blade.php — already has POST logout form (reference pattern).

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply:
   - FR 1.1 login (exists — login pages work, route /login/student and /login/staff, Fortify authenticateUsing queries DB). Login is fully wired to database: Student::where('student_id', $loginId) or Lecturer::where('staff_id', $loginId), then Hash::check($password, $user->password).
   - NFR 2.4 session timeout — currently SESSION_LIFETIME=120 in .env. User wants 1 minute for testing (will extend later). Change config/session.php lifetime to 1, .env SESSION_LIFETIME=1.
   - Implicit: after login, user must be redirected to the correct page. Currently Fortify home = '/dashboard'. User wants: after login → redirect to /my-timetable-ui. Change config/fortify.php 'home' to '/my-timetable-ui'.
   - Implicit: nav bar must show user profile info from database (name, role/student_id/staff_id). User model has student()/lecturer() relationships — use these to fetch and display info.
   - Flag any other FR/NFR that touches auth/session.
2. Confirm the implementation approach:
   - POST /logout route: already registered by Fortify — no route file changes needed.
   - Logout action: app/Livewire/Actions/Logout.php already does everything. No changes needed.
   - What's missing: (a) nav bar logout button is dummy alert → replace with real POST form; (b) nav bar has no user profile display → add name/role from Auth::user(); (c) session timeout not at 1 min; (d) post-login redirect goes to /dashboard instead of /my-timetable-ui.
   - User profile in nav bar: use Auth::user() to get name, role, and related student_id/staff_id. The User model has: $user->name, $user->role, $user->student?->student_id, $user->lecturer?->staff_id. Display these in the top-right area of the nav bar.
   - Any middleware implications? Does the auth middleware need session timeout enforcement?
3. Flag any auth/security concerns:
   - Logout must be POST only (CSRF protection) — already handled by Fortify route.
   - Session invalidation: already handled in Logout action.
   - Does the nav bar button need role-based visibility? (only show when logged in — check auth()->check() in blade).
   - User profile display must handle both student and lecturer roles (different fields: student_id vs staff_id).
4. Wait for my OK on (1), (2), and (3) before writing the SDD proposal/design/tasks.

Feature to implement
- Name: Logout + Session Timeout + Login Redirect + User Profile
- FR/NFR refs: NFR 2.4 (session timeout — set to 1 min for testing, will extend later), implicit FR 1.1 (login exists → logout + redirect must work)
- What changes:
  - resources/views/partials/ui-nav-bar.blade.php — THREE changes:
    (a) Replace dummy alert logout button with real POST form to route('logout'), matching pattern in dashboard.blade.php
    (b) Add user profile display in top-right area: show Auth::user()->name, Auth::user()->role, and Auth::user()->student?->student_id OR Auth::user()->lecturer?->staff_id (depending on role)
    (c) Wrap logout + profile in @auth / @guest guards if not already done
  - config/session.php — change 'lifetime' from 120 to 1 (NFR 2.4 testing; will extend later)
  - .env — change SESSION_LIFETIME=120 to SESSION_LIFETIME=1
  - config/fortify.php — change 'home' from '/dashboard' to '/my-timetable-ui' (post-login redirect)
- What already exists (do NOT re-implement):
  - app/Livewire/Actions/Logout.php — full logout implementation already works
  - POST /logout route — provided by Fortify (no route changes needed)
  - dashboard.blade.php, sidebar.blade.php, desktop-user-menu.blade.php, verify-email.blade.php — all already have working logout forms (use as reference for nav bar pattern)
  - Login pages and routes — already work (/login/student, /login/staff), fully wired to database via FortifyServiceProvider authenticateUsing
  - User model — has student()/lecturer() relationships, loginId(), isStudent(), isLecturer() helpers
- What to add:
  - Wire nav bar logout button: replace <button class="logout-btn" onclick="alert('Logout')"> with <form method="POST" action="{{ route('logout') }}"> + @csrf + <button type="submit">Logout</button>
  - Add user profile to nav bar: top-right area showing name, role, student_id/staff_id from Auth::user() and its relationships
  - Set session lifetime = 1 min (config + .env) for testing
  - Change Fortify home redirect from /dashboard to /my-timetable-ui

Backend conventions: follow CodingMAIN.md §10 exactly. No new Actions needed (Logout action already exists). Config-only changes for session + Fortify home. Run composer run lint:check + composer run types:check after apply. Commit prefix: feat:.

What to reuse:
  - app/Livewire/Actions/Logout.php — no changes needed, just call from nav bar form
  - Fortify POST /logout route — already registered, no changes needed
  - Existing logout form pattern from dashboard.blade.php / sidebar.blade.php as reference for nav bar HTML
  - User model relationships (student/lecturer) for profile display

Changelog (generate BEFORE the proposal, keep updating as you build): create `page-changelogs/logout-session-timeout-changelog.md` now (even if only header + empty Files Changed). Follow the exact format of `page-changelogs/my-timetable-changelog.md`: `# Changelog — Logout + Session Timeout + Login Redirect + User Profile` → `## Files Changed` → one `### \`<file path>\`` per changed file → per-file table `| Timestamp | Location | Change | Detail |`. Log every touched file (nav bar, config/session.php, .env, config/fortify.php). Use server-local ISO-ish timestamps.

Deliverables: .sdd/changes/logout-session-timeout/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/), updated resources/views/partials/ui-nav-bar.blade.php (logout form + user profile), config/session.php change, .env change, config/fortify.php change, page-changelogs/logout-session-timeout-changelog.md (created now, filled as you build). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: feat:.

Constraints: no new migrations (no DB changes), no new models, no new Livewire components, no new composer dependencies, no new frontend UI mock pages. Session timeout set to 1 min for testing — will extend later.
```

---

## What the agent will need to figure out

1. **Nav bar user profile layout** — the current nav bar has a logout button at line ~38. The agent needs to read the full nav bar HTML to understand the layout and decide where to place the user profile (name + role + ID) in the top-right. Likely near the logout button, wrapped in a user-info container.

2. **@auth/@guest guards** — the nav bar is shared across all pages (logged in or not). The agent needs to check if it already has `@auth` / `@guest` directives, and wrap the profile + logout accordingly (show profile/logout only when logged in; show login link when not).

3. **Student vs Lecturer profile display** — the user profile needs to show different fields depending on role: `student?->student_id` for students, `lecturer?->staff_id` for staff. The agent should use `@if(auth()->user()->isStudent())` or similar conditional.

4. **Fortify home redirect** — changing `config/fortify.php` `'home'` from `/dashboard` to `/my-timetable-ui` affects ALL users (students + staff). Confirm this is intended (both roles go to my-timetable-ui after login).

## Answers to your questions

**Q1: Is login linked with database?**
Yes. `FortifyServiceProvider` authenticateUsing queries `Student::where('student_id', $loginId)` or `Lecturer::where('staff_id', $loginId)`, then `Hash::check($password, $user->password)`. Fully wired to users + students + lecturers tables.

**Q2: Do you already have logout functions?**
Yes — 90% done. `POST /logout` route (Fortify), `Logout` action (app/Livewire/Actions/Logout.php), and 4 views already have working forms. Only the nav bar button is dummy (`onclick="alert('Logout')"`).
