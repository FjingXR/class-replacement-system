# Design: cancel-class-enhancement

Frozen baseline: `proposal.md` (R2 PASS 2026-10-07). This design pins every
implementer decision. Shared-file edits (ui-common, mock-data comments) +
5 page blades + 1 new test file; one promoted partial.

## 0. Scenario table (normative — the behavior contract)

| # | Scenario | Expected |
|---|---|---|
| S1 | Own class, `status:'normal'`, non-holiday day, end-time future | Cancel Class? button visible in class modal |
| S2 | Others' class (any page) | No cancel button; modal read-only as today |
| S3 | Own class `status:'pending'/'replacement'/'conflict'` | No cancel button |
| S4 | Own class on holiday-day cell | No cancel button (`isConflict` guard) |
| S5 | Own class whose END datetime ≤ now (real clock) | No cancel button |
| S6 | Modal opened before end time, confirm attempted after | Confirm re-checks; shows "This class has already ended" hint, aborts |
| S7 | Confirm with no reason picked | Yes button disabled; inline hint |
| S8 | Reason = Other, detail empty | Yes disabled until detail non-empty |
| S9 | Confirm valid (my-timetable / cohort / venue) | Block vanishes from EVERY grid (twins synced); venue slot bookable; summary cards treat slot free; ledger entry written; modal → success state |
| S10 | Success → "Arrange Replacement Now" | Navigate `/replacement-arrangement?code&date&duration` (cancelled class values); undo toast shows there |
| S11 | Success → "I'll Do It Later" | Stay; page rebuilds; undo toast shows on the same page |
| S12 | Any page load with an un-undone ledger entry | Toast "Class cancelled — arrange replacement when ready" + Undo, 12 s |
| S13 | replacement-home load with entry | Row present with `conflictReason` = reason + "Just cancelled" chip |
| S13b | Replacement arrangement submitted for the cancelled class | Entry retained but `consumed:'arranged'`: state still replayed (block stays gone, row stays listed), chip gone, load-toast stops; undo no longer offered |
| S14 | Undo (from any landing page) | Newest entry removed from ledger; twin statuses restored, conflictedClasses row removed, chip gone, toast "Class restored" |
| S15 | Ledger entry whose match keys no longer resolve (stale) | applyLedger skips silently; entry dropped |
| S16 | Merged-cohort block (e.g. myTimetable AMCS2093 L, cohorts DFT2+DSF2) | BOTH cohortTimetable twin rows (dft2s1, dsf2s1) are cancelled/restored |

## 1. ClassCancellation (new, ui-common.js — the OOP home)

```js
const ClassCancellation = {
    REASONS: ['Medical Leave', 'Annual Leave', 'Official Event',
              'Family Emergency', 'Venue/Facility Issue', 'Other'],
    LEDGER_KEY: 'classCancellationLedger',
    UNDO_TOAST_MS: 12000,

    isCancellable(event, days, weekIndex) { ... },   // S1–S5: own + normal + non-holiday day + end-datetime > new Date()
    validate(reason, detail) { ... },                // returns {ok, hint}; S7–S8
    cancel(event, days, weekIndex) { ... },          // S9: mutate twins + ledger append + reapply; returns entry
    undo(entry) { ... },                             // S14
    applyLedger() { ... },                           // S12/S13/S15: called once per page load (bootstrap)
    activeEntry() { ... },                           // most recent un-removed, un-consumed entry or null (S12)
    endDateTime(event, days, weekIndex) { ... }      // parse days[di].date + hours — see §2 (ISO pin) + §10 (parsing)
};
```

- REASONS deliberately reuse the `conflictedClasses.conflictReason`
  vocabulary ('Medical Leave', 'Annual Leave', 'Official Event' are
  existing values) + new cancellation-specific entries + 'Other'.
- `isCancellable` recomputes the end-time check every call — cheap; the
  confirm path calls it again (S6).

## 2. Ledger (sessionStorage — the persistence story)

- Key `classCancellationLedger`; value: JSON array of entries (newest
  last). Entry shape:
