# Review Log: replacement-approval-status

## proposal.md Round 1 — 2026-08-03 11:10

### 🔴 Fixed
- None

### 🟡 Addressed
- Fixed `proceed()` line reference: was "lines 1580-1620", corrected to "line 1378"
- Fixed `showToast()` line reference in ui-common.js: was "lines 368-396", corrected to "lines 387-410"
- Fixed call site count: was "7 call sites", corrected to "6 call sites" (line 1528 is the definition, not a call)
- Added `.toast` CSS cleanup (lines 758-772) to DRY scope — was missing from Files Modified table

### 🔴 Outstanding
- None

## design.md Round 1 — 2026-08-03 11:25

### 🔴 Fixed
- None

### 🟡 Addressed
- Added semantic mapping section explaining page-local vs shared showToast callback differences
- Clarified that all 6 call sites are compatible with shared version's semantics
- Documented replacement-home showToast signature bug as known issue (out of scope)

### 🔴 Outstanding
- None

## tasks.md Round 1 — 2026-08-03 11:30

### 🔴 Fixed
- None

### 🟡 Addressed
- None

### 🔴 Outstanding
- None
