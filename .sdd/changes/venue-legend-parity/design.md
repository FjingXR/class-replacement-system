# Design: venue-legend-parity

Frozen baseline: `proposal.md` (R1 PASS). Single blade + spec + changelog;
no CSS, no shared files.

## 1. Legend include (venue blade ~L408–417) — final array, verbatim

```blade
@include('partials.ui-legend-bar', [
    'items' => [
        ['color' => 'var(--color-success-container)', 'label' => 'Available', 'tip' => 'Free slot — click to book this venue (Sunday, holiday and lead-time slots can\'t be booked)'],
        ['color' => 'var(--color-primary-container)', 'label' => 'Your Classes', 'tip' => 'Normal or replacement sessions assigned to you'],
        ['color' => 'var(--color-success-container)', 'label' => 'Others\' Classes', 'tip' => 'Normal or replacement sessions by other lecturers'],
        ['color' => 'var(--color-surface-variant)', 'label' => 'Others\' Pending', 'tip' => 'Replacement request by other lecturers, awaiting PL approval'],
        ['color' => 'var(--color-tertiary-container)', 'label' => 'Your Pending', 'tip' => 'Your replacement request, awaiting PL approval'],
        ['color' => 'var(--color-error-container)', 'label' => 'Conflict / Public Holiday', 'tip' => 'Scheduling conflict or public holiday (on venue, public-holiday slots show as empty \'PH\' cells — not red)'],
    ]
])
```

Items 2–5 are byte-identical to `CohortTimetable-UI-design-template.blade.php:76–79`
(labels + tips + tokens). Item 1 = the previous venue item 1, unchanged.
Item 6 = cohort's item 5 label + the pinned venue tip extension.

## 2. cellRender conflict branch (venue blade head branch)

Insert AFTER the pending check (`if (e.status === 'pending') { … }`),
BEFORE the `else` mine/others fallback:

```js
else if (e.status === 'conflict') {
    div.classList.add('event-conflict');   // §10.0: conflict = red, owner-agnostic (parity with cohort)
}
```

- Owner-agnostic deliberately — cohort's `statusClassFn` colors conflict
  red regardless of owner (cohort blade:364–366).
- **Order note (do not "fix")**: venue checks pending-then-conflict;
  cohort's statusClassFn checks conflict-then-pending. Divergence is
  deliberate and harmless — `e.status` is single-valued, the branches are
  mutually exclusive by data.
- `.event-conflict` is an **unscoped global** rule (theme.css:1938), defined
  after `.event-block` (theme.css:1901), so its background wins the
  specificity tie — no new CSS needed.

## 3. Test edits (tests/venue-timetable.spec.ts)

- **TC34** (~L245): `toHaveCount(4)` → `toHaveCount(6)`.
- **TC35** (~L250): replace the 4 positional `toContainText` assertions
  with ONE exact-text array assertion on TC35's existing locator
  (`.legend-bar .legend-item` — the partial renders labels as plain
  unclassed `<span>`s, so assert array text on the item locator itself),
  covering all 6 labels in order:
  Available, Your Classes, Others' Classes, Others' Pending, Your Pending,
  Conflict / Public Holiday. Exact-text (not substring) so item-position
  regressions fail. (Reviewer suggestion adopted.)
- No other test asserts legend tips/count (verified R1: zero tip
  assertions repo-wide).

## 4. Sweep (verify checklist)

1. Default view legend = 6 items in order.
2. Week idx 3 + venue B110: Monday `AMCS2093` block is RED
   (`event-conflict`), tooltip remark "Lecturer on leave".
3. Pending set unchanged (grey `event-others-pending` on the
   venue-event-blocks §9 verified weeks; tertiary unobservable).
4. Booking: green cells + hint intact; Available legend item present.
5. Mobile legend renders 6 items (shared partial handles wrap).
6. Changelog postscript notes the supersession of venue-event-blocks'
   TC34/TC35 expectations.
