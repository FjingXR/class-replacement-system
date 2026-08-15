# Changelog — Venue Timetable UI

## [2026-08-15] Detail modal: status description as its own row

Added a **Status Description** row ("Replacement request awaiting approval" / "Class booked for this venue") separate from the Status badge.

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `openModal()` | Updated | Split `Status` and `Status Description` rows. |

---

## [2026-08-15] Detail modal refinements: gray × close, split rows, status badge

### Summary

- **Close button** — normal gray `×`; removed header `modal-status-badge`.
- **Split rows** — Subject Code/Name, Start/End Time split; status as badge row with brief description.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Removed header `modal-status-badge`. |
| `openModal()` | Updated | Split rows, badge status + description. |

---

## [2026-08-15] Detail modal redesign + fix (was never opening)

### Summary

- **Fix**: the Booked-Class detail modal opened with `.classList.add('open')` but only `.modal-overlay.show` is styled — the modal never displayed. Now uses `DetailModal` (`.show`).
- **Redesign**: converts the static `modal-field` rows to the shared `DetailModal` "Detail Sheet" (identity header, single flat group, definition rows without per-row borders). Same data (Course/Name/Lecturer/Venue/Cohort/Time/Status/Remarks).

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Body emptied (JS-generated now). |
| `openModal()` | Rewritten | Uses `DetailModal.render`; fixed `.open` → `.show`. |
| `closeModal()` | Updated | Uses `DetailModal.close()`. |

---

## [2026-08-15] Page-specific summary card descriptions

### Summary

Added page-specific `description` text to each summary card (Total Slots / Available / Pending / Unavailable) instead of relying on the shared generic descriptions in `ui-summary-bar`.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Summary bar cards | Updated | Each card now passes a page-specific `description` (e.g. Unavailable = "booked class, Sunday, or public holiday"). |

---

## [2026-08-15] Summary: Sunday + Public Holiday folded into "Unavailable"

### Summary

The venue summary previously had an "Occupied" card while Sunday (`.cell-sun`) and Public Holiday (`.cell-ph`) cells were not counted at all — so `Total` did not cover the whole grid and the labels were misleading. `Occupied` + `Sunday` + `Public Holiday` are all "cannot book this slot", so they're now grouped under a single **Unavailable** card.

New cards: **Total Slots**, **Available**, **Pending**, **Unavailable** (= Occupied + Sunday + Public Holiday). `Total = Available + Pending + Unavailable` now exactly equals the grid cells. Legend updated to Available / Pending / Unavailable with a tooltip explaining the grouped reasons.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Summary bar cards | Updated | Replaced "Occupied" card with "Unavailable"; `valueId` `sumOccupied` → `sumUnavailable`. |
| Legend bar items | Updated | "Occupied" → "Unavailable" with tip `Cannot book — slot is booked, Sunday, or public holiday`. |
| `updateSummaries()` | Updated | `unavailable = occupied + sunday + ph`; `total = available + pending + unavailable`. |

---

## [2026-08-15] Summary stats now match the rendered grid (accurate counts)

### Summary

The summary cards were inaccurate in two ways:
1. **Occupied/Pending** counted event objects, not hour slots — a 3-hour booking showed as "1" instead of the 3 cells it occupies on the grid.
2. **Available** only counted Monday–Friday (`di < 5`), while the grid also renders Saturday as a working day, so `Total`/`Available` under-counted.

Now `updateSummaries()` counts exactly the cells the grid renders (`.cell-occupied`, `.cell-pending`, `.cell-available`), so the cards always match the timetable — including overlapping bookings from multiple cohorts that share an hour slot. Falls back to 0 when no venue is selected.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `updateSummaries()` | Rewritten | Counts rendered `.cell-content` cells (`occupied`/`pending`/`available`) directly from the `.timetable` DOM instead of re-deriving from `events`; this handles overlapping bookings and all working days (incl. Saturday). |

---

## [2026-08-13] Legend bar background fix

### Summary

Legend bar now has `background: var(--color-surface)` so swatches render on the same surface as timetable cells, ensuring color consistency.

### Files Changed

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|---|---|---|---|
| 2026-08-13 | `.legend-bar` | Updated | Added `padding`, `background: var(--color-surface)`, `border`, `border-radius` |

---

