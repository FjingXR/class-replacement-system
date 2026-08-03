## proposal.md Round 1 — 2026-07-05

### 🟡 Addressed
- Sort specification: Hint text now explicitly lists the 3 sortable columns ("Click column headers to sort (Requested Time, Course Code, Date)")
- Backend contradiction: Out-of-scope reworded to exclude "database persistence, API endpoints, or server-side business logic" while acknowledging the trivial route
- No. pagination: Clarified as global sequence (1–20 across all pages)
- Live result count: Added placement (right side of toolbar) and format ("Showing X of Y results")
- Summary stat cards: Added explicit token mapping for each card (—color-error, —color-secondary, —color-tertiary, —color-on-primary-container)
- Modal fields per status: Added convention table specifying which fields are null per status (Pending, Approved, Rejected, Cancelled, Completed)
- Route response: Added inline `Route::get()` closure with view name
- Empty state wording: Added separate message for filtered-empty vs fully-empty state
- Nav item href: Updated to match the active page URL (`/my-request-history-ui`)

## proposal.md Round 2 — 2026-07-05

### 🔴 Fixed
- Cancelled reviewedBy/reviewedAt field convention: Modal field spec now reads "null for Pending and Cancelled, populated for Approved/Rejected/Completed" — consistent with the mock data convention
- Removed "Cancelled" from the "populated" list in the modal fields line

### 🟡 Addressed (declarative additions before freeze)
- Inline rejection reason: Clarified as "muted subtitle below the badge for Rejected entries only"
- Summary stat cards: Clarified counts reflect unfiltered full dataset
- Pagination reset: Added explicit rule — filters/search/sort reset to page 1

### ✅ PASS — proposal.md is frozen.

## design.md Round 1 — 2026-07-05

### 🔴 Fixed
- Default sort: Added state variable declaration `let sortState = { field: 'requestedAt', dir: 'asc' }` in §5

### 🟡 Addressed
- Summary cards unfiltered: Added explicit note in §9 — counts computed from full `mockRequests` array, not from filtered/paginated data
- Mock data distribution: Added distribution spec (6/5/4/3/2, 15 unique courses) in §2
- Sort arrow indicator: Added `.sort-arrow` rendering spec in §5 — ▲/▼ on active column only
- "Tracked via a flag": Replaced with direct "set `currentPage = 1`" language in §7
- 'L'/'T' mapping: Added `classType` rendering note in §5 row 4
- Cancelled/Completed cards: Added note in §9 clarifying they're counted in Total but have no card

### 🟡 Addressed
- CTA button URL: Changed from `/replacement-home-ui` to `/replacement-arrangement` (correct submission page)

### ✅ PASS — design.md is frozen.

## tasks.md Round 1 — 2026-07-05

### 🟡 Addressed
- Date range filter: Added "skip if empty" guard in Task 3 step 4
- Modal field population: Added cross-reference to design §8 with conditional visibility notes in Task 4
- Summary card color mapping: Replaced "4 color variants" with explicit token mapping in Task 2
- Sequential No. formula: Added `((currentPage - 1) * pageSize) + rowIndex + 1` in Task 3 step 10
- Result count format: Added "Showing X of Y results" format in Task 3
- Sort hint element: Added checkbox for sort hint HTML div in Task 1

### ✅ PASS — tasks.md is frozen.

## design.md Round 2 (Post-freeze) — 2026-07-20

### 🔴 Removed: Date range filter — replaced with Week Dropdown

- Toolbar: removed From/To date inputs, added `<select id="weekFilter">` with static options:
  - All Weeks, Week 1: 31 Aug – 6 Sep, Week 2: 7 Sep – 13 Sep, Week 3: 14 Sep – 20 Sep, Week 4: 21 Sep – 27 Sep
- ASCII toolbar diagram in §4 updated to show Week dropdown instead of From/To
- Data flow §7 changed from `classDate >= dateFrom && classDate <= dateTo` to `classDate >= weekStart && classDate <= weekEnd`
- Responsive §11: "date inputs" → "week dropdown"
- CSS class: `date-input` → `week-dropdown`
- Consistency: added "ensure all visual tokens match replacement-home-ui" to Task 2

### 🟡 Cross-doc audit (all clean after fixes):
- proposal.md line 19 ✓ (Week Dropdown)
- proposal.md line 94 ✓ ("replaced by week filter + search")
- design.md line 147 ✓ (Week dropdown in Group A)
- design.md design.md line 76-77 ✓ (ASCII toolbar)
- design.md line 273 ✓ ("week dropdown")
- tasks.md line 18 ✓ (week-dropdown CSS)
- tasks.md line 43 ✓ (week filter in pipeline)
- tasks.md line 59 ✓ (week filter onchange handler)
