# Proposal: Venue Timetable UI

## Why This Change Is Needed

Lecturers and Programme Leaders currently have no way to view a venue's schedule before booking a replacement class. When submitting a replacement request on the `/replacement-arrangement` page, they must manually check if a venue is free by cross-referencing multiple timetable pages (My Timetable, Cohort Timetables). This wastes time and increases the risk of booking conflicts.

A dedicated **Venue Timetable** page gives staff a quick, view-only overview of any venue's weekly schedule, showing all booked classes across all cohorts. Empty slots are visually distinct (green = available), and clicking any cell opens a modal with a "Book This Venue" shortcut that pre-fills the venue and date in the replacement arrangement flow.

## Scope

### In Scope

1. **New Blade template** `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`
   - `@extends('layouts.ui-template', ['activeNav' => 'venue-timetable'])`
   - Page header: "Venue Timetable"
   - Description: "View weekly class schedule for any venue across all cohorts."
   - Semester chip: "202605 Semester · 15-Jun-2026 ~ 20-Sep-2026" (from `MockData.semester`)
   - Venue dropdown: 23 Block B rooms (from `MockData.venues`)
   - Week picker: prev/next arrows + dropdown (14 weeks, from `MockData.semester`)

2. **Legend bar (4 items)**
   - Available — `--color-secondary` (green) — no class booked
   - Replacement — `--color-primary` (blue) — approved replacement
   - Pending — `--color-tertiary` (yellow) — awaiting approval
   - Conflict — `--color-error` (red) — holiday/clash
   - **No "Normal Class"** — if a class is booked, the slot is occupied (not available)

3. **Timetable grid** (Time × Day, Mon–Sun)
   - Cells show: Course code + Cohort + Status badge
   - Empty cells = Available (green, clickable on desktop only)
   - Click any cell (booked or available) → opens modal

4. **Summary cards (5 cards)** via `@include('partials.ui-summary-bar')`
   - Total Classes | Available | Replacement | Pending | Conflict

5. **Modal (view-only + Book button)**
   - For booked classes: Course Code, Name, Cohort(s), Time, Day, Date, Status badge, Remarks
   - "Book This Venue" button → opens `/replacement-arrangement?venue=XXX&date=YYY`
   - For available slots: "This slot is available." + "Book This Venue" button
   - Close button

6. **Mobile view (≤768px)**
   - Card layout (view-only, no "Book This Venue" button)
   - Same pattern as student-my-timetable mobile design

7. **Nav bar update**
   - Add "Venue Timetable" as 6th nav item
   - `href="/venue-timetable-ui"`, `key="venue-timetable"`

8. **Route** `GET /venue-timetable-ui` in `routes/web.php`

### Out of Scope

- No booking functionality on this page (booking happens on `/replacement-arrangement`)
- No venue management (add/edit/delete venues)
- No real-time availability updates
- No filter by status (just venue + week selection)
- No search (venue dropdown is sufficient)

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
