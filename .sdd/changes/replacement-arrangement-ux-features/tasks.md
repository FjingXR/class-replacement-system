# Tasks: Replacement Arrangement — UX Enhancement Features

## Task 1: F3 — Progress Indicator (start here — simplest, no JS dependencies)

- [ ] Add HTML `progress-wrapper` div above timetable (after venue selector)
- [ ] Add CSS: `.progress-wrapper`, `.progress-bar`, `.progress-fill`, `.progress-text`
- [ ] Add JS function `updateProgress()`
- [ ] Call `updateProgress()` inside existing `updateCounter()` function
- [ ] Test: select slots → progress bar updates; clear → resets

## Task 2: F4 — Venue Capacity Badge

- [ ] Remove hardcoded `<option>` elements from `buildingSelector` HTML
- [ ] Add JS function `buildVenueDropdown()` using `MockData.venues[]`
- [ ] Add CSS: `.venue-warning { color: var(--color-error); margin-left: 4px; }`
- [ ] Call `buildVenueDropdown()` in DOMContentLoaded
- [ ] Test: venue dropdown shows capacity; warning icon if capacity < students

## Task 3: F7 — Conflict Warning Toast

- [ ] Add JS function `checkConflict(dayIndex, hourIndex)` using `MockData.myTimetable.eventsByWeek[currentWeek]`
- [ ] Add toast CSS: `.toast { position: fixed; bottom: 24px; ... }`
- [ ] Add JS function `showToast(message, callback?)`
- [ ] Integrate `checkConflict()` into `toggleCell()` on selection
- [ ] Test: select slot that overlaps with myTimetable → yellow toast appears

## Task 4: F2 — Selection Undo (Ctrl+Z)

- [ ] Add state: `let selectionHistory = [];`
- [ ] Add JS function `pushHistory(entry)`
- [ ] Add JS function `undoSelection()` — pop last entry, reverse action
- [ ] Integrate `pushHistory()` into `toggleCell()` on both select and deselect
- [ ] Add Ctrl+Z handler in `handleKeyDown(e)` (see Task 5)
- [ ] Test: select slot → Ctrl+Z → slot deselected; deselect → Ctrl+Z → slot re-selected

## Task 5: F1 — Keyboard Shortters

- [ ] Add state: `let focusedCell = { day: null, hour: null };`
- [ ] Add CSS: `.cell-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }`
- [ ] Add CSS: `.help-overlay`, `.help-card`
- [ ] Add HTML: help overlay div (hidden by default)
- [ ] Add JS function `handleKeyDown(e)` — switch on key
- [ ] Add JS function `focusCell(day, hour)`, `unfocusCell()`
- [ ] Add JS function `showHelp()`, `hideHelp()`
- [ ] Add event listener: `document.addEventListener('keydown', handleKeyDown)`
- [ ] Test: arrow keys navigate; Enter/Space toggle; Escape closes; ? shows help

## Task 6: Changelog + Final Testing

- [ ] Update `page-changelogs/replacement-arrangement-changelog.md` with all changes
- [ ] Run `composer run lint:check` — confirm no new failures
- [ ] Run `composer run types:check` — confirm no new failures
- [ ] Test all 5 features together on desktop
- [ ] Test mobile layout (≤768px) — progress bar, venue dropdown, toast work
- [ ] Commit with prefix: `ui:`
