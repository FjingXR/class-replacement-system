# Tasks: Replacement Arrangement — UX Enhancement Features

## Task 1: F3 — Progress Indicator (start here — simplest, no JS dependencies)

- [x] Add HTML `progress-wrapper` div above timetable (after venue selector)
- [x] Add CSS: `.progress-wrapper`, `.progress-bar`, `.progress-fill`, `.progress-text`
- [x] Add JS function `updateProgress()`
- [x] Call `updateProgress()` inside existing `updateCounter()` function
- [x] Test: select slots → progress bar updates; clear → resets

## Task 2: F4 — Venue Capacity Badge

- [x] Remove hardcoded `<option>` elements from `buildingSelector` HTML
- [x] Add JS function `buildVenueDropdown()` using `MockData.venues[]`
- [x] Add CSS: `.venue-warning { color: var(--color-error); margin-left: 4px; }`
- [x] Call `buildVenueDropdown()` in DOMContentLoaded
- [x] Test: venue dropdown shows capacity; warning icon if capacity < students

## Task 3: F7 — Conflict Warning Toast

- [x] Add JS function `checkConflict(dayIndex, hourIndex)` using `MockData.myTimetable.eventsByWeek[currentWeek]`
- [x] Add toast CSS: `.toast { position: fixed; bottom: 24px; ... }`
- [x] Add JS function `showToast(message, callback?)`
- [x] Integrate `checkConflict()` into `toggleCell()` on selection
- [x] Test: select slot that overlaps with myTimetable → yellow toast appears

## Task 4: F2 — Selection Undo (Ctrl+Z)

- [x] Add state: `let selectionHistory = [];`
- [x] Add JS function `pushHistory(entry)`
- [x] Add JS function `undoSelection()` — pop last entry, reverse action
- [x] Integrate `pushHistory()` into `toggleCell()` on both select and deselect
- [x] Add Ctrl+Z handler in `handleKeyDown(e)` (see Task 5)
- [x] Test: select slot → Ctrl+Z → slot deselected; deselect → Ctrl+Z → slot re-selected

## Task 5: F1 — Keyboard Shortters

- [x] Add state: `let focusedCell = { day: null, hour: null };`
- [x] Add CSS: `.cell-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }`
- [x] Add CSS: `.help-overlay`, `.help-card`
- [x] Add HTML: help overlay div (hidden by default)
- [x] Add JS function `handleKeyDown(e)` — switch on key
- [x] Add JS function `focusCell(day, hour)`, `unfocusCell()`
- [x] Add JS function `showHelp()`, `hideHelp()`
- [x] Add event listener: `document.addEventListener('keydown', handleKeyDown)`
- [x] Test: arrow keys navigate; Enter/Space toggle; Escape closes; ? shows help

## Task 6: Changelog + Final Testing

- [x] Update `page-changelogs/replacement-arrangement-changelog.md` with all changes
- [x] Run `composer run lint:check` — confirm no new failures
- [x] Run `composer run types:check` — confirm no new failures
- [x] Test all 5 features together on desktop
- [x] Test mobile layout (≤768px) — progress bar, venue dropdown, toast work
- [x] Commit with prefix: `ui:`
