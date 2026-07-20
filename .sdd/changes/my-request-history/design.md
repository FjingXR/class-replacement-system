# Design: Lecturer — My Request History

## Technical Approach

The page is a **standalone Blade template** with inline `<style>` and `<script>`, following the same pattern as `replacement-home-UI-design-template.blade.php` and `MyTimetable-UI-design-template.blade.php`. All visual tokens (colors, fonts, spacing, border-radius, shadows, transitions) are inherited from the shared `theme.css` and must produce a pixel-consistent appearance with `replacement-home-ui` — same header shape, toolbar layout, table zebra/hover, badge rounding, pagination, summary card layout, modal overlay, and empty state. No backend or database — all data is client-side mock data in a JavaScript array.

The page renders entirely via a JavaScript DOM builder function (`renderTable()`), giving full control over sorting, filtering, and pagination without page reloads.

## Architecture Decisions

### 1. Rendering Strategy: JS DOM Builder vs Static HTML

**Decision:** Use a `renderTable()` function that clears and rebuilds the `<tbody>` and `<thead>` whenever search/filter/sort/page changes.

**Rationale:** Matches the existing Builder pattern in replacement-home; keeps the template self-contained; avoids server round-trips for filtering/sorting; enables instant client-side modal rendering.

### 2. Data Structure

Distribution: 6 Pending, 5 Approved, 4 Rejected, 3 Completed, 2 Cancelled — 15 unique courses (5 courses reused with 2 entries each as Lecture + Tutorial).

```javascript
const mockRequests = [
    {
        id: 1,
        requestedAt: '2026-08-11T12:45:00',   // ISO timestamp for sorting
        courseCode: 'BMSE3153',
        courseName: 'Software Project Management',
        classType: 'L',                         // 'L' | 'T'
        classDate: '2026-08-31',                // ISO date for sorting
        classDay: 'Monday',
        timeStart: '09:00',
        timeEnd: '11:00',
        duration: 2,
        venue: 'B104',
        totalStudents: 20,
        cohorts: ['DSF1', 'DSF2'],
        status: 'Approved',                     // 'Pending' | 'Approved' | 'Rejected' | 'Cancelled' | 'Completed'
        rejectionReason: null,                  // string or null
        replacementDate: '2026-09-07',
        replacementTime: '09:00 – 11:00',
        replacementVenue: 'B105',
        reviewedBy: 'Dr. Ahmad (HOD)',
        reviewedAt: '2026-08-12T15:15:00',
        remarks: ''
    },
    // ... 19 more entries
];
```

### 3. Status → Badge Color Mapping

| Status | CSS Class | Background Token | Text Token |
|--------|-----------|-----------------|------------|
| Pending | `status-pending` | `--color-tertiary-container` | `--color-on-tertiary-container` |
| Approved | `status-approved` | `--color-secondary-container` | `--color-on-secondary-container` |
| Rejected | `status-rejected` | `--color-error-container` | `--color-on-error-container` |
| Cancelled | `status-cancelled` | `--color-surface-variant` | `--color-on-surface-variant` |
| Completed | `status-completed` | `--color-primary-container` | `--color-on-primary-container` |

### 4. Page Layout (top to bottom)

```
┌──────────────────────────────────────────────────────────────┐
│  Top Navigation Bar (fixed, 56px, "Replacement History" active│
├──────────────────────────────────────────────────────────────┤
│  Page Header                                                  │
│  ┌─ My Request History (title) ────────────────────────────┐  │
│  │  "View and monitor all replacement requests submitted   │  │
│  │   during the current semester."                         │  │
│  └─────────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Toolbar                                                     │
│  ┌──────────┐ ┌────────────────────┐ ┌────────────────┐   │
│  │ Status ▼ │ │ Week: Week 1 ▼    │ │ 🔍 Search      │   │
│  │          │ │                   │ │ Showing X of Y │   │
│  └──────────┘ └────────────────────┘ └────────────────┘   │
├──────────────────────────────────────────────────────────────┤
│  Sort hint: "Click column headers to sort (Requested Time,   │
│             Course Code, Date)"                              │
├──────────────────────────────────────────────────────────────┤
│  Grid Wrapper (table with sticky header)                     │
│  ┌────────────────────────────────────────────────────────┐  │
│  │  Header row (12 columns, sticky, z-index:10)           │  │
│  │  Data rows (zebra stripes, hover highlight)            │  │
│  │  ... 10 rows per page                                  │  │
│  └────────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Pagination Bar                                              │
│  ┌─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ───┐   │
│  │  Showing 1–10 of 20           < 1 2 >               │   │
│  └────────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Summary Stat Cards (4 cards, edge-to-edge grid)             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│  │  20      │ │  5       │ │  6       │ │  4       │      │
│  │  TOTAL   │ │ APPROVED │ │ PENDING  │ │ REJECTED │      │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘      │
├──────────────────────────────────────────────────────────────┤
│  Empty State (shown when filters match 0 rows or no data)    │
│  ┌────────────────────────────────────────────────────────┐  │
│  │  📅 (calendar icon)                                    │  │
│  │  "No replacement requests match your search..."        │  │
│  └────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────┘

─── [View Details Modal] ─── (overlay, centered)
┌──────────────────────────────────────────────────────┐
│  Request Details                              [✕]    │
├──────────────────────────────────────────────────────┤
│  (12 fields showing full request information)        │
│                          [Close]                     │
└──────────────────────────────────────────────────────┘
```

