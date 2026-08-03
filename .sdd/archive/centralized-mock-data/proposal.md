# Proposal: Centralized Mock Data Module (`public/js/mock-data.js`)

## Why This Change Is Needed

The 5 UI design templates currently embed duplicated, inconsistent mock data inline in their `@section('page-scripts')` blocks (e.g., `facultyData`, `eventsData`, `mockRequests`, `venueSlotData`, `weekData`, conflicted-class rows). Each page carries its own copy of week-range logic and dates, which has already produced drift: MyTimetable and CohortTimetable compute semester weeks from `2026-06-15` while Replacement Home uses `2026-08-31`, and Replacement Arrangement uses a third hardcoded window. Any data correction (a cohort name, a venue capacity, a class time) must be applied in multiple files by hand, violating the project's DRY/shared-module convention (`theme.css`, `ui-common.js`).

This change creates a single source of truth for all JS-rendered frontend mock data: one `public/js/mock-data.js` defining `window.MockData`, loaded once by the shared layout, and consumed by every template. It follows the established shared-module pattern, mirrors the real seed datasets (`dataset/cohorts.md`, `dataset/lecturers.md`, the fixed student-count table in CodingMAIN.md §8 / `DatabaseSeeder::STUDENT_COUNTS`), and is **throwaway-by-design**: when Sprint 3 wires real backend data, pages swap `MockData.*` references for Livewire/DB data with minimal churn.

This is the direct continuation of the frozen `request-approval` change, which already moved its approval mock data into `mock-data.js` and explicitly designated migrating the other pages' data as future work (see "Relationship to the request-approval change" below).

## Scope

### In Scope

**1. New/extended file: `public/js/mock-data.js`**

Defines `window.MockData = { ... }` with these sections:

| Section | Contents | Source |
|---------|----------|--------|
| `semester` | `{ label: '202605 Semester', startDate: '2026-08-31', endDate: '2026-12-06', weeks: 14 }` — canonical week-1 Monday + label/end for the semester chips | canonical constant (fixes drift) |
| `holidays` | declarative list `{ week, dayIndex, label }` (e.g., Week 3 Thursday per CohortTimetable rule `d===3 && w===3`) | extracted from existing inline holiday rules |
| `cohorts` | 14 cohorts: code, programme, year, semester, group, faculty, studentCount | `dataset/cohorts.md` + CodingMAIN.md §8 table |
| `lecturers` | 14 staff: staff_id, name, role, department, is_pl | `dataset/lecturers.md` |
| `venues` | 23 Block B rooms: code, type (Tutorial/Lecture Hall/Lab/Cisco Lab), capacity, allowed sessions | CodingMAIN.md §3 venue matrix |
| `timetable` | per-cohort class events consumed by MyTimetable + CohortTimetable (both access patterns preserved: week-indexed and cohort→week indexed — see design.md) | extracted verbatim from `eventsData` / `facultyData` / `rsd3g2Base` |
| `requests` | my-request-history request list | extracted verbatim from `mockRequests` |
| `approvalRequests` | the request-approval dataset — **same array object as the bare `approvalRequests` compatibility global** (no data duplication) | sibling change's existing data |
| `urgencyReferenceDate` | `2026-08-29` — same value as bare `URGENCY_REFERENCE_DATE` compatibility global | sibling change's existing data |
| `conflictedClasses` | replacement-home table rows | extracted verbatim from replacement-home inline data |
| `arrangementWeeks` | replacement-arrangement's hardcoded 3-week window (18-Aug ~ 07-Sep, labelled Week 9/10/11) — **page-specific, NOT standardized to semester start** | extracted verbatim from `weekData` |
| `venueSlots` | replacement-arrangement venue×time slot matrix | extracted verbatim from `venueSlotData` |

**Fidelity rule:** extraction is a **verbatim copy** of each page's existing data values — no normalization, no renaming, no bug-fixing. Known quirks (e.g., day-label anomalies in `arrangementWeeks`) are preserved as-is and documented in design.md. The only intentional data changes are listed in §4 below.

**Display-name rule:** `cohort`/`lecturer`/`venue` fields inside events/requests are **free-text display strings** extracted verbatim (some reference names not present in the registries, e.g. venues like `A101`, cohort labels like `CSF2 (S1)`). They are **not** joined to the `cohorts`/`lecturers`/`venues` registries. The registries mirror the real dataset purely for future Sprint 1 alignment and for pages that consume them directly (e.g., cohort dropdown options).

**Module display names:** there is **no `modules` registry** — module code + name remain per-event display fields, extracted verbatim. (Cross-page code→name conflicts observed in the existing mock pages are intentional per-page mock differences, not data to unify.)

