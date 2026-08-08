@extends('layouts.ui-template', ['activeNav' => 'request-approval'])

@section('title', 'Request Approval')

@section('page-styles')

        /* ───── Toggle ───── */
        .toggle-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .toggle-wrapper input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }
        .toggle-track {
            position: relative;
            width: 36px;
            height: 20px;
            border-radius: 10px;
            background: var(--color-outline);
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .toggle-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-wrapper input:checked + .toggle-track {
            background: var(--color-primary, #4f46e5);
        }
        .toggle-wrapper input:checked + .toggle-track .toggle-thumb {
            transform: translateX(16px);
        }
        .toggle-label {
            font-size: 13px;
            color: var(--color-on-surface-variant);
            font-weight: 500;
        }

        /* ───── Clear Button ───── */
        .btn-clear {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--color-outline);
            background: transparent;
            color: var(--color-on-surface-variant);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            margin-left: 8px;
        }
        .btn-clear:hover {
            background: var(--color-surface-variant);
            color: var(--color-on-surface);
        }

        /* ───── Column Widths ───── */
        .col-no { width: 40px; text-align: center; }
        .col-original, .col-replacement { white-space: normal; }

        .col-replacement .cell-class-block .class-time {
            font-weight: 600;
        }
        .col-replacement .cell-class-block .class-time.status-pending {
            color: #f59e0b;
        }
        .col-replacement .cell-class-block .class-time.status-approved {
            color: #10b981;
        }
        .col-replacement .cell-class-block .class-time.status-rejected {
            color: #ef4444;
        }
        .col-replacement .cell-class-block .class-time.status-cancelled {
            color: var(--color-on-surface-variant);
            opacity: 0.6;
        }
        .col-replacement .cell-class-block .class-time.status-completed {
            color: #3b82f6;
        }
        .col-replacement .cell-class-block .class-venue {
            font-size: 11px;
            color: var(--color-on-surface-variant);
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .col-replacement .cell-class-block .class-venue::before {
            content: '📍';
            font-size: 10px;
        }

        /* ───── Status Badges (page-specific overrides) ───── */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
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
        .urgency-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .urgency-urgent { background: var(--color-error-container); color: var(--color-on-error-container); }
        .urgency-normal { background: var(--color-secondary-container); color: var(--color-on-secondary-container); }

        /* ───── Action Buttons ───── */
        .btn-approve { padding: 4px 10px; border-radius: 4px; border: 1px solid var(--color-secondary); background: transparent; color: var(--color-secondary); font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s, color 0.15s; }
        .btn-approve:hover { background: var(--color-secondary); color: var(--color-on-secondary); }
        .btn-reject { padding: 4px 10px; border-radius: 4px; border: 1px solid var(--color-error); background: transparent; color: var(--color-error); font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s, color 0.15s; }
        .btn-reject:hover { background: var(--color-error); color: var(--color-on-error); }
        .btn-view { padding: 4px 10px; border-radius: 4px; border: 1px solid var(--color-outline); background: transparent; color: var(--color-on-surface-variant); font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s; }
        .btn-view:hover { background: var(--color-surface-variant); }

        /* ───── Summary Card Colors ───── */
        .summary-card.card-total .summary-value { color: var(--color-on-primary-container); }
        .summary-card.card-approved .summary-value { color: var(--color-secondary); }
        .summary-card.card-pending .summary-value { color: var(--color-tertiary); }
        .summary-card.card-rejected .summary-value { color: var(--color-error); }
        .summary-card.card-hours .summary-value { color: var(--color-on-surface); }
        .summary-card.card-hours {
            border: 1px dashed var(--color-outline-strong);
            background: var(--color-surface-variant);
        }
        .summary-card.card-total {
            border: 2px solid var(--color-primary);
            background: var(--color-primary-container);
        }



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

        /* ───── Urgency Filter Chips ───── */
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

        /* ───── Request Age Indicator ───── */
        .request-age { font-size: 11px; color: var(--color-on-surface-variant); margin-top: 2px; }
        .request-age::before { content: '● '; font-size: 8px; }
        .age-fresh::before { color: var(--color-primary); }
        .age-waiting::before { color: var(--color-tertiary); }
        .age-stale::before { color: var(--color-error); }

        /* ───── Reject Preset Chips ───── */
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

        /* ───── Viewed Row Indicator ───── */
        .row-viewed td:first-child { border-left: 3px solid var(--color-primary); }

        /* ───── Keyboard Highlight ───── */
        .row-focused { background: var(--color-primary-container) !important; border-left: 3px solid var(--color-primary); }

        /* ───── Slot Validity Icons ───── */
        .slot-icon { font-size: 11px; margin-left: 4px; font-weight: 600; }
        .slot-valid { color: var(--color-primary); }
        .slot-conflict { color: var(--color-error); }
        .slot-tentative { color: var(--color-tertiary); }

        /* ───── Row Flash Animations ───── */
        .row-flash-approved { animation: flashGreen 0.6s ease; }
        .row-flash-rejected { animation: flashRed 0.6s ease; }
        @keyframes flashGreen { 0% { background: var(--color-secondary-container); } 100% { background: transparent; } }
        @keyframes flashRed { 0% { background: var(--color-error-container); } 100% { background: transparent; } }

        /* ───── Row Urgency Indicators ───── */
        .row-urgent { border-left: 3px solid var(--color-error); }
        .row-soon { border-left: 3px solid var(--color-tertiary); }

        /* ───── Active Filter Chips ───── */
        .filter-chips {
            display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
            padding: 6px 12px; margin-bottom: 8px;
            background: var(--color-surface-variant); border: 1px solid var(--color-outline-variant);
            border-radius: var(--radius-sm); font-size: 12px;
        }
        .filter-chips:empty { display: none; }
        .filter-chips-label { font-weight: 600; color: var(--color-on-surface-variant); margin-right: 2px; }
        .filter-chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 12px;
            background: var(--color-primary-container); color: var(--color-on-primary-container);
            font-size: 12px; font-weight: 500;
        }
        .filter-chip-remove {
            display: inline-flex; align-items: center; justify-content: center;
            width: 16px; height: 16px; border-radius: 50%; border: none;
            background: transparent; color: var(--color-on-primary-container);
            font-size: 14px; font-weight: 700; cursor: pointer; line-height: 1;
            transition: background 0.15s;
        }
        .filter-chip-remove:hover { background: var(--color-primary); color: #fff; }

        /* ───── Lecturer Cell ───── */
        .lecturer-cell-name { font-weight: 600; font-size: 13px; color: var(--color-on-surface); }
        .lecturer-cell-id { font-size: 11px; color: var(--color-on-surface-variant); opacity: 0.7; font-weight: 400; }
        .lecturer-cell-email {
            font-size: 12px; color: var(--color-primary); cursor: pointer;
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 10px;
            background: var(--color-primary-container); color: var(--color-on-primary-container);
            transition: opacity 0.15s;
        }
        .lecturer-cell-email:hover { opacity: 0.8; }
        .lecturer-cell-email .copy-icon { font-size: 10px; opacity: 0.6; }

        /* ───── Mini Request Timeline ───── */
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

        /* ───── Responsive Card View ───── */
        .request-card {
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background 0.15s, box-shadow 0.15s;
        }
        .request-card:hover {
            background: var(--color-surface-variant);
            box-shadow: var(--shadow-sm);
        }
        .request-card:active {
            transform: scale(0.99);
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .card-code {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-on-surface);
        }
        .card-body {
            font-size: 12px;
            color: var(--color-on-surface-variant);
            line-height: 1.6;
        }
        .card-body strong {
            color: var(--color-on-surface);
            font-weight: 600;
        }
        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid var(--color-outline);
            font-size: 11px;
            color: var(--color-on-surface-variant);
        }
        .card-age { font-weight: 600; }

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
        @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeekFilter()', 'nextOnclick' => 'nextWeekFilter()', 'selectId' => 'weekFilter', 'selectOnclick' => 'weekFilterChanged(this.value)', 'showTodayBtn' => false])
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
        <table class="timetable" id="timetable">
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
    <button class="btn-approve" onclick="bulkApprove()">Approve Selected</button>
    <button class="btn-reject" onclick="bulkReject()">Reject Selected</button>
</div>

@include('partials.ui-summary-bar', ['cards' => [
    ['class' => 'card-total', 'valueId' => 'summaryTotal', 'label' => 'Total Requests'],
    ['class' => 'card-pending', 'valueId' => 'summaryPending', 'label' => 'Pending Requests'],
    ['class' => 'card-approved', 'valueId' => 'summaryApproved', 'label' => 'Approved'],
    ['class' => 'card-rejected', 'valueId' => 'summaryRejected', 'label' => 'Rejected'],
    ['class' => 'card-total', 'valueId' => 'summaryReviewed', 'label' => 'Total Reviewed']
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
            <h2>Request Details</h2>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <div class="modal-footer-left">
                <button class="btn-danger" id="rejectRequestBtn" onclick="openRejectModal(currentModalId)" style="display:none">✕ Reject</button>
            </div>
            <div class="modal-footer-right" style="gap: 8px">
                <button class="btn-approve" id="approveRequestBtn" onclick="approveRequest(currentModalId)" style="display:none">✓ Approve</button>
                <button class="btn-outline" id="closeModalBtn" onclick="closeModal()">Close</button>
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
            <p style="margin-bottom:8px;font-size:13px;color:var(--color-on-surface-variant)">Please provide a reason for rejection (required):</p>
            <textarea id="rejectReasonInput" placeholder="Enter rejection reason..." rows="4" style="width:100%;padding:8px;border:1px solid var(--color-outline);border-radius:4px;font-size:13px;resize:vertical;font-family:inherit"></textarea>
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
            <p style="margin-bottom:8px;font-size:13px;color:var(--color-on-surface-variant)">Notes (optional):</p>
            <textarea id="approveNotesInput" placeholder="Optional note for the audit trail..." rows="2" style="width:100%;padding:8px;border:1px solid var(--color-outline);border-radius:4px;font-size:13px;resize:vertical;font-family:inherit"></textarea>
        </div>
        <div class="modal-footer">
            <div class="modal-footer-left"></div>
            <div class="modal-footer-right">
                <button class="btn-outline" onclick="closeApproveNotesModal()">Cancel</button>
                <button class="btn-approve" id="confirmApproveBtn" onclick="confirmApproveWithNotes()">✓ Approve</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
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
                showToast('Email copied: ' + email, null, 2000);
            });
        }

        // ── Slot validity helper ──
        function slotValidityHtml(r) {
            if (r.slotValidity === 'conflict') {
                return '<span class="slot-icon slot-conflict">⚠</span>' + (r.conflictReason ? ' <span style="color:var(--color-error);font-size:11px">' + r.conflictReason + '</span>' : '');
            }
            return '<span class="slot-icon slot-valid">✓</span>';
        }

        // ── Request age helper (§7.5) ──
        function requestAgeHtml(requestedAt) {
            const REFERENCE_DATE = new Date('2026-08-29T00:00:00');
            const diff = Math.floor((REFERENCE_DATE - new Date(requestedAt).getTime()) / 86400000);
            if (diff < 0) return '<div class="request-age request-age--unknown">—</div>';
            const cls = diff <= 1 ? 'age-fresh' : diff <= 3 ? 'age-waiting' : 'age-stale';
            return '<div class="request-age ' + cls + '">' + diff + ' day' + (diff !== 1 ? 's' : '') + ' ago</div>';
        }

        // ── Short date helper ──
        function formatShortDate(iso) {
            if (!iso) return '—';
            const d = new Date(iso + 'T00:00:00');
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }

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
                '\nOriginal: ' + dayAbbr(r.classDay) + ' ' + formatShortDate(r.classDate) + ' ' + r.timeStart + '–' + r.timeEnd +
                '\nReplacement: ' + formatShortDate(r.replacementDate) + ' ' + r.replacementTime +
                '\nVenue: ' + (r.replacementVenue || r.venue);
        }

        // ── Sort state ──
        let sortState = { field: 'requestedAt', dir: 'asc' };
        let currentPage = 1;
        let rpp = 10;
        const RPP_OPTIONS = [10, 20, 50];

        // ── Feature state ──
        let selectedIds = new Set();
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
            { label: '#', sortable: false },
            { label: 'Requested Timestamp', sortable: true, field: 'requestedAt' },
            { label: 'Lecturer', sortable: false },
            { label: 'Course Code & Name', sortable: false },
            { label: 'Original Class', sortable: true, field: 'classDate' },
            { label: 'Proposed Replacement', sortable: true, field: 'replacementDate' },
            { label: 'Students', sortable: false },
            { label: 'Urgency', sortable: true, field: 'urgencyDays' },
            { label: 'Status', sortable: true, field: 'status' },
            { label: 'Actions', sortable: false }
        ];

        // ── Filter state ──
        let currentFiltered = [];

        // ── Render table header ──
        function renderHeader() {
            const thead = document.getElementById('tableHead');
            let html = '<tr><th class="col-checkbox"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>';
            columns.forEach((col, i) => {
                if (col.sortable) {
                    const arrow = sortState.field === col.field ? (sortState.dir === 'asc' ? ' ▲' : ' ▼') : '';
                    html += '<th class="sortable" onclick="toggleSort(\'' + col.field + '\')">' + col.label + arrow + '</th>';
                } else {
                    html += '<th>' + col.label + '</th>';
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
            const isSelected = selectedIds.has(r.id);
            const isViewed = viewedIds.has(r.id);
            let rowClass = '';
            if (isViewed) rowClass += ' row-viewed';
            if (isSelected) rowClass += ' row-selected';
            if (level === 'urgent') rowClass += ' row-urgent';
            else if (urgencyDays(r.classDate) <= 7) rowClass += ' row-soon';

            let html = '<tr data-id="' + r.id + '"' + (rowClass ? ' class="' + rowClass.trim() + '"' : '') + ' onclick="openModalById(' + r.id + ')" style="cursor:pointer">';

            // Checkbox column
            if (r.status === 'Pending') {
                html += '<td class="col-checkbox"><input type="checkbox" ' + (isSelected ? 'checked' : '') + ' onchange="toggleRowSelect(' + r.id + ')"></td>';
            } else {
                html += '<td class="col-checkbox"></td>';
            }

            html += '<td>' + (offset + i + 1) + '</td>';
            html += '<td>' + formatDateTime(r.requestedAt) + requestAgeHtml(r.requestedAt) + '</td>';
            var lec = lookupLecturer(r.lecturer);
            if (lec) {
                html += '<td><div class="lecturer-cell-name">' + lec.name + ' <span class="lecturer-cell-id">(' + lec.staffId + ')</span></div>';
                html += '<div class="lecturer-cell-email" onclick="copyEmail(\'' + lec.email + '\', event)" title="Click to copy email">' + lec.email + ' <span class="copy-icon">📋</span></div></td>';
            } else {
                html += '<td>' + r.lecturer + '</td>';
            }
            html += '<td><div class="cell-code">' + r.courseCode + '</div><div class="cell-name">' + r.courseName + '</div></td>';
            html += '<td>' + formatClassBlock(r) + '</td>';

            // Proposed Replacement with slot validity icon
            const slotIcon = r.slotValidity === 'conflict'
                ? '<span class="slot-icon slot-conflict" title="Conflict">⚠</span>'
                : '<span class="slot-icon slot-valid" title="Slot available">✓</span>';
            html += '<td>' + formatReplacementBlock(r) + ' ' + slotIcon + '</td>';

            html += '<td>' + r.totalStudents + '</td>';
            html += '<td><span class="urgency-badge ' + urgencyClass(level) + '">' + urgencyLabel(level) + '</span></td>';
            html += '<td><span class="badge ' + statusClass(r.status) + '" onclick="openModalById(' + r.id + ')" style="cursor:pointer">' + r.status + '</span></td>';

            const actions = r.status === 'Pending'
                ? '<div style="display:flex;gap:6px;align-items:center"><button class="btn-approve" onclick="approveRequest(' + r.id + ')">✓ Approve</button><button class="btn-reject" onclick="openRejectModal(' + r.id + ')">✕ Reject</button></div>'
                : '<button class="btn-view" onclick="openModalById(' + r.id + ')">👁 View</button>';
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
            const total = MockData.approvalRequests.length;
            const source = hideCompleted
                ? MockData.approvalRequests.filter(r => r.status !== 'Completed')
                : MockData.approvalRequests;
            const pending = source.filter(r => r.status === 'Pending').length;
            const approved = source.filter(r => r.status === 'Approved').length;
            const rejected = source.filter(r => r.status === 'Rejected').length;
            const reviewed = source.filter(r => ['Approved', 'Rejected', 'Completed'].includes(r.status)).length;

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
                chips.push('<span class="filter-chip">Week: ' + weekLabel + '<button class="filter-chip-remove" onclick="document.getElementById(\'weekFilter\').value=\'all\';weekFilterChanged()" title="Remove">&times;</button></span>');
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
                card.innerHTML =
                    '<div class="card-header">' +
                        '<span class="card-code">#' + r.id + '</span>' +
                        '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>' +
                    '</div>' +
                    '<div class="card-body">' +
                        '<div><strong>Course:</strong> ' + r.courseCode + ' - ' + r.courseName + '</div>' +
                        '<div><strong>Date:</strong> ' + formatShortDate(r.classDate) + ' ' + to12h(r.timeStart) + '</div>' +
                        '<div><strong>Lecturer:</strong> ' + (function() { var l = lookupLecturer(r.lecturer); return l ? l.name + ' (' + l.staffId + ')' : r.lecturer; })() + '</div>' +
                        '<div><strong>Urgency:</strong> <span class="urgency-badge ' + urgencyClass(urgencyLevel(r.classDate)) + '">' + urgencyLabel(urgencyLevel(r.classDate)) + '</span></div>' +
                        '<div>' + requestAgeHtml(r.requestedAt) + '</div>' +
                    '</div>';
                card.addEventListener('click', function() { openModalById(r.id); });
                container.appendChild(card);
            });
        }

        // ── Bulk selection (§7.1) ──
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
            const bar = document.getElementById('bulkActionBar');
            const count = document.getElementById('bulkCount');
            if (selectedIds.size === 0) { bar.classList.remove('visible'); return; }
            bar.classList.add('visible');
            count.textContent = selectedIds.size + ' selected';
        }

        function bulkApprove() {
            const ids = [...selectedIds];
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
        function weekFilterChanged() {
            currentPage = 1;
            renderTable();
            saveFilters();
        }

        // ── Mini timeline (§7p) ──
        function buildTimeline(r) {
            const steps = [
                { label: 'Submitted', time: r.requestedAt, done: true },
                { label: 'Viewed', time: r.viewedAt, done: !!r.viewedAt },
                { label: 'Reviewed', time: r.reviewedAt, done: !!r.reviewedAt }
            ];
            let html = '<div class="request-timeline">';
            let foundFirstIncomplete = false;
            steps.forEach((s, i) => {
                const cls = s.done ? 'completed' : (!foundFirstIncomplete && (foundFirstIncomplete = true) ? 'active' : 'pending');
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

        // ── Modal ──
        let currentModalId = null;

        function openModalById(id) {
            const r = MockData.approvalRequests.find(x => x.id === id);
            if (!r) return;
            currentModalId = r.id;
            viewedIds.add(r.id);

            const body = document.getElementById('modalBody');
            let html = '';

            // Timeline
            html += buildTimeline(r);

            // Section 1: Request Information
            html += '<div class="modal-section"><div class="modal-section-title">Request Information</div>';
            var modalLec = lookupLecturer(r.lecturer);
            if (modalLec) {
                html += '<p><strong>Lecturer:</strong> ' + modalLec.name + '</p>';
                html += '<p><strong>Staff ID:</strong> ' + modalLec.staffId + '</p>';
                html += '<p><strong>Email:</strong> <span class="lecturer-cell-email" onclick="copyEmail(\'' + modalLec.email + '\', event)" title="Click to copy">' + modalLec.email + ' 📋</span></p>';
            } else {
                html += '<p><strong>Lecturer:</strong> ' + r.lecturer + '</p>';
            }
            html += '<p><strong>Course:</strong> ' + r.courseCode + ' — ' + r.courseName + '</p>';
            html += '<p><strong>Requested:</strong> ' + formatDateTime(r.requestedAt) + '</p>';
            html += '<p><strong>Status:</strong> <span class="badge ' + statusClass(r.status) + '">' + r.status + '</span></p>';
            if (r.rejectionReason) {
                html += '<p style="color:var(--color-error)"><strong>Rejection Reason:</strong> ' + r.rejectionReason + '</p>';
            }
            html += '</div>';

            // Section 2: Original Class Detail
            html += '<div class="modal-section"><div class="modal-section-title">Original Class Detail</div>';
            html += '<p><strong>Date:</strong> ' + r.classDay + ', ' + r.classDate + '</p>';
            html += '<p><strong>Time:</strong> ' + r.timeStart + ' – ' + r.timeEnd + ' (' + r.duration + 'h)</p>';
            html += '<p><strong>Venue:</strong> ' + r.venue + '</p>';
            html += '<p><strong>Students:</strong> ' + r.totalStudents + ' (' + r.cohorts.join(', ') + ')</p>';
            html += '</div>';

            // Section 3: Requested Replacement Class
            html += '<div class="modal-section"><div class="modal-section-title">Requested Replacement Class</div>';
            html += '<p><strong>Date:</strong> ' + r.replacementDate + '</p>';
            html += '<p><strong>Time:</strong> ' + r.replacementTime + '</p>';
            html += '<p><strong>Venue:</strong> ' + r.replacementVenue + '</p>';
            html += '<p><strong>Slot Validity:</strong> ' + slotValidityHtml(r) + '</p>';
            html += '</div>';

            // Section 4: Review (if reviewed)
            if (r.reviewedBy) {
                html += '<div class="modal-section"><div class="modal-section-title">Review</div>';
                html += '<p><strong>Reviewed By:</strong> ' + r.reviewedBy + '</p>';
                html += '<p><strong>Reviewed At:</strong> ' + formatDateTime(r.reviewedAt) + '</p>';
                if (r.remarks) {
                    html += '<p><strong>Remarks:</strong> ' + r.remarks + '</p>';
                }
                html += '</div>';
            }

            body.innerHTML = html;

            // Toggle footer buttons
            document.getElementById('rejectRequestBtn').style.display = r.status === 'Pending' ? '' : 'none';
            document.getElementById('approveRequestBtn').style.display = r.status === 'Pending' ? '' : 'none';
            document.getElementById('closeModalBtn').style.display = r.status === 'Pending' ? 'none' : '';

            document.getElementById('modalOverlay').classList.add('show');
            renderTable();
        }

        function openModal(index) {
            const r = currentFiltered[index];
            if (!r) return;
            openModalById(r.id);
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('show');
            currentModalId = null;
        }

        // ── Approve (§7.6 — approval notes modal) ──
        function approveRequest(id) {
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
            selectedIds.clear();
            renderTable();
            updateNavBadge();
            showToast(label + ' approved.' + notesLine, function() {
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
                showToast('Please provide a rejection reason.', null, 3000);
                return;
            }
            const isBulk = currentRejectId === null;
            const ids = isBulk ? [...selectedIds] : [currentRejectId];
            const prevStatuses = {};
            ids.forEach(id => { const r = MockData.approvalRequests.find(x => x.id === id); prevStatuses[id] = r.status; r.status = 'Rejected'; });
            const label = isBulk ? ids.length + ' request(s)' : 'Request #' + currentRejectId;
            closeRejectModal();
            selectedIds.clear();
            renderTable();
            updateNavBadge();
            showToast(label + ' rejected.', function() {
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
            const select = document.getElementById('weekFilter');
            select.innerHTML = '<option value="all">All Weeks</option>';
            weekRanges.forEach((w, i) => {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = w.label;
                select.appendChild(opt);
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
            selectedIds.clear();
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
                if (typeof showToast === 'function') showToast('Request #' + id + ' not found', 'error');
                return;
            }
            openModalById(id);
        }

        // ── DOMContentLoaded ──
        document.addEventListener('DOMContentLoaded', function() {
            populateWeekFilter();
            document.getElementById('statusFilter').value = 'Pending';

            // Skeleton loading
            showSummarySkeleton();
            withSkeleton(function() {
                isInitialLoad = false;
                restoreFilters();
                initLocalRpp();
                renderTable();
                updateNavBadge();
                checkDeepLink();
                hideSummarySkeleton();
            }, document.getElementById('tableBody'), 10, 400);

            initRpp({
                selectId: 'rowsPerPage',
                storageKey: 'request-approval-rpp',
                defaultVal: 10,
                onChange: function(size) {
                    rpp = size;
                    currentPage = 1;
                    window.scrollTo(0, 0);
                    showSummarySkeleton();
                    withSkeleton(function() { renderTable(); hideSummarySkeleton(); }, document.getElementById('tableBody'), 10, 400);
                    saveFilters();
                }
            });

            document.getElementById('searchInput').addEventListener('input', function() {
                currentPage = 1;
                window.scrollTo(0, 0);
                showSummarySkeleton();
                withSkeleton(function() { renderTable(); hideSummarySkeleton(); }, document.getElementById('tableBody'), 10, 400);
                saveFilters();
            });
            document.getElementById('statusFilter').addEventListener('change', function() {
                currentPage = 1;
                window.scrollTo(0, 0);
                showSummarySkeleton();
                withSkeleton(function() { renderTable(); hideSummarySkeleton(); }, document.getElementById('tableBody'), 10, 400);
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
