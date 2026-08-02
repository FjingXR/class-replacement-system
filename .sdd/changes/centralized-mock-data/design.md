# Design — Centralized Mock Data Module (`public/js/mock-data.js`)

> Frozen proposal: `.sdd/changes/centralized-mock-data/proposal.md`. This design operationalizes it.

## 1. Technical approach

A single plain-JS module exposes `window.MockData` (plus two compatibility globals owned by the sibling `request-approval` chain). It is loaded by the shared layout **after** `ui-common.js` and before each page's inline `@section('page-scripts')`. Pages delete their inline data arrays and read from `MockData.*`; all render/sort/filter/week-range logic stays in page scripts, now parameterised by `MockData.semester`.

**Why this shape (not JSON+fetch):** every page renders synchronously on `DOMContentLoaded`. A synchronous `<script>`-loaded object avoids async restructuring of all 5 pages' render entry points — minimal work, matches the existing `theme.css` / `ui-common.js` shared-module convention, and is throwaway on Sprint 3.

## 2. File layout

**`public/js/mock-data.js`** — single file, structured top-to-bottom:

```js
// ─────────────────────────────────────────────────────────────
// Mock data — single source of truth for all UI design templates.
// Throwaway: deleted when Sprint 3 wires real Livewire/DB data.
// Read-only: pages MUST treat MockData as immutable; derive local
// copies (slice()/spread) before mutating.
// ─────────────────────────────────────────────────────────────

window.MockData = {
    semester,        // §2.1
    holidays,        // §2.2
    cohorts,         // §2.3 — mirrors dataset/cohorts.md + CodingMAIN §8
    lecturers,       // §2.4 — mirrors dataset/lecturers.md
    venues,           // §2.5 — mirrors CodingMAIN §3 venue matrix
    myTimetable,     // §2.6 — was MyTimetable inline eventsData + week-11 seed
    cohortTimetable, // §2.7 — was CohortTimetable facultyData + allEvents
    requests,        // §2.8 — was my-request-history mockRequests
    conflictedClasses, // §2.9 — was replacement-home inline array
    arrangementWeeks,  // §2.10 — was replacement-arrangement weekData (page-specific)
    venueSlots,         // §2.11 — was replacement-arrangement venueSlotData
};

// Compatibility globals — owned by the sibling request-approval chain.
// MockData.approvalRequests aliases the SAME array (no duplicate data).
// DO NOT redeclare in a page script (throws "Identifier already declared").
const approvalRequests = [ /* 20 entries — see request-approval design.md §4 */ ];
const URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00');
window.MockData.approvalRequests = approvalRequests;
window.MockData.urgencyReferenceDate = URGENCY_REFERENCE_DATE;
// (Soft-freeze note 2026-08-01: aliasing lines use `window.MockData.*` not
// bare `MockData.*` — the object is assigned as `window.MockData` above, so
// the bare form fails outside a browser's global context, though the two are
// identical in-browser. Verified via `node --check` + runtime smoke.)
```

### 2.1 `semester` (canonical — fixes drift)
```js
const fmtChip = d => `${String(d.getDate()).padStart(2,'0')}-${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][d.getMonth()]}-${d.getFullYear()}`; // hyphen format, matches existing static chip
semester: {
    label: '202605 Semester',     // chip label, preserved verbatim
    startDate: '2026-08-31',      // Week-1 Monday (was 2026-06-15 on 2 pages)
    endDate: '2026-12-06',        // Week-14 Sunday (start + 13*7 + 6 days)
    weeks: 14,
    chipText: '202605 Semester · 31-Aug-2026 ~ 06-Dec-2026',  // precomputed; pages render this verbatim (see §4 step 4)
}
```
> `chipText` is precomputed inside mock-data.js using `fmtChip` (internal, hyphen-format — matches the existing static chip on all 5 pages). Pages render `MockData.semester.chipText` directly; no per-page formatter and no edit to `ui-common.js` (which belongs to the sibling chain). *(The existing static chip uses hyphens `15-Jun-2026`; the page-local `fmt`/`formatDate` helpers all produce spaces `15 Jun 2026`, so they must NOT be used for the chip — see §4 step 4.)*

