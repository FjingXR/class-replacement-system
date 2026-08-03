# Proposal: Lecturer — My Request History

## Why This Change Is Needed

Lecturers currently have no way to review their previously submitted replacement requests in a single view. The existing system focuses on submitting new requests (Replacement Arrangement page) and viewing conflicted classes (Replacement Home Dashboard), but once a request is submitted, there is no dedicated page to track its approval progress. Lecturers must rely on memory or manual record-keeping to know whether their requests were approved, rejected, or still pending.

A dedicated **My Request History** page gives lecturers a searchable, filterable, sortable list of all replacement requests they have submitted during the current semester, with clear status indicators and quick access to full request details.

## Scope

### In Scope

- A new standalone Blade template: `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`
- A new route in `routes/web.php`: `Route::get('/my-request-history-ui', fn() => view('ui-design-templates.my-request-history-UI-design-template'))` — trivial closure returning a static view, same pattern as existing UI template routes
- Full top navigation bar (identical HTML structure to existing pages) — logo, nav links (Dashboard → `/dashboard`, My Timetable → `/my-timetable-ui`, Cohort Timetables → `#`, Replacement Arrangement → `/replacement-arrangement`, Replacement History → `/my-request-history-ui`), theme toggle, notification bell, user panel with logout. The **"Replacement History"** nav item is marked `.active` since this page is the request history feature.
- Page title "My Request History" with description: "View and monitor all replacement requests submitted during the current semester."
- Toolbar with:
  - **Filter by Status** dropdown (All Statuses, Pending, Approved, Rejected, Cancelled, Completed)
  - **Week Dropdown** (All Weeks, Week 1: 31 Aug – 6 Sep, Week 2: 7 Sep – 13 Sep, Week 3: 14 Sep – 20 Sep, Week 4: 21 Sep – 27 Sep — filters by `classDate` falling within the selected week range)
  - **Search input** (filters by course code or course name only — not by status, venue, or other fields)
  - **Live result count** — placed at the right side of the toolbar, format: "Showing X of Y results"
- Small italic hint text between toolbar and table: "Click column headers to sort (Requested Time, Course Code, Date)" — explicitly lists the 3 sortable columns
- Responsive data table with **12 columns**:
  1. No. (sequential row number — global sequence across all pages, e.g. rows 1–20, not resetting per page)
  2. Requested Time (date + time the request was submitted)
  3. Course Code & Course Name (code bold, name lighter weight below)
  4. Type (Lecture / Tutorial)
  5. Date (original class date)
  6. Day (day of week)
  7. Time (class start–end time)
  8. Duration (class length in hours)
  9. Venue (room code)
  10. Total Students (number)
  11. Affected Cohort(s) (cohort codes separated by line breaks)
  12. Status (color-coded badge with optional inline rejection reason — for Rejected entries, displayed as a muted subtitle below the badge text)
- Table features:
  - Sticky table header
  - Hover highlight on rows
  - Zebra striping
  - Sortable columns (only 3): Requested Time (asc/desc), Course Code (asc/desc), Date (asc/desc) — all other columns are not sortable
  - Arrow indicator on active sort column
  - Default sort: Requested Time (ascending — earliest first)
  - Pagination: 10 rows per page, page controls. Changing filters, search query, or sort column resets pagination to page 1.
- **Status badges** using existing theme tokens:
  | Status | CSS Class | Background Token | Text Token |
  |--------|-----------|-----------------|------------|
  | Pending | `status-pending` | `--color-tertiary-container` | `--color-on-tertiary-container` |
  | Approved | `status-approved` | `--color-secondary-container` | `--color-on-secondary-container` |
  | Rejected | `status-rejected` | `--color-error-container` | `--color-on-error-container` |
  | Cancelled | `status-cancelled` | `--color-surface-variant` | `--color-on-surface-variant` |
  | Completed | `status-completed` | `--color-primary-container` | `--color-on-primary-container` |
- **View Details modal** — clicking a Status badge opens a popup modal (same overlay/close pattern as MyTimetable's class detail modal) showing:
  - Request No. (auto-generated ID)
  - Requested Time
  - Status badge
  - Course Code & Name
  - Class Type
  - Cohort(s)
  - Original Date, Day, Time, Duration, Venue, Total Students
  - Replacement Date, Time, Venue (if applicable — shown only for Approved/Completed)
  - Reviewed By & Reviewed At (if applicable — null for Pending and Cancelled, populated for Approved/Rejected/Completed)
  - Remarks / Rejection Reason (if applicable — null for Approved/Completed with no remarks)
  - Close button
- Summary stat cards below the table — 4 cards styled identically to the replacement-home summary bar (same CSS classes, token mapping, and layout pattern). Card counts reflect the **unfiltered full dataset** (not the currently filtered results):
  - **Total Requests** (total count — `--color-error` value color)
  - **Approved** (count of approved — `--color-secondary` value color)
  - **Pending** (count of pending — `--color-tertiary` value color)
  - **Rejected** (count of rejected — `--color-on-primary-container` value color)
- Empty state:
  - When the page has no requests at all (no data): calendar SVG icon + "You haven't submitted any replacement requests for this semester." + secondary CTA "Submit a Replacement Request"
  - When filters/search produce zero results from existing data: calendar SVG icon + "No replacement requests match your search or filter criteria." + "Try adjusting your filters."
- 20 mock request entries covering all 5 status types:
  - 6 Pending, 5 Approved, 4 Rejected, 3 Completed, 2 Cancelled
  - 15 unique courses (5 courses reused with 2 entries each — Lecture + Tutorial)
  - Modal field convention per status:
    - **Pending:** reviewedBy = null, reviewedAt = null, remarks = null, replacementDate = null, replacementTime = null, replacementVenue = null
    - **Approved:** reviewedBy populated, reviewedAt populated, remarks may be null or populated, replacementDate/time/venue populated
    - **Rejected:** reviewedBy populated, reviewedAt populated, remarks = rejection reason string, replacementDate/time/venue = null
    - **Cancelled:** reviewedBy = null, reviewedAt = null, remarks may be null or populated, replacement = null
    - **Completed:** reviewedBy populated, reviewedAt populated, remarks may be null, replacement populated
- Dark/light theme toggle (same IIFE pattern as other pages)
- Link to `/css/theme.css` and Inter font from Google Fonts
- `page-changelogs/my-request-history-changelog.md` — create new per-page changelog

### Explicitly Out of Scope

- No request submission on this page (submission happens on `/replacement-arrangement`)
- No database persistence, API endpoints, or server-side business logic — the only backend change is the route returning a static view (same pattern as existing UI templates)
- No CRUD operations (view-only history page)
- No user authentication checks (follows same mock-data pattern as other UI templates)
- No export/download functionality
- No multi-select or batch actions
- No real-time updates or WebSocket connections
- No "filter by course" dropdown (replaced by week filter + search)
- No separate Request ID column (uses sequential No. instead)

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | **Create** — new standalone request history template |
| `routes/web.php` | **Edit** — add `GET /my-request-history-ui` returning `ui-design-templates.my-request-history-UI-design-template` |
| `page-changelogs/my-request-history-changelog.md` | **Create** — new per-page changelog |

No existing files are modified beyond these. The new template is self-contained (inline `<style>` and `<script>`), borrowing CSS patterns from existing templates.
