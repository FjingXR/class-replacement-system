# Proposal: Replacement Home — Mobile Responsive (Rule #10)

## Why This Change Is Needed

The replacement-home page has partial mobile support (card view replaces table on ≤768px) but does not fully comply with CodingMAIN.md Rule #10. Specifically:

1. The Quick View modal remains centered on mobile instead of converting to a bottom-sheet
2. The Keyboard Shortcuts modal has the same centered issue (button hidden on mobile, but modal should be consistent)
3. The toolbar layout needs better stacking on small screens

Most other Rule #10 requirements (summary cards, responsive typography, full-width inputs, safe area insets, touch targets) are already handled by shared CSS in `theme.css`.

## Scope

### In Scope

Enhance the **existing** `replacement-home-UI-design-template.blade.php` with 3 mobile-specific improvements:

**M1: Quick View Modal → Bottom Sheet (≤768px)**
- Modal slides up from bottom (not centered)
- 80vh max height, scrollable body
- Drag handle bar at top for visual affordance
- Full-screen backdrop
- "Arrange Replacement" button full-width in footer
- Close button full-width below arrange button
- CSS: override `.modal-overlay` and `.modal` positioning in page-level `@media (max-width: 768px)`

**M3: Keyboard Shortcuts Modal → Bottom Sheet (≤768px)**
- Same bottom-sheet pattern as M1
- 60vh max height (shorter content)
- Drag handle bar at top
- Close button full-width in footer
- CSS: override `#keyboardModal` in same `@media` block

**M4: Toolbar Mobile Layout (≤768px)**
- Week-nav arrows + select span full width
- Result count left-aligned, keyboard button right-aligned (if visible)
- Subtle separator between toolbar sections

### Already Done (no action needed)

These Rule #10 items are already implemented in shared `theme.css`:
- ✅ Summary cards: 1-column grid on mobile (`theme.css` line 1555)
- ✅ Responsive typography: `.page-title` uses `clamp(18px, 4vw, 20px)`
- ✅ Full-width inputs: `.filter-select, .search-input { width: 100% !important; }`
- ✅ Safe area insets: `env(safe-area-inset-*)` on body
- ✅ Card view: `.replacement-card` replaces table on ≤768px (page template)
- ✅ Grid/pagination/sort-hint hidden on mobile (page template)
- ✅ Touch targets: 48×48px for all buttons/links on mobile (`theme.css` line 1535)

### Out of Scope

- Shared nav drawer (already in `ui-template`)
- Other pages (each page gets its own SDD if needed)
- Backend changes
- New dependencies
- Skeleton loading, scroll restoration, toast position (cross-cutting, separate SDD)
- Swipe gestures, pull-to-refresh (overkill for mock-data FYP)

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | **Extend** — add bottom-sheet modal CSS + toolbar CSS in `@media` block; add sheet-handle divs to 2 modals |

No shared files (`theme.css`, `ui-common.js`) are modified — this is page-specific mobile work.
