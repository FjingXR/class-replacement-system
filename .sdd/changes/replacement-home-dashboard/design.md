# Design: Replacement Arrangement Home Dashboard

## Technical Approach

The page is a **standalone Blade template** with inline `<style>` and `<script>`, following the same pattern as `MyTimetable-UI-design-template.blade.php` and `replacement-arrangement-UIdesign-template.blade.php`. No backend or database — all data is client-side mock data in a JavaScript array.

The page renders entirely via JavaScript DOM construction (like the MyTimetable builder), giving full control over sorting, filtering, and pagination without page reloads.

## Architecture Decisions

### 1. Rendering Strategy: JS DOM Builder vs Static HTML

**Decision:** Use a `renderTable(data)` function that clears and rebuilds the `<tbody>` whenever search/filter/sort/page changes.

**Rationale:** Matches the existing Builder pattern in MyTimetable; keeps the template self-contained; avoids server round-trips for filtering/sorting.

### 2. Data Structure

```javascript
const conflictedClasses = [
    {
        id: 1,
        code: 'BMIT5555',
        name: 'Software Engineering',
        type: 'L',            // 'L' | 'T'
        date: '2026-09-04',   // ISO format for sorting
        day: 'Thursday',
        timeStart: '10:00',
        timeEnd: '12:00',
        duration: 2,          // hours
        venue: 'B110',
        totalStudents: 35,
        cohorts: ['DFT2 (S1)', 'DSF2 (S1)'],  // cohort array
        conflictReason: 'Public Holiday'
    },
    // ... 14 entries
];
```

### 3. Conflict Reason → Badge Color Mapping

| Reason | CSS Class | Theme Token Background | Theme Token Text |
|--------|-----------|----------------------|-------------------|
| Public Holiday | `badge-holiday` | `--color-error-container` | `--color-on-error-container` |
| Annual Leave | `badge-annual-leave` | `--color-primary-container` | `--color-on-primary-container` |
| Medical Leave | `badge-medical-leave` | `--color-tertiary-container` | `--color-on-tertiary-container` |
| Official Event | `badge-official-event` | `--color-secondary-container` | `--color-on-secondary-container` |
| Emergency Leave | `badge-emergency-leave` | `--color-error` | white |

### 4. Page Layout (top to bottom)

```
┌──────────────────────────────────────────────────────────────┐
│  Top Navigation Bar (fixed, 56px, identical to MyTimetable)  │
├──────────────────────────────────────────────────────────────┤
│  Page Header (padded)                                        │
│  ┌─ Replacement Arrangement (title) ──────────────────────┐  │
│  │  "The following classes require replacement arrange-   │  │
│  │   ments. Select a class to submit a replacement        │  │
│  │   request."                                            │  │
│  └────────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Toolbar                                                     │
│  ┌──────────────────────┐ ┌─────────────────────────────┐    │
│  │ 🔍 Search (420px)   │ │ From: [date] To: [date]     │    │
│  │ Week: [Week 1 ▼]    │ │ Showing X of Y              │    │
│  └──────────────────────┘ └─────────────────────────────┘    │
├──────────────────────────────────────────────────────────────┤
│  Sort hint: "Click Date or Course Code & Name to sort"       │
├──────────────────────────────────────────────────────────────┤
│  Table Wrapper (grid-wrapper)                                │
│  ┌────────────────────────────────────────────────────────┐  │
│  │  Header row (sticky, 14 columns)                       │  │
│  │  Data rows (zebra stripes, hover highlight)            │  │
│  │  Data rows ...                                         │  │
│  └────────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Pagination                                                  │
│  ┌─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ───┐   │
│  │  Showing 1-10 of 14       < 1 2 >                   │   │
│  └────────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Summary Stat Cards (edge-to-edge grid, hidden when empty)   │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │  14      │ │  5       │ │  345     │ │  28      │       │
│  │  TOTAL   │ │ VENUES   │ │ STUDENTS │ │ DURATION │       │
│  │  CONFLICT│ │ AFFECTED │ │ AFFECTED │ │ HOURS    │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
├──────────────────────────────────────────────────────────────┤
│  Empty State (shown when filters match 0 rows)               │
│  ┌────────────────────────────────────────────────────────┐  │
│  │  📅 (calendar icon)                                    │  │
│  │  "No classes currently require replacement             │  │
│  │   arrangements."                                       │  │
│  └────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────┘
```

### 5. Table Column Specification

| # | Column | Width | Sortable | Sort Key | Details |
|---|--------|-------|----------|----------|---------|
| 1 | No. | 50px | No | — | Global sequential (1–14 across all pages, not resetting per page) |
| 2 | Course Code & Name | 200px | Yes | `code` | Code: `font-weight:700`, Name: `font-weight:400; opacity:0.7` as block below |
| 3 | Class Type | 90px | No | — | "Lecture" or "Tutorial" |
| 4 | Week | 80px | No | — | "Week N" computed from date relative to semester start (31 Aug 2026) |
| 5 | Date | 110px | Yes | `date` (ISO) | Format: "DD Mon YYYY" |
| 6 | Day | 80px | No | — | Full day name |
| 7 | Days Left | 100px | No | — | Color-coded: ≤7d red (`.urgency-high`), 8–30d teal (`.urgency-mid`), 31+ default (`.urgency-low`) |
| 8 | Time | 130px | No | — | Format: "HH:MM AM/PM - HH:MM AM/PM" via `to12h()` |
| 9 | Duration | 70px | No | — | Format: "Xh" |
| 10 | Venue | 70px | No | — | Room code |
| 11 | Total Students | 80px | No | — | Number |
| 12 | Affected Cohort(s) | 130px | No | — | Cohorts separated by `<br>` |
| 13 | Conflict Reason | 140px | No | — | Colored badge (see §3) |
| 14 | Action | 150px | No | — | "Arrange Replacement" button. Click handler: `goToReplacementWith(code, date)` → `window.location.href = '/replacement-arrangement?course=CODE&date=DATE'` |

