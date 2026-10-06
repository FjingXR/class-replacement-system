# Proposal: venue-event-blocks

## Why the Change Is Needed

`/venue-timetable-ui` is the only timetable page that renders booked classes as
one flat red square per half-hour cell (`cell-content cell-occupied`), with no
subject code, no time range, and no mine/others distinction. Every other
timetable page (Cohort Timetable, My Timetable, Student My Timetable) renders
the same kind of session as a merged, labelled `event-block span-N` block with
`event-mine` / `event-others` (and `-pending`) status colors, via the shared
`buildTimetableGrid` default path. The venue page lost that rendering because
it opts into the builder's `cellRender` selection-cell model.

Consequences today:

- A booked 2-hour class shows as 4 identical red squares — visually noisy and
  inconsistent with the other timetable pages (§10.0 rule 1: colors consistent
  across pages).
- The reader cannot tell whose class occupies the venue without clicking each
  red cell (each opens the modal just to read the lecturer).
- Hover shows a bare "Occupied" label instead of the informative event tooltip.

Additionally (user request during planning): the page's **summary cards are
disabled** (`ui-summary-bar` include commented out, L418–433) while the
dormant `updateSummaries()` DOM counting would become stale under this change.
They are restored as part of this change.

## What Changes (user-approved decisions baked in)

Single Blade file, `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`, plus its test spec:

1. **Booked / pending classes render as cohort-style merged event blocks** in
   the venue grid's `cellRender`:
   - head half-hour → `div.event-block span-{N}` + `td.colSpan = span`
     (`span = e.end − e.start + 1`; a 2-hour class = span-4, units are half-hour cells — identical to cohort);
   - continuation half-hours → `td.style.display = 'none'` (the merge);
   - status → class mapping copied verbatim from Cohort Timetable's
     `statusClassFn`: `pending` → `event-mine-pending` / `event-others-pending`,
     else `event-mine` / `event-others`;
     ownership test `e.lecturer === MockData.currentUser.name`
     (same pairing the Cohort legend uses);
   - block content `ev-code` / `ev-venue` / `ev-time` + `buildReplacementNote`
     with the same lecturers-ownership check as cohort;
   - hover tooltip `name · lecturer` (via the shared `.event-block::after`
     `data-name` / `data-tip2` attrs);
   - click → `openModal(e, di)` (booked-class detail modal unchanged).
2. **Keyboard navigation extended**: the venue keydown handler accepts
   `.event-block` as a focusable grid cell (arrow roving + Enter → modal),
   alongside available cells (which keep Enter → Book tooltip, B shortcut).
3. **Legend bar → trimmed 4-item set** (user decision Q2):
   Available, Your Classes, Others' Classes, Pending — using the existing
   container tokens; tips cover rare states (others'-pending greys,
   Sunday/holiday/lead-time blackout).
4. **Summary cards restored** (user request): 4 cards — Total Slots /
   Available / Pending / Unavailable — with span-weighted counting from the
   week's event array (upgrading the dormant DOM-cell counting, which would
   mis-count once blocks merge).
   **Overlap-safe counting rule (decision — not implementer's choice):**
   the slot-grid's `slotMap` is last-write-wins, so the counting must dedupe
   before summing: reduce `weekEvents` to **distinct head slots keyed
   `"<di>:<start>"` (iterate in `weekEvents` order, LAST event wins — exactly
   how the grid resolves duplicates)**, count only events on non-offday days
   (Sunday/PH days never render — the holiday-first branch wins), then
   Σ spans per status. `sumPending` / `unavailable` (occupied spans +
   Sunday + PH + too-soon cell counts) come from that deduped set. Guarantee:
   cards match what the grid renders (the page's own invariant, L894–896).
   Known assumption (recorded in design.md): mock data has only
   exact-duplicate overlaps; last-write-wins semantics follow the grid even
   for exotic partial overlaps.
5. **Tests** `tests/venue-timetable.spec.ts`. Full fix list (stale today,
   verified against mock data):
   - **TC32** `#tableBody .event-block` becomes live and fails today:
     default venue is `venues[0]` = B002 whose only events are `MPU-3232`
     (multiple cohorts share it), so `/BMIT\d+/` never matches → assert with
     a generic course-code regex (`/[A-Z]{2,4}-\d{4}|[A-Z]{4}\d{4}/`
     covering `MPU-3232`-style and `AMCS/BMIT/AMIS`-style codes) — final
     pattern pinned in design.md;
   - **TC34/TC35** legend: today they expect 4 items
     "Available/Replacement/Pending/Conflict", matching no current page state
     → rewrite to the new 4-item set;
   - **TC36/TC37/TC38**: summary cards 5 → 4; id list
     `sumTotal` / `sumAvailable` / `sumPending` / `sumUnavailable`
     (drop `sumReplacement` / `sumConflict`);
   - **TC40/TC41**: target `#mdlCourse` / `#mdlVenue`, which exist NOWHERE in
     the codebase (`DetailModal.render` emits `.detail-row` without ids) —
     TC41's `textContent()` on a missing locator times out → rewritten as
     text-level assertions on `#eventModal` (lane: contains the subject-code
     string / the `CODE — Type (N seats)` venue string); exact selectors
     pinned in design.md (decision: rewrite selectors, no id additions to
     the shared DetailModal);
   - **TC58** (mobile): expects 5 summary cards → 4;
   - TC39/TC42/TC43/TC44 (modal open/close paths) verified coherent — become
     live unchanged.

6. **Booking hint copy corrected** (user decision B — green-overlap
   mitigation): the booking hint (~L397)
   `Click any green slot to book this venue` →
   `Click any green empty slot to book this venue`. The current line becomes
   literally false once green `event-others` blocks exist on the page; this
   is the only page copy changed (one `<span>` text), the svg and
   `.booking-hint` container untouched.

## Explicitly Won't Change

- **Booking affordances**: `cell-available` (with `cell-no-fit`), Book
  tooltip, `B` shortcut, `cell-too-soon` blackout, Sunday/`cell-sun` and
  holiday/`cell-ph` cells — all untouched; branch order stays venue's
  sunday/holiday-first (a booked class on an offday still shows the offday cell).
- **Mobile card list** (`venue-available-card` / booked cards): unchanged.
- **Modal, toast, favourites, venue dropdown, week nav, PPN**: unchanged.
- **Shared files**: `theme.css`, `ui-common.js`, `mock-data.js`, all partials —
  no edits (no promotion: the event-block builder is only being duplicated a
  2nd time; promote-on-3rd-rule says wait).
- Mock data: none added — venue events already derive from
  `MockData.cohortTimetable.events` via `getVenueEvents()`.

## Known Trade-off (accepted, documented)

`--color-success-container` currently means both "bookable cell" and
"others' class block" on the venue page. After this change the same token
also colors `event-others`. Mitigation instead of recolor: blocks are
text-filled rounded rectangles vs empty cells (form differs), legend lists
both meanings with clarifying tips, and the booking hint now says
"green **empty** slot" (item 6). Alternatives were rejected (breaks a
wider convention — see explore-brief). Q1 decision: **accept**.

## Constraints

- Token-only colors (`var(--color-…)`) — no hex/rgb anywhere.
- §10.0 canonical pairs respected; the one accepted overlap is documented above.
- Frontend mock phase: no migrations / models / backend logic.
- No new dependencies.
- Re-verification required: stale-opcache restart after Blade change;
  `composer run lint:check` + `types:check`.
- `page-changelogs/venue-timetable-ui-changelog.md` postscript after apply.
