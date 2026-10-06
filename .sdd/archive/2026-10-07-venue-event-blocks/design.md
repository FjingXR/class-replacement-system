# Design: venue-event-blocks

Frozen baseline: `proposal.md` (R2 PASS 2026-10-06). This design pins every
remaining implementer decision. Single-file code change +
test-spec update + changelog; **no shared files**.

## 1 Approach

`venue-timetable-UI-design-template.blade.php` keeps
`buildTimetableGrid({ cellRender })` (the booking cell model it shares with
replacement-arrangement). Only the **occupied-state branches** of its
`cellRender` change — they now emit the cohort-default-path event-block
markup instead of per-half-hour `cell-content` divs. Booking branches
(available / too-soon / holiday / Sunday) are untouched.

## 2 Event-block rendering (`cellRender` `info.event` branch)

```js
} else if (info && info.event) {
    const e = info.event;
    const div = document.createElement('div');
    div.className = 'event-block span-' + info.span;   // span = e.end - e.start + 1 (half-hours)
    div.setAttribute('tabindex', '0');
    div.setAttribute('role', 'button');
    div.setAttribute('aria-label', `View class details: ${e.code} ${hours[hi]}`);
    div._evt = e; div._di = di; div.__eventData = e;   // parity with default builder
    div.dataset.name  = e.name  || '';
    div.dataset.venue = e.venue || '';
    div.dataset.tip2  = e.lecturer || '—';             // `.event-block::after` tooltip: "name · lecturer"
    var isMine = e.lecturer === MockData.currentUser.name;
    if (e.status === 'pending') {
        div.classList.add(isMine ? 'event-mine-pending' : 'event-others-pending');
    } else {
        div.classList.add(isMine ? 'event-mine' : 'event-others');
    }
    const startTime = to12h(hours[e.start]);
    const endTime   = to12h(hours[e.end + 1] || add30min(hours[e.end]));
    div.innerHTML =
        '<span class="ev-code">' + e.code + '(' + e.type + ')</span>' +
        '<span class="ev-venue">' + e.venue + '</span>' +
        '<span class="ev-time">' + startTime + ' - ' + endTime + '</span>' +
        buildReplacementNote(e, {
            checkOwnership: function (ev) { return ev.lecturer === MockData.currentUser.name; }
        });
    div.addEventListener('click', function () { openModal(e, di); });
    div.addEventListener('focus', function () { focusedCell = div; });
    td.appendChild(div);
    if (info.span > 1) td.colSpan = info.span;   // parity: builder sets it only when span > 1
    /* mobile booked card — UNCHANGED */
    mobileSlotDayHeader(di);
    document.getElementById('mobileCardList').appendChild(createEventCard(e, di));
} else if (info && info.occupied) {
    td.style.display = 'none';                          // continuation consumed by head's colSpan
}
```

Decisions pinned:
- **No `cell-content` class on blocks** — the keyboard selector (§3) includes
  `.event-block` explicitly instead. Leaves `.cell-*` counting selectors
  meaning unchanged for available/too-soon/sun/ph cells.
- **No conflict/pending-of-holiday branch** — unreachable: venue's
  sunday/holiday branch runs FIRST in `cellRender` (kept per proposal).
- **`ev-venue` kept** even though it equals the page's venue — parity with
  the cohort/My-Timetable block markup beats local de-duplication (a block
  template that omits a field would fork the shared CSS layout).
- `td.colSpan` is set inside `cellRender` before the builder appends the
  row — same visual merge as the default builder path (`ui-common.js:783-785`).
- No new CSS: `.event-block`, `.span-*` (no CSS needed — layout is
  `td.colSpan`, same as cohort), `.event-mine`/`event-others`/`-pending`
  all already exist in `theme.css` (1898–1955) and are scoped `.timetable …`
  which applies to this grid (`partials.ui-grid-table` table class).

## 3 Keyboard navigation (~L1019–1062, three one-line edits)

1. Active check (~L1038):
   `if (!active || !(active.classList.contains('cell-content') || active.classList.contains('event-block')) || active.tabIndex !== 0) return;`
