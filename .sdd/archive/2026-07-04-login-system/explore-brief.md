# Explore Brief: login-system

## Problem Statement

The class replacement system needs a complete login system that distinguishes between three user roles (Students, Lecturers, Programme Leaders) with role-based access control. The existing codebase has Laravel Fortify wired but uses email-based login with a single `users` table and no role distinction. The proposal (Form 2) defines three roles: Students (read-only), Lecturers (create/submit replacement requests), and Programme Leaders (all lecturer capabilities + FCFS approval queue).

## Rejected Approaches

| Approach | Why Rejected |
|---|---|
| Single unified login form (one page for all) | User prefers separate login pages for clarity |
| Email-based login | User wants Student ID / Staff ID only |
| IDs in parent `users` table (nullable columns) | User prefers properly normalized child tables with IDs in child tables only |
| Custom auth controller (remove Fortify) | Stripped-down Fortify retains rate limiting & session management without reimplementation |
| Registration / password reset / email verification / 2FA / Passkeys | Not needed for prototype; accounts are pre-seeded |
| Separate `faculties`/`departments` as plain varchar | User wants proper referential integrity with FK tables |
| `student_name` in students table | Redundant with `users.name` |
| 3 role values (`student`, `lecturer`, `programme_leader`) | Redundant with `is_pl` boolean; PL is a sub-type of lecturer |
| Remember Me | Deferred to future if time permits |
| `password_hash` column name | Laravel convention is `password`; Fortify expects `password` cast as `hashed` |
| `user_id` as PK on users table | Laravel convention is `id`; avoiding `$primaryKey` override |
| Separate `faculties`/`departments` tables skipped | User explicitly wants them for referential integrity |

## Final Solution

### Environment
- CachyOS KDE Plasma, PostgreSQL
- Laravel app with Fortify (to be stripped down to login + logout only)
- Livewire + Flux UI for frontend

### Database Schema (8 tables)

All tables follow Laravel naming conventions: primary key named `id`, foreign keys named `<table>_id`, timestamps `created_at`/`updated_at`.

**1. `faculties`**

| Column | Type | Constraints | Sample |
|---|---|---|---|
| id | int | PK, NOT NULL | 1 |
| faculty_code | varchar(20) | NOT NULL, UNIQUE | FOCS |
| faculty_name | varchar(255) | NOT NULL | Faculty of Computing and Information Technology |
| created_at | timestamp | NOT NULL | — |
| updated_at | timestamp | NOT NULL | — |

Seed data (3):
- FOCS — Faculty of Computing and Information Technology
- FAFB — Faculty of Accountancy, Finance and Business
- FSSH — Faculty of Social Science and Hospitality

**2. `departments`**

| Column | Type | Constraints | Sample |
|---|---|---|---|
| id | int | PK, NOT NULL | 1 |
| dept_code | varchar(20) | NOT NULL, UNIQUE | DCIT |
| dept_name | varchar(255) | NOT NULL | Department of Computing and Information Technology |
| faculty_id | int | FK → faculties, NOT NULL | 1 |
| created_at | timestamp | NOT NULL | — |
| updated_at | timestamp | NOT NULL | — |

Seed data (3):
- DCIT — Department of Computing and Information Technology (FOCS)
- DSSH — Department of Social Science and Hospitality (FSSH)
- DACB — Department of Accountancy and Business (FAFB)

**3. `programmes`**

| Column | Type | Constraints | Sample |
|---|---|---|---|
| id | int | PK, NOT NULL | 1 |
| programme_code | varchar(20) | NOT NULL, UNIQUE | RSD |
| programme_name | varchar(255) | NOT NULL | Bachelor in IT (Hons) Software Systems Development |
| faculty_id | int | FK → faculties, NOT NULL | 1 |
| created_at | timestamp | NOT NULL | — |
| updated_at | timestamp | NOT NULL | — |

Seed data (5):
- RSD — Bachelor in IT (Hons) Software Systems Development (FOCS)
- DFT — Diploma in Information Technology (FOCS)
- DSF — Diploma in Software Engineering (FOCS)
- RAF — Bachelor in Accountancy (FAFB)
- RBU — Bachelor in Business Administration (FAFB)

