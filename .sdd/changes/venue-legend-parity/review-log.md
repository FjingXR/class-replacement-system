## proposal Round 1 — 2026-10-07

Reviewed by sdd-reviewer.

### 🔴 Fixed
- (None)

### 🟡 Addressed
- Exemplar remark corrected: week-3 B110 AMCS2093 conflict remark is
  "Lecturer on leave" (not "Lab equipment failure") — fixed in proposal +
  explore-brief (would have misled the verify sweep).
- Item 6 tip pinned byte-exact: "Scheduling conflict or public holiday
  (on venue, public-holiday slots show as empty 'PH' cells — not red)".
- Reviewer notes adopted for design: order-divergence note (pending-then-
  conflict on venue vs conflict-then-pending in cohort statusClassFn —
  deliberate, status-exclusive); TC35 suggestion to switch to exact-text
  array assertion.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — proposal.md FROZEN.

## design + tasks Round 1 — 2026-10-07

Reviewed by sdd-reviewer (batched — single blade + spec scope; reviewer
concurred with batching).

### 🔴 Fixed
- (None)

### 🟡 Addressed
- design.md §3: deleted the leftover crossed-out draft locator — the
  partial renders labels as plain unclassed `<span>`s (no `.legend-label`),
  so TC35 asserts array text on the existing `.legend-bar .legend-item`
  locator itself; instruction now executable as written.
- design.md §2: corrected the CSS claim — `.event-conflict` is an
  UNSCOPED global rule (theme.css:1938), defined after `.event-block`
  (1901); the earlier "scoped `.timetable .event-conflict`" statement was
  false (verified by reviewer against theme.css).
- tasks.md T3: appended `composer run lint:check` +
  `composer run types:check` (no-new-failures form) per the AGENTS.md
  standing rule for PHP-touched edits.
- Reviewer's optional suggestions adopted design-wise: name the venue
  blade file explicitly in §1/§2 references (unambiguous from context);
  note the `event-block` text-color interplay is resolved by the
  1901 < 1938 file-order win.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — design.md + tasks.md FROZEN. All artifacts frozen; ready for
/sdd-apply.
