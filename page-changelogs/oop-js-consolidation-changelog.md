# Changelog — OOP JS Consolidation (Phase 1)

## Files Changed

### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | — | Extended | Grew from 5 → 17 shared functions. Added `updateWeekArrows(prevDisabled, nextDisabled)`, `hours` const (08:00–18:30 half-hour slots), `add30min(t)`, `goToReplacement()` (→ `/replacement-arrangement`), `compareBy(sortState, va, vb)`, `makeSortableHeader(col, sortState, render)`, `paginate(cfg)`, `updateResultCount(cfg)`, `closeOnEsc(closeFn)`, `closeOnOverlayClick(e, closeFn)`, `togglePassword()`, `ripple(e, btn)` — extracted from inline copies across templates |

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `<script>` | Removed duplicates | Removed inline `hours` const + `add30min(t)` (now shared from ui-common.js) |
| 2026-08-01 | `<script>` | Fixed shadowing bug | Removed local `to12h(t)` that shadowed the shared version with different minute padding — page now uses the shared `to12h` |
| 2026-08-01 | `<script>` | Modal helpers | Replaced inline `closeModalOutside` + raw `keydown` Escape listener with shared `closeOnEsc(closeModal)` + `closeOnOverlayClick(e, closeModal)` |
| 2026-08-01 | `<script>` | Removed duplicate | Removed local `goToReplacement()` (shared version used) |
| 2026-08-01 | `<style>` | Week arrows | Added `.week-arrow:disabled` styles (opacity 0.3, cursor not-allowed, transparent hover) |

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `<script>` | Removed duplicates | Removed inline `hours` const + `add30min(t)` (now shared) |
| 2026-08-01 | `<script>` | Removed duplicate | Removed local `formatHour(h)` — call site now uses shared `to12h(startStr)` |
| 2026-08-01 | `<script>` | Fixed shadowing bug | `navigateHome()` was shadowing the shared version and sent users to `/my-timetable-ui`; local wrapper now calls `navigateTo('/')` (kept local because `navigateTo` has the unsaved-selections guard) |
| 2026-08-01 | `<script>` | Week arrows | Removed inline `updateWeekButtons()`; `buildTimetable()` now calls shared `updateWeekArrows(prevDisabled, nextDisabled)` (args reversed vs my-timetable because this page's `weekData` is newest-first) |
| 2026-08-01 | `<style>` | Week arrows | Added `.week-arrow:disabled` styles (same as my-timetable) |

### `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `<script>` | Pagination state | `let currentPage` → `const pageState = { currentPage: 1 }` (contract for shared `paginate(cfg)`) |
| 2026-08-01 | `<script>` | Removed duplicates | Deleted inline `updatePagination()` + `updateResultCount()` — replaced with shared `paginate({ data, pageSize, state: pageState, infoId, controlsId, render })` + `updateResultCount({ elId, data, total, label })` |
| 2026-08-01 | `<script>` | Sorting | Inline sort comparator → shared `compareBy(sortState, va, vb)`; inline sortable-header builder → shared `makeSortableHeader(col, sortState, render)` |
| 2026-08-01 | `<script>` | Removed duplicate | Removed local `goToReplacement()` (shared version used) |

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `<script>` | Pagination state | `let currentPage` → `const pageState = { currentPage: 1 }` |
| 2026-08-01 | `<script>` | Removed duplicates | Deleted inline `updatePagination()` + `updateResultCount()` — replaced with shared `paginate(...)` + `updateResultCount({ elId, data, total, label })` |
| 2026-08-01 | `<script>` | Sorting | Inline sort comparator → shared `compareBy(sortState, va, vb)`; inline sortable-header builder → shared `makeSortableHeader(col, sortState, render)` |
| 2026-08-01 | `<script>` | Modal helpers | Replaced inline overlay-click + `keydown` Escape listeners with shared `closeOnEsc(closeModal)` + `closeOnOverlayClick(e, closeModal)` |

### `resources/views/auth/login-staff.blade.php` & `resources/views/auth/login-student.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `<script>` | Removed duplicates | Removed inline `togglePassword()` + `ripple(e, btn)` — now resolved from ui-common.js (staff/student login pages stay separate per requirement) |

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `@section('page-scripts')` | Compatibility fix | Removed duplicate `const hours` + `add30min(t)` (byte-identical to shared versions). Required: page loads ui-common.js via layout, and the duplicate `const hours` was a redeclaration SyntaxError that broke the whole page script |

## Verification

- All 7 pages checked with Playwright — 0 console errors: `/login/staff`, `/login/student`, `/my-timetable-ui`, `/replacement-arrangement`, `/replacement-home-ui`, `/my-request-history-ui`, `/cohort-timetable-ui`
- Regressed: password toggle + ripple on both login pages, sort arrows + pagination + result count on home & history, Escape/overlay modal close on history, week arrows on my-timetable & arrangement, cohort timetable grid + summary (uses shared `hours`/`add30min`)

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`, `CohortTimetable-UI-design-template.blade.php`, `replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `buildTimetable()` / `renderTimetable()` | Hour header blocks | Time header row changed from 22 half-hour columns to 11 one-hour blocks, each `th` spans 2 columns (`colSpan = 2`) labeled `08:00` / `09:00`, `09:00` / `10:00`, … `18:00` / `19:00` (start on top line, end hour on bottom line — no "to" text; last pair computed via `add30min()` since `19:00` is not in the `hours` array). Body cells still 30-min slots — header pairs cover them 2 at a time. Applied to all 3 timetable pages |
| 2026-08-01 | `<style>` | Header alignment | `.hour-header` selector upgraded to `.timetable thead th.hour-header` and given `text-align: center` — global `theme.css` `.timetable th { text-align: left }` (higher specificity) was overriding the centering on all 3 timetable pages |
| 2026-08-01 | `replacement-home-UI-design-template.blade.php` | Week picker | `#weekFilter` redesigned to match my-timetable's `#weekSelect` — replaced plain `.filter-select` with `.week-picker` group (`‹` `.week-arrow` + `.week-select` pill + `›`). Added local `.week-arrow`/`.week-select` CSS (copied from my-timetable). New JS: `prevWeekFilter()`/`nextWeekFilter()` cycle through options (dispatch change → `weekFilterChanged()` resets page + rebuilds table), `updateWeekArrowState()` uses shared `updateWeekArrows()` — prev disabled on "All Weeks", next disabled on last week. Verified: 0 console errors, filtering works per week, arrows disable at extremes |
| 2026-08-01 | `replacement-home-UI-design-template.blade.php` | Week option labels | `populateWeekDropdown()` now renders `Week N · 31 Aug 2026 ~ 6 Sep 2026` style labels (new `weekRangeLabel(weekNum)` helper computes Monday–Sunday range from semester start `2026-08-31`, formatted via shared `formatDate()`) — matches my-timetable's `#weekSelect` label format. "All Weeks" stays the default/first option. Verified: options show `Week 1 · 31 Aug 2026 ~ 6 Sep 2026` … `Week 4 · 21 Sep 2026 ~ 27 Sep 2026`, default value `all`, 0 console errors |

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php` & `CohortTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 | `weekData` | 14-week semester | Both pages now generate exactly 14 weeks (Mon 15-Jun-2026 → Sun 20-Sep-2026, matches the "202605 Semester" chip), labeled `Week 1` … `Week 14` with range `15 Jun 2026 ~ 21 Jun 2026` style. Cohort: label `Week ${w+8}` → `Week ${w}`. My-timetable: replaced 3 hardcoded weeks (old Week 9–11) with the generator; `eventsData` rekeyed `0,1,2` → `9,10,11` (old weeks 18/25-Aug/01-Sep now map to new weeks 10/11/12) |
| 2026-08-01 | `currentWeek` | Default = current week | New shared-local `currentWeekIndex()` computes the week containing today (`Math.floor((today − 15-Jun)/7)`, clamped 0–13) — today = 01-Aug-2026 → Week 7 (idx 6). Cohort also uses it when a cohort is selected (was hardcoded `2`); localStorage restore still overrides afterwards |
| 2026-08-01 | `weekData` | Dynamic today flag | `today: true` now computed from real date (`dt.getTime() === todayMidnight`) instead of hardcoded — Week 7's Saturday (01-Aug-2026) column is highlighted on my-timetable; verified via `weekData` inspection on both pages. Verified: 14 options both pages, default idx 6, arrows reach idx 0/13 with correct disabled states, 0 console errors |
| 2026-08-01 | `eventsData` (my-timetable only) | Weekly repeating schedule | Every week now shows the same normal classes, copied from Week 12 (index 11): new `weeklyTemplate` = `eventsData[11].filter(e => e.status === 'normal')` (9 events: OOP L, Data Structures T, Database Systems L, Computer Networks L, Software Engineering L, Mobile App Dev T, AI L, AI T, Project Management L) assigned to all other 13 weeks (`eventsData[i] = weeklyTemplate.slice()`). Week 12 keeps its full 12 events (incl. 2 replacement + 1 pending). Removed old weeks 9/10 hardcoded blocks (now redundant). Verified: weeks 1–11/13/14 = 9 events each, week 12 = 12, default current week (7) renders the schedule, 0 console errors |
| 2026-08-01 | `CohortTimetable-UI-design-template.blade.php` | Removed toolbar + result count | Deleted the `.toolbar` div with `#resultCount` ("Showing N of N events") from the content, plus all 5 JS lines setting `resultCount.textContent` (empty-state + buildTimetable). Page now shows semester-bar (faculty/cohort/week) directly above the grid. Verified: no `#resultCount`/`.toolbar` in DOM, weeks with events still render (7 rows), 0 console errors |
| 2026-08-01 | `MyTimetable-UI-design-template.blade.php` & `CohortTimetable-UI-design-template.blade.php` | Status naming unified | Legend + summary cards on My Timetable renamed to match Cohort Timetable: legend `Confirmed Replacement`/`Pending Approval`/`Public Holiday / Conflict` → `Replacement`/`Pending`/`Conflict`; summary cards `Confirmed Replacement`/`Pending Approval`/`Conflicts / Public Holiday` → `Replacements`/`Pending`/`Conflicts`. Cohort's event-cell note `(Pending Approval)` → `(Pending)` (its own legend said `Pending`). Cross-page check: arrangement page legend (`Available`/`Your Current Selection`/`Pending (You)`/`Reserved by Others`/`Occupied / Class on Public Holiday`) is venue-occupancy naming — intentionally left. Verified: legend/summary render new names, week 3 pending event shows `(Pending)` note, 0 console errors |
| 2026-08-01 | `MyTimetable-UI-design-template.blade.php` | Week persistence | New `myTimetableWeek` localStorage key — selected week survives refresh. `currentWeek` still defaults to today's week (`currentWeekIndex()`) on first use; once the user picks a week (`selectWeek`/`prevWeek`/`nextWeek`), `saveWeek()` persists it and `loadSavedWeek()` restores it in `DOMContentLoaded` before building the grid |
| 2026-08-01 | `CohortTimetable-UI-design-template.blade.php` | Week persistence fix | `restoreState()` re-calls `saveState()` after restoring the saved week. Before: `onCohortChange()` (invoked during restore) saved today's week, so the first refresh displayed the saved week but the *second* refresh fell back to today's week. Verified on both pages: fresh visit → today's week; select Week 4 → survives two consecutive refreshes; 0 console errors |
| 2026-08-01 | `replacement-arrangement-UIdesign-template.blade.php` | Legend moved above Selection Summary | `.legend` block relocated from after `.footer-area` (bottom of page) to directly above `#selSummary` — new order: toolbar → timetable → legend → Selection Summary → footer-area. Names untouched (venue-occupancy legend). Verified: legend renders above the summary panel with all 5 items, timetable unaffected (7 rows, cell statuses intact), 0 console errors |
| 2026-08-01 | `partials/ui-nav-bar.blade.php` & `student-my-timetable-UI-design-template.blade.php` | Remove Dashboard from nav | Removed `Dashboard` nav item from the shared top-bar/nav-drawer partial (staff pages) and student timetable page's `navItems` array. No longer shown in the header for either role |
| 2026-08-01 | `public/css/theme.css` & `partials/ui-nav-bar.blade.php` | User-panel session color | `.user-panel` background changes with auth session age: `< 30s → error-container` (red), `< 2 min → tertiary-container`, `≥ 2 min → primary-container` (default). CSS classes `.session-critical` / `.session-warning` added to theme.css. JS polls every 30 s and toggles the class. Mock: `authStart = Date.now() - 25s` so the red→green→primary transition is visible immediately. Comment left for backend to replace with real session-start timestamp |
| 2026-08-01 | `partials/ui-nav-bar.blade.php`, `routes/web.php`, `student-my-timetable-UI-design-template.blade.php` | Nav label dedupe (OOP) | The `navItems` array was duplicated in 3 places with an inconsistent label: shared nav partial default said `Replacement History`, but `routes/web.php` hardcoded a duplicate with `Request History` for `/my-request-history-ui` (student page had its own copy too). Dropped the route override (falls back to the shared partial default) and unified all labels to `Request History` — single source of truth. Verified: nav shows `Request History` on both `/my-request-history-ui` and `/replacement-home-ui`, 0 console errors |
| 2026-08-01 | `partials/ui-nav-bar.blade.php` & `public/css/theme.css` | Theme-toggle left tooltip | Added `data-tip="Switch between light & dark mode" data-tip-pos="left"` to the theme-toggle button. New CSS variant `[data-tip][data-tip-pos="left"]` positions the tooltip to the LEFT of the element (vertically centered, arrow pointing right) instead of above — this keeps it inside the `.top-bar`'s 56px height so it isn't clipped by `overflow: hidden` (the reason the earlier above-positioned logout tooltip was invisible). Reuses the shared CSS tooltip system |
| 2026-08-01 | `partials/ui-nav-bar.blade.php` | Nav order | Reordered shared nav items (left→right): My Timetable, Cohort Timetables, **Venue Timetable** (was last, now 3rd), Replacement Arrangement, Request History, **Request Approval** (was 4th, now last/6th). Applies to the shared default for all staff pages |
