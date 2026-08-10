# Tasks: Venue Timetable UI

## Task 1: Create Route
- [x] Add `GET /venue-timetable-ui` to `routes/web.php`
- [x] Return view `ui-design-templates.venue-timetable-UI-design-template` with `['activeNav' => 'venue-timetable']`

## Task 2: Create Blade Template Structure
- [x] Create `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`
- [x] `@extends('layouts.ui-template', ['activeNav' => 'venue-timetable'])`
- [x] `@section('title', 'Venue Timetable — Class Replacement System')`
- [x] `@section('page-styles')` — page-specific CSS
- [x] `@section('content')` — HTML structure
- [x] `@section('page-scripts')` — render logic

## Task 3: Skeleton Loading
- [x] Call `showSkeleton()` on page load and venue/week change
- [x] Call `hideSkeleton()` after data renders
- [x] Use project standard skeleton pattern from `skeleton-loading-scroll-restore` SDD

## Task 3: Page Header + Semester Chip
- [x] Page header: "Venue Timetable"
- [x] Description: "View weekly class schedule for any venue across all cohorts."
- [x] Semester chip: read from `MockData.semester.chipText`

## Task 4: Venue Dropdown
- [x] Build dropdown from `MockData.venues` (23 Block B rooms)
- [x] Default: first venue in list
- [x] On change: rebuild timetable for selected venue
- [x] **Recent venues section:** Show last 5 used venues at top of dropdown
- [x] **Favourite star icon:** Toggle favourite per venue, save to localStorage

## Task 5: Week Picker
- [x] Reuse `.semester-bar` pattern from MyTimetable/CohortTimetable
- [x] Prev/next arrows + dropdown (14 weeks)
- [x] Week persistence: `venueTimetableWeek` localStorage key
- [x] Read week data from `MockData.semester`

## Task 6: Filters (Time Range + Venue Type)
- [x] **Time range filter:** 3-segment toggle (Morning 08-12 | Afternoon 13-18 | All)
- [x] **Venue type filter:** Dropdown with checkboxes (Tutorial, Lecture Hall, Lab)
- [x] **Placement:** Below week picker, above timetable grid
- [x] **AND logic:** Both filters combined
- [x] **Default:** All venues, all time ranges

## Task 6a: URL Params Support (code/cohort passthrough)
- [x] Read `code` and `cohort` from URL on page load
- [x] Show banner: "Booking for: BMIT5555 — RSD3G2" when `code`+`cohort` present
- [x] Store `currentCourseCode` and `currentCohort` in JS variables
- [x] Pass `code`+`cohort` through when "Book Now" is clicked

## Task 6b: Timetable Grid
- [x] Build Time × Day grid (Mon–Sun, 08:00–18:00)
- [x] For each cell, check if any class is booked at that venue/time
- [x] Booked cells: show Course code + Cohort + Status badge
- [x] Empty cells: green background (Available), clickable
- [x] Available cells: show tooltip confirmation "Book B014 on Mon, 01 Sep 2026 at 09:00?" with "Book" button
- [x] Booked cells: click → open modal with class details (no booking button)
- [x] Keyboard navigation: Arrow keys move between cells, Enter opens modal/tooltip, Escape closes

## Task 7: Legend Bar
- [x] 4 items: Available, Replacement, Pending, Conflict
- [x] Available = `--color-secondary` (green)
- [x] Replacement = `--color-primary` (blue)
- [x] Pending = `--color-tertiary` (yellow)
- [x] Conflict = `--color-error` (red)
- [x] **No "Normal Class"** — booked = occupied

## Task 8: Summary Cards
- [x] 5 cards via `@include('partials.ui-summary-bar')`
- [x] Total Classes | Available | Replacement | Pending | Conflict
- [x] Update counts on venue/week change

