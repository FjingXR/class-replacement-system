# Explore Brief — Request Approval Bugfixes & Cross-Page Consistency

## Problem Statement

The request-approval page has 7 bugs discovered during smoke testing. Additionally, a cross-page audit revealed that the `openModal(index)` bug also exists in my-request-history, and 11 CSS classes are duplicated between pages instead of being in `theme.css`.

## Bugs Found (request-approval)

### BUG 1 — `openModal(index)` opens wrong request when sorted (CRITICAL)
- **Root cause**: Status badge and View button use `openModal(offset + i)` (positional index), but `openModal()` accesses `currentFiltered[index]`. After sorting, `currentFiltered` order ≠ visible row order.
- **Impact**: Clicking any row's Status badge or View button opens the **wrong request** when table is sorted.
- **Same bug exists in**: my-request-history (line 938, line 770-772)
- **Fix**: Change to use request ID instead of index. Add `openModalById(id)` function, update all onclick handlers.

### BUG 2 — `buildTimeline` marks wrong step as 'active' (VISUAL)
- **Root cause**: Logic `i === steps.filter(x => !x.done).length` doesn't find the first incomplete step.
- **Impact**: With Submitted=done, Viewed=done, Reviewed=pending → Reviewed shows "pending" instead of "active" (pulsing).
- **Fix**: Use a `foundFirstIncomplete` flag to mark the first incomplete step as 'active'.

### BUG 3 — Group collapse/expand broken (FEATURE FAILURE) — RESOLVED BY REMOVAL
- **Root cause**: Code creates `<tbody class="group-body">` inside `<tbody id="tableBody">` — browsers strip nested `<tbody>` as invalid HTML.
- **Impact**: `collapsedGroups` Set tracks state, but no DOM element exists to hide. All rows always visible.
- **Resolution**: Feature removed entirely (groupFilter dropdown, grouping logic, CSS). No longer applicable.

### BUG 4 — Request age shows negative values (DATA BUG)
- **Root cause**: `requestAgeHtml()` uses `Date.now()` but mock data has future dates (Sep 2026). Today is Aug 6, 2026.
- **Impact**: 10 rows show "-34 days ago", "-22 days ago" etc.
- **Fix**: Either fix mock data dates to be in the past, or use fixed reference date like urgency does.

### BUG 5 — Summary bar at bottom (UX ISSUE)
- **Root cause**: `@include('partials.ui-summary-bar')` placed after table/pagination in HTML.
- **Impact**: Summary cards (8 Pending, 5 Approved, 4 Rejected, 11 Total) are below the fold.
- **Fix**: Move summary bar to top (before toolbar), consistent with my-request-history.

### BUG 6 — Bulk approve uses `confirm()` not approval notes modal (INCONSISTENCY)
- **Root cause**: `bulkApprove()` uses `confirm()` while single `approveRequest()` uses `openApproveNotesModal()`.
- **Impact**: Bulk approve lacks the enhanced summary and optional notes that single approve has.
- **Fix**: Route bulk approve through `openApproveNotesModal(ids)` for consistency.

### BUG 7 — `viewedIds` not cleared on "Reset Filters" (MINOR)
- **Root cause**: `resetFilters()` clears `selectedIds` but not `viewedIds`.
- **Impact**: Viewed indicators (blue left border) persist across filter resets.
- **Fix**: Add `viewedIds.clear()` to `resetFilters()`.

## Cross-Page Inconsistencies

### CSS Duplications (11 classes — should be in `theme.css`)
| CSS Class | Pages Affected |
|-----------|---------------|
| `.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed` | my-request-history, request-approval |
| `.modal-section-title` | my-request-history, request-approval |
| `.btn-danger`, `.btn-outline` | my-request-history, request-approval |
| `.col-checkbox`, `.row-selected`, `.bulk-checkbox` | my-request-history, request-approval |

### Feature Inconsistencies
| Feature | my-request-history | request-approval | Proposed Fix |
|---------|-------------------|------------------|--------------|
| Bulk action bar | `.bulk-action-bar` (fixed bottom) | `.batch-bar` (inline) | Unify to `.bulk-action-bar` (fixed bottom) |
| Request age | `getRequestAge()` + `ageClass()` → "(X days)" | `requestAgeHtml()` → "● X days ago" | Unify to request-approval's format with dot indicator |
| Keyboard highlight | `focusedRowIndex` + `.row-focused` | `activeRowIndex` + `.row-active` | Unify to `focusedRowIndex` + `.row-focused` |
| Timeline | `.timeline-item` + `.timeline-dot-wrap` + `.timeline-line` | `.timeline-step` + `.timeline-connector` | Unify to request-approval's simpler structure |
| Reset filters | `clearAll()` → status=all | `resetFilters()` → status=Pending | Keep request-approval's default (Pending is actionable) |
| Summary bar position | TOP | BOTTOM | Move request-approval to TOP |

### Missing Features in request-approval
| Feature | my-request-history | request-approval |
|---------|-------------------|------------------|
| RPP selector | ✓ `initRpp()` | ✗ hardcoded PAGE_SIZE=10 |
| Filter persistence | ✓ `saveFilters()`/`restoreFilters()` | ✗ none |
| Semester chip | ✓ shows semester info | ✗ none |

## Proposed Solution

### Phase 1: Promote shared CSS to `theme.css`
Move 11 duplicated CSS classes from both pages to `theme.css`:
- Status badges (`.status-*`)
- Modal section titles (`.modal-section-title`)
- Button styles (`.btn-danger`, `.btn-outline`)
- Bulk selection (`.col-checkbox`, `.row-selected`, `.bulk-checkbox`)

### Phase 2: Fix critical bugs
1. Add `openModalById(id)` function to both pages
2. Update all onclick handlers to use ID instead of index
3. Fix `buildTimeline` step classification logic
4. Fix group collapse/expand (use CSS-based visibility)
5. Fix negative request age (use fixed reference date)

### Phase 3: Align features
1. Unify bulk action bar to `.bulk-action-bar` (fixed bottom)
2. Unify request age format to "● X days ago"
3. Unify keyboard highlight to `focusedRowIndex` + `.row-focused`
4. Move summary bar to top in request-approval
5. Add RPP selector to request-approval
6. Add filter persistence to request-approval

### Phase 4: Cleanup
1. Remove duplicated CSS from both pages
2. Ensure all pages use `MockData.*` consistently
3. Update changelogs

## Key Cross-Module Data Flows

```
mock-data.js (MockData.approvalRequests)
    ↓ read by
request-approval-UI-design-template.blade.php
    ↓ uses
ui-common.js (helpers: formatDateTime, statusClass, formatClassBlock, etc.)
    ↓ uses
theme.css (status badges, buttons, modals)
```

## Additional Features (User-Confirmed)

1. **Add "Hide Completed" toggle** — For consistency with my-request-history. Will filter out Completed entries from table and summary.
2. **Add deep link support (`?id=N`)** — For direct linking to a specific request. Will open modal on page load if valid ID provided.
3. **Add responsive card view** — CSS is defined but no JS rendering. Will add card rendering for mobile viewport.
