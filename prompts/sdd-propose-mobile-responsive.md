# Spec: /sdd-propose for Mobile Responsive — Replacement Home

> **Single-page change** — affects only `replacement-home-UI-design-template.blade.php`.
> This prompt overrides the standard `sdd-propose-ui-page.md` workflow where it conflicts.

## Context (read first)

1. Read `CodingMAIN.md` §10.0 (UI Design Rules) — especially rule 9 (mobile responsive) and rule 10.
2. Read `public/css/theme.css` — current shared CSS, existing `@media` breakpoints at 1024px and 768px.
3. Read `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` — the page to enhance.
4. Read `page-changelogs/replacement-home-changelog.md` — learn house style + existing changes.

## Current mobile state

Already done:
- ✅ Card view (`.replacement-card`) replaces table on ≤768px
- ✅ Grid wrapper, pagination bar, sort hint hidden on mobile

Not done (required by Rule #10):
- ❌ Summary cards not stacked on mobile (`.summary-bar` has no mobile CSS)
- ❌ Filters not full-width on mobile (`.toolbar`, `.search-wrapper`, `.week-nav`)
- ❌ Quick View modal not bottom-sheet on mobile (no mobile CSS for `#quickViewModal`)
- ❌ Touch targets not audited (buttons/links may be < 44×44px)
- ❌ No responsive typography (title, card text)
- ❌ Keyboard shortcuts button hidden but not replaced with mobile alternative

## Scope — enhancements

### A. Summary Cards (≤768px)
| Item | Detail |
|------|--------|
| Current | 5 cards in a row (`.summary-bar` flex) |
| Target | 2-column grid: 3 cards top row, 2 cards bottom row |
| CSS | `.summary-bar { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }` in media query |
| Full-width | Last row cards span full width if odd count |

### B. Toolbar / Filters (≤768px)
| Item | Detail |
|------|--------|
| Search input | Full width, stack above week picker |
| Week nav | Full width below search |
| Layout | Stack toolbar-left items vertically |
| Result count | Hide on mobile (card view shows count implicitly) |
| Keyboard shortcut button | Hide on mobile |

### C. Quick View Modal → Bottom Sheet (≤768px)
| Item | Detail |
|------|--------|
| Position | Slide up from bottom (not centered) |
| Height | 80vh max, scrollable content |
| Drag handle | Top handle bar for visual affordance |
| Overlay | Full-screen backdrop |
| Animation | Slide up with CSS transition |
| Button | "Arrange Replacement" button full-width in footer |
| Close button | Full-width below arrange button |

### D. Touch Targets (WCAG 2.5.5)
| Item | Detail |
|------|--------|
| Minimum | All buttons/links ≥ 44×44px |
| Affected | Week arrows, card tap area, modal buttons, arrange button |
| Method | Add `min-height: 44px; min-width: 44px` where needed |

### E. Responsive Typography (≤768px)
| Item | Detail |
|------|--------|
| Page title | 24px → 20px |
| Card code | 14px → 13px |
| Card body | 12px → 11px |
| Method | CSS `clamp()` or media query overrides |

### F. Card View Improvements (≤768px)
| Item | Detail |
|------|--------|
| Card spacing | Reduce margin-bottom from 8px to 6px |
| Card padding | Reduce from 14px 16px to 12px 14px |
| Tap area | Entire card is tappable (already has `cursor: pointer`) |
| Active state | Already has `transform: scale(0.99)` — keep |

## Files to change

| File | Change type |
|------|-------------|
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | **Extend** — add mobile CSS in `@section('page-styles')` `@media` block |

Only the page template is modified. No shared files (`theme.css`, `ui-common.js`) are touched — this is page-specific mobile work.

## Deliverables (SDD format)

### 1. `proposal.md`
- Change name: `replacement-home-mobile-responsive`
- Problem: replacement-home page lacks full mobile compliance with Rule #10
- Scope: single page — `replacement-home-UI-design-template.blade.php`
- Out of scope: shared nav drawer (already in `ui-template`), other pages, backend, new dependencies

### 2. `design.md`
- §1 Existing mobile state (what's already done)
- §2 Summary cards mobile layout (2-column grid)
- §3 Toolbar mobile layout (stacked filters, full-width)
- §4 Quick View modal → bottom sheet (CSS + structure)
- §5 Touch target audit + fixes
- §6 Responsive typography
- §7 Card view refinements
- §8 Mobile view section (mandatory — document all mobile behaviors)

### 3. `tasks.md`
- Task 1: Summary cards — add 2-column grid CSS in media query
- Task 2: Toolbar — add stacked layout CSS in media query
- Task 3: Quick View modal — add bottom-sheet CSS in media query
- Task 4: Touch targets — audit + add min-height/min-width
- Task 5: Responsive typography — add clamp()/media query overrides
- Task 6: Card view — refine spacing/padding for mobile
- Task 7: Verify all mobile changes work together
- Task 8: Lint + typecheck
- Task 9: Update changelog

### 4. Changelog
- Update `page-changelogs/replacement-home-changelog.md` (ALREADY EXISTS — do NOT create new file)

## Constraints
- No new dependencies (pure CSS)
- All mobile CSS in the page's `@section('page-styles')` inside `@media (max-width: 768px)`
- Must not break desktop layout (all mobile rules inside media query)
- Must not break existing card view (`.replacement-card` already works)
- Quick View modal bottom-sheet must reuse existing `.modal-overlay` / `.modal` classes from `theme.css`
- Touch targets must meet WCAG 2.5.5 (≥44×44px)
- Commit prefix: `ui:`
