# Venue Dropdown — Nested Grouped Dropdown Plan

## Goal
Replace the native `<select>` venue dropdown on both venue-timetable and replacement-arrangement pages with a shared, custom nested dropdown component. Hover-to-expand groups, click-to-select.

---

## Design Decisions

| Aspect | Decision |
|--------|----------|
| **Interaction** | Click trigger to open → hover group to expand → click venue to select |
| **Groups** | ★ Favourites → Tutorial → Lecture Hall → Lab → CiscoLab (empty groups hidden) |
| **Hover** | One group open at a time; closes on mouse leave |
| **Star icon** | On favourited venues **in type groups only** (not in Favourites group) |
| **Favourites in type groups** | `[⭐] B002 — (35 seats)` (no type label, implied by group) |
| **Favourites group** | `⭐ B002 — Tutorial (35 seats)` (type label for context) |
| **Max favourites** | 5 — star button disables with tooltip "Maximum 5 favourites" |
| **Close** | Click outside dropdown, or select a venue |
| **State persistence** | Each page handles its own (not baked into component) |
| **Venue type filter** | **Removed** — redundant with grouped dropdown |

---

## Files to Create/Modify

| # | File | Action |
|---|------|--------|
| 1 | `public/css/theme.css` | **Add** — nested dropdown CSS |
| 2 | `public/js/ui-common.js` | **Add** — `VenueDropdown` class (~120 lines) |
| 3 | `resources/views/partials/ui-venue-dropdown.blade.php` | **Create** — shared Blade partial |
| 4 | `venue-timetable-UI-design-template.blade.php` | **Modify** — replace `<select>` + venue-type filter with partial, update JS |
| 5 | `replacement-arrangement-UIdesign-template.blade.php` | **Modify** — replace `<select>` with partial, update JS |

---

## Step 1: CSS in `theme.css`

Add a `/* ── Nested Venue Dropdown ── */` section:

- `.venue-dd` — relative container
- `.venue-dd-trigger` — the clickable button showing current venue
- `.venue-dd-panel` — the dropdown panel (hidden by default, `position: absolute`)
- `.venue-dd-group` — each expandable group
- `.venue-dd-group-header` — the group label (hover target)
- `.venue-dd-group-items` — the venue list (max-height, overflow, transition)
- `.venue-dd-item` — each venue row (click target, hover highlight)
- `.venue-dd-item .fav-star` — the star icon on favourited items
- States: `.open` (panel visible), `.expanded` (group open), `.active` (selected venue)

---

## Step 2: `VenueDropdown` class in `ui-common.js`

```js
class VenueDropdown {
  constructor(container, {
    venues,           // MockData.venues array
    onSelect,         // callback(code) when venue selected
    storageKey,       // localStorage key for favourites (default: 'venueFavourites')
    maxFavourites,    // default: 5
    initialCode       // pre-selected venue code
  })

  // Public
  select(code)          // programmatically select a venue
  getSelected()         // returns current venue code
  refreshFavourites()   // re-render favourites group (call after toggling fav externally)
  getFavourites()       // returns array of favourite codes
  toggleFavourite(code) // add/remove from favourites (enforces max limit)
  destroy()             // cleanup event listeners

  // Private
  _buildGroups()           // build Favourites + type groups from venues
  _toggleGroup(groupEl)    // expand one group, collapse others
  _close()                 // close the entire dropdown
}
```

**Favourites**: read/written to localStorage (same key as current `venueFavourites`).

**Group expansion**: `mouseenter` on `.venue-dd-group-header` → add `.expanded` to that group, remove from others. `mouseleave` on `.venue-dd-group` → remove `.expanded`.

---

## Step 3: Blade partial `ui-venue-dropdown.blade.php`

```blade
@php
    $selectId = $selectId ?? 'venueSelect';
    $storageKey = $storageKey ?? 'venueFavourites';
@endphp
<div class="venue-dd" id="{{ $selectId }}Dropdown">
    <button class="venue-dd-trigger" type="button">Select a venue &#9662;</button>
    <div class="venue-dd-panel"></div>
</div>
```

Minimal — the JS class populates the panel content.

---

## Step 4: venue-timetable changes

- **Remove**: venue-type filter HTML (`.filter-bar` block), `toggleVenueTypeDropdown()`, `applyVenueTypeFilter()`, `venueTypeFilters` variable, related CSS (`.filter-bar`, `.venue-type-filter`, `.venue-type-btn`, `.venue-type-dropdown`)
- **Replace**: `<select id="venueSelect">` + `<button class="fav-btn">` with `@include('partials.ui-venue-dropdown')` + external star button
- **Update `onVenueChange`**: read from `venueDropdown.getSelected()` instead of `document.getElementById('venueSelect').value`
- **State persistence**: simplify `saveState`/`restoreState` to only save venue code (remove venueTypes from `createStatePersistence`)
- **Favourite toggle**: star button calls `venueDropdown.toggleFavourite(code)` + `venueDropdown.refreshFavourites()`
- **Capacity check**: star button disabled when `getFavourites().length >= 5`, tooltip "Maximum 5 favourites"

---

## Step 5: replacement-arrangement changes

- **Replace**: `<select id="buildingSelector">` with `@include('partials.ui-venue-dropdown', ['selectId' => 'buildingSelector'])`
- **Remove**: `buildVenueDropdown(filterByCourse)` function
- **Update `onVenueChange`**: read from `venueDropdown.getSelected()`
- **Keep**: course-based venue filtering — pass a `filter` option to VenueDropdown constructor (e.g. `filter: (v) => v.type === 'Tutorial' || ...`)

---

## Order of Implementation

1. CSS in `theme.css` (visual foundation)
2. `VenueDropdown` class in `ui-common.js` (core logic)
3. Blade partial (markup shell)
4. Wire into venue-timetable (more complex — has favourites, state, star button)
5. Wire into replacement-arrangement (simpler)
6. Test both pages, verify hover/click/favourites/limit all work

---

## Notes

- The existing `getFavourites()` / `toggleFavourite()` in venue-timetable will be **replaced** by the VenueDropdown class methods
- `updateRecent()` stays page-specific (recent venues are only on venue-timetable)
- The `venueFavourites` localStorage key stays the same for backward compatibility
- `createStatePersistence` on venue-timetable will be simplified (remove `venueTypeDropdown` checkbox-group field)
