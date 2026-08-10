# Review Log: auth-wiring

## proposal.md Round 1 — 2026-08-03 09:55

### 🔴 Fixed
- Feature 4: `config('session.lifetime')` mechanism is broken — replaced with session-stored lifetime + custom middleware approach
- Feature 8: No specified hook point — specified inside `authenticateUsing()` before DB query, clarified coexistence with existing Fortify rate limiter

### 🟡 Addressed
- Feature 3: Added note that `config/fortify.php` `home` becomes fallback
- Feature 5: Specified exact HTML for remember me checkbox
- Feature 6: Noted JS needs server-side remaining time (design phase will handle)
- Feature 8: Specified lockout message delivery via Fortify error bag
- Traceability table: Added FR 4.13, NFR 2.5, NFR 3.4, noted student 30d is deliberate deviation from NFR 2.4
- Feature 9: Specified idle timeout as 25 min → warning → 30 min logout (aligned with staff session lifetime)

## proposal.md Round 2 — 2026-08-03 10:00

### 🔴 Fixed
- Feature 8: Added explicit `if ($loginType === 'staff') { ... }` guard around all lockout logic; added note that failed-attempt counting only increments when staff ID exists in DB

### 🟡 Addressed
- Feature 4: Clarified `last_activity` comes from Laravel's session timestamp
- Noted EnsureSessionLifetime middleware needs registration in HTTP kernel

## proposal.md Round 3 — 2026-08-03 10:05

### 🔴 Fixed
- Feature 3: Added explicit note that `/student-my-timetable-ui` route exists in `routes/web.php` with template at `student-my-timetable-UI-design-template.blade.php` — reviewer flagged as non-existent but it does exist

### 🟡 Addressed
- Feature 4: Clarified testing mode toggle (comment in config or env — implementer's choice)
- Feature 6/7: Noted design.md should flag these as stubs

## proposal.md Round 4 — 2026-08-03 10:10

### ✅ Pass — proposal.md is now FROZEN

No 🔴 issues. Round 3 fix verified. 4 🟡 suggestions noted for design.md phase:
- Feature 8: Move implementation details (cache keys, control flow) to design.md
- Feature 3: Add fallback for undefined role
- Feature 4: NFR 2.4 deviation to be tracked as explicit override
- Feature 9: Multi-tab idle tracking behavior to be specified

## design.md Round 1 — 2026-08-03 10:15

### 🔴 Fixed
1. Kernel.php doesn't exist in Laravel 11 → replaced with bootstrap/app.php
2. Session config lifetime was a hard ceiling → set to 43200, enforce per-role via middleware
3. last_activity never set → confirmed Laravel's built-in UpdateSessionTimestamp handles it
4. Lockout error message not delivered → use ValidationException with custom message
5. Countdown had no route/view structure → specified data attributes, HTML, JS logic
6. Duplicate initials function → use existing Auth::user()->initials() model method

### 🟡 Addressed
- Added user panel wiring code snippet (proposal had no design detail)
- Added staff session lifetime production/testing toggle comment
- /session/extend not needed → page reload suffices
- Multi-tab: BroadcastChannel with localStorage fallback
- NFR 2.4 deviation tracked in "NFR overrides" section

## design.md Round 2 — 2026-08-03 10:20

### 🔴 Fixed
- last_activity: Replaced non-existent UpdateSessionTimestamp with self-contained `session('_auth_last_activity')` tracked by EnsureSessionLifetime middleware itself
- Countdown data attribute: Updated to use `session('_auth_last_activity')` instead of non-existent `session('last_activity')`

### 🟡 Addressed
- Auto-logout script/form placement: specified in ui-nav-bar.blade.php (included on every page via layout)
- Countdown partial inclusion: @include in ui-template.blade.php layout inside @auth block

## design.md Round 3 — 2026-08-03 10:25

### ✅ Pass — design.md is now FROZEN

No 🔴 issues. Round 2 fix verified. All 9 features covered with concrete implementation details. 2 🟡 notes:
- Lockout error delivery differs from proposal wording (ValidationException vs return null) — same outcome
- Countdown "Still here?" reloads full page — placeholder for frontend SDD

## tasks.md Round 1 — 2026-08-03 10:30

### ✅ Pass — tasks.md is now FROZEN

No 🔴 issues. 3 🟡 items fixed:
- Task 3.3: Clarified middleware order (read previous timestamp first, then update)
- Task 6.3: Added placement detail (after nav bar, before @yield('content'))
- Task 8.1: Added BroadcastChannel channel name and localStorage key pattern
