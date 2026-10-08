# Proposal: venue-legend-parity

## Why the Change Is Needed

The venue page's legend diverges from the cohort page's, and the divergence
masks a real §10.0 consistency violation: the venue `cellRender` has no
conflict branch, so a conflicted class (e.g. week 3 B110 Mon `AMCS2093`,
"Lecturer on leave") renders **red `event-conflict` on the cohort page
but as an ordinary green/blue block on the venue page**. Same class, two
meanings for the same color — exactly what the UI design rules forbid.
Additionally the venue legend's single "Pending" item conflates the
Your/Others pending split that the cohort legend (and the grey vs tertiary
block colors on this very page) already express.

User decision: make the venue legend carry the cohort page's exact items,
plus the venue-only `Available` item, and add the missing conflict branch
so the legend is truthful.

## What Changes (user-approved decisions baked in)

1. **Venue legend → 6 items** (venue blade ~L408–417), in order:
   1. `--color-success-container` — **Available** — tip unchanged ("Free
      slot — click to book this venue (Sunday, holiday and lead-time slots
      can't be booked)") — venue-only item, KEPT by user decision (the
      page's core booking affordance).
   2. `--color-primary-container` — **Your Classes** — tip verbatim from
      cohort ("Normal or replacement sessions assigned to you").
   3. `--color-success-container` — **Others' Classes** — tip: cohort's
      ("Normal or replacement sessions by other lecturers") — replaces the
      old venue tip that folded the booking note in (Available now covers
      that).
   4. `--color-surface-variant` — **Others' Pending** — tip verbatim from
      cohort ("Replacement request by other lecturers, awaiting PL
      approval") — the grey blocks this page already renders.
   5. `--color-tertiary-container` — **Your Pending** — tip verbatim from
      cohort ("Your replacement request, awaiting PL approval") — CSS
      already exists (`event-mine-pending`); unobservable in mock data,
      same accepted status as on cohort.
   6. `--color-error-container` — **Conflict / Public Holiday** — tip
      EXACTLY: `Scheduling conflict or public holiday (on venue,
      public-holiday slots show as empty 'PH' cells — not red)` (cohort's
      tip extended with the pinned venue note).
2. **Conflict branch in venue `cellRender`** (head branch, after the
   pending check, before the mine/others fallback):
   `else if (e.status === 'conflict') { div.classList.add('event-conflict'); }`
   — conflicted classes render red regardless of ownership (parity with
   the cohort builder's fallback). CSS already exists (theme.css:1938) —
   no new CSS.
3. **Tests** (`tests/venue-timetable.spec.ts`): TC34 legend count 4 → 6;
   TC35 labels updated to the 6-item set. This DELIBERATELY SUPERSEDES the
   venue-event-blocks frozen expectations (supersession logged in the
   changelog postscript).

## Explicitly Won't Change

- **Branch order in `cellRender`**: sunday/holiday branch stays FIRST (a
  booked class on an offday still shows the offday cell) — pinned decision
  of the archived venue-event-blocks change; re-flipping it is out of
  scope. Consequence (accepted): public-holiday-day classes never render as
  blocks on venue — the "Public Holiday" half of item 6 is anchored by the
  tip, not by red blocks; holiday slots show as transparent "PH" cells.
- Booking affordances: `cell-available`, Book tooltip, `B` shortcut,
  booking hint copy ("green empty slot") — untouched.
- The class-detail modal status-badge bug (`badge badge-normal` >
  "Conflict" on holiday days, ui-common.js:905) — EXPLICITLY OUT OF SCOPE
  (user deferred; separate change; it lives in shared ui-common and affects
  cohort/my/student modals).
- `cell-ph` styling (transparent + PH hover) — untouched.
- Cohort page (its legend was already relabelled this session), My
  Timetable, Student My Timetable — untouched.
- Mock data, theme.css, ui-common.js — untouched.

## Known Trade-off (accepted)

The conflict-red on venue now means ONLY "event.status === 'conflict'" —
public-holiday days still show transparent PH cells, not red. The legend
item's label ("Conflict / Public Holiday") is kept identical to cohort for
cross-page label parity (user decision), with the tip carrying the venue
nuance. Slightly imprecise label on this page; accepted for consistency.

## Constraints

- Token-only colors; no new CSS (`.event-conflict` exists).
- Single file (venue blade) + its spec + changelog.
- No dependencies; frontend mock phase.
- Stale-cache restart after blade edit; sweep verification.
