# Venue Dropdown — Nested Grouped Dropdown

## Changes Made

### New files
- `resources/views/partials/ui-venue-dropdown.blade.php` — shared Blade partial for the nested dropdown

### Modified files
- `public/css/theme.css` — added `.venue-dd-*` CSS classes (trigger, panel, groups, items, expand animation)
- `public/js/ui-common.js` — added `VenueDropdown` class with hover-to-expand groups, favourites management, localStorage persistence
- `venue-timetable-UI-design-template.blade.php` — replaced native `<select>` + venue-type filter with shared partial, updated JS (onVenueChange, favourites, state persistence)
- `replacement-arrangement-UIdesign-template.blade.php` — replaced native `<select>` with shared partial, updated JS (onVenueChange, course-based filtering via setFilter)

### Removed
- Venue-type filter CSS (`.filter-bar`, `.venue-type-filter`, `.venue-type-btn`, `.venue-type-dropdown`)
- Venue-type filter HTML (checkbox dropdown)
- JS functions: `toggleVenueTypeDropdown()`, `applyVenueTypeFilter()`, `resetFilters()`, `buildVenueDropdown()`
- `venueTypeFilters` variable, `venueState` createStatePersistence for venueTypes

## Design
- Click trigger to open → hover group to expand → click venue to select
- Groups: ★ Favourites → Tutorial → Lecture Hall → Lab → CiscoLab (empty groups hidden)
- One group open at a time; closes on mouse leave
- Star icon on favourited venues in type groups; Favourites group shows type label
- Max 5 favourites — star button disables with tooltip when full
- Shared localStorage key `venueFavourites` (backward compatible)
