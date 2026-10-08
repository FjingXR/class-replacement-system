# Explore Brief: cancel-class-enhancement

## Problem

"Cancel Class" exists only on My Timetable and is a mock stub: confirm
modal → toast "Class cancelled." → nothing changes (no state, no record, no
undo data, no navigation). The user wants a real, cross-page cancellation
flow with reasons, undo, and hand-off to the replacement-arrangement flow.

## User decisions (grilled, one per branch — ALL LOCKED)

1. **RBAC (Q1)**: own classes only, on every page. Cancel affordance appears
   in the class modal iff `e.lecturer === MockData.currentUser.name` (FR 4.13
   "Lecturer = create/submit/cancel own"). Others' blocks stay read-only.
   Applies to Cohort Timetable + Venue Timetable (new) + My Timetable.
2. **Model (Q2)**: block DISAPPEARS from grids; slot frees up (counts as
   bookable again). Event object is RETAINED with `status:'cancelled'` +
   cancellation metadata (reason, detail, cancelledAt) — filtered from
   rendering, not deleted, so undo can reinsert.
3. **Reason (Q3)**: enum + Other detail, MANDATORY, modeled on the existing
   PL Rejection Reason pattern (request-approval). OOP home: a small
   cancellation class in ui-common (static REASONS + validate + describe).
   Vocabulary should reuse `conflictedClasses.conflictReason` style
   (mock-data §2.10: 'Medical Leave', 'Annual Leave', 'Official Event',
   'Public Holiday') + cancellation-specific entries + 'Other' revealing a
   required free-text detail.
4. **Undo vs redirect (Q4 — AMENDED by user's "arrange now vs later" idea,
   adopted in follow-up discussion)**: after confirm, the confirm modal does
   NOT navigate — its content swaps to a success state offering
   **"Arrange Replacement Now"** (navigates to
   `/replacement-arrangement?code=…&date=…&duration=…` prefilled with the
   cancelled class, via the existing `goToReplacementWith` param scheme) or
   **"I'll Do It Later"** (stays on the timetable). The undo toast is bound
   to the cancellation STATE (MockData `_lastCancellation`, checked on page
   load by any landing page), not to a fixed page: "later" shows it on the
   timetable, "now" shows it on the arrangement page. Supersedes the
   original auto-redirect decision.
5. **Replacement-home list (Q5)**: cancelled class is APPENDED to
   `MockData.conflictedClasses` with `conflictReason` = chosen reason. The
   redirect lands where the class now lives; click row → existing
   `goToReplacementWith()` arrange flow.
6. **Time boundary (Q6 — REFINED by user follow-up, end-time rule)**:
   cancellable ⇔ the class's END date-time is in the future relative to the
   REAL clock (`new Date()`). A 9–11 am class today is cancellable until
   11:00, then it is history. This finer rule SUBSUMES the original
   "current + future weeks" decision (past weeks always fail the check,
   future weeks always pass). Week navigation remains mockNow-anchored —
   the real-clock boundary is cancel-specific; accepted cosmetic mismatch
   while real today sits inside mockNow's current week.
7. **Extras (Q7 — all three IN scope)**:
   - Impact preview in confirm modal (subject, date, time, venue,
     cohort(s), total students affected).
   - Confirm ("Yes, Cancel Class") DISABLED until reason valid (enum picked;
     if Other → detail non-empty). Inline hint on validation failure.
   - "Just cancelled" chip on the newly added replacement-home row (fades on
     undo or next full reload).

## Key facts (verified in code)

- Current stub: `MyTimetable-UI-design-template.blade.php:214–232, 306–317`
  (`cancelClass()`, `closeCancelConfirm(true)` → toast only).
  Existing guard: cancel hidden when `isConflict || event.status ===
  'pending'` — extend this guard to all 3 pages.
