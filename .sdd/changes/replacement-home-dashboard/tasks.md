# Tasks: Replacement Arrangement Home Dashboard

## Task 1 — Create Blade template with header and base structure
- [x] Copy the `<!DOCTYPE html>` shell, `<head>` block (theme.css link, Inter font, dark/light IIFE, inline `<style>` tag) from `MyTimetable-UI-design-template.blade.php`
- [x] Copy the complete top navigation bar HTML (`.top-bar` with logo, 5 nav links, theme toggle, notification bell, user panel) from `MyTimetable-UI-design-template.blade.php`
- [x] Copy all header CSS (`.top-bar`, `.top-logo`, `.nav-items`, `.nav-item`, `.top-right`, `.theme-toggle`, `.notif-btn`, `.notif-badge`, `.user-panel`, `.user-profile`, `.user-avatar`, `.user-info`, `.user-name`, `.user-role`, `.logout-btn`) from `MyTimetable-UI-design-template.blade.php`
- [x] Set "Replacement Arrangement" nav item as `.active`
- [x] Create `.app-container` wrapper with `padding: 16px 24px; padding-top: 72px`
- [x] Add `toggleTheme()`, `updateIcon()`, `navigateHome()`, `goToReplacement()` JS functions

**Effort:** 1 hour

## Task 2 — Add page header, toolbar, and table CSS
- [x] Add page title "Replacement Arrangement" with subtitle text "The following classes require replacement arrangements. Select a class to submit a replacement request." — CSS + HTML
- [x] Add toolbar CSS (`.toolbar`, `.search-bar`, `.filter-dropdown`, `.result-count`)
- [x] Add table CSS (`.timetable` base, sticky thead, hover row highlight, zebra striping)
- [x] Add column width CSS for all 11 columns
- [x] Add conflict reason badge CSS (`.badge-holiday`, `.badge-annual-leave`, `.badge-medical-leave`, `.badge-official-event`, `.badge-emergency-leave`)
- [x] Add pagination CSS (`.pagination-bar`, `.page-btn`)
- [x] Add empty state CSS (`.empty-state`)
- [x] Add responsive breakpoints: 1024px (table horizontally scrollable, toolbar wraps to 2 rows) and 768px (sticky first column, toolbar stacks vertically)

**Effort:** 1 hour

## Task 3 — Create mock data and renderTable function
- [x] Create `conflictedClasses` array with 14 entries across 5 conflict reasons
- [x] Create `to12h(t)` helper function (24h → 12h AM/PM)
- [x] Create `renderTable()` function implementing the full pipeline:
  - Search filter (case-insensitive, code OR name matches — `.toLowerCase().includes(query)`)
  - Conflict reason filter
  - Sort by date or course code (asc/desc toggle) — reads from global `sortState` object, declared in this task
  - Pagination (page size 10, compute offset/slice)
  - Build `<tr>` elements with 11 columns, applying transformations:
    - 24h `timeStart`/`timeEnd` → 12h AM/PM format using `to12h()` helper
    - `'L'` → `"Lecture"`, `'T'` → `"Tutorial"`
    - ISO date `2026-09-04` → display `"04 Sep 2026"`
    - Conflict reason → badge CSS class (see design.md Section 3)
  - Action button with `window.location.href = '/replacement-arrangement'`
- [x] Declare `let sortState = { field: 'date', dir: 'asc' }` (wired with click handlers in Task 4)
- [x] Create `updatePagination()` function (showing page range + page number buttons)
- [x] Create `updateResultCount()` function ("Filtered: X of Y classes")

**Effort:** 1.5 hours

## Task 4 — Wire search, filter, sort, and pagination interactions
- [x] Add search input `oninput` handler → call `renderTable()`
- [x] Add conflict reason dropdown `onchange` handler → call `renderTable()`
- [x] Add sort toggle on "Date" and "Course Code" column headers (click to toggle asc/desc, show direction arrow)
- [x] Add pagination button click handlers (prev, page numbers, next)
- [x] Add `sortState` tracking and visual indicator (arrow on active sort column)

**Effort:** 1 hour

## Task 5 — Add empty state and route
- [x] Add empty state HTML (calendar SVG icon, heading text, subtext)
- [x] Show/hide empty state in `renderTable()` when filtered count === 0
- [x] Hide table and pagination when empty
- [x] Add route `GET /replacement-home-ui` in `routes/web.php`
- [x] Update `changelog.md` with new page entry

**Effort:** 0.5 hour
