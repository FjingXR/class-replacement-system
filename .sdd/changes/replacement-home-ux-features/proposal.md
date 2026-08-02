# Proposal: Replacement Home — UX Enhancement Features

## Why This Change Is Needed

The existing Replacement Home Dashboard is functional but lacks polish for power users. Lecturers with many conflicted classes need faster ways to scan, filter, and act on rows. The current page has no rows-per-page control, no filter memory, no keyboard navigation, and requires clicking a specific button to arrange replacements. These 4 features address real usability gaps identified during testing, matching the UX improvements already applied to the My Request History page.

## Scope

### In Scope

Enhance the **existing** `replacement-home-UI-design-template.blade.php` with 4 features:

**F1: Rows Per Page Selector**
- `<select>` dropdown with options: 10 / 25 / 50 / All
- Position: bottom-left of table, next to pagination info ("Showing X of Y results")
- Default: 10 rows per page
- State: refactor `pageSize` from standalone `const` to `pageState.pageSize = 10` (default). Update `paginate()` call to read `cfg.state.pageSize`.
- On change: update `pageState.pageSize`, reset `pageState.currentPage = 1`, re-render table
- CSS: reuse `.filter-select` class from existing toolbar dropdowns

**F2: Filter Presets (localStorage)**
- Auto-save filter state to localStorage on every change (search input, week dropdown)
- localStorage key: `'rh-filters'`
- On page load (DOMContentLoaded), restore saved filters from localStorage (if exists)
- Reset Filters button: small icon button (✕) in `toolbar-right`, next to `#resultCount`. Clears localStorage entry AND resets all filters to defaults.
- State shape: `{ search: '', week: 'all' }` (reason filter intentionally excluded — was removed on 2026-07-20)
- No UI for "saved presets" — just auto-remember last state

**F5: Keyboard Shortcuts**
- Desktop only (≤768px: shortcut hint hidden, card tap replaces row focus + Enter)
- Arrow Left / Arrow Right: navigate pagination (prev/next page)
- Arrow Up / Arrow Down: move focus between rows in the table
- Enter: open the arrangement page for the focused row (call `goToReplacementWith(code, date)`)
- Escape: clear focus / close any open modal (checks modal state first; if modal open, close modal and `stopPropagation()`; otherwise fall through to existing drawer handler)
- Show keyboard shortcut hint in toolbar (small text: "↑↓ navigate · ←→ paginate · Enter arrange")
- Visual focus indicator on current row (`.row-focused` class)
- `focusedRowIndex` tracks position in `currentFiltered[]` — no `id` field needed in mock data

**F6: Quick View Modal**
- Click ANY row (not just the "Arrange Replacement" button) → open a detail modal
- **UX trade-off acknowledged**: Currently 1 click to navigate; F6 makes it 2 clicks (row → modal → button). Justification: users benefit from seeing full class details before committing to a replacement arrangement.
- Modal shows full class details:
  - Course Code & Name
  - Class Type (Lecture/Tutorial)
  - Date, Day, Time, Duration
  - Venue
  - Total Students
  - Affected Cohort(s)
  - Conflict Reason (with badge)
  - Days Left (with urgency colour)
  - Semester Week
- Modal footer: "Arrange Replacement" button (primary) + "Close" button (outline)
- "Arrange Replacement" button calls `goToReplacementWith(code, date)` and closes modal
- CSS: reuse existing `.modal-overlay`, `.modal`, `.modal-header`, `.modal-body`, `.modal-footer` classes from theme.css
- **Mobile (≤768px)**: modal converts to bottom-sheet — `align-items: flex-end`, slide-up animation, 80vh max-height, drag handle element. Pre-focus "Arrange Replacement" button on modal open (`btn.focus()`).

### Out of Scope

- No backend changes (frontend mock phase only)
- No new dependencies
- No new files (enhance existing template only)
- No route changes
- No changes to mock data structure (all features work with existing `MockData.conflictedClasses`)
- No conflict reason filter dropdown (removed during original implementation — search bar alone is sufficient)
- No export/download functionality
- No bulk actions (view-only dashboard with arrangement navigation)

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | **Enhance** — add 4 features (CSS + JS + HTML) |
| `page-changelogs/replacement-home-changelog.md` | **Update** — add entries for each feature |

No other files are modified.
