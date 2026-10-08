# Proposal: cancel-class-enhancement

## Why the Change Is Needed

"Cancel Class" exists only on My Timetable and is a mock stub: confirm
modal → toast "Class cancelled." → nothing changes. No state mutation, no
reason captured, no undo, no hand-off to the replacement flow, and no
cancel affordance anywhere else. The user decisions (grilled, all locked —
see explore-brief.md) turn this into a real cross-page cancellation flow
that connects naturally to the replacement-arrangement pipeline.

## What Changes (user-approved decisions baked in)

1. **Cancel affordance on 3 pages — own classes only.** Cohort Timetable,
   Venue Timetable and My Timetable all show `Cancel Class?` in the class
   modal **iff** `e.lecturer === MockData.currentUser.name` (FR 4.13:
   lecturers cancel own). Others' blocks stay read-only. Existing guard
   (hidden for `isConflict || status === 'pending'`) is preserved.
2. **Real cancellation state + session-persistent ledger.** On confirm:
   event status → `'cancelled'` (block vanishes from ALL grids — twin
   datasets myTimetable §2.6 AND cohortTimetable.events §2.7 must both be
   updated via key-match; week-number equivalence of the two datasets is
   pinned in design), the slot becomes bookable again (availability counts
   treat cancelled as free), and the event object is retained (not
   deleted) with cancellation metadata for undo (the ledger entry IS the
   state; `_lastCancellation` in MockData is a re-application of it on
   page load, not a second store).
   **Persistence (review R1 fix — decision):** all in-app navigation is a
   full page load and MockData re-seeds each load, so the cancellation
   state lives in a **sessionStorage-backed cancellation ledger**: every
   page load re-applies the ledger to MockData (re-marks twin events
   cancelled, re-appends conflictedClasses rows, restores
   `_lastCancellation`). Survives navigation AND refresh within the
   browser tab session; cleared on tab close (mock phase — no DB).
   Undo removes the ledger entry and re-applies.
3. **Mandatory reason, OOP shape.** Enumerated reasons + required free-text
   detail when `Other` is picked, modeled on the PL Rejection Reason
   pattern. A small `ClassCancellation` class in ui-common owns the REASONS
   list, `validate()`, and row-mapping — one source reused by all pages.
   Vocabulary aligned with `conflictedClasses.conflictReason`
   (mock-data §2.10). **Cancellable events are `status === 'normal'` only**
   (review R1 pin): `pending` (existing guard), `replacement` (already
   arranged — its own flow owns changes), `conflict`, and holiday-day
   blocks are never cancellable.
4. **Immediate-vs-later choice (supersedes auto-redirect).** After confirm,
   the modal swaps to a success state: **"Arrange Replacement Now"**
   (navigates to `/replacement-arrangement?code=…&date=…&duration=…`
   prefilled with the cancelled class) or **"I'll Do It Later"** (stays on
   the timetable).
5. **Cancelled class joins the requires-replacement list.** A row is
   appended to `MockData.conflictedClasses` with `conflictReason` = chosen
   reason, so replacement-home lists it; click → existing
   `goToReplacementWith()` arrange flow.
6. **Undo follows the cancellation state, not a page.** The ledger
   (`_lastCancellation`) is checked on page load; the landing page
   (timetable after "Later", arrangement page after "Now",
   replacement-home on a later visit) shows the toast with an **Undo**
   button at an EXTENDED duration (≥ 10 s — destructive action; exact value
   pinned in design). Undo: restore twin event statuses, remove the
   conflictedClasses row, remove the ledger entry, re-render.
7. **Time boundary — end-time rule.** Cancellable ⇔ class END date-time >
   real clock (`new Date()`). A 9–11 am class today is cancellable until
   11:00. Subsumes week-granularity rules; the check re-runs at confirm
   time (guards a modal left open past expiry).8. **UX extras (user-selected).** Impact preview in the confirm modal
   (subject, date, time, venue, cohort(s), students affected — student
   count comes from the myTimetable twin when resolvable, else the preview
   shows cohort(s) only; the conflictedClasses row's `totalStudents` = twin
   `studentCounts` sum when resolvable, else `0` with the row's cohort
   labels still shown — design pins the rendering); confirm button gated on valid reason (inline hint when invalid); "Just
   cancelled" chip on the newly-added replacement-home row (session-scoped
   via the ledger, removed on undo).

## Explicitly Won't Change

- Others' classes: no cancel, no cancel-request entity (Q1 rejected).
- No PL approval step for cancellations (FR 4.13 = direct cancel-own).
- **Student My Timetable is a third, separate dataset** (`studentTimetable`
  §2.8 with its own `cancelledFlags` demo) — a lecturer cancellation does
  NOT propagate there (accepted demo inconsistency; that page already
  demonstrates cancelled filtering via its own flags).
- `cancelled` is **not a new status** (review R1 correction): it already
  exists as a filter value in student-my-timetable and is accepted by
  replacement-arrangement's `extractSlotsForSubject` slot-picker — the
  latter is load-bearing for this flow (see Impact Scope).
- Grid holiday/offday cells, `replacement-home` conflict-reason badge
  mapping, Sunday/OFF/Today badges — untouched.
- `MockData.holidays`, venue dropdown, booking flows — untouched.
- No backend/migrations — frontend mock phase; state is sessionStorage +
  in-memory, tab-session-scoped (no DB).
- No student-facing notification surface in this change.

## Known Trade-off (accepted)

A cancelled class leaves no on-grid trace on LECTURER-facing timetable
pages (user decision: block disappears). The recovery path is Undo from
any landing page's toast while the tab session lives; after tab close, the
cancellation is permanent until the demo is re-seeded. Student-facing
grids keep their own demo dataset (see Won't-Change) so students still see
the class — an accepted inconsistency of the mock phase.

## Impact Scope

- Shared: `public/js/ui-common.js` (ClassCancellation class, cancellation
  ledger + on-load re-application, undo-toast bootstrapping), possibly a
  promoted confirm-modal partial (3 pages need the same modal ⇒ promotion
  likely justified — design decides).
- Pages: `MyTimetable-UI-design-template.blade.php` (rework stub),
  `CohortTimetable-UI-design-template.blade.php`,
  `venue-timetable-UI-design-template.blade.php` (cancel wiring),
  `replacement-home-UI-design-template.blade.php` (undo toast + chip +
  cancelled-row rendering),
  `replacement-arrangement-UIdesign-template.blade.php` (undo-toast
  bootstrap after "Arrange Now"; NOTE its slot-picker
  `extractSlotsForSubject` already treats cancelled originals as
  extractable — behavior verified, wired through, not redesigned).
- Data: `public/js/mock-data.js` (§ comments documenting the `cancelled`
  status semantics + ledger key; datasets stay as-is at rest).
- Tests: NEW dedicated `tests/cancel-class.spec.ts` (flow spans pages; no
  my-timetable/cohort spec exists — actual specs today are
  venue-timetable, confirm-guards, ui-regression) + small additions where
  venue-timetable.spec.ts already covers shared surfaces.
- Changelogs: postscripts in the 5 affected pages' `page-changelogs/`
  (cohort, my-timetable, venue, replacement-home,
  replacement-arrangement; student-my-timetable is untouched).

## Constraints

- Theme tokens only; reuse ToastManager (undo), DetailModal, StatusText.
- Promote-on-3rd rule honored: 3 pages need the cancel modal ⇒ promote
  shared markup/helpers, record in design.md "Promoted to shared".
- No new dependencies.
