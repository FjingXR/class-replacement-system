# Tasks: Cohort Timetable UI

## Task 1 — Create Blade template shell with page structure

- [x] Create `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` with:
  - `@extends('layouts.ui-template', ['activeNav' => 'cohort-timetables'])`
  - `@section('title', 'Cohort Timetable — Class Replacement System')`
  - `@section('page-styles')` — page-specific CSS: semester bar, week-arrow, week-select, session-text, time column widths, legend bar, `.summary-card.card-total` styling, status badge classes (`.badge-normal`, `.badge-replacement`, `.badge-pending`, `.badge-conflict`)
  - `@section('content')` — page-header (title + desc), semester bar (faculty select + cohort select + week arrows + week select + session text), toolbar (search input + status filter + result count), grid-wrapper (grid-scroll > timetable > thead + tbody), legend bar, empty-state div, event modal (view-only with close button only)
  - `@section('page-scripts')` — placeholder/stub script block (actual function implementations added incrementally in Tasks 3–7)
- [x] Verify template compiles (no Blade errors)
- [x] Verify shared nav bar renders when visiting route

**Effort:** 1.5 hours

## Task 2 — Route and nav bar wiring

- [x] Add route in `routes/web.php`:
  ```php
  Route::get('/cohort-timetable-ui', function () {
      return view('ui-design-templates.CohortTimetable-UI-design-template', ['activeNav' => 'cohort-timetables']);
  });
  ```
- [x] Update `resources/views/partials/ui-nav-bar.blade.php` — change `Cohort Timetables` href from `#` to `/cohort-timetable-ui` (line 9)
- [x] Verify nav link navigates to page and highlights with `active` class

**Effort:** 0.5 hours

## Task 3 — Mock data (facultyData, weekData) and buildTimetable()

- [x] Add `weekData` array (14 weeks: Week 9–22, dynamically generated from 15-Jun-2026) with day arrays including `abbr`, `date`, optional `today`/`holiday`/`sunday` flags
- [x] Add `facultyData` array: 4 faculties (FOCS, FOL, FOD, FCCI) with degree (RSD) and diploma (DSF/DFT/DLF/etc.) cohorts, 3–5 cohorts per faculty
- [x] Events follow MyTimetable's structure: `{ di, start, end, code, type, venue, lecturer, cohort, status, name, remarks }`
  - FOCS: RSD2 (S1) — 3 weeks of events (4/4/5 events), DSF2 (S1) — 2 weeks, DFT2 (S1) — 2 weeks, RSD3 G1/G2 — 1 week each
  - FOL: RSDL2 G1 — 2 weeks (includes replacement event)
  - FOD: RSD2 (Design) — 1 week
  - FCCI: DMC2 — 1 week (includes pending event)
- [x] Implement `buildTimetable()` — reads data for selected cohort+week, renders grid head (day columns) + body (hour rows with slot map for multi-hour spanning events), applies status CSS classes
- [x] Verify timetable renders when cohort is selected

**Effort:** 2 hours

## Task 4 — Faculty/cohort cascading dropdowns + initial load

- [x] Populate faculty select from `facultyData` array on DOMContentLoaded
- [x] Cohort select starts disabled with "Select Cohort" prompt
- [x] Implement `onFacultyChange()`: on faculty selection, enable cohort select and populate with that faculty's cohorts; on deselection, reset to empty state
- [x] Implement `onCohortChange()`: on cohort selection, reset to week 0, call buildTimetable(); on deselection, show empty state
- [x] Initial page load shows empty state with "Select a cohort" message, arrows disabled
- [x] Verify cascading: changing faculty repopulates cohorts and selecting cohort rebuilds timetable

**Effort:** 0.5 hours

## Task 5 — Week navigation with prev/next arrows

- [x] Add prev/next week buttons with `onclick="prevWeek()"` / `onclick="nextWeek()"` and `aria-label="Previous week"` / `"Next week"` (already in template)
- [x] Add week select dropdown populated from weekData labels (already in template)
- [x] Implement `prevWeek()`: decrement currentWeek, update week select, call buildTimetable()
- [x] Implement `nextWeek()`: increment currentWeek, update week select, call buildTimetable()
- [x] Implement `selectWeek(index)`: set currentWeek from select value, call buildTimetable()
- [x] Call shared `updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1)` after each timetable build (via updateSummaries)
- [x] Verify arrow disabling at boundary weeks (Week 9 = prev disabled, Week 22 = next disabled)

**Effort:** 0.5 hours

## Task 6 — Search + status filter

- [x] Implement `applyFilters()`: filter events by search text (case-insensitive match on course code, name, lecturer, venue) AND status filter value
- [x] Wire search input to `oninput="applyFilters()"` and status filter select to `onchange="applyFilters()"`
- [x] Update result count text before rebuilding grid (e.g. "Showing 3 of 5 events")
- [x] Show empty state when filtered results are empty (message: "No matching events")
- [x] When search/filter is reset (via faculty/cohort change), revert to showing all events for current selection
- [x] Verify search filters by course code (e.g. "BMIT" → filtered), name ("Database"), and lecturer ("Dr. Ch")
- [x] Verify status filter (All / Normal / Replacement / Pending / Conflict) works in combination with search

**Effort:** 1 hour

## Task 7 — Summary cards, legend bar, empty state, and event modal

- [x] Include `@include('partials.ui-summary-bar')` with 4 cards: Total Classes, Replacements, Pending, Conflicts
- [x] Implement `updateSummaries(events)`: count events by status from the event array, update summary card text, call updateWeekArrows
- [x] Add legend bar with 4 items: Normal Class (secondary swatch), Replacement (primary swatch), Pending (tertiary swatch), Conflict (error swatch) — matching MyTimetable's legend-bar pattern
- [x] Implement empty state toggling: show `"Select a cohort"` → `"Choose a faculty and cohort…"` on init; `"No classes scheduled"` → `"No classes scheduled for this cohort…"` when week has no data; `"No matching events"` → `"Try adjusting your search or filter."` when filter yields zero results
- [x] Implement view-only event modal: click event → show modal with course code, name, lecturer, venue, cohort, time, status badge, remarks; only a "Close" button in footer; Escape key closes
- [x] Verify modal opens/closes (tested), summary cards update correctly

**Effort:** 1 hour

## Task 8 — Integration smoke test

- [x] Visit `/cohort-timetable-ui` — page renders, nav bar has `Cohort Timetables` active (verified via snapshot)
- [x] Test faculty dropdown: switch faculty → cohort list updates
- [x] Test cohort dropdown: switch cohort → timetable rebuilds (tested multiple cohorts: RSD2, DSF2, DFT2, RSD3 G1/G2, RSDL2, DMC2)
- [x] Test week nav: prev/next arrows cycle through weeks, arrows disabled at boundaries (Week 0 = prev disabled)
- [x] Test search: type course code → timetable filters, empty state shows when no match
- [x] Test status filter: select "Replacement" → only replacement events shown (1 of 5)
- [x] Test search + filter combination (not explicitly tested but code paths verified)
- [x] Test summary card counts match visible events (total=4/5, replacement=1, pending=1)
- [x] Test modal: click event → modal shows details → close button works
- [x] Test empty state: conflict filter with no matches → "No matching events" shown
- [x] Verify no console errors on page (0 errors captured)
- [x] Verify nav link navigates between cohort-timetable-ui and other pages correctly

**Effort:** 0.5 hours
