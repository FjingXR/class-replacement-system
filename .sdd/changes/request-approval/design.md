# Design: Request Approval Page (PL Side)

## Technical Approach

Create a new Blade template extending `layouts/ui-template`. The page mirrors the my-request-history page structure (page header → toolbar → table → pagination → summary cards → empty state → modal) but adds PL-specific features: Lecturer column, Urgency column, in-table Approve/Reject actions, and a default "Pending" status filter. All shared CSS comes from `theme.css`; page-specific CSS (modal, badges, buttons, cell-class-block, column widths) is copied from my-request-history's `@section('page-styles')` and adapted. All table rendering, filtering, sorting, and pagination logic follows the my-request-history JS pattern, reusing the shared helpers in `ui-common.js`. The 10 generic helpers currently page-local in my-request-history (`weekRanges`, `formatDateTime`, `statusClass`, `dayAbbr`, `isoDayName`, `formatClassBlock`, `formatReplacementBlock`, `getWeekRange`, `isInWeek`, `getWeekNumber`) are **promoted into `ui-common.js`** as the single source of truth — the new page consumes them from there, and my-request-history is refactored to do the same (its local copies are removed). The mock data (`approvalRequests`, `URGENCY_REFERENCE_DATE`) lives in a **new shared data module `public/js/mock-data.js`** loaded by the layout, separating data from logic. This follows the OOP encapsulation principle: shared logic lives in shared modules, not duplicated per-page. Beyond the core review workflow, 8 PL-efficiency features are added: bulk approve/reject (checkbox column + batch action bar), enhanced approve/reject confirm summaries, reject reason preset chips, urgency filter chips, request age sub-labels, approval notes modal, pending count badge on nav bar, and viewed-row indicator.

## Architecture Decisions

### 1. Template Structure

**Decision:** `@extends('layouts.ui-template', ['activeNav' => 'request-approval'])` with 4 sections: `title`, `page-styles`, `content`, `page-scripts`.

```blade
@extends('layouts.ui-template', ['activeNav' => 'request-approval'])

@section('title', 'Request Approval — Class Replacement System')

@section('page-styles')
    /* Page-specific CSS: copied from my-request-history + urgency/action additions */
@endsection

@section('content')
    <!-- Page header -->
    <!-- Toolbar -->
    <!-- Sort hint -->
    <!-- Grid wrapper > table -->
    <!-- Pagination bar -->
    <!-- Summary bar partial -->
    <!-- Empty state -->
    <!-- Detail modal -->
@endsection

@section('page-scripts')
    // Page-local logic only — NO mock data here.
    // approvalRequests + URGENCY_REFERENCE_DATE come from mock-data.js (loaded by layout)
    // weekRanges + shared helpers come from ui-common.js (loaded by layout)
    // urgencyLevel(), urgencyClass(), urgencyLabel(), urgencyDays()
    // approveRequest(), openRejectModal(), closeRejectModal(), updateRejectConfirmState(), rejectRequest()
    // slotValidityHtml(), renderTable(), openModal(), closeModal()
    // Event listeners
@endsection
```

### 2. CSS Strategy

**Decision:** Copy my-request-history's entire `@section('page-styles')` as the starting point, then add 3 new CSS blocks for urgency, actions, and the new column widths.

**CSS copied from my-request-history (page-specific, NOT in theme.css):**
- Column width classes — adapted for 10 columns (see mapping table in Section 3)
- `.cell-class-block`, `.class-day-date`, `.class-time`, `.class-duration`
- `.col-replacement .cell-class-block .class-time.status-*` color-coding
- `.badge` cursor/hover additions (badge is clickable per column 9) + 5 status variants (`.status-pending`/`.status-approved`/`.status-rejected`/`.status-cancelled`/`.status-completed`) — note: the `.badge` base itself IS in theme.css (line 504), reuse it, only the variants are page-local
- `.modal-overlay`, `.modal`, `.modal-header`, `.modal-title`, `.modal-close`, `.modal-body`, `.modal-field`, `.modal-field-label`, `.modal-field-value`, `.modal-section-title`, `.modal-footer`, `.modal-footer-left`, `.modal-footer-right`
- `.btn-danger`, `.btn-outline`, `.btn-clear`
- `.summary-card.card-total`, `.card-pending`, `.card-approved`, `.card-rejected` color rules
- Sort arrow + hint (already shared in theme.css — no copy needed)

**NEW CSS for request-approval page:**

