# Tasks: My Request History

## Task 1 — Create Blade template with header and base structure

- [x] Copy the `<!DOCTYPE html>` shell, `<head>` block (theme.css link, Inter font, dark/light IIFE, inline `<style>` tag) from `replacement-home-UI-design-template.blade.php`
- [x] Copy the complete top navigation bar HTML (`.top-bar` with logo, 5 nav links, theme toggle, notification bell, user panel) from `replacement-home-UI-design-template.blade.php`
- [x] Copy all header CSS (`.top-bar`, `.top-logo`, `.nav-items`, `.nav-item`, `.top-right`, `.theme-toggle`, `.notif-btn`, `.notif-badge`, `.user-panel`, `.user-profile`, `.user-avatar`, `.user-info`, `.user-name`, `.user-role`, `.logout-btn`)
- [x] Set "Replacement History" nav item as `.active` with `href="/my-request-history-ui"`
- [x] Create `.app-container` wrapper with `padding: 16px 24px; padding-top: 72px`
- [x] Add `toggleTheme()`, `updateIcon()`, `navigateHome()` JS functions
- [x] Add sort hint HTML div between toolbar and grid wrapper: `<div class="sort-hint">Click column headers to sort (Requested Time, Course Code, Date)</div>`

**Effort:** 1 hour

## Task 2 — Add page header, toolbar, table, badge, pagination, modal, summary, and empty state CSS

- [x] Add page header CSS + HTML: title "My Request History" with description
- [x] Add toolbar CSS (`.toolbar`, `.search-wrapper`, `.search-input`, `.filter-select`, `.result-count`, `.sort-hint`)
- [x] Add table CSS (`.timetable` base, sticky thead, hover highlight, zebra striping)
- [x] Add 12 column width classes (`.col-no`, `.col-requested-at`, `.col-code`, `.col-type`, `.col-date`, `.col-day`, `.col-time`, `.col-duration`, `.col-venue`, `.col-students`, `.col-cohort`, `.col-status`)
- [x] Add sort arrow CSS (`.sort-arrow`) — active column shows ▲ or ▼
- [x] Add 5 status badge CSS classes (`.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed`) using theme tokens
- [x] Add pagination CSS (`.pagination-bar`, `.pagination-info`, `.pagination-controls`, `.page-btn`)
- [x] Add summary bar CSS (`.summary-bar`, `.summary-card`, `.summary-value`, `.summary-label` with 4 color variants: Total Requests → `--color-error`, Approved → `--color-secondary`, Pending → `--color-tertiary`, Rejected → `--color-on-primary-container`)
- [x] Add modal CSS (`.modal-overlay`, `.modal`, `.modal-header`, `.modal-title`, `.modal-close`, `.modal-body`, `.modal-footer`) matching MyTimetable pattern
- [x] Add empty state CSS (`.empty-state`, `.empty-icon`, `.empty-title`, `.empty-text`)
- [x] Ensure all visual tokens (colors, fonts, spacing, border-radius) match `replacement-home-ui` — same header style, toolbar shape, table zebra/hover, badge rounding, pagination, summary card, modal overlay, empty state
- [x] Add responsive breakpoints: 1024px (table scrollable, toolbar wraps) and 768px (toolbar stacks, summary wraps to 2 columns, pagination stacks)

**Effort:** 1.5 hours

## Task 3 — Create mock data and renderTable function

- [x] Create `mockRequests` array with 20 entries following the distribution: 6 Pending, 5 Approved, 4 Rejected, 3 Completed, 2 Cancelled; 15 unique courses (5 reused as L+T pairs)
- [x] Each entry includes all fields: `id`, `requestedAt`, `courseCode`, `courseName`, `classType`, `classDate`, `classDay`, `timeStart`, `timeEnd`, `duration`, `venue`, `totalStudents`, `cohorts`, `status`, `rejectionReason`, `replacementDate`, `replacementTime`, `replacementVenue`, `reviewedBy`, `reviewedAt`, `remarks`
- [x] Per-status field conventions: Pending has null for reviewed/replacement fields; Approved has replacement + reviewer populated; Rejected has reviewer + rejectionReason; Cancelled has null reviewed/replacement; Completed has replacement + reviewer populated
- [x] Create helper functions: `to12h(t)`, `formatDate(iso)`, `statusClass(status)`
- [x] Declare state variables: `let currentPage = 1; const pageSize = 10; let sortState = { field: 'requestedAt', dir: 'asc' }; let currentFiltered = [];`
- [x] Create `renderTable()` implementing the full pipeline:
  1. Start with `mockRequests`
  2. Apply search filter (`.toLowerCase().includes()` on `courseCode` OR `courseName`)
  3. Apply status filter (if not "All Statuses")
   4. Apply week filter: `classDate >= weekStart && classDate <= weekEnd` (skip if "All Weeks")
   5. Apply sort using `sortState.field` and `sortState.dir`
   6. Set `currentPage = 1` if filter/sort changed from previous call
  7. Compute pagination offset
  8. Slice data for current page
  9. Build `<thead>` with sortable headers for Requested Time, Course Code, Date (show ▲/▼ arrow on active sort column)
  10. Build `<tbody>` rows with all 12 columns (apply transformations: 24h→12h, 'L'/'T'→Lecture/Tutorial, ISO→display date, status badge class, cohorts joined with `<br>`, rejection reason as subtitle). No. column formula: `((currentPage - 1) * pageSize) + rowIndex + 1` for global sequential numbering
  11. Update pagination controls and result count
- [x] Create `updatePagination()` and `updateResultCount()` functions (result count format: "Showing X of Y results" where X = filtered count, Y = total unfiltered count)

**Effort:** 2 hours

## Task 4 — Wire search, filter, sort, pagination, and modal interactions

- [x] Add search input `oninput` handler → reset page to 1, call `renderTable()`
- [x] Add status filter `onchange` handler → reset page to 1, call `renderTable()`
- [x] Add week filter `onchange` handler → reset page to 1, call `renderTable()`
- [x] Add sort toggle on Requested Time, Course Code, and Date column headers (click to toggle asc/desc, update arrow, rebuild table)
- [x] Add pagination button click handlers (prev, page numbers, next) — `currentPage` changes, call `renderTable()`
- [x] Create `openModal(request)` — populates modal body from the clicked row's data (render all fields from design §8, respecting conditional visibility: replacement fields hidden if null, reviewedBy/reviewedAt hidden if null, remarks hidden if null) and shows overlay
- [x] Create `closeModal()` — hides overlay, clears modal body
- [x] Wire modal close behaviors: click ✕, click Close button, click overlay background, press Escape key
- [x] Add `onclick="openModal(data[currentFiltered index])"` on each Status badge in the table rows

**Effort:** 1.5 hours

## Task 5 — Add empty state, summary stats, route, and changelog

- [x] Add empty state HTML (two variants: fully-empty and filtered-empty)
- [x] Show/hide empty state in `renderTable()` — when `mockRequests.length === 0` use fully-empty message; when filtered count === 0 use filtered-empty message
- [x] Hide table, pagination, and summary bar when empty
- [x] Create `updateSummary()` — compute counts from unfiltered `mockRequests`, populate 4 stat cards
- [x] Call `updateSummary()` on initial page load and after any data change
- [x] Add route `GET /my-request-history-ui` in `routes/web.php` returning view `ui-design-templates.my-request-history-UI-design-template`
- [x] Create `page-changelogs/my-request-history-changelog.md` with initial entry

**Effort:** 1 hour
