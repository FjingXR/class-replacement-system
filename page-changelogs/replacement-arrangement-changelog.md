# Changelog — Replacement Arrangement (Selected Subject Page)

## [2026-08-19] Fix: week-navigation confirmation preserves selection + back button checks saved slots

### Problem

1. `guardedWeekNav` called `deselectBlock()` which permanently destroyed the saved selection (`selectedSlotsByVenue[currentVenue][week] = null`). Navigating back to the original week did NOT restore the user's selection.
2. `goBack()` and `navigateTo()` only checked `selectedBlock` — if the user had saved selections in other weeks (from a week-change guard), the back button skipped the confirmation and silently lost all saved data.

### Fix

- Added `clearVisualSelection()` — clears the grid visuals and `selectedBlock` WITHOUT nulling `selectedSlotsByVenue`. The selection is preserved and restored by `loadCurrentWeek()` when navigating back.
- `guardedWeekNav` now: calls `saveCurrentWeek()` → temporarily suppresses `weekNav.onBeforeNavigate` (prevents `saveCurrentWeek` from overwriting with null during navigation) → calls `clearVisualSelection()` → navigates → restores callback.
- `guardedWeekChange` uses the same save-before-clear pattern.
- `guardedJumpToToday` delegates to `guardedWeekNav` (removed redundant `saveCurrentWeek` that was undoing the save).
- `goBack()` / `navigateTo()` now check `selectedBlock || getGlobalTotal() > 0` — catches saved selections across all venues/weeks.
- Confirmation message updated: "Your selection will be **saved**. You can return to this week later to continue."

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `clearVisualSelection()` | Added | Clears grid visuals + `selectedBlock` without touching `selectedSlotsByVenue` |
| `guardedWeekNav()` | Fixed | Saves via `saveCurrentWeek()`, suppresses `onBeforeNavigate`, uses `clearVisualSelection()` instead of `deselectBlock()` |
| `guardedJumpToToday()` | Fixed | Removed redundant `saveCurrentWeek()` inside actionFn |
| `guardedWeekChange()` | Fixed | Uses same save-before-clear + suppress pattern |
| `goBack()` | Fixed | Checks `getGlobalTotal() > 0` in addition to `selectedBlock` |
| `navigateTo()` | Fixed | Same `getGlobalTotal()` check + updated message |

#### `tests/confirm-guards.spec.ts`

| Location | Change | Detail |
|---|---|---|
| Week next arrow test | Added | Verifies confirmation popup + confirm navigates |
| Week prev arrow test | Added | Verifies cancel keeps selection |
| Week dropdown test | Added | Verifies cancel restores dropdown value |
| Back button test | Added | Verifies back button shows confirmation for saved selections |

---

## [2026-08-19] Fix subject dropdown lock + week-navigation confirmation guards

Two fixes addressing bugs reported after the dropdown guard feature was added.

### Fix 1 — Subject dropdown disabled when arriving from replacement-home

When visiting `/replacement-arrangement` with URL params (e.g. from `/replacement-home-ui`), the subject dropdown was permanently disabled (`sel.disabled = true`). Removed the disable so the user can change subjects. The existing guard in `onSubjectChange()` handles confirmation when a grid selection exists.

### Fix 2 — Week-navigation confirmation when grid slots are selected

Added confirmation guards to all week-navigation entry points (prev/next arrows, week dropdown, Today button). When `selectedBlock` is active and the user navigates to a different week, a confirmation modal appears. Confirm clears the selection and navigates; Cancel stays on the current week (dropdown value restored if changed).

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `applyUrlParams()` | Removed | `sel.disabled = true` line removed — subject selector stays editable |
| `guardedWeekNav()` | Added | Shared guard for week navigation: confirm → `deselectBlock()` + proceed |
| `guardedPrevWeek()` | Added | Guard wrapper for prev-week arrow |
| `guardedNextWeek()` | Added | Guard wrapper for next-week arrow |
| `guardedJumpToToday()` | Added | Guard wrapper for Today button |
| `guardedWeekChange()` | Added | Guard wrapper for week dropdown; restores select value on cancel |
| `ui-week-nav` include | Updated | `prevOnclick` → `guardedPrevWeek()`, `nextOnclick` → `guardedNextWeek()`, `selectOnclick` → `guardedWeekChange()` |
| Today button handler | Updated | Uses `guardedJumpToToday()` |

