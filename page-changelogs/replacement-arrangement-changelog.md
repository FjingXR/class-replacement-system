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

## [2026-10-03] Disabled print icon on the week-nav toolbar

Shared `ui-week-nav` gained an opt-in `'showPrint' => true` arg rendering a printer icon-button
(inline SVG, `.print-btn` in theme.css), right-aligned at the toolbar edge via `margin-left: auto`.
Enabled stub: click fires the shared `toast.show('Printing is coming soon')` bottom-left toast bar;
`title="Coming soon"` native tooltip on hover. No JS beyond the one-liner onclick.

## [2026-10-03] Slot picker moved beside the subject selector

`.toolbar-primary` was a column stack (slot picker under the subject select); it is now a row —
select (`flex: 1 1 240px`, max 320) with the `.slot-dd` picker immediately to its right
(`flex: 1 1 200px`, max 280, gap 12). `.toolbar-center` stops flex-growing (content-sized) and
`.toolbar-filters` takes `flex-basis: 100%` + `flex-end`, so the week nav owns its own right-aligned
row — the single toolbar line could never fit subject + slot + venue + week nav together. Mobile
(≤768px) keeps the stacked full-width look via `flex-basis` overrides. Trigger span ellipsizes.
Verified side-by-side at 1280/1440/1024, stacked at 375; picker panel opens as before; 0 console
errors.

## [2026-10-03] Max-selection tip gains a "click to remove" hint

At `MAX_SELECTION` the summary tip now reads "Tip: Maximum selection reached. Click the selected
block to remove it." (previously the remove hint vanished exactly when it was needed). The action
phrase is wrapped in a new `.tip-action` span — `var(--color-primary)` + bold — in both branches
that show it, and `.sel-summary-tip` drops its `opacity: 0.65` so the accent reads at full strength
while the "Tip:" prefix stays muted via `--color-on-surface-variant`. Verified dark + light themes,
0 console errors.
Follow-up (same day): in dark theme `--color-primary` stays `#004D98` (dark navy — too dim on
`#1B2838`), so `html.dark .sel-summary-tip .tip-action` now uses the dark palette's bright accent
token `--color-on-primary-container` (`#6BA3E0`); light theme keeps `--color-primary`.

## [2026-10-03] Hover preview carries the message; "Select ?" chip removed

The per-cell hover chip (`--hover-label: 'Select ?'`) is gone — `.cell-available::after` is
suppressed (`content: none`) on this page since the theme renders the chip via an empty-content
overlay. `previewBlock()` now injects a `.preview-label` span centered across the 4-slot preview
overlay: "Select these slots?" on `.preview-ok` (yellow) and "Not enough slots" on `.preview-fail`
(red). The label renders as a solid pill (`--color-tertiary`/`--color-on-tertiary` on OK,
`--color-error`/`--color-on-error` on fail, padded + rounded) so it stays crisp over the
translucent preview fill — a darker-fill variant was tried and reverted (fill stays at the shared
40% `color-mix`). Verified dark + light, 0 console errors. Uncommitted at user request.

## [2026-10-03] Selected-block remove hint redesigned

The old "Click Me to Remove Slots" text was dead code — it lived on `.cell-selected .sel-text`
(elements never created, hidden under the block overlay anyway). Now: the selected block shows its
time range at rest (`.ev-time-label`, e.g. "8:00 AM – 10:00 AM"), and on hover/focus theme.css's
`.event-block.event-selection::after` (previously a bare transparent "REMOVE?") becomes a solid red
danger pill — "Remove these slots?" (`--color-error`/`--color-on-error`), with the block border
swapping to error on hover; `bottom: auto` added to undo the generic `.event-block::after` tooltip's
`bottom: 100%` (was squashing the pill to a 6px sliver). Summary card "×" gains a shared
`data-tip="Remove this selection"` tooltip. Verified dark + light, 0 console errors.

## [2026-10-03] Toolbar groups on a single row

