# Changelog — Lecturer My Timetable

## [2026-08-16] Replaced hardcoded inline styles with shared utility classes

Refactored hardcoded `font-size`/`font-weight`/`color` inline styles to use shared CSS classes from `theme.css`. Specifically, the cancel-modal description text now uses `.section-heading-sub`.

### Files Changed

#### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Line | Before | After |
|---|---|---|
| 227 | `style="font-size:13px;opacity:0.7;margin-top:6px"` | `class="section-heading-sub" style="margin-top:6px"` |

---

## [2026-08-16] Venue dropdown redesigned as cascading 4-level columns

The venue dropdown is now a **cascading column picker** with a fixed 4-level hierarchy — **Type → Block → Floor → Room** — where each level occupies its own vertical column and a child column appears immediately to the right of its parent. Hovering (or clicking) a parent reveals the next column; only the **Room** is selectable.

```
 TYPE          BLOCK       FLOOR           ROOM
 Tutorial   ›  Block B  ›  Ground Floor  ›  B002 — 35 seats ...
 Lecture ...                            ›  Floor 1  ›  B100 — 35 seats ...
 Lab        ›  ...        ...
 CiscoLab   ›  ...
```

- Chevrons (`›`) appear only on items with a level beneath them; final Room rows show capacity + a checkmark/highlight when selected.
- **Favourites** and **Recent** are utility groups that are **not** hierarchy levels — they render at the top of the first column as **expandable 2-level groups** (`★ Favourites › [rooms]`, `Recent › [rooms]`), hidden when empty.
- Floor rule: 1st digit after the letter — `B0__` = Ground Floor, `B1__` = Floor 1, etc.
- The open menu grows horizontally to fit columns; no internal horizontal scrollbar.

### Files Changed

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `VenueDropdown._buildGroups()` | Rewritten | Creates the 4 `.venue-col` containers and resets active state (`activeType/activeBlock/activeFloor/activeUnit`). |
| `VenueDropdown._buildTypeColumn()` | Rewritten | Renders `★ Favourites` and `Recent` as expandable 2-level parent rows (hidden when empty), then `Types` list. |
| `VenueDropdown._buildBlockColumn()` | New | Hovering a Type builds the Block column (`Block A/B/C…`) for that type. |
| `VenueDropdown._buildFloorColumn()` | New | Hovering a Block builds the Floor column (Ground Floor / Floor N). |
| `VenueDropdown._buildRoomColumn()` | New | Hovering a Floor builds the Room column (final selectable rooms with capacity). |
| `VenueDropdown._buildUnitColumn()` | New | Hovering Favourites/Recent builds its room list in the second column (2-level only). |
| `VenueDropdown._createParentItem()` | New | Creates a `.venue-col-item-parent` row with label + chevron, tagged with `data-type`/`data-block`/`data-floor`/`data-unit`. |
| `VenueDropdown._createRoomItem()` | New | Creates a `.venue-col-item-room` row (star/clock icon for fav/recent, code, capacity, ✓ + `.selected` when active). |
| `VenueDropdown._updateActiveParents()` | Updated | Toggles `.active` on the current type/block/floor/unit parent row. |
| `VenueDropdown._open()` | Updated | Resets to just the Type column on every open. |
| `VenueDropdown._updateActive()` | Updated | Syncs `.selected` + ✓ checkmark across all room rows on select. |

#### `public/css/theme.css`

| Location | Change | Detail |
|---|---|---|
| `.venue-dd-group*`, `.venue-dd-item*` | Removed | Old single-level flyout markup replaced by the cascading column model. |
| `.venue-col` | Added | Vertical column; hidden until `.visible`. |
| `.venue-col-header` | Added | Uppercase section label (Favourites / Recent / Types / Blocks / Floors / Rooms). |
| `.venue-col-item`, `.venue-col-item-parent` | Added | Row + chevron styling (`.active` highlights the current parent). |
| `.venue-col-item-room`, `.selected`, `.venue-col-item-check` | Added | Final room rows with capacity meta, selected highlight + ✓. |

---

## [2026-08-16] Today button now persists week selection

The "Today" button now saves the current week to `localStorage` (via `WeekNavigator.jumpToToday()` calling `this.save()`), consistent with arrow/dropdown navigation. Previously, clicking Today would jump the view but not persist — a page refresh would revert to the old week.

### Files Changed

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `WeekNavigator.jumpToToday()` | Updated | Now calls `this.save()` after updating the UI, matching the behavior of `prevWeek()`/`nextWeek()`/`selectWeek()`. |

---

## [2026-08-15] Replace Now passes original class duration to replacement-arrangement