### 5. Table Column Specification

State variables:
```javascript
let currentPage = 1;
const pageSize = 10;
let sortState = { field: 'requestedAt', dir: 'asc' };  // default: earliest first
let currentFiltered = [];
```

| # | Column | Width | Sortable | Sort Key | Details |
|---|--------|-------|----------|----------|---------|
| 1 | No. | 50px | No | — | Global sequential (1–20 across all pages) |
| 2 | Requested Time | 110px | Yes | `requestedAt` (ISO) | Format: "DD Mon YYYY HH:MM AM/PM" |
| 3 | Course Code & Name | flex | Yes | `courseCode` | Code bold, name lighter below |
| 4 | Type | 80px | No | — | "Lecture" (`classType: 'L'`) or "Tutorial" (`classType: 'T'`) |
| 5 | Date | 100px | Yes | `classDate` (ISO) | Format: "DD Mon YYYY" |
| 6 | Day | 70px | No | — | Full day name |
| 7 | Time | 130px | No | — | "HH:MM AM/PM – HH:MM AM/PM" |
| 8 | Duration | 70px | No | — | "X.0 hours" |
| 9 | Venue | 70px | No | — | Room code |
| 10 | Total Students | 80px | No | — | Number |
| 11 | Affected Cohort(s) | 110px | No | — | Cohorts separated by `<br>` |
| 12 | Status | 130px | No | — | Color badge + rejection reason subtitle for Rejected |

Sort arrows: When a sortable column header is clicked, render `▲` (ascending) or `▼` (descending) arrow next to the label using a `.sort-arrow` span (font-size:11px, opacity:0.6). Only the currently active sort column shows an arrow.

### 6. Toolbar Specification

The toolbar is a single horizontal bar with two visual groups:

**Group A (left):** Status filter dropdown + Week dropdown — flex row, gap 12px
**Group B (right):** Search input + result count — flex row, gap 12px

| Control | HTML Element | Attributes | Behavior |
|---------|-------------|-----------|----------|
| Status filter | `<select>` | `id="statusFilter"` | Options: All Statuses, Pending, Approved, Rejected, Cancelled, Completed |
| Week | `<select>` | `id="weekFilter"` | Options: All Weeks, Week 1: 31 Aug – 6 Sep, Week 2: 7 Sep – 13 Sep, Week 3: 14 Sep – 20 Sep, Week 4: 21 Sep – 27 Sep; filters by `classDate` within start/end of selected week |
| Search | `<input type="text">` | `id="searchInput" placeholder="Search course..."` | Filters by `courseCode` or `courseName` (`.toLowerCase().includes()`) |
| Result count | `<span>` | `id="resultCount"` | Format: "Showing X of Y results" — X = filtered, Y = total unfiltered |

### 7. Filtering & Sorting Data Flow

```
User types in search box
  → oninput handler triggers renderTable()
  → filter by courseCode.toLowerCase().includes(query) OR courseName.toLowerCase().includes(query)

User selects status from dropdown
  → onchange handler triggers renderTable()
  → filter by status === selectedValue (or show all if "All Statuses")

User selects week from dropdown
  → onchange handler triggers renderTable()
  → filter by classDate between weekStart and weekEnd (or show all if "All Weeks")

User clicks sortable header (Requested Time, Course Code, Date)
  → toggleSort(field) toggles asc/desc
  → stored in sortState = { field: 'requestedAt'|'courseCode'|'classDate', dir: 'asc'|'desc' }

renderTable():
  1. Start with mockRequests
  2. Apply search text filter (course code/name only)
  3. Apply status filter
   4. Apply week filter: `classDate >= weekStart && classDate <= weekEnd` (skip if "All Weeks")
   5. Apply sort (sortState.field + sortState.dir)
   6. After any filter/sort change, set `currentPage = 1`
  7. Compute offset = (currentPage - 1) * pageSize
  8. Slice data for current page
  9. Build <tr> elements (apply transformations: 24h→12h, 'L'/'T'→Lecture/Tutorial, ISO→display date, badge class)
  10. Update pagination controls and result count
```

