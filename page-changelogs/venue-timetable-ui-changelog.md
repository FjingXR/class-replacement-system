# Changelog — Venue Timetable UI

## [2026-08-15] Detail modal: status description as its own row

Added a **Status Description** row ("Replacement request awaiting approval" / "Class booked for this venue") separate from the Status badge.

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `openModal()` | Updated | Split `Status` and `Status Description` rows. |

---

## [2026-08-15] Detail modal refinements: gray × close, split rows, status badge

### Summary

- **Close button** — normal gray `×`; removed header `modal-status-badge`.
- **Split rows** — Subject Code/Name, Start/End Time split; status as badge row with brief description.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Removed header `modal-status-badge`. |
| `openModal()` | Updated | Split rows, badge status + description. |

---

## [2026-08-15] Detail modal redesign + fix (was never opening)

### Summary

- **Fix**: the Booked-Class detail modal opened with `.classList.add('open')` but only `.modal-overlay.show` is styled — the modal never displayed. Now uses `DetailModal` (`.show`).
- **Redesign**: converts the static `modal-field` rows to the shared `DetailModal` "Detail Sheet" (identity header, single flat group, definition rows without per-row borders). Same data (Course/Name/Lecturer/Venue/Cohort/Time/Status/Remarks).

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Modal shell | Updated | Body emptied (JS-generated now). |
| `openModal()` | Rewritten | Uses `DetailModal.render`; fixed `.open` → `.show`. |
| `closeModal()` | Updated | Uses `DetailModal.close()`. |

---

## [2026-08-15] Page-specific summary card descriptions

### Summary

Added page-specific `description` text to each summary card (Total Slots / Available / Pending / Unavailable) instead of relying on the shared generic descriptions in `ui-summary-bar`.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Summary bar cards | Updated | Each card now passes a page-specific `description` (e.g. Unavailable = "booked class, Sunday, or public holiday"). |

---

## [2026-08-15] Summary: Sunday + Public Holiday folded into "Unavailable"

### Summary

The venue summary previously had an "Occupied" card while Sunday (`.cell-sun`) and Public Holiday (`.cell-ph`) cells were not counted at all — so `Total` did not cover the whole grid and the labels were misleading. `Occupied` + `Sunday` + `Public Holiday` are all "cannot book this slot", so they're now grouped under a single **Unavailable** card.

New cards: **Total Slots**, **Available**, **Pending**, **Unavailable** (= Occupied + Sunday + Public Holiday). `Total = Available + Pending + Unavailable` now exactly equals the grid cells. Legend updated to Available / Pending / Unavailable with a tooltip explaining the grouped reasons.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| Summary bar cards | Updated | Replaced "Occupied" card with "Unavailable"; `valueId` `sumOccupied` → `sumUnavailable`. |
| Legend bar items | Updated | "Occupied" → "Unavailable" with tip `Cannot book — slot is booked, Sunday, or public holiday`. |
| `updateSummaries()` | Updated | `unavailable = occupied + sunday + ph`; `total = available + pending + unavailable`. |

---

## [2026-08-15] Summary stats now match the rendered grid (accurate counts)

### Summary

The summary cards were inaccurate in two ways:
1. **Occupied/Pending** counted event objects, not hour slots — a 3-hour booking showed as "1" instead of the 3 cells it occupies on the grid.
2. **Available** only counted Monday–Friday (`di < 5`), while the grid also renders Saturday as a working day, so `Total`/`Available` under-counted.

Now `updateSummaries()` counts exactly the cells the grid renders (`.cell-occupied`, `.cell-pending`, `.cell-available`), so the cards always match the timetable — including overlapping bookings from multiple cohorts that share an hour slot. Falls back to 0 when no venue is selected.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Location | Change | Detail |
|---|---|---|
| `updateSummaries()` | Rewritten | Counts rendered `.cell-content` cells (`occupied`/`pending`/`available`) directly from the `.timetable` DOM instead of re-deriving from `events`; this handles overlapping bookings and all working days (incl. Saturday). |

---

## [2026-08-13] Legend bar background fix

### Summary

