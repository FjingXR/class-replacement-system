@extends('layouts.ui-template', ['activeNav' => 'replacement-arrangement', 'pageKey' => 'replacementHome'])

@section('title', 'Replacement Arrangement — Class Replacement System')

@section('page-styles')

        /* ───── Column Widths ───── */
        .col-no { width: 50px; }
        .col-code { width: 200px; }
        .col-original { width: 200px; }
        .col-original { white-space: normal; }
        .col-urgency { width: 100px; }
        .col-venue { width: 70px; }
        .col-students { width: 80px; }
        .col-cohort { width: 130px; }
        .col-reason { width: 140px; vertical-align: middle; }
        .col-action { width: 150px; }

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

        /* ───── Urgency ───── */
        .urgency-high { color: var(--color-error); font-weight: 700; }
        .urgency-mid { color: var(--color-secondary); font-weight: 600; }
        .urgency-low { color: var(--color-on-surface-variant); font-weight: 500; }

        /* ───── Reason Badges ───── */
        .badge-holiday {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }
        .badge-annual-leave {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
        }
        .badge-medical-leave {
            background: var(--color-tertiary-container);
            color: var(--color-on-tertiary-container);
        }
        .badge-official-event {
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
        }
        .badge-emergency-leave {
            background: var(--color-error);
            color: white;
        }

        /* ───── Summary Card Colors ───── */
        .summary-card.card-conflicted .summary-value { color: var(--color-error); }
        .summary-card.card-venues .summary-value { color: var(--color-primary); }
        .summary-card.card-students .summary-value { color: var(--color-tertiary); }
        .summary-card.card-duration .summary-value { color: var(--color-secondary); }
        .summary-card.card-courses .summary-value { color: var(--color-on-primary-container); }

        .btn-replace-now {
            padding: 8px 20px;
            border-radius: 8px;
            border: none;
            background: var(--color-secondary);
            color: var(--color-on-secondary);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: filter 0.15s;
        }
        .btn-replace-now:hover {
            filter: brightness(1.08);
        }

        /* ───── Rows Per Page ───── */
        .rows-per-page {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-end;
            padding: 8px 16px;
            border-top: 1px solid var(--color-outline-variant);
            background: var(--color-surface);
        }
        .rpp-label {
            font-size: 13px;
            color: var(--color-on-surface-variant);
            white-space: nowrap;
        }
        .rpp-select {
            padding: 4px 8px;
            border: 1px solid var(--color-outline);
            border-radius: 6px;
            font-size: 13px;
            color: var(--color-on-surface);
            background: var(--color-surface);
            cursor: pointer;
        }
        .rpp-select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px var(--color-primary-container);
        }

        /* ───── Responsive Card View ───── */
        .card-view { display: none; }
        .replacement-card {
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background 0.15s, box-shadow 0.15s;
        }
        .replacement-card:hover {
            background: var(--color-surface-variant);
            box-shadow: var(--shadow-sm);
        }
        .replacement-card:active {
            transform: scale(0.99);
        }
        .rc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .rc-code {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-on-surface);
        }
        .rc-body {
            font-size: 12px;
            color: var(--color-on-surface-variant);
            line-height: 1.6;
        }
        .rc-body strong {
            color: var(--color-on-surface);
            font-weight: 600;
        }
        .rc-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid var(--color-outline);
            font-size: 11px;
            color: var(--color-on-surface-variant);
        }
        .rc-footer .badge {
            font-size: 11px;
            padding: 3px 8px;
        }

        /* ───── My Filters Panel ───── */
        .my-filters-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 16px;
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-outline-variant);
        }
        .my-filters-title {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-on-surface-variant);
        }
        .my-filters-actions {
            display: flex;
            gap: 6px;
        }
        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border: 1px solid var(--color-outline);
            border-radius: 6px;
            font-size: 12px;
            color: var(--color-on-surface);
            background: transparent;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-outline:hover {
            background: var(--color-surface-hover);
        }

        /* ───── Quick View Modal ───── */
        .quick-view-field {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid var(--color-outline-variant);
        }
        .quick-view-field:last-child { border-bottom: none; }
        .quick-view-label {
            font-size: 12px;
            color: var(--color-on-surface-variant);
            font-weight: 600;
        }
        .quick-view-value {
            font-size: 13px;
            color: var(--color-on-surface);
            text-align: right;
        }
        .quick-view-empty {
            font-size: 13px;
            color: var(--color-on-surface-variant);
            font-style: italic;
        }
        .quick-view-header {
            text-align: center;
        }

        /* ───── Table Row Hover ───── */
        .table-body tr {
            cursor: pointer;
        }
        .table-body tr:hover {
            background: var(--color-surface-hover);
        }

        @media (max-width: 768px) {
            .grid-wrapper, .pagination-bar, .rows-per-page, .sort-hint { display: none !important; }
            .card-view { display: block; }
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        <div class="page-header">
            <h1 class="page-title">Replacement Arrangement</h1>
            <span class="semester-chip" id="semesterChip"></span>
            <p class="page-desc">The following classes require replacement arrangements. Select a class to submit a replacement request.</p>
        </div>

        <!-- ─── Toolbar ─── -->
        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" id="searchInput" placeholder="Search by course code or name...">
                </div>
                <div class="week-nav">
                    <button class="week-arrow" onclick="prevWeekFilter()" aria-label="Previous week">&#8249;</button>
                    <select class="week-select" id="weekFilter" onchange="weekFilterChanged(this.value)"></select>
                    <button class="week-arrow" onclick="nextWeekFilter()" aria-label="Next week">&#8250;</button>
                </div>
            </div>
            <div class="toolbar-right">
                <span class="result-count" id="resultCount">Showing 14 of 14 classes</span>
                <button class="btn-icon" onclick="showKeyboardShortcuts()" title="Keyboard Shortcuts" style="margin-left:auto; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid var(--color-outline); border-radius:8px; color:var(--color-on-surface-variant); background:var(--color-surface); cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><path d="M6 8h.001"/><path d="M10 8h.001"/><path d="M14 8h.001"/><path d="M18 8h.001"/><path d="M8 12h.001"/><path d="M12 12h.001"/><path d="M16 12h.001"/><path d="M7 16h10"/></svg>
                </button>
            </div>
        </div>

        <!-- My Saved Filters -->
        <div class="my-filters-panel">
            <div class="my-filters-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                My Filters
            </div>
            <div class="my-filters-actions">
                <button class="btn-outline" onclick="saveFilter()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save
                </button>
                <button class="btn-outline" onclick="applyFilter()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Apply
                </button>
                <button class="btn-outline" onclick="deleteFilter()" style="color:var(--color-error)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    Delete
                </button>
            </div>
        </div>

        <div class="sort-hint">Click <strong>Course Code &amp; Name</strong> or <strong>Original Class</strong> to sort</div>

        <!-- ─── Grid Wrapper ─── -->
        <div class="grid-wrapper">
            <div class="grid-scroll">
                <table class="timetable" id="timetable">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- ─── Card View (mobile) ─── -->
        <div class="card-view" id="cardView"></div>

        <!-- ─── Pagination ─── -->
        <div class="pagination-bar" id="paginationBar">
            <span class="pagination-info" id="paginationInfo">Showing 1-10 of 14</span>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>

        <!-- ─── Rows Per Page ─── -->
        <div class="rows-per-page">
            <span class="rpp-label">Rows Per Page:</span>
            <select class="rpp-select" id="rppSelect" onchange="setRpp(this.value)">
                <option value="5" selected>5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <!-- ─── Summary Dashboard ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-conflicted', 'valueId' => 'summaryConflicted', 'label' => 'Total Conflicted'],
                ['class' => 'card-venues', 'valueId' => 'summaryVenues', 'label' => 'Venues Affected'],
                ['class' => 'card-students', 'valueId' => 'summaryStudents', 'label' => 'Students Affected'],
                ['class' => 'card-duration', 'valueId' => 'summaryDuration', 'label' => 'Duration Hours'],
                ['class' => 'card-courses', 'valueId' => 'summaryCourses', 'label' => 'Distinct Courses'],
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
            <h3 class="empty-title">No classes currently require replacement arrangements.</h3>
            <p class="empty-text">Try adjusting your search or filter criteria.</p>
        </div>

        <!-- Keyboard Shortcuts Modal -->
        <div id="keyboardModal" style="display:none; position:fixed; inset:0; z-index:3000; background:rgba(0,0,0,0.45); backdrop-filter:blur(4px); align-items:center; justify-content:center;">
            <div class="modal-box" style="width:420px; max-width:90vw; max-height:80vh; overflow-y:auto;">
                <div class="modal-header">
                    <div class="modal-title">Keyboard Shortcuts</div>
                    <button class="modal-close-btn" onclick="hideKeyboardShortcuts()" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div style="border:1px solid var(--color-outline-variant); border-radius:8px; overflow:hidden; font-size:13px;">
                        <div style="display:grid; grid-template-columns:1fr 1fr; background:var(--color-surface-variant); font-weight:600; color:var(--color-on-surface); padding:8px 14px; border-bottom:1px solid var(--color-outline-variant);">
                            <span>Action</span><span style="text-align:right">Shortcut</span>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; padding:7px 14px; border-bottom:1px solid var(--color-outline-variant); color:var(--color-on-surface);">
                            <span>Focus search bar</span><span style="text-align:right"><code style="padding:2px 6px; border:1px solid var(--color-outline); border-radius:4px; font-size:11px; background:var(--color-surface);">/</code></span>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; padding:7px 14px; border-bottom:1px solid var(--color-outline-variant); color:var(--color-on-surface);">
                            <span>Clear all filters</span><span style="text-align:right"><code style="padding:2px 6px; border:1px solid var(--color-outline); border-radius:4px; font-size:11px; background:var(--color-surface);">Esc</code></span>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; padding:7px 14px; border-bottom:1px solid var(--color-outline-variant); color:var(--color-on-surface);">
                            <span>Next page</span><span style="text-align:right"><code style="padding:2px 6px; border:1px solid var(--color-outline); border-radius:4px; font-size:11px; background:var(--color-surface);">→</code></span>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; padding:7px 14px; border-bottom:1px solid var(--color-outline-variant); color:var(--color-on-surface);">
                            <span>Previous page</span><span style="text-align:right"><code style="padding:2px 6px; border:1px solid var(--color-outline); border-radius:4px; font-size:11px; background:var(--color-surface);">←</code></span>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; padding:7px 14px; border-bottom:1px solid var(--color-outline-variant); color:var(--color-on-surface);">
                            <span>Open quick view</span><span style="text-align:right"><code style="padding:2px 6px; border:1px solid var(--color-outline); border-radius:4px; font-size:11px; background:var(--color-surface);">Enter</code></span>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; padding:7px 14px; border-bottom:1px solid var(--color-outline-variant); color:var(--color-on-surface);">
                            <span>Show keyboard shortcuts</span><span style="text-align:right"><code style="padding:2px 6px; border:1px solid var(--color-outline); border-radius:4px; font-size:11px; background:var(--color-surface);">?</code></span>
                        </div>
                    </div>
                    <div style="margin-top:10px; font-size:11px; color:var(--color-on-surface-variant); text-align:center;">
                        Keyboard shortcuts only work when no input field is focused.
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-primary" onclick="hideKeyboardShortcuts()" style="padding:6px 16px;">OK</button>
                </div>
            </div>
        </div>

        <!-- Quick View Modal -->
        <div id="quickViewModal" style="display:none; position:fixed; inset:0; z-index:3000; background:rgba(0,0,0,0.45); backdrop-filter:blur(4px); align-items:center; justify-content:center;" onclick="if(event.target===this)hideQuickView()">
            <div class="modal-box" style="width:500px; max-width:90vw; max-height:80vh; overflow-y:auto;">
                <div class="modal-header">
                    <div class="modal-title" id="qvTitle">Replacement Details</div>
                    <button class="modal-close-btn" onclick="hideQuickView()" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="modal-body" id="qvBody" style="font-size:13px; color:var(--color-on-surface);"></div>
                <div class="modal-footer" style="display:flex; justify-content:flex-end;">
                    <button class="btn-primary" onclick="hideQuickView()" style="padding:6px 16px;">OK</button>
                </div>
            </div>
        </div>

@endsection

@section('page-scripts')
        const conflictedClasses = MockData.conflictedClasses;

        function badgeClass(reason) {
            const map = {
                'Public Holiday': 'badge-holiday',
                'Annual Leave': 'badge-annual-leave',
                'Medical Leave': 'badge-medical-leave',
                'Official Event': 'badge-official-event',
                'Emergency Leave': 'badge-emergency-leave'
            };
            return map[reason] || '';
        }

        function dayAbbr(day) {
            return day.substring(0, 3);
        }

        function formatClassBlock(c) {
            var d = dayAbbr(c.day);
            var dateStr = formatDate(c.date);
            var wn = computeWeek(c.date);
            var weekTag = wn ? ' (Week ' + wn + ')' : '';
            var timeStr = to12h(c.timeStart) + ' to ' + to12h(c.timeEnd);
            var hrs = c.duration + ' hr' + (c.duration > 1 ? 's' : '');
            return '<div class="cell-class-block"><span class="class-day-date">' + d + ', ' + dateStr + weekTag + '</span><br><span class="class-time">' + timeStr + '</span> <span class="class-duration">(' + hrs + ')</span></div>';
        }

        function daysLeft(iso) {
            const now = new Date();
            now.setHours(0, 0, 0, 0);
            const target = new Date(iso + 'T00:00:00');
            return Math.ceil((target - now) / (1000 * 60 * 60 * 24));
        }

        function urgencyClass(days) {
            if (days <= 7) return 'urgency-high';
            if (days <= 30) return 'urgency-mid';
            return 'urgency-low';
        }

        function computeWeek(isoDate) {
            const semesterStart = new Date(MockData.semester.startDate);
            const date = new Date(isoDate + 'T00:00:00');
            const diff = Math.floor((date - semesterStart) / (1000 * 60 * 60 * 24));
            return Math.floor(diff / 7) + 1;
        }

        function weekRangeLabel(weekNum) {
            const start = new Date(MockData.semester.startDate);
            start.setDate(start.getDate() + (weekNum - 1) * 7);
            const end = new Date(start);
            end.setDate(end.getDate() + 6);
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const fmtFull = d => String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
            const fmtShort = d => String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()];
            if (window.innerWidth <= 768) {
                return 'Week ' + weekNum + ' \u00B7 ' + fmtShort(start) + ' ~ ' + fmtShort(end);
            }
            return 'Week ' + weekNum + ' \u00B7 ' + fmtFull(start) + ' ~ ' + fmtFull(end);
        }

        var state = { rpp: 5 };
        const pageState = { currentPage: 1 };
        let sortState = { field: 'date', dir: 'asc' };
        let currentFiltered = [];

        function buildTable() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const reason = 'all';
            const weekVal = document.getElementById('weekFilter').value;

            let filtered = conflictedClasses.filter(function(c) {
                const matchesSearch = query === '' ||
                    c.code.toLowerCase().includes(query) ||
                    c.name.toLowerCase().includes(query);
                const matchesReason = reason === 'all' || c.conflictReason === reason;
                const matchesWeek = weekVal === 'all' || String(computeWeek(c.date)) === weekVal;
                return matchesSearch && matchesReason && matchesWeek;
            });

            if (sortState.field) {
                filtered.sort(function(a, b) {
                    var va, vb;
                    if (sortState.field === 'date') {
                        va = a.date;
                        vb = b.date;
                    } else if (sortState.field === 'code') {
                        va = a.code;
                        vb = b.code;
                    }
                    return compareBy(sortState, va, vb);
                });
            }

            currentFiltered = filtered;

            const offset = (pageState.currentPage - 1) * state.rpp;
            const pageData = filtered.slice(offset, offset + state.rpp);

            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            const tr = document.createElement('tr');
            const columns = [
                { label: '#', cls: 'col-no', sortable: false },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'code' },
                { label: 'Original Class', cls: 'col-original', sortable: true, field: 'date' },
                { label: 'Days Left', cls: 'col-urgency', sortable: false },
                { label: 'Venue', cls: 'col-venue', sortable: false },
                { label: 'Students', cls: 'col-students', sortable: false },
                { label: 'Affected Cohort(s)', cls: 'col-cohort', sortable: false },
                { label: 'Conflict Reason', cls: 'col-reason', sortable: false },
                { label: 'Action', cls: 'col-action', sortable: false },
            ];
            columns.forEach(function(col) {
                tr.appendChild(makeSortableHeader(col, sortState, function() {
                    pageState.currentPage = 1;
                    buildTable();
                }));
            });
            head.appendChild(tr);

            if (pageData.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
            } else {
                document.getElementById('emptyState').style.display = 'none';
                pageData.forEach(function(c, i) {
                    const row = document.createElement('tr');
                    var cells = [
                        { html: String(offset + i + 1), cls: 'col-no' },
                        { html: '<span class="cell-code">' + c.code + '</span><span class="cell-name">' + c.name + ' <span style="font-weight:400;font-size:12px;color:var(--color-on-surface-variant)">(' + (c.type === 'L' ? 'L' : 'T') + ')</span></span>', cls: 'col-code' },
                        { html: formatClassBlock(c), cls: 'col-original' },
                        { html: '<span class="' + urgencyClass(daysLeft(c.date)) + '">' + daysLeft(c.date) + ' days</span>', cls: 'col-urgency' },
                        { html: c.venue, cls: 'col-venue' },
                        { html: String(c.totalStudents), cls: 'col-students' },
                        { html: c.cohorts.join('<br>'), cls: 'col-cohort' },
                        { html: '<span class="badge ' + badgeClass(c.conflictReason) + '">' + c.conflictReason + '</span>', cls: 'col-reason' },
                        { html: '<button class="btn-action" onclick="event.stopPropagation(); goToReplacementWith(\'' + c.code + '\',\'' + c.date + '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> Arrange Replacement</button>', cls: 'col-action' },
                    ];
                    cells.forEach(function(cell) {
                        const td = document.createElement('td');
                        td.className = cell.cls;
                        td.innerHTML = cell.html;
                        row.appendChild(td);
                    });
                    (function(row, idx) {
                        row.onclick = function() { quickView(idx); };
                        row.style.cursor = 'pointer';
                    })(row, i);
                    body.appendChild(row);
                });
            }

            paginate({ data: currentFiltered, pageSize: state.rpp, state: pageState, infoId: 'paginationInfo', controlsId: 'paginationControls', render: buildTable });
            updateResultCount({ elId: 'resultCount', data: currentFiltered, total: conflictedClasses.length, label: 'classes' });
            updateSummary();
            renderCards();
        }

        function renderCards() {
            var container = document.getElementById('cardView');
            if (!container) return;
            container.innerHTML = '';
            currentFiltered.forEach(function(c) {
                var days = daysLeft(c.date);
                var card = document.createElement('div');
                card.className = 'replacement-card';
                card.setAttribute('role', 'button');
                card.setAttribute('tabindex', '0');
                card.addEventListener('click', function() { goToReplacementWith(c.code, c.date); });
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); goToReplacementWith(c.code, c.date); }
                });
                card.innerHTML =
                    '<div class="rc-header">' +
                        '<span class="rc-code">' + c.code + ' <span style="font-weight:400;font-size:12px;color:var(--color-on-surface-variant)">(' + (c.type === 'L' ? 'Lecture' : 'Tutorial') + ')</span></span>' +
                        '<span class="badge ' + badgeClass(c.conflictReason) + '">' + c.conflictReason + '</span>' +
                    '</div>' +
                    '<div class="rc-body">' +
                        '<strong>' + c.name + '</strong><br>' +
                        c.day + ', ' + formatDate(c.date) + ' · Week ' + computeWeek(c.date) + '<br>' +
                        to12h(c.timeStart) + ' – ' + to12h(c.timeEnd) + ' · ' + c.venue +
                    '</div>' +
                    '<div class="rc-footer">' +
                        '<span class="' + urgencyClass(days) + '">' + days + ' days left</span>' +
                        '<span>' + c.cohorts.join(', ') + '</span>' +
                    '</div>';
                container.appendChild(card);
            });
        }

        function updateSummary() {
            const total = conflictedClasses.length;
            const filtered = currentFiltered;

            const venues = new Set(filtered.map(function(c) { return c.venue; }));
            const students = filtered.reduce(function(sum, c) { return sum + c.totalStudents; }, 0);
            const duration = filtered.reduce(function(sum, c) { return sum + c.duration; }, 0);
            const courses = new Set(filtered.map(function(c) { return c.code; }));

            document.getElementById('summaryConflicted').textContent = total;
            document.getElementById('summaryVenues').textContent = venues.size;
            document.getElementById('summaryStudents').textContent = students;
            document.getElementById('summaryDuration').textContent = duration;
            document.getElementById('summaryCourses').textContent = courses.size;

            const show = filtered.length > 0;
            document.getElementById('summaryBar').style.display = show ? 'grid' : 'none';
        }

        function populateWeekDropdown() {
            const weeks = new Set(conflictedClasses.map(function(c) { return computeWeek(c.date); }));
            const sel = document.getElementById('weekFilter');
            sel.innerHTML = '<option value="all">All Weeks</option>';
            Array.from(weeks).sort(function(a, b) { return a - b; }).forEach(function(w) {
                const opt = document.createElement('option');
                opt.value = String(w);
                opt.textContent = weekRangeLabel(w);
                sel.appendChild(opt);
            });
        }

        function goToReplacementWith(code, date) {
            window.location.href = '/replacement-arrangement?code=' + encodeURIComponent(code) + '&date=' + encodeURIComponent(date);
        }

        function weekFilterChanged() {
            pageState.currentPage = 1;
            buildTable();
            updateWeekArrowState();
        }

        function prevWeekFilter() {
            const sel = document.getElementById('weekFilter');
            if (sel.selectedIndex > 0) {
                sel.selectedIndex--;
                sel.dispatchEvent(new Event('change'));
            }
        }

        function nextWeekFilter() {
            const sel = document.getElementById('weekFilter');
            if (sel.selectedIndex < sel.options.length - 1) {
                sel.selectedIndex++;
                sel.dispatchEvent(new Event('change'));
            }
        }

        function setRpp(n) {
            state.rpp = parseInt(n) || 5;
            pageState.currentPage = 1;
            buildTable();
        }

        function updateWeekArrowState() {
            const sel = document.getElementById('weekFilter');
            updateWeekArrows(sel.selectedIndex <= 0, sel.selectedIndex >= sel.options.length - 1);
        }

        function saveFilter() {
            var prefs = {
                search: document.getElementById('searchInput').value,
                weekFilter: document.getElementById('weekFilter').value,
                sortBy: sortState.field,
                sortAsc: sortState.dir === 'asc',
            };
            localStorage.setItem('replacementHomeFilterPrefs', JSON.stringify(prefs));
            showToast('Filters saved', 'success');
        }

        function applyFilter() {
            var raw = localStorage.getItem('replacementHomeFilterPrefs');
            if (!raw) { showToast('No saved filter found', 'warning'); return; }
            var prefs = JSON.parse(raw);
            document.getElementById('searchInput').value = prefs.search || '';
            document.getElementById('weekFilter').value = prefs.weekFilter || 'all';
            sortState.field = prefs.sortBy || 'date';
            sortState.dir = prefs.sortAsc !== undefined ? (prefs.sortAsc ? 'asc' : 'desc') : 'asc';
            pageState.currentPage = 1;
            buildTable();
            showToast('Filters applied', 'success');
        }

        function deleteFilter() {
            localStorage.removeItem('replacementHomeFilterPrefs');
            showToast('Saved filter deleted', 'info');
        }

        function loadFilterPrefs() {
            var raw = localStorage.getItem('replacementHomeFilterPrefs');
            if (!raw) return;
            var prefs = JSON.parse(raw);
            if (prefs.search) document.getElementById('searchInput').value = prefs.search;
            if (prefs.weekFilter) document.getElementById('weekFilter').value = prefs.weekFilter;
            if (prefs.sortBy) sortState.field = prefs.sortBy;
            if (prefs.sortAsc !== undefined) sortState.dir = prefs.sortAsc ? 'asc' : 'desc';
        }

        function showKeyboardShortcuts() {
            var el = document.getElementById('keyboardModal');
            if (el) { el.style.display = 'flex'; }
        }

        function hideKeyboardShortcuts() {
            var el = document.getElementById('keyboardModal');
            if (el) { el.style.display = 'none'; }
        }

        function quickView(idx) {
            var c = currentFiltered[idx];
            if (!c) return;

            document.getElementById('qvTitle').textContent = c.name + ' (' + c.type + ')';

            var daysLeftVal = daysLeft(c.date);
            var urgencyCls = daysLeftVal <= 3 ? 'urgent' : daysLeftVal <= 7 ? 'warning' : 'safe';
            var wn = computeWeek(c.date);
            var weekTag = wn ? ' (Week ' + wn + ')' : '';

            var fields = [
                { label: 'Course Code', value: c.code },
                { label: 'Course Name', value: c.name },
                { label: 'Type', value: c.type === 'L' ? 'Lecture' : 'Tutorial' },
                { label: 'Week', value: 'Week ' + (wn || '-') },
                { label: 'Day', value: c.day },
                { label: 'Date', value: formatDate(c.date) + weekTag },
                { label: 'Time', value: to12h(c.timeStart) + ' to ' + to12h(c.timeEnd) + ' (' + c.duration + ' hr' + (c.duration > 1 ? 's' : '') + ')' },
                { label: 'Venue', value: c.venue },
                { label: 'Students', value: String(c.totalStudents) },
                { label: 'Affected Cohort(s)', value: c.cohorts.join(', ') },
                { label: 'Days Left', value: daysLeftVal + ' days' },
                { label: 'Conflict Reason', value: c.conflictReason },
            ];

            var html = fields.map(function(f) {
                return '<div class="quick-view-field"><span class="quick-view-label">' + f.label + '</span><span class="quick-view-value">' + (f.value || '<span class="quick-view-empty">—</span>') + '</span></div>';
            }).join('');

            document.getElementById('qvBody').innerHTML = html;
            document.getElementById('quickViewModal').style.display = 'flex';
        }

        function hideQuickView() {
            document.getElementById('quickViewModal').style.display = 'none';
        }

        function clearAll() {
            document.getElementById('searchInput').value = '';
            document.getElementById('weekFilter').value = 'all';
            sortState.field = 'date';
            sortState.dir = 'asc';
            pageState.currentPage = 1;
            buildTable();
            updateWeekArrowState();
        }

        function goNextPage() {
            var totalPages = Math.ceil(currentFiltered.length / state.rpp);
            if (pageState.currentPage < totalPages) {
                pageState.currentPage++;
                buildTable();
            }
        }

        function goPrevPage() {
            if (pageState.currentPage > 1) {
                pageState.currentPage--;
                buildTable();
            }
        }

        document.addEventListener('keydown', function(e) {
            var tag = (e.target || {}).tagName || '';
            var isInput = (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT');

            if (e.key === '/' && !isInput) {
                e.preventDefault();
                var s = document.getElementById('searchInput');
                if (s) s.focus();
                return;
            }
            if (e.key === 'Escape') {
                var qvEl = document.getElementById('quickViewModal');
                if (qvEl && qvEl.style.display === 'flex') {
                    hideQuickView();
                    return;
                }
                var el = document.getElementById('keyboardModal');
                if (el && el.style.display === 'flex') {
                    hideKeyboardShortcuts();
                    return;
                }
                clearAll();
                return;
            }
            if (e.key === '?' && !isInput) {
                e.preventDefault();
                showKeyboardShortcuts();
                return;
            }
            if (isInput) return;
            if (e.key === 'ArrowRight') { goNextPage(); }
            if (e.key === 'ArrowLeft') { goPrevPage(); }
            if (e.key === 'Enter') {
                var focused = document.querySelector('.table-body tr:focus, .table-body tr:focus-within');
                if (focused) {
                    var rows = Array.from(document.querySelectorAll('.table-body tr'));
                    var idx = rows.indexOf(focused);
                    if (idx >= 0) { quickView(idx); }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            populateWeekDropdown();
            loadFilterPrefs();
            buildTable();
            updateWeekArrowState();
            initWeekKeyboardShortcuts();
            document.getElementById('searchInput').addEventListener('input', function() {
                pageState.currentPage = 1;
                buildTable();
            });
            document.getElementById('weekFilter').addEventListener('change', weekFilterChanged);
        });
@endsection