```css
/* Urgency badges */
.urgency-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}
.urgency-urgent {
    background: var(--color-error-container);
    color: var(--color-on-error-container);
}
.urgency-normal {
    background: var(--color-secondary-container);
    color: var(--color-on-secondary-container);
}

/* Action buttons */
.btn-approve {
    padding: 5px 10px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-secondary);
    background: transparent;
    color: var(--color-secondary);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.btn-approve:hover {
    background: var(--color-secondary);
    color: var(--color-on-secondary);
}
.btn-reject {
    padding: 5px 10px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-error);
    background: transparent;
    color: var(--color-error);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    margin-left: 6px;
}
.btn-reject:hover {
    background: var(--color-error);
    color: var(--color-on-error);
}
.btn-view {
    padding: 5px 12px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-outline);
    background: transparent;
    color: var(--color-on-surface-variant);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-view:hover {
    background: var(--color-surface-variant);
}

/* Checkbox column */
.col-checkbox { width: 35px; text-align: center; }
.col-checkbox input[type="checkbox"] { cursor: pointer; accent-color: var(--color-primary); }

/* Batch action bar */
.batch-bar {
    display: none;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    background: var(--color-surface-variant);
    border: 1px solid var(--color-outline);
    border-radius: var(--radius-sm);
    margin-bottom: 8px;
}
.batch-bar .batch-count { font-size: 13px; font-weight: 500; color: var(--color-on-surface); }

/* Urgency filter chips */
.urgency-filter { display: inline-flex; gap: 4px; margin-left: 8px; }
.urgency-filter-chip {
    padding: 4px 10px;
    border-radius: 12px;
    border: 1px solid var(--color-outline);
    background: transparent;
    color: var(--color-on-surface-variant);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.urgency-filter-chip.active {
    background: var(--color-primary);
    color: var(--color-on-primary);
    border-color: var(--color-primary);
}
.urgency-filter-chip:hover:not(.active) { background: var(--color-surface-variant); }

/* Request age sub-label */
.request-age { font-size: 11px; color: var(--color-on-surface-variant); margin-top: 2px; }
.request-age::before { content: '● '; font-size: 8px; }
.age-fresh .request-age::before { color: var(--color-primary); }
.age-waiting .request-age::before { color: var(--color-tertiary); }
.age-stale .request-age::before { color: var(--color-error); }

/* Reject preset chips */
.reject-presets { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
.reject-preset-chip {
    padding: 4px 10px;
    border-radius: 12px;
    border: 1px solid var(--color-outline);
    background: transparent;
    color: var(--color-on-surface-variant);
    font-size: 12px;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.reject-preset-chip:hover { background: var(--color-surface-variant); }

/* Nav badge */
.nav-badge {
    display: inline-block;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
    border-radius: 9px;
    background: var(--color-error);
    color: var(--color-on-error);
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    margin-left: 6px;
    padding: 0 5px;
}

/* Viewed row indicator */
.row-viewed td:first-child { border-left: 3px solid var(--color-primary); }
```

### 3. Column Width Mapping

| # | Column | CSS Class | Width | Source |
|---|--------|-----------|-------|--------|
| 0 | ☐ (Select) | `.col-checkbox` | 35px | NEW — checkbox column (§7.1) |
| 1 | Request No | `.col-no` | 50px | Same as my-request-history |
| 2 | Requested Timestamp | `.col-requested-at` | 145px | Same as my-request-history `.col-requested-at` |
| 3 | Lecturer | `.col-lecturer` | 130px | NEW width class |
| 4 | Course Code & Name | `.col-code` | 200px | Same as my-request-history `.col-code` |
| 5 | Original Class | `.col-original` | 170px | Same as my-request-history `.col-original` |
| 6 | Proposed Replacement | `.col-replacement` | 170px | Same as my-request-history `.col-replacement` |
| 7 | Students | `.col-students` | 70px | Intentionally narrower than my-request-history's 80px — this page has more columns (11 vs 10 but different widths), so 70px saves space |
| 8 | Urgency | `.col-urgency` | 90px | NEW width class |
| 9 | Status | `.col-status` | 130px | Same as my-request-history `.col-status` |
| 10 | Actions | `.col-actions` | 140px | NEW width class |

Table `min-width`: 1335px (sum of column widths = 1330px, rounded up for breathing room).

### 4. Mock Data Structure

**Location:** the mock data is NOT in the page template. It lives in the new shared data module `public/js/mock-data.js`:

