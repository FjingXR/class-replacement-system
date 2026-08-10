# Proposal: Venue Timetable UI

## Why This Change Is Needed

Lecturers and Programme Leaders currently have no way to view a venue's schedule before booking a replacement class. When submitting a replacement request on the `/replacement-arrangement` page, they must manually check if a venue is free by cross-referencing multiple timetable pages (My Timetable, Cohort Timetables). This wastes time and increases the risk of booking conflicts.

A dedicated **Venue Timetable** page gives staff a quick, view-only overview of any venue's weekly schedule, showing all booked classes across all cohorts. Empty slots are visually distinct (green = available), and clicking any cell shows a tooltip confirmation before redirecting to the booking page with venue, date, and time pre-filled.

## Scope

### In Scope

1. **New Blade template** `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`
   - `@extends('layouts.ui-template', ['activeNav' => 'venue-timetable'])`
   - Page header: "Venue Timetable"
   - Description: "View weekly class schedule for any venue across all cohorts."
   - Semester chip: "202605 Semester · 15-Jun-2026 ~ 20-Sep-2026" (from `MockData.semester`)
   - Venue dropdown: 23 Block B rooms (from `MockData.venues`)
   - Week picker: prev/next arrows + dropdown (14 weeks, from `MockData.semester`)
   - **Skeleton loading:** Use project standard pattern from `skeleton-loading-scroll-restore` SDD change

2. **Legend bar (4 items)**
   - Available — `--color-secondary` (green) — no class booked
   - Replacement — `--color-primary` (blue) — approved replacement
   - Pending — `--color-tertiary` (yellow) — awaiting approval
   - Conflict — `--color-error` (red) — holiday/clash
   - **No "Normal Class"** — if a class is booked, the slot is occupied (not available)

3. **Venue display format**
   - Show venue with type and capacity: `"B014 — Tutorial (35 seats)"`
   - Same format as buildingSelector in replacement-arrangement

3. **Timetable grid** (Time × Day, Mon–Sun)
   - Cells show: Course code + Cohort + Status badge
   - Empty cells = Available (green, clickable)
   - Available cells: show tooltip confirmation "Book B014 on Mon, 01 Sep 2026 at 09:00?" with "Book" button
   - Booked cells: click → open modal with class details (no booking button)
   - Keyboard navigation: Arrow keys move between cells, Enter opens modal/tooltip, Escape closes

4. **Summary cards (5 cards)** via `@include('partials.ui-summary-bar')`
   - Total Classes | Available | Replacement | Pending | Conflict

5. **Modal (view-only)**
   - For booked classes: Course Code, Name, Cohort(s), Time, Day, Date, Status badge, Remarks
   - For available slots: "This slot is available." + "Book This Venue" button
   - "Book This Venue" button → opens `/replacement-arrangement?venue=XXX&date=YYY&time=ZZZ&code=XXX&cohort=XXX`
   - Close button

6. **URL params support**
   - Read `code` and `cohort` from URL (when coming from My Timetable flow)
   - Show banner: "Booking for: BMIT5555 — RSD3G2" when `code`+`cohort` present
   - Pass `code`+`cohort` through when "Book Now" is clicked

6. **Mobile view (≤768px)**
   - Card layout
   - Available slots: tappable → open modal with "Book This Venue" button
   - Same pattern as student-my-timetable mobile design

7. **Nav bar update**
   - Add "Venue Timetable" as 6th nav item
   - `href="/venue-timetable-ui"`, `key="venue-timetable"`

8. **Route** `GET /venue-timetable-ui` in `routes/web.php`

9. **Venue Favourites (A1)**
   - Star icon per venue in dropdown
   - Save to localStorage (`venueFavourites` key)
   - **Backend ready:** Prepared `users.favourites` JSONB column
   - Favourited venues shown at top of dropdown

10. **Booking History (A2)**
    - Tab/toggle: "Current Week" | "Past 4 Weeks"
    - Past 4 weeks: collapsed rows with date + course + status
    - Scrollable container

