# Changelog — Student My Timetable

## [2026-08-24] Nav swap: Request History → Upcoming Replacements (TASK-006)

Student role drops "Request History" (Ch1 §1.1.4 view-only — status is already
visible via timetable colors per FR 1.3) and gains "Upcoming Replacements"
(FR 1.4). The `/my-request-history-ui` route stays for lecturers/PLs.

### Files Changed

#### `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Line 6 (`navItems`) | Replaced | `['key'=>'replacement-history', …]` → `['key'=>'upcoming-replacements','label'=>'Upcoming Replacements','href'=>'/upcoming-replacements-ui']` |

New page stub + route + `MockData.upcomingReplacements` dataset recorded in
`upcoming-replacements-ui-changelog.md`; UI build spec in
`todo list/upcoming-replacements-ui-plan.md`.

---

## [2026-08-16] Today button now persists week selection

The "Today" button now saves the current week to `localStorage` (via `WeekNavigator.jumpToToday()` calling `this.save()`), consistent with arrow/dropdown navigation. Previously, clicking Today would jump the view but not persist — a page refresh would revert to the old week.

### Files Changed

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `WeekNavigator.jumpToToday()` | Updated | Now calls `this.save()` after updating the UI, matching the behavior of `prevWeek()`/`nextWeek()`/`selectWeek()`. |

---

## [2026-08-15] Event hover tooltip shows lecturer instead of venue

The event-block tooltip previously showed `name · venue` (venue already on the block). It now shows the **lecturer** (`name · Prof. Dr. Khoo Teik Huat`) — the key info a student doesn't get from the block. Uses the new `tooltipExtra(event)` option on the shared `buildTimetableGrid`.

### Files Changed

#### `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `buildTimetable()` | Updated | Passes `tooltipExtra` returning `event.lecturer`. |

#### `public/js/ui-common.js` + `public/css/theme.css`

Shared: `buildTimetableGrid` now sets `dataset.tip2` from `cfg.tooltipExtra`; `.event-block::after` reads `attr(data-tip2)`.

---

## [2026-08-15] Class detail modal redesign: unified detail sheet

The Class Detail modal now uses the shared `DetailModal` "Detail Sheet" via the shared `openClassModal()` (single flat group, definition rows, identity header). No page-specific changes needed.

---

## SDD Change: `student-my-timetable-ui`
**Date:** 2026-08-02
**Scope:** View-only weekly timetable for students + rule #6 CSS promotion + 14 UI enhancements

---

## Files Created

### `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php` (588 lines)
- New student page template — view-only weekly grid for RSD3(S1)G2 cohort
- Reads from `MockData.cohortTimetable.rsd3g2Base` + `rsd3g2Flags`
- Cancelled-filter pipeline using `MockData.studentTimetable.cancelledFlags`
- Holiday conflict derivation from `MockData.holidays`
- 14 enhancements: Today button, week subtitle, date-range select, heatmap bar, empty state, keyboard nav, modal status timeline, dynamic notif count, event `.meta` pocket, semester progress bar, week-change transition, event hover tooltip, copy course code, smooth scroll to grid

### `resources/views/partials/ui-legend-bar.blade.php`
- Shared legend bar partial (4 items: Normal Class, Replacement, Pending, Conflict)
- Uses `var(--color-secondary/primary/tertiary/error)` tokens
- Consumed by MyTimetable, CohortTimetable, and Student page

---

## Files Modified

### `public/js/mock-data.js`
- Added `MockData.studentTimetable` section (`activeCohort`, `cancelledFlags`, `notificationCount`)
- Week 4 cancels ALL 7 events → triggers empty state
- Updated holidays comment: "CONSUMED BY CohortTimetable + Student My Timetable"

### `resources/views/partials/ui-nav-bar.blade.php`
- Added `$navItems` default block (5 items with key/label/href)
- Replaced hardcoded `<a>` tags with `@foreach ($items as $it)`
- Added `$notifCount` parameter (default `3`) + `id="notifBadge"`

### `resources/views/layouts/ui-template.blade.php`
- Forward `$notifCount ?? 3` to `@include('partials.ui-nav-bar', ...)`

