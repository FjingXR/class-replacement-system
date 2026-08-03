# Tasks: Replacement Arrangement Home Dashboard

## Task 1 — Create Blade template with header and base structure
- [x] Copy the `<!DOCTYPE html>` shell, `<head>` block (theme.css link, Inter font, dark/light IIFE, inline `<style>` tag) from `MyTimetable-UI-design-template.blade.php`
- [x] Copy the complete top navigation bar HTML (`.top-bar` with logo, 5 nav links, theme toggle, notification bell, user panel) from `MyTimetable-UI-design-template.blade.php`
- [x] Copy all header CSS (`.top-bar`, `.top-logo`, `.nav-items`, `.nav-item`, `.top-right`, `.theme-toggle`, `.notif-btn`, `.notif-badge`, `.user-panel`, `.user-profile`, `.user-avatar`, `.user-info`, `.user-name`, `.user-role`, `.logout-btn`) from `MyTimetable-UI-design-template.blade.php`
- [x] Set "Replacement Arrangement" nav item as `.active`; set "Replacement History" href to `/my-request-history-ui`
- [x] Create `.app-container` wrapper with `padding: 16px 24px; padding-top: 72px`
- [x] Add `toggleTheme()`, `updateIcon()`, `navigateHome()`, `goToReplacement()`, `goToReplacementWith()`, `populateWeekDropdown()` JS functions

**Effort:** 1 hour

## Task 2 — Add page header, toolbar, table, badge, pagination, summary, and empty state CSS
- [x] Add page title "Replacement Arrangement" with subtitle text "The following classes require replacement arrangements. Select a class to submit a replacement request." — CSS + HTML
- [x] Add toolbar CSS (`.toolbar`, `.search-wrapper`, `.search-icon`, `.search-input`, `.filter-input`, `.filter-select`, `.result-count`, `.sort-hint`)
- [x] Add table CSS (`.timetable` base, sticky thead, hover row highlight, zebra striping, min-width 1350px)
- [x] Add column width CSS for 14 columns (`.col-no`, `.col-code`, `.col-type`, `.col-week`, `.col-date`, `.col-day`, `.col-urgency`, `.col-time`, `.col-duration`, `.col-venue`, `.col-students`, `.col-cohort`, `.col-reason`, `.col-action`)
- [x] Add sort arrow CSS (`.sort-arrow`) — active column shows ▲ or ▼
- [x] Add urgency color CSS (`.urgency-high` red, `.urgency-mid` teal, `.urgency-low` default)
- [x] Add conflict reason badge CSS (`.badge-holiday`, `.badge-annual-leave`, `.badge-medical-leave`, `.badge-official-event`, `.badge-emergency-leave`)
- [x] Add pagination CSS (`.pagination-bar`, `.pagination-info`, `.pagination-controls`, `.page-btn`)
- [x] Add summary bar CSS (`.summary-bar`, `.summary-card`, `.summary-value`, `.summary-label` with 5 color variants: card-conflicted, card-venues, card-students, card-duration, card-courses)
- [x] Add empty state CSS (`.empty-state`, `.empty-icon`, `.empty-title`, `.empty-text`)
- [x] Add responsive breakpoints: 1024px (table scrollable, toolbar wraps) and 768px (toolbar stacks, summary wraps to 2 columns, pagination stacks)

**Effort:** 1.5 hours

## Task 3 — Create mock data and buildTable function
- [x] Create `conflictedClasses` array with 14 entries across 5 conflict reasons, each including `cohorts` array field
- [x] Create helper functions: `to12h(t)`, `formatDate(iso)`, `badgeClass(reason)`, `computeWeek(isoDate)`, `daysLeft(isoDate)`, `urgencyClass(days)`
- [x] Declare state variables: `let currentPage = 1; const pageSize = 10; let sortState = { field: 'date', dir: 'asc' }; let currentFiltered = [];`
- [x] Create `buildTable()` function implementing the full pipeline:
  1. Start with `conflictedClasses`
  2. Apply search filter (`.toLowerCase().includes()` on code OR name)
  3. Apply date range filter (`classDate >= dateFrom && classDate <= dateTo`, skip if empty)
  4. Apply week filter (`computeWeek(date) === weekVal`, skip if "All Weeks")
  5. Apply sort (`sortState.field` + `sortState.dir`) — default Date ascending
  6. After any filter/sort change, set `currentPage = 1`
  7. Compute offset = `(currentPage - 1) * pageSize`
  8. Slice data for current page
  9. Build `<thead>` with sortable headers for Date and Course Code (show ▲/▼ arrow on active sort column)
  10. Build `<tbody>` rows with 14 columns (apply transformations: 24h→12h, 'L'/'T'→Lecture/Tutorial, ISO→display date, Week number, Days Left with urgency color, cohorts joined with `<br>`, badge class, action button with `goToReplacementWith(code, date)` passing query params)
  11. Update pagination controls and result count ("Showing X of Y classes")
- [x] Create `updatePagination()` function (showing page range + page number buttons)
- [x] Create `updateResultCount()` function ("Showing X of Y classes")
- [x] Create `updateSummary()` function (computes 5 stats from filtered dataset: total conflicted, unique venues, student sum, duration sum, unique courses)

**Effort:** 2 hours

## Task 4 — Wire search, filter, sort, and pagination interactions
- [x] Add search input `oninput` handler → reset page to 1, call `buildTable()`
- [x] Add date range From/To `onchange` handlers → reset page to 1, call `buildTable()`
- [x] Add week dropdown `onchange` handler → reset page to 1, call `buildTable()`
- [x] Add sort toggle on "Date" and "Course Code" column headers (click to toggle asc/desc, update arrow, rebuild table)
- [x] Add pagination button click handlers (prev, page numbers, next) — `currentPage` changes, call `buildTable()`
- [x] Add `sortState` tracking and visual indicator (arrow on active sort column)

**Effort:** 1.5 hours

## Task 5 — Add empty state, summary dashboard, route, and changelog
- [x] Add empty state HTML (calendar SVG icon, heading text, subtext)
- [x] Show/hide empty state in `buildTable()` when filtered count === 0
- [x] Hide table, pagination, and summary bar when empty
- [x] Add summary dashboard HTML (5 cards: Total Conflicted, Venues Affected, Students Affected, Duration Hours, Distinct Courses) with color token mapping
- [x] Call `updateSummary()` after every filter/sort change
- [x] Add route `GET /replacement-home-ui` in `routes/web.php`
- [x] Create `page-changelogs/replacement-home-changelog.md` with initial entry documenting all implementation batches and refinements

**Effort:** 1 hour
