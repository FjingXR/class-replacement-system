# Review Log — venue-event-blocks

## Proposal Round 1 — 2026-10-06 10:48

### 🟡 Addressed
- (None — first round; no fixes applied yet. 🟡 findings listed below are recommended fixes, not yet made.)

### 🔴 Outstanding
- **Span-weighted summary counting triple-counts overlapping events** (proposal.md item 4; explore-brief.md summary table): `MockData.cohortTimetable.events` duplicates shared classes per cohort at the same venue+slot in all 14 weeks (mock-data.js L568–586; e.g. B002 di0 12–13 ×3, B009 di1 12–15 ×2, B107 di1 12–15 ×3, B006 di4 13–16 ×3). Grid slotMap (ui-common.js L689–697) is last-write-wins → renders 1 block; Σ-spans over weekEvents counts every duplicate → sumPending/sumUnavailable/sumTotal inflated on the DEFAULT venue (B002 Monday: card 6, grid 2). Breaks the "stats match the grid" invariant (blade L894–896). Decision needed: dedupe to distinct (di, start) head slots (grid-equivalent) before summing spans.
- **Stale-test fix list incomplete** (proposal.md item 5): once `.event-block` is live — (a) TC32 fails: default venue B002's only events are `MPU-3232`, first block text won't match `/BMIT\d+/` (tests L222–230); (b) TC40/TC41 target `#mdlCourse`/`#mdlVenue` which exist nowhere in the code (DetailModal.row renders `.detail-row` without ids, ui-common.js L1047–1054; venue openModal L924) — TC41's `textContent()` on a 0-element locator times out (tests L293–308); decide: rewrite selectors or add ids in venue modal; (c) TC58 (mobile, tests L461–465) expects 5 summary cards → must become 4, missing from the proposal's test list. The brief's instruction to "review their select/expect coherence" for TC32/TC39–44 was dropped in the proposal.

### 🟡 Recommended (non-blocking)
- New colSpan merge is fragile under partial overlap (head slot overwriting another event's continuation in slotMap → colSpan'd td followed by visible tds). Mock data today has only exact-duplicate overlaps; record the assumption or add a guard in design.md.
- Proposal item 4 dropped the brief's "non-offday days only" qualifier for span-weighted counts — carry it forward so events on Sunday/PH days (not rendered, holiday-first branch) aren't counted.

### ✅ Verdict
FAIL — proposal is factually accurate on all verified code/line/test claims and decision-complete on Q1/Q2/branch-order/no-promotion, but must resolve before design/apply: (1) specify an overlap-safe summary counting rule, (2) extend the test-fix list to TC32, TC40/TC41, TC58.

## Proposal Round 2 — 2026-10-06

