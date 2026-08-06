@extends('layouts.ui-template', ['activeNav' => 'replacement-arrangement', 'pageKey' => 'replacementHome'])

@section('title', 'Replacement Arrangement — Class Replacement System')

@section('page-styles')

        /* ───── Column Widths ───── */
        .col-original { width: 200px; }
        .col-original { white-space: normal; }
        .col-urgency { width: 100px; }
        .col-venue { width: 70px; }
        .col-cohort { width: 130px; }
        .col-reason { width: 140px; vertical-align: middle; }
        .col-action { width: 150px; }

        .grid-scroll .timetable { min-width: 1120px; }

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

        /* ───── Responsive Card View ───── */
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

        @media (max-width: 768px) {
            .grid-wrapper, .pagination-bar, .sort-hint { display: none !important; }
            .card-view { display: block; }
            #kbShortcutsBtn { display: none !important; }
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'Replacement Arrangement', 'description' => 'The following classes require replacement arrangements. Select a class to submit a replacement request.'])

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
                @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeekFilter()', 'nextOnclick' => 'nextWeekFilter()', 'selectId' => 'weekFilter', 'selectOnclick' => 'weekFilterChanged(this.value)', 'showTodayBtn' => false])
            </div>
            <div class="toolbar-right">
                <span class="result-count" id="resultCount">Showing 14 of 14 classes</span>
                <button id="kbShortcutsBtn" class="btn-icon" onclick="showKeyboardShortcuts()" title="Keyboard Shortcuts" style="margin-left:auto; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid var(--color-outline); border-radius:8px; color:var(--color-on-surface-variant); background:var(--color-surface); cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><path d="M6 8h.001"/><path d="M10 8h.001"/><path d="M14 8h.001"/><path d="M18 8h.001"/><path d="M8 12h.001"/><path d="M12 12h.001"/><path d="M16 12h.001"/><path d="M7 16h10"/></svg>
                </button>
            </div>
        </div>

        <div class="sort-hint">Click <strong>Course Code &amp; Name</strong> or <strong>Original Class</strong> to sort</div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table')

        <!-- ─── Card View (mobile) ─── -->
        <div class="card-view" id="cardView"></div>

        <!-- ─── Pagination ─── -->
        <div class="pagination-bar" id="paginationBar">
            @include('partials.ui-rpp', ['id' => 'rppSelect', 'default' => 10, 'options' => [10, 25, 50, 'all']])
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
        @include('partials.ui-empty-state', ['title' => 'No classes currently require replacement arrangements.', 'text' => 'Try adjusting your search or filter criteria.'])

        <!-- Keyboard Shortcuts Modal -->
        <div class="modal-overlay" id="keyboardModal">
            <div class="modal" style="max-width:420px">
                <div class="modal-header">
                    <h3 class="modal-title">Keyboard Shortcuts</h3>
                    <button class="modal-close" onclick="hideKeyboardShortcuts()">✕</button>
                </div>
                <div class="modal-body">
                    <div class="modal-field"><span class="modal-field-label">Focus search</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:4px;font-size:12px;background:var(--color-surface-variant)">/</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Clear filters</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:4px;font-size:12px;background:var(--color-surface-variant)">Esc</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Next page</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:4px;font-size:12px;background:var(--color-surface-variant)">→</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Previous page</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:4px;font-size:12px;background:var(--color-surface-variant)">←</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Open quick view</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:4px;font-size:12px;background:var(--color-surface-variant)">Enter</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Show shortcuts</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:4px;font-size:12px;background:var(--color-surface-variant)">?</code></span></div>
                    <p style="margin-top:12px;font-size:11px;color:var(--color-on-surface-variant);text-align:center">Shortcuts only work when no input field is focused.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn-close-modal" onclick="hideKeyboardShortcuts()">Close</button>
                </div>
            </div>
        </div>

        <!-- Quick View Modal -->
        <style>
            #quickViewModal .modal-footer {
                justify-content: space-between !important;
                width: 100% !important;
            }
        </style>
        <div class="modal-overlay" id="quickViewModal">
            <div class="modal" style="max-width:500px">
                <div class="modal-header">
                    <h3 class="modal-title" id="qvTitle">Replacement Details</h3>
                    <button class="modal-close" onclick="hideQuickView()">✕</button>
                </div>
                <div class="modal-body" id="qvBody"></div>
                <div class="modal-footer">
                    <button class="btn-action" id="qvArrangeBtn" onclick="qvArrange()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Arrange Replacement
                    </button>
                    <button class="btn-close-modal" onclick="hideQuickView()">Close</button>
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

        var state = { rpp: 10 };
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


        function showKeyboardShortcuts() {
            var el = document.getElementById('keyboardModal');
            if (el) { el.classList.add('show'); }
        }

        function hideKeyboardShortcuts() {
            var el = document.getElementById('keyboardModal');
            if (el) { el.classList.remove('show'); }
        }

        var qvCurrent = null;

        function quickView(idx) {
            var c = currentFiltered[idx];
            if (!c) return;

            qvCurrent = c;
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
                return '<div class="modal-field"><span class="modal-field-label">' + f.label + '</span><span class="modal-field-value">' + (f.value || '<span style="color:var(--color-on-surface-variant);font-style:italic">—</span>') + '</span></div>';
            }).join('');

            document.getElementById('qvBody').innerHTML = html;
            document.getElementById('quickViewModal').classList.add('show');
        }

        function qvArrange() {
            if (qvCurrent) {
                var c = qvCurrent.code;
                var d = qvCurrent.date;
                hideQuickView();
                goToReplacementWith(c, d);
            }
        }

        function hideQuickView() {
            qvCurrent = null;
            document.getElementById('quickViewModal').classList.remove('show');
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
                if (qvEl && qvEl.classList.contains('show')) {
                    hideQuickView();
                    return;
                }
                var el = document.getElementById('keyboardModal');
                if (el && el.classList.contains('show')) {
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
            buildTable();
            updateWeekArrowState();
            initWeekKeyboardShortcuts();
            initRpp({
                selectId: 'rppSelect',
                storageKey: 'rpp-page-size',
                defaultVal: 10,
                onChange: function(size) {
                    state.rpp = size;
                    pageState.currentPage = 1;
                    buildTable();
                }
            });
            document.getElementById('searchInput').addEventListener('input', function() {
                pageState.currentPage = 1;
                buildTable();
            });
            document.getElementById('weekFilter').addEventListener('change', weekFilterChanged);
        });
@endsection
