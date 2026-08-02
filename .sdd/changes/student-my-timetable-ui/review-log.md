## proposal Round 1 — 2026-08-01 20:44
### 🔴 Fixed
 - *(none — first review round; fixes applied before Round 2)*
### 🟡 Addressed
 - *(none — first review round)*
### 🔴 Outstanding
 - **Rule-#6 promotion scope incomplete.** Items 9a–9d cover only legend + summary-card-color + modal-shell CSS (~260 of ~480 duplicated lines). The grid CSS block — `.semester-bar`/`.week-arrow`/`.week-select`, `.time-col`+`day-label`/`date-label`/`holiday-label`+`.today`/`.holiday-col`/`.sunday-col` variants, `.hour-header`/`.hour-top`/`.hour-bottom`, `.hour-cell`/`.sunday-slot`/`.holiday-slot`/`.event-public-holiday`, `.event-block`/`:hover`/`:active`/`.event-normal`/`.event-replacement`/`.event-pending`/`.sunday-slot .event-block`/`.ev-code`/`.ev-venue`/`.ev-time`/`.ev-note`/`.cell-empty` — is byte-identical between MyTimetable and CohortTimetable (~200 lines) and will become a 3rd copy on the student page. Must extend promotion (9e–9h) OR declare explicit out-of-scope with justification.
 - **Dead `.today-highlight` CSS** (`rgba(26,95,180,0.04) !important`) duplicated in both templates; unused by either JS (rule-#1 hardcoded-rgba violation). Decide: delete (preferred) or out-of-scope.
 - **Reduced student nav does not achieve FR 1.4 goal.** `$navItems` only filters which items render; the partial still hardcodes `href="/my-timetable-ui"` for "My Timetable" → a student clicking it lands on the lecturer page (which the proposal itself flags as FR 1.4-violating). Must parametrize hrefs per item per context, OR explicitly declare the mock-phase gap (defer to Sprint-3 RBAC redirect).
### 🟡 Outstanding (non-blocking)
 - CohortTimetable `.badge-replacement` uses hardcoded gold `#d4a017` (rule #1 + #2 violation); modal-CSS promotion list doesn't address `.badge-*` variants. Decide handling.
 - Modal field source ambiguous: item 7 says "mirror CohortTimetable's modal" but CohortTimetable's modal lacks Class Type / Day / Date fields (those come from MyTimetable). Clarify the field list source split.
 - "Page-header chip" for cohort has no precedent in either reference template (only `.semester-chip` exists). Specify class + placement.
 - ".today-highlight" — see 🔴 above.
 - FR/NFR attribution: "Cancelled classes hidden … per requirement" overstates — no FR mandates it; it's a design decision. "Rejected → slot reverts to available" conflates slot state machine with event status. FR 1.4 (UI exclusion, in-scope) vs NFR 2.1/3.4 (middleware, out-of-scope) split should be explicit.
 - Missing changelog stub per sdd-propose-ui-page.md deliverable #4 — must exist before proposal freezes.
 - `currentWeekIndex()` in BOTH templates has a second stale `new Date(2026, 5, 15)` (separate from the `weekData` builder) — item 10 only mentions the week generator. Note both occurrences.
 - `MockData.holidays` consumption on the student page is NEW behavior (CohortTimetable computes holiday inline; MyTimetable has dead holiday code). Proposal should state this is a new pattern, and that CohortTimetable's inline holiday computation is NOT refactored by this change.
 - 3 other pages (replacement-arrangement, replacement-home, my-request-history) also carry the stale 15-Jun chip. Add out-of-scope acknowledgment so it's not read as an oversight.

## proposal Round 2 — 2026-08-01 20:55
### 🔴 Fixed
 - **Rule-#6 promotion scope incomplete** (Round 1 🔴 #1) — RESOLVED. Added items 9e (semester-bar/week-picker), 9f (time-column), 9g (hour-header), 9h (hour-cell+event-block), 9i (delete dead `.today-highlight`). Verified every listed selector is byte-identical between the two templates; `.week-select` min-width standardized to 160. No silent 3-copy trigger introduced by the student page.
 - **Reduced nav does not achieve FR 1.4 goal** (Round 1 🔴 #2) — RESOLVED. Item 8 now parametrizes `$navItems` as `{key,label,href}` items with default = current 5 + hrefs; existing pages unchanged. Student page passes 3 items with student-correct hrefs (My Timetable → `/student-my-timetable-ui`).
### 🟡 Addressed
 - `.modal-footer` `justify-content` divergence (MyT `space-between` vs Cohort `flex-end`) flagged in item 9d; standardized to `flex-end` in `theme.css` + MyT keeps a 1-line page-local override.
 - Cohort Timetables nav-omission rationale re-justified (was contradicting §6 RBAC matrix): now framed as mock-phase user decision (own-cohort view satisfies FR 1.2); cross-cohort browse deferred to Sprint 3 RBAC. Open doc-note added that §6 wording may need tightening.
 - `mock-data.js` §2.2 `holidays` header comment ("CONSUMED BY CohortTimetable ONLY") will be updated to list the student page as a consumer — declarative edit added to item 4.
 - Cohort-chip placement clarified (item 11): immediately after the semester chip in the same `.page-header` row, reusing `.semester-chip`.
 - Modal close-trigger phrasing tightened (item 7): student page matches MyTimetable's shared-helper pattern (`closeOnEsc`/`closeOnOverlayClick` from `ui-common.js`); CohortTimetable's inline handler stays as-is (out of scope).
### 🔴 Outstanding
 - *(none — batch passes)*

**Verdict: proposal.md frozen. Proceed to Batch 2 (design.md).**

## design Round 1 — 2026-08-01 20:58
### 🔴 Fixed
 - *(none)*
### 🟡 Addressed
 - **§1.4 `cancelledFlags` indexing convention undocumented** — added sentence: keys are 0-indexed (same as `rsd3g2Flags`), aligned with `currentWeek`. JS coercion note added (property access coerces numeric keys to strings).
 - **§1.5 `currentWeek+1` ambiguous in day-builder context** — changed to `loopW+1` (0-indexed week loop counter during `weekData` construction, runs at build time not user-week-selection time). Eliminates `ReferenceError` risk.
 - **`.modal-footer-left` / `.modal-footer-right` omitted from MyT keep-list** — added to "Stays page-specific" list in §4 and to §5 MyT row. Prevents accidental stripping of two-button footer wrappers.
 - **`.semester-bar select:not(.week-select)` promoted but not consumed by student page** — added note: "student page does not render this element; promotion consolidates 2 existing copies + future-proofs."
 - **`closeOnOverlayClick` signature implicit in §1.7** — added inline `closeModalOutside(e)` wrapper pattern (same as MyT), making the event-argument requirement explicit.
 - **§1.5 holiday-loop variable name `currentWeek` clashed with user-week symbol** — resolved as part of fix #2 (`loopW`).
### 🔴 Outstanding
 - *(none — batch passes)*

**Verdict: design.md frozen. Proceed to Batch 3 (specs/) or Batch 4 (tasks.md).**

## tasks Round 1 — 2026-08-01 21:03
### 🔴 Fixed
 - **Task 7 missing dependency on Task 4** — changed `depends: Tasks 1, 2, 3` → `depends: Tasks 1, 2, 3, 4`. Student page relies entirely on theme.css for styling; must have promoted CSS before rendering.
### 🟡 Addressed
 - **Task 1 omits `activeCohort`** — added `activeCohort: 'rsd3s1g2'` to match frozen proposal shape exactly.
 - **Task 4 "Delete `.today-highlight`" misleading** — removed from Task 4 (covered by Tasks 5 and 6 in their respective files). Added note that `.semester-bar select:not(.week-select)` is CohortTimetable-only (1 copy, not 2).
 - **Task 8 missing `navItems` context** — clarified route passes `['activeNav' => 'my-timetable']` only; `navItems` is in `@extends` per Task 7.
### 🔴 Outstanding
 - *(none — batch passes)*

**Verdict: tasks.md frozen. All batches complete.**

## proposal Round 3 (unfreeze for items 13–22) — 2026-08-02
### 🔴 Fixed
 - *(none)*
### 🟡 Addressed
 - **Item 20 layout forwarding path unspecified** — added note that `layouts/ui-template.blade.php` must accept and forward `$notifCount ?? 3` to the nav partial.
 - **Item 15 week select option format ambiguous** — clarified: uses `weekData[i].start/end` formatted as `d-MMM` via helper `fmt(d)`, dependent on frozen item 10's weekData rebuild.
 - **Item 16 heatmap grey token + priority unspecified** — added `--color-surface-variant` for grey; added explicit priority: red > yellow > blue > green > grey.
 - **Item 18 keyboard nav select-focus edge case** — added: arrow listeners on `document`; browser consumes arrows when `<select>` is focused, no conflict.
 - **Item 19 timeline step naming confusing** — renamed third step from "Pending" to "Awaiting Replacement"; clarified it's a visual approximation of future backend workflow.
 - **Item 22 progress bar Unicode characters** — changed to CSS-width bar (`<div class="progress-fill" style="width: ${pct}%">`), not Unicode blocks.
 - **Impact Scope updated** — added `ui-template.blade.php` (notifCount forwarding) + new CSS blocks (heatmap/progress-bar/empty-state/status-timeline/keyboard-focus).
### 🔴 Outstanding
 - *(none — batch passes)*

**Verdict: proposal.md re-frozen. Proceed to design.md update.**

## design Round 2 (unfreeze for §1.9–§1.18) — 2026-08-02
### 🔴 Fixed
 - **notifCount forwarding path broken** — §1.16 now explicitly specifies the `ui-template.blade.php` `@include` change with before/after code. Required layout modification is unambiguous.
 - **`.empty-state` CSS collision** — §1.13 reuses existing `.empty-state` from `theme.css` (with `.empty-icon`/`.empty-title`/`.empty-text` subclasses). §4.13 updated to "No new CSS needed."
### 🟡 Addressed
 - **`fmt(d)` helper unspecified** — added code snippet in §1.10: `function fmt(d) { return d.getDate() + ' ' + d.toLocaleString('en', { month: 'short' }); }`.
 - **Enter handler missing from §1.14** — added `if (e.key === 'Enter' && e.target.classList.contains('event-block'))` with `__eventData` pattern.
 - **Heatmap active cell + responsive notes** — added: active toggle runs unconditionally (empty week can be active); no responsive breakpoint needed (14 cells ≈ 420px fits tablet+).
 - **`.meta` pocket not referenced in modal** — §1.7 now explicitly notes "Does not read `event.meta.*` (empty pocket, backend-ready)."
### 🔴 Outstanding
 - *(none — batch passes)*

**Verdict: design.md re-frozen. Proceed to tasks.md update.**

## tasks Round 2 (unfreeze for enhancements) — 2026-08-02
### 🔴 Fixed
 - *(none)*
### 🟡 Addressed
 - *(none — clean pass)*
### 🔴 Outstanding
 - *(none — batch passes)*

**Verdict: tasks.md re-frozen. All batches complete with enhanced scope (items 1–22).**

## proposal/design/tasks Round 4 (unfreeze for items 23–25) — 2026-08-02
### 🔴 Fixed
 - *(none)*
### 🟡 Addressed
 - **§4.17 CSS selector mismatch** — corrected from `.grid-scroll.transition` to `.grid-scroll` + `.grid-scroll.grid-transitioning`.
 - **Event tooltip mobile overflow** — added note: tooltip irrelevant on mobile (desktop-only for mock phase).
### 🔴 Outstanding
 - *(none — batch passes)*

**Verdict: All artifacts re-frozen with items 1–25. Ready for implementation.**

## proposal/design/tasks Round 5 (item 26) — 2026-08-02
### 🔴 Fixed
 - *(none)*
### 🟡 Addressed
 - *(none — clean pass)*
### 🔴 Outstanding
 - *(none)*

**Verdict: All artifacts re-frozen with items 1–26. Ready for implementation.**

## proposal/tasks Round 6 (mock data gap fix) — 2026-08-02
### 🔴 Fixed
 - **Empty-state untestable** — week 4 `cancelledFlags` expanded from 1 event to ALL 7 events (`BMIT7070/7071/7072/8080/7073/7074/7075`). Verified exact 1:1 match with `rsd3g2Base` codes.
### 🟡 Addressed
 - **Holidays comment stale** — Task 1 now explicitly updates header from "CONSUMED BY CohortTimetable ONLY" to "CONSUMED BY CohortTimetable + Student My Timetable".
### 🔴 Outstanding
 - *(none)*

**Verdict: All artifacts re-frozen. Mock data coverage complete for all 26 features.**

