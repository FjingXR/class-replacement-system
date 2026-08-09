# Design — Request Approval Bugfixes & Cross-Page Consistency

## Architecture

### Current State
- `theme.css` — shared CSS (status badges, buttons, modals defined here but also duplicated in pages)
- `ui-common.js` — shared JS helpers
- `mock-data.js` — shared mock data (`MockData.approvalRequests`, `MockData.requests`)
- Page templates — contain duplicated CSS and page-specific JS

### Target State
- `theme.css` — single source of truth for ALL shared CSS (no duplicates in pages)
- `ui-common.js` — shared JS helpers (no changes needed)
- `mock-data.js` — shared mock data (no changes needed)
- Page templates — only page-specific CSS and JS

## Design Decisions

### D15: Add RPP (Results Per Page) selector

**Problem**: request-approval page lacks RPP selector that my-request-history has.

**Solution**: Copy `initRpp()` pattern from my-request-history. Add RPP selector HTML in toolbar, add `renderRpp()` function, update `filterData()` to respect RPP value.

```javascript
// Add to request-approval page
let rpp = 10;
const RPP_OPTIONS = [10, 20, 50];

function renderRpp() {
    // Similar to my-request-history's renderRpp()
}

function initRpp() {
    // Restore from localStorage if available
    const saved = localStorage.getItem('request-approval-rpp');
    if (saved && RPP_OPTIONS.includes(parseInt(saved))) rpp = parseInt(saved);
    renderRpp();
}
```

**Files affected**: request-approval-UI-design-template.blade.php

### D16: Add filter persistence

**Problem**: Filters reset on page reload.

**Solution**: Reuse `saveFilters()` / `restoreFilters()` pattern from my-request-history. Persist to localStorage.

```javascript
// Add to request-approval page
function saveFilters() {
    const state = {
        weekFilter: document.getElementById('weekFilter')?.value || '',
        statusFilter: document.getElementById('statusFilter')?.value || '',
        urgencyFilter: document.getElementById('urgencyFilter')?.value || '',
        courseFilter: document.getElementById('courseFilter')?.value || '',
        hideCompleted: document.getElementById('hideCompletedToggle')?.checked || false,
        rpp: rpp,
        sortCol: sortCol,
        sortDir: sortDir
    };
    localStorage.setItem('request-approval-filters', JSON.stringify(state));
}

function restoreFilters() {
    const saved = localStorage.getItem('request-approval-filters');
    if (!saved) return false;
    try {
        const state = JSON.parse(saved);
        // Restore each filter element
        if (state.weekFilter) document.getElementById('weekFilter').value = state.weekFilter;
        if (state.statusFilter) document.getElementById('statusFilter').value = state.statusFilter;
        if (state.urgencyFilter) document.getElementById('urgencyFilter').value = state.urgencyFilter;
        if (state.courseFilter) document.getElementById('courseFilter').value = state.courseFilter;
        if (state.hideCompleted !== undefined) {
            hideCompleted = state.hideCompleted;
            document.getElementById('hideCompletedToggle').checked = hideCompleted;
        }
        if (state.rpp) { rpp = state.rpp; renderRpp(); }
        if (state.sortCol !== undefined) { sortCol = state.sortCol; sortDir = state.sortDir || 'asc'; }
        return true;
    } catch { return false; }
}
```

**Files affected**: request-approval-UI-design-template.blade.php

### D17: Add "Hide Completed" toggle

**Problem**: request-approval lacks "Hide Completed" toggle that my-request-history has.

**Solution**: Add toggle in toolbar, filter out Completed entries when on.

```javascript
let hideCompleted = true; // default ON

function toggleHideCompleted() {
    hideCompleted = !hideCompleted;
    document.getElementById('hideCompletedToggle').checked = hideCompleted;
    filterData();
    renderTable();
    updateSummary();
    saveFilters();
}
```

**Files affected**: request-approval-UI-design-template.blade.php

### D18: Add deep link support (`?id=N`)

**Problem**: No direct linking to specific requests.

**Solution**: Parse URL params, open modal if valid ID provided.

```javascript
// In DOMContentLoaded, after render
function checkDeepLink() {
    const params = new URLSearchParams(window.location.search);
    const id = parseInt(params.get('id'));
    if (!id) return;
    const r = MockData.approvalRequests.find(x => x.id === id);
    if (!r) {
        showToast('Request #' + id + ' not found', 'error');
        return;
    }
    openModalById(id);
}
```