```js
{ id: 'cxl-' + Date.now() + '-' + (++ClassCancellation._seq),   // counter suffix: no same-ms collision
  matchKey: { week, code, di, start, lecturer },   // §3 twin matching
  reason, detail, cancelledAt: ISO,
  row: { ...conflictedClasses row inserted },       // for undo removal
  chip: true }                                      // per-entry; cleared when entry is consumed
```
- **Row/URL date format**: `entry.row.date` and the Arrange-Now URL date
  are ISO `'YYYY-MM-DD'`, derived from `weekIndex`/`di` via the semester
  start-date math (ui-common week builder) — NOT the display string
  `days[di].date` (`'DD Mon YYYY'`), which replacement-home's
  `daysLeft()`/`getWeekNumber()` parsers would reject. Display strings
  (`days[di].date`, 12h times) are used only for modal/preview text.
- `applyLedger()` — **run inside a DOMContentLoaded listener registered by
  ui-common.js itself** (a separate listener; the layout's hook at
  `layouts/ui-template.blade.php:55` is NOT edited). Module scope is too
  early for the TOAST half: ui-common.js loads in the body BEFORE the
  `#toastBar` markup (`ui-template.blade.php:47` vs `:67`) and
  `ToastManager.show()` silently no-ops without it (R2 review 🔴1) — so ledger replay AND the
  load-toast both wait for DOMContentLoaded. For each entry — resolve
  matches in `MockData.myTimetable.eventsByWeek[week]` AND
  `MockData.cohortTimetable.events` (`item.week === week`), set
  `status:'cancelled'` + `cancelledReason`/`cancelledDetail` on each match;
  if `MockData.conflictedClasses` lacks the row id, push `entry.row`;
  drop entries with zero matches (S15). Idempotent by construction
  (status already 'cancelled' → no-op; row id-checked before push).
- NOT localStorage: tab-session scope per proposal (tab close = demo
  reset).

## 3. Twin matching + week equivalence (S16)

- Both datasets are 0-indexed on semester week (myTimetable
  `eventsByWeek` keys; cohortTimetable `item.week` — spot-verified during
  explore; the cancel-class.spec.ts MUST assert a same-key twin cancel
  across both stores).
- Match predicate: `week === entry.matchKey.week && code && di && start`
  all equal AND `lecturer === entry.matchKey.lecturer`. Merged-cohort
  myTimetable blocks intentionally match MULTIPLE cohortTimetable rows
  (S16); a cohortTimetable block matches its own myTimetable twin when
  one exists; unresolvable = valid (own cohortTimetable-only blocks like
  AMCS2093 T — cancel still works, preview shows cohorts only, row
  `totalStudents = 0`).
- `totalStudents` for the row: twin `studentCounts` sum when resolvable,
  else `0`; the row renders cohort labels regardless (design pin from
  proposal R2).

## 4. Grid + counting integration

- `buildTimetableGrid` (ui-common): skip events with
  `status === 'cancelled'` at BOTH assembly points (cellRender path
  dayEvents filter + default path) — one central filter covers cohort,
  my, venue grids. Student-my-timetable pre-filters cancelled itself
  (student blade:115) and uses its own dataset — unaffected.
- `getVenueEvents` (venue blade): filter out cancelled events so
  `updateSummaries` heads/counts treat the slot as free (grid + cards stay
  grid-equivalent — same invariant as venue-event-blocks §5).
- My-timetable `updateSummary` operates on the raw array — filter
  cancelled there too (or `computeSummary` skips cancelled internally).
- **Same-load snapshot pages (R2 fix)**: the cohort page snapshots event
  COPIES once per load (`Object.assign({}, e)` at CohortTimetable ~:121),
  so mutating the canonical stores alone leaves a stale visible block on a
  same-load rebuild ("Later" path). Pin: each page's event-assembly is a
  re-callable function re-run before every rebuild (cohort's `allEvents`
  assembly extracted; my-timetable already shares element references;
  venue re-derives per render) — no page keeps a stale snapshot after
  cancel/undo.