**4. `cohorts`**

| Column | Type | Constraints | Sample |
|---|---|---|---|
| id | int | PK, NOT NULL | 1 |
| programme_id | int | FK → programmes, NOT NULL | 1 |
| current_year | int | NOT NULL, CHECK(1-3) | 3 |
| semester | int | NOT NULL, CHECK(1-3) | 1 |
| tutorial_group | int | NOT NULL | 1 |
| academic_year | varchar(20) | NOT NULL | 2025/26 |
| intake | varchar(20) | NOT NULL | June 2025 |
| created_at | timestamp | NOT NULL | — |
| updated_at | timestamp | NOT NULL | — |

Seed data (14):
- FOCS: DFT1(S1), DFT2(S1), DSF1(S1), DSF2(S1), RSD1(S1)G1, RSD2(S1)G1, RSD2(S1)G2, RSD2(S1)G3, RSD3(S1)G1, RSD3(S1)G2, RSD3(S1)G3
- FAFB: RAF2(S3)G2, RAF2(S3)G4, RBU1(S1)G1

**5. `users` (modify existing migration)**

| Column | Type | Constraints | Sample |
|---|---|---|---|
| id | int | PK, NOT NULL | 1 |
| name | varchar(100) | NOT NULL | Foo Jing |
| email | varchar(100) | NOT NULL, UNIQUE, CHECK(domain `*@tarc.edu.my` OR `*@student.tarc.edu.my`) | foofjing@tarc.edu.my |
| password | varchar(255) | NOT NULL (auto-hashed via Laravel cast) | $2y$12$... |
| role | varchar(20) | NOT NULL, CHECK(`student` OR `lecturer`) | lecturer |
| email_verified_at | timestamp | nullable | — |
| remember_token | varchar(100) | nullable | — |
| two_factor_secret | text | nullable | — |
| two_factor_recovery_codes | text | nullable | — |
| two_factor_confirmed_at | timestamp | nullable | — |
| created_at | timestamp | NOT NULL | — |
| updated_at | timestamp | NOT NULL | — |

Note: `two_factor_*` columns retained from existing migration but unused (2FA disabled). `password` column auto-hashes value via Laravel's `hashed` cast on the User model.

**6. `students`**

| Column | Type | Constraints | Sample |
|---|---|---|---|
| user_id | int | PK, FK → users, NOT NULL | 2 |
| student_id | varchar(30) | NOT NULL, UNIQUE | 25SMR10186 |
| cohort_id | int | FK → cohorts, NOT NULL | 1 |
| created_at | timestamp | NOT NULL | — |
| updated_at | timestamp | NOT NULL | — |

1:1 relationship with `users` (user_id is both PK and FK).

**7. `lecturers`**

| Column | Type | Constraints | Sample |
|---|---|---|---|
| user_id | int | PK, FK → users, NOT NULL | 1 |
| staff_id | varchar(30) | NOT NULL, UNIQUE | 5425 |
| dept_id | int | FK → departments, NOT NULL | 1 |
| is_pl | boolean | NOT NULL, default false | true |
| created_at | timestamp | NOT NULL | — |
| updated_at | timestamp | NOT NULL | — |

1:1 relationship with `users` (user_id is both PK and FK).

### Relationship Hierarchy

```
faculties
  ├── departments (FK: faculty_id)
  └── programmes (FK: faculty_id)

departments
  └── lecturers (FK: dept_id)

programmes
  └── cohorts (FK: programme_id)

cohorts
  └── students (FK: cohort_id)

users (parent, authenticatable)
  ├── students (1:1, user_id PK+FK)
  └── lecturers (1:1, user_id PK+FK)
```

### Auth Configuration

**Fortify strip-down — `config/fortify.php`:**
- Features: NONE (remove `registration`, `resetPasswords`, `emailVerification`, `twoFactorAuthentication`, `passkeys`)
- Username field: custom (not `email` — handled via `authenticateUsing()` callback)
- Views: only `login` registered (but two custom views: student and staff)
- Rate limiters: `login` (5/min per IP+ID)

**Auth flow — 2 separate login pages:**

