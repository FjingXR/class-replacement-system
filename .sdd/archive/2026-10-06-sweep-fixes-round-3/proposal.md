---
name: sweep-fixes-round-3
created: 2026-10-06
status: proposing
---
# Proposal — sweep-fixes-round-3

> **Input:** user update list (4 items) round-3. **Gate 2026-10-06:** rename + new route (A), shared sort helper + refactor all 3 tables (A), role-pair logo with `'/'` fallback (A).
> **Hard rules:** theme.css tokens only; shared partials/partials-first; duplication rule applied at the 3rd table; frontend mock only.

## R-1 — Rename "Upcoming Replacements" → "Replacement History" (+ route)

The page lists student replacement requests of **all statuses, past and future** (Pending + Confirmed + Past card + `showPast`), so the new name is accurate and pairs with the staff nav's "Request History" (`my-request-history`). Full rename: blade file, route `/replacement-history-ui`, nav key `replacement-history`, page title/empty-state strings, student nav items, `MockData.upcomingReplacements → MockData.replacementHistory`, notification deep-link rows `link: '/upcoming-replacements-ui'`, `pageKey upcomingReplacements → replacementHistory` (+ the `NOTIF_ROLE_BY_PAGE` entry), comment refs, and `git mv` of the changelog + the historical plan doc (which stays a dated artifact).

## R-2 — Sortable columns on the (renamed) Replacement History table

Local `columns` config + `sortState` per the house pattern (request-approval). Fields:
- **Subject** → `r.code + r.name`
- **Original Slot** → composite `[week, di, start]` (the current default order keeps as no-field fallback)
- **New Slot** → `[week, newDi, newStart]`; rows awaiting PL approval sort last
- **Lecturer** → string
- **Status** → order map `pending: 0, replacement: 1`, tiebreak by slot composite
- `#` → non-sortable (row number), cf. approval's `{ sortable:false, tip }`

Desktop rows and mobile cards render from the same `visible` array — one sort point covers both.

## R-3 — Logo click → role default page

Layout `ui-template` accepts optional `homeUrl` and emits `window.PAGE_HOME`; `navigateHome()` (ui-common.js:20) uses `window.PAGE_HOME || '/'`. Staff pages pass `/my-timetable-ui`; the two student pages pass `/student-my-timetable-ui`. Covers the arrangement page's own top-logo automatically (shared global). The welcome view stays the no-config fallback.

## R-4 — Sortable-table duplication: promote the shared head helper

`ui-common.js` gains `SortableHead` (`th(col, sortState)` → header cell html incl. arrow + `onclick="toggleSort('…')"`; `flip(sortState, field)` → state toggle). The 3rd table (student) uses it; **request-approval + my-request-history `renderHeader` loops are refactored to call it** (markup output byte-compatible: same `data-tip`, `class="sortable"`, ONCLICK, ▲/▼ text arrows). Comparators and page side effects (render, save filters, page reset) remain page-local — they differ genuinely.

## Acceptance criteria

1. `/replacement-history-ui` reachable; nav label/links/deep-links/`MockData` refs consistent; no `upcoming-replacements` refs left outside dated history docs.
2. Student table: 5 sortable columns flip asc/desc with arrows; `#` renumbers after re-sort; mobile cards follow the same order.
3. Existing two tables' headers output unchanged markup; sorting still works (regression).
4. Logo: student page → student timetable, staff page → staff timetable, arrangement → staff timetable.
5. 0 console errors on touched pages; changelog postscripts + sweep-report notes; Playwright verify all above.

## Scope addendum (user-directed, same round — 2026-10-06)

The original 4-item scope above grew during the round with three user requests and one user-reported bug; all verified in-session (tasks T11–T13 + T8+):

1. **Sortable columns extended to the remaining display-only columns where reasonable** — my-request-history: Requested Replacement (date+time), Requested Venue, Students, Status (process-order map); request-approval: Lecturer (by **resolved name**, the stored value is an id), Course Code & Name, Students; replacement-home (table missed by the original inventory — JS-built): Days Left (computed urgency) + Venue. Deliberately NOT sortable: Cohort(s) (multi-value, tip explains), Actions columns, Conflict Reason (has a dedicated filter).
2. **All-Weeks de-duplication** (user bug report): the page's legacy hand-insert IIFE + the shared `includeAll` flag both ran → two identical options; single writer = the helper flag, IIFE removed (which also fixes silent loss of the option on breakpoint re-populate).
3. **Arrangement page-local `navigateHome()`** (shadowed the shared one, hardcoded `/`) updated to `window.PAGE_HOME || '/'` — without it the logo rule silently failed on that page.
