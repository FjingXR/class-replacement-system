# Reusable /sdd-propose template for a NEW backend feature

> Copy this template each time you start a new backend feature (auth, migration, model,
> controller, API endpoint, queue job, etc.). Replace only the `[ ]` brackets.
> The agent auto-loads `AGENTS.md` (which already points to `CodingMAIN.md`), so you do
> NOT need to repeat the full file tree — just tell the agent which FRs/NFRs to target.

---

## Anatomy of the prompt (7 parts — always include all seven)

| # | Part | What it does |
|---|------|--------------|
| 1 | **Command + goal** | `/sdd-propose`, then one line: "create a new backend feature" (or "implement FR X.Y"). |
| 2 | **Read-first list** | CodingMAIN.md (esp. §5 Domain Model, §6 RBAC, §7 FR/NFR), the FR source (`../final/FR&NFR.md` or `../final/Ch1.pdf`), relevant existing code (models, migrations, controllers, actions, config files). |
| 3 | **Discuss before generating** | After reading FR&NFR.md, talk to the user: confirm which FRs/NFRs apply, flag anything that touches auth/middleware/queues/DB, check if the logic is sound, and agree on the implementation approach. Only then write the proposal. |
| 4 | **Feature details** | 5 fields: feature name, FR/NFR refs, what changes (files), what already exists (don't re-implement), what to add. |
| 5 | **Backend conventions** | Point to §10 Coding Conventions in CodingMAIN.md (do not rewrite them — say "follow §10 exactly"). Specifically: follow existing patterns (Actions, FormRequests, Policies); never hardcode hex; use `composer run lint:check` + `composer run types:check`. |
| 6 | **What already exists / what to reuse** | Which models, migrations, controllers, actions, config already exist; which ones to extend vs. create new. |
| 7 | **Deliverables + constraints** | SDD files, code files, migration, config changes, lint check, commit prefix. |

The tricky parts are: (1) listing **what already exists** so you don't re-implement, (2) flagging **auth/middleware implications** (does this touch routes that need `auth` middleware?), and (3) checking FR/NFR logic.
Ask yourself: *does this feature touch the DB? If yes, migration goes first. Does it touch auth? If yes, middleware + session implications.*

---

## Template (fill the `[ ]` brackets)

```
/sdd-propose

Implement a new backend feature for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §5 Domain Model, §6 RBAC, §7 FR/NFR, §10 Coding Conventions).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this feature.
- [EXISTING CODE FILES] — list every file you will touch or extend (model, migration, controller, action, config, route file, view).
- [RELEVANT CONFIG FILES] — e.g. config/session.php, config/fortify.php, .env, etc.

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply to this feature and tell me if each one is implementation-ready (flag anything that depends on another unfinished feature).
2. Confirm the implementation approach with me: which files to create vs. extend, which existing patterns/actions to reuse, middleware implications, session/queue implications.
3. Flag any auth/security concerns (CSRF, session invalidation, route protection, password hashing).
4. Wait for my OK on (1), (2), and (3) before writing the SDD proposal/design/tasks.

Feature to implement
- Name: [FEATURE NAME]
- FR/NFR refs: [e.g. FR 1.1, NFR 2.4]
- What changes: [list files to create or modify — model, migration, controller, action, config, route, view]
- What already exists: [list existing code this feature extends or reuses — e.g. "Logout action exists in app/Livewire/Actions/Logout.php", "POST /logout route provided by Fortify"]
- What to add: [list the new code — e.g. "wire nav bar button to POST /logout", "set SESSION_LIFETIME=30"]

Backend conventions: follow CodingMAIN.md §10 exactly (Action pattern for single-purpose invokable classes; FormRequest for validation; Policy for authorization; follow existing naming; run composer run lint:check + composer run types:check after apply; commit prefix feat: / fix: / refactor:).

What to reuse: [list existing code — models, actions, migrations, config values, middleware — that this feature builds on].

Changelog (generate BEFORE the proposal, keep updating as you build): create `page-changelogs/[change-name]-changelog.md` now (even if only header + empty Files Changed). Follow the exact format of an existing file in `page-changelogs/` (read one to learn house style): `# Changelog — <Feature Name>` → `## Files Changed` → one `### \`<file path>\`` per changed file → per-file table `| Timestamp | Location | Change | Detail |`. Log every touched file. Use server-local ISO-ish timestamps.

Deliverables: .sdd/changes/[change-name]/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/), all code files listed above, migration (if DB changes), config changes (if any). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: feat: (new feature) or fix: (bug fix) or refactor: (restructure).

