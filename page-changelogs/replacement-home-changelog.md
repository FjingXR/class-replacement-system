# Changelog — Replacement Home Dashboard

## Files Changed

### `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`

| Change | Detail |
|--------|--------|
| New page created | Replacement Arrangement Home Dashboard — lists all conflicted classes in a searchable, filterable, sortable table with pagination, conflict reason badges, and "Arrange Replacement" action buttons |
| Fix filter dropdown dark mode | Added `.filter-dropdown option` with explicit `background` and `color` overrides to fix low-contrast option text in dark mode |
| Remove reason filter | Commented out reasonFilter dropdown HTML, removed JS references, extended search bar width to 420px — search alone is sufficient for filtering |
| Add summary dashboard | Added 4 stat cards below the table (Total Conflicted, Venues Affected, Students Affected, Duration Hours) with `updateSummary()` JS tied to filtered dataset — hidden when empty state is shown |
| Replace Need Action with Duration Hours | Changed 4th card from duplicate count-based metric to total teaching hours across conflicted classes |
| Add Distinct Courses card | Added 5th summary card; made mock data entries 2 and 10 share codes with entries 1 and 9 (12 distinct courses out of 14 total); compressed all cards to fit in a 5-column grid |
| Remove gap between summary cards | Root cause was not `gap` but `width: 80%` + `justify-self: center` leaving ~10% whitespace in every grid cell; removed both so cards fill cells edge-to-edge, set gap to 4px, trimmed card padding |
| Narrow summary cards | Undid `max-width` approach; instead set each card to `width: 80%` with `justify-self: center` so each card is 20% narrower within its grid cell |
| Adjust summary card gap | Changed gap from 0 to 6px — 0 still produced visual gaps due to card borders; 6px gives clean, minor separation |
| Shrink card width | Each card set to 80% width and centered within its grid cell for a more compact appearance |
| Revert narrowing | Removed `max-width: 720px` and centering — cards return to full 1fr cell width (edge-to-edge with 4px gap) |
| Add Affected Cohort(s) column | Inserted new column between Students and Conflict Reason; added `cohorts` array to all 14 mock entries (mix of 1–2 cohorts per row); rendered with `<br>` separators; added `.col-cohort { width: 130px }` CSS; increased table min-width to 1170px |
| Add Days Left / Urgency column | Inserted between Day and Time (13 columns total); computed from `c.date` relative to today; color-coded urgency (≤7d red, 8–30d teal, 31+ default); added `urgencyClass()` and `daysLeft()` helpers; added `.col-urgency` CSS |
| Default sort by Date ascending | Changed `sortState` from `{ field: '', dir: 'asc' }` to `{ field: 'date', dir: 'asc' }` — Date column gets default sort arrow and data sorts earliest-first on page load |
| Add sort hint | Added italic "Click **Date** or **Course Code & Name** to sort" between toolbar and table — names only the two sortable columns |

### `routes/web.php`

| Change | Detail |
|--------|--------|
| New route | `GET /replacement-home-ui` → `replacement-home-UI-design-template` |
