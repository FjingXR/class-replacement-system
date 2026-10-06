# Proposal: login-system

## Why the Change Is Needed

The TARUMT Class Replacement System requires authentication that distinguishes between three user roles — Students, Lecturers, and Programme Leaders — each with different capabilities. The current codebase ships Laravel Fortify with email-based login, a single flat `users` table, no role distinction, and every auth feature enabled (registration, password reset, email verification, 2FA, passkeys). None of this matches the prototype's requirements.

Per the Form 2 Proposal:
- **Students** have read-only access (view consolidated master timetable, monitor replacement request status).
- **Lecturers** can create and submit replacement requests.
- **Programme Leaders** inherit all lecturer capabilities and additionally manage the First-Come-First-Served approval queue.

Accounts are pre-seeded (no registration), there is no self-service password reset or email verification, and 2FA/Passkeys are not needed for the prototype. Login must use Student ID (for students) or Staff ID (for lecturers/PLs) rather than email.

**Why keep Fortify (stripped) rather than removing it:** Fortify retains rate limiting (5 attempts/min per IP+ID) and session management out of the box. Removing Fortify means reimplementing these security features manually. Stripping Fortify's `features` array to login/logout only achieves the goal with far less code and risk.

## What's In Scope

1. **Database schema** — 6 new tables + modification of existing `users` table:
   - `faculties`, `departments`, `programmes`, `cohorts` (reference/support data)
   - `students`, `lecturers` (1:1 child tables of `users`)
   - `users` (add `role` column by editing the existing `0001_01_01_000000_create_users_table.php` migration inline; retain existing Fortify columns)
2. **Auth configuration** — Strip Laravel Fortify down to login + logout only; disable registration, password reset, email verification, 2FA, and passkeys.
3. **Login flow** — Two separate login pages (`/login/student`, `/login/staff`); custom `Fortify::authenticateUsing()` callback that resolves the parent `users` record via the relevant child table (`student_id` or `staff_id`) and verifies the password. The default Fortify `/login` GET view is replaced: `/login` GET redirects to `/login/student` (default landing), and unauthenticated users hitting a protected route are redirected to `/login/student`. The `/login` POST endpoint remains Fortify's unified submission handler.
4. **Role-based access control** — Custom `role` middleware accepting comma-separated role parameters from the 2-value `role` column (`student`, `lecturer`). Routes any lecturer (including PLs) can access use `role:lecturer`; student-only routes use `role:student`; PL-only routes are protected by `role:lecturer` followed by a separate `pl` middleware that checks `lecturers.is_pl`. The `programme_leader` role value does NOT exist in the `role` column — PLs have `role=lecturer` and `lecturers.is_pl=true`.
5. **Dashboard** — Single `/dashboard` route with role-based conditional rendering; shared layout, role-specific navigation items and content sections.
6. **Seed data** — Hardcoded in `DatabaseSeeder.php`: 3 faculties, 3 departments, 5 programmes, 14 cohorts, 14 lecturers (from `dataset/lecturers.md`), ~153-198 mock students (per-cohort ranges). Default password `Tarumt@2026`.
7. **Datasets** — Create `dataset/cohorts.md` documenting the 14 cohorts.

## What's Explicitly Out of Scope

- **Registration / sign-up** — Accounts are pre-seeded only.
- **Forgot password / password reset** — Not implemented.
- **Email verification** — Disabled.
- **Two-factor authentication (2FA)** — Disabled; `two_factor_*` columns retained on `users` for migration continuity but unused.
- **Passkeys / WebAuthn** — Disabled.
- **Remember Me** — Deferred to a future change.
- **Profile editing / settings pages** — Not part of this change.
- **OAuth / Socialite** — Not included.
- **API token auth (Sanctum)** — Not included; no API routes.
- **Admin account management UI** — Not included; seeding only.
- **Class scheduling / replacement request logic** — Belongs to future changes; this change only establishes auth + role foundation.
- **Timetable rendering** — Dashboard shows role-appropriate placeholder content; actual timetable data/logic is out of scope.

## Impact Scope

