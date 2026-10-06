# Explore Brief — sweep-fixes-round-2

**Date:** 2026-10-06 · **Branch:** `fjing` @ `4c920a2` · **Input:** sweep report remainder (F-5, F-6, F-8, F-9 — all 💡, deferred by round-1)
**Scope rule:** explore only — NOTHING in this brief is implemented yet.

---

## F-5 — legend hint says "Hover…" (touch surfaces) — SHARED PARTIAL

**Investigation.** The hint is one shared partial: `partials/ui-legend-bar.blade.php:11` —
`<span class="legend-hint">Hover a colour to learn more</span>`, tooltips via `data-tip`.
Consumed by **5 pages** (venue:399, cohort:73, arrangement:1016, my-timetable:188, student-my-timetable:48) with per-page `$items` params, styles in `theme.css:1271/1275`. On mobile both venue and cohort still render it (sweep screenshots) — and the tooltip system is hover/mouse-driven.

**Options.**
- **A (recommended):** CSS device-agnostic copy — ship BOTH texts (hover/touch spans) and swap with `@media (hover: none)`. Zero JS, theme-token only, fixes all 5 pages at once (3rd-duplication = it's already one partial). Copy: "Tap or hover to learn more"? No — single elegant option: hint becomes **"Tap or hover a colour to learn more"** for everyone? Simplest honest label; or keep two spans. Recommend two-span swap (precise per device).
- **B:** change the copy once to device-neutral phrasing — 1-line edit, slightly imprecise.
- **C:** hide the hint on `(hover: none)` — least text, but then mobile legend loses guidance.

## F-6 — venue mobile: 63 stacked bookable-slot cards

**Investigation.** `createEventCard`-adjacent mobile builder (venue:785–825): every 30-min bookable slot emits a `.venue-available-card` into `#mobileCardList` (63 cards on a fully-free day set). Each click → `showAvailableTooltip(null, di, hi)` → Book → arrangement. Booking itself is a 30-min START granule (`BOOK_SPAN = 4` → needs 4 consecutive slots ⇒ the "day-end guard" exists), so all 63 are start-candidates.

**Options.**
- **A (recommended):** day-grouped list — insert a sticky mini day-header before each day's run (Mon … 14 cards, Thu … 14 cards, …). Keeps all candidates, kills the wall of identical buttons; ~30 lines SVC in the builder + small CSS. No flow change.
- **B:** compact grid of small chips (2–3 per row) per day — denser, still scrollable; more styling churn.
- **C:** leave (documented known limitation only).

## F-8 — arrangement selection span (2 h default) vs class duration (1.5 h)

**Investigation — narrower than the sweep's framing.** `BLOCK_SPAN` IS duration-derived when `duration` comes via URL (arrangement:2837–2841: `Math.round(hrs*2)`, clamp 0.5–4 h) — and the home entry DOES pass it (home:431–437). So the sweep's 4-slot block happened on a **direct/no-duration entry** where 4 = the documented default.
The one real gap: **venue-arrival path** — subject + conflict slot picked ON the arrangement page; `selectSlot`/`commitSlotSelection` (arrangement:2638–2666) carry the slot's start/end (duration derivable) but never update `BLOCK_SPAN`. Landing from venue on a 1.5 h class ⇒ block stays 2 h.

**Options.**
- **A (recommended):** on `commitSlotSelection`, re-derive `BLOCK_SPAN` from the picked slot's span (clamped 0.5–4 h, whole-half-hour) — same rule as the URL branch, guarantees consistency no matter the entry path. Need care: re-derive BEFORE any committed selection preview; also `MAX_SELECTION` floor stays.
- **B:** accept the 4-slot default on no-URL entries (document it).

## F-9 — arrangement available cells lack a11y attributes (venue parity)

**Investigation.** Cell factory (arrangement:1553–1587): `.cell-available` branches bind `click`/`mouseenter`/`mouseleave` (:1575–1577, :1584–1586) — **no `tabIndex`, no `role="button"`, no `aria-label`**. The venue grid's cells have exactly those three (venue:792–801) — feature-parity gap; keyboard/AT users can't operate the main booking surface, and hover-preview is mouse-only.
Also selection previews live on `mouseenter` only — a focus handler would give keyboard users the same block preview (bonus, same lines).

**Options.**
- **A (recommended):** mirror the venue contract on both `.cell-available` branches: `tabIndex = 0`, `role = 'button'`, `aria-label = 'Available slot: ${day.abbr} ${hours[hi]}' (+ ', currently in preview' dynamic state optional)`, plus Enter/Space → `toggleCell` and `focus` → `previewBlock` (blur → clearPreview). Wrap-up: one small local helper shared by the two branches (they're dual by structure — could collapse into one branch, but minimal diff wins).
- **B:** a11y pass as part of a broader keyboard/AT audit round (bigger scope, defer).

---

## Recommendation

Take **A on all four**: F-5 two-span CSS swap (shared partial), F-6 day-grouped mobile list, F-8 commit-time BLOCK_SPAN re-derivation, F-9 venue-parity attributes + focus preview. All frontend-only, no datasets touched, no new dependencies. F-8's "URL branch stays the source of truth" principle is preserved; F-6/F-9 are arrangement/venue-local; F-5 is a 2-line shared-partial edit benefiting 5 pages.
