# Review Log: timetable-print

## proposal.md Round 1 — 2026-08-09 00:30

### 🔴 Fixed
- Print button would leak to 4 non-timetable pages (Replacement Home, Request Approval, My Request History, Replacement Arrangement) — added `$showPrintBtn` parameter to conditionally render button
- Missing button position specification — clarified in proposal (after week nav arrows, before week selector dropdown)
- No mention of dark mode print behavior — added "force CSS custom property overrides" to print output description
- No acceptance criteria — added 5 test scenarios

### 🟡 Addressed
- Button should respect `$disabled` state when nav is disabled (Cohort Timetable) — noted in proposal scope

## proposal.md Round 2 — 2026-08-09 00:35

### 🔴 Fixed
- (none)

### 🟡 Fixed
- Button position still missing from proposal — added placement detail (after arrows, before dropdown)
- Disabled state behavior unspecified — added that print button is disabled when week nav is disabled

## design.md Round 1 — 2026-08-09 00:40

### 🔴 Fixed
- Missing screen styles for `.print-btn` — added full CSS block with sizing, padding, border, hover/active/disabled states
- `data-time` attribute approach unspecified — specified exact JS change for all 3 templates
- CohortTimetable missing `data.name`/`data.venue` attributes — added fix for all 3 templates, noting this also fixes existing hover tooltip bug

### 🟡 Addressed
- Print `::after` content scope mismatch — noted that `::after` adds venue+time as supplementary detail, primary content (code, name) already in block
- `.week-chip` and `#toastBar` selectors not verified — noted as defensive/inherited from explore-brief
- Template line numbers are fragile — noted implementer should verify current lines

## design.md Round 2 — 2026-08-09 00:45

### 🔴 Fixed
- Print button would appear in print output — added `.print-btn` to hidden elements list, removed the separate "show print button in print" rule

### 🟡 Addressed
- (none)

## tasks.md Round 1 — 2026-08-09 00:50

### 🔴 Fixed
- (none)

### 🟡 Addressed
- Task 9 line number inconsistency (line ~49 vs ~449) — cosmetic, won't cause rework
