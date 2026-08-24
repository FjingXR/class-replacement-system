# Changelog — Upcoming Replacements (Student)

## [2026-08-24] Stub page + route + mock data (TASK-006 Phase 1)

Student role drops "Request History" and gets "Upcoming Replacements" instead
(Ch1 §1.1.4 view-only; FR 1.4 upcoming replacement details). UI design deferred to
Sprint 3 per `todo list/upcoming-replacements-ui-plan.md`.

### Files Created

#### `resources/views/ui-design-templates/upcoming-replacements-UI-design-template.blade.php`
- Stub template: `@extends('layouts.ui-template')` with the 2-item student `navItems`
  (Student My Timetable + Upcoming Replacements), `pageKey='upcomingReplacements'`
- Reuses `partials.ui-page-header` + `partials.ui-empty-state` ("UI design pending")
- Semester chip + notif badge init copied from sibling student page pattern

#### `page-changelogs/todo list/upcoming-replacements-ui-plan.md`
- Implementation Details doc for the upcoming UI build (data contract, status→color map,
  OOP reuse inventory, openClassModal wiring, layout sketch, hard rules, checklist)

### Files Modified

| File | Change |
|---|---|
| `routes/web.php` | Added `GET /upcoming-replacements-ui` → stub view (`activeNav='upcoming-replacements'`) |
| `public/js/mock-data.js` | New §2.12 `upcomingReplacements` — 3 rows keyed to real `rsd3g2Base` courses + `rsd3g2Flags` weeks (BMIT7074 w1 approved, BMIT2233 w2 pending/no venue yet, BMIT5678 w7 approved); grid-compatible `di/start/end` index space |
| `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php` | Nav item `replacement-history` → `upcoming-replacements` |

### Key Decisions
- `/my-request-history-ui` route kept — lecturers/PLs still use it; only the student nav link is replaced.
- Status vocabulary mirrors timetable legend: `replacement`=approved-upcoming (blue), `pending`=awaiting PL (yellow) — `.badge-replacement` / `.badge-pending`.
- Pending rows carry null new-slot fields so the UI can show "Awaiting PL approval" without inventing data.
