# Explore Brief — venue-event-blocks

Captures the explore/planning conversation (2026-10-06) that crystallized into this change.
Serves as the completeness checklist for proposal review.

## Problem, restated

`/venue-timetable-ui` opts into `buildTimetableGrid({ cellRender })` (the selection-cell
model it shares with replacement-arrangement) and therefore renders every booked
half-slot as a bare red `cell-content cell-occupied` div. Cohort / My Timetable /
Student My Timetable use the builder's DEFAULT path and render the same class as a
merged, labelled `event-block span-N` with mine/others status colors. The venue page
should read like the other timetables for booked state, while keeping its unique
booking affordances (green available cells, Book tooltip, lead-time blackout).

## Final solution — mapping tables (ALL dimensions)

### Event rendering (occupied state), venue cellRender head cell

| Dim | Value | Source of parity |
|---|---|---|
| container | `div.event-block span-{N}`, `td.colSpan = span` | ui-common.js:744 default path |
| continuation half-hours | `td.style.display = 'none'` | ui-common.js:787 default path |
| status → class | `pending` → `event-mine-pending` (mine) / `event-others-pending` (others); else `event-mine` / `event-others` | CohortTimetable blade `statusClassFn` (358–369) + theme.css 1940–1955 |
| ownership test | `e.lecturer === MockData.currentUser.name` (currentUser = "En. Lim Jia Zheng") | mock-data.js:80 |
| block content | `ev-code` = `CODE(type)`, `ev-venue` = `e.venue`, `ev-time` = 12h start–end via `to12h`, + `buildReplacementNote(e, {checkOwnership})` | ui-common.js:774–778 |
| hover tooltip | `data-name` = `e.name`, `data-venue` = `e.venue`, `data-tip2` = `e.lecturer` → theme.css `.event-block::after` shows `name · lecturer` | theme.css:2446 |
| interaction | click → `openModal(e, di)`; `tabindex="0"`; stash `_evt`/`_di` for keyboard Enter | venue openModal (924) |
| span semantics | `span = e.end − e.start + 1` half-hour cells (2h class = span-4), identical to cohort | slotMap build (ui-common.js:689–697) |

### Untouched cell branches (booking journey preserved)

| Branch | Class | Keeps |
|---|---|---|
| Sunday / public holiday | `cell-ph` / `cell-sun` | branch order stays venue's `sunday||holiday` FIRST — booked class on a holiday still shows the holiday cell (no event block) |
| head/continuation of booked/pending | `event-block …` | NEW (was `cell-occupied`/`cell-pending`) |
| lead-time blackout | `cell-too-soon` | unchanged |
| bookable | `cell-available` (+ `cell-no-fit`) | unchanged: Book tooltip, B shortcut, keyboard |

### Legend — trimmed 4-item set (user decision Q2)

| Swatch token | Label | Tip |
|---|---|---|
| `--color-success-container` | Available | Free slot — click to book (Sunday, holiday and lead-time slots can't be booked) |
| `--color-primary-container` | Your Classes | Your sessions in this venue, incl. approved replacement |
| `--color-success-container` | Others' Classes | Other lecturers' sessions (also empty green = bookable; Q1: overlap accepted) |
| `--color-tertiary-container` | Pending | Replacement request awaiting PL approval (others' pending shows grey `--color-surface-variant`) |

### Summary cards — restored (user request "add appropriate summary cards")

Restore `@include('partials.ui-summary-bar')` (previously commented out, L418–433) with the
venue-designed 4-card set; counts become span-weighted (summary dormant-code upgraded):

| Card class | valueId | Count (span-weighted, deduped — see below) |
|---|---|---|
| `card-total` | `sumTotal` | available + pending + unavailable |
| `card-available` | `sumAvailable` | `.cell-available` cell count (unchanged) |
| `card-pending` | `sumPending` | Σ spans of `status === 'pending'` head events |
| `card-conflict` | `sumUnavailable` | Σ spans of non-pending head events + Sunday + PH + too-soon cell counts |

**Overlap-safe dedupe (review R1 blocker fix)**: `cohortTimetable.events`
duplicates shared classes once per cohort at the same venue+slot (e.g. B002
Mon 12–13 appears ×3, across all 14 weeks). The grid's slotMap is
last-write-wins → reduce events to distinct head slots keyed `"<di>:<start>"`
(iterating `weekEvents` in order, LAST wins — grid-equivalent), then Σ spans.
Count only non-offday days (holiday-first branch means Sunday/PH events never
render). Known assumption: mock data has only exact-duplicate overlaps;
last-write-wins also governs exotic partial overlaps (same as the grid).

(kept valueId `sumUnavailable`; updater stays null-guarded, id names unchanged.)

### Keyboard nav

Venue keydown handler (~L1035): active-check accepts `.event-block` too;
roving selector → `.cell-content[tabindex="0"], .event-block[tabindex="0"]`;
Enter on `.event-block` → `openModal(active._evt, active._di)`. B-shortcut unchanged.

### Tests (`tests/venue-timetable.spec.ts`)

Full fix list (stale expectations verified against mock data in review R1):

- TC32, TC39–44 already target `#tableBody .event-block` (currently no-ops behind
  `count > 0` — become live); TC32 additionally fixed: default venue B002 only has
  `MPU-3232` events, so the `/BMIT\d+/` regex → generic course-code regex.
- TC40/TC41 target nonexistent `#mdlCourse`/`#mdlVenue` ids (`DetailModal` renders
  `.detail-row` without ids) → rewritten as text-level assertions on `#eventModal`
  (exact selectors pinned in design.md).
- TC34/TC35 legend: stale today (expect 4 items "Available/Replacement/Pending/Conflict",
  page shows 3 "Available/Pending/Unavailable") → rewrite to the new 4-item set.
- TC36–TC38 summary: TC36 expects 5 cards → 4; TC37 id list → `sumTotal`, `sumAvailable`,
  `sumPending`, `sumUnavailable` (drop `sumReplacement`/`sumConflict`).
- TC58 (mobile): expects 5 summary cards → 4.

## Rejected approaches and WHY

- **(b) Recolor others'-class blocks only on venue** — breaks "same color = same meaning" across pages (§10.0 rule 2); venue would diverge from cohort exactly where we're converging.
- **(c) Recolor available cells (de-green)** — breaks "green = bookable" shared with arrangement legend + the page's own "Click any green slot" hint copy.
- **(d) Extract shared `createEventBlock()` into ui-common.js** — this is only the 2nd occurrence of the event-block builder (cohort default path = 1st); promote-on-3rd rule says wait. Avoids risk to cohort/My-Timetable/Student pages that share the builder; the inline head-cell code stays venue-local.
- **(e) Drop `cellRender` and extend the default builder with venue cells** — inversion risk on shared builder; heavier review chain for a page-local change. Rejected.

## Key data flow

`getVenueEvents(venueCode, week)` → filters `MockData.cohortTimetable.events` by
`item.event.venue === venueCode && item.week === weekIndex`, tags `cohort: item.cohortId`
(venue-timetable blade 698–714). Result: full cohort event shape — `code, type, venue,
lecturer, cohort, status, name, remarks, di, start, end` — so no mock-data change; the
mine/others rule transfers verbatim.

## Known open questions

None — Q1 (green overlap: accept) and Q2 (4-item legend) were answered by the user
during planning; summary cards were explicitly requested by the user (4-card venue set
restored as previously designed, plus needed span-weighted counting).