---

## [2026-08-19] Confirmation guards on dropdown changes when slots are selected

When the user has selected grid slots (`selectedBlock`), changing the **venue**, **subject**, or **original slot to replace** now pops a confirmation modal warning that the selection will be removed. Cancel restores the previous value; Confirm clears the selection and applies the change. No popup when nothing is selected. `Clear ALL` already had its own confirmation — unchanged.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| State vars | Added | `revertingChange`, `lastSubject`, `lastSlotIndex`, `slotPickerSlots` for guard tracking + cancel snap-back. |
| `hideConfirmModal()` | Updated | Resets the Cancel button to default after custom handlers. |
| `confirmChangeWithSelection()` | Added | Shared guard: skips when no `selectedBlock`; otherwise confirm → `deselectBlock()` + proceed, cancel → cancelFn. |
| `onVenueChange()` | Updated | Guards via `confirmChangeWithSelection`; cancel re-selects previous venue (suppressed by `revertingChange`); body moved to `applyVenueChange()`. |
| `onSubjectChange()` | Updated | Guards subject changes; cancel restores `#subjectSelector` to `lastSubject`; body moved to `applySubjectChange()`. |
| `renderSlotPicker()` | Updated | Stores rendered slots in `slotPickerSlots` for cancel restore. |
| `selectSlot()` | Updated | Guards re-picks while slots are selected; cancel restores previous pick; body moved to `commitSlotSelection()`. |
| `DOMContentLoaded` | Updated | Sets `lastSubject` after `applyUrlParams()` (no guard triggers at load). |

#### `tests/confirm-guards.spec.ts`

| Location | Change | Detail |
|---|---|---|
| New file | Added | 7 Playwright tests covering venue/subject/slot-trigger guards (confirm + cancel paths), Clear ALL cancel, and no-popup-without-selection. |

---

## [2026-08-16] Moved `.info-label` from local `<style>` to shared `theme.css`

The `.info-label` class (used for field overlines in the info panel) was defined locally in the page's `<style>` block. Moved to `theme.css` as a shared utility class so other pages can reuse it.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change |
|---|---|
| `<style>` block ~line 591 | Removed local `.info-label` definition (now in `theme.css`) |

---

## [2026-08-15] Selection target from URL `duration` param

The page now reads a `duration` (hours) query param and caps the replacement selection at `duration × 2` (30-min slots) via `MAX_SELECTION`, so the proposed replacement matches the original class length. Defaults to 4 slots (2 hours) when the param is absent/invalid. Done with the user's explicit note that URL params are **not** trusted authority in the real backend — this is mock-phase convenience.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `MAX_SELECTION` | Updated | `const` → `let`, default 4. |
| `readUrlParams()` | Updated | Reads `duration`. |
| `DOMContentLoaded` | Updated | Sets `MAX_SELECTION = round(duration * 2)` when a valid positive `duration` is present. |

---

## [2026-08-15] Page-specific summary card descriptions

### Summary

Added page-specific `description` text to each summary card (Total Slots / Available / Pending / Unavailable) instead of relying on the shared generic descriptions.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Summary bar cards | Updated | Each card now passes a page-specific `description` (e.g. Available = "Free slots you can select as the replacement"). |

---

## [2026-08-15] Summary bar added; Occupied/Reserved/Sunday/PH → one "Unavailable" card

### Summary

Added the shared summary bar (like the venue timetable) with cards **Total Slots / Available / Pending / Unavailable**, so users immediately see the breakdown of the current week's slots. `Unavailable` groups every "cannot book" reason — Occupied, Reserved by Others, Sunday, and Public Holiday — matching the "one Unavailable card" convention from the venue page.