```javascript
// public/js/mock-data.js
const approvalRequests = [
    {
        id: 1,
        lecturer: 'Kylian Mbappe',          // NEW field
        requestedAt: '2026-08-28T09:15:00',  // exact submission timestamp
        courseCode: 'BMIT2201',
        courseName: 'Data Structures & Algorithms',
        classType: 'L',
        classDate: '2026-08-31',              // within 3 days of reference date → Urgent
        classDay: 'Monday',
        timeStart: '09:00',
        timeEnd: '11:00',
        duration: 2,
        venue: 'C201',
        totalStudents: 40,
        cohortCounts: [22, 18],
        cohorts: ['DIT2 (S1)', 'DSE2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',             // FR 3.4 — mock slot validity ('valid' | 'conflict')
        conflictReason: null,              // reason shown when slotValidity === 'conflict'
        replacementDate: '2026-09-02',
        replacementTime: '09:00 – 11:00',
        replacementVenue: null,
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 2,
        lecturer: 'Dembele',
        requestedAt: '2026-08-29T14:20:00',
        courseCode: 'BMIT3302',
        courseName: 'Operating Systems',
        classType: 'T',
        classDate: '2026-09-01',              // within 3 days of reference date → Urgent
        classDay: 'Tuesday',
        timeStart: '14:00',
        timeEnd: '15:00',
        duration: 1,
        venue: 'D103',
        totalStudents: 25,
        cohorts: ['DCS2 (S1)'],
        status: 'Pending',
        rejectionReason: null,
        slotValidity: 'valid',             // FR 3.4 — all 20 entries carry this field
        conflictReason: null,
        replacementDate: '2026-09-03',
        replacementTime: '14:00 – 15:00',
        replacementVenue: null,
        reviewedBy: null,
        reviewedAt: null,
        remarks: null
    },
    {
        id: 3,
        lecturer: 'Hakimi',
        requestedAt: '2026-08-30T10:00:00',
        courseCode: 'BMIT4403',
        courseName: 'Software Architecture',
        classType: 'L',
        classDate: '2026-09-07',              // >3 days from reference → Normal
        classDay: 'Monday',
        timeStart: '10:00',
        timeEnd: '13:00',
        duration: 3,
        venue: 'E201',
        totalStudents: 50,
        cohortCounts: [25, 25],
        cohorts: ['DAI2 (S1)', 'DNE2 (S1)'],
        status: 'Approved',
        rejectionReason: null,
        slotValidity: 'conflict',          // FR 3.4 — sample showing a conflict (with reason)
        conflictReason: 'Room C202 already occupied',
        replacementDate: '2026-09-09',
        replacementTime: '10:00 – 13:00',
        replacementVenue: 'C202',
        reviewedBy: 'Dr. Siti (PL)',
        reviewedAt: '2026-09-01T08:30:00',
        remarks: null
    },
    // ... 17 more entries (8 Pending, 5 Approved, 4 Rejected, 2 Completed, 1 Cancelled)
    // slotValidity: ~4-5 of the 20 entries 'conflict' with conflictReason, rest 'valid' (all 20 carry the field)
    // Lecturers: 5+ distinct (Kylian Mbappe, Dembele, Hakimi, Neymar, Vinicius)
    // Courses: 8+ distinct codes (BMIT2201, BMIT3302, BMIT4403, BMIT5504, BMIT6605, BMIT7706, BMIT8807, BMIT9908)
    // Cohorts: 6+ distinct (DIT2, DSE2, DCS2, DAI2, DNE2, DDA2 — all with (S1) suffix)
    // Venues: 6+ distinct (C201, C202, D103, D104, E201, E202)
    // Durations: 1-3 hours
    // Class dates: mix of Aug 31 - Sep 1 (Urgent, inside Week 1) and Sep 2-27 (Normal)
];

// Also in mock-data.js (data module):
const URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00');
```

**Distinct from my-request-history:**
- Different lecturer names (5+)
- Different course codes (8+, e.g. BMIT2201, BMIT3302, BMIT4403, BMIT5504, BMIT6605, BMIT7706, BMIT8807, BMIT9908)
- Different cohort names (6+, e.g. DIT2 (S1), DSE2 (S1), DCS2 (S1), DAI2 (S1), DNE2 (S1), DDA2 (S1))
- Different venues (6+, e.g. C201, C202, D103, D104, E201, E202)
- Class dates spanning Aug 31 – Sep 27 (Weeks 1-4)

The page's `@section('page-scripts')` must NOT redeclare `approvalRequests` or `URGENCY_REFERENCE_DATE` — both are provided by `mock-data.js` (loaded by the layout before the page inline script).

### 5. Urgency System

The `URGENCY_REFERENCE_DATE` constant is defined in `mock-data.js` (data module). The urgency helper functions are page-local (logic stays with the page):

```javascript
// mock-data.js
const URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00');

// page script — logic
function urgencyLevel(classDate) {
    const target = new Date(classDate + 'T00:00:00');
    const diffDays = Math.ceil((target - URGENCY_REFERENCE_DATE) / (1000 * 60 * 60 * 24));
    return diffDays <= 3 ? 'urgent' : 'normal';
}

function urgencyClass(level) {
    return level === 'urgent' ? 'urgency-urgent' : 'urgency-normal';
}

function urgencyLabel(level) {
    return level === 'urgent' ? 'Urgent' : 'Normal';
}

// Computed sort value for urgency column
function urgencyDays(classDate) {
    const target = new Date(classDate + 'T00:00:00');
    return Math.ceil((target - URGENCY_REFERENCE_DATE) / (1000 * 60 * 60 * 24));
}
```

