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
| `request-approval` | 2026-07-30 | Request approval UI mock page |
| `replacement-home-dashboard` | — | Replacement home dashboard UI mock page |
| `centralized-mock-data` | 2026-08-01 | Centralized mock data to mock-data.js |
| `mock-data-centralization` | 2026-08-01 | Mock data centralization (continued) |

## In Progress (proposed, not yet applied)

| SDD | Date | What it does |
|-----|------|-------------|
| `student-my-timetable-ui` | 2026-08-01 | Student my timetable UI mock page — frontend-only |

## Queued (not yet proposed)

| # | SDD name | What it does | Depends on | Prompt file |
|---|----------|-------------|------------|-------------|
| 1 | `logout-session-timeout` | Logout button wiring + session timeout (1 min testing) + role-based login redirect + nav bar user profile | student-my-timetable-ui (for student redirect target) | `prompts/run-logout-session-timeout.md` |

## Deferred / Future

| # | Feature | Why deferred | Target sprint |
|---|---------|-------------|---------------|
| 1 | Session timeout 30 min (production) | Set to 1 min for testing; will extend when ready for prod | Sprint 3 hardening |
| 2 | Advanced auth features | Brainstorming phase — see discussion below | TBD |

---

## Proposed advanced auth features (discuss with user)

> Excludes: forgot password, create new account (per user request).

### Tier 1 — Essential (should have for FYP demo)

| # | Feature | What it does | Effort | FR/NFR ref |
|---|---------|-------------|--------|------------|
| A1 | **Remember me** | "Remember me" checkbox on login → longer session (e.g. 7 days) vs. default 1 min / 30 min. Uses Fortify's built-in `remember` feature. | Small | — |
| A2 | **Password change** | Logged-in user can change their own password (current password + new password + confirm). Useful for demo + security. | Small | — |
| A3 | **Session awareness banner** | Show a countdown or "Session expires in X min" warning when session is about to timeout. Prompt user to extend or auto-redirect to login. | Medium | NFR 2.4 |

### Tier 2 — Nice to have (polish for demo)

| # | Feature | What it does | Effort | FR/NFR ref |
|---|---------|-------------|--------|------------|
| B1 | **Active session indicator** | Nav bar shows "Last active: X min ago" or green dot when session is alive. | Small | — |
| B2 | **Role-based login landing** | After login, student → student timetable, staff → staff timetable, PL → PL dashboard. Already in logout-session-timeout SDD. | Done (in SDD) | — |
| B3 | **Login audit trail** | Log every login attempt (success/fail) with timestamp, IP, user agent. Store in `login_log` table. Useful for demo + security. | Medium | — |

### Tier 3 — Advanced (overkill for FYP but good to know)

| # | Feature | What it does | Effort | FR/NFR ref |
|---|---------|-------------|--------|------------|
| C1 | **Two-factor auth (2FA)** | TOTP-based 2FA (Google Authenticator). Fortify has built-in support. | Large | — |
| C2 | **Session management** | View all active sessions (device, IP, last active). Revoke individual or all sessions. | Large | — |
| C3 | **Password strength indicator** | Real-time password strength meter on password change form. | Small | — |
| C4 | **Auto-logout on inactivity** | JS-based countdown that redirects to logout after X min of no mouse/keyboard activity. Complements server-side session timeout. | Medium | NFR 2.4 |

### Recommendation for this FYP

**Do A1 + A2 + A3** (Tier 1). They're small-to-medium effort, add real value to the demo, and show security awareness without overcomplicating the project. B2 is already done in the logout-session-timeout SDD. Skip Tier 3 unless you have spare time.