`.toolbar-filters` loses its `flex-basis: 100%` own-row rule — subject select, slot picker, venue
dropdown, favourite, week nav, Today and print all share one toolbar line at ≥ ~1150px (container
content box is 1190px). To fit: week labels drop the year via the page's `labelFn` ("Week 1 · 27 Jul ~ 02 Aug" — the year
is constant within a semester; select 285 → 212px) and the full dated label
("Week 1 · 27 Jul 2026 ~ 02 Aug 2026") shows on hover as a `data-tip` — the shared tooltip renders
above — kept current by patching `WeekNavigator._updateSelect` so arrows/Today stay in sync.
Venue trigger caps at 204px (its natural width — full label + arrow visible; longer venue names
ellipsize but a `data-tip` synced via MutationObserver shows the full label above the trigger on
hover), `.toolbar-center` gains `gap: 8px`
(the venue trigger + favourite star had none — reclaimed via group gap 16 → 10 and filters gap
12 → 6 so the row still fits), subject select caps at 235px, and the slot picker gets 260px so its
primary info is fully visible — trigger text also compacted to "W3 · Aug 17 · 10:00 AM–11:30 AM"
(comma/en-dash form, no clipping).
subject select caps at 235px, and the slot picker gets 260px so its primary info is fully visible —
trigger text also compacted to "W3 · Aug 17 · 10:00 AM–11:30 AM" (comma/en-dash form, no clipping).
Below ~1150px the groups still wrap gracefully and ≤768px keeps the stacked layout. Verified
1280 / 1440 / 1024 / 375, 0 console errors.

## [2026-10-03] Blocked-state tooltip on green slots while a selection exists

After a block is selected, the remaining available (green) cells gain
`data-tip="Clear your selection first — click it to remove"` — set in `renderMergedBlock`, removed
in `clearMergedBlock` (both deselect paths route through it). Hovering another green slot now
explains why it can't be picked, rendered above the cell by the shared tooltip system, instead of
silent feedback with only the click-time modal. Deselect restores the normal "Select these slots?"
preview. Verified tooltip above + cleanup, 0 console errors.

## [2026-10-03] Summary tip dedupe + success-colored hint

The "Click an available (green) time slot to begin." sentence appeared twice in the empty state —
once in `.sel-summary-empty` and again in `#summaryTip` below it. Removed the empty-state paragraph
("No time slots selected." remains; the tip line is the single home for the instruction). The
phrase "available (green)" is now wrapped in a `.tip-success` span (`--color-success` + bold;
`--color-on-success-container` in dark via the same html.dark override pattern as `.tip-action`)
so the referenced colour is obvious in both themes. Verified dark + light, 0 console errors.

## [2026-10-03] Subject selector hover tooltip

`#subjectSelector` gains a `data-tip` (shared tooltip, renders above): before a subject is picked it
reads "Select a subject first to trigger the timeslots selector"; after, it mirrors the selected
option's full text ("BMIT6767 — Object-Oriented Programming") — the single-row select ellipsizes
long names, so the tooltip carries the complete info. Synced on init (after applyUrlParams) and on
change. Verified placeholder → picked → hover above, 0 console errors.

## [2026-10-03] Footer button tooltips (Submit Request / Clear ALL)

Both footer buttons are wrapped in `.btn-tip` spans carrying state-aware `data-tip`s, synced in
`updateCounter`: Submit Request — "Select a time slot first to enable submission" when disabled /
"Send your replacement request for approval" when armed; Clear ALL — "Nothing to clear yet — select
a time slot first" / "Clear all your selected slots". Disabled controls swallow mouse events, so
the tips live on the wrapper spans with `.btn-tip .btn:disabled { pointer-events: none }` letting
hover pass through; the shared tooltip renders above. Verified all four states, 0 console errors.

## [2026-10-03] Primary buttons adopt the shared .btn-action hover

