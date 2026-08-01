/sdd-propose

Create a new frontend-only UI page for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §9 Page Inventory, §10.0 UI Design Rules — now 8 rules).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this page.
- prompts/sdd-propose-ui-page.md — the spec to follow.
- resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php — copy its look.
- resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php — copy its view-only modal pattern.
- public/js/mock-data.js — the single source of truth for mock data (window.MockData). Read its header comment before adding anything.
- page-changelogs/my-timetable-changelog.md — learn house style.

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply to this page (FR 1.2 view timetable, FR 1.4 view-only, FR 1.5 email on updates — but FR 1.5 is backend, defer) and tell me if each one is logical for the mock phase.
2. Confirm the UI design details with me: legend = 4 items (Normal/Replacement/Pending/Conflict), 5 summary cards (Total Classes, Class Hours, Replacements, Pending, Conflicts), view-only modal with Close ✕, week picker, reduced student nav (Dashboard, My Timetable, Replacement History).
3. Wait for my OK on (1) and (2) before writing the SDD proposal/design/tasks.

Page to create
- Name: Student My Timetable
- Template file: resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php
- Route: /student-my-timetable-ui  in routes/web.php, passing $activeNav
- activeNav: my-timetable
- Primary user / role: Student (view-only — FR 1.2, FR 1.4)
- Purpose: A student views their own cohort's weekly timetable. Shows all statuses (Normal, Replacement, Pending, Conflict) EXCEPT cancelled classes, which are fully hidden.
- Mock-data scope: hardcode active cohort = RSD3(S1)G2. READ from window.MockData.* (MockData.myTimetable for events, MockData.semester for the chip + week list, MockData.cohorts if needed); do NOT inline an eventsData/weekData array. Add a new section MockData.studentTimetable = { cancelledFlags: { <weekIdx>: ['<code>'] } } in public/js/mock-data.js (read-only registry) to mark 1–2 classes cancelled; the page filters out any event whose status === 'cancelled' OR whose code is in cancelledFlags for that week, before render and before counting summaries.

Design rules: follow CodingMAIN.md §10.0 exactly (color tokens only; same name+same color per the canonical legend/status→color map; OOP @extends/@include/shared theme.css+ui-common.js+mock-data.js; mock data in mock-data.js NOT inline; icon over text; detail/secondary info in modals; on 3rd duplication promote element to shared partial/theme.css/ui-common.js/mock-data.js and refactor existing pages too; minimise steps/fewest clicks; confirm critical actions with a popup). Note: this page is view-only with NO critical actions, so rule #8 does not apply.

Copy from MyTimetable-UI-design-template: page header + .semester-chip; .semester-bar week picker (prev/next arrows + week <select>); .grid-wrapper > .grid-scroll > table.timetable grid; .legend-bar with 4 items (Normal Class=--color-secondary, Replacement=--color-primary, Pending=--color-tertiary, Conflict=--color-error); 5 summary cards via @include('partials.ui-summary-bar').

Remove/change:
- Action buttons: remove "Replace Now" + goToReplacement(); remove "Cancel Class" + cancelClass() + #cancelConfirmOverlay.
- Role-only logic: remove all lecturer functions (create/replace/cancel). Modal footer = Close ✕ only, no action buttons.
- Wrong labels: rename 5th card "Teaching Hours" → "Class Hours" (keep the .card-hours class name unchanged).
- Statuses to hide: cancelled classes — filter out before render, NO legend swatch, NO summary card, never counted.
- Cohort: fixed to RSD3(S1)G2 (no cohort selector dropdown); show once in the .semester-chip, drop the per-cell cohort field.
- Nav: reduced student set (Dashboard, My Timetable, Replacement History) via a $navItems param on the SHARED partials/ui-nav-bar.blade.php (DRY — do NOT create a student copy).

Promote-on-3rd-duplication: the .legend-bar (Normal Class/Replacement/Pending/Conflict) appears on MyTimetable + CohortTimetable → this student page makes it the 3rd copy. Promote the legend bar to a new Blade partial `resources/views/partials/ui-legend-bar.blade.php` (or a shared class in theme.css) and refactor all three pages to @include it. Record this in design.md under "Promoted to shared". Audit any other element that hits 3 copies and promote it too.

Deliverables: .sdd/changes/student-my-timetable-ui/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/cohort-timetable-ui/; design.md MUST include a "Promoted to shared" section), the Blade template (render logic only in @section('page-scripts'); NO inline mock data — read from window.MockData.*), the new MockData.studentTimetable section added to public/js/mock-data.js, route in routes/web.php, page-changelogs/student-my-timetable-ui-changelog.md. After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: ui:.

Constraints: no migrations/models/backend logic (frontend mock phase); no new dependencies; under ~1500 lines (split into partials if larger).
