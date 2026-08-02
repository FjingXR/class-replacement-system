# Changelog — Replacement Arrangement (Selected Subject Page)

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
