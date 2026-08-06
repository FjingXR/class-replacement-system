# Changelog — Request Approval (PL Side)

## [2026-08-06] Initial Implementation — 17 PL-Efficiency Features

### Summary

New Blade template for Programme Leader replacement request review. 11-column table (checkbox + 10 data columns), 20 mock entries, 17 PL-efficiency features (bulk approve/reject, enhanced confirms, reject presets, urgency filter, request age, approval notes, nav badge, viewed indicator, keyboard shortcuts, review-next auto-advance, slot validity icons, toast notifications, undo stack, animated transitions, smart grouping, mini timeline, skeleton loading). OOP: 9 shared helpers promoted to `ui-common.js`, mock data in shared `mock-data.js`.

## Files Changed

### `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | — | Created page | New Blade template extending `layouts.ui-template` with `activeNav = 'request-approval'`. 11-column PL review interface: checkbox, #, Requested Timestamp, Lecturer, Course Code & Name, Original Class, Proposed Replacement, Students, Urgency, Status, Actions |
| 2026-08-06 13:00 | — | Toolbar | Search (code/name/lecturer), status filter (default Pending), urgency filter chips (All/Urgent/Normal), group filter (None/Course/Lecturer), week filter, Reset Filters |
| 2026-08-06 13:00 | — | Table columns | 11 columns: checkbox (35px), # (50px), Requested Timestamp (145px), Lecturer (130px), Course Code & Name (200px), Original Class (170px), Proposed Replacement (170px), Students (70px), Urgency (90px), Status (130px), Actions (140px). min-width 1335px |
| 2026-08-06 13:00 | — | Urgency system | Fixed reference date `2026-08-29`. `urgencyLevel(classDate)` → ≤3 days = Urgent (red badge), >3 days = Normal (green badge). `urgencyDays(classDate)` for sort field |
| 2026-08-06 13:00 | — | Default sort | FR 3.2: `requestedAt` asc (FIFO queue). Reset Filters returns to this default |
| 2026-08-06 13:00 | — | Approve flow | `approveRequest(id)` → `openApproveNotesModal([id])` → summary + optional notes textarea → `confirm()` → `showToast()` with undo → `reviewNextAfterAction()` |
| 2026-08-06 13:00 | — | Reject flow | `openRejectModal(id)` → 5 preset chips + mandatory textarea → `rejectRequest()` → `showToast()` with undo → `reviewNextAfterAction()` |
| 2026-08-06 13:00 | — | Bulk actions | Checkbox column, Select All header, batch bar (fixed below toolbar), `bulkApprove()` with multi-line summary, `bulkReject()` via reject modal |
| 2026-08-06 13:00 | — | Request age | `requestAgeHtml(requestedAt)` → "X days ago" with green/amber/red dot (`age-fresh`/`age-waiting`/`age-stale`) |
| 2026-08-06 13:00 | — | Nav badge | Red badge on "Request Approval" nav link showing Pending count, updated via `updateNavBadge()` |
| 2026-08-06 13:00 | — | Viewed indicator | `viewedIds` Set tracks opened requests; `.row-viewed` adds blue left-border accent |
| 2026-08-06 13:00 | — | Keyboard shortcuts | ArrowUp/Down navigate, Enter opens modal, A/R approve/reject Pending, Escape clears. Paused when modal open |
| 2026-08-06 13:00 | — | Review-next | `reviewNextAfterAction()` auto-opens next Pending after approve/reject |
| 2026-08-06 13:00 | — | Slot validity icons | `slotValidityHtml(r)` → ✓ (green) for valid, ⚠ (red) for conflict with reason. Also inline icon in Proposed Replacement column |
| 2026-08-06 13:00 | — | Toast notifications | All `alert()` replaced with `showToast(message, undoCallback, duration)` from `ui-common.js` |
| 2026-08-06 13:00 | — | Undo stack | Capture `prevStatus` before status change, pass restore function as undo callback. Last action only |
| 2026-08-06 13:00 | — | Animated transitions | Row flash (`row-flash-approved`/`row-flash-rejected`), group expand/collapse (`max-height` transition), filter fade |
| 2026-08-06 13:00 | — | Smart grouping | "Group by" dropdown: None/Course/Lecturer. Collapsible group headers with count badges |
| 2026-08-06 13:00 | — | Mini timeline | `buildTimeline(r)` → 3-step visual lifecycle (Submitted→Viewed→Reviewed) with colored dots and connectors |
| 2026-08-06 13:00 | — | Skeleton loading | `showSkeleton()` → 10 shimmer rows + skeleton cards. 300ms on load, 150ms on filter change |
| 2026-08-06 13:00 | — | Detail modal | 3 sections (Request Info, Original Class, Requested Replacement) + Slot Validity + Reviewed By/At. Timeline prepended. Footer: Pending→Reject+Approve, non-Pending→Close |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | — | New route | `GET /request-approval-ui` → `ui-design-templates.request-approval-UI-design-template` with `activeNav = 'request-approval'` |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | After Replacement Arrangement | Nav link + badge | Added "Request Approval" nav item with `'badge'=>true` flag; rendering adds `<span class="nav-badge" id="navPendingBadge"></span>` inside the link |

### `public/js/ui-common.js`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | Appended | OOP helper promotion | 9 shared helpers promoted from my-request-history: `weekRanges`, `formatDateTime(iso)`, `statusClass(status)`, `isoDayName(iso)`, `formatClassBlock(r)`, `formatReplacementBlock(r)`, `getWeekRange(weekVal)`, `isInWeek(classDate, weekVal)`, `getWeekNumber(iso)` |

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-08-06 13:00 | Lines 465–546 | OOP refactor | Removed 10 page-local helper copies — now resolve from `ui-common.js` via layout. No behavior change |

## Key Design Decisions

- **OOP architecture** — Inheritance (`@extends` layout), composition (`@include` partials), encapsulation (`theme.css` + `ui-common.js` + `mock-data.js`). 9 helpers promoted to shared module; mock data in shared data module
- **11-column layout** — checkbox + 10 data columns. Dropped Type/Venue/Cohort from my-request-history; added checkbox, Lecturer, Urgency, Actions
- **Fixed urgency reference** — `2026-08-29` keeps badges deterministic in mock data (real `new Date()` would compute all entries as "Normal" in July)
- **In-memory state simulation** — Approve/reject modify `MockData.approvalRequests` in-memory for demo purposes; undo restores previous status. No persistence
- **Toast over alert** — Non-blocking notifications via shared `showToast()` from `ui-common.js`; undo restores previous state within 5s window
- **3-layer Escape handler** — Single keydown handler closes topmost modal: approveNotes → rejectReason → detail
- **Skeleton on load/filter** — 300ms initial, 150ms on filter change; reuses `.skeleton`/`.skeleton-shimmer` from `theme.css`
