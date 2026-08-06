# Changelog — Replacement Home Dashboard

## [2026-08-06] Touch Target Upgrade: 44px → 48px (8dp)

### Changed
- **Touch targets**: Upgraded from 44×44px to 48×48px (8dp) in `theme.css` for all mobile buttons/links. Pill-tab padding also increased to 12px.

## [2026-08-06] Bug Fix: qvArrange() Navigation Broken

### Fixed
- **`qvArrange()` JS bug**: `hideQuickView()` nullified `qvCurrent` before reading its `code`/`date`, causing a `TypeError`. Fix: saved values to local vars first, then hide, then navigate.

## [2026-08-03] Mobile Responsive: Quick View Bottom Sheet + Touch Targets

### Added
- **Quick View modal → bottom-sheet on mobile (≤768px)**: Modal slides up from bottom, 80vh max height, scrollable body, drag handle bar, full-screen backdrop, full-width buttons in footer
- **Touch targets**: Upgraded from 40px (tablet) to 44px (WCAG 2.5.5) on mobile for week arrows, modal buttons, and arrange button
- **Toolbar week-nav**: Full-width stacked layout on mobile (search above, week picker below)

### Changed
- Modal animation: bottom-sheet slide-up on mobile (replaces centered scale on small screens)

## [2026-08-03] Refactor: Rows Per Page promoted to shared OOP component

### Changed
- **Rows Per Page (RPP)**: Removed local CSS/HTML/JS; now uses shared `partials.ui-rpp` Blade partial + `initRpp()` from `ui-common.js` + `.rpp-wrapper`/`.rpp-select` from `theme.css`
- RPP moved from standalone div below pagination-bar to **inside** `.pagination-bar` (consistent with my-request-history)
- Default changed from 5 to 10; options now `[10, 25, 50, 'all']`; localStorage key `'rpp-page-size'`

## [2026-08-03] UX Refinement: Remove Filter Presets, Standardise Modals

### Removed
- **F2 – Filter Presets (localStorage)**: Removed My Filters panel (Save/Apply/Delete buttons), associated CSS, and JS functions (`saveFilter`, `applyFilter`, `deleteFilter`, `loadFilterPrefs`)

### Changed
- **Keyboard Shortcuts Modal**: Replaced custom inline-styled modal (`.modal-box`, `z-index:3000`) with standard `.modal-overlay` + `.modal` pattern from `theme.css` (consistent with my-request-history, replacement-arrangement)
- **Quick View Modal**: Replaced custom inline-styled modal with standard `.modal-overlay` + `.modal` pattern; fields now use `.modal-field` / `.modal-field-label` / `.modal-field-value` classes
- Both modals now toggle via `.show` class instead of `style.display`, and use `.modal-close` (✕) button + `.btn-close-modal` footer button
- Removed unused custom CSS: `.my-filters-panel`, `.btn-outline`, `.quick-view-*`, `.table-body tr` cursor/hover

## [2026-08-03] UX Enhancement: Rows Per Page, Filter Presets, Keyboard Shortcuts, Quick View Modal, Column Consolidation

### Added
- **F1 – Rows Per Page Selector**: Dropdown below pagination with options [5, 10, 25, 50]; default 5 rows per page; resets to page 1 on change
- **F2 – Filter Presets (localStorage)**: My Filters panel with Save/Apply/Delete buttons; persists search, filter, and sort state to localStorage (`replacementHomeFilterPrefs`); restores on page load
- **F5 – Keyboard Shortcuts**: Help icon button in toolbar; `?` global keydown opens modal; shortcuts: `/` focus search, `Esc` clear filters, `←`/`→` page nav, `Enter` quick view
- **F6 – Quick View Modal**: Click any table row to open modal showing full class details (12 fields); closes on OK, X, backdrop click, or `Esc`
- **F7 – Column Consolidation**: Merged 14 columns → 9 columns; date/time/week merged into "Original Class" column using `formatClassBlock()`; Type merged as `(L)`/`(T)` suffix in Course Code column

### Changed
- Table now displays 9 columns (was 14): #, Course Code & Name, Original Class, Days Left, Venue, Students, Affected Cohorts, Conflict Reason, Action
- Sort by "Original Class" sorts by underlying date value
- Keyboard shortcuts don't trigger when input/textarea/select is focused

## Files Changed