The **Replace Now** button now includes `duration` (in hours, derived from the event's `start`/`end` slot indices via `(end - start + 1) / 2`) in the URL when navigating to `/replacement-arrangement`. The arrangement page then caps the selection at that many 30-min slots (duration × 2) so the replacement matches the original class length.

### Files Changed

#### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `btnReplaceNow` | Updated | `goToReplacement(..., { day, start, end, venue, duration })` — duration = `(end - start + 1) / 2` hours. |

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `goToReplacement()` | Updated | Appends `duration` to the query string when `opts.duration` is a valid number. |

---

## [2026-08-15] Event hover tooltip shows cohort(s) instead of venue

The event-block tooltip previously showed `name · venue`, but the venue is already displayed on the block. It now shows the **cohort(s)** (`name · DFT2 (S1)`) — useful for a lecturer teaching across cohorts (the lecturer in the tooltip would just be the viewer). Implemented via a new `tooltipExtra(event)` option on the shared `buildTimetableGrid`; the CSS tooltip reads `attr(data-tip2)`.

### Files Changed

#### `public/js/ui-common.js`

| Location | Change | Detail |
|---|---|---|
| `buildTimetableGrid()` | Updated | Sets `div.dataset.tip2` from optional `cfg.tooltipExtra(event)` (defaults to venue, so other callers are unaffected). |

#### `public/css/theme.css`

| Location | Change | Detail |
|---|---|---|
| `.event-block::after` | Updated | Tooltip content `attr(data-name) ' · ' attr(data-venue)` → `attr(data-name) ' · ' attr(data-tip2)`. |

#### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `buildTimetable()` | Updated | Passes `tooltipExtra` returning the event's `cohorts.join(' + ')` (or `cohort`/venue fallback). |

---

## [2026-08-15] Class detail modal: remove Lecturer row + add Status Description row

### Summary

- Removed the **Lecturer** row (this is the lecturer's own timetable — self-referential).
- Added a **Status Description** row: "Scheduled class with no issues" / "Replacement request awaiting approval" / "Scheduling conflict — needs attention".

### Files Changed

#### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `openModal()` | Updated | Dropped `Lecturer` row; added `Status Description` row (description now its own row, not inline caption). |

---

## [2026-08-15] Detail modal refinements: gray × close, general title, split rows, dot colours

### Summary

- **Close button** — normal gray `×` (was red dot); removed header `modal-status-badge`.
- **General title** — header now "Class Details" (course code → subtitle); status as badge row with a brief description.
- **Split rows** — Start/End Time split; status uses `.badge-*`.
- **Timeline dot colours vary by status** for pending requests (`dot-warning`).

### Files Changed

#### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `openModal()` | Updated | General title, split rows, badge status, dot colours. |

#### `resources/views/partials/ui-class-detail-modal.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Removed header `modal-status-badge`. |

---

## [2026-08-15] Class detail modal redesign: unified detail sheet

### Summary

The Class Detail modal now uses the shared `DetailModal` "Detail Sheet": identity header (code title + name subtitle + status badge), optional pending global timeline, single flat group with definition rows (no per-row borders). Same fields preserved.

### Files Changed

#### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `openModal()` | Rewritten | Uses `DetailModal.render` (field data unchanged). |

---

## [2026-08-15] Page-specific summary card descriptions

Added page-specific `description` text to each summary card (Total Classes / Teaching Hours / Replacements / Pending / Conflicts) instead of relying on the shared generic descriptions in `ui-summary-bar`. (`MyTimetable-UI-design-template.blade.php` summary bar.)

---

## [2026-08-13] Phase 3 UX Enhancement: Legend Tooltips

### Summary

All legend items now have hover tooltips describing what each status means.

### Files Changed

#### `resources/views/partials/ui-legend-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 2-7 | Added | `tip` property to default items array |
| 2026-08-13 | Line 13 | Added | `title="{{ $item['tip'] }}"` on `.legend-item` div |

---

## [2026-08-13] Phase 3 UX Enhancement: Day Badges

### Summary

All three day badges (Today, Holiday, OFF) now use consistent badge styling. When today is a public holiday, both badges appear.

### Files Changed

#### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 859-870 | Changed | `dayHeader()`: holiday and today badges are no longer mutually exclusive |
| 2026-08-13 | Line 862 | Changed | `.holiday-label` → `.holiday-badge` |
| 2026-08-13 | Line 866 | Changed | `.off-label` → `.off-badge` |

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 1246-1267 | Replaced | `.holiday-label` + `.off-label` → `.holiday-badge` + `.off-badge` (badge styling) |

---

## [2026-08-13] Phase 3 UX Enhancement: Collapsible Guide Block

### Summary

Added an expandable guide block with page-specific workflow instructions.

### Files Changed

#### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 150-157 | Added | `@include('partials.ui-guide-block')` with 4 workflow tips |

---

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
| 2026-08-08 | Line 169 | Replace Now button | Updated `goToReplacement()` call to pass `currentModalEvent?.code` and `currentModalEvent?.cohort` as parameters — enables pre-filling subject in replacement-arrangement page. Added `currentModalEvent` state variable (line 251) and set it in `openModal()` (line 265). |
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
| 2026-08-13 | `@section('page-styles')` | macOS-style update (Phase 4) | `cancel-overlay`: `rgba(0,0,0,0.55)` → `0.45`, `blur(4px)` → `blur(8px)` to match theme.css modal. All 4 button classes (`btn-replace-now`, `btn-cancel-class`, `btn-cancel-secondary`, `btn-cancel-danger`): `font-weight: 600` → `500` for macOS consistency. Added `:focus-visible` outline rings to all 4 buttons. |
| 2026-08-13 | `@section('page-styles')` | macOS modal refinement | `cancel-overlay` blur synced from `8px` → `6px` to match refined theme.css modal overlay. |
| 2026-08-13 | `public/css/theme.css` (shared) | macOS table fix | `.timetable`: `border-collapse:collapse` → `separate` + `border-spacing:4px`; removed 1px cell borders; hover uses `var(--color-surface-variant)`; removed zebra striping. `.badge` border-radius 6px→8px. |
| 2026-08-14 | `@section('page-styles')` | Offday-slot today-cell fix | Added `.today-cell.offday-slot { background: transparent; }` override so PH/Sunday empty cells are not red when today. |
| 2026-08-14 | `prevWeek/nextWeek/selectWeek` | WeekNavigator delegation | Local nav logic replaced with `weekNav.prevWeek()/nextWeek()/selectWeek()`; `buildTimetable()` syncs `currentWeek = weekNav.currentWeek`. Removed manual select-index/subtitle/progress/arrow/save updates. |
