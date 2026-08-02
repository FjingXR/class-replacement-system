# Tasks: My Request History UX Features

## Task 1 — F1: Rows Per Page Selector

- [ ] Add `let rowsPerPage = 10;` state variable (replace hardcoded `pageSize`)
- [ ] Create `renderPaginationControls()` function building dropdown + info text
- [ ] Add `<select>` with options 10/25/50/All in pagination bar (bottom-left)
- [ ] Wire `onchange` to update `rowsPerPage`, reset `currentPage = 1`, call `renderTable()`
- [ ] Update `renderTable()` pagination logic to use `rowsPerPage` instead of `pageSize`
- [ ] Update pagination info text format to include rows-per-page selector

**Effort:** 0.5 hours

## Task 2 — F2: Bulk Selection + Batch Cancel

- [ ] Add `let selectedIds = new Set();` state variable
- [ ] Add checkbox column as FIRST column in `<thead>` and `<tbody>`
- [ ] Header checkbox: `toggleSelectAll()` — selects all Pending rows
- [ ] Row checkboxes: `toggleSelectRow(id)` — add/remove from `selectedIds`
- [ ] Only Pending rows have enabled checkboxes; others `disabled` + `opacity: 0.4`
- [ ] Add `.row-selected` CSS class: `background: var(--color-primary-container)`
- [ ] Create floating action bar HTML (fixed bottom-center): "Cancel Selected (N)"
- [ ] Create `updateBatchBar()` — show/hide bar based on `selectedIds.size`
- [ ] Create batch cancel confirmation modal HTML
- [ ] Wire batch cancel flow: open modal → show selected list → confirm → alert → clear
- [ ] Add CSS: `.col-checkbox`, `.row-selected`, `.batch-action-bar`, `.batch-modal`

**Effort:** 1.5 hours

## Task 3 — F3: Request Age Indicator

- [ ] Create `calculateAgeClass(requestedAt)` returning 'age-green'|'age-amber'|'age-red'
- [ ] Apply age class in `renderTable()` when building "Requested At" `<td>`
- [ ] Add CSS: `.age-green`, `.age-amber`, `.age-red` (text color only)
- [ ] Verify age calculation works with current mock dates

**Effort:** 0.5 hours

## Task 4 — F4: Quick Actions in Rows

- [ ] Add `.btn-quick-cancel` button inside status `<td>` for Pending rows only
- [ ] Add CSS: `.btn-quick-cancel { display: none; }` + `tr:hover .btn-quick-cancel { display: inline-block; }`
- [ ] Wire click to existing `confirmCancelRequest(index)` flow
- [ ] Style button: outline red, 12px font, padding 2px 8px

**Effort:** 0.5 hours

## Task 5 — F5: Filter Presets (localStorage)

- [ ] Create `saveFilters()` — serializes current filter state to `localStorage.setItem('mrh-filters', ...)`
- [ ] Create `loadFilters()` — reads from localStorage, applies to DOM elements, calls `renderTable()`
- [ ] Wire `saveFilters()` in every filter handler (status, week, search, exclude completed)
- [ ] Call `loadFilters()` in `DOMContentLoaded` before `renderTable()`
- [ ] Update Reset Filters button to also `localStorage.removeItem('mrh-filters')`

**Effort:** 0.5 hours

## Task 6 — F6: Status History Timeline in Modal

- [ ] Create `renderTimeline(request)` function building timeline HTML
- [ ] Add timeline section in modal body (after existing fields, before footer)
- [ ] Timeline events: Request Submitted → Under Review → Final Status
- [ ] Add CSS: `.timeline-section`, `.timeline-item`, `.timeline-dot`, `.timeline-line`, `.timeline-content`, `.timeline-label`, `.timeline-time`
- [ ] Only show for non-normal statuses (Pending, Approved, Rejected, Cancelled)

**Effort:** 1 hour

## Task 7 — F7: Keyboard Shortcuts

- [ ] Add `let focusedRowIndex = -1;` state variable
- [ ] Add `document.addEventListener('keydown', ...)` handler
- [ ] Arrow Down/Up: move focus between rows (skip disabled)
- [ ] Enter: open modal for focused row
- [ ] Escape: close modal or clear focus
- [ ] Add `.row-focused` CSS class: `outline: 2px solid var(--color-primary)`
- [ ] Ensure shortcuts don't fire when inputs/selects are focused

**Effort:** 1 hour

## Task 8 — Update Changelog

- [ ] Add entries for each feature in `page-changelogs/my-request-history-changelog.md`
- [ ] Follow 4-column format: Timestamp | Location | Change | Detail

**Effort:** 0.5 hours

## Task 9 — Verify

- [ ] Run `php artisan view:clear`
- [ ] Test all 7 features in browser
- [ ] Verify no JS errors in console
- [ ] Verify dark mode works for all new elements

**Effort:** 0.5 hours
