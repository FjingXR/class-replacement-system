# Tasks: Student My Timetable UI

*All prior artifacts frozen. Tasks derived from `design.md` §5 file-by-file map.*

---

### Task 1 — Mock data additions (no dependencies) ✅
**File:** `public/js/mock-data.js`
- [x] Add `MockData.studentTimetable = { activeCohort: 'rsd3s1g2', cancelledFlags: { 4: ['BMIT7070', 'BMIT7071', 'BMIT7072', 'BMIT8080', 'BMIT7073', 'BMIT7074', 'BMIT7075'], 5: ['BMIT8080'], 6: ['BMIT7073'] }, notificationCount: 3 }` (0-indexed week keys). Week 4 cancels ALL 7 events → triggers empty state (item 17). Weeks 5–6 cancel 1 event each (realistic partial cancellation).
- [x] Update `MockData.holidays` header comment: change "CONSUMED BY CohortTimetable ONLY" to "CONSUMED BY CohortTimetable + Student My Timetable".
- [x] Verify `MockData.semester.chipText` already contains the current semester text; update if stale.

---

### Task 2 — Create shared legend partial (no dependencies) ✅
**File (NEW):** `resources/views/partials/ui-legend-bar.blade.php`
- [x] 4 legend items with swatches using `var(--color-secondary/primary/tertiary/error)` tokens.
- [x] Class names: `.legend-bar`, `.legend-item`, `.legend-swatch` (matches design.md §4.2).

---

### Task 3 — Parametrize nav partial (no dependencies) ✅
**File:** `resources/views/partials/ui-nav-bar.blade.php`
- [x] Add `$navItems` default block at top (design.md §1.8: 5 items with `key`/`label`/`href`).
- [x] Add `$notifCount` parameter (default `3`) and use `{{ $notifCount ?? 3 }}` in the badge span.
- [x] Add `id="notifBadge"` to `<span class="notif-badge">`.
- [x] Replace hardcoded `<a>` tags with `@foreach ($items as $it)`.
- [x] Existing pages pass only `activeNav` → unchanged behavior (defaults kick in).

---

### Task 3b — Forward notifCount in layout (depends: Task 3) ✅
**File:** `resources/views/layouts/ui-template.blade.php`
- [x] Change the `@include('partials.ui-nav-bar', ...)` call to forward `$notifCount ?? 3`.
- [x] Existing pages don't pass `$notifCount` → default `3` kicks in. Student page passes real count via `@extends`.

---

### Task 4 — Promote CSS to theme.css (depends: none, but apply before Tasks 5–6) ✅
**File:** `public/css/theme.css`
- [x] Append blocks 4.2–4.8 from design.md §4 table (legend, summary-card, modal-shell, semester-bar/week-picker, time-column, hour-header, hour-cell+event-block).
- [x] `.week-select` min-width → 160; `.modal-footer` → `flex-end`.
- [x] `.semester-bar select:not(.week-select)` block comes from CohortTimetable only (1 copy, not 2); promotion consolidates it for future use.
- [x] Append blocks 4.10–4.16 (enhancement CSS): `.today-btn`, `.week-subtitle`, `.heatmap-bar/.heatmap-cell`, `.event-block:focus-visible`, `.status-timeline/.step`, `.semester-progress/.progress-track/.progress-fill/.progress-label`.
- [x] Append blocks 4.17–4.19 (new enhancement CSS): `.grid-transitioning` (week-change fade), `.event-block::after` (hover tooltip) + `.ev-code` cursor pointer, `.copy-toast/.copy-toast.show`.
- [x] Note: `.empty-state` already exists in `theme.css` — no new CSS needed for empty state (reused by student page).

---

