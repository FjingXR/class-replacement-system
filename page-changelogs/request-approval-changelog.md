# Changelog — Request Approval (PL Side)

## [2026-08-07] Layout Consistency — Align with my-request-history structure

### Summary

Restructured page layout to match my-request-history pattern. Moved summary bar from top to bottom of page. Moved RPP selector from filter toolbar to pagination bar below the grid. Updated RPP to use shared `ui-rpp` partial and `initRpp()` from `ui-common.js`.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-07 | HTML structure | Layout restructure | Reordered: toolbar → grid → pagination bar → bulk action bar → summary bar (was: summary bar → toolbar → bulk action → grid → pagination) |
| 2026-08-07 | Pagination bar | RPP moved | Added `@include('partials.ui-rpp')` to pagination bar; removed inline `<select id="rppSelect">` from filter toolbar |
| 2026-08-07 | Bulk action bar | Position moved | Moved from above grid to below pagination bar (hidden until items selected) |
| 2026-08-07 | Summary bar | Position moved | Moved from top of page to very bottom (below bulk action bar) |
| 2026-08-07 | JS: initRpp | Refactor | Replaced local `changeRpp()`/`initRpp()` with shared `initRpp()` from `ui-common.js` |
| 2026-08-07 | JS: rppSelect → rowsPerPage | Refactor | Updated all DOM references from `rppSelect` to `rowsPerPage` to match partial |

---

## [2026-08-06] Tasks 19-24: SDD Change Request — Bug Fixes, CSS Promotion, Feature Alignment, New Features

### Summary

SDD change request applied across request-approval and my-request-history pages. Bug fixes for `openModal` (unified to `openModalById`), `buildTimeline` (unified timeline classes), `requestAgeHtml` (unified age format with `age-fresh`/`age-waiting`/`age-stale`), and `viewedIds` tracking. 11 CSS classes promoted from both pages to `theme.css` (status badges, modal section title, buttons, bulk selection). Feature alignment: bulk action bar, age format, keyboard highlight, and timeline now use shared implementations. New features: RPP (rows per page), filter persistence via localStorage, Hide Completed toggle, deep link support (`?id=N`), and responsive card view. Removed: groupFilter dropdown.

### Files Changed

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 | After line 1741 | CSS promotion | Added 11 promoted classes: `.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed` (status badges), `.modal-section-title` (modal section header), `.btn-danger`, `.btn-outline` (action buttons), `.col-checkbox`, `.row-selected`, `.bulk-checkbox` (bulk selection) |

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 | Page styles | CSS dedup | Verified no duplicated CSS classes remain — `.status-*` in page are nested selectors (`.col-replacement .cell-class-block .class-time.status-*`) distinct from standalone promoted classes |
| 2026-08-06 | JS: openModalById | Bug fix | Unified modal opener — `openModal(index)` now delegates to `openModalById(id)` |
| 2026-08-06 | JS: buildTimeline | Feature alignment | Timeline uses unified `.request-timeline` / `.timeline-step` / `.timeline-dot` / `.timeline-connector` classes |
| 2026-08-06 | JS: requestAgeHtml | Feature alignment | Unified age format: `age-fresh` (≤1 day), `age-waiting` (≤3 days), `age-stale` (>3 days) with colored dots |
| 2026-08-06 | JS: viewedIds | Bug fix | `viewedIds` Set properly tracks opened requests; `openModalById` adds to set |
| 2026-08-06 | JS: rpp | New feature | RPP dropdown with options 10/20/50, persisted via `saveFilters()` |
| 2026-08-06 | JS: saveFilters/restoreFilters | New feature | Full filter persistence: week, status, urgency, rpp, sort, hideCompleted |
| 2026-08-06 | JS: hideCompleted | New feature | Toggle filters out Completed entries from table and summary counts |
| 2026-08-06 | JS: checkDeepLink | New feature | `?id=N` URL parameter opens specific request modal on page load |
| 2026-08-06 | JS: renderCards | New feature | Responsive card view for mobile (<768px) replaces table grid |
| 2026-08-06 | Toolbar HTML | New elements | Added RPP select, Hide Completed toggle, urgency filter chips |
| 2026-08-06 | Group filter | Removed | Removed groupFilter dropdown (None/Course/Lecturer) — no longer needed |

---

## [2026-08-06] Tasks 19-21: Hide Completed Toggle, Deep Link, Responsive Cards

### Summary

