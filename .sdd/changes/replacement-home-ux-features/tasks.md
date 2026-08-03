# Tasks: Replacement Home — UX Enhancement Features

## Task 1: F1 — Rows Per Page Selector
**Estimate**: 30 min

- [ ] Refactor `pageState` to include `pageSize`: `pageState = { currentPage: 1, pageSize: 10 }`
- [ ] Remove standalone `const pageSize = 10`
- [ ] Update all `paginate()` calls to use `pageState.pageSize`
- [ ] Add `renderPaginationControls()` function: builds dropdown (10/25/50/All) + info text
- [ ] Add dropdown HTML in pagination bar, left side
- [ ] Add `change` event listener: update `pageState.pageSize`, reset `pageState.currentPage = 1`, call `buildTable()`
- [ ] Test: changing rows per page re-renders table correctly

## Task 2: F2 — Filter Presets (localStorage)
**Estimate**: 30 min

- [ ] Add `saveFilters()` function: saves `{ search, week }` to localStorage `'rh-filters'`
- [ ] Add `loadFilters()` function: restores saved state from localStorage
- [ ] Call `saveFilters()` in search input `input` handler
- [ ] Call `saveFilters()` in week filter `change` handler
- [ ] In `DOMContentLoaded`: call `populateWeekDropdown()` → `loadFilters()` → `buildTable()` (order matters)
- [ ] Add reset button (✕ icon) in `toolbar-right`, next to `#resultCount`
- [ ] Add `click` listener on reset button: clear localStorage, reset filters, call `buildTable()`
- [ ] Test: filters persist across page reload; reset clears everything

## Task 3: F5 — Keyboard Shortcuts
**Estimate**: 45 min

- [ ] Add `focusedRowIndex = -1` state variable
- [ ] Add `keydown` listener on `document`
- [ ] Implement Arrow Up/Down: move `focusedRowIndex`, clamp to `[0, currentFiltered.length - 1]`
- [ ] Implement Arrow Left/Right: navigate pagination (prev/next page)
- [ ] Implement Enter: call `goToReplacementWith(code, date)` for focused row
- [ ] Implement Escape: check modal state → close modal + `stopPropagation()`; otherwise clear focus
- [ ] Add `.row-focused` CSS class: `outline: 2px solid var(--color-primary); outline-offset: -2px;`
- [ ] Add shortcut hint in toolbar: `<span class="keyboard-hint">↑↓ navigate · ←→ paginate · Enter arrange</span>`
- [ ] Hide hint on mobile (≤768px): `.keyboard-hint { display: none; }` in media query
- [ ] Test: keyboard navigation works correctly; modal + keyboard interaction doesn't conflict

## Task 4: F6 — Quick View Modal
**Estimate**: 45 min

- [ ] Add modal overlay HTML structure (reuse `.modal-overlay`, `.modal`, `.modal-header`, `.modal-body`, `.modal-footer`)
- [ ] Add `openQuickView(index)` function: builds modal HTML from `currentFiltered[index]` using `.modal-field` pattern
- [ ] Add `closeQuickView()` function: hides overlay
- [ ] Add `onclick="openQuickView(${offset + i})"` to `<tr>` elements (except on button click)
- [ ] Add "Arrange Replacement" button in modal footer: calls `goToReplacementWith(code, date)` + `closeQuickView()`
- [ ] Add "Close" button in modal footer: calls `closeQuickView()`
- [ ] Pre-focus "Arrange Replacement" button on modal open: `btn.focus()`
- [ ] Add mobile bottom-sheet CSS (≤768px): `align-items: flex-end`, slide-up animation, 80vh max-height, drag handle
- [ ] Update mobile card tap: open Quick View Modal instead of navigating directly
- [ ] Test: modal opens on row click; arrangement button works; mobile bottom-sheet displays correctly

## Task 5: Changelog Update
**Estimate**: 10 min

- [ ] Add entries to `page-changelogs/replacement-home-changelog.md` for each feature (F1, F2, F5, F6)
- [ ] Follow existing format: `| Timestamp | Location | Change | Detail |`

## Task 6: Final Verification
**Estimate**: 10 min

- [ ] Run `composer run lint:check` — confirm no new failures
- [ ] Run `composer run types:check` — confirm no new failures
- [ ] Verify total line count < 1500
- [ ] Test all 4 features together (no conflicts)

---

**Total estimated time**: ~2.5 hours
