# Tasks: login-system

**Status:** proposal.md and design.md are frozen.

> Each task is ≤ 2 hours. Dependencies noted in brackets.

## Database Layer

- [ ] **T1 — Modify users migration + create faculties/departments/programmes migrations** [no deps]
  - Edit `database/migrations/0001_01_01_000000_create_users_table.php`: add `role` column (`varchar(20)`, NOT NULL, CHECK `student` or `lecturer`). Note: email-domain CHECK constraint deferred per design.md (optional, beyond frozen proposal scope)
  - Create `database/migrations/0001_01_01_000003_create_faculties_table.php`: `id`, `faculty_code` (UNIQUE), `faculty_name`, timestamps
  - Create `database/migrations/0001_01_01_000004_create_departments_table.php`: `id`, `dept_code` (UNIQUE), `dept_name`, `faculty_id` (FK → faculties), timestamps
  - Create `database/migrations/0001_01_01_000005_create_programmes_table.php`: `id`, `programme_code` (UNIQUE), `programme_name`, `faculty_id` (FK → faculties), timestamps
  - Run `php artisan migrate:fresh` to verify

- [ ] **T2 — Create cohorts/students/lecturers migrations with FK constraints** [depends on T1]
  - Create `database/migrations/0001_01_01_000006_create_cohorts_table.php`: `id`, `programme_id` (FK → programmes), `current_year` (CHECK 1-3), `semester` (CHECK 1-3), `tutorial_group`, `academic_year`, `intake`, timestamps
  - Create `database/migrations/0001_01_01_000007_create_students_table.php`: `user_id` (PK+FK → users), `student_id` (UNIQUE), `cohort_id` (FK → cohorts), timestamps
  - Create `database/migrations/0001_01_01_000008_create_lecturers_table.php`: `user_id` (PK+FK → users), `staff_id` (UNIQUE), `dept_id` (FK → departments), `is_pl` (boolean, default false), timestamps
  - Verify migration ordering: users → faculties → departments → programmes → cohorts → students/lecturers
  - Run `php artisan migrate:fresh` to verify all 8 tables exist with correct FK constraints

## Factory & Dataset (before seeder — seeder depends on these)

- [ ] **T3 — Update UserFactory + create dataset/cohorts.md** [no deps]
  - `database/factories/UserFactory.php`: add `'role' => 'lecturer'` to `definition()` (default)
  - Add `student()` state method: `role => 'student'`, email generator producing `{student_id}@student.tarc.edu.my` (per frozen design.md)
  - Add `lecturer()` state method: `role => 'lecturer'`, email generator producing `{name}@tarc.edu.my`
  - Create `dataset/cohorts.md` documenting all 14 cohorts with their programme, year, semester, tutorial group, academic year, intake
  - Verify `User::factory()->create()` works without constraint violations

## Eloquent Models

- [ ] **T4 — Create Eloquent models (Faculty, Department, Programme, Cohort)** [depends on T1, T2]
  - `app/Models/Faculty.php`: `$fillable`, `hasMany Departments`, `hasMany Programmes`
  - `app/Models/Department.php`: `$fillable`, `belongsTo Faculty`, `hasMany Lecturers`
  - `app/Models/Programme.php`: `$fillable`, `belongsTo Faculty`, `hasMany Cohorts`
  - `app/Models/Cohort.php`: `$fillable`, `belongsTo Programme`, `hasMany Students`
  - Verify `php artisan model:show` or Tinker for each

- [ ] **T5 — Create Student and Lecturer models** [depends on T1, T2]
  - `app/Models/Student.php`: `$primaryKey = 'user_id'`, `$incrementing = false`, `$fillable`, `belongsTo User`, `belongsTo Cohort`
  - `app/Models/Lecturer.php`: `$primaryKey = 'user_id'`, `$incrementing = false`, `$fillable`, `belongsTo User`, `belongsTo Department`
  - Verify via Tinker