Pages that compute weeks from a semester start read `MockData.semester.startDate` only (detect via `new Date(MockData.semester.startDate)`). MyTimetable + CohortTimetable migrate from `new Date(2026, 5, 15)`; ReplacementHome's existing `2026-08-31` already matches.

### 2.2 `holidays` (declarative; consumed by CohortTimetable only)
```js
holidays: [
    { week: 3, dayIndex: 3, label: 'Public Holiday' },  // Week 3 Thursday — was CohortTimetable inline rule `d===3 && w===3`
]
```
- **CohortTimetable** consumes this (its holiday CSS/render path activates).
- **MyTimetable** has no holiday render path today (its `day.holiday` is always undefined — dead conflict branch); this change does **not** add one. Explicit non-goal.
- **Replacement-arrangement**'s holiday (`04-Sep Thu`, inside `arrangementWeeks`) stays embedded in that page-specific dataset — NOT duplicated into `holidays` (avoids double-sourcing).

### 2.3 `cohorts` — mirrors `dataset/cohorts.md` + CodingMAIN §8
14 entries: `{ code, programme, year, semester, group, faculty, studentCount }`. Codes use the dataset's own format (DFT/DSF omit the `G1` suffix, RSD/RAF/RBU include it). `studentCount` from CodingMAIN §8 / `DatabaseSeeder::STUDENT_COUNTS`.
> Note: these are a **registry** for future Sprint-1 seed alignment and for any cohort-dropdown UI. Existing page event/request `cohort` fields (e.g. `CSF2 (S1)`, `DFT2 (S1)`) are free-text display strings and are **not** joined to this registry (fidelity rule).

### 2.4 `lecturers` — mirrors `dataset/lecturers.md`
14 entries: `{ staffId, name, role, department, isPl }`. `isPl` derivation: `role === 'Programme Leader'` → rows 5425 (Pn. Surayaini Binti Basri) and 5516 (En. Mohd Nur Rahmat Bin Mohd Taat). 
> Note: registry only; page event `lecturer` fields (e.g. `Dr. Christopher Lazarus`, `En. Lim Jia Zheng`, `Dr. Tan Ah Meng`) are free-text display strings, not joined.

### 2.5 `venues` — mirrors CodingMAIN §3 (23 Block B rooms)
23 entries: `{ code, type, capacity, allowedSessions }`. `type` ∈ `Tutorial` (16: B002, B014–B018, B100–B109), `LectureHall` (B110, B111), `Lab` (B005, B009–B011), `CiscoLab` (B006). `allowedSessions` per CodingMAIN §3.
> Note: page event `venue` fields reference rooms outside this registry (e.g. `A101`–`A106`, `B201`–`B205`, `B301`–`B305`, `C201`–`C202`) — free-text display strings, not joined.

### 2.6 `myTimetable` (was MyTimetable inline)
Two fields preserve the page's existing access pattern:
```js
myTimetable: {
    seedWeek: 11,                 // the week defined with full events
    eventsByWeek: { 11: [ /* 12 events, verbatim */ ] },
    // weekly-repeat rule: every other week copies seedWeek's status==='normal' events
}
```
Event shape (verbatim): `{ di, start, end, code, type, venue, lecturer, cohort, studentCount?, cohorts?, studentCounts?, status, name, remarks, requestedAt?, requestedBy? }`.

**Copy semantics (critical):** MyTimetable currently mutates `eventsData[i] = weeklyTemplate.slice()` to populate weeks 0..10,12,13. Post-refactor the page MUST derive a **local** `eventsByWeek` copy before mutating — `MockData` is shared read-only. Page-side pattern:
```js
const seedEvents = MockData.myTimetable.eventsByWeek[MockData.myTimetable.seedWeek];
const weeklyTemplate = seedEvents.filter(e => e.status === 'normal');
const eventsByWeek = {};
const sw = MockData.myTimetable.seedWeek;
for (let i = 0; i < MockData.semester.weeks; i++) {
    eventsByWeek[i] = (i === sw) ? seedEvents.slice() : weeklyTemplate.slice();
}
```
> `.slice()` (shallow) suffices *because* MyTimetable's render path only reassigns week-array slots (`eventsByWeek[i] = …`), never mutates individual event fields (`event.status = …` etc. never occurs at L699–963). Any future logic that mutates event fields would need `seedEvents.map(e => ({...e}))` instead.

