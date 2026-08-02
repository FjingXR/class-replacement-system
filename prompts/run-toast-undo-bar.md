/sdd-propose

Add a shared toast/undo bar component for critical actions across the TARUMT Class Replacement System.

Read first (mandatory):
- CodingMAIN.md — single source of truth (esp. §10.0 UI Design Rules — rule #10 toast/undo bar).
- ../final/FR&NFR.md — the requirements; find every FR/NFR that touches this page.
- prompts/sdd-propose-ui-page.md — the spec to follow.
- public/css/theme.css — shared CSS (read-only, add new toast styles here).
- public/js/ui-common.js — shared JS helpers (read-only, add new showToast() here).
- resources/views/layouts/ui-template.blade.php — shared layout (add toast HTML here).
- resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php — has bulk-action-bar (reference for position) + 3 critical actions (single cancel, batch cancel, quick cancel).
- resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php — has 1 critical action (cancel class).
- resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php — has 2 critical actions (submit request, clear ALL).
- page-changelogs/my-request-history-changelog.md — learn house style.

Discuss with me BEFORE you generate the proposal (do not skip):
1. List the FRs/NFRs that apply to this toast/undo bar (FR 3.5 approve with one click, FR 3.6 reject with mandatory reason, FR 2.5 cancel pending request — all need toast feedback after action). Tell me if each one is logical for the mock phase.
2. Confirm the toast design details with me:
   - Position: bottom-left (same as `.bulk-action-bar`: `position: fixed; bottom: 24px; left: 24px; z-index: 200`)
   - Styling: matches `.bulk-action-bar` (surface bg, outline border, shadow, radius-md, padding 10px 16px)
   - Content: success message text + Undo button + close ✕ button
   - Undo button: outline primary, hover fills primary
   - Auto-dismiss: 5 seconds (clearTimeout on manual close)
   - Animation: slideUp 0.3s ease
   - JS helper: `showToast(message, undoCallback, duration=5000)` in ui-common.js
   - HTML: `<div class="toast-bar" id="toastBar">` added to layouts/ui-template.blade.php before `</body>`
3. Confirm the 6 critical actions to add toast/undo to:
   - my-request-history: single cancel (quickCancel) — "Request #{id} cancelled."
   - my-request-history: batch cancel (batchCancelSelected) — "{N} requests cancelled."
   - MyTimetable: cancel class (cancelClass) — "Class cancelled."
   - replacement-arrangement: submit request (proceed) — "Replacement request submitted."
   - replacement-arrangement: clear ALL (clearAll) — "All selections cleared."
   - replacement-arrangement: navigate away with unsaved (navigateTo) — "Selections cleared."
4. Wait for my OK on (1), (2), and (3) before writing the SDD proposal/design/tasks.

Page to create
- Name: Toast/Undo Bar (shared component)
- NOT a new page — adding shared CSS + JS + HTML to existing shared files
- Files to modify:
  - public/css/theme.css — add .toast-bar styles
  - public/js/ui-common.js — add showToast() + dismissToast() helpers
  - resources/views/layouts/ui-template.blade.php — add toast HTML before </body>
  - resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php — add toast calls to 3 cancel actions
  - resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php — add toast call to cancelClass()
  - resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php — add toast calls to proceed(), clearAll(), navigateTo()

Design rules: follow CodingMAIN.md §10.0 exactly (color tokens only; same name+same color per the canonical legend/status→color map; OOP @extends/@include/shared theme.css+ui-common.js+mock-data.js; mock data in mock-data.js NOT inline; icon over text; detail/secondary info in modals; on 3rd duplication promote element to shared partial/theme.css/ui-common.js/mock-data.js and refactor existing pages too; minimise steps/fewest clicks; confirm critical actions with a popup; **after critical action, show toast bar at bottom-left with success message + Undo button (5s auto-dismiss)**).

Toast component design:
- CSS: `.toast-bar` matches `.bulk-action-bar` styling (surface bg, outline border, shadow, radius-md, padding 10px 16px, fixed bottom-left z-index 200)
- CSS: `.toast-message` — font-size 13px, font-weight 500, color on-surface
- CSS: `.toast-undo` — outline primary button, hover fills primary
- CSS: `.toast-close` — borderless, color on-surface-variant, hover on-surface
- CSS: `@keyframes toastSlideUp` — translateY(20px) → translateY(0), opacity 0 → 1, 0.3s ease
- JS: `showToast(message, undoCallback, duration=5000)` — shows toast, sets undo onclick, starts auto-dismiss timer
- JS: `dismissToast()` — hides toast, clears timer
- HTML: `<div class="toast-bar" id="toastBar"><span class="toast-message"></span><button class="toast-undo" style="display:none">Undo</button><button class="toast-close" onclick="dismissToast()">✕</button></div>`

Critical actions to add toast (with undo callbacks):
1. my-request-history: `quickCancel(id)` — save removed item + index → splice → showToast("Request #{id} cancelled.", () => { mockRequests.splice(index, 0, removed); renderTable(); })
2. my-request-history: `batchCancelSelected()` — save removed items array → filter out selected → showToast("{N} requests cancelled.", () => { mockRequests.push(...removed); renderTable(); })
3. MyTimetable: `cancelClass()` — save class data → remove from events → showToast("Class cancelled.", () => { events.push(saved); buildTimetable(); })
4. replacement-arrangement: `proceed()` — after confirm → showToast("Replacement request submitted.", null) — no undo (submission is final)
5. replacement-arrangement: `clearAll()` — save selectedCells → clear → showToast("All selections cleared.", () => { selectedCells = saved; renderSelection(); })
6. replacement-arrangement: `navigateTo()` — save selectedCells → clear → showToast("Selections cleared.", () => { selectedCells = saved; renderSelection(); })

Promote-on-3rd-duplication: while building, if any markup block / CSS class / JS helper ends up the SAME across 3+ pages, promote it to a shared file (partial `resources/views/partials/`, `theme.css`, `ui-common.js`, or `mock-data.js`) and replace the inline copies in the new page AND existing pages with @include / var() / helper() / MockData.*. List every promotion in design.md under "Promoted to shared".

Changelog (generate BEFORE the proposal, keep updating as you build):
- Update `page-changelogs/my-request-history-changelog.md` (ALREADY EXISTS — do NOT create new file).
- Add new entries under `### \`public/css/theme.css\``, `### \`public/js/ui-common.js\``, `### \`resources/views/layouts/ui-template.blade.php\`` for the shared component.
- Add new entries under `### \`resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php\`` for toast calls added to cancel actions.
- Follow the exact format: `| Timestamp | Location | Change | Detail |` with server-local ISO-ish timestamps.
- Log EVERY change: CSS additions, JS function additions, HTML modifications, toast call additions.

Deliverables: .sdd/changes/toast-undo-bar/ (sdd.yaml, proposal.md, design.md, tasks.md — model format on .sdd/changes/cohort-timetable-ui/; design.md MUST include a "Promoted to shared" section), page-changelogs/my-request-history-changelog.md (UPDATED with new entries). After apply: run composer run lint:check + composer run types:check; confirm no new failures. Commit prefix: ui:.

Constraints: no migrations/models/backend logic (frontend mock phase); no new dependencies; no new files (enhance existing shared files only); toast component is shared via theme.css + ui-common.js + layouts/ui-template.blade.php (not page-local).