**Files affected**: request-approval-UI-design-template.blade.php

### D19: Add responsive card view

**Problem**: CSS defined but no JS rendering for mobile.

**Solution**: Add `renderCards()` function similar to my-request-history.

```javascript
function renderCards(filtered) {
    const container = document.getElementById('requestsCards');
    if (!container) return;
    container.innerHTML = '';
    filtered.forEach((r, i) => {
        const card = document.createElement('div');
        card.className = 'request-card';
        card.setAttribute('data-id', r.id);
        card.innerHTML = `
            <div class="card-header">
                <span class="card-id">#${r.id}</span>
                <span class="status-badge ${statusClass(r.status)}">${r.status}</span>
            </div>
            <div class="card-body">
                <div class="card-field"><span class="card-label">Course:</span> ${r.courseCode} - ${r.courseName}</div>
                <div class="card-field"><span class="card-label">Date:</span> ${formatDateTime(r.classDate, r.classStartTime)}</div>
                <div class="card-field"><span class="card-label">Lecturer:</span> ${r.lecturerName}</div>
                <div class="card-field"><span class="card-label">Urgency:</span> ${requestUrgencyHtml(r)}</div>
                <div class="card-field"><span class="card-label">Age:</span> ${requestAgeHtml(r.requestedAt, r.status)}</div>
            </div>
        `;
        card.addEventListener('click', () => openModalById(r.id));
        container.appendChild(card);
    });
}
```

**Files affected**: request-approval-UI-design-template.blade.php

### D1: Use ID-based modal opening instead of index-based

**Problem**: `openModal(index)` accesses `currentFiltered[index]`, but after sorting, the index doesn't match the visible row.

**Solution**: Add `openModalById(id)` function that finds the request by ID from `MockData.approvalRequests`.

```javascript
// New function
function openModalById(id) {
    const r = MockData.approvalRequests.find(x => x.id === id);
    if (!r) return;
    // ... rest of modal logic
}

// Update onclick handlers
// Before: onclick="openModal(0)"
// After: onclick="openModalById(1)"

// Note: my-request-history page uses same pattern but searches mockRequests instead
```

**Files affected**: request-approval-UI-design-template.blade.php, my-request-history-UI-design-template.blade.php

### D2: Fix timeline step classification

**Problem**: Logic `i === steps.filter(x => !x.done).length` doesn't find the first incomplete step.

**Solution**: Use a `foundFirstIncomplete` flag.

```javascript
// Before (broken)
const cls = s.done ? 'completed' : (i === steps.filter(x => !x.done).length ? 'active' : 'pending');

// After (fixed)
let foundFirstIncomplete = false;
const cls = s.done ? 'completed' : (!foundFirstIncomplete && (foundFirstIncomplete = true) ? 'active' : 'pending');
```

**Files affected**: request-approval-UI-design-template.blade.php

### D3: Group collapse/expand — RESOLVED BY REMOVAL

**Problem**: Browser strips nested `<tbody>` elements, making grouping broken.

**Resolution**: Feature removed entirely (groupFilter dropdown, grouping logic, CSS). No fix needed.

### D4: Fix negative request age

**Problem**: Mock data has future dates but `requestAgeHtml()` uses `Date.now()`.

**Solution**: Use fixed reference date (same as urgency system). Fix mock data dates to be in the past where possible.

```javascript
// Before (broken)
const diff = Math.floor((Date.now() - new Date(requestedAt).getTime()) / 86400000);

// After (fixed)
const REFERENCE_DATE = new Date('2026-08-29T00:00:00');
const diff = Math.floor((REFERENCE_DATE - new Date(requestedAt).getTime()) / 86400000);
if (diff < 0) return '<div class="request-age request-age--unknown">—</div>';
const displayDiff = Math.abs(diff);
return '<div class="request-age ' + cls + '">' + displayDiff + ' day' + (displayDiff !== 1 ? 's' : '') + ' ago</div>';
```

**Files affected**: request-approval-UI-design-template.blade.php

### D5: Move summary bar to top

**Problem**: Summary cards are at bottom, not visible without scrolling.

**Solution**: Move `@include('partials.ui-summary-bar')` before the toolbar.