Constraints: [list anything out of scope — e.g. "no new frontend pages", "no new Livewire components unless needed", "no new composer dependencies"].
```

---

## Worked example — Logout + Session Timeout (reference, do not reuse as-is)

```
/sdd-propose

Implement logout and session timeout for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §6 RBAC, §7 FR/NFR, §10 Coding Conventions).
- ../final/FR&NFR.md — find every FR/NFR that touches auth/session.
- app/Livewire/Actions/Logout.php — already exists and works.
- routes/web.php — current routes.
- config/fortify.php — Fortify config (features currently empty).
- config/session.php — session lifetime config (currently 120 min).
- .env — SESSION_LIFETIME=120.
- resources/views/partials/ui-nav-bar.blade.php — nav bar with dummy logout button (onclick="alert('Logout')").
- resources/views/dashboard.blade.php — already has POST logout form.
- resources/views/layouts/app/sidebar.blade.php — already has POST logout form.
- resources/views/components/desktop-user-menu.blade.php — already has POST logout form.

Discuss with me BEFORE you generate the proposal (do not skip):
1. FRs/NFRs that apply: FR 1.1 login (done), NFR 2.4 session timeout 30 min (not enforced), FR 4.10 PL role (irrelevant here). Flag that SESSION_LIFETIME=120 in .env vs NFR 2.4 says 30 min — changing now will kill dev sessions. Propose: defer to Sprint 3 hardening, or use dev override.
2. Implementation approach: POST /logout route already provided by Fortify. app/Livewire/Actions/Logout.php already implements the action. dashboard.blade.php + sidebar.blade.php + desktop-user-menu.blade.php already have <form method="POST" action="{{ route('logout') }}">. What's missing: nav bar logout button is a dummy alert. Also: session lifetime config.
3. Auth/security concerns: logout must invalidate session + regenerate CSRF token (already done in Logout action). Route POST /logout must be POST only (CSRF). Session timeout is config-only.

Feature to implement
- Name: Logout + Session Timeout
- FR/NFR refs: NFR 2.4 (session timeout 30 min), implicit FR 1.1 (login exists → logout must work)
- What changes:
  - resources/views/partials/ui-nav-bar.blade.php — wire logout button to POST /logout
  - config/session.php — set lifetime = 30 (NFR 2.4)
  - .env — set SESSION_LIFETIME=30
- What already exists:
  - app/Livewire/Actions/Logout.php — already implemented (Auth::logout, session invalidate, regenerate token, redirect /)
  - POST /logout route — provided by Fortify (AuthenticatedSessionController@destroy)
  - dashboard.blade.php, sidebar.blade.php, desktop-user-menu.blade.php — already have working logout forms
- What to add:
  - Replace dummy alert in nav bar with a real POST form + CSRF token
  - Enforce session lifetime = 30 min (config + .env)

Backend conventions: follow CodingMAIN.md §10 exactly (Action pattern already used for Logout; no new actions needed here; config-only changes). Run composer run lint:check + composer run types:check after apply. Commit prefix: feat:.

What to reuse:
  - app/Livewire/Actions/Logout.php — call directly from the nav bar form (no changes needed)
  - Fortify POST /logout route — already registered, no route file changes needed
  - Existing logout forms in dashboard/sidebar/desktop-user-menu as reference for the nav bar form pattern

Changelog (generate BEFORE the proposal, keep updating as you build): create `page-changelogs/logout-session-timeout-changelog.md` now (even if only header + empty Files Changed). Follow the exact format of `page-changelogs/my-timetable-changelog.md`: `# Changelog — Logout + Session Timeout` → `## Files Changed` → one `### \`<file path>\`` per changed file → per-file table `| Timestamp | Location | Change | Detail |`. Log every touched file. Use server-local ISO-ish timestamps.

Deliverables: .sdd/changes/logout-session-timeout/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/), updated nav bar view, config/session.php change, .env change, page-changelogs/logout-session-timeout-changelog.md (created now, filled as you build). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: feat:.

Constraints: no new migrations (no DB changes), no new models, no new Livewire components, no new composer dependencies, no frontend UI mock pages. Session timeout deferred to Sprint 3 hardening if user prefers (dev sessions annoying at 30 min).
```