`.btn-primary` (Submit Request + modal Confirm) now mirrors replacement-home's `.btn-action` hover:
brightness(1.08) + neutral `0 2px 8px rgba(0,0,0,0.15)` shadow + `scale(0.97)` on active, with
`transform/filter/box-shadow` transitions. Also removes the stale green rgba shadows
(`rgba(151,230,194,…)` / light `rgba(46,194,126,…)`) left from the old green theme on the now-blue
primary. The Today chip (container-styled, opacity hover) is intentionally untouched. Verified
computed hover styles, 0 console errors.

## [2026-10-03] Leave-confirmation navigates immediately

Confirming the "Unsaved Changes" modal (Back button and logo/home paths — goBack/navigateTo) now
leaves at once: the on-screen clearing, "Selections cleared." undo toast and the 5-second
navigate-or-undo countdown in goBack are gone. Selections are in-memory and die with the page, so
the modal itself remains the only guard. Verified modal → immediate navigation on both paths plus
the no-selection instant back, 0 console errors.

## [2026-10-03] Browser-level beforeunload guard

A `beforeunload` handler now warns when leaving with unsaved selections via paths the in-page modal
cannot reach: refresh, tab close, address-bar navigation and the browser's own back/forward arrows
(native dialog — browsers force their own generic wording; we only control whether it appears).
An `allowUnload` flag is set in the goBack/navigateTo confirm callbacks so the confirmed in-page
leave bypasses the guard (no double prompt). Verified: refresh with selection prompts, accepted
reload proceeds, no-selection navigation stays silent, in-page confirm navigates with no native
dialog, 0 console errors.

## [2026-10-03] bfcache restore resets selections + re-arms the unload guard

Chrome's back-forward cache resurrects the frozen page (selections, undo history AND the
allowUnload bypass) on browser back/forward, contradicting the "selections will be lost" promise.
A `pageshow` handler (e.persisted) now re-arms the beforeunload guard, wipes selectedSlotsByVenue
and selectionHistory, and runs deselectBlock + updateCounter — a clean slate on every restore.
Verified via synthetic persisted-pageshow (4 selected → clean slate, stale Ctrl+Z stays inert) and
the re-armed refresh guard firing afterwards; real back-nav returns clean, 0 console errors.

## [2026-10-03] Conflict Schedule context strip