**Files affected**: request-approval-UI-design-template.blade.php

### D6: Route bulk approve through approval notes modal

**Problem**: Bulk approve uses `confirm()`, single approve uses `openApproveNotesModal()`.

**Solution**: Change `bulkApprove()` to call `openApproveNotesModal([...selectedIds])`.

**Files affected**: request-approval-UI-design-template.blade.php

### D7: Clear viewedIds on reset

**Problem**: `viewedIds` not cleared in `resetFilters()`.

**Solution**: Add `viewedIds.clear()` to `resetFilters()`.

**Files affected**: request-approval-UI-design-template.blade.php

### D8: Promote shared CSS to theme.css

**Problem**: 11 CSS classes duplicated between pages.

**Solution**: Move to `theme.css`, remove from page templates.

**CSS to promote:**
- `.status-pending`, `.status-approved`, `.status-rejected`, `.status-cancelled`, `.status-completed`
- `.modal-section-title`
- `.btn-danger`, `.btn-outline`
- `.col-checkbox`, `.row-selected`, `.bulk-checkbox`

**Files affected**: theme.css, request-approval-UI-design-template.blade.php, my-request-history-UI-design-template.blade.php

### D9: Unify bulk action bar

**Problem**: Different class names and positions.

**Solution**: Use `.bulk-action-bar` (fixed bottom) in both pages.

**Files affected**: request-approval-UI-design-template.blade.php

### D10: Unify request age format

**Problem**: Different formats and class names.

**Solution**: Use request-approval's format with dot indicator in both pages.

**Files affected**: my-request-history-UI-design-template.blade.php

### D11: Unify keyboard highlight

**Problem**: Different variable and class names.

**Solution**: Use `focusedRowIndex` + `.row-focused` in both pages.

**Files affected**: request-approval-UI-design-template.blade.php, my-request-history-UI-design-template.blade.php

### D20: Unify timeline CSS in my-request-history

**Problem**: Timeline structure differs between pages (request-approval uses `.timeline-step` + `.timeline-connector`; my-request-history uses `.timeline-item` + `.timeline-dot-wrap` + `.timeline-line`).

**Solution**: Rename my-request-history classes to match request-approval structure.

```css
/* Before (my-request-history) */
.timeline-item { ... }
.timeline-dot-wrap { ... }
.timeline-line { ... }

/* After (matches request-approval) */
.timeline-step { ... }
.timeline-connector { ... }
```

**Files affected**: my-request-history-UI-design-template.blade.php

## Mapping Tables

### CSS Classes to Promote to theme.css

| Class | Current Location | Target Location |
|-------|-----------------|-----------------|
| `.status-pending` | Both pages | `theme.css` |
| `.status-approved` | Both pages | `theme.css` |
| `.status-rejected` | Both pages | `theme.css` |
| `.status-cancelled` | Both pages | `theme.css` |
| `.status-completed` | Both pages | `theme.css` |
| `.modal-section-title` | Both pages | `theme.css` |
| `.btn-danger` | Both pages | `theme.css` |
| `.btn-outline` | Both pages | `theme.css` |
| `.col-checkbox` | Both pages | `theme.css` |
| `.row-selected` | Both pages | `theme.css` |
| `.bulk-checkbox` | Both pages | `theme.css` |

### Feature Alignment

| Feature | Before | After |
|---------|--------|-------|
| Bulk action bar class | `.batch-bar` | `.bulk-action-bar` |
| Bulk action bar position | Inline below toolbar | Fixed bottom |
| Request age format | "(X days)" | "● X days ago" |
| Keyboard highlight class | `.row-active` | `.row-focused` |
| Keyboard highlight variable | `activeRowIndex` | `focusedRowIndex` |
| Summary bar position | Bottom | Top |
| Bulk approve flow | `confirm()` | `openApproveNotesModal()` |

## Key Cross-Module Data Flows

```
mock-data.js
    └── MockData.approvalRequests (20 entries)
    └── MockData.requests (20 entries)
         ↓ read by
request-approval-UI-design-template.blade.php
my-request-history-UI-design-template.blade.php
         ↓ uses
ui-common.js (helpers: formatDateTime, statusClass, formatClassBlock, etc.)
         ↓ uses
theme.css (status badges, buttons, modals — SINGLE SOURCE)
```
