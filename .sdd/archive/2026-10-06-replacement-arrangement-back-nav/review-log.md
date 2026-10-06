# Review Log: replacement-arrangement-back-nav

## Archive Note — 2026-10-06

### 🔴 Fixed
- (none)

### ✅ Applied — all tasks complete, live-verified
- `BackNavigator` class live at `public/js/ui-common.js:2729` — route map: replacement-home / my-request-history / venue-timetable / my-timetable, default `/replacement-home-ui`; the `venue-timetable` branch preserves `code` + `cohort` params so the venue page's "Booking for:" banner survives the round-trip.
- Source pages emit `from=` (`my-request-history` blade:230, `replacement-home` blade:445, `venue-timetable` blade:1009); the arrangement back button calls `BackNavigator.navigate()` (arrangement blade:1907/1911) behind the unsaved-selections confirm modal.
- Final task "Test each source page navigates correctly" — **performed 2026-10-06 via Playwright, 6/6 PASS**: `getBackUrl()` correct for all 4 `from` values + default fallback; real `.back-btn` click (no selections) navigated to `/my-request-history-ui`.

### 🟡 Addressed
- (none)

### 🔴 Outstanding
- (none)