2. Roving selector (~L1041): `'.cell-content[tabindex="0"], .event-block[tabindex="0"]'`
3. Enter (~L1052): unchanged — `active._evt` now also matches event blocks
   → `openModal(active._evt, active._di)`. `B` shortcut unchanged
   (`.cell-available` only).

## 4 Legend (4 items — Q2 trimmed; Q1 overlap accepted)

Replace the 3-item `@include('partials.ui-legend-bar', [...])` args (~L410):

| # | color arg | label | tip |
|---|---|---|---|
| 1 | `var(--color-success-container)` | `Available` | `Free slot — click to book this venue (Sunday, holiday and lead-time slots can't be booked)` |
| 2 | `var(--color-primary-container)` | `Your Classes` | `Your sessions in this venue, incl. replacement sessions` |
| 3 | `var(--color-success-container)` | `Others' Classes` | `Other lecturers' sessions — these are filled blocks, empty green cells are bookable` |
| 4 | `var(--color-tertiary-container)` | `Pending` | `Replacement request awaiting PL approval — others' pending requests show grey` |

Tips carry the Q1 mitigation (green overlap explanation) and fold
others'-pending (grey `--color-surface-variant`), Sunday/PH and too-soon
into item 1/4 tips. All tokens exist; partial stays data-driven.

### §4a Booking hint copy (unfreeze round, user decision B)

Blade ~L397, inside the existing `.booking-hint` div — span text ONLY:

```html
<!-- before --> <span>Click any green slot to book this venue</span>
<!-- after  --> <span>Click any green empty slot to book this venue</span>
```

The svg and container attrs are untouched; this is the sole copy change in
the change (justification: the old line is literally false once green
`event-others` blocks render — see proposal item 6 / Known Trade-off).

## 5 Summary cards (restored)

Un-comment the `@include('partials.ui-summary-bar', [...])` (~L418–433) with
its previously designed 4 cards (same `valueId`s, card classes, and inline
`'description'` strings it already had):

`card-total/sumTotal "Total Slots"` · `card-available/sumAvailable
"Available"` · `card-pending/sumPending "Pending"` ·
`card-conflict/sumUnavailable "Unavailable"`.

`updateSummaries(events)` rewritten to span-weighted, deduped counting
(grid-equivalent per proposal item 4):

```js
function updateSummaries(events) {
    if (!document.getElementById('sumTotal')) return;
    let occupied = 0, pending = 0, available = 0, tooSoon = 0;
    if (currentVenue && events) {
        available = document.querySelectorAll('.timetable .cell-content.cell-available').length;
        tooSoon   = document.querySelectorAll('.timetable .cell-content.cell-too-soon').length;
        const days = weekData[currentWeek].days;
        const heads = {};                              // last-write-wins = grid slotMap semantics
        events.forEach(function (e) {
            if (days[e.di] && (days[e.di].sunday || days[e.di].holiday)) return;  // non-offday only
            heads[e.di + ':' + e.start] = e;           // iterate weekEvents order — LAST wins
        });
        Object.keys(heads).forEach(function (k) {
            const e = heads[k], span = e.end - e.start + 1;
            if (e.status === 'pending') pending += span; else occupied += span;
        });
    }
    const sunday = document.querySelectorAll('.timetable .cell-content.cell-sun').length;
    const ph     = document.querySelectorAll('.timetable .cell-content.cell-ph').length;
    const unavailable = occupied + sunday + ph + tooSoon;
    document.getElementById('sumTotal').textContent = available + pending + unavailable;
    document.getElementById('sumAvailable').textContent   = available;
    document.getElementById('sumPending').textContent     = pending;
    document.getElementById('sumUnavailable').textContent = unavailable;
}
```

- Purposely no longer DOM-counts `.cell-occupied`/`.cell-pending` (those
  elements no longer exist once blocks merge).
- `sumUnavailable` id & `card-conflict` class kept (unchanged updater contract).
- Guaranteed border case parity with the grid: exact duplicates collapse to
  one head (e.g. B002 Monday `MPU-3232` ×3 → 2 slots counted).

## 6 Tests (`tests/venue-timetable.spec.ts`) — all edits pinned

