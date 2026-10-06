# Design: login-system

**Status:** proposal.md is frozen; this document implements the frozen proposal.

## Technical Approach

### Architecture Overview

The login system is built on **Laravel 11+ with a stripped-down Laravel Fortify**. Fortify normally registers a full auth feature set (registration, password reset, email verification, 2FA, passkeys); this change sets `features` to `[]`, which disables registration of those routes while keeping Fortify's core login/logout handlers and rate limiting.

Fortify's default authentication pipeline assumes a single `email` column on the `users` table. This change overrides that via `Fortify::authenticateUsing()` — a custom callback that resolves the parent `User` record through the relevant 1:1 child table (`students.student_id` or `lecturers.staff_id`) before verifying the password against `users.password`.

**Critical Fortify config changes required in `config/fortify.php`:**
- `'username' => 'login_id'` — the login forms send a field named `login_id` (not `email`). Fortify's `LoginRequest` validates `Fortify::username()` as `required|string`; leaving it at the default `'email'` causes a 422 before `authenticateUsing()` is even invoked.
- `'lowercase_usernames' => false` — Fortify's `CanonicalizeUsername` middleware lowercases the username field before authentication. Student IDs are mixed-case (e.g., `25SMR10186`) and PostgreSQL `varchar` is case-sensitive; lowercasing would break student login lookups.

`Fortify::authenticateUsing()` callback must include a fallback for invalid/missing `login_type`:

```php
Fortify::authenticateUsing(function (Request $request) {
    $loginType = $request->input('login_type');
    if (!in_array($loginType, ['student', 'staff'], true)) {
        return null; // auth fail
    }
    $user = match ($loginType) {
        'student' => Student::where('student_id', $request->input('login_id'))->first()?->user,
        'staff' => Lecturer::where('staff_id', $request->input('login_id'))->first()?->user,
    };
    return $user && Hash::check($request->password, $user->password) ? $user : null;
});
```

Authentication uses Laravel's default `web` session guard (cookie-based sessions stored in the `sessions` table). Two separate login pages provide role-appropriate UIs but both POST to the same Fortify endpoint; a hidden `login_type` field (`student` | `staff`) tells the `authenticateUsing()` callback which child table to query.

### Key Architecture Decisions

| Decision | Chosen Approach | Alternative Rejected | Rationale |
|---|---|---|---|
| Auth backend | Stripped Fortify (`features: []`) | Custom controllers from scratch | Retains rate limiting (5/min) + session handling; less code |
| Login identifier | `student_id` / `staff_id` in child tables | `email` column on `users` | User requirement; IDs are the natural credential at TARUMT |
| User↔role modeling | Parent `users` + 1:1 child `students`/`lecturers` | Single flat `users` with nullable ID columns | Properly normalized; no sparse columns on `users` |
| Role representation | `users.role` (2 values: `student`, `lecturer`) + `lecturers.is_pl` boolean | 3-value `role` enum | Avoids redundancy between `role` and `is_pl`; PL is a sub-type of lecturer |
| PL-only route protection | Separate `pl` middleware checking `lecturers.is_pl` | Single `role:programme_leader` middleware | `programme_leader` role value doesn't exist; keeps `role` middleware simple (2 values) |
| `/dashboard` access | Single route, `auth` only, Blade conditional rendering | Per-role routes (`/student/dashboard`, etc.) | Shared UI per user feedback; single layout, role-gated content |
| Account creation | Hardcoded PHP seeder | Registration form / CSV import / Admin UI | Prototype scope; ~167-212 users, no self-registration |
| Default password | `Tarumt@2026` (standardized, auto-hashed via `hashed` cast) | Per-user random passwords | Simpler seeding; no password-reset flow to distribute them |
| Migration strategy | Edit existing `0001_01_01_000000_create_users_table.php` inline to add `role` | New `add_role_to_users_table` migration | Cleaner `migrate:fresh` output; prototype has no production data |
| Legacy migrations | Retain `passkeys` table + `two_factor_*` columns | Delete migrations | Avoids breaking rollback history; columns nullable/unused |

