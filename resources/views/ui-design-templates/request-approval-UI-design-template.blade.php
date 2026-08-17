@extends('layouts.ui-template', ['activeNav' => 'request-approval'])

@section('title', 'Request Approval')

@section('page-styles')

        /* ───── Column Widths ───── */
        .col-no { width: 40px; text-align: center; }
        .col-original, .col-replacement { white-space: normal; }

        .col-replacement .cell-class-block .class-time {
            font-weight: 600;
            white-space: nowrap;
        }
        .col-replacement .cell-class-block .class-venue {
            font-size: 11px;
            color: var(--color-on-surface-variant);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        /* Slot-validity badge — sits to the LEFT of the venue; bg varies by status */
        .slot-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            flex-shrink: 0;
        }
        .slot-badge.slot-valid {
            background: var(--color-success-container);
            color: var(--color-on-success-container);
            border: 1px solid var(--color-success);
        }
        .slot-badge.slot-conflict {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
            border: 1px solid var(--color-error);
        }

        /* ───── Status Badges (page-specific overrides) ───── */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: filter 0.15s;
        }
        .badge:hover {
            filter: brightness(1.1);
        }
        .badge-subtitle {
            display: block;
            font-size: 11px;
            font-weight: 400;
            opacity: 0.7;
            margin-top: 2px;
        }

        /* ───── Urgency Badges ───── */
        .urgency-badge { display: inline-block; padding: 2px 8px; border-radius: var(--radius-md); font-size: 11px; font-weight: 600; }
        .urgency-urgent { background: var(--color-error-container); color: var(--color-on-error-container); }
        .urgency-normal { background: var(--color-success-container); color: var(--color-on-success-container); }

        /* ───── Action Buttons ───── */
        .btn-approve { padding: 4px 10px; border-radius: var(--radius-sm); border: 1px solid var(--color-success); background: transparent; color: var(--color-success); font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s, color 0.15s; }
        .btn-approve:hover { background: var(--color-success); color: var(--color-on-success); }
        .btn-reject { padding: 4px 10px; border-radius: var(--radius-sm); border: 1px solid var(--color-error); background: transparent; color: var(--color-error); font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s, color 0.15s; }
        .btn-reject:hover { background: var(--color-error); color: var(--color-on-error); }
        .btn-view { padding: 4px 10px; border-radius: var(--radius-sm); border: 1px solid var(--color-outline); background: transparent; color: var(--color-on-surface-variant); font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s; }
        .btn-view:hover { background: var(--color-surface-variant); }

        /* ───── Summary Card Colors (approved/rejected/total accents in theme.css) ───── */

        /* ───── Bulk Selection (page-specific overrides) ───── */
        .col-checkbox input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--color-primary); cursor: pointer; }
        .bulk-action-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            background: var(--color-surface);
            border-top: 1px solid var(--color-outline);
            box-shadow: var(--shadow-lg);
        }
        .bulk-action-bar.visible { display: flex; }
        .bulk-action-bar .bulk-count { font-size: 13px; font-weight: 600; color: var(--color-on-surface); }
        .btn-bulk-clear {
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--color-outline);
            background: transparent;
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-bulk-clear:hover { background: var(--color-surface-variant); }

        /* ───── Urgency Filter Chips ───── */
        .urgency-filter { display: inline-flex; gap: 4px; margin-left: 8px; }
        .urgency-filter-chip {
            padding: 4px 10px;
            border-radius: var(--radius-lg);
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

        /* ───── Request Age Indicator (base .request-age styles in theme.css) ───── */

        /* ───── Reject Preset Chips ───── */
        .reject-presets { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
        .reject-preset-chip {
            padding: 4px 10px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-outline);
            background: transparent;
            color: var(--color-on-surface-variant);
            font-size: 12px;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .reject-preset-chip:hover { background: var(--color-surface-variant); }

        /* ───── Viewed Row Indicator ───── */
        .row-viewed td:first-child { border-left: 3px solid var(--color-primary); }

        /* ───── Keyboard Highlight ───── */
        .row-focused { background: var(--color-primary-container) !important; border-left: 3px solid var(--color-primary); }

        /* ───── Row Flash Animations ───── */
        .row-flash-approved { animation: flashGreen 0.6s ease; }
        .row-flash-rejected { animation: flashRed 0.6s ease; }
        @keyframes flashGreen { 0% { background: var(--color-success-container); } 100% { background: transparent; } }
        @keyframes flashRed { 0% { background: var(--color-error-container); } 100% { background: transparent; } }

        /* ───── Row Urgency Indicators ───── */
        .row-urgent { border-left: 3px solid var(--color-error); }
        .row-soon { border-left: 3px solid var(--color-tertiary); }

        /* ───── Lecturer Cell ───── */
        .lecturer-cell-name { font-weight: 600; font-size: 13px; color: var(--color-on-surface); }
        .lecturer-cell-id { font-size: 11px; color: var(--color-on-surface-variant); opacity: 0.7; font-weight: 400; }
        .lecturer-cell-email {
            font-size: 12px; color: var(--color-primary); cursor: pointer;
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: var(--radius-md);
            background: var(--color-primary-container); color: var(--color-on-primary-container);
            transition: opacity 0.15s;
        }
        .lecturer-cell-email:hover { opacity: 0.8; }
        .lecturer-cell-email .copy-icon { font-size: 10px; opacity: 0.6; }

        /* ───── Responsive Card View (base .request-card styles in theme.css) ───── */
        @media (max-width: 768px) {
            .grid-wrapper, .pagination-bar, .sort-hint { display: none !important; }
            .requests-cards { display: grid !important; gap: 12px; padding: 12px; }
            .card-view { display: block; }
        }
        @media (min-width: 769px) {
            .requests-cards { display: none !important; }
        }

@endsection

@section('content')
<div class="page-header">
    <h1>Request Approval</h1>
    <p>Review and manage replacement requests from lecturers in your program.</p>
</div>

@include('partials.ui-guide-block', [
    'guideTitle' => 'How to use this page',
    'guideItems' => [
        '<strong>Sort</strong> — click any column header to sort ascending/descending',
        '<strong>Search</strong> — type in the search box to filter by course, lecturer, or keywords',
        '<strong>Filter</strong> — use the status dropdown or urgency chips to narrow results',
        '<strong>Hover headers</strong> — hover a column name to see what it means',
        '<strong>Bulk action</strong> — tick checkboxes then approve/reject multiple requests at once',
        '<strong>Quick action</strong> — use the approve/reject buttons on individual rows',
        '<strong>Request age</strong> — colour indicates how long ago it was submitted: <span style="color:var(--color-primary)">● ≤1 day</span> <span style="color:var(--color-tertiary)">● 2–3 days</span> <span style="color:var(--color-error)">● 4+ days</span>',
    ]
])

<div class="toolbar">
    <div class="toolbar-left">
        <input class="search-input" id="searchInput" placeholder="Search course code, name, or lecturer...">
        <select class="filter-select" id="statusFilter">
            <option value="all">All Status</option>
            <option value="Pending" selected>Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
            <option value="Cancelled">Cancelled</option>
            <option value="Completed">Completed</option>
        </select>
        <div class="urgency-filter" id="urgencyFilter">
            <button class="urgency-filter-chip active" data-urgency="all" onclick="setUrgencyFilter('all')">All</button>
            <button class="urgency-filter-chip" data-urgency="urgent" onclick="setUrgencyFilter('urgent')">Urgent</button>
            <button class="urgency-filter-chip" data-urgency="normal" onclick="setUrgencyFilter('normal')">Normal</button>
        </div>
        @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeekFilter()', 'nextOnclick' => 'nextWeekFilter()', 'selectId' => 'weekFilter', 'selectOnclick' => 'onWeekFilterChange(this.value)', 'showTodayBtn' => false])
        <label class="toggle-wrapper" id="completedToggle">
            <input type="checkbox" id="hideCompletedToggle" checked onchange="toggleHideCompleted()">
            <span class="toggle-track"><span class="toggle-thumb"></span></span>
            <span class="toggle-label">Hide Completed</span>
        </label>
        <button class="btn-clear" id="clearFilters">Reset Filters</button>
    </div>
    <div class="toolbar-right">
        <span class="result-count" id="resultCount"></span>
    </div>
</div>

<div class="sort-hint">Click column headers to sort (Requested Timestamp, Original Class, Proposed Replacement, Urgency, Status)</div>

<div class="filter-chips" id="filterChips"></div>

<div class="grid-wrapper" id="gridWrapper">
    <div class="grid-scroll">
        <table class="timetable data-table" id="timetable">
            <thead id="tableHead"></thead>
            <tbody id="tableBody"></tbody>
        </table>
    </div>
</div>

<div class="requests-cards" id="requestsCards" style="display:none"></div>

<div class="pagination-bar" id="paginationBar">
    @include('partials.ui-rpp', ['id' => 'rowsPerPage', 'default' => 10, 'options' => [10, 20, 50]])
    <span class="pagination-info" id="paginationInfo"></span>
    <div class="pagination-controls" id="paginationControls"></div>
</div>

<div class="bulk-action-bar" id="bulkActionBar">
    <span class="bulk-count" id="bulkCount"></span>
    <button class="btn-bulk-clear" onclick="clearSelection()">Clear</button>
    <button class="btn-approve" onclick="bulkApprove()">Approve Selected</button>
    <button class="btn-reject" onclick="bulkReject()">Reject Selected</button>
</div>

@include('partials.ui-summary-bar', ['cards' => [
    ['class' => 'card-total', 'valueId' => 'summaryTotal', 'label' => 'Total Requests',
        'description' => 'Replacement requests <strong>matching your current filters</strong> in the selected period.'],
    ['class' => 'card-pending', 'valueId' => 'summaryPending', 'label' => 'Pending Requests',
        'description' => 'Requests still <strong>waiting for your approval</strong> — no decision made yet.'],
    ['class' => 'card-approved', 'valueId' => 'summaryApproved', 'label' => 'Approved',
        'description' => 'Requests you have <strong>approved</strong> and are ready to proceed.'],
    ['class' => 'card-rejected', 'valueId' => 'summaryRejected', 'label' => 'Rejected',
        'description' => 'Requests you <strong>declined</strong> — the lecturer will need an alternative arrangement.'],
    ['class' => 'card-total', 'valueId' => 'summaryReviewed', 'label' => 'Total Reviewed',
        'description' => 'Requests already <strong>decided</strong> (approved, rejected, or completed).']
]])

<div class="empty-state" id="emptyState" style="display:none">
    <div class="empty-icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <h3 id="emptyTitle">No requests found</h3>
    <p id="emptyText">No requests match your search or filter criteria.</p>
</div>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modalTitle">Request Details</span>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <div class="modal-footer-left">
                <button class="btn-outline" id="closeModalBtn" onclick="closeModal()">Close</button>
            </div>
            <div class="modal-footer-right" style="gap: 8px">
                <button class="btn-danger" id="rejectRequestBtn" onclick="openRejectModal(currentModalId)" style="display:none">✕ Reject</button>
                <button class="btn-approve" id="approveRequestBtn" onclick="approveRequest(currentModalId)" style="display:none">✓ Approve</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="rejectReasonModal">
    <div class="modal">
        <div class="modal-header">
            <h2>Rejection Reason</h2>
            <button class="modal-close" onclick="closeRejectModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="reject-presets">
                <button class="reject-preset-chip" onclick="applyRejectPreset('Venue unavailable')">Venue unavailable</button>
                <button class="reject-preset-chip" onclick="applyRejectPreset('Insufficient notice')">Insufficient notice</button>
                <button class="reject-preset-chip" onclick="applyRejectPreset('Slot conflict')">Slot conflict</button>
                <button class="reject-preset-chip" onclick="applyRejectPreset('Lecturer unavailable')">Lecturer unavailable</button>
                <button class="reject-preset-chip" onclick="applyRejectPreset('')">Other</button>
            </div>
            <p class="section-heading-sub" style="margin-bottom:8px">Please provide a reason for rejection (required):</p>
            <textarea id="rejectReasonInput" placeholder="Enter rejection reason..." rows="4" class="modal-textarea"></textarea>
        </div>
        <div class="modal-footer">
            <div class="modal-footer-left">
                <button class="btn-outline" id="cancelRejectBtn" onclick="closeRejectModal()">Cancel</button>
            </div>
            <div class="modal-footer-right">
                <button class="btn-danger" id="confirmRejectBtn" disabled onclick="rejectRequest()">Confirm Reject</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="approveNotesModal">
    <div class="modal">
        <div class="modal-header">
            <h2>Approve Request</h2>
            <button class="modal-close" onclick="closeApproveNotesModal()">✕</button>
        </div>
        <div class="modal-body">
            <div id="approveNotesSummary" style="margin-bottom:12px;font-size:13px;white-space:pre-line;color:var(--color-on-surface)"></div>
            <p class="section-heading-sub" style="margin-bottom:8px">Notes (optional):</p>
            <textarea id="approveNotesInput" placeholder="Optional note for the audit trail..." rows="2" class="modal-textarea"></textarea>
        </div>
        <div class="modal-footer">
            <div class="modal-footer-left">
                <button class="btn-outline" onclick="closeApproveNotesModal()">Cancel</button>
            </div>
            <div class="modal-footer-right">
                <button class="btn-approve" id="confirmApproveBtn" onclick="confirmApproveWithNotes()">✓ Approve</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
        initHeaderTooltips();
        // ── Shared data from mock-data.js ──

        // ── Page-local urgency helpers ──
        function urgencyLevel(classDate) {
            const target = new Date(classDate + 'T00:00:00');
            const diffDays = Math.ceil((target - MockData.urgencyReferenceDate) / (1000 * 60 * 60 * 24));
            return diffDays <= 3 ? 'urgent' : 'normal';
        }

        function urgencyClass(level) {
            return level === 'urgent' ? 'urgency-urgent' : 'urgency-normal';
        }

        function urgencyLabel(level) {
            return level === 'urgent' ? 'Urgent' : 'Normal';
        }

        function urgencyDays(classDate) {
            const target = new Date(classDate + 'T00:00:00');
            return Math.ceil((target - MockData.urgencyReferenceDate) / (1000 * 60 * 60 * 24));
        }

        // ── Lecturer lookup + copy email ──
        function lookupLecturer(name) {
            return MockData.lecturers.find(function(l) { return l.name === name; }) || null;
        }

        function copyEmail(email, event) {
            event.stopPropagation();
            navigator.clipboard.writeText(email).then(function() {
                toast.show('Email copied: ' + email, null, 2000);
            });
        }

        // ── Slot validity helper ──
        function slotValidityHtml(r) {
            const badge = '<span class="slot-badge ' + (r.slotValidity === 'conflict' ? 'slot-conflict' : 'slot-valid') + '">' + (r.slotValidity === 'conflict' ? '⚠' : '✓') + '</span>';
            if (r.slotValidity === 'conflict') {
                return badge + (r.conflictReason ? ' <span style="color:var(--color-error);font-size:11px">' + r.conflictReason + '</span>' : '');
            }
            return badge;
        }

        // ── Request age helper (§7.5) ──
        // ── Filter persistence (§7) ──
        function saveFilters() {
            const state = {
                weekFilter: document.getElementById('weekFilter')?.value || 'all',
                statusFilter: document.getElementById('statusFilter')?.value || 'Pending',
                urgencyFilter: urgencyFilter,
                rpp: rpp,
                sortField: sortState.field,
                sortDir: sortState.dir,
                hideCompleted: hideCompleted
            };
            localStorage.setItem('request-approval-filters', JSON.stringify(state));
        }

        function restoreFilters() {
            const saved = localStorage.getItem('request-approval-filters');
            if (!saved) return false;
            try {
                const state = JSON.parse(saved);
                if (state.weekFilter) document.getElementById('weekFilter').value = state.weekFilter;
                if (state.statusFilter) document.getElementById('statusFilter').value = state.statusFilter;
                if (state.urgencyFilter) { urgencyFilter = state.urgencyFilter; document.querySelectorAll('.urgency-filter-chip').forEach(btn => { btn.classList.toggle('active', btn.dataset.urgency === urgencyFilter); }); }
                if (state.rpp) { rpp = state.rpp; document.getElementById('rowsPerPage').value = state.rpp; }
                if (state.sortField) { sortState.field = state.sortField; sortState.dir = state.sortDir || 'asc'; }
                if (state.hideCompleted !== undefined) { hideCompleted = state.hideCompleted; document.getElementById('hideCompletedToggle').checked = hideCompleted; }
                return true;
            } catch (e) { return false; }
        }

        // ── Approve summary helper (§7.2) ──
        function approveSummary(r) {
            return '#' + r.id + ' ' + r.courseCode + ' — ' + r.courseName +
                '\nLecturer: ' + r.lecturer +
                '\nOriginal: ' + dayAbbr(r.classDay) + ' ' + DateHelper.formatDate(r.classDate) + ' ' + r.timeStart + '–' + r.timeEnd +
                '\nReplacement: ' + DateHelper.formatDate(r.replacementDate) + ' ' + r.replacementTime +
                '\nVenue: ' + (r.replacementVenue || r.venue);
        }

        // ── Sort state ──
        let sortState = { field: 'requestedAt', dir: 'asc' };
        let currentPage = 1;
        let rpp = 10;
        const RPP_OPTIONS = [10, 20, 50];

        // ── Feature state ──
        let bulk = new BulkSelection({ barId: 'bulkActionBar', countId: 'bulkCount' });
        let viewedIds = new Set();
        let currentApproveIds = [];
        let currentRejectId = null;
        let urgencyFilter = 'all';
        let focusedRowIndex = -1;
        let isInitialLoad = true;
        let hideCompleted = true;

        // ── RPP (rows per page) ──
        function initLocalRpp() {
            const saved = localStorage.getItem('request-approval-rpp');
            if (saved && RPP_OPTIONS.includes(parseInt(saved))) {
                rpp = parseInt(saved);
                document.getElementById('rowsPerPage').value = saved;
            }
        }

        function toggleHideCompleted() {
            hideCompleted = document.getElementById('hideCompletedToggle').checked;
            currentPage = 1;
            renderTable();
            updateSummary();
            saveFilters();
        }

        // ── Table columns ──
        const columns = [
            { label: '#', sortable: false, tip: 'Row number' },
            { label: 'Requested Timestamp', sortable: true, field: 'requestedAt', tip: 'When the replacement was requested. Age colour: green ≤1 day, amber 2–3 days, red 4+ days' },
            { label: 'Lecturer', sortable: false, tip: 'Lecturer who submitted the request' },
            { label: 'Course Code & Name', sortable: false, tip: 'Course affected by the conflict' },
            { label: 'Original Class', sortable: true, field: 'classDate', tip: 'Original class the request refers to — its state varies (still upcoming, replaced, cancelled, holiday, etc.)' },
            { label: 'Proposed Replacement', sortable: true, field: 'replacementDate', tip: 'Proposed new date, time, and venue' },
            { label: 'Students', sortable: false, tip: 'Number of students enrolled' },
            { label: 'Urgency', sortable: true, field: 'urgencyDays', tip: 'Days until the original class: ≤3 days = Urgent (red), 4+ days = Normal' },
            { label: 'Status', sortable: true, field: 'status', tip: 'Current approval status' },
            { label: 'Actions', sortable: false, tip: 'Approve or reject this request' }
        ];

        // ── Filter state ──
        let currentFiltered = [];

        // ── Render table header ──
        function renderHeader() {
            const thead = document.getElementById('tableHead');
            let html = '<tr><th class="col-checkbox"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>';
            columns.forEach((col, i) => {
                const tip = col.tip ? ' data-tip="' + col.tip + '"' : '';
                if (col.sortable) {
                    const arrow = sortState.field === col.field ? (sortState.dir === 'asc' ? ' ▲' : ' ▼') : '';
                    html += '<th class="sortable"' + tip + ' onclick="toggleSort(\'' + col.field + '\')">' + col.label + arrow + '</th>';
                } else {
                    html += '<th' + tip + '>' + col.label + '</th>';
                }
            });
            html += '</tr>';
            thead.innerHTML = html;
        }

        // ── Sort logic ──
        function toggleSort(field) {
            if (sortState.field === field) {
                sortState.dir = sortState.dir === 'asc' ? 'desc' : 'asc';
            } else {
                sortState.field = field;
                sortState.dir = 'asc';
            }
            currentPage = 1;
            renderTable();
            saveFilters();
        }

        function sortData(data) {
            const field = sortState.field;
            const dir = sortState.dir === 'asc' ? 1 : -1;
            return [...data].sort((a, b) => {
                let va, vb;
                if (field === 'urgencyDays') {
                    va = urgencyDays(a.classDate);
                    vb = urgencyDays(b.classDate);
                    return (va - vb) * dir;
                }
                va = a[field] || '';
                vb = b[field] || '';
                if (typeof va === 'string') return va.localeCompare(vb) * dir;
                return (va - vb) * dir;
            });
        }

        // ── Filter logic ──
        function filterData() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const status = document.getElementById('statusFilter').value;
            const week = document.getElementById('weekFilter').value;

            let result = MockData.approvalRequests.filter(r => {
                if (status !== 'all' && r.status !== status) return false;
                if (week !== 'all' && !isInWeek(r.classDate, week)) return false;
                if (hideCompleted && r.status === 'Completed') return false;
                if (search) {
                    const haystack = (r.courseCode + ' ' + r.courseName + ' ' + r.lecturer).toLowerCase();
                    if (!haystack.includes(search)) return false;
                }
                return true;
            });

            if (urgencyFilter !== 'all') {
                result = result.filter(r => urgencyLevel(r.classDate) === urgencyFilter);
            }

            return result;
        }

        // ── Render a single row ──
        function renderRow(r, offset, i) {
            const level = urgencyLevel(r.classDate);
            const isSelected = bulk.has(r.id);
            const isViewed = viewedIds.has(r.id);
            let rowClass = '';
            if (isViewed) rowClass += ' row-viewed';
            if (isSelected) rowClass += ' row-selected';
            if (level === 'urgent') rowClass += ' row-urgent';
            else if (urgencyDays(r.classDate) <= 7) rowClass += ' row-soon';

            var lecName = (typeof lookupLecturer === 'function' && lookupLecturer(r.lecturer)) ? lookupLecturer(r.lecturer).name : r.lecturer;

            let html = '<tr data-id="' + r.id + '"' + (rowClass ? ' class="' + rowClass.trim() + '"' : '')
                + ' onclick="openModalById(' + r.id + ')" style="cursor:pointer">';

            // Checkbox column — stopPropagation so clicking it doesn't open the row modal
            if (r.status === 'Pending') {
                html += '<td class="col-checkbox"><input type="checkbox" ' + (isSelected ? 'checked' : '') + ' onchange="toggleRowSelect(' + r.id + ')" onclick="event.stopPropagation()"></td>';
            } else {
                html += '<td class="col-checkbox"></td>';
            }

            html += '<td>' + (offset + i + 1) + '</td>';
            html += '<td>' + formatDateTime(r.requestedAt) + requestAgeHtml(r.requestedAt, urgencyLabel(level)) + '</td>';
            var lec = lookupLecturer(r.lecturer);
            if (lec) {
                html += '<td><div class="lecturer-cell-name">' + lec.name + ' <span class="lecturer-cell-id">(' + lec.staffId + ')</span></div>';
                html += '<div class="lecturer-cell-email" onclick="copyEmail(\'' + lec.email + '\', event)" data-tip="Click to copy email">' + lec.email + ' <span class="copy-icon">📋</span></div></td>';
            } else {
                html += '<td>' + r.lecturer + '</td>';
            }
            html += '<td><div class="cell-code">' + r.courseCode + '</div><div class="cell-name">' + r.courseName + '</div></td>';
            html += '<td>' + HtmlBuilder.classBlock(r) + '</td>';

            // Proposed Replacement — slot validity as a status-colored badge beside the venue
            const slotBadge = '<span class="slot-badge ' + (r.slotValidity === 'conflict' ? 'slot-conflict' : 'slot-valid') + '" data-tip="' + (r.slotValidity === 'conflict' ? (r.conflictReason || 'Slot conflict') : 'Slot available') + '">' + (r.slotValidity === 'conflict' ? '⚠' : '✓') + '</span>';
            html += '<td class="col-replacement">' + HtmlBuilder.replacementBlock(r, { colorStatus: false, slotBadge: slotBadge }) + '</td>';

            html += '<td>' + r.totalStudents + '</td>';
            html += '<td><span class="urgency-badge ' + urgencyClass(level) + '">' + urgencyLabel(level) + '</span></td>';
            html += '<td><span class="badge ' + statusClass(r.status) + '" onclick="openModalById(' + r.id + ')" style="cursor:pointer">' + r.status + '</span></td>';

            const actions = r.status === 'Pending'
                ? '<div class="action-row"><button class="btn-approve" onclick="event.stopPropagation();approveRequest(' + r.id + ')">✓ Approve</button><button class="btn-reject" onclick="event.stopPropagation();openRejectModal(' + r.id + ')">✕ Reject</button></div>'
                : '<button class="btn-view" onclick="event.stopPropagation();openModalById(' + r.id + ')">👁 View</button>';
            html += '<td>' + actions + '</td>';
            html += '</tr>';
            return html;
        }

        // ── Render table body ──
        function renderBody() {
            const tbody = document.getElementById('tableBody');
            const sorted = sortData(currentFiltered);
            const offset = (currentPage - 1) * rpp;
            const page = sorted.slice(offset, offset + rpp);

            if (page.length === 0) {
                tbody.innerHTML = '';
                document.getElementById('emptyState').style.display = '';
                document.getElementById('gridWrapper').style.display = 'none';
                document.getElementById('paginationBar').style.display = 'none';
                return;
            }

            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('gridWrapper').style.display = '';
            document.getElementById('paginationBar').style.display = '';

            let html = '';
            page.forEach((r, i) => {
                html += renderRow(r, offset, i);
            });
            tbody.innerHTML = html;

            updateBatchBar();
            highlightRow();
        }

        // ── Update summary cards ──
        function updateSummary() {
            const total = currentFiltered.length;
            const pending = currentFiltered.filter(r => r.status === 'Pending').length;
            const approved = currentFiltered.filter(r => r.status === 'Approved').length;
            const rejected = currentFiltered.filter(r => r.status === 'Rejected').length;
            const reviewed = currentFiltered.filter(r => ['Approved', 'Rejected', 'Completed'].includes(r.status)).length;

            document.getElementById('summaryTotal').textContent = total;
            document.getElementById('summaryPending').textContent = pending;
            document.getElementById('summaryApproved').textContent = approved;
            document.getElementById('summaryRejected').textContent = rejected;
            document.getElementById('summaryReviewed').textContent = reviewed;
        }

        // ── Update pagination ──
        function updatePagination() {
            const total = currentFiltered.length;
            const totalPages = Math.ceil(total / rpp);
            const info = document.getElementById('paginationInfo');
            const controls = document.getElementById('paginationControls');

            if (total === 0) {
                info.textContent = 'No results';
                controls.innerHTML = '';
                return;
            }

            const start = (currentPage - 1) * rpp + 1;
            const end = Math.min(currentPage * rpp, total);
            info.textContent = 'Showing ' + start + '-' + end + ' of ' + total;

            let html = '';
            if (currentPage > 1) {
                html += '<button class="page-btn" onclick="goToPage(' + (currentPage - 1) + ')">‹</button>';
            }
            for (let p = 1; p <= totalPages; p++) {
                html += '<button class="page-btn' + (p === currentPage ? ' active' : '') + '" onclick="goToPage(' + p + ')">' + p + '</button>';
            }
            if (currentPage < totalPages) {
                html += '<button class="page-btn" onclick="goToPage(' + (currentPage + 1) + ')">›</button>';
            }
            controls.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            renderBody();
            updatePagination();
        }

        // ── Update result count ──
        function updateResultCount() {
            document.getElementById('resultCount').textContent = currentFiltered.length + ' result' + (currentFiltered.length !== 1 ? 's' : '');
        }

        // ── Active filter chips ──
        function renderFilterChips() {
            const container = document.getElementById('filterChips');
            const chips = [];
            const status = document.getElementById('statusFilter').value;
            const urgency = urgencyFilter;
            const search = document.getElementById('searchInput').value.trim();
            const week = document.getElementById('weekFilter').value;

            if (status !== 'all') {
                chips.push('<span class="filter-chip">Status: ' + status + '<button class="filter-chip-remove" onclick="document.getElementById(\'statusFilter\').value=\'all\';renderTable()" title="Remove">&times;</button></span>');
            }
            if (urgency !== 'all') {
                chips.push('<span class="filter-chip">Urgency: ' + (urgency === 'urgent' ? 'Urgent' : 'Normal') + '<button class="filter-chip-remove" onclick="setUrgencyFilter(\'all\');renderTable()" title="Remove">&times;</button></span>');
            }
            if (week !== 'all') {
                const weekLabel = document.getElementById('weekFilter').selectedOptions[0]?.textContent || week;
                chips.push('<span class="filter-chip">Week: ' + weekLabel + '<button class="filter-chip-remove" onclick="document.getElementById(\'weekFilter\').value=\'all\';onWeekFilterChange()" title="Remove">&times;</button></span>');
            }
            if (search) {
                chips.push('<span class="filter-chip">Search: "' + search + '"<button class="filter-chip-remove" onclick="document.getElementById(\'searchInput\').value=\'\';renderTable()" title="Remove">&times;</button></span>');
            }
            container.innerHTML = '<span class="filter-chips-label">Filters:</span>' + chips.join('');
        }

        // ── Main render ──
        function renderTable() {
            currentFiltered = filterData();
            renderHeader();
            renderBody();
            updateSummary();
            updatePagination();
            updateResultCount();
            renderCards();
            renderFilterChips();
        }

        // ── Render cards (mobile responsive view) ──
        function renderCards() {
            const container = document.getElementById('requestsCards');
            if (!container) return;
            container.innerHTML = '';
            currentFiltered.forEach(function(r, i) {
                const card = document.createElement('div');
                card.className = 'request-card';
                card.setAttribute('data-id', r.id);
                card.innerHTML = HtmlBuilder.requestCard(r, {
                    lookupLecturer: lookupLecturer,
                    urgencyLevel: urgencyLevel,
                    urgencyClass: urgencyClass,
                    urgencyLabel: urgencyLabel,
                    requestAgeHtml: requestAgeHtml
                });
                card.addEventListener('click', function() { openModalById(r.id); });
                container.appendChild(card);
            });
        }

        // ── Bulk selection (§7.1) ──
        function toggleSelectAll() {
            const visible = currentFiltered.filter(r => r.status === 'Pending');
            if (bulk.size === visible.length) { bulk.clear(); }
            else { bulk.setAll(visible.map(r => r.id)); }
            renderTable();
        }

        function toggleRowSelect(id) {
            bulk.toggle(id);
            renderTable();
        }

        function updateBatchBar() {
            bulk.updateBar();
        }

        function clearSelection() {
            bulk.clear();
            renderTable();
        }

        function bulkApprove() {
            const ids = [...bulk.ids];
            openApproveNotesModal(ids);
        }

        function bulkReject() {
            currentRejectId = null;
            document.getElementById('rejectReasonInput').value = '';
            document.getElementById('confirmRejectBtn').disabled = true;
            document.getElementById('rejectReasonModal').classList.add('show');
            document.getElementById('rejectReasonInput').focus();
        }

        // ── Reject presets (§7.3) ──
        function applyRejectPreset(text) {
            const input = document.getElementById('rejectReasonInput');
            input.value = text;
            updateRejectConfirmState();
            input.focus();
        }

        // ── Urgency filter (§7.4) ──
        function setUrgencyFilter(level) {
            urgencyFilter = level;
            document.querySelectorAll('.urgency-filter-chip').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.urgency === level);
            });
            currentPage = 1;
            renderTable();
            saveFilters();
        }

        // ── Week filter change handler ──
        function onWeekFilterChange(value) {
            if (typeof pageState !== 'undefined' && pageState) pageState.currentPage = 1;
            currentPage = 1;
            saveFilters();
            renderTable();
            updateWeekArrowState();
        }

        // ── Modal ──
        let currentModalId = null;

        function openModalById(id) {
            const r = MockData.approvalRequests.find(x => x.id === id);
            if (!r) return;
            currentModalId = r.id;
            viewedIds.add(r.id);

            const statusDescMap = {
                'Pending': 'Awaiting your approval',
                'Approved': 'Replacement scheduled — ready to proceed',
                'Rejected': 'Declined — lecturer needs an alternative',
                'Completed': 'Replacement conducted',
                'Cancelled': 'Request withdrawn'
            };
            const statusDot = r.status === 'Pending' ? 'dot-warning' : r.status === 'Approved' || r.status === 'Completed' ? 'dot-success' : r.status === 'Rejected' ? 'dot-error' : 'dot-primary';

            // Global timeline (always visible above the tabs); dot colour follows each step's status
            const timeline = [
                { label: 'Submitted', time: formatDateTime(r.requestedAt), state: 'completed' },
                { label: 'Viewed', time: r.viewedAt ? formatDateTime(r.viewedAt) : '—', state: r.viewedAt ? 'completed' : 'pending', dot: r.viewedAt ? 'dot-success' : '' },
                { label: 'Reviewed', time: r.reviewedAt ? formatDateTime(r.reviewedAt) : (r.status === 'Pending' ? 'Pending' : '—'), state: r.reviewedAt ? 'completed' : (r.status === 'Pending' ? 'active' : 'pending'), dot: statusDot }
            ];

            // Tab 1: Request Information
            var modalLec = lookupLecturer(r.lecturer);
            var reqRows = '';
            if (modalLec) {
                reqRows += DetailModal.row('Lecturer', modalLec.name, { strong: true });
                reqRows += DetailModal.row('Staff ID', modalLec.staffId ? '<span class="detail-value--muted">' + modalLec.staffId + '</span>' : null);
                reqRows += DetailModal.row('Email', '<span class="lecturer-cell-email" onclick="copyEmail(\'' + modalLec.email + '\', event)" data-tip="Click to copy email">' + modalLec.email + ' <span class="copy-icon">📋</span></span>');
            } else {
                reqRows += DetailModal.row('Lecturer', r.lecturer, { strong: true });
            }
            reqRows += DetailModal.row('Subject Code', r.courseCode, { strong: true });
            reqRows += DetailModal.row('Subject Name', r.courseName);
            reqRows += DetailModal.row('Requested', formatDateTime(r.requestedAt));
            reqRows += DetailModal.row('Status', '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>');
            reqRows += DetailModal.row('Status Description', statusDescMap[r.status] || '');
            if (r.rejectionReason) {
                reqRows += DetailModal.row('Rejection Reason', r.rejectionReason, { strong: true });
            }
            const reqSection = DetailModal.section('Request Information', reqRows);

            // Tab 2: Original Class Detail — one value per row, no grouping
            const origSection = DetailModal.section('Original Class',
                DetailModal.row('Date', r.classDate) +
                DetailModal.row('Day', r.classDay) +
                DetailModal.row('Start Time', DateHelper.to12h(r.timeStart), { strong: true }) +
                DetailModal.row('End Time', DateHelper.to12h(r.timeEnd)) +
                DetailModal.row('Duration', r.duration + 'h') +
                DetailModal.row('Venue', r.venue) +
                DetailModal.row('Total Students', String(r.totalStudents)) +
                DetailModal.row('Cohort(s)', r.cohorts.join(', '))
            );

            // Tab 3: Requested Replacement Class — one value per row
            const repSection = DetailModal.section('Replacement Class',
                DetailModal.row('Date', r.replacementDate) +
                DetailModal.row('Time', DateHelper.format12hRange(r.replacementTime), { strong: true }) +
                DetailModal.row('Venue', r.replacementVenue) +
                DetailModal.row('Slot Validity', slotValidityHtml(r))
            );

            const tabs = [{ key: 'info', label: 'Request Info', html: reqSection }];
            tabs.push({ key: 'original', label: 'Original Class', html: origSection });
            tabs.push({ key: 'replacement', label: 'Replacement Class', html: repSection });
            if (r.reviewedBy) {
                const reviewSection = DetailModal.section('Review',
                    DetailModal.row('Reviewed By', r.reviewedBy) +
                    DetailModal.row('Reviewed At', formatDateTime(r.reviewedAt)) +
                    (r.remarks ? DetailModal.row('Remarks', r.remarks) : '')
                );
                tabs.push({ key: 'review', label: 'Review', html: reviewSection });
            }

            DetailModal.render({
                modalId: 'modalOverlay',
                title: 'Request Details',
                subtitle: '#' + r.id + ' · ' + r.courseCode + ' — ' + r.courseName,
                timeline: timeline,
                tabs: tabs
            });

            // Toggle footer buttons — bottom-left Close is always visible
            document.getElementById('rejectRequestBtn').style.display = r.status === 'Pending' ? '' : 'none';
            document.getElementById('approveRequestBtn').style.display = r.status === 'Pending' ? '' : 'none';
            document.getElementById('closeModalBtn').style.display = '';

            renderTable();
        }

        function openModal(index) {
            const r = currentFiltered[index];
            if (!r) return;
            openModalById(r.id);
        }

        function closeModal() {
            DetailModal.close();
            currentModalId = null;
        }

        // ── Approve (§7.6 — approval notes modal) ──
        function approveRequest(id) {
            closeModal();               // auto-close Request Details when acting from it
            openApproveNotesModal([id]);
        }

        function openApproveNotesModal(ids) {
            currentApproveIds = ids;
            const summary = ids.map(id => {
                const r = MockData.approvalRequests.find(x => x.id === id);
                return approveSummary(r);
            }).join('\n\n');
            document.getElementById('approveNotesSummary').textContent = summary;
            document.getElementById('approveNotesInput').value = '';
            document.getElementById('approveNotesModal').classList.add('show');
            document.getElementById('approveNotesInput').focus();
        }

        function confirmApproveWithNotes() {
            const notes = document.getElementById('approveNotesInput').value.trim();
            const ids = currentApproveIds;
            const prevStatuses = {};
            ids.forEach(id => { const r = MockData.approvalRequests.find(x => x.id === id); prevStatuses[id] = r.status; r.status = 'Approved'; });
            const label = ids.length === 1 ? 'Request #' + ids[0] : ids.length + ' requests';
            const notesLine = notes ? '\nNotes: ' + notes : '';
            closeApproveNotesModal();
            bulk.clear();
            renderTable();
            updateNavBadge();
            toast.show(label + ' approved.' + notesLine, function() {
                ids.forEach(id => { const r = MockData.approvalRequests.find(x => x.id === id); r.status = prevStatuses[id]; });
                renderTable();
                updateNavBadge();
            });
        }

        function closeApproveNotesModal() {
            document.getElementById('approveNotesModal').classList.remove('show');
            currentApproveIds = [];
        }

        // ── Reject modal ──
        function openRejectModal(id) {
            closeModal();               // auto-close Request Details when acting from it
            currentRejectId = id;
            document.getElementById('rejectReasonInput').value = '';
            document.getElementById('confirmRejectBtn').disabled = true;
            document.getElementById('rejectReasonModal').classList.add('show');
            document.getElementById('rejectReasonInput').focus();
        }

        function updateRejectConfirmState() {
            const hasReason = document.getElementById('rejectReasonInput').value.trim().length > 0;
            document.getElementById('confirmRejectBtn').disabled = !hasReason;
        }

        function rejectRequest() {
            const reason = document.getElementById('rejectReasonInput').value.trim();
            if (!reason) {
                toast.show('Please provide a rejection reason.', null, 3000);
                return;
            }
            const isBulk = currentRejectId === null;
            const ids = isBulk ? [...bulk.ids] : [currentRejectId];
            const prevStatuses = {};
            ids.forEach(id => { const r = MockData.approvalRequests.find(x => x.id === id); prevStatuses[id] = r.status; r.status = 'Rejected'; });
            const label = isBulk ? ids.length + ' request(s)' : 'Request #' + currentRejectId;
            closeRejectModal();
            bulk.clear();
            renderTable();
            updateNavBadge();
            toast.show(label + ' rejected.', function() {
                ids.forEach(id => { const r = MockData.approvalRequests.find(x => x.id === id); r.status = prevStatuses[id]; });
                renderTable();
                updateNavBadge();
            });
        }

        function closeRejectModal() {
            document.getElementById('rejectReasonModal').classList.remove('show');
            currentRejectId = null;
        }

        // ── Review next auto-advance (§7j) ──
        function reviewNextAfterAction(actedOnId) {
            const actedIndex = actedOnId ? currentFiltered.findIndex(r => r.id === actedOnId) : -1;
            let nextIndex = -1;
            for (let i = actedIndex + 1; i < currentFiltered.length; i++) {
                if (currentFiltered[i].status === 'Pending') { nextIndex = i; break; }
            }
            if (nextIndex >= 0) {
                focusedRowIndex = nextIndex;
                openModalById(currentFiltered[nextIndex].id);
            } else {
                focusedRowIndex = -1;
                closeModal();
            }
        }

        // ── Keyboard shortcuts (§7i) ──
        function highlightRow() {
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach((tr, i) => {
                tr.classList.toggle('row-focused', i === focusedRowIndex);
            });
        }

        // ── Populate week filter ──
        function populateWeekFilter() {
            populateWeekSelect('weekFilter', {
                includeAll: true,
                labelFn: function(w) { return w.label; }
            });
        }

        // ── Reset filters ──
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = 'Pending';
            document.getElementById('weekFilter').value = 'all';
            urgencyFilter = 'all';
            rpp = 10;
            document.getElementById('rowsPerPage').value = '10';
            hideCompleted = true;
            document.getElementById('hideCompletedToggle').checked = true;
            bulk.clear();
            viewedIds.clear();
            currentPage = 1;
            sortState = { field: 'requestedAt', dir: 'asc' };
            document.querySelectorAll('.urgency-filter-chip').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.urgency === 'all');
            });
            localStorage.removeItem('request-approval-filters');
            renderTable();
        }

        // ── Deep link support ──
        function checkDeepLink() {
            const params = new URLSearchParams(window.location.search);
            const id = parseInt(params.get('id'));
            if (!id) return;
            const r = MockData.approvalRequests.find(function(x) { return x.id === id; });
            if (!r) {
                toast.show('Request #' + id + ' not found', 'error');
                return;
            }
            openModalById(id);
        }

        // ── DOMContentLoaded ──
        document.addEventListener('DOMContentLoaded', function() {
            populateWeekFilter();
            updateWeekArrowState();
            document.getElementById('statusFilter').value = 'Pending';

            // Skeleton loading
            SkeletonLoader.showSummary();
            SkeletonLoader.with(function() {
                isInitialLoad = false;
                restoreFilters();
                initLocalRpp();
                renderTable();
                updateNavBadge();
                checkDeepLink();
                SkeletonLoader.hideSummary();
            }, document.getElementById('tableBody'), 10, 400);

            initRpp({
                selectId: 'rowsPerPage',
                storageKey: 'request-approval-rpp',
                defaultVal: 10,
                onChange: function(size) {
                    rpp = size;
                    rebuildTable({ render: renderTable });
                    saveFilters();
                }
            });

            document.getElementById('searchInput').addEventListener('input', function() {
                rebuildTable({ render: renderTable });
                saveFilters();
            });
            document.getElementById('statusFilter').addEventListener('change', function() {
                rebuildTable({ render: renderTable });
                saveFilters();
            });
            document.getElementById('clearFilters').addEventListener('click', resetFilters);

            document.getElementById('modalOverlay').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
            document.getElementById('rejectReasonModal').addEventListener('click', function(e) {
                if (e.target === this) closeRejectModal();
            });
            document.getElementById('approveNotesModal').addEventListener('click', function(e) {
                if (e.target === this) closeApproveNotesModal();
            });

            document.getElementById('rejectReasonInput').addEventListener('input', updateRejectConfirmState);

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Modal close — 3-layer Escape
                if (e.key === 'Escape') {
                    if (document.getElementById('approveNotesModal').classList.contains('show')) {
                        closeApproveNotesModal();
                    } else if (document.getElementById('rejectReasonModal').classList.contains('show')) {
                        closeRejectModal();
                    } else if (document.getElementById('modalOverlay').classList.contains('show')) {
                        closeModal();
                    } else {
                        focusedRowIndex = -1;
                        highlightRow();
                    }
                    return;
                }

                // Pause keyboard nav when modal is open
                if (document.querySelector('.modal-overlay.show')) return;
                if (!currentFiltered.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    focusedRowIndex = Math.min(focusedRowIndex + 1, currentFiltered.length - 1);
                    highlightRow();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    focusedRowIndex = Math.max(focusedRowIndex - 1, 0);
                    highlightRow();
                } else if (e.key === 'Enter' && focusedRowIndex >= 0) {
                    openModalById(currentFiltered[focusedRowIndex].id);
                } else if ((e.key === 'a' || e.key === 'A') && focusedRowIndex >= 0) {
                    const r = currentFiltered[focusedRowIndex];
                    if (r.status === 'Pending') approveRequest(r.id);
                } else if ((e.key === 'r' || e.key === 'R') && focusedRowIndex >= 0) {
                    const r = currentFiltered[focusedRowIndex];
                    if (r.status === 'Pending') openRejectModal(r.id);
                }
            });
        });
@endsection
