# Proposal: Replacement Home — Mobile Responsive (Rule #10)

## Why This Change Is Needed

The replacement-home page has partial mobile support (card view replaces table on ≤768px) but does not fully comply with CodingMAIN.md Rule #10. Specifically, the Quick View modal remains centered on mobile instead of converting to a bottom-sheet, and touch targets fall short of WCAG 2.5.5 (44×44px). Most other Rule #10 requirements (summary cards, responsive typography, full-width inputs, safe area insets) are already handled by shared CSS in `theme.css`.

## Scope

### In Scope

Enhance the **existing** `replacement-home-UI-design-template.blade.php` with 2 mobile-specific improvements:

**M1: Quick View Modal → Bottom Sheet (≤768px)**
- Modal slides up from bottom (not centered)
- 80vh max height, scrollable body
- Drag handle bar at top for visual affordance
- Full-screen backdrop
- "Arrange Replacement" button full-width in footer
- Close button full-width below arrange button
- CSS: override `.modal-overlay` and `.modal` positioning in page-level `@media (max-width: 768px)`

**M2: Touch Targets (WCAG 2.5.5)**
- Upgrade touch targets from 40px (tablet breakpoint in `theme.css`) to 44×44px on mobile
- Affected elements: week arrows, modal close button, arrange button, card tap area
- CSS: page-level `@media (max-width: 768px)` overrides

### Already Done (no action needed)

These Rule #10 items are already implemented in shared `theme.css`:
- ✅ Summary cards: 1-column grid on mobile (`theme.css` line 1500)
- ✅ Responsive typography: `.page-title` uses `clamp(18px, 4vw, 20px)` (`theme.css` line 1545)
- ✅ Full-width inputs: `.filter-select, .search-input { width: 100% !important; }` (`theme.css` line 1564)
- ✅ Safe area insets: `env(safe-area-inset-*)` on body (`theme.css` line 1555)
- ✅ Card view: `.replacement-card` replaces table on ≤768px (page template)
- ✅ Grid/pagination/sort-hint hidden on mobile (page template)

### Out of Scope

- Shared nav drawer (already in `ui-template`)
- Other pages (each page gets its own SDD if needed)
- Backend changes
- New dependencies
- Skeleton loading, scroll restoration, toast position (cross-cutting, separate SDD)

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | **Extend** — add bottom-sheet modal CSS + touch target CSS in `@media` block |

No shared files (`theme.css`, `ui-common.js`) are modified — this is page-specific mobile work.
