# Proposal: Replacement Arrangement Home Dashboard

## Why This Change Is Needed

Lecturers currently have no single-page overview of all conflicted classes that require replacement arrangements. The existing Replacement Arrangement page (`/replacement-arrangement`) is a slot-selection interface for submitting replacement requests, not a dashboard that surfaces *which* classes need attention. Lecturers must manually cross-reference their timetable to identify conflicts, then navigate elsewhere to arrange replacements. This wastes time and increases the risk of missed replacement deadlines.

A dedicated **Replacement Arrangement Home Page** gives lecturers a searchable, filterable, sortable list of all conflicted classes, with one-click access to start the replacement process for any row.

## Scope

### In Scope

- A new standalone Blade template: `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`
- A new route: `GET /replacement-home-ui` in `routes/web.php`
- Full top navigation bar (identical HTML structure to MyTimetable page) — logo, 5 nav links (Dashboard → `/dashboard`, My Timetable → `/my-timetable-ui`, Cohort Timetables → `#`, Replacement Arrangement → `/replacement-arrangement`, Replacement History → `#`), theme toggle, notification bell, user panel with logout. The **"Replacement Arrangement"** nav item is marked `.active` since this page is the Replacement Arrangement home dashboard. Note: the active link's `href="/replacement-arrangement"` differs from the current page URL (`/replacement-home-ui`) — this is intentional and consistent with how other UI template nav bars work (the nav item represents the feature category, and clicking it navigates to the feature's primary/legacy page). Users reach this dashboard via a direct URL or through the "Arrange Replacement" button flow on other pages.
- Page title & description section
- Toolbar with:
  - Search bar (filters by course code / course name)
  - Conflict reason dropdown filter (All, Public Holiday, Annual Leave, Medical Leave, Official Event, Emergency Leave)
  - Live result count
- Responsive data table with 11 columns:
  1. No.
  2. Course Code & Course Name (code bold, name lighter weight)
  3. Class Type (Lecture / Tutorial)
  4. Date
  5. Day
  6. Time
  7. Duration
  8. Venue
  9. Total Students
  10. Conflict Reason (colored badge per reason)
  11. Action ("Arrange Replacement" button → navigates to `/replacement-arrangement`). In this mock-data template, the navigation is a simple `window.location.href = '/replacement-arrangement'`. In a production backend implementation, this would pass query parameters (e.g., `?course=BMIT5555&date=04-Sep-2026`) so the target page can pre-fill its context.
- Table features:
  - Sticky table header
  - Hover highlight on rows
  - Zebra striping
  - Sort by date (asc/desc) — sorts by the Date column's underlying timestamp
  - Sort by course code (asc/desc) — sorts by the course code portion only (e.g., BMIT1234 < BMIT3456); the course name is display-only and not part of the sort key
  - Pagination: 10 rows per page, page controls
- Empty state when search/filter yields no results
- 14 mock conflicted-class entries covering all 5 conflict reasons
- Conflict reason badge colors using existing theme tokens
- Dark/light theme toggle (same IIFE + `toggleTheme()` pattern as other pages)
- Link to `/css/theme.css` and Inter font from Google Fonts
- `changelog.md` update with new page entry

### Explicitly Out of Scope

- No replacement request submission on this page (submission happens on the existing `/replacement-arrangement` page)
- No backend/database integration — purely client-side mock data
- No CRUD operations on conflicted classes (view-only dashboard)
- No user authentication checks (follows the same mock-data pattern as other UI templates)
- No export/download functionality
- No multi-select or batch actions
- No real-time updates or WebSocket connections

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | **Create** — new standalone dashboard template |
| `routes/web.php` | **Edit** — add `GET /replacement-home-ui` route |
| `changelog.md` | **Edit** — document the new page |

No existing files are modified beyond these three. The new template is self-contained (inline `<style>` and `<script>`), borrowing CSS patterns from `MyTimetable-UI-design-template.blade.php` for the header but otherwise independent.
