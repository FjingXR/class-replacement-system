# Design: Phase 2 — Template Migration to Shared OOP Classes

## Technical Approach

Migrate inline helpers in 4 templates to use shared classes from `ui-common.js`. Each template is migrated independently — no cross-template dependencies.

## Migration Details

### 1. `replacement-home-UI-design-template.blade.php`

**Inline helpers to remove:**

| Helper | Lines | Replacement |
|--------|-------|-------------|
| `computeWeek(isoDate)` | 270-277 | `getWeekNumber(iso)` from `ui-common.js` |
| `weekRangeLabel(weekNum)` | 279-292 | New `DateHelper.weekRangeLabel(weekNum)` static method |
| `buildTable()` | 299-392 | Refactor to use `HtmlBuilder` for row construction |
| `renderCards()` | 394-450 | Refactor to use `HtmlBuilder` for card construction |

**Data flow change:**
- Before: `computeWeek(c.date)` → inline calculation
- After: `getWeekNumber(c.date)` → shared function using `weekRanges` array

**New shared method needed:**
```javascript
// Add to DateHelper class in ui-common.js
static weekRangeLabel(weekNum) {
    // Uses weekRanges from ui-common.js to build label
    // Handles responsive width check internally
}
```

### 2. `request-approval-UI-design-template.blade.php`

**Inline helpers to remove:**

| Helper | Lines | Replacement |
|--------|-------|-------------|
| `formatShortDate(iso)` | 553-558 | `DateHelper.formatDate(iso)` (already exists) |
| `renderTable()` | 882-893 | Refactor to use `HtmlBuilder` for row construction |
| `renderCards()` | 894-950 | Refactor to use `HtmlBuilder` for card construction |

**Note:** `formatShortDate()` is functionally identical to `DateHelper.formatDate()` — same output format.

### 3. `my-request-history-UI-design-template.blade.php`

**Inline helpers to remove:**

| Helper | Lines | Replacement |
|--------|-------|-------------|
| `renderTable()` | 560-745 | Refactor to use `HtmlBuilder` for row construction |
| `renderCards()` | 747-810 | Refactor to use `HtmlBuilder` for card construction |

**Note:** Already uses `HtmlBuilder.classBlock()` and `HtmlBuilder.replacementBlock()` — extend pattern to table rows.

### 4. `student-my-timetable-UI-design-template.blade.php`

**Inline helpers to remove:**

| Helper | Lines | Replacement |
|--------|-------|-------------|
| `fmtShort(d)` | 61-64 | New `DateHelper.fmtShort(d)` static method |

**New shared method needed:**
```javascript
// Add to DateHelper class in ui-common.js
static fmtShort(d) {
    return String(d.getDate()).padStart(2, '0') + ' ' + 
           d.toLocaleString('en', { month: 'short' });
}
```

## Shared Class Extensions

### DateHelper additions (ui-common.js)

```javascript
static fmtShort(d) {
    return String(d.getDate()).padStart(2, '0') + ' ' + 
           d.toLocaleString('en', { month: 'short' });
}

static weekRangeLabel(weekNum) {
    const range = weekRanges.find(w => w.value === weekNum);
    if (!range) return 'Week ' + weekNum;
    const startParts = range.start.split('-');
    const endParts = range.end.split('-');
    const start = new Date(startParts[0], startParts[1] - 1, startParts[2]);
    const end = new Date(endParts[0], endParts[1] - 1, endParts[2]);
    if (window.innerWidth <= 768) {
        return 'Week ' + weekNum + ' \u00B7 ' + DateHelper.fmtShort(start) + ' ~ ' + DateHelper.fmtShort(end);
    }
    return 'Week ' + weekNum + ' \u00B7 ' + DateHelper.fmt(start) + ' ~ ' + DateHelper.fmt(end);
}
```

## Migration Strategy

1. **Add new DateHelper methods** to `ui-common.js` first (`fmtShort`, `weekRangeLabel`)
2. **Migrate templates one at a time** in order of complexity:
   - `student-my-timetable` (1 helper, simplest)
   - `request-approval` (3 helpers, moderate)
   - `my-request-history` (2 helpers, moderate)
   - `replacement-home` (4 helpers, most complex)
3. **Verify each template** loads without console errors before proceeding
4. **Run full test suite** after all migrations complete

## Dependencies

- `ui-common.js` — `DateHelper`, `HtmlBuilder`, `getWeekNumber()`, `weekRanges`
- `mock-data.js` — read-only, no changes needed

## Risk Mitigation

- Each template migrated independently — no cascading failures
- New `DateHelper` methods are additive — no breaking changes to existing code
- `renderTable()`/`renderCards()` refactored in-place — same function signatures preserved
