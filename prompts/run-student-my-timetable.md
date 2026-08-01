/sdd-propose

Create a new frontend-only UI page for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §9 Page Inventory, §10.0 UI Design Rules).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this page.
- prompts/sdd-propose-ui-page.md — the spec to follow.
- resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php — copy its look.
- resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php — copy its view-only modal pattern.
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
- Mock-data scope: hardcode cohort = RSD3(S1)G2. Reuse the weekData + eventsData + weeklyTemplate pattern from my-timetable-ui, but filter out any event with status === 'cancelled' before render and before counting summaries. Include at least one cancelled entry in the mock to prove it's hidden.

Design rules: follow CodingMAIN.md §10.0 exactly (color tokens only; same name+same color per the canonical legend/status→color map; OOP @extends/@include/shared theme.css+ui-common.js; icon over text; detail in modals).

Copy from MyTimetable-UI-design-template: page header + .semester-chip; .semester-bar week picker (prev/next arrows + week <select>); .grid-wrapper > .grid-scroll > table.timetable grid; .legend-bar with 4 items (Normal Class=--color-secondary, Replacement=--color-primary, Pending=--color-tertiary, Conflict=--color-error); 5 summary cards via @include('partials.ui-summary-bar').

Remove/change:
- Action buttons: remove "Replace Now" + goToReplacement(); remove "Cancel Class" + cancelClass() + #cancelConfirmOverlay.
- Role-only logic: remove all lecturer functions (create/replace/cancel). Modal footer = Close ✕ only, no action buttons.
- Wrong labels: rename 5th card "Teaching Hours" → "Class Hours" (keep the .card-hours class name unchanged).
- Statuses to hide: cancelled classes — filter out before render, NO legend swatch, NO summary card, never counted.
- Cohort: fixed to RSD3(S1)G2 (no cohort selector dropdown); show once in the .semester-chip, drop the per-cell cohort field.
- Nav: reduced student set (Dashboard, My Timetable, Replacement History) via a $navItems param on the SHARED partials/ui-nav-bar.blade.php (DRY — do NOT create a student copy).

Deliverables: .sdd/changes/student-my-timetable-ui/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/cohort-timetable-ui/), the Blade template (mock data inline <script>, no backend wiring), route in routes/web.php, page-changelogs/student-my-timetable-ui-changelog.md. After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: ui:.

Constraints: no migrations/models/backend logic (frontend mock phase); no new dependencies; under ~1500 lines (split into partials if larger).
