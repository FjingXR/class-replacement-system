# Design: Replacement Approval Status

## Technical Approach

Extend the shared toast system in `ui-common.js` with optional link parameters, then refactor the replacement-arrangement page to use the shared toast (removing its local duplicate) and call the enhanced toast after submission.

### Architecture Decision

**Why extend `showToast()` instead of creating a new function?**

The shared `showToast()` in `ui-common.js` already handles:
- Message display via `#toastBar` in `ui-template.blade.php`
- Auto-dismiss with configurable duration
- Optional undo button (hidden when callback is null)
- Debouncing (clears previous toast timer)

Adding `linkText` and `linkUrl` as optional parameters preserves backward compatibility — all existing callers continue to work unchanged. This follows Rule 7 (promote-on-3rd-duplication).

**Why remove the page-local `showToast()`?**

The replacement-arrangement page defines its own `showToast(message, callback)` at line 1528 that:
- Creates a new `<div class="toast">` element (not the shared `#toastBar`)
- Has a fixed 2-second dismiss (not configurable)
- Lacks undo button support
- Has separate `.toast` CSS (lines 758-772) that styles a different element

This is a DRY violation — the same functionality exists in two places. Removing it and migrating to the shared version ensures consistency across all pages.

---

## Data Flow

### Current Flow (after Proceed confirmation)

```
User clicks "Proceed"
  → proceed() shows confirmation modal with slot list
  → User clicks "Confirm"
  → showToast('Replacement request submitted.', null)
  → Page-local toast appears (center of screen, 2s auto-dismiss)
  → Page stays on timetable grid
```

### New Flow

```
User clicks "Proceed"
  → proceed() shows confirmation modal with slot list
  → User clicks "Confirm"
  → Build slot details string from selectedSlotsByVenue
  → showToast(
      '✓ Submitted — Pending Approval · B103 · Mon, 4 Aug · 10:00-12:00',
      null,
      5000,
      'View →',
      '/my-request-history-ui'
    )
  → Shared #toastBar shows with message + "View →" link
  → Toast auto-dismisses after 5 seconds
  → User can click "View →" to navigate to /my-request-history-ui
```

### Toast Rendering (ui-template.blade.php)

```html
<!-- Before -->
<div id="toastBar" class="toast-bar">
    <span class="toast-message"></span>
    <button class="toast-undo" style="display:none">Undo</button>
</div>

<!-- After -->
<div id="toastBar" class="toast-bar">
    <span class="toast-message"></span>
    <a class="toast-link" href="#" style="display:none"></a>
    <button class="toast-undo" style="display:none">Undo</button>
</div>
```

### Toast JS (ui-common.js)

```javascript
// Before
function showToast(message, undoCallback, duration = 5000) { ... }

// After (backward compatible)
function showToast(message, undoCallback, duration = 5000, linkText, linkUrl) {
    // ... existing logic ...
    
    const linkEl = bar.querySelector('.toast-link');
    if (linkText && linkUrl) {
        linkEl.textContent = linkText;
        linkEl.href = linkUrl;
        linkEl.style.display = 'inline-block';
    } else {
        linkEl.style.display = 'none';
    }
    
    // ... rest of existing logic ...
}
```

---

## Slot Details String Builder

The enhanced toast needs to show submitted slot details. Build a summary string from the current selections:

```javascript
function buildSubmissionToastMessage() {
    const slots = [];
    for (const venue in selectedSlotsByVenue) {
        for (const week in selectedSlotsByVenue[venue]) {
            for (const slot of selectedSlotsByVenue[venue][week]) {
                const dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                const weekLabel = weekData[week].label;
                const dayData = weekData[week].days[slot.day];
                const dayName = dayNames[slot.day];
                const dateStr = dayData.date.replace(/ \d{4}$/, ''); // "4 Aug"
                const timeRange = to12h(slot.hour) + '–' + add30min(slot.hour);
                slots.push(`${venue} · ${dayName}, ${dateStr} · ${timeRange}`);
            }
        }
    }
    return '✓ Submitted — Pending Approval · ' + slots.join(' | ');
}
```

---

## Semantic Mapping: Page-Local vs Shared showToast

**Critical difference:** The page-local `showToast(message, callback)` and shared `showToast(message, undoCallback, duration)` have different callback semantics:

| Aspect | Page-Local (line 1528) | Shared (ui-common.js) |
|--------|------------------------|----------------------|
| Callback trigger | Fires **after toast dismisses** (post-action) | Fires **when Undo button clicked** (undo action) |
| DOM strategy | Creates new `<div class="toast">` each call | Uses single reusable `#toastBar` element |
| Positioning | Bottom-center | Bottom-left |
| Undo button | None | Shows when undoCallback provided |

