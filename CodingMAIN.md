# TARUMT Class Replacement System — Main Project File

> **Purpose:** Single source of truth for this project. Read this first before any coding session.
> Referenced by: FYP proposal (`../final/25SMR10186_Form 2_Proposal (v2025-11).docx.pdf`), Chapter 1 (`../final/Ch1.pdf`), Chapter 3 FR & NFR (`../final/FR&NFR.md`), Full Functional Specifications V2 (`../TARUMT Class Replacement System - Full Functional Specifications V2.pdf`), and the live codebase in this directory.

---

## 1. Project Overview

A web application that replaces the manual Google-Sheets-based class replacement workflow at TAR UMT Sabah (FOCS faculty). It automates discovery of **conflict-free replacement windows** via a **Multi-Entity Matrix Intersection Engine**, prevents double-booking via **Optimistic Concurrency Control (OCC)**, and routes requests through a **First-Come-First-Served (FCFS) approval dashboard** for Programme Leaders.

- **Student:** Poong Foo Jing (25SMR10186) — Bachelor in IT (Hons) Software Systems Development, RSD3G2
- **Supervisor:** Mr. Lim Jia Zheng | **Moderator:** Ms. Teng Nga Sing
- **Client:** Faculty of Computing and Information Technology (FOCS), TAR UMT Sabah
- **Development model:** Agile (iterative) — FYP1 (14 weeks, design) + FYP2 (7 weeks, 3 sprints)
- **SDG alignment:** SDG 4 (Quality Education), SDG 8 (Decent Work & Economic Growth)

---

## 2. Problem Domain (5 Pain Points)

1. **Fragmented process & human error** — each lecturer keeps a personal schedule spreadsheet; no single source of truth; manual cross-referencing (Babaei et al., 2015).
2. **Blind spots in multi-cohort scheduling** — modules shared across cohorts (e.g. RSD3G1 + RSD2G2 + RSD2G3) require manually overlaying separate timetables.
3. **Venue availability tracking** — room occupancy lives in a static PDF, separate from scheduling; no capacity-aware filtering; room priority rules (Lab-only for P sessions, B006 for Networking) unenforced.
4. **Data race conditions** — Google Sheets has no transaction isolation → simultaneous submissions cause double-booking with no rollback (Kung & Robinson, 1981).
5. **Elevated PL administrative burden** — PL re-verifies every request from scratch (schedules + cohorts + venue) before writing "Y" in a cell; no audit trail.

---

## 3. Solution Architecture (5 Objectives)

| # | Objective | Core Idea | Status |
|---|-----------|-----------|--------|
| 1 | **Multi-Entity Matrix Intersection Engine** | 4-vector deterministic set intersection: lecturer availability × cohort free schedules (1+ cohorts) × room occupancy × capacity filter. Returns color-coded weekly grid. | 🔲 Not built |
| 2 | **Optimistic Concurrency Control (OCC) layer** | Millisecond-precision transactional validation at submission time; abort + rollback + UI alert on conflict. ACID transactions. | 🔲 Not built |
| 3 | **FCFS Digital Approval Dashboard** | Chronological queue; one-click approve (→ Occupied) or reject with mandatory reason (→ Available); timestamped audit trail. | 🔲 Not built (UI template planned) |
| 4 | **Role-Based Access Control (RBAC) + notifications** | 3 tiers: Student (view-only), Lecturer (create/submit/edit/cancel own), PL (hybrid = admin + lecturer rights). Email notifications: PL on submission, proposer on outcome. | 🟡 Partially built (auth + roles) |
| 5 | **Prototype Deployment** | Laravel + PostgreSQL, seeded with 14 cohorts, 14 staff, 23 rooms. | 🟡 Partially built (seeders) |

### Slot State Machine (Objective 1–3 core)

| State | Meaning |
|-------|---------|
| `available` | Free — satisfies lecturer × cohort(s) × room × capacity |
| `pending` | Drafted by current user, awaiting PL approval ("Pending (Self)") |
| `reserved` | Claimed by another lecturer's submission (FCFS priority) — "Reserved by Another Faculty" |
| `occupied` | PL approved (locked permanently) or blocked by master timetable |

### Visual grid colors (Module 2)
- **Green** — satisfies all 4 constraints, clickable
- **Red** — occupied by existing class
- **Yellow** — pending request submitted by you
- **Grey** — reserved by another lecturer
- **Blue** — current selection

### Venue types & rules
- **Tutorial Rooms** (cap ≤ 35): B100–B109, B014–B018, B002 — L/T sessions
- **Lecture Halls** (cap > 35): B110, B111 — large multi-cohort assemblies
- **Computer Labs** (cap 28): B005, B009–B011 — Practical-only 'P' sessions
- **Cisco Specialized Lab** (cap 32): B006 — priority for Networking/IoT workloads
- **Excluded:** Block C venues (operational boundary for prototype)
- **MPU-3133 venue rule (user decision 2026-08-01):** may use **tutorial rooms OR lecture halls — labs excluded only**. ⚠ Ch1.pdf/specs V2 say "Lecture-only → B110/B111 only"; the user confirmed the FR&NFR behavior is correct ("Tutorial-only" is a mislabel — treat as "no labs allowed").

### Edge cases the engine must handle
- No common slot found → return empty state, never crash
- Single-cohort request → 3 vectors (lecturer × cohort × room)
- All-day occupancy → correct empty result
- Session-type venue restrictions (e.g. MPU-3133 → exclude labs; a module marked P must use a lab; L-only modules must not use labs)
- **Duration-aware windows (user decision 2026-08-01):** engine only returns windows ≥ the original class duration; grid cells are 1h and clicking a green cell selects the whole contiguous block (a 2h class needs 2 adjacent green cells)