- **Cohort page summary (R1 fix)**: cohort feeds raw week events into
  `computeSummary` (CohortTimetable blade ~:376) — feed the filtered
  array there as well, or `computeSummary` skips `status === 'cancelled'`
  internally; implementer's choice, counts must agree with the grid on
  ALL THREE pages.

## 5. UI wiring (per page)

- **Class modal cancel button**: `openClassModal` (cohort, my-timetable)
  and venue's inline `openModal` (DetailModal.render) gain an OPTIONAL
  trailing action button, rendered by a new shared helper
  `CancelClass.renderButton(container, event, days, weekIndex)` — it
  self-hides when `!ClassCancellation.isCancellable(...)` (S1–S5). Pages
  call it right after their existing modal render; zero changes to other
  modal consumers (additive cfg/button append, non-breaking).
- **Confirm modal — PROMOTED partial** (3rd-duplication rule: 3 pages need
  identical markup): new `resources/views/partials/ui-cancel-class-modal.blade.php`
  (overlay + impact preview node + reason enum + Other textarea + gated
  Yes + success state with the two buttons), included on cohort, venue,
  my-timetable blades. Controller = `CancelClassModal` object (ui-common):
  `open(event, days, weekIndex, context)`, renders impact preview
  (subject, day+date, 12h time range, venue, cohort(s), students-or-cohorts
  only per §3), reason widgets, validation hints, success state.
  - My-timetable's inline `cancelConfirmOverlay` + `cancelClass()` +
    `closeCancelConfirm()` are REMOVED (replaced by the shared modal).
- **Success state**: "✓ Class cancelled" + class summary + two buttons:
  `Arrange Replacement Now` → `goToReplacementWith`-style URL
  (`/replacement-arrangement?code=…&date=…&duration=…`, values from the
  cancelled class; date = ISO 'YYYY-MM-DD' per §2 — NOT the display
  string) — note replacement-home's
  `goToReplacementWith` stays where it is; the arrangement URL scheme is
  replicated in the shared controller (2nd use of the param scheme —
  documented, below promote threshold). `I'll Do It Later` → close modal,
  rebuild grid, show the undo toast via
  `ClassCancellation.showUndoToast()` (S11).
