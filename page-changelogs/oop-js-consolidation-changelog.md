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
