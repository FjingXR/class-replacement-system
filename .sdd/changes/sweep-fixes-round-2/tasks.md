# Tasks — sweep-fixes-round-2

- [x] T1 (D-1) ui-legend-bar partial: dual hint spans (`hint-hover` / `hint-touch`)
- [x] T2 (D-1) theme.css: `.hint-touch { display:none }` + `@media (hover: none)` swap (served CSS verified; media-emulation not available in this harness — rule is standard CSS)
- [x] T3 (D-2) venue mobile builder: `.m-slot-day` day headers before each day's card run + local CSS; verified: 5 headers Week 11 (Mon/Wed/Thu/Fri/Sat — only days WITH cards), Week 12 rebuild → fresh 6-header set, no duplicates, 0 console errors
- [x] T4 (D-3) arrangement `commitSlotSelection`: BLOCK_SPAN re-derivation — **fixed a verify-caught off-by-one**: `slot.end` is INCLUSIVE (codebase convention `(end − start + 1)`, cf. upcoming:162/277; the first live check produced 2 slots for a 1.5 h class). Final: `Math.min(Math.max(slot.end - slot.start + 1, 1), 8)`. Verified live: venue-arrival → subject BMIT6767 → W3 slot (10:00–11:30) commits → `BLOCK_SPAN = 3` → grid block "10:00 AM – 11:30 AM" → confirm modal "**3 slots**" ✓ home-path URL behaviour untouched
- [x] T5 (D-4) arrangement: `setupAvailableCell()` helper (both branches): role=button, tabIndex 0, aria-label "Available slot: Thu 11:00", Enter/Space → toggle (verified: Enter selects "11:00 AM – 12:30 PM" 3-slot block, second Enter deselects), focus/blur → preview; 0 console errors
- [x] T6 live verification sweep complete (all above + both-themes visuals in changelog screenshots)
- [x] BONUS (design T9): previously-skipped pages' token scan (static across all 9 templates) → found + fixed venue `color: #fff` ×2 → `var(--color-on-primary)`; all other hits = shadow/scrim ink (documented as tolerated) or `&#9734;` false positive
- [x] T7 changelog postscripts: replacement-arrangement, venue-timetable-ui, oop-blade-refactor (shared partial + theme.css)
- [x] T8 sweep report: F-5/F-6/F-8/F-9 marked fixed

**Scope note:** T7 trimmed vs plan (the 3 timetable pages' own changelogs not re-touched — their only change is the shared partial, logged once in oop-blade-refactor; arrangement/venue got real postscripts). Bad state at verify: some blocked-selection guard modals surfaced during scripted flows ("Clear current selection", switch-confirm) — all behave per design (highlighted, not bugs).
