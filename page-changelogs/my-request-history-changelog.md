# Changelog — Lecturer My Request History

## [2026-08-16] Replaced hardcoded inline styles with shared utility classes

Refactored 3 hardcoded inline style instances to use shared CSS classes from `theme.css`. Paragraph descriptions use `.page-desc`, title+badge rows use `.kicker` + `.badge-sm`.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Line | Before | After |
|---|---|---|
| 589 | `style="font-size:14px;color:..."` | `class="page-desc" style="color:var(--color-on-surface);line-height:1.5;margin-bottom:12px"` |
| 627-630 | `style="display:flex;align-items:center;gap:6px"` + badge inline override | `class="kicker"` + `class="badge badge-sm"` |
| 636 | `style="font-size:14px;color:..."` | `class="page-desc" style="color:var(--color-on-surface);line-height:1.5;margin-bottom:8px"` |

---

## [2026-08-15] Promote bulk selection to shared OOP BulkSelection class

### Summary

Replaced the page-local `selectedIds` Set + `updateBulkBar()`/`clearAllSelections()` DOM code with the shared `BulkSelection` class from `ui-common.js` (same OOP pattern as `ModalController`). The local `bulk` instance is configured with `checkboxSelector: '.row-checkbox'` and `headerCheckboxId: 'headerCheckbox'` so `bulk.clear()` also resets row + header checkboxes. `updateBulkBar()`/`clearAllSelections()` now just delegate to `bulk.updateBar()`/`bulk.clear()`.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| State | Updated | `let selectedIds = new Set()` → `let bulk = new BulkSelection({...checkboxSelector, headerCheckboxId})`. |
| Header/row checkbox handlers | Updated | Use `bulk.add/delete`; drop redundant `updateBulkBar()` calls. |
| `updateBulkBar()` | Updated | Delegates to `bulk.updateBar()`. |
| `clearAllSelections()` | Updated | Delegates to `bulk.clear()` (which resets row + header checkboxes). |
| Batch cancel | Updated | `selectedIds.*` → `bulk.*`. |

---

## [2026-08-15] Detail modal: status description as its own row

Status description now shown in a dedicated **Status Description** row (was inline caption). Description from `statusDesc()` map.

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `openModalById()` | Updated | Split `Status` (badge) and `Status Description` rows. |

---

## [2026-08-15] Detail modal refinements: gray × close, body status badge, split rows, dot colours

### Summary

- **Close button** — normal gray `×` (was red dot); removed header `modal-status-badge`.
- **Status badge + description in body**.
- **One value per row** — Subject Code/Name, Start/End Time split; status description via `statusDesc`.
- **Timeline dot colours vary by status** (`dot-*`).

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Removed header `modal-status-badge`. |
| `openModalById()` | Updated | Split rows, timeline `dot-*`, removed `status` header cfg. |

---

## [2026-08-15] Detail modal redesign: category tabs + unified detail sheet

### Summary

The Request Details modal now uses the shared `DetailModal` "Detail Sheet" system:
- Identity header (left-aligned title + subtitle, status badge top-right).
- Global horizontal timeline (Request Submitted → Under Review → Status) pinned above the tabs.
- Category tabs: **General Info / Original Class / Replacement Class**.
- Groups instead of boxed sections; definition rows without per-row borders; `--strong`/`--muted` emphasis.
- Removed the old `div.timeline` page CSS; all fields preserved.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | `.modal-title#modalTitle` + `.modal-status-badge`; body emptied. |
| `openModalById()` | Rewritten | Uses `DetailModal.render` with global timeline + 3 tabs. |
| Page styles | Removed | Old `.timeline`/`.timeline-*` F6 CSS block. |

---

## [2026-08-15] Row polish: plain class time, consistent AM/PM, corrected tips, age legend

### Summary

