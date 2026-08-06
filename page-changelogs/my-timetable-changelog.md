# Changelog — Lecturer My Timetable

## Files Changed

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-21 08:09 | Lines 233–242 | Semester bar redesign | Changed from `height: 44px`, `background: secondary-container`, no border/shadow to `padding: 12px 16px`, `background: surface`, `border: 1px solid outline`, `border-radius: radius-md`, `box-shadow: shadow-sm` — matches request history toolbar style |
| 2026-07-21 08:09 | Lines 249–260 | Week arrow color | `color: on-secondary-container` → `on-surface-variant`; hover `rgba(12,51,33,0.1)` → `surface-variant` |
| 2026-07-21 08:09 | Lines 264–280 | Week select restyle | `border: rgba(12,51,33,0.15)` → `border: outline`; `background: rgba(12,51,33,0.06)` → `background: surface-variant`; `color: on-secondary-container` → `on-surface-variant` |
| 2026-07-21 08:09 | Lines 557–604 | Summary bar redesign | Changed from `display: flex` horizontal layout to `display: grid` with `grid-template-columns: repeat(4, 1fr)` and `gap: 4px`. Cards now vertical (`.summary-value` above `.summary-label`), `background: surface`, `border: 1px solid outline`, `box-shadow: shadow-sm`. Total card: `border: 2px solid primary` + `background: primary-container`. Removed per-card background colors. |
| 2026-07-21 08:09 | Lines 948–964 | Summary bar HTML | Renamed `.num` → `.summary-value`, `.label` → `.summary-label`. Labels now single-line with CSS `text-transform: uppercase` instead of using `<br>` for line breaks. |
| 2026-07-21 08:09 | Lines 296–302 | Gap fix | Removed `flex: 1` from `.grid-wrapper` and `padding-bottom: 0` from `.grid-scroll` — legend bar now sits directly below the timetable without extra gap. |
| 2026-07-21 08:09 | Line 294 | Dark mode fix | Added `html.dark .week-select { color-scheme: dark }` — ensures native dropdown renders correctly in dark mode (matches request history fix). |
| 2026-07-21 08:09 | Lines 835–836, 850–853 | Responsive | Updated responsive rules for grid layout: 1024px → `grid-template-columns: repeat(2, 1fr)`; 768px → `.summary-value { font-size: 20px }`, `.summary-label { font-size: 11px }`. |
| 2026-07-21 08:09 | Line 588 | Total card color | Changed `.summary-card.card-total .summary-value` color from `var(--color-on-primary-container)` to `var(--color-secondary)` — matches Normal Class legend swatch. |
| 2026-07-21 08:15 | Lines 231–246, 900–903 | Page header | Added `page-header` / `page-title` / `page-desc` CSS and HTML — matches my-request-history page header style. Title: "My Timetable", description: "View your weekly class schedule and manage replacement requests across all cohorts." |
| 2026-07-21 08:15 | Lines 264–280 | Week select restyle | Changed from `border: outline`, `background: surface-variant`, `color: on-surface-variant` to `border: none`, `background: secondary-container`, `color: on-secondary-container` — matches replacement-arrangement week selector style. |
| 2026-07-21 08:15 | Line 82 | Time column center | Added `text-align: center` to `.time-col`. |
| 2026-08-01 14:26 | Lines 299–312 | New summary card | Added `Teaching Hours` summary card (2nd position, after Total Classes) — shows total teaching hours for the week, computed from event spans (`(end - start + 1) * 0.5` per slot), displayed as decimal when needed (e.g. 21.5). Card style: `1px dashed outline-strong` border + `surface-variant` background, value color `on-surface`. Matches CohortTimetable's `card-hours` pattern. |
| 2026-08-01 14:26 | Lines 588–590 | Summary bar 5 cards | Added `card-hours` entry to `@include('partials.ui-summary-bar')` — summary bar now has 5 cards (Total Classes, Teaching Hours, Confirmed Replacement, Pending Approval, Conflicts/Public Holiday). Base grid in `theme.css` already uses `repeat(5, 1fr)`. |
| 2026-08-01 14:26 | Lines 936–952 | Summary logic | `updateSummary()` now computes `hours` and updates `sumHours` element — same formula as CohortTimetable. |
| 2026-08-01 14:56 | Lines 69–75, 543–555 | Semester chip | Removed `session-text` span (16px, centered in semester bar) and replaced with `semester-chip` pill in page header — same style (`secondary-container` bg, 20px radius, 12px/600) and position (after page title, before description) as cohort-timetable-ui. Text format changed to `202605 Semester · 15-Jun-2026 ~ 20-Sep-2026`. Semester bar now contains only week arrows + select (left-aligned). |
| 2026-08-01 15:01 | Line 7 | CSS dedup | Removed page-local `.semester-chip` CSS — now shared via `theme.css` (added there alongside `.page-header` section). |
| 2026-08-02 | `@section('page-scripts')` | Centralised mock data (Task 9) | Migrated all inline mock data (lecturers, schedules, cohorts) to `public/js/mock-data.js` — page now reads from `window.MockData.myTimetable`. Week baseline changed from 2026-06-15 to 2026-08-31. Semester chip text now reads `MockData.semester.chipText` instead of hardcoded string. |
| 2026-08-03 08:10 | Lines 138–142 | Semester Progress | Added progress bar (`semester-progress` div with `progress-label` + `progress-track` + `progress-fill`) — shows "Week N of 14" with visual fill bar. CSS already in `theme.css`. |
| 2026-08-03 08:10 | Lines 151–158 | Today button | Added `today-btn` button in semester-bar with clock icon — jumps to current week and scrolls to grid. CSS already in `theme.css`. |
| 2026-08-03 08:10 | Lines 161–162 | Week subtitle | Added `week-subtitle` div below semester-bar — shows "Week N of 14 · DD Mon YYYY ~ DD Mon YYYY". CSS already in `theme.css`. |
| 2026-08-03 08:10 | Lines 233–234 | Copy toast | Added `copy-toast` div for clipboard feedback. CSS already in `theme.css`. |
| 2026-08-03 08:10 | Lines 241–273 | Holiday data | `weekData` now reads `MockData.holidays` to populate `holiday` and `holidayLabel` fields — enables off-day highlighting for non-Sunday public holidays. |
| 2026-08-03 08:10 | Lines 305–318 | Progress/subtitle functions | Added `updateProgress()` (updates progress bar fill and label) and `updateWeekSubtitle()` (updates week subtitle text). Both called on week change. |
| 2026-08-03 08:10 | Lines 428–443 | Today + offday highlighting | `buildTimetable()` now adds `.today` class to current day column and `.offday` class to non-Sunday public holidays. Sunday shows "OFF" label without offday class (no dashed borders/opacity). |
| 2026-08-03 08:10 | Lines 460–463 | Offday slot class | Hour cells on Sunday or public holidays now use `.offday-slot` class (unified) instead of separate `.sunday-slot` / `.holiday-slot`. |
| 2026-08-03 08:10 | Lines 474–478 | Event block a11y | Added `tabindex="0"`, `__eventData`, `dataset.name`, `dataset.venue` to event blocks — enables keyboard navigation (Enter to open modal). |
| 2026-08-03 08:10 | Lines 595–603 | Today button handler | Added click listener for `todayBtn` — resets to current week, updates UI, scrolls to grid. |
| 2026-08-03 08:10 | Lines 605–613 | Keyboard navigation | Added `keydown` listener — ArrowLeft/ArrowRight for week navigation, Enter to open focused event modal. Skips when SELECT focused or modal open. |
| 2026-08-03 08:10 | Lines 615–624 | Copy to clipboard | Added `click` listener on `.ev-code` — copies subject code to clipboard and shows toast notification. |
| 2026-08-03 08:20 | Lines 175–185 | Empty state | Added `empty-state` div inside `grid-wrapper` with calendar-X icon and "No classes this week / All classes for this week have been cancelled." message. CSS already in `theme.css`. |
| 2026-08-03 08:20 | Lines 425–435 | Empty state logic | `buildTimetable()` now checks if events array is empty — hides table and shows `emptyState` div, otherwise shows table and hides empty state. |
| 2026-08-03 08:20 | Lines 367–373 | Status timeline | Added `status-timeline` HTML in `openModal()` for pending events — shows 3-step progress: "Submitted ✓ → Under Review → Awaiting Replacement". CSS already in `theme.css`. |
| 2026-08-03 | `@section('page-styles')` | Today column highlight (hour cells) | Added `.today-cell` CSS with `rgba(141,181,230,0.12)` background + hover `0.22` — highlights ALL hour cells in today's row (not just time-col). Selector uses `.timetable td.today-cell` to beat theme.css `:nth-child(2n)` specificity. Mobile override: `transparent` (today card header already stands out via solid primary bg). |
| 2026-08-03 | `@section('page-styles')` | Today badge CSS | Added `.today-badge` pill (inline in day-label, primary bg, 10px bold, uppercase) — visually marks today's row. |
| 2026-08-03 | `@section('page-styles')` | Mobile today card | Added `@media (max-width:768px)` override: `.timetable td.time-col.today` gets solid `var(--color-primary)` bg with `on-primary` text — distinguishable from other cards which use `primary-container`. |
| 2026-08-03 | `@section('page-scripts')` | today-cell class in buildTimetable() | Hour cells in today's row now get `today-cell` class (line 502). |
| 2026-08-03 | `@section('page-scripts')` | Today badge in buildTimetable() | Day label now includes `<span class="today-badge">Today</span>` when `day.today` is true. |
| 2026-08-03 | `@section('page-scripts')` | todayMs uses real-time | Changed `new Date('2026-09-18')` to `new Date()` — today column now highlights the actual current day instead of hardcoded date. |
| 2026-08-03 | `@section('page-scripts')` | currentWeekIndex uses real-time | Changed `new Date('2026-09-18')` to `new Date()` — default week selection uses actual current date. |
| 2026-08-04 | — | Refactored: replaced inline page-header/week-nav/empty-state/grid-table/modal with `@include('partials.…')` (OOP Phase 1) | Page uses `ui-page-header`, `ui-week-nav`, `ui-grid-table`, `ui-empty-state`, `ui-class-detail-modal` partials. |
