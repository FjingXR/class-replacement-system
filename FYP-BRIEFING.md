# FYP Briefing — TARUMT Class Replacement System

> **Purpose:** Share this file with any AI/tool (e.g., commandcode) that needs to understand your FYP project context.

---

## Project Identity

- **Title:** TARUMT Class Replacement System
- **Student:** Poong Foo Jing (25SMR10186), Bachelor in IT (Hons) Software Systems Development, RSD3G2
- **Supervisor:** Mr. Lim Jia Zheng
- **Moderator:** Ms. Teng Nga Sing
- **Client:** Faculty of Computing and Information Technology (FOCS), TAR UMT Sabah
- **Dev model:** Agile (FYP1: 14 weeks design + FYP2: 7 weeks, 3 sprints)
- **SDG alignment:** SDG 4 (Quality Education), SDG 8 (Decent Work & Economic Growth)

---

## What It Does

Replaces manual Google-Sheets-based class replacement workflow at TAR UMT Sabah. When a lecturer has a schedule conflict, they submit a replacement request. The system finds conflict-free slots via a **Multi-Entity Matrix Intersection Engine**, prevents double-booking via **Optimistic Concurrency Control (OCC)**, and routes requests through an **FCFS approval dashboard** for Programme Leaders.

---

## 5 Pain Points (Problem Domain)

1. **Fragmented process & human error** — each lecturer keeps a personal schedule spreadsheet; no single source of truth
2. **Blind spots in multi-cohort scheduling** — modules shared across cohorts require manually overlaying separate timetables
3. **Venue availability tracking** — room occupancy lives in a static PDF, separate from scheduling
4. **Data race conditions** — Google Sheets has no transaction isolation → double-booking with no rollback
5. **Elevated PL administrative burden** — PL re-verifies every request from scratch; no audit trail

---

