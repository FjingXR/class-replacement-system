# Proposal: Replacement Arrangement Home Dashboard

## Why This Change Is Needed

Lecturers currently have no single-page overview of all conflicted classes that require replacement arrangements. The existing Replacement Arrangement page (`/replacement-arrangement`) is a slot-selection interface for submitting replacement requests, not a dashboard that surfaces *which* classes need attention. Lecturers must manually cross-reference their timetable to identify conflicts, then navigate elsewhere to arrange replacements. This wastes time and increases the risk of missed replacement deadlines.

A dedicated **Replacement Arrangement Home Page** gives lecturers a searchable, filterable, sortable list of all conflicted classes, with one-click access to start the replacement process for any row.

## Scope

### In Scope

- A new standalone Blade template: `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`
- A new route: `GET /replacement-home-ui` in `routes/web.php`
- Full top navigation bar (identical HTML structure to MyTimetable page) — logo, 5 nav links (Dashboard → `/dashboard`, My Timetable → `/my-timetable-ui`, Cohort Timetables → `#`, Replacement Arrangement → `/replacement-arrangement`, Replacement History → `/my-request-history-ui`), theme toggle, notification bell, user panel with logout. The **"Replacement Arrangement"** nav item is marked `.active` since this page is the Replacement Arrangement home dashboard. Note: the active link's `href="/replacement-arrangement"` differs from the current page URL (`/replacement-home-ui`) — this is intentional and consistent with how other UI template nav bars work (the nav item represents the feature category, and clicking it navigates to the feature's primary/legacy page). Users reach this dashboard via a direct URL or through the "Arrange Replacement" button flow on other pages.
- Page title & description section
- Toolbar with:
  - **Search bar** (filters by course code / course name — widened to 420px)
  - **Date range filter** (From / To date inputs for filtering by class date)
  - **Week dropdown** (dynamically populated from mock data, filters by semester week number)
  - **Live result count** — format: "Showing X of Y classes", placed at right side of toolbar
- Small italic **sort hint** text between toolbar and table: "Click **Date** or **Course Code & Name** to sort"
- Responsive data table with **14 columns**:
  1. No.
  2. Course Code & Course Name (code bold, name lighter weight below)
  3. Class Type (Lecture / Tutorial)
  4. Week (computed from class date relative to semester start 31 Aug 2026)
  5. Date
  6. Day
  7. Days Left (color-coded urgency: ≤7d red, 8–30d teal, 31+ default)
  8. Time
  9. Duration
  10. Venue
  11. Total Students
  12. Affected Cohort(s) (cohort codes separated by line breaks)
  13. Conflict Reason (colored badge per reason)
  14. Action ("Arrange Replacement" button → navigates to `/replacement-arrangement?course=CODE&date=DATE` passing query parameters so the target page can pre-fill its context)
- Table features:
  - Sticky table header
  - Hover highlight on rows
  - Zebra striping
  - Sortable columns: **Date** (asc/desc, default asc) and **Course Code** (asc/desc) — arrow indicator shows on active sort column; default sort is Date ascending (earliest first)
  - Pagination: 10 rows per page, page controls. Changing search query or filter resets pagination to page 1.
- **Summary dashboard** — 5 stat cards below the table, styled identically to the table area (same surface background, border, radius, shadow). Card counts reflect the **currently filtered dataset** (not unfiltered total), and the entire bar is hidden when the empty state is shown:
  - **Total Conflicted** (count — `--color-error` value color)
  - **Venues Affected** (unique venues — `--color-primary`)
  - **Students Affected** (sum of students — `--color-tertiary`)
  - **Duration Hours** (sum of hours — `--color-secondary`)
  - **Distinct Courses** (unique course codes — `--color-on-primary-container`)
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
- No conflict reason filter dropdown (removed during implementation — the search bar alone is sufficient for filtering; commented out in the HTML to preserve the pattern for future use)

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | **Create** — new standalone dashboard template |
| `routes/web.php` | **Edit** — add `GET /replacement-home-ui` route |
| `page-changelogs/replacement-home-changelog.md` | **Create** — new per-page changelog documenting all implementation batches and refinements |

No existing files are modified beyond these three. The new template is self-contained (inline `<style>` and `<script>`), borrowing CSS patterns from `MyTimetable-UI-design-template.blade.php` for the header but otherwise independent.
