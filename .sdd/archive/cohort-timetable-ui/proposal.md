# Proposal: Cohort Timetable UI

## Why

The "Cohort Timetables" nav item has been a placeholder (`href="#"`) since the nav bar was created. Staff users need the ability to view timetables for any cohort across different faculties — similar to how they view their own timetable on the My Timetable page, but filtered by faculty and cohort selection.

## Scope

### In scope

1. **New route** `/cohort-timetable-ui` — returns the new cohort timetable view, passing `['activeNav' => 'cohort-timetables']` as view data
2. **New Blade template** `ui-design-templates/CohortTimetable-UI-design-template.blade.php` — extends `layouts.ui-template` with `@section('title', 'Cohort Timetable — Class Replacement System')`
3. **Faculty + Cohort dropdowns** — cascading select/dropdowns in the page header: selecting a faculty filters the available cohorts. Mock faculties: `Faculty of Computing and Information Technology (FOCS)`, `Faculty of Engineering (FOE)`, `Faculty of Business (FOB)`, `Faculty of Accountancy, Finance and Business (FAFB)`. Each faculty has 2–3 mock cohorts (e.g., FOCS → `DFT2 (S1)`, `DSF2 (S1)`, `DIT2 (S1)`)
4. **Mock timetable data** — hardcoded JS data per faculty+cohort+week combination, following the same structure as MyTimetable's `timetableData` (course code, name, time, venue, lecturer, status). Each week has 5–7 events per cohort across Mon–Fri, 08:00–18:00
5. **Week navigation** — previous/next week arrows + week selector dropdown (consistent with my-timetable-ui), with arrow buttons disabled at boundary weeks via shared `updateWeekArrows()` from `ui-common.js`
6. **Nav bar update** — change `Cohort Timetables` nav item `href` from `#` to `/cohort-timetable-ui`; route returns `view(..., ['activeNav' => 'cohort-timetables'])`
7. **Legend bar** — color-coded legend for timetable cells (normal class, replacement, pending, conflict) — consistent with my-timetable-ui
8. **Summary cards** — use `@include('partials.ui-summary-bar')` with 4 cards: total classes, replacements, pending, conflict — consistent with my-timetable-ui
9. **Search/filter** — toolbar with search input (searches course code, course name, and lecturer) + status filter dropdown (All / Normal / Replacement / Pending / Conflict) — pattern follows replacement-home-ui's toolbar, not my-timetable (which lacks search)

**Initial-load behavior:** On first load, the page shows the timetable for the first faculty's first cohort's current week. If no timetable data exists for the selected faculty+cohort, the grid shows an empty-state message: "No classes scheduled for this cohort in the selected week." If search/filter returns zero results, the grid shows the shared empty-state pattern used by other templates. The dropdowns are independent of the empty state — user can always select any faculty/cohort.

### Out of scope

- Backend/database integration — all timetable data is mock/frontend-only
- Real cohort enrolment data — mock cohort list hardcoded for display
- Multi-week data persistence — week navigation loads static mock data per week
- Role-based access control — the page is accessible via the route without authentication middleware
- Degree/diploma program type filter — the cohort naming convention (e.g., `DFT2` = diploma) already distinguishes program types; a dedicated filter can be added in a future iteration if needed

## Impact Scope

- `routes/web.php` — one new route + one existing nav link href change
- `resources/views/ui-design-templates/` — new file `CohortTimetable-UI-design-template.blade.php`
- `resources/views/partials/ui-nav-bar.blade.php` — update `Cohort Timetables` href to `/cohort-timetable-ui`
- `public/css/theme.css` — no changes expected (shared CSS already covers timetable grid, nav, summary cards, etc.)
- `public/js/ui-common.js` — no changes expected (shared functions already available)
