@extends('layouts.ui-template', ['activeNav' => 'replacement-history'])

@section('title', 'My Request History — Class Replacement System')

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
        .col-no { width: 50px; }
        .col-requested-at { width: 145px; }
        .col-code { width: 200px; }
        .col-type { width: 80px; }
        .col-original { width: 170px; }
        .col-replacement { width: 170px; }
        .col-original, .col-replacement { white-space: normal; }
        .col-venue { width: 75px; }
        .col-students { width: 80px; }
        .col-cohort { width: 120px; }
        .col-status { width: 130px; }

        /* ───── Multi-line Cell ───── */
        .cell-class-block {
            line-height: 1.55;
            white-space: pre-line;
        }
        .cell-class-block .class-day-date {
            font-weight: 600;
            color: var(--color-on-surface);
        }
        .cell-class-block .class-time {
            font-size: 12px;
            color: var(--color-on-surface-variant);
        }
        .cell-class-block .class-duration {
            color: var(--color-on-surface);
            font-weight: 500;
        }
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

        /* ───── Status Badges ───── */
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
        .status-pending {
            background: var(--color-tertiary-container);
            color: var(--color-on-tertiary-container);
        }
        .status-approved {
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
        }
        .status-rejected {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }
        .status-cancelled {
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
        }
        .status-completed {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
        }

        /* ───── Summary Card Colors ───── */
        .summary-card.card-total .summary-value { color: var(--color-on-primary-container); }
        .summary-card.card-approved .summary-value { color: var(--color-secondary); }
        .summary-card.card-pending .summary-value { color: var(--color-tertiary); }
        .summary-card.card-rejected .summary-value { color: var(--color-error); }
        .summary-card.card-total {
            border: 2px solid var(--color-primary);
            background: var(--color-primary-container);
        }

        /* ───── Modal ───── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(2px);
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.show {
            display: flex;
        }
        .modal {
            background: var(--color-surface);
            border-radius: var(--radius-md);
            max-width: 520px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        }
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            border-bottom: 1px solid var(--color-outline);
        }
        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--color-on-surface);
            margin: 0;
        }
        .modal-close {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: var(--color-on-surface-variant);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: background 0.15s;
        }
        .modal-close:hover {
            background: var(--color-surface-variant);
        }
        .modal-body {
            padding: 20px 24px;
        }
        .modal-field {
            display: flex;
            padding: 6px 0;
            border-bottom: 1px solid var(--color-outline);
            border-bottom-width: 0.5px;
        }
        .modal-field:last-child {
            border-bottom: none;
        }
        .modal-section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--color-on-surface-variant);
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 10px 0 4px;
        }
        .modal-section-title:first-child {
            padding-top: 0;
        }

        .modal-field-label {
            width: 130px;
            flex-shrink: 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-on-surface-variant);
        }
        .modal-field-value {
            flex: 1;
            font-size: 13px;
            color: var(--color-on-surface);
        }
        .modal-footer {
            padding: 12px 24px;
            border-top: 1px solid var(--color-outline);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-footer-left {
            display: flex;
            gap: 8px;
        }
        .modal-footer-right {
            display: flex;
            gap: 8px;
        }
        .btn-danger {
            padding: 8px 20px;
            border-radius: 8px;
            border: 1px solid var(--color-error);
            background: transparent;
            color: var(--color-error);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .btn-danger:hover {
            background: var(--color-error);
            color: #fff;
        }
        .btn-outline {
            padding: 8px 20px;
            border-radius: 8px;
            border: 1px solid var(--color-outline);
            background: transparent;
            color: var(--color-on-surface);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-outline:hover {
            background: var(--color-surface-variant);
        }

        /* ───── Empty State CTA ───── */
        .empty-cta {
            margin-top: 16px;
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            background: var(--color-secondary);
            color: var(--color-on-secondary);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: filter 0.15s;
        }
        .empty-cta:hover {
            filter: brightness(1.08);
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        <div class="page-header">
            <h1 class="page-title">My Request History</h1>
            <p class="page-desc">View and monitor all replacement requests submitted during the current semester.</p>
        </div>

        <!-- ─── Toolbar ─── -->
        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" id="searchInput" placeholder="Search course code or name...">
                </div>
                <select class="filter-select" id="statusFilter">
                    <option value="all">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="Completed">Completed</option>
                </select>
                <select class="filter-select" id="weekFilter">
                    <option value="all">All Weeks</option>
                </select>
                <label class="toggle-wrapper" id="completedToggle">
                    <input type="checkbox" id="hideCompleted" checked>
                    <span class="toggle-track"><span class="toggle-thumb"></span></span>
                    <span class="toggle-label">Exclude Completed</span>
                </label>
                <button class="btn-clear" id="clearFilters" title="Reset all filters">Reset Filters</button>
            </div>
            <div class="toolbar-right">
                <span class="result-count" id="resultCount">Showing 20 of 20 results</span>
            </div>
        </div>

        <div class="sort-hint">Click column headers to sort (Requested At, Course Code, Original Class)</div>

        <!-- ─── Grid Wrapper ─── -->
        <div class="grid-wrapper" id="gridWrapper">
            <div class="grid-scroll">
                <table class="timetable" id="timetable">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- ─── Pagination ─── -->
        <div class="pagination-bar" id="paginationBar">
            <span class="pagination-info" id="paginationInfo">Showing 1-10 of 20</span>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>

        <!-- ─── Summary Stat Cards ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'summaryTotal', 'label' => 'Total Requests'],
                ['class' => 'card-approved', 'valueId' => 'summaryApproved', 'label' => 'Approved'],
                ['class' => 'card-pending', 'valueId' => 'summaryPending', 'label' => 'Pending'],
                ['class' => 'card-rejected', 'valueId' => 'summaryRejected', 'label' => 'Rejected'],
            ]
        ])

        <!-- ─── Empty State ─── -->
        <div class="empty-state" id="emptyState" style="display:none">
            <svg class="empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <h3 class="empty-title" id="emptyTitle">You haven't submitted any replacement requests for this semester.</h3>
            <p class="empty-text" id="emptyText">Submit a replacement request for any conflicted class.</p>
            <button class="empty-cta" id="emptyCta" onclick="window.location.href='/replacement-arrangement'" style="display:none">Submit a Replacement Request</button>
        </div>

    <!-- ═══ View Details Modal ═══ -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="detailsModal">
            <div class="modal-header">
                <h3 class="modal-title">Request Details</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <div class="modal-footer-left">
                    <button class="btn-danger" id="cancelRequestBtn" style="display:none" onclick="confirmCancelRequest()">Cancel Request</button>
                </div>
                <div class="modal-footer-right">
                    <button class="btn-outline" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
        const mockRequests = [
            { id: 1, requestedAt: '2026-08-30T10:30:00', courseCode: 'BMIT5555', courseName: 'Software Engineering', classType: 'L', classDate: '2026-08-31', classDay: 'Monday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B104', totalStudents: 35, cohortCounts: [20, 15], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: '2026-09-02', replacementTime: '09:00 – 11:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 2, requestedAt: '2026-08-31T14:15:00', courseCode: 'BMIT5555', courseName: 'Software Engineering', classType: 'T', classDate: '2026-09-02', classDay: 'Wednesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B105', totalStudents: 28, cohorts: ['DFT2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-04', replacementTime: '14:00 – 16:00', replacementVenue: 'B110', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-01T09:00:00', remarks: null },
            { id: 3, requestedAt: '2026-09-01T08:45:00', courseCode: 'BMIT6767', courseName: 'Object-Oriented Programming', classType: 'L', classDate: '2026-09-03', classDay: 'Thursday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B103', totalStudents: 24, cohorts: ['DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: '2026-09-07', replacementTime: '09:00 – 11:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 4, requestedAt: '2026-09-02T11:20:00', courseCode: 'BMIT6767', courseName: 'Object-Oriented Programming', classType: 'T', classDate: '2026-09-07', classDay: 'Monday', timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B106', totalStudents: 20, cohorts: ['DSF2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-09', replacementTime: '11:00 – 13:00', replacementVenue: 'B201', reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-03T16:30:00', remarks: null },
            { id: 5, requestedAt: '2026-09-03T09:10:00', courseCode: 'BMIT5678', courseName: 'Database Systems', classType: 'T', classDate: '2026-09-08', classDay: 'Tuesday', timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B105', totalStudents: 30, cohortCounts: [15, 15], cohorts: ['DSF2 (S1)', 'DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: '2026-09-10', replacementTime: '11:00 – 13:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 6, requestedAt: '2026-09-04T15:00:00', courseCode: 'BMIT9012', courseName: 'Computer Networks', classType: 'L', classDate: '2026-09-10', classDay: 'Thursday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 22, cohorts: ['DFT2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-14', replacementTime: '08:00 – 10:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-07T10:00:00', remarks: null },
            { id: 7, requestedAt: '2026-09-05T13:30:00', courseCode: 'BMIT3456', courseName: 'Artificial Intelligence', classType: 'T', classDate: '2026-09-11', classDay: 'Friday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B103', totalStudents: 18, cohorts: ['DSF2 (S1)'], status: 'Rejected', rejectionReason: 'Insufficient notice period. Requests must be submitted at least 5 working days in advance.', replacementDate: '2026-09-14', replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-08T08:15:00', remarks: null },
            { id: 8, requestedAt: '2026-09-06T10:00:00', courseCode: 'BMIT7890', courseName: 'Project Management', classType: 'L', classDate: '2026-09-14', classDay: 'Monday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 20, cohortCounts: [10, 10], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: '2026-09-16', replacementTime: '14:00 – 16:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 9, requestedAt: '2026-09-07T16:45:00', courseCode: 'BMIT7890', courseName: 'Project Management', classType: 'T', classDate: '2026-09-15', classDay: 'Tuesday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B202', totalStudents: 15, cohorts: ['DFT2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: '2026-09-17', replacementTime: '08:00 – 10:00', replacementVenue: 'B103', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-09T14:00:00', remarks: 'Replacement conducted successfully.' },
            { id: 10, requestedAt: '2026-09-08T07:30:00', courseCode: 'BMIT9999', courseName: 'Machine Learning', classType: 'T', classDate: '2026-09-16', classDay: 'Wednesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B110', totalStudents: 15, cohorts: ['DSF2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-18', replacementTime: '10:00 – 12:00', replacementVenue: 'B105', reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-10T11:00:00', remarks: null },
            { id: 11, requestedAt: '2026-09-09T12:15:00', courseCode: 'BMIT1234', courseName: 'Data Structures', classType: 'L', classDate: '2026-09-18', classDay: 'Friday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 30, cohorts: ['DSF2 (S1)'], status: 'Rejected', rejectionReason: 'Venue unavailable on the requested replacement date.', replacementDate: '2026-09-21', replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-11T09:30:00', remarks: null },
            { id: 12, requestedAt: '2026-09-10T14:00:00', courseCode: 'BMIT4567', courseName: 'Web Development', classType: 'L', classDate: '2026-09-21', classDay: 'Monday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 32, cohorts: ['DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: '2026-09-23', replacementTime: '09:00 – 11:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 13, requestedAt: '2026-09-11T08:30:00', courseCode: 'BMIT4567', courseName: 'Web Development', classType: 'T', classDate: '2026-09-22', classDay: 'Tuesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 25, cohortCounts: [12, 13], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: '2026-09-24', replacementTime: '14:00 – 16:00', replacementVenue: 'B106', reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-14T15:45:00', remarks: 'Replacement completed. Student attendance recorded.' },
            { id: 14, requestedAt: '2026-09-12T10:45:00', courseCode: 'BMIT8888', courseName: 'Cloud Computing', classType: 'T', classDate: '2026-09-23', classDay: 'Wednesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B105', totalStudents: 20, cohorts: ['DSF2 (S1)'], status: 'Approved', rejectionReason: null, replacementDate: '2026-09-25', replacementTime: '10:00 – 12:00', replacementVenue: 'B202', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-15T13:00:00', remarks: null },
            { id: 15, requestedAt: '2026-09-13T09:00:00', courseCode: 'BMIT7777', courseName: 'Cybersecurity', classType: 'L', classDate: '2026-08-31', classDay: 'Monday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 18, cohortCounts: [10, 8], cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], status: 'Cancelled', rejectionReason: null, replacementDate: '2026-09-02', replacementTime: '08:00 – 10:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: 'Request withdrawn by lecturer.' },
            { id: 16, requestedAt: '2026-09-14T11:30:00', courseCode: 'BMIT7777', courseName: 'Cybersecurity', classType: 'T', classDate: '2026-09-02', classDay: 'Wednesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B202', totalStudents: 12, cohorts: ['DFT2 (S1)'], status: 'Pending', rejectionReason: null, replacementDate: '2026-09-04', replacementTime: '14:00 – 16:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 17, requestedAt: '2026-09-15T15:30:00', courseCode: 'BMIT3344', courseName: 'Embedded Systems', classType: 'T', classDate: '2026-09-07', classDay: 'Monday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B103', totalStudents: 12, cohorts: ['DSF2 (S1)'], status: 'Completed', rejectionReason: null, replacementDate: '2026-09-10', replacementTime: '08:00 – 10:00', replacementVenue: 'B104', reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-16T08:00:00', remarks: 'Replacement completed.' },
            { id: 18, requestedAt: '2026-09-16T07:15:00', courseCode: 'BMIT2222', courseName: 'Mobile Computing', classType: 'L', classDate: '2026-09-09', classDay: 'Wednesday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 28, cohorts: ['DFT2 (S1)'], status: 'Cancelled', rejectionReason: null, replacementDate: '2026-09-11', replacementTime: '09:00 – 11:00', replacementVenue: null, reviewedBy: null, reviewedAt: null, remarks: null },
            { id: 19, requestedAt: '2026-09-17T13:00:00', courseCode: 'BMIT1111', courseName: 'Human-Computer Interaction', classType: 'L', classDate: '2026-09-15', classDay: 'Tuesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 22, cohorts: ['DSF2 (S1)'], status: 'Rejected', rejectionReason: 'Scheduling conflict with another lecturer\'s booking.', replacementDate: '2026-09-17', replacementTime: '14:00 – 16:00', replacementVenue: null, reviewedBy: 'Dr. Lim (Dean)', reviewedAt: '2026-09-18T10:30:00', remarks: null },
            { id: 20, requestedAt: '2026-09-18T09:45:00', courseCode: 'BMIT4433', courseName: 'Information Security', classType: 'T', classDate: '2026-09-17', classDay: 'Thursday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 18, cohorts: ['DFT2 (S1)'], status: 'Rejected', rejectionReason: 'Lecturer unavailable on the requested date.', replacementDate: '2026-09-21', replacementTime: '10:00 – 12:00', replacementVenue: null, reviewedBy: 'Dr. Ahmad (HOD)', reviewedAt: '2026-09-19T08:00:00', remarks: null },
        ];

        const weekRanges = [
            { value: '1', label: 'Week 1: 31 Aug – 6 Sep', start: '2026-08-31', end: '2026-09-06' },
            { value: '2', label: 'Week 2: 7 Sep – 13 Sep', start: '2026-09-07', end: '2026-09-13' },
            { value: '3', label: 'Week 3: 14 Sep – 20 Sep', start: '2026-09-14', end: '2026-09-20' },
            { value: '4', label: 'Week 4: 21 Sep – 27 Sep', start: '2026-09-21', end: '2026-09-27' },
        ];

        function formatDateTime(iso) {
            if (!iso) return '';
            const [datePart, timePart] = iso.split('T');
            const [y, mo, d] = datePart.split('-');
            const [h, mi] = timePart.split(':');
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const day = parseInt(d);
            const month = months[parseInt(mo) - 1];
            const year = parseInt(y);
            let hh = parseInt(h);
            const mm = mi;
            const ampm = hh >= 12 ? 'PM' : 'AM';
            hh = hh === 0 ? 12 : hh > 12 ? hh - 12 : hh;
            return day + ' ' + month + ' ' + year + ', ' + hh + ':' + mm + ' ' + ampm;
        }

        function statusClass(status) {
            const map = {
                'Pending': 'status-pending',
                'Approved': 'status-approved',
                'Rejected': 'status-rejected',
                'Cancelled': 'status-cancelled',
                'Completed': 'status-completed'
            };
            return map[status] || '';
        }

        function dayAbbr(day) {
            return day.substring(0, 3);
        }

        function isoDayName(iso) {
            var p = iso.split('-');
            var d = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
            return ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];
        }

        function formatClassBlock(r) {
            var d = dayAbbr(r.classDay);
            var dateStr = formatDate(r.classDate);
            var wn = getWeekNumber(r.classDate);
            var weekTag = wn ? ' (Week ' + wn + ')' : '';
            var timeStr = to12h(r.timeStart) + ' to ' + to12h(r.timeEnd);
            var hrs = r.duration + ' hr' + (r.duration > 1 ? 's' : '');
            return '<div class="cell-class-block"><span class="class-day-date">' + d + ', ' + dateStr + weekTag + '</span><br><span class="class-time">' + timeStr + '</span> <span class="class-duration">(' + hrs + ')</span></div>';
        }

        function formatReplacementBlock(r) {
            if (!r.replacementDate) return '<span style="color:var(--color-on-surface-variant);opacity:0.5">&mdash;</span>';
            var d = dayAbbr(isoDayName(r.replacementDate));
            var dateStr = formatDate(r.replacementDate);
            var wn = getWeekNumber(r.replacementDate);
            var weekTag = wn ? ' (Week ' + wn + ')' : '';
            var statusCls = statusClass(r.status);
            return '<div class="cell-class-block"><span class="class-day-date">' + d + ', ' + dateStr + weekTag + '</span><br><span class="class-time ' + statusCls + '">' + r.replacementTime + '</span></div>';
        }

        function getWeekRange(weekVal) {
            const found = weekRanges.find(function(w) { return w.value === weekVal; });
            return found || null;
        }

        function isInWeek(classDate, weekVal) {
            if (weekVal === 'all') return true;
            const range = getWeekRange(weekVal);
            if (!range) return true;
            return classDate >= range.start && classDate <= range.end;
        }

        function getWeekNumber(iso) {
            for (var i = 0; i < weekRanges.length; i++) {
                if (iso >= weekRanges[i].start && iso <= weekRanges[i].end) return weekRanges[i].value;
            }
            return '';
        }

        let currentPage = 1;
        const pageSize = 10;
        let sortState = { field: 'requestedAt', dir: 'asc' };
        let currentFiltered = [];

        function renderTable() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const statusVal = document.getElementById('statusFilter').value;
            const weekVal = document.getElementById('weekFilter').value;

            let filtered = mockRequests.filter(function(r) {
                const matchesSearch = query === '' ||
                    r.courseCode.toLowerCase().includes(query) ||
                    r.courseName.toLowerCase().includes(query);
                const matchesStatus = statusVal === 'all' || r.status === statusVal;
                const matchesWeek = isInWeek(r.classDate, weekVal);
                const matchesCompleted = !document.getElementById('hideCompleted').checked || r.status !== 'Completed';
                return matchesSearch && matchesStatus && matchesWeek && matchesCompleted;
            });

            if (sortState.field) {
                filtered.sort(function(a, b) {
                    var va, vb;
                    if (sortState.field === 'requestedAt') {
                        va = a.requestedAt;
                        vb = b.requestedAt;
                    } else if (sortState.field === 'courseCode') {
                        va = a.courseCode;
                        vb = b.courseCode;
                    } else if (sortState.field === 'classDate') {
                        va = a.classDate;
                        vb = b.classDate;
                    }
                    if (va < vb) return sortState.dir === 'asc' ? -1 : 1;
                    if (va > vb) return sortState.dir === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            currentFiltered = filtered;

            const offset = (currentPage - 1) * pageSize;
            const pageData = filtered.slice(offset, offset + pageSize);

            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            const tr = document.createElement('tr');
            const columns = [
                { label: '#', cls: 'col-no', sortable: false },
                { label: 'Requested At', cls: 'col-requested-at', sortable: true, field: 'requestedAt' },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'courseCode' },
                { label: 'Type', cls: 'col-type', sortable: false },
                { label: 'Original Class', cls: 'col-original', sortable: true, field: 'classDate' },
                { label: 'Requested Replacement', cls: 'col-replacement', sortable: false },
                { label: 'Requested Venue', cls: 'col-venue', sortable: false },
                { label: 'Students', cls: 'col-students', sortable: false },
                { label: 'Affected Cohort(s)', cls: 'col-cohort', sortable: false },
                { label: 'Status (Click for detail)', cls: 'col-status', sortable: false },
            ];
            columns.forEach(function(col) {
                const th = document.createElement('th');
                th.className = col.cls;
                if (col.sortable) {
                    th.classList.add('sortable');
                    var arrow = '';
                    if (sortState.field === col.field) {
                        arrow = '<span class="sort-arrow">' + (sortState.dir === 'asc' ? '&#9650;' : '&#9660;') + '</span>';
                    }
                    th.innerHTML = col.label + arrow;
                    th.addEventListener('click', function() {
                        if (sortState.field === col.field) {
                            sortState.dir = sortState.dir === 'asc' ? 'desc' : 'asc';
                        } else {
                            sortState.field = col.field;
                            sortState.dir = 'asc';
                        }
                        currentPage = 1;
                        renderTable();
                    });
                } else {
                    th.textContent = col.label;
                }
                tr.appendChild(th);
            });
            head.appendChild(tr);

            const isFullyEmpty = mockRequests.length === 0;
            const isFilteredEmpty = pageData.length === 0;

            if (isFullyEmpty) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = "You haven't submitted any replacement requests for this semester.";
                document.getElementById('emptyText').textContent = 'Submit a replacement request for any conflicted class.';
                document.getElementById('emptyCta').style.display = 'inline-block';
                document.getElementById('gridWrapper').style.display = 'none';
                document.getElementById('paginationBar').style.display = 'none';
                document.getElementById('summaryBar').style.display = 'none';
            } else if (isFilteredEmpty) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'No replacement requests match your search or filter criteria.';
                document.getElementById('emptyText').textContent = 'Try adjusting your filters.';
                document.getElementById('emptyCta').style.display = 'none';
                document.getElementById('gridWrapper').style.display = 'none';
                document.getElementById('paginationBar').style.display = 'none';
                document.getElementById('summaryBar').style.display = 'none';
            } else {
                document.getElementById('emptyState').style.display = 'none';
                document.getElementById('gridWrapper').style.display = 'block';
                document.getElementById('paginationBar').style.display = 'flex';
                document.getElementById('summaryBar').style.display = 'grid';

                pageData.forEach(function(r, i) {
                    const row = document.createElement('tr');
                    const badgeHtml = '<span class="badge ' + statusClass(r.status) + '" onclick="openModal(' + (offset + i) + ')">' + r.status + '</span>';
                    var cells = [
                        { html: String(offset + i + 1), cls: 'col-no' },
                        { html: formatDateTime(r.requestedAt), cls: 'col-requested-at' },
                        { html: '<span class="cell-code">' + r.courseCode + '</span><span class="cell-name">' + r.courseName + '</span>', cls: 'col-code' },
                        { html: r.classType === 'L' ? 'Lecture' : 'Tutorial', cls: 'col-type' },
                        { html: formatClassBlock(r), cls: 'col-original' },
                        { html: formatReplacementBlock(r), cls: 'col-replacement' },
                        { html: r.venue, cls: 'col-venue' },
                        { html: String(r.totalStudents), cls: 'col-students' },
                        { html: r.cohorts.join('<br>'), cls: 'col-cohort' },
                        { html: badgeHtml, cls: 'col-status' },
                    ];
                    cells.forEach(function(cell) {
                        const td = document.createElement('td');
                        td.className = cell.cls;
                        td.innerHTML = cell.html;
                        row.appendChild(td);
                    });
                    body.appendChild(row);
                });
            }

            document.getElementById('gridWrapper').querySelector('.grid-scroll').scrollLeft = 0;
            updatePagination();
            updateResultCount();
            updateSummary();
        }

        function updatePagination() {
            const totalPages = Math.ceil(currentFiltered.length / pageSize);
            const info = document.getElementById('paginationInfo');
            if (currentFiltered.length === 0) {
                info.textContent = 'Showing 0 of 0';
            } else {
                const from = (currentPage - 1) * pageSize + 1;
                const to = Math.min(currentPage * pageSize, currentFiltered.length);
                info.textContent = 'Showing ' + from + '–' + to + ' of ' + currentFiltered.length;
            }

            const controls = document.getElementById('paginationControls');
            controls.innerHTML = '';

            const prev = document.createElement('button');
            prev.className = 'page-btn';
            prev.textContent = '\u2039';
            prev.disabled = currentPage <= 1;
            prev.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            });
            controls.appendChild(prev);

            for (var p = 1; p <= totalPages; p++) {
                (function(page) {
                    const btn = document.createElement('button');
                    btn.className = 'page-btn';
                    if (page === currentPage) btn.classList.add('active');
                    btn.textContent = String(page);
                    btn.addEventListener('click', function() {
                        currentPage = page;
                        renderTable();
                    });
                    controls.appendChild(btn);
                })(p);
            }

            const next = document.createElement('button');
            next.className = 'page-btn';
            next.textContent = '\u203A';
            next.disabled = currentPage >= totalPages;
            next.addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            });
            controls.appendChild(next);
        }

        function updateResultCount() {
            const count = document.getElementById('resultCount');
            count.textContent = 'Showing ' + currentFiltered.length + ' of ' + mockRequests.length + ' results';
        }

        function updateSummary() {
            const total = mockRequests.length;
            const approved = mockRequests.filter(function(r) { return r.status === 'Approved'; }).length;
            const pending = mockRequests.filter(function(r) { return r.status === 'Pending'; }).length;
            const rejected = mockRequests.filter(function(r) { return r.status === 'Rejected'; }).length;

            document.getElementById('summaryTotal').textContent = total;
            document.getElementById('summaryApproved').textContent = approved;
            document.getElementById('summaryPending').textContent = pending;
            document.getElementById('summaryRejected').textContent = rejected;
        }

        function openModal(index) {
            const r = currentFiltered[index];
            if (!r) return;
            const body = document.getElementById('modalBody');
            var html = '';

            function field(label, value) {
                if (value === null || value === '') return '';
                return '<div class="modal-field"><span class="modal-field-label">' + label + '</span><span class="modal-field-value">' + value + '</span></div>';
            }

            function section(title) {
                return '<div class="modal-section-title">' + title + '</div>'; }

            function cohortBreakdown(r) {
                if (!r.cohortCounts) return String(r.totalStudents);
                var parts = [];
                for (var ci = 0; ci < r.cohorts.length; ci++) {
                    parts.push(r.cohortCounts[ci]);
                }
                return parts.join(' + ') + ' = ' + r.totalStudents;
            }

            function statusDesc(s) {
                var map = { 'Pending': 'Awaiting approval', 'Approved': 'Replacement scheduled', 'Rejected': 'Request declined', 'Cancelled': 'Request withdrawn', 'Completed': 'Replacement conducted' };
                return map[s] || '';
            }

            html += section('General Info');
            html += field('Request No.', '#' + r.id);
            html += field('Requested At', formatDateTime(r.requestedAt));
            html += field('Status', '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span><span style="color:var(--color-on-surface-variant);font-size:12px;margin-left:8px">' + statusDesc(r.status) + '</span>');
            if (r.status === 'Rejected') {
                html += field('Rejection Reason', r.rejectionReason);
            }
            html += field('Course Code', r.courseCode);
            html += field('Course Name', r.courseName);
            html += field('Class Type', r.classType === 'L' ? 'Lecture' : 'Tutorial');

            html += section('Original Class Detail');
            html += field('Affected Cohort(s)', r.cohorts.join(', '));
            html += field('Total Students', cohortBreakdown(r));
            html += field('Original Date', formatDate(r.classDate));
            html += field('Original Day', r.classDay);
            html += field('Original Time', to12h(r.timeStart) + ' – ' + to12h(r.timeEnd));
            html += field('Duration', String(r.duration) + ' hours');
            html += field('Original Venue', r.venue);

            html += section('Requested Replacement Class');
            html += field('Replacement Date', r.replacementDate ? formatDate(r.replacementDate) : null);
            html += field('Replacement Time', r.replacementTime);
            html += field('Replacement Venue', r.replacementVenue || '—');
            html += field('Reviewed By', r.reviewedBy);
            html += field('Reviewed At', r.reviewedAt ? formatDateTime(r.reviewedAt) : null);

            body.innerHTML = html;
            document.getElementById('modalOverlay').classList.add('show');
            document.getElementById('cancelRequestBtn').style.display = r.status === 'Pending' ? 'inline-block' : 'none';
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('show');
        }

        function confirmCancelRequest() {
            if (confirm('Are you sure you want to cancel this replacement request? This action cannot be undone.')) {
                alert('Your replacement request has been cancelled.');
                closeModal();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const weekSel = document.getElementById('weekFilter');
            weekSel.innerHTML = '<option value="all">All Weeks</option>';
            weekRanges.forEach(function(w) {
                const opt = document.createElement('option');
                opt.value = w.value;
                opt.textContent = w.label;
                weekSel.appendChild(opt);
            });

            renderTable();

            document.getElementById('searchInput').addEventListener('input', function() {
                currentPage = 1;
                renderTable();
            });
            document.getElementById('statusFilter').addEventListener('change', function() {
                currentPage = 1;
                renderTable();
            });
            document.getElementById('weekFilter').addEventListener('change', function() {
                currentPage = 1;
                renderTable();
            });
            document.getElementById('hideCompleted').addEventListener('change', function() {
                currentPage = 1;
                renderTable();
            });
            document.getElementById('clearFilters').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('weekFilter').value = 'all';
                document.getElementById('hideCompleted').checked = true;
                currentPage = 1;
                renderTable();
            });

            document.getElementById('modalOverlay').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        });
@endsection
