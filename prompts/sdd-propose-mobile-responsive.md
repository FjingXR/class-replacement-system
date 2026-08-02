# Spec: /sdd-propose for Mobile Responsive + Nav Drawer

> **Cross-cutting change** — affects ALL existing UI pages, not a single new page.
> This prompt overrides the standard `sdd-propose-ui-page.md` workflow where it conflicts.

## Context (read first)

1. Read `CodingMAIN.md` §10.0 (UI Design Rules) — especially rule 9 (mobile responsive) and rule 10.
2. Read `public/css/theme.css` — current shared CSS, existing `@media` breakpoints at 1024px and 768px.
3. Read `resources/views/partials/ui-nav-bar.blade.php` — current desktop nav.
4. Read `resources/views/layouts/ui-template.blade.php` — layout wrapper.
5. Read `public/js/ui-common.js` — existing shared JS helpers.
6. Read every page under `resources/views/ui-design-templates/` to inventory mobile issues:
   - `student-my-timetable-UI-design-template.blade.php`
   - `MyTimetable-UI-design-template.blade.php`
   - `CohortTimetable-UI-design-template.blade.php`
   - `my-request-history-UI-design-template.blade.php`
   - `replacement-arrangement-UIdesign-template.blade.php`
   - `replacement-home-UI-design-template.blade.php`

## Scope — all enhancements

### A. Nav Drawer (≤768px)
| Item | Detail |
|------|--------|
| Trigger | 3-line hamburger icon (☰) in top-left, replaces desktop links |
| Drawer | Slides in from left, full height, dark surface background |
| Items | Same `$navItems` array, stacked vertically, active indicator |
| Close | Tap X button, tap overlay, or swipe left |
| Overlay | Semi-transparent black backdrop, closes drawer on tap |
| Body scroll | Locked when drawer open (`overflow: hidden`) |
| Breakpoint | ≤768px |

### B. Timetable Grid Card Layout (≤768px)
| Item | Detail |
|------|--------|
| Convert | Data table → card layout (each day = one card) |
| Card content | Day name, date, event blocks stacked vertically |
| Scroll | Vertical scroll (no horizontal) |
| Sticky | Day card header sticks on scroll |

### C. Summary Cards (≤768px)
| Item | Detail |
|------|--------|
| Layout | 2-column grid (5 cards → 3+2) |
| Fallback | 1-column if 2 still squishes |
| Full-width | Cards span full width of container |

### D. Legend Bar (≤768px)
| Item | Detail |
|------|--------|
| Layout | Flex-wrap, items flow into 2 rows naturally |
| Gap | Maintain consistent gap between items |

### E. Semester Bar (≤768px)
| Item | Detail |
|------|--------|
| Week select | Reduce min-width, allow text truncation |
| Arrows + Today | Stack below select or reduce padding |
| Full-width | Bar spans full width |

### F. Page Header (≤768px)
| Item | Detail |
|------|--------|
| Chips | Stack vertically with spacing |
| Title | Reduce font size |
| Description | Full width |

### G. Bottom Sheet Modals (≤768px)
| Item | Detail |
|------|--------|
| Position | Slide up from bottom (not centered) |
| Height | 80vh max, scrollable content |
| Drag handle | Top handle bar for swipe-to-dismiss |
| Overlay | Full-screen backdrop |
| Animation | Slide up from bottom with transition |

### H. Touch Targets (WCAG 2.5.5)
| Item | Detail |
|------|--------|
| Minimum size | All buttons/links ≥ 44×44px |
| Padding | Add tap area padding where needed |
| Affects | Nav items, legend items, week arrows, Today button, modal close |

### I. Swipe Gestures (≤768px)
| Item | Detail |
|------|--------|
| Week navigation | Swipe left = next week, swipe right = previous week |
| Visual hint | Subtle arrow indicator on swipe edges |
| Threshold | Minimum 50px swipe distance |
| Debounce | Prevent rapid swiping |

### J. Collapsible Day Rows (≤768px)
| Item | Detail |
|------|--------|
| Behavior | Each day card collapsible (tap header to expand/collapse) |
| Default | Expanded on load, collapsed on scroll past |
| Icon | Chevron down/up indicator |
| Animation | Smooth height transition |

### K. Responsive Typography (≤768px)
| Item | Detail |
|------|--------|
| Page title | Reduce from 24px to 20px |
| Day labels | Reduce from 14px to 13px |
| Event text | Reduce from 12px to 11px |
| Method | CSS `clamp()` for fluid scaling |

### L. Safe Area Insets
| Item | Detail |
|------|--------|
| iPhone notch | `env(safe-area-inset-top)` for status bar |
| Home indicator | `env(safe-area-inset-bottom)` for bottom nav |
| CSS | `padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left)` |

### M. Full-width Form Inputs (≤768px)
| Item | Detail |
|------|--------|
| Selects | `width: 100%` on mobile |
| Text inputs | `width: 100%` on mobile |
| Buttons | Full-width primary actions |

