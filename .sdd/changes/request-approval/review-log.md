## proposal.md Round 1 — 2026-07-30

### 🔴 Fixed
 - (none — first review round)

### 🟡 Addressed
 - Urgency logic contradiction: replaced `new Date()` with fixed reference date `new Date('2026-08-29T00:00:00')` so urgency badges are deterministic and visible (some mock entries have class dates within 3 days of Aug 29 → "Urgent", the rest → "Normal")
 - "Total Reviewed" summary card count rule defined: count of entries where status is Approved, Rejected, or Completed (excluding Pending and Cancelled)
 - `formatDateTime()` attribution corrected — it is page-local in my-request-history, must be copied locally (not in ui-common.js)
 - theme.css reuse claim corrected — modal, status badge, and button CSS classes are page-local in my-request-history, must be copied into this page's `@section('page-styles')`
 - Modal Section 1: Rejection Reason field added back (conditionally shown for Rejected status, matching my-request-history lines 788-790); section rename "General Info" → "Request Information" acknowledged
 - Redundant modal entry points clarified: Pending rows have Approve/Reject buttons (no View), non-Pending rows have View button; Status badge is clickable for all rows
 - `urgencyDays` sort field clarified as derived/computed value (not stored in mock data)
 - Sort profile divergence from my-request-history documented with rationale
 - Urgency sort redundancy with classDate sort acknowledged (both monotonic in class date) but justified (groups Urgent entries first)
 - Sort hint updated to list sortable column names (matching my-request-history pattern)

### 🔴 Outstanding
 - (none — all Round 1 issues addressed)

## proposal.md Round 2 — 2026-07-30

### ✅ Round 1 Fixes Verified
 - Urgency logic contradiction: RESOLVED — uses `URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00')` (fixed demo date). Math verified: 08-29→09-01 → diffDays 0-3 → "Urgent"; later dates → "Normal"
 - "Total Reviewed" count rule: RESOLVED — defined as Approved + Rejected + Completed (excluding Pending + Cancelled). Distribution check: 5+4+2 = 11
 - `formatDateTime()` attribution: RESOLVED — correctly stated as page-local in my-request-history (lines 465-479), not in ui-common.js
 - theme.css reuse claim (modal/badge/button): RESOLVED — explicitly listed as page-specific to copy
 - Modal Rejection Reason field: RESOLVED — conditionally shown for Rejected status, matching my-request-history lines 788-790
 - Redundant modal entry points: RESOLVED — Pending rows → Approve/Reject only; non-Pending → View only; Status badge clickable for all rows
 - `urgencyDays` field: RESOLVED — stated as derived/computed value, not stored in mock data
 - Sort profile divergence: RESOLVED — PL workflow rationale provided; urgency sort redundancy acknowledged

### 🟡 Addressed
 - `.cell-class-block` family misattributed to theme.css — moved to "Page-specific CSS (must be copied)" list. Also added `.col-replacement .cell-class-block .class-time.status-*` color-coding, `.modal-field-label`, `.modal-field-value`, `.summary-card.card-*` color rules to the explicit page-specific list

### 🔴 Outstanding
 - (none — no blocking issues remain)

### ✅ PASS — proposal.md is frozen.

## design.md Round 1 — 2026-07-30

### 🔴 Fixed
 - (none — first review round)

