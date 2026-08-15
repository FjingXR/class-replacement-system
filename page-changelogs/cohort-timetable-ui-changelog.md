# Changelog — Cohort Timetable UI

## [2026-08-16] Today button now persists week selection

The "Today" button now saves the current week to `cohortTimetableState` (via a local `goToday()` wrapper that calls `weekNav.jumpToToday()` + `saveState()`), consistent with arrow/dropdown navigation. Previously, clicking Today would jump the view but not persist — a page refresh would revert to the old week.

### Files Changed

#### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `goToday()` | Added | New wrapper function that calls `weekNav.jumpToToday()`, syncs `currentWeek`, and calls `saveState()` — same pattern as `prevWeek()`/`nextWeek()`/`selectWeek()`. |
| Today button listener | Updated | Replaced `weekNav.initTodayBtn()` with a single listener that calls `goToday()`, ensuring `cohortTimetableState` is persisted. |

---

## [2026-08-15] Event hover tooltip shows lecturer instead of venue

The event-block tooltip previously showed `name · venue` (venue already on the block). It now shows the **lecturer** (`name · Dr. Christopher Lazarus`) — useful since the cohort is the page context and the lecturer isn't on the block. Uses the new `tooltipExtra(event)` option on the shared `buildTimetableGrid`.

### Files Changed

#### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `buildTimetable()` | Updated | Passes `tooltipExtra` returning `event.lecturer`. |

#### `public/js/ui-common.js` + `public/css/theme.css`

Shared: `buildTimetableGrid` now sets `dataset.tip2` from `cfg.tooltipExtra`; `.event-block::after` reads `attr(data-tip2)`.

---

## [2026-08-15] Class detail modal redesign: unified detail sheet

The Class Detail modal now uses the shared `DetailModal` "Detail Sheet" via the shared `openClassModal()` (single flat group, definition rows). Cohort extra field preserved. No page-specific changes needed.

---

## [2026-08-15] Page-specific summary card descriptions

Added page-specific `description` text to each summary card (Total Classes / Teaching Hours / Replacements / Pending / Conflicts) instead of relying on the shared generic descriptions. (`CohortTimetable-UI-design-template.blade.php` summary bar.)

---

## [2026-08-13] Phase 3 UX Enhancement: Collapsible Guide Block

### Summary

Added an expandable guide block with page-specific workflow instructions.

### Files Changed

#### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 41-48 | Added | `@include('partials.ui-guide-block')` with 4 workflow tips |

