## proposal.md Round 1 — 2026-07-05

### 🟡 Addressed
- Active nav-link: "Replacement Arrangement" link is now explicitly marked `.active` on this page
- Sort behavior: Course code column sorts by code portion only (not concatenated display); clarified in-scope item
- Active-link strategy: Resolved — follows same pattern as other pages where the matching nav category gets `.active`

## proposal.md Round 2 — 2026-07-05

### 🔴 Addressed
- Arrange Replacement button context: Added note that the mock template navigates directly, but production would pass query params (`?course=...&date=...`)

### 🟡 Addressed
- Nav link href vs page URL: Clarified that the active "Replacement Arrangement" link intentionally points to `/replacement-arrangement` (legacy page); users reach this dashboard via direct URL or button flows
- Navigation path to dashboard: Added note about discoverability — users land here via direct URL or button clicks from other pages

## proposal.md Round 3 — 2026-07-05

### ✅ PASS
- No 🔴 issues. Proposal is frozen.

### 🟡 Will resolve in design.md
- Badge color mapping table for 5 conflict reasons
- Page title and description text
- Empty state message content and illustration
- Responsive behavior for 11-column table
- Pagination control spec (prev/next + page numbers)
- Live result count format and placement

## design.md Round 1 — 2026-07-05

### 🟡 Addressed
- Case-insensitive search implemented (toLowerCase)
- Action button navigation behavior documented (window.location.href with production query-param note)
- Filtered count vs pagination range clarified ("Filtered: X of Y classes" in toolbar, "1-10 of 14" in pagination)
- 24h→12h time conversion noted in renderTable data flow step 7

### ✅ PASS
- No 🔴 issues. Design.md is frozen.

## tasks.md Round 1 — 2026-07-05

### 🟡 Addressed
- Search filter: `+` changed to `or` with explicit `.toLowerCase().includes()` code
- Responsive breakpoints: added specific behavior descriptions (scrollable, wrap, sticky)
- renderTable pipeline: added explicit data transformation sub-items (24h→12h, L/T→Lecture/Tutorial, ISO→display date, badge class)
- Sort state: declared global `sortState` in Task 3 with note that click handlers come in Task 4
- Subtitle text: quoted exact text "The following classes require replacement arrangements..."
- Source file: referenced exact filename `MyTimetable-UI-design-template.blade.php`

### ✅ PASS
- No 🔴 issues. Tasks.md is frozen.

## Batch 4 (Implementation) — 2026-07-05

### ✅ All 5 tasks implemented
- Task 1: Blade shell + header + nav — created `replacement-home-UI-design-template.blade.php` (944 lines)
- Task 2: Page header, toolbar, table CSS, badge CSS, pagination CSS, empty state CSS, responsive breakpoints — all in same file
- Task 3: Mock data (14 entries, 5 conflict reasons), `buildTable()` function, helpers (`to12h`, `formatDate`, `badgeClass`), `updatePagination()`, `updateResultCount()`
- Task 4: Search oninput, reasonFilter onchange, sortable column headers (date & code), pagination prev/next/page-number clicks — all wired in DOMContentLoaded
- Task 5: Empty state HTML + show/hide logic in `buildTable()`, route `GET /replacement-home-ui` in `web.php`, `changelog.md` entry added

## Post-hoc SDD artifact sync — 2026-07-20

### 🔴 Scope & Design updates (back-ported from changelog)
The implementation evolved beyond the frozen SDD spec during build. All three artifacts (proposal.md, design.md, tasks.md) were updated to match the final implementation:

**proposal.md:**
- Nav: "Replacement History" href updated to `/my-request-history-ui`
- Toolbar: reason filter → date range + week dropdown + widened search (420px)
- Columns: 11 → 14 (added Week, Days Left/Urgency, Affected Cohort(s))
- Added: summary dashboard (5 cards), sort hint, default sort Date asc, pagination reset rule
- Out-of-scope: added reason filter removal note

**design.md:**
- Data structure: added `cohorts` field
- Toolbar ASCII: shows date range + week dropdown + sort hint
- Table spec: 14 columns with sort keys, widths, and formatting
- Data flow: date range → week → sort; removed reason filter; added summary update
- Added: §9 Summary Stat Cards (5 cards, color mapping, filtered counts)
- Responsive: removed "sticky first column" (not implemented)
- File changes: `page-changelogs/` path

**tasks.md:**
- Task 1–5 descriptions updated to match actual implementation details
- All checkboxes remain [x]