- `updateSummaryStats()` counts exactly the grid's rendered cells, so `Total = Available + Pending + Unavailable` always matches the timetable (incl. overlapping/selected states).
- "Available" includes the user's current selection (still a pickable slot), keeping the total stable while selecting.
- Legend updated: "Reserved by Others" + "Occupied / Class on Public Holiday" → single **Unavailable** entry; tooltip explains the grouped reasons.
- Guide bullet updated to match.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| After legend | Added `@include('partials.ui-summary-bar', ...)` | Cards: Total Slots, Available, Pending, Unavailable (`sumTotal`/`sumAvailable`/`sumPending`/`sumUnavailable`). |
| Legend bar items | Updated | Collapsed "Reserved by Others" + "Occupied / Class on Public Holiday" into one "Unavailable" entry (`Cannot book — booked by others, Sunday, or public holiday`). |
| Guide bullet | Updated | `Slot status — Available (green), Unavailable (booked / Sunday / public holiday)`. |
| `updateSummaryStats()` (new) | Added | Counts rendered cells: available(+selected), pending, unavailable(=occupied+reserved+sunday+ph). |
| `updateCounter()` | Updated | Calls `updateSummaryStats()` on every build/select/deselect. |

---

## [2026-08-15] Selection summary now scoped to the current week

### Summary

The selection summary previously listed every selected slot across all 14 weeks and venues, regardless of which week the user was viewing. Now `updateSelectionSummary()` only shows selections for the currently viewed week (`weekNav.currentWeek`), so the summary + "X of 4 slots" total match what's visible in the timetable.

Note: the global 4-slot cap (`MAX_SELECTION`) still applies across all weeks/venues via `getGlobalTotal()`.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `updateSelectionSummary()` | Updated | Iterates only the current week's selections per venue (`venueData[currentWeek]`) instead of all weeks. The summary grid, count, and total now reflect the selected period. |

---

## [2026-08-13] OOP Refactor: Legend color consistency

### Summary

Fixed legend swatches looking different from cells by:
1. Legend swatches now use container tokens (`--color-success-container`, etc.) matching cell backgrounds
2. Legend bar now has `background: var(--color-surface)` so swatches render on the same surface as cells

### Files Changed

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|---|---|---|---|
| 2026-08-13 | `.legend-bar` | Updated | Added `padding`, `background: var(--color-surface)`, `border`, `border-radius` |

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|---|---|---|---|
| 2026-08-13 | Lines 688-692 | Changed | Legend items now use container tokens (e.g. `--color-success-container` instead of `--color-success`) |

---

## [2026-08-13] OOP Refactor: Align shared base with theme.css

### Summary

Removed ~140 lines of duplicate inline CSS that re-declared shared classes (`.toolbar`, `.toolbar-left`, `.toolbar-right`, `.grid-wrapper`, `.grid-scroll`, `.timetable`, `.time-col`, `.hour-header`, `.hour-cell`, `.btn-outline`, `.btn-danger`) with different values. Now relies on `theme.css` for the shared grid/toolbar/table geometry. Promoted `.cell-content` and `.cell-available` + hover/active to `theme.css` (shared with venue-timetable). Kept page-specific overrides: floating top bar, `.app-container` padding-top, `.selector-dropdown`, `.toolbar-center`, `.hint-text`, selection cell model (`.cell-occupied`, `.cell-selected`, `.cell-pending`, `.cell-reserved`), footer/submit area, selection summary, progress, help overlay.

### Visual Changes

- Toolbar: `border-radius` md→lg, `gap` 16→12px, lost `flex-wrap` and `transition`
- Timetable: `table-layout` fixed→auto, `text-align` center→left, time-col 110→120px
- Buttons: `.btn-outline`/`.btn-danger` now use theme.css outlined style (was filled)

