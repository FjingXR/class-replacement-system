# Explore Brief: student-request-history

## Problem

Students currently have no dedicated page to view replacement request statuses. The "Request History" nav item points to `/my-request-history-ui` — the **lecturer** page that exposes cancel actions, bulk selection, and batch cancel. That violates FR 1.4 (students shall NOT create/edit/delete requests) and fails FR 1.3 (students shall view request statuses) because the lecturer page shows ALL lecturers' requests, not the ones affecting the student's cohort.

## Scope

**In scope:**
- New frontend-only UI page `student-request-history-UI-design-template.blade.php`
- Route `/student-request-history-ui` with `activeNav: replacement-history`
- View-only table: read `MockData.requests`, filter to the student's cohort (RSD3(S1)G2 — same as `studentTimetable.activeCohort`)
- Status badges (Pending / Approved / Rejected / Cancelled / Completed) — canonical §10.0 map
- Detail modal (read-only, no cancel button)
- Summary cards (Total, Approved, Pending, Rejected — no action cards)
- Search + status filter + week filter (reuse existing toolbar pattern)
- Mobile card layout (mandatory rule #9)
- Student nav set: `Student My Timetable` + `Request History` (same reduced nav as student-my-timetable page)

**Out of scope:**
- Any cancel / bulk-cancel / edit actions (FR 1.4 — view-only)
- Request age indicator colors (lecturer UX feature F3 — not needed for students)
- Rows-per-page selector, filter presets/localStorage, keyboard shortcuts (lecturer UX features — keep the page lean)
- Backend, migrations, Livewire wiring
- Changes to the lecturer page or `MockData.requests` structure (read-only reuse)

## Decisions

### 1. New page vs. reusing the lecturer page
**Decision:** Create a NEW dedicated student page.
**Why:** FR 1.4 explicitly forbids student write actions. Retrofitting view-only mode into the lecturer page (hiding cancel buttons, bulk bar, etc.) complicates it and risks accidental violations. A dedicated page also matches the existing pattern: `student-my-timetable-ui` was built separately from `MyTimetable` for the same reason. The lecturer page stays untouched.

### 2. Data source
**Decision:** Reuse `MockData.requests` (20 entries) and filter by cohort.
**Why:** The single-source rule (§10.0 rule 3) forbids duplicating datasets. Each request has a `cohorts` array (e.g. `['DFT2 (S1)', 'DSF2 (S1)']`). The student page filters to requests where `cohorts` includes the student's cohort — `RSD3(S1)G2` (matches `MockData.studentTimetable.activeCohort`).
**Caveat:** the existing 20 requests only cover DFT2/DSF2 cohorts — NONE affect RSD3(S1)G2. **The page will show the empty state by default.** Two options:
- **A (recommended):** Add a new `MockData.studentRequests` section with ~6–8 requests for RSD3(S1)G2 (copies of the real dataset structure). This mirrors how `studentTimetable` was added as its own section. The lecturer page keeps `MockData.requests` unchanged.
- **B:** Point the student page at a filtered `MockData.requests` — but it renders an empty state, useless for demo.

### 3. Column set
**Decision:** Leaner than the lecturer page. Columns: Requested At, Course Code & Name, Original Class, Replacement (date/time/venue), Status. Drop: Students count, Cohort(s) (redundant — all rows are the student's cohort), Quick Cancel column, checkbox column.
**Why:** Students only need to know *what changed* and *what state it's in* (FR 1.3). Fewer columns = scannable, matches "don't overwhelm" rule #5.

### 4. Actions / modals
**Decision:** Detail modal only — read-only, `Close` button, no Cancel Request. Same modal pattern as lecturer page (`DetailModal.render` with General Info / Original Class / Replacement Class tabs + timeline).
**Why:** FR 1.4 view-only. Reuses `DetailModal` helper in `ui-common.js` — zero new modal code.

### 5. Summary cards
**Decision:** 4 cards — Total Requests, Approved, Pending, Rejected. No "Replacement Hours" (lecturer-centric metric).
**Why:** Matches what a student cares about (FR 1.3 statuses). Reuses `partials/ui-summary-bar`.

### 6. Filters
**Decision:** Search + status dropdown + week filter (all exist in the toolbar pattern). No "Exclude Completed" toggle, no filter chips persistence.
**Why:** Keep it minimal — students check status, they don't curate lists. Reuses `ui-week-nav` partial.

## Cross-Module Data Flows

```
student-request-history-UI-design-template.blade.php
  ├── layouts/ui-template.blade.php           (inheritance — @extends)
  ├── partials/ui-nav-bar.blade.php           ($navItems = student set: Student My Timetable + Request History)
  ├── partials/ui-page-header.blade.php       (title + semester chip)
  ├── partials/ui-week-nav.blade.php          (week filter)
  ├── partials/ui-grid-table.blade.php        (table shell)
  ├── partials/ui-summary-bar.blade.php       (4 cards)
  ├── partials/ui-empty-state.blade.php       (empty state — no CTA button, view-only)
  ├── public/js/mock-data.js                  (READ: MockData.semester, MockData.studentRequests*)
  ├── public/js/ui-common.js                  (READ: DetailModal, statusClass, HtmlBuilder, formatDate, to12h, paginate, SkeletonLoader, rebuildTable, populateWeekSelect, isInWeek)
  └── public/css/theme.css                    (READ: all shared classes — .badge, .status-*, .toolbar, .table, .summary-card, .modal)
```

## Implementation Approach

1. **Mock data:** add `MockData.studentRequests` (or extend `studentTimetable`) in `mock-data.js` — ~6–8 requests with `cohorts: ['RSD3(S1)G2']`, mixed statuses (Pending/Approved/Rejected/Completed), realistic dates within the semester window.
2. **Blade template:** `resources/views/ui-design-templates/student-request-history-UI-design-template.blade.php` — `@extends('layouts.ui-template', ['activeNav' => 'replacement-history', 'navItems' => student set])`; toolbar (search + status filter + week nav), grid table, 4 summary cards, read-only detail modal, mobile card view.
3. **Route:** `/student-request-history-ui` in `routes/web.php` → the template with `activeNav: replacement-history`.
4. **JS:** render logic in `@section('page-scripts')` — filter `MockData.studentRequests`, sort by requestedAt desc, paginate (10/page), search/filter, render table + cards, update summary. NO mutation of MockData, NO cancel actions.
5. **Changelog:** `page-changelogs/student-request-history-changelog.md` (new file, house format).
6. **Lint:** `composer run lint:check` + `composer run types:check`.

## Open Questions

1. **Student nav on this page:** the student-my-timetable page uses `navItems` = [Student My Timetable, Request History]. Should the student Request History nav item link to the NEW `/student-request-history-ui` (recommended), and should we also update the student-my-timetable page's nav link to point here? (It currently points to the lecturer page.)
2. **Empty-state CTA:** the lecturer page's empty state has a "Submit a Replacement Request" CTA. Students can't submit (FR 1.4) — should the student empty state have NO button (recommended) or a muted "Contact your lecturer" hint?
3. **Mock data shape:** option A (new `studentRequests` section with RSD3(S1)G2 data) vs option B (filter existing `requests` → always empty). A is recommended for demo value.

## Rejected Approaches

| Approach | Reason Rejected |
|----------|----------------|
| Reuse `/my-request-history-ui` with a "student mode" flag | Violates FR 1.4 risk; couples lecturer page to student mode; bloats the lecturer page; contradicts the precedent of building `student-my-timetable-ui` separately |
| Filter existing `MockData.requests` to RSD3(S1)G2 | Yields zero rows — all 20 requests are DFT2/DSF2; useless for demo, contradicts FR 1.3 demo value |
| Show all 20 requests unfiltered | Students would see other cohorts' requests — wrong data isolation (FR 2.10 spirit: role-appropriate visibility) |
| Copy lecturer page and delete actions | ~900 lines copied → violates DRY/promote-on-3rd; better to build lean and reuse shared helpers |
| Add cancel/bulk actions for students | Directly violates FR 1.4 |

## Estimated Effort

| Task | Estimate |
|------|----------|
| Mock data section (`studentRequests`) | 15 min |
| Blade template (header, toolbar, table, cards, modal, mobile) | 60 min |
| Route + nav wiring | 10 min |
| JS render/filter/paginate logic | 40 min |
| Changelog + lint + typecheck | 15 min |
| **Total** | **~2.5 hrs** |
