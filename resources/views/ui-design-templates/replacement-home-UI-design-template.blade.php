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
        .urgency-mid { color: var(--color-warning); font-weight: 600; }
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
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
        }
        .badge-emergency-leave {
            background: var(--color-error);
            color: var(--color-on-error);
        }

        /* ───── Summary Card Colors ───── */
        .summary-card.card-conflict .summary-value { color: var(--color-error); }
        .summary-card.card-venues .summary-value { color: var(--color-primary); }
        .summary-card.card-students .summary-value { color: var(--color-tertiary); }
        .summary-card.card-duration .summary-value { color: var(--color-primary); }
        .summary-card.card-courses .summary-value { color: var(--color-on-primary-container); }

        .btn-replace-now {
            padding: 8px 20px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-primary);
            color: var(--color-on-primary);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
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

        @include('partials.ui-guide-block', [
            'guideTitle' => 'How to use this page',
            'guideItems' => [
                '<strong>Sort</strong> — click any column header to sort ascending/descending',
                '<strong>Search</strong> — type in the search box to filter by course code or name',
                '<strong>Hover headers</strong> — hover a column name to see what it means',
                '<strong>Start arranging</strong> — click the Action button on a row to begin the replacement process',
                '<strong>Week filter</strong> — use the week navigator to view conflicts for a specific week',
            ]
        ])

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
                {{--<button id="kbShortcutsBtn" class="btn-icon" onclick="showKeyboardShortcuts()" title="Keyboard Shortcuts" style="margin-left:auto; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border:1px solid var(--color-outline); border-radius:8px; color:var(--color-on-surface-variant); background:var(--color-surface); cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><path d="M6 8h.001"/><path d="M10 8h.001"/><path d="M14 8h.001"/><path d="M18 8h.001"/><path d="M8 12h.001"/><path d="M12 12h.001"/><path d="M16 12h.001"/><path d="M7 16h10"/></svg>
                </button>--}}
            </div>
        </div>

        <div class="sort-hint">Click <strong>Course Code &amp; Name</strong> or <strong>Original Class</strong> to sort</div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table', ['tableClass' => 'timetable data-table'])

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
                ['class' => 'card-conflict', 'valueId' => 'summaryConflicted', 'label' => 'Total Conflicted',
                    'description' => 'Classes in the selected period that <strong>need a replacement</strong> arrangement.'],
                ['class' => 'card-venues', 'valueId' => 'summaryVenues', 'label' => 'Venues Affected',
                    'description' => 'Number of <strong>unique venues</strong> involved in the conflicted classes.'],
                ['class' => 'card-students', 'valueId' => 'summaryStudents', 'label' => 'Students Affected',
                    'description' => 'Total <strong>students impacted</strong> by the scheduling conflicts.'],
                ['class' => 'card-duration', 'valueId' => 'summaryDuration', 'label' => 'Duration Hours',
                    'description' => 'Total <strong>hours of class time</strong> that need to be rescheduled.'],
                ['class' => 'card-courses', 'valueId' => 'summaryCourses', 'label' => 'Distinct Courses',
                    'description' => 'Number of <strong>different courses</strong> affected by the conflicts.'],
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
                    <div class="modal-field"><span class="modal-field-label">Focus search</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:var(--radius-xs);font-size:12px;background:var(--color-surface-variant)">/</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Clear filters</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:var(--radius-xs);font-size:12px;background:var(--color-surface-variant)">Esc</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Next page</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:var(--radius-xs);font-size:12px;background:var(--color-surface-variant)">→</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Previous page</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:var(--radius-xs);font-size:12px;background:var(--color-surface-variant)">←</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Open quick view</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:var(--radius-xs);font-size:12px;background:var(--color-surface-variant)">Enter</code></span></div>
                    <div class="modal-field"><span class="modal-field-label">Show shortcuts</span><span class="modal-field-value"><code style="padding:2px 6px;border:1px solid var(--color-outline);border-radius:var(--radius-xs);font-size:12px;background:var(--color-surface-variant)">?</code></span></div>
                    <p class="hint-text" style="margin-top:12px;text-align:center">Shortcuts only work when no input field is focused.</p>
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
        <div class="modal-overlay" id="quickViewModal" onclick="if(event.target===this)hideQuickView()">
            <div class="modal" style="max-width:500px">
                <div class="modal-header">
                    <span class="modal-title" id="qvTitle">Replacement Details</span>
                    <button class="modal-close" onclick="hideQuickView()">✕</button>
                </div>
                <div class="modal-body" id="qvBody"></div>
                <div class="modal-footer">
                    <button class="btn-close-modal" onclick="hideQuickView()">Close</button>
                    <button class="btn-action" id="qvArrangeBtn" onclick="qvArrange()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Arrange Replacement
                    </button>
                </div>
            </div>
        </div>

@endsection