Legend bar now has `background: var(--color-surface)` so swatches render on the same surface as timetable cells, ensuring color consistency.

### Files Changed

#### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|---|---|---|---|
| 2026-08-13 | `.legend-bar` | Updated | Added `padding`, `background: var(--color-surface)`, `border`, `border-radius` |

---

## [2026-08-13] OOP Refactor: Align cell-available with shared theme.css

### Summary

Removed duplicate `.cell-available` + hover CSS (now in `theme.css`). Simplified `.cell-content.cell-occupied` to remove redundant positioning (inherited from `.cell-content` in theme.css). Updated JS to wrap available cells in `.cell-content` class for consistency with replacement-arrangement.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 470-482 | Removed | `.cell-available` + hover (now in theme.css via `.cell-content` + `.cell-available`) |
| 2026-08-13 | Lines 485-495 | Simplified | `.cell-content.cell-occupied` — removed `position: absolute; top/left/right/bottom: 0;` (inherited from `.cell-content`) |
| 2026-08-13 | Line 1208 | Changed | `div.className = 'cell-available'` → `'cell-content cell-available'` |

---

## [2026-08-13] Phase 3 UX Enhancement: Legend Tooltips

### Summary

Added hover tooltips to all legend items describing each status.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 634-639 | Added | `tip` property to each legend item |

---

## [2026-08-13] Phase 3 UX Enhancement: Shared Legend Bar

### Summary

Replaced inline legend with shared `ui-legend-bar` partial using `$items` parameter.

### Files Changed

#### `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-13 | Lines 632-649 | Replaced | Inline legend → `@include('partials.ui-legend-bar', ['items' => [...]])` |
| 2026-08-13 | Line 634 | Changed | Legend swatch: `var(--color-primary)` → `var(--color-success)` |
| 2026-08-13 | Line 476 | Changed | `.cell-available` background: `var(--color-primary-container)` → `var(--color-success-container)` |

---

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
| 2026-08-13 | `public/css/theme.css` (shared) | CSS tooltip + legend refactor | Added `[data-tip]` CSS tooltip system (above element, inverse-surface bg, arrow, opacity transition). Legend bar: `flex-direction: column`, hint moved to top-left above swatches. Legend swatches use container tokens matching cell backgrounds. Legend bar has `background: var(--color-surface)` for consistent rendering. |
| 2026-08-14 | `buildTimetable()` | OOP refactor | Hand-built `<table>` replaced with shared `buildTimetableGrid({cellRender})` from ui-common.js. cellRender handles holiday/Sunday (cell-ph/cell-sun), event/occupied (cell-pending/cell-occupied), available cells + booking tooltip + mobile available cards. |
| 2026-08-14 | `prevWeek/nextWeek/selectWeek`, today btn | WeekNavigator delegation | Local nav logic replaced with `weekNav.prevWeek()/nextWeek()/selectWeek()` + `currentWeek` re-sync. Today button now uses `weekNav.initTodayBtn()` (class jumpToToday) + persist listener. |

## [2026-10-03] Disabled print icon on the week-nav toolbar

Shared `ui-week-nav` gained an opt-in `'showPrint' => true` arg rendering a printer icon-button
(inline SVG, `.print-btn` in theme.css), right-aligned at the toolbar edge via `margin-left: auto`.
Enabled stub: click fires the shared `toast.show('Printing is coming soon')` bottom-left toast bar;
`title="Coming soon"` native tooltip on hover. No JS beyond the one-liner onclick.

Venue Timetable renders its own identical hand-placed button (`.print-btn`, inline
`margin-left:auto`, same toast onclick) instead of the partial arg — the page's `showPrint` stays
off so exactly one button renders per page.

Venue Timetable renders its own identical hand-placed button (`.print-btn`, inline
`margin-left:auto`) instead of the partial arg — the page's `showPrint` stays off so exactly one
button renders per page.

## [2026-10-03] Book offered only when the slot can fit a booking

