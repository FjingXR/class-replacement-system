@extends('layouts.ui-template', ['activeNav' => 'replacement-arrangement'])

@section('title', 'Replacement Arrangement — Class Replacement System')

@section('page-styles')

        /* ───── Column Widths ───── */
        .col-no { width: 50px; }
        .col-code { width: 200px; }
        .col-type { width: 90px; }
        .col-week { width: 80px; }
        .col-date { width: 110px; }
        .col-day { width: 80px; }
        .col-urgency { width: 100px; }
        .col-time { width: 130px; }
        .col-duration { width: 70px; }
        .col-venue { width: 70px; }
        .col-students { width: 80px; }
        .col-cohort { width: 130px; }
        .col-reason { width: 140px; }
        .col-action { width: 150px; }

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
            justify-content: flex-end;
            gap: 8px;
        }
        .btn-close-modal {
            padding: 8px 20px;
            border-radius: 8px;
            border: 1px solid var(--color-outline-strong);
            background: transparent;
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-close-modal:hover {
            background: var(--color-surface-variant);
        }
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

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        <div class="page-header">
            <h1 class="page-title">Replacement Arrangement</h1>
            <span class="semester-chip">202605 Semester · 15-Jun-2026 ~ 20-Sep-2026</span>
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
                <select class="filter-select" id="weekFilter">
                    <option value="all">All Weeks</option>
                </select>
            </div>
            <div class="toolbar-right">
                <span class="result-count" id="resultCount">Showing 14 of 14 classes</span>
            </div>
        </div>

        <div class="sort-hint">Click <strong>Date</strong> or <strong>Course Code &amp; Name</strong> to sort</div>

        <!-- ─── Grid Wrapper ─── -->
        <div class="grid-wrapper">
            <div class="grid-scroll">
                <table class="timetable" id="timetable">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- ─── Pagination ─── -->
        <div class="pagination-bar" id="paginationBar">
            <span class="pagination-info" id="paginationInfo">Showing 1-10 of 14</span>
            <div class="pagination-controls" id="paginationControls"></div>
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

@endsection

@section('page-scripts')
        const conflictedClasses = [
            { id: 1, code: 'BMIT5555', name: 'Software Engineering', type: 'L', date: '2026-09-04', day: 'Thursday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B110', totalStudents: 35, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Public Holiday' },
            { id: 2, code: 'BMIT5555', name: 'Software Engineering', type: 'T', date: '2026-09-04', day: 'Thursday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B111', totalStudents: 28, cohorts: ['DFT2 (S1)'], conflictReason: 'Public Holiday' },
            { id: 3, code: 'BMIT6767', name: 'Object-Oriented Programming', type: 'L', date: '2026-09-10', day: 'Thursday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B103', totalStudents: 24, cohorts: ['DFT2 (S1)'], conflictReason: 'Annual Leave' },
            { id: 4, code: 'BMIT5678', name: 'Database Systems', type: 'T', date: '2026-09-10', day: 'Thursday', timeStart: '11:00', timeEnd: '13:00', duration: 2, venue: 'B105', totalStudents: 30, cohorts: ['DSF2 (S1)', 'DFT2 (S1)'], conflictReason: 'Medical Leave' },
            { id: 5, code: 'BMIT9012', name: 'Computer Networks', type: 'L', date: '2026-09-11', day: 'Friday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 22, cohorts: ['DFT2 (S1)'], conflictReason: 'Official Event' },
            { id: 6, code: 'BMIT3456', name: 'Artificial Intelligence', type: 'T', date: '2026-09-12', day: 'Saturday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B103', totalStudents: 18, cohorts: ['DSF2 (S1)'], conflictReason: 'Annual Leave' },
            { id: 7, code: 'BMIT7890', name: 'Project Management', type: 'L', date: '2026-09-15', day: 'Tuesday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 20, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Medical Leave' },
            { id: 8, code: 'BMIT9999', name: 'Machine Learning', type: 'T', date: '2026-09-15', day: 'Tuesday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B202', totalStudents: 15, cohorts: ['DFT2 (S1)'], conflictReason: 'Emergency Leave' },
            { id: 9, code: 'BMIT1234', name: 'Data Structures', type: 'L', date: '2026-09-18', day: 'Friday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B104', totalStudents: 30, cohorts: ['DSF2 (S1)'], conflictReason: 'Public Holiday' },
            { id: 10, code: 'BMIT1234', name: 'Data Structures', type: 'T', date: '2026-09-18', day: 'Friday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B201', totalStudents: 25, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Public Holiday' },
            { id: 11, code: 'BMIT4567', name: 'Web Development', type: 'L', date: '2026-09-20', day: 'Sunday', timeStart: '09:00', timeEnd: '11:00', duration: 2, venue: 'B110', totalStudents: 32, cohorts: ['DFT2 (S1)'], conflictReason: 'Official Event' },
            { id: 12, code: 'BMIT8888', name: 'Cloud Computing', type: 'T', date: '2026-09-22', day: 'Tuesday', timeStart: '10:00', timeEnd: '12:00', duration: 2, venue: 'B105', totalStudents: 20, cohorts: ['DSF2 (S1)'], conflictReason: 'Annual Leave' },
            { id: 13, code: 'BMIT7777', name: 'Cybersecurity', type: 'L', date: '2026-09-25', day: 'Friday', timeStart: '08:00', timeEnd: '10:00', duration: 2, venue: 'B106', totalStudents: 18, cohorts: ['DFT2 (S1)', 'DSF2 (S1)'], conflictReason: 'Medical Leave' },
            { id: 14, code: 'BMIT3333', name: 'Embedded Systems', type: 'T', date: '2026-09-28', day: 'Monday', timeStart: '14:00', timeEnd: '16:00', duration: 2, venue: 'B202', totalStudents: 12, cohorts: ['DSF2 (S1)'], conflictReason: 'Emergency Leave' },
        ];

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
            const semesterStart = new Date('2026-08-31');
            const date = new Date(isoDate + 'T00:00:00');
            const diff = Math.floor((date - semesterStart) / (1000 * 60 * 60 * 24));
            return Math.floor(diff / 7) + 1;
        }

        const pageState = { currentPage: 1 };
        const pageSize = 10;
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

            const offset = (pageState.currentPage - 1) * pageSize;
            const pageData = filtered.slice(offset, offset + pageSize);

            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            const tr = document.createElement('tr');
            const columns = [
                { label: '#', cls: 'col-no', sortable: false },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'code' },
                { label: 'Type', cls: 'col-type', sortable: false },
                { label: 'Week', cls: 'col-week', sortable: false },
                { label: 'Date', cls: 'col-date', sortable: true, field: 'date' },
                { label: 'Day', cls: 'col-day', sortable: false },
                { label: 'Days Left', cls: 'col-urgency', sortable: false },
                { label: 'Time', cls: 'col-time', sortable: false },
                { label: 'Hrs', cls: 'col-duration', sortable: false },
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
                        { html: '<span class="cell-code">' + c.code + '</span><span class="cell-name">' + c.name + '</span>', cls: 'col-code' },
                        { html: c.type === 'L' ? 'Lecture' : 'Tutorial', cls: 'col-type' },
                        { html: 'Week ' + computeWeek(c.date), cls: 'col-week' },
                        { html: formatDate(c.date), cls: 'col-date' },
                        { html: c.day, cls: 'col-day' },
                        { html: '<span class="' + urgencyClass(daysLeft(c.date)) + '">' + daysLeft(c.date) + ' days</span>', cls: 'col-urgency' },
                        { html: to12h(c.timeStart) + ' - ' + to12h(c.timeEnd), cls: 'col-time' },
                        { html: String(c.duration) + 'h', cls: 'col-duration' },
                        { html: c.venue, cls: 'col-venue' },
                        { html: String(c.totalStudents), cls: 'col-students' },
                        { html: c.cohorts.join('<br>'), cls: 'col-cohort' },
                        { html: '<span class="badge ' + badgeClass(c.conflictReason) + '">' + c.conflictReason + '</span>', cls: 'col-reason' },
                        { html: '<button class="btn-action" onclick="goToReplacementWith(\'' + c.code + '\',\'' + c.date + '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> Arrange Replacement</button>', cls: 'col-action' },
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

            paginate({ data: currentFiltered, pageSize: pageSize, state: pageState, infoId: 'paginationInfo', controlsId: 'paginationControls', render: buildTable });
            updateResultCount({ elId: 'resultCount', data: currentFiltered, total: conflictedClasses.length, label: 'classes' });
            updateSummary();
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
                opt.textContent = 'Week ' + w;
                sel.appendChild(opt);
            });
        }

        function goToReplacementWith(code, date) {
            window.location.href = '/replacement-arrangement?code=' + encodeURIComponent(code) + '&date=' + encodeURIComponent(date);
        }

        document.addEventListener('DOMContentLoaded', function() {
            populateWeekDropdown();
            buildTable();
            document.getElementById('searchInput').addEventListener('input', function() {
                pageState.currentPage = 1;
                buildTable();
            });
            document.getElementById('weekFilter').addEventListener('change', function() {
                pageState.currentPage = 1;
                buildTable();
            });
        });
@endsection
