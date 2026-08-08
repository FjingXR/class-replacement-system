# Design: Venue Timetable UI

## Technical Approach

Create a new Blade template extending `layouts.ui-template`. The page is a **view-only weekly timetable** for any venue, showing all booked classes across all cohorts. It reuses the timetable grid pattern from `CohortTimetable` and `MyTimetable`, with a venue dropdown replacing the cohort selector. Empty slots are green (available) and clickable, showing a tooltip confirmation before redirecting to the booking page.

## Architecture Decisions

### 1. Template Structure

**Decision:** `@extends('layouts.ui-template', ['activeNav' => 'venue-timetable'])` with 4 sections: `title`, `page-styles`, `content`, `page-scripts`.

```blade
@extends('layouts.ui-template', ['activeNav' => 'venue-timetable'])

@section('title', 'Venue Timetable — Class Replacement System')

@section('page-styles')
    /* Page-specific CSS: venue dropdown, available-slot styling, modal overrides */
@endsection

@section('content')
    <!-- Page header -->
    <!-- Semester chip -->
    <!-- Venue dropdown + Week picker -->
    <!-- Grid wrapper > timetable -->
    <!-- Legend bar (4 items) -->
    <!-- Summary bar (5 cards) -->
    <!-- Empty state -->
    <!-- Detail modal -->
@endsection

@section('page-scripts')
    // Render logic ONLY — reads window.MockData.*
    // venueData from MockData.venues
    // timetableData built from MockData.cohortTimetable (all cohorts)
@endsection
```

### 2. Data Source — All Cohorts, Filtered by Venue

**Decision:** Read from `MockData.cohortTimetable` (all cohorts) and filter by selected venue.

```javascript
// Build venue-specific timetable from all cohorts
function buildVenueTimetable(venueCode, weekIndex) {
    const events = [];
    Object.keys(MockData.cohortTimetable).forEach(cohortKey => {
        const cohortData = MockData.cohortTimetable[cohortKey];
        if (cohortData.rsd3g2Base) {
            cohortData.rsd3g2Base.forEach(event => {
                if (event.venue === venueCode) {
                    events.push({
                        ...event,
                        cohort: cohortKey,
                        status: 'normal',
                        remarks: ''
                    });
                }
            });
        }
    });
    return events;
}
```

### 3. Legend Bar — 4 Items

**Decision:** Use `@include('partials.ui-legend-bar')` with 4 items (no "Normal Class").

| Item | Token | Meaning |
|------|-------|---------|
| Available | `--color-secondary` (green) | No class booked — slot is free |
| Replacement | `--color-primary` (blue) | Approved replacement session |
| Pending | `--color-tertiary` (yellow) | Awaiting PL approval |
| Conflict | `--color-error` (red) | Occupied / Holiday / Clash |

**Note:** "Conflict" covers three scenarios:
1. **Occupied** — another class already booked at this time
2. **Holiday** — falls on a public holiday (from `MockData.holidays`)
3. **Clash** — scheduling conflict with another booking

### 4. Available Slot Interaction

**Decision:** Available (green) slots are clickable on **all viewports**, showing a tooltip confirmation before redirecting.

- **Desktop:** Click available slot → tooltip: "Book B014 on Mon, 01 Sep 2026 at 09:00?" with "Book" button → redirect to `/replacement-arrangement?venue=XXX&date=YYY&time=ZZZ`
- **Mobile:** Tap available slot → modal with "Book This Venue" button → redirect to booking page

### 5. "Book This Venue" Button

**Decision:** Button opens `/replacement-arrangement` with venue, date, and time pre-filled via URL params.

```javascript
function bookVenue(venueCode, date, time) {
    window.location.href = `/replacement-arrangement?venue=${venueCode}&date=${date}&time=${time}`;
}
```

### 6. Summary Cards

**Decision:** 5 cards via `@include('partials.ui-summary-bar')`:

| Card | Class | Value |
|------|-------|-------|
| Total Classes | `card-total` | Count of all booked slots |
| Available | `card-available` | Count of empty slots (Mon–Fri, 08:00–18:00) |
| Replacement | `card-replacement` | Count of replacement status |
| Pending | `card-pending` | Count of pending status |
| Conflict | `card-conflict` | Count of conflict status |

### 7. Keyboard Navigation

**Decision:** Full keyboard navigation for accessibility:

- **Arrow keys (↑↓←→):** Move between timetable cells
- **Enter:** Open modal for booked cells, or trigger tooltip for available cells
- **Escape:** Close modal/tooltip
- Focus indicator visible on focused cell

### 8. Venue Display Format

**Decision:** Show venue with type and capacity: `"B014 — Tutorial (35 seats)"` (same format as buildingSelector in replacement-arrangement).

### 9. Booked Class Modal

**Decision:** Modal shows class details only (no "Book This Venue" button). Useful for checking who booked the venue.

