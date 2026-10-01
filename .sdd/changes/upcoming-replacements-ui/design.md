# Design — upcoming-replacements-ui

> **Frozen inputs:** `proposal.md` (frozen 2026-10-01 after 3 review rounds), `explore-brief.md` (incl. §4.6 full-parity amendment)
> **Source plan:** `page-changelogs/todo list/upcoming-replacements-ui-plan.md`
> **House-law precedence:** CodingMAIN §10.0 rules are non-negotiable; where this design refines the proposal, it says so explicitly.

---

## 1. Technical approach

Single-page build inside the existing stub template. No new route, no new partials, no backend. Three layers:

1. **Blade (`@section('content')`)** — static shell: page header (unchanged from stub) → toolbar (`ui-week-nav` + show-past toggle) → summary strip (`ui-summary-bar`, 2 cards) → card-list container (empty div, JS-rendered) → `ui-empty-state` → detail modal (`ui-class-detail-modal` via shared layout).
2. **Page CSS (`@section('page-styles')`)** — `.upcoming-card*` card layout + ≤768px adjustments only.
3. **Page JS (`@section('page-scripts')`)** — a small render module: week-state → filter `MockData.upcomingReplacements` → render cards/summary/empty state; card click → `openClassModal`. **Render/event functions (`renderUpcoming`, `openReplacementModal`, the today handler) are declared at script top level, NOT inside a `DOMContentLoaded` closure** — inline handler strings (`weekFilterChanged({onRebuild: renderUpcoming})`) execute in global scope (precedent: `my-request-history`, `replacement-home`); only the initial render call sits inside `DOMContentLoaded`.

Shared modules consumed read-only: `mock-data.js` (§2.12), `ui-common.js` (`populateWeekSelect`, `weekFilterChanged`, `prevWeekFilter`, `nextWeekFilter`, `updateWeekArrowState`, `currentWeekIndex`, `generateWeekData`, `to12h`, `openClassModal`), `theme.css` (tokens, badges, summary, toggle, empty-state, week-nav, modal + its mobile bottom-sheet rules).

## 2. Architecture decisions