### 2.7 `cohortTimetable` (was CohortTimetable inline)
Preserves the page's cohort→week access pattern:
```js
cohortTimetable: {
    faculties: [ /* facultyData verbatim: { id, name, cohorts:[{id,name}] } */ ],
    // events as a flat list the page re-indexes into allEvents[cohortId][weekIdx]
    events: [
        { cohortId: 'rsd2s1', week: 0, event: { di,start,end,code,type,venue,lecturer,status,name,remarks,requestedAt?,requestedBy? } },
        // ...all addEvent() calls flattened verbatim...
    ],
    // RSD3 G2 special: base array + flag overrides (verbatim)
    rsd3g2Base: [ /* 7 events */ ],
    rsd3g2Flags: { 1:[[...],...], 2:[[...],...], /* verbatim */ },
}
```
The page rebuilds `allEvents` from `MockData.cohortTimetable.events` (loop + push into `allEvents[cid][wk]`), then applies the rsd3g2 base+flags exactly as today. Holiday rule `holiday: d===3 && w===3` becomes a read from `MockData.holidays`.
> **RSD3 G2 disambiguation:** `MockData.cohortTimetable.events` contains **only** the direct `addEvent()` calls for the *non*-rsd3g2 cohorts (rsd2s1, rsd3s1g1, dsf2s1, dft2s1, dmc2s1, dit2s1 across weeks 0–2). The rsd3s1g2 cohort is reconstructed **only** via `rsd3g2Base` + `rsd3g2Flags` (matching the original `for (let w=0; w<14; w++) rsd3g2Base.forEach(...)` loop at source L734–736) — it must NOT also be flattened into `events`, or every RSD3 G2 week would be double-added (14 events instead of 7).

### 2.8 `requests` (was my-request-history `mockRequests`)
Full verbatim array (rich shape incl. `id, requestedAt, courseCode, courseName, classType, classDate, classDay, timeStart, timeEnd, duration, venue, totalStudents, cohortCounts?, cohorts[], status, rejectionReason, replacementDate, replacementTime, replacementVenue, reviewedBy, reviewedAt, remarks` — note `id` is the first field of every entry). my-request-history reads `MockData.requests`; the sibling request-approval page keeps reading its own `approvalRequests` global (deliberately distinct dataset per the frozen sibling chain).

### 2.9 `conflictedClasses` (was replacement-home inline)
14-entry verbatim array, shape `{ id, code, name, type, date, day, timeStart, timeEnd, duration, venue, totalStudents, cohorts[], conflictReason }`.

### 2.10 `arrangementWeeks` (was replacement-arrangement `weekData`) — page-specific
3-entry verbatim array (Week 11/10/9 **descending**), shape `{ label, days:[{abbr,date,holiday?}] }`. **NOT** standardised to `MockData.semester` — this page shows a fixed demo window; its day-label quirks (e.g. `01 Sep` labelled `Mon` which is actually a Tuesday) are preserved per the fidelity rule and documented here as known mock-data quirks. Fixing them is out of scope.

### 2.11 `venueSlots` (was replacement-arrangement `venueSlotData`)
Verbatim object keyed by venue code, values are arrays of `[dayIndex, hourIndex, statusInt]` triples.

## 3. Layout wiring

`resources/views/layouts/ui-template.blade.php` — add one line after the existing `ui-common.js` include (idempotent):
```blade
<script src="/js/ui-common.js"></script>
<script src="/js/mock-data.js"></script>   {{-- NEW (or already present if request-approval applied first) --}}
```
Tag placement **after** `ui-common.js` matches the sibling contract (order is functional either way since `mock-data.js` is data-only and depends on nothing; chosen for cross-chain consistency).

## 4. Per-page refactor mechanics (uniform)

For each of the 5 templates:
1. Remove the inline data array/`const`/IIFE block.
2. Replace with a read from `MockData.*` (assigning to a **page-local const of the same name** where the existing render code references it, to minimise render-code churn — e.g. `const eventsData = MockData.myTimetable.eventsByWeek` then the existing `eventsData[11]` reads still work).

> Exception: where a page previously mutated the shared object (MyTimetable `eventsData[i] = ...`), the page-local alias must be a **deep-enough copy**. For MyTimetable specifically, do NOT alias directly — derive the per-week map via the pattern in §2.6.