### 🟡 Addressed
 - `.col-students` width attribution relabeled as intentionally 70px (narrower than my-request-history's 80px)
 - Table `min-width` arithmetic corrected: 1295px sum, rounded to 1300px (was incorrectly stated as 1400px)

### 🔴 Outstanding
 - Mock-data sample entry duplicates my-request-history: id:1 was a near-verbatim copy (BMIT5555/Software Engineering/B104/DFT2+DSF2). Violates frozen proposal distinctness requirement. Fixing: rewriting sample entries with distinct values (BMIT2201/C201/DIT2+DSE2 etc.)
 - Modal-footer Close-button visibility not handled: `closeBtn` fetched but never shown/hidden. Pending rows would show 3 buttons (Reject+Approve+Close) contradicting frozen proposal's 2-button layout. Fixing: adding `closeBtn.style.display` toggle for Pending vs non-Pending


## design.md Round 2 — 2026-07-30

### ✅ Round 1 Fixes Verified
- Mock-data sample entry duplication: RESOLVED — all 3 sample entries rewritten with distinct values (BMIT2201/C201/DIT2+DSE2, BMIT3302/D103/DCS2, BMIT4403/E201/DAI2+DNE2). Zero overlap with my-request-history's sets.
- Modal-footer Close-button visibility: RESOLVED — `closeBtn.style.display` now toggles ('none' for Pending, 'inline-block' for non-Pending). Header ✕ noted as always visible, separate from status-gated footer Close.

### 🟡 Addressed
- (none new — both Round 1 🟡 items already resolved and remain correct)

### 🔴 Outstanding
- (none — no blocking issues remain)

### ✅ PASS — design.md is frozen.

## tasks.md Round 1 — 2026-07-30

### 🔴 Fixed
 - (none — first review round)

### 🟡 Addressed
 - Missing changelog task: Added Task 8 to create `page-changelogs/request-approval-changelog.md`
 - Empty state `emptyCta` adaptation: Added explicit note in Task 5 to remove `emptyCta` references and use PL-specific empty-state messages
 - Sort-logic note incomplete: Expanded Task 5 sort logic to cover all 5 sortable fields (requestedAt, classDate, replacementDate, urgencyDays, status) and remove dead `courseCode` case
 - Empty-state two-variant handling: Added explicit Task 5 checkbox for empty-state show/hide logic with PL-specific messages
 - Modal overlay HTML: Added search input `id="searchInput"` + `class="search-input"`, reset button `class="btn-clear"`, result count `class="result-count"`
 - Dead CSS cleanup: Added note to remove `.empty-cta` CSS; clarified `.btn-clear` CSS should be kept
 - Modal title: Added "Request Details" to Task 3

### 🔴 Outstanding
 - Modal overlay inline `style="display:none"` contradicts classList show/hide pattern: removed inline style, relying on CSS `.modal-overlay { display: none }` + `.modal-overlay.show { display: flex }` (matching my-request-history line 414)


## tasks.md Round 2 — 2026-07-30

### ✅ Round 1 Fixes Verified
All 5 Round 1 issues confirmed fixed:

**🔴 Modal overlay inline `style="display:none"`: RESOLVED** — Task 3 line 36 now explicitly says `<div class="modal-overlay" id="modalOverlay">` with no inline `style`. Default hiding via CSS `.modal-overlay { display: none }`, showing via classList toggle `.modal-overlay.show { display: flex }`. (The empty-state `<div>` retains its inline `display:none` as noted — that's the correct pattern for the empty state, not the modal.)

**🟡 Changelog task: RESOLVED** — Task 8 (lines 111-117) creates `page-changelogs/request-approval-changelog.md` documenting all files changed and key design decisions.

**🟡 Empty-state emptyCta adaptation: RESOLVED** — Task 5 line 67 explicitly states "**Remove all `emptyCta` references**" with rationale (will return null/TypeError). Task 2 line 21 also removes `.empty-cta` CSS. PL-specific messages provided for both variants.

**🟡 Sort-logic expansion: RESOLVED** — Task 5 line 66 now covers all 5 sortable fields: `requestedAt` (string compare), `classDate` (string compare), `replacementDate` (string compare, NEW), `urgencyDays` (numeric compare via `urgencyDays(r.classDate)`, NEW), `status` (string compare, NEW). Dead `courseCode` case removed.

**🟡 Empty-state two-variant handling: RESOLVED** — Task 5 line 67 contains complete two-variant logic: fully empty vs filtered empty, each with PL-specific title/text messages, keep/show/hide instructions for `emptyState`, `emptyTitle`, `emptyText`, `gridWrapper`, `paginationBar`, `summaryBar`.

### 🟡 Addressed
- (none — all Round 1 🟡 items are now resolved and verified in the current tasks.md)

### 🔴 Outstanding
- (none — no remaining blocking issues)

### Cross-Check Notes (vs frozen proposal.md + design.md)
All 11 cross-check items pass: column count, sortable fields, default Pending filter, urgency system, approve/reject behavior, modal footer toggling, search scope, summary card logic, CSS strategy, changelog presence, empty state no CTA.

### ✅ PASS — tasks.md is frozen.

## CHAIN UNFROZEN — OOP Refactor — 2026-08-01

### 🔴 Fixed
 - (none — no review issues; unfreeze triggered by user decision to add OOP concepts)

### 🟡 Addressed
 - proposal.md, design.md, tasks.md all UNFROZEN for decision-level change: page now utilizes OOP concepts
 - proposal.md §9 rewritten into 9.1–9.5: inheritance (@extends), composition (@include partials), encapsulation (theme.css + ui-common.js), and NEW §9.5 promoting 10 page-local helpers from my-request-history into ui-common.js (single source of truth)
 - proposal.md Files Changed: +`public/js/ui-common.js` (Modify), +`my-request-history-UI-design-template.blade.php` (Modify — remove promoted helpers)
 - design.md: Technical Approach updated, §1 page-scripts comment updated, Dependencies + File Changes updated with promotion
 - tasks.md: new Task 1 (promote helpers + refactor my-request-history + regression check), tasks renumbered 1–9, Task 5 uses shared helpers (no copy), pagination/result-count use shared ui-common.js functions, smoke test adds my-request-history regression check, changelog task documents OOP decisions

### 🔴 Outstanding
 - (none — pending fresh review of the unfrozen artifacts)


## OOP Re-Review Round 1 — 2026-08-01

### ✅ Verified (codebase cross-check)
- All 10 promoted helpers exist page-local in `my-request-history-UI-design-template.blade.php` lines 465–546; line numbers match proposal §9.5 and tasks.md Task 1 exactly (465/472/488/499/503/509/519/529/534/541)
- None of the 10 helpers exist in `public/js/ui-common.js` — Task 1 "append" creates no duplicate declarations
- Dependency claim verified: the 10 depend only on `weekRanges` (also promoted) + `to12h`/`formatDate` (already shared); `formatDateTime`/`statusClass`/`dayAbbr`/`isoDayName` are self-contained
- Collision check: no other page in `ui-design-templates/` defines any of the 10 names — promotion is safe for every page loading the layout
- 6 claimed reusable functions verified in ui-common.js with matching signatures: `compareBy`, `makeSortableHeader`, `paginate`, `updateResultCount`, `closeOnEsc`, `closeOnOverlayClick`
- `layouts/ui-template.blade.php` loads `/js/ui-common.js` before the inline `<script>` containing `@yield('page-scripts')` — page can consume promoted helpers
- `theme.css` has `.badge` base (L504); no `.status-*` variants in theme.css (page-local, as claimed)
- Tasks 1–9 sequential, no gaps/duplicates; all efforts ≤ 2 hours
- §9.5 helper table == design.md == tasks.md Task 1 — 10/10 identical names; Files Changed tables consistent (6 files)
- my-request-history regression checks present in Task 1 and Task 8 smoke test

### 🟡 Notes
- `page-changelogs/request-approval-changelog.md` ALREADY EXISTS (dated 2026-08-01 15:00) — proposal Files Changed + Task 9 said "Create" with stale "done" claims. FIXED: rewrote to "Update/replace", added pre-implementation note, added ui-common.js + my-request-history sections and OOP design decisions
- design.md §2 listed `.badge` base as page-specific — it IS in theme.css (L504). FIXED: reuse base from theme.css, page-local only for variants + cursor/hover
- tasks.md Task 5 "Write weekRanges[]" contradicted its own do-not-redeclare note. FIXED: reworded to "Reference shared weekRanges from ui-common.js (do NOT redeclare)"

### 🔴 Outstanding
- (none)

### ✅ PASS — all artifacts re-frozen (proposal.md, design.md, tasks.md).


## CHAIN UNFROZEN — FR/NFR Alignment (FR 3.2, 3.4, 3.6) — 2026-08-01

### 🔴 Fixed
 - (none — no review issues; unfreeze triggered by user decision to align the page with FR & NFR requirements)

### 🟡 Addressed
 - proposal.md, design.md, tasks.md all UNFROZEN for decision-level changes driven by FR & NFR chapter 3:
 - FR 3.6 (mandatory rejection reason): Reject flow changed from confirm()→alert() to a Rejection Reason modal — textarea #rejectReasonInput, Confirm Reject disabled until non-whitespace input, reason echoed in confirm() dialog. Reject button onclick → openRejectModal(r.id). New design.md §7 + §12, tasks.md Task 7
 - FR 3.4 (pre-computed slot validity): new mock field slotValidity ('valid'|'conflict', ~4-5 conflicts with conflictReason) + Slot Validity line in modal Section 3 (slotValidityHtml + .slot-valid/.slot-conflict CSS). New design.md §11, tasks.md Task 5 + 7
 - FR 3.2 (time-based queue): default sortState = { field: 'requestedAt', dir: 'asc' } (FIFO); Reset Filters returns to it. design.md §6.1, tasks.md Task 6
 - FR 3.7: confirmed modal's existing Reviewed By / Reviewed At fields suffice for design phase (no new work)
 - proposal.md: added FR Traceability section mapping FR 3.2-3.7, 2.8, 4.15 and NFR 1.3/3.1/3.3 to page features

### 🔴 Outstanding
 - (none — pending fresh review of the unfrozen artifacts)


## FR Re-Review Round 1 — 2026-08-01

### ✅ Verified (FR/NFR cross-check)
- FR 3.2 (time-based queue): default status filter "Pending" + default sort `requestedAt` asc FIFO (proposal §6.1 == design §6.1 == tasks Task 6); Reset Filters returns to both defaults
- FR 3.4 (pre-computed slot validity): `slotValidity` mock field on all 20 entries + Slot Validity line in modal Section 3 via `slotValidityHtml(r)` with `.slot-valid`/`.slot-conflict` CSS (proposal §5.1 == design §11 == tasks Task 5/7); renders for every request
- FR 3.5 (one-click approve): inline Approve button in Actions column + modal footer Approve; confirm()→alert() documented as design-phase simulation
- FR 3.6 (mandatory rejection reason): ENFORCED in design — `updateRejectConfirmState()` disables `#confirmRejectBtn` until non-whitespace AND `rejectRequest()` re-validates with blocking guard; all reject entry points route through `openRejectModal(id)`, no bypass
- FR 3.7 (audit trail): Reviewed By / Reviewed At in modal Section 3 + Rejection Reason (Section 1, Rejected only); backend audit log explicitly deferred
- Function names consistent across proposal §4.1/§5.1, design §7/§11/§12, tasks Task 7: openRejectModal, closeRejectModal, updateRejectConfirmState, rejectRequest, slotValidityHtml, currentRejectId
- Mock data: status distribution 8/5/4/2/1 = 20 and summary counts (Total Reviewed = 11) consistent; urgency math verified (08-31/09-01 → Urgent, 09-07 → Normal)
- Tasks 1-9 sequential, all efforts ≤ 2h; Files Changed = 6 files in proposal + design, all 6 documented in Task 9
- Codebase ground-truth re-verified: 10 helpers at my-request-history L465-546, none in ui-common.js; layout loads ui-common.js (L33) before page-scripts (L39); `.modal-overlay` page-local, z-index 100, display:none + .show; closeOnOverlayClick target-check safe per overlay

### 🟡 Addressed (fixed after Round 1)
- design.md §4 sample entries id:2/id:3 missing slotValidity/conflictReason — ADDED; id:3 now demonstrates a conflict sample; all-20-entries rule noted
- Modal stacking: `.modal-overlay` z-index 100 → DOM order decides top layer. design.md §12 now mandates `#rejectReasonModal` AFTER `#modalOverlay` in DOM; removed "or replacing it — implementer choice" ambiguity; stacking chosen (detail modal stays open beneath)
- Escape handling: `closeOnEsc` (ui-common.js L163) attaches unconditional listener per call → two calls close BOTH modals. design.md §12 + tasks Task 7 now specify ONE keydown handler: if `#rejectReasonModal` has `.show` → closeRejectModal() else closeModal()
- design.md File Changes L439 changelog "Create" → "Update/replace" (matches proposal + Task 9)
- proposal §6 + tasks Task 5 urgent dates 08-29/08-30 fall outside weekRanges (Week 1 starts 08-31) — constrained to 08-31/09-01 (Urgent AND in Week 1); design.md mock comments updated
- design §12 Cancel button now has id="cancelRejectBtn" (matches tasks Task 7)

### 🔴 Outstanding
- proposal.md §4 stale flow "Calls `rejectRequest(id)` → opens the Rejection Reason modal" — FIXED to "Calls `openRejectModal(id)` → opens the Rejection Reason modal (FR 3.6) → user enters the mandatory reason → Confirm Reject calls `rejectRequest()` → confirm() → alert()"


## FR Re-Review Round 2 — 2026-08-01

### ✅ Round 1 Fixes Verified
- 🔴 proposal.md §4 stale reject flow: RESOLVED — routes through `openRejectModal(id)` → reason modal → no-arg `rejectRequest()` → confirm() → alert(). Zero stale `rejectRequest(id)`/`rejectRequest(r.id)` calls remain in any artifact
- 🟡 design.md §4 slotValidity on samples: RESOLVED — id:2 `slotValidity:'valid'` + `conflictReason:null`; id:3 `slotValidity:'conflict'` + `conflictReason:'Room C202 already occupied'`; all-20-entries rule noted
- 🟡 design.md §12 modal stacking: RESOLVED — same z-index 100 + DOM order decides top; `#rejectReasonModal` mandated AFTER `#modalOverlay`; stacking chosen
- 🟡 Escape handling: RESOLVED — design §12 + tasks Task 7 specify identical ONE keydown handler; "do NOT call closeOnEsc twice" in both
- 🟡 design.md File Changes changelog label: RESOLVED — "Update/replace"
- 🟡 Urgent dates: RESOLVED — constrained to 08-31/09-01 (in Week 1); 08-29/08-30 prohibited
- 🟡 Cancel button id: RESOLVED — `id="cancelRejectBtn"` (design §12 == tasks Task 7)
- Cross-checks: function names consistent; Tasks 1-9 sequential, all ≤ 2h; FR Traceability accurate; column width sum 1295px → min-width 1300px

### 🟡 Addressed (Round 2 recommendations applied)
- tasks.md Task 7: closeModal() now explicitly says do NOT use closeOnEsc (ONE keydown handler covers Escape) — removes the Round-1 bug reintroduction vector; added DOM-order line (#rejectReasonModal AFTER #modalOverlay)
- tasks.md Task 5: stale date span "Aug 29 - Sep 27" → "Aug 31 - Sep 27" (matches the 08-29/08-30 prohibition)
- design.md Dependencies: `closeOnEsc()` dropped/annotated "NOT used on this page (replaced by ONE keydown handler)"

### 🔴 Outstanding
- (none)

### ✅ PASS — all artifacts re-frozen (proposal.md, design.md, tasks.md).


## CHAIN UNFROZEN — mock-data.js shared data module — 2026-08-01

### 🔴 Fixed
 - (none — no review issues; unfreeze triggered by user decision to move mock data into a shared public/js/mock-data.js module)

### 🟡 Addressed
 - proposal.md, design.md, tasks.md all UNFROZEN for decision-level change:
 - NEW shared module `public/js/mock-data.js` holds `mockRequests` (20 entries) + `URGENCY_REFERENCE_DATE` — data separated from logic (encapsulation)
 - Layout `ui-template.blade.php` modified: `<script src="/js/mock-data.js"></script>` added after ui-common.js (both before page inline script)
 - Page `@section('page-scripts')` references globals, does NOT redeclare (duplicate const would throw "Identifier already declared")
 - urgency helper FUNCTIONS stay page-local; only URGENCY_REFERENCE_DATE constant moves to mock-data.js
 - Other pages' inline mock data untouched (migration is future work)
 - proposal.md §6 rewritten (mock-data.js), §9.1 layout description updated, NEW §9.6 (shared data module), page-local list updated, Files Changed +2 files (mock-data.js Create, ui-template Modify)
 - design.md: Technical Approach, §1 page-scripts comment, §4 mock data location, §5 urgency split (constant in mock-data.js / logic page-local), Dependencies, File Changes updated
 - tasks.md: Task 5 now creates mock-data.js + layout script tag + writes data there; Task 8 adds mock-data.js load verification + other-pages regression; Task 9 changelog documents mock-data.js
 - changelog file: added mock-data.js + layout sections

### 🔴 Outstanding
 - (none — pending fresh review of the unfrozen artifacts)


## CHAIN UNFROZEN (tasks.md batch) — mock-data.js ownership merge — 2026-08-01

### 🔴 Fixed
- Task 5 amended with an order-robust guard: if `public/js/mock-data.js` already exists (created by the sibling `centralized-mock-data` change), DO NOT overwrite — keep `approvalRequests` + `URGENCY_REFERENCE_DATE` compatibility globals intact; if absent, create per the original contract. Layout script tag becomes idempotent (skip if present).

### 🟡 Addressed
- Cross-change sequencing: `centralized-mock-data` owns the `window.MockData` sections of the file; this chain owns the bare globals. Both may be applied in either order.

### 🔴 Outstanding
- (none — re-verified by @sdd-reviewer in centralized-mock-data Batch 1 Round 2)

## mock-data.js Re-Review Round 1 — 2026-08-01

### ✅ Verified (codebase cross-check)
- Layout insertion point: `layouts/ui-template.blade.php` — `/js/ui-common.js` at L33, inline `<script>` containing `@yield('page-scripts')` at L34–40; `<script src="/js/mock-data.js"></script>` goes between them (proposal §6/§9.6 == design §4 == tasks Task 5)
- Cross-script global pattern works: ui-common.js declares top-level `const hours` (L47); no page redeclares it (my-request-history's `let hours = 0` L677 is function-local) — shared-const pattern is safe when names are unique
- mock-data.js consistency: proposal §6 + §9.6 == design §4 + §5 == tasks Task 5 — same file `public/js/mock-data.js`, same globals (`approvalRequests` 20 entries, `URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00')`), same insertion point; page script references globals, never redeclares
- Files Changed: 8 files in proposal == 8 in design; all 8 documented in Task 9
- Tasks 1–9 sequential; efforts 1h/30m/1h/1h/1.5h/2h/1.5h/30m/15m — all ≤ 2h
- No leftover page-embedded mock-data instructions anywhere (proposal §6/§9.6, design §1/§4, tasks Task 5)
- FR alignment intact: FR 3.2 (§6.1 == §6.1 == Task 6), FR 3.4 (§5.1 == §11 == Tasks 5/7), FR 3.6 (§4.1 == §12 == Task 7)
- OOP promotion intact: 10-helper table (§9.5 == design Dependencies == Task 1); `weekRanges` exists only page-local in my-request-history L465, removed by Task 1, no other page declares it
- `URGENCY_REFERENCE_DATE` appears nowhere in existing code; other pages' datasets use distinct names (replacement-home `conflictedClasses`, MyTimetable `dayNames`, CohortTimetable `facultyData`/`rsd3g2Base`, replacement-arrangement `weekData`)

### 🟡 Notes
- Task 8 "global is harmless" check covers only replacement-home + my-timetable; the my-request-history regression check must ALSO verify the layout script-tag addition did not break it (now covered by Task 8 my-request-history regression + no-console-errors checks)

### 🔴 Outstanding
- **FIXED — user decision (Option 1)**: mock-data.js global renamed from `mockRequests` → `approvalRequests` (my-request-history's top-level `const mockRequests` at L442 stays; the new global no longer collides, so that page's inline script survives the layout's new script tag). File name unchanged (`mock-data.js`); `URGENCY_REFERENCE_DATE` unchanged (unique name). All three artifacts updated to reference `approvalRequests` (proposal §6/§9.6 + Files Changed, design §4/§5 + File Changes + mock code blocks, tasks Tasks 5/8/9). Files Changed rows updated: mock-data.js row now says "defines `approvalRequests` (20 entries) + `URGENCY_REFERENCE_DATE`".

### ✅ PASS — all artifacts re-frozen (proposal.md, design.md, tasks.md).

## Implementation-advisory notes — 2026-08-01 (cross-check vs sdd-propose template)

> Read-only cross-check of the frozen artifacts against the `prompts/sdd-propose-template.md` Steps 3-5 (6 identity fields + copy list + remove list). Chain stays FROZEN — these are advisory notes for the `/sdd-apply` implementer, not a new unfreeze round. All 6 identity fields + copy list + the bulk of the remove list MATCH the frozen artifacts. Two under-specified gaps surfaced; neither blocks implementation.

### ✅ Verified matches (no action needed)
- Step 3 (6 identity fields): Name / File / Route / activeNav / Role / Purpose+mock-scope all match `proposal.md` (L1, L28, L30, L148, §6) — ✅
- Step 4 (copy from my-request-history): `design.md` Technical Approach + §2 CSS Strategy enumerate the mirrored structure (page header → toolbar → table → pagination → summary cards → empty state → modal) and the exact CSS blocks copied — ✅
- Step 5 (remove/change): `mockRequests` inline → `approvalRequests` from mock-data.js (✅); 10 page-local helpers → promote to `ui-common.js` (✅, §9.5 / Task 1); modal footer "Cancel Request" → Approve/Reject for Pending + Close for non-Pending (✅, §Modal Footer); summary cards 5→4 with Total Reviewed (✅, §1 Summary Cards); columns drop Type/Venue/Cohort + add Lecturer/Urgency/Actions, Students stays standalone col 7 @ 70px (✅, §2 table); 2nd modal `#rejectReasonModal` (FR 3.6) + Slot Validity (FR 3.4) + Reviewed By/At (FR 3.7) (✅); default "Pending" filter + `requestedAt` asc (FR 3.2) (✅); semester chip dropped (✅, §1 header = title + description only)

### 🟡 Gap A — dead `.toggle-*` CSS carried over by the "copy entire page-styles" instruction
- `my-request-history-UI-design-template.blade.php` L7-53 defines `.toggle-wrapper`/`.toggle-track`/`.toggle-thumb`/`.toggle-label`, and L366-370 renders an "Exclude Completed" toggle in the toolbar.
- `proposal.md:33` redefines the request-approval toolbar as search + status filter + week filter + reset (NO toggle) — so the toggle is implicitly dropped from the markup.
- BUT `design.md:46` (§2 CSS Strategy) says "Copy my-request-history's entire `@section('page-styles')` as the starting point, then add 3 new CSS blocks" — this pulls the `.toggle-*` rules in as dead CSS (no element references them).
- **Impact:** cosmetic only — dead CSS, nothing breaks, no functional regression.
- **Implementer guidance:** when copying `@section('page-styles')`, drop the `.toggle-wrapper`/`.toggle-track`/`.toggle-thumb`/`.toggle-label`/`input:checked + .toggle-track` rules (5 blocks, ~47 lines). No toggle markup is rendered, so the CSS has no target. (If a future variant of this page ever needs a toggle, restore from my-request-history at that point.)
- **No unfreeze required** — this is a stripping instruction within the existing "copy + adapt" CSS strategy, not a contract change.

### 🟡 Gap B — empty-state CTA text not specified (role mismatch risk)
- `my-request-history-UI-design-template.blade.php` L415-417 has the empty-state copy: title "You haven't submitted any replacement requests for this semester.", text "Submit a replacement request for any conflicted class.", and a CTA button "Submit a Replacement Request" linking to `/replacement-arrangement` (the lecturer's submit flow).
- `proposal.md:38` says "Empty State: Two variants — fully empty (no requests at all) and filtered empty (no results match filters)" but does NOT specify the copy text and does NOT explicitly drop the CTA button.
- An implementer copy-pasting the empty-state block would carry over the lecturer's "Submit a Replacement Request" CTA — a role mismatch (Programme Leaders don't submit replacement requests; they review them).
- **Impact:** low but real — a PL seeing "Submit a Replacement Request" on an empty approval queue is confusing and contradicts the PL role framing throughout the artifacts.
- **Implementer guidance:** rewrite the empty-state copy for the PL context and DROP the CTA button:
  - Fully-empty variant: title "No requests awaiting review.", text "When lecturers submit replacement requests, they will appear here for your approval."
  - Filtered-empty variant: title "No requests match your filters.", text "Try adjusting the status, week, or search filter."
  - Remove the `<button class="empty-cta" id="emptyCta">` element entirely (style may stay in theme.css / page-styles harmlessly, or be stripped). Keep the `#emptyState` / `#emptyTitle` / `#emptyText` IDs so existing show/hide JS continues to work.
- **No unfreeze required** — this is copy specification within the existing "two empty-state variants" contract (proposal §1 Empty State), not a contract change.

### ℹ️ Convention note (not a gap)
- Frozen artifacts reference the bare global `approvalRequests` (`proposal.md:121`, `design.md:35,149,251`); repo mandate (AGENTS.md rule 4 / sdd-propose spec §4) says read `window.MockData.*`. The alias `MockData.approvalRequests` exists at `mock-data.js:1013` (assigned by the sibling `centralized-mock-data` change), so both forms resolve to the same array — functionally identical. If convention-compliance is desired, the page script may read `MockData.approvalRequests` with zero unfreeze (within the existing Task 5/6 "reference the globals" scope). No action required either way.

### Summary
Chain remains FROZEN. `/sdd-apply` implementer should action Gap A (strip `.toggle-*` CSS) and Gap B (rewrite empty-state copy + drop CTA) during the build; both are within the existing artifact contracts and do not require re-review or re-freezing.

## CHAIN UNFROZEN — 8 new PL efficiency features — 2026-08-01

### 🔴 Fixed
 - (none — decision-level unfreeze to add new features)

### 🟡 Addressed
 - User brainstorm identified 8 UI features for PL efficiency (ranked: 6 small, 2 medium):
   1. Bulk Approve/Reject — Select All + per-row checkboxes on Pending rows + batch action bar (Medium)
   2. Quick approve confirm summary — multi-line confirm() with request details (Small)
   3. Reject reason presets — 5 clickable chips above textarea (Small)
   4. Filter by urgency — "All / Urgent only / Normal only" chip group in toolbar (Small)
   5. Request age sub-label — "X days ago" under Requested Timestamp + coloured dot (Small)
   6. Approval notes — optional notes textarea modal before approve (Medium)
   7. Pending count badge — red badge on nav bar "Request Approval" link (Small)
   8. Viewed indicator — subtle tint on rows opened in modal (Small)
 - Chain unfrozen to add these to proposal.md, design.md, tasks.md
 - "Batch approve/reject" removed from Out of Scope in proposal.md (now in scope)
 - Total task count: 9 → 12 (Tasks 10-12 are new; Tasks 3, 4, 6, 7, 8 updated)
 - Estimated effort impact: ~4.5 hours added (Task 10: 1.5h HTML, Task 11: 1h CSS, Task 12: 2h JS)


## 8 Features Re-Review Round 1 — 2026-08-01

### 🔴 Fixed
 - design.md §12 Escape handler was stale (2-layer: rejectReason → detail). Updated to 3-layer: approveNotes → rejectReason → detail (matching proposal §7c and tasks Task 12)

### 🟡 Addressed
 - design.md §7 `approveRequest()` showed bare confirm()→alert() — added note that §7 is overridden by §18 (Approval Notes Modal) in Task 12
 - design.md §18 lacked DOM stacking note — proposal §7c and Task 10 already cover it; self-containedness improved by the §12 Escape fix (which references approveNotesModal)

### ✅ PASS — all artifacts re-frozen (proposal.md, design.md, tasks.md).

## CHAIN UNFROZEN — 3 keyboard/UX features — 2026-08-01

### 🔴 Fixed
 - (none — decision-level unfreeze to add new features)

### 🟡 Addressed
 - User requested 3 additional UX features:
   1. Keyboard shortcuts — Arrow keys navigate rows, Enter opens modal, A approves, R rejects (Small)
   2. Review next auto-advance — after approve/reject/close, auto-open next Pending request (Small)
   3. Slot validity preview icon — tiny ✓/⚠ in Proposed Replacement column (Tiny)
 - Chain unfrozen to add these to proposal.md (§7i-§7k), design.md (§21-§23), tasks.md (Task 12 update, Task 8 update)
 - Total task count remains 12; effort delta: ~45 min added to Task 12


## CHAIN RE-FROZEN — 3 keyboard/UX features — 2026-08-01

### 🔴 Fixed
 - **Critical:** `rejectRequest()` in design.md §7 — was single-item only, showed "#null" for bulk; now uses conditional label (`selectedIds.size + ' request(s)'` for bulk, `'Request #' + currentRejectId` for single), clears `selectedIds`, re-renders, and calls `reviewNextAfterAction()`
 - **Critical:** `rejectRequest()` in tasks.md Task 8 and Task 12 — updated to describe bulk-mode label branching, `selectedIds.clear()`, `renderTable()`, `reviewNextAfterAction()` call
 - **Minor:** proposal.md §7k tooltip for "Valid" was "Slot available"; updated design.md §23 to match proposal: "Slot available — no conflict"

### 🟡 Addressed
 - Expanded Task 8 smoke test: split "review next auto-advance" into two items — one for approve path, one for reject path
 - Proposal §7k, design.md §21-§23, tasks.md Task 11/12: all consistent across artifacts

### ✅ PASS
 - All 3 features (keyboard shortcuts §7i/§21, review next §7j/§22, slot validity icons §7k/§23) are now fully specified, cross-artifact consistent, and ready for `/sdd-apply`
 - Chain is re-frozen


## CHAIN UNFROZEN — 6 advanced UX features — 2026-08-01

### 🔴 Fixed
 - (none — decision-level unfreeze to add new features)

### 🟡 Addressed
 - User requested 6 advanced UI/UX features:
   1. §7l/§24 — Toast notifications (replace all alert() with showToast() from ui-common.js)
   2. §7m/§25 — Undo stack (3-5 sec toast with Undo button, reverts approve/reject)
   3. §7n/§26 — Animated transitions (row status flash, filter fade, group expand/collapse)
   4. §7o/§27 — Smart grouping (Group by dropdown: None/Course/Lecturer, collapsible section headers)
   5. §7p/§28 — Mini timeline (request lifecycle dots in detail modal: Submitted → Viewed → Reviewed)
   6. §7q/§29 — Skeleton loading (shimmer rows on filter change, 300ms simulated delay)
 - Existing infra reused: showToast() in ui-common.js:387, skeleton CSS in theme.css:1570, toast HTML in ui-template.blade.php:44
 - Chain unfrozen to add to proposal.md (§7l-§7q), design.md (§24-§29), tasks.md (Task 10/11/12/8 updates)


## CHAIN RE-FROZEN — 6 advanced UX features — 2026-08-01

### 🔴 Fixed
 - (none — all items were 🟡, fixed without critical blockers)

### 🟡 Addressed
 - **Skeleton on filter/sort changes:** Added `isInitialLoad` flag to design.md §29 + tasks Task 12; initial load shows 300ms skeleton, filter/sort changes show 150ms skeleton flash
 - **Keyboard nav skips group headers:** Updated `highlightRow()` in design.md §21 to use `querySelectorAll('#dataTable tbody tr:not(.group-header)')` — group header rows are excluded from keyboard navigation; updated tasks Task 12 bullet
 - **Undo after review-next disorientation:** Added design note in design.md §25 documenting the accepted UX quirk — toast shows undo for previous request while modal shows next request; noted as design-phase simulation quirk

### ✅ PASS
 - All 6 advanced UX features (toast §7l/§24, undo §7m/§25, animations §7n/§26, grouping §7o/§27, timeline §7p/§28, skeleton §7q/§29) are now fully specified, cross-artifact consistent, and ready for `/sdd-apply`
 - Chain is re-frozen


## CHAIN RE-FROZEN — MockData convention fix — 2026-08-01

### 🔴 Fixed
 - **Convention compliance:** All 3 artifacts updated to reference `MockData.approvalRequests` / `MockData.urgencyReferenceDate` instead of bare globals `approvalRequests` / `URGENCY_REFERENCE_DATE`. Matches mandated spec (AGENTS.md rule 4 / sdd-propose-ui-page.md §4): "read from `window.MockData.*`."
 - proposal.md: §6, §7m, §10.6, Files Changed table — all updated
 - design.md: Technical Approach, §4 comment, §5 note, §7/§8/§9/§12/§25/§28 code snippets, §30 Dependencies, File Changes table — all updated
 - tasks.md: Tasks 5, 8, 12 — all updated
 - Note: `const approvalRequests = [` in design.md §4 stays as-is — it shows the mock-data.js file content (the data definition), not page-script usage

### ✅ PASS
 - All artifacts now consistent with `window.MockData.*` convention
 - Chain re-frozen