| Route | Method | Purpose |
|---|---|---|
| `/login/student` | GET | Student login form (student_id + password) |
| `/login/staff` | GET | Staff login form (staff_id + password) |
| `/login` | POST | Unified login submission (Fortify) |
| `/logout` | POST | Logout (Fortify) |

**`Fortify::authenticateUsing()` callback logic:**
1. Determine login type from request (hidden field or route parameter indicating student vs staff)
2. If student: query `students` table by `student_id` → get `user_id` → look up `users` table → verify password
3. If staff: query `lecturers` table by `staff_id` → get `user_id` → look up `users` table → verify password
4. Return User model on success, null on failure

**Role middleware (`role:lecturer,programme_leader`):**
- Custom middleware accepting comma-separated role parameters
- Checks `users.role` column
- For PL-specific routes: checks `is_pl` boolean on `lecturers` table
- Applied to route groups for protected pages

### Redirect Logic

| Role | Redirect after login | Content seen |
|---|---|---|
| Student | `/dashboard` | Read-only consolidated timetable |
| Lecturer | `/dashboard` | Replacement request management |
| Programme Leader | `/dashboard` | Replacement requests + FCFS approval queue |

**Logout redirect:** `/` (welcome/landing page)

Single `/dashboard` route with role-based conditional rendering in Blade (`@if`, `@can`). Shared layout: `layouts/app.blade.php` + `layouts/app/sidebar.blade.php`. Navigation menu items and action buttons conditionally rendered based on role.

### Seed Data

**Hardcoded in `DatabaseSeeder.php`:**

1. Faculties (3): FOCS, FAFB, FSSH
2. Departments (3): DCIT (FOCS), DSSH (FSSH), DACB (FAFB)
3. Programmes (5): RSD, DFT, DSF (FOCS); RAF, RBU (FAFB)
4. Cohorts (14): per `dataset/cohorts.md` (to be created)
5. Lecturers (14): per `dataset/lecturers.md` (exists)
   - 11 DCIT (incl. 2 PLs: Surayaini #5425, Mohd Nur Rahmat #5516)
   - 1 DSSH (Muada Bin Ojih #3799)
   - 2 DACB (Tan Sharon #4363, Dr. Chang Foo Chung #5254)
6. Students (~153-198 mock): generated within ranges per cohort
   - DFT1(S1), DFT2(S1), DSF1(S1), DSF2(S1): 22-25 each (random)
   - RSD1(S1)G1: 1
   - RSD2(S1)G1, RSD2(S1)G2, RSD2(S1)G3: 5-10 each (random)
   - RSD3(S1)G1: 6
   - RSD3(S1)G2: 7
   - RSD3(S1)G3: 9
   - RAF2(S3)G2, RAF2(S3)G4, RBU1(S1)G1: 9-15 each (random)

**Default password for all seeded users:** `Tarumt@2026`

### Datasets

- `dataset/lecturers.md` (exists) — 14 lecturers with Staff ID, Name, Email, Role (academic title), Department
- `dataset/cohorts.md` (to be created) — 14 cohorts with full details

## Key Cross-Module Data Flows

1. **Login (Student):** `/login/student` → POST `/login` → `Fortify::authenticateUsing()` → query `students` by `student_id` → get `user_id` → query `users` by `id` → verify password → authenticate → redirect `/dashboard`

2. **Login (Staff):** `/login/staff` → POST `/login` → `Fortify::authenticateUsing()` → query `lecturers` by `staff_id` → get `user_id` → query `users` by `id` → verify password → authenticate → redirect `/dashboard`

3. **Role check (middleware):** Request → `role:lecturer` middleware → check `Auth::user()->role` is `lecturer` → if PL-specific, check `Auth::user()->lecturer->is_pl`

4. **Dashboard render:** `/dashboard` → check `Auth::user()->role` + `is_pl` → render role-appropriate sections (timetable view for students, request management for lecturers, FCFS queue for PLs)

## Known Open Questions / Unresolved Issues

None — all questions resolved during explore grilling. The following were resolved:
- Student mock data distribution: defined with per-cohort ranges
- Column naming: `password` (Laravel convention, auto-hashed) and `id` (Laravel convention)
- Faculty/department tables: included per user request
- All role/permission decisions confirmed
