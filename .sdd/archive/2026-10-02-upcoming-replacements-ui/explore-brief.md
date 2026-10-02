# Explore Brief — upcoming-replacements-ui

> **Mode:** `/sdd-explore` grilling session (2026-10-01)
> **Goal:** Build the real student-facing Upcoming Replacements UI, replacing the stub at `/upcoming-replacements-ui`
> **Source plan:** `page-changelogs/todo list/upcoming-replacements-ui-plan.md` (TASK-006 Sprint 3) — user directive: **"everything follows the plan by default; if not in plan or unsure, ask"**
> **Baseline:** HEAD of `fjing` at explore time = `65a6805`

---

## 1. Problem

`/upcoming-replacements-ui` is the **last student-side stub**: nav link live, route live, dataset live, but the page body is only a placeholder empty state ("UI design pending — Sprint 3"). Students have no list view of confirmed/pending replacement classes for their cohort (RSD3(S1)G2) — they can only see flags on the timetable grid.

Root cause: TASK-006 was deliberately split — Phase 1 (nav/route/stub/dataset) shipped 2026-08-24; Sprint 3 (real UI) was deferred and never built.

## 2. Decisions Locked During Grilling

| # | Decision | Choice | Rationale |
|---|---|---|---|
| 1 | Scope | Build the stub page per the existing plan | Only unfinished student surface; plan is complete (data contract, reuse inventory, layout, checklist) |
| 2 | Demo data | **Add 6 new rows** for weeks 9/11/13 mirroring `rsd3g2Flags`; **keep** the 3 existing rows (weeks 1/2/7) | Fixes grid↔list contradiction in the current week (wk 9) and makes the page demo well on load |
| 3 | Week nav | **Single-week view** (Option A): `ui-week-nav` partial with ‹ › arrows + dropdown + Today button; default = current week | Matches "Upcoming" semantics + timetable mental model; matches plan §5 and §7 checklist |
| 4 | Cards | Plan §5 anatomy exactly; click → shared `openClassModal` with Original/New Slot `extraFields`; no per-card buttons, no request-history link | Plan chose Option A cards; cohort announcements have no `requestId` to link |
| 5 | Summary bar | **Include** — 2 cards (`Approved`, `Pending`), counts per selected week | All 8 sibling pages have a summary strip; plan marks it optional; 2 cards don't overwhelm |
| 6 | Past weeks | **Hidden by default; toggle to reveal** | User decision (not in plan); mirrors `my-request-history`'s "Exclude Completed" toggle pattern |
| 7 | Past status surface | **Neutral "Past" chip on card + detailed past status inside modal** | User decision; at-a-glance signal on the list + full detail in modal; no third status color invented |

## 3. Rejected Approaches (and why)

| Approach | Why rejected |
|---|---|
| Table rows instead of cards | Plan already chose Option A cards; old→new slot transition reads better as a card; "zero extra clicks" |
| Inline card expand instead of modal | Violates §10.0 "detail in modals, not page surface"; would duplicate `openClassModal` |
| "All Weeks" dropdown option (my-request-history style) | Muddies "Upcoming" message; mixes past+future in one list; contradicts plan §5/§7 (per-week cards + per-week empty state) |
| Replace past rows with future-only rows | Loses back-browsing demo data; grid still flags weeks 1/2 so list must match (grid↔list rule) |
| Keep old data + default week nav to nearest week with data | Hides the grid↔list contradiction instead of fixing it; non-standard default |
| Leave data as-is | Page would show empty state on load in week 9 while the grid shows 2 pending flags — direct violation of the plan's consistency rule |
| Third "past/completed" status color | Forbidden — plan §3: "Never invent a third status color"; §10.0 same-meaning-same-color |
| Per-card "Details" button | Plan: whole card is clickable; icon-button adds noise without adding a capability |

## 4. Final Solution

### 4.1 Page composition (top → bottom)

