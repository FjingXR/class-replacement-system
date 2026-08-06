# Tasks: Replacement Home — Mobile Responsive (Rule #10)

## Task 1: Quick View Modal → Bottom Sheet CSS
**Estimate**: 20 min

- [ ] Add `.sheet-handle` div to Quick View modal HTML (inside `.modal`, before `.modal-header`)
- [ ] Add `.sheet-handle` CSS: `width:36px; height:4px; background:var(--color-outline); border-radius:2px; margin:8px auto 0`
- [ ] Add `@keyframes sheetUp`: from `translateY(100%)` to `translateY(0)`
- [ ] Add mobile overrides in `@media (max-width: 768px)` for `#quickViewModal .modal-overlay`: `align-items:flex-end; padding:0`
- [ ] Add mobile overrides for `#quickViewModal .modal`: `max-width:100%; max-height:80vh; border-radius:16px 16px 0 0; overflow-y:auto; animation:sheetUp 0.25s ease`
- [ ] Add mobile overrides for `#quickViewModal .modal-footer`: `flex-direction:column; gap:8px`
- [ ] Add mobile overrides for footer buttons: `width:100%`
- [ ] Test: open Quick View on mobile viewport — modal slides up from bottom, scrollable, buttons full-width

## Task 2: Touch Targets (WCAG 2.5.5)
**Estimate**: 10 min

- [ ] Add mobile overrides in `@media (max-width: 768px)` for week arrows, modal buttons, arrange button: `min-height:44px; min-width:44px`
- [ ] Verify card tap area meets 44px minimum (already has `cursor: pointer` and full-row click)
- [ ] Test: all interactive elements ≥ 44×44px on mobile viewport

## Task 3: Verify All Mobile Changes
**Estimate**: 10 min

- [ ] Test Quick View modal on mobile: bottom-sheet, scrollable, drag handle visible, buttons full-width
- [ ] Test touch targets on mobile: week arrows, modal buttons, card tap
- [ ] Test desktop: no visual changes (all mobile rules inside `@media` query)
- [ ] Test tablet (769–1024px): no regressions from existing shared CSS

## Task 4: Lint + Typecheck
**Estimate**: 5 min

- [ ] Run `composer run lint:check` — confirm no new failures
- [ ] Run `composer run types:check` — confirm no new failures

## Task 5: Update Changelog
**Estimate**: 5 min

- [ ] Update `page-changelogs/replacement-home-changelog.md` with mobile responsive entries
- [ ] Follow existing format: `| Timestamp | Location | Change | Detail |`

---

**Total estimated time**: ~50 min
