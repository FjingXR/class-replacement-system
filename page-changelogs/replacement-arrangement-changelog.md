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
