# Design: Replacement Home — UX Enhancement Features

## Technical Approach

Enhance the existing `replacement-home-UI-design-template.blade.php` (623 lines) with 4 features using CSS + vanilla JS. All features are client-side only, reading from existing `MockData.conflictedClasses`. No backend changes, no new dependencies, no new files.

## Architecture Decisions

### State Management
- **Refactor `pageState`** to include `pageSize`: `pageState = { currentPage: 1, pageSize: 10 }`
- **Add `focusedRowIndex`** for keyboard navigation: tracks position in `currentFiltered[]` (default: -1 = no focus)
- **Filter state** saved to localStorage key `'rh-filters'` with shape `{ search: '', week: 'all' }`

### Feature Implementation

#### F1: Rows Per Page Selector
- **HTML**: Add `<select>` dropdown in pagination bar, left side, before pagination info
- **JS**: 
  - `pageState.pageSize` replaces standalone `const pageSize`
  - `renderPaginationControls()` builds dropdown + info text
  - On change: update `pageState.pageSize`, reset `pageState.currentPage = 1`, call `buildTable()`
- **CSS**: Reuse `.filter-select` class from toolbar

#### F2: Filter Presets (localStorage)
- **JS**:
  - `saveFilters()`: saves `{ search, week }` to localStorage `'rh-filters'`
  - `loadFilters()`: restores saved state on DOMContentLoaded
  - Call `saveFilters()` in search input `input` handler and week filter `change` handler
  - Reset button: clears localStorage, resets filters, calls `buildTable()`
- **HTML**: Add reset button (✕ icon) in `toolbar-right`, next to `#resultCount`

#### F5: Keyboard Shortcuts
- **Desktop only** (≤768px: hint hidden, card tap replaces functionality)
- **JS**:
  - `keydown` listener on `document`
  - Arrow Up/Down: move `focusedRowIndex`, update `.row-focused` class
  - Arrow Left/Right: navigate pagination (prev/next page)
  - Enter: call `goToReplacementWith(code, date)` for focused row
  - Escape: check modal state first → close modal + `stopPropagation()`; otherwise fall through to existing drawer handler
- **CSS**: `.row-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }`
- **HTML**: Add shortcut hint in toolbar: `<span class="keyboard-hint">↑↓ navigate · ←→ paginate · Enter arrange</span>`

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
  → loadFilters() (restore localStorage)
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

### Keyboard Navigation Flow

```
keydown
  → if modal open: Escape closes modal, Arrow/Enter ignored
  → if not modal:
    → Arrow Up: focusedRowIndex-- (clamp to 0)
    → Arrow Down: focusedRowIndex++ (clamp to last row)
    → Arrow Left: prev page
    → Arrow Right: next page
    → Enter: goToReplacementWith(focused row)
    → Escape: focusedRowIndex = -1 (clear focus)
```

## Dependencies

- `public/js/mock-data.js` — `MockData.conflictedClasses` (existing, no changes)
- `public/js/ui-common.js` — `paginate()`, `compareBy()`, `updateResultCount()`, `to12h()`, `formatDate()`, `updateWeekArrows()` (existing, no changes)
- `public/css/theme.css` — `.filter-select`, `.modal-overlay`, `.modal`, etc. (existing, no changes)

## Promoted to Shared

No promotions needed — all new CSS classes (`.row-focused`, `.keyboard-hint`, `.quick-view-modal`) are page-specific. If these patterns are reused on 3+ pages in the future, they should be promoted to `theme.css` / `ui-common.js`.

## Mobile View

- **F1**: Rows-per-page dropdown visible on mobile (full-width in card view pagination)
- **F2**: Filter presets work on mobile (search + week filters in card view)
- **F5**: Keyboard shortcuts hidden on mobile; card tap replaces row focus + Enter
- **F6**: Modal converts to bottom-sheet on mobile (≤768px): `align-items: flex-end`, slide-up animation, 80vh max-height, drag handle element

## File Changes

| File | Action | Lines Added (est.) |
|------|--------|-------------------|
| `replacement-home-UI-design-template.blade.php` | Enhance | +180 lines (CSS: ~40, HTML: ~30, JS: ~110) |
| `page-changelogs/replacement-home-changelog.md` | Update | +8 lines (4 feature entries) |

**Total estimated lines**: 623 + 180 = ~803 lines (well under 1500 limit)
