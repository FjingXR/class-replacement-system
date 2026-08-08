# Tasks: Venue Timetable UI

## Task 1: Create Route
- [ ] Add `GET /venue-timetable-ui` to `routes/web.php`
- [ ] Return view `ui-design-templates.venue-timetable-UI-design-template` with `['activeNav' => 'venue-timetable']`

## Task 2: Create Blade Template Structure
- [ ] Create `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`
- [ ] `@extends('layouts.ui-template', ['activeNav' => 'venue-timetable'])`
- [ ] `@section('title', 'Venue Timetable — Class Replacement System')`
- [ ] `@section('page-styles')` — page-specific CSS
- [ ] `@section('content')` — HTML structure
- [ ] `@section('page-scripts')` — render logic

## Task 3: Page Header + Semester Chip
- [ ] Page header: "Venue Timetable"
- [ ] Description: "View weekly class schedule for any venue across all cohorts."
- [ ] Semester chip: read from `MockData.semester.chipText`

## Task 4: Venue Dropdown
- [ ] Build dropdown from `MockData.venues` (23 Block B rooms)
- [ ] Default: first venue in list
- [ ] On change: rebuild timetable for selected venue

## Task 5: Week Picker
- [ ] Reuse `.semester-bar` pattern from MyTimetable/CohortTimetable
- [ ] Prev/next arrows + dropdown (14 weeks)
- [ ] Week persistence: `venueTimetableWeek` localStorage key
- [ ] Read week data from `MockData.semester`

## Task 6: Timetable Grid
- [ ] Build Time × Day grid (Mon–Sun, 08:00–18:00)
- [ ] For each cell, check if any class is booked at that venue/time
- [ ] Booked cells: show Course code + Cohort + Status badge
- [ ] Empty cells: green background (Available), clickable
- [ ] Available cells: show tooltip confirmation "Book B014 on Mon, 01 Sep 2026 at 09:00?" with "Book" button
- [ ] Booked cells: click → open modal with class details (no booking button)
- [ ] Keyboard navigation: Arrow keys move between cells, Enter opens modal/tooltip, Escape closes

## Task 7: Legend Bar
- [ ] 4 items: Available, Replacement, Pending, Conflict
- [ ] Available = `--color-secondary` (green)
- [ ] Replacement = `--color-primary` (blue)
- [ ] Pending = `--color-tertiary` (yellow)
- [ ] Conflict = `--color-error` (red)
- [ ] **No "Normal Class"** — booked = occupied

## Task 8: Summary Cards
- [ ] 5 cards via `@include('partials.ui-summary-bar')`
- [ ] Total Classes | Available | Replacement | Pending | Conflict
- [ ] Update counts on venue/week change

## Task 9: Modal
- [ ] View-only modal with field list
- [ ] For booked classes: Course Code, Name, Cohort(s), Time, Day, Date, Status badge, Remarks
- [ ] For available slots: "This slot is available." message + "Book This Venue" button
- [ ] "Book This Venue" button → `window.location.href = '/replacement-arrangement?venue=' + venueCode + '&date=' + date + '&time=' + time`
- [ ] Close button
- [ ] Close on overlay click + ESC key

## Task 10: Nav Bar Update
- [ ] Add 6th nav item: "Venue Timetable" → `/venue-timetable-ui`
- [ ] Key: `venue-timetable`

## Task 11: Mobile View (≤768px)
- [ ] Card layout for booked classes
- [ ] Green cards for available slots, **tappable** → open modal with "Book This Venue" button
- [ ] Stack summary cards vertically
- [ ] Full-width venue dropdown
- [ ] Hide week arrows, full-width dropdown

## Task 12: Tablet View (769px–1024px)
- [ ] Reduce grid density
- [ ] Smaller font for event blocks

## Task 13: Changelog
- [ ] Create `page-changelogs/venue-timetable-ui-changelog.md`
- [ ] Log all file changes with timestamps

## Task 14: Lint Check
- [ ] Run `composer run lint:check`
- [ ] Run `composer run types:check`
- [ ] Confirm no new failures