### Data Flow Diagrams

#### Flow 1: Student Login

```
[Browser] GET /login/student
    → Fortify renders login-student view (via FortifyServiceProvider loginView callback)
    → Returns HTML form (student_id + password fields, hidden login_type=student)

[Browser] POST /login  (login_type=student, login_id=25SMR10186, password=...)
    → Fortify RateLimiter::for('login') checks 5/min per IP+login_id
    → FortifyServiceProvider::authenticateUsing(Request $request)
        1. Read $request->input('login_type') → 'student'
        2. Validate: login_type must be one of ['student', 'staff']; if not → return null (auth fail)
        3. Student::where('student_id', $request->input('login_id'))->first()
        4. If found: $user = $student->user (hasOne inverse → users.id)
        5. If $user && Hash::check($request->password, $user->password) → return $user
        6. Else → return null (Fortify throws ValidationException with auth.failed message and increments login rate limiter)
    → Auth::login($user, false)  [no remember me]
    → Fortify redirects to /dashboard (home path config)
    → /dashboard Route::view renders dashboard.blade.php
        → @if(auth()->user()->role === 'student') → student placeholder section
```

#### Flow 2: Staff (Lecturer/PL) Login

```
[Browser] GET /login/staff
    → Returns HTML form (staff_id + password fields, hidden login_type=staff)

[Browser] POST /login  (login_type=staff, login_id=5425, password=...)
    → Fortify RateLimiter::for('login') checks 5/min per IP+login_id
    → FortifyServiceProvider::authenticateUsing(Request $request)
        1. Read $request->input('login_type') → 'staff'
        2. Validate: login_type must be one of ['student', 'staff']; if not → return null (auth fail)
        3. Lecturer::where('staff_id', $request->input('login_id'))->first()
        4. If found: $user = $lecturer->user (hasOne inverse → users.id)
        5. If $user && Hash::check($request->password, $user->password) → return $user
        6. Else → return null (Fortify throws ValidationException with auth.failed message and increments login rate limiter)
    → Auth::login($user, false)
    → Redirect to /dashboard
    → /dashboard Route::view renders dashboard.blade.php
        → @if(auth()->user()->role === 'lecturer')
            → lecturer placeholder section
            → @if(auth()->user()->lecturer && auth()->user()->lecturer->is_pl) → PL FCFS queue placeholder section
```

#### Flow 3: Unauthenticated User Hits Protected Route

```
[Browser] GET /dashboard  (no session)
    → auth middleware detects no authenticated user
    → Redirects to /login  (Fortify default unauthenticated redirect)
    → /login GET handled by Fortify::loginView() callback → redirect()->route('login.student')
    → Student sees login form at /login/student
```

#### Flow 4: Logout

```
[Browser] POST /logout
    → Fortify's AuthenticatedSessionController::destroy handles this (already registered by Fortify)
    → Auth::logout() + session invalidate
    → Fortify::redirects('logout', '/') → redirect to /  (welcome page showing both login options)
    → NOTE: do NOT redefine POST /logout in routes/web.php (Fortify already registers it)
```

#### Flow 5: Role Middleware Check (unit-tested, no production routes yet)

```
Request → auth middleware (must be authenticated first)
       → CheckRole:student middleware
           → if Auth::user()->role !== 'student' → abort(403)
           → else → pass request to controller
```

### Dependencies

#### Internal Dependencies (within this change)

```
faculties ← departments ← ┬─ lecturers ← users (via user_id)
                         └─ programmes ← cohorts ← students ← users (via user_id)
```

Migration order enforced by timestamp sequence:
1. `0001_01_01_000000_create_users_table.php` (MODIFIED — adds `role` column)
2. `0001_01_01_000003_create_faculties_table.php` (NEW)
3. `0001_01_01_000004_create_departments_table.php` (NEW — FK to faculties)
4. `0001_01_01_000005_create_programmes_table.php` (NEW — FK to faculties)
5. `0001_01_01_000006_create_cohorts_table.php` (NEW — FK to programmes)
6. `0001_01_01_000007_create_students_table.php` (NEW — FK to users.id + cohorts.id)
7. `0001_01_01_000008_create_lecturers_table.php` (NEW — FK to users.id + departments.id)

