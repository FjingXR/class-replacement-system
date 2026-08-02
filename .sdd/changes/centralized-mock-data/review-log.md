# Review Log — centralized-mock-data

## Batch 1 (proposal.md) Round 1 — 2026-08-01

### 🔴 Fixed
- Contract conflict with frozen `request-approval` chain over `public/js/mock-data.js`: added "Relationship to the request-approval change" section — merged contract (compatibility globals `mockRequests` + `URGENCY_REFERENCE_DATE` preserved, `MockData.approvalRequests`/`urgencyReferenceDate` alias same values, script tag after ui-common.js, order-robust merge rules); sibling tasks.md Task 5 amended with a guard bullet (unfreeze note in sibling review-log).
- "Only one visual data change" contradicted by standardization rule: proposal now lists the complete set of intentional data corrections (§4) — MyTimetable + CohortTimetable week-baseline shift to 2026-08-31, and all 5 semester chips become dynamic from `MockData.semester` (label preserved, dates derived).
- Replacement-arrangement hardcoded week window: explicit rule added — extracted verbatim as page-specific `MockData.arrangementWeeks`, NOT standardized to semester start (standardization applies only to MyTimetable/CohortTimetable/ReplacementHome).
- Fixed student-count source: now referenced to CodingMAIN.md §8 table + `DatabaseSeeder::STUDENT_COUNTS` (was "fixed student-count table" without a location).
- `modules` registry normalization conflict: registry dropped entirely — module code/name stay per-event display fields, extracted verbatim.

### 🟡 Addressed
- Holidays not covered: `MockData.holidays` section added (declarative `{ week, dayIndex, label }`); MyTimetable keeps current no-holiday behavior (explicit).
- MockData.timetable shape vs source structures: design.md will preserve both access patterns; copy semantics stated (read-only, pages derive local copies).
- Display-name drift (cohort/lecturer/venue free-text vs registries): display-name rule added — no joining.
- Events referencing venues outside Block B: covered by display-name/fidelity rules.
- Data fidelity rule: verbatim extraction, no bug-fixing, quirks documented in design.md.
- Static HTML mock data (toolbar subtitles, selector options): claim scoped to "JS-rendered mock datasets"; residual duplication acknowledged as out of scope.
- No explore-brief: not needed for this refactor; baseline data sources referenced directly.

### 🔴 Outstanding
- (none — pending Round 2 review of the fixed proposal)

## Batch 1 (proposal.md) Round 2 — 2026-08-01

### 🔴 Fixed
- Stale compatibility-global name: merged-contract references to the bare global `mockRequests` renamed to `approvalRequests` in proposal.md (the `requests`/`approvalRequests` section row, the "Relationship to the request-approval change" contract bullet, and the aliasing statement). This matches the frozen sibling chain's rename decision (sibling review-log L268) so the global no longer collides with my-request-history's page-local `const mockRequests` (L442). Remaining `mockRequests` mentions in the proposal correctly refer to that page-local source variable, not the bare global.
- Errata fixed in the sibling unfreeze note (sibling review-log): corrected `mockRequests` → `approvalRequests` in the just-appended Task 5 guard description.

