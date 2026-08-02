/sdd-propose

Enhance the UX of an existing frontend-only UI page for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §9 Page Inventory, §10.0 UI Design Rules — now 9 rules).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this page.
- prompts/sdd-propose-ui-page.md — the spec to follow (adapt for enhancement, not new page).
- resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php — the existing page to enhance.
- page-changelogs/replacement-home-changelog.md — learn house style + existing changes.
- public/css/theme.css — shared CSS tokens (read-only, reuse existing classes).
- public/js/mock-data.js — shared mock data (window.MockData).

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply to this page (FR 2.3 select class block, FR 2.6 weekly timetable grid, NFR 3.1 responsive, NFR 3.2 colour-coded) and tell me if each one is logical for the mock phase.
2. Confirm the 4 UX features with me:
   - F1: Rows Per Page Selector (10/25/50/All) — bottom-left of table
   - F2: Filter Presets (localStorage) — auto-remember last filter state, Reset clears localStorage
   - F5: Keyboard Shortcuts — Arrow keys navigate pagination, Enter opens arrangement page for focused row
   - F6: Quick View Modal — click any row → open modal with full class details
3. Wait for my OK on (1) and (2) before writing the SDD proposal/design/tasks.

Page to enhance
- Name: Replacement Home UX Features
- Template file: resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php (EXISTING — enhance, do not recreate)
- Route: /replacement-home-ui (EXISTING — no route changes needed)
- activeNav: replacement-arrangement (UNCHANGED)
- Primary user / role: Lecturer (view-only with arrange action)
- Purpose: Enhance UX with 4 features: rows-per-page selector, filter presets via localStorage, keyboard shortcuts, and quick view modal for class details.

Design rules: follow CodingMAIN.md §10.0 exactly (color tokens only; same name+same color per the canonical legend/status→color map; OOP @extends/@include/shared theme.css+ui-common.js+mock-data.js; mock data in mock-data.js NOT inline; icon over text; detail/secondary info in modals; on 3rd duplication promote element to shared partial/theme.css/ui-common.js/mock-data.js and refactor existing pages too; minimise steps/fewest clicks; confirm critical actions with a popup). Note: this page has NO critical actions (view-only dashboard with arrangement navigation), so rule #8 does not apply.

Enhancement details (4 features):

F1: Rows Per Page Selector
- Add `<select>` dropdown with options: 10 / 25 / 50 / All
- Position: bottom-left of table, next to pagination info ("Showing X of Y results")
- State: `let rowsPerPage = 10;` (default 10), `let currentPage = 1;`
- On change: update `rowsPerPage`, reset `currentPage = 1`, re-render table
- CSS: reuse `.filter-select` class from existing toolbar dropdowns
- JS: add `renderPaginationControls()` function to build dropdown + info text
- Reference: same implementation as my-request-history-ux-features F1

F2: Filter Presets (localStorage)
- On ANY filter change (search input, week dropdown), save the current filter state to localStorage key `'rh-filters'`
- On page load (DOMContentLoaded), restore saved filters from localStorage (if exists)
- Reset Filters button: clears localStorage entry AND resets all filters to defaults
- No UI for "saved presets" — just auto-remember last state
- State shape: `{ search: '', week: 'all' }`
- JS: `saveFilters()`, `loadFilters()`, call `saveFilters()` in every filter change handler, call `loadFilters()` in DOMContentLoaded before `buildTable()`
- Reference: same implementation as my-request-history-ux-features F5

F5: Keyboard Shortcuts
- Arrow Left / Arrow Right: navigate pagination (prev/next page)
- Arrow Up / Arrow Down: move focus between rows in the table
- Enter: open the arrangement page for the focused row (call `goToReplacementWith(code, date)`)
- Escape: clear focus / close any open modal
- Show keyboard shortcut hint in toolbar (small text: "↑↓ navigate · ←→ paginate · Enter arrange")
- Implementation: add `keydown` event listener on document, track `focusedRowIndex` state
- CSS: `.row-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }` for visual focus indicator
- Reference: same implementation as my-request-history-ux-features F7

F6: Quick View Modal
- Click ANY row (not just the "Arrange Replacement" button) → open a detail modal
- Modal shows full class details:
  - Course Code & Name
  - Class Type (Lecture/Tutorial)
  - Date, Day, Time, Duration
  - Venue
  - Total Students
  - Affected Cohort(s)
  - Conflict Reason (with badge)
  - Days Left (with urgency colour)
  - Semester Week
- Modal footer: "Arrange Replacement" button (primary) + "Close" button (outline)
- "Arrange Replacement" button calls `goToReplacementWith(code, date)` and closes modal
- CSS: reuse existing `.modal-overlay`, `.modal`, `.modal-header`, `.modal-body`, `.modal-footer` classes from theme.css
- JS: `openQuickView(index)` — builds modal HTML from `currentFiltered[index]`, shows overlay
- Reference: similar to my-request-history-UX modal pattern, but for class details instead of request details

Mock data adjustments:
- Current mock data is in `MockData.conflictedClasses` (already centralized in mock-data.js)
- NO changes to mock-data.js needed — all 4 features work with existing data structure
- The `id` field may need to be added to each entry for keyboard navigation tracking (check if already present)

Promote-on-3rd-duplication: while building, if any markup block / CSS class / JS helper ends up the SAME across 3+ pages, promote it to a shared file (partial `resources/views/partials/`, `theme.css`, `ui-common.js`, or `mock-data.js`) and replace the inline copies in the new page AND existing pages with @include / var() / helper() / MockData.*. List every promotion in design.md under "Promoted to shared".

Changelog (generate BEFORE the proposal, keep updating as you build):
- Update `page-changelogs/replacement-home-changelog.md` (ALREADY EXISTS — do NOT create new file).
- Add new entries under `### \`resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php\`` for each feature.
- Follow the exact format: `| Timestamp | Location | Change | Detail |` with server-local ISO-ish timestamps.
- Log EVERY change: CSS additions, JS function additions, HTML modifications.

Deliverables: .sdd/changes/replacement-home-ux-features/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/my-request-history-ux-features/; design.md MUST include a "Promoted to shared" section), the ENHANCED Blade template (render logic in @section('page-scripts'); NO inline mock data — read from window.MockData.*), page-changelogs/replacement-home-changelog.md (UPDATED with new entries). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: ui:.

Constraints: no migrations/models/backend logic (frontend mock phase); no new dependencies; no new files (enhance existing template only); keep under ~1500 lines total (current: 623 lines, adding ~200 lines for 4 features = ~823 lines).
