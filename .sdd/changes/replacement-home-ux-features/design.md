# Design: Replacement Home — UX Enhancement Features

## Technical Approach

Enhance the existing `replacement-home-UI-design-template.blade.php` (623 lines) with 3 features using CSS + vanilla JS. All features are client-side only, reading from existing `MockData.conflictedClasses`. No backend changes, no new dependencies, no new files.

## Architecture Decisions

### State Management
- **Refactor `pageState`** to include `pageSize`: `pageState = { currentPage: 1, pageSize: 10 }`

### Feature Implementation

#### F7: Column Consolidation (14 → 9 columns)
- **Merge 6 date/time columns** (Week, Date, Day, Time, Hrs) into one "Original Class" column
- **Merge Type column** into "Course Code & Name" as `(L)` or `(T)` suffix
- **New column structure (9 columns):**
  1. No. (50px)
  2. Course Code & Name (L/T) (200px) — Type merged as suffix
  3. Original Class (200px) — merged cell format
  4. Days Left (100px) — kept separate for quick scanning
  5. Venue (70px)
  6. Students (80px)
  7. Affected Cohort(s) (130px)
  8. Conflict Reason (140px)
  9. Action (150px)
- **JS**: Add `formatClassBlock(c)` function (same pattern as my-request-history):
  ```javascript
  function formatClassBlock(c) {
      var d = dayAbbr(c.day);
      var dateStr = formatDate(c.date);
      var wn = computeWeek(c.date);
      var weekTag = wn ? ' (Week ' + wn + ')' : '';
      var timeStr = to12h(c.timeStart) + ' to ' + to12h(c.timeEnd);
      var hrs = c.duration + ' hr' + (c.duration > 1 ? 's' : '');
      return '<div class="cell-class-block"><span class="class-day-date">' + d + ', ' + dateStr + weekTag + '</span><br><span class="class-time">' + timeStr + '</span> <span class="class-duration">(' + hrs + ')</span></div>';
  }
  ```
- **JS**: Add `dayAbbr(day)` helper: returns first 3 characters of day name
- **CSS**: Reuse `.cell-class-block`, `.class-day-date`, `.class-time`, `.class-duration` from my-request-history pattern
- **Columns array**: Update to 9 columns, remove old date/time columns
- **Table min-width**: Reduce from 1350px to ~1000px (less horizontal scroll)

#### F1: Rows Per Page Selector
- **HTML**: Add `<select>` dropdown in pagination bar, left side, before pagination info
- **JS**: 
  - `pageState.pageSize` replaces standalone `const pageSize`
  - `renderPaginationControls()` builds dropdown + info text
  - On change: update `pageState.pageSize`, reset `pageState.currentPage = 1`, call `buildTable()`
- **CSS**: Reuse `.filter-select` class from toolbar

#### F6: Quick View Modal
- **HTML**: Add modal overlay structure (reuse existing `.modal-overlay`, `.modal` classes)
- **JS**:
  - `openQuickView(index)`: builds modal HTML from `currentFiltered[index]`, shows overlay
  - `closeQuickView()`: hides overlay
  - "Arrange Replacement" button: calls `goToReplacementWith(code, date)` + `closeQuickView()`
  - Pre-focus "Arrange Replacement" button on modal open: `btn.focus()`
- **CSS**:
  - Desktop: centered modal (existing pattern)
  - Mobile (≤768px): bottom-sheet — `align-items: flex-end`, slide-up animation, 80vh max-height, drag handle
- **Row click handler**: Add `onclick="openQuickView(${offset + i})"` to `<tr>` elements (except on "Arrange Replacement" button click)

### Data Flow

```
DOMContentLoaded
  → populateWeekDropdown()
  → buildTable()
    → filter by search + week
    → sort by date/code
    → paginate (using pageState.pageSize)
    → render table rows (with onclick for quick view)
    → render cards (mobile)
    → updateSummary()
    → renderPaginationControls() (dropdown + info)
    → updateResultCount()
```

## Dependencies

- `public/js/mock-data.js` — `MockData.conflictedClasses` (existing, no changes)
- `public/js/ui-common.js` — `paginate()`, `compareBy()`, `updateResultCount()`, `to12h()`, `formatDate()`, `updateWeekArrows()` (existing, no changes)
- `public/css/theme.css` — `.filter-select`, `.modal-overlay`, `.modal`, etc. (existing, no changes)

## Promoted to Shared

No promotions needed — all new CSS classes (`.quick-view-modal`, `.cell-class-block`) are page-specific. If these patterns are reused on 3+ pages in the future, they should be promoted to `theme.css` / `ui-common.js`.

## Mobile View

- **F7**: Column consolidation works on mobile — merged "Original Class" cell displays same format
- **F1**: Rows-per-page dropdown visible on mobile (full-width in card view pagination)
- **F6**: Modal converts to bottom-sheet on mobile (≤768px): `align-items: flex-end`, slide-up animation, 80vh max-height, drag handle element

## File Changes

| File | Action | Lines Added (est.) |
|------|--------|-------------------|
| `replacement-home-UI-design-template.blade.php` | Enhance | +150 lines (CSS: ~40, HTML: ~20, JS: ~90) |
| `page-changelogs/replacement-home-changelog.md` | Update | +6 lines (3 feature entries) |

**Total estimated lines**: 623 + 150 = ~773 lines (well under 1500 limit)