- `ToastManager.show(message, undoCallback, duration, linkText, linkUrl,
  details)` (ui-common.js:2360) natively supports undo button + link — the
  redirect toast on replacement-home uses undoCallback; origin-page toast
  (before redirect) is informational only.
- `MockData.conflictedClasses` (§2.10, mock-data.js:662+) row shape:
  `{ id, code, name, type, date: 'YYYY-MM-DD', day, timeStart: 'HH:MM',
  timeEnd, duration, venue, totalStudents, cohorts: ['DFT2 (S1)'], 
  conflictReason }`. replacement-home reads this array and does NOT mutate
  it today (comment says read-only — this change adds the mutation point).
- replacement-home → arrangement: `goToReplacementWith(code, date,
  duration)` builds `/replacement-arrangement?code=…&date=…&duration=…`.
- Week data / holidays: `holidayLabel` population at ui-common.js:69–82;
  week index ↔ date math exists there (reuse for row `date`/`day` fields).
- Statuses today: normal/pending/replacement/approved/rejected/conflict —
  `cancelled` is NEW; `StatusText.label()` needs a 'Cancelled' entry
  (though cancelled blocks never render, the label keeps the vocabulary
  complete).
- Datasets are TWINS: my-timetable renders `MockData.myTimetable` (§2.6);
  cohort + venue render `MockData.cohortTimetable.events` (§2.7). The same
  physical class can exist in BOTH arrays. **Cancellation must key-match
  and update BOTH twins** (match key: week + code + di + start [+ lecturer])
  so the block vanishes everywhere and undo restores everywhere. This is
  the main cross-module data flow.

## Data flows (who calls who)

- Page modal (cohort/venue/my) → shared confirm modal (shared partial or
  promoted helper — design decision) → `ClassCancellation.validate(reason,
  detail)` → mutate MockData (both twin events → 'cancelled'; append
  conflictedClasses row; set `MockData._lastCancellation`) → rebuild grid →
  success state in-modal → user picks: **Arrange Now** → navigate
  `/replacement-arrangement?code=…&date=…&duration=…` (prefilled), or
  **Later** → stay put.
- Whichever page the user lands on (arrangement page after "Now", timetable
  after "Later") checks `MockData._lastCancellation` on load → toast with
  Undo → Undo: restore twin event statuses + remove conflictedClasses row +
  clear `_lastCancellation` + re-render.
- replacement-home also reads `_lastCancellation` → "Just cancelled" chip on
  that row (persists for the session until arranged or undone).
- Venue availability counting (`updateSummaries` style) must treat
  `cancelled` as free (filter before counting) — else cards disagree with
  grid.

## Rejected approaches and WHY

- Cancel-others on cohort/venue (PL override): contradicts FR 4.13; user
  rejected (Q1).
- Cancelled block stays visible on grid: user rejected (Q2) — chose
  disappears; consequence accepted: no on-grid trace, undo is the only
  recovery path within the toast window.
- Persistent "Restore" UI instead of toast undo: user rejected (Q4) —
  heavier scope.
- Free-text-only reason: rejected (Q3) — unstructured vocabulary.
- Cancel → new request entity needing PL approval: rejected — FR 4.13 gives
  lecturers direct cancel-own; no approval step exists in the domain flow.

## Known open questions for /sdd-propose

- Confirm modal: extend My Timetable's inline modal vs promote a shared
  cancellation-confirm partial/class used by all 3 pages (2nd-vs-3rd
  duplication rule — 3 pages now need it ⇒ promotion likely justified).
- Exact REASONS enum list (start from conflictReason vocabulary + 'Other').
- Whether the builder should filter `cancelled` events centrally
  (buildTimetableGrid) or each page filters at event assembly; venue
  `getVenueEvents` must filter regardless (availability counts).
- Where `_lastCancellation` lives in MockData and its exact shape.
- Test plan: which spec(s) cover cancel flow (my-timetable spec exists?
  cohort/venue specs exist) + replacement-home undo assertion.