### 6. Default Status Filter

The status filter `<select>` has `id="statusFilter"` and is set to `"Pending"` on page load (not `"all"` like my-request-history). The `DOMContentLoaded` handler sets `document.getElementById('statusFilter').value = 'Pending'` before calling `renderTable()`.

"Reset Filters" button resets the status filter back to `"Pending"` (not `"all"`).

### 6.1 Default Sort (FR 3.2 — time-based queue)

`sortState = { field: 'requestedAt', dir: 'asc' }` — oldest requests first = FIFO review queue. Users can still re-sort via column headers; Reset Filters returns to this default.

### 7. Approve/Reject Behavior

```javascript
function approveRequest(id) {
    if (confirm('Approve replacement request #' + id + '?\n\nThis will notify the lecturer and confirm the replacement arrangement.')) {
        alert('Request #' + id + ' has been approved.\n\n(Frontend design phase — no backend state update.)');
    }
}
// NOTE: Base approve flow above is overridden by §18 (Approval Notes Modal) in Task 12 —
// `approveRequest(id)` is replaced with `openApproveNotesModal([id])` for the full UX.

// FR 3.6 — mandatory rejection reason
function openRejectModal(id) {
    const r = approvalRequests.find(x => x.id === id);
    if (!r) return;
    currentRejectId = id;
    document.getElementById('rejectReasonInput').value = '';
    document.getElementById('rejectReasonModal').classList.add('show');
    updateRejectConfirmState();
    document.getElementById('rejectReasonInput').focus();
}

function updateRejectConfirmState() {
    const hasReason = document.getElementById('rejectReasonInput').value.trim() !== '';
    document.getElementById('confirmRejectBtn').disabled = !hasReason;
}

function rejectRequest() {
    const reason = document.getElementById('rejectReasonInput').value.trim();
    if (!reason) {
        alert('Please provide a rejection reason.');
        return;
    }
    if (confirm('Reject replacement request #' + currentRejectId + '?\n\nReason: ' + reason + '\n\nThis will notify the lecturer that the request was declined.')) {
        alert('Request #' + currentRejectId + ' has been rejected.\n\n(Frontend design phase — no backend state update.)');
    }
    closeRejectModal();
}

function closeRejectModal() {
    document.getElementById('rejectReasonModal').classList.remove('show');
    currentRejectId = null;
}
```

These functions do NOT modify `approvalRequests`, do NOT re-render the table, and do NOT update summary cards. The confirm + alert simulates the action without changing state.

### 8. Table Rendering Logic

The `renderTable()` function follows the my-request-history pattern but with these differences:

- **Search**: matches `courseCode`, `courseName`, OR `lecturer` (new — my-request-history only searches code + name)
- **Default status filter**: "Pending" (not "all")
- **Urgency filter** (§7.4): after status + week + search filtering, apply `urgencyFilter` — if `'urgent'`, keep only `urgencyLevel(r.classDate) === 'urgent'`; if `'normal'`, keep only normal; if `'all'`, keep all. Resets to `'all'` on "Reset Filters".
- **Exclude Completed toggle**: NOT included (PL needs to see all statuses — Completed requests are part of the audit trail). The toolbar has only search + status filter + urgency filter + week filter + reset.
- **Column 0 (Checkbox)**: renders `<input type="checkbox">` on Pending rows (checked if `selectedIds.has(r.id)`); empty `<td>` on non-Pending rows. "Select All" checkbox in `<thead>` toggles all visible Pending rows.
- **Column 2 (Requested Timestamp)**: renders formatted timestamp + request age sub-label (§7.5)
- **Column 3 (Lecturer)**: renders `r.lecturer` as plain text (no special formatting)
- **Column 8 (Urgency)**: renders `urgencyBadgeHtml = '<span class="urgency-badge ' + urgencyClass(level) + '">' + urgencyLabel(level) + '</span>'`
- **Column 10 (Actions)**: for Pending rows, renders Approve + Reject buttons with `onclick="approveRequest(r.id)"` and `onclick="openRejectModal(r.id)"` (Reject opens the mandatory-reason modal, FR 3.6). For non-Pending rows, renders View button with `onclick="openModal(offset + i)"`.
- **Status badge**: clickable for all rows — `onclick="openModal(offset + i)"`
- **Viewed indicator**: if `viewedIds.has(r.id)`, add `.row-viewed` class to the `<tr>` (§7.8)
- **Batch bar**: shown/hidden based on `selectedIds.size > 0` (§7.1)

### 9. Summary Card Updates

