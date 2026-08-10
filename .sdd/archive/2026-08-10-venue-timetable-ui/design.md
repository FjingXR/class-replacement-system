# Design: Venue Timetable UI

## Technical Approach

Create a new Blade template extending `layouts.ui-template`. The page is a **view-only weekly timetable** for any venue, showing all booked classes across all cohorts. It reuses the timetable grid pattern from `CohortTimetable` and `MyTimetable`, with a venue dropdown replacing the cohort selector. Empty slots are green (available) and clickable, showing a tooltip confirmation before redirecting to the booking page.

**Skeleton loading:** Use the project standard skeleton loading pattern from `skeleton-loading-scroll-restore` SDD change (now applied). Call `showSkeleton()` on page load and venue/week change, then `hideSkeleton()` after data renders.

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

- **Desktop:** Click available slot → tooltip: "Book B014 on Mon, 01 Sep 2026 at 09:00?" with "Book" button → redirect to `/replacement-arrangement?venue=XXX&date=YYY&time=ZZZ&code=XXX&cohort=XXX`
- **Mobile:** Tap available slot → modal with "Book This Venue" button → redirect to booking page

### 5. "Book This Venue" Button

**Decision:** Button opens `/replacement-arrangement` with venue, date, time, code, and cohort pre-filled via URL params.

```javascript
function bookVenue(venueCode, date, time) {
    let url = `/replacement-arrangement?venue=${venueCode}&date=${date}&time=${time}`;
    if (currentCourseCode) url += `&code=${currentCourseCode}`;
    if (currentCohort) url += `&cohort=${currentCohort}`;
    window.location.href = url;
}
```

### 5a. URL Params Support (code/cohort passthrough)

**Decision:** Read `code` and `cohort` from URL when coming from My Timetable flow.

```javascript
// Read URL params on page load
const params = new URLSearchParams(window.location.search);
const courseCode = params.get('code');
const cohort = params.get('cohort');

// Show banner if code+cohort present
if (courseCode && cohort) {
    document.getElementById('bookingBanner').textContent = `Booking for: ${courseCode} — ${cohort}`;
    document.getElementById('bookingBanner').style.display = '';
}
```

**Banner location:** Below page header, above venue dropdown.

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

### 12. Venue Favourites (A1)

**Decision:** Star icon per venue, save to localStorage + prepared `users.favourites` JSONB column.

- **Desktop:** Star icon next to venue name in dropdown
- **Mobile:** Star icon on venue card header
- **Storage:** `localStorage.setItem('venueFavourites', JSON.stringify(['B014', 'B025']))`
- **Backend ready:** `users.favourites` JSONB column for future migration
- **UI:** Favourited venues shown at top of dropdown with star icon

```javascript
// localStorage structure
{
    "venueFavourites": ["B014", "B025"],
    "venueRecent": ["B014", "B025", "B033", "B041", "B052"]
}
```

### 13. Booking History (A2)

**Decision:** Show past 4 weeks of bookings for selected venue.

- **Data source:** `MockData.cohortTimetable` (all cohorts), filtered by venue
- **Time range:** Current week - 4 weeks to current week
- **UI:** Tab/toggle above timetable: "Current Week" | "Past 4 Weeks"
- **Past weeks:** Show as collapsed rows with date + course + status
- **No pagination:** All 4 weeks visible in scrollable container

### 14. Time Range Filter (A3)

**Decision:** Filter by Morning (08-12) / Afternoon (13-18) / All.

- **Placement:** Below week picker, above timetable grid
- **UI:** 3-segment toggle button (Morning | Afternoon | All)
- **Default:** All
- **Behavior:** Filters visible rows in timetable (hide 12-13 lunch break)
- **AND logic:** Combined with venue type filter

### 15. Venue Type Filter (A4)

**Decision:** Filter by Tutorial / Lecture Hall / Lab.

- **Placement:** Next to time range filter (horizontal filter bar)
- **UI:** Dropdown with checkboxes: [x] Tutorial [x] Lecture Hall [x] Lab
- **Default:** All checked
- **Data:** `MockData.venues[].type` field
- **AND logic:** Combined with time range filter

### 16. Quick Book Shortcut (B3)

**Decision:** Press `B` on focused available cell → tooltip confirmation → redirect.

- **Trigger:** `keydown` event when cell is focused
- **Key:** `B` (case-insensitive)
- **Behavior:** Same as click — show tooltip with "Book" button
- **Accessibility:** Announce to screen reader "Press B to book this slot"
- **Visual hint:** Show "(B)" label on focused available cells

### 17. Recent Venues Dropdown (B4)

