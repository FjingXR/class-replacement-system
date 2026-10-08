# Explore Brief: venue-legend-parity

## Problem

The venue page's legend (4 items, set by the archived `venue-event-blocks`
change) diverges from the cohort page's legend, and the divergence hides a
real inconsistency: conflicted classes render on the venue grid as
ordinary green/blue blocks, invisible as conflicts.

User decisions (locked in planning discussion, 2026-10-07):

1. Venue legend = **6 items**: `Available` (venue-only, kept) + the
   cohort page's exact 5 (labels/tips/tokens verbatim): Your Classes,
   Others' Classes, Others' Pending, Your Pending, **Conflict / Public
   Holiday** (label as just relabelled on cohort this session).
2. A **conflict branch** is added to the venue `cellRender` — without it
   the new red legend item would have no anchor (legend would lie).
3. Process: full SDD (user chose).

## Verified facts

- Cohort legend (CohortTimetable blade:74–81): 5 items — primary Your
  Classes / success Others' Classes / surface-variant Others' Pending /
  tertiary Your Pending / **error "Conflict / Public Holiday"** (label
  updated this session; tip "Scheduling conflict or public holiday").
- Venue legend (venue blade:408–417): 4 items — success Available /
  primary Your Classes / success Others' Classes / tertiary Pending (tip
  folds the grey note in).
- Venue `cellRender` head branch (venue blade:772–804): pending →
  `event-mine-pending`/`event-others-pending`; ELSE → `event-mine`/
  `event-others`. **No conflict branch** — a conflicted event
  (`e.status === 'conflict'`) silently renders as an ordinary block.
  Concrete reachable case: week idx 3, B110 Mon — `AMCS2093` (dft2s1,
  remarks "Lecturer on leave", status `conflict`) renders red
  `event-conflict` on cohort (default builder fallback, ui-common ~:757)
  but green/blue on venue.
- `.event-conflict` CSS exists (theme.css:1938, error-container) — no new
  CSS needed for the branch.
- Holiday-day classes are UNREACHABLE on venue (sunday/holiday branch runs
  first → `cell-ph`, pinned decision in archived venue-event-blocks §1:
  "a booked class on an offday still shows the offday cell"). So the
  "Public Holiday" half of the red item has no red anchor on venue —
  holiday slots show as transparent "PH"-hover cells. Branch order stays
  (re-flipping it would decision-level contradict the archived change);
  the item's tip carries the explanation. `cell-ph` is transparent
  (theme.css:2149) — deliberately NOT error-colored.
- `event-mine-pending` (tertiary, "Your Pending") is unobservable in mock
  data on every page (established: no pending donor equals currentUser) —
  cohort lists it anyway; venue listing it is parity, not a lie about
  reachable states.
- Tests: venue spec TC34 (legend 4 items) + TC35 (labels Available/Your
  Classes/Others' Classes/Pending) were set by venue-event-blocks — this
  change DELIBERATELY SUPERSEDES them (count → 6, labels updated).
  Booking-hint copy "Click any green empty slot to book this venue" stays
  valid (Available kept).

## Rejected approaches and WHY

1. **Strict 5-item parity (drop Available)** — rejected by user: the
   venue page's core function is booking; the green bookable cell would
   lose its legend entry (hint alone insufficient — §10.0 rule 2).
2. **Re-coloring conflicted venue blocks without a legend item** — moot;
   user wants the legend item.
3. **Flipping the venue branch order so holiday-day classes render (giving
   the "Public Holiday" half a red anchor)** — rejected: decision-level
   contradiction of the archived venue-event-blocks pinned branch order;
   the PH cell (transparent + hover) is the venue's established holiday
   representation.
4. **Separate legend items "Conflict" and "Public Holiday"** — rejected:
   user pinned the exact cohort wording "Conflict / Public Holiday" (which
   cohort itself just adopted for the same reason).
5. **Bundling the class-detail modal badge bug** (`badge badge-normal` >
   "Conflict" on holiday days, ui-common.js:905 — diagnosed this session,
   user said "don't fix yet") — rejected for THIS change: different file
   (ui-common, shared), affects cohort/my/student modals; separate change.

## Final solution

1. Venue legend include → 6 items (Available first, then cohort's 5
   verbatim — including the reworded Others'/Your Pending split; the old
   single "Pending" item with its folded tip is replaced).
2. Venue `cellRender` head branch gains, AFTER the pending check and
   BEFORE the mine/others fallback:
   `else if (e.status === 'conflict') { div.classList.add('event-conflict'); }`
   (no ownership split — cohort's builder colors conflict red regardless
   of owner; parity).
3. Venue spec TC34/TC35 updated (6 items; new label list).
4. Changelog postscript (venue) + stale-cache restart + sweep (red block
   on B110 week 3; grey/tertiary pending states unchanged per
   venue-event-blocks §9 verified set).

## Known open questions

None blocking. The modal badge bug is explicitly out of scope (separate
change, user-deferred).
