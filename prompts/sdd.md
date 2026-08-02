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
| `centralized-mock-data` | 2026-08-01 | Centralized mock data to mock-data.js |
| `mock-data-centralization` | 2026-08-01 | Mock data centralization (continued) |
| `request-approval` | 2026-08-01 | Request approval UI mock page — PL side; frozen after 14 review rounds; globals `approvalRequests` + `URGENCY_REFERENCE_DATE` + Task 5 guard appended |
| `student-my-timetable-ui` | 2026-08-02 | Student my timetable UI mock page — frontend-only; SDD artifacts frozen (proposal 2 rounds, design 1 round, tasks 1 round); ready for `/sdd-apply` |

## In Progress (proposed, not yet applied)

| SDD | Date | What it does |
|-----|------|-------------|
| *(none)* | — | — |

## Queued (not yet proposed)

| # | SDD name | What it does | Depends on | Prompt file |
|---|----------|-------------|------------|-------------|
| 1 | `logout-session-timeout` | Logout + **role-based session lifetime (student 30 days, staff 30 min)** + role-based redirect + user panel wiring + **A1 remember me + A3 session countdown + B1 session indicator + (optional) C4 auto-logout** | student-my-timetable-ui (for student redirect target) | `prompts/run-logout-session-timeout.md` |

## Deferred / Future

| # | Feature | Why deferred | Target sprint |
|---|---------|-------------|---------------|
| 1 | Session timeout 30 min (production) | Set to 1 min for testing; will extend when ready for prod | Sprint 3 hardening |

---

## Advanced auth features — final decisions

> Excludes: forgot password, create new account, password change (A2), 2FA (C1), session management (C2), password strength (C3), login audit trail (B3) — all skipped per user decision.

### Accepted (included in logout-session-timeout SDD)

| # | Feature | What it does | Frontend status |
|---|---------|-------------|----------------|
| — | **Role-based session lifetime** | Students (view-only, low risk): 30-day session. Staff (approve/reject, higher risk): 30-min session. | Backend config — no frontend needed |
| — | **User panel wiring** | Replace 4 hardcoded values in .user-panel (avatar "KL", name "Kylian Mbappe", role "Lecturer", dummy logout) with dynamic data from Auth::user(). Add student_id/staff_id. | Existing HTML — just replace data |
| A1 | **Remember me** | "Remember me" checkbox on login → longer session (e.g. 7 days) vs. default 1 min. Uses Fortify's built-in `remember` feature. | **TBD** — no design yet. SDD will include placeholder/mock UI on login forms. |
| A3 | **Session expiry countdown** | Banner/modal that appears when session is about to timeout (e.g. "Session expires in 2 min. Still here?"). Auto-logout if no response. | **TBD** — no design yet. SDD will include placeholder/mock UI (likely a Blade partial + JS countdown). |
| B1 | **Active session indicator** | Nav bar shows green dot or "Session active" text when user is logged in and session is alive. | **TBD** — no design yet. SDD will include placeholder/mock UI in nav bar. |
| C4 | **Auto-logout on inactivity** *(optional)* | JS tracks mouse/keyboard activity. Idle for X min → warning modal → auto-logout at 0. Complements A3. | **TBD** — no design yet. ~3-4 hours effort. Only if user wants it. |

### Skipped (will NOT be implemented)

| # | Feature | Why skipped |
|---|---------|-------------|
| A2 | Password change | User decision — not needed for this FYP |
| B3 | Login audit trail | Overkill — new migration + model + UI page for minimal demo value |
| C1 | Two-factor auth (2FA) | User decision — too complex for this FYP |
| C2 | Session management | User decision — too complex for this FYP |
| C3 | Password strength indicator | User decision — not needed |
