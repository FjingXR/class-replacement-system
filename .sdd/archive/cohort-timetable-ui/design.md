# Design: Cohort Timetable UI

## Technical Approach

Frontend-only Blade template extending `layouts.ui-template`, with hardcoded mock data (matching the pattern established by `MyTimetable-UI-design-template.blade.php`). No backend, no Livewire, no database.

## Architecture Decisions

### 1. Template Structure

```blade
@extends('layouts.ui-template', ['activeNav' => 'cohort-timetables'])

@section('title', 'Cohort Timetable — Class Replacement System')

@section('page-styles')
    /* Page-specific CSS: semester bar, cohort dropdowns, search toolbar, day column widths */
    /* All shared CSS (nav, grid, table, pagination, summary, empty state) loads from theme.css */
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Cohort Timetable</h1>
        <p class="page-desc">View the weekly timetable for any cohort across all faculties.</p>
    </div>

    {{-- Faculty + Cohort cascading dropdowns --}}
    <div class="semester-bar">
        <select id="facultySelect" onchange="onFacultyChange()">...</select>
        <select id="cohortSelect" onchange="onCohortChange()">...</select>
        <button class="week-arrow" onclick="prevWeek()">‹</button>
        <select class="week-select" id="weekSelect" onchange="selectWeek(this.value)"></select>
        <button class="week-arrow" onclick="nextWeek()">›</button>
        <span class="session-text">202605 Semester</span>
    </div>

    {{-- Toolbar with search + status filter --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <div class="search-wrapper">
                <svg class="search-icon">...</svg>
                <input class="search-input" placeholder="Search by course code, name or lecturer...">
            </div>
            <select class="filter-select" id="statusFilter">...</select>
        </div>
        <div class="toolbar-right">
            <span class="result-count">Showing N of N events</span>
        </div>
    </div>

    {{-- Timetable grid --}}
    <div class="grid-wrapper">
        <div class="grid-scroll">
            <table class="timetable" id="timetable">
                <thead id="tableHead"></thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>

    {{-- Summary cards --}}
    @include('partials.ui-summary-bar', ['cards' => [
        ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes'],
        ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements'],
        ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending'],
        ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts'],
    ]])

    {{-- Legend bar --}}
    <div class="legend-bar">...</div>

    {{-- Empty state (hidden by default) --}}
    <div class="empty-state" id="emptyState" style="display:none">...</div>
@endsection

@section('page-scripts')
    // Mock data: weekData, facultyData (faculty → cohorts → events)
    // Build timetable grid from current selection
    // Search/filter logic
    // Week navigation with updateWeekArrows()
    // Faculty/cohort switching
    // Summary card updates
@endsection
```

### 2. Data Model

Three data structures in the page's JS:

```javascript
// weekData — identical to MyTimetable's weekData (shared between both pages)
const weekData = [
    { label: 'Week 9', range: '18 Aug – 24 Aug 2026', days: [...] },
    { label: 'Week 10', range: '25 Aug – 31 Aug 2026', days: [...] },
    { label: 'Week 11', range: '01 Sep – 07 Sep 2026', days: [...] },
];

// facultyData — maps faculty → cohorts → week → events
const facultyData = {
    'FOCS': {
        label: 'Faculty of Computing and Information Technology',
        cohorts: {
            'DFT2 (S1)': {              // diploma
                label: 'DFT2 (S1)',
                weeks: {
                    0: [ /* events for Week 9 */ ],
                    1: [ /* events for Week 10 */ ],
                    2: [ /* events for Week 11 */ ],
                }
            },
            'DSF2 (S1)': { ... },      // diploma
            'RSD2 (S1)': { ... },      // degree
        }
    },
    'FOE': { ... },
    'FOB': { ... },
    'FAFB': { ... },
};
```

Each event follows the same structure as MyTimetable, with `cohort` included per-event:
```javascript
{
    di: 0,          // day index (0=Mon … 6=Sun)
    start: 4,       // start hour slot (08:00 = 0, each slot = 30 min)
    end: 7,         // end hour slot
    code: 'BMIT6767',
    type: 'L',      // 'L' = Lecture, 'T' = Tutorial, 'P' = Practical
    venue: 'B103',
    lecturer: 'Dr. ...',
    cohort: 'DFT2 (S1)',
    status: 'normal',  // 'normal' | 'replacement' | 'pending' | 'conflict'
    name: 'Object-Oriented Programming',
    remarks: ''
}
```

### 3. Faculty-Cohort Cascading

When user changes faculty select:
1. Clear and repopulate cohort dropdown with that faculty's cohorts
2. Select the first cohort
3. Reset search input to empty and status filter to "All"
4. Rebuild timetable

