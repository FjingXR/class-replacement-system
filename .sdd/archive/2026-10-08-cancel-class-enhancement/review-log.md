## proposal Round 1 — 2026-10-07

Reviewed by sdd-reviewer (agent recovered this session).

### 🔴 Fixed
- **State-persistence contradiction (decision-level)**: in-app navigation
  = full page load + MockData re-seed ⇒ in-memory mutations (twin
  cancelled statuses, conflictedClasses row, _lastCancellation, chip)
  would evaporate on navigate; flow unimplementable as written. FIXED:
  proposal now authorizes a sessionStorage-backed cancellation ledger
  re-applied to MockData on every page load; survives navigation +
  refresh within the tab session; undo removes the ledger entry.
- replacement-arrangement page missing from Impact Scope (undo-toast
  bootstrap after "Arrange Now" + its extractSlotsForSubject slot-picker
  interplay) — added, with the slot-picker synergy noted.

### 🟡 Addressed
- 'cancelled' is NOT a new status (student-my-timetable:115 filter;
  replacement-arrangement:2548 slot-picker accepts it) — proposal
  corrected; student-my-timetable added to Won't-Change as accepted
  third-dataset boundary.
- Cancellable events pinned to status==='normal' only (replacement/
  conflict/pending/holiday-day excluded).
- Week-number equivalence of twin datasets flagged as design-must-pin.
- Students-affected sourcing pinned (myTimetable twin counts; cohort/
  venue pages show cohort list only in preview when twin unresolvable).
- Test plan reworded to actual inventory: NEW tests/cancel-class.spec.ts
  (+ venue spec additions) — no my-timetable/cohort spec exists.
- Undo toast duration pinned ≥10s (default 5s too short for a
  destructive-action undo on a landing page).

### 🔴 Outstanding
- (None)

### ✅ Verdict
Round 1 FAIL → fixes applied → re-review queued (round 2).
## proposal Round 2 — 2026-10-07

Reviewed by sdd-reviewer.

### 🔴 Fixed
- (None)

### 🟡 Addressed
- totalStudents fallback for unresolvable-twin branch pinned (row gets 0 +
  cohort labels; rendering detail delegated to design).
- Ledger wording tightened: _lastCancellation is a re-application of the
  sessionStorage ledger on page load, not a second store.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — proposal.md FROZEN.
## design Round 1 — 2026-10-07

Reviewed by sdd-reviewer.

### 🔴 Fixed
- §6 toastShownFor suppression removed (incoherent with normative S12:
  persisted flag would kill the recovery path; in-memory one is dead
  code). Pinned instead: ONE shared showUndoToast() helper — bootstrap
  fires it on every page load while an un-undone entry exists; "Later"
  path calls it explicitly (no page load occurs there).

### 🟡 Addressed
- Bootstrap location pinned: self-bootstrap at end of ui-common.js (no
  layout-blade edit); initDataTipTooltips name corrected.
- Cohort-page computeSummary added to §4 filter requirements (was
  missing — S9 would fail there).
- Entry lifecycle pinned: chip per-entry; entry CONSUMED on undo OR on
  replacement-arrangement submit (S13b added to scenario table +
  arrangement submit hook added to §5 — replaces the "no code change"
  claim for that page).
- Stack semantics for multiple sequential cancellations pinned (chips
  per-entry; toast binds to newest; undo consumes newest).
- Ledger row/URL date pinned ISO 'YYYY-MM-DD' via semester date math
  (days[di].date display string rejected by home parsers).
- §8 tests extended with S6/S11/S12/S15/S13b.
- id collision: counter suffix added.

### 🔴 Outstanding
- (None)

### ✅ Verdict
Round 1 FAIL → fixes applied → re-review queued (round 2).
## design Round 2 — 2026-10-07

Reviewed by sdd-reviewer.

### 🔴 Fixed
- Bootstrap timing: module-scope toast would fire before #toastBar exists
  (head-loaded ui-common vs body-end bar; show() no-ops) — S12/S10 toast
  could NEVER appear. Re-pinned: applyLedger + activeEntry/showUndoToast
  run inside a DOMContentLoaded listener registered by ui-common itself
  (layout hook untouched).
- Entry-consumption split: removing the entry on arrangement-submit would
  resurrect the cancelled class on next navigation (ledger = only state
  replay). Now: Undo REMOVES the entry; arrangement submit RETAINS it
  marked consumed:'arranged' (state still replayed; chip gone; no toast;
  undo not offered). S13b + §6 reworded (soft re-freeze, declarative per
  reviewer).

