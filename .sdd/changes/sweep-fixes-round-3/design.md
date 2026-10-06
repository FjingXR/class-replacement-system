# Design — sweep-fixes-round-3

Gates frozen 2026-10-06: rename+route (A) · shared sort helper (A) · role-pair logo w/ `'/'` fallback (A).

## D-1 — Rename implementation order (single commit, no broken intermediate)

1. `git mv` blade → `replacement-history-UI-design-template.blade.php`; `git mv` changelog → `replacement-history-changelog.md`; `git mv` "todo list/upcoming-replacements-ui-plan.md" → `replacement-history-ui-plan.md` (dated doc keeps content).
2. `routes/web.php:39`: `Route::get('/replacement-history-ui', … view('ui-design-templates.replacement-history-UI-design-template', ['activeNav' => 'replacement-history']))`.
3. The page itself: `@extends` config (`activeNav`, `pageKey: 'replacementHistory'`), student `navItems` (`key`→`replacement-history`, `label`→`Replacement History`, `href`→`/replacement-history-ui`), `@section('title', 'Replacement History')`, `MockData.upcomingReplacements` → `MockData.replacementHistory`, empty-state/heading strings if any say "Upcoming".
4. `student-my-timetable` navItems (same key/label/href).
5. `ui-common.js`: `NOTIF_ROLE_BY_PAGE` map key `replacementHistory: 'student'` (:1609) + §10.0 comment ref (:1396 old page name → new).
6. `mock-data.js`: §2.12 base name + comment (:659–661, plan-doc ref) + every `link: '/upcoming-replacements-ui'` → `'/replacement-history-ui'` (~8 notification rows).

## D-2 — Shared sortable head — **REVISED AT APPLY: the helper already existed**

`ui-common.js:1156` already ships `makeSortableHeader(col, sortState, render)` + `compareBy(sortState, va, vb)` (DOM `<th>`, `span.sort-arrow`, addEventListener click). Audit result: **my-request-history already uses them** (its `renderTable` builds `tr` + checkbox th + `makeSortableHeader` per col); **request-approval was the outlier** (string-html `renderHeader` + local `toggleSort` duplicating the machinery). So instead of adding a new `SortableHead`:
- request-approval `renderHeader` refactored to `makeSortableHeader` (click callback = its old toggleSort side effects: `currentPage = 1; renderTable(); saveFilters();`); local `toggleSort` deleted.
- markup delta: text arrow `' ▲'` → styled `span.sort-arrow` — this UNIFIES approval's arrows with the history page's existing look (consistency ✓, intentional).
- the new student table uses the same helper (`sortState = { field: null }` = natural order; `applySort()` local comparator with composite keys + nulls-last for awaiting-PL rows + chronological tiebreak).

Original draft (kept for the record): a new string-based `SortableHead.th/flip` — superseded.

## D-3 — Logo home (layout + ui-common + 9 pages)

- `layouts/ui-template.blade.php`: accept `homeUrl` in the @extends param allowlist (top-of-layout @php defaults), emit after the script includes: `<script>window.PAGE_HOME = '{{ $homeUrl ?? '/' }}';</script>`.
- `ui-common.js:20`: `navigateHome(){ window.location.href = window.PAGE_HOME || '/'; }`.
- Staff pages (my-timetable, cohort, venue, replacement-home, arrangement, my-request-history, request-approval): add `'homeUrl' => '/my-timetable-ui'` to their @extends config.
- Student pages: `'homeUrl' => '/student-my-timetable-ui'`.
- Arrangement's own `top-logo` uses the same `navigateHome()` global — nothing to change there.

## D-4 — Playwright verify suite (T6 — the round's core)

Order: rename targets first (route + nav link + deep-link), then sorting (per-column toggle ×2 on the student table; header-markup regression on approval + history), then logo from staff/student/arrangement, then week/past filters under sort, both themes, 0 console errors each touched page. Screenshots under `/tmp/opencode/r3-shots/`.

## Risks / guardrails

- Rename is the widest blast radius (6 code files + 3 `git mv`s) — `grep -rn "upcoming-replacements\|upcomingReplacements"` must come back empty outside dated history (postscripts/archives).
- Header refactor must be visually identical — screenshot diff on approval + history vs pre-change (the sweep archive has pre-change shots).
- `MockData` base rename touches §2.12 whose name is also referenced by the student table + any F-10 requester contract comment.
