# Changelog — Upcoming Replacements (Student)

## [2026-08-24] Stub page + route + mock data (TASK-006 Phase 1)

Student role drops "Request History" and gets "Upcoming Replacements" instead
(Ch1 §1.1.4 view-only; FR 1.4 upcoming replacement details). UI design deferred to
Sprint 3 per `todo list/upcoming-replacements-ui-plan.md`.

### Files Created

#### `resources/views/ui-design-templates/upcoming-replacements-UI-design-template.blade.php`
- Stub template: `@extends('layouts.ui-template')` with the 2-item student `navItems`
  (Student My Timetable + Upcoming Replacements), `pageKey='upcomingReplacements'`
- Reuses `partials.ui-page-header` + `partials.ui-empty-state` ("UI design pending")
- Semester chip + notif badge init copied from sibling student page pattern

#### `page-changelogs/todo list/upcoming-replacements-ui-plan.md`
- Implementation Details doc for the upcoming UI build (data contract, status→color map,
  OOP reuse inventory, openClassModal wiring, layout sketch, hard rules, checklist)

### Files Modified

| File | Change |
|---|---|
| `routes/web.php` | Added `GET /upcoming-replacements-ui` → stub view (`activeNav='upcoming-replacements'`) |
| `public/js/mock-data.js` | New §2.12 `upcomingReplacements` — 3 rows keyed to real `rsd3g2Base` courses + `rsd3g2Flags` weeks (BMIT7074 w1 approved, BMIT2233 w2 pending/no venue yet, BMIT5678 w7 approved); grid-compatible `di/start/end` index space |
| `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php` | Nav item `replacement-history` → `upcoming-replacements` |

### Key Decisions
- `/my-request-history-ui` route kept — lecturers/PLs still use it; only the student nav link is replaced.
- Status vocabulary mirrors timetable legend: `replacement`=approved-upcoming (blue), `pending`=awaiting PL (yellow) — `.badge-replacement` / `.badge-pending`.
- Pending rows carry null new-slot fields so the UI can show "Awaiting PL approval" without inventing data.

## [2026-10-02] Cross-page refactor — shared `syncSummarySection` helper

The summary auto-hide logic added above was promoted to `ui-common.js` (`syncSummarySection(visible)`, §10.0 rule 7) and is now used by my-request-history, replacement-home and request-approval too; this page's local 2-liner was replaced with the shared call. Behavior unchanged (re-verified: default/empty/restore all pass).

## [2026-10-02] User revision — empty state above summary; summary hidden when empty

- The `ui-empty-state` include moved **above** the summary strip in the DOM.
- When the view is empty, `#summarySection` (5 cards + hint) is **hidden entirely** — nothing to summarize (house precedent: my-request-history hides its summary bar on empty); it restores on any non-empty view. Verified: Week 7 → empty state shown + summary hidden; Week 10 → summary back + empty state hidden (and positioned above summary in the DOM); 0 console errors.

## [2026-10-02] User revision — modal slots as data-label/data-value rows

Slot info in the modal is no longer one long string: each fact gets its own definition row, grouped per slot — **Original Slot**: Week 12 / Day Wednesday / Date 14 Oct 2026 / Time 2:00 PM to 4:00 PM / Duration 2 hrs / Venue A104 — and the same block for **New Slot** (pending: single "Awaiting PL approval" row with the muted PL explanation). Modal groups are now Class · Original Slot · New Slot · Status (badge + Requested At/Remarks + past Status Note). Full day names from the row data; time/duration math shared with the list (**Duration rows now "x hours y minutes"** — singular/plural, zero parts omitted: "2 hours", "1 hour 30 minutes"). The transient `.slot-line` chip CSS was removed (unused). Verified: approved modal = 4 groups × (4+6+6+2) rows, pending = (4+6+1+2), 0 console errors.

## [2026-10-02] User revision — modal slot rows as full-width blocks

Long slot strings no longer cram into the narrow label/value column: Original/New Slot rows stack (label above, value below) and the slot renders as a full-width highlighted chip (`.slot-line`, page-scoped CSS using `:has()` so only rows carrying it are affected — other rows keep the standard label/value layout). Pending New Slot keeps the inline muted PL explanation inside the chip. Verified: 2 slot rows stack column-direction, chip spans the modal body width, 0 console errors.

## [2026-10-02] User revision — modal slimmed (no Requested By; Awaiting tooltip)

- **"Requested By" row removed** (the lecturer already appears in the Class group) — the pending-only "Request" group dissolved; "Requested At" moved into the Replacement group. Modals now have exactly 2 sections: Class · Replacement.
- **"Awaiting PL approval" shows its description inline** (user: a modal should show detail, not hide it behind hover): the value now reads "Awaiting PL approval *— the Programme Leader (PL) has not confirmed this replacement; the new slot is not decided yet*" with the sentence in the shared `detail-value--muted` variant (replaces the previous tooltip attempt).
- Verified: pending modal = 2 groups / 8 rows (Requested By gone, tooltip live), approved modal unchanged, 0 console errors.

