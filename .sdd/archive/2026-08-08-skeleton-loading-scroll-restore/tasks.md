# Tasks: Skeleton Loading + Scroll Restoration

## Task 1: Add `prefers-reduced-motion` media query to theme.css
- [x] Add `@media (prefers-reduced-motion: reduce) { .skeleton { animation: none; } }` after line 1709 in `public/css/theme.css`
- **Files:** `public/css/theme.css`
- **Estimate:** 5 min

## Task 2: Add `withSkeleton()` to ui-common.js
- [x] Add `withSkeleton(callback, container, count=10, delay=400)` function after `hideSkeleton()` (line 421)
- [x] Implement sync/async callback support with try/finally error handling
- [x] Ensure `hideSkeleton()` runs even if callback throws
- **Files:** `public/js/ui-common.js`
- **Estimate:** 15 min

## Task 3: Add `showSummarySkeleton()`/`hideSummarySkeleton()` to ui-common.js
- [x] Add `showSummarySkeleton()` — queries `.summary-card .summary-value`, stores original HTML in `dataset.original`, replaces with skeleton div
- [x] Add `hideSummarySkeleton()` — cleans up `dataset.original` marker only (does NOT restore; `updateSummary()` sets real values)
- [x] Place after `withSkeleton()` in ui-common.js
- **Files:** `public/js/ui-common.js`
- **Estimate:** 15 min

## Task 4: Add `clearScrollPosition()` to ui-common.js
- [x] Add `clearScrollPosition(key)` — calls `sessionStorage.removeItem('scroll_' + key)`
- [x] Place after `restoreScrollPosition()` (line 432)
- **Files:** `public/js/ui-common.js`
- **Estimate:** 5 min

## Task 5: Add `initScrollRestore()` to ui-common.js
- [x] Add `initScrollRestore(pageKey)` — adds `pageshow` listener for restore, adds nav-click listeners for clear
- [x] Expose `window._scrollToTop` for filter/search handlers
- [x] Place after `clearScrollPosition()`
- **Files:** `public/js/ui-common.js`
- **Estimate:** 15 min

## Task 6: Wire `initScrollRestore()` globally in ui-template.blade.php
- [x] Add `var pageKey = document.body.dataset.page; if (pageKey) initScrollRestore(pageKey);` inside existing `DOMContentLoaded` handler (line 36)
- **Files:** `resources/views/layouts/ui-template.blade.php`
- **Estimate:** 5 min

## Task 7: Wire skeleton loading in my-request-history page
- [x] Wrap `renderTable()` call on initial load with `showSummarySkeleton()` + `withSkeleton(() => { renderTable(data); hideSummarySkeleton(); }, tableBody, 10, 400)`
- [x] Add `window.scrollTo(0, 0)` + `showSummarySkeleton()` + `withSkeleton(() => { renderTable(filteredData); hideSummarySkeleton(); }, tableBody, 10, 400)` in filter/search change handlers
- **Note:** Target container is `<tbody id="tableBody">` — `showSkeleton()` creates `<div>` inside `<tbody>` (brief invalid HTML during 400ms skeleton window)
- **Files:** `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`
- **Estimate:** 20 min

## Task 8: Wire skeleton loading in replacement-home page
- [x] Wrap `renderTable()` call on initial load with `showSummarySkeleton()` + `withSkeleton(() => { renderTable(data); hideSummarySkeleton(); }, tableBody, 10, 400)`
- [x] Add `window.scrollTo(0, 0)` + `showSummarySkeleton()` + `withSkeleton(() => { renderTable(filteredData); hideSummarySkeleton(); }, tableBody, 10, 400)` in filter/search change handlers
- **Files:** `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`
- **Estimate:** 20 min

## Task 9: Wire skeleton loading in request-approval page
- [x] Remove local `showSkeleton()` implementation (lines 920-933) — promoted to shared
- [x] Wrap `renderTable()` call on initial load with `showSummarySkeleton()` + `withSkeleton(() => { renderTable(data); hideSummarySkeleton(); }, tableBody, 10, 400)`
- [x] Add `window.scrollTo(0, 0)` + `showSummarySkeleton()` + `withSkeleton(() => { renderTable(filteredData); hideSummarySkeleton(); }, tableBody, 10, 400)` in filter/search change handlers
- **Files:** `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`
- **Estimate:** 20 min

## Task 10: Verify and test
- [x] Test skeleton appears on initial page load (all 3 pages)
- [x] Test skeleton appears on filter/search changes
- [x] Test summary cards skeleton-ize
- [x] Test `prefers-reduced-motion` disables shimmer
- [x] Test scroll restore on browser Back/Forward (my-request-history + replacement-home only)
- [x] Test scroll clear on nav link clicks
- [x] Test scroll reset to top on filter/search changes
- **Estimate:** 15 min

---

**Total estimated time:** ~130 min (~2.2 hours)
