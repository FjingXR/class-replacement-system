# Proposal: Replacement Home — UX Enhancement Features

## Why This Change Is Needed

The existing Replacement Home Dashboard is functional but lacks polish for power users. Lecturers with many conflicted classes need faster ways to scan, filter, and act on rows. The current page has 14 columns causing excessive horizontal scrolling, no rows-per-page control, and requires clicking a specific button to arrange replacements. These 3 features address real usability gaps identified during testing, matching the UX improvements already applied to the My Request History page.

## Scope

### In Scope

Enhance the **existing** `replacement-home-UI-design-template.blade.php` with 3 features:

**F7: Column Consolidation (14 → 9 columns)**
- Merge 6 date/time columns (Week, Date, Day, Time, Hrs) into one "Original Class" column
- Merge Type column into "Course Code & Name" as `(L)` or `(T)` suffix
- **New column structure (9 columns):**
  1. No. (50px)
  2. Course Code & Name (L/T) (200px) — Type merged as suffix
  3. Original Class (200px) — merged cell format:
     ```
     Mon, 01 Sep 2026 (Week 1)
     09:00 AM to 11:00 AM (2 hrs)
     ```
  4. Days Left (100px) — kept separate for quick scanning (urgency colour preserved)
  5. Venue (70px)
  6. Students (80px)
  7. Affected Cohort(s) (130px)
  8. Conflict Reason (140px)
  9. Action (150px)
- **Benefits:** 36% fewer columns, matches my-request-history UI pattern, reduced horizontal scroll
- CSS: reuse `.cell-class-block`, `.class-day-date`, `.class-time`, `.class-duration` from my-request-history pattern

**F1: Rows Per Page Selector**
- `<select>` dropdown with options: 10 / 25 / 50 / All
- Position: bottom-left of table, next to pagination info ("Showing X of Y results")
- Default: 10 rows per page
- State: refactor `pageSize` from standalone `const` to `pageState.pageSize = 10` (default). Update `paginate()` call to read `cfg.state.pageSize`.
- On change: update `pageState.pageSize`, reset `pageState.currentPage = 1`, re-render table
- CSS: reuse `.filter-select` class from existing toolbar dropdowns

**F6: Quick View Modal**
- Click ANY row (not just the "Arrange Replacement" button) → open a detail modal
- **UX trade-off acknowledged**: Currently 1 click to navigate; F6 makes it 2 clicks (row → modal → button). Justification: users benefit from seeing full class details before committing to a replacement arrangement.
- Modal shows full class details:
  - Course Code & Name (with Type suffix)
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
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | **Enhance** — add 3 features (CSS + JS + HTML) |
| `page-changelogs/replacement-home-changelog.md` | **Update** — add entries for each feature |

No other files are modified.
