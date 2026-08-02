# Tasks — Centralized Mock Data Module (`public/js/mock-data.js`)

> Frozen artifacts: `proposal.md`, `design.md`. Each task ≤ 2 hours.
> Refactor only — no new pages, no backend. Verbatim fidelity (preserve existing data quirks); only the two §4 corrections (week-baseline + chip) change visibly.

## ⚠ Merge procedure vs sibling state (READ FIRST)

**On-disk reality (verified 2026-08-01):** `public/js/mock-data.js` **already exists** (534 lines) — the frozen sibling `request-approval` chain was partially applied: the file currently contains ONLY the bare globals `const approvalRequests = [ ...20 entries... ]` (L1) and `const URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00')` (L534). There is **no** `window.MockData` block yet. The layout does NOT yet load `mock-data.js`.

**Therefore:**
- **Task 1 does NOT recreate the file.** It PREPENDS the file header comment + `window.MockData = { ...11 sections... }` block ABOVE the existing `const approvalRequests` line. The 11 sections do NOT include `approvalRequests`/`urgencyReferenceDate` inside the object literal (those would hit the `const` TDZ). The existing `const approvalRequests` + `const URGENCY_REFERENCE_DATE` lines stay untouched at the bottom.
- **Task 2 does NOT re-add `const approvalRequests` / `const URGENCY_REFERENCE_DATE`** — they already exist; re-adding throws `SyntaxError: Identifier already declared`. Task 2 APPENDS only the two aliasing lines at the very end: `MockData.approvalRequests = approvalRequests;` and `MockData.urgencyReferenceDate = URGENCY_REFERENCE_DATE;` (must execute AFTER the existing `const` declarations).
- If, at apply time, the file does NOT yet exist (sibling not applied), Task 1+2 create it in one pass: `window.MockData = {...}`, then the two `const` globals, then the two aliasing lines — matching design §2 preamble ordering.

## Task 1 — Create/extend `public/js/mock-data.js` skeleton + registries

- [x] **If `public/js/mock-data.js` already exists** (starts with `const approvalRequests = [`): PREPEND a file header comment + `window.MockData = { ... }` skeleton ABOVE it (do NOT recreate the file). **If it does not exist:** create it with the header + `window.MockData = { ... }` skeleton, followed by the two bare globals (see Task 2). Either way, the skeleton defines all section keys: `semester, holidays, cohorts, lecturers, venues, myTimetable, cohortTimetable, requests, conflictedClasses, arrangementWeeks, venueSlots` (NOT `approvalRequests`/`urgencyReferenceDate` — those are aliased in Task 2).
- [x] Populate `semester` (§2.1): `label`, `startDate: '2026-08-31'`, `endDate: '2026-12-06'`, `weeks: 14`, and precompute `chipText` via internal `fmtChip` (hyphen-format) → `'202605 Semester · 31-Aug-2026 ~ 06-Dec-2026'`
- [x] Populate `holidays` (§2.2): `[{ week: 3, dayIndex: 3, label: 'Public Holiday' }]` (CohortTimetable rule `d===3 && w===3`)
- [x] Populate `cohorts` (§2.3): 14 entries mirroring `dataset/cohorts.md` + CodingMAIN §8 student counts (verbatim codes incl. DFT/DSF omitting `G1`, RSD/RAF/RBU including it)
- [x] Populate `lecturers` (§2.4): 14 entries from `dataset/lecturers.md`; `isPl` = `role === 'Programme Leader'` (rows 5425, 5516)
- [x] Populate `venues` (§2.5): 23 Block B rooms from CodingMAIN §3 (`{ code, type, capacity, allowedSessions }`)

**Effort:** ~1.5h

## Task 2 — Add page-data sections + aliasing to `mock-data.js` (verbatim extraction)