The venue page advertised "Book" on cells too late in the day for the arrangement page's
4-slot booking span (e.g. 18:00) — users landed straight into the conflict state. Cells where
start + BOOK_SPAN (= 4 half-hour slots, matching the arrangement's default MAX_SELECTION) would
run past day end now carry .cell-no-fit: hover label "Not bookable" + not-allowed cursor, and
clicking shows an explanatory toast ("A booking needs 120 minutes — not enough time left in the
day.") instead of the Book tooltip. Fitting cells behave exactly as before. Also: the page's
week position moved to its own localStorage key ('venueTimetableWeek'), so browsing the
replacement-arrangement grid no longer jumps this page's week. 0 console errors.

### Follow-up: a fresh Book click resets that booking's sticky memories

If a slot's auto-select was previously discarded (bookingIntentCancelled) or its reminder
dismissed (bookingIntentDismissed), re-booking the SAME slot from the venue page now clears
those keys before navigating — a fresh Book click is a fresh intent, so the banner shows and
the auto-select fires again. Other bookings' memories are untouched.

## [2026-10-03] Round-2 fixes: Class Details on desktop + tooltip staleness + keyboard

1. **Class Details modal was unreachable on desktop**: occupied/pending cells had no click
   listener (openModal was wired only to the mobile cards). Desktop cells now open the modal
   (cursor pointer, focusable, aria-label), keyboard focus tracked.
2. **Book tooltip went stale across week/venue switches**: the tooltip survived grid rebuilds
   still offering the old date/venue. buildTimetable now dismisses it on every rebuild.
3. **Keyboard B shortcut needed arrow-key navigation first**: clicking or focusing a cell now
   sets the tracked cell, so B works after click/Tab too.

Verified: occupied/pending modals on desktop, tooltip dismissal on week + venue change,
B-after-click, plus the venue-side regression set — 0 console errors.

## [2026-10-03] Round-3 fixes: mobile card list finally visible + keyboard reaches booked cells

1. **Mobile card list was built but never shown**: buildTimetable hid #mobileCardList and nothing
   re-showed it — 100+ cards (booked classes AND bookable slots) were dead markup at phone widths
   while the 7×22 desktop grid squeezed into 356px with no scroll. The list now reveals after
   every rebuild when cards exist (media-query aware), #timetable hides on ≤768px via
   body[data-page] scoped CSS, and a resize listener keeps both honest without a rebuild.
2. **Keyboard dead-end on booked cells**: occupied/pending cells are focusable (round 2) but
   arrows/Enter did nothing on them. Arrow navigation now walks ALL focusable cells; Enter on a
   booked cell opens its Class Details modal (Enter on available cells still opens the Book
   tooltip; B stays available-only).

Verified: card list on mobile (B103 booked cards + B002 bookable cards), week change re-render,
desktop unaffected, Enter/arrow/B keyboard paths, resize both directions — 0 console errors.

## [2026-10-04] Round-4 fixes: stale keyboard cell + venue guard

1. **B shortcut used a detached cell**: focusedCell survived grid rebuilds — B after a week/venue
   switch opened the Book tooltip from the old cell. buildTimetable now clears it (mirroring the
   tooltip dismissal).
2. **Unknown venue codes could clobber the selection**: onVenueChange assigned
   `currentVenue = MockData.venues.find(...)` BEFORE its guard — a stale favourite code left
   currentVenue undefined (trigger stale, page half-broken). VenueDropdown.select() now validates
   against the master list (ignores unknown codes), and onVenueChange resolves into a temp before
   committing. (Not user-reachable in the mock — the panel only lists real venues — hardening.)
3. Shared: week-filter keyboard fallback ([ / ]) no longer crashes on pages without #weekFilter.

Verified star sync across venue switches, favourites cap tooltip, B-after-rebuild, and the
regression set — 0 console errors.

## [2026-10-04] Lead-time rule: venue booking blocked < 3 working days out

Same rule as the arrangement page (shared `isSlotTooSoon`, MOCK_NOW anchor — see that page's
changelog entry): otherwise-free cells inside the 3-working-day window (and past days) render
read-only (`cell-too-soon`, hover "Min. 3 working days ahead") — **no Book tooltip, no
keyboard, no mobile bookable card**. Booked/pending classes still render normally, including in
past weeks (read-only browsing of history stays intact).

The summary bar counts too-soon cells as Unavailable (Unavailable = booked + Sunday + holiday +
too soon) so Total stays consistent; the legend tooltip documents the new reason.

Verified: Week 11 Mon/Tue blocked + Wed–Sat bookable, booked classes visible inside blocked
days, past weeks read-only with classes shown, summary arithmetic, no tooltip on too-soon
cells, mobile card list reduced to informational cards — 0 console errors.

**Follow-up:** the lead-time rule is announced in the UI — a notice under the venue header
("Bookable from Wednesday, 07 Oct 2026 onward — replacement requests need at least 3 working
days' notice."), date computed from MOCK_NOW + holidays via the shared
`leadTimeCutoff()`/`renderLeadTimeNote()` helpers.

**Rework:** the lead-time notice moved above the grid and became a contextual banner (booking-
banner family, neutral info tone) — shown while the viewed week has blocked days, hidden on
fully open weeks, copy adapts (passed week / current week / partially blocked).

**Tweak:** the lead-time banner's date ("Wednesday, 07 Oct 2026") uses the success/green
token so the opening date reads as the focal point.

**Fix ("Today" consistency):** the venue template's local `getTodayMs()` shadow (pre-refactor
leftover) was deleted — it silently overrode the shared mock-anchored helper, making this page
read the REAL clock while the arrangement read `MockData.mockNow`. Both pages now use the one
shared anchor.

**Note (no visible change):** the shared `ui-today-btn` partial and `WeekNavigator` gained an
"earliest bookable" variant used by the replacement-arrangement page (where unbookable weeks
are hidden and "Today" would point off the visible range). The venue page keeps its full week
list and "Today" — it's the browsing/history page, so no change applies here.

**Note (no visible change):** the "Book from" grid chip is opt-in (cfg.bookableBadge) — only
the replacement-arrangement page renders it; this page keeps its full week list + "Today"
button (id todayBtn, unchanged).

**Note (no visible change):** the BOOKINGS OPEN badge (success-green) remains opt-in on the
replacement-arrangement page; this page keeps its today-badge (primary) and full week list.

**Note (shared anchor move):** `MockData.mockNow` → Mon 5 Oct 2026 — the venue's current week
is now Week 10 (05 Oct ~ 11 Oct) with the TODAY badge on the Mon column; the lead-time
blackout boundary becomes Thu 08 Oct on both pages.

**Tweak (label parity):** the venue's week select drops its custom labelFn and uses the shared
default — "Week 10 · 28 Sep 2026 ~ 04 Oct 2026" (· separator, both years) — now identical in
format to the arrangement's selector. Nothing else changes.

**Tweak (summary cards commented out):** the four summary cards (Total Slots / Available /
Pending / Unavailable) are Blade-commented on this page — the grid already shows the same
state colour-coded and the legend explains it, so the counts added little value to the booking
journey. The shared `ui-summary-bar` partial is untouched (8 other pages use it); the include
is restored by uncommenting, and `updateSummaries()` is null-guarded + keeps its counting so
the page runs without the cards.

**Tweak (dropdown arrow consistency):** the venue dropdown trigger now uses the SAME design as
the week-select family — the label and the chevron are split (label span + shared 10×6
stroke-1.5 chevron SVG, currentColor/token-toned), so the filled "▾" text glyph is gone;
typography aligned to the week select (13px/600, height 36px, radius-md — was 14px/500,
radius-sm). The old stale-green arrow hex (#3d5a48, an orphan of a previous palette) is
replaced across ALL selects by per-theme strokes mirroring the tokens
(dark/on-surface-variant #9EAAB8, light #5A6978; semester-bar selects mirror
on-primary-container #6BA3E0 / #003366) since SVG data-URIs cannot use var().

### Postscript — sweep-fixes-round-1 (2026-10-06, F-4)

**Status badges no longer leak raw lowercase enums.** New shared `StatusText.label()`
promoted to ui-common (3 sites on this page = the duplication threshold): history card
(:660), event card (:846), modal Status row (:914) now print
"Normal" / "Replacement" / "Pending" (Title case §10.0 vocabulary) while the
`badge-*` CSS keys stay raw-enum — zero visual/color change, text only.
