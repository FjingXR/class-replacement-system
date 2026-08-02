/sdd-propose

Enhance the UX of an existing frontend-only UI page for the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §9 Page Inventory, §10.0 UI Design Rules — now 8 rules).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this page.
- prompts/sdd-propose-ui-page.md — the spec to follow (adapt for enhancement, not new page).
- resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php — the existing page to enhance.
- page-changelogs/my-request-history-changelog.md — learn house style + existing changes.
- public/css/theme.css — shared CSS tokens (read-only, reuse existing classes).

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply to this page (FR 2.1 view request history, FR 2.2 filter by status, FR 2.3 search requests, FR 2.4 view request details, FR 2.5 cancel pending request) and tell me if each one is logical for the mock phase.
2. Confirm the 6 UX features with me:
   - F1: Rows Per Page Selector (10/25/50/All) — bottom-left of table
   - F2: Bulk Selection + Batch Cancel — checkbox column, only Pending selectable, floating action bar
   - F3: Request Age Indicator — color-code "Requested At" cell (green <3d, amber 3-7d, red >7d)
   - F4: Quick Actions in Rows — hover Pending row → show "Cancel" button in last column
   - F5: Filter Presets (localStorage) — auto-remember last filter state, Reset clears localStorage
   - F6: Status History Timeline in Modal — vertical timeline with dots/lines for request lifecycle
3. Wait for my OK on (1) and (2) before writing the SDD proposal/design/tasks.

Page to enhance
- Name: My Request History UX Features
- Template file: resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php (EXISTING — enhance, do not recreate)
- Route: /my-request-history-ui (EXISTING — no route changes needed)
- activeNav: replacement-history (UNCHANGED)
- Primary user / role: Lecturer (view-only with cancel capability)
- Purpose: Enhance UX with 6 features: rows-per-page selector, bulk selection + batch cancel, request age indicator, quick cancel on hover, filter presets via localStorage, and status history timeline in modal.

Design rules: follow CodingMAIN.md §10.0 exactly (color tokens only; same name+same color per the canonical legend/status→color map; OOP @extends/@include/shared theme.css+ui-common.js+mock-data.js; mock data in mock-data.js NOT inline; icon over text; detail/secondary info in modals; on 3rd duplication promote element to shared partial/theme.css/ui-common.js/mock-data.js and refactor existing pages too; minimise steps/fewest clicks; confirm critical actions with a popup).

Enhancement details (6 features):

F1: Rows Per Page Selector
- Add `<select>` dropdown with options: 10 / 25 / 50 / All
- Position: bottom-left of table, next to pagination info ("Showing X of Y results")
- State: `let rowsPerPage = 10;` (default 10), `let currentPage = 1;`
- On change: update `rowsPerPage`, reset `currentPage = 1`, re-render table
- CSS: reuse `.filter-select` class from existing toolbar dropdowns
- JS: add `renderPaginationControls()` function to build dropdown + info text

F2: Bulk Selection + Batch Cancel
- Add checkbox column as FIRST column (header = "select all" checkbox)
- Only Pending rows are selectable (checkbox disabled + muted for other statuses)
- State: `let selectedIds = new Set();` — tracks selected request IDs
- Floating action bar appears when ≥1 selected: "Cancel Selected (N)" button
- Position: fixed at bottom-center of viewport, above table
- Visual: selected rows get `background: var(--color-primary-container);` (reuse existing token)
- Batch cancel flow: click button → open NEW confirmation modal (not the single-cancel modal) showing list of selected request IDs + course codes → user confirms → alert("X requests cancelled.") → clear selection → close modal
- CSS: `.col-checkbox { width: 40px; text-align: center; }`, `.row-selected { background: var(--color-primary-container); }`, `.batch-action-bar { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); ... }`
- JS: `toggleSelectAll()`, `toggleSelectRow(id)`, `updateBatchBar()`, `openBatchCancelModal()`, `confirmBatchCancel()`

F3: Request Age Indicator
- Color-code the "Requested At" cell based on days since request submission
- Thresholds: Green: <3 days (`var(--color-secondary)`), Amber: 3-7 days (`var(--color-tertiary)`), Red: >7 days (`var(--color-error)`)
- IMPORTANT: Mock dates are in September 2026. To make age calculation meaningful, adjust mock dates to be relative to "today" (use `new Date()` in JS). Example: some requests 2 days ago, some 5 days ago, some 10 days ago.
- Implementation: add `calculateAgeClass(requestedAt)` function that returns 'age-green'|'age-amber'|'age-red'
- Apply in `renderTable()` when building the "Requested At" cell: `<td class="col-requested-at age-${ageClass}">`
- CSS: `.age-green { color: var(--color-secondary); }`, `.age-amber { color: var(--color-tertiary); }`, `.age-red { color: var(--color-error); }` (just text color, no background)

