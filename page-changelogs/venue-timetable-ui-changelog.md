# Changelog — Venue Timetable UI

## [2026-08-13] Phase 3 UX Enhancement: Collapsible Guide Block

### Summary

Added an expandable guide block with page-specific workflow instructions.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 546-555 | Added | `@include('partials.ui-guide-block')` with 5 workflow tips |

---

## Files Changed

### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | — | Created page | New Blade template with venue dropdown, week picker, timetable grid, legend bar, summary cards, modal |
| 2026-08-09 | `@section('page-styles')` | Page-specific CSS | Venue dropdown, filter bar, segment toggle, venue type filter, booking banner, tab toggle, history panel, hint text, error banner, toast, print button, available tooltip, booked modal, keyboard hints, no-match banner, responsive styles |
| 2026-08-09 | `@section('content')` | HTML structure | Page header with print button, booking banner, error banner, no-match banner, venue + week picker, tab toggle, filter bar (time range + venue type), history panel, grid wrapper, hint text, legend bar, summary bar, empty state, mobile card list, detail modal, available tooltip, toast |
| 2026-08-09 | `@section('page-scripts')` | Render logic | State variables, week data builder, URL params reader, init function, venue dropdown builder (with recent/favourites), venue change handler, week nav, tab toggle, filter logic, venue events getter, timetable grid builder, mobile card builder, summary updater, modal (booked class), available tooltip, keyboard navigation, favourites localStorage, recent venues localStorage, state persistence, toast notification |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | After `request-approval-ui` route | New route | Added `Route::get('/venue-timetable-ui', ...)` returning view `ui-design-templates.venue-timetable-UI-design-template` |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-09 | `$items` array | Nav item | Added "Venue Timetable" as 6th nav item with key `venue-timetable` and href `/venue-timetable-ui` |

### Design Updates (SDD enhancements)

| Timestamp | Change | Detail |
|-----------|--------|--------|
| 2026-08-08 | URL params support | Read `code` and `cohort` from URL when coming from My Timetable flow |
| 2026-08-08 | Booking banner | Show "Booking for: BMIT5555 — RSD3G2" when `code`+`cohort` present |
| 2026-08-08 | Code/cohort passthrough | Pass `code`+`cohort` through when "Book Now" is clicked |
| 2026-08-08 | Advanced features | Added: venue favourites, booking history, time/venue type filters, quick book shortcut, recent venues |
| 2026-08-09 | Skeleton loading | `withSkeleton()` on venue change, standard pattern |
| 2026-08-09 | Empty states | Hint text for no classes, no-match banner for filter |
| 2026-08-09 | Error handling | Error banner for MockData failure, toast for slot taken |
| 2026-08-09 | Print button | Disabled printer icon in header with "Coming soon" tooltip |
| 2026-08-09 | Booking history | Tab toggle for Current Week / Past 4 Weeks with history panel |
| 2026-08-09 | Quick book (B3) | Keyboard shortcut B on focused available cell |
| 2026-08-09 | Keyboard nav | Arrow keys, Enter, Escape navigation on grid |
| 2026-08-09 | Mobile view | Card layout for booked/available slots on ≤768px |
| 2026-08-09 | Tablet view | Reduced grid density on 769px–1024px |
| 2026-08-09 | UI Polish (A1) | Print icon moved to rightmost of semester-bar |
| 2026-08-09 | UI Polish (A2) | Timetable design matched to replacement-arrangement |
| 2026-08-09 | UI Polish (A3) | Favourite star changed to separate button (36px) with better touch target |
| 2026-08-09 | UI Polish (A4) | Morning/Afternoon labels now show time ranges (8AM–12PM / 1PM–6PM) |
| 2026-08-09 | UI Polish (A5) | Added booking hint banner: "Click any green slot to book this venue" |
| 2026-08-09 | Bug Fix (B1) | Venue type filter now actually filters venues in dropdown |
| 2026-08-09 | Bug Fix (B2) | Morning/Afternoon filter now hides all cells outside range (including holiday/Sunday) |
| 2026-08-09 | Bug Fix (B3) | Holiday/Sunday cells now show lock icon + "Holiday"/"OFF" label |
| 2026-08-09 | Bug Fix (B4) | Today button now works (initTodayBtn() called in DOMContentLoaded) |
| 2026-08-09 | Bug Fix (B5) | Tooltip only shows for cells matching current time filter |
| 2026-08-09 | Bug Fix (B6) | Mock data made more distinct with events in B014, B015, B016 |
| 2026-08-09 | UX (C1) | Past 4 Weeks changed from tabs to collapsible details element |
| 2026-08-13 | `@section('page-styles')` | macOS-style update (Phase 4) | Removed ~48 lines of duplicate modal CSS (`.modal`, `.modal-header`, `.modal-title`, `.modal-status-badge`, `.modal-close`, `.modal-body`, `.modal-field`, `.field-label`, `.field-value`, `.modal-footer`, `.btn-close-modal`, `#eventModal.modal-overlay`) — all now served by `theme.css`. Standardized border-radius: `fav-btn`, `segment-toggle`, `venue-type-btn`, `print-btn`, `booking-hint`, `no-match-banner button` 6px→8px; `history-status`, `venue-event-status`, `btn-book`, `error-banner button` 4px→6px. Standardized shadows: `venue-type-dropdown`, `toast-notification`, `available-tooltip` → layered `0 4px 16px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06)`. Fixed hardcoded `#fff` → `var(--color-on-primary)` in `segment-toggle button.active`, `btn-book`, `no-match-banner button`. Added `:focus-visible` rings to `fav-btn`, `segment-toggle button`, `venue-type-btn`, `print-btn`, `btn-book`, `error-banner button`, `no-match-banner button`. |
| 2026-08-13 | `public/css/theme.css` (shared) | macOS table fix | `.timetable`: `border-collapse:collapse` → `separate` + `border-spacing:4px`; removed 1px cell borders; hover uses `var(--color-surface-variant)`; removed zebra striping. `.badge` border-radius 6px→8px. |
