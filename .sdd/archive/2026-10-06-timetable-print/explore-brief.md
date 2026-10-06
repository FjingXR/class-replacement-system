# Explore Brief: Timetable Print

## Problem

Timetable pages (My Timetable, Cohort Timetable, Student My Timetable) have no way to print or export the current week's schedule. Users need hard copies for lectures, orientation handouts, or personal reference.

## Scope

**In scope:**
- Add a print button (icon only) to all 3 timetable pages
- Browser print dialog (`window.print()`)
- Print-optimized CSS via `@media print`
- Current week only
- Expanded event blocks (code + name + venue + time inline)

**Out of scope:**
- PDF download (jsPDF/html2canvas)
- Multi-week batch printing
- Venue timetable (not yet built)
- Server-side PDF generation

## Decisions

### Print Method
- `window.print()` — zero dependencies, native browser print preview includes "Save as PDF"

### Print Button
- **Location:** Inside `ui-week-nav.blade.php` partial (appears on all timetable pages)
- **Style:** Icon only (printer SVG), matches existing toolbar button styling
- **Position:** After week navigation arrows, before week selector dropdown

### Layout
- **Orientation:** Landscape
- **Fit:** Allow multi-page if grid is too tall (better readability than cramped single-page)
- **Sticky column:** Removed for print (not needed with fit-to-width)
- **Overflow:** Force `overflow: visible` in print

### Content Included
| Element | Include? | Notes |
|---------|----------|-------|
| Page title | Yes | "My Timetable" / "Cohort Timetable" |
| Week label | Yes | "Week 5 · 14 Jul ~ 20 Jul" |
| Cohort filters | Yes | Faculty/cohort dropdowns (Cohort Timetable) |
| Cohort chip | Yes | "RSD3(S1)G2" (Student My Timetable) |
| Grid table | Yes | Full width, expanded event blocks |
| Legend bar | Yes | Normal/Replacement/Pending/Conflict swatches |
| Summary bar | No | Dynamic percentages, not useful on paper |
| Semester progress bar | No | Transient visual indicator |
| Week navigation | No | Useless on paper |
| Today highlight | No | Transient state |

### Event Blocks (Print)
- Expand to show: course code, class type, subject name, venue, time
- Remove hover/focus effects (no outlines, no brightness/scale)
- Blocks may increase height → allow multi-page flow

### Colors
- **Force light background** for print (save ink, readable on paper)
- Override `--color-surface` → white, `--color-on-surface` → dark
- Event block colors remain distinguishable (keep color coding)

## Cross-Module Data Flows

```
ui-week-nav.blade.php
  ├── MyTimetable-UI-design-template.blade.php
  ├── CohortTimetable-UI-design-template.blade.php
  └── student-my-timetable-UI-design-template.blade.php

theme.css (@media print rules)
  ├── .timetable, .event-block, .time-col, .hour-cell
  ├── .legend-bar, .summary-bar (hidden)
  └── .semester-bar, .nav-item (hidden)

ui-common.js
  └── printTimetable() — calls window.print()
```

## Implementation Approach

1. **CSS:** Add `@media print` block to `theme.css` — hide non-essential elements, force light colors, adjust grid layout, expand event blocks
2. **JS:** Add `printTimetable()` function to `ui-common.js` — simple `window.print()` call
3. **Blade:** Add print button to `ui-week-nav.blade.php` partial with printer SVG icon

## Open Questions

None — all design decisions resolved through grilling.

## Rejected Approaches

| Approach | Reason Rejected |
|----------|----------------|
| jsPDF + html2canvas | Adds ~300KB dependency, rasterized output (not vector), worse quality |
| Server-side PDF (DOMPDF/WKHtmlToPdf) | Requires backend logic, not frontend-only, slower, complex setup |
| Single-page forced fit | Cramped unreadable text, better to allow multi-page |
| Dark background print | Wastes toner/ink, poor readability on paper |
| Print summary bar | Dynamic percentages are meaningless on static paper |
