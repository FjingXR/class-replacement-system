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
│  ┌────────────┐ ┌──────────────────┐ ┌──────────────────┐   │
│  │ 🔍 Search  │ │ Filter: Reason ▼ │ │ Showing X of Y   │   │
│  └────────────┘ └──────────────────┘ └──────────────────┘   │
├──────────────────────────────────────────────────────────────┤
│  Table Wrapper (grid-wrapper)                                │
│  ┌────────────────────────────────────────────────────────┐  │
│  │  Header row (sticky)                                   │  │
│  │  Data rows (zebra stripes)                             │  │
│  │  Data rows ...                                         │  │
│  └────────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────────┤
│  Pagination                                                  │
│  ┌─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ─── ───┐   │
│  │  Showing 1-10 of 14       < 1 2 >                   │   │
│  └────────────────────────────────────────────────────────┘  │
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

| # | Column | Width | Sortable | Details |
|---|--------|-------|----------|---------|
| 1 | No. | 50px | No | Auto-increment row number (1-indexed within current page) |
| 2 | Course Code & Name | flex | Yes (by code) | Code: `font-weight:700`, Name: `font-weight:400; opacity:0.7` |
| 3 | Class Type | 90px | No | "Lecture" or "Tutorial" |
| 4 | Date | 110px | Yes (by timestamp) | Format: "DD Mon YYYY" |
| 5 | Day | 80px | No | Full day name |
| 6 | Time | 130px | No | Format: "HH:MM AM/PM - HH:MM AM/PM" |
| 7 | Duration | 70px | No | Format: "X hrs" |
| 8 | Venue | 70px | No | Room code |
| 9 | Total Students | 80px | No | Number |
| 10 | Conflict Reason | 140px | No | Colored badge |
| 11 | Action | 150px | No | "Arrange Replacement" primary button. Click handler: `window.location.href = '/replacement-arrangement'` (mock template) — per frozen proposal, production would pass `?course=X&date=Y` query params |

### 6. Responsive Behavior

At viewport widths below 1024px:
- Table container becomes horizontally scrollable (overflow-x: auto)
- No columns are hidden — all 11 columns remain visible
- Toolbar wraps to 2 rows (search on top, filters on bottom)

At viewport widths below 768px:
- Table scrolls horizontally with sticky first column (No. + Course) for context
- Toolbar stacks vertically

### 7. Filtering & Sorting Data Flow

```
User types in search box
  → onSearchInput() triggers renderTable()
  → filter conflictedClasses by code.toLowerCase().includes(query.toLowerCase()) OR name.toLowerCase().includes(query.toLowerCase())

User selects conflict reason from dropdown
  → onReasonFilterChange() triggers renderTable()
  → filter by reason === selectedValue (or show all if "All Reasons")

User clicks "Date" or "Course Code" header
  → toggleSort(field) toggles asc/desc
  → stored in sortState = { field: 'date'|'code', dir: 'asc'|'desc' }

renderTable():
  1. Start with conflictedClasses
  2. Apply search text filter
  3. Apply reason filter
  4. Apply sort
  5. Compute pagination (offset = (page-1) * pageSize)
  6. Slice data for current page
  7. Build <tr> elements for sliced data (convert 24h timeStart/timeEnd to 12h AM/PM format, compute duration from timeStart/timeEnd difference, map 'L'/'T' to "Lecture"/"Tutorial", apply badge class per conflictReason)
  8. Update pagination controls (showing page range "1-10 of 14") and result count in toolbar ("Filtered: X of Y classes" — where X is filtered count, Y is total unfiltered count)
```

### 8. Empty State

When filtered data is empty:
- Show a centered block below the table area
- Calendar SVG icon in 25% opacity
- Heading: "No classes currently require replacement arrangements."
- Subtext: "Try adjusting your search or filter criteria."
- Hide pagination when empty

### 9. Pagination

- Page size: 10
- Controls: "Showing X-Y of Z" text + prev (`<`) / page numbers / next (`>`) buttons
- At 14 entries: page 1 shows 1-10, page 2 shows 11-14
- Previous button disabled on page 1, next button disabled on last page
- Current page button highlighted

### 10. Nav Bar Integration

- Same HTML structure as MyTimetable page's `.top-bar`
- Active nav item: "Replacement Arrangement" (`class="nav-item active"`)
- Theme toggle: same IIFE + `toggleTheme()`
- The app-container uses `padding-top: 72px` to clear the fixed 56px top-bar + 16px gap

### 11. Route

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
| `changelog.md` | Add entry for new page |
