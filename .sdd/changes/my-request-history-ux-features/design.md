# Design: My Request History — UX Enhancement Features

## Technical Approach

All 7 features are implemented as **additive changes** to the existing Blade template. No refactoring of existing code — only new CSS classes, new JS functions, and new HTML elements appended to existing structures.

## Architecture Decisions

### 1. Rows Per Page (F1)

State change:
```javascript
let rowsPerPage = 10;  // 10 | 25 | 50 | Infinity
let currentPage = 1;
```

Replace hardcoded `pageSize = 10` with dynamic `rowsPerPage`. Add `renderPaginationControls()` that builds the dropdown + info text in the pagination bar. CSS reuses `.filter-select` class.

### 2. Bulk Selection (F2)

State:
```javascript
let selectedIds = new Set();
```

New checkbox column as first `<td>` in each row. Header has "select all" checkbox. Only Pending rows have enabled checkboxes; others are `disabled` with `opacity: 0.4`.

Floating action bar:
```css
.batch-action-bar {
    position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%);
    background: var(--color-primary-container); border: 1px solid var(--color-primary);
    border-radius: var(--radius-md); padding: 12px 24px;
    box-shadow: var(--shadow-lg); z-index: 50;
    display: none; /* shown when selectedIds.size > 0 */
}
```

Batch cancel modal: separate from single-cancel modal. Shows list of selected request IDs + course codes. Confirm → `alert("X requests cancelled.")` → clear selection.

### 3. Request Age Indicator (F3)

Helper function:
```javascript
function calculateAgeClass(requestedAt) {
    const now = new Date();
    const req = new Date(requestedAt);
    const diffMs = now - req;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    if (diffDays < 3) return 'age-green';
    if (diffDays <= 7) return 'age-amber';
    return 'age-red';
}
```

Applied in `renderTable()` when building the "Requested At" `<td>`:
```html
<td class="col-requested-at age-green">...</td>
```

CSS (text color only):
```css
.age-green { color: var(--color-secondary); }
.age-amber { color: var(--color-tertiary); }
.age-red { color: var(--color-error); }
```

### 4. Quick Actions (F4)

Add `.btn-quick-cancel` inside the status `<td>` for Pending rows. CSS:
```css
.btn-quick-cancel { display: none; }
tr:hover .btn-quick-cancel { display: inline-block; }
```

Click calls existing `confirmCancelRequest(index)`. Only rendered for Pending status rows.

### 5. Filter Presets (F5)

```javascript
function saveFilters() {
    const state = {
        status: document.getElementById('statusFilter').value,
        week: document.getElementById('weekFilter').value,
        search: document.getElementById('searchInput').value,
        excludeCompleted: document.getElementById('excludeCompleted').checked
    };
    localStorage.setItem('mrh-filters', JSON.stringify(state));
}

function loadFilters() {
    const saved = localStorage.getItem('mrh-filters');
    if (!saved) return;
    const state = JSON.parse(saved);
    // apply to DOM elements, then renderTable()
}
```

Called in every filter `onchange`/`oninput` handler. `loadFilters()` called in `DOMContentLoaded` before `renderTable()`. Reset Filters clears localStorage.

### 6. Status History Timeline (F6)

New section in modal body after existing fields:
```html
<div class="timeline-section">
    <div class="timeline-title">Request Timeline</div>
    <div class="timeline-item">
        <div class="timeline-dot" style="background: var(--color-tertiary);"></div>
        <div class="timeline-line"></div>
        <div class="timeline-content">
            <div class="timeline-label">Request Submitted</div>
            <div class="timeline-time">03 Sep 2026, 10:30 AM</div>
        </div>
    </div>
    <!-- ... more items -->
</div>
```

CSS:
```css
.timeline-section { margin-top: 24px; border-top: 1px solid var(--color-outline); padding-top: 16px; }
.timeline-title { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
.timeline-item { display: flex; gap: 12px; position: relative; padding-bottom: 16px; }
.timeline-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; margin-top: 3px; }
.timeline-line { width: 2px; background: var(--color-outline); position: absolute; left: 5px; top: 15px; bottom: 0; }
.timeline-item:last-child .timeline-line { display: none; }
.timeline-label { font-size: 13px; font-weight: 600; }
.timeline-time { font-size: 12px; color: var(--color-on-surface-variant); margin-top: 2px; }
```

Timeline events:
- "Request Submitted" — always shown, uses `requestedAt`
- "Under Review" — always shown, timestamp = `requestedAt + 1-24h` (mock)
- Final status event (Approved/Rejected/Cancelled) — only if status is not Pending

### 7. Keyboard Shortcuts (F7)

```javascript
let focusedRowIndex = -1;

document.addEventListener('keydown', function(e) {
    // Don't handle if modal is open or input is focused
    if (document.getElementById('modalOverlay').style.display === 'flex') {
        if (e.key === 'Escape') closeModal();
        return;
    }
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;

    if (e.key === 'ArrowDown') { /* move focus down */ }
    if (e.key === 'ArrowUp') { /* move focus up */ }
    if (e.key === 'Enter' && focusedRowIndex >= 0) { /* open modal for focused row */ }
    if (e.key === 'Escape') { /* clear focus */ }
});
```

Focus indicator CSS:
```css
tr.row-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }
```

## Promoted to Shared

None. All changes are page-specific enhancements. No CSS classes, JS helpers, or markup blocks are duplicated across 3+ pages.

## File Changes

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | Enhance — add 7 features |
| `page-changelogs/my-request-history-changelog.md` | Update — add feature entries |
