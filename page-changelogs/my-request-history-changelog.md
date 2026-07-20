# Changelog — Lecturer My Request History

## Files Changed

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 18:00 | — | Created page | New Blade template with full top nav (Replacement History active), toolbar (status + week dropdowns, search), 12-column sortable table, pagination (10/page), 4 summary cards (Total/Approved/Pending/Rejected), status badge modal, 2-variant empty state, dark/light theme toggle |
| 2026-07-20 18:00 | — | Mock data | 20 entries (6 Pending, 5 Approved, 4 Rejected, 3 Completed, 2 Cancelled), 15 unique courses (5 reused as L+T pairs), per-status field conventions (Pending/Cancelled null reviewer, Approved/Completed populated replacement, Rejected has rejectionReason) |
| 2026-07-20 18:00 | — | Week dropdown | Static week ranges (Week 1–4: 31 Aug – 27 Sep) replacing date range From/To inputs, filters by `classDate` within selected week bounds |
| 2026-07-20 18:00 | — | Design consistency | All visual tokens match `replacement-home-ui`: same nav bar, toolbar shape, table zebra/hover, badge rounding, pagination, summary card layout, modal overlay, empty state |
| 2026-07-20 18:14 | Line 783 (new) | Bug fix | Added `id="gridWrapper"` to `<div class="grid-wrapper">` — `getElementById('gridWrapper')` was returning null, causing JS crash and no mock data rendering |
| 2026-07-20 18:14 | Lines 755–775 | Bug fix | Moved `.search-wrapper` to first child of `.toolbar-left` (was rightmost), result count moved to `.toolbar-right` alone — search bar now leftmost in toolbar |
| 2026-07-20 18:14 | Lines 317–323 | Bug fix | Added `.filter-select option { background; color }` and `html.dark .filter-select { color-scheme: dark }` — dropdown option text was unreadable in dark mode |
| 2026-07-20 18:14 | Line 1078 | Change | Removed rejection reason text from badge — badge now shows only status name (e.g. "Rejected" without reason subtitle) |
| 2026-07-20 18:14 | Lines 1217–1220 | Change | Modal now shows `Rejection Reason` and `Remarks` as separate fields — previously conditionally showed one or the other based on status |
| 2026-07-20 18:20 | Lines 787, 1009–1020 | UX rename | Column headers: `Requested Time` → `Requested At`, `Date` → `Class Date`, `Day` → `Class Day`, `Time` → `Class Time` — disambiguates original class columns from request metadata |
| 2026-07-20 18:20 | Lines 1020, 1211–1213 | UX rename | `Status` → `Status (Click for detail)` column header; modal field labels `Day` → `Original Day`, `Time` → `Original Time` to match existing `Original Date` in modal |
| 2026-07-20 18:31 | Lines 384–396, 1055–1065, 1123–1134, 960–983 | Column refactor | Merged `Class Date`, `Class Day`, `Class Time`, `Hrs` into a single `Original Class` column with multi-line format (`Mon, 31 Aug 2026<br>09:00 AM to 11:00 AM (2 hrs)`). Added `Requested Replacement` column showing replacement date/time from mock data (`—` when no replacement). 12 columns → 10 columns. Added helper functions `dayAbbr`, `isoDayName`, `formatClassBlock`, `formatReplacementBlock`. Updated sort-hint, min-width (1350px → 1220px), and column widths. |
| 2026-07-20 18:41 | Lines 882–901 | Mock data | Added `replacementDate` and `replacementTime` to all 12 Pending/Rejected/Cancelled entries — every request now has proposed replacement data regardless of status |
| 2026-07-20 18:41 | Lines 978–983, 396–426 | Color coding | `formatReplacementBlock` now applies per-status CSS classes (`status-pending`, `-approved`, `-rejected`, `-cancelled`, `-completed`) to the replacement time text, giving each status a distinct color (amber/green/red/grey/blue) |
| 2026-07-20 18:41 | Lines 970–983 | Format | Added `getWeekNumber` helper; both `Original Class` and `Requested Replacement` blocks now show `(Week N)` after the date |
| 2026-07-20 18:41 | Lines 1062–1064 | Column rename | `Venue` → `Requested Venue`, `Cohort(s)` → `Affected Cohort(s)` |
| 2026-07-20 18:41 | Lines 409–411, 974 | Duration style | Changed `.class-dash` → `.class-duration` with `color: var(--color-on-surface)` and `font-weight: 500` for better visibility |
| 2026-07-20 18:52 | Lines 1274–1340, 1285–1295 | Modal restructure | Grouped fields into 3 sections (`General Info`, `Original Class Detail`, `Requested Replacement Class`) with section headers. Rejection reason/Remarks now appear immediately below Status. `Venue` → `Original Venue`. Added `cohortCounts` field to 5 multi-cohort entries; Total Students shows breakdown (`20 + 15 = 35`) for multi-cohort entries. |
| 2026-07-20 19:06 | Lines 1298, 1319 | Modal polish | Status field now shows brief description after badge (e.g. `Pending` badge + "Awaiting approval"). `Replacement Venue` now shows `—` for entries without a venue, making it visible for every status. |
| 2026-07-20 19:12 | Lines 1306 | Modal cleanup | Removed `Remarks` field from modal entirely. |
| 2026-07-20 19:15 | Lines 1150, 1152 | Bug fix | Empty state: `gridWrapper` and `summaryBar` now hidden (`'none'`) when filtered results are empty — previously showed table header + stacked summary cards alongside empty state message. |
| 2026-07-20 19:29 | Lines 896–904, 1127–1132, 1464–1474 | Feature | Added `Hide Completed` toggle (default: on) — filters out Completed entries from table and summary. Added `✕ Clear` button in toolbar-right — resets search, status filter, week filter, and completed toggle to defaults. |
| 2026-07-20 19:32 | Lines 896, 899, 904 | UX | Renamed `Hide Completed` → `Exclude Completed`, `✕ Clear` → `Reset Filters`. Moved button to toolbar-left after the toggle. |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 18:00 | Line 28 (new) | New route | Added `Route::get('/my-request-history-ui', ...)` returning view `ui-design-templates.my-request-history-UI-design-template` |
