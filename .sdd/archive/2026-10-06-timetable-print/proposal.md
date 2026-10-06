# Proposal: Timetable Print

## Why

Timetable pages (My Timetable, Cohort Timetable, Student My Timetable) have no print functionality. Users need hard copies of their weekly schedules for:
- Lecturers carrying schedules to class
- Admins printing cohort timetables for orientation handouts
- Students pinning schedules on noticeboards/fridges

Currently, users must screenshot or manually recreate schedules — error-prone and time-consuming.

## What

Add a print button (icon-only, printer SVG) to the shared week navigation partial (`ui-week-nav.blade.php`), controlled by a `$showPrintBtn` parameter. The button is placed after the week navigation arrows and before the week selector dropdown. Only timetable pages pass `showPrintBtn: true`; non-timetable pages (Replacement Home, Request Approval, My Request History, Replacement Arrangement) omit it. When the week nav is disabled (e.g., Cohort Timetable before a cohort is selected), the print button is also disabled.

Clicking the button opens the browser's native print dialog (`window.print()`), which includes "Save as PDF" as a built-in option.

Print output is optimized via `@media print` CSS:
- Landscape orientation, full-width grid
- Light background (force CSS custom property overrides for dark mode)
- Expanded event blocks showing code, name, venue, time
- Legend bar included; summary bar, progress bar, navigation controls hidden
- Today's highlight removed (transient state)
- Multi-page flow allowed if grid exceeds one page

## Scope

### In Scope
| Item | Detail |
|------|--------|
| Print button | Icon-only printer button in `ui-week-nav.blade.php` via `$showPrintBtn` param |
| Print CSS | `@media print` block in `theme.css` |
| Print function | `printTimetable()` in `ui-common.js` |
| Target pages | My Timetable, Cohort Timetable, Student My Timetable |

### Out of Scope
| Item | Reason |
|------|--------|
| PDF download (jsPDF) | +300KB dependency, rasterized output, worse quality |
| Multi-week batch printing | Out of initial scope, can add later |
| Venue timetable | Not yet built |
| Server-side PDF generation | Frontend-only project, no backend changes |
| Print on non-timetable pages | Not applicable — no timetable grid to print |

## Impact

### Files Modified
| File | Change |
|------|--------|
| `public/css/theme.css` | Add `@media print` block (~60 lines) |
| `public/js/ui-common.js` | Add `printTimetable()` function (~5 lines) |
| `resources/views/partials/ui-week-nav.blade.php` | Add conditional print button via `$showPrintBtn` param |

### Pages Updated (pass `showPrintBtn: true`)
| Template | Route |
|----------|-------|
| `MyTimetable-UI-design-template.blade.php` | `/my-timetable-ui` |
| `CohortTimetable-UI-design-template.blade.php` | `/cohort-timetable-ui` |
| `student-my-timetable-UI-design-template.blade.php` | `/student-my-timetable-ui` |

### Dependencies
- None — pure CSS + vanilla JS + `window.print()` API

### Risk
- **Low** — additive change, no existing behavior modified
- Print CSS only applies in `@media print`, zero runtime impact on screen rendering
- `$showPrintBtn` parameter defaults to `false`, so existing non-timetable pages are unaffected

## Acceptance Criteria

1. Click print on each of the 3 target pages → correct timetable content prints in landscape
2. Print from dark mode → output has light background (CSS custom properties overridden)
3. Print from non-timetable pages → button does not appear
4. Multi-page print → no content clipping, day labels visible on each page
5. Expanded event blocks → code, name, venue, time all visible without hover/click