```
PageHeader   — title 'Upcoming Replacements', desc (stub's existing text), chips: [RSD3(S1)G2]
Toolbar      — ui-week-nav (‹ › + #weekSelect + Today btn) + toggle "show past" (OFF by default)
SummaryBar   — 2 cards: Approved (#sumApproved), Pending (#sumPending)  ← counts for selected week
Card list    — .upcoming-card grid for rows where week === selectedWeek AND (row.week >= currentWeek OR showPast ON)
EmptyState   — "No upcoming replacements this week" when the filtered list is empty
CopyToast    — #copyToast (stub already wires it — keep) *(superseded 2026-10-01 — see proposal §2: removed, inert, no copy affordance)*
```

### 4.2 Complete status → color mapping (canonical, plan §3 — unchanged)

| status | Badge class | Token bg | Card shows |
|---|---|---|---|
| `replacement` (approved) | `.badge-replacement` | `var(--color-primary)` | full new slot: `newDay, newTime · newVenue` |
| `pending` (awaiting PL) | `.badge-pending` | `var(--color-warning)` | "Awaiting PL approval" (never an invented venue) |
| past row (derived, not a dataset status) | `.badge-past` (NEW, neutral gray token) + status badge kept | neutral token from theme.css | card chip "Past"; modal status description notes it has already taken place |

### 4.3 Card anatomy (plan §5)

```
┌ code · name · type ────────── [badge] ┐
│ originalDay, originalTime · originalVenue  →  newDay, newTime · newVenue │
│ lecturer                                                    [Past chip?] │
└ click = detail modal ─────────────────┘
```

- Sort within a week: by `di` (day index), then `start` (assumption — plan silent)
- Pending card: new-slot area = "Awaiting PL approval"; `requestedAt` stays `''` per data contract

### 4.4 Modal wiring (plan §4 — `openClassModal`, ui-common.js:564)

- Synthetic event: `code, name, type, lecturer, venue: newVenue||originalVenue, status, remarks: 'Requested '+requestedAt`
- `dayIndex`: `newDi` when approved, `di` when pending; `days` derived from `MockData.semester` for the row's week
- `extraFields`: `Original Slot` = `originalDay, originalTime · originalVenue`; `New Slot` = `newDay, newTime · newVenue` or "Awaiting PL approval"
- Past rows: modal must communicate completion — prefer an `extraFields` entry (e.g. status detail "This replacement has already taken place"); only if placement is awkward, extend `openClassModal` with an optional cfg flag (plan §4 explicitly allows extending the shared fn — never copy it)
- Shared modal's pending 3-step timeline renders automatically for `status:'pending'`

### 4.5 Past-toggle behavior (user decision)

- Checkbox toggle in the toolbar next to the week nav; **default OFF** (past rows hidden)
- Toggle ON reveals past rows when browsing a week < currentWeek; current/future weeks unaffected
- Follow the `my-request-history` toggle-wrapper house pattern (checkbox + track/thumb + label; exact label e.g. "Show Past" — settle at design following house style)

### 4.6 Demo data changes (decision 2)

> **⚠ AMENDED 2026-10-01 (proposal review round 1, user-approved):** decision 2 is upgraded to **full parity** — all 18 `rsd3g2Flags` entries get matching §2.12 rows (+15 rows, 18 total), not just weeks 9/11/13. The original "6 rows, weeks 9/11/13" framing undercounted the flagged weeks (missed wk1 BMIT7072/BMIT5678, wk7 BMIT7074). Success criterion is the list→grid direction: every §2.12 (week, code) pair backed by a flag entry, and every flag entry has a row. The **7 new** replacement-status rows carry mock-invented collision-free new slots (the 2 pre-existing replacement rows already carry hand-set new slots).

- §2.12 `upcomingReplacements` (mock-data.js:657): keep rows 1–3 (weeks 1, 2, 7); **add 6 rows**: *(superseded 2026-10-01 — see amendment above; full parity = +15 rows, 18 total)*
  - wk 9: `BMIT7075` pending, `BMIT2233` pending (matches `rsd3g2Flags[9]`)
  - wk 11: `BMIT7072` replacement, `BMIT5678` replacement (matches `rsd3g2Flags[11]`)
  - wk 13: `BMIT7071` pending, `BMIT2233` pending (matches `rsd3g2Flags[13]`)
