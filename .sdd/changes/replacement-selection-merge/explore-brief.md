# Explore Brief: Replacement Selection Merge

## Problem

On the Replacement Arrangement page, when a user selects a starting time slot, the system currently selects each 30-minute cell independently (4 separate ✓ cells for a 2-hour class). This looks fragmented and doesn't represent the logical unit — one continuous replacement block.

## Goal

Merge consecutive selected cells into **one continuous event-block** that spans the full required duration, visually matching how event-blocks look on other timetable pages (CohortTimetable, MyTimetable, etc.).

## Scope

- **In scope:** Replacement Arrangement page only (`replacement-arrangement-UIdesign-template.blade.php`)
- **Out of scope:** Other timetable pages (they already render event-blocks correctly via colSpan)
- **Out of scope:** Backend wiring (duration will come from backend later; frontend keeps `?duration=` URL param for now)

---

## Decisions (from grilling session)

### D1: Duration Source
- **Decision:** Keep `?duration=` URL param (default 4 slots = 2 hours)
- **Why:** Already working, simplest for frontend. Backend will pass this when navigating.
- **Rejected:** Reading duration from subject/course mock data (no duration field there)

### D2: Selection Behavior
- **Decision:** Clicking a starting slot auto-selects exactly N consecutive slots (N = `MAX_SELECTION`)
- **Why:** Matches the "exactly the required duration" requirement
- **No partial selections** — user cannot select fewer slots than the duration

### D3: Hover Preview
- **Decision:** Hovering over an available starting slot shows a preview of the full duration block:
  - **Green** if all N slots in the range are available
  - **Red** if any slot in the range is occupied/pending/reserved/Sunday/holiday
  - **Cursor: not-allowed** when any slot in the range is unavailable
- **Why:** User needs to see the full range before committing

### D4: Single Block Only
- **Decision:** Only ONE block can be selected at a time across all venues/weeks
- **If block exists and user clicks new slot:** Show message "Clear current selection first"
- **Why:** Keeps the flow simple; user must clear before selecting elsewhere

### D5: Deselect
- **Decision:** Clicking anywhere on the selected block deselects the ENTIRE block
- **Hover text:** "REMOVE" shows on the entire block on hover
- **Why:** Block is one logical unit — partial deselect doesn't make sense

### D6: Block Visual
- **Decision:** One continuous colored block with:
  - Rounded corners ONLY on outer edges (no rounded corners between cells)
  - No text inside the block (just colored background)
  - Uses `position: absolute` on the first cell, `width` spanning N cells
- **Why:** Matches event-block style on other pages; clean appearance

### D7: Data Structure
- **Decision:** Store block-level entries:
  ```js
  { day: number, startHour: number, endHour: number, venue: string }
  ```
- **Replaces:** Individual `{ day, hour }` pairs in `selectedCells` and `selectedSlotsByVenue`
- **End hour is inclusive** (e.g., `endHour: 7` means the block covers hours 4,5,6,7 = 08:00–10:00)

### D8: Undo History
- **Decision:** Block-level undo — one Ctrl+Z undoes the entire block
- **History entries:**
  ```js
  { action: 'select', day: 0, startHour: 4, endHour: 7, venue: 'B103', week: 3 }
  { action: 'deselect', day: 0, startHour: 4, endHour: 7, venue: 'B103', week: 3 }
  ```
- **Rejected:** Per-cell undo (too granular, confusing for users)

### D9: Selection Summary Card
- **Decision:** Keep the summary card, but show block info:
  ```
  Replacement Slot
  Mon 01 Sep · 08:00 – 10:00
  4 slots · B103
  ```
- **Buttons stay:** "Clear this Page", "Clear ALL", "Proceed"
- **When empty:** Show existing "No time slots selected" empty state

### D10: Conflict Validation
- **Decision:** Before creating the block, validate every cell in the range
- **If any cell is unavailable:** Block preview shows red, cursor becomes not-allowed, click is prevented
- **No partial blocks** — it's all-or-nothing

---

## Cross-Module Data Flows

```
URL param ?duration=
    ↓
MAX_SELECTION = Math.round(duration * 2)
    ↓
buildTimetable() → cellRender callback
    ↓
Each cell gets click handler: toggleCell(di, hi, el)
    ↓
toggleCell() → validate range → create/remove block → update DOM → update summary → save state
    ↓
selectedSlotsByVenue = { venue: { week: [{ day, startHour, endHour }] } }
    ↓
loadCurrentWeek() → deserialize block → re-render merged block
```

## Key Technical Approach

1. **Merged block rendering:** Use `position: absolute` on the first cell's `.cell-content`, set `width` to span N cells. The block covers intermediate cell borders naturally.

2. **Hover preview:** On `mouseenter` of an available cell, compute the range `[hi, hi + MAX_SELECTION - 1]`. Check all cells in range. Apply temporary preview class (green/red) to all cells in range. Remove on `mouseleave`.

3. **colSpan alternative:** NOT using `td.colSpan` because the grid structure is fixed at build time. Absolute positioning is more flexible and doesn't require rebuilding the table.

4. **State persistence:** Serialize/deserialize block-level entries in `selectedSlotsByVenue`. On `loadCurrentWeek()`, re-create the merged block from stored data.

## Open Questions

None — all decisions resolved during grilling.