---

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

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` — UI refinements (search removal, legend reorder, faculty-first flow)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `@section('page-styles')` | Removed search/filter CSS | Deleted `.search-wrapper`, `.search-icon`, `.search-input`, `.search-input::placeholder`, `.filter-select`, `.filter-select option`, `html.dark .filter-select` — ~50 lines |
| 2026-08-01 | `@section('page-styles')` | Added disabled select CSS | `.semester-bar select:disabled, select:disabled:hover` — opacity 0.5, `cursor: not-allowed`, surface-variant bg — clear visual cue that cohort/week controls are locked until a faculty is chosen |
| 2026-08-01 | `@section('page-styles')` | Responsive cleanup | Removed `.toolbar-left`, `.search-input`, `.filter-select`, `.search-wrapper` rules from both `@media` breakpoints; `.toolbar-right` left-aligned at 1024px |
| 2026-08-01 | `@section('content')` | Removed search bar & status filter | Toolbar-left (search wrapper + filter select) deleted entirely; toolbar now contains only the result-count span in `.toolbar-right` |
| 2026-08-01 | `@section('content')` | Legend above summary bar | Moved `.legend-bar` block to directly after grid-wrapper, before the `@include('partials.ui-summary-bar')` — legend now sits above the summary cards |
| 2026-08-01 | `@section('content')` | Faculty-first disabled states | `#cohortSelect` and `#weekSelect` now render with `disabled` attribute in HTML; prev/next arrows also start disabled — user cannot pick a cohort or week before choosing a faculty |
| 2026-08-01 | `@section('content')` | Guidance empty state | Default empty state text changed from "No classes scheduled" → title "Select a faculty first" + "Choose a faculty, then pick a cohort to view its weekly timetable." |
| 2026-08-01 | `@section('page-scripts')` | Removed applyFilters() | Entire `applyFilters()` (~140 lines) deleted along with its search/status-filter wiring (`oninput="applyFilters()"`, `onchange="applyFilters()"`) — no filtering on this page |
| 2026-08-01 | `@section('page-scripts')` | onFacultyChange() | Now also disables `#weekSelect`; when a faculty is picked, enables `#cohortSelect` only, keeps week locked, shows guidance "Pick a cohort from {faculty name}…". On reset → re-disables cohort + week, restores initial guidance |
| 2026-08-01 | `@section('page-scripts')` | onCohortChange() | Now enables `#weekSelect` only after a cohort is selected; on reset → disables week, shows guidance |
| 2026-08-01 | `@section('page-scripts')` | buildTimetable() | No-cohort branch also disables `#weekSelect` before showing guidance |
| 2026-08-01 | `@section('page-scripts')` | Init | DOMContentLoaded now explicitly disables `#cohortSelect` + `#weekSelect` and shows "Select a faculty first" guidance |

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` — semester chip, today column, maximized FOCS mock data, 2 faculties

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `@section('page-styles')` | Added `.semester-chip` | Replaced `.session-text` CSS with `.semester-chip` — inline-block pill (secondary-container bg, 12px, weight 600, radius 20px) for the semester info in the page header |
| 2026-08-01 | `@section('content')` | Semester info moved to header | Removed `202605 Semester (Monday, 15-Jun-2026 ~ Sunday, 20-Sep-2026)` `.session-text` span from semester bar; added `<span class="semester-chip">202605 Semester · 15-Jun-2026 ~ 20-Sep-2026</span>` under the page title |
| 2026-08-01 | `@section('page-scripts')` | Fixed today/holiday week bug | weekData generator used `w === 11` (which maps to **Week 19** since label = w+8) — corrected to `w === 3` (Week 11, index 2). Today column + public holiday now render on the correct week |
| 2026-08-01 | `@section('page-scripts')` | Default week → Week 11 | `currentWeek = 2` (was 0) and `onCohortChange()` selects week index 2 — matches MyTimetable, so the today column is immediately visible on cohort select |
| 2026-08-01 | `@section('page-scripts')` | facultyData reduced to 2 | Removed FOL (4 cohorts) and FOD (4 cohorts); kept FOCS (5 cohorts) + FCCI (reduced to 2 cohorts: DMC2, DIT2) = 7 cohorts total (was 17) |
| 2026-08-01 | `@section('page-scripts')` | Maximized FOCS mock data | All 5 FOCS cohorts now have 3 full weeks of data (weeks 0/1/2): RSD2 (6/6/7 events, 6 courses), RSD3 G1 (5/5/5), RSD3 G2 (5/5/5, shifted days), DSF2 (5/5/5), DFT2 (5/5/5). ~80 events total with replacement + pending statuses spread across weeks |
| 2026-08-01 | `@section('page-scripts')` | FCCI DMC2 data | Kept week 0 (3 events incl. pending) + added week 2 (3 events incl. replacement + pending) so the default week shows data; DIT2 left empty to demo the "No classes scheduled" state |

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` — new "Teaching Hours" summary card (staff)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `@section('page-styles')` | Added `.summary-card.card-hours` | Neutral styling distinct from count cards: `--color-on-surface` value, `--color-surface-variant` background, dashed `--color-outline-strong` border |
| 2026-08-01 | `@section('content')` | New summary card | Added `['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Teaching Hours']` after Total Classes — summary bar now 5 cards (grid already supports 5 columns) |
| 2026-08-01 | `@section('page-scripts')` | updateSummaries() | Computes `hours += (e.end - e.start + 1) * 0.5` per event (slots are 0.5h each); displays integer or 1-decimal (e.g. RSD2 Week 11 = 14h). All 4 reset branches now zero `sumHours` too |

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` — RSD3 G2 maximized to full semester (weeks 1–14, +45%)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `@section('page-scripts')` | RSD3 (S1) G2 — 5 → 7 courses (+45%) | Added `rsd3g2Base` array: existing 5 courses + BMIT7074 Software Testing (T, Tue 8-11, A106) + BMIT7075 Mobile Application Development (L, Thu 12-15, A106) |
| 2026-08-01 | `@section('page-scripts')` | RSD3 (S1) G2 — weeks 1–3 → weeks 1–14 | Replaced 3 explicit week blocks with a `for` loop generating all 14 dropdown weeks (indexes 0–13 = Week 9…22) → every week now shows 7 events / 14h |
| 2026-08-01 | `@section('page-scripts')` | RSD3 (S1) G2 — status flags | `rsd3g2Flags` map sets realistic replacement/pending statuses per week (e.g. W1 Capstone+Testing replaced, W2 IT Ethics pending, W4/W9/W13 pending, W3/W7/W11 replacement) — visible on non-holiday days; Thu events on the Week 11 public-holiday render as conflicts by design |
| 2026-08-02 | `@section('page-scripts')` | Centralised mock data (Task 9) | Migrated facultyData, allEvents, and weekData to `public/js/mock-data.js` — page now reads from `window.MockData.cohortTimetable`. RSD3 G2 reconstructed from shared data via `rsd3g2Base`/`rsd3g2Flags`. Holiday rule now reads `MockData.holidays` instead of hardcoded map. Semester chip text reads `MockData.semester.chipText`. |
| 2026-08-02 | `@section('page-scripts')` lines 660-683 | Fix RSD3 G2 reconstruction after centralisation | Three bugs: (1) Only flagged weeks created → week 0 (default) had no events. Fix: loop all 14 weeks first. (2) `rsd3g2Base` lacks `status` field → `event-normal` class not applied → transparent background. Fix: default `status: 'normal'`. (3) `rsd3g2Flags` format is `[code, status, date]` tuples but code treated entries as numeric indices → ghost entries with no code/status. Fix: match by `evt.code === flagCode` and set status/remarks in-place |

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` — selection persists across refresh (localStorage)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `@section('page-scripts')` | `saveState()` | Writes `{ faculty, cohort, week }` to `localStorage` key `cohortTimetableState` (try/catch — safe when storage unavailable) |
| 2026-08-01 | `@section('page-scripts')` | `restoreState()` | On DOMContentLoaded: re-validates saved faculty/cohort against `facultyData` (graceful fallback if a faculty/cohort was removed), re-selects via `onFacultyChange()`/`onCohortChange()`, then applies saved week — else normal empty-state flow |
| 2026-08-01 | `@section('page-scripts')` | saveState() wired in | Called at the end of `onFacultyChange()` (both branches), `onCohortChange()` (both branches), `selectWeek()`, `prevWeek()`, `nextWeek()` — any selection change is persisted |
| 2026-08-01 | `@section('page-scripts')` | Init | `restoreState()` called last in DOMContentLoaded (after empty-state setup, which it overrides). localStorage = longest lifespan (survives refresh + browser restarts) |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 | JS lines 660-683 | Fix RSD3 G2 reconstruction | (1) Populate all 14 weeks with base events first (was only flagged weeks → week 0 missing). (2) Default `status: 'normal'` on base copies (rsd3g2Base lacks status → event-normal class not applied → no color). (3) Apply flags by matching code instead of numeric index (flags are `[code, status, date]` tuples, not indices → ghost entries with no code) |
| 2026-07-30 | Line 9 | Nav link | Changed `Cohort Timetables` href from `#` → `/cohort-timetable-ui`, active class on `activeNav === 'cohort-timetables'` |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | Line 31 (before auth group) | New route | `GET /cohort-timetable-ui` → `view('ui-design-templates.CohortTimetable-UI-design-template')` with `activeNav` |