### Replacement flow decisions (user-confirmed 2026-08-01)
1. **Student counts:** fixed per cohort (see §8) — capacity-filter demos are reproducible.
2. **Timetable data:** will be **synthesized** (no real sheets provided) — I generate a realistic 14-week dataset for 14 cohorts / 14 staff / 23 rooms, aligned with the MPU-3133 & MPU-3232 use cases; user reviews it.
3. **Original block release:** after PL approval, the original class block is marked replaced/cancelled and its old time+room become **bookable by others**; the new slot becomes `occupied`.
4. **PL Master Configuration Panel: DROPPED** — not in FR&NFR; specs V2 mention is superseded by FR&NFR as source of truth.
5. **Notifications:** implement exactly FR 1.5 (students on timetable updates), 4.15 (PL on submission), 4.16 (proposer on outcome). No cross-lecturer alerts unless requested later.
6. **MPU-3133 venue rule:** tutorial rooms + lecture halls OK, labs excluded (see §3 venue rules).

---

## 4. Tech Stack & Dev Tools

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 13 (PHP ^8.3) — `laravel/livewire-starter-kit` |
| Live components | Livewire 4 + Flux 2 + Blaze |
| Frontend | Blade templates, TailwindCSS (theme.css for custom), vanilla JS (`ui-common.js`) |
| Database | PostgreSQL (`class_replacement` DB, user `philler`, no password) |
| Auth | Laravel Fortify + passkeys (WebAuthn) |
| Tooling | Pint (lint), PHPStan/Larastan (types), PHPUnit 12 (tests), Laravel Pail (logs), Sail (Docker) |

### Commands
```bash
composer run dev              # laravel dev server
php artisan serve             # http://localhost:8000
php artisan migrate:fresh --seed   # reset + seed
php artisan migrate --seed
composer run lint             # pint --parallel (fix)
composer run lint:check       # pint --test
composer run types:check      # phpstan analyse
composer run test             # lint:check + types:check + phpunit
npm run dev / npm run build   # vite (not heavily used; UI uses static /css /js)
```

### Test login
- Default password for ALL seeded users: `Tarumt@2026`
- Lecturers: login by email (`surayaini@tarc.edu.my`, etc.) via `/login/staff`
- Students: `{yy}{PROGCODE}{seq}@student.tarc.edu.my` (e.g. `25RSD0001@student.tarc.edu.my`) via `/login/student`
- PLs: Pn. Surayaini Binti Basri (5425), En. Mohd Nur Rahmat Bin Mohd Taat (5516) — `is_pl = true`

---

## 5. Domain Model & Database Schema

### Existing tables (migrations in `database/migrations/`)
| Table | Key fields | Notes |
|-------|-----------|-------|
| `users` | id, name, email, password, **role** (`student`/`lecturer` only — PL is `lecturer` + `is_pl=true`, no admin role), two-factor, passkeys | Laravel default + role column |
| `faculties` | faculty_code (unique), faculty_name | FOCS, FAFB |
| `departments` | dept_code (unique), dept_name, faculty_id | DCIT, DSSH, DACB |
| `programmes` | programme_code (unique), programme_name, faculty_id | DFT, DSF, RSD, RAF, RBU |
| `cohorts` | programme_id, current_year (1–3), semester (1–3), tutorial_group, academic_year, intake | CHECK constraints on year/semester |
| `students` | **user_id (PK)**, student_id (unique), cohort_id | student_id format `{yy}{PROG}{seq}` e.g. 25RSD0001 |
| `lecturers` | **user_id (PK)**, staff_id (unique), dept_id, is_pl (bool) | |
| `passkeys` | user_id, credential_id, ... | WebAuthn |

### Models (`app/Models/`)
- `User` — role-based helpers: `loginId()`, `isStudent()`, `isLecturer()`, `initials()`; `HasOne` student/lecturer
- `Faculty` (HasMany departments, programmes) → `Department` (HasMany lecturers) → `Lecturer` (BelongsTo user, department)
- `Programme` (HasMany cohorts) → `Cohort` (HasMany students) → `Student` (BelongsTo cohort, user)
- `Lecturer`/`Student` use `user_id` as primary key, `incrementing = false`

### Pending schema (needed for Objectives 1–3)
Not yet migrated — design during Sprint 1 (canonical names per FR 4.9 / NFR 5.3):
- `venues` (room code, capacity, room type Tutorial/Lecture Hall/Lab/Cisco Lab, allowed session types L/T/P)
- `timetable_blocks` / `class_sessions` (lecturer, cohort(s), module, day, time, duration, venue, session type)
- `modules` (code, name, session type constraints)
- **`time_slots`** (FR 4.9 canon): slot record with **integer `version` column (OCC pattern)** + `status` column driving the state machine (`available` / `pending` / `reserved` / `occupied`)
- `replacement_requests` (time_slot_id, proposer, PL, timestamps, rejection reason)
- `audit_logs` (PL identity, timestamp, action, slot ID, rejection reason, **OCC validation outcomes per FR 4.12**)

---

## 6. User Roles & Permissions Matrix

| Capability | Student | Lecturer | Programme Leader |
|-----------|:-------:|:--------:|:----------------:|
| Login / Logout | ✅ | ✅ | ✅ |
| View consolidated master timetable | ✅ | ✅ | ✅ |
| View cohort timetables | ✅ | ✅ | ✅ |
| View global replacement history ledger | ✅ | ✅ | ✅ |
| Create / draft / submit replacement request | ❌ | ✅ | ✅ (hybrid inheritance) |
| Edit / cancel own pending request | ❌ | ✅ | ✅ |
| View other lecturers' requests | ❌ | ❌ | ✅ |
| Approve / reject (FCFS queue) | ❌ | ❌ | ✅ |
| Audit trail access | ❌ | ❌ | ✅ |
| Master seed-data configuration | ❌ | ❌ | ❌ *(dropped 2026-08-01 — not in FR&NFR)* |