F4: Quick Actions in Rows
- On hover over a Pending row, show a small "Cancel" button in the LAST column area (after Status badge)
- Button styled as `btn-cancel-class` (outline red, 12px font, padding 2px 8px)
- Click triggers the EXISTING `confirmCancelRequest()` flow (single cancel confirmation)
- Only visible for Pending status rows (hidden for other statuses)
- Implementation: add `mouseenter`/`mouseleave` event listeners on each row in `renderTable()`
- CSS: `.btn-quick-cancel { display: none; }`, `tr:hover .btn-quick-cancel { display: inline-block; }` (only for Pending rows via JS class toggle)
- JS: `showQuickCancel(row, requestId)`, `hideQuickCancel(row)`, `quickCancel(requestId)` (calls existing `confirmCancelRequest()` with correct index)

F5: Filter Presets (localStorage)
- On ANY filter change (status dropdown, week dropdown, search input, exclude completed toggle), save the current filter state to localStorage key `'mrh-filters'`
- On page load (DOMContentLoaded), restore saved filters from localStorage (if exists)
- Reset Filters button: clears localStorage entry AND resets all filters to defaults
- No UI for "saved presets" — just auto-remember last state
- State shape: `{ status: 'all', week: 'all', search: '', excludeCompleted: true }`
- JS: `saveFilters()`, `loadFilters()`, call `saveFilters()` in every filter change handler, call `loadFilters()` in DOMContentLoaded before `renderTable()`

F6: Status History Timeline in Modal
- Inside the detail modal, add a "Request Timeline" section AFTER the existing fields
- Vertical timeline with dots + connecting lines:
  - "Request Submitted" — `requestedAt` timestamp (always shown)
  - "Under Review" — always shown, timestamp = `requestedAt + random 1-24 hours` (mock)
  - Final status event (one of):
    - "Approved" — timestamp = `reviewedAt` (if status = Approved/Completed)
    - "Rejected" — timestamp = `reviewedAt` (if status = Rejected)
    - "Cancelled" — timestamp = `reviewedAt` or `requestedAt + 1h` (if status = Cancelled)
- Only shown for non-normal statuses (Pending, Approved, Rejected, Cancelled) — NOT for Completed
- Visual: dots colored by status (Pending=tertiary, Approved=secondary, Rejected=error, Cancelled=surface-variant), lines use `var(--color-outline)`
- CSS: `.timeline-section { margin-top: 24px; }`, `.timeline-item { display: flex; align-items: flex-start; gap: 12px; }`, `.timeline-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }`, `.timeline-line { width: 2px; background: var(--color-outline); flex-shrink: 0; }`, `.timeline-content { flex: 1; }`
- JS: `renderTimeline(request)` — builds timeline HTML, called in `openModal()` after existing fields

Mock data adjustments:
- Current mock data has 20 entries with dates in September 2026. To make F3 (age indicator) meaningful:
  - Keep the `id` field (already present: 1-20)
  - Keep all other fields unchanged
  - NO changes to mock-data.js (mock data stays inline in the Blade template per existing pattern)
  - The age calculation will use `new Date()` in JS — with current dates, all requests will be >7 days (red). This is acceptable for mock phase.

Promote-on-3rd-duplication: while building, if any markup block / CSS class / JS helper ends up the SAME across 3+ pages, promote it to a shared file (partial `resources/views/partials/`, `theme.css`, `ui-common.js`, or `mock-data.js`) and replace the inline copies in the new page AND existing pages with @include / var() / helper() / MockData.*. List every promotion in design.md under "Promoted to shared".

Changelog (generate BEFORE the proposal, keep updating as you build):
- Update `page-changelogs/my-request-history-changelog.md` (ALREADY EXISTS — do NOT create new file).
- Add new entries under `### \`resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php\`` for each feature.
- Follow the exact format: `| Timestamp | Location | Change | Detail |` with server-local ISO-ish timestamps.
- Log EVERY change: CSS additions, JS function additions, HTML modifications, mock data adjustments.

Deliverables: .sdd/changes/my-request-history-ux-features/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/my-request-history/; design.md MUST include a "Promoted to shared" section), the ENHANCED Blade template (render logic in @section('page-scripts'); NO inline mock data — read from window.MockData.*), page-changelogs/my-request-history-changelog.md (UPDATED with new entries). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: ui:.

Constraints: no migrations/models/backend logic (frontend mock phase); no new dependencies; no new files (enhance existing template only); keep under ~1500 lines total (current: 800 lines, adding ~200 lines for 6 features = ~1000 lines).