### 🔴 Fixed
- **Overlap-safe summary counting specified** (proposal.md item 4): decision baked in, no implementer choice left — dedupe `weekEvents` to distinct head slots keyed `"<di>:<start>"`, last-write-wins in `weekEvents` order (grid-equivalent to slotMap's last-write-wins), count only non-offday days (holiday-first branch wins), `sumUnavailable` = occupied spans + Sunday + PH + too-soon counts. Known assumption (mock data has only exact-duplicate overlaps; last-write-wins governs exotic partial overlaps) recorded in-proposal and delegated to design.md. Ground truth re-verified: B002's events are exactly 3 × `MPU-3232` di0 12–13 exact duplicates (mock-data.js L475/492/527); blade invariant at venue blade L894.
- **Stale-test fix list completed** (proposal.md item 5): TC32 (generic course-code regex; verified default venue B002 has only `MPU-3232`, so `/BMIT\d+/` at tests L228 can never match once `.event-block` is live), TC34/TC35 (legend rewrite; tests L245–256 confirmed), TC36–TC38 (5→4 cards; id list `sumTotal`/`sumAvailable`/`sumPending`/`sumUnavailable`, dropping `sumReplacement`/`sumConflict` — tests L262–273), TC40/TC41 (rewritten as text-level assertions on `#eventModal`; decision: rewrite selectors, NO id additions to shared DetailModal — `#mdlCourse`/`#mdlVenue` confirmed to exist only in the test file; exact selectors pinned in design.md), TC58 (5→4 — tests L461–465). TC39/TC42/TC43/TC44 coherent.

### 🟡 Addressed
- Partial-overlap colSpan fragility (R1 🟡): recorded as known assumption in proposal item 4, to be documented in design.md.
- Non-offday qualifier for span-weighted counts (R1 🟡): restored in item 4.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — both Round-1 blockers resolved in-artifact against verified ground truth. Regex `/[A-Z]{2,4}-\d{4}|[A-Z]{4}\d{4}/` checked against every code in mock-data.js (all non-MPU = `[A-Z]{4}\d{4}`, all MPU = `MPU-\d{4}`) — covered. No new issues introduced. Ready for design.md, which must pin: final regex pattern, TC40/TC41 exact selectors, partial-overlap assumption.

## Design Round 1 — 2026-10-06

### 🔴 Outstanding (at review time)
- **§8.4 pending-reachability sweep cites unreachable demo states; §9 wrong data-source claim** (design.md §8.4, §9): "pending demo states live on B103 ([0,6..7,3]) / B104 [3,2..3,4]" are §2.11 `MockData.venueSlots` pairs — the venue page reads ONLY `semester`/`venues`/`cohortTimetable.events` (blade L537/561/599/700–702), never `venueSlots`. The "mock §2.6/2.7 seeds" claim is half-wrong: §2.6 = `myTimetable` does not feed `getVenueEvents()`; only §2.7 `flags` (mock-data.js L552–566) reach the grid. Pin the concrete reachable pending states (all grey `event-others-pending` — no reachable tertiary mine-pending exists, since every pending flag's lecturer ≠ `En. Lim Jia Zheng`): week 1 B110 Mon 2–5 AMIS1012 (dsf2s1, L554 — wins last-write over the normal dft2s1 twin L407), week 4 B006 Tue 11–12 AMCS1013 (dsf1s1, L557), week 7 B110 Mon 2–5 AMIS1012 (dft2s1, L560), week 8 B015 Tue 6–9 BMIT2073 (rsd3s1g2, L561), **week 10 B002 (default venue) Mon 12–13 MPU-3232 (rsd3s1g3, L562)**, week 13 B014 Tue 2–5 BMIT3173 (rsd3s1g1, L565). Record in §9 that `event-mine-pending` (tertiary) is unobservable on this page under the frozen "Mock data: none added" constraint, else §8.4's "pending venues show tertiary/grey variants" step cannot be verified.

### 🟡 Recommended (non-blocking)
- §8.4 example "B002 default week: Unavailable = 2" contradicts the design's own formula (occupies + sunday + ph + tooSoon) — one offday column renders 24 offday cells every week, so B002 ≈ 26; verifier would file a false counting-bug alarm.

### 🔴 Fixed (same round, before R2)
- §8.4/§9 pending reachability pinned: unreachable `venueSlots` (B103/B104) and §2.6 (`myTimetable`) references removed; concrete §2.7-flag-derived pending states pinned (week 1 B110 / week 4 B006 / week 7 B110 / week 8 B015 / **week 10 B002 default venue** / week 13 B014 — all grey `event-others-pending`); §9 records `event-mine-pending` (tertiary) as unobservable under "Mock data: none added" (tertiary verifiable via `/cohort-timetable-ui` instead).
- §8.4 example fixed: "the single MPU-3232 block contributes exactly 2 to Unavailable (plus ~24 offday `cell-sun`/`cell-ph` cells; formula includes them)".
- Nits: `td.colSpan` set only when `span > 1` (byte-parity with builder); §7 a11y wording tightened (builder grants tabindex only; role/aria-label are venue-local stricter additions); §6 baseline paragraph de-self-edited (baseline = §8.1 run output).

## Design Round 2 — 2026-10-06

### 🔴 Fixed
- §8.4/§9 pending-reachability sweep verified against mock-data.js exactly: the §2.7 flags (L552–566) contain exactly 6 'pending' entries → weeks 1/4/7/8/10/13; base-event mapping all correct (AMIS1012 dsf2s1/dft2s1 → B110 di1 start2 end5, L430/L407; AMCS1013 dsf1s1 → B006 di1 11–12, L422; BMIT2073 rsd3s1g2 → B015 di2 6–9, L518; MPU-3232 rsd3s1g3 → B002 di0 12–13, L527; BMIT3173 rsd3s1g1 → B014 di2 2–5, L509). All 6 lecturers ≠ currentUser (`En. Lim Jia Zheng`, L80) → grey-only. Week-1 last-write note verified (dsf2s1 pending L430 follows dft2s1 normal L407 → one grey block). §2.11/§2.6 references correctly dropped.
- §8.4 "exactly 2 to Unavailable" verified (3 exact-duplicate MPU-3232 donors dedupe to one head, normal in week 2 → occupied = 2). Formula self-consistency re-derived: 66 available + 0 pending + 88 unavailable = 154 = 7×22 grid slots.
- Nits verified: colSpan gate matches builder L783–785; a11y wording matches L745; §6 baseline paragraph; partial-overlap assumption documented.

### 🟡 Addressed (after R2, same-round fixes)
- 🔴 Outstandings fixed below.
- **4 of 6 weekday labels wrong** → corrected via generateWeekData abbr derivation: week 1 B110 → **Tue** 2–5, week 7 B110 → **Tue** 2–5, week 8 B015 → **Wed** 6–9, week 13 B014 → **Wed** 2–5 (weeks 4/10 already correct).
- **"~24 offday cells" ignored too-soon** → §8.4 example rewritten with true default-view composition: 2 occupied + 22 sun + 64 too-soon = Unavailable 88, Pending 0, Available 66, Total 154 (7×22); added the week-10 clause (same B002 head pending → 2 land in sumPending).

### 🔴 Outstanding
- **§9's tertiary-variant fallback claim is factually wrong** — `event-mine-pending` is unreachable on `/cohort-timetable-ui` too (cohort page never reads `myTimetable`; no 'pending' flag has lecturer === currentUser; the only page with currentUser pending rows is `/my-timetable-ui`, which renders `event-pending`, not the mine/others pair) → under frozen mock data `event-mine-pending` has NO reachable instance on any page.

🔴 **R3 fix**: §9 rewritten — tertiary `event-mine-pending` unobservable on EVERY page of the mock phase; its verification = code-path inspection (both pending branches share the identical cellRender expression) + CSS presence (theme.css:1952); /cohort-timetable-ui fallback claim removed. Weekday labels corrected in both §8.4 and §9 lists.

### ✅ Verdict (R2)
FAIL — tertiary-claim untenable; two 🟡 (weekday labels ×4, Unavailable composition) fixed in the same edit.

## Design Round 3 — 2026-10-06

### 🔴 Fixed
- **Weekday labels corrected (§8.4 + §9)** — re-verified against flags L552–566 → base events + abbr array (ui-common.js L77): wk1 B110 Tue 2–5 ✓ (dsf2s1 L430 last-write over dft2s1 twin L407 — one grey block), wk4 B006 Tue 11–12 ✓, wk7 B110 Tue 2–5 ✓, wk8 B015 Wed 6–9 ✓ (rsd3s1g2 L518), wk10 B002 Mon 12–13 ✓, wk13 B014 Wed 2–5 ✓ (rsd3s1g1 L509).
- **§8.4 default-view composition verified end-to-end** — B002 = venues[0]; week idx 2 exact; grid 7×22 = 154; B002∩wk2 = 3 exact-dup MPU-3232 → deduped head span 2; branch order shields the 2 occupied Monday cells from cell-too-soon; isSlotTooSoon whole-day, cutoff Thu 08 Oct → 64 too-soon = 3×22 − 2; Sun 22, PH 0; **Available 66, Pending 0, Unavailable 88, Total 154**; week-10 clause (same head pending → 2 land in sumPending) confirmed (rsd3s1g3 donor last in base order).
- **§9 tertiary rewrite verified** — venue blade zero `myTimetable`/`venueSlots` refs; myTimetable pending rows L243/253 (lecturer = currentUser) render via default builder `event-pending` (MyTimetable blade has no statusClassFn; ui-common L760–761); cohort page never reads myTimetable; theme.css:1952 present; cohort L364–365 expression identical to design §2 → code-path-inspection + CSS-presence verification executable.

### 🟡 Addressed
- R2 carry-overs (weekday labels ×4, Unavailable composition) — fixed and verified.
- §9 stale parenthetical (R3 pass note): reworded to "no *reachable* mine-pending instance (the L358–365 branch there is dead code under the mock data)" — soft-freeze declarative edit, no implementer impact.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — all three R3 fixes grounded; §5↔§8.4 counting system (66/0/88/154, week-10 pending redirect, branch-order interplay) arithmetically closed. **proposal.md + design.md FROZEN.** Batch 3 = tasks.md (specs/ skipped: single-page change, house precedent — no archived change carries specs/).

## Tasks Round 1 — 2026-10-06

### 🔴 Fixed
- (None — first round; no fixes applied yet.)

### 🟡 Addressed
- (None at review time. 🟡 findings listed below are recommended, not yet made.)

### 🔴 Outstanding
- **T7 sweep drops 4 frozen design §8.4 verification steps + §9's code-path-inspection check, all with zero automated coverage**: (1) normal `event-others` green blocks (no automated test asserts mine/others classes; the change's core distinction otherwise verified nowhere); (2) week-4 B006 Tue 11–12 AMCS1013 + week-8 B015 Wed 6–9 BMIT2073 pending instances from the frozen §8.4/§9 reachable set; (3) "too-soon/cell-no-fit unchanged" regression check (no automated coverage; T1/T4 touch that state); (4) "mobile card list intact ≤768px" (T1 rewrites the head branch housing mobileSlotDayHeader + createEventCard — blade L793–796 — and no automated mobile booked-card test exists); (5) design §9 pins tertiary event-mine-pending verification = code-path inspection + theme.css:1952 presence — no task carries it. Fix: append these to T7 (current batch).
- 🟡 non-blocking: T0's expected baseline-failing set omits TC38 (blade L422–433 commented out → #sumTotal absent → TC38 L275 fails today; true set = TC34/35/36/37/**38**/40/41/58). No functional impact (T6 compares against the recorded baseline-tests.txt; TC38 needs no edit).
- 🟡 non-blocking: T9 doesn't restate frozen §8 step-7 "no commit until instructed"; only implies it via "leave .sdd/changes/ for /sdd-verify".

### 🟡 Recommended (non-blocking)
- T2 "Enter ~L1050" vs actual L1052 (design says ~L1052); T6 could save a post-change run file for the audit trail.

### ✅ Verdict
FAIL — otherwise task coverage, ordering/deps, granularity, and every pinned value (line numbers, regexes, selectors, ids, 66/0/88/154 at week idx 2, sweep facts against mock-data L406/L552–566) verified against frozen design/proposal and ground truth; specs/ skip confirmed defensible (skill rule + zero specs/ in .sdd/archive/). Sole blocker: complete T7 with the frozen §8.4/§9 verification sub-steps before /sdd-apply.

### 🔴 R2 fix (same-round)
- T7 expanded to the full 9-item sweep: others-green class check (3), week-4/week-8 pending instances (4), too-soon/cell-no-fit regression check (8), mobile booked-card list check (9), §9 tertiary code-path-inspection + theme.css:1952 step (5).
- T0: baseline "expected set" now lists TC38 too, and defers to the recorded baseline-tests.txt as authoritative.
- T9: explicit "No commit until the user explicitly says commit" line added.
- Nits: T2 Enter ~L1052; T6 saves post-tests.txt for the audit trail.

## Tasks Round 2 — 2026-10-06

### 🔴 Fixed
- **T7 sweep completed** (R1 blocker): all 5 omitted sub-checks present and verified phrase-by-phrase against frozen design §8.4/§9 — (1) normal `event-others` green classList check (item 3, B002 MPU-3232 week idx 2, no-automated-coverage note accurate); (2) week-4 B006 Tue 11–12 AMCS1013 + week-8 B015 Wed 6–9 BMIT2073 (item 4, abbrs match R2/R3-corrected labels); (3) too-soon/cell-no-fit regression (item 8); (4) mobile ≤768px booked-card list via rewritten head branch (item 9, cross-referenced to T1's rewrite risk); (5) §9 tertiary `event-mine-pending` code-path inspection + `theme.css:1952` presence (item 5). Pending list mirrors §8.4's sweep set incl. the week-1 one-grey-block-not-two last-write caveat; default-view cards 154/66/0/88 @ week idx 2 B002 and week-10 "(2 → sumPending)" match §8.4 exactly; items 6–7 match §8.4 modal/keyboard/Book steps.
- **T0 baseline set completed** (R1 🟡): TC38 added → full expected set TC34/35/36/37/38/40/41/58; recorded `baseline-tests.txt` designated authoritative over the pre-pinned list.
- **T9 no-commit line added** (R1 🟡): "No commit until the user explicitly says commit (standing rule)" — matches frozen §8 step 7.
- **Nits** (R1 🟡): T2 Enter ~L1052 ✓; T6 saves `post-tests.txt` ✓.

### 🟡 Addressed
- (None — no 🟡 findings this round.)

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — sole R1 blocker fully resolved with zero drift from frozen §8.4/§9 and no new issues. **Batch 3 passes; proposal.md + design.md + tasks.md FROZEN.** Change ready for `/sdd-apply`.

## Unfreeze — user decision B (green-overlap copy mitigation) — 2026-10-06

User reviewed the Q1 green-overlap trade-off (green empty bookable cells vs green
event-others blocks) and chose **option B**: reword the booking hint from
"Click any green slot to book this venue" -> "Click any green empty slot to book
this venue". Decision-level (visible copy the implementer would otherwise not
write) -> unfreeze + re-review from the proposal point; design and tasks unfreeze
as downstream artifacts.

Deltas this round:
- **proposal.md**: new item 6 (hint reword, sole copy change) + Known Trade-off
  mitigation line updated to reference item 6.
- **design.md**: new §4a pinning the exact before/after span text (~L397),
  svg/container untouched.
- **tasks.md**: new T3b (T5 dep updated to [T1..T4 + T3b]); T7 item 6 extended
  to verify the new hint text during the sweep.

## Proposal Round 3 — 2026-10-06

### 🔴 Fixed
- **Item 6 present as a proper top-level list item** (proposal.md L95–101): follows item 5's test sub-list (last sub-bullet L92–93) and precedes `## Explicitly Won't Change` (L103). Pins the exact pair `Click any green slot to book this venue` → `Click any green empty slot to book this venue`, at `~L397`, "the only page copy changed (one `<span>` text)", svg and `.booking-hint` container untouched.
- **Ground truth verified**: `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php` **L397** = `<span>Click any green slot to book this venue</span>` (byte-exact match to item 6's "before" string), inside `<div class="booking-hint" id="bookingHint">` (L395) with the `<svg>` sibling on L396; grid include at L404 → the hint is static page markup outside `cellRender`.
- **Known Trade-off coherence** (L117–125): mitigation list now ends `…and the booking hint now says "green **empty** slot" (item 6)` (L122–124) — references the new item correctly; legend-tips mitigation (frozen) still listed, Q1 decision line unchanged.
- **No contradiction with `## Explicitly Won't Change`** (L103–115): its booking-affordance bullet covers `cell-available`/`cell-no-fit`, Book tooltip, `B` shortcut, `cell-too-soon`, `cell-sun`/`cell-ph` — cell/JS affordances, not the hint line; mobile/modal/shared-files/mock-data bullets unaffected. The hint text appears nowhere in the won't-change list.
- **§10.0 house rule**: item 6 is pure text — zero hex/rgb/token edits across the whole delta (only color mention is the pre-existing `--color-success-container` prose at L119). No `#rrggbb`/`rgb(` anywhere in proposal.md/design.md/tasks.md.

### 🟡 Addressed
- (None — no prior 🟡 carry-over; R2 closed with empty Outstanding.)

### 🟡 Recommended (non-blocking)
- Reword L100 "this is the only page copy changed" → "the only copy change this mitigation introduces": read globally it collides with item 3 (legend labels/tips are also page copy this change rewrites). Scoped to user decision B it is true, and items 1–5 pin all other text, so implementer impact = zero.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — item 6 is factually exact against blade L397, structurally a clean list item, coherent with Won't-Change and the Known Trade-off, and adds no color surface.

## Design Round 4 — 2026-10-06

### 🔴 Fixed
- **§4a placed correctly**: `### §4a Booking hint copy (unfreeze round, user decision B)` at design.md **L97–108**, immediately after §4's legend-tips paragraph (L93–95) and before `## 5 Summary cards` (L110).
- **Exact before/after pinned**: L102 `<!-- before --> <span>Click any green slot to book this venue</span>` / L103 `<!-- after  --> <span>Click any green empty slot to book this venue</span>` — the "before" string verified byte-exact against blade **L397**; location pin "Blade ~L397, inside the existing `.booking-hint` div" correct (container L395, svg L396); L106 "The svg and container attrs are untouched" matches the delta scope.
- **Justification cross-reference**: L106–108 cites proposal item 6 / Known Trade-off ✓.
- **§1 ↔ §4a no contradiction**: §1 (L13–14) "Booking branches (available / too-soon / holiday / Sunday) are untouched" scopes `cellRender` branches; §4a's target is the static `.booking-hint` span at L395–398 (grid include L404), pinned "span text ONLY" → unambiguous, no branch or JS touched.
- **Frozen facts un-drifted (spot checks)**: §4 legend table still the 4-item set (L86–91: `var(--color-success-container)` / `var(--color-primary-container)` / `var(--color-success-container)` / `var(--color-tertiary-container)` with labels Available / Your Classes / Others' Classes / Pending); summary numbers unchanged at L217 (`Total 154 = 7×22, Available 66, Pending 0, Unavailable 88`) and §8 sweep text L200–219 intact; §5–§9 untouched by the delta; no hex/rgb introduced (only pre-existing `var(--color-…)` in §4).

### 🟡 Addressed
- (None — no prior 🟡 carry-over; R3 closed with empty Outstanding.)

### 🟡 Recommended (non-blocking)
- §8 step 2 (L198) still enumerates "Edit blade (§2/§3/§4/§5)" — add `§4a` so the apply sequence lists the new edit section; §8 step 4's sweep omits the hint check, but tasks T7 item 6 (L105–107) covers it, so there is no verification hole. Optional: one clause in §4a stating "static markup outside `cellRender`" (currently implied by the L397/`.booking-hint` pin).

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — §4a pins a byte-exact single-span copy edit at the verified line, justified via proposal item 6, with §1 compatible and zero color-token or frozen-fact drift.

## Tasks Round 3 — 2026-10-06

### 🔴 Fixed
- **T3b present and correctly slotted**: tasks.md **L48–51**, between T3 (L43–46) and T4 (L53): `**T3b — Booking hint copy [no deps]** (design §4a)` with `~L397`, exact before → after strings (byte-match to blade L397 and design §4a L102–103), "span text only; svg/container untouched".
- **T5 dependency updated**: L63 `**T5 — Fix stale expectations [T1..T4 + T3b]**` ✓ — the new task is in the set.
- **T7 item 6 extended**: L105–107 keeps the modal/Enter check and adds `booking hint on the same page reads Click any green empty slot to book this venue (T3b)`. Executability verified: `#bookingHint` is force-shown on every render (blade **L582** `style.display = 'flex'`) so the text is visible in the default view, and `tests/venue-timetable.spec.ts` has **zero** assertions on `bookingHint`/`green slot` → the sweep step is the sole (and correctly placed) verification.
- **Numbering/deps coherent**: T0–T9 with T3b is consistent; chain T5→`[T1..T4 + T3b]`, T6→`[T5]`, T7→`[T6]`, T8→`[T7]`, T9→`[T8]` valid; T3b `[no deps]` matches design §4a (no dependency on T1–T4).
- **Frozen facts intact**: T7 still the full **9-item** sweep (items 1–9, L89–114) — only item 6 grew; default-view cards **154 / 66 / 0 / 88 @ weeks idx 2, B002** (L89–90) unchanged; T0 baseline set, T5's TC list, T6/T8/T9 unchanged; no color/hex in any delta line (T3b + T7 item 6 are pure text).

### 🟡 Addressed
- (None — no 🟡 findings this round.)

### 🟡 Recommended (non-blocking)
- L3 Status line still reads "proposal.md and design.md frozen (R3 PASS …)" — pre-unfreeze wording; refresh at the next freeze (the Unfreeze entry above supersedes it meanwhile). T5's `+ T3b` dep is broader than strictly needed (T5 edits no hint text) — harmless.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — T3b slotted, T5 dep and T7 item 6 extended exactly as decided, 9-item sweep and 154/66/0/88 intact, pure-copy delta with zero color-token surface.

## Apply-time Unfreeze Round 1 — TC41 — 2026-10-07

Apply-time unfreeze (user-approved) after T6 post-change run: TC41 failed.
Amendment: TC41 regex → `/[A-Z]\d{3}/` with rationale "shared DetailModal
Venue row = bare venue code (ui-common.js:876)". Reviewed by `general`
agent (sdd-reviewer unavailable; same protocol).

### 🔴 Outstanding
- Amendment FAIL — misdiagnosis: the real TC41 cause is locator ambiguity
  (`:has-text("Venue")` also matches the Status Description row "Class
  booked for this venue", blade L939 — strict-mode violation), NOT the row
  format. The page's own `openModal` (blade L924) renders the Venue row as
  `CODE — Type (N seats)`; the frozen design's format premise was correct.
  Round-1 amendment left TC41 failing (verified by live run). User
  re-approval obtained for round-2 locator fix.

### ✅ Verdict
FAIL — round-2 unfreeze required (locator disambiguation + rationale
correction in design §6/§8, tasks T5/T7).

## Apply-time Unfreeze Round 2 — TC41 — 2026-10-07

Round-2 amendment (user-approved): spec TC41 locator →
`page.locator('#eventModal .detail-row', { hasText: /\(\d+ seats\)/ })`,
assertion `toContainText(/[A-Z]\d{3}/)`; design §6/§8 and tasks T5/T7
rationale rewritten to the true root cause with the restored
`CODE — Type (N seats)` premise (blade L924). Reviewed by `general` agent.

### 🔴 Fixed
- Locator ambiguity resolved: exactly one modal row can match
  `/\(\d+ seats\)/` (Venue row); verified strict-mode-safe against all 10
  rows the venue `openModal` emits incl. pending variants.
- Round-1 misdiagnosis purged from all artifacts (grep: 0 residual
  "bare venue-code"/"ui-common.js:876" rationale hits).

### 🟡 Addressed
- Latent fallback noted (non-blocking): blade L924 `e.venue` fallback would
  defeat the locator if `currentVenue` were ever falsy — unreachable on
  this page; recorded for future maintainers.

### 🔴 Outstanding
- (None)

### ✅ Verdict
PASS — targeted runs `-g "TC41"` and `-g "TC40|TC41|TC42|TC43|TC44"` both
green; design/tasks/spec agree; no decision-level drift beyond the
approved TC41 fix.

## T7 Sweep Findings — declarative amendments — 2026-10-07

Post-implementation sweep (T7, all 9 items executed; screenshots in the
outer repo root `.playwright-mcp/venue-t7-*.png` — 6 files: default-cards,
b110-mine-block, b110-week1-pending, b006-week4-pending,
b002-week10-pending, mobile-cards). Two data-level findings, both
declarative corrections (no implementer decision changes; code already
written and correct):

### 🟡 Addressed
- design §9 reachable-pending list corrected: week 8 B015 `BMIT2073`
  (rsd3s1g2, pending) is shadowed by rsd3s1g3's normal exact-duplicate
  twin later in the events array → renders green `event-others`,
  sumPending 0 — grid + counters agree per §5 last-write-wins (verified
  live; not an implementation defect). Live-verified pending set: wk1
  B110 (single grey block over twin), wk4 B006, wk10 B002 (sumPending 2).
- Baseline/reachability discrepancy documented in `post-tests.txt`
  accounting block (16 pre-existing out-of-scope failures = macos-ui-
  refactor staleness; user-approved scope-split).

### 🔴 Outstanding
- (None)

### ✅ Verdict
N/A (sweep record, not a review round — artifacts amended declaratively
only; frozen decision content untouched).
