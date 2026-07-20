# Changelog — Lecturer My Request History

## Files Changed

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 18:00 | — | Created page | New Blade template with full top nav (Replacement History active), toolbar (status + week dropdowns, search), 12-column sortable table, pagination (10/page), 4 summary cards (Total/Approved/Pending/Rejected), status badge modal, 2-variant empty state, dark/light theme toggle |
| 2026-07-20 18:00 | — | Mock data | 20 entries (6 Pending, 5 Approved, 4 Rejected, 3 Completed, 2 Cancelled), 15 unique courses (5 reused as L+T pairs), per-status field conventions (Pending/Cancelled null reviewer, Approved/Completed populated replacement, Rejected has rejectionReason) |
| 2026-07-20 18:00 | — | Week dropdown | Static week ranges (Week 1–4: 31 Aug – 27 Sep) replacing date range From/To inputs, filters by `classDate` within selected week bounds |
| 2026-07-20 18:00 | — | Design consistency | All visual tokens match `replacement-home-ui`: same nav bar, toolbar shape, table zebra/hover, badge rounding, pagination, summary card layout, modal overlay, empty state |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-20 18:00 | Line 28 (new) | New route | Added `Route::get('/my-request-history-ui', ...)` returning view `ui-design-templates.my-request-history-UI-design-template` |
