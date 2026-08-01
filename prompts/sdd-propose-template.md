# Reusable /sdd-propose template for a NEW UI frontend-only page

> Copy this template each time you start a new UI page. Replace only the `[ ]` brackets.
> The agent auto-loads `AGENTS.md` (which already points to `CodingMAIN.md` + the
> `prompts/sdd-propose-ui-page.md` spec), so you do NOT need to repeat all 5 UI rules.
> Just tell the agent which page to copy and which parts to remove.

---

## Anatomy of the prompt (7 parts — always include all seven)

| # | Part | What it does |
|---|------|--------------|
| 1 | **Command + goal** | `/sdd-propose`, then one line: "create a new frontend-only UI page". |
| 2 | **Read-first list** | CodingMAIN.md (esp. §9 Page Inventory, §10.0 UI Design Rules), `../final/FR&NFR.md`, the spec file, the closest existing page + its changelog. |
| 3 | **Discuss before generating** | After reading FR&NFR.md, talk to the user: confirm which FRs/NFRs apply, check if the logic is sound, and agree on the UI design details (legend, cards, modal, what to remove). Only then write the proposal. |
| 4 | **Page details** | 6 fields: name, file path, route, activeNav, role, purpose, mock scope. |
| 5 | **Design rules** | Point to the 5 rules in §10.0 (do not rewrite them — say "follow §10.0 exactly"). |
| 6 | **What to copy / what to remove** | Which existing page to copy the look from; which parts to delete (buttons, lecturer logic, renamed labels). |
| 7 | **Deliverables + constraints** | 4 SDD files, route, changelog, lint check, commit prefix. |

The tricky parts are: (1) picking the **closest page**, (2) listing **what to remove**, and (3) checking FR/NFR logic.
Ask yourself: *what does the closest page have that my page should NOT have?* Delete those.
For FR/NFR: read `../final/FR&NFR.md`, list every FR/NFR that touches this page, and ask the user whether each one is logical for the mock phase.

---

## Template (fill the `[ ]` brackets)

```
/sdd-propose

Create a new frontend-only UI page for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §9 Page Inventory, §10.0 UI Design Rules).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this page.
- prompts/sdd-propose-ui-page.md — the spec to follow.
- resources/views/ui-design-templates/[CLOSEST EXISTING PAGE].blade.php — copy its look.
- page-changelogs/[CLOSEST PAGE]-changelog.md — learn house style.

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply to this page and tell me if each one is logical for the mock phase (flag any contradiction or anything that needs backend, which we defer).
2. Confirm the UI design details with me: legend items + colors, summary cards, modal pattern, week picker, what to copy and what to remove.
3. Wait for my OK on (1) and (2) before writing the SDD proposal/design/tasks.

Page to create
- Name: [PAGE NAME]
- Template file: resources/views/ui-design-templates/[kebab-case]-UI-design-template.blade.php
- Route: /[kebab-route]-ui  in routes/web.php, passing $activeNav
- activeNav: [dashboard | my-timetable | cohort-timetables | replacement-arrangement | replacement-history | request-approval]
- Primary user / role: [Student | Lecturer | Programme Leader]
- Purpose: [1-2 sentences what it does]
- Mock-data scope: [what mock JS objects / columns / cards it needs]

Design rules: follow CodingMAIN.md §10.0 exactly (color tokens only; same name+same color per the canonical legend/status→color map; OOP @extends/@include/shared theme.css+ui-common.js+mock-data.js; mock data in mock-data.js NOT inline; icon over text; detail in modals; **on 3rd duplication promote element to shared partial/theme.css/ui-common.js/mock-data.js and refactor existing pages too**).

Copy from [CLOSEST PAGE]: [list the parts to reuse — page header, legend, grid, summary cards, modal pattern, week picker, etc.].
Remove/change: [list parts to delete — action buttons, lecturer-only logic, renamed labels, statuses to hide].

Mock data: read from window.MockData.* in the page's @section('page-scripts') (do NOT inline). If the page needs NEW mock data, add ONE section to public/js/mock-data.js (e.g. MockData.[pageName] = {...}) and reference it. Treat MockData as read-only; slice()/spread before mutating.

Promote-on-3rd-duplication: while building, if any markup block / CSS class / JS helper ends up the SAME across 3+ pages, promote it to a shared file (partial `resources/views/partials/`, `theme.css`, `ui-common.js`, or `mock-data.js`) and replace the inline copies in the new page AND existing pages with @include / var() / helper() / MockData.*. List every promotion in design.md under "Promoted to shared".

Deliverables: .sdd/changes/[change-name]/ (sdd.yaml, proposal.md, design.md, tasks.md — model the format on .sdd/changes/; design.md MUST include a "Promoted to shared" section listing any element moved out on 3rd duplication), the Blade template (render logic only in @section('page-scripts'); no inline mock data), route in routes/web.php, page-changelogs/[change-name]-changelog.md. After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: ui:.

Constraints: no migrations/models/backend logic (frontend mock phase); no new dependencies; under ~1500 lines (split into partials if larger).
```

