# Venue Dropdown — Cascading 4-Column Hierarchy

## Changes Made

### New files
- `resources/views/partials/ui-venue-dropdown.blade.php` — shared Blade partial for the dropdown (`.venue-dd` container + trigger + panel)

### Modified files
- `public/css/theme.css` — replaced the old grouped-flyout `.venue-dd-*` styles with the cascading column model: `.venue-col` (`display:none` → `.visible`), `.venue-col-header` (uppercase section label), `.venue-col-item` / `.venue-col-item-parent` (label + chevron, `.active` highlight), `.venue-col-item-room` (code + capacity, `.selected` + ✓ checkmark)
- `public/js/ui-common.js` — `VenueDropdown` rewritten around 4 cascading columns (`Type | Block | Floor | Room`); favourites management + localStorage persistence kept
- `venue-timetable-UI-design-template.blade.php` — replaced native `<select>` + venue-type filter with shared partial, updated JS (onVenueChange, favourites, state persistence)
- `replacement-arrangement-UIdesign-template.blade.php` — replaced native `<select>` with shared partial, updated JS (onVenueChange, course-based filtering via `setFilter`)

### Removed
- Old flyout markup/CSS/JS: `.venue-dd-group*`, `.venue-dd-item*`, hover-to-expand flyouts, `_addFlatGroup()` / `_createGroup()` / `_expandGroup()`
- Venue-type filter CSS (`.filter-bar`, `.venue-type-filter`, `.venue-type-btn`, `.venue-type-dropdown`)
- Venue-type filter HTML (checkbox dropdown)
- JS functions: `toggleVenueTypeDropdown()`, `applyVenueTypeFilter()`, `resetFilters()`, `buildVenueDropdown()`
- `venueTypeFilters` variable, `venueState` createStatePersistence for venueTypes

## Design
- Click trigger to open → hover (or click) a parent to reveal the next column → click a Room to select
- **4-level hierarchy, one vertical column each**: Type → Block → Floor → Room (child column immediately to the right of its parent)
- Chevrons only on items with a level beneath them; Rooms are the only selectable level (capacity meta + selected ✓/highlight)
- **Favourites** and **Recent** are utility groups (not hierarchy levels) — expandable **2-level** groups at the top of the first column (`★ Favourites › [rooms]`, `Recent › [rooms]`), hidden when empty
- Floor rule: 1st digit after letter — `B0__` = Ground Floor, `B1__` = Floor 1, `B2__` = Floor 2
- Menu grows horizontally to fit columns; no internal horizontal scrollbar
- Room rows show star icon (favourite) / clock icon (recent) in their utility column
- Max 5 favourites — star button disables with tooltip when full
- Shared localStorage key `venueFavourites` / `venueRecent` (backward compatible)