### `.sdd/changes/cohort-timetable-ui/tasks.md`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-30 | All sections | Task completion | All 8 task sections marked complete with implementation details and verified test results |

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` — OOP Phase 1 partial extraction

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-04 | — | Refactored: replaced inline page-header/week-nav/empty-state/grid-table/modal with `@include('partials.…')` (OOP Phase 1) | Page uses `ui-page-header`, `ui-week-nav`, `ui-grid-table`, `ui-empty-state`, `ui-class-detail-modal` partials. |
| 2026-08-13 | `public/css/theme.css` (shared) | macOS table fix | `.timetable`: `border-collapse:collapse` → `separate` + `border-spacing:4px`; removed 1px cell borders; hover uses `var(--color-surface-variant)`; removed zebra striping. `.badge` border-radius 6px→8px. |
| 2026-08-14 | `@section('page-styles')` | Offday-slot today-cell fix | Added `.today-cell.offday-slot { background: transparent; }` override so PH/Sunday empty cells are not red when today. |
| 2026-08-14 | `prevWeek/nextWeek/selectWeek` | WeekNavigator delegation | Local nav logic replaced with `weekNav.prevWeek()/nextWeek()/selectWeek()`; `buildTimetable()` syncs `currentWeek = weekNav.currentWeek`; `saveState()` kept after each nav. |