### Files Changed

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | After `.cell-empty` | Added | `.cell-content`, `.cell-available`, `.cell-available:hover`, `.cell-available:active` — shared selection cell model |

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 88-116 | Removed | `.app-container` full block, `.toolbar`, `.toolbar-left` (replaced with 1-line override) |
| 2026-08-13 | Lines 165-169 | Removed | `.toolbar-right` |
| 2026-08-13 | Lines 181-195 | Removed | `.grid-wrapper`, `.grid-scroll` |
| 2026-08-13 | Lines 197-299 | Removed | `.timetable`, `.timetable th/td`, `.time-header-col`, `.time-col`, `.holiday-badge`, `.hour-header`, `.hour-cell` |
| 2026-08-13 | Line 367 | Removed | `.timetable tr:last-child td` |
| 2026-08-13 | Lines 301-323 | Removed | `.cell-content`, `.cell-available` + hover/active (now in theme.css) |
| 2026-08-13 | Lines 437-445 | Removed | `.btn-outline` |
| 2026-08-13 | Lines 470-476 | Removed | `.btn-danger` |
| 2026-08-13 | Line 284 | Removed | Duplicate `.cell-time-label` |

---

## [2026-08-13] Phase 3 UX Enhancement: Legend Tooltips

### Summary

Added hover tooltips to all 5 legend items describing each status.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 882-886 | Added | `tip` property to each legend item |

---

## [2026-08-13] Phase 3 UX Enhancement: Holiday Badge

### Summary

Updated inline holiday CSS and JS to use `.holiday-badge` class matching the shared badge design.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 258-269 | Replaced | `.holiday-label` CSS → `.holiday-badge` with badge styling |
| 2026-08-13 | Line 1186 | Changed | `class="holiday-label"` → `class="holiday-badge"` |

---

## [2026-08-13] Phase 3 UX Enhancement: Shared Legend Bar

### Summary

Replaced inline legend with shared `ui-legend-bar` partial using `$items` parameter.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 876-884 | Replaced | Inline legend → `@include('partials.ui-legend-bar', ['items' => [...]])` |
| 2026-08-13 | Lines 474-500 | Removed | Inline `.legend`, `.legend-item`, `.legend-swatch` CSS (now uses theme.css) |

---

## [2026-08-13] Phase 3 UX Enhancement: Collapsible Guide Block

### Summary

Added an expandable guide block with page-specific workflow instructions.

### Files Changed

#### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 857-865 | Added | `@include('partials.ui-guide-block')` with 5 workflow tips |

---

