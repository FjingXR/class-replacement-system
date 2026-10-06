# Tasks: venue-event-blocks

**Status:** APPLIED 2026-10-07 — all tasks T0–T9 complete (apply-time
unfreeze rounds 1–2 for TC41 + declarative §9 correction; see
review-log.md). Awaiting `/sdd-verify`.

> Each task ≤ 2 hours. Dependencies noted in brackets. Code changes come ONLY
> after this file is frozen. Spec files skipped (single-page change, house
> precedent).

## Preflight

- [x] **T0 — Baseline test run [no deps]**
  `npx playwright test tests/venue-timetable.spec.ts` — record the exact
  failing set before any edit (expected failure candidates: TC34/TC35
  legend staleness, TC36/TC37/**TC38**/TC58 summary staleness, TC40/TC41
  phantom ids; TC32 passes today only because `.event-block` count is 0
  [T0 note: the recorded `baseline-tests.txt` output, not this pre-pinned
  list, is the authoritative baseline]). Save output to
  `.sdd/changes/venue-event-blocks/baseline-tests.txt`.

## Venue blade — `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

- [x] **T1 — Event-block rendering in cellRender [T0]** (design §2)
  - In the `info.event` head branch (~L772): replace the `cell-content`
    div with the design §2 event-block div verbatim (`event-block span-{N}`,
    tabindex/role/aria-label, `_evt`/`_di`/`__eventData`, dataset
    name/venue/tip2=lecturer, mine/others mapping (±`-pending`),
    `ev-code`/`ev-venue`/`ev-time` via `to12h`,
    `buildReplacementNote` with ownership check, click → `openModal`,
    focus → `focusedCell`, `if (info.span > 1) td.colSpan = info.span`).
  - In the `info && info.occupied` continuation branch (~L797): replace the
    `cell-content cell-occupied/cell-pending` div with
    `td.style.display = 'none'`.
  - Keep mobile booked-card emission (mobileSlotDayHeader + createEventCard)
    inside the head branch.
  - Unchanged branches (sunday/holiday/too-soon/available) — do not touch.

- [x] **T2 — Keyboard nav [T1]** (design §3)
  - Active check (~L1038): accept `event-block` alongside `cell-content` in
    the gate condition.
  - Roving selector (~L1041): `.cell-content[tabindex="0"], .event-block[tabindex="0"]`.
  - Enter (~L1052): no edit needed (`active._evt` covers blocks) — verify.

- [x] **T3 — Legend 4-item set [no deps]** (design §4)
  - Replace the 3-item `@include('partials.ui-legend-bar', [...])` args
    (~L410–416) with the §4 table verbatim (4 items, tokens + tips exactly
    as pinned).

- [x] **T3b — Booking hint copy [no deps]** (design §4a)
  - Booking-hint `<span>` (~L397): `Click any green slot to book this venue`
    → `Click any green empty slot to book this venue` (span text only;
    svg/container untouched).

- [x] **T4 — Summary cards restore + span-weighted counting [no deps]** (design §5)
  - Un-comment the `@include('partials.ui-summary-bar', [...])` block
    (~L418–433) keeping its existing 4 cards / ids / descriptions as-is.
  - Replace `updateSummaries()` body (~L889–918) with the design §5
    span-weighted deduped counter verbatim (events-array heads keyed
    `<di>:<start>` last-write-wins, non-offday guard, offday/too-soon DOM
    counts, updater contract `sumTotal/sumAvailable/sumPending/sumUnavailable`).

## Tests — `tests/venue-timetable.spec.ts` [T0]

- [x] **T5 — Fix stale expectations [T1..T4 + T3b]** (design §6 table)
  - TC32: `/BMIT\d+/` → `/[A-Z]{2,4}-\d{4}|[A-Z]{4}\d{4}/`.
  - TC34/TC35: 4-item legend; labels Available / Your Classes /
    Others' Classes / Pending.
  - TC36: `.summary-card` count 5 → 4.
  - TC37: id list → sumTotal, sumAvailable, sumPending, sumUnavailable.
  - TC38: leave unchanged (still valid).
  - TC40: `#eventModal .detail-row:has-text("Subject Code")` →
    `toContainText(/…code regex same as TC32/)`.
  - TC41: `page.locator('#eventModal .detail-row', { hasText: /\(\d+ seats\)/ })`
    → `toContainText(/[A-Z]\d{3}/)` (apply-time unfreeze round 2: the
    `:has-text("Venue")` locator was ambiguous — Status Description row also
    matches — while the page's own `openModal` (blade L924) does render
    `CODE — Type (N seats)`; user-approved 2026-10-07).
  - TC58: 5 → 4.
  - TC39/TC42–44: no edit (become live as-is).

## Verify

- [x] **T6 — Test suite green [T5]**
  `npx playwright test tests/venue-timetable.spec.ts` — all previously
  failing (T0 baseline) tests now pass; compare counts with
  `baseline-tests.txt` and save the post-change output to
  `.sdd/changes/venue-event-blocks/post-tests.txt`.

- [x] **T7 — Studio sweep [T6]** (design §8.3–8.5 — ALL sub-checks)
  Restart server stale-free
  (`pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &`),
  then Playwright on `/venue-timetable-ui` per design §8.4 + §9:
  1. Default view cards = **Total 154 / Available 66 / Pending 0 /
     Unavailable 88** (weeks idx 2, B002).
  2. Span-4 `AMCS2093` B110 Mon block = blue **`event-mine`**.
  3. Normal blocks = green **`event-others`** (e.g. B002's `MPU-3232` in
     week 2 — the mine/others distinction is not covered by any automated
     test; verify the class on the block's classList directly).
  4. Pending (grey `event-others-pending`) reachable set, all confirmed:
     week 10 B002 Mon 12–13 `MPU-3232` (2 → sumPending); week 1 B110
     **Tue** 2–5 `AMIS1012` (single grey block after last-write over the
     normal dft2s1 twin — NOT two blocks); week 4 B006 Tue 11–12
     `AMCS1013`; week 8 B015 **Wed** 6–9 `BMIT2073`.
  5. Tertiary `event-mine-pending` = **code-path inspection + CSS
     presence** (design §9): confirm the cellRender pending expression
     branches on `isMine`, and `theme.css:1952` exists; no reachable mock
     instance exists on this page — record the inspection in the
     review/verify notes.
  6. Modal via click + Enter on a block (subject-code row + venue row
     `CODE — Type (N seats)`, blade `openModal` L924); booking hint on the
     same page reads `Click any green empty slot to book this venue` (T3b).
  7. Keyboard arrows across blocks AND available cells; Book tooltip +
     `B` shortcut on available cells.
  8. Regression: `cell-too-soon` blackout + `cell-no-fit` unchanged
     (no automated coverage — verify visually/programmatically).
  9. Mobile (≤768px viewport): booking card list intact — booked cards
     still emitted by the rewritten head branch
     (mobileSlotDayHeader + createEventCard path) and available cards work.
  Screenshots to `/tmp/opencode/`.

- [x] **T8 — Cross-page + lint [T7]**
  - `/cohort-timetable-ui` quick sanity (no shared files touched).
  - `composer run lint:check && composer run types:check` — no new failures.

- [x] **T9 — Docs [T8]**
  - Postscript in `page-changelogs/venue-timetable-ui-changelog.md`
    (append `venue-event-blocks` entry per house format).
  - Tick tasks.md as completed; leave `.sdd/changes/` for `/sdd-verify`.
  - **No commit until the user explicitly says commit** (standing rule).
