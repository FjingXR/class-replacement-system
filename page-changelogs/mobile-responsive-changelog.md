# Changelog — Mobile Responsive + Nav Drawer

## Files Changed

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 19:40 | Lines 1-55 | Rewrite | Add hamburger button, drawer overlay, and drawer container with nav items. Desktop nav hidden on mobile via CSS media query. |

### `resources/views/layouts/ui-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 19:40 | Line 5 | Modify | Update viewport meta to include `viewport-fit=cover` for iPhone safe area support |
| 2026-08-02 19:40 | Line 37 | Modify | Add `initMobileNav()` call in DOMContentLoaded handler |
| 2026-09-30 13:30 | Lines 18-26 (head, before theme.css link) | Add | Anti-FOUC guard: inline `<style>` setting `html.dark/html.light` background + `.nav-drawer { display: none }`, applied until theme.css loads. Extensions that force layout before stylesheets arrive (e.g. MetaMask content script, confirmed via forced-layout console warning + rAF frame capture) painted the unstyled drawer fully visible at the left edge, then its `transition: transform 0.3s` slid it away (~0.2s "auto open then auto close" flash on every page switch). Guard is overridden by theme.css's own `.nav-drawer { display: flex }` once the stylesheet applies; verified drawer open/close + desktop layout unaffected, and delayed-CSS screencast shows the drawer never paints. |

### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 19:40 | After line 658 | Extend | Add nav hamburger CSS (hidden by default, shown on mobile) |
| 2026-08-02 19:40 | After line 658 | Extend | Add nav drawer CSS (slide-in from left, full height, dark surface) |
| 2026-08-02 19:40 | After line 658 | Extend | Add drawer overlay CSS (semi-transparent backdrop) |
| 2026-08-02 19:40 | After line 658 | Extend | Add drawer item CSS (stacked vertically, active indicator) |
| 2026-08-02 19:40 | After line 658 | Extend | Add card layout CSS for timetable grid (each day = a card on mobile) |
| 2026-08-02 19:40 | After line 658 | Extend | Add summary cards mobile CSS (2-column grid on mobile) |
| 2026-08-02 19:40 | After line 658 | Extend | Add bottom sheet modal CSS (slide up from bottom on mobile) |
| 2026-08-02 19:40 | After line 658 | Extend | Add touch target CSS (minimum 44×44px for WCAG 2.5.5) |
| 2026-08-02 19:40 | After line 658 | Extend | Add collapsible card CSS (expand/collapse with chevron) |
| 2026-08-02 19:40 | After line 658 | Extend | Add responsive typography CSS (font size reductions on mobile) |
| 2026-08-02 19:40 | After line 658 | Extend | Add safe area CSS (iPhone notch/home indicator padding) |
| 2026-08-02 19:40 | After line 658 | Extend | Add full-width input CSS (selects/inputs 100% on mobile) |
| 2026-08-02 19:40 | After line 658 | Extend | Add skeleton loading CSS (shimmer animation for placeholders) |
| 2026-08-02 19:40 | After line 658 | Extend | Add mobile toast position CSS (bottom-center on mobile) |

### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 19:40 | After line 197 | Extend | Add `initMobileNav()` function (drawer open/close, swipe-to-close, Escape key) |
| 2026-08-02 19:40 | After line 197 | Extend | Add `initSwipeGesture()` function (threshold, debounce, left/right callbacks) |
| 2026-08-02 19:40 | After line 197 | Extend | Add `initCollapsibleCards()` function (toggle expand/collapse on day cards) |
| 2026-08-02 19:40 | After line 197 | Extend | Add `showSkeleton()` and `hideSkeleton()` functions (loading placeholders) |
| 2026-08-02 19:40 | After line 197 | Extend | Add `saveScrollPosition()` and `restoreScrollPosition()` functions (sessionStorage) |
