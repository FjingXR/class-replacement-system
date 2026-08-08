# Proposal: Skeleton Loading + Scroll Restoration

## Why

CodingMAIN.md §10.0 Rule 9 (user-mandated 2026-08-01) mandates two UX enhancements for all UI pages:

> **Skeleton loading:** Grey placeholder shapes with shimmer animation while data loads.
> **Scroll restoration:** Remember scroll position on browser back/forward via `sessionStorage`.

Partial implementations exist but are incomplete. This change completes them to satisfy Rule 9.

## What Already Exists

| Component | Location | Status |
|-----------|----------|--------|
| `.skeleton`, `.skeleton-row`, `.skeleton-card`, `.skeleton-text` CSS | `theme.css:1683-1709` | ✅ Exists |
| `@keyframes skeleton-shimmer` | `theme.css:1690` | ✅ Exists |
| `showSkeleton(container, type, count)` | `ui-common.js:410` | ✅ Exists (default count=5) |
| `hideSkeleton(container)` | `ui-common.js:419` | ✅ Exists |
| `saveScrollPosition(key)` | `ui-common.js:425` | ✅ Exists (uses `scroll_` prefix) |
| `restoreScrollPosition(key)` | `ui-common.js:429` | ✅ Exists (uses `scroll_` prefix) |
| Auto-save on scroll (debounced) | `ui-common.js:434-442` | ✅ Exists |

## What's New (Genuinely Missing)

### Feature 1: Skeleton Loading — Gaps

| Gap | Description |
|-----|-------------|
| `withSkeleton(callback, container, count=10, delay=400)` | Generic wrapper that shows skeleton, runs callback, hides skeleton after delay (sync/async) |
| `showSummarySkeleton()` / `hideSummarySkeleton()` | Skeleton-ize summary card values; cleanup marker only (real values set by `updateSummary()`) |
| Filter/search skeleton trigger | Pages need to call `withSkeleton()` when filters change (not just initial load) |
| Summary cards skeleton | Currently only request-approval has local summary skeleton; promote to shared |
| `prefers-reduced-motion` media query | Must disable shimmer animation for accessibility (WCAG 2.1) |

### Feature 2: Scroll Restoration — Gaps

| Gap | Description |
|-----|-------------|
| `clearScrollPosition(key)` | Remove saved position from sessionStorage |
| `initScrollRestore(pageKey)` | One-call wiring: beforeunload save + pageshow restore + nav click clear |
| Deliberate navigation clear | Currently no logic to clear scroll position on nav link clicks |
| Filter/search reset to top | No logic to scroll to top when filters change |
| Global wiring in layout | `initScrollRestore()` not called from `ui-template.blade.php` |

## What This Change Adds

### New JS Functions (ui-common.js)

| Function | Signature | Purpose |
|----------|-----------|---------|
| `withSkeleton` | `(callback, container, count=10, delay=400)` | Show skeleton → run callback → hide skeleton after delay (sync/async) |
| `showSummarySkeleton` | `()` | Skeleton-ize all `.summary-card .summary-value` elements |
| `hideSummarySkeleton` | `()` | Clean up `dataset.original` marker; real values set by page's `updateSummary()` |
| `clearScrollPosition` | `(key)` | Remove `scroll_{key}` from sessionStorage |
| `initScrollRestore` | `(pageKey)` | Wire pageshow restore + nav-click clear for a page |

### CSS Additions (theme.css)

| Addition | Purpose |
|----------|---------|
| `@media (prefers-reduced-motion: reduce)` | Disable shimmer animation, show static grey blocks |

### Wiring Changes

| File | Change |
|------|--------|
| `ui-template.blade.php` | Add `initScrollRestore(document.body.dataset.page)` in DOMContentLoaded (guarded by `if (pageKey)`) |
| my-request-history, replacement-home, request-approval | Call `showSummarySkeleton()` + `withSkeleton(() => renderTable(...), tableBody, 10, 400)` on initial load; same pattern + `window.scrollTo(0, 0)` on filter/search changes |

**Timing note:** `showSummarySkeleton()` is called before `withSkeleton()`. `hideSummarySkeleton()` is called inside the `withSkeleton()` callback (after `renderTable()` + `updateSummary()`). The table skeleton and summary skeleton are independent — table skeleton hides after the callback + delay; summary skeleton restores immediately after `updateSummary()`.

## What's Not in Scope

- Backend API integration (mock phase; hooks ready)
- Print/export to PDF (separate SDD change)
- Loading spinners (skeleton replaces them)
- Infinite scroll or lazy loading
- Changing existing `saveScrollPosition()`/`restoreScrollPosition()` signatures (already working)

## Impact

| Area | Files | Change |
|------|-------|--------|
| Shared CSS | `public/css/theme.css` | Add `prefers-reduced-motion` media query |
| Shared JS | `public/js/ui-common.js` | Add 5 functions: `withSkeleton`, `showSummarySkeleton`, `hideSummarySkeleton`, `clearScrollPosition`, `initScrollRestore` |
| Layout template | `resources/views/layouts/ui-template.blade.php` | Wire `initScrollRestore()` globally (guarded by `data-page`) |
| Page templates | my-request-history, replacement-home, request-approval | Call `showSummarySkeleton()` + `withSkeleton()` on initial load + filter/search; remove local `showSkeleton()` from request-approval |

## Constraints

- All CSS in `theme.css` only — no inline styles, no page-specific `<style>` blocks
- All JS in `ui-common.js` only — reusable functions, not page-specific
- No new dependencies
- Follow existing token system (`--color-surface-variant`, `--color-outline`)
- Shimmer respects `prefers-reduced-motion`
- Existing `showSkeleton()`/`hideSkeleton()`/`saveScrollPosition()`/`restoreScrollPosition()` signatures unchanged (backward compatible)