| Test | Today | Change to |
|---|---|---|
| TC32 (L222) | `/BMIT\d+/` | same structure, regex → `/[A-Z]{2,4}-\d{4}\|[A-Z]{4}\d{4}/` (verified against every mock code: non-MPU = `[A-Z]{4}\d{4}`, MPU = `MPU-\d{4}`) |
| TC34 (L245) | `toHaveCount(4)` | `toHaveCount(4)` — stays 4 (now truthfully matches) |
| TC35 (L250) | Available/Replacement/Pending/Conflict | Available / Your Classes / Others' Classes / Pending (item captions of §4) |
| TC36 (L262) | 5 `.summary-card` | `toHaveCount(4)` |
| TC37 (L267) | ids incl. `sumReplacement`/`sumConflict` | ids `sumTotal`, `sumAvailable`, `sumPending`, `sumUnavailable` |
| TC38 (L275) | `#sumTotal` numeric | unchanged (no edit needed) |
| TC40 (L293) | `#mdlCourse` (nonexistent) | `page.locator('#eventModal .detail-row:has-text("Subject Code")')` → `toContainText(/regex same as TC32/)` |
| TC41 (L301) | `#mdlVenue` (nonexistent) | `page.locator('#eventModal .detail-row', { hasText: /\(\d+ seats\)/ })` → `toContainText(/[A-Z]\d{3}/)` (**apply-time unfreeze round 2, 2026-10-07**: TC41's real baseline failure cause = locator ambiguity — `:has-text("Venue")` also matches the Status Description row whose value contains the word "venue" (blade L939); the page's own `openModal` (blade L924) renders the Venue row as `CODE — Type (N seats)` so the original format premise was correct; row filter `hasText: /\(\d+ seats\)/` matches only the Venue row; user-approved) |
| TC58 (L461) | mobile 5 cards | 4 |
| TC39/TC42–44 | modal open/close via `.event-block` | unchanged — become live as-is |
| TC33 | `.cell-available` empty | unchanged (available cells untouched) |

Baseline is recorded by §8.1's run (do not pre-guess the exact stale set in
this doc — a baseline paragraph that lists it goes stale the moment one test
is patched; the run output is the record).

## 7 Cross-cutting house rules

- **Promoted to shared: (none)** — the event-block builder is duplicated a
  2nd time (cohort default path = 1st). Promote-on-3rd-rule: wait; recorded
  above and in review-log as future extraction candidate
  (`createEventBlock()` into `ui-common.js` if a 3rd occurs).
- **Toast/undo bar: (none)** — no actions added; existing toast untouched.
- **Mobile view**: no markup changes; restored summary cards flow through the
  shared `.summary-bar` mobile CSS (2-col wrap). Grid remains the desktop
  surface; mobile booking surface (card list) untouched.
- **Colors**: token-only; no new tokens; §10.0 canonical pairs; the one
  accepted overlap is the Q1 trade-off (documented proposal + legend tips).
- **Accessibility**: blocks get `tabindex`, `role="button"`, `aria-label`;
  keyboard parity with cohort (the default builder grants `tabindex`
  (`ui-common.js:745`) but no `role`/`aria-label` — the venue sketch is
  stricter than cohort here, which is fine).

## 8 Apply & verify sequence

1. `npx playwright test tests/venue-timetable.spec.ts` — record baseline
   failures (expect TC34/TC35/TC36 + TC37/TC58-ish; report exact).
2. Edit blade (§2/§3/§4/§5) + spec (§6).
3. `pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &`
4. Playwright manual sweep on `/venue-timetable-ui`: span-4 block on B110
   Monday (`AMCS2093` = mine, blue), others green; **pending-parity states —
   concrete, reachable set (all grey `event-others-pending`; every pending
   flag's lecturer ≠ currentUser, so `event-mine-pending`/tertiary has NO
   reachable mock state on this page — see §9)**: week 10 B002 (default
   venue!) Mon 12–13 `MPU-3232` (rsd3s1g3); week 1 B110 **Tue** 2–5 `AMIS1012`
   (dsf2s1 — wins last-write over the normal dft2s1 twin at the same
   slot, which the sweep should confirm as one grey block, not two);
   week 4 B006 Tue 11–12 `AMCS1013`; week 8 B015 **Wed** 6–9 `BMIT2073`;
   click block → modal (subject code + venue row `CODE — Type (N seats)` —
   the page's own `openModal` at blade L924; see the TC41 round-2 unfreeze
   note in §6);
   arrows+Enter across blocks AND available cells; Book tooltip/B shortcut
   on available cells; too-soon/cell-no-fit unchanged; mobile card list
   intact (≤768px); summary numbers spot-check on the **default view**
   (B002, week idx 2 — mockNow = Mon 05 Oct 2026): the single deduped
   `MPU-3232` head contributes **exactly 2** to Unavailable; the rest of
   that week's Unavailable = 22 `cell-sun` + 64 `cell-too-soon`
   (Mon/Tue/Wed inside the 3-working-day window; PH = 0) → expected cards:
   **Total 154 = 7×22, Available 66, Pending 0, Unavailable 88**. In week 10
   the same B002 head is pending (grey block) → its 2 half-hours land in
   **sumPending**, not Unavailable.