| # | Decision | Rationale |
|---|---|---|
| AD-1 | Reuse `prevWeekFilter`/`nextWeekFilter`/`updateWeekArrowState` verbatim by naming the select **`weekFilter`** | These shared helpers hard-target `document.getElementById('weekFilter')` (ui-common.js:1800–1814) — using the same id gets arrow + change-dispatch behavior for free; deviating from the id would force copying them (forbidden) |
| AD-2 | Week state derived, not stored: `w0 = parseInt(weekFilter.value, 10) - 1` | `weekRanges` values are 1-based strings (ui-common.js:1832); dataset weeks + `rsd3g2Flags` keys are 0-based. Single conversion point, no stale state |
| AD-3 | Days source = `generateWeekData()[w0].days` | The plan §4's `weekDays()` is pseudocode (doesn't exist). `generateWeekData()` pushes sequentially from week 1, so **array index = 0-based week**; `.days` supplies exactly the `{abbr, date, holiday}` shape `openClassModal` needs (ui-common.js:583–584) |
| AD-4 | JS renders cards into a container (like `my-request-history` renders its table); Blade holds no per-card markup | 18 rows × week filtering is data-driven; static Blade duplication would violate single-source rules |
| AD-5 | Past filter is a **render-time predicate**, not a data mutation: visible ⟺ `r.week === w0 && (r.week >= currentWeekIndex() \|\| showPast)` | `MockData` is read-only (AGENTS.md rule 4); toggle state stays in DOM (`#showPast.checked`) |
| AD-6 | Pending modals get `requestedAt`/`requestedBy` **populated from `rsd3g2Flags[w]` tuple index 3 + the row's lecturer**; the §2.12 dataset contract is untouched | Kills the grid↔list asymmetry the round-2 review flagged (grid fabricates these; list would show '—'). Index 3 is the datetime string; index 4 is `requestId` (never used here — no request-history link). Flags with short tuples (no index 3) fall back to the **same default the grid fabricates** (`'01 Sep 2026, 09:15 AM'`, student-my-timetable:101–102) so the modal never disagrees with the grid for the same (week, code) |
| AD-7 | `.badge-past` class goes in **`theme.css`**, not `@section('page-styles')` | §10.0 rule 2 (non-negotiable): a new status-like label = ONE class in theme.css so a future page reuses the identical name+color. Neutral pair = `--color-surface-variant` bg / `--color-on-surface-variant` text (same semantic family as table C "Cancelled" / legend B "Reserved"). Not a third *status color* — statuses stay blue/yellow; "Past" is a neutral qualifier chip beside them |
| AD-8 | `.upcoming-card*` layout CSS stays in `@section('page-styles')` | Genuine page-specific layout (no other page has card lists of slot transitions); promote-on-3rd does not trigger |
| AD-9 | No `openClassModal` extension needed | Past-status note fits via `extraFields` (verified: ui-common.js:599–602 inserts extras before the Status row); the shared function stays untouched → smaller blast radius than plan §4's "extend if needed" allowance |
| AD-12 | **`requestId` is NOT passed to the list modal — accepted asymmetry** (plan-governed: the plan §4 snippet omits it) | 4 pending flags carry a tuple index 4 `requestId` (wk2/9/13 BMIT2233, wk4 BMIT5678), and the shared modal would render "View Full Request" if it were passed. The plan's wiring deliberately omits it — students were dropped from request history (TASK-006) and the link target is a staff-scoped page. Consequence, recorded as accepted: clicking the same pending slot on the *grid* offers "View Full Request"; on *this list* it doesn't. (The frozen proposal's rationale "cohort announcements carry no requestId" was factually imprecise — corrected via soft-freeze annotation in review-log; the **decision** — omit requestId — is unchanged and plan-compliant) |
| AD-10 | Today button wired page-side: set `weekFilter.value = String(currentWeekIndex() + 1)` + dispatch `change` | Shared `jumpToToday()` targets `#weekSelect` (different id, timetable-grid pattern); pages with `#weekFilter` wire their own handler (precedent: CohortTimetable:430 wires `todayBtn` itself) |
| AD-11 | Summary counts derived from the same visible-rows predicate as the card list | One filter, two projections — counts can never disagree with the cards |

## 3. Component map (Blade)

```
@extends('layouts.ui-template', [ stub params unchanged: activeNav, pageKey, navItems, notifCount ])
@section('title', 'Upcoming Replacements')
@section('page-styles')  → .upcoming-card* + ≤768px media query
@section('content')
  ui-page-header        (unchanged stub include: title, description, chips [RSD3(S1)G2])
  <div class="toolbar">
    ui-week-nav         { prevOnclick:'prevWeekFilter()', nextOnclick:'nextWeekFilter()',
                          selectId:'weekFilter', selectOnclick:'weekFilterChanged({onRebuild: renderUpcoming})',
                          showTodayBtn: true }
    toggle-wrapper      <input id="showPast" type="checkbox"> + track/thumb + label "Show Past"
                        (markup = theme.css .toggle-wrapper pattern, cf. my-request-history:192–196)
  Initial week selection (BINDING for criterion 1): the partial renders an EMPTY select —
                        populateWeekSelect('weekFilter', { selected: currentWeekIndex() + 1 })
                        MUST be called during init (cfg.selected path, ui-common.js:1947) so the
                        page loads on 0-based week 9 ("Week 10"), never "Week 1".
  </div>
  ui-summary-bar        { cards: [ {class:'card-approved', valueId:'sumApproved', label:'Approved'},
                                   {class:'card-pending',  valueId:'sumPending',  label:'Pending',
                                    description: 'Replacement requests still <strong>waiting for PL approval</strong> for your cohort.'} ] }
                        (explicit description override — shared default is staff-flavored)
  <div id="upcomingList" class="upcoming-list"></div>   ← JS render target
  ui-empty-state        { title:'No replacements this week', text:'No upcoming replacement classes for RSD3(S1)G2 in the selected week.' }
@section('page-scripts') → render module + modal + today wiring
```

Inert stub `#copyToast` div **removed** (proposal §2; logged in changelog).

## 4. Data flow

```
MockData.upcomingReplacements (§2.12, 18 rows, read-only)
        │  filter: r.week === w0 && (r.week >= currentWeekIndex() || #showPast.checked)
        ▼
   visible rows (sorted by di, then start)  ──────────────► renderUpcoming()
        │                                                        ├─► #upcomingList  cards
        │                                                        ├─► #sumApproved / #sumPending
        │                                                        └─► empty state show/hide
        └─ card click ─► openReplacementModal(r)
                synthetic event = {
                  code, name, type, lecturer,
                  venue:  r.newVenue || r.originalVenue,
                  status: r.status,                        // 'replacement' | 'pending'
                  start:  r.status === 'pending' ? r.start  : r.newStart,   // REQUIRED (ui-common.js:572)
                  end:    r.status === 'pending' ? r.end    : r.newEnd,     // REQUIRED
                  remarks: r.requestedAt ? 'Requested ' + r.requestedAt : '',
                  requestedAt: pending ? (flagsTuple[3] || '01 Sep 2026, 09:15 AM') : undefined,  // AD-6, incl. grid's default fallback
                  requestedBy: pending ? r.lecturer        : undefined,     // AD-6
                }
                dayIndex = r.status === 'pending' ? r.di : r.newDi
                days     = generateWeekData()[r.week].days
                title    = r.code + ' — Replacement'
                extraFields = [
                  { label:'Original Slot', value: r.originalDay + ', ' + r.originalTime + ' · ' + r.originalVenue },
                  { label:'New Slot',      value: r.newDay ? (r.newDay + ', ' + r.newTime + ' · ' + r.newVenue)
                                                           : 'Awaiting PL approval' },
                  ...past rows only: { label:'Status Note', value:'This replacement has already taken place.' },
                ]
                // NOTE: pending rows whose ORIGINAL day is a holiday (wk2 BMIT7075 di3, wk4 BMIT5678 di2)
                // render badge 'Conflict' inside the modal (shared openClassModal remap, ui-common.js:569) —
                // expected; the CARD badge stays yellow 'Pending' and the grid behaves identically.
```

Events: `weekFilter` change → `weekFilterChanged({onRebuild: renderUpcoming})` → `updateWeekArrowState()` (shared). `#showPast` change → `renderUpcoming()`. `#todayBtn` click → AD-10. Card keyboard support: `role="button"`, `tabindex="0"`, Enter/Space triggers the same modal (a11y parity with click).

## 5. Mock data spec — §2.12 additions (15 rows, 18 total)

Authoring rules (binding for tasks):
- Original slot `di/start/end` copied verbatim from the course's `rsd3g2Base` row; `originalDay/originalTime` display strings derived with `to12h(hours[start])` / `hours[end+1] || add30min(hours[end])` — same math the grid uses
- Pending rows: `newDi/newStart/newEnd/newDay/newTime/newVenue = null`, `requestedAt: ''` (dataset contract); list/modal populate from flags at render (AD-6)
- Replacement rows: 2 pre-existing (wk1 BMIT7074, wk7 BMIT5678) keep their hand-set new slots; the **7 new** rows use the invented slots below. Collision set = **every** `rsd3g2Base` row **+ the pre-existing rows' hand-set new slots** (mock-data.js:663: wk1 7074 → di3 4–7; :677: wk7 5678 → di0 12–15) **+ the other invented slots in the same week** (round-1 review caught wk1 7072 originally colliding with 7074's di3 4–7 — re-authored to di3 8–11). All are L/T-safe venues — no labs for L
- Row `id`s: existing 1–3 untouched; new rows pinned sequentially **4–18** in the §5 table order
- Changelog footnote: pre-existing wk1 BMIT7074 new venue B005 is a Lab (P-only) carrying a T class — pre-existing Phase-1 data, out of scope, noted to pre-empt parity nitpicks
- Modal Remarks phrasing: list modals say "Requested 26-Aug-2026" while grid modals show the raw flag remark `26-Aug-2026` — deliberate phrasing difference per brief §4.4, not a parity bug

