---
name: sweep-fixes-round-1
created: 2026-10-06
status: proposing
---
# Proposal — sweep-fixes-round-1

> **Change:** Fix the 🟡 batch from the pre-submission bug sweep (F-1, F-4, F-10, F-11) + reclassify F-7; frontend-only, mock-phase.
> **Source:** `playwright snapshot/pre-submission-bug-sweep.md` (user-approved 2026-10-06, "Fix batch")
> **Out of scope:** F-2 (role-scoped badge — decision pending), F-12 (FOUC hex — decision pending), F-5/F-6 (deferred polish)

## F-1 — replacement-home: negative days-left (opathic clock + "Urgent" mislabel)
**Diagnosis (verified in code):**
1. `daysLeft()` in `replacement-home-UI-design-template.blade.php:274` uses `new Date()` (**real clock**) while every other page runs on the shared `DateHelper.getTodayMs()` demo anchor (Oct 5 2026). Presentation-clock inconsistency bug in its own right.
2. `urgencyClass()` (:281) has no negative branch → any `days ≤ 7` including −31 renders urgency-high.
3. Three render sites print the raw negative: table "DAYS LEFT" cell, mobile card `.rc-footer` (`ui-common.js:2169`), quick-view modal Status row (:467) — and modal Status Description (:468) calls −31 "Urgent — arrange a replacement soon".

**Fix:** anchor the clock; introduce ONE shared "days left → label" contract and use it at all 3 sites:
- `days < 0` → **"Overdue"** (+ description "Class date has passed…" per user's hide/keep decision)
- `days === 0` → "Today" ; `days === 1` → "1 day left" (grammar); else "N days left"
- promoted to `ui-common.js` (3rd-duplication rule: table cell + card + modal all need it)

**OPEN SUB-DECISION — RESOLVED 2026-10-06 (design.md D-1 + tasks T3):** what happens to a past class's *Arrange Replacement* action — **user chose: label "Overdue", KEEP the row and the button enabled** (the arrangement page's lead-time banner explains why no weeks are selectable).

## F-4 — venue-timetable: raw lowercase status enums in badges
**Diagnosis:** `e.status` ('normal' / 'replacement', lowercase internal enum) is string-interpolated raw at 3 sites: history card (:660), mobile/desktop event card (:846), + modal (:914). Other pages either store Title-case values or map manually (upcoming page :215/:235 inline ternary). Violates same-name-same-meaning.
**Fix:** promote ONE shared `StatusText.label(status)` helper to ui-common (normal→Normal, replacement→Replacement, pending→Pending, approved→Approved, rejected→Rejected — Title case, keyed by enum), use it at the 3 venue sites; leave Title-case-native datasets untouched (already equal to their labels). CSS badge classes stay keyed by enum (`badge-normal` etc.) — no visual/color change.

## F-7 — RECLASSIFIED: week-select label "inconsistency" is a test artifact
Fresh 1440 load renders the **full** label on arrangement (verified live). The compact appearance came from options baked during a 768 resize (labels are populated once, not on viewport change). The real, much smaller issue: **week-select option labels don't re-format when the viewport crosses the 768 breakpoint at runtime.**
**Fix (small, shared):** `WeekNavigator` remembers its `populateWeekSelect` cfg and re-populates on a (debounced) breakpoint-crossing resize, preserving the saved week. Mirrors the venue `mobileCardList` resize precedent.

## F-10 — my-request-history: no owner scoping in mock data
**Diagnosis:** `MockData.requests` rows have no requester field; the page renders all 20 as "mine" while the approval page attributes the same courses to 5 lecturers.
**Fix:** mock-data only — add `requester: 'En. Lim Jia Zheng'` to rows attributed to the persona in the approval dataset, and the other lecturers' names to the rest (kept consistent with `approvalRequests`' lecturer column); add a one-line page filter `requester === MockData.currentUser.name` (comment: backend = `WHERE requester_id = auth:id`, drop the filter). Counts ("17 of 20") then honestly reflect *the persona's* rows.
**Note:** `MockData.requests` is NOT part of the frozen approvalRequests section — safe to edit.

## F-11 — generic `currentWeek` localStorage key
**Diagnosis:** `WeekNavigator`'s default storage key is `'currentWeek'` (ui-common.js:307) while arrangement/venue pass namespaced keys. my-timetable + student-my-timetable share the generic one.
**Fix:** default becomes `'myTimetableWeek'` (both timetable pages inherit); one-time migration: on load, if new key absent and old `currentWeek` present, migrate value then remove old key.

## Acceptance criteria
1. All pages render identical day-counts from the shared anchor (arrangement/home/venue agree on "today").
2. No raw negative day numbers or "Urgent" copy for past classes anywhere (table / card / modal).
3. No lowercase status enums visible in any UI string.
4. History table shows only the persona's rows; counts reflect them.
5. Resizing across 768 re-labels week selects without page reload.
6. Week persistence keeps working (migration path); zero console errors after all fixes; each affected page's changelog updated.
