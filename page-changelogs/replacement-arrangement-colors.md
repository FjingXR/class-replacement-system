# Color Reference — Replacement Arrangement

## Legend Swatches

| Label | CSS Token | Dark Mode | Light Mode | Matches Cell |
|---|---|---|---|---|
| Available | `var(--color-success-container)` | `rgba(46, 125, 90, 0.35)` | `rgba(46, 125, 90, 0.25)` | `.cell-available` |
| Your Current Selection | `var(--color-primary-container)` | `rgba(0, 77, 152, 0.35)` | `rgba(0, 77, 152, 0.25)` | `.cell-selected` |
| Pending (You) | `var(--color-tertiary-container)` | `rgba(219, 168, 0, 0.35)` | `rgba(219, 168, 0, 0.25)` | `.cell-pending` |
| Reserved by Others | `var(--color-surface-variant)` | `#243447` | `#E4E8EE` | `.cell-reserved` |
| Occupied / Public Holiday | `var(--color-error-container)` | `rgba(198, 40, 40, 0.35)` | `rgba(198, 40, 40, 0.25)` | `.cell-occupied` |

## Cell States (Timetable Grid)

| State | CSS Class | Background Token | Dark Mode | Light Mode | Extra |
|---|---|---|---|---|---|
| Available | `.cell-available` | `var(--color-success-container)` | `rgba(46, 125, 90, 0.35)` | `rgba(46, 125, 90, 0.25)` | `cursor: pointer` |
| Selected | `.cell-selected` | `var(--color-primary-container)` | `rgba(0, 77, 152, 0.35)` | `rgba(0, 77, 152, 0.25)` | `border: 2px solid var(--color-primary)` |
| Pending | `.cell-pending` | `var(--color-tertiary-container)` | `rgba(219, 168, 0, 0.35)` | `rgba(219, 168, 0, 0.25)` | `cursor: not-allowed` |
| Reserved | `.cell-reserved` | `var(--color-surface-variant)` | `#243447` | `#E4E8EE` | `cursor: not-allowed` |
| Occupied | `.cell-occupied` | `var(--color-error-container)` | `rgba(198, 40, 40, 0.35)` | `rgba(198, 40, 40, 0.25)` | `pointer-events: none` |

## Selected Cell Text Colors

| Element | CSS Token | Dark Mode | Light Mode |
|---|---|---|---|
| `.sel-text` | `var(--color-on-primary-container)` | `#6BA3E0` | `#003366` |

## Base Color Tokens (Solid Foregrounds)

| Token | Dark Mode | Light Mode | Used By |
|---|---|---|---|
| `--color-success` | `#2E7D5A` | `#2E7D5A` | `.cell-selected` border reference |
| `--color-primary` | `#004D98` | `#004D98` | `.cell-selected` border |
| `--color-tertiary` | `#DBA800` | `#DBA800` | — |
| `--color-error` | `#C62828` | `#C62828` | — |
| `--color-surface-variant` | `#243447` | `#E4E8EE` | `.cell-reserved` background |

## Container Tokens (Translucent Backgrounds)

| Token | Dark Mode | Light Mode | Opacity Change |
|---|---|---|---|
| `--color-success-container` | `rgba(46, 125, 90, 0.35)` | `rgba(46, 125, 90, 0.25)` | 0.35 → 0.25 |
| `--color-primary-container` | `rgba(0, 77, 152, 0.35)` | `rgba(0, 77, 152, 0.25)` | 0.35 → 0.25 |
| `--color-tertiary-container` | `rgba(219, 168, 0, 0.35)` | `rgba(219, 168, 0, 0.25)` | 0.35 → 0.25 |
| `--color-error-container` | `rgba(198, 40, 40, 0.35)` | `rgba(198, 40, 40, 0.25)` | 0.35 → 0.25 |
| `--color-surface-variant` | `#243447` | `#E4E8EE` | Solid (different hex) |

## Design Notes

- Legend swatches use the **same tokens** as cell backgrounds (container variants)
- Container tokens are translucent RGBA — they appear as muted/tinted versions of the solid foreground
- Dark mode uses 35% opacity containers; light mode uses 25% opacity containers
- `--color-surface-variant` is the exception: solid hex, different between dark (#243447) and light (#E4E8EE)
- Legend and cell colors are always in sync — changing a cell token automatically updates the legend
- Legend bar has `background: var(--color-surface)` so swatches render on the same surface as timetable cells
