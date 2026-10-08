# Tasks: cancel-class-enhancement

**Status:** proposal.md frozen (R2 PASS), design.md frozen (R4 PASS); see
review-log.md. specs/ skipped per house precedent — design §0 scenario
table S1–S13b is the behavioral contract.

> Each task ≤ 2 hours. Dependencies noted. Code changes ONLY after this
> file is frozen.

## Preflight

- [x] **T0 — Baseline test run [no deps]**
  `npx playwright test` (full suite) — record pass/fail baseline (expect
  the 16 known pre-existing venue failures from the archived
  venue-event-blocks change: TC05/TC08–11/TC21–25/TC56/TC57/TC60/TC62/
  TC65/TC69). Save to
  `.sdd/changes/cancel-class-enhancement/baseline-tests.txt`. That failing
  set is the authoritative baseline.

## Shared module (ui-common.js)

- [x] **T1a — ClassCancellation predicates [T0]** (design §1, §3)
  REASONS enum (6 values incl. 'Other'), `validate(reason, detail)`
  (S7/S8: reason required; Other ⇒ detail non-empty), `isCancellable`
  (own + status==='normal' + non-holiday day + end-datetime > real clock),
  `endDateTime` (parse days[di].date + end hour — design §2/§10),
  twin-match resolution (week+code+di+start+lecturer; 0-indexed weeks both
  datasets; multi-match for merged cohorts). Pure logic — no DOM, no
  storage.
- [x] **T1b — ClassCancellation mutations + raw ledger helpers [T1a]** (design §1, §2, §3)
  `cancel()` (mutate twins → status:'cancelled' + cancelledReason/
  cancelledDetail; build conflictedClasses row: ISO date via semester date
  math, totalStudents = twin sum or 0; append if id absent; append entry
  to ledger), `undo(entry)` (restore twin statuses, remove row, remove
  entry), `activeEntry()` (newest un-removed un-consumed), `_seq` counter,
  RAW ledger read/append/remove helpers (LEDGER_KEY + try/catch degrade
  per design §10 — the guard wraps EVERY ledger access here; T2 only adds
  replay/bootstrap/toast). No DOM beyond storage.
- [x] **T2 — Ledger replay + bootstrap + undo toast [T1b]** (design §2, §6)
  `applyLedger()` idempotent replay (twins, row push, stale-drop S15)
  consuming the raw helpers; DOMContentLoaded listener registered by
  ui-common itself (layout blade untouched) calling applyLedger +
  showUndoToast() when activeEntry() exists; `showUndoToast()` =
  toast.show('Class cancelled — arrange replacement when ready',
  undo→undo(activeEntry), 12000); entry `consumed:'arranged'` flag
  respected by activeEntry().
- [x] **T3 — Grid + counting integration [T1b]** (design §4)
  SOLE OWNER of all filter/count edits: buildTimetableGrid skip
  `status === 'cancelled'` at BOTH assembly points; venue `getVenueEvents`
  filter; my-timetable updateSummary + cohort computeSummary filtered
  feeds (edits land in the venue/cohort/my blades here — T6/T7 wire UI
  only). Counts must agree with grids on all 3 pages.

## Promoted UI (partial + controller)

- [x] **T4 — ui-cancel-class-modal partial + CancelClassModal [T1b, T2]** (design §5)
  New `resources/views/partials/ui-cancel-class-modal.blade.php` (overlay,
  impact preview node, reason enum widgets, Other textarea, gated Yes,
  success state with "Arrange Replacement Now" / "I'll Do It Later") +
  `CancelClassModal` controller in ui-common: open(event, days, weekIndex),
  impact preview (students from myTimetable twin when resolvable, else
  cohorts only), validation hints, confirm-time isCancellable recheck (S6
  abort hint), success state (Now → `/replacement-arrangement?code&date&
  duration` with ISO date; Later → close + rebuild + showUndoToast()).
  Also `CancelClass.renderButton(container, event, days, weekIndex)` —
  self-hiding append helper for modal footers.
- [x] **T5 — My Timetable wiring [T3, T4]** (design §5)
  Remove inline `cancelConfirmOverlay` + `cancelClass()` +
  `closeCancelConfirm()` stub; include the partial; call renderButton in
  the class-modal flow; grid rebuild after cancel/undo uses the existing
  eventsByWeek references (verify no stale snapshot — design §4).

## Remaining pages

- [x] **T6 — Cohort Timetable wiring [T3, T4]** (design §5)
  Include partial; renderButton in openClassModal flow; extract the
  allEvents assembly into a re-callable function re-run before rebuild
  (same-load snapshot fix). (computeSummary filtering owned by T3.)
- [x] **T7 — Venue Timetable wiring [T3, T4]** (design §5)
  Include partial; renderButton into eventModal footer; rebuild on
  cancel/undo. (getVenueEvents filter owned by T3.)
- [x] **T8 — replacement-home chip [T2]** (design §5)
  `.just-cancelled-chip` CSS in @section('page-styles') (tokens only);
  buildTable renders chip iff the row's OWN ledger entry exists and is
  unconsumed (iterate entries); undo re-render clears it.
- [x] **T9 — replacement-arrangement submit hook [T2]** (design §5, §6)
  In `proceed()` confirm callback: if submitted subject code === an entry's
  matchKey.code, mark that entry `consumed:'arranged'` (RETAINED) — the
  flag must SURVIVE navigation, so re-serialize the ledger through T1b's
  raw guarded helpers (never a bare sessionStorage.setItem in the blade).
  No other change to the page.

## Tests

- [x] **T10 — tests/cancel-class.spec.ts [T1a..T9]** (design §8)
  Scenarios: S1–S5 guard matrix (cohort/venue/my; end-time check via week-1
  class = real-clock past), S7/S8 gating, S9 cancel → block gone +
  summary, S16 twin sync → home row + chip, S13/S14 undo restores, S10
  Arrange-Now URL params (ISO date), S6 clock-seeded recheck
  (page.clock.install or addInitScript Date stub — pick one, name it),
  S11 Later → no navigation + toast, S12 pre-seeded ledger → toast on
  load, S15 garbage entry → no toast/crash, S13b arrangement submit →
  chip+toast gone. Plus one venue-timetable.spec.ts addition: cancelled
  class's cell becomes `.cell-available`.

## Verify

- [x] **T11 — Suite + studio sweep [T10]**
  Full suite → save `.sdd/changes/cancel-class-enhancement/post-tests.txt`
  (compare T0 baseline; pre-existing failures unchanged). Studio sweep
  (stale-cache restart first: `pkill -9 php && rm -f storage/framework/
  views/*.php && php artisan serve --port=8000 &`): walk S1–S13b manually
  on all pages incl. mobile spot-check; screenshots to
  `.playwright-mcp/`.
- [x] **T12 — Docs + lint [T11]**
  `composer run lint:check` + `types:check` (no NEW failures; none
  expected — no PHP touched). Postscripts in 5 changelogs (cohort,
  my-timetable, venue, replacement-home, replacement-arrangement);
  mock-data.js §2.6/§2.7/§2.10 header comments document `cancelled`
  semantics + ledger key. Tick tasks.md; NO commit until user says so.