@section('page-scripts')
        initHeaderTooltips();
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
            var wn = getWeekNumber(c.date);
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
                const matchesWeek = weekVal === 'all' || String(getWeekNumber(c.date)) === weekVal;
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
                { label: '#', cls: 'col-no', sortable: false, tip: 'Row number' },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'code', tip: 'Course affected by the conflict' },
                { label: 'Original Class', cls: 'col-original', sortable: true, field: 'date', tip: 'Original class session with a conflict' },
                { label: 'Days Left', cls: 'col-urgency', sortable: false, tip: 'Days remaining before the original class' },
                { label: 'Venue', cls: 'col-venue', sortable: false, tip: 'Assigned venue for the class' },
                { label: 'Students', cls: 'col-students', sortable: false, tip: 'Number of enrolled students' },
                { label: 'Cohort(s)', cls: 'col-cohort', sortable: false, tip: 'Affected student cohorts' },
                { label: 'Conflict Reason', cls: 'col-reason', sortable: false, tip: 'Why the scheduling conflict exists' },
                { label: 'Action', cls: 'col-action', sortable: false, tip: 'Start arranging a replacement' },
            ];
            columns.forEach(function(col) {
                tr.appendChild(makeSortableHeader(col, sortState, function() {
                    pageState.currentPage = 1;
                    buildTable();
                }));
            });
            head.appendChild(tr);

            if (pageData.length === 0) {
                document.getElementById('emptyState').style.display = 'flex';
            } else {
                document.getElementById('emptyState').style.display = 'none';
                pageData.forEach(function(c, i) {
                    const row = document.createElement('tr');
                    var cells = HtmlBuilder.replacementHomeRow(c, {
                        index: String(offset + i + 1),
                        daysLeft: daysLeft,
                        urgencyClass: urgencyClass,
                        formatClassBlock: formatClassBlock,
                        badgeClass: badgeClass
                    });
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
                var card = document.createElement('div');
                card.className = 'replacement-card';
                card.setAttribute('role', 'button');
                card.setAttribute('tabindex', '0');
                card.addEventListener('click', function() { goToReplacementWith(c.code, c.date, c.duration); });
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); goToReplacementWith(c.code, c.date, c.duration); }
                });
                card.innerHTML = HtmlBuilder.replacementHomeCard(c, {
                    daysLeft: daysLeft,
                    urgencyClass: urgencyClass,
                    badgeClass: badgeClass
                });
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
            populateWeekSelect('weekFilter', { includeAll: true });
        }

        function goToReplacementWith(code, date, duration) {
            let url = '/replacement-arrangement?code=' + encodeURIComponent(code) + '&date=' + encodeURIComponent(date);
            if (duration !== undefined && duration !== null && !isNaN(duration)) {
                url += '&duration=' + duration;
            }
            url += '&from=replacement-home';
            window.location.href = url;
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

            var daysLeftVal = daysLeft(c.date);
            var urgencyCls = daysLeftVal <= 3 ? 'urgency-urgent' : daysLeftVal <= 7 ? 'urgency-warning' : 'urgency-normal';
            var wn = getWeekNumber(c.date);
            var weekTag = wn ? ' (Week ' + wn + ')' : '';
            var typeLabel = c.type === 'L' ? 'Lecture' : 'Tutorial';

            DetailModal.render({
                modalId: 'quickViewModal',
                title: 'Replacement Details',
                subtitle: c.code + ' · ' + c.name + ' (' + typeLabel + ')',
                body: DetailModal.section('Replacement Details',
                    DetailModal.row('Status', '<span class="urgency-badge ' + urgencyCls + '">' + daysLeftVal + ' days left</span>') +
                    DetailModal.row('Status Description', daysLeftVal <= 3 ? 'Urgent — arrange a replacement soon' : daysLeftVal <= 7 ? 'Approaching — plan a replacement' : 'Within normal lead time') +
                    DetailModal.row('Conflict Reason', '<span class="badge ' + badgeClass(c.conflictReason) + '">' + c.conflictReason + '</span>') +
                    DetailModal.row('Subject Code', c.code, { strong: true }) +
                    DetailModal.row('Subject Name', c.name) +
                    DetailModal.row('Class Type', typeLabel) +
                    DetailModal.row('Week', 'Week ' + (wn || '-')) +
                    DetailModal.row('Day', c.day) +
                    DetailModal.row('Date', formatDate(c.date) + weekTag) +
                    DetailModal.row('Start Time', to12h(c.timeStart)) +
                    DetailModal.row('End Time', to12h(c.timeEnd)) +
                    DetailModal.row('Duration', c.duration + ' hr' + (c.duration > 1 ? 's' : '')) +
                    DetailModal.row('Venue', c.venue) +
                    DetailModal.row('Students', String(c.totalStudents)) +
                    DetailModal.row('Cohort(s)', c.cohorts.join(', '))
                )
            });
        }

        function qvArrange() {
            if (qvCurrent) {
                var c = qvCurrent.code;
                var d = qvCurrent.date;
                var dur = qvCurrent.duration;
                hideQuickView();
                goToReplacementWith(c, d, dur);
            }
        }

        function hideQuickView() {
            qvCurrent = null;
            DetailModal.close();
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
            SkeletonLoader.showSummary();
            SkeletonLoader.with(function() { buildTable(); SkeletonLoader.hideSummary(); }, document.getElementById('tableBody'), 10, 400);
            updateWeekArrowState();
            initWeekKeyboardShortcuts();
            initRpp({
                selectId: 'rppSelect',
                storageKey: 'rpp-page-size',
                defaultVal: 10,
                onChange: function(size) {
                    state.rpp = size;
                    rebuildTable({ render: buildTable });
                }
            });
            document.getElementById('searchInput').addEventListener('input', function() {
                rebuildTable({ render: buildTable });
            });
            document.getElementById('weekFilter').addEventListener('change', function() {
                rebuildTable({ render: buildTable, after: updateWeekArrowState });
            });
        });
@endsection
