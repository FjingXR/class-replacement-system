# Review Log — replacement-home-ux-features

## proposal.md Round 1 — 2026-08-03 05:45

### 🔴 Fixed
- F2 Reset button location undefined → Added: "small icon button (✕) in `toolbar-right`, next to `#resultCount`"
- F6 mobile bottom sheet behavior missing → Added: mobile conversion spec (align-items: flex-end, slide-up, 80vh, drag handle)
- F5 mobile scope undefined → Added: "Desktop only (≤768px: shortcut hint hidden, card tap replaces row focus + Enter)"
- F1 pageSize / pageState structure → Added: "Refactor `pageSize` from standalone `const` to `pageState.pageSize = 10`"

### 🟡 Addressed
- F2 filter state shape omits reason field → Added note: "reason filter intentionally excluded — was removed on 2026-07-20"
- F5 Escape key handler conflicts → Added: "checks modal state first; if modal open, close modal and stopPropagation()"
- F6 UX trade-off (1 click → 2 clicks) → Added justification in proposal
- F5 id field needed → Added: "focusedRowIndex tracks position in currentFiltered[] — no id field needed"

### 🔴 Outstanding
- None

## design.md Round 1 — 2026-08-03 05:55

### 🟢 Pass
- No blocking issues found

### 🟡 Addressed (for implementation)
- F5 keyboard navigation edge cases (empty array, page transition clamping) → implementer to handle during build
- F6 modal content template → implementer to follow existing `.modal-field` pattern
- F2 loadFilters() must be called after populateWeekDropdown() → noted in data flow
- F1 "All" option → implementer to use `currentFiltered.length`
- F6 card view on mobile → implementer to open Quick View Modal instead of navigating directly

### 🔴 Outstanding
- None

## tasks.md Round 1 — 2026-08-03 06:05

### 🟢 Pass
- No blocking issues found

### 🟡 Addressed (for implementation)
- F5 Escape key modal state check → implementer to check `.modal-overlay.show`
- F1 "All" option → implementer to use `currentFiltered.length`
- F6 row click vs button click → implementer to add `event.stopPropagation()` on button

### 🔴 Outstanding
- None
