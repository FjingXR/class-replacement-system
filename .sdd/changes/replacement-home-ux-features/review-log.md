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
