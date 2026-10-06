# Tasks: Timetable Print

## Task 1: Add `.print-btn` screen styles to theme.css
- [ ] Add `.print-btn` CSS block after existing `.today-btn` styles (line ~1199)
- [ ] Include: sizing (32x32), border, border-radius, background, color, cursor, transitions
- [ ] Include hover, active, and disabled states
- **Files:** `public/css/theme.css`
- **Estimate:** 10 min

## Task 2: Add `@media print` block to theme.css
- [ ] Add `@page { size: landscape; margin: 10mm; }` rule
- [ ] Add CSS custom property overrides for light theme (`--color-surface`, `--color-on-surface`, etc.)
- [ ] Add hidden elements list (`.top-bar`, `.semester-bar`, `.summary-bar`, `.semester-progress`, `.print-btn`, etc.)
- [ ] Add grid layout rules (`.grid-wrapper`, `.grid-scroll`, `.timetable` overflow/width)
- [ ] Remove sticky positioning (`.time-header-col`, `.time-col`)
- [ ] Remove hover/focus effects on `.event-block`
- [ ] Remove today highlight (`.today-cell`, `.today`)
- [ ] Add event block expansion rules (font-size, padding, height, overflow, white-space)
- [ ] Add `.event-block::after` content using `attr(data-venue)` and `attr(data-time)`
- **Files:** `public/css/theme.css`
- **Estimate:** 20 min

## Task 3: Add `printTimetable()` to ui-common.js
- [ ] Add `printTimetable()` function after `initScrollRestore()` (line ~489)
- [ ] Function body: `window.print();`
- **Files:** `public/js/ui-common.js`
- **Estimate:** 5 min

## Task 4: Add print button to ui-week-nav.blade.php
- [ ] Add `$showPrintBtn ?? false` parameter to partial
- [ ] Add print button HTML after the next arrow, before the week selector dropdown
- [ ] Button: `.print-btn` class, `onclick="printTimetable()"`, printer SVG icon
- [ ] Add `@if($disabled ?? false) disabled @endif` for disabled state
- **Files:** `resources/views/partials/ui-week-nav.blade.php`
- **Estimate:** 10 min

## Task 5: Wire `showPrintBtn: true` in MyTimetable template
- [ ] Add `'showPrintBtn' => true` to the `@include('partials.ui-week-nav', ...)` call (line ~142)
- **Files:** `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`
- **Estimate:** 5 min

## Task 6: Wire `showPrintBtn: true` in CohortTimetable template
- [ ] Add `'showPrintBtn' => true` to the `@include('partials.ui-week-nav', ...)` call (line ~49)
- **Files:** `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`
- **Estimate:** 5 min

## Task 7: Wire `showPrintBtn: true` in StudentMyTimetable template
- [ ] Add `'showPrintBtn' => true` to the `@include('partials.ui-week-nav', ...)` call (line ~27)
- **Files:** `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`
- **Estimate:** 5 min

## Task 8: Add `data-time` attribute to MyTimetable event blocks
- [ ] Add `div.dataset.time = startStr + ' – ' + endStr;` to `buildTimetable()` (line ~440)
- [ ] Verify existing `div.dataset.name` and `div.dataset.venue` are present
- **Files:** `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`
- **Estimate:** 5 min

## Task 9: Add data attributes to CohortTimetable event blocks
- [ ] Add `div.dataset.name = e.name || '';`
- [ ] Add `div.dataset.venue = e.venue || '';`
- [ ] Add `div.dataset.time = startStr + ' – ' + endStr;`
- [ ] These fix the existing hover tooltip bug (theme.css line 1226)
- **Files:** `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`
- **Estimate:** 10 min

## Task 10: Add `data-time` attribute to StudentMyTimetable event blocks
- [ ] Add `div.dataset.time = startStr + ' – ' + endStr;` to `buildTimetable()` (line ~313)
- [ ] Verify existing `div.dataset.name` and `div.dataset.venue` are present
- **Files:** `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`
- **Estimate:** 5 min

## Task 11: Verify and test
- [ ] Test print button appears on My Timetable page
- [ ] Test print button appears on Cohort Timetable page (after cohort selected)
- [ ] Test print button appears on Student My Timetable page
- [ ] Test print button does NOT appear on non-timetable pages (Replacement Home, Request Approval, etc.)
- [ ] Test print button is disabled when week nav is disabled (Cohort Timetable before cohort selection)
- [ ] Test browser print dialog opens on click
- [ ] Test print output is landscape orientation
- [ ] Test print output has light background (even when screen is dark mode)
- [ ] Test event blocks show code, name, venue, time in print output
- [ ] Test legend bar appears in print output
- [ ] Test summary bar does NOT appear in print output
- [ ] Test semester progress bar does NOT appear in print output
- [ ] Test week navigation does NOT appear in print output
- [ ] Test today highlight does NOT appear in print output
- [ ] Test multi-page print (if grid exceeds one page) — day labels visible on each page
- **Files:** All modified files
- **Estimate:** 20 min

---

**Total estimated time:** ~110 min (~1.8 hours)