## [2026-08-13] OOP Refactor: Align cell-available with shared theme.css

### Summary

Removed duplicate `.cell-available` + hover CSS (now in `theme.css`). Simplified `.cell-content.cell-occupied` to remove redundant positioning (inherited from `.cell-content` in theme.css). Updated JS to wrap available cells in `.cell-content` class for consistency with replacement-arrangement.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 470-482 | Removed | `.cell-available` + hover (now in theme.css via `.cell-content` + `.cell-available`) |
| 2026-08-13 | Lines 485-495 | Simplified | `.cell-content.cell-occupied` — removed `position: absolute; top/left/right/bottom: 0;` (inherited from `.cell-content`) |
| 2026-08-13 | Line 1208 | Changed | `div.className = 'cell-available'` → `'cell-content cell-available'` |

---

## [2026-08-13] Phase 3 UX Enhancement: Legend Tooltips

### Summary

Added hover tooltips to all legend items describing each status.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 634-639 | Added | `tip` property to each legend item |

---

## [2026-08-13] Phase 3 UX Enhancement: Shared Legend Bar

### Summary

Replaced inline legend with shared `ui-legend-bar` partial using `$items` parameter.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 632-649 | Replaced | Inline legend → `@include('partials.ui-legend-bar', ['items' => [...]])` |
| 2026-08-13 | Line 634 | Changed | Legend swatch: `var(--color-primary)` → `var(--color-success)` |
| 2026-08-13 | Line 476 | Changed | `.cell-available` background: `var(--color-primary-container)` → `var(--color-success-container)` |

---

## [2026-08-13] Phase 3 UX Enhancement: Collapsible Guide Block

### Summary

Added an expandable guide block with page-specific workflow instructions.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 546-555 | Added | `@include('partials.ui-guide-block')` with 5 workflow tips |

---

## Files Changed

### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | — | Created page | New Blade template with venue dropdown, week picker, timetable grid, legend bar, summary cards, modal |
| 2026-08-09 | `@section('page-styles')` | Page-specific CSS | Venue dropdown, filter bar, segment toggle, venue type filter, booking banner, tab toggle, history panel, hint text, error banner, toast, print button, available tooltip, booked modal, keyboard hints, no-match banner, responsive styles |
| 2026-08-09 | `@section('content')` | HTML structure | Page header with print button, booking banner, error banner, no-match banner, venue + week picker, tab toggle, filter bar (time range + venue type), history panel, grid wrapper, hint text, legend bar, summary bar, empty state, mobile card list, detail modal, available tooltip, toast |
| 2026-08-09 | `@section('page-scripts')` | Render logic | State variables, week data builder, URL params reader, init function, venue dropdown builder (with recent/favourites), venue change handler, week nav, tab toggle, filter logic, venue events getter, timetable grid builder, mobile card builder, summary updater, modal (booked class), available tooltip, keyboard navigation, favourites localStorage, recent venues localStorage, state persistence, toast notification |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | After `request-approval-ui` route | New route | Added `Route::get('/venue-timetable-ui', ...)` returning view `ui-design-templates.venue-timetable-UI-design-template` |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | `$items` array | Nav item | Added "Venue Timetable" as 6th nav item with key `venue-timetable` and href `/venue-timetable-ui` |

### Design Updates (SDD enhancements)