## [2026-10-02] User revision — modal grouped by category

The detail modal on upcoming-replacements-ui showed ~14 flat rows (overwhelming). Added an **additive `cfg.groups` option to the shared `openClassModal`** (one `DetailModal.section` per group; omit → original flat behavior, verified intact on student-my-timetable). The page now renders 3 tidy sections — **Class** (code, name, type, lecturer) · **Replacement** (status badge incl. holiday-Conflict remap parity, Original/New Slot with the new date format via shared `fmtSlot`, Status Note for past rows, Remarks) · **Request** (pending only: Requested At/By + Submitted→Under Review→Awaiting timeline). Dropped: redundant Status Description row; no View Full Request (AD-12 unchanged). `fmtSlot` hoisted to a top-level page helper shared by list + modal. Verified: pending/approved/conflict modals, 3 groups render, 0 console errors.

## [2026-10-02] User revision — summary cards v2 (Upcoming/Past/Hours replace Lectures/Tutorials)

Lectures/Tutorials cards dropped as low-value; the strip is now **Total · Upcoming · Pending · Past · Hours** (Approved removed — it duplicated Upcoming whenever Show Past is off). Upcoming = confirmed replacements with week ≥ current week (blue); Past = confirmed & already taken (**grey** `card-hours` variant — green read too close to Total); Hours = Σ slot durations of the visible rows (neutral). All still derive from the same visible predicate (AD-11). Verified: Week 10 = 2/0/2/0/3.5 · All+Past = 18/2/9/7/34 · All no-Past = 6/2/4/0/11 · Week 12 = 2/2/0/0/4; 0 console errors.

## [2026-10-02] User revision — slot time format

Original/New Slot cells (table + mobile cards) now render as: **`Week 14 · Fri, 30 Oct 2026, 12:00 PM to 1:30 PM (1.5 hrs) @ B103`** — full "Week N" prefix, `Fri,` day comma, 12-hour times joined with "to" (shared `to12h`), computed duration from the 30-min index space (`(end−start+1) × 0.5 hrs`, "1 hr" singular handled), `@ venue`. One shared `fmtSlot` helper serves both table (withWeek=true) and cards (withWeek=false — the card's week chip already carries the week). Verified: pending + approved rows, both duration variants present in data (1.5/2 hrs), 0 console errors.

## [2026-10-02] Promoted to shared — closeModal (§10.0 rule 7)

Audit triggered promote-on-3rd: the `ui-class-detail-modal` partial hardcodes `onclick="closeModal()"`, forcing every page to define a global — 7 pages did, 3 byte-identical. **Additive promote:** one shared `closeModal()` (targets `#classModal`) added to `ui-common.js` Modal-helpers block; existing pages' local copies override it (they load later, so nothing breaks); upcoming-replacements dropped its copy and keeps only the `closeModalOutside`/`closeOnEsc` wiring (those helpers were already shared). Remaining 6 pages' copies = recorded tech debt for a future cleanup change. Verified: shared function active on the page, modal × / `closeModal()` / Esc all close correctly, 0 console errors.

## [2026-10-02] User revision — persisted view state

Week selection + Show Past toggle now persist across refreshes via `localStorage` (`upcoming-replacements-view`, house pattern cf. my-request-history `saveFilters`/`restoreFilters`). Restored on load after the shared `populateWeekSelect` (validated against existing option values; `all` persists too); arrows recompute post-restore; Today button still returns to the current week and persists that. First-ever visit (no saved state) still lands on the current week, preserving the frozen load-state criterion. Verified: Week 3 + past → reload keeps both; All Weeks → reload keeps; Today → reload stays on Week 10; 0 console errors.

## [2026-10-01] User revision — slot dates, subject type, # column

- **Original/New Slot cells show real dates + week**: `Wk 10 · Thu 01 Oct 2026, 14:00 – 16:00 · A106` (dates derived from `generateWeekData()[r.week].days[di]` — built once per render; pending new slots stay "Awaiting PL approval"). Mobile cards show the same dates (week digit stays in the card chip).
- **Subject shows code · name (Type)**: e.g. `BMIT7075 · Mobile Application Development (L)` — table and cards.
- **Week column removed; `#` row-number column at leftmost** (per-view sequence 1…N).
- Modal unchanged (it already has dedicated Day/Date rows). Verified all three in browser, 0 console errors.

## [2026-10-01] User revision — status-badge tooltips

All status badges (table + mobile cards) now carry house-pattern `data-tip` tooltips (shared `initDataTipTooltips` hover system, zero new CSS/JS): **Replacement** → "Confirmed — attend the new slot shown", **Pending** → "Awaiting PL approval — the new slot is not confirmed yet", **Past** → "This replacement has already taken place". The **Show Past** toggle got a tooltip too ("Show replacement classes from weeks that have already passed"). Verified all on hover, 0 console errors.

## [2026-10-01] User revision — "All Weeks" option in the week selector

Week selector gains an **All Weeks** option (page-side only — prepended after shared `populateWeekSelect` so the value-based Week-10 load state is preserved; no `ui-common.js` changes). Safe with the shared arrow helpers because they are `selectedIndex`-based: at All, `‹` disables and `›` walks into Week 1; Today still jumps back to the current week. `renderUpcoming` branches on `value === 'all'` (shows all rows subject to the Show-Past predicate, sorted week → day → time). A **Week column** (digit only, e.g. `10`) was added to the table and a matching digit chip to mobile cards so All-view rows stay identifiable. Verified: load state intact (Week 10, 2 rows); All + toggle off = 6 future rows (6/2/4/3/3); All + past = 18 rows (18/9/9/10/8); arrows/Today/card chip all correct; 0 console errors.

## [2026-10-01] User revision — 5 summary cards (Combo A)

Summary strip expanded from 2 to **5 cards**, still derived from the same visible-rows predicate as the list (AD-11): **Total** (`card-total`, green) · **Approved** (`card-approved`) · **Pending** (`card-pending`) · **Lectures** (L count, `card-replacement` blue) · **Tutorials** (T count, `card-hours` neutral). Reuses existing theme.css color variants only (zero new CSS); Lectures/Tutorials get explicit hover descriptions via the partial's `description` override (their borrowed classes' defaults describe other meanings). Verified: Week 10 = 2/0/2/1/1, Week 12 = 2/2/0/2/0, Week 2 (past) = 3/0/3/2/1; 5 cards fit mobile without overflow; 0 console errors.