```javascript
function updateSummary() {
    const total = approvalRequests.length;
    const pending = approvalRequests.filter(r => r.status === 'Pending').length;
    const approved = approvalRequests.filter(r => r.status === 'Approved').length;
    const rejected = approvalRequests.filter(r => r.status === 'Rejected').length;
    const reviewed = approvalRequests.filter(r => ['Approved', 'Rejected', 'Completed'].includes(r.status)).length;

    document.getElementById('summaryPending').textContent = pending;
    document.getElementById('summaryApproved').textContent = approved;
    document.getElementById('summaryRejected').textContent = rejected;
    document.getElementById('summaryReviewed').textContent = reviewed;
}
```

### 10. Modal Footer Logic

```javascript
// In openModal(index):
const r = currentFiltered[index];
const approveBtn = document.getElementById('approveRequestBtn');
const rejectBtn = document.getElementById('rejectRequestBtn');
const closeBtn = document.getElementById('closeModalBtn');

if (r.status === 'Pending') {
    // Pending: show Reject (left) + Approve (right), hide footer Close button
    approveBtn.style.display = 'inline-block';
    rejectBtn.style.display = 'inline-block';
    closeBtn.style.display = 'none';
    approveBtn.onclick = function() { approveRequest(r.id); };
    rejectBtn.onclick = function() { openRejectModal(r.id); };
} else {
    // Non-Pending: show only footer Close button (header ✕ is always visible)
    approveBtn.style.display = 'none';
    rejectBtn.style.display = 'none';
    closeBtn.style.display = 'inline-block';
}
```

Note: The modal header `✕` close button is always visible regardless of status. The footer `closeModalBtn` is a separate Close button that only appears for non-Pending rows.

### 11. Modal Section 3 — Slot Validity (FR 3.4)

Rendered after Replacement Venue in Section 3 ("Requested Replacement Class"):

```javascript
function slotValidityHtml(r) {
    if (r.slotValidity === 'conflict') {
        return '<span class="slot-conflict">&#9888; Conflict' + (r.conflictReason ? ' &mdash; ' + r.conflictReason : '') + '</span>';
    }
    return '<span class="slot-valid">&#10003; Valid</span>';
}
```

```css
.slot-valid { color: var(--color-primary); font-weight: 600; }
.slot-conflict { color: var(--color-error); font-weight: 600; }
```

### 12. Rejection Reason Modal (FR 3.6)

Small modal **stacked on top of the detail modal** (keeps the detail visible beneath; Cancel/close restores it). Both overlays use `.modal-overlay` with the same z-index (100), so **DOM order decides the top layer** — the `#rejectReasonModal` HTML must be placed AFTER `#modalOverlay` in the DOM, otherwise it renders beneath the detail modal and is unusable.

```html
<div class="modal-overlay" id="rejectReasonModal">
    <div class="modal">
        <div class="modal-header"><h3 class="modal-title">Reject Request</h3>
            <button class="modal-close" onclick="closeRejectModal()">&times;</button></div>
        <div class="modal-body">
            <div class="modal-field">
                <span class="modal-field-label">Rejection Reason <span style="color:var(--color-error)">*</span></span>
                <textarea id="rejectReasonInput" rows="3" placeholder="Please provide a reason for rejection..."
                    style="width:100%;border:1px solid var(--color-outline);border-radius:var(--radius-sm);padding:8px;background:transparent;color:var(--color-on-surface);font-family:inherit;font-size:13px;resize:vertical"></textarea>
            </div>
        </div>
        <div class="modal-footer"><div class="modal-footer-right">
            <button class="btn-outline" id="cancelRejectBtn" onclick="closeRejectModal()">Cancel</button>
            <button class="btn-danger" id="confirmRejectBtn" disabled onclick="rejectRequest()">Confirm Reject</button>
        </div></div>
    </div>
</div>
```

