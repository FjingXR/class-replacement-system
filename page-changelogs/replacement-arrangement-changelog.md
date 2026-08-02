# Changelog — Replacement Arrangement (Selected Subject Page)

## Files Changed

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Change | Detail |
|--------|--------|
| Per-venue slot data | `slotData` replaced with `venueSlotData` (4 venues: B103–B106 each with unique availability pattern) |
| Selections per venue | `selectedSlotsByWeek` → `selectedSlotsByVenue` — switching venues saves and restores picks |
| onVenueChange handler | Added; wires buildingSelector to rebuild timetable with new venue's data |
| Summary cards show venue | Added `.card-venue` line in each summary card; info panel label changed to "Venue" |
| Proceed confirmation | Venue name included in each line item |
| clearAll | Extended to clear all venues' selections |
| Time column center | Added `text-align: center` to `.time-col` |

### `routes/web.php`

| Change | Detail |
|--------|--------|
| New route | `GET /replacement-home-ui` → `replacement-home-UI-design-template` |

## Commits

- `db7ce18` — Conflict slots: show CONFLICT label, redirect to replacement-arrangement page on click
- `628a42e` — Header nav links added, per-venue slot data with selection persistence per venue
- `aeddba2` — Weekly Summary Bar added, hour-cell height increased to 80px, summary bar stats, padding/spacing adjustments

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` — Centralised mock data (Task 9)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-02 | `@section('page-scripts')` | Centralised mock data | `weekData` and `venueSlotData` moved to `public/js/mock-data.js` — page-specific data, NOT standardised to semester. Semester chip now reads `MockData.semester.chipText` dynamically. |
| 2026-08-02 | `.week-nav` wrapper | OOP refactor | Arrows + select wrapped in `.week-nav` div; local `.week-nav` CSS removed (now in `theme.css`). |
| 2026-08-02 | `@section('page-scripts')` | Dynamic week options | Week selector options generated from `MockData.arrangementWeeks` with mobile date format (no year). |
| 2026-08-02 | `@media (max-width: 768px)` | Footer overflow fix | `.footer-area` stacks column on mobile; `.footer-right` gets `flex-wrap: wrap`, `gap: 8px`; buttons get smaller padding/font. Verified working at 375px width. |
| 2026-08-02 | JS (`buildTimetable`) + CSS | Mobile time slot labels | Added `.cell-time-label` span inside each timetable cell showing the hour (e.g. "10:00"); hidden on desktop, visible on mobile at 8px font below each cell's content. |
| 2026-08-02 | CSS (`@media 768px`) | Mobile cell sizing | `.hour-cell` and `.cell-content` set to 48×48px, row gap 8dp on mobile. |