## [2026-10-01] User revision — table on desktop, summary below, Today pinned to week nav


Three user-directed tweaks to the Sprint-3 build (all page-scoped; no shared files touched):

| # | Change | Implementation |
|---|---|---|
| 1 | Desktop shows a **table** instead of cards; cards become **mobile-only** | `@include('partials.ui-grid-table', [...])` (house pattern, cf. my-request-history) with JS-built thead (Subject / Original Slot / New Slot / Lecturer / Status) + tbody rows (`role="button" tabindex="0"`, click/Enter/Space → same modal); `.upcoming-list` hidden on desktop, shown ≤768px where `#upcomingGridWrapper` hides (`.card-view` switching pattern) |
| 2 | Summary bar moved **below** the table/cards | `ui-summary-bar` include relocated after the list containers |
| 3 | Today button **sticks to the right of the week nav** (was floating mid-toolbar via shared `space-between`) | Page-scoped `.toolbar { justify-content: flex-start; }` override — week-nav → Today → Show Past group left-aligned |

Re-verified: desktop table renders with correct rows/badges/Past chip, row click + keyboard open the modal, empty week hides the table, mobile keeps cards + 44px targets, summary below on both breakpoints, 0 console errors.

## [2026-10-01] Sprint 3 build — real Upcoming Replacements UI (TASK-006 complete)


