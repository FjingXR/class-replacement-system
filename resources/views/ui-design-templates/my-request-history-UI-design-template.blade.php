@extends('layouts.ui-template', ['activeNav' => 'replacement-history', 'pageKey' => 'myRequestHistory'])

@section('title', 'My Request History — Class Replacement System')

@section('page-styles')

        /* ───── Column Widths ───── */
        .col-original, .col-replacement { white-space: normal; }

        /* th widths force columns to fill the container; td inherits from th via table-layout: auto */
        .timetable th.col-requested-at { width: 150px; }
        .timetable th.col-code { width: 220px; }
        .timetable th.col-original { width: 200px; }
        .timetable th.col-replacement { width: 200px; }
        .timetable th.col-venue { width: 110px; }
        .timetable th.col-students { width: 80px; }
        .timetable th.col-cohort { width: 140px; }
        .timetable th.col-status { width: 120px; }
        .timetable th.col-actions { width: 100px; }

        .col-replacement .cell-class-block .class-time {
            font-weight: 600;
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

        /* ───── Summary Card Colors (approved/rejected/total accents in theme.css) ───── */




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
            border-radius: var(--radius-sm);
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
            color: var(--color-on-error);
        }
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
        .btn-bulk-clear:hover {
            background: var(--color-surface-variant);
        }

        /* ───── F3: Request Age Indicator (base .request-age styles in theme.css) ───── */

        /* ───── F4: Quick Actions in Rows ───── */
        .btn-inline-cancel {
            padding: 4px 10px;
            border-radius: var(--radius-sm);
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
            color: var(--color-on-error);
        }

        /* ───── F7: Keyboard Shortcuts ───── */
        .row-focused { outline: 2px solid var(--color-primary); outline-offset: -2px; }

        /* ───── Empty State CTA ───── */
        .empty-cta {
            margin-top: 16px;
            padding: 10px 24px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-success);
            color: var(--color-on-success);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: filter 0.15s;
        }
        .empty-cta:hover {
            filter: brightness(1.08);
        }

        /* ───── Responsive Card View (base .request-card styles in theme.css) ───── */

        @media (max-width: 768px) {
            .grid-wrapper, .pagination-bar, .sort-hint, .filter-chips { display: none !important; }
            .card-view { display: block; }
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'My Request History', 'description' => 'View and monitor all replacement requests submitted during the current semester.'])

        @include('partials.ui-guide-block', [
            'guideTitle' => 'How to use this page',
            'guideItems' => [
                '<strong>Sort</strong> — click any column header to sort ascending/descending',
                '<strong>Search</strong> — type in the search box to filter by course code, name, or keywords',
                '<strong>Filter</strong> — use the status dropdown to view requests by approval status',
                '<strong>Hover headers</strong> — hover a column name to see what it means',
                '<strong>Cancel request</strong> — tick the checkbox on pending requests then use "Cancel Selected"',
                '<strong>View details</strong> — click the details icon on any row to see the full request',
                '<strong>Request age</strong> — colour indicates how long ago it was submitted: <span style="color:var(--color-primary)">● ≤1 day</span> <span style="color:var(--color-tertiary)">● 2–3 days</span> <span style="color:var(--color-error)">● 4+ days</span>',
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

        <div class="filter-chips" id="filterChips"></div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table', ['wrapperId' => 'gridWrapper', 'tableClass' => 'timetable data-table'])

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
                ['class' => 'card-total', 'valueId' => 'summaryTotal', 'label' => 'Total Requests',
                    'description' => 'Replacement requests <strong>you submitted</strong> that match your current filters.'],
                ['class' => 'card-hours', 'valueId' => 'summaryHours', 'label' => 'Replacement Hours',
                    'description' => 'Total <strong>replacement class hours</strong> across your filtered requests.'],
                ['class' => 'card-approved', 'valueId' => 'summaryApproved', 'label' => 'Approved',
                    'description' => 'Your requests that have been <strong>approved</strong> and are ready to proceed.'],
                ['class' => 'card-pending', 'valueId' => 'summaryPending', 'label' => 'Pending',
                    'description' => 'Your requests still <strong>waiting for approval</strong> or a volunteer.'],
                ['class' => 'card-rejected', 'valueId' => 'summaryRejected', 'label' => 'Rejected',
                    'description' => 'Your requests that were <strong>declined</strong> and need an alternative arrangement.'],
            ]
        ])

        <!-- ─── Empty State ─── -->
        @include('partials.ui-empty-state', ['title' => "You haven't submitted any replacement requests for this semester.", 'text' => 'Submit a replacement request for any conflicted class.', 'ctaLabel' => 'Submit a Replacement Request', 'ctaOnclick' => "window.location.href='/replacement-arrangement?from=my-request-history'"])

    <!-- ═══ View Details Modal ═══ -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="detailsModal">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle">Request Details</span>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <div class="modal-footer-left">
                    <button class="btn-outline" onclick="closeModal()">Close</button>
                </div>
                <div class="modal-footer-right">
                    <button class="btn-danger" id="cancelRequestBtn" style="display:none" onclick="openCancelConfirm()">Cancel Request</button>
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
                <div class="modal-footer-left">
                    <button class="btn-outline" onclick="closeCancelConfirm()">No, Keep It</button>
                </div>
                <div class="modal-footer-right">
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
                <div class="modal-footer-left">
                    <button class="btn-outline" onclick="closeBatchCancelConfirm()">No, Keep Them</button>
                </div>
                <div class="modal-footer-right">
                    <button class="btn-danger" id="confirmBatchCancelAction">Yes, Cancel All</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
        initHeaderTooltips();
        let mockRequests = MockData.requests;

        const pageState = { currentPage: 1 };
        let rowsPerPage = parseInt(localStorage.getItem('mrh-rows-per-page')) || 10;
        let sortState = { field: 'requestedAt', dir: 'desc' };
        let currentFiltered = [];
        let bulk = new BulkSelection({ barId: 'bulkActionBar', countId: 'bulkCount', checkboxSelector: '.row-checkbox', headerCheckboxId: 'headerCheckbox' });
        let searchDebounce = null;
        let focusedRowIndex = -1;

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

        function renderFilterChips() {
            var container = document.getElementById('filterChips');
            var chips = [];
            var status = document.getElementById('statusFilter').value;
            var search = document.getElementById('searchInput').value.trim();
            var week = document.getElementById('weekFilter').value;
            var excludeCompleted = document.getElementById('hideCompleted').checked;

            if (status !== 'all') {
                chips.push('<span class="filter-chip">Status: ' + status + '<button class="filter-chip-remove" onclick="document.getElementById(\'statusFilter\').value=\'all\';pageState.currentPage=1;saveFilters();renderTable()" title="Remove">&times;</button></span>');
            }
            if (week !== 'all') {
                var weekLabel = document.getElementById('weekFilter').selectedOptions[0] ? document.getElementById('weekFilter').selectedOptions[0].textContent : week;
                chips.push('<span class="filter-chip">Week: ' + weekLabel + '<button class="filter-chip-remove" onclick="document.getElementById(\'weekFilter\').value=\'all\';pageState.currentPage=1;saveFilters();renderTable()" title="Remove">&times;</button></span>');
            }
            if (search) {
                chips.push('<span class="filter-chip">Search: "' + search + '"<button class="filter-chip-remove" onclick="document.getElementById(\'searchInput\').value=\'\';pageState.currentPage=1;saveFilters();renderTable()" title="Remove">&times;</button></span>');
            }
            if (excludeCompleted) {
                chips.push('<span class="filter-chip">Exclude Completed<button class="filter-chip-remove" onclick="document.getElementById(\'hideCompleted\').checked=false;pageState.currentPage=1;saveFilters();renderTable()" title="Remove">&times;</button></span>');
            }
            container.innerHTML = chips.length > 0 ? '<span class="filter-chips-label">Active Filters:</span>' + chips.join('') : '';
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
                        bulk.add(id);
                    } else {
                        bulk.delete(id);
                    }
                });
                highlightSelectedRows();
            });
            thCheck.appendChild(headerCheck);
            tr.appendChild(thCheck);

            const columns = [
                { label: 'Requested At', cls: 'col-requested-at', sortable: true, field: 'requestedAt', tip: 'When the replacement was requested. Age colour: green ≤1 day, amber 2–3 days, red 4+ days' },
                { label: 'Course Code & Name', cls: 'col-code', sortable: true, field: 'courseCode', tip: 'Course affected by the conflict' },
                { label: 'Original Class', cls: 'col-original', sortable: true, field: 'classDate', tip: 'Original class the request refers to — its state varies (still upcoming, replaced, cancelled, holiday, etc.)' },
                { label: 'Requested Replacement', cls: 'col-replacement', sortable: false, tip: 'Proposed new date and time' },
                { label: 'Requested Venue', cls: 'col-venue', sortable: false, tip: 'Venue requested for the replacement' },
                { label: 'Students', cls: 'col-students', sortable: false, tip: 'Number of enrolled students' },
                { label: 'Cohort(s)', cls: 'col-cohort', sortable: false, tip: 'Affected student cohorts' },
                { label: 'Status', cls: 'col-status', sortable: false, tip: 'Current approval status' },
                { label: 'Quick Cancel', cls: 'col-actions', sortable: false, tip: 'Cancel a pending request' },
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

            updateResultCount({ elId: 'resultCount', data: currentFiltered, total: mockRequests.length, label: 'results' });
            updateSummary();
            bulk.updateBar();

            if (isFullyEmpty) {
                document.getElementById('emptyState').style.display = 'flex';
                document.getElementById('emptyTitle').textContent = "You haven't submitted any replacement requests for this semester.";
                document.getElementById('emptyText').textContent = 'Submit a replacement request for any conflicted class.';
                document.getElementById('emptyCta').style.display = 'inline-block';
                document.getElementById('gridWrapper').style.display = 'none';
                document.getElementById('paginationBar').style.display = 'none';
                document.getElementById('summaryBar').style.display = 'none';
            } else if (isFilteredEmpty) {
                document.getElementById('emptyState').style.display = 'flex';
                document.getElementById('emptyTitle').textContent = 'No replacement requests match your search or filter criteria.';
                document.getElementById('emptyText').textContent = 'Try adjusting your filters.';
                document.getElementById('emptyCta').style.display = 'none';
                document.getElementById('gridWrapper').style.display = 'none';
                document.getElementById('paginationBar').style.display = 'none';
                document.getElementById('summaryBar').style.display = 'none';
            } else {
                document.getElementById('emptyState').style.display = 'none';
                document.getElementById('gridWrapper').style.display = '';
                document.getElementById('paginationBar').style.display = 'flex';
                document.getElementById('summaryBar').style.display = 'grid';

                pageData.forEach(function(r, i) {
                    const row = document.createElement('tr');
                    row.dataset.id = r.id;
                    row.dataset.pending = r.status === 'Pending' ? 'true' : 'false';
                    if (bulk.has(r.id)) row.classList.add('row-selected');

                    const globalIndex = offset + i;
                    const isPending = r.status === 'Pending';
                    const badgeHtml = '<span class="badge ' + statusClass(r.status) + '" onclick="openModalById(' + r.id + ')">' + r.status + '</span>';

                    const checkTd = document.createElement('td');
                    checkTd.className = 'col-checkbox';
                    const rowCheck = document.createElement('input');
                    rowCheck.type = 'checkbox';
                    rowCheck.className = 'bulk-checkbox row-checkbox';
                    rowCheck.dataset.id = r.id;
                    rowCheck.disabled = !isPending;
                    if (bulk.has(r.id)) rowCheck.checked = true;
                    rowCheck.addEventListener('change', function() {
                        var id = parseInt(this.dataset.id);
                        if (this.checked) {
                            bulk.add(id);
                        } else {
                            bulk.delete(id);
                        }
                        highlightSelectedRows();
                    });
                    checkTd.appendChild(rowCheck);
                    row.appendChild(checkTd);

                    var cells = [
                        { html: formatDateTime(r.requestedAt) + requestAgeHtml(r.requestedAt), cls: 'col-requested-at' },
                        { html: '<span class="cell-code">' + r.courseCode + ' <span class="cell-type">(' + r.classType + ')</span></span><span class="cell-name">' + r.courseName + '</span>', cls: 'col-code' },
                        { html: HtmlBuilder.classBlock(r), cls: 'col-original' },
                        { html: HtmlBuilder.replacementBlock(r, { showVenue: false, colorStatus: false }), cls: 'col-replacement' },
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
                        openModalById(r.id);
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

            renderCards();
            renderFilterChips();
        }

        function renderCards() {
            var container = document.getElementById('cardView');
            if (!container) return;
            container.innerHTML = '';
            currentFiltered.forEach(function(r, i) {
                var card = document.createElement('div');
                card.className = 'request-card';
                card.setAttribute('role', 'button');
                card.setAttribute('tabindex', '0');
                card.addEventListener('click', function() { openModalById(r.id); });
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModalById(r.id); }
                });
                card.innerHTML = HtmlBuilder.myRequestCard(r, {
                    requestAgeHtml: requestAgeHtml
                });
                container.appendChild(card);
            });
        }

        function highlightSelectedRows() {
            document.querySelectorAll('#tableBody tr').forEach(function(row) {
                var id = parseInt(row.dataset.id);
                if (bulk.has(id)) {
                    row.classList.add('row-selected');
                } else {
                    row.classList.remove('row-selected');
                }
            });
        }

        function updateBulkBar() {
            bulk.updateBar();
        }

        function quickCancel(id) {
            var r = mockRequests.find(function(r) { return r.id === id; });
            if (!r) return;
            pendingCancelId = id;
            document.getElementById('cancelConfirmBody').innerHTML =
                '<p class="page-desc" style="color:var(--color-on-surface);line-height:1.5;margin-bottom:12px">Are you sure you want to cancel this replacement request? This action cannot be undone.</p>' +
                '<div style="background:var(--color-surface-variant);border-radius:var(--radius-sm);padding:12px;font-size:13px;line-height:1.6">' +
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
                    bulk.delete(pendingCancelId);
                    pendingCancelId = null;
                    closeCancelConfirm();
                    closeModal();
                    renderTable();
                    toast.show('Request #' + removed.id + ' cancelled.', function() {
                        mockRequests.splice(idx, 0, removed);
                        renderTable();
                    });
                }
            }
        });

        function batchCancelSelected() {
            var count = bulk.size;
            if (count === 0) return;
            var rows = [];
            mockRequests.forEach(function(r) {
                if (bulk.has(r.id)) {
                    rows.push(r);
                }
            });
            var listHtml = rows.map(function(r) {
                return '<div style="padding:8px 0;border-bottom:1px solid var(--color-outline);font-size:13px;line-height:1.5">' +
                    '<div class="kicker">' +
                    '<strong>' + r.courseCode + '</strong> — ' + r.courseName +
                    ' <span class="badge ' + statusClass(r.status) + ' badge-sm">' + r.status + '</span></div>' +
                    '<div style="color:var(--color-on-surface-variant);font-size:12px;margin-top:2px">' +
                    r.classDay + ', ' + formatDate(r.classDate) + ' &middot; ' + r.timeStart + ' – ' + r.timeEnd + ' &middot; ' + r.venue +
                    '</div></div>';
            }).join('');
            document.getElementById('batchCancelBody').innerHTML =
                '<p class="page-desc" style="color:var(--color-on-surface);line-height:1.5;margin-bottom:8px">Cancel ' + count + ' selected request(s)? This action cannot be undone.</p>' +
                '<div style="max-height:200px;overflow-y:auto">' + listHtml + '</div>';
            document.getElementById('batchCancelOverlay').style.display = 'flex';
        }

        function closeBatchCancelConfirm() {
            document.getElementById('batchCancelOverlay').style.display = 'none';
        }

        document.getElementById('confirmBatchCancelAction').addEventListener('click', function() {
            var removed = mockRequests.filter(function(r) { return bulk.has(r.id); });
            mockRequests = mockRequests.filter(function(r) { return !bulk.has(r.id); });
            var count = removed.length;
            bulk.clear();
            closeBatchCancelConfirm();
            renderTable();
            toast.show(count + ' request' + (count !== 1 ? 's' : '') + ' cancelled.', function() {
                mockRequests.push.apply(mockRequests, removed);
                renderTable();
            });
        });

        function updateSummary() {
            const total = currentFiltered.length;
            const approved = currentFiltered.filter(function(r) { return r.status === 'Approved'; }).length;
            const pending = currentFiltered.filter(function(r) { return r.status === 'Pending'; }).length;
            const rejected = currentFiltered.filter(function(r) { return r.status === 'Rejected'; }).length;
            let hours = 0;
            currentFiltered.forEach(function(r) { hours += r.duration || 0; });

            document.getElementById('summaryTotal').textContent = total;
            document.getElementById('summaryHours').textContent = hours;
            document.getElementById('summaryApproved').textContent = approved;
            document.getElementById('summaryPending').textContent = pending;
            document.getElementById('summaryRejected').textContent = rejected;
        }

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

        function openModalById(id) {
            const r = mockRequests.find(x => x.id === id);
            if (!r) return;

            const statusDot = r.status === 'Pending' ? 'dot-warning' : r.status === 'Approved' || r.status === 'Completed' ? 'dot-success' : r.status === 'Rejected' ? 'dot-error' : 'dot-primary';

            // Global timeline (always visible above the tabs); dot colour follows each step's status
            const reviewTime = r.reviewedAt ? formatDateTime(r.reviewedAt) : null;
            const timeline = [
                { label: 'Request Submitted', time: formatDateTime(r.requestedAt), state: 'completed' },
                { label: 'Under Review', time: reviewTime || '—', state: r.status === 'Pending' ? 'active' : 'completed', dot: r.status === 'Pending' ? 'dot-warning' : 'dot-success' },
                { label: r.status, time: reviewTime || '—', state: r.status === 'Pending' ? 'pending' : 'completed', dot: statusDot }
            ];

            // Tab 1: General Info
            const genRows =
                DetailModal.row('Request No.', '#' + r.id, { strong: true }) +
                DetailModal.row('Requested At', formatDateTime(r.requestedAt)) +
                DetailModal.row('Status', '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>') +
                DetailModal.row('Status Description', statusDesc(r.status)) +
                (r.status === 'Rejected' && r.rejectionReason ? DetailModal.row('Rejection Reason', r.rejectionReason, { strong: true }) : '') +
                DetailModal.row('Subject Code', r.courseCode, { strong: true }) +
                DetailModal.row('Subject Name', r.courseName) +
                DetailModal.row('Class Type', r.classType === 'L' ? 'Lecture' : 'Tutorial');
            const genSection = DetailModal.section('General Info', genRows);

            // Tab 2: Original Class Detail — one value per row
            const origSection = DetailModal.section('Original Class',
                DetailModal.row('Cohort(s)', r.cohorts.join(', ')) +
                DetailModal.row('Total Students', cohortBreakdown(r)) +
                DetailModal.row('Original Date', formatDate(r.classDate)) +
                DetailModal.row('Original Day', r.classDay) +
                DetailModal.row('Start Time', to12h(r.timeStart), { strong: true }) +
                DetailModal.row('End Time', to12h(r.timeEnd)) +
                DetailModal.row('Duration', String(r.duration) + ' hours') +
                DetailModal.row('Original Venue', r.venue)
            );

            // Tab 3: Requested Replacement Class
            const repSection = DetailModal.section('Replacement Class',
                DetailModal.row('Replacement Date', r.replacementDate ? formatDate(r.replacementDate) : null) +
                DetailModal.row('Replacement Time', r.replacementTime ? DateHelper.format12hRange(r.replacementTime) : null, { strong: true }) +
                DetailModal.row('Replacement Venue', r.replacementVenue || '—') +
                DetailModal.row('Reviewed By', r.reviewedBy) +
                DetailModal.row('Reviewed At', r.reviewedAt ? formatDateTime(r.reviewedAt) : null)
            );

            DetailModal.render({
                modalId: 'modalOverlay',
                title: 'Request Details',
                subtitle: '#' + r.id + ' · ' + r.courseCode + ' — ' + r.courseName,
                timeline: timeline,
                tabs: [
                    { key: 'general', label: 'General Info', html: genSection },
                    { key: 'original', label: 'Original Class', html: origSection },
                    { key: 'replacement', label: 'Replacement Class', html: repSection }
                ]
            });

            document.getElementById('cancelRequestBtn').style.display = r.status === 'Pending' ? 'inline-block' : 'none';
        }

        function openModal(index) {
            const r = currentFiltered[index];
            if (!r) return;
            openModalById(r.id);
        }

        function closeModal() {
            DetailModal.close();
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
            bulk.clear();   // clears ids + unchecks .row-checkbox + #headerCheckbox
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

        // Week filter change — uses shared weekFilterChanged() from ui-common.js.
        // It reads the select value, resets page, calls renderTable(), updates arrows.



        document.addEventListener('DOMContentLoaded', function() {
            populateWeekSelect('weekFilter', { includeAll: true });

            document.getElementById('semesterChip').textContent = MockData.semester.chipText;

            SkeletonLoader.showSummary();
            SkeletonLoader.with(function() { renderTable(); SkeletonLoader.hideSummary(); }, document.getElementById('tableBody'), 10, 400);
            updateWeekArrowState();
            initWeekKeyboardShortcuts();

            document.getElementById('searchInput').addEventListener('input', function() {
                saveFilters();
                rebuildTable({ render: renderTable });
            });
            document.getElementById('statusFilter').addEventListener('change', function() {
                saveFilters();
                rebuildTable({ render: renderTable });
            });
            document.getElementById('hideCompleted').addEventListener('change', function() {
                saveFilters();
                rebuildTable({ render: renderTable });
            });
            document.getElementById('clearFilters').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('weekFilter').value = 'all';
                document.getElementById('hideCompleted').checked = true;
                localStorage.removeItem('mrh-filters');
                rebuildTable({ render: renderTable, after: updateWeekArrowState });
            });

            restoreFilters();

            initRpp({
                selectId: 'rowsPerPage',
                storageKey: 'rpp-page-size',
                defaultVal: 10,
                onChange: function(size) {
                    rowsPerPage = size;
                    rebuildTable({ render: renderTable });
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
                    var rowId = parseInt(rows[focusedRowIndex].dataset.id);
                    if (rowId) openModalById(rowId);
                } else if (e.key === 'Escape') {
                    focusedRowIndex = -1;
                    clearFocusedRow(rows);
                }
            });

            /* ── Deep Link: ?id=N opens modal ── */
            var urlParams = new URLSearchParams(window.location.search);
            var deepLinkId = parseInt(urlParams.get('id'));
            if (deepLinkId) {
                var tryCount = 0;
                var poll = setInterval(function() {
                    tryCount++;
                    if (mockRequests.find(function(r) { return r.id === deepLinkId; })) {
                        clearInterval(poll);
                        openModalById(deepLinkId);
                    } else if (tryCount > 20) {
                        clearInterval(poll);
                    }
                }, 100);
            }
        });
@endsection