### 🟡 Addressed
- Cohort same-load snapshot staleness pinned (page event-assembly
  re-callable, re-run before rebuild; my-timetable/venue already safe).
- S14 wording: "newest entry removed" (not "ledger cleared") per stack
  semantics.
- Arrangement-submit match predicate pinned (entry.matchKey.code ===
  submitted code; subject switching leaves entries untouched).
- Chip CSS moved to replacement-home @section('page-styles') (single
  consumer; theme.css promotion retracted).
- S6 test clock-seeding mechanism named; _lastCancellation ↔ activeEntry()
  traceability line added.

### 🔴 Outstanding
- (None)

### ✅ Verdict
Round 2 FAIL → fixes applied → re-review queued (round 3).
## design Round 3 — 2026-10-07

Reviewed by sdd-reviewer.

### 🔴 Fixed
- §5 edit seams from R1/R2 fixes (both were one-line patches the reviewer
  pre-cleared): (a) arrangement-submit hook now RETAINS the entry marked
  consumed:'arranged' (the R1 "remove by row/matchKey" phrasing would
  have reintroduced the resurrection bug); (b) Arrange-Now URL date =
  ISO per §2 (was days[di].date display string).

### 🟡 Addressed
- Chip predicate: per-entry (iterate ledger entries; row's own entry
  unconsumed) — was activeEntry()-only, contradicting §6 stack semantics.
- activeEntry() one-liner updated to "un-removed, un-consumed".
- Layout hook citation :60→:55; body-load (not head) rationale corrected.
- (Reviewer pre-approved: "should pass without another full re-review"
  after this targeted patch — full re-review still run per protocol.)

### 🔴 Outstanding
- (None)

### ✅ Verdict
Round 3 FAIL → fixes applied → re-review queued (round 4).
## design Round 4 — 2026-10-07

Reviewed by sdd-reviewer (targeted: the 4 round-3 patch points + seam sweep).

### 🔴 Fixed
- (None)

### 🟡 Addressed
- §1 endDateTime stale "see §3" pointer → "see §2/§10" (cosmetic, bundled).

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — design.md FROZEN (R4). specs/ skipped per house precedent
(single-flow change; the normative scenario table S1–S13b in design §0
serves as the behavioral spec — reviewer accepted this in round 1).
## tasks Round 1 — 2026-10-07

Reviewed by sdd-reviewer.

### 🔴 Fixed
- T1/T2 ledger-write seam circular: T1 split into T1a (pure predicates —
  REASONS/validate/isCancellable/endDateTime/twin-match) and T1b
  (mutations + RAW ledger helpers with the §10 try/catch guard wrapping
  every access); T2 now solely owns applyLedger/bootstrap/showUndoToast.
  No unguarded persistence writes possible.

### 🟡 Addressed
- T4 dependency corrected to [T1b, T2] (uses showUndoToast).
- T3 pinned SOLE owner of all filter/count edits (T5/T6/T7 wire UI only;
  "(may be T3)" ambiguity removed).
- T1 sizing resolved via the T1a/T1b split (reviewer's suggestion).
- T10 dep reworded [T1a..T9].

### 🔴 Outstanding
- (None)

### ✅ Verdict
Round 1 FAIL → fixes applied → re-review queued (round 2).
## tasks Round 2 — 2026-10-07

Reviewed by sdd-reviewer (targeted).

### 🔴 Fixed
- (None)

### 🟡 Addressed
- T9 one-clause fix: consumed-flag write re-serialized through T1b's raw
  guarded ledger helpers (no bare sessionStorage.setItem in the blade).
- (T4/T10 sizing split suggested as optional polish — deferred to apply;
  implementer may split T4/T10 in-flight without artifact unfreeze since
  design pins all behavior already.)

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — tasks.md FROZEN. All 4 batches frozen; ready for /sdd-apply.

## Verify Follow-ups — 2026-10-08

Verification verdict: PASS (sdd-reviewer). Two follow-ups closed
post-verify: S4/S6 tests added (13 passing; real holiday fixture +
setFixedTime clock), screenshot paths confirmed (outer repo root
.playwright-mcp/). Remaining 💡 items recorded in post-tests.txt
(S13b test bypass, synthetic seed date, minor dead code) — non-blocking.
