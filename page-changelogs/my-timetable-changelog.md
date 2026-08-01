# Changelog — Lecturer My Timetable

## Files Changed

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-21 08:09 | Lines 233–242 | Semester bar redesign | Changed from `height: 44px`, `background: secondary-container`, no border/shadow to `padding: 12px 16px`, `background: surface`, `border: 1px solid outline`, `border-radius: radius-md`, `box-shadow: shadow-sm` — matches request history toolbar style |
| 2026-07-21 08:09 | Lines 249–260 | Week arrow color | `color: on-secondary-container` → `on-surface-variant`; hover `rgba(12,51,33,0.1)` → `surface-variant` |
| 2026-07-21 08:09 | Lines 264–280 | Week select restyle | `border: rgba(12,51,33,0.15)` → `border: outline`; `background: rgba(12,51,33,0.06)` → `background: surface-variant`; `color: on-secondary-container` → `on-surface-variant` |
| 2026-07-21 08:09 | Lines 557–604 | Summary bar redesign | Changed from `display: flex` horizontal layout to `display: grid` with `grid-template-columns: repeat(4, 1fr)` and `gap: 4px`. Cards now vertical (`.summary-value` above `.summary-label`), `background: surface`, `border: 1px solid outline`, `box-shadow: shadow-sm`. Total card: `border: 2px solid primary` + `background: primary-container`. Removed per-card background colors. |
| 2026-07-21 08:09 | Lines 948–964 | Summary bar HTML | Renamed `.num` → `.summary-value`, `.label` → `.summary-label`. Labels now single-line with CSS `text-transform: uppercase` instead of using `<br>` for line breaks. |
| 2026-07-21 08:09 | Lines 296–302 | Gap fix | Removed `flex: 1` from `.grid-wrapper` and `padding-bottom: 0` from `.grid-scroll` — legend bar now sits directly below the timetable without extra gap. |
| 2026-07-21 08:09 | Line 294 | Dark mode fix | Added `html.dark .week-select { color-scheme: dark }` — ensures native dropdown renders correctly in dark mode (matches request history fix). |
| 2026-07-21 08:09 | Lines 835–836, 850–853 | Responsive | Updated responsive rules for grid layout: 1024px → `grid-template-columns: repeat(2, 1fr)`; 768px → `.summary-value { font-size: 20px }`, `.summary-label { font-size: 11px }`. |
| 2026-07-21 08:09 | Line 588 | Total card color | Changed `.summary-card.card-total .summary-value` color from `var(--color-on-primary-container)` to `var(--color-secondary)` — matches Normal Class legend swatch. |
| 2026-07-21 08:15 | Lines 231–246, 900–903 | Page header | Added `page-header` / `page-title` / `page-desc` CSS and HTML — matches my-request-history page header style. Title: "My Timetable", description: "View your weekly class schedule and manage replacement requests across all cohorts." |
| 2026-07-21 08:15 | Lines 264–280 | Week select restyle | Changed from `border: outline`, `background: surface-variant`, `color: on-surface-variant` to `border: none`, `background: secondary-container`, `color: on-secondary-container` — matches replacement-arrangement week selector style. |
| 2026-07-21 08:15 | Line 82 | Time column center | Added `text-align: center` to `.time-col`. |
| 2026-08-01 14:26 | Lines 299–312 | New summary card | Added `Teaching Hours` summary card (2nd position, after Total Classes) — shows total teaching hours for the week, computed from event spans (`(end - start + 1) * 0.5` per slot), displayed as decimal when needed (e.g. 21.5). Card style: `1px dashed outline-strong` border + `surface-variant` background, value color `on-surface`. Matches CohortTimetable's `card-hours` pattern. |
| 2026-08-01 14:26 | Lines 588–590 | Summary bar 5 cards | Added `card-hours` entry to `@include('partials.ui-summary-bar')` — summary bar now has 5 cards (Total Classes, Teaching Hours, Confirmed Replacement, Pending Approval, Conflicts/Public Holiday). Base grid in `theme.css` already uses `repeat(5, 1fr)`. |
| 2026-08-01 14:26 | Lines 936–952 | Summary logic | `updateSummary()` now computes `hours` and updates `sumHours` element — same formula as CohortTimetable. |