- [ ] **T6 — Modify User model** [depends on T4, T5]
  - Add `role` to `#[Fillable(...)]` attribute
  - Add `student()` hasOne relationship: `$this->hasOne(Student::class)`
  - Add `lecturer()` hasOne relationship: `$this->hasOne(Lecturer::class)`
  - Remove `implements PasskeyUser` from class declaration
  - Remove `use PasskeyAuthenticatable` trait
  - Remove `use TwoFactorAuthenticatable` trait
  - Verify `$user->student` and `$user->lecturer` return expected models via Tinker

## Auth Configuration

- [ ] **T7 — Strip Fortify config + update FortifyServiceProvider** [depends on T5]
  - `config/fortify.php`: set `'features' => []` (empty array)
  - `config/fortify.php`: set `'username' => 'login_id'`
  - `config/fortify.php`: set `'lowercase_usernames' => false`
  - `config/fortify.php`: set `'home' => '/dashboard'` (verify default)
  - `app/Providers/FortifyServiceProvider.php`: remove `createUsersUsing` call
  - `app/Providers/FortifyServiceProvider.php`: remove `resetUserPasswordsUsing` call
  - `app/Providers/FortifyServiceProvider.php`: remove registration/reset/2FA/verify-email view registrations; keep only `loginView` → register both `pages::auth.login-student` and `pages::auth.login-staff`
  - `app/Providers/FortifyServiceProvider.php`: set `Fortify::loginView(fn () => redirect()->route('login.student'))`
  - `app/Providers/FortifyServiceProvider.php`: implement `Fortify::authenticateUsing()` callback with `login_type` validation (`['student', 'staff']`), child-table lookup, and password verification
  - `app/Providers/FortifyServiceProvider.php`: keep login rate limiter (`configureRateLimiting`)
  - Verify `Fortify::authenticateUsing()` callback compiles and does not throw (Tinker test; full browser login verified in T14)

## Middleware

- [ ] **T8 — Create CheckRole and CheckPl middleware** [no deps]
  - `app/Http/Middleware/CheckRole.php`: accept comma-separated role parameter(s), check `Auth::user()->role`, `abort(403)` on mismatch, return `$next($request)` on match
  - `app/Http/Middleware/CheckPl.php`: assert `Auth::user()->role === 'lecturer'`, check `Auth::user()->lecturer->is_pl`, `abort(403)` if not PL, return `$next($request)` on match
  - `bootstrap/app.php`: register `role` alias → `CheckRole::class`
  - `bootstrap/app.php`: register `pl` alias → `CheckPl::class`
  - Verify with `php artisan route:list` and middleware unit tests (T14)

## Routes

- [ ] **T9 — Update routes/web.php** [no deps]
  - Add `Route::get('/login/student', ...)->middleware('guest')->name('login.student')` → renders `pages::auth.login-student`
  - Add `Route::get('/login/staff', ...)->middleware('guest')->name('login.staff')` → renders `pages::auth.login-staff`
  - Modify existing `/dashboard` route: replace `middleware(['auth', 'verified'])` with `middleware(['auth'])` only (remove `verified`)
  - Replace the `Route::get('/', closure)` definition with `Route::view('/', 'welcome')->name('home')`
  - Remove `require __DIR__.'/settings.php'` line
  - Remove dummy routes: `Route::view('/login-dummy', 'login-dummy')` and the duplicate `Route::view('/replacement-arrangement', ...)`. Note: the corresponding view files (`login-dummy.blade.php`, `replacement-arrangement.blade.php`) become orphaned dead code; they are intentionally left on disk to avoid accidental deletion — they do not affect the running application.
  - Verify: do NOT redefine POST `/logout` (Fortify already registers it)
  - Run `php artisan route:list` to verify all routes

## Views

- [ ] **T10 — Create student and staff login views** [no deps]
  - `resources/views/pages/auth/login-student.blade.php`: `<x-layouts::auth>` layout, Flux UI form with `login_id` (label: "Student ID"), `password`, hidden `login_type=student`, POST to `route('login')`, `@error` handling for `login_id` and `password`
  - `resources/views/pages/auth/login-staff.blade.php`: `<x-layouts::auth>` layout, Flux UI form with `login_id` (label: "Staff ID"), `password`, hidden `login_type=staff`, POST to `route('login')`, `@error` handling
  - Both forms wrapped in `guest` middleware (handled at route level, not view)
  - Verify forms render correctly

