# Design: Replacement Home — Mobile Responsive (Rule #10)

## Technical Approach

Add page-specific mobile CSS in `replacement-home-UI-design-template.blade.php`'s existing `@media (max-width: 768px)` block. Two changes: Quick View modal → bottom-sheet, and touch target upgrades. All CSS is page-level — no shared files modified.

## Architecture Decisions

### M1: Quick View Modal → Bottom Sheet (≤768px)

The Quick View modal (`#quickViewModal`) currently uses the shared `.modal-overlay` + `.modal` classes from `theme.css`, which center the modal. On mobile, we override:

**CSS overrides (page-level `@media (max-width: 768px)`):**
```css
#quickViewModal .modal-overlay {
    align-items: flex-end;        /* bottom of viewport */
    padding: 0;                   /* full-width */
}
#quickViewModal .modal {
    max-width: 100%;              /* full-width */
    max-height: 80vh;             /* capped height */
    border-radius: 16px 16px 0 0; /* rounded top only */
    overflow-y: auto;             /* scrollable */
    animation: sheetUp 0.25s ease;
}
#quickViewModal .modal-footer {
    flex-direction: column;       /* stack buttons vertically */
    gap: 8px;
}
#quickViewModal .modal-footer .btn-action,
#quickViewModal .modal-footer .btn-close-modal {
    width: 100%;                  /* full-width buttons */
}
```

**Drag handle:** Add a `<div class="sheet-handle"></div>` inside `.modal` before `.modal-header`:
```css
.sheet-handle {
    width: 36px; height: 4px;
    background: var(--color-outline);
    border-radius: 2px;
    margin: 8px auto 0;
}
```

**Animation:** `@keyframes sheetUp` slides from `translateY(100%)` to `translateY(0)`.

**HTML change:** Add drag handle div to modal markup (page template only).

### M2: Touch Targets (WCAG 2.5.5)

The shared `theme.css` sets 40px min at tablet breakpoint (≤1024px). We upgrade to 44px on mobile:

```css
@media (max-width: 768px) {
    #quickViewModal .modal-close,
    #quickViewModal .btn-action,
    #quickViewModal .btn-close-modal,
    .week-arrow,
    .replacement-card {
        min-height: 44px;
        min-width: 44px;
    }
}
```

### Data Flow

No JS changes — pure CSS enhancement. The existing `quickView()` and `hideQuickView()` functions work unchanged; only the visual presentation differs on mobile.

## Dependencies

- `public/css/theme.css` — existing `.modal-overlay`, `.modal`, `.modal-footer` classes (no changes)
- `public/js/ui-common.js` — no changes

## Promoted to Shared

No promotions — all CSS is page-specific. If bottom-sheet modal pattern is needed on other pages, promote to `theme.css` in a future SDD.

## Mobile View

- **M1**: Quick View modal converts to bottom-sheet on ≤768px: slides up from bottom, 80vh max, drag handle, full-width buttons, scrollable body
- **M2**: Touch targets upgraded to 44×44px on mobile (WCAG 2.5.5)
- **Already done**: Summary cards 1-column, responsive typography (clamp), full-width inputs, safe area insets, card view, grid hidden — all in shared `theme.css`

## File Changes

| File | Action | Lines Added (est.) |
|------|--------|-------------------|
| `replacement-home-UI-design-template.blade.php` | Extend `@media` block | +25 lines (CSS: ~20, HTML: ~5 for drag handle) |

**Total estimated lines**: 680 + 25 = ~705 lines (well under 1500 limit)
