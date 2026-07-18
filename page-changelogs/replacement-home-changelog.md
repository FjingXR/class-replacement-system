# Changelog — Replacement Home Dashboard

## Files Changed

### `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`

| Change | Detail |
|--------|--------|
| New page created | Replacement Arrangement Home Dashboard — lists all conflicted classes in a searchable, filterable, sortable table with pagination, conflict reason badges, and "Arrange Replacement" action buttons |
| Fix filter dropdown dark mode | Added `.filter-dropdown option` with explicit `background` and `color` overrides to fix low-contrast option text in dark mode |
| Remove reason filter | Commented out reasonFilter dropdown HTML, removed JS references, extended search bar width to 420px — search alone is sufficient for filtering |

### `routes/web.php`

| Change | Detail |
|--------|--------|
| New route | `GET /replacement-home-ui` → `replacement-home-UI-design-template` |