## Files Changed

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Change | Detail |
|--------|--------|
| Per-venue slot data | `slotData` replaced with `venueSlotData` (4 venues: B103–B106 each with unique availability pattern) |
| Selections per venue | `selectedSlotsByWeek` → `selectedSlotsByVenue` — switching venues saves and restores picks |
| onVenueChange handler | Added; wires buildingSelector to rebuild timetable with new venue's data |
| Summary cards show venue | Added `.card-venue` line in each summary card; info panel label changed to "Venue" |
| Proceed confirmation | Venue name included in each line item |
| clearAll | Extended to clear all venues' selections |
| Time column center | Added `text-align: center` to `.time-col` |

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` — Subject dropdown + URL params (2026-08-08)

| Change | Detail |
|--------|--------|
| Subject dropdown | Added `<select id="subjectSelector">` in toolbar-center — shows all courses from `MockData.courses` |
| URL param reading | Added `readUrlParams()` function — reads `code`, `cohort`, `venue`, `date`, `time` from URL |
| Pre-select subject | When `code` param present, pre-selects and disables dropdown |
| Venue capacity filtering | `buildVenueDropdown(filterByCourse)` — filters venues by `capacity >= studentCount` and `allowedSessions` |
| Venue count note | Shows "Showing N venues that fit X students" when course selected |
| Course info display | Shows course name, type, cohort(s), student count when selected |

### `routes/web.php`

| Change | Detail |
|--------|--------|
| New route | `GET /replacement-home-ui` → `replacement-home-UI-design-template` |

## Commits

- `db7ce18` — Conflict slots: show CONFLICT label, redirect to replacement-arrangement page on click
- `628a42e` — Header nav links added, per-venue slot data with selection persistence per venue
- `aeddba2` — Weekly Summary Bar added, hour-cell height increased to 80px, summary bar stats, padding/spacing adjustments

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` — Centralised mock data (Task 9)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 | `@section('page-scripts')` | Centralised mock data | `weekData` and `venueSlotData` moved to `public/js/mock-data.js` — page-specific data, NOT standardised to semester. Semester chip now reads `MockData.semester.chipText` dynamically. |
| 2026-08-02 | `.week-nav` wrapper | OOP refactor | Arrows + select wrapped in `.week-nav` div; local `.week-nav` CSS removed (now in `theme.css`). |
| 2026-08-02 | `@section('page-scripts')` | Dynamic week options | Week selector options generated from `MockData.arrangementWeeks` with mobile date format (no year). |
| 2026-08-02 | `@media (max-width: 768px)` | Footer overflow fix | `.footer-area` stacks column on mobile; `.footer-right` gets `flex-wrap: wrap`, `gap: 8px`; buttons get smaller padding/font. Verified working at 375px width. |
| 2026-08-02 | JS (`buildTimetable`) + CSS | Mobile time slot labels | Added `.cell-time-label` span inside each timetable cell showing start/end time (e.g. "10:00\n10:30"); hidden on desktop, visible on mobile at 7px font positioned bottom-right inside cell-content. |
| 2026-08-02 | CSS (`@media 768px`) | Mobile cell sizing | `.hour-cell` and `.cell-content` set to 48×48px, row gap 8dp on mobile. |
| 2026-08-02 | CSS (`@media 768px`) | Hide theme toggle mobile | `.theme-toggle { display: none }` on mobile view. |
| 2026-08-03 05:30 | Lines 1398–1432 | Navigate-away toast | Refactored `goBack()` to show toast before navigating: saves selections, clears them, sets 5s navigation timer, calls `showToast('Selections cleared.', undoCallback)`. Undo cancels timer and restores selections. |

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` — UX Enhancement Features (F1-F4, F7)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 | `@section('page-styles')` | F3: Progress bar CSS | Added `.progress-wrapper`, `.progress-bar`, `.progress-fill`, `.progress-text` styles. |
| 2026-08-02 | `@section('page-styles')` | F4: Venue warning CSS | Added `.venue-warning { color: var(--color-error); margin-left: 4px; }`. |
| 2026-08-02 | `@section('page-styles')` | F7: Toast CSS | Added `.toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); ... }`. |
| 2026-08-02 | `@section('page-styles')` | F1: Keyboard focus CSS | Added `.cell-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }`. |
| 2026-08-02 | `@section('page-styles')` | F1: Help overlay CSS | Added `.help-overlay`, `.help-card` styles. |
| 2026-08-02 | HTML (above timetable) | F3: Progress bar HTML | Added `<div class="progress-wrapper">` with bar + text. |
| 2026-08-02 | HTML (end of page) | F1: Help overlay HTML | Added `<div class="help-overlay">` with shortcut list (hidden by default). |
| 2026-08-02 | `buildingSelector` HTML | F4: Dynamic venue dropdown | Removed hardcoded `<option>` elements; dropdown built from `MockData.venues[]` in JS. |
| 2026-08-02 | `@section('page-scripts')` | F1: Keyboard state | Added `let focusedCell = { day: null, hour: null };`. |
| 2026-08-02 | `@section('page-scripts')` | F2: Undo state | Added `let selectionHistory = [];`. |
| 2026-08-02 | `@section('page-scripts')` | F1: handleKeyDown() | Added keyboard event handler for arrows, Enter/Space, Escape, ?. |
| 2026-08-02 | `@section('page-scripts')` | F1: focusCell/unfocusCell | Added grid navigation functions. |
| 2026-08-02 | `@section('page-scripts')` | F1: showHelp/hideHelp | Added help overlay toggle functions. |
| 2026-08-02 | `@section('page-scripts')` | F2: pushHistory() | Added history entry push function. |
| 2026-08-02 | `@section('page-scripts')` | F2: undoSelection() | Added undo function (pop + reverse action + toast). |
| 2026-08-02 | `@section('page-scripts')` | F3: updateProgress() | Added progress bar update function. |
| 2026-08-02 | `@section('page-scripts')` | F4: buildVenueDropdown() | Added dynamic venue dropdown builder from MockData.venues. |
| 2026-08-02 | `@section('page-scripts')` | F7: checkConflict() | Added conflict check function using MockData.myTimetable. |
| 2026-08-02 | `@section('page-scripts')` | F7: showToast() | Added toast notification function (reused by F2 and F7). |
| 2026-08-02 | `toggleCell()` | F2: History push | Added `pushHistory()` calls on select/deselect. |
| 2026-08-02 | `toggleCell()` | F7: Conflict check | Added `checkConflict()` call on selection; show toast if conflict. |
| 2026-08-02 | `updateCounter()` | F3: Progress update | Added `updateProgress()` call inside existing function. |
| 2026-08-02 | `DOMContentLoaded` | F4: Venue init | Added `buildVenueDropdown()` call to replace hardcoded options. |
| 2026-08-02 | `DOMContentLoaded` | F1: Keyboard listener | Added `document.addEventListener('keydown', handleKeyDown)`. |

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` — Approval Status Toast (DRY Cleanup)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | `@section('page-styles')` | Remove page-local `.toast` CSS | Removed lines 758-772 (duplicate toast styling); now handled by shared `ui-common.js` toast bar. |
| 2026-08-03 | `@section('page-scripts')` | Remove page-local `showToast()` | Removed duplicate function at line 1528; all call sites now use shared `showToast()` from `ui-common.js`. |
| 2026-08-03 | `@section('page-scripts')` | Add `buildSubmissionToastMessage()` | New helper function builds slot details string from `selectedSlotsByVenue` for enhanced submission toast. |
| 2026-08-03 | `proceed()` confirm callback | Enhanced submission toast | Replaced `showToast('Replacement request submitted.', null)` with `showToast(buildSubmissionToastMessage(), null, 5000, 'View →', '/my-request-history-ui')`. Shows venue, day, date, time range + "View →" link. |

