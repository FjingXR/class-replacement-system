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

## Implementation & verify — 2026-10-08

Implementation landed via the parallel session in commit `b47221e`
("fix(venue-timetable): conflict red, twin merge, 6-item legend, modal
rows") together with the spec de-stale (`37a2b2f`, 37 → 0 failures);
TC34/TC35 rewritten to the 6-item expectations per frozen design §3.

### Verify (design §4 sweep) — all PASS
1. ✅ Legend 6 items in order (desktop): Available · Your Classes ·
   Others' Classes · Others' Pending · Your Pending · Conflict / Public
   Holiday.
2. ✅ B110 + Week 4 (idx 3): Monday `AMCS2093(L)` renders
   `event-conflict` (red) while `AMIS1012(L)` stays `event-others`
   (green). Note: block tooltip carries the lecturer name; the
   "Lecturer on leave" remark surfaces in the detail modal (design §4.2
   wording adjusted accordingly).
3. ✅ Pending states unchanged (suite green; venue-event-blocks §9 set).
4. ✅ Booking intact: 130 `cell-available` cells, hint logic unchanged.
5. ✅ Mobile legend renders 6 items (375 px).
6. ✅ Changelog postscript written (supersession note included).

Evidence: `.playwright-mcp/venue-legend-parity-b110-wk3-conflict-red.png`,
`.playwright-mcp/venue-legend-parity-mobile-legend.png`;
`baseline-tests.txt` (60 passed / 2 skipped / 0 failed).

### ✅ Verdict
IMPLEMENTED & VERIFIED — change complete; archive when convenient.