5. `/cohort-timetable-ui` sanity (untouched shared files — quick).
6. `composer run lint:check` && `composer run types:check` — no new failures.
7. Changelog postscript in `page-changelogs/venue-timetable-ui-changelog.md`;
   tick tasks; no commit until instructed.

## 9 Known assumptions / risks

- **Pending-parity reachability (review R1 blocker fix)**: `MockData.venueSlots`
  (§2.11) and `myTimetable` (§2.6) **do not feed this page** — the grid derives
  only from `cohortTimetable.events` + `flags` (§2.7, mock-data.js L552–566).
  Reachable pending states (all `event-others-pending`, grey) = week 1 B110
  **Tue** 2–5 `AMIS1012` (dsf2s1), week 4 B006 Tue 11–12 `AMCS1013` (dsf1s1),
  week 7 B110 **Tue** 2–5 `AMIS1012` (dft2s1), week 8 B015 **Wed** 6–9
  `BMIT2073` (rsd3s1g2), week 10 B002 Mon 12–13 `MPU-3232` (rsd3s1g3,
  default venue), week 13 B014 **Wed** 2–5 `BMIT3173` (rsd3s1g1).
  **T7 apply-time correction (2026-10-07, declarative — no code impact):**
  the list above was derived from flags alone and ignored exact-duplicate
  twins later in the events array. Verified live: week 8 B015 `BMIT2073`
  (rsd3s1g2, pending) is shadowed by rsd3s1g3's **normal** twin (same
  slot, later in array) → renders green `event-others` and contributes 0
  to sumPending — grid + counters agree per the §5 last-write-wins rule.
  Live-verified pending set: week 1 B110 Tue (AMIS1012, single grey block
  over its twin), week 4 B006 Tue (AMCS1013), week 10 B002 Mon (MPU-3232,
  sumPending = 2). Weeks 7/13 not separately exercised (same mechanism).
  Consequence, recorded as a known limitation: **`event-mine-pending`
  (tertiary) is unobservable on EVERY page of the mock phase** —
  `/my-timetable-ui` holds currentUser pending rows but renders them via the
  default builder's `event-pending` class (not the mine/others pair), and
  `/cohort-timetable-ui` is `cohortTimetable.events`-driven (never reads
  `myTimetable`) and has no *reachable* mine-pending instance either (the
  L358–365 mine-pending branch there is dead code under the mock data — no
  pending donor equals `currentUser`). The tertiary branch is
  still emitted (both pending branches share the identical `cellRender`
  expression) and its CSS exists (`theme.css:1952`) — its verification is
  satisfied by code-path inspection + CSS presence, not by a reachable mock
  state.
- **Partial-overlap fragility** (carried from R1 🟡): a future event whose
  head lands mid-span of an earlier head could leave a colSpan'd `<td>`
  followed by hidden/broken tds. Mock data today only has exact-duplicate
  overlaps → unobservable; last-write-wins keeps the grid and the counters
  agreeing by construction. Real overlap validation is a Sprint-2 backend
  concern (out of scope for mock phase).
- **Reachability of pending-state parity**: pending variants assume a
  `pending` event exists in the target venue/week mock data (per §2.7 seeds;
  verified during sweep in §8.4).
- The `hours` half-hour grid gives `span-4` = 2h — the user-quoted example.