### `public/js/ui-common.js` — Toast Link Extension

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | `showToast()` | Add link parameters | Extended signature with optional `linkText` and `linkUrl` params; shows `.toast-link` element when both provided. |

### `resources/views/layouts/ui-template.blade.php` — Toast Link Element

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | `#toastBar` | Add toast-link element | Added `<a class="toast-link" href="#" style="display:none"></a>` between `.toast-message` and `.toast-undo`. |

### `public/css/theme.css` — Toast Link Styles

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 | Toast styles section | Add `.toast-link` CSS | Added link styling (color, underline, hover state) for toast link button. |

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` — OOP Phase 1 partial extraction

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-04 | — | Refactored: replaced inline page-header/week-nav/grid-table with `@include('partials.…')` (OOP Phase 1) | Page uses `ui-page-header`, `ui-week-nav`, `ui-grid-table` partials. |
| 2026-08-13 | `@section('page-styles')` | macOS-style update (Phase 4) | `.back-btn` shadow standardized to layered `0 2px 8px rgba(0,0,0,0.1), 0 1px 3px rgba(0,0,0,0.06)`; hover shadow `0 4px 16px rgba(0,0,0,0.15), 0 1px 4px rgba(0,0,0,0.08)`. `.selector-dropdown:focus` shadow uses CSS variable. Modal overlay `rgba(0,0,0,0.5)` → `rgba(0,0,0,0.45)`. |
| 2026-08-13 | `.timetable` CSS | macOS table fix | `border-collapse:collapse` → `separate` + `border-spacing:4px`; removed 1px cell borders; removed `border-color` from transition. |
| 2026-08-14 | `buildTimetable()` | OOP refactor | Hand-built `<table>` replaced with shared `buildTimetableGrid({cellRender})` from ui-common.js. Custom cellRender renders venue-slot selection model (available/occupied/pending/reserved/ph/sun) + time label + toggle click. Post-build `loadCurrentWeek()`/`updateCounter()`/`weekNav._updateArrows()` kept. |