### 🟡 Addressed
- Reviewer optional suggestions deferred to design.md (Batch 2): holidays consumed by CohortTimetable only (arrangement's holiday stays inside `arrangementWeeks`); `lecturers.is_pl` derivation (Role = "Programme Leader" → rows 5425, 5516); dev-date "today" pre-semester clamp behavior note.

### 🔴 Outstanding
- (none — pending Round 3 verification)

## Batch 2 (design.md) Round 1 — 2026-08-01

### 🔴 Fixed
- Chip-rendering format regression: design §4 step 4 previously pointed at the page-local `fmt`/`ui-common formatDate`, but all of those produce space-format dates (`31 Aug 2026`) — contradicting the frozen proposal §4#2 (mandates hyphens `31-Aug-2026`) and the existing static chip. Fixed by precomputing `MockData.semester.chipText` once in mock-data.js via an internal `fmtChip` (hyphen-format); pages now render `MockData.semester.chipText` verbatim. No per-page formatter, no edit to the sibling-owned `ui-common.js`.

### 🟡 Addressed
- Holidays label error in frozen proposal (soft-freeze declarative correction): "Week 3 Wednesday" → "Week 3 Thursday" (source CohortTimetable L641 `d===3` where index 3 = Thursday in `['Mon','Tue','Wed','Thu','Fri','Sat','Sun']`).
- §2.7 RSD3 G2 disambiguation note added: `events[]` contains only non-rsd3g2 addEvent calls; rsd3s1g2 reconstructed only via `rsd3g2Base` + `rsd3g2Flags` (avoids double-add → 14 vs 7 events/week).
- §2.6 shallow-copy rationale note added: `.slice()` suffices because MyTimetable's render path only reassigns week-array slots, never mutates event fields.
- §4 step 3 ReplacementHome note added: it has no `weekData` IIFE — its `computeWeek()`/`weekRangeLabel()` use literal `'2026-08-31' === MockData.semester.startDate`; literals may stay or be standardised.
- §2.8 `id` field added to the my-request-history mockRequests shape enumeration (was overlooked).

### 🔴 Outstanding
- (none — pending Round 2 verification)

## Batch 4 (tasks.md) Round 1 — 2026-08-01

### 🔴 Fixed
- Destructive-overwrite hazard: discovered `public/js/mock-data.js` already exists (534 lines — the frozen sibling `request-approval` chain was partially applied; file currently contains ONLY the bare `const approvalRequests` (L1) + `const URGENCY_REFERENCE_DATE` (L534) globals; layout does NOT load it yet). Task 1's "Create" verb would have deleted the sibling's 20-entry dataset; Task 2's re-add of the `const`s would have thrown `SyntaxError: Identifier already declared`. Fixed by adding a "⚠ Merge procedure vs sibling state" preamble: Task 1 PREPENDS the `window.MockData` block above the existing globals (does NOT recreate); Task 2 APPENDS only the two aliasing lines (`MockData.approvalRequests = approvalRequests;` / `MockData.urgencyReferenceDate = URGENCY_REFERENCE_DATE;`) after the existing `const`s (TDZ-safe). Fresh-create path also documented for the sibling-not-applied case.

### 🟡 Addressed
- Task 8 row-count verifier: "13+ request rows" → "20 request rows identical" (toolbar "Showing 20 of 20"; `mockRequests` has `id` 1–20).
- Task 2 dit2s1/dmc2s1 coverage note: flatten every existing `addEvent()` call verbatim; dit2s1 has none (leave empty); dmc2s1 has weeks 0,2 only (leave week 1 empty) — honours fidelity, prevents "fill-in" mistakes.
- Task 8 week-computation bullet: marked no-op — `weekRanges` is a static precomputed lookup, NOT computation from a semester start; §4 step 3 standardization does not apply here.
- Task 3 smoke rephrased: the "Identifier already declared" hazard only fires on the sibling route (Task 10); the 4 non-sibling routes can't collide. Wording corrected to a factually-correct check.
- Task 6 line ref: `conflictedClasses` L308+ → L311–325 (L308 is `@endsection`).
- Task 5 removal regions rephrased: `facultyData` (L650–670) + the entire events block L673–806 (contains addEvent fn + non-rsd3g2 calls + nested rsd3g2Base/Flags + apply loop) + weekData literals (L625, L807) — no overlap ambiguity.

### 🔴 Outstanding
- (none — pending Round 2 verification)

## Final Freeze — 2026-08-01

### 🔴 Outstanding
- (none — all batches pass)

### Frozen artifacts
- ⛔ `proposal.md` — frozen Batch 1 Round 3 (with one approved soft-freeze: holidays "Wednesday"→"Thursday")
- ⛔ `design.md` — frozen Batch 2 Round 2
- ⛔ `tasks.md` — frozen Batch 4 Round 2
- Batch 3 (specs/) intentionally skipped — data-only refactor, no new behaviours

### Sibling chain amended (unfreeze)
- `request-approval/tasks.md` Task 5 guard bullet + `request-approval/review-log.md` unfreeze note — `approvalRequests`/`URGENCY_REFERENCE_DATE` compatibility globals owned by sibling; `window.MockData` owned by this change; merge never overwrite; either apply order is safe.

**Status: ready for `/sdd-apply centralized-mock-data`.**

## Soft-freeze declarative addition — design.md — 2026-08-01

### 🟡 Addressed
- design.md §2 preamble aliasing code block: corrected bare `MockData.approvalRequests`/`MockData.urgencyReferenceDate` → `window.MockData.*`. The object is assigned as `window.MockData`, so the bare form fails outside a browser's global context (caught by `node --check` runtime smoke during Task 1+2 apply); in-browser they're identical. Declarative-only — does not change any implementer's code (the production file already uses `window.MockData.*`).

### 🔴 Outstanding
- (none — declarative soft-freeze only; no decision-level change)
