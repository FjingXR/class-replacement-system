# Review Log: ui-js-oop-refactor

## proposal.md Round 1 — 2026-08-10 16:30

### 🔴 Fixed
- Count mismatch: explore-brief said "6 classes/namespaces" but listed 7 — fixed brief to say 7
- `formatClassBlock`/`formatReplacementBlock` mapping unclear — added explicit "Functions Replaced" column with old function names

### 🟡 Addressed
- Open questions (auto-save, events vs callback) resolved in proposal
- Acceptance criterion #5 narrowed to "pages that currently use" patterns
- Missing utility functions explicitly listed as out-of-scope

### 🔴 Outstanding
- None

---

## proposal.md Round 2 — 2026-08-10 16:35

### 🔴 Fixed
- `initWeekKeyboardShortcuts` breakage — added to WeekNavigator scope
- Blade partial `ui-week-nav` onclick handlers — added to Impact, documented `window.weekNav` exposure

### 🟡 Addressed
- Function name mismatches (`prev`→`prevWeek`, `loadWeek`→`loadSavedWeek`, etc.)
- `weekFilterChanged`/`prevWeekFilter`/`nextWeekFilter` added to out-of-scope

### 🔴 Outstanding
- None

---

## proposal.md Round 3 — 2026-08-10 16:40

### 🟡 Addressed
- `updateWeekArrowState` added to out-of-scope
- `updateWeekProgress`→`updateProgress` naming aligned
- Per-template patterns annotated (prevWeek/nextWeek defined in templates, not ui-common.js)
- Keyboard shortcut dual-purpose clarified (table pages keep standalone)

### 🔴 Outstanding
- None

### ✅ Verdict: Proposal frozen

---

## design.md Round 1 — 2026-08-10 16:45

### 🔴 Fixed
- ModalController overlay click listener leak — stored `_onOverlayClick` reference, removed in `close()`

### 🟡 Addressed
- HtmlBuilder dependency note added (getWeekNumber, statusClass as globals)
- WeekNavigator template migration steps documented
- `_updateArrows()` added to all navigation methods
- TableController `compareBy(va, vb)` method added
- `_buildTimetable()` typeof guard made explicit

### 🔴 Outstanding
- None

---

## design.md Round 2 — 2026-08-10 16:50

### 🔴 Fixed
- `_updateSubtitle()` / `_updateProgress()` now use `this._currentWeek`, `this._semester`, `this._weekData` instead of globals
- `_updateArrows()` implementation details added (reads `weekSelect`/`weekFilter`)

### 🟡 Addressed
- `selectWeek()` doesn't call `_updateSelect()` — documented as intentional (select already updated by user click)

### 🔴 Outstanding
- None

---

## design.md Round 3 — 2026-08-10 16:55

### 🔴 Fixed
- ModalController `open()` now calls `this.close()` at top to clean up previous listeners
- `jumpToToday()` typeof guard already correct in `_buildTimetable()`
- Proposal updated: `updateWeekArrows` moved to out-of-scope (design delegates to standalone function)

### 🟡 Addressed
- `initKeyboard()` guard added (`_keyboardBound` flag)
- `_updateArrows()` reads `weekSelect` OR `weekFilter` — documented

### 🔴 Outstanding
- None

---

## design.md Round 4 — 2026-08-10 17:00

### 🟡 Addressed
- HtmlBuilder dependency note corrected: `isoDayName` → `DateHelper.isoDayName()`
- Public aliases added: `saveWeek()`, `loadSavedWeek()`, `initWeekKeyboardShortcuts()`, `updateWeekSubtitle()`, `updateWeekProgress()`
- `_updateArrows()` now computes from `this._currentWeek` instead of DOM state
- `semester` getter added, template migration updated to use `window.weekNav.semester`

### 🔴 Outstanding
- None

### ✅ Verdict: Design frozen

---

## tasks.md Round 1 — 2026-08-10 17:05

### 🟡 Addressed
- Task 13 split into Task 16 (core) and Task 17 (UI helpers) — better size for 2-hour guideline
- Task 12 split into per-template tasks (12-15) — matches pattern of timetable migration tasks

### 🔴 Outstanding
- None

### ✅ Verdict: Tasks frozen
