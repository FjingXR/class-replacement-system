# Explore Brief — sweep-fixes-round-3

**Date:** 2026-10-06 · **Branch:** `fjing` @ `472f72a` · **Input:** user update list (4 items)
**Scope rule:** explore only — nothing implemented yet.

## 1 — Rename "Upcoming Replacements" → "Replacement History"?

**Investigation.** The page (student-facing — its nav is the student pair [Student My Timetable, Upcoming Replacements]) shows the student's replacement requests of **all statuses** (Pending + Confirmed) **and**, after the F-1/"Overdue" + exclude-past work, **past rows too** (week filter, `showPast` toggle, "Past" summary card exists). Titles say "Upcoming" but the content is genuinely a history (+ pending + future). So **yes, "Replacement History" is more accurate** — and it pairs naturally with the staff nav's existing "Request History" (my-request-history).

Full reference inventory (rename blast radius): `routes/web.php:39`, blade filename + `@extends` config (activeNav `upcoming-replacements`, pageKey `upcomingReplacements`) + title/empty-state strings, `student-my-timetable` navItems (label + href), `MockData.upcomingReplacements` name + ~15 notification deep-link `page: 'upcoming-replacements-ui'` refs, `ui-common.js` comment, changelog `git mv upcoming-replacements-ui-changelog.md → replacement-history-changelog.md`. Historical plan doc keeps its old filename (dated artifact).

**Naming options.**
- **A (recommend):** "Replacement History" + route `/replacement-history-ui` — user's suggestion, most accurate, stays short in the student nav.
- **B:** "My Replacement History" — matches the "Student My Timetable" label pattern; longer; the student nav already loses label width on mobile drawer.
- **C:** title-only rename (keep route + files) — zero blast radius but mocks-with-backend intent favours renaming now, cheap in mock phase.

## 2 — Sortable columns on the renamed table

**Investigation.** Cols are `#, Subject, Original Slot, New Slot, Lecturer, Status` (head built in JS, `renderUpcoming` :169–210, default composite sort = week → di → start; desktop rows + mobile cards share one `visible` array ✓ one sort point). Data available per row: `code/name/type`, `week/di/start/end/originalTime/originalVenue`, `newDi/newStart/newEnd/newTime/newVenue`, `lecturer`, `status` (`pending` | `replacement`).

**Proposed sort fields** (mirroring request-approval's pattern): Subject (code string), Original Slot (week→di→start composite = the existing default), New Slot (pending rows "Awaiting PL approval" sort last), Lecturer, Status (`pending` first, then `replacement`; week
→di→start as tiebreak). `#` stays non-sortable (row number, cf. approval's `{sort:false, tip}`).

**Consistency decision needed.** request-approval AND my-request-history each carry a **local** duplicate of the sortable-head machinery (`sortState`, `toggleSort`, arrow markup). Building the 3rd table = the 3rd duplication → per the duplication rule, **promote the header markup + state to a small shared helper** in `ui-common.js` (`SortableHead.cols(labels, state, onToggle)` producing th html incl. aria-sort/arrow + a shared toggle helper), refactoring the 2 existing pages' renderHeader to call it — comparators stay page-local (they differ genuinely). Alternative: mirror-local (3rd copy, fastest, violates the rule).

## 3 — Logo click → role default page

**Investigation.** `navigateHome()` (ui-common.js:20) currently goes to `'/'` (the welcome view — pre-login). Logo lives in `partials/ui-nav-bar.blade.php:13` (all regular pages) + the arrangement page's own `top-logo` (:947) — both call the same shared fn ✓ one hook suffices. Role in the mock: staff = default (nav-bar default items, user-panel "Lecturer" LJZ 5770); the 2 student pages declare per-page `navItems` — a clean, existing role signal.

**Design (A, recommend):** layout `ui-template` accepts an optional `homeUrl` extends-param and emits `<script>window.PAGE_HOME='…'</script>`; regular pages pass it (staff → `/my-timetable-ui`, student pages → `/student-my-timetable-ui`), `navigateHome()` uses `window.PAGE_HOME || '/'`. Explicit per page (no nav-DOM guessing), covers the arrangement page automatically via the shared global. Welcome page stays the fallback (zero-config safety).

## 4 — Other pages' tables sortable

**Investigation result: requested-approval (6 sortable) and my-request-history (4 sortable) are ALREADY sortable** — both the house pattern. So item 4 = (a) the renamed student table (item 2), (b) verifying the two staff tables still sort correctly in Playwright, (c) reviewing whether any of their currently non-sortable columns are "suitable" to add — judgement: Lecturer/Course Code & Name on approval are squad-display cols (not sorted by design: the queue's sort fields are timestamp-driven + urgency+status); no additions recommended there unless the user wants them.

## Playwright plan (the round's verification core)

1. Rename: visit `/replacement-history-ui` direct nav ✓, nav link click from student timetable ✓, notification deep-links to the page ✓, `mock-data.js` refs resolve.
2. Logo: from a student page → `/student-my-timetable-ui`; from a staff page → `/my-timetable-ui`; arrangement page logo ✓ (same global).
3. Sorting: each sortable column ×2 on the student table (asc/desc arrows + order flips; `#` renumbers after sort); existing two tables smoke-test sort toggle + default state; mobile cards follow sort on the student table.
4. Regression: week filter/`showPast` still re-render sorted; exclude-past; 0 console errors on all touched pages; both themes spot-check.
