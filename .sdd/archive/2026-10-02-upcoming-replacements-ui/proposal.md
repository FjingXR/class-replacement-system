# Proposal — upcoming-replacements-ui

> **Change:** Build the real student-facing Upcoming Replacements UI, replacing the stub page
> **Role:** Student (view-only) — cohort RSD3(S1)G2
> **Explore baseline:** `.sdd/changes/upcoming-replacements-ui/explore-brief.md` (grilled + user-approved 2026-10-01)
> **Source plan:** `page-changelogs/todo list/upcoming-replacements-ui-plan.md` (TASK-006 Sprint 3)
> **Standing directive:** everything follows the plan by default; deviations only where grilled with the user

---

## 1. Why this change is needed

`/upcoming-replacements-ui` is the **last student-side stub**. TASK-006 Phase 1 (2026-08-24) shipped the route, nav link, stub template, and the `MockData.upcomingReplacements` dataset — the page body is still a placeholder ("UI design pending — Sprint 3"). Students have no list view of confirmed/pending replacement classes; they can only infer them from flags on the timetable grid.

Two concrete problems today:

1. **Missing FR 1.3 surface** — FR 1.3 ("Students shall be able to view the status of replacement requests") has no student-facing list realization; this page is it (mock phase).
2. **Grid↔list inconsistency in the current week** — today is 0-indexed week 9 (semester 2026-07-27, 14 weeks). The grid (`rsd3g2Flags[9]`) shows 2 pending flags (BMIT7075, BMIT2233), but the §2.12 dataset has rows only at weeks 1/2/7 (all past). When the page defaults to the current week — as the plan requires — it would show an empty list while the grid shows pending replacements, violating the plan's own rule ("New rows MUST reference real `rsd3g2Base` courses and weeks that exist in `rsd3g2Flags` so grid ↔ list never contradict").

## 2. In scope

1. **Build the page body** inside the existing stub template (same URL `/upcoming-replacements-ui`, same route, same nav wiring — nothing new created):
   - `ui-week-nav` single-week navigation (‹ › arrows + dropdown + Today button), defaults to current week
   - **Summary strip** (2 cards: `Approved`, `Pending`) recomputed per selected week — user decision (plan marks it optional; all 8 sibling pages have one)
   - **Replacement cards** per plan §5 anatomy: `code · name · type` + status badge, `original slot → new slot` with arrow, lecturer; whole card clickable
   - **Detail modal** via shared `openClassModal` with `Original Slot` / `New Slot` extra fields; pending shows "Awaiting PL approval" (no invented venue); approved shows full new-slot info. **Correction to the plan's §4 snippet (factual defect):** the synthetic event MUST carry `start`/`end` — `openClassModal` unconditionally calls `to12h(hours[event.start])` (ui-common.js:572) and crashes without them — paired to the day-index rule: approved → `start: newStart, end: newEnd` + `dayIndex: newDi`; pending → `start, end` (original) + `dayIndex: di`. Past rows additionally get a modal extra field noting the replacement has already taken place
   - **Empty state** ("No upcoming replacements this week") when the selected week has no visible rows
   - **Hide-past toggle** (user decision, not in plan): past weeks' rows hidden by default; toggle reveals them — follows the `my-request-history` toggle-wrapper house pattern
   - **Past chip + modal detail** (user decision): revealed past cards get a neutral "Past" chip (existing neutral tokens; NOT a third status color) and the modal notes the replacement has already taken place
   - **Remove the stub's inert `#copyToast` div** — this page has no copy affordance and never calls `initEvCodeCopy`; dead markup (plan's reuse inventory is a menu, not a mandate; logged in changelog)
   - Mobile responsive ≤768px (mandatory §10.0)
2. **Mock data top-up — full grid↔list parity** (`mock-data.js` §2.12; user decision amending grilled decision 2, 2026-10-01): add **15 rows** so all 18 `rsd3g2Flags` entries have a matching list row (12-row count initially stated to the user was an undercount — wk1 BMIT7072/BMIT5678 and wk7 BMIT7074 flags were missed; intent approved = full parity):
   - wk1: BMIT7072, BMIT5678 (replacement) · wk2: BMIT7073, BMIT7075 (pending) · wk3: BMIT7070 (replacement) · wk4: BMIT7071, BMIT5678 (pending) · wk5: BMIT2233 (replacement) · wk7: BMIT7074 (replacement) · wk9: BMIT7075, BMIT2233 (pending) · wk11: BMIT7072, BMIT5678 (replacement) · wk13: BMIT7071, BMIT2233 (pending)
   - Keep the 3 existing rows (weeks 1/2/7) → dataset totals 18 rows
   - Original slots (`di/start/end`) copied from each course's `rsd3g2Base` row; pending rows keep `requestedAt: ''` + null new-slot fields per contract
   - The **7 new** `replacement`-status rows (wk1 ×2, wk3, wk5, wk7, wk11 ×2) need **mock-invented new slots** (grid encodes only the original-slot flag + requested-date remark) — plausible times inside the row's week that do not collide with that course's other slots. The 2 pre-existing replacement rows (wk1 BMIT7074, wk7 BMIT5678) already carry hand-set new slots and stay untouched
3. **Documentation**: update `page-changelogs/upcoming-replacements-ui-changelog.md`, flip the status in `upcoming-replacements-ui-plan.md`, update `todo-list.md` TASK-006.

## 3. Out of scope

