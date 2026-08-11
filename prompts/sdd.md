# SDD Tracker

> Living index of all SDD changes — what's been applied, what's in progress, what's queued.
> Update this file every time an SDD is created, applied, or deferred.

---

## Applied (already built)

| SDD | Date | What it did |
|-----|------|-------------|
| `oop-blade-refactor` | 2026-07-29 | OOP Blade refactor — layouts, partials, shared theme |
| `refactor-blade-oop` | 2026-07-29 | Continued Blade OOP refactoring |
| `cohort-timetable-ui` | 2026-07-29 | Cohort timetable UI mock page |
| `my-request-history` | 2026-07-20 | My request history UI mock page |
| `replacement-home-dashboard` | — | Replacement home dashboard UI mock page |
| `centralized-mock-data` | 2026-08-02 | Centralized mock data to mock-data.js — all 5 pages refactored, layout wired, 49/49 tasks checked, committed `61b016a` |
| `mock-data-centralization` | 2026-08-01 | Mock data centralization (continued) |
| `request-approval` | 2026-08-01 | Request approval UI mock page — PL side; frozen after 17 review rounds + 6 advanced UX features; 17 PL-efficiency features total; ready for `/sdd-apply` |
| `student-my-timetable-ui` | 2026-08-02 | Student my timetable UI mock page — frontend-only; SDD artifacts frozen (proposal 2 rounds, design 1 round, tasks 1 round); ready for `/sdd-apply` |
| `logout-modal` | 2026-08-03 | Logout confirmation modal — 5s countdown, skip/undo, "Don't ask me again" (localStorage). 27 subtasks, 6 review rounds. Committed with nav bar wiring, modal markup, styles, JS, layout inclusion. |

## In Progress (proposed, not yet applied)

| SDD | Date | What it does |
|-----|------|-------------|
| *(none)* | — | — |

## Queued (not yet proposed)

| # | SDD name | What it does | Depends on | Prompt file |
|---|----------|-------------|------------|-------------|
| 1 | `auth-wiring` | Logout + **role-based session lifetime (student 30 days, staff 30 min)** + role-based redirect + user panel wiring + **A1 remember me + A3 session countdown + B1 session indicator + staff login lockout (3 fails → 10 min lock) + C4 auto-logout (staff only)** + **remember me help text + lockout countdown timer + session expiry modal at 60s + session indicator tooltip** | student-my-timetable-ui (for student redirect target) | `prompts/run-auth-wiring.md` |

## Deferred / Future

| # | Feature | Why deferred | Target sprint |
|---|---------|-------------|---------------|
| 1 | Session timeout 30 min (production) | Set to 1 min for testing; will extend when ready for prod | Sprint 3 hardening |

---

## Advanced auth features — final decisions

> Excludes: forgot password, create new account, password change (A2), 2FA (C1), session management (C2), password strength (C3), login audit trail (B3) — all skipped per user decision.

### Accepted (included in auth-wiring SDD)

| # | Feature | What it does | Frontend status |
|---|---------|-------------|----------------|
| — | **Role-based session lifetime** | Students (view-only, low risk): 30-day session. Staff (approve/reject, higher risk): 30-min session. | Backend config — no frontend needed |
| — | **Staff login lockout** | 3 consecutive failed logins → lock for 10 min. Unlock → 3 fresh attempts → lock again. Hint: "Forgot password? Reset at TARUMT intranet." Cache-based, no migration. Staff-only. | Error message on login form (already has $errors display) |
| — | **User panel wiring** | Replace 4 hardcoded values in .user-panel (avatar "KL", name "Kylian Mbappe", role "Lecturer", dummy logout) with dynamic data from Auth::user(). Add student_id/staff_id. | Existing HTML — just replace data |
| A1 | **Remember me** | "Remember me" checkbox on login → longer session (e.g. 7 days) vs. default 1 min. Uses Fortify's built-in `remember` feature. | **TBD** — no design yet. SDD will include placeholder/mock UI on login forms. |
| A1+ | **Remember me help text** | Context-aware text below checkbox: "Keep me logged in for 30 days" (student) / "30 minutes" (staff). | Placeholder — simple `<p>` below checkbox |
| A3 | **Session expiry countdown** | Banner/modal that appears when session is about to timeout (e.g. "Session expires in 2 min. Still here?"). Auto-logout if no response. | **TBD** — no design yet. SDD will include placeholder/mock UI (likely a Blade partial + JS countdown). |
| A3+ | **Session expiry modal at 60s** | Blocking modal overlay at 60s remaining forces user attention. Banner at 120s stays as advance warning. | Placeholder — modal reusing existing patterns |
| B1 | **Active session indicator** | Nav bar shows green dot or "Session active" text when user is logged in and session is alive. | **TBD** — no design yet. SDD will include placeholder/mock UI in nav bar. |
| B1+ | **Session indicator tooltip** | Hover tooltip on green dot: "Session active". Clarifies meaning. | 1-line `title` attribute |
| C4 | **Auto-logout on inactivity** *(optional)* | JS tracks mouse/keyboard activity. Idle for X min → warning modal → auto-logout at 0. Complements A3. | **TBD** — no design yet. ~3-4 hours effort. Only if user wants it. |
| Lockout+ | **Lockout error with countdown** | Red error banner with live countdown timer when staff account is locked. Shows "Try again in X min Y sec". | Placeholder — new Blade partial + JS |

### Skipped (will NOT be implemented)

| # | Feature | Why skipped |
|---|---------|-------------|
| A2 | Password change | User decision — not needed for this FYP |
| B3 | Login audit trail | Overkill — new migration + model + UI page for minimal demo value |
| C1 | Two-factor auth (2FA) | User decision — too complex for this FYP |
| C2 | Session management | User decision — too complex for this FYP |
| C3 | Password strength indicator | User decision — not needed |