### `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 10:00 | — | New page created | Replacement Arrangement Home Dashboard — 944-line standalone Blade template with top nav, searchable/filterable/sortable 11-column table, pagination, conflict reason badges, "Arrange Replacement" action buttons |
| 2026-07-20 10:30 | CSS (`.filter-dropdown option`) | Fix filter dropdown dark mode | Added explicit `background`/`color` overrides for dropdown options to fix low-contrast text in dark mode |
| 2026-07-20 11:00 | HTML (toolbar) + JS | Remove reason filter | Commented out reasonFilter dropdown HTML; removed JS references; extended `.search-input` width from 260px to 420px |
| 2026-07-20 11:30 | CSS + HTML + JS | Add summary dashboard | 4 stat cards below table (Total Conflicted, Venues Affected, Students Affected, Duration Hours) with `updateSummary()` JS; hidden when empty |
| 2026-07-20 11:45 | HTML (card 4) | Replace Need Action with Duration Hours | Changed 4th card from `filtered.length` to sum of `c.duration` across filtered rows |
| 2026-07-20 12:00 | CSS + HTML + JS + mock data | Add Distinct Courses card | Added 5th card; entries 2 & 10 now share codes with entries 1 & 9 (12 distinct / 14 total); grid → `repeat(5, 1fr)`; padding compressed |
| 2026-07-20 12:15 | CSS (`.summary-bar`) | Remove gap between summary cards | Removed `width: 80%` + `justify-self: center` root cause; cards fill cells edge-to-edge; `gap: 4px` |
| 2026-07-20 12:30 | CSS (`.summary-bar`) | Revert card narrowing | Removed `max-width: 720px` auto-centering; bar back to full 1fr width |
| 2026-07-20 13:00 | CSS + JS columns + mock data | Add Affected Cohort(s) column | Inserted between Students and Conflict Reason (12 columns); `cohorts` array on all 14 entries; rendered with `<br>`; table min-width → 1170px |
| 2026-07-20 13:30 | CSS + JS helpers + columns | Add Days Left / Urgency column | Inserted between Day and Time (13 columns); `daysLeft()` + `urgencyClass()` helpers; color-coded (≤7d red, 8–30d teal, 31+ default); table min-width → 1270px |
| 2026-07-20 13:45 | JS (`sortState`) | Default sort by Date ascending | Changed `sortState.field` from `''` to `'date'`, `dir` to `'asc'`; Date column shows sort arrow on page load |
| 2026-07-20 14:00 | HTML + CSS | Add sort hint | Added "Click **Date** or **Course Code & Name** to sort" between toolbar and table; `.sort-hint { text-align: left }` |
| 2026-07-20 14:30 | CSS + JS columns + helpers | Add Week column | Inserted between Type and Date (14 columns); `computeWeek()` relative to semester start (Aug 31, 2026); `.col-week { width: 80px }`; table min-width → 1350px |
| 2026-07-20 14:45 | HTML (toolbar) + JS | Add week dropdown | Added `filter-select` dropdown dynamically populated via `populateWeekDropdown()` from unique weeks in data |
| 2026-07-20 15:00 | HTML + JS + CSS | Remove date range filter | Removed From/To date inputs, `matchesDate` logic, date event listeners, `.filter-input`/`.filter-label` CSS |
| 2026-07-20 15:30 | CSS (`.filter-select`) | Fix weekFilter dark mode | Added `html.dark .filter-select { color-scheme: dark; }` to force native dark dropdown rendering, overriding GTK green tint |
| 2026-08-02 12:00 | HTML + JS (full page) | Centralised mock data (Task 6) | Removed 14-row inline `conflictedClasses` array; replaced with `MockData.conflictedClasses` reference; standardised `computeWeek()` and `weekRangeLabel()` to use `MockData.semester.startDate`; replaced static semester chip with dynamic `#semesterChip` populated from `MockData.semester.chipText` |
| 2026-08-02 | `@section('page-scripts')` | Centralised mock data (Task 9) | `conflictedClasses` moved to `mock-data.js` (page-specific, not shared). Week computation standardized to `MockData.semester.startDate`. Semester chip now reads `MockData.semester.chipText` dynamically. |
| 2026-08-02 | CSS + HTML + JS | Mobile card view | Added `.replacement-card` / `.rc-header` / `.rc-body` / `.rc-footer` card styles; added `#cardView` container; added `renderCards()` JS function; on mobile (≤768px) grid/pagination/sort-hint hidden, card-view shown; cards show course code, conflict reason badge, name, date/time/venue, days left, cohorts |
| 2026-08-03 | CSS + JS columns + cells | F7: Column consolidation (14 → 9) | Merged Week/Date/Day/Time/Duration into single "Original Class" column using `formatClassBlock(c)` + `dayAbbr()` helpers; merged Type into Course Code as `(L)`/`(T)` suffix; added `.cell-class-block` / `.class-day-date` / `.class-time` / `.class-duration` CSS; removed `.col-type`/`.col-week`/`.col-date`/`.col-day`/`.col-time`/`.col-duration`; added `event.stopPropagation()` on action button; sort hint updated |
| 2026-08-03 | CSS + HTML + JS | F1: Rows Per Page selector | Added `state.rpp` (default 5), `setRpp(n)` function, `rppSelect` dropdown (5/10/25/50), CSS for `.rows-per-page`/`.rpp-label`/`.rpp-select`; replaced `pageSize` with `state.rpp` in offset/slice/paginate; resets to page 1 on change; hidden on mobile |
| 2026-08-03 | HTML + JS | F5: Keyboard Shortcuts modal | Added help icon button in toolbar; added `#keyboardModal` overlay with 7-row shortcuts table (/, Esc, →, ←, Enter, ?); added `showKeyboardShortcuts()`/`hideKeyboardShortcuts()` functions; added global `keydown` listener for `/` (focus search), `Esc` (close modal or clear filters), `?` (open modal), `ArrowRight`/`ArrowLeft` (page navigation); added `clearAll()`/`goNextPage()`/`goPrevPage()` helper functions |
| 2026-08-04 | — | Refactored: replaced inline page-header/week-nav/empty-state/grid-table/modal with `@include('partials.…')` (OOP Phase 1) | Page uses `ui-page-header`, `ui-week-nav`, `ui-grid-table`, `ui-empty-state` partials. |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 10:00 | Line ~25 (new) | New route | Added `Route::get('/replacement-home-ui', ...)` returning the standalone template view |
