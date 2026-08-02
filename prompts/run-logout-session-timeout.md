# /sdd-propose prompt — Logout + Session Timeout

> Paste this into a fresh forked session to run /sdd-propose for logout + session timeout.
> The agent auto-loads AGENTS.md → reads CodingMAIN.md. No need to repeat full file tree.

---

```
/sdd-propose

Implement logout button wiring and session timeout for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §6 RBAC, §7 FR/NFR, §10 Coding Conventions).
- ../final/FR&NFR.md — find every FR/NFR that touches auth/session.
- app/Livewire/Actions/Logout.php — already implemented (calls Auth::logout, invalidates session, regenerates CSRF token, redirects to /).
- config/fortify.php — Fortify config; POST /logout route is already provided by Fortify (AuthenticatedSessionController@destroy).
- config/session.php — session lifetime config (currently 'lifetime' => (int) env('SESSION_LIFETIME', 120)).
- .env — SESSION_LIFETIME=120.
- resources/views/partials/ui-nav-bar.blade.php — nav bar with dummy logout button (line ~38: onclick="alert('Logout')" — needs real form).
- resources/views/dashboard.blade.php — already has POST logout form (reference pattern).
- resources/views/layouts/app/sidebar.blade.php — already has POST logout form (reference pattern).
- resources/views/components/desktop-user-menu.blade.php — already has POST logout form (reference pattern).
- resources/views/pages/auth/verify-email.blade.php — already has POST logout form (reference pattern).

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply:
   - FR 1.1 login (exists — login pages at resources/views/auth/login-student.blade.php and login-staff.blade.php work, route /login/student and /login/staff defined in routes/web.php).
   - NFR 2.4 session timeout 30 min — not enforced (SESSION_LIFETIME=120 in .env, config/session.php defaults to 120). Flag: changing to 30 now will kill dev sessions every 30 min. Propose either (a) defer to Sprint 3 hardening, or (b) change now with a note that dev can temporarily override via .env.
   - Flag any other FR/NFR that touches auth/session.
2. Confirm the implementation approach:
   - POST /logout route: already registered by Fortify — no route file changes needed.
   - Logout action: app/Livewire/Actions/Logout.php already does everything (Auth::logout, session invalidate, regenerate token, redirect /). No changes needed.
   - What's missing: nav bar logout button is a dummy alert. Fix: replace the onclick="alert('Logout')" with a <form method="POST" action="{{ route('logout') }}"> + @csrf + @method('DELETE') submit button, matching the pattern in dashboard.blade.php / sidebar.blade.php / desktop-user-menu.blade.php.
   - Session timeout: change config/session.php 'lifetime' to 30, and .env SESSION_LIFETIME=30.
   - Any middleware implications? Does the auth middleware need session timeout enforcement?
3. Flag any auth/security concerns:
   - Logout must be POST only (CSRF protection) — already handled by Fortify route.
   - Session invalidation: already handled in Logout action (Session::invalidate + regeneraToken).
   - Does the nav bar button need role-based visibility? (e.g. only show when logged in — check auth()->check() in blade).
4. Wait for my OK on (1), (2), and (3) before writing the SDD proposal/design/tasks.

Feature to implement
- Name: Logout + Session Timeout
- FR/NFR refs: NFR 2.4 (session timeout 30 min), implicit FR 1.1 (login exists → logout must work)
- What changes:
  - resources/views/partials/ui-nav-bar.blade.php — replace dummy alert logout button with real POST form to route('logout'), matching the existing pattern in dashboard.blade.php
  - config/session.php — change 'lifetime' from 120 to 30 (NFR 2.4)
  - .env — change SESSION_LIFETIME=120 to SESSION_LIFETIME=30
- What already exists (do NOT re-implement):
  - app/Livewire/Actions/Logout.php — full logout implementation already works
  - POST /logout route — provided by Fortify (no route changes needed)
  - dashboard.blade.php, sidebar.blade.php, desktop-user-menu.blade.php, verify-email.blade.php — all already have working logout forms (use as reference for nav bar pattern)
  - Login pages and routes — already work (/login/student, /login/staff)
- What to add:
  - Wire nav bar logout button: replace <button class="logout-btn" onclick="alert('Logout')"> with <form method="POST" action="{{ route('logout') }}"> + @csrf + <button type="submit">Logout</button>
  - Enforce session lifetime = 30 min (config + .env)
  - Optionally: wrap nav bar logout in @auth / @guest checks if not already done

Backend conventions: follow CodingMAIN.md §10 exactly. No new Actions needed (Logout action already exists). Config-only changes for session. Run composer run lint:check + composer run types:check after apply. Commit prefix: feat:.

What to reuse:
  - app/Livewire/Actions/Logout.php — no changes needed, just call from nav bar form
  - Fortify POST /logout route — already registered, no changes needed
  - Existing logout form pattern from dashboard.blade.php / sidebar.blade.php as reference for nav bar HTML

Changelog (generate BEFORE the proposal, keep updating as you build): create `page-changelogs/logout-session-timeout-changelog.md` now (even if only header + empty Files Changed). Follow the exact format of `page-changelogs/my-timetable-changelog.md`: `# Changelog — Logout + Session Timeout` → `## Files Changed` → one `### \`<file path>\`` per changed file → per-file table `| Timestamp | Location | Change | Detail |`. Log every touched file (nav bar, config/session.php, .env). Use server-local ISO-ish timestamps.

Deliverables: .sdd/changes/logout-session-timeout/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/), updated resources/views/partials/ui-nav-bar.blade.php, config/session.php change, .env change, page-changelogs/logout-session-timeout-changelog.md (created now, filled as you build). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: feat:.

Constraints: no new migrations (no DB changes), no new models, no new Livewire components, no new composer dependencies, no new frontend UI mock pages. Session timeout may be deferred to Sprint 3 hardening if user prefers (dev sessions annoying at 30 min).
```

---

## How the agent will use this

1. Agent reads CodingMAIN.md + FR&NFR.md.
2. Agent reads all the "Read first" files listed above.
3. Agent discusses FRs/NFRs, implementation approach, and security concerns with you.
4. You confirm or adjust.
5. Agent generates `.sdd/changes/logout-session-timeout/` (sdd.yaml, proposal.md, design.md, tasks.md).
6. Agent creates `page-changelogs/logout-session-timeout-changelog.md` with header + empty Files Changed.
7. Agent applies the changes (edits nav bar, config, .env).
8. Agent runs `composer run lint:check` + `composer run types:check`.
9. Agent commits with `feat:` prefix.

## What the agent will likely find

- **Logout is 90% done.** The `Logout` action exists, the Fortify route exists, 4 views already have the real form. Only the nav bar button is dummy.
- **Session timeout is config-only.** Just change `config/session.php` lifetime + `.env` value.
- **The tricky part is the nav bar.** It's shared across ALL pages — check if it already has `@auth`/`@guest` guards, and whether the button should be a link (for non-logged-in state) or a form submit (for logged-in state).