The plain four-line Conflict Schedule text block becomes an icon-led context strip, repositioned
below "How to use this page" and above the toolbar: an error-tinted warning chip ("Conflict
Schedule"), then book/clock/users icon segments for Subject (code emphasised), Time Slot and
Cohorts, with a right-anchored primary-container badge for Total Students (data-tip "Total
students"). Empty states keep the muted "Not selected" + step hints; dead .course-label-center and
.title-* rules removed. Wraps to 2 lines at 1024/768, stacks cleanly at 375; verified dark + light,
0 console errors.

## [2026-10-03] Grid locked until a subject is picked

Clicking an available cell with no subject no longer selects: toggleCell shows the toast "Select a
subject first to trigger the timeslots selector" (same copy as the subject tooltip) and pulses the
subject dropdown (attention-pulse ring) to point the user at the fix. The hover preview becomes a
preview-fail pill reading "Pick a subject first", and free cells read not-allowed via a
no-subject class on the timetable (toggled in renderTitleSummary). Two pre-existing stale-preview
gaps fixed on the way: buildTimetable and applySubjectChange now clearPreview() so an open hover
preview never survives a rebuild. Verified locked hover/click, unlock -> normal selection, re-lock
after clearing the subject, 0 console errors.

## [2026-10-03] Booking intent honoured from venue-timetable handoff

The venue-timetable "Book" handoff (?venue&date&time) previously dropped the date/time entirely —
the user arrived with no reminder of the promised slot. Now: a primary-container intent banner
("Booking B103 · Tue, 08 Sep 2026 · 10:30 — pick a subject to pre-fill the slots.", dismissible)
renders above the Conflict Schedule strip, and picking a subject auto-selects the booked block
(the venue page's Book click already confirmed it) with a toast — falling back to pulsing the
target cell when the block cannot fit. applyUrlParams now applies venue before subject so the
auto-select lands on the booked venue's grid, and the resolver jumps weeks if the booked date
lives outside the restored week. Intent is consumed by any selection or the banner's ×. Verified
round-trip (banner -> pick -> auto-selected Tue 10:30), code-param landing (fully pre-selected),
dismiss path and overflow fallback, 0 console errors.

## [2026-10-03] Sticky booking-intent dismissal

Dismissing the booking-intent banner now sticks across navigation: the x records the booking's key
(venue|date|time) in sessionStorage and re-arrivals at the same booking keep the banner suppressed.
The reminder is suppressed, not the feature — the auto-select on subject pick still fires for a
dismissed booking. A different booking (different slot) shows its banner normally; the flag is
per-tab and clears with the session. Returning without dismissing still shows the banner (the
booking remains pending — the URL is the source of truth). Verified: dismiss -> leave -> return
stays suppressed with auto-select intact, different booking re-shows, 0 console errors.

## [2026-10-03] Manual-selection fallback announced on arrival

When the booked slot cannot be auto-selected (overflow past day end / unavailable), the toast
"Your booked slot needs manual selection — it is highlighted on the grid." now fires on ARRIVAL
(before any subject choice) instead of after picking a subject. evaluateBookingFit() runs at the
end of applyUrlParams — the grid is already on the booked venue, so the fit-check is valid and the
pulse lands on the final grid. The banner copy gains a manual variant ("pick a subject, then
select the highlighted slot manually" -> "select the highlighted slot on the grid." after the
subject pick) and now clears on the real manual selection (selectBlock hook) instead of the
subject pick. Auto-select flow unchanged. Verified arrival toast/banner/pulse, post-subject copy
update without auto-select, manual completion clearing the banner, and the auto path regression,
0 console errors.

## [2026-10-03] Conflict case: error-toned intent banner

When the booked slot cannot be auto-selected (conflict/overflow), the intent banner no longer reads
as a calm instruction ("select the highlighted slot on the grid.") — it becomes an error-toned
notice (.booking-intent-manual: --color-error-container / --color-on-error-container, warning icon)
reading exactly "Your booked slot needs manual selection — it is highlighted on the grid.", paired
with the arrival toast and the pulsed cell. The banner persists (pre- and post-subject pick) until
a real manual selection or the dismiss x. Auto-select flow keeps its primary-container banner.
Verified text/colors, persistence across the subject pick, manual-completion clearing, 0 console
errors.

## [2026-10-03] Manual-selection banner: concrete slot pointer, toast removed

The conflict banner's ambiguous "it is highlighted on the grid." now names the slot outright:
"Your booked slot needs manual selection — find Wed, 09 Sep 2026 · 13:30 on the grid." The
arrival toast for this case is removed entirely — the persistent banner + cell pulse carry the
message (toast was redundant). Auto-select flow unchanged. Verified banner wording, empty toast
bar on manual arrival, auto path regression, 0 console errors.

## [2026-10-03] Conflict banner states the conflict outright

The manual-selection banner now leads with the reason: "Your booked slot (Wed, 09 Sep 2026 ·
13:30) is conflicted — please select an available slot manually." — slot, conflict status and
action in one sentence (was: "needs manual selection — find … on the grid", which never said WHY).
Verified banner wording on conflict arrival, 0 console errors.

## [2026-10-03] Navigation bug-fix batch (Playwright round-trip audit)

Six bugs found by testing venue-timetable <-> replacement-arrangement with browser back,
forward, refresh and the system back button — all fixed and verified:

1. **Subject dropdown contradiction after back/forward** — Chrome's form restoration re-filled
   the dropdown while the page booted with currentCourse null (grid locked under a filled
   selector; re-picking the shown subject fired no change event). reconcileSubjectState() now
   runs on pageshow (+120ms belt & braces): what the dropdown shows is what gets applied.
2. **Refresh resurrected a deselected block** — the booking auto-select re-fired on every load.
   discardSelection() (block click, card x, card click, clear selection, change-confirms) now
   records bookingIntentCancelled (sessionStorage, venue|date|time) and consumeBookingIntent()
   treats a cancelled booking as spent: no re-select, banner cleared, no toast. Per booking —
   other bookings still auto-select.
3. **Booking context lost on system back** — BackNavigator.getBackUrl() now carries code/cohort
   back to venue-timetable, so the "Booking for:" banner survives the round-trip.
4. **Stale "Pre-selected..." toast with a dead Undo** — discardSelection() dismisses the toast
   bar the moment the selection is discarded.
5. **Week bleed between pages** — WeekNavigator takes a storage key; the arrangement uses
   'arrangementWeek', venue-timetable 'venueTimetableWeek' (other pages keep the shared
   'currentWeek'). Browsing the arrangement no longer moves the venue page's week.
6. (Venue page side) unbookable late slots no longer offer Book — see venue changelog.

Verified: forward-restore reconciliation, deselection remembered across refresh (both with and
without &code), banner context through system back, toast dismissal, per-page weeks, no-fit
guard, auto/conflict/sticky-dismissal regressions, refresh-cancel keeping the selection, other
timetable pages unaffected — 0 console errors throughout.

### Follow-up: cancelled bookings no longer show the banner either

Manual testing flagged an inconsistency: after deselect + refresh, the cancelled booking's
banner re-appeared promising "pick a subject to pre-fill the slots" — a promise the cancelled
flag then broke (nothing pre-filled). renderBookingIntent() now treats a cancelled booking like
a spent intent: no banner at all. The two sticky memories stay distinct: the banner's x only
silences the reminder (auto-select still fires), while deselecting the block ends the whole
intent (no banner, no re-select). Re-booking the same slot from the venue page starts fresh
(see venue changelog). Verified: deselect+refresh leaves no banner and no toast; subject re-pick
stays silent; x-dismissed bookings still auto-select; same-slot re-book restores both.

## [2026-10-03] Round-2 navigation fixes (multi-week model + intent snap-back)

Full Playwright sweep of both pages surfaced 9 more issues; fixed per product decisions
(multi-week selection capped at 4 total slots; booking intent snaps the grid back):

1. **Grid built from the STALE week** (root cause, pre-existing): WeekNavigator ran
   _updateSelect AFTER _buildTimetable, and the arrangement's getDays() reads the selector —
   arrow/programmatic week jumps rendered the PREVIOUS week's dates (and holiday placeholders)
   under the new week's label. Selector now syncs BEFORE the build.
2. **Booking intent landed on the browsed week/venue**: the intent now carries its weekIndex;
   at consume time the grid snaps back to the booked venue + week before auto-selecting
   (skipped while a selection is active — nothing is discarded behind the user's back; an
   identical restored selection quietly fulfils the intent).
3. **MAX_SELECTION bypass across weeks**: per-week memory let users stack unlimited blocks.
   selectBlock now enforces the 4-slot TOTAL budget with a clear toast, and returns
   success/failure so the auto-select stays quiet when refused.
4. **Submit summarized only the on-screen week**: proceed() now lists EVERY saved block across
   weeks/venues in the confirmation ("…following N blocks"), and the No-Selection guard uses
   the global total.
5. **Submit/Clear-ALL disabled with a live selection on another week**: updateCounter() now
   drives counter + button states + tooltips from the global total.
6. **Unfittable cell click was a silent no-op**: now toasts "Not enough time left in the day
   for a 120-minute selection." (hover preview already warned).
7. Week round-trip selection loss was this same stale-grid bug in disguise (the restore call
   inside buildTimetable was reading a wrong-week grid); no extra hook needed — an
   onAfterNavigate double-call was removed again.

Verified: snap-back (week + venue), cap + toast, cross-week submit summary, round-trip restore,
submit/clear enablement, unfittable toast, my-timetable unaffected, plus the full regression set
(auto/conflict/sticky-cancelled intent flows, refresh-cancel, Escape closes) — 0 console errors.

## [2026-10-03] Round-3 fixes: summary panel, booking intent lifecycle, Clear-ALL undo, URL hardening

Third Playwright sweep (multi-week seams + never-audited paths) found 7 issues; fixed per
product decisions (panel lists all weeks; intent survives changes; invalid venue param falls back):

1. **Selection summary ignored other weeks**: the panel showed "No time slots selected." while
   Submit was enabled for another week's block (infoTotal contradicted the panel). It now lists
   EVERY saved selection across weeks/venues (sorted week → day), matching the Submit dialog;
   each card's × removes that block via the new removeSavedBlock().
2. **Booking intent died on subject/venue changes**: the change-confirm's discard silently marked
   the booking cancelled (sticky flag) — re-picking gave no banner and no pre-fill. The intent is
   now only spent by explicitly removing THE booked block (clicking it or its summary card, via
   intentMatchesBlock); a persistent bookingIntentMemory re-arms the intent in the change-confirm
   so the next subject application re-selects the booked slot automatically (snap-back included).
3. **Clear ALL's Undo corrupted state across weeks**: undo after navigating re-rendered the old
   block onto the WRONG week and the next navigation baked it into both weeks (8/4 slots). The
   undo now only re-renders when the view hasn't moved since the clear; the modal copy no longer
   claims "cannot be undone".
4. **Phantom venue from URL**: ?venue=ZZZ99 rendered a non-existent venue (submittable!). Invalid
   venue params are now dropped (default venue, no banner, booking intent dropped with it).
   (Bad date/time params already degraded gracefully.)
5. Minor: dead selCount reference left as-is (element doesn't exist in markup).

Verified: cross-week summary + per-card remove, intent-through-changes (subject AND venue,
including the auto re-select), explicit-deselect still cancels, undo guard (same-week restore +
no cross-week corruption), venue fallback, plus the full regression set (arrival auto-select,
conflict banner, sticky ×/cancelled memory, refresh-cancel, submit enablement) — 0 console errors.

## [2026-10-04] Round-4 fixes: undo integrity, keyboard model, duration clamp, submit toast

Fourth Playwright sweep (undo system, keyboard model, URL params, favourites) — 12 issues:

1. **Ctrl+Z migrated selections across weeks/venues**: undo of a deselect re-rendered the block
   onto the CURRENT grid and wrote it to the CURRENT week — deselect on Week 7, navigate, undo →
   the booking block silently became a Week-3 selection. History entries now carry venue+week;
   the undo restores into the ORIGINAL slot and re-renders only when the view still matches.
2. **Grid keys hijacked form controls**: with the subject <select> focused, arrows moved the grid
   focus ring and SPACE placed a grid block (the select could not be operated by keyboard).
   handleKeyDown now yields to select/input/textarea (Escape still blurs).
3. **?duration= URL param unvalidated**: duration=8 produced an 8-hour/16-slot block and a
   16-slot budget. The span now derives from the duration clamped to 0.5–4h (BLOCK_SPAN,
   decoupled from the budget), and the multi-week budget = max(4, span) — a 1-hour class can
   now book two 1-hour blocks; absurd durations can't balloon the cap.
4. **Submission toast never showed** (long-standing): proceed()'s callback declared a local
   `const toast = buildSubmissionToastMessage()` that shadowed the toast manager — every submit
   threw `toast.show is not a function` after clearing state. Renamed; the "✓ Submitted —
   Pending Approval" toast with the View → link now renders.
5. **Help overlay advertised dead shortcuts**: [ / ] (week nav) and Ctrl+1/2/3 (venue switch)
   were documented but unimplemented. Both now work (Ctrl+N picks the Nth venue of the filtered
   list via the new VenueDropdown.getFiltered()).
6. **Ctrl+Z after submit resurrected the submitted selection** (double-submit risk) — submit now
   clears the selection history.
7. **Booking intent lost on venue change**: the venue path used its own confirm (no re-arm), so
   changing venue silently killed the booking. The venue confirm now re-arms the intent AFTER
   applying (the user's venue choice sticks; the banner re-appears: "…will pre-fill when you
   next pick a subject."), and the next subject pick snaps back + re-selects. The subject path
   keeps its immediate re-select. Banner copy updated for the armed state.
8. **Focus ring left the viewport** when arrowing down a tall grid — focusCell now
   scrollIntoView(nearest).
9. **Help overlay ignored backdrop clicks** (modals close on backdrop; help didn't).
10. **Arrangement grid clipped on mobile**: the 7×22 table squeezed into 356px with
    overflow hidden. ≤768px the grid now scrolls horizontally (min-width 820px).
11. Misc: dead selCount reference noted (harmless); segment-toggle CSS has no markup (harmless).

Verified: all of the above plus the regression set (arrival auto-select, conflict banner,
sticky ×, cancelled memory, refresh-cancel, cap, submit enablement, my-timetable) — 0 errors.

## [2026-10-04] Lead-time rule: slots must be ≥ 3 working days from today

New business rule (N13): replacement slots inside the 3-working-day blackout cannot be
selected — approvals need runway. Boundary agreed: a slot ON the 3rd working day IS selectable.

- Shared helper `isSlotTooSoon(weekData, week, day)` in ui-common: past days and days < 3
  working days from the anchor are read-only; working days = Mon–Fri, skipping the page's
  holiday flags (the boundary shifts automatically if a holiday is added ahead).
- **MOCK_NOW anchor (demo stability)**: `MockData.mockNow` (Sun 4 Oct 2026) drives
  DateHelper.getTodayMs() — the Today button, the week-generation `today` flags and this rule
  all derive from it, so the demo behaves identically whenever it is presented (a real clock
  would progressively push every week into the blackout until nothing is selectable — the
  semester data ends 1 Nov). The anchor is commented in mock-data.js.
- Too-soon cells render read-only (`cell-too-soon`, hover "Min. 3 working days ahead") —
  occupied/pending classes still render normally inside past/too-soon weeks.
- Click/keyboard attempts toast "Slots must be at least 3 working days from today."
- Booking-intent banners distinguish the reason: "…is within 3 working days — please pick a
  later slot manually." (vs the conflict copy).
- Ctrl+Z re-select re-validates — a slot that slipped into the window can't be resurrected.

Verified: Today anchor (Week 10, 04 Oct flag), current week fully read-only, Week 11
Mon/Tue blocked + Wed–Sat selectable, past classes still visible, future bookings unaffected,
too-soon banner, keyboard refusal — 0 console errors.

**Follow-up:** the blackout is now announced in the UI — a lead-time notice above the grid
("Bookable from Wednesday, 07 Oct 2026 onward — replacement requests need at least 3 working
days' notice."), date computed from MOCK_NOW + the holiday calendar via the new shared
`leadTimeCutoff()`/`renderLeadTimeNote()`. Also fixed: the cutoff's holiday lookup built
unpadded date keys (`5 Oct 2026`) while generateWeekData emits padded ones (`05 Oct 2026`) —
holidays inside the counting window were silently skipped; the cutoff now shifts correctly
(verified: a Mon holiday moves the boundary Wed→Thu).

**Rework:** the lead-time notice is now a contextual banner in the booking-banner family
(neutral info tone, calendar icon) instead of a floating italic hint line. It renders only
while the viewed week contains blocked days — "This week is within 3 working days of today —
bookable from Wednesday, 07 Oct 2026 onward." on the current week, "This week has already
passed — …" on past weeks, "Some slots this week are too soon — …" on partially blocked ones —
and auto-hides on fully open weeks.

**Tweak:** the lead-time banner's date ("Wednesday, 07 Oct 2026") uses the success/green
token so the opening date reads as the focal point.

**Fix ("Today" accuracy):** the two week-index helpers in `ui-common.js` (`currentWeekIndex`
/ `WeekNavigator._currentWeekIndex`) still derived "today" from the REAL clock, so the Today
button jumped a week past the demo anchor while the grid highlight followed `MockData.mockNow`
— now both read the mock anchor like everything else.