When user changes cohort select:
1. Update active faculty/cohort state variables
2. Reset search input to empty and status filter to "All"
3. Call buildTimetable() — reads from `facultyData[currentFaculty].cohorts[currentCohort].weeks[currentWeek]`
4. Update summary cards
5. Update week arrow disabled state via `updateWeekArrows()`

### 4. Search + Status Filter

Search debounced on input (course code, name, lecturer). Status filter has options: All, Normal, Replacement, Pending, Conflict.

Both search and status filter filter the events displayed in the timetable grid. Filtered results update summary counts. Zero results show the empty state.

Following the pattern from replacement-home-ui (`.search-wrapper`, `.search-input`, `.filter-select`).

### 5. Week Navigation

Identical to my-timetable: `currentWeek` ranges from 0 (Week 9, earliest) to 2 (Week 11, latest). `prevWeek()` decrements index, `nextWeek()` increments. Arrow buttons disabled at boundaries via shared `updateWeekArrows()` from `ui-common.js`.

### 6. Nav Bar Wiring

`resources/views/partials/ui-nav-bar.blade.php` line 9:
```html
<a class="nav-item {{ $activeNav === 'cohort-timetables' ? 'active' : '' }}" href="/cohort-timetable-ui">Cohort Timetables</a>
```

Route in `routes/web.php`:
```php
Route::get('/cohort-timetable-ui', function () {
    return view('ui-design-templates.CohortTimetable-UI-design-template', ['activeNav' => 'cohort-timetables']);
});
```

### 7. Visual Consistency

- **Page header**: `page-header` + `page-title` + `page-desc` — same as replacement-home-ui and my-timetable-ui
- **Semester bar**: Same visual design as my-timetable-ui's `.semester-bar` — surface background, border, shadow, week arrows + select
- **Faculty/cohort dropdowns**: Styled like `.week-select` from my-timetable (secondary container background, custom chevron SVG, same padding/font)
- **Timetable grid**: Uses shared CSS from `theme.css` (`.grid-wrapper`, `.grid-scroll`, `.timetable`, hour cells, day columns, event blocks)
- **Summary cards**: Via `partials/ui-summary-bar.blade.php` — same 4-card layout as my-timetable
- **Legend bar**: Same design as my-timetable's `.legend-bar` — flex row with swatches and labels
- **Empty state**: Uses shared `.empty-state` CSS from `theme.css` — icon + title + message
- **Status badge colors**: Defined as page-specific CSS in `@section('page-styles')` — no shared CSS exists for timetable event statuses. Define `.badge-normal`, `.badge-replacement`, `.badge-pending`, `.badge-conflict` with appropriate colors (following the color conventions from other pages: green for normal, amber for replacement, blue for pending, red for conflict). These are distinct from the request-level status classes used by my-request-history page.
- **Search toolbar**: Same styling as replacement-home-ui (`.search-wrapper`, `.search-input`, `.filter-select`)

### 8. Empty State Behavior

- **No data for cohort**: If `facultyData[currentFaculty].cohorts[currentCohort].weeks[currentWeek]` is empty/undefined, show empty-state message: "No classes scheduled for this cohort in the selected week."
- **No search results**: If filter/search yields zero matches from the events array, show empty-state message: "No events match your search criteria."
- Empty state hidden by default (`display:none`), shown when either condition is met, hidden when data is available.

### 9. Event Modal

The cohort timetable is **view-only** — no "Replace Now" or "Cancel Class" buttons. Clicking an event opens a modal showing:
- Course code + name
- Lecturer
- Venue
- Cohort
- Time slot
- Status badge
- Remarks (if any)

No action buttons in the modal footer — just "Close".

### 10. Initial Load State

On DOMContentLoaded:
1. Set currentFaculty to first key in facultyData
2. Populate faculty select dropdown from facultyData keys
3. Populate cohort select from first faculty's cohorts
4. Populate week select from weekData
5. Set currentWeek to 2 (Week 11, the most recent/current week)
6. Call buildTimetable()

## Dependencies

- `layouts/ui-template.blade.php` — shared layout with nav bar, theme.css, ui-common.js
- `public/css/theme.css` — shared grid, timetable, summary card, empty state, toolbar CSS
- `public/js/ui-common.js` — shared `updateWeekArrows()` function
- `partials/ui-summary-bar.blade.php` — summary card partial

## File Changes

| File | Change |
|------|--------|
| `routes/web.php` | New route `/cohort-timetable-ui` with `['activeNav' => 'cohort-timetables']` |
| `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` | Create — new page (extends ui-template, ~600–800 lines) |
| `resources/views/partials/ui-nav-bar.blade.php` | Change `Cohort Timetables` href from `#` to `/cohort-timetable-ui` |
