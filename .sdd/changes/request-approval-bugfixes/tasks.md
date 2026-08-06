# Tasks — Request Approval Bugfixes & Cross-Page Consistency

## Phase 1: Promote Shared CSS to theme.css

### Task 1: Move status badge CSS to theme.css
- [ ] Copy `.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed` from request-approval page to `theme.css`
- [ ] Remove these classes from request-approval-UI-design-template.blade.php
- [ ] Remove these classes from my-request-history-UI-design-template.blade.php
- [ ] Verify both pages still render status badges correctly

### Task 2: Move button CSS to theme.css
- [ ] Copy `.btn-danger`, `.btn-outline` from request-approval page to `theme.css`
- [ ] Remove these classes from request-approval-UI-design-template.blade.php
- [ ] Remove these classes from my-request-history-UI-design-template.blade.php
- [ ] Verify both pages still render buttons correctly

### Task 3: Move modal section CSS to theme.css
- [ ] Copy `.modal-section-title` from request-approval page to `theme.css`
- [ ] Remove this class from request-approval-UI-design-template.blade.php
- [ ] Remove this class from my-request-history-UI-design-template.blade.php
- [ ] Verify both pages still render modal sections correctly

### Task 4: Move bulk selection CSS to theme.css
- [ ] Copy `.col-checkbox`, `.row-selected`, `.bulk-checkbox` from request-approval page to `theme.css`
- [ ] Remove these classes from request-approval-UI-design-template.blade.php
- [ ] Remove these classes from my-request-history-UI-design-template.blade.php
- [ ] Verify both pages still render checkboxes correctly

## Phase 2: Fix Critical Bugs

### Task 5: Fix openModal(index) bug in request-approval
- [ ] Add `openModalById(id)` function that finds request by ID from `MockData.approvalRequests`
- [ ] Update Status badge onclick: `openModal(offset + i)` → `openModalById(r.id)`
- [ ] Update View button onclick: `openModal(offset + i)` → `openModalById(r.id)`
- [ ] Update keyboard Enter handler: `openModal(activeRowIndex)` → `openModalById(currentFiltered[activeRowIndex].id)`
- [ ] Test: Sort by any column, click a row → correct request opens

### Task 6: Fix openModal(index) bug in my-request-history
- [ ] Add `openModalById(id)` function that finds request by ID from `mockRequests`
- [ ] Update row click handler: `openModal(globalIndex)` → `openModalById(r.id)`
- [ ] Update card click handler: `openModal(i)` → `openModalById(r.id)`
- [ ] Update keyboard Enter handler: click badge → `openModalById(rows[focusedRowIndex].dataset.id)`
- [ ] Test: Sort by any column, click a row → correct request opens

### Task 7: Fix buildTimeline step classification
- [ ] Change logic from `i === steps.filter(x => !x.done).length` to use `foundFirstIncomplete` flag
- [ ] Test: Open detail modal → first incomplete step pulses (active)

### Task 8: Fix negative request age
- [ ] Add `REFERENCE_DATE = new Date('2026-08-29T00:00:00')` constant
- [ ] Update `requestAgeHtml()` to use reference date instead of `Date.now()`
- [ ] Handle future dates: show "—" instead of negative values
- [ ] Test: Verify all rows show positive ages or "—"

## Phase 3: Align Features

### Task 10: Move summary bar to top in request-approval
- [ ] Move `@include('partials.ui-summary-bar')` before the toolbar
- [ ] Test: Summary cards visible without scrolling

### Task 11: Route bulk approve through approval notes modal
- [ ] Update `bulkApprove()` to call `openApproveNotesModal([...selectedIds])`
- [ ] Remove `confirm()` call from `bulkApprove()`
- [ ] Test: Select multiple rows → click "Approve Selected" → approval notes modal opens

### Task 12: Clear viewedIds on reset
- [ ] Add `viewedIds.clear()` to `resetFilters()`
- [ ] Test: View some rows → click "Reset Filters" → blue borders disappear

### Task 13: Unify bulk action bar
- [ ] Rename `.batch-bar` to `.bulk-action-bar` in request-approval
- [ ] Update CSS to use fixed bottom position
- [ ] Update JS references from `batchBar` to `bulkActionBar`
- [ ] Test: Select rows → bar appears at bottom