Built the student-facing page for real per SDD change `upcoming-replacements-ui`
(stub → full page). Two plan corrections surfaced during SDD — the §4 modal
snippet was missing required `event.start`/`event.end`, and the days API is
`generateWeekData()[w0].days` (the plan's `weekDays()` call was pseudocode) —
both recorded in `todo list/upcoming-replacements-ui-plan.md` §4.

### Files Modified

| File | Change |
|---|---|
| `resources/views/ui-design-templates/upcoming-replacements-UI-design-template.blade.php` | Full rebuild of the Phase-1 stub: toolbar (shared `ui-week-nav` + today button + show-past `.toggle-wrapper`), `ui-summary-bar` (Approved/Pending counts, student-context description), JS-rendered card list, `ui-empty-state`, shared `ui-class-detail-modal`. Page CSS (`.upcoming-card*`, ≤768px single column). Page JS — top-level `renderUpcoming`/`openReplacementModal`; init calls `populateWeekSelect('weekFilter', {selected: currentWeekIndex()+1})`; page-side today wiring per AD-10; delegated card click/Enter/Space (`.upcoming-card` is `<button>`); page-side `closeModal`/`closeModalOutside`/`closeOnEsc` per house pattern. Removed the inert `#copyToast` div (stub leftover). |
| `public/js/mock-data.js` | §2.12 `upcomingReplacements` 3 → 18 rows: 8 new pending ids 4–11 + 7 new replacement ids 12–18, all keyed to real `rsd3g2Base` slots + `rsd3g2Flags` weeks/dates so grid ↔ list never contradict. **Data fix:** pre-existing row id 3 (wk7 BMIT5678) `originalTime` corrected '09:00 – 12:00' → '09:00 – 11:00' — grid formula `hours[end+1]` for di2/start2/end5 gives 11:00, and the row's own modal recomputed 11:00, so the card contradicted its modal. |
| `public/css/theme.css` | +1 class `.badge-past` — neutral Past qualifier chip using `--color-surface-variant`/`--color-on-surface-variant`, §10.0 rule 2 (no new hex/rgb). |
| `page-changelogs/todo list/upcoming-replacements-ui-plan.md` | Status flipped to built (plan rule 8); two plan corrections appended to §4. |
| `page-changelogs/todo list/todo-list.md` | TASK-006 status `in_progress` → `completed`. |

### Key Decisions
- Load state defaults to the current week (Week 10 = 2 pending cards, 0 approved / 2 pending),
  selected via `populateWeekSelect`'s `selected` option rather than post-init dropdown writes.
- Pending modal `requestedAt` comes from `rsd3g2Flags` with the grid-default fallback
  '01 Sep 2026, 09:15 AM'; list modals carry no "View Full Request" footer button (students see
  status, not requests — staff-only affordance).
- Past (completed) replacements stay queryable: show-past toggle in the toolbar, neutral
  `.badge-past` chip + Past chips on cards, Status Note in the modal — approved-upcoming view
  stays the default.
- Page-side `closeModal`/`closeModalOutside`/`closeOnEsc` follow the house pattern of sibling
  student pages; only page-specific JS (`renderUpcoming`, `openReplacementModal`) lives in the page.
- Fully keyboard accessible: cards are real buttons (Tab + Enter/Space), delegated click handler.
- Mobile ≤768px: card grid collapses to a single column.

### Footnote
Pre-existing wk1 BMIT7074 row points at new venue B005, which is a Lab (P-only) carrying a
T class — pre-existing Phase-1 data, out of scope for this change.

### Verification
All 9 proposal §6 criteria pass in-browser:
- [x] Load state — Week 10 default, 2 pending cards, summary 0 approved / 2 pending
- [x] Pending modal `requestedAt` from flags with grid-default fallback '01 Sep 2026, 09:15 AM'
- [x] No "View Full Request" footer on list modals
- [x] Show-past toggle + Past chips + modal Status Note
- [x] Summary bar recomputes with filters (Approved/Pending)
- [x] Unflagged weeks show the empty state
- [x] Keyboard accessible (Tab to cards, Enter/Space open modal, Esc closes)
- [x] Mobile ≤768px renders single-column cards
- [x] 0 console errors

## Post-2026-10-02 cross-reference

The hardcoded badge feed built with this page (`notifBadge.textContent = … notificationCount`, `'notifCount' => 3`, and the server-side "3" echo) was removed by the `notifications-panel` change — the bell badge is now computed by `refreshNotifBadge()` in `ui-common.js`; see `notifications-panel-changelog.md`.

### Postscript — sweep-fixes-round-3 (2026-10-06, RENAME + sorting)

1. **Page renamed** "Upcoming Replacements" → **"Replacement History"** (route
   `/replacement-history-ui`, nav key `replacement-history`, pageKey
   `replacementHistory`): the list was never only upcoming — Pending + Confirmed +
   Past rows with `Show Past` and the Past summary card. Legacy localStorage view
   state under `upcoming-replacements-view` migrates once to
   `replacement-history-view`. Changelog + plan doc `git mv`'d to match.
2. **Sortable columns** (house pattern via shared `makeSortableHeader`): Subject,
   Original Slot (week→day→time), New Slot (awaiting-PL rows sort last), Lecturer,
   Status (Pending first, then confirmed; chronological tiebreak). `#` stays a row
   number and re-numbers under any sort; desktop table + mobile cards share one
   ordered array. Sort state is session-only.
3. **Fix: `All Weeks` de-duplicated** — the page historically hand-inserted its
   `All Weeks` option via a page-side IIFE *after* `populateWeekSelect`; when the
   shared helper's `includeAll` flag was switched on for this page both ran and the
   dropdown showed two. Single writer now = `populateWeekSelect(…, { includeAll: true })`
   (the flag already existed); the IIFE is gone, which also means the option survives
   the 768-breakpoint re-populate (the hand-inserted one silently vanished there).

**Addendum (2026-10-06, later same day):** the duplicate `All Weeks` fix above
superseded the page's own insert-IIFE entirely; default load keeps the current week
("Week 11") and a previously saved `All Weeks` view restores from VIEW_KEY.