### Files to Create

| Path | Purpose |
|---|---|
| `app/Models/Faculty.php` | Eloquent model for `faculties` |
| `app/Models/Department.php` | Eloquent model for `departments` |
| `app/Models/Programme.php` | Eloquent model for `programmes` |
| `app/Models/Cohort.php` | Eloquent model for `cohorts` |
| `app/Models/Student.php` | Eloquent model for `students` (1:1 with User) |
| `app/Models/Lecturer.php` | Eloquent model for `lecturers` (1:1 with User) |
| `app/Http/Middleware/CheckRole.php` | Custom role middleware with parameter (`role:student`, `role:lecturer`) |
| `app/Http/Middleware/CheckPl.php` | Custom middleware checking `lecturers.is_pl` boolean (registered as `pl` alias) |
| `database/migrations/0001_01_01_000003_create_faculties_table.php` | Migrations for faculties |
| `database/migrations/0001_01_01_000004_create_departments_table.php` | Migrations for departments |
| `database/migrations/0001_01_01_000005_create_programmes_table.php` | Migrations for programmes |
| `database/migrations/0001_01_01_000006_create_cohorts_table.php` | Migrations for cohorts |
| `database/migrations/0001_01_01_000007_create_students_table.php` | Migrations for students |
| `database/migrations/0001_01_01_000008_create_lecturers_table.php` | Migrations for lecturers |
| `resources/views/pages/auth/login-student.blade.php` | Student login view |
| `resources/views/pages/auth/login-staff.blade.php` | Staff login view |
| `dataset/cohorts.md` | Dataset file documenting 14 cohorts |

### Files to Modify

