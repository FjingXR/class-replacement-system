# Explore Brief: replacement-home-mobile-responsive

## Scope Expansion

Original SDD covered M1 (Quick View → bottom-sheet) + M2 (touch targets). M2 is already handled by shared `theme.css:1535-1544` (44×44px for all buttons/links on mobile). Expanding to:

| ID | Feature | Rationale |
|----|---------|-----------|
| M1 | Quick View Modal → Bottom Sheet | Modal centered on mobile; should slide up from bottom |
| M2 | Touch Targets | ⚠️ Already in theme.css (now 48×48px / 8dp) — REMOVE from SDD scope |
| M3 | Keyboard Shortcuts Modal → Bottom Sheet | Same issue as M1 — centered on mobile |
| M4 | Toolbar Mobile Layout | Week-nav needs full-width stacking; result count + buttons better aligned |

## Rejected Approaches

| Approach | Why Rejected |
|----------|--------------|
| Promote bottom-sheet to theme.css | Only 2 modals on this page need it; premature promotion before other pages need it |
| Modify ui-template.blade.php | Page-specific behavior, not cross-cutting |
| Add swipe gestures | Overkill for mock-data FYP; no real data to swipe through |
| Skeleton loading on mobile | Cross-cutting concern — separate SDD (already noted in proposal §Out of Scope) |

## Final Solution

### M1: Quick View Modal → Bottom Sheet (≤768px)

**Target:** `#quickViewModal`

**HTML change:**
- Add `<div class="sheet-handle"></div>` inside `.modal`, before `.modal-header`

**CSS (page-level `@media (max-width: 768px)`):**
```css
#quickViewModal .modal-overlay {
    align-items: flex-end;
    padding: 0;
}
#quickViewModal .modal {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 16px 16px 0 0;
    overflow-y: auto;
    animation: sheetUp 0.25s ease;
}
#quickViewModal .modal-footer {
    flex-direction: column;
    gap: 8px;
}
#quickViewModal .modal-footer .btn-action,
#quickViewModal .modal-footer .btn-close-modal {
    width: 100%;
}
```

**Animation:**
```css
@keyframes sheetUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}
```

**Drag handle CSS:**
```css
.sheet-handle {
    width: 36px; height: 4px;
    background: var(--color-outline);
    border-radius: 2px;
    margin: 8px auto 0;
}
```

### M3: Keyboard Shortcuts Modal → Bottom Sheet (≤768px)

**Target:** `#keyboardModal`

**HTML change:**
- Add `<div class="sheet-handle"></div>` inside `.modal`, before `.modal-header`

**CSS (page-level `@media (max-width: 768px)`):**
```css
#keyboardModal .modal-overlay {
    align-items: flex-end;
    padding: 0;
}
#keyboardModal .modal {
    max-width: 100%;
    max-height: 60vh;
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

**Note:** Keyboard shortcuts modal is hidden on mobile (`#kbShortcutsBtn { display: none }`), so M3 is LOW priority. Including for consistency if button is ever re-enabled.

### M4: Toolbar Mobile Layout (≤768px)

**Current state (theme.css:718-728):**
- `.toolbar` → column layout
- `.toolbar-left` → full width
- `.search-wrapper` → full width
- `.toolbar-right` → full width, centered

**Page-specific overrides (in `@media (max-width: 768px)`):**
```css
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
```

**Rationale:** Week-nav arrows + select should span full width; result count left-aligned, keyboard button right-aligned (if visible).

## Key Data Flows

No JS changes. Pure CSS enhancement. Existing functions `quickView()`, `hideQuickView()`, `showKeyboardShortcuts()`, `hideKeyboardShortcuts()` work unchanged.

## Files Changed

| File | Action |
|------|--------|
| `replacement-home-UI-design-template.blade.php` | Extend `@media` block + add sheet-handle divs to 2 modals |

No shared files modified.

## Open Questions

1. **M3 priority:** Keyboard shortcuts button is hidden on mobile. Should M3 be dropped from scope since the button is invisible? (Recommendation: keep for future-proofing, but mark as optional)
2. **M4 scope:** Is the week-nav full-width + result-count alignment enough, or do we need more granular control?

## Estimated Effort

| Task | Estimate |
|------|----------|
| M1: Quick View bottom-sheet | 20 min |
| M3: Keyboard shortcuts bottom-sheet | 10 min |
| M4: Toolbar layout | 10 min |
| Verify all + desktop regression | 10 min |
| Changelog | 5 min |
| **Total** | **~55 min** |
