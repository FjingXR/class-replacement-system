# Review Log: login-system

## proposal Round 1 — 2026-07-04 07:40

### 🔴 Fixed
- `app/Model/Lecturer.php` path typo → corrected to `app/Models/Lecturer.php`
- Impact Scope omitted affected test suite, `routes/settings.php`, and `home` route-name handling → added test inventory under "Modules/Systems Affected", `routes/settings.php` removal in `routes/web.php` modify entry, `home` route name addition, and acceptance criterion #10 for test suite passing
- `/login` GET behavior unspecified → added explicit statement that `/login` GET redirects to `/login/student`, unauthenticated users hitting protected routes redirect to `/login/student`

### 🟡 Addressed
- Table count internal inconsistency ("7 new tables") → corrected to "6 new tables + modification of existing `users` table"; also fixed in "Modules/Systems Affected"
- `DatabaseSeeder.php` placed under "Files to Create" despite (Modify) tag → moved to "Files to Modify" table
- `role` middleware syntax (`role:lecturer,programme_leader`) contradicted the rejected 3-role decision → reworded: `role:student` / `role:lecturer`; PL-only routes use separate `is_pl` check; explicitly stated `programme_leader` does NOT exist as a role value
- `User.php` modification list incomplete (didn't mention removing PasskeyUser interface + PasskeyAuthenticatable/TwoFactorAuthenticatable traits) → added to modify entry
- No migration file listed for `users` table modification → added `database/migrations/0001_01_01_000000_create_users_table.php` to "Files to Modify" with inline edit description
- `routes/settings.php` not in scope → added removal to `routes/web.php` modify entry
- `FortifyServiceProvider.php` modification too high-level → expanded to include removal of registration/reset/2FA/email-verification view registrations and `createUsersUsing`/`resetUserPasswordsUsing` action bindings
- Acceptance criterion #9 wording imprecise → rephrased to "routes are not registered (removed from Fortify `features`); requests to those paths return 404"

### 🔴 Outstanding
- (None — awaiting re-review)

## proposal Round 2 — 2026-07-04 07:45

### 🔴 Fixed
- `/dashboard` route middleware ambiguity — AC #1–3 would fail. Now explicitly: modify existing `/dashboard` route, remove `verified` middleware, keep `auth`, apply role middleware per AC #4–5. Also clarified "modify" vs "Add". Also addressed the duplicate `/replacement-arrangement` and `/login-dummy` dummy routes (removed).

### 🟡 Addressed
- AC #9 conflated two removal mechanisms (Fortify `features` vs `routes/settings.php` removal) → split into AC #9 (Fortify feature routes) and AC #10 (settings/passkey routes)
- Misleading justification for removing `routes/settings.php` (said "enabled by 2FA/passkeys") → reworded to "profile/settings pages — not part of this change"
- `PasswordConfirmationTest.php` misclassified → in AC #13, explicitly removed (no in-app flow exercises password confirmation after settings removal)
- Existing dummy routes `/login-dummy` and duplicate `/replacement-arrangement` not addressed → added removal to `routes/web.php` modify entry
- `config/fortify.php` wording "strip to login/logout only" misleading (no `Features::login()`) → reworded to "set `features` array to `[]` (empty) — login and logout remain available since they are not feature-flagged"
- PL-only middleware approach left as OR (two options) → committed to single approach: `role:lecturer` followed by separate `pl` middleware checking `lecturers.is_pl`; added `CheckPl.php` middleware file to "Files to Create" and `pl` alias to `bootstrap/app.php` modify entry

### 🟢 Added from optional suggestions
- AC #11: seeded users have `email_verified_at` set to `now()` (defensive — even though `verified` middleware removed from `/dashboard`)
- AC #12: rate-limited login attempts redirect back to relevant login page with 429

### 🔴 Outstanding
- (None — awaiting re-review)

## proposal Round 3 — 2026-07-04 07:50

### 🟡 Addressed
- AC #13 omitted Settings tests (`SecurityTest.php`, `ProfileUpdateTest.php`) from removal list → added to AC #13
- `database/factories/UserFactory.php` missing from Modify list (role column NOT NULL would break factory-based tests) → added to "Files to Modify" with `role` in definition + `student()`/`lecturer()` state methods
- Dashboard route middleware wording "apply role middleware per AC #4–5" ambiguous (could be misread as putting `role:X` on `/dashboard`) → rephrased: keep `auth` only, role differentiation via Blade; middleware aliases for future role-exclusive routes
- AC #4 and #5 referenced lecturer-only/PL-only routes that don't exist in this scope → reframed to verify middleware behavior directly via unit tests
- "Uncomment and name the `/` route as `home`" didn't say to remove the active closure → rephrased to "replace the existing `Route::get('/', closure)` with `Route::view('/', 'welcome')->name('home')`"
- Optional: `User.php` "add role to fillable" → "add `role` to the `#[Fillable(...)]` attribute" (matches PHP 8 attribute style)

### 🔴 Outstanding
- (None — awaiting Round 4 review)

## proposal Round 4 — 2026-07-04 07:55

### 🟡 Addressed (declarative soft-freeze touch-up)
- `ExampleTest.php` misclassified on line 91 of proposal.md as "affected by disabled features/old `/login` route" → clarified that ExampleTest only calls `route('home')` (restored by the `/` route name fix), passes unchanged, no test-file edit needed

### 🟢 Verdict
- READY TO FREEZE — 0 🔴 critical, 0 outstanding 🟡 blockers
- All Round 1-3 issues verified resolved against actual codebase

### 🔴 Outstanding
- (None)

**✅ proposal.md FROZEN at Round 4.**

## design Round 1 — 2026-07-04 08:05

### 🔴 Fixed
- Fortify `username` config left as open question — actually mandatory: `LoginRequest` validates `Fortify::username()` field, so default `'email'` causes 422 before `authenticateUsing()` runs. Resolved: set `'username' => 'login_id'` AND `'lowercase_usernames' => false` in `config/fortify.php` (lowercasing would break mixed-case student IDs on case-sensitive PostgreSQL). Closed Open Question #2.
- Dashboard references non-existent `DashboardController` and `<x-employee:*>` Blade components — contradicted frozen proposal (which uses `Route::view('dashboard', 'dashboard')` + inline Blade sections). Replaced all `DashboardController` references with `Route::view` rendering; replaced `<x-employee:*>` components with inline placeholder sections in `dashboard.blade.php`. Aligned Flow 1/2 with this.

### 🟡 Addressed
- `/logout` route Fortify already registers it; proposal instruction to "add /logout route" would create a conflicting duplicate → added explicit note in Flow 4 to NOT redefine `/logout` in `routes/web.php`; rely on Fortify's default + `redirects('logout', '/')`
- `/login` GET redirect mechanism underspecified → specified: `Fortify::loginView(fn () => redirect()->route('login.student'))` in `FortifyServiceProvider`; Flow 3 updated
- DB-level CHECK constraints (role + email domain) not addressed → added "Database CHECK Constraints" subsection; flagged email-domain CHECK as beyond frozen proposal scope (requires factory email generator patch); role CHECK is safe with factory
- Student/Lecturer models (`user_id` as PK+FK, no `id`) missing Eloquent PK overrides → added `[PK = user_id, $incrementing = false]` notes to model relationship graph
- Student mock ID format deferred as TBD → decided: `{YY}{3-letter prog code}{4-digit seq padded}` (e.g., `25RSD0001`); closed Open Question #1
- Error-path in login flows said "Fortify throws AuthenticationException" → corrected to "ValidationException with auth.failed message + increments rate limiter" (verified against Fortify vendor source)
- Login routes missing `guest` middleware note → added to Login View Design section
- Open Questions section → renamed to "Resolved Design Questions" (both closed)

### 🔴 Outstanding
- (None — awaiting re-review)

## design Round 2 — 2026-07-04 08:15

### 🟡 Addressed
- `authenticateUsing()` callback missing fallback for invalid `login_type` → added validation step to Flow 1/2 step 2 (`login_type must be one of ['student', 'staff']; if not → return null`), plus full callback code block in Architecture Overview section
- Optional: clarified Student/Lecturer `$primaryKey = 'user_id'` and `$incrementing = false` must both be set

### 🟢 Verdict
- READY TO FREEZE — 0 🔴 critical, 0 outstanding
- All Round 1 issues verified resolved

### 🔴 Outstanding
- (None)

**✅ design.md FROZEN at Round 2.**

## tasks Round 1 — 2026-07-04 08:25

### 🔴 Fixed
- T10/T12 ordering inversion: T10 (seed reference data) depended on T12 (dataset/cohorts.md) → reordered: Factory & Dataset (now T3) placed before Seeder (now T12/T13)
- T12 student() state email format `{name}@student.tarc.edu.my` → fixed to `{student_id}@student.tarc.edu.my` (matching frozen design.md)

### 🟡 Addressed
- Dependency annotations: all 15 tasks now have `[depends on X]` or `[no deps]` annotations
- T6 (middleware) cross-referenced T13 instead of T14 → corrected
- T7 "verify login works via browser" premature → replaced with "verify callback compiles (Tinker)"
- T14 dep annotation missing T3 (factory) → added
- T14 `test_login_screen_can_be_rendered` guidance → added update instruction
- T9 orphaned dummy views → acknowledged as intentionally left on disk

### 🟢 Verdict
- READY TO FREEZE — 0 🔴 critical, 0 outstanding blockers

### 🔴 Outstanding
- (None)

**✅ tasks.md FROZEN at Round 1.**
## Archive Note — 2026-10-06

### ✅ Applied (moved here from the project-level `.sdd/changes/` pile)
- Implemented: exact migration files from T1/T2 exist (`database/migrations/0001_01_01_000003_create_faculties_table.php` … `000008_create_lecturers_table.php`), role-split login views exist (`resources/views/auth/login-staff.blade.php`, `login-student.blade.php`), routes `/login/staff` + `/login/student` live in `routes/web.php` (L7–13).
- tasks.md checkboxes were never maintained (0/15 ticked) — same organic-implementation pattern as macos-ui-refactor.
- Later archived changes built on it: `2026-08-06-auth-wiring`, `auth-wiring-enhancements`, `2026-08-10-login-oop-refactor`.
- Status: applied.