11. **Time Range Filter (A3)**
    - 3-segment toggle: Morning (08-12) | Afternoon (13-18) | All
    - Default: All
    - AND logic with venue type filter

12. **Venue Type Filter (A4)**
    - Dropdown with checkboxes: Tutorial, Lecture Hall, Lab
    - Default: All checked
    - AND logic with time range filter

13. **Quick Book Shortcut (B3)**
    - Press `B` on focused available cell → tooltip confirmation
    - Visual hint: "(B)" label on focused cells
    - Accessibility: screen reader announcement

14. **Recent Venues Dropdown (B4)**
    - Show last 5 used venues at top of dropdown
    - Storage: `localStorage` (`venueRecent` key)
    - Dropdown sections: "Recent" + "All Venues"

15. **Empty States**
    - No venues match filter → "No venues match criteria" + "Show all venues" button
    - No classes booked → Grid all green, hint text "All slots available"
    - No conflict/cancelled slots → Slot picker hidden, "No slots need replacement" message

16. **Error Handling**
    - MockData load failure → Error banner with refresh button
    - No venue schedule data → "No schedule data for this venue" message
    - Slot already taken → Toast notification + auto-refresh grid

17. **Print Button (UI only)**
    - Printer icon in page header (disabled, tooltip: "Coming soon")
    - OOP approach: shared print function in `ui-common.js` (deferred)

18. **Backend Integration Notes**
    - API endpoints documented (venues, courses, replacements)
    - WebSocket notes for real-time availability
    - Database schema notes (users.favourites, replacements)

19. **Testing Scenarios**
    - 26 test cases covering core functionality, filters, keyboard nav, URL params, empty states, errors, mobile

### Out of Scope

- No booking functionality on this page (booking happens on `/replacement-arrangement`)
- No venue management (add/edit/delete venues)
- No real-time availability updates
- No filter by status (just venue + week selection)
- No search (venue dropdown is sufficient)
- No print functionality (deferred to another session with OOP approach)

### Related Feature: Slot Picker (replacement-arrangement)

When user selects a subject on replacement-arrangement, a **slot picker** shows conflict/cancelled slots only:

- **Trigger:** Subject selected in dropdown
- **Data source:** `MockData.cohortTimetable` (all weeks), filtered by course code + status
- **Filter:** Only show `status === 'conflict'` or `status === 'cancelled'` slots
- **Display:** Week number, day, time, venue, status badge
- **Selection:** Single slot (radio button)
- **After selection:** User picks new venue + time on timetable grid
- **URL params passed:** `code`, `cohort`, `week`, `day`, `time`, `venue` (original slot)

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php` | **Create** — new venue timetable template |
| `routes/web.php` | **Edit** — add `GET /venue-timetable-ui` |
| `resources/views/partials/ui-nav-bar.blade.php` | **Edit** — add 6th nav item |
| `page-changelogs/venue-timetable-ui-changelog.md` | **Create** — new changelog |

## FR Traceability

| FR | Requirement | Page Feature |
|----|-------------|--------------|
| FR 2.2 | Filter by status | Legend bar shows status indicators |
| FR 4.1 | View venue availability | Core purpose of the page |
| FR 4.2 | Check venue schedule | Timetable grid shows all classes |
| FR 4.3 | Book venue for replacement | "Book This Venue" button in modal |
| NFR 3.1 | Responsive design | Mobile card layout |
| NFR 3.3 | Simple English | Clear labels and descriptions |
| FR 5.1 | Filter by time | Time range filter (Morning/Afternoon/All) |
| FR 5.2 | Filter by venue type | Venue type filter (Tutorial/Lecture Hall/Lab) |
| NFR 2.1 | User preferences | Venue favourites + recent venues (localStorage) |
| NFR 4.1 | Keyboard accessibility | Quick book shortcut (press B) |
