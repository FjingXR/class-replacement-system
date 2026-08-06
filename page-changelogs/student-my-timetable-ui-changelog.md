# Changelog — Student My Timetable

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

## [2026-08-04] Refactored: replaced inline page-header/week-nav/empty-state/grid-table/modal with `@include('partials.…')` (OOP Phase 1)

Page uses `ui-page-header`, `ui-week-nav`, `ui-grid-table`, `ui-empty-state`, `ui-class-detail-modal` partials.