---

## Worked example — Student My Timetable (reference, do not reuse as-is)

```
/sdd-propose

Create a new frontend-only UI page for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §9 Page Inventory, §10.0 UI Design Rules).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this page.
- prompts/sdd-propose-ui-page.md — the spec to follow.
- resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php — copy its look.
- resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php — copy its view-only modal pattern.
- public/js/mock-data.js — the single source of truth for all mock data (window.MockData).
- page-changelogs/my-timetable-changelog.md — learn house style.

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply to this page (e.g. FR 1.2 view timetable, FR 1.4 view-only, FR 1.5 email on updates — but email is backend, defer) and tell me if each one is logical for the mock phase.
2. Confirm the UI design details with me: legend 4 items (Normal/Replacement/Pending/Conflict), 5 summary cards (Total Classes, Class Hours, Replacements, Pending, Conflicts), view-only modal with Close button, week picker, remove Replace Now + Cancel Class.
3. Wait for my OK on (1) and (2) before writing the SDD proposal/design/tasks.

Page to create
- Name: Student My Timetable
- Template file: resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php
- Route: /student-my-timetable-ui  in routes/web.php, passing $activeNav
- activeNav: my-timetable
- Primary user / role: Student (view-only — FR 1.2, FR 1.4)
- Purpose: A student views their own cohort's weekly timetable. Shows all statuses (Normal, Replacement, Pending, Conflict) EXCEPT cancelled classes, which are fully hidden.
- Mock-data scope: read from window.MockData.* — reuse MockData.myTimetable, MockData.cohorts, MockData.semester, MockData.holidays. Hardcode the active cohort = RSD3(S1)G2. Before render and before counting summaries, filter out any event with status === 'cancelled'.

Design rules: follow CodingMAIN.md §10.0 exactly (color tokens only; same name+same color per the canonical legend/status→color map; OOP @extends/@include/shared theme.css+ui-common.js+mock-data.js; mock data in mock-data.js NOT inline; icon over text; detail in modals; on 3rd duplication promote element to shared partial/theme.css/ui-common.js/mock-data.js and refactor existing pages too).

Copy from MyTimetable-UI-design-template: page header + .semester-chip; .semester-bar week picker (prev/next arrows + week <select>); .grid-wrapper > .grid-scroll > table.timetable grid; .legend-bar with 4 items (Normal Class=--color-secondary, Replacement=--color-primary, Pending=--color-tertiary, Conflict=--color-error); 5 summary cards via @include('partials.ui-summary-bar').
Remove/change: remove the "Replace Now" button and goToReplacement(); remove the "Cancel Class" button, cancelClass(), and #cancelConfirmOverlay; rename "Teaching Hours" card to "Class Hours". Cancelled = NO legend item, NO summary card, never counted.

Mock data: read from window.MockData.myTimetable (and MockData.semester/cohorts/holidays) in the page's @section('page-scripts') — do NOT inline an eventsData array. Derive a local per-week copy (slice) before mutating; treat MockData as read-only.

Promote-on-3rd-duplication: the .legend-bar (Normal Class/Replacement/Pending/Conflict) already appears on MyTimetable + CohortTimetable → this student page makes it the 3rd copy. Promote the legend bar to a new Blade partial `resources/views/partials/ui-legend-bar.blade.php` (or a shared class in theme.css) and refactor all three pages to @include it. Record this in design.md under "Promoted to shared".

Deliverables: .sdd/changes/student-my-timetable-ui/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/cohort-timetable-ui/; design.md MUST include a "Promoted to shared" section), the Blade template (render logic only in @section('page-scripts'); no inline mock data), route in routes/web.php, page-changelogs/student-my-timetable-changelog.md. After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: ui:.

Constraints: no migrations/models/backend logic (frontend mock phase); no new dependencies; under ~1500 lines (split into partials if larger).
```

---

## How to pick the [CLOSEST EXISTING PAGE]
Look in `resources/views/ui-design-templates/`. Pick the one that looks most like your new page:
- timetable view with week picker → `MyTimetable-UI-design-template`
- cohort-viewer with cascading dropdowns → `CohortTimetable-UI-design-template`
- searchable/sortable data table → `replacement-home-UI-design-template` or `my-request-history-UI-design-template`
- slot-selection grid with venue dropdown → `replacement-arrangement-UIdesign-template`

If unsure, name two — the agent will read both and pick the best fit.
