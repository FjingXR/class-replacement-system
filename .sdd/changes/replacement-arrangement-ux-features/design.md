# Design: Replacement Arrangement — UX Enhancement Features

## Technical Design

### F1: Keyboard Shortcuts

**State:**
```javascript
let focusedCell = { day: null, hour: null };
```

**New CSS:**
```css
.cell-focused {
    outline: 2px solid var(--color-primary);
    outline-offset: -2px;
}
.help-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
}
.help-card {
    background: var(--color-surface);
    border-radius: 12px;
    padding: 24px;
    max-width: 400px;
    box-shadow: var(--shadow-lg);
}
```

**New JS Functions:**
- `handleKeyDown(e)` — switch on key: ArrowUp/Down/Left/Right (navigate), Enter/Space (toggle), Escape (close modal/clear), Shift+/ (show help)
- `focusCell(day, hour)` — add `.cell-focused` class to target cell
- `unfocusCell()` — remove `.cell-focused` from current cell
- `showHelp()` — display help overlay
- `hideHelp()` — hide help overlay

**Event Listener:**
```javascript
document.addEventListener('keydown', handleKeyDown);
```

---

### F2: Selection Undo (Ctrl+Z)

**State:**
```javascript
let selectionHistory = [];
```

**History Entry Shape:**
```javascript
{ action: 'select'|'deselect', day: di, hour: hi, venue: currentVenue, week: currentWeek }
```

**New CSS:**
```css
.toast {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--color-surface);
    border: 1px solid var(--color-outline);
    padding: 8px 16px;
    border-radius: 8px;
    z-index: 200;
    box-shadow: var(--shadow-md);
}
```

**New JS Functions:**
- `pushHistory(entry)` — push to selectionHistory[]
- `undoSelection()` — pop last entry, reverse action, show toast
- `showToast(message, callback?)` — display toast, auto-dismiss 2s

**Integration:**
- Call `pushHistory()` in `toggleCell()` on both select and deselect
- Ctrl+Z handler in `handleKeyDown(e)`

---

### F3: Progress Indicator

**HTML (add above timetable):**
```html
<div class="progress-wrapper" id="progressWrapper">
    <div class="progress-bar" id="progressBar">
        <div class="progress-fill" id="progressFill"></div>
    </div>
    <span class="progress-text" id="progressText">Selected 0 of 4 slots</span>
</div>
```

**New CSS:**
```css
.progress-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 8px 0;
}
.progress-bar {
    flex: 1;
    height: 8px;
    background: var(--color-outline);
    border-radius: 4px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s, background 0.3s;
    background: var(--color-outline);
}
.progress-text {
    font-size: 12px;
    color: var(--color-on-surface-variant);
    white-space: nowrap;
}
```

**New JS Functions:**
- `updateProgress()` — calculate percentage, update fill width + text + color

**Integration:**
- Call `updateProgress()` in `updateCounter()` (already exists)

---

### F4: Venue Capacity Badge

**Current:** Hardcoded `<option>B103</option>` in HTML

**New:** Dynamic dropdown built from `MockData.venues[]`

**New JS Functions:**
- `buildVenueDropdown()` — iterate MockData.venues, create `<option>` with capacity text, check if capacity < cohort totalStudents, add warning icon

**Option Text Format:**
```
B103 — Tutorial Room (35 seats)
```

**Warning Icon:**
- If venue.capacity < currentCohortStudents → add `⚠` icon after venue name
- CSS: `.venue-warning { color: var(--color-error); margin-left: 4px; }`

**Integration:**
- Call `buildVenueDropdown()` in DOMContentLoaded (replace hardcoded `<option>` elements)

---

### F7: Conflict Warning Toast

**New JS Functions:**
- `checkConflict(dayIndex, hourIndex)` — iterate `MockData.myTimetable.eventsByWeek[currentWeek]`, return conflicting course code or null

**Overlap Logic:**
```javascript
function checkConflict(dayIndex, hourIndex) {
    const events = MockData.myTimetable.eventsByWeek[currentWeek] || [];
    for (const event of events) {
        if (event.di === dayIndex && hourIndex >= event.start && hourIndex < event.end) {
            return event.code;
        }
    }
    return null;
}
```

**Integration:**
- Call `checkConflict()` in `toggleCell()` on selection (not deselection)
- If conflict: `showToast('This slot overlaps with your ' + courseCode + ' class')`
- Reuse toast from F2

---

## Promoted to Shared

No elements promoted — all 5 features are page-specific (keyboard navigation, undo stack, progress bar, venue badge, conflict check are unique to this page's slot-selection workflow).

## Mobile View

- **F1:** Keyboard shortcuts not applicable on mobile (no physical keyboard). Help overlay still accessible via `?` button if needed.
- **F2:** Ctrl+Z not applicable on mobile. Consider adding undo button in toolbar for mobile (future enhancement).
- **F3:** Progress bar full-width on mobile.
- **F4:** Venue dropdown full-width on mobile.
- **F7:** Toast works on mobile (fixed position).
