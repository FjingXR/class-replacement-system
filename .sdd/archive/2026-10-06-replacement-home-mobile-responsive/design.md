# Design: Replacement Home — Mobile Responsive (Rule #10)

## Technical Approach

Add page-specific mobile CSS in `replacement-home-UI-design-template.blade.php`'s existing `@media (max-width: 768px)` block. Three changes: Quick View modal → bottom-sheet, Keyboard Shortcuts modal → bottom-sheet, and toolbar layout improvements. All CSS is page-level — no shared files modified.

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

### M3: Keyboard Shortcuts Modal → Bottom Sheet (≤768px)

Same pattern as M1 but for `#keyboardModal`. Lower priority since the keyboard shortcuts button is hidden on mobile (`#kbShortcutsBtn { display: none }`), but included for consistency.

**CSS overrides:**
```css
#keyboardModal .modal-overlay {
    align-items: flex-end;
    padding: 0;
}
#keyboardModal .modal {
    max-width: 100%;
    max-height: 60vh;             /* shorter content */
    border-radius: 16px 16px 0 0;
    overflow-y: auto;
    animation: sheetUp 0.25s ease;
}
#keyboardModal .modal-footer {
    flex-direction: column;
}
#keyboardModal .modal-footer .btn-close-modal {
    width: 100%;
}
```

**HTML change:** Add drag handle div to keyboard modal markup.

### M4: Toolbar Mobile Layout (≤768px)

The shared `theme.css` already makes the toolbar column layout at 1024px and full-width at 768px. Page-specific improvements:

```css
@media (max-width: 768px) {
    .week-nav {
        width: 100%;
        justify-content: space-between;
    }
    .week-select {
        flex: 1;
        min-width: 0;
    }
    .toolbar-right {
        justify-content: space-between;
        padding-top: 8px;
        border-top: 1px solid var(--color-outline);
    }
}
```

**Rationale:** Week-nav arrows + select span full width for easier tapping; result count and keyboard button (if visible) separated with subtle border.

### Data Flow

No JS changes — pure CSS enhancement. The existing `quickView()`, `hideQuickView()`, `showKeyboardShortcuts()`, `hideKeyboardShortcuts()` functions work unchanged; only the visual presentation differs on mobile.

## Dependencies

- `public/css/theme.css` — existing `.modal-overlay`, `.modal`, `.modal-footer`, `.toolbar` classes (no changes)
- `public/js/ui-common.js` — no changes

## Promoted to Shared

No promotions — all CSS is page-specific. If bottom-sheet modal pattern is needed on other pages, promote to `theme.css` in a future SDD.

## Mobile View

- **M1**: Quick View modal converts to bottom-sheet on ≤768px: slides up from bottom, 80vh max, drag handle, full-width buttons, scrollable body
- **M3**: Keyboard shortcuts modal converts to bottom-sheet on ≤768px: slides up from bottom, 60vh max, drag handle, full-width close button
- **M4**: Toolbar layout improved: week-nav full-width, result count + buttons aligned with separator
- **Already done**: Summary cards 1-column, responsive typography (clamp), full-width inputs, safe area insets, touch targets 48px (8dp), card view, grid hidden — all in shared `theme.css`

## File Changes

| File | Action | Lines Added (est.) |
|------|--------|-------------------|
| `replacement-home-UI-design-template.blade.php` | Extend `@media` block + add sheet-handle divs | +35 lines (CSS: ~25, HTML: ~10 for 2 drag handles) |

**Total estimated lines**: 609 + 35 = ~644 lines (well under 1500 limit)