## Tech Stack

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
composer run lint             # pint --parallel (fix)
composer run lint:check       # pint --test
composer run types:check      # phpstan analyse
composer run test             # lint:check + types:check + phpunit
```

### Test Login
- Default password for ALL seeded users: `Tarumt@2026`
- Lecturers: login by email (`surayaini@tarc.edu.my`, etc.) via `/login/staff`
- Students: `{yy}{PROGCODE}{seq}@student.tarc.edu.my` (e.g. `25RSD0001@student.tarc.edu.my`) via `/login/student`
- PLs: Pn. Surayaini Binti Basri (5425), En. Mohd Nur Rahmat Bin Mohd Taat (5516) — `is_pl = true`

---

## 3 User Roles

| Role | Permissions |
|------|-------------|
| **Student** | View-only: own cohort timetable, request status (FR 1.1–1.5) |
| **Lecturer** | View own timetable, submit/edit/cancel own requests (FR 2.1–2.12) |
| **Programme Leader** | All lecturer rights + view all requests, approve/reject, audit trail (FR 3.1–3.7) |

### RBAC Matrix

| Capability | Student | Lecturer | PL |
|-----------|:-------:|:--------:|:--:|
| Login / Logout | ✅ | ✅ | ✅ |
| View consolidated master timetable | ✅ | ✅ | ✅ |
| View cohort timetables | ✅ | ✅ | ✅ |
| View global replacement history | ✅ | ✅ | ✅ |
| Create / draft / submit replacement request | ❌ | ✅ | ✅ |
| Edit / cancel own pending request | ❌ | ✅ | ✅ |
| View other lecturers' requests | ❌ | ❌ | ✅ |
| Approve / reject (FCFS queue) | ❌ | ❌ | ✅ |
| Audit trail access | ❌ | ❌ | ✅ |

---

## 5 Core Objectives (Backend — Not Yet Built)

| # | Objective | Core Idea | Status |
|---|-----------|-----------|--------|
| 1 | **Multi-Entity Matrix Intersection Engine** | 4-vector set intersection: lecturer × cohorts × room × capacity | 🔲 Not built |
| 2 | **Optimistic Concurrency Control (OCC)** | Integer version column, abort on conflict | 🔲 Not built |
| 3 | **FCFS Digital Approval Dashboard** | Chronological queue, one-click approve/reject | 🔲 Not built |
| 4 | **RBAC + Notifications** | 3 roles, email on submission/outcome | 🟡 Partially built |
| 5 | **Prototype Deployment** | Laravel + PostgreSQL, seeded data | 🟡 Partially built |

### Slot State Machine

| State | Meaning |
|-------|---------|
| `available` | Free — satisfies all 4 constraints |
| `pending` | Drafted by current user, awaiting PL approval |
| `reserved` | Claimed by another lecturer's submission |
| `occupied` | PL approved (locked permanently) |

### Visual Grid Colors

- **Green** — available (clickable)
- **Red** — occupied by existing class
- **Yellow** — pending request submitted by you
- **Grey** — reserved by another lecturer
- **Blue** — current selection

---

## 8 UI Pages Developed (Frontend Mock)

| # | Page | Route | Role |
|---|------|-------|------|
| 1 | My Timetable | `/my-timetable-ui` | Lecturer |
| 2 | Cohort Timetable | `/cohort-timetable-ui` | Lecturer |
| 3 | Replacement Home | `/replacement-home-ui` | Lecturer |
| 4 | Replacement Arrangement | `/replacement-arrangement` | Lecturer |
| 5 | My Request History | `/my-request-history-ui` | Lecturer |
| 6 | Request Approval | `/request-approval-ui` | PL |
| 7 | Student My Timetable | `/student-my-timetable-ui` | Student |
| 8 | Venue Timetable | `/venue-timetable-ui` | Lecturer/PL |

### Missing Pages

- Student Request History (FR 1.3)
- Dashboard (role-based landing)
- Notifications (email notification center)

### Nav Bar (5 items)

Dashboard → My Timetable → Cohort Timetables → Replacement Arrangement → Replacement History

---

## 10 UI Design Rules (Non-Negotiable)

1. **Colors from `theme.css` only** — never hardcode hex/rgb
2. **Same name + same color = same meaning** everywhere
3. **OOP:** `@extends`, `@include`, shared modules
4. **Mock data in `mock-data.js`** — never inline
5. **Icon buttons** over text labels
6. **Detail/secondary info** in modals
7. **Promote on 3rd duplication** (DRY)
8. **Minimize steps** — fewest clicks possible
9. **Confirm critical actions** with popup
10. **Mobile responsive** (≤768px breakpoint)

---

## Database Schema

### Existing Tables

| Table | Key Fields | Notes |
|-------|-----------|-------|
| `users` | id, name, email, password, **role** (`student`/`lecturer`), two-factor, passkeys | PL = `lecturer` + `is_pl=true` |
| `faculties` | faculty_code, faculty_name | FOCS, FAFB |
| `departments` | dept_code, dept_name, faculty_id | DCIT, DSSH, DACB |
| `programmes` | programme_code, programme_name, faculty_id | DFT, DSF, RSD, RAF, RBU |
| `cohorts` | programme_id, current_year, semester, tutorial_group, academic_year, intake | CHECK constraints |
| `students` | **user_id (PK)**, student_id, cohort_id | `{yy}{PROG}{seq}` |
| `lecturers` | **user_id (PK)**, staff_id, dept_id, is_pl | |
| `passkeys` | user_id, credential_id, ... | WebAuthn |

### Pending Schema (Not Yet Migrated)

- `venues` — room code, capacity, type, allowed sessions
- `timetable_blocks` / `class_sessions` — lecturer, cohort(s), module, day, time, duration, venue
- `modules` — code, name, session type constraints
- `time_slots` — with **integer `version` column (OCC)** + `status` column
- `replacement_requests` — time_slot_id, proposer, PL, timestamps, rejection reason
- `audit_logs` — PL identity, timestamp, action, slot ID, rejection reason

---

## Dataset

### Cohorts (14 — already seeded)

- **FOCS (11):** DFT1(S1), DFT2(S1), DSF1(S1), DSF2(S1), RSD1(S1)G1, RSD2(S1)G1, RSD2(S1)G2, RSD2(S1)G3, RSD3(S1)G1, RSD3(S1)G2, RSD3(S1)G3
- **FAFB (3):** RAF2(S3)G2, RAF2(S3)G4, RBU1(S1)G1

### Student Counts (Fixed)

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

### Staff (14 — already seeded)

- **DCIT (11):** 9 Lecturers + 2 PLs (Surayaini 5425, Mohd Nur Rahmat 5516)
- **DACB (2):** Tan Sharon (4363), Chang Foo Chung (5254)
- **DSSH (1):** Muada Bin Ojih (3799)

### Venues (23 Block B rooms)

- Tutorial (16): B002, B014–B018, B100–B109
- Lecture Halls (2): B110, B111
- Labs (4): B005, B009–B011
- Cisco Lab (1): B006

---

## SDD Workflow

- Uses **Software Design Documents (SDD)** for planning
- `.sdd/changes/` — active proposals
- `.sdd/archive/` — completed/stale changes
- `prompts/sdd-propose-template.md` — template for new page proposals
- `page-changelogs/` — per-page development history

---

## Key Files to Read

| # | File | Purpose |
|---|------|---------|
| 1 | `CodingMAIN.md` | **Single source of truth** — project overview, architecture, schema, FR/NFR, conventions |
| 2 | `final/FR&NFR.md` | Chapter 3 FR (17) + NFR (21) — the requirements |
| 3 | `public/js/mock-data.js` | All mock data (window.MockData) |
| 4 | `public/css/theme.css` | Shared CSS tokens + components |
| 5 | `resources/views/layouts/ui-template.blade.php` | Base layout (OOP inheritance) |
| 6 | `routes/web.php` | All routes |
| 7 | `.sdd/changes/` | Active SDD proposals |
| 8 | `.sdd/archive/` | Completed SDD changes |

---

## Current Phase

**Frontend mock phase** — all 8 pages are UI templates with mock data. Backend (Objectives 1–5) not yet built.

---

*Last updated: 2026-08-11*