| Timestamp | Change | Detail |
|-----------|--------|--------|
| 2026-08-08 | URL params support | Read `code` and `cohort` from URL when coming from My Timetable flow |
| 2026-08-08 | Booking banner | Show "Booking for: BMIT5555 — RSD3G2" when `code`+`cohort` present |
| 2026-08-08 | Code/cohort passthrough | Pass `code`+`cohort` through when "Book Now" is clicked |
| 2026-08-08 | Advanced features | Added: venue favourites, booking history, time/venue type filters, quick book shortcut, recent venues |
| 2026-08-09 | Skeleton loading | `withSkeleton()` on venue change, standard pattern |
| 2026-08-09 | Empty states | Hint text for no classes, no-match banner for filter |
| 2026-08-09 | Error handling | Error banner for MockData failure, toast for slot taken |
| 2026-08-09 | Print button | Disabled printer icon in header with "Coming soon" tooltip |
| 2026-08-09 | Booking history | Tab toggle for Current Week / Past 4 Weeks with history panel |
| 2026-08-09 | Quick book (B3) | Keyboard shortcut B on focused available cell |
| 2026-08-09 | Keyboard nav | Arrow keys, Enter, Escape navigation on grid |
| 2026-08-09 | Mobile view | Card layout for booked/available slots on ≤768px |
| 2026-08-09 | Tablet view | Reduced grid density on 769px–1024px |
| 2026-08-09 | UI Polish (A1) | Print icon moved to rightmost of semester-bar |
| 2026-08-09 | UI Polish (A2) | Timetable design matched to replacement-arrangement |
| 2026-08-09 | UI Polish (A3) | Favourite star changed to separate button (36px) with better touch target |
| 2026-08-09 | UI Polish (A4) | Morning/Afternoon labels now show time ranges (8AM–12PM / 1PM–6PM) |
| 2026-08-09 | UI Polish (A5) | Added booking hint banner: "Click any green slot to book this venue" |
| 2026-08-09 | Bug Fix (B1) | Venue type filter now actually filters venues in dropdown |
| 2026-08-09 | Bug Fix (B2) | Morning/Afternoon filter now hides all cells outside range (including holiday/Sunday) |
| 2026-08-09 | Bug Fix (B3) | Holiday/Sunday cells now show lock icon + "Holiday"/"OFF" label |
| 2026-08-09 | Bug Fix (B4) | Today button now works (initTodayBtn() called in DOMContentLoaded) |
| 2026-08-09 | Bug Fix (B5) | Tooltip only shows for cells matching current time filter |
| 2026-08-09 | Bug Fix (B6) | Mock data made more distinct with events in B014, B015, B016 |
| 2026-08-09 | UX (C1) | Past 4 Weeks changed from tabs to collapsible details element |
| 2026-08-13 | `@section('page-styles')` | macOS-style update (Phase 4) | Removed ~48 lines of duplicate modal CSS (`.modal`, `.modal-header`, `.modal-title`, `.modal-status-badge`, `.modal-close`, `.modal-body`, `.modal-field`, `.field-label`, `.field-value`, `.modal-footer`, `.btn-close-modal`, `#eventModal.modal-overlay`) — all now served by `theme.css`. Standardized border-radius: `fav-btn`, `segment-toggle`, `venue-type-btn`, `print-btn`, `booking-hint`, `no-match-banner button` 6px→8px; `history-status`, `venue-event-status`, `btn-book`, `error-banner button` 4px→6px. Standardized shadows: `venue-type-dropdown`, `toast-notification`, `available-tooltip` → layered `0 4px 16px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06)`. Fixed hardcoded `#fff` → `var(--color-on-primary)` in `segment-toggle button.active`, `btn-book`, `no-match-banner button`. Added `:focus-visible` rings to `fav-btn`, `segment-toggle button`, `venue-type-btn`, `print-btn`, `btn-book`, `error-banner button`, `no-match-banner button`. |
| 2026-08-13 | `public/css/theme.css` (shared) | macOS table fix | `.timetable`: `border-collapse:collapse` → `separate` + `border-spacing:4px`; removed 1px cell borders; hover uses `var(--color-surface-variant)`; removed zebra striping. `.badge` border-radius 6px→8px. |
| 2026-08-13 | `public/css/theme.css` (shared) | CSS tooltip + legend refactor | Added `[data-tip]` CSS tooltip system (above element, inverse-surface bg, arrow, opacity transition). Legend bar: `flex-direction: column`, hint moved to top-left above swatches. Legend swatches use container tokens matching cell backgrounds. Legend bar has `background: var(--color-surface)` for consistent rendering. |
| 2026-08-14 | `buildTimetable()` | OOP refactor | Hand-built `<table>` replaced with shared `buildTimetableGrid({cellRender})` from ui-common.js. cellRender handles holiday/Sunday (cell-ph/cell-sun), event/occupied (cell-pending/cell-occupied), available cells + booking tooltip + mobile available cards. |
| 2026-08-14 | `prevWeek/nextWeek/selectWeek`, today btn | WeekNavigator delegation | Local nav logic replaced with `weekNav.prevWeek()/nextWeek()/selectWeek()` + `currentWeek` re-sync. Today button now uses `weekNav.initTodayBtn()` (class jumpToToday) + persist listener. |