**Copy semantics:** `MockData` is **read-only shared state**. Pages must not mutate it; where a page previously mutated a shared array (e.g., MyTimetable's `eventsData[i] = weeklyTemplate.slice()`), it now derives a local copy (`slice()` / spread) before mutating. This prevents cross-page contamination.

**2. Layout wiring: `resources/views/layouts/ui-template.blade.php`**

Add `<script src="/js/mock-data.js"></script>` **immediately after** `<script src="/js/ui-common.js"></script>` (matching the sibling request-approval contract; both load before page inline scripts). If the tag already exists (request-approval applied first), no change needed.

**3. Refactor all 5 templates to read from `MockData.*`**

- `MyTimetable-UI-design-template.blade.php`
- `CohortTimetable-UI-design-template.blade.php`
- `replacement-home-UI-design-template.blade.php`
- `replacement-arrangement-UIdesign-template.blade.php`
- `my-request-history-UI-design-template.blade.php`

Each page's inline data arrays are removed and replaced with reads from the corresponding `MockData` section. Pure render/sort/filter/week-range computation stays in page scripts. Pages deriving weeks from a semester start (MyTimetable, CohortTimetable, ReplacementHome) standardize on `MockData.semester.startDate`. `ui-common.js` is **not** touched by this change (the 10-helper promotion there belongs to the request-approval chain).

**4. Intentional data corrections (complete list — nothing else changes visually)**

1. Week baseline of MyTimetable + CohortTimetable changes from `2026-06-15` to `2026-08-31` (canonical semester start). Default-week/"today" highlight changes accordingly.
2. The static semester chip on all 5 pages (`202605 Semester · 15-Jun-2026 ~ 20-Sep-2026`) becomes **dynamic**, rendered from `MockData.semester` (label text `202605 Semester` preserved verbatim; dates render as 31-Aug-2026 ~ 06-Dec-2026).

Everything else renders exactly as today.

**5. Documentation updates**

- Per-page changelog entries in `page-changelogs/` for each of the 5 affected templates
- `CodingMAIN.md` — §10 conventions (add `mock-data.js` to the shared-modules list) and §13 key-files map
- `mock-data.js` section map documented in design.md (what lives where, for future maintainers)

### Not In Scope

- No backend/database wiring (Sprint 3; this file is deleted/absorbed when real data arrives)
- No visual/UX redesign — same layout, same colors, same behavior (except the two items in §4)
- No new pages (request-approval remains its own SDD change; it continues to consume the compatibility globals)
- No changes to `theme.css` or `ui-common.js`
- No changes to `dataset/cohorts.md` / `dataset/lecturers.md` / the seeder
- No normalization of per-page mock display strings (fidelity rule above)
- No fixing of known data quirks beyond §4 (documented, not fixed)

## Relationship to the request-approval change (frozen chain)

`.sdd/changes/request-approval/` is a **frozen** chain (re-frozen 2026-08-01) that already:
- defines `public/js/mock-data.js` with bare globals `approvalRequests` (20 approval entries) + `URGENCY_REFERENCE_DATE`,
- loads the file in the layout **after** `ui-common.js`,
- explicitly designates migrating other pages' inline mock data into `mock-data.js` as **future work** — which this change is.

**Merged contract (this change adopts it):**
1. `mock-data.js` always keeps the bare globals `approvalRequests` and `URGENCY_REFERENCE_DATE` (the request-approval page script references them directly and does not redeclare them; the global is named `approvalRequests` per the sibling's rename decision so it does not collide with my-request-history's page-local `const mockRequests`). `MockData.approvalRequests` and `MockData.urgencyReferenceDate` **alias the same values** — no duplicate data.
2. Script-tag position: after `ui-common.js` (identical to the sibling).
3. **Order-robust merge:** if request-approval is applied first, this change **extends** the existing file (preserving its globals); if this change is applied first, request-approval's Task 5 must not overwrite the file. The sibling's tasks.md Task 5 is amended with a guard bullet (unfreeze amendment recorded in its review-log; re-verified in this change's review rounds).

## Impact Scope

**Files touched:**
- **New (or extended if sibling applied first):** `public/js/mock-data.js`
- **Modified:** `resources/views/layouts/ui-template.blade.php` (1 line, idempotent)
- **Refactored:** 5 files under `resources/views/ui-design-templates/`
- **Docs:** 5 `page-changelogs/*.md` + `CodingMAIN.md`
- **Amended (sibling):** `.sdd/changes/request-approval/tasks.md` (Task 5 guard) + its `review-log.md`

**Verification:** all 5 UI routes render with identical layout/behavior (except the §4 date corrections), no console errors, no broken interactions (theme toggle, nav, filters, sort, pagination, modals, week pickers); sibling page unaffected.

**Risk:** low — data-only refactor; pages keep their render logic; verified in-browser page by page.