| wk | code | status | original (from base) | new slot (invented) | new venue |
|----|------|--------|----------------------|----------------------|-----------|
| 1 | BMIT7072 | replacement | di2, 12–15 (Wed) | newDi3, 8–11 (Thu 12:00–14:00) | B101 |
| 1 | BMIT5678 | replacement | di2, 2–5 (Wed) | newDi0, 12–15 (Mon) | B110 |
| 2 | BMIT7073 | pending | di4, 0–3 (Fri) | — null — | — |
| 2 | BMIT7075 | pending | di3, 12–15 (Thu) | — null — | — |
| 3 | BMIT7070 | replacement | di0, 8–11 (Mon) | newDi2, 8–11 (Wed) | B102 |
| 4 | BMIT7071 | pending | di1, 4–7 (Tue) | — null — | — |
| 4 | BMIT5678 | pending | di2, 2–5 (Wed) | — null — | — |
| 5 | BMIT2233 | replacement | di4, 8–10 (Fri) | newDi3, 8–11 (Thu) | B103 |
| 7 | BMIT7074 | replacement | di1, 8–11 (Tue) | newDi2, 8–11 (Wed) | B104 |
| 9 | BMIT7075 | pending | di3, 12–15 (Thu) | — null — | — |
| 9 | BMIT2233 | pending | di4, 8–10 (Fri) | — null — | — |
| 11 | BMIT7072 | replacement | di2, 12–15 (Wed) | newDi3, 4–7 (Thu) | B101 |
| 11 | BMIT5678 | replacement | di2, 2–5 (Wed) | newDi0, 12–15 (Mon) | B110 |
| 13 | BMIT7071 | pending | di1, 4–7 (Tue) | — null — | — |
| 13 | BMIT2233 | pending | di4, 8–10 (Fri) | — null — | — |