- [ ] **T11 — Modify dashboard.blade.php and welcome.blade.php** [no deps]
  - `resources/views/dashboard.blade.php`: add inline Blade conditional sections:
    - `@if(auth()->user()->role === 'student')` → read-only timetable placeholder
    - `@elseif(auth()->user()->role === 'lecturer')` → replacement request management placeholder
    - `@if(auth()->user()->lecturer && auth()->user()->lecturer->is_pl)` → PL FCFS queue placeholder
  - `resources/views/welcome.blade.php`: replace registration/login links with links to `route('login.student')` and `route('login.staff')` (guarded with `@guest`)
  - Verify `/dashboard` renders role-appropriate content per authenticated user

## Seeder

- [ ] **T12 — DatabaseSeeder: seed reference data** [depends on T3 (dataset/cohorts.md), T4]
  - `database/seeders/DatabaseSeeder.php`: seed 3 faculties (FOCS, FAFB, FSSH)
  - Seed 3 departments (DCIT→FOCS, DSSH→FSSH, DACB→FAFB)
  - Seed 5 programmes (RSD/DFT/DSF→FOCS, RAF/RBU→FAFB)
  - Seed 14 cohorts per `dataset/cohorts.md` (created in T3)
  - Run `php artisan migrate:fresh --seed` and verify reference tables populated

- [ ] **T13 — DatabaseSeeder: seed users** [depends on T12]
  - Seed 14 lecturers from `dataset/lecturers.md`:
    - Create `User` for each: name, email, password=Hash::make('Tarumt@2026'), role='lecturer', email_verified_at=now()
    - Create `Lecturer` for each: staff_id, dept_id (map by department), is_pl (true for Surayaini #5425 and Mohd Nur Rahmat #5516)
  - Seed ~153-198 mock students per cohort ranges:
    - Student ID format: `{25}{3-letter prog code}{4-digit seq padded}` (e.g., `25RSD0001`)
    - Per cohort: `random_int(min, max)` students with sequential IDs
    - Create `User` for each: name=fake, email=`{student_id}@student.tarc.edu.my`, password=Hash::make('Tarumt@2026'), role='student', email_verified_at=now()
    - Create `Student` for each: student_id, cohort_id
  - Run `php artisan db:seed` and verify login works with seeded credentials

## Testing

- [ ] **T14 — Clean up test suite** [depends on T3 (factory), T7, T9, T10]
  - Delete: `tests/Feature/Auth/PasswordResetTest.php`, `RegistrationTest.php`, `EmailVerificationTest.php`, `TwoFactorChallengeTest.php`, `PasswordConfirmationTest.php`
  - Delete: `tests/Feature/Settings/SecurityTest.php`, `ProfileUpdateTest.php`
  - Refactor `tests/Feature/Auth/AuthenticationTest.php`: replace email-based login with `login_id` + `login_type=lecturer` or `login_type=student` assertions; test both student and staff login flows; delete the `test_users_with_two_factor_enabled_are_redirected_to_two_factor_challenge` method (2FA disabled); update `test_login_screen_can_be_rendered` to assert redirect to `route('login.student')` (or target `/login/student` directly instead of `route('login')` GET)
  - Refactor `tests/Feature/DashboardTest.php`: assert unauthenticated users redirect to `/login` → `/login/student`; assert authenticated users see `/dashboard`
  - Verify `tests/Feature/ExampleTest.php` still passes (it only calls `route('home')`)
  - Run `php artisan test` and verify all remaining tests pass

- [ ] **T15 — Add middleware unit tests** [depends on T8]
  - Create `tests/Feature/Middleware/CheckRoleTest.php`:
    - Test `role:lecturer` blocks student (403)
    - Test `role:lecturer` passes lecturer
    - Test `role:student` blocks lecturer (403)
    - Test `role:student` passes student
  - Create `tests/Feature/Middleware/CheckPlTest.php`:
    - Test `pl` blocks non-PL lecturer (403)
    - Test `pl` passes PL (is_pl = true)
    - Test `pl` blocks student (403)
  - Use test routes with middleware applied
  - Run `php artisan test` and verify all pass