- [x] `myTimetable` (§2.6): `seedWeek: 11`, `eventsByWeek: { 11: [ …12 events verbatim from MyTimetable L663–675… ] }`
- [x] `cohortTimetable` (§2.7): `faculties` (verbatim `facultyData`), `events` (flatten every existing `addEvent('rsd2s1'|'rsd3s1g1'|'dsf2s1'|'dft2s1'|'dmc2s1'|'dit2s1', ...)` call in source L683–L803 verbatim — **dit2s1 has none** (leave empty to honour fidelity), **dmc2s1 has weeks 0,2 only** (leave week 1 empty). EXCLUDE the L734–L736 rsd3s1g2 loop entries (those are reconstructed from `rsd3g2Base` + `rsd3g2Flags` per §2.7 disambiguation).), `rsd3g2Base` (7 events), `rsd3g2Flags` (verbatim)
- [x] `requests` (§2.8): full verbatim `mockRequests` array from my-request-history L442+ (20 entries, `id` 1–20; preserve `id` as first field)
- [x] `conflictedClasses` (§2.9): 14-entry verbatim array from replacement-home L311–325
- [x] `arrangementWeeks` (§2.10): 3-entry verbatim array (Week 11/10/9 descending, incl. `04-Sep Thu holiday:true`); preserve day-label quirks
- [x] `venueSlots` (§2.11): verbatim `venueSlotData` object (venue → `[dayIndex,hourIndex,statusInt]` triples)
- [x] **Aliasing (merge with existing sibling globals — see ⚠ preamble):** if the bare `const approvalRequests` / `const URGENCY_REFERENCE_DATE` are already present (sibling applied), do NOT re-add them (re-adding throws `SyntaxError: Identifier already declared`). ONLY APPEND at the very end of the file: `MockData.approvalRequests = approvalRequests;` and `MockData.urgencyReferenceDate = URGENCY_REFERENCE_DATE;` (these execute after the existing `const` declarations). If the file was created fresh in Task 1 (sibling not applied), add the bare globals + the aliasing lines per design §2 preamble.

**Effort:** ~2h

## Task 3 — Wire layout + end-to-end smoke