Sort arrows: When a sortable column header is clicked, render `▲` (ascending) or `▼` (descending) arrow next to the label using a `.sort-arrow` span (font-size:11px, opacity:0.6). Only the currently active sort column shows an arrow. Default sort is Date ascending (`sortState = { field: 'date', dir: 'asc' }`).

### 6. Responsive Behavior

At viewport widths below 1024px:
- Table container becomes horizontally scrollable (`overflow-x: auto`)
- No columns are hidden — all 14 columns remain visible via horizontal scroll
- Toolbar wraps — left group stacks above right group

At viewport widths below 768px:
- Table scrolls horizontally (no sticky columns)
- Toolbar stacks vertically (all controls in one column)
- Summary cards wrap to 2 columns
- Nav items font-size reduces to 12px, user info hidden

### 7. Filtering & Sorting Data Flow

```
User types in search box
  → oninput handler triggers buildTable()
  → filter conflictedClasses by code.toLowerCase().includes(query) OR name.toLowerCase().includes(query)

User selects a date range (From / To)
  → onchange handler triggers buildTable()
  → filter by classDate >= dateFrom (skip if empty) AND classDate <= dateTo (skip if empty)

User selects a week from dropdown
  → onchange handler triggers buildTable()
  → filter by computeWeek(date) === weekVal (or show all if "All Weeks")

User clicks "Date" or "Course Code" header
  → click handler toggles asc/desc on sortState
  → stored in sortState = { field: 'date'|'code', dir: 'asc'|'desc' }

buildTable():
  1. Start with conflictedClasses
  2. Apply search text filter (course code/name)
  3. Apply date range filter (From/To)
  4. Apply week filter (week number)
  5. Apply sort (sortState.field + sortState.dir) — default: Date asc
  6. After any filter/sort change, set currentPage = 1
  7. Compute offset = (currentPage - 1) * pageSize
  8. Slice data for current page
  9. Build <tr> elements (convert 24h→12h, 'L'/'T'→Lecture/Tutorial, ISO→display date, compute Week/Days Left, apply badge class, cohorts joined with <br>)
  10. Update pagination controls and result count ("Showing X of Y classes")
  11. Update summary cards from filtered dataset
```

### 8. Empty State

When filtered data is empty:
- Show a centered block below the table area
- Calendar SVG icon in 25% opacity
- Heading: "No classes currently require replacement arrangements."
- Subtext: "Try adjusting your search or filter criteria."
- Hide pagination and summary bar when empty

### 9. Summary Stat Cards

Same CSS pattern as the table wrapper:
```css
.summary-bar { display:grid; grid-template-columns:repeat(5,1fr); gap:4px; margin-top:20px; }
.summary-card { background:var(--color-surface); border:1px solid var(--color-outline);
                border-radius:var(--radius-md); padding:14px 12px; text-align:center; }
.summary-value { font-size:24px; font-weight:700; }
.summary-label { font-size:12px; font-weight:500; color:var(--color-on-surface-variant);
                 text-transform:uppercase; letter-spacing:0.5px; }
```

Color mapping (counts computed from the **currently filtered** dataset — recalculated on every filter/sort change):
- Total Conflicted → `--color-error` (`.card-conflicted`)
- Venues Affected → `--color-primary` (`.card-venues`)
- Students Affected → `--color-tertiary` (`.card-students`)
- Duration Hours → `--color-secondary` (`.card-duration`)
- Distinct Courses → `--color-on-primary-container` (`.card-courses`)

Cards are edge-to-edge in a 5-column grid. The entire bar is hidden when empty state is shown.

### 10. Pagination

- Page size: 10
- Controls: "Showing X-Y of Z" text + prev (`<`) / page numbers / next (`>`) buttons
- At 14 entries: page 1 shows 1-10, page 2 shows 11-14
- Previous button disabled on page 1, next button disabled on last page
- Current page button highlighted

### 11. Nav Bar Integration

- Same HTML structure as MyTimetable page's `.top-bar`
- Active nav item: "Replacement Arrangement" (`class="nav-item active"`)
- Theme toggle: same IIFE + `toggleTheme()`
- The app-container uses `padding-top: 72px` to clear the fixed 56px top-bar + 16px gap

### 12. Route

```php
Route::get('/replacement-home-ui', function () {
    return view('ui-design-templates.replacement-home-UI-design-template');
});
```

## Dependencies

- `/css/theme.css` — shared design tokens (already exists)
- Google Fonts: Inter (already used in other templates)
- No npm packages, no build tools, no backend dependencies

## File Changes

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | Create — new template |
| `routes/web.php` | Add `GET /replacement-home-ui` route (closure, same pattern as other UI routes) |
| `page-changelogs/replacement-home-changelog.md` | Create — per-page changelog documenting all implementation batches |