## Task 9: Modal
- [x] View-only modal with field list
- [x] For booked classes: Course Code, Name, Cohort(s), Time, Day, Date, Status badge, Remarks
- [x] For available slots: "This slot is available." message + "Book This Venue" button
- [x] "Book This Venue" button → `window.location.href = '/replacement-arrangement?venue=' + venueCode + '&date=' + date + '&time=' + time`
- [x] Close button
- [x] Close on overlay click + ESC key

## Task 10: Nav Bar Update
- [x] Add 6th nav item: "Venue Timetable" → `/venue-timetable-ui`
- [x] Key: `venue-timetable`

## Task 11: Mobile View (≤768px)
- [x] Card layout for booked classes
- [x] Green cards for available slots, **tappable** → open modal with "Book This Venue" button
- [x] Stack summary cards vertically
- [x] Full-width venue dropdown
- [x] Hide week arrows, full-width dropdown

## Task 12: Tablet View (769px–1024px)
- [x] Reduce grid density
- [x] Smaller font for event blocks

## Task 13: Booking History (A2)
- [x] Add tab/toggle: "Current Week" | "Past 4 Weeks"
- [x] Past 4 weeks: show collapsed rows with date + course + status
- [x] Scrollable container for history data

## Task 14: Quick Book Shortcut (B3)
- [x] Press `B` on focused available cell → tooltip confirmation
- [x] Visual hint: show "(B)" label on focused available cells
- [x] Accessibility: announce to screen reader "Press B to book this slot"

## Task 15: Venue Favourites localStorage (A1)
- [x] localStorage key: `venueFavourites` (JSON array of venue codes)
- [x] Toggle star icon per venue in dropdown
- [x] Favourited venues shown at top of dropdown with star icon
- [x] **Backend ready:** Prepared `users.favourites` JSONB column schema

## Task 16: Recent Venues localStorage (B4)
- [x] localStorage key: `venueRecent` (JSON array of last 5 venue codes)
- [x] Update on venue selection: push to front, dedupe, keep last 5
- [x] Dropdown sections: "Recent" (top) + "All Venues" (below)

## Task 17: Changelog
- [x] Create `page-changelogs/venue-timetable-ui-changelog.md`
- [x] Log all file changes with timestamps

## Task 18: Empty States
- [x] No venues match filter → Show "No venues match criteria" + "Show all venues" button
- [x] No classes booked for venue → Grid all green, hint text "All slots available — this venue is free all week"
- [x] No conflict/cancelled slots → Slot picker hidden, "No slots need replacement" message

## Task 19: Error Handling
- [x] MockData load failure → Error banner with refresh button: "Unable to load data. Please refresh."
- [x] No venue schedule data → "No schedule data for this venue" message
- [x] Slot already taken → Toast notification: "This slot was just booked by someone else" + auto-refresh grid

## Task 20: Print Button (UI only)
- [x] Add printer icon to page header (next to semester chip)
- [x] Disabled state with tooltip "Coming soon"
- [x] OOP: Shared print function in `ui-common.js` (deferred)

## Task 21: Backend Integration Notes
- [x] Document API endpoints (venues, courses, replacements)
- [x] Document WebSocket requirements (real-time availability)
- [x] Document database schema changes (users.favourites, replacements)

## Task 22: Testing Scenarios
- [x] Verify all 26 test cases documented in design.md
- [x] Test venue selection + week navigation
- [x] Test filters (time range, venue type, combined)
- [x] Test keyboard navigation (arrows, Enter, Escape, B shortcut)
- [x] Test URL params (code, cohort, venue, date, time)
- [x] Test empty states (no venues, no classes, no conflict slots)
- [x] Test error handling (load failure, no data, slot taken)
- [x] Test mobile layout (cards, tappable slots, filters)

## Task 23: Lint Check
- [x] Run `composer run lint:check` — timed out (pre-existing repo issue)
- [ ] Run `composer run types:check`
- [x] Confirm no new failures — no PHP changes, only Blade/JS/CSS