- Staff-side pages (request-approval, replacement-home, venue-timetable, replacement-arrangement) — untouched
- Backend/migrations/models — mock phase only
- New shared partials or theme.css promotions unless promote-on-3rd triggers during build (`.badge-past` and `.upcoming-card*` are single-page → `@section('page-styles')`)
- Linking cards to `/my-request-history-ui` — cohort announcements carry no `requestId`; the shared modal's "View Full Request" button only renders when `event.requestId` exists, so it stays absent by default
- Rejected-status visibility for students — settled by the TASK-006 drop decision (students view upcoming only); not relitigated
- Email notifications (FR 1.9) — backend-deferred; the nav notif badge remains a mock stand-in

## 4. FR/NFR traceability (logic check per sdd-propose-ui-page spec)

*FR numbers synced to latest FR&NFR.md (48 FRs, 2026-10-01).*

| Req | Relevance | Logic for mock phase |
|---|---|---|
| FR 1.2 (view cohort timetable) | Indirect — grid is the sibling page | n/a here |
| **FR 1.3** (view request status for cohort, pending/approved) | **Primary driver** | ✅ read-only list + status badges; pending/approved realized now; "rejected" intentionally out (see §3) |
| **FR 1.4** (view upcoming replacement details — new date, time, venue) | **Primary driver** (this FR is literally this page) | ✅ cards show new date/time/venue; whole card opens read-only detail modal |
| **FR 1.5–1.8** (cannot create/edit/delete/modify) | **Binding constraint** | ✅ no create/edit/delete actions anywhere on the page; whole cards open a read-only modal |
| FR 1.9 (email on timetable updates) | Deferred | 🔲 backend (Sprint 3 real integration); notif badge is the mock stand-in |
| NFR 3.1 (responsive) | Mandatory | ✅ ≤768px card layout, drawer from shared layout, 44px touch targets |
| NFR 1.3 (pages < 2s) | Trivially met | ✅ static mock, no queries |

No FR/NFR contradiction with CodingMAIN.md found. Status badges reuse the **existing** `.badge-replacement` / `.badge-pending` classes unchanged (`.badge-pending` bg = `--color-warning` per plan §3 + `theme.css:665` — authoritative for this page; theme.css untouched). *Known repo-level drift, out of scope here:* CodingMAIN §10.0 table A names `--color-tertiary` for Pending while shipped theme.css uses `--color-warning` (both yellow) — flagged to the user for a separate cleanup decision.

## 5. Impact scope

| File | Change |
|---|---|
| `resources/views/ui-design-templates/upcoming-replacements-UI-design-template.blade.php` | **Main build** — replace stub placeholder with toolbar (week nav + toggle), summary strip, card list, empty-state logic, render JS in `@section('page-scripts')`, page CSS in `@section('page-styles')` |
| `public/js/mock-data.js` | §2.12 `upcomingReplacements`: +15 rows → 18 total (full `rsd3g2Flags` parity, weeks 1/2/3/4/5/7/9/11/13); original slots from `rsd3g2Base`; 7 new replacement rows with invented collision-free new slots |
| `page-changelogs/upcoming-replacements-ui-changelog.md` | Append the build entry |
| `page-changelogs/todo list/upcoming-replacements-ui-plan.md` | Flip "Status: Stub shipped" → built |
| `page-changelogs/todo list/todo-list.md` | Mark TASK-006 Sprint 3 item done |
| `public/js/ui-common.js` | **Only if needed** — tiny optional extension to `openClassModal` for the past-status description (plan §4 explicitly allows extending the shared fn; never copying it) |

Not touched: `routes/web.php` (route exists), `student-my-timetable-UI-design-template.blade.php` (nav already points here), all staff templates, DB/migrations. *Soft-freeze annotations (design round 1, 2026-10-01 — declarative clarifications, decisions unchanged):* (1) `theme.css` receives **one additive class** (`.badge-past`) per §10.0 rule 2 mandate — refined by design AD-7, overriding the "unless promote-on-3rd" default above. (2) §3's requestId rationale was factually imprecise: 4 pending flags DO carry tuple index-4 requestIds; the **decision** stands (list modal omits requestId, per plan §4 wiring) — see design AD-12 for the corrected premise and the accepted grid↔list asymmetry.

## 6. Success criteria

> **Week indexing (binding):** `weekRanges` select values are 1-based strings `"1"…"14"` (ui-common.js:1832); `upcomingReplacements[].week` and `rsd3g2Flags` keys are **0-based** (plan §2 contract). Every week comparison uses `selectValue - 1`. Current 0-based week = 9 (displayed "Week 10").

1. Page loads on 0-based week 9 (dropdown shows "Week 10") showing 2 pending cards + summary 0/2 — grid and list agree
2. **Full parity, list→grid direction:** every §2.12 row's (week, code) pair is backed by an `rsd3g2Flags` entry, and every one of the 18 flag entries (weeks 1/2/3/4/5/7/9/11/13) has a matching list row — toggle-ON browsing shows complete data for every flagged past week
3. Approved card → blue badge + full new slot; click → modal opens without errors (synthetic event carries `start`/`end` + `dayIndex: newDi`) and shows Original/New Slot fields
4. Pending card → yellow badge + "Awaiting PL approval"; modal shows no invented venue (`dayIndex: di`, original `start`/`end`)
5. Past toggle OFF → browsing weeks 1–8 shows empty states where the filter yields nothing visible; ON → all flagged past weeks render rows with the neutral "Past" chip; modal notes completion
6. Summary counts recompute on week change; unflagged weeks (0/6/8/10/12) show the empty state
7. Mobile ≤768px usable (cards stack, 44px targets)
8. Colors only via theme.css tokens; no hardcoded hex/rgb; `.badge-replacement`/`.badge-pending` reused unchanged
9. Changelog + plan + todo-list updated; lint/types checks pass (no new failures)