- **replacement-home**: `buildTable()` renders the chip when the row's OWN
  ledger entry exists and is unconsumed (iterate ledger entries — NOT just
  `activeEntry()`, which is newest-only; §6 stack semantics require
  per-entry chips) (small
  `.just-cancelled-chip` style — CSS lives in the replacement-home
  blade's `@section('page-styles')` (single consumer — house rule AGENTS
  #3), tokens only); undo clears it on re-render.
- **replacement-arrangement**: undo-toast bootstrap only (global), PLUS
  one submit hook: when a replacement request is submitted, locate the
  matching ledger entry by row id/matchKey and mark it
  `consumed:'arranged'` — RETAINED, not removed (see §6 lifecycle; S13b:
  chip gone, toast stops; undo no longer offered after arrangement).
  Minimal insertion in the page's existing submit path.

## 6. Undo toast on load (S12) — NO suppression mechanism

Global self-bootstrap (end of ui-common.js, after applyLedger): if
`activeEntry()` exists, `toast.show('Class cancelled — arrange replacement
when ready', undoCallback, 12000)`.

- The toast is shown by ONE shared helper, `ClassCancellation.showUndoToast()`
  (wraps `toast.show(msg, undoCallback, 12000)`):
  - called by the bootstrap on every page load while an un-undone,
    unconsumed entry exists (fresh `ToastManager.show()` per load — the
    single toast bar replaces any prior message; no dedupe flag needed —
    R1 review: a persisted `toastShownFor` flag would contradict S12, an
    in-memory one is dead code);
  - called explicitly by the "I'll Do It Later" path after the grid
    rebuild (no page load happens, so the bootstrap can't fire).
- The cancel path does NOT double-toast: after confirm + rebuild the
  modal's success state carries the message; "Now" navigates (target page
  load → bootstrap toast).

**Entry lifecycle (R2 fix — split semantics)**:
- **Undo → entry REMOVED from the ledger** (S14: newest entry removed;
  next-older entry resumes toast/chip per stack semantics). Removal is
  safe: undo restores the twins directly at click time.
- **Arrangement submit → entry RETAINED but marked `consumed:'arranged'`**
  (S13b): the ledger entry is the ONLY thing replaying the cancelled state
  across page loads — removing it at submit would resurrect the cancelled
  block on every grid and drop the home row mid-arrangement. Consumed
  entries: still replayed by `applyLedger` (twins stay cancelled, row
  stays listed), but skipped by `activeEntry()` (no toast) and render no
  chip. Undo is no longer offered. Submit-match predicate:
  `entry.matchKey.code === submittedSubjectCode` — a submit for a
  different subject leaves every entry untouched (the page allows subject
  switching).

**Multiple sequential cancellations (stack semantics — pinned)**: the
ledger is an array; chips are per-entry (a home row shows its chip iff its
own entry is unconsumed); the toast binds to the NEWEST un-undone entry
(`activeEntry()`); Undo consumes the newest only, after which the next
older entry's toast/chip resume on subsequent loads. Accepted demo
trade-off — no multi-undo UI.

## 7. Promoted to shared (record)

1. `ClassCancellation` + `CancelClassModal` (ui-common) — new shared
   module (3 pages consume immediately).
2. `partials/ui-cancel-class-modal.blade.php` — new partial (3 includes).
3. `buildTimetableGrid` cancelled filter — shared builder change.
(No 3rd duplication created; all promotions justified by immediate
3-consumer use. The chip CSS is deliberately NOT promoted — single
consumer, page-styles only.)

Traceability: the frozen proposal's `_lastCancellation` name is
materialized as `activeEntry()` (the ledger's newest un-consumed entry
re-applied on load); no separate `_lastCancellation` variable exists.

## 8. Tests (new `tests/cancel-class.spec.ts` + venue spec additions)

- Guard matrix S1–S5 via modal-button visibility on cohort/venue/my pages
  (own vs others block; pending block; end-time check with a class from
  week 1 — real clock makes it non-cancellable).
- S7/S8 gating: Yes disabled until reason; Other detail required.
- S9 happy path (my-timetable): cancel → block gone, summary updates.
- S16 twin sync: cancel merged-cohort block on cohort page → replacement-
  home lists row (visit page), chip present.
- S13/S14: home row + undo restores (undo via toast click) → row gone.
- S10: "Arrange Now" URL contains code/date/duration params (ISO date).
- S6: confirm-time recheck — clock-seeded test (Playwright
  `page.clock.install()` before `goto` + `setFixedTime` past the end
  time, or an addInitScript Date stub — implementer picks one, named in
  the test), confirm → abort hint shown.
- S11: "Later" → no navigation; undo toast visible on the same page.
- S12: pre-seed `sessionStorage.classCancellationLedger` before load →
  toast + Undo on load; block gone on the affected page.
- S15: ledger with a garbage/unresolvable entry → no toast, no crash,
  entry dropped.
- S13b: submit an arrangement for the cancelled class → chip + toast gone.
- venue-timetable.spec.ts: add one assertion that cancelling frees the
  slot (cancelled class's cell becomes `.cell-available`) — leverages the
  existing venue spec fixtures.

## 9. Changelogs + docs

Postscripts in: cohort-timetable-ui, my-timetable, venue-timetable-ui,
replacement-home, replacement-arrangement changelogs. `mock-data.js` §2.6/
§2.7/§2.10 header comments gain 2–3 lines documenting `cancelled`
semantics + the ledger key (data itself unchanged at rest).

## 10. Known risks

- Date parsing `days[di].date` ('DD Mon YYYY', English months) — pinned
  format; `endDateTime` builds `new Date(dateStr + ' ' + endTime12h)`
  with the same `to12h` vocabulary used grid-wide; test S5 covers a
  boundary.
- Confirm-guards.spec.ts existing patterns for disabled-confirm buttons —
  reuse its selector style for S7/S8.
- Undo across navigation relies on sessionStorage being present
  (file:// or privacy modes could throw — wrap in try/catch, degrade to
  in-memory-only, same-session undo only).