### 10. Mobile View

**Decision:** Card layout on mobile (≤768px), same pattern as `student-my-timetable`.

- Each booked class = a card with: Course Code, Cohort, Day, Time, Status badge
- Available slots = green cards with "Available" label, **tappable** → open modal with "Book This Venue" button
- Modal shows venue details + booking button
- Legend bar wraps to 2 rows if needed

### 11. Nav Bar Update

**Decision:** Add 6th nav item to `partials/ui-nav-bar.blade.php`:

```php
$items = $navItems ?? [
    ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard'],
    ['key'=>'my-timetable','label'=>'My Timetable','href'=>'/my-timetable-ui'],
    ['key'=>'cohort-timetables','label'=>'Cohort Timetables','href'=>'/cohort-timetable-ui'],
    ['key'=>'replacement-arrangement','label'=>'Replacement Arrangement','href'=>'/replacement-home-ui'],
    ['key'=>'replacement-history','label'=>'Replacement History','href'=>'/my-request-history-ui'],
    ['key'=>'venue-timetable','label'=>'Venue Timetable','href'=>'/venue-timetable-ui'],  // NEW
];
```

## Reusable Components

| Component | Source | Notes |
|-----------|--------|-------|
| Week picker | `MyTimetable` / `CohortTimetable` | Reuse `.semester-bar` pattern |
| Legend bar | `@include('partials.ui-legend-bar')` | Add "Available" item |
| Summary cards | `@include('partials.ui-summary-bar')` | 5 cards |
| Timetable grid | `CohortTimetable` | Similar structure |
| Modal shell | `CohortTimetable` / `MyTimetable` | Reuse `.modal-overlay` pattern |
| Venue dropdown | `MockData.venues` | Already exists in mock-data.js |

## Mobile & Tablet View Design (rule #9 — mandatory)

### Breakpoints
- **Tablet:** `@media (max-width: 1024px)` — reduce grid density
- **Mobile:** `@media (max-width: 768px)` — card layout

### Mobile Layout (≤768px)

**Page header:**
- Stack title, semester-chip, venue dropdown, description vertically
- Reduce font sizes

**Venue dropdown:**
- Full-width on mobile

**Week picker:**
- Hide prev/next arrows, full-width dropdown

**Timetable grid → Card layout:**
- Hide `<table.timetable>` on mobile
- Show `.card-list` container
- Each booked class = card:
  ```html
  <div class="venue-event-card">
      <div class="venue-event-header">
          <span class="venue-event-code">BMIT7070</span>
          <span class="venue-event-status status-normal">Normal</span>
      </div>
      <div class="venue-event-body">
          <div class="venue-event-row"><span class="venue-event-label">Cohort</span><span class="venue-event-value">RSD3(S1)G2</span></div>
          <div class="venue-event-row"><span class="venue-event-label">Day</span><span class="venue-event-value">Monday</span></div>
          <div class="venue-event-row"><span class="venue-event-label">Time</span><span class="venue-event-value">09:00 – 11:00</span></div>
      </div>
  </div>
  ```
- Available slots = green cards with "Available" label, **tappable** → open modal with "Book This Venue" button

**Mobile available slot modal:**
```html
<div class="venue-available-modal">
    <h3>Book B014 — Tutorial (35 seats)</h3>
    <p>Mon, 01 Sep 2026 at 09:00</p>
    <button class="btn btn-primary" onclick="bookVenue('B014','2026-09-01','09:00')">Book This Venue</button>
</div>
```

**Legend bar:**
- Wrap to 2 rows if needed

**Summary cards:**
- Stack to 1 column on mobile

### Tablet Layout (769px–1024px)

**Timetable grid:**
- Keep table layout but reduce cell padding
- Smaller font for event blocks

## Dependencies

- `mock-data.js` — `MockData.venues`, `MockData.cohortTimetable`, `MockData.semester`, `MockData.holidays`
- `ui-common.js` — `to12h`, `formatDate`, `closeOnEsc`, `closeOnOverlayClick`, `updateWeekArrows`
- `theme.css` — shared component CSS (grid, legend, summary, modal)
- `partials/ui-legend-bar.blade.php` — reuse (add "Available" item)
- `partials/ui-summary-bar.blade.php` — reuse
- `partials/ui-nav-bar.blade.php` — add 6th nav item
- `layouts/ui-template.blade.php` — base layout

## File Changes

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php` | **Create** — new venue timetable template |
| `routes/web.php` | **Edit** — add `/venue-timetable-ui` route |
| `resources/views/partials/ui-nav-bar.blade.php` | **Edit** — add 6th nav item |
| `resources/views/partials/ui-legend-bar.blade.php` | **Edit** — add "Available" item (or create page-specific legend) |
| `page-changelogs/venue-timetable-ui-changelog.md` | **Create** — new changelog |
