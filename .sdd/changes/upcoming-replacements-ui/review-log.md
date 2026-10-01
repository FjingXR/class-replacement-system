# Review Log — upcoming-replacements-ui

## proposal.md Round 1 — 2026-10-01
### 🔴 Fixed
 - Success criterion 2 misstated `rsd3g2Flags` (claimed 6 flagged weeks; actually 9 flagged weeks / 18 entries) and demanded a coverage level the approved data plan could never satisfy. **User decision (asked per standing directive): full parity** — +15 rows, 18 total, list→grid direction criterion; residual-gap option rejected. Amendment recorded in explore-brief §4.6. (Reviewer's option-b escalation path taken.)
 - Plan §4's `openClassModal` snippet omits `start`/`end` in the synthetic event → `to12h(hours[event.start])` (ui-common.js:572) would throw on first card click. Pinned in proposal §2: approved → `newStart/newEnd` + `dayIndex: newDi`; pending → original `start/end` + `dayIndex: di`. Factual correction to the plan, not a scope deviation. *(Count correction, round 2: 7 new replacement rows need invented slots, not 8 — the 2 pre-existing replacement rows already carry hand-set new slots.)*
### 🟡 Addressed
 - §4 color claim ("matches table A exactly") was false vs plan §3/theme.css:665. Restated: reuse existing `.badge-replacement`/`.badge-pending` unchanged; `--color-warning` authoritative for this page; §10.0-table-A wording drift flagged to user as separate repo-level cleanup.
 - wk11 new-slot invention rule (brief §4.6) carried into proposal §2, generalized to all new replacement-status rows. *(Count corrected in round 2: 7, not 8.)*
 - 0-based/1-based week indexing trap pinned as a binding note above the success criteria (`selectValue - 1`).
### 💡 Addressed
 - Inert `#copyToast` div: decision recorded — remove during build (no copy affordance on this page; plan inventory is a menu, not a mandate); changelog will log it.
### 🔴 Outstanding
 - (none)

## proposal.md Round 2 — 2026-10-01
### 🔴 Fixed
 - "8 replacement-status rows" miscount contradicted its own enumeration (wk1×2 + wk3 + wk5 + wk7 + wk11×2 = **7**; the 2 pre-existing replacement rows already carry hand-set new slots). Corrected in proposal §2 + §5, explore-brief §4.6 amendment, and the round-1 log entry above.
### 🟡 Addressed
 - Stale decision-2 bullets in brief §4.6 marked "(superseded — see amendment above)".
 - Pending-row `requestedAt` asymmetry vs the grid (grid fabricates requestedAt/requestedBy; list contract sends '') — deferred to design.md as an explicit accept-or-populate decision (populate source identified: `rsd3g2Flags[w]` tuple **index 3** — the datetime string; index 4 is the requestId, not a timestamp).
### 💡 Carried to design.md
 - Plan §4's `weekDays()` is pseudocode — pin `generateWeekData()[w].days` as the real days source.
 - Pass an explicit student-context `description` override to `ui-summary-bar`'s `card-pending` (default text is staff-flavored).
### 🔴 Outstanding
 - (none)

## proposal.md Round 3 — 2026-10-01
### Verdict
 - **PASS.** Count fix verified correct and consistent across all four locations (18 flag entries / +15 rows / 7 new replacement rows). No regressions in operative content.
### 🟡 Addressed (soft-fixes in support files, applied post-pass)
 - Brief §4.1 + §5 copyToast rows marked superseded (they contradicted the proposal's removal decision).
 - Review-log round-2 "tuple element 4" corrected to "tuple index 3" (index 4 = requestId, not a timestamp).
 - Round-1 line-9 "all 8" count residual corrected to 7.
### 🔴 Outstanding
 - (none)

**✅ proposal.md FROZEN after Round 3 (PASS) — 2026-10-01.**

## design.md Round 1 — 2026-10-01
### 🔴 Fixed
 - wk1 BMIT7072 invented new slot (di3 4–7) collided with the **pre-existing** wk1 BMIT7074 hand-set new slot (di3 4–7, mock-data.js:663) — same cohort-hour claimed twice in week 1. Re-authored to **di3 8–11 (Thu 12:00–14:00), B101** (collision-free vs base rows 8080 di3 0–3 / 7075 di3 12–15, vs 7074's di3 4–7, and vs wk1's other invented slot di0 12–15). §5 authoring-rule wording extended: collision set now explicitly includes pre-existing rows' hand-set new slots.
 - design.md §4 pseudocode dropped AD-6's fallback (`flagsTuple[3] || ''`) — synced to `flagsTuple[3] || '01 Sep 2026, 09:15 AM'` (the grid's exact fabricated default, student-my-timetable:102), incl. the default-load week-9 BMIT7075 row.
### 🟡 Addressed
 - Holiday-day pending rows (wk2 BMIT7075 di3, wk4 BMIT5678 di2) render badge 'Conflict' inside the modal (shared remap, ui-common.js:569) — documented as expected in §4 + §11; criterion 4 evaluated on the card.
 - requestId (🟡4): **plan-governed decision** — plan §4's snippet omits requestId, so the list modal omits it too (AD-12); asymmetry vs grid modals recorded as accepted. The frozen proposal's imprecise rationale corrected via declarative soft-freeze annotation on proposal.md (allowed for frozen artifacts).
 - theme.css refinement (🟡5): `.badge-past` → theme.css per §10.0 rule 2 mandate (AD-7, verified lawful — `.status-cancelled` uses the same `--color-surface-variant`/`-on-` pair); soft-freeze annotation added to proposal impact row.
 - `renderUpcoming`/`openReplacementModal`/today handler pinned as script-top-level globals (inline-handler scope); only initial render inside DOMContentLoaded (🟡6).
 - 💡 items: `.upcoming-card.past` fork resolved to border treatment (no opacity token exists); row ids 4–18 pinned; B005-lab/T-class pre-existing anomaly + remarks-phrasing difference documented in §5.
### 🔴 Outstanding
 - (none)

## design.md Round 2 — 2026-10-01
### Verdict
 - **PASS.** Both round-1 blockers verified fixed, no regressions; all §5 triples/collision sets, AD citations, and the two soft-freeze annotations verified against source.
### 🟡 Addressed (post-pass, before freeze)
 - Reviewer condition: initial week selection pinned in design §3 as BINDING for criterion 1 — `populateWeekSelect('weekFilter', { selected: currentWeekIndex() + 1 })` during init (the partial renders an empty select; without `cfg.selected` the page would silently load on "Week 1").
 - Two stale line citations corrected: AD-1 → ui-common.js:1800–1814; AD-6 → student-my-timetable:101–102.
### 🔴 Outstanding
 - (none)

**✅ design.md FROZEN after Round 2 (PASS) — 2026-10-01.**

## tasks.md Round 1 — 2026-10-01
### Verdict
 - **PASS.** All 12 ADs + §5/§6/§3/§4/§8/§9/§11 requirements map to tasks with no contradictions; numeric/API claims spot-checked against the repo and accurate.
### 🟡 Addressed (current-batch edits)
 - C1 amended: preserve the stub's `#semesterChip`/`#notifBadge` init (page-scripts-scoped; loss would be a silent regression no criterion caught).
 - A1 split into A1a (8 pending rows) + A1b (7 replacement rows) to respect the ≤2h ceiling.
 - C3 remarks source disambiguated ("row's `requestedAt`").
### 🔴 Outstanding
 - (none)

**✅ tasks.md FROZEN after Round 1 (PASS) — 2026-10-01.**

## Implementation deviations — 2026-10-01
- **Row 3 originalTime fix (user-approved):** existing wk7 BMIT5678 row displayed `'09:00 – 12:00'`, but its own `di/start/end` (2, 2–5) + the grid's `hours[end+1]` formula compute `'09:00 – 11:00'`; the modal recomputes from start/end, so the card would contradict its own modal. User chose "fix row 3 too" over byte-identical preservation. New rows all use the formula (dataset style = raw `hours[]` strings joined with ' – ', not `to12h` output — design §5's "to12h" wording was imprecise; values verified equivalent).
- **Count note:** 18 rows = 9 replacement + 9 pending (existing rows are 2 replacement + 1 pending; tasks.md's "18 10 8" expectation was a miscount, corrected during A1b verification).

## Implementation gaps fixed during D1 verification — 2026-10-01
- **Missing modal shell:** B1 omitted `@include('partials.ui-class-detail-modal')` (design §1 layer 1 listed it, §3 component map didn't) — `openClassModal` crashed on `DetailModal.render` (null `#classModal`). Added the include after the empty-state include (house pattern: student-my-timetable:68).
- **Missing close helpers:** `closeModal`/`closeModalOutside` are page-side per house pattern (modal partial's `onclick`/`$overlayOnclick` reference them; ui-common.js does not define them) — design/tasks never mentioned them. Added the 3-function block + `closeOnEsc(closeModal)` verbatim from student-my-timetable:143–150.
- Both caught by D1 (modal crash + unclosable modal), fixed, and re-verified: all 9 proposal §6 criteria pass, 0 console errors.

## Post-apply user revision — 2026-10-01
- User-directed changes (direct instruction, overrides frozen design §3/§6): desktop = **table** (`ui-grid-table` house pattern) with cards mobile-only; summary bar below the list; Today button pinned right of the week nav (page-scoped `.toolbar { justify-content: flex-start }` — no shared-file edits). All re-verified in-browser (desktop table + mobile cards + modal wiring + empty-state hiding + 0 console errors); recorded in the changelog. Frozen artifacts amended only by this log note — a future re-freeze would fold §3/§6 to match.





