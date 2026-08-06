# Tasks: Replacement Home — UX Enhancement Features

## Task 1: F7 — Column Consolidation (14 → 9 columns)
**Estimate**: 45 min

- [x] Add `dayAbbr(day)` helper function: returns first 3 characters of day name
- [x] Add `formatClassBlock(c)` function (same pattern as my-request-history):
  - Line 1: Day, Date (Week N)
  - Line 2: Time (Duration)
- [x] Add CSS classes: `.cell-class-block`, `.class-day-date`, `.class-time`, `.class-duration` (reuse from my-request-history)
- [x] Update `columns` array: remove Week, Date, Day, Time, Hrs columns; add Original Class column
- [x] Update `cells` array in row rendering: merge Type into Course Code as `(L)` or `(T)` suffix; use `formatClassBlock(c)` for Original Class
- [x] Remove old column-specific code (Week, Date, Day, Time, Hrs rendering)
- [x] Update table min-width from 1350px to ~1000px
- [x] Test: table displays correctly with 9 columns; merged cell shows correct format

## Task 2: F1 — Rows Per Page Selector
**Estimate**: 30 min

- [x] Refactor `pageState` to include `pageSize`: `pageState = { currentPage: 1, pageSize: 10 }`
- [x] Remove standalone `const pageSize = 10`
- [x] Update all `paginate()` calls to use `pageState.pageSize`
- [x] Add `renderPaginationControls()` function: builds dropdown (10/25/50/All) + info text
- [x] Add dropdown HTML in pagination bar, left side
- [x] Add `change` event listener: update `pageState.pageSize`, reset `pageState.currentPage = 1`, call `buildTable()`
- [x] Test: changing rows per page re-renders table correctly

## Task 3: F6 — Quick View Modal
**Estimate**: 45 min

- [x] Add modal overlay HTML structure (reuse `.modal-overlay`, `.modal`, `.modal-header`, `.modal-body`, `.modal-footer`)
- [x] Add `openQuickView(index)` function: builds modal HTML from `currentFiltered[index]` using `.modal-field` pattern
- [x] Add `closeQuickView()` function: hides overlay
- [x] Add `onclick="openQuickView(${offset + i})"` to `<tr>` elements (except on button click)
- [x] Add "Arrange Replacement" button in modal footer: calls `goToReplacementWith(code, date)` + `closeQuickView()`
- [x] Add "Close" button in modal footer: calls `closeQuickView()`
- [x] Pre-focus "Arrange Replacement" button on modal open: `btn.focus()`
- [x] Add mobile bottom-sheet CSS (≤768px): `align-items: flex-end`, slide-up animation, 80vh max-height, drag handle
- [x] Update mobile card tap: open Quick View Modal instead of navigating directly
- [x] Test: modal opens on row click; arrangement button works; mobile bottom-sheet displays correctly

## Task 4: Changelog Update
**Estimate**: 10 min

- [x] Add entries to `page-changelogs/replacement-home-changelog.md` for each feature (F7, F1, F6)
- [x] Follow existing format: `| Timestamp | Location | Change | Detail |`

## Task 5: Final Verification
**Estimate**: 10 min

- [x] Run `composer run lint:check` — confirm no new failures
- [x] Run `composer run types:check` — confirm no new failures
- [x] Verify total line count < 1500
- [x] Test all 3 features together (no conflicts)

---

**Total estimated time**: ~2 hours 15 min