### N. Skeleton Loading
| Item | Detail |
|------|--------|
| When | While data loads (page init, week switch) |
| Shape | Grey placeholder blocks matching layout (cards, rows, headers) |
| Animation | Subtle shimmer/pulse effect |
| Duration | Until `DOMContentLoaded` or data fetch completes |

### O. Scroll Restoration
| Item | Detail |
|------|--------|
| Behavior | Remember scroll position per page |
| Trigger | Browser back/forward navigation |
| Implementation | `sessionStorage` to save/restore `scrollTop` |
| Scope | Timetable grid, request history list |

### P. Toast Position (≤768px)
| Item | Detail |
|------|--------|
| Mobile | Toasts at bottom-center (thumb-reachable) |
| Desktop | Toasts stay top-right (existing) |
| CSS | `bottom: 24px; left: 50%; transform: translateX(-50%)` on mobile |

### Q. Viewport Meta
| Item | Detail |
|------|--------|
| Tag | `<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">` |
| Location | `resources/views/layouts/ui-template.blade.php` `<head>` |
| Purpose | Proper scaling + safe area support |

## Files to change

| File | Change type |
|------|-------------|
| `resources/views/partials/ui-nav-bar.blade.php` | **Rewrite** — hamburger + drawer markup |
| `resources/views/layouts/ui-template.blade.php` | **Modify** — drawer container + overlay + safe-area |
| `public/css/theme.css` | **Extend** — all mobile media queries (shared) |
| `public/js/ui-common.js` | **Extend** — `initMobileNav()`, `initSwipeGesture()`, `initCollapsibleCards()` |
| All 6 page templates | **Minimal** — verify no page-specific overrides break shared mobile rules |

## Deliverables (SDD format)

### 1. `proposal.md`
- Change name: `mobile-responsive`
- Problem: all pages lack mobile layout; nav overflows; grid unscrollable; modals not mobile-friendly
- Scope: cross-cutting (all 6 pages + shared files)
- Out of scope: new pages, backend logic, new dependencies, Performance & Polish

### 2. `design.md`
- §1 Nav drawer: markup, CSS, JS behavior, breakpoint
- §2 Card layout: timetable grid → card conversion
- §3 Shared mobile CSS: media queries for all components
- §4 Bottom sheet modals: markup, CSS, JS
- §5 Touch targets: audit + fixes
- §6 Swipe gestures: JS implementation
- §7 Collapsible cards: markup, CSS, JS
- §8 Typography: responsive font sizes
- §9 Safe area insets: CSS
- §10 Full-width inputs: CSS
- §11 Page-specific fixes (if any)
- §12 Skeleton loading: markup, CSS, animation
- §13 Scroll restoration: JS implementation
- §14 Toast position: mobile CSS
- §15 Viewport meta: HTML tag
- §16 Promoted to shared: all new mobile CSS → `theme.css`, JS → `ui-common.js`

### 3. `tasks.md`
- Task 1: Add hamburger + drawer markup to `ui-nav-bar.blade.php`
- Task 2: Add drawer container + overlay + safe-area to `ui-template.blade.php`
- Task 3: Add `initMobileNav()` to `ui-common.js`
- Task 4: Add `initSwipeGesture()` to `ui-common.js`
- Task 5: Add `initCollapsibleCards()` to `ui-common.js`
- Task 6: Add mobile media queries to `theme.css` (all components)
- Task 7: Add card layout CSS for timetable grid
- Task 8: Add bottom sheet modal CSS
- Task 9: Add touch target fixes to `theme.css`
- Task 10: Add responsive typography to `theme.css`
- Task 11: Add safe area insets to `theme.css`
- Task 12: Add full-width input CSS
- Task 13: Add skeleton loading CSS to `theme.css`
- Task 14: Add scroll restoration JS to `ui-common.js`
- Task 15: Add mobile toast position CSS to `theme.css`
- Task 16: Add viewport meta to `ui-template.blade.php`
- Task 15: Add skeleton loading CSS to `theme.css`
- Task 16: Add scroll restoration JS to `ui-common.js`
- Task 17: Add mobile toast position CSS to `theme.css`
- Task 18: Add viewport meta to `ui-template.blade.php`
- Task 19: Verify + fix each page template (6 pages)
- Task 20: Lint + typecheck
- Task 21: Changelog

### 4. Changelog
- `page-changelogs/mobile-responsive-changelog.md`

## Constraints
- No new dependencies (pure CSS + vanilla JS)
- All mobile CSS in `theme.css` (shared, not page-specific `@section('page-styles')`)
- Hamburger icon: inline SVG (no icon library)
- Drawer animation: CSS `transform: translateX()` + `transition: 0.3s`
- Must not break desktop layout (all mobile rules inside `@media (max-width: 768px)`)
- Card layout must show same data as grid (no info loss)
- Touch targets must meet WCAG 2.5.5 (≥44×44px)
