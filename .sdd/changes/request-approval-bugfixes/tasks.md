# Tasks — Request Approval Bugfixes & Cross-Page Consistency

## Phase 1: Promote Shared CSS to theme.css

### Task 1: Move status badge CSS to theme.css
- [x] Copy `.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed` from request-approval page to `theme.css`
- [x] Remove these classes from request-approval-UI-design-template.blade.php
- [x] Remove these classes from my-request-history-UI-design-template.blade.php
- [x] Verify both pages still render status badges correctly

### Task 2: Move button CSS to theme.css
- [x] Copy `.btn-danger`, `.btn-outline` from request-approval page to `theme.css`
- [x] Remove these classes from request-approval-UI-design-template.blade.php
- [x] Remove these classes from my-request-history-UI-design-template.blade.php
- [x] Verify both pages still render buttons correctly

### Task 3: Move modal section CSS to theme.css
- [x] Copy `.modal-section-title` from request-approval page to `theme.css`
- [x] Remove this class from request-approval-UI-design-template.blade.php
- [x] Remove this class from my-request-history-UI-design-template.blade.php
- [x] Verify both pages still render modal sections correctly

### Task 4: Move bulk selection CSS to theme.css
- [x] Copy `.col-checkbox`, `.row-selected`, `.bulk-checkbox` from request-approval page to `theme.css`
- [x] Remove these classes from request-approval-UI-design-template.blade.php
- [x] Remove these classes from my-request-history-UI-design-template.blade.php
- [x] Verify both pages still render checkboxes correctly

## Phase 2: Fix Critical Bugs

### Task 5: Fix openModal(index) bug in request-approval
- [x] Add `openModalById(id)` function that finds request by ID from `MockData.approvalRequests`
- [x] Update Status badge onclick: `openModal(offset + i)` → `openModalById(r.id)`
- [x] Update View button onclick: `openModal(offset + i)` → `openModalById(r.id)`
- [x] Update keyboard Enter handler: `openModal(activeRowIndex)` → `openModalById(currentFiltered[activeRowIndex].id)`
- [x] Test: Sort by any column, click a row → correct request opens

### Task 6: Fix openModal(index) bug in my-request-history
- [x] Add `openModalById(id)` function that finds request by ID from `mockRequests`
- [x] Update row click handler: `openModal(globalIndex)` → `openModalById(r.id)`
- [x] Update card click handler: `openModal(i)` → `openModalById(r.id)`
- [x] Update keyboard Enter handler: click badge → `openModalById(rows[focusedRowIndex].dataset.id)`
- [x] Test: Sort by any column, click a row → correct request opens

### Task 7: Fix buildTimeline step classification
- [x] Change logic from `i === steps.filter(x => !x.done).length` to use `foundFirstIncomplete` flag
- [x] Test: Open detail modal → first incomplete step pulses (active)

### Task 8: Fix negative request age
- [x] Add `REFERENCE_DATE = new Date('2026-08-29T00:00:00')` constant
- [x] Update `requestAgeHtml()` to use reference date instead of `Date.now()`
- [x] Handle future dates: show "—" instead of negative values
- [x] Test: Verify all rows show positive ages or "—"

## Phase 3: Align Features

### Task 10: Move summary bar to top in request-approval
- [x] Move `@include('partials.ui-summary-bar')` before the toolbar
- [x] Test: Summary cards visible without scrolling

### Task 11: Route bulk approve through approval notes modal
- [x] Update `bulkApprove()` to call `openApproveNotesModal([...selectedIds])`
- [x] Remove `confirm()` call from `bulkApprove()`
- [x] Test: Select multiple rows → click "Approve Selected" → approval notes modal opens

### Task 12: Clear viewedIds on reset
- [x] Add `viewedIds.clear()` to `resetFilters()`
- [x] Test: View some rows → click "Reset Filters" → blue borders disappear

### Task 13: Unify bulk action bar
- [x] Rename `.batch-bar` to `.bulk-action-bar` in request-approval
- [x] Update CSS to use fixed bottom position
- [x] Update JS references from `batchBar` to `bulkActionBar`
- [x] Test: Select rows → bar appears at bottom

