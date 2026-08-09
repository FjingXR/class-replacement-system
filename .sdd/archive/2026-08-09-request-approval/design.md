# Design: Request Approval Page (PL Side)

## Technical Approach

Create a new Blade template extending `layouts/ui-template`. The page mirrors the my-request-history page structure (page header → toolbar → table → pagination → summary cards → empty state → modal) but adds PL-specific features: Lecturer column, Urgency column, in-table Approve/Reject actions, and a default "Pending" status filter. All shared CSS comes from `theme.css`; page-specific CSS (modal, badges, buttons, cell-class-block, column widths) is copied from my-request-history's `@section('page-styles')` and adapted. All table rendering, filtering, sorting, and pagination logic follows the my-request-history JS pattern, reusing the shared helpers in `ui-common.js`. The 10 generic helpers currently page-local in my-request-history (`weekRanges`, `formatDateTime`, `statusClass`, `dayAbbr`, `isoDayName`, `formatClassBlock`, `formatReplacementBlock`, `getWeekRange`, `isInWeek`, `getWeekNumber`) are **promoted into `ui-common.js`** as the single source of truth — the new page consumes them from there, and my-request-history is refactored to do the same (its local copies are removed). The mock data (`MockData.approvalRequests`, `MockData.urgencyReferenceDate`) lives in a **shared data module `public/js/mock-data.js`** loaded by the layout, separating data from logic. This follows the OOP encapsulation principle: shared logic lives in shared modules, not duplicated per-page. Beyond the core review workflow, 17 PL-efficiency features are added: bulk approve/reject (checkbox column + batch action bar), enhanced approve/reject confirm summaries, reject reason preset chips, urgency filter chips, request age sub-labels, approval notes modal, pending count badge on nav bar, viewed-row indicator, keyboard shortcuts (Arrow/Enter/A/R/Escape), review-next auto-advance after approve/reject, slot validity preview icons (✓/⚠/?) in the Proposed Replacement column, toast notifications (replacing browser alerts), undo stack (3-5 sec toast with Undo button), animated transitions (row status flash, filter fade, group expand/collapse), smart grouping (Group by dropdown: None/Course/Lecturer), mini request lifecycle timeline in detail modal, and skeleton loading placeholders.

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
    // MockData.approvalRequests + MockData.urgencyReferenceDate come from mock-data.js (loaded by layout)
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

The page's `@section('page-scripts')` must NOT redeclare `MockData.approvalRequests` or `MockData.urgencyReferenceDate` — both are provided by `mock-data.js` (loaded by the layout before the page inline script). The page reads them via `MockData.*`.

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
    const r = MockData.approvalRequests.find(x => x.id === id);
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
    const isBulk = currentRejectId === null;
    const label = isBulk ? selectedIds.size + ' request(s)' : 'Request #' + currentRejectId;
    if (confirm('Reject ' + label + '?\n\nReason: ' + reason + '\n\nThis will notify the lecturer(s) that the request was declined.')) {
        alert(label + ' rejected.\n\n(Frontend design phase — no backend state update.)');
        reviewNextAfterAction(isBulk ? null : currentRejectId);
    }
    closeRejectModal();
    selectedIds.clear();
    renderTable();
}