**Decision:** Show last 5 used venues at top of dropdown.

- **Storage:** `localStorage.setItem('venueRecent', JSON.stringify(['B014', 'B025', 'B033', 'B041', 'B052']))`
- **Update:** On venue selection, push to front, dedupe, keep last 5
- **UI:** Dropdown sections:
  ```
  ── Recent ──────────
  B014 — Tutorial (35 seats)
  B025 — Lecture Hall (120 seats)
  ── All Venues ──────
  B001 — Tutorial (35 seats)
  ...
  ```
- **Limit:** 5 recent venues max

### 18. Empty States

**Decision:** Handle three empty state scenarios:

**A) No venues match filter:**
- Show message: "No venues match criteria"
- Show "Show all venues" button to reset filters
- Trigger: venue type + time range filters exclude all venues

**B) No classes booked for venue:**
- Keep grid empty (all slots green/available)
- Show hint text: "All slots available — this venue is free all week"
- No modal needed — entire grid is clickable

**C) No conflict/cancelled slots in slot picker:**
- Show message: "No slots need replacement"
- Hide slot picker section entirely
- User can still manually select venue + time on grid

### 19. Error Handling

**Decision:** Handle three error scenarios:

**A) MockData fails to load:**
- Show error banner: "Unable to load data. Please refresh."
- Include refresh button
- Hide timetable grid until data loads

**B) Venue has no data in cohortTimetable:**
- Show message: "No schedule data for this venue"
- Keep venue dropdown enabled (user can try another)
- Grid remains empty (all green)

**C) Slot already taken when clicking "Book":**
- Show toast notification: "This slot was just booked by someone else"
- Auto-refresh grid after 2 seconds
- Show updated availability

### 20. Print Button (Future Enhancement)

**Decision:** Add print button to page header (no functionality yet).

- **Location:** Next to semester chip or in toolbar
- **Icon:** Printer icon from `resources/views/flux/icon/`
- **Behavior:** Button visible but disabled (tooltip: "Coming soon")
- **OOP approach:** When implemented, use shared print function in `ui-common.js`
- **Scope:** Deferred to separate session (not in this SDD's implementation)

### 21. Backend Integration Notes

**Decision:** Document API endpoints and real-time features for future backend phase.

**API Endpoints Needed:**
```
GET  /api/venues                    — list all venues
GET  /api/venues/:code/schedule     — get venue schedule (week range)
GET  /api/courses                   — list all courses
GET  /api/courses/:code/slots       — get conflict/cancelled slots for course
POST /api/replacements              — create replacement request
GET  /api/replacements/:id/status   — check replacement status
```

**Real-time Features (WebSocket):**
- Live venue availability updates (when someone books a slot)
- Toast notification when selected slot is taken by another user
- Auto-refresh grid on slot status change

**Database Tables:**
- `users.favourites` — JSONB column for venue favourites
- `users.recent_venues` — JSONB column for recent venues
- `replacements` — replacement request records
- `venues` — venue registry (code, type, capacity)

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

## Testing Scenarios

### Core Functionality
1. **Venue selection:** Select different venues → grid updates correctly
2. **Week navigation:** Prev/next arrows + dropdown → correct week loads
3. **Slot rendering:** Booked slots show correct course, cohort, status
4. **Available slots:** Green slots are clickable → tooltip shows
5. **Modal:** Click booked slot → modal shows correct details

### Filters
6. **Time range filter:** Morning/Afternoon/All → correct rows shown
7. **Venue type filter:** Tutorial/Lecture Hall/Lab → correct venues shown
8. **Combined filters:** AND logic works correctly
9. **No matches:** "No venues match criteria" + reset button

### Keyboard Navigation
10. **Arrow keys:** Move between cells correctly
11. **Enter:** Opens modal for booked, tooltip for available
12. **Escape:** Closes modal/tooltip
13. **B shortcut:** Triggers book action on available cell

### URL Params
14. **Code + cohort:** Subject pre-selected, dropdown disabled
15. **Venue + date + time:** Venue selected, slot highlighted
16. **Invalid params:** Graceful fallback (no crash)

### Empty States
17. **No classes:** Grid all green, hint text shown
18. **No conflict slots:** Slot picker hidden, message shown
19. **No venues match:** Error message + reset button

### Error Handling
20. **Load failure:** Error banner with refresh button
21. **No venue data:** "No schedule data" message
22. **Slot taken:** Toast + auto-refresh

### Mobile
23. **Card layout:** All booked classes show as cards
24. **Available slots:** Tappable → modal with booking button
25. **Filters:** Full-width on mobile
26. **Summary cards:** Stack to 1 column