- All 8 flagged course codes verified present in `rsd3g2Base` (mock-data.js:457)
- Original slot (`di/start/end`) for each new row MUST be copied from that course's `rsd3g2Base` row (same 30-min index space; hours map ui-common.js:316)
- Pending rows: `newDi/newStart/newEnd/newDay/newTime/newVenue = null`, `requestedAt: ''` (contract §2 of plan)
- wk11 approved rows: new-slot values are **mock-invented** (the grid encodes only the original-slot flag + requested-date remark) — pick plausible times inside week 11 that don't collide with the same course's other slots
- Week model: semester 2026-07-27, 14 weeks, week index 0-based; today (2026-10-01) = week 9

## 5. Cross-module data flows

| From | To | What |
|---|---|---|
| Blade page `upcoming-replacements-UI-design-template.blade.php` | `layouts.ui-template` | `@extends` with `activeNav='upcoming-replacements'`, `pageKey='upcomingReplacements'`, student navItems array (2 items), `notifCount` from stub |
| Page JS | `public/js/mock-data.js` §2.12 | reads `MockData.upcomingReplacements` (read-only — slice/spread before any mutation) |
| Page JS | `public/js/ui-common.js` | `populateWeekSelect`, `weekData`/`weekRanges` + `MockData.semester` for week math; `openClassModal` for detail; `initEvCodeCopy('copyToast')` *(superseded 2026-10-01 — see proposal §2: copyToast removed)* |
| Blade page | `partials/ui-page-header`, `partials/ui-week-nav`, `partials/ui-today-btn`, `partials/ui-summary-bar`, `partials/ui-empty-state` | includes only — no new partials unless promote-on-3rd triggers |
| Page CSS | `public/css/theme.css` tokens | only custom-property tokens; new classes `.upcoming-card*`, `.badge-past` go in `@section('page-styles')` (single-page scope; promote to theme.css only if a 2nd page needs them) |

Staff pages untouched; no backend; no new datasets beyond §2.12 edits.

## 6. Verification (plan §7, binding)

- [ ] Student nav renders both items; active state follows `activeNav`
- [ ] Cards show only the selected week's rows (weeks 1/2/7/9/11/13 have demo data)
- [ ] Defaults to current week (9): 2 pending cards visible; Approved/Pending summary = 0/2
- [ ] Approved row → blue badge, full new-slot info; modal shows Original/New Slot fields
- [ ] Pending row → yellow badge, "Awaiting PL approval"; modal shows no invented venue
- [ ] Empty weeks → empty state visible
- [ ] Past toggle OFF: browsing weeks 1/2/7 shows empty state; ON: rows appear with Past chip; modal notes completion
- [ ] Summary counts recompute on week change
- [ ] Responsive ≤768px (cards stack, 44px touch targets)
- [ ] Grid↔list consistency: every `rsd3g2Flags` entry for RSD3(S1)G2 has a matching §2.12 row
- [ ] Playwright via `with_server.py` if browser available (unavailable at last session end — fallback: curl + grep on rendered HTML)
- [ ] Update `page-changelogs/upcoming-replacements-ui-changelog.md` + flip status in the plan + `todo-list.md` TASK-006 Sprint 3

## 7. Known Open Questions (carry into /sdd-propose)

1. **Toggle label/semantics** — "Show Past" unchecked vs "Hide Past" checked (my-request-history uses "Exclude Completed" checked). Pick the house-consistent form at design time.
2. **Modal past-status mechanism** — extraFields entry vs tiny optional cfg flag on `openClassModal`; decide when the design sees exact row ordering.
3. **wk11 invented new-slot times** — must be plausible and collision-free; exact values chosen at implementation.
4. **Browser verification** — Playwright/desktop browser was unavailable at the previous session end; if still down, verify via cache-cleared server + curl/grep.

## 8. Out of Scope

- Staff-side pages (request-approval, replacement-home, venue-timetable)
- Backend/DB work (mock phase only)
- New shared partials or theme.css promotions (unless promote-on-3rd triggers during build)
- Linking cards to `/my-request-history-ui` (no `requestId` on cohort announcements)