function closeRejectModal() {
    document.getElementById('rejectReasonModal').classList.remove('show');
    currentRejectId = null;
}
```

These functions do NOT modify `MockData.approvalRequests`, do NOT re-render the table, and do NOT update summary cards. The confirm + alert simulates the action without changing state.

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
    const total = MockData.approvalRequests.length;
    const pending = MockData.approvalRequests.filter(r => r.status === 'Pending').length;
    const approved = MockData.approvalRequests.filter(r => r.status === 'Approved').length;
    const rejected = MockData.approvalRequests.filter(r => r.status === 'Rejected').length;
    const reviewed = MockData.approvalRequests.filter(r => ['Approved', 'Rejected', 'Completed'].includes(r.status)).length;

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
        const r = MockData.approvalRequests.find(x => x.id === id);
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
        const r = MockData.approvalRequests.find(x => x.id === id);
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
    const count = MockData.approvalRequests.filter(r => r.status === 'Pending').length;
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

### 21. Keyboard Shortcuts (§7i)

**State:** `activeRowIndex = -1` — index into `currentFiltered`, -1 = none selected.

**Active scope:** Only when no `.modal.show` elements exist (all modals closed). When any modal is open, keyboard shortcuts are paused — keydown listener checks `document.querySelector('.modal.show')` and returns early if truthy.

```javascript
document.addEventListener('keydown', function(e) {
    if (document.querySelector('.modal.show')) return; // modal open — pause nav
    if (!currentFiltered.length) return;
    if (e.key === 'ArrowDown') { e.preventDefault(); activeRowIndex = Math.min(activeRowIndex + 1, currentFiltered.length - 1); highlightRow(); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); activeRowIndex = Math.max(activeRowIndex - 1, 0); highlightRow(); }
    else if (e.key === 'Enter' && activeRowIndex >= 0) { openModal(activeRowIndex); }
    else if ((e.key === 'a' || e.key === 'A') && activeRowIndex >= 0) {
        const r = currentFiltered[activeRowIndex];
        if (r.status === 'Pending') approveRequest(r.id);
    }
    else if ((e.key === 'r' || e.key === 'R') && activeRowIndex >= 0) {
        const r = currentFiltered[activeRowIndex];
        if (r.status === 'Pending') openRejectModal(r.id);
    }
    else if (e.key === 'Escape') { activeRowIndex = -1; highlightRow(); }
});

function highlightRow() {
    // Skip .group-header rows — only navigate data rows
    const dataRows = document.querySelectorAll('#dataTable tbody tr:not(.group-header)');
    dataRows.forEach((tr, i) => {
        tr.classList.toggle('row-active', i === activeRowIndex);
    });
}
```

In `renderTable()`, after building rows, re-apply highlight:
```javascript
highlightRow(); // re-apply .row-active after re-render
```

**CSS:**
```css
.row-active { background: var(--color-primary-container) !important; border-left: 3px solid var(--color-primary); }
```

### 22. Review Next Auto-Advance (§7j)

After any approve/reject action completes (approve notes confirmed, reject reason confirmed), auto-advance to the next Pending request:

```javascript
function reviewNextAfterAction(actedOnId) {
    const actedIndex = currentFiltered.findIndex(r => r.id === actedOnId);
    // Find next Pending after the acted-on request
    let nextIndex = -1;
    for (let i = actedIndex + 1; i < currentFiltered.length; i++) {
        if (currentFiltered[i].status === 'Pending') { nextIndex = i; break; }
    }
    if (nextIndex >= 0) {
        activeRowIndex = nextIndex;
        openModal(nextIndex); // opens detail modal; PL can then A/R from there
    } else {
        // No more Pending — close modal, clear highlight
        activeRowIndex = -1;
        closeModal();
    }
    renderTable(); // update counts/badges after status change
}
```

Called at the end of:
- `confirmApproveWithNotes()` → after `alert()` success → `reviewNextAfterAction(ids[0])`
- `rejectRequest()` → after `alert()` success → `reviewNextAfterAction(rejectingId)`

**Scope:** Only auto-advances when user explicitly approves/rejects. Does NOT auto-advance on modal close (Escape / overlay click). Does NOT auto-advance pages — if last Pending on current page is acted on, modal closes.

### 23. Slot Validity Preview Icon (§7k)

Show `slotValidity` value as a tiny icon in the Proposed Replacement cell during `renderTable()`:

```javascript
// Inside renderTable(), in the Proposed Replacement column cell:
const validityMap = { Valid: '✓', Conflict: '⚠', Tentative: '?' };
const validityClass = { Valid: 'slot-valid', Conflict: 'slot-conflict', Tentative: 'slot-tentative' };
const validityTip = { Valid: 'Slot available — no conflict', Conflict: 'Conflict — another class scheduled', Tentative: 'Pending venue confirmation' };
const slotIcon = validityMap[r.slotValidity] || '';
const slotClass = validityClass[r.slotValidity] || '';
const slotTitle = validityTip[r.slotValidity] || '';

