---
name: sweep-fixes-round-2
created: 2026-10-06
status: proposing
---
# Proposal — sweep-fixes-round-2

> **Change:** Implement the 4 explored fixes from the sweep remainder (F-5 legend copy, F-6 venue mobile day-grouping, F-8 BLOCK_SPAN re-derivation, F-9 arrangement cell a11y) — all "option A" per the explore brief, **user-approved 2026-10-06 (all four selected)**.
> **Explore baseline:** `explore-brief.md` (lines/finding analysis verified in-session).
> **Hard rule:** frontend mock only; theme.css tokens; no new dependencies; no datasets touched.

## F-5 — legend hint copy is device-aware
`partials/ui-legend-bar.blade.php:11` gains two spans (hover + touch copy) swapped by CSS `@media (hover: none)` in `theme.css` (tokens only):
- pointer devices: "Hover a colour to learn more" (unchanged)
- touch devices: "Tap a colour to learn more"
One partial edit → all 5 consumer pages fixed.

## F-6 — venue mobile slot list grouped by day
`venue-timetable-UI-design-template.blade.php` mobile-card builder (:785–825): before each new day's card run, emit a **day header row** (dedicated class, e.g. `.m-slot-day` — "Thu · 08 Oct 2026") into `#mobileCardList`. Cards keep their existing click → tooltip → Book flow. Header styling is theme-token only; list still rebuilt-clean each pass (the builder clears stale cards).

## F-8 — BLOCK_SPAN re-derives from the picked conflict slot
`commitSlotSelection` (arrangement :2664+) derives the slot's span (`slot.end − slot.start`) and updates `BLOCK_SPAN` with the same clamps as the URL branch (0.5–4 h → `Math.round(hrs*2)`, min floor). The URL param branch (:2837–2841) remains the initial source of truth; picking a slot whose duration differs (e.g. 1.5 h) resizes the block preview + confirm modal copy for ALL later picks in the session. MAX_SELECTION floor unchanged.

## F-9 — arrangement available cells reach venue's a11y contract
Both `.cell-available` branches (:1573–1577, :1582–1586) gain — extracted into one branch-local block to avoid duplication:
- `tabIndex = 0`, `role = 'button'`, `aria-label = 'Available slot: ${day.abbr} ${hours[hi]}'`
- `keydown` Enter/Space → `toggleCell(di, hi, div)` (preventDefault; Space too)
- `focus` → `previewBlock(di, hi, div)`, `blur` → `clearPreview()` (keyboard users get the same block preview mouse users get)
No change to mouse behaviour; too-soon/holiday cells stay inert.

## Acceptance criteria
1. All 5 legend pages show tap-appropriate copy under `(hover: none)` emulation, hover copy untouched elsewhere.
2. Venue mobile list shows day headers; card count unchanged; flow intact.
3. Venue-arrival + subject/slot pick on a 1.5 h class produces a 3-slot block preview + "3 slots" in the confirm modal.
4. Arrangement cells are keyboard-operable (Tab + Enter/Space), announce themselves, and focus previews the block; 0 console errors on all touched pages; changelog postscripts for every touched page + shared-file.
