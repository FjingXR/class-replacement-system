# Proposal — Request Approval Bugfixes & Cross-Page Consistency

## Why

The request-approval page has 7 bugs discovered during smoke testing. Additionally, a cross-page audit revealed that the `openModal(index)` bug also exists in my-request-history, and 11 CSS classes are duplicated between pages instead of being in `theme.css`. These issues affect correctness, UX consistency, and OOP compliance.

## What

### Bugs to Fix (7)

1. **`openModal(index)` opens wrong request when sorted** — Both pages use positional index instead of request ID. After sorting, clicking a row opens the wrong request.

2. **`buildTimeline` marks wrong step as 'active'** — The logic `i === steps.filter(x => !x.done).length` doesn't correctly identify the first incomplete step as 'active'.

3. **Group collapse/expand broken** — Browser strips nested `<tbody>` elements (invalid HTML), so collapse feature doesn't work.

4. **Request age shows negative values** — Mock data has future dates but `requestAgeHtml()` uses `Date.now()`, resulting in "-34 days ago".

5. **Summary bar at bottom** — Should be at top for at-a-glance visibility, consistent with my-request-history.

6. **Bulk approve uses `confirm()`** — Should use approval notes modal for consistency with single approve.

7. **`viewedIds` not cleared on reset** — Minor inconsistency with how `selectedIds` is handled.

### Cross-Page Consistency (11 CSS + 4 Features)

**CSS to promote to `theme.css`:**
- `.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed`
- `.modal-section-title`
- `.btn-danger`, `.btn-outline`
- `.col-checkbox`, `.row-selected`, `.bulk-checkbox`

**Features to unify:**
- Bulk action bar: `.batch-bar` → `.bulk-action-bar` (fixed bottom)
- Request age: Unify format to "● X days ago" with dot indicator
- Keyboard highlight: `.row-active` → `.row-focused`
- Timeline: Unify to request-approval's simpler structure (`.timeline-step` + `.timeline-connector`)
- Reset filters: Keep request-approval's default (status=Pending) as it's the actionable queue
- Summary bar position: Move to top in request-approval

### OOP Compliance

- Remove 11 duplicated CSS classes from both pages
- Ensure all pages use `MockData.*` consistently
- Promote shared patterns to `theme.css`

## Scope

### In Scope
- Fix 7 bugs in request-approval
- Fix `openModal(index)` bug in my-request-history
- Promote 11 CSS classes to `theme.css`
- Align 4 features across pages
- Add RPP selector to request-approval
- Add filter persistence to request-approval

### Additional Features
- Add "Hide Completed" toggle (consistency with my-request-history)
- Add deep link support (`?id=N`)
- Add responsive card view for mobile

### Out of Scope
- Backend integration (mock data phase only)
- Changes to replacement-home, timetable pages (not affected by these bugs)
- Semester chip (not in baseline audit, deferred to future change)

## Acceptance Criteria

1. Clicking any row's Status badge or View button opens the correct request, even when sorted
2. Timeline shows pulsing animation on the first incomplete step
3. Group collapse/expand actually hides/shows rows
4. Request age shows positive values or "—" for future dates
5. Summary cards appear at the top of the page
6. Bulk approve shows approval notes modal with summary
7. All 11 duplicated CSS classes removed from page templates
8. Both pages use consistent class names and patterns
9. RPP selector shows all RPP values from data, filters table correctly
10. Filter persistence: After page reload, previously selected filters are restored
11. "Hide Completed" toggle defaults ON; toggling OFF shows Completed entries; Reset restores default
12. Deep link: `?id=5` opens modal for request #5; `?id=999` shows toast, no modal
13. Responsive cards show on mobile; desktop shows table only