**Resolution:** After analyzing all 6 call sites, the callbacks are actually **undo actions** (restore selections), not post-action callbacks. The shared showToast's semantics are correct:

| Line | Current Call | Callback Purpose | Shared Version Behavior |
|------|--------------|------------------|------------------------|
| 1292 | `showToast('This slot overlaps with your ' + conflict + ' class')` | None | ✓ Works — no undo needed |
| 1398 | `showToast('Replacement request submitted.', null)` | None | ✓ Works — will be replaced with enhanced toast |
| 1419 | `showToast('All selections cleared.', function() { restore })` | Undo restore | ✓ Works — Undo button fires restore |
| 1444 | `showToast('Selections cleared.', function() { restore })` | Undo restore | ✓ Works — Undo button fires restore |
| 1474 | `showToast('Selections cleared.', function() { restore })` | Undo restore | ✓ Works — Undo button fires restore |
| 1592 | `showToast('Selection undone')` | None | ✓ Works — no undo needed |

**Note:** The page-local version's "post-action" behavior was actually a bug — the restore callback fires after the toast disappears, giving users no way to trigger it. The shared version's "Undo button" approach is the correct UX pattern.

---

## CSS Changes

### Remove (page-local `.toast` CSS)

Lines 758-772 in `replacement-arrangement-UIdesign-template.blade.php`:

```css
/* REMOVE THIS BLOCK */
.toast {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    background: var(--color-primary);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    z-index: 10000;
    opacity: 1;
    transition: opacity 0.3s ease;
    pointer-events: auto;
}
```

### Add (toast link styling in `theme.css`)

```css
.toast-link {
    color: var(--color-primary);
    text-decoration: underline;
    font-weight: 600;
    margin-left: 0.75rem;
    cursor: pointer;
    white-space: nowrap;
}

.toast-link:hover {
    color: var(--color-primary-dark);
}
```

---

## Migrated Call Sites

All 6 call sites in `replacement-arrangement-UIdesign-template.blade.php`:

| Line | Current Call | Migration Notes |
|------|--------------|-----------------|
| 1292 | `showToast('This slot overlaps with your ' + conflict + ' class')` | No change — 1 arg, shared version handles this |
| 1398 | `showToast('Replacement request submitted.', null)` | **Replace** with enhanced toast call |
| 1419 | `showToast('All selections cleared.', function() { ... })` | No change — 2 args, shared version handles this |
| 1444 | `showToast('Selections cleared.', function() { ... })` | No change — 2 args, shared version handles this |
| 1474 | `showToast('Selections cleared.', function() { ... })` | No change — 2 args, shared version handles this |
| 1592 | `showToast('Selection undone')` | No change — 1 arg, shared version handles this |

**Behavior change:** All call sites will now use 5-second auto-dismiss (shared default) instead of the old 2-second local dismiss. This is acceptable — the longer duration gives users more time to read the message.

---

## Mobile Considerations

- Toast bar already responsive in `ui-template.blade.php` (max-width, flex-wrap)
- "View →" link may wrap on narrow screens — acceptable with `white-space: nowrap` removed
- No new mobile-specific CSS needed

---

## Known Issues (Out of Scope)

**`replacement-home-UI-design-template.blade.php` showToast signature mismatch:**

Lines 593, 598, 606, 611 pass strings as the second argument:
```javascript
showToast('Filters saved', 'success');      // line 593
showToast('No saved filter found', 'warning'); // line 598
showToast('Filters applied', 'success');    // line 606
showToast('Saved filter deleted', 'info');  // line 611
```

The shared `showToast(message, undoCallback, duration)` expects `function|null` for the second parameter. Passing `'success'` (a truthy string) causes the Undo button to appear with a non-functional handler.

**This is a pre-existing bug** — not introduced by this change. Should be fixed separately (change all to `null` as second argument).

---

## Dependencies

| Dependency | Type | Notes |
|------------|------|-------|
| `ui-common.js` showToast() | Shared | Extending with optional params |
| `ui-template.blade.php` #toastBar | Shared | Adding link element |
| `theme.css` | Shared | Adding `.toast-link` style |
| `replacement-arrangement-UIdesign-template.blade.php` | Page | Removing local duplicate, migrating call sites |

---

## Acceptance Criteria

- [ ] After confirming submission, toast shows "✓ Submitted — Pending Approval"
- [ ] Toast includes slot details: venue code, day, date, time range
- [ ] Toast has "View →" link that navigates to `/my-request-history-ui`
- [ ] Toast auto-dismisses after 5 seconds
- [ ] Existing undo functionality still works (backward compatible)
- [ ] Page-local `showToast()` function removed
- [ ] Page-local `.toast` CSS removed
- [ ] All 6 call sites use shared `showToast()` from `ui-common.js`
- [ ] Mobile: toast displays correctly at 768px breakpoint
