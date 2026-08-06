@extends('layouts.ui-template', ['activeNav' => 'replacement-history', 'pageKey' => 'myRequestHistory'])

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
        .timetable td.col-requested-at { width: 180px; white-space: normal; }
        .col-original { width: 170px; }
        .col-replacement { width: 170px; }
        .col-original, .col-replacement { white-space: normal; }
        .col-venue { width: 75px; }
        .col-cohort { width: 120px; }
        .col-status { width: 130px; }

        .grid-scroll .timetable { min-width: 1235px; }

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
        .summary-card.card-hours .summary-value { color: var(--color-on-surface); }
        .summary-card.card-hours {
            border: 1px dashed var(--color-outline-strong);
            background: var(--color-surface-variant);
        }
        .summary-card.card-total {
            border: 2px solid var(--color-primary);
            background: var(--color-primary-container);
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

        /* ───── F2: Bulk Selection ───── */
        .bulk-checkbox {
            width: 16px;
            height: 16px;
            accent-color: var(--color-primary);
            cursor: pointer;
            margin: 0 auto;
            display: block;
        }
        .bulk-checkbox:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        .col-checkbox { width: 40px; text-align: center; }
        .row-selected { background: var(--color-primary-container) !important; }
        .bulk-action-bar {
            display: none;
            position: fixed;
            bottom: 24px;
            left: 24px;
            z-index: 50;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: 10px 16px;
            align-items: center;
            gap: 12px;
        }
        .bulk-action-bar.visible { display: flex; }
        .bulk-action-bar .bulk-count {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-on-surface);
        }
        .btn-bulk-cancel {
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid var(--color-error);
            background: transparent;
            color: var(--color-error);
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .btn-bulk-cancel:hover {
            background: var(--color-error);
            color: #fff;
        }
        .btn-bulk-clear {
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid var(--color-outline);
            background: transparent;
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-bulk-clear:hover {
            background: var(--color-surface-variant);
        }

        /* ───── F3: Request Age Indicator ───── */
        .age-green { color: var(--color-secondary) !important; }
        .age-amber { color: var(--color-tertiary) !important; }
        .age-red { color: var(--color-error) !important; }
        .age-relative { font-size: 11px; opacity: 0.7; }

        /* ───── F4: Quick Actions in Rows ───── */
        .col-actions { width: 70px; text-align: center; }
        .btn-inline-cancel {
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid var(--color-error);
            background: transparent;
            color: var(--color-error);
            font-family: inherit;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.15s, background 0.15s, color 0.15s;
        }
        tr:hover .btn-inline-cancel { opacity: 1; }
        .btn-inline-cancel:hover {
            background: var(--color-error);
            color: #fff;
        }

        /* ───── F6: Status History Timeline ───── */
        .timeline {
            padding: 8px 0 4px 0;
        }
        .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            position: relative;
            padding-bottom: 16px;
        }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-dot-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-shrink: 0;
            width: 20px;
        }
        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .timeline-line {
            width: 2px;
            flex: 1;
            min-height: 16px;
            background: var(--color-outline);
            margin-top: 4px;
        }
        .timeline-item:last-child .timeline-line { display: none; }
        .timeline-text {
            flex: 1;
            font-size: 12px;
            line-height: 1.4;
        }
        .timeline-label {
            font-weight: 600;
            color: var(--color-on-surface);
        }
        .timeline-time {
            font-size: 11px;
            color: var(--color-on-surface-variant);
            margin-top: 1px;
        }

        /* ───── F7: Keyboard Shortcuts ───── */
        .row-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }

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
            .card-view { display: block; }
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'My Request History', 'description' => 'View and monitor all replacement requests submitted during the current semester.'])

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
                @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeekFilter()', 'nextOnclick' => 'nextWeekFilter()', 'selectId' => 'weekFilter', 'selectOnclick' => 'weekFilterChanged(this.value)', 'showTodayBtn' => false])
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
        @include('partials.ui-grid-table', ['wrapperId' => 'gridWrapper'])

        <!-- ─── Card View (mobile) ─── -->
        <div class="card-view" id="cardView"></div>

        <!-- ─── Pagination ─── -->
        <div class="pagination-bar" id="paginationBar">
            @include('partials.ui-rpp', ['id' => 'rowsPerPage', 'default' => 10, 'options' => [10, 25, 50, 'all']])
            <span class="pagination-info" id="paginationInfo">Showing 1-10 of 20</span>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>

        <!-- ─── Bulk Action Bar ─── -->
        <div class="bulk-action-bar" id="bulkActionBar">
            <span class="bulk-count" id="bulkCount">0 selected</span>
            <button class="btn-bulk-clear" onclick="clearAllSelections()">Clear</button>
            <button class="btn-bulk-cancel" id="btnBatchCancelRequest" onclick="batchCancelSelected()">Cancel Selected Request(s)</button>
        </div>

        <!-- ─── Summary Stat Cards ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'summaryTotal', 'label' => 'Total Requests'],
                ['class' => 'card-hours', 'valueId' => 'summaryHours', 'label' => 'Replacement Hours'],
                ['class' => 'card-approved', 'valueId' => 'summaryApproved', 'label' => 'Approved'],
                ['class' => 'card-pending', 'valueId' => 'summaryPending', 'label' => 'Pending'],
                ['class' => 'card-rejected', 'valueId' => 'summaryRejected', 'label' => 'Rejected'],
            ]
        ])

        <!-- ─── Empty State ─── -->
        @include('partials.ui-empty-state', ['title' => "You haven't submitted any replacement requests for this semester.", 'text' => 'Submit a replacement request for any conflicted class.', 'ctaLabel' => 'Submit a Replacement Request', 'ctaOnclick' => "window.location.href='/replacement-arrangement'"])

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
                    <button class="btn-danger" id="cancelRequestBtn" style="display:none" onclick="openCancelConfirm()">Cancel Request</button>
                </div>
                <div class="modal-footer-right">
                    <button class="btn-outline" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Cancel Confirmation Modal ═══ -->
    <div class="modal-overlay" id="cancelConfirmOverlay" style="display:none">
        <div class="modal" style="max-width:420px">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Cancellation</h3>
                <button class="modal-close" onclick="closeCancelConfirm()">✕</button>
            </div>
            <div class="modal-body" id="cancelConfirmBody">
                <p style="font-size:14px;color:var(--color-on-surface);line-height:1.5">Are you sure you want to cancel this replacement request? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <div class="modal-footer-left"></div>
                <div class="modal-footer-right">
                    <button class="btn-outline" onclick="closeCancelConfirm()">No, Keep It</button>
                    <button class="btn-danger" id="confirmCancelAction">Yes, Cancel Request</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Batch Cancel Confirmation Modal ═══ -->
    <div class="modal-overlay" id="batchCancelOverlay" style="display:none">
        <div class="modal" style="max-width:440px">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Batch Cancellation</h3>
                <button class="modal-close" onclick="closeBatchCancelConfirm()">✕</button>
            </div>
            <div class="modal-body" id="batchCancelBody"></div>
            <div class="modal-footer">
                <div class="modal-footer-left"></div>
                <div class="modal-footer-right">
                    <button class="btn-outline" onclick="closeBatchCancelConfirm()">No, Keep Them</button>
                    <button class="btn-danger" id="confirmBatchCancelAction">Yes, Cancel All</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
        let mockRequests = MockData.requests;

        const weekRanges = [
            { value: '1', label: 'Week 1 \u00b7 31 Aug 2026 ~ 06 Sep 2026', labelShort: 'Week 1 \u00b7 31 Aug ~ 06 Sep', start: '2026-08-31', end: '2026-09-06' },
            { value: '2', label: 'Week 2 \u00b7 07 Sep 2026 ~ 13 Sep 2026', labelShort: 'Week 2 \u00b7 07 Sep ~ 13 Sep', start: '2026-09-07', end: '2026-09-13' },
            { value: '3', label: 'Week 3 \u00b7 14 Sep 2026 ~ 20 Sep 2026', labelShort: 'Week 3 \u00b7 14 Sep ~ 20 Sep', start: '2026-09-14', end: '2026-09-20' },
            { value: '4', label: 'Week 4 \u00b7 21 Sep 2026 ~ 27 Sep 2026', labelShort: 'Week 4 \u00b7 21 Sep ~ 27 Sep', start: '2026-09-21', end: '2026-09-27' },
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

        const pageState = { currentPage: 1 };
        let rowsPerPage = parseInt(localStorage.getItem('mrh-rows-per-page')) || 10;
        let sortState = { field: 'requestedAt', dir: 'desc' };
        let currentFiltered = [];
        let selectedIds = new Set();
        let searchDebounce = null;
        let focusedRowIndex = -1;

        function getRequestAge(requestedAt) {
            const now = new Date();
            const then = new Date(requestedAt);
            const diffMs = now - then;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            return Math.max(0, diffDays);
        }

        function ageClass(days) {
            if (days <= 2) return 'age-green';
            if (days <= 7) return 'age-amber';
            return 'age-red';
        }

        function relativeTime(days) {
            if (days === 0) return 'Today';
            if (days === 1) return '1 day ago';
            return days + ' days ago';
        }

        function saveFilters() {
            var filters = {
                status: document.getElementById('statusFilter').value,
                search: document.getElementById('searchInput').value,
                excludeCompleted: document.getElementById('hideCompleted').checked
            };
            localStorage.setItem('mrh-filters', JSON.stringify(filters));
        }

        function restoreFilters() {
            var raw = localStorage.getItem('mrh-filters');
            if (!raw) return;
            try {
                var filters = JSON.parse(raw);
                if (filters.status) document.getElementById('statusFilter').value = filters.status;
                if (filters.search) document.getElementById('searchInput').value = filters.search;
                if (typeof filters.excludeCompleted === 'boolean') document.getElementById('hideCompleted').checked = filters.excludeCompleted;
            } catch (e) {}
        }

        function renderTable() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const statusVal = document.getElementById('statusFilter').value;
            const weekVal = document.getElementById('weekFilter').value;
            focusedRowIndex = -1;

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
                    return compareBy(sortState, va, vb);
                });
            }

            currentFiltered = filtered;

            const isAll = rowsPerPage === 'all' || rowsPerPage === Infinity;
            const effectivePageSize = isAll ? filtered.length : rowsPerPage;
            const offset = (pageState.currentPage - 1) * effectivePageSize;
            const pageData = filtered.slice(offset, offset + effectivePageSize);

            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            const tr = document.createElement('tr');
            const thCheck = document.createElement('th');
            thCheck.className = 'col-checkbox';
            const headerCheck = document.createElement('input');
            headerCheck.type = 'checkbox';
            headerCheck.className = 'bulk-checkbox';
            headerCheck.id = 'headerCheckbox';
            headerCheck.addEventListener('change', function() {
                var pendingRows = body.querySelectorAll('tr[data-pending="true"] .row-checkbox');
                pendingRows.forEach(function(cb) {
                    cb.checked = headerCheck.checked;
                    var id = parseInt(cb.dataset.id);
                    if (headerCheck.checked) {
                        selectedIds.add(id);
                    } else {
                        selectedIds.delete(id);
                    }
                });
                updateBulkBar();
                highlightSelectedRows();
            });
            thCheck.appendChild(headerCheck);
            tr.appendChild(thCheck);

            const columns = [
                { label: 'Requested At', cls: 'col-requested-at', sortable: true, field: 'requestedAt' },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'courseCode' },
                { label: 'Original Class', cls: 'col-original', sortable: true, field: 'classDate' },
                { label: 'Requested Replacement', cls: 'col-replacement', sortable: false },
                { label: 'Requested Venue', cls: 'col-venue', sortable: false },
                { label: 'Students', cls: 'col-students', sortable: false },
                { label: 'Affected Cohort(s)', cls: 'col-cohort', sortable: false },
                { label: 'Status', cls: 'col-status', sortable: false },
                { label: 'Quick Cancel', cls: 'col-actions', sortable: false },
            ];
            columns.forEach(function(col) {
                tr.appendChild(makeSortableHeader(col, sortState, function() {
                    pageState.currentPage = 1;
                    renderTable();
                }));
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
                    row.dataset.id = r.id;
                    row.dataset.pending = r.status === 'Pending' ? 'true' : 'false';
                    if (selectedIds.has(r.id)) row.classList.add('row-selected');

                    const globalIndex = offset + i;
                    const isPending = r.status === 'Pending';
                    const badgeHtml = '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>';

                    const days = getRequestAge(r.requestedAt);
                    const ageCls = ageClass(days);

                    const checkTd = document.createElement('td');
                    checkTd.className = 'col-checkbox';
                    const rowCheck = document.createElement('input');
                    rowCheck.type = 'checkbox';
                    rowCheck.className = 'bulk-checkbox row-checkbox';
                    rowCheck.dataset.id = r.id;
                    rowCheck.disabled = !isPending;
                    if (selectedIds.has(r.id)) rowCheck.checked = true;
                    rowCheck.addEventListener('change', function() {
                        var id = parseInt(this.dataset.id);
                        if (this.checked) {
                            selectedIds.add(id);
                        } else {
                            selectedIds.delete(id);
                        }
                        updateBulkBar();
                        highlightSelectedRows();
                    });
                    checkTd.appendChild(rowCheck);
                    row.appendChild(checkTd);

                    var cells = [
                        { html: '<span class="' + ageCls + '">' + formatDateTime(r.requestedAt) + '<br><span class="age-relative">(' + relativeTime(days) + ')</span></span>', cls: 'col-requested-at' },
                        { html: '<span class="cell-code">' + r.courseCode + ' <span class="cell-type">(' + r.classType + ')</span></span><span class="cell-name">' + r.courseName + '</span>', cls: 'col-code' },
                        { html: formatClassBlock(r), cls: 'col-original' },
                        { html: formatReplacementBlock(r), cls: 'col-replacement' },
                        { html: r.venue, cls: 'col-venue' },
                        { html: String(r.totalStudents), cls: 'col-students' },
                        { html: r.cohorts.join('<br>'), cls: 'col-cohort' },
                        { html: badgeHtml, cls: 'col-status' },
                        { html: isPending ? '<button class="btn-inline-cancel" onclick="quickCancel(' + r.id + ')">Cancel</button>' : '<span style="color:var(--color-on-surface-variant)">-</span>', cls: 'col-actions' },
                    ];
                    cells.forEach(function(cell) {
                        const td = document.createElement('td');
                        td.className = cell.cls;
                        td.innerHTML = cell.html;
                        row.appendChild(td);
                    });
                    row.addEventListener('click', function(e) {
                        if (e.target.closest('.bulk-checkbox') || e.target.closest('.btn-inline-cancel')) return;
                        openModal(globalIndex);
                    });
                    row.style.cursor = 'pointer';
                    body.appendChild(row);
                });
            }

            document.getElementById('gridWrapper').querySelector('.grid-scroll').scrollLeft = 0;

            if (isAll) {
                document.getElementById('paginationInfo').textContent = 'Showing all ' + filtered.length + ' requests';
                document.getElementById('paginationControls').innerHTML = '';
            } else {
                paginate({ data: currentFiltered, pageSize: effectivePageSize, state: pageState, infoId: 'paginationInfo', controlsId: 'paginationControls', render: renderTable });
            }

            updateResultCount({ elId: 'resultCount', data: currentFiltered, total: mockRequests.length, label: 'results' });
            updateSummary();
            updateBulkBar();
            renderCards();
        }

        function renderCards() {
            var container = document.getElementById('cardView');
            if (!container) return;
            container.innerHTML = '';
            currentFiltered.forEach(function(r, i) {
                var days = getRequestAge(r.requestedAt);
                var ageCls = ageClass(days);
                var card = document.createElement('div');
                card.className = 'request-card';
                card.setAttribute('role', 'button');
                card.setAttribute('tabindex', '0');
                card.addEventListener('click', function() { openModal(i); });
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModal(i); }
                });
                card.innerHTML =
                    '<div class="card-header">' +
                        '<span class="card-code">' + r.courseCode + ' (' + (r.classType === 'L' ? 'Lecture' : 'Tutorial') + ')</span>' +
                        '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>' +
                    '</div>' +
                    '<div class="card-body">' +
                        '<strong>' + r.courseName + '</strong><br>' +
                        dayAbbr(r.classDay) + ', ' + formatDate(r.classDate) + '<br>' +
                        to12h(r.timeStart) + ' – ' + to12h(r.timeEnd) + ' · ' + r.venue +
                    '</div>' +
                    '<div class="card-footer">' +
                        '<span class="' + ageCls + ' card-age">' + relativeTime(days) + '</span>' +
                        '<span>' + r.cohorts.join(', ') + '</span>' +
                    '</div>';
                container.appendChild(card);
            });
        }

        function highlightSelectedRows() {
            document.querySelectorAll('#tableBody tr').forEach(function(row) {
                var id = parseInt(row.dataset.id);
                if (selectedIds.has(id)) {
                    row.classList.add('row-selected');
                } else {
                    row.classList.remove('row-selected');
                }
            });
        }

        function updateBulkBar() {
            var bar = document.getElementById('bulkActionBar');
            var countEl = document.getElementById('bulkCount');
            if (selectedIds.size > 0) {
                bar.classList.add('visible');
                countEl.textContent = selectedIds.size + ' selected';
            } else {
                bar.classList.remove('visible');
            }
        }

        function quickCancel(id) {
            var r = mockRequests.find(function(r) { return r.id === id; });
            if (!r) return;
            pendingCancelId = id;
            document.getElementById('cancelConfirmBody').innerHTML =
                '<p style="font-size:14px;color:var(--color-on-surface);line-height:1.5;margin-bottom:12px">Are you sure you want to cancel this replacement request? This action cannot be undone.</p>' +
                '<div style="background:var(--color-surface-variant);border-radius:8px;padding:12px;font-size:13px;line-height:1.6">' +
                '<strong>' + r.courseCode + '</strong> — ' + r.courseName + '<br>' +
                'Class: ' + r.classDay + ', ' + formatDate(r.classDate) + '<br>' +
                'Status: <span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>' +
                '</div>';
            document.getElementById('cancelConfirmOverlay').style.display = 'flex';
        }

        var pendingCancelId = null;
        document.getElementById('confirmCancelAction').addEventListener('click', function() {
            if (pendingCancelId !== null) {
                var idx = mockRequests.findIndex(function(r) { return r.id === pendingCancelId; });
                if (idx !== -1) {
                    var removed = mockRequests.splice(idx, 1)[0];
                    selectedIds.delete(pendingCancelId);
                    pendingCancelId = null;
                    closeCancelConfirm();
                    closeModal();
                    renderTable();
                    showToast('Request #' + removed.id + ' cancelled.', function() {
                        mockRequests.splice(idx, 0, removed);
                        renderTable();
                    });
                }
            }
        });

        function batchCancelSelected() {
            var count = selectedIds.size;
            if (count === 0) return;
            var rows = [];
            mockRequests.forEach(function(r) {
                if (selectedIds.has(r.id)) {
                    rows.push(r);
                }
            });
            var listHtml = rows.map(function(r) {
                return '<div style="padding:8px 0;border-bottom:1px solid var(--color-outline);font-size:13px;line-height:1.5">' +
                    '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">' +
                    '<strong>' + r.courseCode + '</strong> — ' + r.courseName +
                    ' <span class="badge ' + statusClass(r.status) + '" style="font-size:10px;padding:2px 6px">' + r.status + '</span></div>' +
                    '<div style="color:var(--color-on-surface-variant);font-size:12px;margin-top:2px">' +
                    r.classDay + ', ' + formatDate(r.classDate) + ' &middot; ' + r.timeStart + ' – ' + r.timeEnd + ' &middot; ' + r.venue +
                    '</div></div>';
            }).join('');
            document.getElementById('batchCancelBody').innerHTML =
                '<p style="font-size:14px;color:var(--color-on-surface);line-height:1.5;margin-bottom:8px">Cancel ' + count + ' selected request(s)? This action cannot be undone.</p>' +
                '<div style="max-height:200px;overflow-y:auto">' + listHtml + '</div>';
            document.getElementById('batchCancelOverlay').style.display = 'flex';
        }

        function closeBatchCancelConfirm() {
            document.getElementById('batchCancelOverlay').style.display = 'none';
        }

        document.getElementById('confirmBatchCancelAction').addEventListener('click', function() {
            var removed = mockRequests.filter(function(r) { return selectedIds.has(r.id); });
            mockRequests = mockRequests.filter(function(r) { return !selectedIds.has(r.id); });
            var count = removed.length;
            selectedIds.clear();
            closeBatchCancelConfirm();
            renderTable();
            showToast(count + ' request' + (count !== 1 ? 's' : '') + ' cancelled.', function() {
                mockRequests.push.apply(mockRequests, removed);
                renderTable();
            });
        });

        function updateSummary() {
            const total = mockRequests.length;
            const approved = mockRequests.filter(function(r) { return r.status === 'Approved'; }).length;
            const pending = mockRequests.filter(function(r) { return r.status === 'Pending'; }).length;
            const rejected = mockRequests.filter(function(r) { return r.status === 'Rejected'; }).length;
            let hours = 0;
            mockRequests.forEach(function(r) { hours += r.duration || 0; });

            document.getElementById('summaryTotal').textContent = total;
            document.getElementById('summaryHours').textContent = hours;
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

            if (r.status === 'Pending' || r.status === 'Approved' || r.status === 'Rejected') {
                html += '<div class="modal-section-title">Request Timeline</div>';
                html += '<div class="timeline">';
                var submittedTime = formatDateTime(r.requestedAt);
                var reviewTime = r.reviewedAt ? formatDateTime(r.reviewedAt) : null;
                var statusColor = r.status === 'Pending' ? 'var(--color-tertiary)' : r.status === 'Approved' ? 'var(--color-secondary)' : 'var(--color-error)';
                html += '<div class="timeline-item"><div class="timeline-dot-wrap"><div class="timeline-dot" style="background:var(--color-primary)"></div><div class="timeline-line"></div></div><div class="timeline-text"><div class="timeline-label">Request Submitted</div><div class="timeline-time">' + submittedTime + '</div></div></div>';
                html += '<div class="timeline-item"><div class="timeline-dot-wrap"><div class="timeline-dot" style="background:var(--color-outline)"></div><div class="timeline-line"></div></div><div class="timeline-text"><div class="timeline-label">Under Review</div><div class="timeline-time">' + (reviewTime || '—') + '</div></div></div>';
                html += '<div class="timeline-item"><div class="timeline-dot-wrap"><div class="timeline-dot" style="background:' + statusColor + '"></div></div><div class="timeline-text"><div class="timeline-label">' + r.status + '</div><div class="timeline-time">' + (reviewTime || '—') + '</div></div></div>';
                html += '</div>';
            }

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

        function openCancelConfirm() {
            document.getElementById('cancelConfirmOverlay').style.display = 'flex';
        }

        function closeCancelConfirm() {
            document.getElementById('cancelConfirmOverlay').style.display = 'none';
        }

        function clearAllSelections() {
            selectedIds.clear();
            document.querySelectorAll('.row-checkbox').forEach(function(cb) { cb.checked = false; });
            var headerCheck = document.getElementById('headerCheckbox');
            if (headerCheck) headerCheck.checked = false;
            highlightSelectedRows();
            updateBulkBar();
        }

        function highlightFocusedRow(rows) {
            rows.forEach(function(r, i) {
                if (i === focusedRowIndex) {
                    r.classList.add('row-focused');
                    r.scrollIntoView({ block: 'nearest' });
                } else {
                    r.classList.remove('row-focused');
                }
            });
        }

        function clearFocusedRow(rows) {
            rows.forEach(function(r) { r.classList.remove('row-focused'); });
        }

        function weekFilterChanged() {
            pageState.currentPage = 1;
            saveFilters();
            renderTable();
            updateWeekArrowState();
        }


        document.addEventListener('DOMContentLoaded', function() {
            var weekSel = document.getElementById('weekFilter');
            weekSel.innerHTML = '<option value="all">All Weeks</option>';
            var isMobile = window.innerWidth <= 768;
            weekRanges.forEach(function(w) {
                var opt = document.createElement('option');
                opt.value = w.value;
                opt.textContent = isMobile ? w.labelShort : w.label;
                weekSel.appendChild(opt);
            });

            document.getElementById('semesterChip').textContent = MockData.semester.chipText;

            renderTable();
            updateWeekArrowState();
            initWeekKeyboardShortcuts();

            document.getElementById('searchInput').addEventListener('input', function() {
                pageState.currentPage = 1;
                saveFilters();
                renderTable();
            });
            document.getElementById('statusFilter').addEventListener('change', function() {
                pageState.currentPage = 1;
                saveFilters();
                renderTable();
            });
            document.getElementById('hideCompleted').addEventListener('change', function() {
                pageState.currentPage = 1;
                saveFilters();
                renderTable();
            });
            document.getElementById('clearFilters').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('weekFilter').value = 'all';
                document.getElementById('hideCompleted').checked = true;
                localStorage.removeItem('mrh-filters');
                pageState.currentPage = 1;
                renderTable();
                updateWeekArrowState();
            });

            restoreFilters();

            initRpp({
                selectId: 'rowsPerPage',
                storageKey: 'rpp-page-size',
                defaultVal: 10,
                onChange: function(size) {
                    rowsPerPage = size;
                    pageState.currentPage = 1;
                    renderTable();
                }
            });

            document.getElementById('modalOverlay').addEventListener('click', function(e) {
                closeOnOverlayClick(e, closeModal);
            });
            document.getElementById('cancelConfirmOverlay').addEventListener('click', function(e) {
                if (e.target === this) closeCancelConfirm();
            });
            document.getElementById('batchCancelOverlay').addEventListener('click', function(e) {
                if (e.target === this) closeBatchCancelConfirm();
            });
            closeOnEsc(closeModal);

            /* ── F7: Keyboard Shortcuts ── */
            document.addEventListener('keydown', function(e) {
                if (document.getElementById('modalOverlay').classList.contains('show')) {
                    if (e.key === 'Escape') closeModal();
                    return;
                }
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') return;

                var rows = document.querySelectorAll('#tableBody tr');
                if (rows.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    focusedRowIndex = Math.min(focusedRowIndex + 1, rows.length - 1);
                    highlightFocusedRow(rows);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    focusedRowIndex = Math.max(focusedRowIndex - 1, 0);
                    highlightFocusedRow(rows);
                } else if (e.key === 'Enter' && focusedRowIndex >= 0) {
                    e.preventDefault();
                    var badge = rows[focusedRowIndex].querySelector('.badge');
                    if (badge) badge.click();
                } else if (e.key === 'Escape') {
                    focusedRowIndex = -1;
                    clearFocusedRow(rows);
                }
            });

            /* ── Deep Link: ?id=N opens modal ── */
            var urlParams = new URLSearchParams(window.location.search);
            var deepLinkId = parseInt(urlParams.get('id'));
            if (deepLinkId) {
                var idx = currentFiltered.findIndex(function(r) { return r.id === deepLinkId; });
                if (idx !== -1) {
                    setTimeout(function() { openModal(idx); }, 100);
                }
            }
        });
@endsection