### 8. View Details Modal

**Trigger:** Click on the Status badge in any table row.

**Structure (same overlay pattern as MyTimetable):**
```html
<div class="modal-overlay" id="modalOverlay" style="display:none">
    <div class="modal" id="detailsModal">
        <div class="modal-header">
            <h3 class="modal-title">Request Details</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- dynamically populated by JS -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>
```

**Modal content (populated by JS `openModal(request)`):**
- Request No. (auto-generated from index)
- Requested Time (formatted)
- Status (badge matching table color)
- Course Code & Course Name
- Class Type
- Cohorts (comma-separated)
- Original class: Date, Day, Time, Duration, Venue, Total Students
- Replacement: Date, Time, Venue (hidden if null)
- Reviewed By, Reviewed At (hidden if null)
- Remarks / Rejection Reason (hidden if null)

**CSS classes:** `.modal-overlay` (fixed, inset:0, rgba black, backdrop-blur), `.modal` (surface bg, rounded, max-width:520px), `.modal-header`, `.modal-body` (padding:20px 24px, grid of key-value rows), `.modal-footer`, `.modal-close` — all consistent with MyTimetable modal pattern.

**Close behavior:** Click ✕, click Close button, click overlay background, or press Escape.

### 9. Summary Stat Cards

Same CSS pattern as replacement-home's `.summary-bar`:

```css
.summary-bar { display:grid; grid-template-columns:repeat(4,1fr); gap:4px; margin-top:20px; }
.summary-card { background:var(--color-surface); border:1px solid var(--color-outline);
                border-radius:var(--radius-md); padding:14px 16px; text-align:center; }
.summary-value { font-size:24px; font-weight:700; }
.summary-label { font-size:12px; font-weight:500; color:var(--color-on-surface-variant);
                 text-transform:uppercase; letter-spacing:0.5px; }
```

Color mapping (counts computed from the **full unfiltered** `mockRequests` array — not from currently filtered/paginated data):
- Total Requests → `--color-error`
- Approved → `--color-secondary`
- Pending → `--color-tertiary`
- Rejected → `--color-on-primary-container`

Only Total, Approved, Pending, and Rejected are shown as cards. Cancelled and Completed entries are counted in Total but do not have their own card.

### 10. Empty State

Two variants, same structure (SVG calendar icon, heading, subtext):

**Fully empty (no data at all):**
- Icon: calendar SVG (same as replacement-home)
- Heading: "You haven't submitted any replacement requests for this semester."
- Subtext: "Submit a replacement request for any conflicted class."
- CTA button: "Submit a Replacement Request" → `window.location.href = '/replacement-arrangement'`

**Filtered empty (data exists but no match):**
- Icon: calendar SVG
- Heading: "No replacement requests match your search or filter criteria."
- Subtext: "Try adjusting your filters."
- No CTA button

### 11. Responsive Behavior

At viewport widths below 1024px:
- Table container becomes horizontally scrollable (`overflow-x: auto`)
- Toolbar wraps — left group stacks above right group

At viewport widths below 768px:
- Toolbar stacks vertically (all controls in one column)
- Filter dropdown and week dropdown take full width
- Search input takes full width
- Summary cards wrap to 2 columns
- Pagination bar stacks vertically (info above controls)

### 12. Sort Hint

Small italic text between toolbar and table:
```html
<div class="sort-hint">Click column headers to sort (Requested Time, Course Code, Date)</div>
```
CSS: right-aligned, 12px, italic, `--color-on-surface-variant`, opacity 0.5.

### 13. Nav Bar Integration

- Same HTML structure as replacement-home page's `.top-bar`
- Active nav item: "Replacement History" with `href="/my-request-history-ui"`
- Theme toggle: same IIFE + `toggleTheme()`
- App container padding: `padding-top: 72px` to clear the fixed 56px top-bar + 16px gap

## Dependencies

- `/css/theme.css` — shared design tokens (already exists)
- Google Fonts: Inter (already used in other templates)
- No npm packages, no build tools, no backend dependencies

## File Changes

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | Create — new template |
| `routes/web.php` | Add `GET /my-request-history-ui` route (closure, same pattern as other UI routes) |
| `page-changelogs/my-request-history-changelog.md` | Create — new per-page changelog |