- **Class time plain text** — "Requested Replacement" time no longer gets a coloured `status-*` chip; removed the page-specific `.class-time.status-*` badge CSS. It's plain bold text.
- **Consistent time format** — replacement time ranges now render as `11:00 AM to 1:00 PM` via `DateHelper.format12hRange` (table + modal); modal "Original Time" uses `to` between 12h times.
- **Header tips** — Requested At tip documents age colours; Original Class tip corrected ("its state varies…" — not all are being replaced).
- **Request age** — `requestAgeHtml` now relative to actual today (was hardcoded `2026-08-29`), shows **Today**/**Yesterday**, keeps colour legend (green ≤1 day, amber 2–3, red 4+). Guide block documents the colours.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Page styles | Updated | Removed `.col-replacement .class-time.status-*` badge CSS block. |
| `replacementBlock` call | Updated | `replacementBlock(r, { showVenue: false, colorStatus: false })`. |
| Header columns | Updated | Requested At + Original Class tips rewritten. |
| Details modal | Updated | Original Time `to12h(a) to to12h(b)`; Replacement Time via `format12hRange`. |
| Guide block | Updated | Added request-age colour legend item. |
| Summary bar | Updated | Page-specific card descriptions. |

---

## [2026-08-15] Nav label, Requested Replacement column, status-chip colors

### Summary

Three page-scoped polish changes to the My Request History page:
1. The header nav label "Replacement History" was renamed to "Request History" for this page only (throttled per-page via `$navItems` override through the shared layout).
2. The "Requested Replacement" column no longer renders venue data (the separate "Requested Venue" column already shows it); its header tooltip was updated accordingly. The shared `HtmlBuilder.replacementBlock()` gained a `showVenue` option so venue is still shown on other pages (request-approval) that use the shared helper.
3. The `class-time.status-*` time chips on this page now use `color-*-container` backgrounds with `color-on-*-container` font colors (consistent with status badges) instead of flat `--color-warning/success/error/primary` text.

### Files Changed

#### `resources/views/layouts/ui-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| nav-bar include | Updated | Pass `'navItems' => $navItems ?? null` through to `partials.ui-nav-bar` so a page/route can override nav labels per-page. `$navItems` defaults to `null` → partial uses its built-in defaults for all other pages. |

#### `routes/web.php`

| Location | Change | Detail |
|---|---|---|
| `GET /my-request-history-ui` | Updated | Pass a `navItems` array with the replacement-history label changed to "Request History"; other nav items identical to the shared default. |

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `HtmlBuilder.replacementBlock(r, opts)` | Updated | Accepts optional `opts.showVenue`. When `false`, omits the `<span class="class-venue">` from the block. Default (`opts` absent) keeps venue, preserving request-approval rendering. |

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `columns[]` Requested Replacement | Updated | Header tooltip changed from "Proposed new date, time, and venue" → "Proposed new date and time". |
| table body `col-replacement` cell | Updated | Calls `HtmlBuilder.replacementBlock(r, { showVenue: false })` so no venue renders in the column. |
| page-styles | Added | Page-scoped `.col-replacement .class-time.status-*` rules: `status-pending` → tertiary-container/on-tertiary-container, `status-approved` → success-container/on-success-container, `status-rejected` → error-container/on-error-container, `status-cancelled` → surface-variant/on-surface-variant, `status-completed` → primary-container/on-primary-container; each with `display:inline-block; padding:2px 8px; border-radius:var(--radius-xs)`. Scoped to this page only (via `.col-replacement` + this page's `<style>` block). |

---

## [2026-08-15] Summary stats now follow the week/status/search filters

### Summary

The summary cards previously always showed stats for the whole semester (all 20 requests) regardless of the active week/status/search filter — contradicting the hint text "Stats are for this week only". Now `updateSummary()` reads from `currentFiltered` instead of the full `mockRequests` array, so the cards always reflect the selected period (All Weeks or Week N, plus any search/status/Exclude-Completed filters).

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `updateSummary()` | Updated | Total/Approved/Pending/Rejected/Hours now computed from `currentFiltered` (the filtered table data) instead of the full `mockRequests` list. |

#### `resources/views/partials/ui-summary-bar.blade.php`

| Location | Change | Detail |
|---|---|---|
| summary hint | Updated | Hint text changed from "Stats are for **this week only**." to "Stats are for the **selected period** (filters apply)." since stats now reflect the selected filters. |

---

## [2026-08-13] Phase 3 UX Enhancement: Collapsible Guide Block

### Summary

Added an expandable guide block with page-specific workflow instructions.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 290-299 | Added | `@include('partials.ui-guide-block')` with 6 workflow tips |

---

## [2026-08-13] Phase 3 UX Enhancement: Table Header Tooltips

### Summary

Added hover tooltips to all column headers for better usability.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 552-562 | Added | `tip` property to each column definition with user-friendly descriptions |
| 2026-08-13 | Lines 564-569 | Modified | `makeSortableHeader()` now uses `col.tip` for `title` attribute |

---

## [2026-08-13] Fix blank right-side gap — gridWrapper flex root cause

### Summary

The large blank space on the right of the timetable was NOT caused by `table-layout` — it was caused by the page JS setting `gridWrapper.style.display = 'flex'` in `renderTable()`. A flex container's child (`.grid-scroll`) shrinks to its content width instead of stretching to fill, so the table only occupied ~1479px of an ~1864px wrapper, leaving ~385px of empty space.

Fix: `renderTable()` now resets `gridWrapper.style.display = ''` (block default), matching the behavior of the `/request-approval-ui` page. The table now fills the wrapper width via the existing `width: 100%` + `table-layout: auto` from `theme.css`, with `th` widths acting as preferred minimums so columns expand proportionally (not equally) on desktop.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | `renderTable()` show branch | Fix | Changed `gridWrapper.style.display = 'flex'` → `''` (block). Flex made `.grid-scroll` a shrink-to-content flex item, leaving blank space on the right. Block makes it stretch to fill the wrapper. |

### Verified (no page-level horizontal overflow)

Tablet/small laptop (1200px, 1024px, 900px): table keeps readable column widths (~1317px natural content width); horizontal scrolling happens inside `.grid-scroll`, not the page. Mobile (≤768px): card layout preserved, table hidden. `body { overflow-x: hidden }` (theme.css:141) is a pre-existing base rule and is NOT relied upon — verified no page overflow even with `body { overflow-x: visible }`. No cell content overflow at any tested width.

---

## [2026-08-13] Table layout: fixed → auto with th widths (fill container)

### Summary

Changed timetable from `table-layout: fixed` to `auto` (inherited from `theme.css`). Added explicit `width` on `th` elements to force columns to expand and fill the container — eliminates dead space on the right at full screen. `table-layout: auto` still allows content to wrap naturally. Horizontal scroll kicks in when screen is too small.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | `@section('page-styles')` | Changed | Removed `table-layout: fixed` override and `min-width: 1120px`. Added explicit `th` widths: `.col-requested-at` 150px, `.col-code` 220px, `.col-original` 200px, `.col-replacement` 200px, `.col-venue` 110px, `.col-students` 80px, `.col-cohort` 140px, `.col-status` 120px, `.col-actions` 100px. Table now fills full container width with no dead space. |

---

## [2026-08-13] Table layout: fixed → auto (responsive)

### Summary

Changed timetable from `table-layout: fixed` to `auto` (inherited from `theme.css`). Removed all explicit column widths — columns now size by content and adapt to screen width. Added `min-width: 1120px` to prevent table from collapsing too narrow. Horizontal scroll kicks in naturally when screen is too small.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | `@section('page-styles')` | Changed | Removed `table-layout: fixed` override and all explicit column widths (`.col-code`, `.col-original`, `.col-replacement`, `.col-venue`, `.col-students`, `.col-cohort`, `.col-status`, `.col-actions`, `.col-requested-at`). Added `min-width: 1120px` on `.grid-scroll .timetable`. Table now uses `table-layout: auto` from `theme.css`. |

---

## [2026-08-13] Bug fixes: skeleton loading + emptyCta null reference

### Summary

Fixed skeleton loading bug where `hideSummary()` restored stale innerHTML from `dataset.original`, overwriting correct values set by `updateSummary()`. Fixed null reference error on `emptyCta` element (missing ID in shared partial).

### Files Changed

#### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | `SkeletonLoader.hideSummary()` | Fixed | Removed `el.innerHTML = el.dataset.original` restore — `updateSummary()` already sets correct values via `textContent`. Now only cleans up `dataset.original`. |

#### `resources/views/partials/ui-empty-state.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Line 17 | Fixed | Added `id="emptyCta"` to CTA button — JS references this ID but it was missing, causing null reference in `renderTable()` empty-state branches. |

---

## [2026-08-10] Phase 2 Template Migration: Inline helpers → Shared OOP classes

### Summary

Refactored `renderCards()` to use `HtmlBuilder.myRequestCard()`.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-10 | Lines 760-773 | Refactored | `renderCards()` now uses `HtmlBuilder.myRequestCard(r, opts)` |

#### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-10 | HtmlBuilder class | Added | `HtmlBuilder.myRequestCard(r, opts)` — constructs my-request-history card HTML |

---

### Summary

SDD change request applied. Bug fix: `openModal(index)` now delegates to `openModalById(id)` for consistent modal handling. Feature alignment: unified timeline classes (`.timeline-step`, `.timeline-dot`, `.timeline-connector`, `.timeline-text`) and unified request age format (`age-fresh`/`age-waiting`/`age-stale` with colored dots). 11 CSS classes promoted to `theme.css` — no duplicated CSS remains in page template.

### Files Changed

#### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 | JS: openModalById | Bug fix | Unified modal opener — `openModal(index)` now delegates to `openModalById(id)` |
| 2026-08-06 | CSS: timeline classes | Feature alignment | Timeline uses unified `.timeline-step`, `.timeline-dot`, `.timeline-connector`, `.timeline-text` classes (matching request-approval pattern) |
| 2026-08-06 | CSS: request age | Feature alignment | Unified age format: `.age-fresh` (≤1 day, primary color), `.age-waiting` (≤3 days, tertiary color), `.age-stale` (>3 days, error color) with colored dots |
| 2026-08-06 | Page styles | CSS dedup | Verified no duplicated CSS classes remain — promoted classes (status badges, buttons, bulk selection) now only in `theme.css` |

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 | After line 1741 | CSS promotion | Added 11 promoted classes shared by request-approval and my-request-history: `.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed`, `.modal-section-title`, `.btn-danger`, `.btn-outline`, `.col-checkbox`, `.row-selected`, `.bulk-checkbox` |

---

## [2026-08-03] Refactor: Rows Per Page promoted to shared OOP component

### Changed
- **Rows Per Page (RPP)**: Removed local CSS/HTML/JS; now uses shared `partials.ui-rpp` Blade partial + `initRpp()` from `ui-common.js` + `.rpp-wrapper`/`.rpp-select` from `theme.css`
- localStorage key changed from `'mrh-rows-per-page'` to `'rpp-page-size'` (shared across pages)

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
| 2026-07-21 07:35 | Lines 637–640, 927–944 | UX | Swapped summary colors: Total now uses `--color-on-primary-container` (neutral), Rejected now uses `--color-error` (red). Added visual wrapper to Total card: `border: 2px solid var(--color-primary)` + `background: var(--color-primary-container)`. |
| 2026-07-21 07:35 | Lines 732–736, 969–978, 1435–1449 | Feature | Added `Cancel Request` button in modal footer-left for Pending status only (hidden for other statuses). Button shows red danger styling, prompts browser confirmation dialog on click, then alerts and closes modal. Modal footer now uses flex layout with `modal-footer-left` and `modal-footer-right` sections. |
| 2026-08-01 14:31 | Lines 164–175 | New summary card | Added `Replacement Hours` summary card (2nd position, after Total Requests) — sums `duration` across all requests (shows 40 hrs for 20 × 2-hr requests). Card style: `1px dashed outline-strong` border + `surface-variant` background, value color `on-surface` — matches `card-hours` pattern on timetable pages. |
| 2026-08-01 14:31 | Lines 390–399 | Summary bar 5 cards | Added `card-hours` entry to `@include('partials.ui-summary-bar')` — summary bar now has 5 cards (Total Requests, Replacement Hours, Approved, Pending, Rejected). Base grid in `theme.css` already uses `repeat(5, 1fr)`. |
| 2026-08-01 14:31 | Lines 744–759 | Summary logic | `updateSummary()` now sums `r.duration` across all requests (consistent with Total counting all 20, independent of Exclude Completed toggle) and updates `summaryHours`. |
| 2026-08-01 15:01 | Line 341 | Semester chip | Added `semester-chip` pill (`202605 Semester · 15-Jun-2026 ~ 20-Sep-2026`) after page title — same style/position as my-timetable and cohort-timetable. CSS moved to shared `theme.css`. |

| 2026-08-02 14:00 | Lines 316–336 | F1: CSS | Added `.rows-per-page-wrapper`, `.rows-per-page-select` styles for rows-per-page dropdown in pagination bar |
| 2026-08-02 14:00 | Lines 540–548 | F1: HTML | Added `<select>` with options 10/25/50/All in pagination bar (bottom-left) |
| 2026-08-02 14:00 | Lines 712, 788–790 | F1: JS state | Added `rowsPerPage` state variable (reads from localStorage), `renderTable()` uses dynamic `effectivePageSize` |
| 2026-08-02 14:00 | Lines 1103–1110 | F1: JS handler | Added `rowsPerPage` change handler — updates `rowsPerPage`, saves to localStorage, resets page, re-renders |
| 2026-08-02 14:00 | Lines 338–388 | F2: CSS | Added `.bulk-checkbox`, `.col-checkbox`, `.row-selected`, `.bulk-action-bar`, `.btn-bulk-cancel` styles |
| 2026-08-02 14:00 | Lines 553–557 | F2: HTML | Added floating bulk action bar with count text and "Cancel Selected" button |
| 2026-08-02 14:00 | Lines 715, 798–820, 880–899 | F2: JS | Added `selectedIds` Set, checkbox column in header/rows, `toggleSelectAll`, `toggleSelectRow`, `highlightSelectedRows`, `updateBulkBar`, `bulkCancelSelected` |
| 2026-08-02 14:00 | Lines 390–393 | F3: CSS | Added `.age-green`, `.age-amber`, `.age-red` text color classes |
| 2026-08-02 14:00 | Lines 718–730, 877–878, 902 | F3: JS | Added `getRequestAge()` and `ageClass()` helpers; "Requested At" cell wrapped in age-colored span |
| 2026-08-02 14:00 | Lines 395–414 | F4: CSS | Added `.col-actions`, `.btn-inline-cancel` styles (hidden by default, shown on row hover) |
| 2026-08-02 14:00 | Lines 832, 911 | F4: HTML | Added empty actions column header + inline Cancel button in Pending rows |
| 2026-08-02 14:00 | Lines 959–966 | F4: JS | Added `quickCancel(id)` — confirms then removes request from mock data |
| 2026-08-02 14:00 | Lines 732–752 | F5: JS | Added `saveFilters()` and `restoreFilters()` — serializes filter state to localStorage |
| 2026-08-02 14:00 | Lines 1087–1110 | F5: JS wiring | Added `saveFilters()` calls in all filter handlers; `restoreFilters()` on DOMContentLoaded; Reset Filters clears localStorage |
| 2026-08-02 14:00 | Lines 416–462 | F6: CSS | Added `.timeline`, `.timeline-item`, `.timeline-dot-wrap`, `.timeline-dot`, `.timeline-line`, `.timeline-text`, `.timeline-label`, `.timeline-time` styles |
| 2026-08-02 14:00 | Lines 1047–1057 | F6: JS | Added timeline section in modal for Pending/Approved/Rejected: Request Submitted → Under Review → Final Status |
| 2026-08-02 14:00 | Lines 416–418 | F7: CSS | Added `.row-focused` outline style for keyboard focus indicator |
| 2026-08-02 14:00 | Lines 716, 1116–1138 | F7: JS | Added `focusedRowIndex` state, `keydown` handler (ArrowDown/Up/Enter/Escape), `highlightFocusedRow()`, `clearFocusedRow()` |
| 2026-08-02 15:00 | Lines 735–739, 394 | UX | Added `relativeTime(days)` helper + `.age-relative` CSS; "Requested At" cell now shows "(n days ago)" after formatted date |
| 2026-08-02 15:00 | Line 837 | UX | Changed blank `col-actions` header label from empty to `'Quick Cancel'` |
| 2026-08-02 15:00 | Lines 586–631 | UX | Replaced JS `confirm()` with two dedicated modals: single cancel confirm (shows request details) + batch cancel confirm (shows list of selected requests) |
| 2026-08-02 15:00 | Line 559 | UX | Renamed `bulkCancelBtn` → `btnBatchCancelRequest` to reduce ambiguity |
| 2026-08-02 15:00 | Lines 558, 395–407 | UX | Added "Clear" button to bulk action bar (`.btn-bulk-clear`), wired to `clearAllSelections()` |
| 2026-08-02 16:00 | Lines 483–524 | Feature | Added responsive card view (`.card-view`) — on mobile (<768px), table hidden and requests shown as stacked cards with code, status, date, time, venue, age |
| 2026-08-02 16:00 | Lines 1309–1317 | Feature | Added deep link support — URL `?id=N` auto-opens modal for that request on page load |
| 2026-08-02 16:00 | Lines 1175–1205 | Feature | Added `renderCards()` — builds card view from `currentFiltered` data, click/Enter opens modal |
| 2026-08-02 | `@section('page-scripts')` | Centralised mock data (Task 9) | `mockRequests` array moved to `public/js/mock-data.js` — page now reads from `MockData.requests`. Semester chip now reads `MockData.semester.chipText` dynamically. |
| 2026-08-02 | Lines 644–648, CSS | Week picker arrows | Replaced plain `<select class="filter-select">` for weekFilter with `<div class="week-picker">` containing prev/next arrow buttons + `week-select` dropdown (matching `replacement-home-ui`). Added CSS: `.week-picker`, `.week-arrow`, `.week-select`. Added JS: `weekFilterChanged()`, `prevWeekFilter()`, `nextWeekFilter()`, `updateWeekArrowState()`. Week filter now resets to "All Weeks" on page refresh (removed from `saveFilters()`/`restoreFilters()`). |

### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 04:30 | End of file | Toast/Undo Bar CSS | Added `.toast-bar` styles (position fixed, bottom-left z-index 200, surface bg, outline border, shadow, radius-md). Added `.toast-message`, `.toast-undo` (outline primary button), `.toast-close` (borderless). Added `@keyframes toastSlideUp` (translateY 20px→0, 0.3s ease). |

### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 04:30 | End of file | Toast/Undo Bar JS | Added `showToast(message, undoCallback, duration=5000)` — shows toast bar, sets undo onclick, starts 5s auto-dismiss timer. Added `dismissToast()` — hides toast, clears timer. |

### `resources/views/layouts/ui-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 04:30 | Before `</body>` | Toast/Undo Bar HTML | Added `<div class="toast-bar" id="toastBar">` with `.toast-message`, `.toast-undo` (hidden by default), `.toast-close` ✕ button. |

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-03 04:30 | Lines 1163–1172 | Single cancel toast | Refactored `confirmCancelAction` click handler: saves removed item before splice, calls `showToast('Request #{id} cancelled.', undoCallback)` to allow re-insertion. |
| 2026-08-03 04:30 | Lines 1203–1208 | Batch cancel toast | Refactored `confirmBatchCancelAction` click handler: saves removed items before filter, calls `showToast('{N} requests cancelled.', undoCallback)` to allow re-insertion. |
| 2026-08-03 05:30 | Line 775 | Bug fix | Changed `const mockRequests` to `let mockRequests` — batch cancel handler was reassigning the variable (`mockRequests = mockRequests.filter(...)`) which threw `TypeError: Assignment to constant variable`. |

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` — OOP Phase 1 partial extraction

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-04 | — | Refactored: replaced inline page-header/week-nav/empty-state/grid-table with `@include('partials.…')` (OOP Phase 1) | Page uses `ui-page-header`, `ui-week-nav`, `ui-grid-table`, `ui-empty-state` partials. |
| 2026-08-06 | Lines 758, 835–838 | Column merge | Merged `Type` column into `Course Code & Name` — now shows `BMIT6767 (T)` / `Object-Ooped Programming` on two lines. Removed `.col-type` CSS, reduced min-width 1315px→1235px. |

### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 | Lines 8–12 | Scrollbar visibility | Increased scrollbar height 6px→8px, added track background (`surface-variant`), added `scrollbar-color`/`scrollbar-width` on `.grid-scroll` for cross-browser visibility |
| 2026-08-06 | Line 514 | Cell type style | Added `.cell-type` (opacity 0.5, font-size 12px) for inline type label ` (L)` / ` (T)` in merged Course Code column |
| 2026-08-06 | Line 834, 74 | Requested At two-line format | Added `<br>` between date/time and relative age — now shows `30 Jun 2026, 8:03 PM` / `(37 days ago)`. Added `white-space: normal` to `.timetable td.col-requested-at` to override global `nowrap`. |
| 2026-08-06 | Line 419 | Scrollbar always-visible | Changed `.grid-scroll` from `overflow-x: auto` to `overflow-x: scroll` so horizontal scrollbar is always rendered (not just on hover), ensuring users discover scrollable right-side columns |
| 2026-08-13 | `@section('page-styles')` | macOS-style update (Phase 4) | Removed toggle CSS (promoted to `theme.css`). Removed filter-chip CSS (promoted to `theme.css`). Removed hardcoded status colors `#f59e0b`/`#10b981`/`#ef4444`/`#3b82f6` (now in `theme.css`). Fixed `.btn-bulk-cancel:hover` and `.btn-inline-cancel:hover` color from `#fff` to `var(--color-on-error)`. |
| 2026-08-13 | Lines 370–395, 390–408 | macOS modal button order | Reordered cancel-confirm and batch-cancel modal footers: dismiss button ("No, Keep It" / "No, Keep Them") moved to `modal-footer-left`, destructive action ("Yes, Cancel Request" / "Yes, Cancel All") stays in `modal-footer-right` — matches macOS Cancel-left / action-right convention. |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 18:00 | Line 28 (new) | New route | Added `Route::get('/my-request-history-ui', ...)` returning view `ui-design-templates.my-request-history-UI-design-template` |

### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | End of file | Promoted toggle + filter-chip CSS | Moved `.toggle-wrapper`/`.toggle-track`/`.toggle-thumb`/`.toggle-label` and `.filter-chips`/`.filter-chip`/`.filter-chip-remove` from page-specific to shared `theme.css` — used by my-request-history and request-approval. Added `:focus-within` ring on toggle. Fixed `toggle-thumb` background from `#fff` to `var(--color-surface)`. Fixed `filter-chip-remove:hover` color from `#fff` to `var(--color-on-primary)`. |
| 2026-08-13 | End of file | Status time colors | Added `.class-time.status-pending` → `var(--color-warning)`, `.status-approved` → `var(--color-success)`, `.status-rejected` → `var(--color-error)`, `.status-completed` → `var(--color-primary)` — replaces hardcoded `#f59e0b`, `#10b981`, `#ef4444`, `#3b82f6` in both request pages. |
| 2026-08-13 | `public/js/mock-data.js` | Week filter fix | Semester `startDate` shifted from `2026-06-15` to `2026-07-27` so mock data dates (relative to today) fall in filterable weeks. `parseDate` in `ui-common.js` fixed: `.split("-")` → `.split(" ")` to match space-separated date format. |
| 2026-08-15 | `<th>` headers | Header hover tooltips | Switched from native `title` to JS `initHeaderTooltips()` with a fixed-position tooltip div — tooltips appear above headers, avoids `overflow:hidden` clipping on `.grid-wrapper`. |
| 2026-08-15 | Week filter | Fix infinite recursion | Removed local `function weekFilterChanged(value)` override — its declaration was hoisted and shadowed the shared function in ui-common.js, causing `_sharedWeekFilterChanged = window.weekFilterChanged` to capture itself → stack overflow. Now uses shared `weekFilterChanged()` directly. |