3. Week-range generator: keep the `weekData` IIFE in the page script, but read the start date from `MockData.semester.startDate` (replace `new Date(2026, 5, 15)` → `new Date(MockData.semester.startDate)`). Applies to MyTimetable (L640/L686) and CohortTimetable (L625/L807). Not promoted to `ui-common.js` (that file belongs to the sibling request-approval chain's helper-promotion task).
   > **ReplacementHome** has no `weekData` IIFE — it computes weeks on-the-fly via `computeWeek()` (L353) and `weekRangeLabel()` (L360), each containing the literal `new Date('2026-08-31')`. Since `'2026-08-31' === MockData.semester.startDate`, these literals may be left untouched (no behavioural change) **or** refactored to `new Date(MockData.semester.startDate)` for consistency — either is acceptable.
4. Semester chip: replace the static `<span class="semester-chip">202605 Semester · 15-Jun-2026 ~ 20-Sep-2026</span>` with:
```blade
<span class="semester-chip" id="semesterChip"></span>
```
and in the page script set the precomputed string from mock-data.js (renders identical hyphen-format on all 5 pages; do NOT use the page-local `fmt`/`ui-common formatDate` — they produce spaces):
```js
document.getElementById('semesterChip').textContent = MockData.semester.chipText;
```
Net visible effect: all 5 chips show `202605 Semester · 31-Aug-2026 ~ 06-Dec-2026` — the only chip-content change.

## 5. Dev-date / "today" clamp behaviour (note for implementers)

With the baseline moved to `2026-08-31`, a developer viewing the page on a real date **before** the semester start (e.g. today, 2026-08-01) computes a negative week index. MyTimetable and CohortTimetable already clamp via `Math.max(0, Math.min(weekData.length-1, idx))`, so the default selected week becomes **Week 1**. This is correct and intended — do **not** "fix" the clamp; it is the existing behaviour, just now anchored to the canonical start.

## 6. Dependencies & sequencing

- **Depends on:** nothing new. Reuses the existing `ui-template.blade.php` layout and the `ui-common.js` helpers already present.
- **Coordinates with (frozen sibling `request-approval`):**
  - Same file (`public/js/mock-data.js`), same layout line, after `ui-common.js`.
  - This change owns `window.MockData.*` + the `semester`/`holidays`/`cohorts`/`lecturers`/`venues`/page-data sections.
  - Sibling owns the bare globals `approvalRequests` + `URGENCY_REFERENCE_DATE` (its 20-entry dataset). This change adds `MockData.approvalRequests`/`MockData.urgencyReferenceDate` as aliases to the same values — no data duplication, sibling page works unchanged.
  - **Apply order:** either order is safe — mock-data.js tasks in both changes "merge, never overwrite" (sibling Task 5 guard; this change's tasks guard likewise). If this change applies first, the sibling's Task 5 sees the file already present and only ensures its globals exist.
  - `ui-common.js` helper promotion (sibling's 10 helpers) is the sibling's responsibility; this change does not edit `ui-common.js`.

## 7. Verification strategy

- `php artisan serve`; load each of the 5 routes in turn: `/my-timetable-ui`, `/cohort-timetable-ui`, `/replacement-home-ui`, `/replacement-arrangement`, `/my-request-history-ui`.
- For each: confirm tables/grids render with the same rows as before, no console errors, theme toggle / nav / week picker / filters / sort / pagination / modal still work.
- Visually compare against the existing screenshots in the project root (`my-timetable-*.png`, `cohort-week3-pending-check.png`, etc.) — expect identical layout; only MyTimetable/CohortTimetable week-date labels + all 5 semester chips change.
- Sibling page (`/request-approval-ui`, not yet built) is unaffected by this change.

## 8. Risks & mitigations

| Risk | Mitigation |
|------|-----------|
| Copy-semantics regression (MyTimetable mutation) | §2.6 mandates a local per-week copy; verified via the existing week-persistence screenshot |
| Sibling contract drift | Compatibility globals + aliasing + sibling Task 5 guard; verified by reviewer Round 3 |
| Hidden second consumer of a removed inline var | Each refactor keeps a page-local alias of the same name where the render code reads it → zero render-code churn |
| Arrangement day-label quirks mistaken for new bugs | Documented in §2.10 as preserved; out of scope to fix |