- Middleware: `CheckRole`, `CheckPl` in `app/Http/Middleware/`
- Route gating + authorization gates (Laravel native) required on all dashboard routes
- **Students are strictly view-only**

---

## 7. Requirements (FR & NFR)

Source: `../final/FR&NFR.md` — Chapter 3, §3.4 (verbatim). Each FR is traceable to one or more of the 5 objectives (§3). Login already supports Student/Staff ID via Fortify `username = login_id` (FR 1.1, 2.1 ✓).

### 7.1 Traceability map (FR → Objective)

| FR # | Requirement summary | Objective |
|------|--------------------|-----------|
| 1.1–1.5 | Student login by ID; view full timetable; view request status; view-only; email on timetable updates | 4, 5 |
| 2.1–2.12 | Lecturer login by staff ID; own timetable; select class block; venue dropdown (default original); real-time recalc; color-coded grid; click green slot; submit; edit/cancel own pending; cannot see/edit others'; cannot approve/reject; email on outcome | 1, 2, 4 |
| 3.1–3.7 | PL inherits all lecturer rights; time-based (FCFS) queue; queue shows proposer/subject/cohorts/time/venue; pre-computed slot validity; 1-click approve; mandatory reject reason; full audit trail | 3, 4 |
| 4.1–4.2 | Seed 14 lecturers / 14 cohorts / 23 Block B rooms; room metadata (name, capacity, type, allowed session L/T/P) | 5, 1 |
| 4.3–4.8 | 4-way set intersection; ≤500ms; "No available slots" message; 3-vector for single cohort; empty result when fully occupied; session-type venue filtering | 1 |
| 4.9–4.12 | OCC via integer version column on `time_slots`; exactly one concurrent submission wins + conflict alert; 4-state machine; OCC outcomes logged | 2 |
| 4.13–4.17 | RBAC 3 roles; unauthenticated → login redirect; email to PL on submission; email to proposer on outcome; **emails via database-backed queue** | 4, 5 |

### 7.2 Functional Requirements (verbatim, FR&NFR.md §3.4.1)

| No. | Requirement |
|-----|-------------|
| 1.0 | **Student** |
| 1.1 | Students shall be able to log in using Student ID and password. |
| 1.2 | Students shall be able to view the full timetable. |
| 1.3 | Students shall be able to view the status of replacement requests (pending / approved / rejected). |
| 1.4 | Students shall not be able to create, edit, or delete any replacement requests or timetable data. |
| 1.5 | Students shall receive email notification for timetable updates. |
| 2.0 | **Lecturer** |
| 2.1 | Lecturers shall be able to log in using Staff ID and password. |
| 2.2 | Lecturers shall be able to view their own teaching timetable. |
| 2.3 | Lecturers shall be able to select a class block that needs replacement. |
| 2.4 | Lecturers shall be able to choose a replacement venue from a dropdown menu, defaulted to the original venue. |
| 2.5 | Lecturers shall be able to change the venue dropdown, which shall recalculate available slots in real-time. |
| 2.6 | Lecturers shall be able to view a weekly timetable grid (time x day) with colour-coded cells showing slot availability. |
| 2.7 | Lecturers shall be able to click a green slot to select it as the proposed replacement. |
| 2.8 | Lecturers shall be able to submit the replacement request for Programme Leader approval. |
| 2.9 | Lecturers shall be able to edit or cancel their own pending requests before the Programme Leader acts on them. |
| 2.10 | Lecturers shall not be able to view or edit other lecturers' requests. |
| 2.11 | Lecturers shall not be able to approve or reject any request. |
| 2.12 | Lecturers shall receive an automated email when their submitted request is approved or rejected. |
| 3.0 | **Programme Leader** |
| 3.1 | Programme Leaders shall inherit all Lecturer privileges (2.1-2.12). |
| 3.2 | Programme Leaders shall be able to view a time-based queue of all pending replacement requests. |
| 3.3 | Each request in the queue shall display the proposer name, subject, affected cohort(s), proposed time, and proposed venue. |
| 3.4 | Each request shall display pre-computed slot validity. |
| 3.5 | Programme Leaders shall be able to approve a request with one click. |
| 3.6 | Programme Leaders shall be able to reject a request by providing a mandatory reason. |
| 3.7 | Programme Leaders shall have every approval and rejection action automatically recorded in the audit trail, including Programme Leader identity, timestamp, action taken, slot ID, and rejection reason (if any). |
| 4.0 | **System** |
| 4.1 | The system shall store timetable data for 14 lecturers, 14 cohorts, and 23 Block B rooms as seed data. |
| 4.2 | The system shall store each room's details including room name, capacity, room type (Tutorial / Lecture Hall / Lab / Cisco Lab), and allowed session type (L / T / P). |
| 4.3 | The system shall compute a four-way set intersection of lecturer availability, cohort free schedules (supporting multiple cohorts), room vacancy, and capacity-aware venue filtering. |
| 4.4 | The system shall return results within 500 milliseconds. |
| 4.5 | The system shall display a "No available slots" message when no common slot is found. |
| 4.6 | The system shall compute intersection with three vectors (lecturer x one cohort x room) for single-cohort requests. |
| 4.7 | The system shall return an empty result set when a lecturer is fully occupied for the entire day. |
| 4.8 | For modules with session-type restrictions (e.g., MPU-3133 Tutorial-only), the system shall filter venues by excluding Labs only. *(User decision: MPU-3133 → tutorial rooms + lecture halls OK, labs excluded.)* |
| 4.9 | The system shall implement Optimistic Concurrency Control using an integer version column pattern on the time_slots table. |
| 4.10 | The system shall allow exactly one submission to succeed when two lecturers submit a request for the same slot at the same time, and return a conflict alert to the other. |
| 4.11 | The system shall manage the slot state machine transitioning a slot's status through Available, Pending (Self), Reserved (Other), and Occupied. |
| 4.12 | The system shall log all Optimistic Concurrency Control validation outcomes in the audit trail. |
| 4.13 | The system shall enforce Role-Based Access Control with three roles: Student (view-only), Lecturer (create/submit/edit own), and Programme Leader (hybrid). |
| 4.14 | The system shall redirect unauthenticated users to the login page. |
| 4.15 | The system shall send an automated email notification to the Programme Leader when a new replacement request is submitted. |
| 4.16 | The system shall send an automated email notification to the proposer when their request is approved or rejected. |
| 4.17 | The system shall send all email notifications in the background through a database-backed queue. |