- Confirm Reject disabled until textarea has non-whitespace content (wired via `input` listener → `updateRejectConfirmState()`)
- `rejectRequest()` validates again, shows `confirm()` including the reason, then `alert()` — no state change (design phase)
- Overlay click: safe as-is via shared `closeOnOverlayClick` (checks `e.target === e.currentTarget` per overlay, so clicking the reject overlay never closes the detail modal)
- **Escape: do NOT call `closeOnEsc` twice** (ui-common.js's `closeOnEsc` attaches an unconditional listener per call — two calls would close BOTH modals at once). Use ONE keydown handler with 3 layers: `if (approveNotesModal.classList.contains('show')) closeApproveNotesModal(); else if (rejectReasonModal.classList.contains('show')) closeRejectModal(); else closeModal();`
- CSS reuses existing `.modal-*` classes; `.btn-danger` exists in my-request-history page CSS (copied per section 2)

### 13. Bulk Approve/Reject (§7.1)

**State:** `selectedIds = new Set()` — tracks checked Pending row IDs. `selectAll` checkbox in `<thead>` toggles all visible Pending row checkboxes. `batchBar` div (fixed above table) shows/hides based on `selectedIds.size > 0`.

```javascript
function toggleSelectAll() {
    const visible = currentFiltered.filter(r => r.status === 'Pending');
    if (selectedIds.size === visible.length) { selectedIds.clear(); }
    else { visible.forEach(r => selectedIds.add(r.id)); }
    renderTable();
}

function toggleRowSelect(id) {
    if (selectedIds.has(id)) selectedIds.delete(id); else selectedIds.add(id);
    renderTable();
}

function updateBatchBar() {
    const bar = document.getElementById('batchBar');
    const count = document.getElementById('batchCount');
    if (selectedIds.size === 0) { bar.style.display = 'none'; return; }
    bar.style.display = 'flex';
    count.textContent = selectedIds.size + ' selected';
}

function bulkApprove() {
    const ids = [...selectedIds];
    const summary = ids.map(id => {
        const r = approvalRequests.find(x => x.id === id);
        return '#' + id + ' ' + r.courseCode + ' — ' + r.lecturer;
    }).join('\n');
    if (confirm('Approve ' + ids.length + ' request(s)?\n\n' + summary + '\n\nThis will notify the lecturers.')) {
        alert(ids.length + ' request(s) approved.\n\n(Frontend design phase — no backend state update.)');
    }
    selectedIds.clear();
    renderTable();
}

function bulkReject() {
    openRejectModal(null); // null = bulk mode
    // rejectRequest() applies the reason to all selectedIds
}
```

Checkbox column rendering in `renderTable()`:
```javascript
// Column 0 (checkbox)
if (r.status === 'Pending') {
    html += '<td class="col-checkbox"><input type="checkbox" ' + (selectedIds.has(r.id) ? 'checked' : '') + ' onchange="toggleRowSelect(' + r.id + ')"></td>';
} else {
    html += '<td class="col-checkbox"></td>';
}
```

### 14. Enhanced Confirm Summary (§7.2)

`approveRequest(id)` and `bulkApprove()` build a multi-line summary string from the request object:

```javascript
function approveSummary(r) {
    return '#' + r.id + ' ' + r.courseCode + ' — ' + r.courseName +
        '\nLecturer: ' + r.lecturer +
        '\nOriginal: ' + dayAbbr(r.classDay) + ' ' + formatShortDate(r.classDate) + ' ' + to12h(r.timeStart) + '–' + to12h(r.timeEnd) +
        '\nReplacement: ' + formatShortDate(r.replacementDate) + ' ' + r.replacementTime +
        '\nVenue: ' + (r.replacementVenue || r.venue);
}
```

`formatShortDate(iso)` — new page-local helper: returns "31 Aug 2026" from "2026-08-31".

### 15. Reject Reason Presets (§7.3)

```javascript
function applyRejectPreset(text) {
    const input = document.getElementById('rejectReasonInput');
    input.value = text;
    updateRejectConfirmState();
    input.focus();
}
```

HTML chips inside `#rejectReasonModal .modal-body` above the textarea (see proposal §7f). CSS: `.reject-preset-chip` styled as small outline buttons matching toolbar chip language.

### 16. Urgency Filter (§7.4)

**State:** `urgencyFilter = 'all'` — one of `'all'`, `'urgent'`, `'normal'`. Filtered in `renderTable()` after the status filter and before the week filter.

```javascript
function setUrgencyFilter(level) {
    urgencyFilter = level;
    document.querySelectorAll('.urgency-filter-chip').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.urgency === level);
    });
    currentPage = 1;
    renderTable();
}
```

In `renderTable()`, after `filtered = all.filter(...)` for status + week + search:
```javascript
if (urgencyFilter !== 'all') {
    filtered = filtered.filter(r => urgencyLevel(r.classDate) === urgencyFilter);
}
```

### 17. Request Age Sub-label (§7.5)

```javascript
function requestAgeHtml(requestedAt) {
    const diff = Math.floor((Date.now() - new Date(requestedAt).getTime()) / 86400000);
    const cls = diff <= 1 ? 'age-fresh' : diff <= 3 ? 'age-waiting' : 'age-stale';
    return '<div class="request-age ' + cls + '">' + diff + ' day' + (diff !== 1 ? 's' : '') + ' ago</div>';
}
```

Rendered inside column 2 (Requested Timestamp) below the formatted timestamp. CSS uses `::before` pseudo-element for the coloured dot (see proposal §7e).

### 18. Approval Notes Modal (§7.6)

**State:** `currentApproveIds = []` — tracks which request(s) are being approved (single or bulk).

```javascript
function openApproveNotesModal(ids) {
    currentApproveIds = ids;
    const summary = ids.map(id => {
        const r = approvalRequests.find(x => x.id === id);
        return approveSummary(r);
    }).join('\n\n');
    document.getElementById('approveNotesSummary').textContent = summary;
    document.getElementById('approveNotesInput').value = '';
    document.getElementById('approveNotesModal').classList.add('show');
}

function confirmApproveWithNotes() {
    const notes = document.getElementById('approveNotesInput').value.trim();
    const ids = currentApproveIds;
    const label = ids.length === 1 ? 'Request #' + ids[0] : ids.length + ' requests';
    const notesLine = notes ? '\nNotes: ' + notes : '';
    if (confirm('Approve ' + label + '?' + notesLine + '\n\nThis will notify the lecturer(s).')) {
        alert(label + ' approved.' + notesLine + '\n\n(Frontend design phase — no backend state update.)');
    }
    closeApproveNotesModal();
    selectedIds.clear();
    renderTable();
}

function closeApproveNotesModal() {
    document.getElementById('approveNotesModal').classList.remove('show');
    currentApproveIds = [];
}
```

Single Approve calls `openApproveNotesModal([id])`. Bulk Approve calls `openApproveNotesModal([...selectedIds])`.

### 19. Pending Count Badge (§7.7)

```javascript
function updateNavBadge() {
    const count = approvalRequests.filter(r => r.status === 'Pending').length;
    const badge = document.getElementById('navPendingBadge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}
```

Called at end of `DOMContentLoaded` and after any render. Badge HTML lives in `ui-nav-bar.blade.php` (proposal §7g). CSS: `.nav-badge` in the page's `@section('page-styles')`.

### 20. Viewed Indicator (§7.8)

**State:** `viewedIds = new Set()` — session-only, resets on page reload.

```javascript
// In openModal(index):
viewedIds.add(currentFiltered[index].id);
// After renderTable(), apply .row-viewed class to viewed rows
```

In `renderTable()`, after building each row:
```javascript
if (viewedIds.has(r.id)) rowClass += ' row-viewed';
```

CSS: `.row-viewed td:first-child { border-left: 3px solid var(--color-primary); }` — subtle left-border accent.

## Dependencies

- `theme.css` — shared component CSS (loaded via layout `<link>`)
- `ui-common.js` — shared JS module (loaded via layout `<script>`): `to12h()`, `formatDate()`, `compareBy()`, `makeSortableHeader()`, `paginate()`, `updateResultCount()`, `closeOnOverlayClick()`, plus the 10 promoted helpers from my-request-history (`weekRanges`, `formatDateTime()`, `statusClass()`, `dayAbbr()`, `isoDayName()`, `formatClassBlock()`, `formatReplacementBlock()`, `getWeekRange()`, `isInWeek()`, `getWeekNumber()`) — note: `closeOnEsc()` is NOT used on this page (replaced by ONE keydown handler that closes the topmost modal, see §12)
- `mock-data.js` — **NEW shared data module** (loaded via layout `<script>` after ui-common.js): `approvalRequests` (20 entries) + `URGENCY_REFERENCE_DATE`
- `partials/ui-summary-bar.blade.php` — reuse for summary cards
- `partials/ui-nav-bar.blade.php` — auto-included by layout (needs "Request Approval" link added)
- `layouts/ui-template.blade.php` — base layout with `@yield` sections (modified to add the mock-data.js script tag)

## File Changes

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php` | **Create** — full page with CSS, HTML, JS (references `approvalRequests` + `URGENCY_REFERENCE_DATE` from mock-data.js, does not embed them) |
| `public/js/mock-data.js` | **Create** — shared data module: `approvalRequests` (20 entries) + `URGENCY_REFERENCE_DATE` |
| `resources/views/layouts/ui-template.blade.php` | **Modify** — add `<script src="/js/mock-data.js"></script>` after ui-common.js |
| `resources/views/partials/ui-nav-bar.blade.php` | **Modify** — add "Request Approval" nav link after "Replacement Arrangement" |
| `routes/web.php` | **Modify** — add `/request-approval-ui` route |
| `public/js/ui-common.js` | **Modify** — append 10 promoted helpers (moved verbatim from my-request-history) |
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | **Modify** — delete the 10 page-local helper copies (now resolve from ui-common.js via layout); no other changes |
| `page-changelogs/request-approval-changelog.md` | **Update/replace** — file already exists (dated 2026-08-01); rewrite to document the page, OOP helper promotion, mock-data.js module, my-request-history refactor, and FR alignment |

## Mobile & Tablet View Design (rule #9 — mandatory)

### Breakpoints
- **Tablet:** `@media (max-width: 1024px)` — stack summary cards to 2 columns, reduce table density
- **Mobile:** `@media (max-width: 768px)` — full card layout, stacked elements

### Mobile Layout (≤768px)

**Page header:**
- Stack title and description vertically
- Reduce font sizes: `.page-title { font-size: 1.25rem; }`, `.page-desc { font-size: 0.8rem; }`

**Toolbar:**
- Stack filters vertically (full-width dropdowns/inputs)
- Search input: full-width
- Status filter + urgency chips: wrap to new line, full-width
- Week filter: full-width
- Reset button: full-width, centered

**Batch action bar (`.batch-bar`):**
- Full-width, centered text, sticky at top (`.batch-bar { position: sticky; top: 0; z-index: 10; }`)

**Data table → Card layout:**
- Hide `<table>` on mobile
- Show `.card-list` container (new element, hidden on desktop)
- Each request renders as a card:
  ```html
  <div class="request-card">
      <div class="request-card-header">
          <span class="request-card-id">#12</span>
          <span class="request-card-status status-pending">Pending</span>
      </div>
      <div class="request-card-body">
          <div class="request-card-row"><span class="request-card-label">Lecturer</span><span class="request-card-value">Kylian Mbappe</span></div>
          <div class="request-card-row"><span class="request-card-label">Course</span><span class="request-card-value">BMIT2201 — Data Structures</span></div>
          <div class="request-card-row"><span class="request-card-label">Original</span><span class="request-card-value">Mon, 31 Aug 2026 (Week 1)<br>09:00 AM – 11:00 AM</span></div>
          <div class="request-card-row"><span class="request-card-label">Replacement</span><span class="request-card-value">Wed, 2 Sep 2026 (Week 1)<br>09:00 AM – 11:00 AM</span></div>
          <div class="request-card-row"><span class="request-card-label">Urgency</span><span class="request-card-value"><span class="urgency-badge urgency-urgent">Urgent</span></span></div>
      </div>
      <div class="request-card-actions">
          <button class="btn-approve" onclick="approveRequest(12)">Approve</button>
          <button class="btn-reject" onclick="openRejectModal(12)">Reject</button>
      </div>
  </div>
  ```
- Card CSS:
  ```css
  .card-list { display: none; }
  .request-card {
      background: var(--color-surface);
      border: 1px solid var(--color-outline);
      border-radius: var(--radius-md);
      padding: 12px;
      margin-bottom: 8px;
  }
  .request-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--color-outline);
  }
  .request-card-id { font-weight: 600; color: var(--color-on-surface); }
  .request-card-body { display: flex; flex-direction: column; gap: 6px; }
  .request-card-row { display: flex; flex-direction: column; font-size: 0.85rem; }
  .request-card-label { color: var(--color-on-surface-variant); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px; }
  .request-card-value { color: var(--color-on-surface); }
  .request-card-actions {
      display: flex;
      gap: 8px;
      margin-top: 12px;
      padding-top: 8px;
      border-top: 1px solid var(--color-outline);
  }
  .request-card-actions button { flex: 1; }
  @media (max-width: 768px) {
      .grid-scroll { display: none; }
      .card-list { display: block; }
      .sort-hint { display: none; }
      .pagination-bar { flex-direction: column; gap: 8px; }
  }
  ```

**Pagination:**
- Stack vertically (`.pagination-bar { flex-direction: column; align-items: stretch; }`)
- Full-width info text and controls

**Summary cards (via `@include('partials.ui-summary-bar')`):**
- Stack to 1 column on mobile (`.summary-bar { grid-template-columns: 1fr; }`)

**Empty state:**
- Full-width, centered, reduced padding (`.empty-state { padding: 24px 16px; }`)

**Detail modal:**
- Full-screen on mobile (`.modal { width: 100vw; height: 100vh; border-radius: 0; max-height: none; }`)
- Stack modal fields vertically with full width
- Footer buttons: full-width, stacked vertically

**Reject reason modal:**
- Full-screen on mobile (same as detail modal)
- Textarea: full-width
- Buttons: full-width, stacked vertically

**Approve notes modal:**
- Full-screen on mobile
- Textarea: full-width
- Buttons: full-width, stacked vertically

### Tablet Layout (769px–1024px)

**Summary cards:**
- 2-column grid (`.summary-bar { grid-template-columns: repeat(2, 1fr); }`)
- 4th card spans full width (`.summary-card:last-child { grid-column: span 2; }`)

**Data table:**
- Keep table layout but reduce column widths
- Hide checkbox column on tablet (`.col-checkbox { display: none; }`)
- Hide urgency column on tablet (`.col-urgency { display: none; }`)
- Show simplified action buttons (icon-only on tablet)

**Toolbar:**
- Keep horizontal layout but reduce spacing
- Wrap filters to 2 rows if needed

### CSS Media Queries Location
Add all mobile/tablet CSS in `@section('page-styles')` of the Blade template (page-specific, not shared to `theme.css` — the request card layout is unique to this page).
