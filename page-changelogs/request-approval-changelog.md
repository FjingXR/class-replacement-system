# Changelog — Request Approval (PL Side)

## [2026-08-16] Replaced hardcoded inline styles with shared utility classes

Refactored 6 hardcoded inline style instances to use shared CSS classes from `theme.css`. Textareas use `.modal-textarea`, labels use `.section-heading-sub`, action buttons use `.action-row`.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Line | Before | After |
|---|---|---|
| 323 | `style="margin-bottom:8px;font-size:13px;color:..."` | `class="section-heading-sub" style="margin-bottom:8px"` |
| 324 | textarea inline styles | `class="modal-textarea"` |
| 345 | `style="margin-bottom:8px;font-size:13px;color:..."` | `class="section-heading-sub" style="margin-bottom:8px"` |
| 346 | textarea inline styles | `class="modal-textarea"` |
| 609 | `style="display:flex;gap:6px;align-items:center"` | `class="action-row"` |

---

## [2026-08-15] Promote bulk selection to shared OOP BulkSelection class

### Summary

Extracted the duplicated bulk-selection state + bar logic (set of selected ids, toggling the bar's `.visible` class, updating the count, clearing) into a shared `BulkSelection` class in `ui-common.js` (mirrors the existing `ModalController`/`TableController` OOP pattern). Both request-approval and my-request-history now drive their bulk UI through `bulk` (`bulk.add/delete/has/toggle/clear/setAll/updateBar`). Page logic stays in the page (e.g. `renderTable()`, `highlightSelectedRows()`), but the raw `selectedIds` Set + bar/toggle/clear wins are now shared. Request-approval's `toggleSelectAll` now uses `bulk.setAll()`.

### Files Changed

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `BulkSelection` | Added | Class owning `ids` Set + `bar`/`count` elements; methods `add/delete/toggle/has/clear/setAll/updateBar`; `clear()` optionally unchecks `checkboxSelector` + `headerCheckboxId`. |

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| State | Updated | `let selectedIds = new Set()` → `let bulk = new BulkSelection({...})`. |
| Bulk fns | Updated | `toggleSelectAll` uses `bulk.setAll()`; `toggleRowSelect`/`clearSelection`/`updateBatchBar`/`bulkApprove` delegate to `bulk`. |
| Other refs | Updated | `selectedIds.*` → `bulk.*` (approve/reject/reset/renderRow). |

---

## [2026-08-15] Bulk bar gains a "Clear" button (consistent with request history)

### Summary

The bulk action bar only had Approve/Reject; the request-history page already had a "Clear" button. Added the same `.btn-bulk-clear` button + `clearSelection()` so the bar matches the history page.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Page styles | Added | `.btn-bulk-clear` styles (outline button, hover = surface-variant). |
| Bulk bar markup | Updated | Added `Clear` button between the count and Approve/Reject. |
| `clearSelection()` | Added | Clears `selectedIds` and re-renders the table (hides the bar). |

---

## [2026-08-15] Fix: duplicate "Click to copy email" tooltip (two tooltip systems)

### Root cause

The `data-tip` attribute was rendered by **two** tooltip implementations at once:
1. **CSS pseudo-element tooltip** — `[data-tip]::after { content: attr(data-tip) }` + `[data-tip]:hover::after` in `theme.css` (a legacy pure-CSS tooltip).
2. **JS design-system tooltip** — `initDataTipTooltips()` in `ui-common.js` (a fixed-position `.data-tip-tooltip` div, the app's shared tooltip for headers/legends/data-tip).

Both fired on hover for **any** non-`th` element carrying `data-tip` (e.g. the email `div.lecturer-cell-email`), so the text appeared twice. (Headers were unaffected because `th[data-tip]::after { display:none }` already suppressed the CSS variant there.)

### Fix

- Removed the CSS pseudo-element tooltip block (`[data-tip]::after`/`::before`, `:hover`, `data-tip-pos="left"` overrides) from `theme.css` — the JS tooltip is now the single source of truth.
- Extended `initDataTipTooltips()` to honour `data-tip-pos="left"` (used by the nav theme-toggle) so no element regresses.
- Output: `Click to copy email` appears exactly once on hover; email still shown once; copy-to-clipboard intact; all other tooltips (legends, headers) unaffected.

### Files Changed

#### `public/css/theme.css`

| Location | Change | Detail |
|---|---|---|
| CSS tooltip block | Removed | Deleted `[data-tip]::after/::before`, `:hover`, `data-tip-pos="left"` rules. |

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `initDataTipTooltips()` | Updated | Added `data-tip-pos="left"` positioning support (top-bar buttons). |

---

## [2026-08-15] Detail modal: status description as its own row

### Summary

The request status's brief description is now shown in a dedicated **Status Description** row (was an inline caption next to the status badge). Description map: Pending → "Awaiting your approval", Approved → "Replacement scheduled — ready to proceed", Rejected → "Declined — lecturer needs an alternative", Completed → "Replacement conducted", Cancelled → "Request withdrawn".

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `openModalById()` | Updated | Split `Status` (badge) and `Status Description` (text) into two rows. |

---

## [2026-08-15] Detail modal refinements: gray × close, body status badge, split rows, dot colours

### Summary

- **Close button** — replaced the small red dot with a normal gray `×` button (top-right); removed the header `modal-status-badge`.
- **Status badge in body** — status now shown as a coloured badge row inside the modal (not the header); each status carries a brief description (e.g. "Awaiting your approval", "Replacement scheduled — ready to proceed").
- **One value per row** — split "Course" into **Subject Code** / **Subject Name**; Original Class split Date/Day and Start/End/Duration; Students split into Total Students + Cohort(s).
- **Timeline dot colours vary by status** — the Reviewed step dot is success/error/warning/primary by request status (`dot-*`).
- **Click-to-copy tooltip above** — email copy hint uses `data-tip` so the shared tooltip shows above the element.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Removed header `modal-status-badge`; close stays `.modal-close`. |
| `openModalById()` | Updated | Split rows, status badge + description, timeline `dot-*`, email `data-tip`, removed `status` header cfg. |

---

## [2026-08-15] Detail modal redesign: category tabs + unified detail sheet

### Summary

The Request Details modal is now built on a shared **"Detail Sheet"** system (`DetailModal` in `ui-common.js`) with:

- **Identity header** — left-aligned title + muted subtitle (request `#id · code — name`), single status badge top-right.
- **Global timeline** — a single horizontal rail (Submitted → Viewed → Reviewed) pinned above the tabs, always visible; shared `.modal-timeline` component.
- **Category tabs** — Request Info / Original Class / Replacement Class, plus a conditional **Review** tab that only appears when review data exists. Click to switch panels; title/timeline/footer stay static.
- **Groups instead of boxes** — `detail-group` + `detail-group-title` with a thin top rule; definition `detail-row`s (label/value) with no per-row bottom borders; `--strong`/`--muted` emphasis.
- Removed the old `<p>`-based boxed sections and `request-timeline` page CSS.

All fields, actions (approve/reject), and footer behaviour preserved.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Header now `.modal-title#modalTitle` + `.modal-status-badge`; body emptied. |
| `openModalById()` | Rewritten | Uses `DetailModal.render` with global timeline + 4 tabs (Review conditional on `reviewedBy`). |
| Page styles | Removed | Old `.request-timeline`/`.timeline-*` CSS block. |

#### `public/css/theme.css`

| Location | Change | Detail |
|---|---|---|
| After modal shell | Added | `.modal--detail`, `.detail-group(.title)`, `.detail-row/label/value` (+ `--strong/--muted`), `.modal-timeline.tl-*`, `.modal-tabs`/`.modal-tab`/`.modal-tab-panel`. |

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `DetailModal` (new) | Added | Shared render/timeline/section/row/tab helpers + open/close. |
| `openClassModal()` | Updated | Rewritten to use `DetailModal` (single flat group). |

---

## [2026-08-15] Fix: duplicate data-tip tooltip on hover (init ran twice)

### Summary

Hovering a `data-tip` element (e.g. the slot badge) showed **two** tooltips. Cause: the global `initDataTipTooltips()` in the `ui-template` layout runs on every page, but request-approval / request-history / replacement-home also call the legacy `initHeaderTooltips()` alias — so the tooltip div + listeners were created twice. `initDataTipTooltips()` is now guarded by `window.__dataTipTooltipsInitialized` so it can only create one.

### Files Changed

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `initDataTipTooltips()` | Updated | Idempotent guard (`window.__dataTipTooltipsInitialized`) — only one tooltip div / listener set is ever created even when called multiple times. |

---

## [2026-08-15] Modal: auto-close details on approve/reject + always-visible Close

### Summary

- When Approve or Reject is clicked inside the Request Details modal, the details modal now **auto-closes** before the approve-notes / rejection-reason modal opens. Only a layer of `closeModal()` in `approveRequest()` and `openRejectModal()` — harmless when invoked from row buttons (no details modal is open there), verified no regression.
- The bottom-left **Close** button in the Request Details modal is now **always visible** (was `display:none` for Pending rows) so the user can always dismiss the modal.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `approveRequest()` | Updated | Calls `closeModal()` before opening the approve-notes modal. |
| `openRejectModal()` | Updated | Calls `closeModal()` before opening the rejection-reason modal. |
| `openModalById()` | Updated | `closeModalBtn` always shown (`display:''`), no longer hidden for Pending rows. |

---

## [2026-08-15] Slot badge/venue gap, tooltips above, age tip shows urgency

### Summary

- **Slot badge ↔ venue gap** — the row's `<td>` now carries `class="col-replacement"` (it was missing, so the page CSS `.class-venue` gap rule never applied). Gap between the slot badge and venue label is now `7px`.
- **Tooltips above elements** — the slot badge and request-age use `data-tip` (rendered above via `initDataTipTooltips`) instead of the native `title` attribute (which draws below).
- **Age tooltip shows urgency** — on request-approval the age tooltip now reads e.g. `3 days ago · Urgency: Urgent` (was redundant "2–3 days old"). `requestAgeHtml(requestedAt, urgencyLabel)` accepts an optional urgency label; the mobile card (`HtmlBuilder.requestCard`) passes it too. Request-history keeps the colour-range info (no urgency concept).

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Page styles | Updated | `.class-venue` gap `4px` → `7px`. |
| `renderRow()` | Updated | Added `class="col-replacement"` to the replacement `<td>`; slot badge uses `data-tip`; age gets `urgencyLabel(level)`. |

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `requestAgeHtml()` | Updated | Optional `urgencyLabel` param; uses `data-tip` (above) instead of `title`; tooltip `… · Urgency: Urgent/Normal`. |
| `HtmlBuilder.requestCard()` | Updated | Passes urgency label to `requestAgeHtml` for mobile cards. |

---

## [2026-08-15] Row polish: time format, slot badge, tips, age + checkbox/modal fixes

### Summary

- **Class time plain text** — the "Proposed Replacement" time no longer gets a coloured `status-*` class; it's plain bold text.
- **Consistent time format** — replacement time ranges now render as `10:00 AM to 1:00 PM` (24h `09:00 – 11:00` → 12h via `DateHelper.format12hRange`) everywhere (table + modal).
- **Slot-validity badge** — the ✓/⚠ slot icon is now a round status-coloured badge (`.slot-badge.slot-valid` green, `.slot-conflict` red) placed to the LEFT of the venue inside `.class-venue`, matching other pages.
- **Header tips** — Original Class tip corrected ("its state varies…" — not all are being replaced); Urgency tip now documents `≤3 days = Urgent (red), 4+ days = Normal`; Requested Timestamp tip documents age colours.
- **Modal/checkbox** — approve/reject buttons now call `event.stopPropagation()` so the details modal no longer pops when acting on a row; the row checkbox also stops propagation so it can be ticked without opening the modal.
- **Request age** — `requestAgeHtml` now computes age relative to actual today (was hardcoded `2026-08-29`), shows **Today**/**Yesterday** instead of "1 day ago", and preserves the colour legend (green ≤1 day, amber 2–3 days, red 4+ days). Guide block documents the colours.
- **Data-tip tooltips above elements** — `initHeaderTooltips()` generalised to `initDataTipTooltips()` so ANY element with a `data-tip` (headers, legend items, etc.) shows a tooltip ABOVE it, centred and clamped to the viewport (flips below if no room above). Auto-initialised in the shared `ui-template` layout for all pages; `ui-common.js` cache-busted to `?v=2`.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Page styles | Updated | Added `.slot-badge` round badge styles (valid/conflict), removed old `.slot-icon` CSS, `.class-venue` no longer uses `::before` 📍. |
| `renderRow()` | Updated | `replacementBlock(r, { colorStatus: false, slotBadge })`; checkbox + approve/reject buttons add `event.stopPropagation()`; replaced trailing slot icon with badge left of venue. |
| `columns` tips | Updated | Original Class, Urgency, Requested Timestamp tips rewritten. |
| `slotValidityHtml()` | Updated | Uses `.slot-badge` instead of `.slot-icon`. |
| Modal | Updated | Times use `DateHelper.to12h` / `format12hRange`. |
| Guide block | Updated | Added request-age colour legend item. |
| Summary bar | Updated | Page-specific card descriptions. |

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `HtmlBuilder.replacementBlock()` | Updated | Added `opts.colorStatus` (defaults to coloured), `opts.slotBadge`, wraps venue in `.venue-label`; time via `DateHelper.format12hRange`. |
| `DateHelper.format12hRange()` | Added | Converts `09:00 – 11:00` → `9:00 AM to 11:00 AM`. |
| `requestAgeHtml()` | Updated | Relative to actual today; Today/Yesterday labels; colour legend tooltip (green ≤1 day, amber 2–3, red 4+). |

#### `resources/views/partials/ui-summary-bar.blade.php`

| Location | Change | Detail |
|---|---|---|
| Summary hint | Updated | "…selected period (filters apply)." → "…selected period and any active filters." |

---

## [2026-08-15] Summary stats now follow the week/status/urgency/search filters

### Summary

The summary cards previously showed stats for all 20 requests regardless of the active week/status/urgency/search filter. Now `updateSummary()` reads from `currentFiltered` (the table data after all filters), so the cards always reflect the selected period (All Weeks or Week N) and other active filters.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `updateSummary()` | Updated | Total/Pending/Approved/Rejected/Reviewed now computed from `currentFiltered` (after week/status/urgency/search/hide-completed filters) instead of the full `MockData.approvalRequests` array. |

#### `resources/views/partials/ui-summary-bar.blade.php`

| Location | Change | Detail |
|---|---|---|
| summary hint | Updated | Hint text changed from "Stats are for **this week only**." to "Stats are for the **selected period** (filters apply)." |

---

## [2026-08-13] Phase 3 UX Enhancement: Collapsible Guide Block

### Summary

Added an expandable guide block with page-specific workflow instructions.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 268-277 | Added | `@include('partials.ui-guide-block')` with 6 workflow tips |

#### `resources/views/partials/ui-guide-block.blade.php` (new)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Full file | Created | Shared partial with expand/collapse guide panel |

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 518-585 | Added | `.guide-block`, `.guide-toggle`, `.guide-content`, `.guide-list` styles |

---

## [2026-08-13] Phase 3 UX Enhancement: Table Header Tooltips

### Summary

Added hover tooltips to all column headers for better usability.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 537-548 | Added | `tip` property to each column definition with user-friendly descriptions |
| 2026-08-13 | Lines 555-567 | Added | `title` attribute injection in `renderHeader()` for each `<th>` element |

---

## [2026-08-10] Phase 2 Template Migration: Inline helpers → Shared OOP classes

### Summary

Migrated inline `formatShortDate()` to `DateHelper.formatDate()`, refactored `renderCards()` to use `HtmlBuilder.requestCard()`.

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-10 | Lines 553-558 | Removed | Deleted inline `formatShortDate(iso)` function definition |
| 2026-08-10 | Lines 593-594 | Replaced | `formatShortDate(r.classDate)` → `DateHelper.formatDate(r.classDate)` |
| 2026-08-10 | Line 909 | Replaced | `formatShortDate(r.classDate)` → `DateHelper.formatDate(r.classDate)` |
| 2026-08-10 | Lines 894-909 | Refactored | `renderCards()` now uses `HtmlBuilder.requestCard(r, opts)` |

#### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-10 | HtmlBuilder class | Added | `HtmlBuilder.requestCard(r, opts)` — constructs request approval card HTML |

---

## [2026-08-09] UI Polish — Layout consistency, summary cards, dynamic dates, table auto-sizing, advanced UX

### Summary

Major UI polish pass. Replaced footballer mock names with real lecturer names + emails. Added 5th summary card (Total Requests), dynamic dates via helpers, table auto-layout, row urgency indicators, filter chips bar, lecturer column with email clipboard copy, venue details in Proposed Replacement column, approve/reject button gap, modal button styling, and row click-to-modal. All features verified via Playwright smoke test (46/46 PASS).

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | CSS: `.row-urgent`, `.row-soon` | Row urgency | Red/amber left border for requests ≤3/≤7 days old |
| 2026-08-09 | CSS: `.filter-chips` | Filter chips | Active filter chips bar with "Filters:" label and styled background |
| 2026-08-09 | CSS: `.lecturer-cell-email` | Email badge | Email styled as pill/badge: `font-size: 12px`, `padding: 2px 8px`, `border-radius: 10px`, primary container background |
| 2026-08-09 | CSS: `.lecturer-cell-id` | Inline ID | Staff ID now inline with name, lighter weight |
| 2026-08-09 | CSS: `.class-venue` | Venue line | Added venue display in Proposed Replacement column with 📍 icon |
| 2026-08-09 | CSS: `.col-replacement .cell-class-block` | Table layout | Changed to `table-layout: auto` for natural column sizing |
| 2026-08-09 | CSS: `.modal-footer .btn-approve`, `.modal-footer .btn-danger` | Modal buttons | Override: `padding: 8px 20px`, `font-size: 13px`, `border-radius: 8px` |
| 2026-08-09 | CSS: `.modal-footer` | Footer layout | `justify-content: space-between; gap: 8px` — Reject left, Approve/Close right |
| 2026-08-09 | CSS: `.btn-approve` changed from `btn-outline` | Approve button | Modal Approve button changed from outline to solid green |
| 2026-08-09 | CSS: `.sort-hint` spacing | Sort hint | `margin-top: 12px; margin-bottom: 8px` |
| 2026-08-09 | CSS: `.btn-danger:disabled` | Disabled state | `opacity: 0.4; cursor: not-allowed; pointer-events: none` |
| 2026-08-09 | JS: Lecturer column | Name + ID + Email | `Name (ID)\n email` layout with `lookupLecturer(name)` from `MockData.lecturers` |
| 2026-08-09 | JS: `copyEmail(email, event)` | Clipboard copy | Copies email to clipboard + shows toast |
| 2026-08-09 | JS: Row click-to-modal | Entire row clickable | `<tr onclick="openModalById(r.id)" style="cursor:pointer">` |
| 2026-08-09 | JS: Proposed Replacement | Venue details | `formatReplacementBlock()` now shows date + time + venue on 3 lines |
| 2026-08-09 | JS: Approve/Reject button gap | Actions column | Flex container with `gap: 6px` wrapping buttons |
| 2026-08-09 | HTML: Filter chips bar | Active filters | Shows "Filters:" label with removable chips for active filters |
| 2026-08-09 | JS: Auto-advance removed | Review next | `reviewNextAfterAction()` calls removed from `confirmApproveWithNotes()` and `rejectRequest()` |

#### `public/js/mock-data.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | Lecturers registry | Real names | Replaced footballer names: Kylian Mbappe→Dr. Christopher Lazarus, Dembele→En. Lim Jia Zheng, Hakimi→En. Jefther Edward, Neymar→Dr. Chang Foo Chung, Vinicius→Pn. Surayaini Binti Basri |
| 2026-08-09 | Lecturers registry | Email addresses | Added email to all 14 lecturers (e.g. christopher@tarumt.edu.my, limjz@tarumt.edu.my) |
| 2026-08-09 | Date helpers | Dynamic dates | Added `_relDateTime()`, `_relDate()`, `_dayName()` helpers; all 40 dates relative to today |
| 2026-08-09 | URGENCY_REFERENCE_DATE | Dynamic | Changed from fixed `new Date('2026-08-29')` to `new Date()` for real-time urgency |

#### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | `formatReplacementBlock()` | Venue line | Now includes `replacementVenue` with 📍 icon prefix |
| 2026-08-09 | `updateNavBadge()` | Nav badge JS | Moved from page to shared module |

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | `.nav-badge` | Nav badge CSS | Promoted from page to shared theme |
| 2026-08-09 | `.btn-danger:disabled` | Disabled state | Promoted from page to shared theme |

#### `resources/views/layouts/ui-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | Script tag | Cache bust | Updated `mock-data.js?v=3` |

### Key Design Decisions

- **Lecturer column**: Name (ID) + email layout with clipboard copy — single click copies email
- **Row urgency indicators**: Left border color coding (red ≤3 days, amber ≤7 days) for at-a-glance prioritization
- **Filter chips bar**: Active filter visualization with "Filters:" label for clarity
- **Venue in Proposed Replacement**: Three-line block (date, time, venue) with 📍 icon
- **Modal button sizing**: Consistent `padding: 8px 20px` across all action buttons
- **Row click-to-modal**: Entire table row is clickable for faster navigation
- **No auto-advance**: Critical actions keep confirmation modal (user declined quick-approve)

---

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

## [2026-08-13] Phase 4 — macOS-style Update

### Files Changed

#### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | `@section('page-styles')` | macOS-style update | Removed toggle CSS (promoted to `theme.css`). Removed filter-chip CSS (promoted to `theme.css`). Removed hardcoded status colors `#f59e0b`/`#10b981`/`#ef4444`/`#3b82f6` (now in `theme.css`). Changed `.btn-approve`/`.btn-reject`/`.btn-view` border-radius from `4px` to `6px`. |
| 2026-08-13 | Lines 350–358, 401–407 | macOS modal button order | Reordered detail modal footer: Close button moved to `modal-footer-left`, Reject + Approve buttons stay in `modal-footer-right` — matches macOS dismiss-left / actions-right convention. Approve-notes modal also reordered: Cancel left, Approve right. |
| 2026-08-13 | `public/js/mock-data.js` + `@section(page-scripts)` | Week filter fix | Semester `startDate` shifted from `2026-06-15` to `2026-07-27` so mock data dates (relative to today) fall in filterable weeks. `parseDate` in `ui-common.js` fixed: `.split("-")` → `.split(" ")` to match space-separated date format. Week dropdown `opt.value = i` → `opt.value = w.value`. |
| 2026-08-15 | `<th>` headers | Header hover tooltips | Switched from native `title` to JS `initHeaderTooltips()` with a fixed-position tooltip div — tooltips appear above headers, avoids `overflow:hidden` clipping on `.grid-wrapper`. |
| 2026-08-15 | Week filter | Fix infinite recursion | Renamed local override to `onWeekFilterChange()` to avoid hoisted `function weekFilterChanged` shadowing the shared function. Updated `selectOnclick` and filter-chip remove button to call `onWeekFilterChange()`. |