Existing migrations (`passkeys`, `two_factor_columns`) run unchanged after the new migrations.

#### Database CHECK Constraints

Two CHECK constraints enforced at the PostgreSQL level via migration:

1. **`users.role` CHECK** — `CHECK (role IN ('student', 'lecturer'))` on the `users` table (added to the modified `0001_01_01_000000_create_users_table.php`). Safe with `UserFactory` because the factory will provide `role` (default `lecturer`) per proposal.md:74.

2. **`users.email` domain CHECK** — `CHECK (email LIKE '%@tarc.edu.my' OR email LIKE '%@student.tarc.edu.my')`. This is an addition beyond the frozen proposal's scope (proposal.md:72 mentions the `role` column only), and requires `UserFactory.php` to generate a `@tarc.edu.my` or `@student.tarc.edu.my` email (not Faker's default `safeEmail()`). The factory's `lecturer()` state should use `{name}@tarc.edu.my` and `student()` state `{student_id}@student.tarc.edu.my`. If this CHECK proves problematic during implementation, it can be deferred to a future change — login does not depend on it.

#### External Package Dependencies

| Package | Version (from composer.json) | Role in this change |
|---|---|---|
| `laravel/fortify` | ^1.37.2 | Login/logout pipeline, rate limiting, `authenticateUsing()` hook |
| `livewire/livewire` | ^4.1 | Not directly used by login, but retained (used by existing Flux UI components) |
| `livewire/flux` | ^2.13.1 | UI components for login forms (`flux:input`, `flux:button`) |

No new packages will be installed. No packages will be removed.

### Model Relationships

```
User (users)
  ├── hasOne Student    (users.id ← students.user_id)
  └── hasOne Lecturer   (users.id ← lecturers.user_id)

Student (students)  [PK = user_id, $incrementing = false]
  ├── belongsTo User    (students.user_id → users.id)
  └── belongsTo Cohort  (students.cohort_id → cohorts.id)

Lecturer (lecturers)  [PK = user_id, $incrementing = false]
  ├── belongsTo User        (lecturers.user_id → users.id)
  └── belongsTo Department  (lecturers.dept_id → departments.id)

Cohort (cohorts)
  ├── belongsTo Programme   (cohorts.programme_id → programmes.id)
  └── hasMany Students      (cohorts.id ← students.cohort_id)

Programme (programmes)
  ├── belongsTo Faculty     (programmes.faculty_id → faculties.id)
  └── hasMany Cohorts       (programmes.id ← cohorts.programme_id)

Department (departments)
  ├── belongsTo Faculty     (departments.faculty_id → faculties.id)
  └── hasMany Lecturers     (departments.id ← lecturers.dept_id)

Faculty (faculties)
  ├── hasMany Departments   (faculties.id ← departments.faculty_id)
  └── hasMany Programmes    (faculties.id ← programmes.faculty_id)
```

### Middleware Design

#### CheckRole Middleware

```
File: app/Http/Middleware/CheckRole.php
Alias: role (registered in bootstrap/app.php)

Usage: role:student  OR  role:lecturer

Logic:
  1. Get allowed roles from parameter (comma-separated)
  2. If Auth::user()->role is NOT in allowed list → abort(403)
  3. Else → $next($request)
```

#### CheckPl Middleware

```
File: app/Http/Middleware/CheckPl.php
Alias: pl (registered in bootstrap/app.php)

Usage: pl  (always after role:lecturer on the same route)

Logic:
  1. Assert Auth::user()->role === 'lecturer' (defensive; route should also have role:lecturer)
  2. Load Auth::user()->lecturer
  3. If ! $lecturer->is_pl → abort(403)
  4. Else → $next($request)
```

### Seeder Design

The `DatabaseSeeder.php` will run in dependency order:

1. **Seed faculties** (3 rows) — FOCS, FAFB, FSSH
2. **Seed departments** (3 rows) — DCIT→FOCS, DSSH→FSSH, DACB→FAFB
3. **Seed programmes** (5 rows) — RSD/DFT/DSF→FOCS, RAF/RBU→FAFB
4. **Seed cohorts** (14 rows) — per `dataset/cohorts.md`
5. **Seed lecturers** (14 rows) — per `dataset/lecturers.md`:
   - For each lecturer: create `User` (name, email, password=Hash::make('Tarumt@2026'), role='lecturer', email_verified_at=now()), then create `Lecturer` (user_id, staff_id, dept_id, is_pl)
   - PLs: Surayaini #5425 (DCIT, is_pl=true), Mohd Nur Rahmat #5516 (DCIT, is_pl=true)
6. **Seed students** (~153-198 rows) — generated per cohort with random counts:
   - For each cohort: `random_int(min, max)` students
   - Student ID format: `{2-digit year}{3-letter programme_code}{4-digit sequential padded}` matching the real TARUMT pattern (e.g., `25RSD0001` for RSD, `25DFT0001` for DFT). Year prefix = `25` (current academic year 2025).
   - For each student: create `User` (name=fake, email=`{student_id}@student.tarc.edu.my`, password=Hash::make('Tarumt@2026'), role='student', email_verified_at=now()), then create `Student` (user_id, student_id, cohort_id)

### Login View Design

Both login pages use the existing `<x-layouts::auth>` layout and Flux UI components for consistency with the rest of the app.

- **`resources/views/pages/auth/login-student.blade.php`**: Form with `login_id` (label: "Student ID"), `password`, hidden `login_type` = `student`. Wrapped in `guest` middleware (redirects already-authenticated users to `/dashboard`).
- **`resources/views/pages/auth/login-staff.blade.php`**: Form with `login_id` (label: "Staff ID"), `password`, hidden `login_type` = `staff`. Wrapped in `guest` middleware.

Both POST to `route('login.store')` (Fortify's `/login` POST route).

**`/login` GET handling:** Fortify owns `GET /login` (named `login`). In `FortifyServiceProvider::boot()`, set `Fortify::loginView(fn () => redirect()->route('login.student'))` so that any hit to `/login` (including Fortify's unauthenticated redirect) lands on the student login page.

### Dashboard View Design

`resources/views/dashboard.blade.php` uses `<x-layouts::app>` (sidebar layout). Content is rendered via `Route::view('dashboard', 'dashboard')` (no controller per frozen proposal.md:77). Role differentiation is handled by inline Blade conditional sections:

```blade
@if(auth()->user()->role === 'student')
    {{-- Student: read-only consolidated timetable placeholder --}}
    <section>
        <h2>My Timetable</h2>
        <p class="text-muted">Timetable content will appear here (future change).</p>
    </section>
@elseif(auth()->user()->role === 'lecturer')
    {{-- Lecturer: replacement request management placeholder --}}
    <section>
        <h2>Replacement Requests</h2>
        <p class="text-muted">Create and manage replacement requests here (future change).</p>
    </section>
    @if(auth()->user()->lecturer && auth()->user()->lecturer->is_pl)
        {{-- Programme Leader: FCFS approval queue placeholder --}}
        <section>
            <h2>FCFS Approval Queue</h2>
            <p class="text-muted">Pending approvals will appear here (future change).</p>
        </section>
    @endif
@endif
```

Content is placeholder (this change establishes auth + role infrastructure only; actual timetable/request/approval logic belongs to future changes). No Blade anonymous components (`<x-employee:*>`) are created in this change.

### Resolved Design Questions

1. **Student ID generation format** — Decided: `{2-digit year}{3-letter programme_code}{4-digit sequential padded}` (e.g., `25RSD0001`, `25DFT0001`). Year prefix `25` for academic year 2025.

2. **Fortify `username` config value** — Decided: set `'username' => 'login_id'` and `'lowercase_usernames' => false` in `config/fortify.php`. The login forms send `login_id` (not `email`); `lowercase_usernames` must be `false` to avoid lowercasing mixed-case student IDs against a case-sensitive PostgreSQL column. This also ensures the rate limiter keys per actual ID + IP.