### Task 14: Unify request age format in my-request-history
- [ ] Update my-request-history to use request-approval's `requestAgeHtml()` format
- [ ] Update CSS classes from `.age-green`, `.age-amber`, `.age-red` to `.age-fresh`, `.age-waiting`, `.age-stale`
- [ ] Test: Both pages show same age format

### Task 15: Unify keyboard highlight in request-approval
- [ ] Rename `activeRowIndex` to `focusedRowIndex`
- [ ] Rename `.row-active` to `.row-focused`
- [ ] Update CSS and JS references
- [ ] Test: Arrow keys highlight rows in both pages

### Task 16: Add RPP selector to request-approval (per design D15)
- [ ] Add RPP selector HTML in toolbar (after week filter, before Reset Filters)
- [ ] Add `RPP_OPTIONS = [10, 20, 50]` constant
- [ ] Add `rpp` state variable, initialize from localStorage
- [ ] Add `renderRpp()` function (similar to my-request-history)
- [ ] Add `initRpp()` function to restore from localStorage
- [ ] Update `filterData()` to slice results based on RPP
- [ ] Note: `saveFilters()` / `restoreFilters()` are created in Task 17 — implement Tasks 16-17 together
- [ ] Test: Change RPP → page reloads with same RPP; table shows correct number of rows

### Task 17: Add filter persistence to request-approval (per design D16)
- [ ] Add `saveFilters()` function that persists to localStorage
- [ ] Add `restoreFilters()` function that restores from localStorage
- [ ] Restore: weekFilter, statusFilter, urgencyFilter, courseFilter, hideCompleted, rpp, sortCol, sortDir
- [ ] Call `restoreFilters()` in DOMContentLoaded after initial render
- [ ] Call `saveFilters()` on every filter/sort change
- [ ] Add to `resetFilters()` — clear localStorage entry, default status to "Pending" (actionable queue)
- [ ] Test: Apply filters → reload page → filters are restored

### Task 18: Unify timeline CSS in my-request-history
- [ ] Rename `.timeline-item` → `.timeline-step` in my-request-history
- [ ] Rename `.timeline-dot-wrap` → `.timeline-connector` in my-request-history
- [ ] Rename `.timeline-line` → `.timeline-connector` in my-request-history
- [ ] Update JS references to new class names
- [ ] Test: Open detail modal in both pages → timeline looks consistent

## Phase 4: Additional Features

### Task 19: Add "Hide Completed" toggle to request-approval
- [ ] Add toggle HTML in toolbar (after week filter, before Reset Filters)
- [ ] Add toggle state variable and change handler
- [ ] Update `filterData()` to filter out Completed entries when toggle is on
- [ ] Update `updateSummary()` to respect toggle
- [ ] Default toggle to ON (hide completed)
- [ ] Add to `resetFilters()` — reset toggle to ON
- [ ] Test: Toggle off → Completed entries appear; Toggle on → Completed hidden

### Task 20: Add deep link support (`?id=N`) to request-approval
- [ ] Add URL params parsing in `DOMContentLoaded`
- [ ] If `?id=N` present, find request by ID and open modal after render
- [ ] Handle case where ID doesn't exist (show toast, don't open modal)
- [ ] Test: Navigate to `/request-approval-ui?id=1` → modal opens for request #1

### Task 21: Add responsive card view to request-approval
- [ ] Add `renderCards()` function similar to my-request-history
- [ ] Cards show: course code, course name, status badge, class date/time, lecturer, urgency
- [ ] Cards are clickable → open modal by ID
- [ ] Cards hidden on desktop, shown on mobile (CSS already defined)
- [ ] Test: Resize to mobile → cards appear; Desktop → table appears

## Phase 5: Cleanup & Verification

### Task 22: Remove all duplicated CSS from both pages
- [ ] Verify no CSS classes are duplicated between pages and theme.css
- [ ] Verify all shared CSS is in theme.css only

### Task 23: Run regression tests
- [ ] Test request-approval: sorting, filtering, pagination, modals, approve/reject, bulk actions
- [ ] Test my-request-history: sorting, filtering, pagination, modals, cancel, bulk cancel
- [ ] Test keyboard shortcuts on both pages
- [ ] Test responsive card view on both pages
- [ ] Test deep link support
- [ ] Test Hide Completed toggle
- [ ] Test RPP selector
- [ ] Test filter persistence

### Task 24: Update changelogs
- [ ] Update page-changelogs/request-approval-changelog.md with bug fixes and new features
- [ ] Update page-changelogs/my-request-history-changelog.md with bug fixes
