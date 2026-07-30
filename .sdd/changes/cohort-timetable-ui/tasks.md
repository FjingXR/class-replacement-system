# Tasks: Cohort Timetable UI

## Task 1 — Create Blade template shell with page structure

- [ ] Create `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` with:
  - `@extends('layouts.ui-template', ['activeNav' => 'cohort-timetables'])`
  - `@section('title', 'Cohort Timetable — Class Replacement System')`
  - `@section('page-styles')` — page-specific CSS: semester bar, week-arrow, week-select, session-text, time column widths, legend bar, `.summary-card.card-total` styling, status badge classes (`.badge-normal`, `.badge-replacement`, `.badge-pending`, `.badge-conflict`)
  - `@section('content')` — page-header (title + desc), semester bar (faculty select + cohort select + week arrows + week select + session text), toolbar (search input + status filter + result count), grid-wrapper (grid-scroll > timetable > thead + tbody), legend bar, empty-state div, event modal (view-only with close button only)
  - `@section('page-scripts')` — placeholder/stub script block (actual function implementations added incrementally in Tasks 3–7)
- [ ] Verify template compiles (no Blade errors)
- [ ] Verify shared nav bar renders when visiting route

**Effort:** 1.5 hours

## Task 2 — Route and nav bar wiring

- [ ] Add route in `routes/web.php`:
  ```php
  Route::get('/cohort-timetable-ui', function () {
      return view('ui-design-templates.CohortTimetable-UI-design-template', ['activeNav' => 'cohort-timetables']);
  });
  ```
- [ ] Update `resources/views/partials/ui-nav-bar.blade.php` — change `Cohort Timetables` href from `#` to `/cohort-timetable-ui` (line 9)
- [ ] Verify nav link navigates to page and highlights with `active` class

**Effort:** 0.5 hours

## Task 3 — Mock data (facultyData, weekData) and buildTimetable()

- [ ] Add `weekData` array (3 weeks: Week 9–11, same structure as MyTimetable) with day arrays including `abbr`, `date`, optional `today`/`holiday`/`sunday` flags
- [ ] Add `facultyData` object: `{ facultyKey: { label, cohorts: { cohortKey: { label, weeks: { 0: [events], 1: [events], 2: [events] } } } } }`
  - FOCS with 3 cohorts (DFT2 diploma, DSF2 diploma, RSD2 degree) — 5–7 events/week each
  - FOE with 2 cohorts (EEE2, MEC2) — 5–7 events/week each
  - FOB with 2 cohorts (BAF2, IBM2) — 5–7 events/week each
  - FAFB with 2 cohorts (AFT2, ACC2) — 5–7 events/week each
- [ ] Events follow MyTimetable's structure: `{ di, start, end, code, type, venue, lecturer, cohort, status, name, remarks }`
- [ ] Implement `buildTimetable()` — reads data for current faculty+cohort+week, renders grid head (day columns) + body (hour rows with event blocks), applies badge CSS classes
- [ ] Verify timetable renders on page load

**Effort:** 2 hours

## Task 4 — Faculty/cohort cascading dropdowns + initial load

- [ ] Populate faculty select from `Object.keys(facultyData)` on DOMContentLoaded
- [ ] Populate cohort select from first faculty's cohorts
- [ ] Implement `onFacultyChange()`: repopulate cohort dropdown, reset search/filter, select first cohort, rebuild timetable
- [ ] Implement `onCohortChange()`: reset search/filter, rebuild timetable, update summaries
- [ ] On DOMContentLoaded: set currentFaculty to first key, currentCohort to first cohort, `currentWeek = 2` (most recent), populate selects, call buildTimetable()
- [ ] Verify cascading: changing faculty repopulates cohorts and rebuilds timetable

**Effort:** 0.5 hours

## Task 5 — Week navigation with prev/next arrows

- [ ] Add prev/next week buttons with `onclick="prevWeek()"` / `onclick="nextWeek()"` and `aria-label="Previous week"` / `"Next week"`
- [ ] Add week select dropdown populated from weekData labels
- [ ] Implement `prevWeek()`: decrement currentWeek, update week select, call buildTimetable()
- [ ] Implement `nextWeek()`: increment currentWeek, update week select, call buildTimetable()
- [ ] Implement `selectWeek(index)`: set currentWeek from select value, call buildTimetable()
- [ ] Call `updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1)` after each timetable build
- [ ] Verify arrow disabling at boundary weeks (Week 9 = prev disabled, Week 11 = next disabled)

**Effort:** 0.5 hours

## Task 6 — Search + status filter

- [ ] Implement `applyFilters()`: filter events by search text (case-insensitive match on course code, name, lecturer) AND status filter value
- [ ] Wire search input to `oninput="applyFilters()"` and status filter select to `onchange="applyFilters()"`
- [ ] Update result count text after filtering
- [ ] Show empty state when filtered results are empty (message: "No events match your search criteria.")
- [ ] When search/filter is reset (via faculty/cohort change), revert to showing all events for current selection
- [ ] Verify search filters by course code, name, and lecturer simultaneously
- [ ] Verify status filter (All / Normal / Replacement / Pending / Conflict) works in combination with search

**Effort:** 1 hour

## Task 7 — Summary cards, legend bar, empty state, and event modal

- [ ] Include `@include('partials.ui-summary-bar')` with 4 cards: Total Classes, Replacements, Pending, Conflicts
- [ ] Implement `updateSummaries(events)`: count events by status from the filtered event array, update summary card text
- [ ] Add legend bar with 4 items: Normal Class (green), Replacement (amber), Pending (blue), Conflict (red) — matching MyTimetable's legend-bar pattern
- [ ] Implement empty state toggling: show `"No classes scheduled for this cohort in the selected week."` when cohort has no data for current week; show `"No events match your search criteria."` when filter/search returns zero results from existing data; hide empty state when data is available
- [ ] Implement view-only event modal: click event → show modal with course code, name, lecturer, venue, cohort, time, status badge, remarks; only a "Close" button in footer
- [ ] Verify modal opens/closes, summary cards update correctly

**Effort:** 1 hour

## Task 8 — Integration smoke test

- [ ] Visit `/cohort-timetable-ui` — verify page renders, nav bar has `Cohort Timetables` active
- [ ] Test faculty dropdown: switch faculty → cohort list updates, timetable rebuilds
- [ ] Test cohort dropdown: switch cohort → timetable updates
- [ ] Test week nav: prev/next arrows cycle through weeks, arrows disabled at boundaries
- [ ] Test search: type course code → timetable filters, empty state shows when no match
- [ ] Test status filter: select "Replacement" → only replacement events shown
- [ ] Test search + filter combination
- [ ] Test summary card counts match visible events
- [ ] Test modal: click event → modal shows details → close button works
- [ ] Test empty state: select a faculty/cohort that has no data for a week
- [ ] Verify theme toggle works, logo navigates to /
- [ ] Verify no console errors on page
- [ ] Verify nav link navigates between cohort-timetable-ui and other pages correctly

**Effort:** 0.5 hours