### 7.3 Non-Functional Requirements (verbatim, FR&NFR.md §3.4.2)

| No. | Requirement |
|-----|-------------|
| 1.0 | **Performance** |
| 1.1 | The system shall complete the matrix intersection calculation within 500 milliseconds from user input to grid display. |
| 1.2 | The system shall complete Optimistic Concurrency Control (OCC) validation at database-write speed with no noticeable delay to the user. |
| 1.3 | The system shall load all dashboard pages within 2 seconds under normal load. |
| 1.4 | The system shall process queued email jobs within 1 minute of the queue worker starting. |
| 2.0 | **Security** |
| 2.1 | The system shall enforce role-based permission checks on all dashboard routes using Laravel middleware. |
| 2.2 | The system shall hash all passwords using Bcrypt and never store passwords in plain text. |
| 2.3 | The system shall use Laravel's Eloquent ORM for all database queries to prevent SQL injection. |
| 2.4 | The system shall terminate inactive sessions after 30 minutes. |
| 2.5 | The system shall apply CSRF token verification on all POST, PUT, and DELETE form submissions. |
| 3.0 | **Usability** |
| 3.1 | The system shall provide a responsive web interface that works on desktop, tablet, and mobile screen sizes. |
| 3.2 | The system shall display the slot availability grid with colour-coded cells labelled with their meaning for accessibility. |
| 3.3 | The system shall display error messages in simple English. |
| 3.4 | The system shall redirect each user to their role-appropriate dashboard immediately after login. |
| 4.0 | **Reliability** |
| 4.1 | The system shall guarantee that no double-booking occurs under any concurrent submission scenario. |
| 4.2 | The system shall maintain at least 99% uptime. |
| 4.3 | The system shall support database backup via PostgreSQL pg_dump. |
| 5.0 | **Maintainability** |
| 5.1 | The system shall follow the PHP Standard Recommendation 12 (PSR-12) coding standard, verifiable through Laravel Pint. |
| 5.2 | The system shall track all database schema changes through Laravel migration files in database/migrations/. |
| 5.3 | The system shall separate core logic into dedicated service classes (e.g., MatrixIntersectionEngine, OCCValidator). |
| 6.0 | **Scalability** |
| 6.1 | The system shall support up to 50 concurrent users without performance degradation. |
| 6.2 | The system shall process email notifications through a separate queue worker to avoid blocking the web application. |
| 7.0 | **Legal and Ethical** |
| 7.1 | The system shall process only timetable schedules, staff names, student names, and cohort codes, which are not covered under Malaysia's Personal Data Protection Act 2010 (Act 709). |
| 7.2 | The system shall be developed only on localhost to ensure no university data leaves TAR UMT. |

### 7.4 NFR implementation notes (verified against codebase where marked ✓)
- **NFR 2.2 ✓** — Bcrypt hashing is Laravel default; seeder uses `Hash::make`.
- **NFR 2.5 ✓** — CSRF protection is Laravel default (VerifyCsrfToken middleware).
- **NFR 5.1 ✓** — Pint configured (`pint.json`, `composer run lint:check`).
- **NFR 5.2 ✓** — all schema changes live in `database/migrations/`.
- **NFR 2.4** — set `config/session.php` `lifetime = 30` during Sprint 3 hardening.
- **NFR 5.3** — create `App\Services\MatrixIntersectionEngine` and `App\Services\OCCValidator` in Sprint 1/2.
- **NFR 4.17/6.2/1.4** — `QUEUE_CONNECTION` must be a database-backed queue; run `php artisan queue:work` as a separate worker.
- **NFR 3.4** — post-login redirect per role (`/dashboard` split by role or role-aware home).
- **NFR 7.2** — never deploy beyond localhost; no external data egress.

---

## 8. Dataset Reference (Seeding)

### Cohorts (14 — already seeded)
- **FOCS (11):** DFT1(S1), DFT2(S1), DSF1(S1), DSF2(S1), RSD1(S1)G1, RSD2(S1)G1, RSD2(S1)G2, RSD2(S1)G3, RSD3(S1)G1, RSD3(S1)G2, RSD3(S1)G3
- **FAFB (3):** RAF2(S3)G2, RAF2(S3)G4, RBU1(S1)G1
- Code format: `{ProgCode}{Year}(S{Sem})G{Group}`; intake = June of enrolment year; academic year 2025/26
- Master dataset in `dataset/cohorts.md`

### Student counts per cohort (FIXED — user decision 2026-08-01, reproducible capacity-filter demos)

