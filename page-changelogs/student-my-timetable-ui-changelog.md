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