// Append to Proposed Replacement cell HTML:
// <span class="slot-icon ${slotClass}" title="${slotTitle}">${slotIcon}</span>
```

**CSS (in page-styles `@section`):**
```css
.slot-icon { font-size: 11px; margin-left: 4px; font-weight: 600; }
.slot-valid { color: var(--color-approved, #2e7d32); }
.slot-conflict { color: var(--color-rejected, #c62828); }
.slot-tentative { color: var(--color-amber, #f59e0b); }
```

CSS: `.row-viewed td:first-child { border-left: 3px solid var(--color-primary); }` — subtle left-border accent.

### 24. Toast Notifications (§7l)

Replace all `alert()` calls with `showToast(message, undoCallback, duration)` from `ui-common.js:387`. The toast bar HTML exists in `ui-template.blade.php:44`; CSS in `theme.css:1653`.

**Refactor mapping:**

| Current code | Replacement |
|-------------|-------------|
| `alert('Request #' + id + ' approved...')` in `approveRequest()` | `showToast('Request #' + id + ' approved.', undoFn, 5000)` |
| `alert(label + ' rejected...')` in `rejectRequest()` | `showToast(label + ' rejected.', undoFn, 5000)` |
| `alert(ids.length + ' request(s) approved...')` in `bulkApprove()` | `showToast(ids.length + ' request(s) approved.', undoFn, 5000)` |
| `alert(label + ' approved...')` in `confirmApproveWithNotes()` | `showToast(label + ' approved.', undoFn, 5000)` |
| `alert('Please provide a rejection reason.')` in `rejectRequest()` | `showToast('Please provide a rejection reason.', null, 3000)` |

**Flow change:** After `showToast()`, `reviewNextAfterAction()` is called immediately (not after a blocking alert). The toast is non-blocking — the PL can continue interacting while it's visible.

### 25. Undo Stack (§7m)

After approve/reject, store the previous status and provide an undo callback:

```javascript
// In approveRequest(id):
    const r = MockData.approvalRequests.find(x => x.id === id);
    const prevStatus = r.status;
    r.status = 'Approved';
    renderTable();
    updateNavBadge();
    showToast('Request #' + id + ' approved.', function() {
        r.status = prevStatus;
        renderTable();
        updateNavBadge();
    });
```

**State:** No dedicated state variable — the undo callback captures `prevStatus` in a closure. Only the **last action** is undoable. When the toast auto-dismisses (5s) or is manually closed, the undo callback is cleared (no reference held).

**Known UX quirk (design-phase):** When review-next auto-advance fires (§22), the modal shows the *next* request while the toast shows undo for the *previous* request. The user sees request B's modal but the toast says "Request #A approved — Undo." This is accepted as a design-phase simulation quirk. In the backend phase, the toast message will include the request ID prominently, and the modal will update to show the reverted request on undo.

**Scope:** Undo restores in-memory status only (design phase simulation). In the backend phase, undo would call a revert API endpoint.

### 26. Animated Transitions (§7n)

**Row flash on status change:**

```javascript
// After renderTable() following approve/reject:
const row = document.querySelector('tr[data-id="' + id + '"]');
if (row) {
    row.classList.add(status === 'Approved' ? 'row-flash-approved' : 'row-flash-rejected');
    setTimeout(() => row.classList.remove('row-flash-approved', 'row-flash-rejected'), 600);
}
```

**CSS:**
```css
.row-flash-approved { animation: flashGreen 0.6s ease; }
.row-flash-rejected { animation: flashRed 0.6s ease; }
@keyframes flashGreen { 0% { background: var(--color-secondary-container); } 100% { background: transparent; } }
@keyframes flashRed { 0% { background: var(--color-error-container); } 100% { background: transparent; } }
```

**Filter fade:** `.grid-scroll` already has `transition: opacity 0.1s` in `theme.css:1130`. Add class toggle:
```javascript
// In renderTable() and setGroupFilter():
gridScroll.classList.add('grid-transitioning');
requestAnimationFrame(() => { gridScroll.classList.remove('grid-transitioning'); });
```

**Group expand/collapse:**
```css
.group-body { transition: max-height 0.3s ease, opacity 0.2s; overflow: hidden; }
.group-body.collapsed { max-height: 0; opacity: 0; }
```

### 27. Smart Grouping (§7o)

**State:** `groupField = 'none'` — toggled by `setGroupFilter(value)`.

**Toolbar HTML:** Add after urgency filter chips, before week filter:
```html
<select id="groupFilter" onchange="setGroupFilter(this.value)">
    <option value="none">No grouping</option>
    <option value="course">Group by Course</option>
    <option value="lecturer">Group by Lecturer</option>
</select>
```

**Algorithm in renderTable():**
```javascript
function renderTable() {
    // ... existing filter/sort logic produces currentFiltered ...
    
    let html = '';
    if (groupField !== 'none') {
        const groups = {};
        currentFiltered.forEach(r => {
            const key = groupField === 'course' ? r.courseCode : r.lecturer;
            if (!groups[key]) groups[key] = [];
            groups[key].push(r);
        });
        const sortedKeys = Object.keys(groups).sort();
        sortedKeys.forEach(key => {
            const rows = groups[key];
            const isCollapsed = collapsedGroups.has(key);
            html += '<tr class="group-header" onclick="toggleGroup(\'' + key.replace(/'/g, "\\'") + '\')">';
            html += '<td colspan="11"><span class="group-arrow">' + (isCollapsed ? '▶' : '▼') + '</span> ';
            html += '<strong>' + key + '</strong> <span class="group-count">' + rows.length + '</span></td></tr>';
            html += '<tbody class="group-body' + (isCollapsed ? ' collapsed' : '') + '">';
            rows.forEach(r => { html += renderRow(r); }); // existing per-row render
            html += '</tbody>';
        });
    } else {
        currentFiltered.forEach(r => { html += renderRow(r); });
    }
    // ... pagination, summary updates ...
}
```

**State:** `collapsedGroups = new Set()` — stores collapsed group keys. `toggleGroup(key)` adds/removes from set, re-renders.

**Interaction with pagination:** Group headers count as 1 row for pagination. If a group spans pages, it is split at the page boundary (group header repeated on next page if needed).

**Interaction with keyboard nav:** Arrow keys skip `.group-header` rows — only navigate data rows.

### 28. Mini Timeline (§7p)

Add a visual lifecycle timeline at the top of the detail modal (`.modal-body`), before Section 1.

**Mock data addition:** Add `viewedAt` (ISO string or null) and `reviewedAt` (ISO string or null) to each `MockData.approvalRequests` entry. For Pending items, `reviewedAt` is null. For all items, `viewedAt` is set to a time after `requestedAt`.

**HTML (generated in `openModal()`):**
```javascript
function buildTimeline(r) {
    const steps = [
        { label: 'Submitted', time: r.requestedAt, done: true },
        { label: 'Viewed', time: r.viewedAt, done: !!r.viewedAt },
        { label: 'Reviewed', time: r.reviewedAt, done: !!r.reviewedAt }
    ];
    let html = '<div class="request-timeline">';
    steps.forEach((s, i) => {
        const cls = s.done ? 'completed' : (i === steps.filter(x => !x.done).length - 1 ? 'active' : 'pending');
        html += '<div class="timeline-step ' + cls + '">';
        html += '<div class="timeline-dot"></div>';
        html += '<div class="timeline-label">' + s.label + '</div>';
        html += '<div class="timeline-time">' + (s.time ? formatDateTime(s.time) : '—') + '</div>';
        html += '</div>';
        if (i < steps.length - 1) html += '<div class="timeline-connector ' + (s.done ? 'completed' : '') + '"></div>';
    });
    html += '</div>';
    return html;
}
```

**CSS (in page-styles `@section`):**
```css
.request-timeline { display: flex; align-items: center; gap: 0; padding: 12px 0 16px; border-bottom: 1px solid var(--color-outline-variant); margin-bottom: 16px; }
.timeline-step { display: flex; flex-direction: column; align-items: center; gap: 4px; position: relative; z-index: 1; }
.timeline-dot { width: 12px; height: 12px; border-radius: 50%; border: 2px solid var(--color-outline); background: var(--color-surface); transition: all 0.3s; }
.timeline-step.completed .timeline-dot { background: var(--color-secondary); border-color: var(--color-secondary); }
.timeline-step.active .timeline-dot { background: var(--color-tertiary); border-color: var(--color-tertiary); animation: pulse 1.5s infinite; }
.timeline-label { font-size: 11px; font-weight: 500; color: var(--color-on-surface-variant); }
.timeline-time { font-size: 10px; color: var(--color-on-surface-variant); opacity: 0.7; }
.timeline-connector { flex: 1; height: 2px; background: var(--color-outline-variant); min-width: 40px; }
.timeline-connector.completed { background: var(--color-secondary); }
.timeline-connector.active { background: linear-gradient(90deg, var(--color-secondary), var(--color-tertiary)); }
@keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(156, 39, 176, 0.4); } 50% { box-shadow: 0 0 0 6px rgba(156, 39, 176, 0); } }
```

### 29. Skeleton Loading (§7q)

Show skeleton placeholder rows on initial load and filter changes.

**Existing CSS:** `.skeleton`, `.skeleton-row` (60px), `.skeleton-card` (80px), `.skeleton-text` (14px, 80% width) with `skeleton-shimmer` animation in `theme.css:1570`.

**Implementation:**
```javascript
function showSkeleton() {
    const tbody = document.querySelector('#dataTable tbody');
    tbody.innerHTML = '';
    for (let i = 0; i < 10; i++) {
        const tr = document.createElement('tr');
        for (let j = 0; j < 11; j++) {
            const td = document.createElement('td');
            td.innerHTML = '<div class="skeleton" style="height:16px;width:' + (60 + Math.random() * 40) + '%"></div>';
            tr.appendChild(td);
        }
        tbody.appendChild(tr);
    }
    document.querySelectorAll('.summary-card .summary-value').forEach(el => {
        el.innerHTML = '<div class="skeleton" style="height:24px;width:40px;display:inline-block"></div>';
    });
}
```

**Trigger points:**
1. `DOMContentLoaded` → `showSkeleton()` → 300ms delay → `renderTable()`
2. `renderTable()` called from filter/sort change → brief skeleton (150ms) → real render

```javascript
let isInitialLoad = true;

// In DOMContentLoaded:
showSkeleton();
setTimeout(() => { isInitialLoad = false; renderTable(); updateNavBadge(); }, 300);

// In renderTable() — brief skeleton flash for filter/sort changes only:
if (!isInitialLoad) {
    showSkeleton();
    setTimeout(() => { renderTableBody(); }, 150);
    return; // renderTableBody() does the actual render
}
// ... full render on initial load
```

## Dependencies

- `theme.css` — shared component CSS (loaded via layout `<link>`)
- `ui-common.js` — shared JS module (loaded via layout `<script>`): `to12h()`, `formatDate()`, `compareBy()`, `makeSortableHeader()`, `paginate()`, `updateResultCount()`, `closeOnOverlayClick()`, plus the 10 promoted helpers from my-request-history (`weekRanges`, `formatDateTime()`, `statusClass()`, `dayAbbr()`, `isoDayName()`, `formatClassBlock()`, `formatReplacementBlock()`, `getWeekRange()`, `isInWeek()`, `getWeekNumber()`) — note: `closeOnEsc()` is NOT used on this page (replaced by ONE keydown handler that closes the topmost modal, see §12)
- `mock-data.js` — **shared data module** (loaded via layout `<script>` after ui-common.js): `MockData.approvalRequests` (20 entries) + `MockData.urgencyReferenceDate`
- `partials/ui-summary-bar.blade.php` — reuse for summary cards
- `partials/ui-nav-bar.blade.php` — auto-included by layout (needs "Request Approval" link added)
- `layouts/ui-template.blade.php` — base layout with `@yield` sections (modified to add the mock-data.js script tag)

## File Changes

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php` | **Create** — full page with CSS, HTML, JS (reads `MockData.approvalRequests` + `MockData.urgencyReferenceDate` from mock-data.js, does not embed them) |
| `public/js/mock-data.js` | **Create** — shared data module: `MockData.approvalRequests` (20 entries) + `MockData.urgencyReferenceDate` |
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