| Cohort | Students | Cohort | Students |
|--------|----------|--------|----------|
| DFT1(S1) | 30 | RSD3(S1)G1 | 14 |
| DFT2(S1) | 28 | RSD3(S1)G2 | 14 |
| DSF1(S1) | 24 | RSD3(S1)G3 | 13 |
| DSF2(S1) | 22 | RAF2(S3)G2 | 12 |
| RSD1(S1)G1 | 18 | RAF2(S3)G4 | 10 |
| RSD2(S1)G1 | 16 | RBU1(S1)G1 | 20 |
| RSD2(S1)G2 | 16 | | |
| RSD2(S1)G3 | 15 | | |

**Capacity implications (by design):** MPU-3133 combined = 83 students → only B110/B111 (cap > 35) fit. MPU-3232 combined = 44 → also B110/B111. Tutorial rooms (≤35) remain valid for single-cohort and small modules. *(Adjustable — tell me and I'll update seeder + this table together.)*
*Seeder key format note: seeder keys always include the group suffix (`DFT1(S1)G1`); display names in `dataset/cohorts.md` omit `G1` for DFT/DSF.*

### Academic staff (14 — already seeded)
- **DCIT (11):** 9 Lecturers + 2 PLs (Surayaini 5425, Mohd Nur Rahmat 5516)
- **DACB (2):** Tan Sharon (4363), Chang Foo Chung (5254)
- **DSSH (1):** Muada Bin Ojih (3799)
- Master dataset in `dataset/lecturers.md`

### Venues (23 Block B rooms — NOT yet seeded, need `venues` migration)
- Tutorial (16): B002, B014–B018, B100–B109 | Lecture Halls (2): B110, B111 | Labs (4): B005, B009–B011 | Cisco Lab (1): B006

### Timetable dataset (TO BE SYNTHESIZED — Sprint 1)
- No real timetable sheets were provided; I will synthesize a realistic 14-week dataset in `dataset/timetable.md` (modules with session types L/T/P, class blocks per cohort, room assignments, MPU-3133 + MPU-3232 use cases embedded). User reviews before seeding.

### High-risk use cases (engine validation targets)
1. **Cross-faculty (MPU-3133 Falsafah dan Isu Semasa):** shared across RAF2, RBU1, RSD3 → must compute unified slot across faculties; venue filter = exclude labs (tutorial rooms + lecture halls allowed, per user decision; conflicts with older Ch1 wording).
2. **Cross-year stacking (MPU-3232 Entrepreneurship):** L&T module shared between RSD2(S1)G2, RSD2(S1)G3, RSD3(S1)G3 → prevent horizontal clashes with concurrent core modules.

---

## 9. Page Inventory & Routes

### Real routes (`routes/web.php`)
| Route | Purpose | Auth |
|-------|---------|------|
| `/` | Welcome / landing | public |
| `/login/student`, `/login/staff` | Role-specific login pages | public |
| `/dashboard` | Authenticated dashboard | `auth` |
| `/settings/profile`, `/settings/appearance`, `/settings/security` | Profile settings | auth (+verified) |

### UI design-template routes (frontend mock phase)
| Route | Template | activeNav |
|-------|----------|-----------|
| `/my-timetable-ui` | `MyTimetable-UI-design-template` | my-timetable |
| `/cohort-timetable-ui` | `CohortTimetable-UI-design-template` | cohort-timetables |
| `/replacement-home-ui` | `replacement-home-UI-design-template` | replacement-arrangement |
| `/replacement-arrangement` | `replacement-arrangement-UIdesign-template` | replacement-arrangement |
| `/my-request-history-ui` | `my-request-history-UI-design-template` | replacement-history |
| `/request-approval-ui` | `request-approval-UI-design-template` (planned per SDD) | request-approval |

### Nav bar (5 items, `partials/ui-nav-bar.blade.php`)
Dashboard → My Timetable → Cohort Timetables → Replacement Arrangement → Replacement History

### Page changelogs
Each page's development history is logged in `page-changelogs/*.md` — **update the relevant changelog when you change a page.**

---

## 10. Coding Conventions (IMPORTANT — OOP Concepts)

The project applies **OOP principles at every layer**; FYP rubric evaluates this. Keep it consistent:

### 0. UI Design Rules (user-mandated 2026-08-01)
These ten rules are **non-negotiable** for every page and every future change:

1. **Colors must be consistent across all pages.**
   - ALL colors come from the CSS custom-property token set in `public/css/theme.css` (`--color-bg`, `--color-surface`, `--color-primary`/`secondary`/`tertiary`/`error` + their `-container`/`-on-*` variants, defined once for dark + once for light).
   - **NEVER hardcode hex/rgb/rgba** in a page's `@section('page-styles')`. Use `var(--color-...)`. If a new color is needed, add the token to `theme.css` once.
   - Slot-grid colors are fixed token-mapped: Green=`--color-secondary`, Red=`--color-error`, Yellow=`--color-tertiary`, Grey=`--color-outline-strong`, Blue=`--color-primary`. Status badges use the same mapping on every page.

2. **Use the same name + same color for the same meaning everywhere.**
   - A status/legend label and its color are a **canonical pair defined once below**; every page must use that exact pair. Never give the same concept a different label or a different color on another page (e.g. if Cohort Timetable shows "Conflict" in red, My Timetable must also show "Conflict" in red — not "Occupied" in red).
   - Identical component = identical class name across pages (`.badge`, `.legend-item`, `.summary-card`, `.filter-select`, `.cell-code`, `.empty-state`, etc.), defined once in `theme.css`.

   #### Canonical legend / status → color map (single source of truth)
   Two semantic contexts share one palette — green=free/ok, red=conflict/occupied, yellow=pending, blue=primary, grey=reserved/neutral.

   **A. Read-only timetable legend** (My Timetable, Cohort Timetable, master views):
   | Label | Token | Meaning |
   |-------|-------|---------|
   | Normal Class | `--color-secondary` (green) | scheduled class |
   | Replacement | `--color-primary` (blue) | approved replacement session |
   | Pending | `--color-tertiary` (yellow) | awaiting PL approval |
   | Conflict | `--color-error` (red) | clashing block |

   **B. Replacement-arrangement slot grid legend** (booking interface, slot states):
   | Label | Token | Meaning |
   |-------|-------|---------|
   | Available | `--color-secondary` (green) | satisfies all constraints |
   | Your Current Selection | `--color-primary` (blue) | active pick |
   | Pending (You) | `--color-tertiary` (yellow) | your submitted request awaiting PL |
   | Reserved by Others | `--color-surface-variant` (grey) | another lecturer's pending request |
   | Occupied / Class on Public Holiday | `--color-error` (red) | locked / blocked |

   **C. Request-history status badges** (`.status-*`):
   | Label | Container tokens |
   |-------|------------------|
   | Pending | `--color-tertiary-container` / `-on-tertiary-container` |
   | Approved | `--color-secondary-container` / `-on-secondary-container` |
   | Rejected | `--color-error-container` / `-on-error-container` |
   | Cancelled | `--color-surface-variant` / `-on-surface-variant` |
   | Completed | `--color-primary-container` / `-on-primary-container` |

   Adding a new status/legend item = add ONE row to the relevant table above + ONE class to `theme.css`, then reuse it everywhere. Do not invent a synonym.

3. **Utilise OOP concepts** (see §10.1–10.4 below — Inheritance, Composition, Encapsulation/DRY, service classes). No copy-paste; reuse layout/partial/shared-module.

4. **Minimise plain text, maximise icon buttons.**
   - Prefer **icon buttons** over text labels where the action is self-evident (edit ✎, cancel ✕, approve ✓, view 👁, chevrons ‹ ›, sort ▲▼, theme-toggle, notifications). Use `title`/`aria-label` for accessibility instead of visible text.
   - Keep visible text to essentials: page title, table headers, status badges, and the key data the user came for. Long action verbs ("Approve Request", "View Details", "Cancel Request") → icon button + tooltip.
   - Reusable inline SVG icon set lives in `resources/views/flux/icon/` (`book-open-text`, `chevrons-up-down`, etc.); reuse those — don't paste ad-hoc SVGs per page.

5. **Don't overwhelm the user — push detail/secondary info into modals.**
   - The page surface shows only what's needed to scan & act: the grid/table, its filters, summary cards, and primary actions. Everything else (full request details, audit history, rejection-reason form, validation breakdown, room/cohort breakdowns, raw slot data) opens in a **modal** on click, not inline.
   - Rule of thumb: if a column/field is "nice to know" rather than "need to scan", it belongs behind an icon button that opens a modal. Keep the default view scannable.
6. **Promote-on-3rd-duplication (DRY / OOP).** When building any page, if a markup block / CSS class / JS helper / mock-data slice is **now the same across 3+ pages** (e.g. the `.legend-bar`, the `.summary-bar` + `.summary-card` pattern, the week picker, the detail modal shell), promote it to a shared file and refactor the duplicates away:
   - shared markup → a new Blade partial in `resources/views/partials/` (then `@include` it on every page);
   - shared CSS → a class in `public/css/theme.css`;
   - shared JS → a helper in `public/js/ui-common.js`;
   - shared mock data → a section in `public/js/mock-data.js`.
   Replace the inline copies in the **new page AND the existing pages** with `@include` / `var(--color-...)` / `helper()` / `MockData.*`. Never leave 3 copies of the same thing — that breaks the OOP/DRY concept the FYP rubric scores. Record every promotion in the SDD `design.md` "Promoted to shared" section.
7. **Minimise steps — fewest clicks possible.** Every common task must reach its outcome in the minimum number of clicks/screens. Prefer one inline action over multi-step forms, pre-select sensible defaults, and avoid hopping between pages for a single task. If a flow needs more than ~3 clicks to finish, rethink it.
8. **Confirm critical actions.** Any destructive or irreversible action (delete, submit, cancel, approve, reject) MUST show a confirmation popup (`confirm()` or a styled overlay) before executing — e.g. "Are you sure?". This makes the system forgiving: users can explore unfamiliar features without fear, knowing a critical move can always be backed out.
9. **Mobile responsive design.** Every page MUST include mobile layout (≤768px breakpoint). The following enhancements are MANDATORY for all UI pages:
   - **Nav drawer:** Hamburger icon (☰) replaces desktop links; slide-in drawer from left with overlay, close on tap/ESC/swipe; body scroll locked when open.
   - **Card layout:** Convert data tables to card layout on mobile (each row = a card with essential columns only). No horizontal scroll.
   - **Summary cards:** 2-column grid on mobile (5 cards → 3+2 layout).
   - **Legend bar:** Flex-wrap, items flow naturally into 2 rows.
   - **Semester bar:** Reduce select width, stack elements if needed, full-width.
   - **Page header:** Chips stack vertically, title reduces font size.
   - **Bottom sheet modals:** Modals slide up from bottom (not centered), 80vh max, drag handle, full-screen backdrop.
   - **Touch targets:** All buttons/links ≥ 44×44px (WCAG 2.5.5).
   - **Swipe gestures:** Swipe left/right to navigate weeks on timetable.
   - **Collapsible cards:** Day cards collapse/expand on tap (chevron indicator).
   - **Responsive typography:** Use `clamp()` for fluid font scaling (title 24→20px, day 14→13px, event 12→11px).
   - **Safe area insets:** Respect iPhone notch/home indicator via `env(safe-area-inset-*)`.
   - **Full-width inputs:** Selects, text inputs, buttons span full width on mobile.
   - **Skeleton loading:** Grey placeholder shapes with shimmer animation while data loads.
   - **Scroll restoration:** Remember scroll position on browser back/forward via `sessionStorage`.
   - **Toast position:** Toasts at bottom-center on mobile (thumb-reachable).
   - **Viewport meta:** `<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">` in `<head>`.
   Add CSS media queries in `theme.css` (shared). JS helpers in `ui-common.js`. Document mobile layout in `design.md` under "Mobile view" section.
10. **Toast/undo bar for critical actions.** After any critical action (delete, submit, cancel, approve, reject), show a temporary toast bar at **bottom-left** (same position as `.bulk-action-bar`: `position: fixed; bottom: 24px; left: 24px; z-index: 200`) with a success message and an **Undo** button. The toast auto-dismisses after **5 seconds** unless manually closed. The undo callback restores the previous state (re-insert cancelled items, revert status, etc.). CSS in `theme.css`, JS helper `showToast(message, undoCallback, duration)` in `ui-common.js`. Document every toast usage in `design.md` under "Toast/undo bar" section.

### 1. Inheritance
- **Blade layout inheritance:** every page `@extends('layouts.ui-template')` and fills `@section('content')`, `@section('page-styles')`, `@section('page-scripts')`, `@yield('title')`. See `resources/views/layouts/ui-template.blade.php` (42 lines).
- PHP: Eloquent models inherit `Model`; `User` extends `Authenticatable`; middleware extends base classes; migrations use anonymous `class extends Migration`.

### 2. Composition
- **Blade partials** via `@include('partials.ui-nav-bar', ['activeNav' => ...])`, `@include('partials.ui-summary-bar', ['cards' => [...]])`, `partials.head`, `partials.settings-heading`.
- `ui-page-header` — page title + semester chip + description
- `ui-week-nav` — week navigation arrows + select + optional today button
- `ui-empty-state` — empty state with icon, title, text, optional CTA
- `ui-grid-table` — grid-wrapper + scrollable timetable shell
- `ui-class-detail-modal` — modal overlay for class details
- PHP: Laravel service classes + middleware composition (e.g. `App\Concerns\PasswordValidationRules` trait mixed into Fortify actions).

### 3. Encapsulation & DRY — shared modules
- **`public/css/theme.css`** (655 lines) — ALL shared CSS (nav bar, app container, page header, toolbar, tables, badges, pagination, summary cards, empty states, responsive breakpoints, color tokens via CSS custom properties). Page-specific styles stay in `@section('page-styles')`.
- **`public/js/ui-common.js`** (197 lines) — shared JS helpers: `updateIcon`, `toggleTheme`, `navigateHome`, `to12h`, `formatDate`, plus consolidated table/sort/pagination/urgency helpers. Page-specific logic stays in `@section('page-scripts')`.
- **`public/js/mock-data.js`** — the **single source of truth for ALL mock data** across every UI page (`window.MockData`). Contains shared registries (`cohorts`, `lecturers`, `venues`, `semester`, `holidays`) and per-page datasets (`myTimetable`, `cohortTimetable`, `requests`, `conflictedClasses`, etc.). Throwaway-by-design: deleted when Sprint 3 wires real Livewire/DB data.
  - **Rule:** every UI page MUST read mock data from `window.MockData.*` — NEVER re-declare cohorts/lecturers/venues/semester or duplicate page datasets inline in a `<script>` block.
  - **Read-only:** pages MUST treat `MockData` as immutable. Derive a local copy (`slice()`/spread) before mutating per-week/per-session state — never mutate `MockData` directly (it contaminates other pages).
  - **New page needs new data?** Add ONE new section to `public/js/mock-data.js` (e.g. `MockData.studentTimetable = {...}`), then reference it from the page. Do NOT inline it.
  - The page still loads via `<script src="/js/mock-data.js"></script>` (already included by `layouts/ui-template`); the page's own `@section('page-scripts')` holds only render logic, no data.
- **NEVER copy-paste** nav bar, tables, helpers, or mock data into a new page — reuse the layout/partials/shared modules.
- Theme toggle: `localStorage('theme')` = `dark`/`light`, `<html class="dark">` default; login pages use `.login-theme-toggle` (avoid class collision with nav `.theme-toggle`).

### 4. General PHP/Laravel conventions
- PSR-4 (`App\`), anonymous classes for migrations, `protected function casts()` for attribute casting
- Route model binding, Eloquent relationships typed with return types + PHPDoc
- Existing middleware pattern: `CheckRole`, `CheckPl` — follow for any new role gating
- Run `composer run lint:check` + `composer run types:check` before finishing any task
- Fortify actions in `app/Actions/Fortify/`; concerns/traits in `app/Concerns/`

### 5. Naming
- Blade templates: `kebab-case-UI-design-template.blade.php` for UI mocks; `layouts/ui-template` for real layout
- Tables: `snake_case` plural; models: `StudlyCase` singular
- CSS classes: semantic kebab-case (`.col-code`, `.badge`, `.summary-card`, `.filter-select`)
- **Core logic service classes (NFR 5.3):** `App\Services\MatrixIntersectionEngine` (Sprint 1), `App\Services\OCCValidator` (Sprint 2) — no business logic in controllers/views

### 6. Git workflow
- Branch `fjing` is the working branch (`master` + `origin/fjing` exist)
- Conventional commits: `ui:`, `feat:`, `fix:`, `refactor:`, `oop:`, `style:`, `docs:`
- **SDD workflow:** each change gets a folder under `.sdd/changes/<change-name>/` with `sdd.yaml`, `proposal.md`, `design.md`, `tasks.md`, `review-log.md` (proposal → design → apply → verify → archive). Existing: `replacement-home-dashboard`, `request-approval`, `cohort-timetable-ui`, `my-request-history`, `oop-blade-refactor`, `refactor-blade-oop`.

---

## 11. Current Status

### ✅ Done
- Laravel 13 project scaffold (Livewire starter kit + Fortify + passkeys + Flux)
- Auth scaffold with role-specific login pages (`/login/student`, `/login/staff`) and role column
- Seeders: 2 faculties, 3 departments, 5 programmes, **14 cohorts**, **14 lecturers** (2 PLs), ~10–15 students/cohort (random)
- **OOP Blade refactor** (SDD: oop-blade-refactor) — shared `layouts/ui-template`, `partials/ui-nav-bar`, `theme.css`, `ui-common.js`
- 5 UI design templates built (My Timetable, Cohort Timetable, Replacement Home, Replacement Arrangement, My Request History) + request-approval template in progress
- Page changelogs in `page-changelogs/`

### 🔲 Not built (FYP2 sprints)
- **Sprint 1:** Matrix Intersection Engine + timetable/module/venue schema + seed timetable data + intersection API
- **Sprint 2:** OCC layer (transactional validation, rollback, conflict alerts) + FCFS approval dashboard + audit trail
- **Sprint 3:** UI integration of real data into templates, RBAC gating on all routes, email notifications, test cases, UAT

---

## 12. Roadmap (FYP2, 7 weeks)

| Sprint | Weeks | Deliverable |
|--------|-------|-------------|
| Sprint 1 | W1–2 | Venue/module/timetable migrations + seed (synthesized `dataset/timetable.md`); `MatrixIntersectionEngine` service (4-vector intersection, capacity filter, venue-type rules, duration-aware windows); API/testable service |
| Sprint 2 | W3–4 | OCC transaction layer on submission (state re-validation at write, rollback + alert); replacement request state machine; FCFS PL dashboard; audit trail |
| Sprint 3 | W5 | Wire real data into the 5 UI templates; RBAC gates on all routes; email notifications; PHPUnit tests for all objectives' success criteria |
| Final | W6–7 | Thesis Chapters 5–7, documentation, viva prep |

### Success criteria to verify per objective (from Ch1)
1. Engine returns correct common free slots for multi-cohort combos in <500ms; capacity filter excludes small rooms; all edge cases handled.
2. Two simultaneous submissions for same slot → exactly one succeeds, other gets rollback alert; outcomes logged in audit trail.
3. Queue sorted by submission timestamp; pre-validated slots; 1-click approve; mandatory rejection reason; audit trail complete.
4. Student view-only; lecturer create/submit/edit/cancel own; PL hybrid; unauthenticated users redirected to login; emails fired on state transitions.
5. All seed data loads without FK errors; all 3 roles log in with test credentials; each objective's criteria verifiable via test cases.

---

## 13. Key Files Map

```
MAIN.md                              ← this file (read first)
../final/FR&NFR.md                   ← Chapter 3 FR/NFR source (traceability in §7)
routes/web.php                       ← public + UI-template routes
routes/settings.php                  ← settings routes
app/Models/                          ← User, Faculty, Department, Programme, Cohort, Lecturer, Student
app/Http/Middleware/                 ← CheckRole, CheckPl
database/migrations/                 ← users, faculties..lecturers, passkeys
database/seeders/DatabaseSeeder.php  ← 14 cohorts, 14 staff, students
dataset/cohorts.md, lecturers.md     ← master datasets
resources/views/layouts/ui-template.blade.php   ← shared layout (inheritance)
resources/views/partials/ui-nav-bar.blade.php   ← shared nav (composition)
resources/views/partials/ui-summary-bar.blade.php
resources/views/partials/ui-page-header.blade.php        ← page header partial (title + chips + description)
resources/views/partials/ui-week-nav.blade.php           ← week navigation partial (arrows + select + today btn)
resources/views/partials/ui-empty-state.blade.php        ← empty state partial (icon + title + text + CTA)
resources/views/partials/ui-grid-table.blade.php         ← grid table partial (wrapper + scroll + table shell)
resources/views/partials/ui-class-detail-modal.blade.php ← class detail modal partial (overlay + header + body + footer)
public/css/theme.css                 ← ALL shared CSS
public/js/ui-common.js               ← ALL shared JS helpers
public/js/mock-data.js               ← ALL mock data (window.MockData), single source — pages READ ONLY
resources/views/ui-design-templates/ ← 5 mock templates
page-changelogs/                     ← per-page change logs
.sdd/changes/                        ← SDD change proposals
prompts/                             ← sdd-propose template + spec (auto-read via AGENTS.md)
```

---

## 14. Notes for Future Sessions (Forking Context)

- This chat is the **main coding session**; fork sessions from it for feature work.
- **Before coding:** read MAIN.md, check `.sdd/changes/` for any active proposal related to your task, and run `git status`/`git log` for uncommitted work.
- **Always:** update the matching `page-changelogs/*.md` for UI changes; follow the SDD proposal → design → tasks flow for non-trivial features.
- Test DB is PostgreSQL; to reset demo data run `php artisan migrate:fresh --seed`.
- If the database isn't reachable, check `php artisan migrate:status` and that PostgreSQL is running (`pg_isready`).
