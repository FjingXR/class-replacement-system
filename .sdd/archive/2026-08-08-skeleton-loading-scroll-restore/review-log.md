# Review Log: skeleton-loading-scroll-restore

## proposal.md Round 1 — 2026-08-08 20:51

### 🔴 Fixed
- Rewrote proposal to distinguish existing code from genuinely new code (was framing entire features as "add" when most already exists)
- Aligned sessionStorage key format: existing code uses `scroll_` (underscore) — proposal now documents this as-is, no changes to existing signatures
- Fixed `prefers-reduced-motion` framing — documented as a gap to fill, not an existing feature

### 🟡 Addressed
- Resolved 3 open questions from explore-brief.md: timing=400ms, key format=`scroll_` (existing), injection=innerHTML replacement (existing pattern)
- Listed all 3 target pages explicitly (my-request-history, replacement-home, request-approval)
- Confirmed `withSkeleton()` signature: `(callback, delay=400)`
- Documented existing `showSkeleton()` default count=5 vs needed count=10

### 🔴 Outstanding
- (none — all issues resolved)

---

## proposal.md Round 2 — 2026-08-08 21:05

### 🔴 Fixed
- (none — no 🔴 issues in this round)

### 🟡 Addressed
- All 3 previous 🔴 issues correctly resolved
- `withSkeleton` error-path behavior deferred to design.md (non-blocking)
- Nav click clear consistency noted for design.md
- Timing ambiguity ("after render") noted for design.md

### 🔴 Outstanding
- (none)

### Verdict: ✅ Proposal frozen — proceed to design.md

---

## design.md Round 1 — 2026-08-08 21:15

### 🔴 Fixed
- Added `showSummarySkeleton()`/`hideSummarySkeleton()` — promotes request-approval's local summary skeleton logic to shared ui-common.js
- Clarified `withSkeleton()` as imperative (executes immediately), not factory — added full implementation with sync/async support and try/finally error handling
- Documented that existing debounced scroll auto-save covers beforeunload case — no explicit handler needed; data flow diagram updated to show scroll event → debounced save
- Fixed CSS values to match actual codebase (heights, margins, border-radius, keyframe values)

### 🟡 Addressed
- Added `container` parameter to `withSkeleton()` signature
- Added page wiring pattern showing full container reference (`document.getElementById('tableBody')`)
- Documented that pages without `data-page` attribute get no scroll restoration (intentional guard)

### 🔴 Outstanding
- (none)

### Verdict: ✅ Design frozen — proceed to tasks.md

---

## tasks.md Round 1 — 2026-08-08 21:25

### 🔴 Fixed
- Fixed `hideSummarySkeleton()` design bug — now cleans up `dataset.original` marker only, does NOT restore (avoids overwriting real data with stale "0")
- Added `window.scrollTo(0, 0)` to filter/search wiring pattern in design.md
- Added `window._scrollToTop` exposure in `initScrollRestore()`
- Updated Task 10 to scope scroll restore testing to my-request-history + replacement-home only (request-approval has no `data-page` attribute)
- Added note about `<div>` inside `<tbody>` invalid HTML trade-off in Tasks 7-9

### 🟡 Addressed
- Fixed misleading "Replace direct `showSkeleton()` call" wording → "Wrap `renderTable()` call with"
- Added `hideSummarySkeleton()` call to all 3 page-wiring tasks (was missing from Tasks 8-9)
- Added line number reference for ui-template.blade.php DOMContentLoaded handler

### 🔴 Outstanding
- (none)

### Verdict: ✅ Tasks frozen — ready for implementation

---

## proposal.md Grill — 2026-08-08 23:02

### 🔴 Fixed
- Signature mismatch: proposal had `withSkeleton(callback, delay=400)` but design has 4 params — fixed to `(callback, container, count=10, delay=400)`
- Missing functions: `showSummarySkeleton()`/`hideSummarySkeleton()` not listed in proposal — added to "What This Change Adds" table
- "Default count=10" framing as a gap — removed (existing `showSkeleton()` already accepts `count` param; just pass `count=10` at call site)

### 🟡 Addressed
- "Wiring Changes" table now shows correct `withSkeleton()` pattern (not direct `showSkeleton()` + `hideSkeleton()`)
- Impact table updated: 3 functions → 5 functions
- Tasks 7-9 updated with exact callback pattern: `withSkeleton(() => { renderTable(data); hideSummarySkeleton(); }, tableBody, 10, 400)`
- Added timing note: `showSummarySkeleton()` before `withSkeleton()`, `hideSummarySkeleton()` inside callback after `updateSummary()`
- Added guard note: `initScrollRestore()` only runs when `data-page` attribute exists on `<body>`

### Verdict: ✅ Proposal aligned with design.md — ready for implementation
