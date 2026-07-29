## proposal.md Round 1 — 2026-07-29

### 🔴 Fixed
1. **`$activeNav` wording imprecise** — reworded from `$activeNav = 'cohort-timetables'` to `['activeNav' => 'cohort-timetables']` view-data convention in items 1 and 6
2. **No initial-load / empty-state specified** — added explicit block describing first-load behavior (first faculty's first cohort), empty-data state message, and search-no-results empty state
3. **Faculty-cohort dropdown interaction ambiguous** — specified cascading behavior (faculty selection filters cohorts) and listed mock faculties + cohort examples
4. **Search/filter underspecified** — specified searchable fields (course code, name, lecturer) and filter dropdown (status: All/Normal/Replacement/Pending/Conflict), with reference to replacement-home-ui's toolbar pattern

### 🟡 Addressed
5. **Summary bar partial** — item 8 now explicitly says `@include('partials.ui-summary-bar')` with 4 cards
6. **Missing page title** — item 2 now includes `@section('title', 'Cohort Timetable — Class Replacement System')`
7. **Nav bar scope omits activeNav** — items 1 and 6 now show the `['activeNav' => 'cohort-timetables']` in route
8. **Mock data shape** — item 4 now describes data follows MyTimetable's `timetableData` structure with fields per week, 5–7 events daily
9. **No explore-brief** — acknowledged; not a blocker for a simple UI template

### 🔴 Outstanding
- (none)

### ✅ PASS — proposal.md is frozen.

## design.md Round 1 — 2026-07-29

### 🔴 Fixed
1. **Status badge CSS classes don't exist in codebase** — design.md Section 7 claimed `.status-normal` etc. as "same CSS classes as other pages". Changed to define new page-specific `.badge-normal`, `.badge-replacement`, `.badge-pending`, `.badge-conflict` in `@section('page-styles')` with explicit color conventions.
2. **Faculty/cohort switching doesn't specify search/filter reset** — design.md Section 3 now explicitly states search input is cleared and status filter reset to "All" on both faculty and cohort change.
3. **Event data model omits `cohort` field** — Added `cohort: 'DFT2 (S1)'` to the event object in Section 2, so modal's Cohort field has a direct data source.

### 🟡 Addressed
- (none)

### 🔴 Outstanding
- (none)

### ✅ PASS — design.md is frozen.

## tasks.md Round 1 — 2026-07-29

### 🔴 Fixed
1. **prevWeek/nextWeek logic inverted** — Task 5: changed `prevWeek()` from "increment" → "decrement", `nextWeek()` from "decrement" → "increment" to match frozen design Section 5
2. **Mock data event counts too low** — Task 3: changed all faculties from 2-4 events to 5-7 events per cohort per week, matching frozen proposal item 4
3. **Empty-state messages ambiguous** — Task 7: now explicitly states both messages with their trigger conditions (no-data vs no-search-results)
4. **Task 1 JS function list conflict with Tasks 3-7** — Task 1: replaced "all JS functions" list with "placeholder/stub script block" to avoid ambiguity
5. **Duplicate summary-bar mention** — Task 1: removed `@include('partials.ui-summary-bar')` from content structure (Task 7 owns this)

### 🟡 Addressed
- (none)

### 🔴 Outstanding
- (none)

### ✅ PASS — tasks.md is frozen.