`type/lecturer/name/cohort` come from the `rsd3g2Base` row of the same code. `requestedAt` (replacement rows) = the date string from the matching `rsd3g2Flags` tuple index 2 (e.g. '26-Aug-2026'), formatted like existing rows.

## 6. CSS plan

- **theme.css (+1 class):** `.badge-past { background: var(--color-surface-variant); color: var(--color-on-surface-variant); }` placed beside the existing `.badge-*` block (~line 664). Tokens only. Documented as the neutral "Past" qualifier per §10.0 rule 2 / AD-7.
- **page-styles:** `.upcoming-list` (CSS grid, `repeat(auto-fill, minmax(320px, 1fr))`, gap from spacing token), `.upcoming-card` (surface bg token, radius token, hover elevation = cell-hover pattern, `cursor: pointer`), `.upcoming-card-head/-slots/-lect` rows, `.slot-arrow` (→ glyph in primary token), `.upcoming-card.past` (reduced emphasis via `1px solid var(--color-outline)` border + `--color-on-surface-variant` text — **no opacity treatment**: theme.css has no opacity token, border option chosen to delete the fork). No hex/rgb anywhere.

## 7. Promoted to shared

| Artifact | Home | Trigger |
|---|---|---|
| `.badge-past` | `theme.css` | §10.0 rule 2 mandate for new status-like labels (not duplication-count) |

Everything else reuses existing shared components unchanged. No new partials; no `ui-common.js` changes; `openClassModal` unmodified (AD-9).

## 8. Toast/undo bar

**None.** The page is view-only (FR 1.4): no create/submit/cancel/approve/reject actions exist, so §10.0 rule 10 has no trigger. Documented per spec.

## 9. Mobile view (≤768px)

- **Toolbar:** week-nav + toggle wrap and stack; select full-width (shared `.week-nav` responsive rules + page media query for the toggle row)
- **Cards:** single-column stack (auto-fill grid collapses naturally); touch target = whole card ≥44px; tap opens the modal
- **Summary:** 2 cards in the shared 2-column mobile grid (theme.css handles it)
- **Modal:** shared bottom-sheet rules from theme.css mobile block apply automatically (`openClassModal` renders the shared shell)
- **Typography:** page title/card text inherit shared `clamp()` rules; no page-specific overrides needed
- Shared drawer/hamburger/safe-area/viewport behavior comes from the layout — nothing page-specific to add

## 10. Dependencies

| On | For | Risk |
|---|---|---|
| `mock-data.js` §2.12 | 15 new rows (§5 table) | Low — additive; existing 3 rows untouched |
| `ui-common.js` | week helpers, `openClassModal`, `to12h`, `currentWeekIndex`, `generateWeekData` | Read-only consumption; no changes |
| `theme.css` | tokens + `+ .badge-past` (1 class) | Additive; no existing rule touched |
| Layout partials | header/week-nav/summary/empty-state/modal shells | None — includes only |

## 11. Verification hooks (→ tasks)

Frozen proposal §6 criteria map 1:1 to tasks; grid↔list parity check is mechanical: for every `rsd3g2Flags` week w and code c, `MockData.upcomingReplacements` must contain a row `{week: w, code: c}` (18 matches), and vice versa. Load state: week select value "10" → w0 = 9 → exactly rows (9, BMIT7075) + (9, BMIT2233), both pending.

**Criterion evaluation notes:**
- Criterion 4 ("Pending card → yellow badge") is evaluated on the **card**; the *modal* badge for wk2 BMIT7075 / wk4 BMIT5678 legitimately shows red "Conflict" (holiday-day remap, see §4 note) — same as the grid, not a bug.
- "View Full Request" must be **absent** on all list modals (AD-12); its presence on grid modals for the same slots is the recorded, accepted asymmetry.
- requestId: absent from all synthetic events.