- [x] `resources/views/layouts/ui-template.blade.php`: add `<script src="/js/mock-data.js"></script>` immediately AFTER the `<script src="/js/ui-common.js"></script>` line (L33). Currently ABSENT (verified) — add it. (Idempotent: skip if already present from a later sibling apply.)
- [x] `php artisan serve`; load `/my-timetable-ui` once — confirm `window.MockData` exists in console (`typeof MockData`, `MockData.semester.chipText`, `MockData.approvalRequests.length === 20`) and NO console errors (in particular no `SyntaxError: Identifier already declared` from the aliasing/globals)
- [x] Confirm other 4 routes still load with no console errors (the 4 non-sibling pages don't declare `approvalRequests`/`URGENCY_REFERENCE_DATE`, so no collision is possible there; the real "Identifier already declared" hazard is on the sibling route, checked in Task 10)

**Effort:** ~0.5h

## Task 4 — Refactor MyTimetable page

- [x] `MyTimetable-UI-design-template.blade.php`: remove inline `eventsData` (L661–683) and the `weekData` IIFE literal `new Date(2026, 5, 15)` (L640, L686)
- [x] Replace with: read `MockData.myTimetable` per §2.6 local-copy pattern (`eventsByWeek` derived from seed + weeklyTemplate slice — do NOT alias directly); `weekData` IIFE reads `new Date(MockData.semester.startDate)`
- [x] Replace static semester chip (L537) → `<span class="semester-chip" id="semesterChip"></span>` + `document.getElementById('semesterChip').textContent = MockData.semester.chipText;`
- [x] Verify in-browser: week-11 events identical to before; default week clamps to Week 1 (dev-date 2026-08-01 is pre-semester) per §5 — do not "fix" the clamp; chip shows `202605 Semester · 31-Aug-2026 ~ 06-Dec-2026`; week picker / theme toggle / nav work; no console errors

**Effort:** ~1h

## Task 5 — Refactor CohortTimetable page

- [x] `CohortTimetable-UI-design-template.blade.php`: remove `facultyData` (L650–670), the entire events block L673–806 (contains the `addEvent()` function definition, all non-rsd3g2 `addEvent()` calls, AND the nested `rsd3g2Base`/`rsd3g2Flags` declaration + apply loop), and the `weekData` IIFE literal start date (L625, L807)
- [x] Replace with: rebuild `allEvents` from `MockData.cohortTimetable.events` (non-rsd3g2 only) + apply `MockData.cohortTimetable.rsd3g2Base`/`rsd3g2Flags` per the original loop (§2.7 + disambiguation note); `weekData` IIFE reads `new Date(MockData.semester.startDate)`; holiday rule reads `MockData.holidays` (`week===3 && dayIndex===3`)
- [x] Replace static semester chip (L497) → dynamic `MockData.semester.chipText`
- [x] Verify in-browser: all cohorts render identical events; RSD3 G2 shows 7 events/week (NOT 14); dit2s1 renders empty (no events, as today); dmc2s1 week 1 empty (as today); holiday (Week 3 Thu) renders; week picker / faculty-cohort dropdown / today-column work; no console errors

**Effort:** ~1.5h

## Task 6 — Refactor ReplacementHome page

- [x] `replacement-home-UI-design-template.blade.php`: remove inline `conflictedClasses` (L311–325); replace with `const conflictedClasses = MockData.conflictedClasses;` (read-only — page does not mutate it)
- [x] Standardise week computation: `computeWeek()`/`weekRangeLabel()` literals `new Date('2026-08-31')` (L353, L360) → `new Date(MockData.semester.startDate)` for consistency (value already equal; pure standardization)
- [x] Replace static semester chip (L242) → dynamic `MockData.semester.chipText`
- [x] Verify in-browser: 14 conflicted rows identical; sort/filter/pagination/summary cards work; nav/theme work; no console errors

**Effort:** ~0.75h

## Task 7 — Refactor ReplacementArrangement page

- [x] `replacement-arrangement-UIdesign-template.blade.php`: remove inline `weekData` (L910–947) + `venueSlotData` (L949+); replace with `const weekData = MockData.arrangementWeeks;` and `const venueSlotData = MockData.venueSlots;` (read-only — page does not mutate)
- [x] DO NOT standardise `weekData` to `MockData.semester` — it is page-specific (§2.10); preserve day-label quirks verbatim
- [x] Replace static semester chip (L764) → dynamic `MockData.semester.chipText`
- [x] Verify in-browser: 3-week grid identical (Week 11/10/9 descending, `04-Sep Thu` holiday cell); venue dropdown / slot selection (`MAX_SELECTION=4`) / summary work; no console errors

**Effort:** ~0.75h

## Task 8 — Refactor MyRequestHistory page

- [x] `my-request-history-UI-design-template.blade.php`: remove inline `mockRequests` (L442+); replace with `const mockRequests = MockData.requests;` (page-local alias of same name — render code unchanged; page does not mutate the array)

> Note: the page-local `const mockRequests` is intentional here — it shadows nothing global (the sibling's bare global is named `approvalRequests`, deliberately distinct). This alias is purely to avoid touching the render code.

- [x] Week computation: **no-op for this page** — its `weekRanges` (L465–470) is a static 4-row lookup table with precomputed date strings, NOT computation from a semester start. Standardisation (§4 step 3) does not apply here. Leave as-is.
- [x] Replace static semester chip (L341) → dynamic `MockData.semester.chipText`
- [x] Verify in-browser: 20 request rows identical (toolbar "Showing 20 of 20"); status filter / sort / pagination / summary cards / modal work; urgency badges correct; no console errors

**Effort:** ~1h

## Task 9 — Documentation updates

- [x] Update `page-changelogs/my-timetable-changelog.md`: note mock-data.js migration, week-baseline → 2026-08-31, dynamic chip
- [x] Update `page-changelogs/cohort-timetable-ui-changelog.md`: same notes + RSD3 G2 reconstruction-from-shared-data
- [x] Update `page-changelogs/replacement-home-changelog.md`: note conflictedClasses moved to mock-data.js + dynamic chip
- [x] Update `page-changelogs/replacement-arrangement-changelog.md`: note weekData/venueSlotData moved to mock-data.js (page-specific, NOT standardised) + dynamic chip
- [x] Update `page-changelogs/my-request-history-changelog.md`: note mockRequests moved to MockData.requests + dynamic chip
- [x] Update `CodingMAIN.md` §10 (conventions): add `public/js/mock-data.js` to the shared-modules list (alongside `theme.css` / `ui-common.js`) with a one-line description
- [x] Update `CodingMAIN.md` §13 (key-files map): add `public/js/mock-data.js ← all UI mock data (throwaway)`

**Effort:** ~1h

## Task 10 — Final end-to-end verification

- [x] `php artisan serve`; load all 5 routes in sequence: `/my-timetable-ui`, `/cohort-timetable-ui`, `/replacement-home-ui`, `/replacement-arrangement`, `/my-request-history-ui`
- [x] For each: confirm tables/grids render with the same rows as before the refactor; cross-check against existing screenshots in the project root (`my-timetable-*.png`, `cohort-week3-pending-check.png`, `home-week-picker.png`, `my-timetable-layout.png`, etc.) — expect identical layout; only MyTimetable/CohortTimetable week-date labels + all 5 semester chips change
- [x] Confirm no console errors on any page; confirm theme toggle, nav, week pickers, filters, sort, pagination, modals all functional
- [x] Confirm sibling route unaffected (if `/request-approval-ui` exists): loads, `approvalRequests`/`URGENCY_REFERENCE_DATE` globals resolve, no "Identifier already declared"
- [x] `composer run lint:check` — only the layout file is PHP-touched (1 idempotent line); confirm no new lint failures introduced
- [x] Commit the change (per the repo's conventional-commit style: `refactor: centralize UI mock data into public/js/mock-data.js`)

**Effort:** ~1h

## Rationale for skipping specs/ (Batch 3)

This change is a **data-only refactor** with no new capabilities or behaviours — the system renders identically except the two §4 corrections (week-baseline + chip text). There are no requirements-style specifications to write beyond what `proposal.md` (scope) and `design.md` (mechanism) already capture. Per the SDD workflow, specs are "optional for simple changes." Skipping Batch 3.
