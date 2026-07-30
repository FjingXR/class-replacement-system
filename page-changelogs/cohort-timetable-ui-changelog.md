# Changelog — Cohort Timetable UI

## Files Changed

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` (new)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | — | Created file | Full Blade template extending `layouts.ui-template` with `activeNav = 'cohort-timetables'`. 1323 lines covering CSS, content, and JS |

#### CSS (`@section('page-styles')`)
| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | — | Semester bar | `.semester-bar` (flex, surface bg, border, gap 8px), `.week-arrow` (28×28, transparent → hover bg, disabled state), `.week-select` (secondary-container bg, custom SVG chevron, min-width 160px), `.session-text` (flex 1, weight 600) |
| 2026-07-30 | — | Faculty/cohort selects | `.semester-bar select:not(.week-select)` — same styling as week-select but larger padding for standalone selects |
| 2026-07-30 | — | Time column | `.time-col` (130px, sticky left, z-15, text-align center), `.today`/`.holiday-col`/`.sunday-col` variants matching MyTimetable, `.hour-header` with `.hour-top`/`.hour-bottom` spans |
| 2026-07-30 | — | Hour cells | `.hour-cell` (80px height, cursor default), `.sunday-slot`/`.holiday-slot` (error-container bg), `.cell-empty` (surface bg) |
| 2026-07-30 | — | Event blocks | `.event-block` (flex col, centered, hover brightness, box-shadow), `.event-normal` (secondary-container), `.event-replacement` (primary-container), `.event-pending` (tertiary-container), `.event-public-holiday` (error-container), `.ev-code`/`.ev-venue`/`.ev-time`/`.ev-note` text styles |
| 2026-07-30 | — | Status badges | `.badge-normal` (secondary bg), `.badge-replacement` (amber/gold), `.badge-pending` (tertiary), `.badge-conflict` (error) — for use in modal and event labels |
| 2026-07-30 | — | Legend bar | `.legend-bar` (50px, primary-container bg, flex row, 28px gap, border-top), `.legend-item`/`.legend-swatch` (18×18, border, border-radius) |
| 2026-07-30 | — | Summary card colors | `.card-total` (border 2px primary + primary-container bg), values: total=secondary, replacement=primary, pending=tertiary, conflict=error |
| 2026-07-30 | — | Modal | `.modal-overlay` (fixed, blur, centered), `.modal` (surface bg, border-radius 16px, slide-in animation), header (title + status badge + close), body (`.modal-field` label/value rows), `.btn-close-modal` |
| 2026-07-30 | — | Search toolbar | `.search-wrapper` (relative for icon), `.search-input` (padding 8-12, 36px left for icon, 420px width), `.filter-select` (secondary-container bg, weight 500) |
| 2026-07-30 | — | Responsive | `@media (max-width: 1024px)` — scrollable grid, stacked toolbar. `@media (max-width: 768px)` — full-width search, hidden user info |

#### Content (`@section('content')`)
| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | — | Page header | Title "Cohort Timetable" + desc "View the weekly timetable for any cohort across all faculties." |
| 2026-07-30 | — | Semester bar | Faculty select → cohort select → prev arrow → week select → next arrow → session text showing "202605 Semester (Monday, 15-Jun-2026 ~ Sunday, 20-Sep-2026)" |
| 2026-07-30 | — | Toolbar | Search wrapper with SVG magnifying glass icon + text input + status filter select (All/Normal/Replacement/Pending/Conflict) + result count |
| 2026-07-30 | — | Grid | `.grid-wrapper` → `.grid-scroll` → `table.timetable` with `#tableHead` + `#tableBody` — populated dynamically by JS |
| 2026-07-30 | — | Summary bar | `@include('partials.ui-summary-bar')` with 4 cards: Total Classes, Replacements, Pending, Conflicts |
| 2026-07-30 | — | Legend bar | 4 legend items with colored swatches using CSS variables (`--color-secondary`/`--color-primary`/`--color-tertiary`/`--color-error`) |
| 2026-07-30 | — | Empty state | Hidden by default (`display:none`), shown via JS. Calendar SVG icon, dynamic title + message |
| 2026-07-30 | — | Event modal | Overlay + modal card: header (course code + status badge + close ×), body fields (Course, Name, Lecturer, Venue, Cohort, Time, Status badge, Remarks), footer with Close button |

#### JavaScript (`@section('page-scripts')`)
| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | — | Helpers | `hours` array (22 slots 08:00–18:30), `add30min(t)`, `dayNames` (MONDAY–SUNDAY) |
| 2026-07-30 | — | weekData | Dynamic 14-week generator (15-Jun → 20-Sep 2026), each with 7-day array (`abbr`, `date`, `sunday`, `today` at week 11 Mon, `holiday` at week 11 Thu) |
| 2026-07-30 | — | facultyData | 4 faculties: FOCS (5 cohorts: RSD2, RSD3 G1/G2, DSF2, DFT2), FOL (4: RSDL2 G1/G2, DLF2, DLM2), FOD (4: RSD2/3 Design, DDM2, DFM2), FCCI (4: RBU2, DMC2, DIT2, DCM2) — 17 cohorts total |
| 2026-07-30 | — | Mock events | ~40 events across 10+ cohorts, indexed by `allEvents[cohortId][weekIdx]`. Includes normal (green), replacement (amber), and pending (blue) events with multi-hour spans. Lecturers, venues, and codes for realism |
| 2026-07-30 | — | populateWeeks() | Fills week select from weekData array |
| 2026-07-30 | — | populateFaculties() | Fills faculty select from facultyData |
| 2026-07-30 | — | onFacultyChange() | Reads faculty select → populates cohort select with matching cohorts; on reset → shows empty state, clears grid, disables arrows |
| 2026-07-30 | — | onCohortChange() | Reads cohort select → resets to week 0 → calls buildTimetable(); on reset → clears grid, shows empty state |
| 2026-07-30 | — | prevWeek()/nextWeek() | Bounds-checked navigation, updates week select UI |
| 2026-07-30 | — | selectWeek(index) | Sets current week from dropdown, rebuilds grid |
| 2026-07-30 | — | buildTimetable() | Main renderer: reads `allEvents[selectedCohortId][currentWeek]`, builds header row + 7 day rows, uses slot-map for multi-hour spanning events, applies CSS classes per status, calls updateSummaries + updateWeekArrows |
| 2026-07-30 | — | applyFilters() | Filters visible events by search query (code/name/lecturer/venue) AND status filter. Rebuilds grid with filtered events, shows "No matching events" when empty |
| 2026-07-30 | — | updateSummaries() | Counts total/replacement/pending/conflict events, updates summary card text, calls updateWeekArrows |
| 2026-07-30 | — | openModal()/closeModal() | View-only modal: fills course/name/lecturer/venue/cohort/time (with day+date+12h range)/status badge/remarks. Status badge uses `.badge-*` classes. Escape key closes |
| 2026-07-30 | — | Init | DOMContentLoaded: populate weeks + faculties, show empty state "Select a cohort", arrows disabled |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | Line 9 | Nav link | Changed `Cohort Timetables` href from `#` → `/cohort-timetable-ui`, active class on `activeNav === 'cohort-timetables'` |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | Line 31 (before auth group) | New route | `GET /cohort-timetable-ui` → `view('ui-design-templates.CohortTimetable-UI-design-template')` with `activeNav` |

### `.sdd/changes/cohort-timetable-ui/tasks.md`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | All sections | Task completion | All 8 task sections marked complete with implementation details and verified test results |
