# Tasks: venue-legend-parity

**Status:** proposal.md frozen (R1 PASS), design.md frozen (R1 PASS); see
review-log.md.

- [ ] **T0 — Baseline [no deps]**
  `npx playwright test tests/venue-timetable.spec.ts` — confirm the
  current state (expect the 16 known pre-existing failures from the
  macos-ui-refactor staleness set; TC34/TC35 passing at 4 items). Note in
  `.sdd/changes/venue-legend-parity/baseline-tests.txt`.
- [ ] **T1 — Blade edits [T0]** (design §1–§2)
  Venue blade: replace the 4-item legend include with the §1 6-item array
  verbatim; insert the `else if (e.status === 'conflict')` branch in the
  cellRender head branch (design §2). Nothing else.
- [ ] **T2 — Spec updates [T1]** (design §3)
  TC34 → 6; TC35 → 6-label exact-text assertion in the file's existing
  locator style.
- [ ] **T3 — Verify [T2]** (design §4)
  Stale-cache restart (`pkill -9 php && rm -f storage/framework/views/*.php
  && php artisan serve --port=8000 &`); full venue spec (pre-existing
  failures unchanged; TC34/TC35 green); sweep checklist §4 items 1–5 with
  screenshots to `.playwright-mcp/`; changelog postscript (venue — notes
  the supersession); `composer run lint:check` + `composer run types:check`
  (no new failures); tick this file. NO commit until user says so.
