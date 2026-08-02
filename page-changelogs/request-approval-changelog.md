# Changelog — Request Approval (PL Side)

> NOTE: Entries below are the planned implementation state (pre-implementation). Reconcile timestamps/details with the actual applied state after `/sdd-apply` completes.

## Files Changed

### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 15:00 | — | Created page | New Blade template extending `layouts/ui-template` with `activeNav = 'request-approval'`. PL review interface: page header, toolbar (search by code/name/lecturer, status filter defaulting to Pending, week filter, Reset Filters), 10-column sortable table with urgency badges, pagination (10/page), 4 summary cards (Pending/Approved/Rejected/Total Reviewed), 3-section detail modal with approve/reject footer, 2-variant empty state, dark/light theme toggle |
| 2026-08-01 15:00 | — | Mock data | 20 entries (8 Pending, 5 Approved, 4 Rejected, 2 Completed, 1 Cancelled), 5 distinct lecturers (Kylian Mbappe, Dembele, Hakimi, Neymar, Vinicius), 8 unique courses (BMIT2201, BMIT3302, BMIT4403, BMIT5504, BMIT6605, BMIT7706, BMIT8807, BMIT9908), 6 cohorts with (S1) suffix, 6 venues (C201, C202, D103, D104, E201, E202), durations 1–3 hours, class dates spanning 29 Aug – 27 Sep. All data distinct from my-request-history |
| 2026-08-01 15:00 | — | Lecturer column | New column 3 (`.col-lecturer`, 130px) rendering lecturer name as plain text — PL reviews who submitted each request |
| 2026-08-01 15:00 | — | Urgency column | New column 8 (`.col-urgency`, 90px) with badge: `Urgent` (red, `--color-error-container`) when class is ≤3 days from fixed reference date `2026-08-29`, else `Normal` (green, `--color-secondary-container`). Fixed reference date (not `new Date()`) keeps badges deterministic in mock demo data |
| 2026-08-01 15:00 | — | Actions column | New column 10 (`.col-actions`, 140px): Pending rows show inline `✓ Approve` (green outline) + `✕ Reject` (red outline) buttons; non-Pending rows show `View` button |
| 2026-08-01 15:00 | — | Default status filter | Status dropdown defaults to `Pending` on load (set in DOMContentLoaded, not `'all'`). Reset Filters also returns status to Pending — PL lands on the actionable queue first |
| 2026-08-01 15:00 | — | Approve/Reject behavior | `approveRequest(id)` / `rejectRequest(id)` show browser `confirm()` then `alert()` — frontend design phase only, does NOT modify mock data, re-render, or update summary cards |
| 2026-08-01 15:00 | — | Modal footer toggling | Pending rows: Reject (left) + Approve (right), footer Close hidden. Non-Pending rows: Close only (header ✕ always visible). No Cancel button (PL doesn't submit requests) |
| 2026-08-01 15:00 | — | Search scope | Search matches course code, course name, OR lecturer (my-request-history only matches code + name) |
| 2026-08-01 15:00 | — | No Exclude Completed toggle | Toolbar has search + status + week + reset only — PL needs all statuses visible as audit trail |
| 2026-08-01 15:00 | — | Summary cards | 4 cards via `@include('partials.ui-summary-bar')`: Pending (amber), Approved (green), Rejected (red), Total Reviewed (blue). Total Reviewed = Approved + Rejected + Completed (excludes Pending + Cancelled). Counts from all mock data (unfiltered) |
| 2026-08-01 15:00 | — | Sort profile | 5 sortable columns: Requested Timestamp (`requestedAt`), Original Class (`classDate`), Proposed Replacement (`replacementDate`), Urgency (`urgencyDays`, computed at sort time), Status (`status`). Lecturer and Course Code & Name un-sortable (searched, not sorted). Default sort = `requestedAt` asc (FIFO queue, FR 3.2) |
| 2026-08-01 15:00 | — | Rejection Reason modal (FR 3.6) | Reject button opens `#rejectReasonModal` — mandatory-reason textarea, Confirm Reject disabled until non-whitespace input, reason echoed in confirm dialog. Satisfies FR 3.6 |
| 2026-08-01 15:00 | — | Slot Validity field (FR 3.4) | New mock `slotValidity` field ('valid'/'conflict', ~4-5 conflicts with `conflictReason`) + Slot Validity line in modal Section 3 (`slotValidityHtml`, `.slot-valid`/`.slot-conflict` CSS). Satisfies FR 3.4 |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 15:00 | — | New route | `GET /request-approval-ui` → `ui-design-templates.request-approval-UI-design-template` with `activeNav = 'request-approval'`. No `->name()` (unreferenced by `route()` helpers) |

### `public/js/mock-data.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 15:00 | — | Created data module | New shared JS data module: `mockRequests` (20 entries: 8 Pending, 5 Approved, 4 Rejected, 2 Completed, 1 Cancelled; distinct lecturers/courses/cohorts/venues; `slotValidity` + `conflictReason` fields for FR 3.4) + `URGENCY_REFERENCE_DATE` constant. Data separated from logic (OOP encapsulation) — page script references the globals, does not redeclare them |

### `resources/views/layouts/ui-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 15:00 | After ui-common.js | Script tag | Added `<script src="/js/mock-data.js"></script>` after the ui-common.js include so the shared data module loads before any page inline script |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 15:00 | After Replacement Arrangement | Nav link | Added `<a class="nav-item ..." href="/request-approval-ui">Request Approval</a>` between "Replacement Arrangement" and "Replacement History" — nav now shows 6 links |

### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 15:00 | Appended | OOP helper promotion | 10 shared helpers promoted from `my-request-history-UI-design-template.blade.php` (page-local) into `ui-common.js` (single source of truth): `weekRanges`, `formatDateTime(iso)`, `statusClass(status)`, `dayAbbr(day)`, `isoDayName(iso)`, `formatClassBlock(r)`, `formatReplacementBlock(r)`, `getWeekRange(weekVal)`, `isInWeek(classDate, weekVal)`, `getWeekNumber(iso)` — no logic changes, moved verbatim |

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-01 15:00 | Lines 465–546 | OOP refactor | Removed the 10 page-local helper copies — they now resolve from `ui-common.js` (loaded by the layout before the page's inline script). No behavior change; verified via regression check (sorting, week filter, pagination, modal, Cancel button) |

## Key Design Decisions

- **OOP architecture** — the page is built on inheritance (`@extends` layout), composition (`@include` partials: nav-bar, summary-bar), and encapsulation (shared `theme.css` + `ui-common.js` + `mock-data.js` modules). 10 duplicated helpers were promoted into `ui-common.js` instead of copy-pasting them into the new page; mock data lives in `mock-data.js` (data separated from logic); my-request-history was refactored to consume the shared versions
- **10-column layout** — drops my-request-history's Type/Venue/Cohort columns (PL reviews, doesn't edit), adds Lecturer, Urgency, Actions. Table `min-width` 1300px, `.col-students` narrowed to 70px to fit
- **Urgency via fixed reference date** — `URGENCY_REFERENCE_DATE = 2026-08-29` (Friday before Week 1). Real `new Date()` would compute every mock entry as "Normal" when viewed in July, defeating the feature
- **In-table quick actions** — Approve/Reject live in the table for Pending rows; status badge and View button both open the detail modal. Confirmation dialogs simulate the action without state change (backend integration comes later)
- **Default Pending filter** — PL workflow starts with the actionable queue; "All" is one click away
- **FR 3.2 time-based queue** — default sort `requestedAt` asc (oldest first = FIFO review order); Reset Filters returns to it
- **FR 3.6 mandatory rejection reason** — reject flow goes through a dedicated reason modal (Confirm disabled until a reason is typed) instead of a bare confirm()
- **FR 3.4 slot validity** — mock `slotValidity` field displayed in the modal (✓ Valid / ⚠ Conflict); backend phase will compute it via the matrix intersection engine (FR 4.3)
- **PL-specific empty state** — no CTA button (PL doesn't submit requests); messages: "No replacement requests to review at this time." / "No requests match your search or filter criteria."