### `public/css/theme.css`
- Appended ~460 lines of promoted CSS (blocks 4.2–4.19)
- Promoted from MyTimetable + CohortTimetable inline: legend, summary-card, modal-shell, semester-bar, time-column, hour-header, hour-cell+event-block
- New enhancement CSS: today-btn, week-subtitle, heatmap-bar, keyboard-focus, status-timeline, semester-progress, week-change-transition, event-tooltip, copy-toast
- Standardized `.week-select` min-width to 160px, `.modal-footer` to `flex-end`

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`
- Replaced inline legend → `@include('partials.ui-legend-bar')`
- Removed inline CSS blocks 4.2–4.8 (now in theme.css)
- Deleted dead `.today-highlight` CSS
- Kept page-specific: `.btn-replace-now`, `.btn-cancel-class`, `.cancel-overlay`, `.modal-footer-left/right`, `.modal-footer{space-between}` override

### `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`
- Replaced inline legend → `@include('partials.ui-legend-bar')`
- Removed inline CSS blocks 4.2–4.8 (now in theme.css)
- Deleted dead `.today-highlight` CSS
- Kept page-specific: `.badge-*` variants
- Did NOT touch inline holiday rule `d===3&&w===3`

### `routes/web.php`
- Added `GET /student-my-timetable-ui` route

---

## Key Decisions
- **Cancelled classes hidden entirely** — design decision (no FR mandates it); keeps student view uncluttered
- **Week 4 cancels ALL 7 events** — ensures empty-state message (item 17) is testable
- **`MockData` is read-only** — events reconstructed via spread copy, `meta: {}` pocket for future backend fields
- **Keyboard nav is mock-phase only** — full WCAG compliance deferred to Sprint 3
- **`$navItems` parametrization** — backward compatible; existing pages pass only `$activeNav`

## Lint/Type Check
- PHPStan: 20 pre-existing errors in Models/Providers/Seeders (none in files we modified)
- Pint: not run (timed out; no PHP files modified in our changes — all Blade/JS/CSS)

---

## [2026-08-10] Phase 2 Template Migration: Inline helpers → Shared OOP classes

### Summary

Migrated inline `fmtShort()` function to `DateHelper.fmtShort()` from `ui-common.js`. Removed 3-line inline function definition.

### Files Changed

#### `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-10 | Lines 61-63 | Removed | Deleted inline `fmtShort(d)` function definition |
| 2026-08-10 | Line 98 | Replaced | `fmtShort(startDate)` → `DateHelper.fmtShort(startDate)` |

#### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-10 | DateHelper class | Added | `DateHelper.fmtShort(d)` static method — returns "DD Mon" format |
| 2026-08-10 | DateHelper class | Added | `DateHelper.weekRangeLabel(weekNum)` static method — returns responsive week range label |

---

Page uses `ui-page-header`, `ui-week-nav`, `ui-grid-table`, `ui-empty-state`, `ui-class-detail-modal` partials.
| 2026-08-13 | `public/css/theme.css` (shared) | macOS table fix | `.timetable`: `border-collapse:collapse` → `separate` + `border-spacing:4px`; removed 1px cell borders; hover uses `var(--color-surface-variant)`; removed zebra striping. `.badge` border-radius 6px→8px. |
| 2026-08-14 | Page styles | Offday-slot today-cell fix | Added `.today-cell.offday-slot { background: transparent; }` override so PH/Sunday empty cells are not red when today. |
| 2026-08-14 | `prevWeek/nextWeek/selectWeek` | WeekNavigator delegation | Local nav logic replaced with `weekNav.prevWeek()/nextWeek()/selectWeek()`; `buildTimetable()` syncs `currentWeek = weekNav.currentWeek`. Removed manual select-index/subtitle/progress/arrow/save updates. |

## Post-2026-10-02 cross-reference

The badge feed added with this page's build (`$notifCount` with server default `3` on the nav bar, `MockData.studentTimetable.notificationCount`, and the page's `notifBadge.textContent` echo) was removed by the `notifications-panel` change — the bell badge is now computed by `refreshNotifBadge()` in `ui-common.js`; see `notifications-panel-changelog.md`.

## [2026-10-03] Disabled print icon on the week-nav toolbar

Shared `ui-week-nav` gained an opt-in `'showPrint' => true` arg rendering a printer icon-button
(inline SVG, `.print-btn` in theme.css), right-aligned at the toolbar edge via `margin-left: auto`.
Enabled stub: click fires the shared `toast.show('Printing is coming soon')` bottom-left toast bar;
`title="Coming soon"` native tooltip on hover. No JS beyond the one-liner onclick.

### Postscript — sweep-fixes-round-1 (2026-10-06, F-11)

