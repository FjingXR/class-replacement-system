# Changelog — Replacement Home Dashboard

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

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 10:00 | Line ~25 (new) | New route | Added `Route::get('/replacement-home-ui', ...)` returning the standalone template view |