### Task 14: Unify request age format in my-request-history
- [x] Update my-request-history to use request-approval's `requestAgeHtml()` format
- [x] Update CSS classes from `.age-green`, `.age-amber`, `.age-red` to `.age-fresh`, `.age-waiting`, `.age-stale`
- [x] Test: Both pages show same age format

### Task 15: Unify keyboard highlight in request-approval
- [x] Rename `activeRowIndex` to `focusedRowIndex`
- [x] Rename `.row-active` to `.row-focused`
- [x] Update CSS and JS references
- [x] Test: Arrow keys highlight rows in both pages

### Task 16: Add RPP selector to request-approval (per design D15)
- [x] Add RPP selector HTML in toolbar (after week filter, before Reset Filters)
- [x] Add `RPP_OPTIONS = [10, 20, 50]` constant
- [x] Add `rpp` state variable, initialize from localStorage
- [x] Add `renderRpp()` function (similar to my-request-history)
- [x] Add `initRpp()` function to restore from localStorage
- [x] Update `filterData()` to slice results based on RPP
- [x] Note: `saveFilters()` / `restoreFilters()` are created in Task 17 — implement Tasks 16-17 together
- [x] Test: Change RPP → page reloads with same RPP; table shows correct number of rows

### Task 17: Add filter persistence to request-approval (per design D16)
- [x] Add `saveFilters()` function that persists to localStorage
- [x] Add `restoreFilters()` function that restores from localStorage
- [x] Restore: weekFilter, statusFilter, urgencyFilter, courseFilter, hideCompleted, rpp, sortCol, sortDir
- [x] Call `restoreFilters()` in DOMContentLoaded after initial render
- [x] Call `saveFilters()` on every filter/sort change
- [x] Add to `resetFilters()` — clear localStorage entry, default status to "Pending" (actionable queue)
- [x] Test: Apply filters → reload page → filters are restored

### Task 18: Unify timeline CSS in my-request-history
- [x] Rename `.timeline-item` → `.timeline-step` in my-request-history
- [x] Rename `.timeline-dot-wrap` → `.timeline-connector` in my-request-history
- [x] Rename `.timeline-line` → `.timeline-connector` in my-request-history
- [x] Update JS references to new class names
- [x] Test: Open detail modal in both pages → timeline looks consistent

## Phase 4: Additional Features

### Task 19: Add "Hide Completed" toggle to request-approval
- [x] Add toggle HTML in toolbar (after week filter, before Reset Filters)
- [x] Add toggle state variable and change handler
- [x] Update `filterData()` to filter out Completed entries when toggle is on
- [x] Update `updateSummary()` to respect toggle
- [x] Default toggle to ON (hide completed)
- [x] Add to `resetFilters()` — reset toggle to ON
- [x] Test: Toggle off → Completed entries appear; Toggle on → Completed hidden

### Task 20: Add deep link support (`?id=N`) to request-approval
- [x] Add URL params parsing in `DOMContentLoaded`
- [x] If `?id=N` present, find request by ID and open modal after render
- [x] Handle case where ID doesn't exist (show toast, don't open modal)
- [x] Test: Navigate to `/request-approval-ui?id=1` → modal opens for request #1

### Task 21: Add responsive card view to request-approval
- [x] Add `renderCards()` function similar to my-request-history
- [x] Cards show: course code, course name, status badge, class date/time, lecturer, urgency
- [x] Cards are clickable → open modal by ID
- [x] Cards hidden on desktop, shown on mobile (CSS already defined)
- [x] Test: Resize to mobile → cards appear; Desktop → table appears

## Phase 5: Cleanup & Verification

### Task 22: Remove all duplicated CSS from both pages
- [x] Verify no CSS classes are duplicated between pages and theme.css
- [x] Verify all shared CSS is in theme.css only

### Task 23: Run regression tests
- [x] Test request-approval: sorting, filtering, pagination, modals, approve/reject, bulk actions
- [x] Test my-request-history: sorting, filtering, pagination, modals, cancel, bulk cancel
- [x] Test keyboard shortcuts on both pages
- [x] Test responsive card view on both pages
- [x] Test deep link support
- [x] Test Hide Completed toggle
- [x] Test RPP selector
- [x] Test filter persistence

### Task 24: Update changelogs
- [x] Update page-changelogs/request-approval-changelog.md with bug fixes and new features
- [x] Update page-changelogs/my-request-history-changelog.md with bug fixes
