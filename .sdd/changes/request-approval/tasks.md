# Tasks: Request Approval Page (PL Side)

## Task 1 — Promote shared helpers to ui-common.js + refactor my-request-history

- [ ] Append the following 10 helpers to `public/js/ui-common.js` (moved verbatim from `my-request-history-UI-design-template.blade.php`, no logic changes): `weekRanges` const (line 465), `formatDateTime(iso)` (line 472), `statusClass(status)` (line 488), `dayAbbr(day)` (line 499), `isoDayName(iso)` (line 503), `formatClassBlock(r)` (line 509), `formatReplacementBlock(r)` (line 519), `getWeekRange(weekVal)` (line 529), `isInWeek(classDate, weekVal)` (line 534), `getWeekNumber(iso)` (line 541)
- [ ] Remove these 10 helpers from `my-request-history-UI-design-template.blade.php` — they now resolve from `ui-common.js` (loaded by the layout before the page's inline script)
- [ ] Verify `http://localhost:8000/my-request-history-ui` still works identically (sorting, week filter, pagination, modal) — no regression from the promotion
- [ ] Verify no duplicate declarations (page no longer redefines `formatDateTime`, `weekRanges`, etc.)

**Effort:** 1 hour

## Task 2 — Create template file + route + nav link

- [ ] Create `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php` with `@extends('layouts.ui-template', ['activeNav' => 'request-approval'])` and 4 empty `@section` blocks: `title`, `page-styles`, `content`, `page-scripts`
- [ ] Add route to `routes/web.php`: `Route::get('/request-approval-ui', function () { return view('ui-design-templates.request-approval-UI-design-template', ['activeNav' => 'request-approval']); });`
- [ ] Add nav link to `resources/views/partials/ui-nav-bar.blade.php` after "Replacement Arrangement" and before "Replacement History": `<a class="nav-item {{ $activeNav === 'request-approval' ? 'active' : '' }}" href="/request-approval-ui">Request Approval</a>`
- [ ] Verify page loads without Blade errors at `http://localhost:8000/request-approval-ui`
- [ ] Verify nav bar shows 6 links with "Request Approval" active

**Effort:** 30 minutes

## Task 3 — Copy page-specific CSS from my-request-history

- [ ] Copy my-request-history's entire `@section('page-styles')` into `request-approval-UI-design-template.blade.php`'s `@section('page-styles')` as the starting point
- [ ] Adapt column width classes: keep `.col-no` (50px), `.col-requested-at` (145px), `.col-code` (200px), `.col-original` (170px), `.col-replacement` (170px), `.col-status` (130px); change `.col-students` to 70px (was 80px in my-request-history); remove `.col-type`, `.col-venue`, `.col-cohort` (columns not used); add `.col-lecturer` (130px), `.col-urgency` (90px), `.col-actions` (140px)
- [ ] Update table `min-width` to 1335px (was ~1350px in my-request-history; +35px for checkbox column)
- [ ] Add NEW CSS: `.urgency-badge`, `.urgency-urgent`, `.urgency-normal` (urgency badge styles using `--color-error-container`/`--color-secondary-container`)
- [ ] Add NEW CSS: `.btn-approve`, `.btn-reject`, `.btn-view` (action button styles using `--color-secondary`/`--color-error`/`--color-outline`)
- [ ] Remove toggle switch CSS (`.toggle-wrapper`, `.toggle-track`, `.toggle-thumb`) — Exclude Completed toggle is NOT used on this page
- [ ] Remove `.empty-cta` CSS (dead code — no CTA button on this page)
- [ ] Keep `.btn-clear` CSS (Reset Filters button uses this class)
- [ ] Verify CSS has no syntax errors

**Effort:** 1 hour

## Task 4 — Build HTML content structure

- [ ] Add page header: `<div class="page-header"><h1>Request Approval</h1><p>Review and manage replacement requests from lecturers in your program.</p></div>`
- [ ] Add toolbar: `.toolbar` with `.toolbar-left` (search input `<input class="search-input" id="searchInput" placeholder="Search course code, name, or lecturer...">`, status filter `<select class="filter-select" id="statusFilter">` with options All/Pending/Approved/Rejected/Cancelled/Completed, week filter `<select class="filter-select" id="weekFilter">`, reset button `<button class="btn-clear" id="clearFilters">Reset Filters</button>`) and `.toolbar-right` (result count `<span class="result-count" id="resultCount">`)
- [ ] Add sort hint: `<div class="sort-hint">Click column headers to sort (Requested Timestamp, Original Class, Proposed Replacement, Urgency, Status)</div>`
- [ ] Add grid wrapper: `<div class="grid-wrapper" id="gridWrapper"><div class="grid-scroll"><table class="timetable" id="timetable"><thead id="tableHead"></thead><tbody id="tableBody"></tbody></table></div></div>`
- [ ] Add pagination bar: `<div class="pagination-bar" id="paginationBar"><span class="pagination-info" id="paginationInfo"></span><div class="pagination-controls" id="paginationControls"></div></div>`
- [ ] Add summary bar: `@include('partials.ui-summary-bar', ['cards' => [['class' => 'card-pending', 'valueId' => 'summaryPending', 'label' => 'Pending Requests'], ['class' => 'card-approved', 'valueId' => 'summaryApproved', 'label' => 'Approved'], ['class' => 'card-rejected', 'valueId' => 'summaryRejected', 'label' => 'Rejected'], ['class' => 'card-total', 'valueId' => 'summaryReviewed', 'label' => 'Total Reviewed']]])`
- [ ] Add empty state: `<div class="empty-state" id="emptyState" style="display:none">` with SVG icon, `<h3 id="emptyTitle">`, `<p id="emptyText">`, no CTA button (PL doesn't submit requests)
- [ ] Add detail modal: `<div class="modal-overlay" id="modalOverlay">` (NO inline `style="display:none"` — CSS `.modal-overlay { display: none }` handles default hiding, `.modal-overlay.show { display: flex }` shows it via classList toggle) with `.modal` containing `.modal-header` (title "Request Details" + ✕ close button), `.modal-body` (`#modalBody`), `.modal-footer` with `.modal-footer-left` (Reject button `#rejectRequestBtn`) and `.modal-footer-right` (Approve button `#approveRequestBtn` + Close button `#closeModalBtn`)
- [ ] Verify HTML structure is valid and all IDs are unique

**Effort:** 1 hour

## Task 5 — Create/extend mock-data.js + write mock data + helper functions

- [ ] Check whether `public/js/mock-data.js` already exists (created by the `centralized-mock-data` change). **If it exists: DO NOT overwrite** — extend/verify it keeps `MockData.approvalRequests` (this page's 20 entries) and `MockData.urgencyReferenceDate` intact; the `window.MockData` sections are owned by the other change. **If it does not exist:** create it with `window.MockData.approvalRequests = [...]` (20 entries) and `window.MockData.urgencyReferenceDate = new Date('2026-08-29T00:00:00')` per the contract in proposal.md.
- [ ] Add `<script src="/js/mock-data.js"></script>` to `layouts/ui-template.blade.php` immediately after the `ui-common.js` script tag (must load BEFORE the page's inline script); if the tag already exists (added by `centralized-mock-data`), no change needed
- [ ] Write 20 distinct mock entries in `MockData.approvalRequests[]` array (in mock-data.js) — all with `lecturer` field, using distinct values: lecturers (Kylian Mbappe, Dembele, Hakimi, Neymar, Vinicius), course codes (BMIT2201, BMIT3302, BMIT4403, BMIT5504, BMIT6605, BMIT7706, BMIT8807, BMIT9908), cohorts (DIT2, DSE2, DCS2, DAI2, DNE2, DDA2 — all with (S1) suffix), venues (C201, C202, D103, D104, E201, E202), durations 1-3 hours, class dates spanning Aug 31 - Sep 27 (Weeks 1-4). Status distribution: 8 Pending, 5 Approved, 4 Rejected, 2 Completed, 1 Cancelled
- [ ] Add `slotValidity` field to all 20 entries (FR 3.4): `'valid'` or `'conflict'`, ~4-5 entries 'conflict' with `conflictReason` string (e.g. "Room C202 already occupied"), rest 'valid'
- [ ] Ensure at least 3-4 entries have `classDate` within 3 days of `2026-08-29` — use class dates **Aug 31 and Sep 1** (both "Urgent" AND inside Week 1; Aug 29/30 fall outside all weekRanges so entries with those dates vanish under the week filter — do NOT use them)
- [ ] Verify NO other page's inline mock data was touched (my-request-history keeps its own distinct dataset — migrating it is future work)
- [ ] In the page's `@section('page-scripts')`: reference shared `weekRanges` from ui-common.js AND `MockData.approvalRequests`/`MockData.urgencyReferenceDate` from mock-data.js — do NOT redeclare any of them in the page script (a duplicate `const` would throw "Identifier already declared" and kill the whole inline script). Same 4-week structure: Week 1: 31 Aug-6 Sep, Week 2: 7-13 Sep, Week 3: 14-20 Sep, Week 4: 21-27 Sep
- [ ] Use shared helper functions from `ui-common.js` (promoted in Task 1 — do NOT copy them): `formatDateTime(iso)`, `statusClass(status)`, `dayAbbr(day)`, `isoDayName(iso)`, `formatClassBlock(r)`, `formatReplacementBlock(r)`, `getWeekRange(weekVal)`, `isInWeek(classDate, weekVal)`, `getWeekNumber(iso)`, `weekRanges` — all loaded via the layout's `<script src="/js/ui-common.js">` before the page's inline script
- [ ] Write NEW page-local helper functions: `urgencyLevel(classDate)`, `urgencyClass(level)`, `urgencyLabel(level)`, `urgencyDays(classDate)` (these use `URGENCY_REFERENCE_DATE` from mock-data.js)
- [ ] Verify no syntax errors in all JS code

**Effort:** 1.5 hours

## Task 6 — Write table rendering + filtering + sorting + pagination

- [ ] Write `renderTable()` function following my-request-history pattern but with 11 columns (including checkbox) and PL-specific differences:
  - Search matches `courseCode`, `courseName`, OR `lecturer` (not just code + name)
  - Default status filter: `document.getElementById('statusFilter').value` defaults to `'Pending'` (set in DOMContentLoaded, not `'all'`)
  - Default sort (FR 3.2 — time-based queue): `sortState = { field: 'requestedAt', dir: 'asc' }` — oldest requests first (FIFO review order); Reset Filters returns to this default
  - No Exclude Completed toggle (remove the `matchesCompleted` filter)
  - Column array: `#` (un-sortable), Requested Timestamp (sortable, `requestedAt`), Lecturer (un-sortable), Course Code & Name (un-sortable), Original Class (sortable, `classDate`), Proposed Replacement (sortable, `replacementDate`), Students (un-sortable), Urgency (sortable, `urgencyDays`), Status (sortable, `status`), Actions (un-sortable). Note: checkbox column 0 is rendered separately (see Task 12)
  - Urgency column renders: `'<span class="urgency-badge ' + urgencyClass(level) + '">' + urgencyLabel(level) + '</span>'`
  - Actions column renders: for Pending → Approve + Reject buttons with `onclick="approveRequest(r.id)"` / `onclick="openRejectModal(r.id)"` (Reject opens mandatory-reason modal, FR 3.6); for non-Pending → View button with `onclick="openModal(offset + i)"`
  - Status badge is clickable for all rows: `onclick="openModal(offset + i)"`
- [ ] Write `updateSummary()` function: count Pending, Approved, Rejected, and Total Reviewed (Approved + Rejected + Completed)
- [ ] Write `updatePagination()` function — call shared `paginate({ data, pageSize, state, infoId, controlsId, render })` from ui-common.js
- [ ] Write `updateResultCount()` function — call shared `updateResultCount({ elId, data, total, label })` from ui-common.js
- [ ] Sort logic: adapt my-request-history's sort function for 5 sortable fields — `requestedAt` (string compare, same as my-request-history), `classDate` (string compare, same as my-request-history), `replacementDate` (string compare, NEW), `urgencyDays` (compute via `urgencyDays(r.classDate)`, numeric compare, NEW), `status` (string compare, NEW). Remove `courseCode` sort case (now un-sortable). At sort time, compute `urgencyDays(r.classDate)` for each entry before comparing.
- [ ] Empty-state logic: adapt my-request-history's two-variant empty state — fully empty (no requests: "No replacement requests to review at this time." / "Pending requests will appear here for your approval."), filtered empty (no results: "No requests match your search or filter criteria." / "Try adjusting your filters."). **Remove all `emptyCta` references** (no CTA button on this page — `document.getElementById('emptyCta')` will return null and cause TypeError). Keep the show/hide logic for `emptyState`, `emptyTitle`, `emptyText`, `gridWrapper`, `paginationBar`, `summaryBar`.
- [ ] Verify all sorting, filtering, and pagination work by testing in browser

**Effort:** 2 hours

## Task 7 — Write modal + approve/reject + event listeners

- [ ] Write `openModal(index)` function: builds 3-section modal body (Request Information with Rejection Reason conditional, Original Class Detail, Requested Replacement Class), toggles footer button visibility (Pending: show Reject + Approve, hide Close; non-Pending: hide Reject + Approve, show Close)
- [ ] Add Slot Validity field (FR 3.4) to modal Section 3 after Replacement Venue: render via `slotValidityHtml(r)` — `<span class="slot-valid">✓ Valid</span>` (green, `--color-primary`) or `<span class="slot-conflict">⚠ Conflict — <reason></span>` (red, `--color-error`). Add `.slot-valid` / `.slot-conflict` CSS
- [ ] Write `closeModal()` function (same as my-request-history) — can use shared `closeOnOverlayClick(e, closeFn)` from ui-common.js; do NOT use `closeOnEsc` for closeModal — Escape is handled by the ONE keydown handler in the DOMContentLoaded line below (wiring closeOnEsc(closeModal) here AND the custom handler would make Escape close BOTH modals at once when the reject modal is open)
- [ ] Place `#rejectReasonModal` AFTER `#modalOverlay` in the DOM (both use `.modal-overlay` z-index 100 → DOM order decides which is on top)
- [ ] Write `approveRequest(id)` function: `confirm()` → `alert()` — does NOT modify MockData.approvalRequests or re-render
- [ ] Write Rejection Reason modal (FR 3.6): HTML overlay `#rejectReasonModal` with textarea `#rejectReasonInput`, Cancel (`#cancelRejectBtn`) + Confirm Reject (`#confirmRejectBtn`, disabled initially); functions `openRejectModal(id)` (clears input, shows modal, focuses textarea), `updateRejectConfirmState()` (enables Confirm only when input has non-whitespace), `rejectRequest()` (re-validates, `confirm()` including reason — label is `selectedIds.size + ' request(s)'` for bulk or `'Request #' + currentRejectId` for single → `alert()`, then `closeRejectModal()`, `selectedIds.clear()`, `renderTable()`, `reviewNextAfterAction(currentRejectId)`), `closeRejectModal()` — does NOT modify MockData.approvalRequests
- [ ] Write `DOMContentLoaded` handler: populate week filter dropdown, set status filter to 'Pending', call `renderTable()`, attach event listeners (search input, status filter, week filter, clear filters, modal overlay click, Escape key — use ONE Escape keydown handler that closes the topmost modal: `if (rejectReasonModal.classList.contains('show')) closeRejectModal(); else closeModal();`. Do NOT call `closeOnEsc` twice — it attaches an unconditional listener per call and would close both modals at once)
- [ ] "Reset Filters" button: resets search to '', status to 'Pending', week to 'all' (NOT 'all' for status — resets to 'Pending'), page to 1, sort to `requestedAt` asc
- [ ] Verify modal opens on badge click and View button click
- [ ] Verify Approve button shows `confirm()` + `alert()` and does NOT change state
- [ ] Verify Reject button opens the reason modal; Confirm Reject stays disabled with empty/whitespace reason; typing a reason enables it; confirm → `alert()` includes the reason; row status does NOT change
- [ ] Verify Cancel button, ✕ button, overlay click, and Escape close the reject modal (Escape closes topmost modal)
- [ ] Verify Close button and ✕ button and overlay click and Escape all close the detail modal
- [ ] Verify Slot Validity renders in modal: conflict entries show red ⚠ Conflict, valid entries show green ✓ Valid
- [ ] Verify no console errors

**Effort:** 1.5 hours

## Task 8 — Final smoke test

- [ ] Visit `http://localhost:8000/request-approval-ui` — verify no Blade compile errors
- [ ] Verify nav bar shows "Request Approval" as active link with red badge showing "8" (pending count)
- [ ] Verify page header, toolbar (search + status filter + urgency filter + week filter + reset), batch bar (hidden), table, pagination, summary cards all render
- [ ] Verify default status filter is "Pending" — table shows only Pending requests on load
- [ ] Change status filter to "All" — verify all 20 entries appear
- [ ] Verify urgency badges: at least 3-4 entries show "Urgent" (red), rest show "Normal" (green)
- [ ] Verify checkbox column: Pending rows have checkboxes, non-Pending rows have empty cells; "Select All" checkbox in header
- [ ] Click "Select All" — verify all visible Pending row checkboxes are checked; batch bar appears with count; deselect one → Select All unchecks; reselect → Select All re-checks
- [ ] Click "Approve Selected" with ≥1 checked — verify confirm dialog shows multi-line summary (ID, course, lecturer, dates, venue); cancel → no alert; confirm → alert with count; checkboxes clear after action
- [ ] Click "Reject Selected" — verify rejection reason modal opens; presets work; confirm applies to all selected
- [ ] Click urgency filter "Urgent" — verify only Urgent entries shown; click "Normal" — only Normal shown; click "All" — all shown
- [ ] Verify request age sub-label: under Requested Timestamp, entries show "X days ago" with green/amber/red dot
- [ ] Click a status badge — verify modal opens with 3 sections of details
- [ ] Click "View" button on a non-Pending row — verify modal opens; verify row gets viewed indicator (left border accent)
- [ ] Click "Approve" on a Pending row — verify approval notes modal opens with summary + optional notes textarea; cancel → no alert; approve with notes → confirm includes notes → alert includes notes; approve without notes → confirm without notes line
- [ ] Click "Reject" on a Pending row — verify rejection reason modal opens; click preset chip → textarea fills, Confirm Reject enabled; click "Other" → textarea clears; type reason → enabled; confirm → alert includes reason; row status does NOT change
- [ ] Click "Reject" on a Pending row, click Cancel — verify modal closes, no alert
- [ ] Verify Slot Validity in modal: open a conflict entry — shows red "⚠ Conflict"; open a valid entry — shows green "✓ Valid"
- [ ] Verify modal footer: Pending rows show Approve + Reject (no Close); non-Pending rows show Close (no Approve/Reject)
- [ ] Verify default sort: table loads with oldest `requestedAt` first (ascending) — FIFO queue order (FR 3.2)
- [ ] Search by course code, course name, and lecturer name — verify filtering works
- [ ] Test sorting on all 5 sortable columns (Requested Timestamp, Original Class, Proposed Replacement, Urgency, Status)
- [ ] Test pagination: verify 10 per page, prev/next buttons, page numbers
- [ ] Test "Reset Filters" button: resets to Pending status, urgency "All", all weeks, empty search
- [ ] Verify summary cards show correct counts: Pending (8), Approved (5), Rejected (4), Total Reviewed (11)
- [ ] Test empty state: search for non-existent term — verify empty state appears (no CTA button)
- [ ] Verify theme toggle works (dark/light)
- [ ] Verify no console errors on any interaction
- [ ] Verify mock-data.js loads: Network tab shows `/js/mock-data.js` loaded with 200; no "Identifier already declared" console errors
- [ ] Verify other pages unaffected: `http://localhost:8000/replacement-home-ui` and `http://localhost:8000/my-timetable-ui` still load with no console errors
- [ ] Regression: `http://localhost:8000/my-request-history-ui` still works after helper promotion (sorting, week filter, pagination, modal, Cancel button)
- [ ] Test keyboard shortcuts: ArrowDown/ArrowUp moves row highlight; Enter opens modal for highlighted row; Escape clears highlight; A on Pending opens approval notes modal; R on Pending opens rejection reason modal
- [ ] Test keyboard shortcuts paused in modal: open any modal, press ArrowDown — no row highlight change; close modal, verify shortcuts resume
- [ ] Test review next auto-approve: approve a Pending row — next Pending row's modal opens automatically; approve the last Pending — modal closes, no more Pending highlighted
- [ ] Test review next auto-reject: reject a Pending row — next Pending row's modal opens automatically; reject the last Pending — modal closes
- [ ] Test slot validity icon: Proposed Replacement column shows ✓ (green) for valid, ⚠ (red) for conflict, ? (amber) for tentative entries
- [ ] Test toast notifications: approve a request — verify toast appears bottom-left with message + Undo button + close ✕; verify toast auto-dismisses after 5s; verify no browser alert() appears
- [ ] Test undo: approve a request → toast appears → click Undo → verify row reverts to Pending; verify nav badge count restores
- [ ] Test undo after reject: reject a request → click Undo → verify row reverts to Pending
- [ ] Test toast without undo: trigger validation error (reject with empty reason) — verify toast shows without Undo, auto-dismisses after 3s
- [ ] Test animated transitions: approve a request — verify row flashes green briefly; reject — verify row flashes red briefly
- [ ] Test smart grouping: select "Group by Course" — verify rows grouped under collapsible course headers with count badges; click header — verify collapse/expand with animation; select "Group by Lecturer" — verify lecturer grouping; select "No grouping" — flat table returns
- [ ] Test grouping + pagination: verify group headers count toward pagination; verify groups split correctly across pages
- [ ] Test grouping + keyboard: verify ArrowDown/ArrowUp skips group headers, only navigates data rows
- [ ] Test mini timeline: open detail modal — verify 3-step timeline (Submitted/Viewed/Reviewed) at top; completed steps = green dots, active = pulsing amber, pending = grey
- [ ] Test skeleton loading: refresh page — verify skeleton shimmer rows appear ~300ms before real content
- [ ] Verify no console errors on any interaction

**Effort:** 45 minutes

## Task 9 — Create changelog

- [ ] Update/replace `page-changelogs/request-approval-changelog.md` (file already exists, dated 2026-08-01) — follow the existing changelog format (see `page-changelogs/my-request-history-changelog.md` for reference); reconcile pre-existing entries with the actual implemented state (entries claiming the page/route/nav are done are incorrect until implementation completes)
- [ ] Document all files changed: new template, mock-data.js creation, layout script tag, route addition, nav bar modification, ui-common.js helper promotion, my-request-history refactor
- [ ] Document key design decisions: 11-column table + checkbox column, urgency system with fixed reference date, in-table approve/reject (no state update), default Pending filter, modal footer button toggling, 17 PL-efficiency features (toast notifications, undo stack, animated transitions, smart grouping, mini timeline, skeleton loading + 11 earlier features)
- [ ] Document OOP decisions: 10 shared helpers promoted to ui-common.js (single source of truth), mock-data.js shared data module (data separated from logic), my-request-history refactored to consume shared versions, toast/undo bar reused from shared ui-common.js

**Effort:** 15 minutes

## Task 10 — Build HTML for 17 PL-efficiency features

- [ ] Add checkbox column header (`<th class="col-checkbox"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>`) as first `<th>` in the `<thead>` (before the `#` column header)
- [ ] Add batch action bar HTML above the `<table>`: `<div class="batch-bar" id="batchBar" style="display:none"><span class="batch-count" id="batchCount"></span><button class="btn-approve" onclick="bulkApprove()">Approve Selected</button><button class="btn-reject" onclick="bulkReject()">Reject Selected</button></div>`
- [ ] Add urgency filter chip group in `.toolbar-left` between the status `<select>` and the week `<select>`: 3 buttons with `data-urgency="all"|"urgent"|"normal"`, "All" has `.active` class by default
- [ ] Add "Group by" `<select id="groupFilter">` in `.toolbar-left` after urgency chips, before week filter: options "No grouping" (value none), "Group by Course" (value course), "Group by Lecturer" (value lecturer)
- [ ] Add approval notes modal HTML `#approveNotesModal` AFTER `#rejectReasonModal` in the DOM: overlay + modal with `#approveNotesSummary` div, `#approveNotesInput` textarea, Cancel button, Approve button `#confirmApproveBtn`
- [ ] Add reject preset chips HTML inside `#rejectReasonModal .modal-body` ABOVE the textarea: 5 `.reject-preset-chip` buttons ("Venue unavailable", "Insufficient notice", "Slot conflict", "Lecturer unavailable", "Other")
- [ ] Add nav badge `<span class="nav-badge" id="navPendingBadge"></span>` inside the "Request Approval" `<a>` in `ui-nav-bar.blade.php`
- [ ] Add `viewedAt` and `reviewedAt` fields (ISO strings or null) to each of the 20 `MockData.approvalRequests` entries in `public/js/mock-data.js` — used by mini timeline (§7p)
- [ ] Verify all new HTML IDs are unique and don't conflict with existing IDs

**Effort:** 1.5 hours

## Task 11 — Write CSS for 17 PL-efficiency features

- [ ] Add `.col-checkbox` width (35px) + checkbox styling (`accent-color: var(--color-primary)`)
- [ ] Add `.batch-bar` (flex, gap, padding, background, border, border-radius, margin-bottom) + `.batch-count` (font-size, font-weight)
- [ ] Add `.urgency-filter` (inline-flex, gap, margin-left) + `.urgency-filter-chip` (padding, border-radius, border, background, color, font-size, cursor, transition) + `.urgency-filter-chip.active` (primary fill) + hover
- [ ] Add `.request-age` (font-size, color, margin-top) + `.request-age::before` (dot character) + `.age-fresh::before` (primary green), `.age-waiting::before` (tertiary amber), `.age-stale::before` (error red)
- [ ] Add `.reject-presets` (flex, wrap, gap, margin-bottom) + `.reject-preset-chip` (padding, border-radius, border, background, color, font-size, cursor, transition) + hover
- [ ] Add `.nav-badge` (inline-block, min-width, height, line-height, border-radius, background error, color on-error, font-size, font-weight, text-align, margin-left, padding)
- [ ] Add `.row-viewed td:first-child` (left-border 3px solid primary)
- [ ] Add `.row-active` (background primary-container !important, border-left 3px solid primary) — keyboard highlight (§7i)
- [ ] Add `.slot-icon` (font-size 11px, margin-left 4px, font-weight 600) + `.slot-valid` (green), `.slot-conflict` (red), `.slot-tentative` (amber) — slot validity preview (§7k)
- [ ] Add `.row-flash-approved` / `.row-flash-rejected` (0.6s animation fade from secondary/error container to transparent) — animated transitions (§7n)
- [ ] Add `.group-header` (cursor pointer, background surface-variant, font-size 13px, padding) + `.group-arrow` (margin-right 6px) + `.group-count` (badge-style: background primary-container, border-radius 10px, padding 2px 8px, font-size 11px) — smart grouping (§7o)
- [ ] Add `.group-body` (transition max-height 0.3s ease, opacity 0.2s, overflow hidden) + `.group-body.collapsed` (max-height 0, opacity 0) — group collapse animation (§7n)
- [ ] Add `.request-timeline` (flex, align-items center, padding, border-bottom, margin-bottom) + `.timeline-step` (flex column, align-items center, gap 4px) + `.timeline-dot` (width 12px, height 12px, border-radius 50%, border 2px solid outline) + `.timeline-step.completed .timeline-dot` (background secondary) + `.timeline-step.active .timeline-dot` (background tertiary, animation pulse) + `.timeline-label` (font-size 11px) + `.timeline-time` (font-size 10px, opacity 0.7) + `.timeline-connector` (flex 1, height 2px, min-width 40px) + `.timeline-connector.completed` (background secondary) + `@keyframes pulse` — mini timeline (§7p)
- [ ] Verify no CSS syntax errors

**Effort:** 1.5 hours

## Task 12 — Write JS for 17 PL-efficiency features

- [ ] Add `selectedIds = new Set()` state variable + `toggleSelectAll()`, `toggleRowSelect(id)`, `updateBatchBar()` functions (§7.1 bulk)
- [ ] Add `bulkApprove()` function: builds summary from `selectedIds`, `confirm()` with multi-line summary (§7.2), `showToast()` with undo, clears `selectedIds`, calls `renderTable()`
- [ ] Add `bulkReject()` function: calls `openRejectModal(null)` — `rejectRequest()` checks if `currentRejectId === null` and applies reason to all `selectedIds` entries; after confirm: `selectedIds.clear()`, `renderTable()`, `reviewNextAfterAction(null)`
- [ ] Add `approveSummary(r)` helper: builds multi-line string from request object (§7.2 confirm summary)
- [ ] Add `formatShortDate(iso)` helper: returns "31 Aug 2026" from "2026-08-31"
- [ ] Add `applyRejectPreset(text)` function: sets `#rejectReasonInput` value, calls `updateRejectConfirmState()` (§7.3 presets)
- [ ] Add `urgencyFilter = 'all'` state + `setUrgencyFilter(level)` function: toggles `.active` class, resets page to 1, calls `renderTable()` (§7.4 urgency filter)
- [ ] Add `requestAgeHtml(requestedAt)` function: computes days since `requestedAt`, returns HTML with `.age-fresh`/`.age-waiting`/`.age-stale` class (§7.5 request age)
- [ ] Add `currentApproveIds = []` state + `openApproveNotesModal(ids)` function: builds summary, shows modal (§7.6 approval notes)
- [ ] Add `confirmApproveWithNotes()` function: reads notes, `confirm()` with notes, `showToast()` with undo, clears state, calls `renderTable()` (§7.6)
- [ ] Add `closeApproveNotesModal()` function
- [ ] Add `viewedIds = new Set()` state + mark viewed in `openModal()` (§7.8 viewed indicator)
- [ ] Add `updateNavBadge()` function: counts Pending, sets `#navPendingBadge` text + visibility (§7.7 nav badge)
- [ ] Add `activeRowIndex = -1` state + keyboard `keydown` listener (§7i): ArrowUp/ArrowDown navigate, Enter opens modal, A/R approve/reject, Escape clears highlight; pause when any `.modal.show` is open
- [ ] Add `highlightRow()` function: toggles `.row-active` on data rows only — filter out `.group-header` rows with `querySelectorAll('#dataTable tbody tr:not(.group-header)')` to skip group headers during keyboard navigation
- [ ] Add `reviewNextAfterAction(actedOnId)` function (§7j): finds next Pending after acted-on index, opens modal or closes if none left; called from `confirmApproveWithNotes()` and `rejectRequest()`
- [ ] Add slot validity icon rendering in `renderTable()` (§7k): map `slotValidity` → ✓/⚠/? with `.slot-valid`/`.slot-conflict`/`.slot-tentative` classes
- [ ] Replace all `alert()` calls with `showToast(message, undoCallback, duration)` (§7l): single approve → `showToast('Request #X approved.', undoFn, 5000)`, single reject → `showToast('Request #X rejected.', undoFn, 5000)`, bulk approve → `showToast('N request(s) approved.', undoFn, 5000)`, bulk reject → `showToast('N request(s) rejected.', undoFn, 5000)`, validation error → `showToast('Please provide a rejection reason.', null, 3000)`
- [ ] Add undo callbacks (§7m): capture `prevStatus` before status change, pass restore function as `showToast` undoCallback — reverts status, re-renders, re-updates nav badge
- [ ] Add row flash animation (§7n): after `renderTable()` following approve/reject, find `<tr>` by data-id, add `.row-flash-approved` or `.row-flash-rejected` class, remove after 600ms via setTimeout
- [ ] Add `groupField = 'none'` state + `collapsedGroups = new Set()` + `setGroupFilter(value)` function (§7o): resets page to 1, calls `renderTable()`
- [ ] Add `toggleGroup(key)` function (§7o): adds/removes key from `collapsedGroups`, re-renders
- [ ] Update `renderTable()` (§7o): when `groupField !== 'none'`, group `currentFiltered` by course/lecturer, insert `<tr class="group-header">` rows, wrap group bodies in `<tbody class="group-body">` with `.collapsed` class for collapsed groups; when `groupField === 'none'`, render flat as before
- [ ] Add `buildTimeline(r)` function (§7p): generates HTML for 3-step timeline (Submitted/Viewed/Reviewed) from `r.requestedAt`, `r.viewedAt`, `r.reviewedAt`
- [ ] Update `openModal()` (§7p): call `buildTimeline(r)` and prepend result to `.modal-body` before Section 1
- [ ] Add `showSkeleton()` function (§7q): generates 10 skeleton rows + 4 skeleton card values using `.skeleton` class from theme.css
- [ ] Add `isInitialLoad = true` flag (§7q): set in DOMContentLoaded after skeleton delay; when `renderTable()` called from filter/sort change (isInitialLoad=false), call `showSkeleton()` + 150ms setTimeout before real render
- [ ] Update `DOMContentLoaded` (§7q): call `showSkeleton()` first, then 300ms delay before `renderTable()` + `updateNavBadge()`, then set `isInitialLoad = false`
- [ ] Update `renderTable()`: add checkbox column (col 0), add request age sub-label in col 2, add urgency filter after status+week+search filtering, add `.row-viewed` class to `<tr>` if viewed, call `updateBatchBar()` at end, call `highlightRow()` at end
- [ ] Update `DOMContentLoaded`: init urgency filter chip listeners, call `updateNavBadge()`, wire `#approveNotesModal` overlay click, extend Escape handler to 3-layer: approveNotes → rejectReason → detail modal, add keyboard `keydown` listener
- [ ] Update `approveRequest(id)`: call `openApproveNotesModal([id])` instead of bare `confirm()` (§7.6 approval notes flow)
- [ ] Verify no console errors on all interactions

**Effort:** 4 hours
