# Proposal: Mobile Responsive + Nav Drawer (Cross-Cutting)

## Why This Change Is Needed

All 6 UI design templates currently lack mobile-responsive layouts. On devices ≤768px:
- The top navigation bar overflows (5 nav links + logo + user panel → horizontal scroll or collapse)
- Data tables require horizontal scrolling (unusable on small screens)
- Modals are centered but too wide for mobile viewports
- Summary cards stack awkwardly
- Week picker controls overflow
- Touch targets are too small (buttons/links < 44×44px)

On tablets (769px–1024px):
- Navigation links are cramped and may overflow
- Tables require horizontal scrolling
- Summary cards may not fit in a single row
- Modals are too wide

This is a **cross-cutting change** that adds mobile + tablet responsive behavior to ALL existing pages via shared CSS/JS in `theme.css` and `ui-common.js`, plus a nav drawer component in the shared nav-bar partial.

## Scope

### In Scope

**A. Nav Drawer (≤768px)**
- Hamburger icon (☰) replaces desktop nav links on mobile
- Slide-in drawer from left with full-height dark surface
- Same `$navItems` array, stacked vertically, active indicator
- Close: tap X, tap overlay, or swipe left
- Semi-transparent backdrop, body scroll locked when open

**B. Timetable Grid → Card Layout (≤768px)**
- Data tables convert to card layout (each row = a card)
- Essential columns only: Course, Original Class, Status, Actions
- Vertical scroll (no horizontal)
- Day card headers sticky on scroll

**C. Summary Cards (≤768px)**
- 2-column grid (5 cards → 3+2 layout)
- Full-width on very small screens

**D. Legend Bar (≤768px)**
- Flex-wrap, items flow into 2 rows naturally

**E. Semester Bar (≤768px)**
- Week select: reduce min-width, allow text truncation
- Arrows + Today button: stack below select or reduce padding
- Full-width bar

**F. Page Header (≤768px)**
- Chips stack vertically with spacing
- Title: reduce font size (24px → 20px)
- Description: full width

**G. Bottom Sheet Modals (≤768px)**
- Slide up from bottom (not centered)
- 80vh max height, scrollable content
- Drag handle bar for swipe-to-dismiss
- Full-screen backdrop

**H. Touch Targets (WCAG 2.5.5)**
- All buttons/links ≥ 44×44px minimum
- Affects: nav items, legend items, week arrows, Today button, modal close

**I. Swipe Gestures (≤768px)**
- Week navigation: swipe left = next week, swipe right = previous week
- Visual hint: subtle arrow indicator on swipe edges
- Minimum 50px swipe distance, debounce rapid swiping

**J. Collapsible Day Rows (≤768px)**
- Each day card collapsible (tap header to expand/collapse)
- Default: expanded on load
- Chevron down/up indicator, smooth height transition

**K. Responsive Typography (≤768px)**
- Page title: 24px → 20px
- Day labels: 14px → 13px
- Event text: 12px → 11px
- CSS `clamp()` for fluid scaling

**L. Safe Area Insets**
- iPhone notch: `env(safe-area-inset-top)` for status bar
- Home indicator: `env(safe-area-inset-bottom)` for bottom nav

**M. Full-width Form Inputs (≤768px)**
- Selects: `width: 100%`
- Text inputs: `width: 100%`
- Buttons: full-width primary actions

**N. Skeleton Loading**
- Grey placeholder blocks while data loads
- Subtle shimmer/pulse animation
- Duration: until `DOMContentLoaded` or data fetch completes

**O. Scroll Restoration**
- Remember scroll position per page (sessionStorage)
- Browser back/forward navigation restores position

**P. Toast Position (≤768px)**
- Mobile: toasts at bottom-center (thumb-reachable)
- Desktop: toasts stay top-right (existing)

**Q. Viewport Meta**
- Add `<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">` to layout `<head>`

### Tablet View (769px–1024px)

**R. Navigation (769px–1024px)**
- Nav links: reduce font size, compress spacing
- User info: hide name, show avatar only
- Logo: reduce margin

**S. Data Tables (769px–1024px)**
- Allow horizontal scroll (existing `overflow-x: auto`)
- Reduce column widths where possible
- Keep table structure (no card conversion)

**T. Summary Cards (769px–1024px)**
- 3-column grid (5 cards → 3+2 layout)
- Reduce padding/font sizes

**U. Modals (769px–1024px)**
- Max-width: 560px (slightly wider than desktop 480px for tablet comfort)
- Keep centered position (no bottom sheet)

**V. Toolbar (769px–1024px)**
- Stack filters vertically (existing rule)
- Full-width search input

**W. Touch Targets (769px–1024px)**
- Minimum 40×40px (slightly smaller than mobile 44×44px)

### Out of Scope

- New pages (this change only modifies existing shared files)
- Backend logic
- New dependencies (pure CSS + vanilla JS)
- Performance optimization (separate concern)
- **DO NOT disturb/change any existing desktop code or design** — all mobile changes must be additive only

## Critical Constraint: Desktop Preservation

**ALL mobile + tablet changes must be wrapped inside `@media` blocks.**

This means:
- **NO existing CSS rules modified** — only NEW rules added inside media queries
- **NO existing HTML structure changed** — only NEW elements added (hamburger, drawer, overlay)
- **NO existing JS functions modified** — only NEW functions added (`initMobileNav()`, `initSwipeGesture()`, etc.)
- **NO existing class names reused** — all new mobile/tablet classes use unique names (`.nav-hamburger`, `.nav-drawer`, `.skeleton`, etc.)
- **Desktop layout remains pixel-perfect** — the only exception is adding `viewport-fit=cover` to the viewport meta tag (safe area support, no visual change)

**Breakpoints:**
- Tablet: `@media (max-width: 1024px)`
- Mobile: `@media (max-width: 768px)`

**Verification:** After implementation, the desktop view (≥1025px) must be identical to the current state. Any deviation = rollback and fix.

## Impact Scope

**Rule: All changes are ADDITIVE ONLY. No existing code is modified or removed.**

| File | Action | Desktop Impact |
|------|--------|----------------|
| `resources/views/partials/ui-nav-bar.blade.php` | **Extend** — add hamburger + drawer markup (hidden on desktop) | None — new elements hidden via `display: none` |
| `resources/views/layouts/ui-template.blade.php` | **Extend** — add viewport-fit=cover + initMobileNav() call | None — viewport-fit only affects safe area, no visual change |
| `public/css/theme.css` | **Extend** — add all mobile CSS inside `@media (max-width: 768px)` | None — all new rules inside media queries |
| `public/js/ui-common.js` | **Extend** — add new mobile JS functions | None — new functions only, no existing code modified |
| All 6 page templates | **Extend** — add data-page attribute for scroll restoration | None — attribute only, no visual change |
| `page-changelogs/mobile-responsive-changelog.md` | **Create** — new changelog | None — new file only |

No new files created beyond the changelog. All mobile CSS lives in `theme.css` (shared). All mobile JS lives in `ui-common.js` (shared).
