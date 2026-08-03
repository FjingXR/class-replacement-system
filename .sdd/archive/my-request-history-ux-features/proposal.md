# Proposal: My Request History — UX Enhancement Features

## Why This Change Is Needed

The existing My Request History page is functional but lacks polish for power users. Lecturers with many requests need faster ways to scan, filter, and act on requests. The current page has no bulk actions, no visual age indicators, no filter memory, and limited keyboard support. These 7 features address real usability gaps identified during testing.

## Scope

### In Scope

Enhance the **existing** `my-request-history-UI-design-template.blade.php` with 7 features:

**F1: Rows Per Page Selector**
- `<select>` dropdown with options: 10 / 25 / 50 / All
- Position: bottom-left of table, next to pagination info
- Default: 10 rows per page
- On change: update rows per page, reset to page 1, re-render

**F2: Bulk Selection + Batch Cancel**
- Checkbox column as FIRST column (header = "select all" checkbox)
- Only Pending rows are selectable (checkbox disabled + muted for other statuses)
- Floating action bar when ≥1 selected: "Cancel Selected (N)" button
- Batch cancel flow: confirmation modal → alert → clear selection
- Selected rows get highlighted background

**F3: Request Age Indicator**
- Color-code "Requested At" cell by days since submission
- Green: <3 days, Amber: 3-7 days, Red: >7 days
- Text color only (no background)
- Uses `new Date()` for age calculation

**F4: Quick Actions in Rows**
- Hover over Pending row shows inline "Cancel" button in last column
- Styled as outline red button (12px font)
- Click triggers existing single-cancel confirmation flow
- Only visible for Pending status rows

**F5: Filter Presets (localStorage)**
- Auto-save filter state to localStorage on every change
- On page load, restore saved filters
- Reset Filters button clears localStorage + resets all filters
- State shape: `{ status, week, search, excludeCompleted }`

**F6: Status History Timeline in Modal**
- Vertical timeline with dots + connecting lines in detail modal
- Shows: Request Submitted → Under Review → Final Status
- Only shown for non-normal statuses (Pending, Approved, Rejected, Cancelled)
- Dots colored by status

**F7: Keyboard Shortcuts**
- `Esc` closes modal
- `Enter` opens detail for focused row
- Arrow keys navigate table rows
- Visual focus indicator on current row

### Out of Scope

- No backend changes (frontend mock phase only)
- No new dependencies
- No new files (enhance existing template only)
- No route changes
- No changes to mock data structure (age calculation uses `new Date()`)

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | **Enhance** — add 7 features (CSS + JS + HTML) |
| `page-changelogs/my-request-history-changelog.md` | **Update** — add entries for each feature |

No other files are modified.