### Task 5 — Refactor MyTimetable (depends: Tasks 2, 4) ✅
**File:** `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`
- [x] Replace inline legend markup → `@include('partials.ui-legend-bar')`.
- [x] Remove inline CSS blocks 4.2–4.8 (now in theme.css); delete `.today-highlight`.
- [x] Keep: `.btn-replace-now`, `.btn-cancel-class`, `.cancel-overlay`, `.modal-footer-left`, `.modal-footer-right`, `.modal-footer{space-between}` override (design.md §4 "Stays page-specific").
- [x] Migrate `weekData` builder + `currentWeekIndex()` + chip → `MockData.semester`.

---

### Task 6 — Refactor CohortTimetable (depends: Tasks 2, 4) ✅
**File:** `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`
- [x] Replace inline legend markup → `@include('partials.ui-legend-bar')`.
- [x] Remove inline CSS blocks 4.2–4.8 (now in theme.css); delete `.today-highlight`.
- [x] Keep: `.badge-*` page-specific variants (design.md §4 "Stays page-specific").
- [x] Migrate `weekData` builder + `currentWeekIndex()` + chip → `MockData.semester`.
- [x] **Do NOT touch** inline holiday rule `d===3&&w===3` (out of scope per design.md §6).

---

### Task 7 — Create student page template (depends: Tasks 1, 2, 3, 3b, 4) ✅
**File (NEW):** `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`
- [x] `@extends('layouts.ui-template', ['activeNav' => 'my-timetable', 'navItems' => [...3 items...], 'notifCount' => MockData.studentTimetable.notificationCount])`.
- [x] Page header: title + semester chip (`MockData.semester.chipText`) + cohort chip ("RSD3(S1)G2") + `.semester-progress` (§1.18).
- [x] Semester bar: `‹` / week `<select>` / `›` / Today button (§1.9) (NO faculty/cohort dropdowns).
- [x] Week subtitle (§1.10): "Week 3 of 14 · 15 Sep – 21 Sep".
- [x] Heatmap bar (§1.12): 14 clickable week squares, color-coded by status priority.
- [x] Grid: `table.timetable` + `.empty-state` (§1.13, hidden by default) fed by `visibleEvents` (design.md §1.3–1.4).
- [x] `@include('partials.ui-legend-bar')` + `@include('partials.ui-summary-bar', [...5 cards...])`.
- [x] Modal (§1.7): view-only (Close ✕ only), field list from design.md §1.7, `closeModalOutside(e)` wrapper, status-timeline for pending (§1.15).
- [x] `@section('page-scripts')`: reconstruction algorithm from design.md §1.3 (with `meta: {}`), cancelled-filter §1.4, holiday derivation §1.5, week calendar §1.6, keyboard nav §1.14, week-change transition toggle (§1.19), copy-course-code click handler + toast (§1.21).
- [x] Event blocks get `tabindex="0"` + `__eventData = event` for keyboard Enter support.
- [x] Event blocks get `data-name` + `data-venue` attributes for hover tooltip (§1.20).
- [x] Toast element: `<div class="copy-toast" id="copyToast"></div>` at bottom of page body.
- [x] Today button + heatmap click handlers include `scrollIntoView({ behavior: 'smooth', block: 'start' })` on `.grid-wrapper` (§1.22).

---

### Task 8 — Add route (depends: Task 7) ✅
**File:** `routes/web.php`
- [x] Add `GET /student-my-timetable-ui` → view `ui-design-templates.student-my-timetable-UI-design-template` with `['activeNav' => 'my-timetable']`.

---

### Task 9 — Verify & lint (depends: Tasks 1–8) ✅
- [x] Run `composer run lint:check` (Pint).
- [x] Run `composer run types:check` (PHPStan/Larastan) — 20 pre-existing errors, none in files we modified.
- [x] Verify MyTimetable and CohortTimetable pages still render correctly after refactors.

---

### Task 10 — Changelog (depends: Task 9) ✅
**File:** `page-changelogs/student-my-timetable-ui-changelog.md`
- [x] Populate with change summary: what was built, files modified/created, decisions made.
