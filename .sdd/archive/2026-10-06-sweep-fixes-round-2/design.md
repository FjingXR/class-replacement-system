# Design — sweep-fixes-round-2

Frozen decisions (user gate 2026-10-06): all four explored fixes, option A each.

## D-1 — F-5 device-aware legend hint
`ui-legend-bar.blade.php:11`:
```blade
<span class="legend-hint" aria-hidden="true">
    <span class="hint-hover">Hover a colour to learn more</span>
    <span class="hint-touch">Tap a colour to learn more</span>
</span>
```
`theme.css` (append to the `.legend-hint` area, ~:1275): `.hint-touch { display: none; }` +
`@media (hover: none) { .legend-bar .hint-hover { display: none; } .legend-bar .hint-touch { display: inline; } }`
— tokens untouched (text only). All 5 consumers fixed without page edits.

## D-2 — F-6 venue mobile day grouping
In the mobile-card emission loop (venue:785–825, inside the per-slot for-each): track `lastDayHeader`; when `days[di].date` differs from the previous card, append
`<div class="m-slot-day">${day.abbr} · ${day.date</span>}</div>` before the card. CSS added in the page's own style block (tokens only): full-width header row, `.legend-hint`-like typography (11px, on-surface-variant, radius-sm, surface-variant bg — matches the event-card family). The builder's clear-then-rebuild pattern keeps headers consistent on week/venue changes (they're re-emitted with the run, no separate lifecycle).

## D-3 — F-8 span re-derivation
`commitSlotSelection` (arrangement:2664) — first lines, before the preview/state commit:
```js
/* F-8: the block size follows the picked conflict slot's duration (same clamp
   as the URL branch) so venue-arrival picks match home-path picks. */
const slotSpan = Math.round(Math.min(Math.max((slot.end - slot.start) / 2, 0.5), 4) * 2);
if (slotSpan !== BLOCK_SPAN) { BLOCK_SPAN = slotSpan; MAX_SELECTION = Math.max(MAX_SELECTION, BLOCK_SPAN); }
```
Wait — derive from slot.start/end directly: `(slot.end - slot.start) / 2` = hours (slot indices are 30-min units). So `slotSpan = slot.end - slot.start` IS already the 30-min-slot count — clamp to [1..8] (0.5–4 h) whole slots:
```js
const slotSpan = Math.min(Math.max(slot.end - slot.start + 1, 1), 8);
if (slotSpan !== BLOCK_SPAN) { BLOCK_SPAN = slotSpan; MAX_SELECTION = Math.max(MAX_SELECTION, BLOCK_SPAN); }
```
> **Corrected during verify-at-apply (2026-10-06):** `slot.end` is INCLUSIVE (the codebase
> convention is `(end − start + 1)`, cf. upcoming-replacements:162/277 — the exclusive-end
> draft above produced "2 slots" for a 1.5 h class in the first live check). The shipped
> line (arrangement:2684) is the corrected one; recorded in tasks.md T4 + both changelogs.
```
Placement BEFORE `clearMergedBlock`/preview logic that reads BLOCK_SPAN. URL branch keeps initial authority (both branches use the same clamp family, min 0.5 h / 4 h cap).

## D-4 — F-9 cell a11y parity
In `cellRender`, both `.cell-available` branches (:1573–1577, :1582–1586) — replace the four handler-lines with one call to a local helper defined right above `buildTimetableGrid`:
```js
/* F-9 — venue-parity a11y for bookable cells (role + keyboard + announce). */
function setupAvailableCell(div, di, hi) {
    div.tabIndex = 0;
    div.setAttribute('role', 'button');
    div.setAttribute('aria-label', 'Available slot: ' + days[di].abbr + ' ' + hours[hi]);
    div.addEventListener('click', () => toggleCell(di, hi, div));
    div.addEventListener('mouseenter', () => previewBlock(di, hi, div));
    div.addEventListener('mouseleave', () => clearPreview());
    div.addEventListener('focus', () => previewBlock(di, hi, div));
    div.addEventListener('blur', () => clearPreview());
    div.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleCell(di, hi, div); }
    });
}
```
Branches become `div.className += ' cell-available'; setupAvailableCell(div, di, hi);`. `days`/`hours` are in scope at both call sites (buildTimetableGrid closure params). Venue keeps its own richer contract (tooltip !== preview) — no shared promotion yet (2 similar ≠ 3 dupes).

## Promoted to shared
- Nothing new (F-5 = existing partial + theme.css; the rest stays page-local until a 3rd duplication).

## Risks / guardrails
- Arrangement is the most stateful page — live-verify: subject/slot pick → preview size, confirm modal copy ("N slots"), deselect, keyboard Tab/Enter, 0 console errors.
- Day-header lifecycle in venue mobile: verify week + venue swaps rebuild cleanly.
- All styling token-only; check both themes.