| Path | Change |
|---|---|
| `database/migrations/0001_01_01_000000_create_users_table.php` | Edit inline to add `role` column (`varchar(20)`, CHECK `student` OR `lecturer`) to the `users` table definition |
| `app/Models/User.php` | Add `student()` and `lecturer()` hasOne relationships; add `role` to the `#[Fillable(...)]` attribute; keep `password` hashed cast; remove `PasskeyUser` interface, `PasskeyAuthenticatable` and `TwoFactorAuthenticatable` traits (features disabled) |
| `database/factories/UserFactory.php` | Add `role` to factory `definition()` (default e.g. `lecturer`); add `student()` and `lecturer()` state methods for test fixtures |
| `config/fortify.php` | Set `features` array to `[]` (empty) — login and logout remain available since they are not feature-flagged |
| `app/Providers/FortifyServiceProvider.php` | Register student/staff login views; implement `Fortify::authenticateUsing()` callback; keep login rate limiter; remove registration/reset/2FA/email-verification view registrations and `createUsersUsing`/`resetUserPasswordsUsing` action bindings (features disabled) |
| `routes/web.php` | Add `/login/student`, `/login/staff`, `/logout` routes; replace `/login` GET with redirect to `/login/student`; **modify** the existing `/dashboard` route — remove `verified` middleware (email verification disabled), keep `auth` only (role differentiation handled by Blade conditional rendering per AC #1–3; the `role` and `pl` middleware aliases are established in `bootstrap/app.php` for use by future role-exclusive routes per AC #4–5); **replace** the existing `Route::get('/', closure)` definition with `Route::view('/', 'welcome')->name('home')` to restore the `home` route name required by auth layout views and `tests/Feature/ExampleTest.php`; remove `require __DIR__.'/settings.php'` (profile/settings pages — not part of this change); remove dummy routes `/login-dummy` and the duplicate `/replacement-arrangement` |
| `bootstrap/app.php` | Register `role` and `pl` middleware aliases |
| `resources/views/dashboard.blade.php` | Add role-based conditional content sections |
| `resources/views/welcome.blade.php` | Add login options (links to student/staff login pages) |
| `database/seeders/DatabaseSeeder.php` | Hardcoded seed data: faculties, departments, programmes, cohorts, lecturers, students |
| `.env` | Ensure PostgreSQL connection config (already set) |

### Modules/Systems Affected

- **Auth module** — Fortify config, User model, FortifyServiceProvider
- **Routing** — New auth routes, dashboard route; `/login` GET replaced with redirect; `home` route name added; `routes/settings.php` inclusion removed
- **Database** — 6 new tables, 1 modified table (`users`), seeder
- **Views** — 2 new login pages, modified dashboard and welcome pages
- **Middleware** — New role middleware
- **Tests** — Existing auth tests (`tests/Feature/Auth/AuthenticationTest.php`, `PasswordResetTest.php`, `RegistrationTest.php`, `EmailVerificationTest.php`, `TwoFactorChallengeTest.php`, `PasswordConfirmationTest.php`), settings tests (`tests/Feature/Settings/SecurityTest.php`, `ProfileUpdateTest.php`), and `tests/Feature/DashboardTest.php` depend on disabled features and the old `/login` GET route. These tests must be **removed** (disabled features) or **refactored** (`DashboardTest`, `AuthenticationTest`) to target the new student/staff login flow. Note: `tests/Feature/ExampleTest.php` is NOT affected — it only calls `route('home')`, which is restored by the `/` route name fix, so it passes unchanged. The test suite must pass after this change.

### Legacy Migrations Retained

The existing migrations `2024_01_01_000000_create_passkeys_table.php` and `2025_08_14_170933_add_two_factor_columns_to_users_table.php` are **intentionally retained** (not deleted). The `passkeys` table and `two_factor_*` columns are created but unused (2FA/passkeys disabled). Keeping these migrations avoids breaking rollback history and remaining test data.

### Dependencies Between Changes

This change is a **foundation** — subsequent changes (replacement request system, FCFS approval dashboard, timetable integration) depend on the auth + role infrastructure established here. No prior SDD changes exist.

## Acceptance Criteria

1. A student can log in via `/login/student` using their `student_id` + password and is redirected to `/dashboard` showing read-only timetable placeholder content.
2. A lecturer can log in via `/login/staff` using their `staff_id` + password and is redirected to `/dashboard` showing replacement request management placeholder content.
3. A Programme Leader can log in via `/login/staff` and sees lecturer content + FCFS approval queue placeholder content on `/dashboard`.
4. The `CheckRole` middleware blocks a student from a `role:lecturer` route (returns 403) — verified by a unit test exercising the middleware directly (no production role-exclusive routes exist in this change; future changes will create them).
5. The `CheckPl` middleware blocks a non-PL lecturer from a `pl`-protected route (returns 403) — verified by a unit test exercising the middleware directly.
6. Logout returns the user to `/` (welcome page).
7. Seeding via `php artisan migrate:fresh --seed` creates all reference data (faculties, departments, programmes, cohorts) and user accounts (14 lecturers, ~153-198 students) with default password `Tarumt@2026`.
8. Login rate limiting (5 attempts/min) is active.
9. Fortify feature routes (registration, password reset, email verification, 2FA challenge, passkey endpoints under Fortify) are not registered (removed from `config/fortify.php` `features` array); requests to those paths return 404.
10. Settings and passkey routes (`/settings/profile`, `/settings/appearance`, `/settings/security`, `/.well-known/passkey-endpoints`) are not registered (removed via `routes/settings.php` include removal); requests to those paths return 404.
11. Seeded users have `email_verified_at` set to `now()` during seeding (so the `verified` middleware would pass even if retained) — though the `verified` middleware is removed from the `/dashboard` route.
12. Rate-limited login attempts (5/min exceeded) redirect back to the relevant login page (`/login/student` or `/login/staff`) with a 429 response.
13. The test suite passes after removing tests for disabled auth features (`PasswordResetTest`, `RegistrationTest`, `EmailVerificationTest`, `TwoFactorChallengeTest`), removing the `PasswordConfirmationTest` (no in-app flow exercises password confirmation after settings removal), removing settings tests (`tests/Feature/Settings/SecurityTest.php`, `ProfileUpdateTest.php` — their routes removed via `routes/settings.php` exclusion), and refactoring `DashboardTest`/`AuthenticationTest` to target the new student/staff login flow.