Added "Hide Completed" toggle to filter out completed requests from view and summary counts. Added deep link support via `?id=N` URL parameter to open a specific request modal on page load. Added responsive card view for mobile devices that replaces the table grid on small screens.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 | Page styles | Toggle CSS | Added `.toggle-wrapper`, `.toggle-track`, `.toggle-thumb`, `.toggle-label` styles matching my-request-history pattern |
| 2026-08-06 | Toolbar HTML | Toggle toggle | Added `<label class="toggle-wrapper">` with checkbox after RPP selector, before Reset Filters button |
| 2026-08-06 | Feature state | New variable | Added `let hideCompleted = true;` — default ON |
| 2026-08-06 | filterData() | Hide Completed | Added `if (hideCompleted && r.status === 'Completed') return false;` filter |
| 2026-08-06 | updateSummary() | Respect toggle | Summary cards now exclude Completed entries when `hideCompleted` is true |
| 2026-08-06 | resetFilters() | Reset toggle | Resets `hideCompleted = true` and checkbox `.checked = true` |
| 2026-08-06 | saveFilters() | Already saved | `hideCompleted` was already persisted in filter state |
| 2026-08-06 | restoreFilters() | Restore toggle | Now also sets `document.getElementById('hideCompletedToggle').checked` |
| 2026-08-06 | After renderTable() | Deep link | Added `checkDeepLink()` function parsing `?id=N` URL param |
| 2026-08-06 | DOMContentLoaded | Deep link call | `checkDeepLink()` called after `renderTable()` in initial load timeout |
| 2026-08-06 | After grid wrapper | Card container | Added `<div class="requests-cards" id="requestsCards">` for mobile card view |
| 2026-08-06 | After renderTable() | renderCards() | Added `renderCards()` function rendering request cards with status, course, date, lecturer, urgency, age |
| 2026-08-06 | renderTable() | Call renderCards | `renderCards()` called at end of `renderTable()` |
| 2026-08-06 | Page styles | Responsive CSS | Added `@media (max-width: 768px)` to show cards/hide grid; `@media (min-width: 769px)` to hide cards |

---

## [2026-08-06] Initial Implementation — 17 PL-Efficiency Features

### Summary

New Blade template for Programme Leader replacement request review. 11-column table (checkbox + 10 data columns), 20 mock entries, 17 PL-efficiency features (bulk approve/reject, enhanced confirms, reject presets, urgency filter, request age, approval notes, nav badge, viewed indicator, keyboard shortcuts, review-next auto-advance, slot validity icons, toast notifications, undo stack, animated transitions, smart grouping, mini timeline, skeleton loading). OOP: 9 shared helpers promoted to `ui-common.js`, mock data in shared `mock-data.js`.

## Files Changed

### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | — | Created page | New Blade template extending `layouts.ui-template` with `activeNav = 'request-approval'`. 11-column PL review interface: checkbox, #, Requested Timestamp, Lecturer, Course Code & Name, Original Class, Proposed Replacement, Students, Urgency, Status, Actions |
| 2026-08-06 13:00 | — | Toolbar | Search (code/name/lecturer), status filter (default Pending), urgency filter chips (All/Urgent/Normal), group filter (None/Course/Lecturer), week filter, Reset Filters |
| 2026-08-06 13:00 | — | Table columns | 11 columns: checkbox (35px), # (50px), Requested Timestamp (145px), Lecturer (130px), Course Code & Name (200px), Original Class (170px), Proposed Replacement (170px), Students (70px), Urgency (90px), Status (130px), Actions (140px). min-width 1335px |
| 2026-08-06 13:00 | — | Urgency system | Fixed reference date `2026-08-29`. `urgencyLevel(classDate)` → ≤3 days = Urgent (red badge), >3 days = Normal (green badge). `urgencyDays(classDate)` for sort field |
| 2026-08-06 13:00 | — | Default sort | FR 3.2: `requestedAt` asc (FIFO queue). Reset Filters returns to this default |
| 2026-08-06 13:00 | — | Approve flow | `approveRequest(id)` → `openApproveNotesModal([id])` → summary + optional notes textarea → `confirm()` → `showToast()` with undo → `reviewNextAfterAction()` |
| 2026-08-06 13:00 | — | Reject flow | `openRejectModal(id)` → 5 preset chips + mandatory textarea → `rejectRequest()` → `showToast()` with undo → `reviewNextAfterAction()` |
| 2026-08-06 13:00 | — | Bulk actions | Checkbox column, Select All header, batch bar (fixed below toolbar), `bulkApprove()` with multi-line summary, `bulkReject()` via reject modal |
| 2026-08-06 13:00 | — | Request age | `requestAgeHtml(requestedAt)` → "X days ago" with green/amber/red dot (`age-fresh`/`age-waiting`/`age-stale`) |
| 2026-08-06 13:00 | — | Nav badge | Red badge on "Request Approval" nav link showing Pending count, updated via `updateNavBadge()` |
| 2026-08-06 13:00 | — | Viewed indicator | `viewedIds` Set tracks opened requests; `.row-viewed` adds blue left-border accent |
| 2026-08-06 13:00 | — | Keyboard shortcuts | ArrowUp/Down navigate, Enter opens modal, A/R approve/reject Pending, Escape clears. Paused when modal open |
| 2026-08-06 13:00 | — | Review-next | `reviewNextAfterAction()` auto-opens next Pending after approve/reject |
| 2026-08-06 13:00 | — | Slot validity icons | `slotValidityHtml(r)` → ✓ (green) for valid, ⚠ (red) for conflict with reason. Also inline icon in Proposed Replacement column |
| 2026-08-06 13:00 | — | Toast notifications | All `alert()` replaced with `showToast(message, undoCallback, duration)` from `ui-common.js` |
| 2026-08-06 13:00 | — | Undo stack | Capture `prevStatus` before status change, pass restore function as undo callback. Last action only |
| 2026-08-06 13:00 | — | Animated transitions | Row flash (`row-flash-approved`/`row-flash-rejected`), group expand/collapse (`max-height` transition), filter fade |
| 2026-08-06 13:00 | — | Smart grouping | "Group by" dropdown: None/Course/Lecturer. Collapsible group headers with count badges |
| 2026-08-06 13:00 | — | Mini timeline | `buildTimeline(r)` → 3-step visual lifecycle (Submitted→Viewed→Reviewed) with colored dots and connectors |
| 2026-08-06 13:00 | — | Skeleton loading | `showSkeleton()` → 10 shimmer rows + skeleton cards. 300ms on load, 150ms on filter change |
| 2026-08-06 13:00 | — | Detail modal | 3 sections (Request Info, Original Class, Requested Replacement) + Slot Validity + Reviewed By/At. Timeline prepended. Footer: Pending→Reject+Approve, non-Pending→Close |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | — | New route | `GET /request-approval-ui` → `ui-design-templates.request-approval-UI-design-template` with `activeNav = 'request-approval'` |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | After Replacement Arrangement | Nav link + badge | Added "Request Approval" nav item with `'badge'=>true` flag; rendering adds `<span class="nav-badge" id="navPendingBadge"></span>` inside the link |

### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | Appended | OOP helper promotion | 9 shared helpers promoted from my-request-history: `weekRanges`, `formatDateTime(iso)`, `statusClass(status)`, `isoDayName(iso)`, `formatClassBlock(r)`, `formatReplacementBlock(r)`, `getWeekRange(weekVal)`, `isInWeek(classDate, weekVal)`, `getWeekNumber(iso)` |

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | Lines 465–546 | OOP refactor | Removed 10 page-local helper copies — now resolve from `ui-common.js` via layout. No behavior change |

## Key Design Decisions

- **OOP architecture** — Inheritance (`@extends` layout), composition (`@include` partials), encapsulation (`theme.css` + `ui-common.js` + `mock-data.js`). 9 helpers promoted to shared module; mock data in shared data module
- **11-column layout** — checkbox + 10 data columns. Dropped Type/Venue/Cohort from my-request-history; added checkbox, Lecturer, Urgency, Actions
- **Fixed urgency reference** — `2026-08-29` keeps badges deterministic in mock data (real `new Date()` would compute all entries as "Normal" in July)
- **In-memory state simulation** — Approve/reject modify `MockData.approvalRequests` in-memory for demo purposes; undo restores previous status. No persistence
- **Toast over alert** — Non-blocking notifications via shared `showToast()` from `ui-common.js`; undo restores previous state within 5s window
- **3-layer Escape handler** — Single keydown handler closes topmost modal: approveNotes → rejectReason → detail
- **Skeleton on load/filter** — 300ms initial, 150ms on filter change; reuses `.skeleton`/`.skeleton-shimmer` from `theme.css`
