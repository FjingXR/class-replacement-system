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

## Task 3: Skeleton Loading
- [ ] Call `showSkeleton()` on page load and venue/week change
- [ ] Call `hideSkeleton()` after data renders
- [ ] Use project standard skeleton pattern from `skeleton-loading-scroll-restore` SDD

## Task 3: Page Header + Semester Chip
- [ ] Page header: "Venue Timetable"
- [ ] Description: "View weekly class schedule for any venue across all cohorts."
- [ ] Semester chip: read from `MockData.semester.chipText`

## Task 4: Venue Dropdown
- [ ] Build dropdown from `MockData.venues` (23 Block B rooms)
- [ ] Default: first venue in list
- [ ] On change: rebuild timetable for selected venue
- [ ] **Recent venues section:** Show last 5 used venues at top of dropdown
- [ ] **Favourite star icon:** Toggle favourite per venue, save to localStorage

## Task 5: Week Picker
- [ ] Reuse `.semester-bar` pattern from MyTimetable/CohortTimetable
- [ ] Prev/next arrows + dropdown (14 weeks)
- [ ] Week persistence: `venueTimetableWeek` localStorage key
- [ ] Read week data from `MockData.semester`

## Task 6: Filters (Time Range + Venue Type)
- [ ] **Time range filter:** 3-segment toggle (Morning 08-12 | Afternoon 13-18 | All)
- [ ] **Venue type filter:** Dropdown with checkboxes (Tutorial, Lecture Hall, Lab)
- [ ] **Placement:** Below week picker, above timetable grid
- [ ] **AND logic:** Both filters combined
- [ ] **Default:** All venues, all time ranges

## Task 6a: URL Params Support (code/cohort passthrough)
- [ ] Read `code` and `cohort` from URL on page load
- [ ] Show banner: "Booking for: BMIT5555 — RSD3G2" when `code`+`cohort` present
- [ ] Store `currentCourseCode` and `currentCohort` in JS variables
- [ ] Pass `code`+`cohort` through when "Book Now" is clicked

## Task 6b: Timetable Grid
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

## Task 13: Booking History (A2)
- [ ] Add tab/toggle: "Current Week" | "Past 4 Weeks"
- [ ] Past 4 weeks: show collapsed rows with date + course + status
- [ ] Scrollable container for history data

## Task 14: Quick Book Shortcut (B3)
- [ ] Press `B` on focused available cell → tooltip confirmation
- [ ] Visual hint: show "(B)" label on focused available cells
- [ ] Accessibility: announce to screen reader "Press B to book this slot"

## Task 15: Venue Favourites localStorage (A1)
- [ ] localStorage key: `venueFavourites` (JSON array of venue codes)
- [ ] Toggle star icon per venue in dropdown
- [ ] Favourited venues shown at top of dropdown with star icon
- [ ] **Backend ready:** Prepared `users.favourites` JSONB column schema

## Task 16: Recent Venues localStorage (B4)
- [ ] localStorage key: `venueRecent` (JSON array of last 5 venue codes)
- [ ] Update on venue selection: push to front, dedupe, keep last 5
- [ ] Dropdown sections: "Recent" (top) + "All Venues" (below)

## Task 17: Changelog
- [ ] Create `page-changelogs/venue-timetable-ui-changelog.md`
- [ ] Log all file changes with timestamps

## Task 18: Empty States
- [ ] No venues match filter → Show "No venues match criteria" + "Show all venues" button
- [ ] No classes booked for venue → Grid all green, hint text "All slots available — this venue is free all week"
- [ ] No conflict/cancelled slots → Slot picker hidden, "No slots need replacement" message

## Task 19: Error Handling
- [ ] MockData load failure → Error banner with refresh button: "Unable to load data. Please refresh."
- [ ] No venue schedule data → "No schedule data for this venue" message
- [ ] Slot already taken → Toast notification: "This slot was just booked by someone else" + auto-refresh grid

## Task 20: Print Button (UI only)
- [ ] Add printer icon to page header (next to semester chip)
- [ ] Disabled state with tooltip "Coming soon"
- [ ] OOP: Shared print function in `ui-common.js` (deferred)

## Task 21: Backend Integration Notes
- [ ] Document API endpoints (venues, courses, replacements)
- [ ] Document WebSocket requirements (real-time availability)
- [ ] Document database schema changes (users.favourites, replacements)

## Task 22: Testing Scenarios
- [ ] Verify all 26 test cases documented in design.md
- [ ] Test venue selection + week navigation
- [ ] Test filters (time range, venue type, combined)
- [ ] Test keyboard navigation (arrows, Enter, Escape, B shortcut)
- [ ] Test URL params (code, cohort, venue, date, time)
- [ ] Test empty states (no venues, no classes, no conflict slots)
- [ ] Test error handling (load failure, no data, slot taken)
- [ ] Test mobile layout (cards, tappable slots, filters)

## Task 23: Lint Check
- [ ] Run `composer run lint:check`
- [ ] Run `composer run types:check`
- [ ] Confirm no new failures