Week persistence key namespaced to `studentTimetableWeek` (was the shared generic
`currentWeek` — pages silently overwrote each other's saved week). Legacy-value migration
handled once in `WeekNavigator.load()`; shared sorting/labels untouched.

### Postscript — sweep-fixes-round-3 (2026-10-06, rename + logo)

Student nav item renamed to "Replacement History" → `/replacement-history-ui` (page
rename, see that changelog). Logo click now lands on Student My Timetable (homeUrl
param) instead of the welcome view.

### Postscript — holiday-badge-generic-label (2026-10-07, shared ui-common)

Day-header holiday badges now read generically as **PUBLIC HOLIDAY** (markup
text `Public Holiday`; the badge CSS already uppercases it), with the specific
holiday name — e.g. `Deepavali Holiday (In Lieu)`, `Christmas Eve` — shown on
hover via the shared `data-tip` tooltip system. Label-less holiday flags show
no tooltip. One-line change in `HtmlBuilder.dayHeader` (ui-common.js:2155);
applies to every timetable page via the shared builder.


---

## [2026-10-07] Class modal grouped into tabs (shared DetailModal taxonomy)

Same as my-timetable: `openClassModal` auto-groups rows into Class Information /
Schedule / Status tabs via `renderModalGroups` (ui-common.js).

---

## [2026-10-07] Confirmed replacement modal shows the replaced original class

Shared `openClassModal` enhancement — replacement blocks show an
**Original Class** tab when the event carries `replacedFor` or a date-shaped
remarks string (rsd3-g2 flags now date the prior week's same weekday).

---

## [2026-10-07] Conflict blocks now render red (was: unstyled grey)

A conflict-status block (e.g. BMIT2073, "Lecturer on leave" week) rendered with
no status class because the shared default classifier lacked a conflict
branch — the block in your paste (`event-block span-4` with no `event-*`
class) is exactly that. Fixed in `buildTimetableGrid` (ui-common.js): conflict
events now get `.event-conflict` (red, §10.0 legend A).

---

## [2026-10-08] Copy + dead-code cleanup (audit follow-ups)

- Conflicts card description said "Scheduling **overlaps**" — now
  "Scheduling clashes and public holidays" (matches what the card counts).
- Dropped the fabricated `'01 Sep 2026, 09:15 AM'` fallback for pending
  `requestedAt` — every pending flag in `rsd3g2Flags` already carries its real
  timestamp; the fallback was dead and invented a date if one ever didn't.
- Removed unused `WEEK_KEY` / `loadSavedWeek()` / `saveWeek()` (week
  persistence runs through `WeekNavigator` directly).

### Postscript — snooze rule extended (2026-10-08, U3 decision)

The undo toast now shows for **5 s** (was 12 s) and **auto-dismissing after the
full display also snoozes** it — surviving the whole toast counts as "seen",
same as clicking ✕. Only navigating away mid-display (timer killed) leaves the
entry unsnoozed, so the toast legitimately re-shows on the next load.
`ToastManager.show` gained an `onAutoDismiss` hook alongside `onManualDismiss`;
`UNDO_TOAST_MS` 12000 → 5000. Live-verified all three paths, 0 console errors.

### Postscript — undo feedback + grid rebuild (2026-10-08, U1/U2)

Clicking the undo toast's **Undo** now works as it looks: `ToastManager`
dismisses the undo bar **before** running the callback (U1 — the callback's
"Class restored." toast was previously wiped ~0 ms after appearing), and the
callback rebuilds whichever grid is on screen after `ClassCancellation.undo()`
(U2 — every timetable page's `buildTimetable()` / replacement-home's
`buildTable()`; the class reappears without a manual reload). Confirmation
toast shows last, over the rebuilt grid. Live-verified on my-timetable,
student page, and replacement-home; 0 console errors.

### Postscript — conflict blocks made unmistakable (2026-10-08)

`.event-conflict` (global, theme.css) upgraded from a plain red tint to
a **2px solid `--color-error` border + diagonal caution stripes**
(`repeating-linear-gradient` over the container tint, token-only via
`color-mix` — adapts to dark theme). Same meaning, same red per §10.0;
just impossible to mistake for an ordinary block at grid glance.
Applies to every page rendering conflicted classes (cohort, venue,
my timetable, student). No legend or test changes needed; verified live
on cohort (dft2s1 W4 AMCS2093) and venue (B110 W4), suite 124 passed.

### Postscript — conflict & PH unified to one loud red (2026-10-09)

On personal timetables everything shown is the viewer's own, so the old
split (conflicted = loud striped `event-conflict`, public-holiday = quiet
plain tint) was meaningless noise. Default builder (`ui-common.js`) now
sends **both** conflict-status and PH-day classes to the loud
`event-conflict` treatment — one red, one meaning: "this class will not
run as scheduled". The shared legend's default conflict swatch reuses the
real `event-conflict` class so the legend shows the exact styling
(Cohort/Venue pages keep their own owner-gated legend split: loud = yours,
quiet tint = others'). Verified: my timetable W8 Monday (Deepavali
in-lieu) + W14 (Christmas) PH classes and student page W1/W5 conflicts +
W8 PH all render loud; suite green.
