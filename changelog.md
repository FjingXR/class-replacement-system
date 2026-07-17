# Changelog — Session 2026-07-05

## Files Changed

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Change | Detail |
|--------|--------|
| App container padding | `padding: 0` → `padding: 16px 24px; padding-top: 72px` (matches Replacement Arrangement) |
| Grid wrapper margin | `margin: 0` → `margin-top: 14px` |
| Grid scroll max-height | `calc(100vh - 56px - 44px - 50px)` → `calc(100vh - 196px)` |
| Hour-cell height | `52px` → `80px` (fit 4-line event content) |
| Cell-empty min-height | `48px` → `80px` |
| Event-block padding | `2px 4px` → `6px 6px` |
| Event-block gap | `1px` → `2px` |
| Weekly Summary Bar | Added 4 stat cards below legend bar (Total Classes, Need Replacement, Pending Approval, Conflicts) with CSS, HTML, and `updateSummary()` JS function |
| Responsive styles | Added `.summary-bar` breakpoints at 1024px and 768px |
| Header nav links | Added real hrefs: Dashboard→`/dashboard`, My Timetable→`/my-timetable-ui`, Replacement Arrangement→`/replacement-arrangement` |
| Conflict slots open modal | Conflict cells now open the detail modal instead of redirecting directly |
| Removed (CONFLICT) label | The `(CONFLICT)` span removed from event blocks; modal handles conflict status display instead |
| Summary card labels | "Need Replacement" → "Confirmed", "Conflicts" → "Conflicts/Public Holiday" |
| Legend label | "Replacement" → "Confirmed Replacement" |
| Pending modal details | Added "Requested At" and "Requested By" fields for pending-status events |
| Cancel Class button | Non-conflict modals now show "Cancel Class?" button; opens confirmation dialog |
| Removed (PENDING) label | Pending event blocks no longer show the "(PENDING)" note on the grid |
| Card-total color | Changed from `primary-container` to `secondary-container` |
| Replacement label | "Confirmed" → "Confirmed Replacement" |
| Cancel button text | Dynamic: "Cancel Request?" for pending, "Cancel Class?" for others |
| Sunday date-label color | Set to `--color-on-error-container` |
| Conflict slot styling | Events on Public Holiday days show `(CONFLICT)` badge with `event-public-holiday` class instead of `event-normal` |
| Conflict slot click | Clicking a conflict slot redirects to `/replacement-arrangement` instead of opening the modal |

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Change | Detail |
|--------|--------|
| Per-venue slot data | `slotData` replaced with `venueSlotData` (4 venues: B103–B106 each with unique availability pattern) |
| Selections per venue | `selectedSlotsByWeek` → `selectedSlotsByVenue` — switching venues saves and restores picks |
| onVenueChange handler | Added; wires buildingSelector to rebuild timetable with new venue's data |
| Summary cards show venue | Added `.card-venue` line in each summary card; info panel label changed to "Venue" |
| Proceed confirmation | Venue name included in each line item |
| clearAll | Extended to clear all venues' selections |

### `draft/RO8/chp2 updated/2.3 Comparative Analysis Problem-Solution Mapping.md`

| Change | Detail |
|--------|--------|
| Synthesis section | Rewritten from paragraph form to B1-level bullet points; removed set notation (∩), code-like terms, and complex sentences |

## Commits

- `db7ce18` — Conflict slots: show CONFLICT label, redirect to replacement-arrangement page on click
- `628a42e` — Header nav links added, per-venue slot data with selection persistence per venue
- `aeddba2` — Weekly Summary Bar added, hour-cell height increased to 80px, summary bar stats, padding/spacing adjustments
