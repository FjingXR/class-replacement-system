# Review Log: phase2-template-migration

## Batch 1 Round 1 — 2026-08-10 17:42
### 🔴 Fixed
 - Corrected `buildTable` counts — only `replacement-home` has `buildTable()`, others use `renderTable()`/`renderCards()`
 - Added missing inline helpers: `formatShortDate`, `computeWeek`, `weekRangeLabel`, `fmtShort`
 - Added migration targets table mapping inline helpers to shared equivalents
 - Clarified that `formatDateTime()` calls are already shared (global alias)
### 🟡 Addressed
 - Added line numbers for each inline helper for implementer reference
### 🔴 Outstanding
 - (none)

## Batch 1 Round 2 — 2026-08-10 17:45
### 🔴 Fixed
 - (none — all previous fixes verified)
### 🟡 Addressed
 - (none)
### 🔴 Outstanding
 - (none)

---
**proposal.md is now FROZEN**

## Batch 2 Round 1 — 2026-08-10 17:50
### 🔴 Fixed
 - (none)
### 🟡 Addressed
 - Noted `weekRangeLabel` uses `weekRanges` array (source of truth) rather than recomputing from semester start
### 🔴 Outstanding
 - (none)

## Batch 4 Round 1 — 2026-08-10 17:55
### 🔴 Fixed
 - Unfroze proposal.md to update "Shared files" section — `ui-common.js` will be extended with new static methods (additive, non-breaking)
 - Made page changelog updates mandatory in Task 6 (per AGENTS.md standing rules)
 - Split Task 5 into sub-tasks for ≤2-hour granularity
### 🟡 Addressed
 - (none)
### 🔴 Outstanding
 - (none)

---
**proposal.md is now RE-FROZEN**
**tasks.md is now FROZEN**
