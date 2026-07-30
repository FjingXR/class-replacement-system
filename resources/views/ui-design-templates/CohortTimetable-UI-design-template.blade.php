@extends('layouts.ui-template', ['activeNav' => 'cohort-timetables'])

@section('title', 'Cohort Timetable — Class Replacement System')

@section('page-styles')

        /* ───── Semester Bar ───── */
        .semester-bar {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            gap: 8px;
            flex-shrink: 0;
            color: var(--color-on-surface);
            transition: background var(--transition), border-color var(--transition);
        }
        .week-arrow {
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
            font-weight: 600;
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .week-arrow:hover {
            background: var(--color-surface-variant);
        }
        .week-arrow:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        .week-arrow:disabled:hover {
            background: transparent;
        }
        .week-select {
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 28px 4px 10px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%233d5a48' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            min-width: 160px;
        }
        .week-select option {
            background: var(--color-surface);
            color: var(--color-on-surface);
        }
        .session-text {
            flex: 1;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            opacity: 0.85;
            white-space: nowrap;
        }
        html.dark .week-select {
            color-scheme: dark;
        }

        /* ───── Faculty & Cohort Selects ───── */
        .semester-bar select:not(.week-select) {
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            padding: 6px 28px 6px 10px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%233d5a48' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            min-width: 160px;
        }
        .semester-bar select:not(.week-select) option {
            background: var(--color-surface);
            color: var(--color-on-surface);
        }
        html.dark .semester-bar select:not(.week-select) {
            color-scheme: dark;
        }

        /* ───── Time Column ───── */
        .time-col {
            width: 130px;
            min-width: 130px;
            left: 0;
            position: sticky;
            z-index: 15;
            background: var(--color-surface);
            font-weight: 600;
            text-align: center;
        }
        .time-col .day-label {
            display: block;
            font-size: 14px;
            text-align: center;
        }
        .time-col .date-label {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: var(--color-on-surface-variant);
            margin-top: 2px;
            text-align: center;
        }
        .time-col .holiday-label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: var(--color-error);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
            text-align: center;
        }
        .time-col.today {
            background: var(--color-primary-container);
        }
        .time-col.today .day-label {
            color: var(--color-on-primary-container);
        }
        .time-col.today .date-label {
            color: var(--color-on-primary-container);
        }
        .time-col.holiday-col {
            background: var(--color-error-container);
        }
        .time-col.holiday-col .day-label {
            color: var(--color-on-error-container);
        }
        .time-col.holiday-col .date-label {
            color: var(--color-on-error-container);
        }
        .time-col.sunday-col {
            background: var(--color-error-container);
        }
        .time-col.sunday-col .day-label {
            color: var(--color-on-error-container);
        }
        .time-col.sunday-col .date-label {
            color: var(--color-on-error-container);
        }

        /* ───── Hour Header ───── */
        .hour-header {
            padding: 6px 4px;
            font-size: 12px;
            min-width: 80px;
        }
        .hour-header .hour-top {
            display: block;
            font-size: 13px;
            font-weight: 600;
        }
        .hour-header .hour-bottom {
            display: block;
            font-size: 10px;
            font-weight: 400;
            opacity: 0.6;
            margin-top: 1px;
        }

        /* ───── Hour Cells ───── */
        .timetable td.hour-cell {
            padding: 0;
            height: 80px;
            min-width: 80px;
            cursor: default;
        }

        .timetable td.hour-cell.sunday-slot {
            background: var(--color-error-container);
        }
        .timetable td.hour-cell.sunday-slot .cell-empty {
            background: var(--color-error-container);
        }

        .timetable td.hour-cell.holiday-slot {
            background: var(--color-error-container);
        }
        .timetable td.hour-cell.holiday-slot .cell-empty {
            background: var(--color-error-container);
        }

        .event-public-holiday {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }

        /* ───── Event Block ───── */
        .event-block {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: filter 0.15s, box-shadow var(--transition);
            position: relative;
            cursor: pointer;
            padding: 6px 6px;
            box-sizing: border-box;
            gap: 2px;
        }
        .event-block:hover {
            filter: brightness(1.08);
            z-index: 5;
            box-shadow: inset 0 0 0 1.5px var(--color-on-surface-variant);
        }
        .event-block:active {
            transform: scale(0.98);
        }

        .event-normal {
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
        }
        .event-replacement {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
        }
        .event-pending {
            background: var(--color-tertiary-container);
            color: var(--color-on-tertiary-container);
        }

        .sunday-slot .event-block,
        .holiday-slot .event-block {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }

        .event-block .ev-code {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
            text-align: center;
        }
        .event-block .ev-venue {
            font-size: 13px;
            font-weight: 500;
            opacity: 0.75;
            text-align: center;
            line-height: 1.2;
        }
        .event-block .ev-time {
            font-size: 13px;
            font-weight: 400;
            opacity: 0.65;
            text-align: center;
            line-height: 1.2;
        }
        .event-block .ev-note {
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            line-height: 1.2;
            margin-top: 1px;
        }

        .cell-empty {
            background: var(--color-surface);
            height: 100%;
            min-height: 80px;
        }

        .timetable tr:last-child td { border-bottom: none; }

        .today-highlight {
            background: rgba(26,95,180,0.04) !important;
        }

        /* ───── Status Badge Classes ───── */
        .badge-normal {
            background: var(--color-secondary);
            color: var(--color-on-secondary);
        }
        .badge-replacement {
            background: #d4a017;
            color: #fff;
        }
        html.light .badge-replacement {
            background: #b8860b;
            color: #fff;
        }
        .badge-pending {
            background: var(--color-tertiary);
            color: var(--color-on-tertiary);
        }
        .badge-conflict {
            background: var(--color-error);
            color: var(--color-on-error);
        }

        /* ───── Legend Bar ───── */
        .legend-bar {
            height: 50px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            gap: 28px;
            flex-shrink: 0;
            border-top: 1px solid var(--color-outline);
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
        }
        .legend-swatch {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            flex-shrink: 0;
            border: 1px solid var(--color-outline-strong);
        }

        /* ───── Summary Card Colors ───── */
        .summary-card.card-total .summary-value { color: var(--color-secondary); }
        .summary-card.card-replacement .summary-value { color: var(--color-primary); }
        .summary-card.card-pending .summary-value { color: var(--color-tertiary); }
        .summary-card.card-conflict .summary-value { color: var(--color-error); }
        .summary-card.card-total {
            border: 2px solid var(--color-primary);
            background: var(--color-primary-container);
        }

        /* ───── Modal ───── */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 999;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .modal {
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            max-width: 480px; width: 100%;
            animation: modalIn 0.2s ease;
        }
        @keyframes modalIn {
            from { opacity:0; transform:scale(0.95) translateY(10px); }
            to { opacity:1; transform:scale(1) translateY(0); }
        }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px 0;
        }
        .modal-title {
            font-size: 18px; font-weight: 700; color: var(--color-on-surface);
        }
        .modal-status-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 3px 12px;
            border-radius: 20px;
        }
        .modal-status-badge.normal {
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
        }
        .modal-status-badge.replacement {
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
        }
        .modal-status-badge.pending {
            background: var(--color-tertiary-container);
            color: var(--color-on-tertiary-container);
        }
        .modal-status-badge.conflict {
            background: var(--color-error-container);
            color: var(--color-on-error-container);
        }
        .modal-close {
            width: 32px; height: 32px; border-radius: 8px; border: none;
            background: var(--color-surface-variant); color: var(--color-on-surface-variant);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            font-size: 20px; line-height: 1;
            transition: background var(--transition);
        }
        .modal-close:hover { background: var(--color-outline); }
        .modal-body {
            padding: 20px 24px;
        }
        .modal-field {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid var(--color-outline);
        }
        .modal-field:last-child {
            border-bottom: none;
        }
        .modal-field .field-label {
            width: 130px;
            flex-shrink: 0;
            font-size: 12px;
            font-weight: 600;
            color: var(--color-on-surface-variant);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding-top: 2px;
        }
        .modal-field .field-value {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            color: var(--color-on-surface);
            line-height: 1.4;
        }
        .modal-footer {
            display: flex; justify-content: flex-end; align-items: center;
            padding: 0 24px 20px;
        }
        .btn-close-modal {
            padding: 10px 24px;
            border-radius: 10px;
            border: 1px solid var(--color-outline-strong);
            background: var(--color-surface);
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background var(--transition), transform 0.15s;
        }
        .btn-close-modal:hover {
            background: var(--color-surface-variant);
        }
        .btn-close-modal:active {
            transform: scale(0.97);
        }

        /* ───── Empty State (shared from theme.css) ───── */

        /* ───── Search Toolbar ───── */
        .search-wrapper {
            position: relative;
        }
        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: 0.4;
            color: var(--color-on-surface-variant);
        }
        .search-input {
            padding: 8px 12px 8px 36px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 13px;
            width: 420px;
            outline: none;
        }
        .search-input::placeholder {
            color: var(--color-on-surface-variant);
            opacity: 0.5;
        }
        .filter-select {
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--color-secondary-container);
            color: var(--color-on-secondary-container);
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            outline: none;
            cursor: pointer;
        }
        .filter-select option {
            background: var(--color-surface);
            color: var(--color-on-surface);
        }
        html.dark .filter-select {
            color-scheme: dark;
        }

        /* ───── Responsive ───── */
        @media (max-width: 1024px) {
            .grid-scroll { overflow-x: auto; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-left { justify-content: flex-start; }
            .search-input { width: 100%; }
            .filter-select { width: auto; flex: 0 0 auto; }
        }
        @media (max-width: 768px) {
            .app-container { padding: 10px 12px; padding-top: 66px; }
            .top-logo { margin-right: 12px; }
            .nav-items { gap: 2px; }
            .nav-item { padding: 0 8px; font-size: 12px; }
            .user-info { display: none; }
            .toolbar-left { width: 100%; }
            .search-wrapper { width: 100%; }
            .search-input { width: 100%; box-sizing: border-box; }
            .filter-select { width: 100%; }
            .toolbar-right { width: 100%; justify-content: center; }
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        <div class="page-header">
            <h1 class="page-title">Cohort Timetable</h1>
            <p class="page-desc">View the weekly timetable for any cohort across all faculties.</p>
        </div>

        <!-- ─── Semester Bar ─── -->
        <div class="semester-bar">
            <select id="facultySelect" onchange="onFacultyChange()">
                <option value="">Select Faculty</option>
            </select>
            <select id="cohortSelect" onchange="onCohortChange()">
                <option value="">Select Cohort</option>
            </select>
            <button class="week-arrow" onclick="prevWeek()" aria-label="Previous week">&#8249;</button>
            <select class="week-select" id="weekSelect" onchange="selectWeek(this.value)"></select>
            <button class="week-arrow" onclick="nextWeek()" aria-label="Next week">&#8250;</button>
            <span class="session-text">202605 Semester (Monday, 15-Jun-2026 ~ Sunday, 20-Sep-2026)</span>
        </div>

        <!-- ─── Toolbar ─── -->
        <div class="toolbar">
            <div class="toolbar-left">
                <div class="search-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input class="search-input" id="searchInput" oninput="applyFilters()" placeholder="Search by course code, name or lecturer...">
                </div>
                <select class="filter-select" id="statusFilter" onchange="applyFilters()">
                    <option value="all">All Status</option>
                    <option value="normal">Normal</option>
                    <option value="replacement">Replacement</option>
                    <option value="pending">Pending</option>
                    <option value="conflict">Conflict</option>
                </select>
            </div>
            <div class="toolbar-right">
                <span class="result-count" id="resultCount">Showing 0 of 0 events</span>
            </div>
        </div>

        <!-- ─── Grid Wrapper ─── -->
        <div class="grid-wrapper">
            <div class="grid-scroll" id="gridScroll">
                <table class="timetable" id="timetable">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>

        <!-- ─── Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts'],
            ]
        ])

        <!-- ─── Legend Bar ─── -->
        <div class="legend-bar">
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-secondary);"></div>
                Normal Class
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-primary);"></div>
                Replacement
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-tertiary);"></div>
                Pending
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-error);"></div>
                Conflict
            </div>
        </div>

        <!-- ─── Empty State ─── -->
        <div class="empty-state" id="emptyState" style="display:none">
            <svg class="empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <h3 class="empty-title" id="emptyTitle">No classes scheduled</h3>
            <p class="empty-text" id="emptyText">No classes scheduled for this cohort in the selected week.</p>
        </div>

        <!-- ─── Event Modal ─── -->
        <div class="modal-overlay" id="eventModal" style="display:none" onclick="if(event.target===this)closeModal()">
            <div class="modal">
                <div class="modal-header">
                    <span class="modal-title" id="modalTitle">Class Details</span>
                    <span class="modal-status-badge" id="modalStatusBadge">Normal</span>
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="modal-field">
                        <span class="field-label">Course</span>
                        <span class="field-value" id="mdlCourse">—</span>
                    </div>
                    <div class="modal-field">
                        <span class="field-label">Name</span>
                        <span class="field-value" id="mdlName">—</span>
                    </div>
                    <div class="modal-field">
                        <span class="field-label">Lecturer</span>
                        <span class="field-value" id="mdlLecturer">—</span>
                    </div>
                    <div class="modal-field">
                        <span class="field-label">Venue</span>
                        <span class="field-value" id="mdlVenue">—</span>
                    </div>
                    <div class="modal-field">
                        <span class="field-label">Cohort</span>
                        <span class="field-value" id="mdlCohort">—</span>
                    </div>
                    <div class="modal-field">
                        <span class="field-label">Time</span>
                        <span class="field-value" id="mdlTime">—</span>
                    </div>
                    <div class="modal-field">
                        <span class="field-label">Status</span>
                        <span class="field-value"><span class="badge" id="mdlStatusBadge">—</span></span>
                    </div>
                    <div class="modal-field">
                        <span class="field-label">Remarks</span>
                        <span class="field-value" id="mdlRemarks">—</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-close-modal" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>

@endsection

@section('page-scripts')
        const hours = [
            '08:00','08:30','09:00','09:30',
            '10:00','10:30','11:00','11:30',
            '12:00','12:30',
            '13:00','13:30','14:00','14:30',
            '15:00','15:30','16:00','16:30',
            '17:00','17:30','18:00','18:30'
        ];

        function add30min(t) {
            const [h, m] = t.split(':').map(Number);
            const total = h * 60 + m + 30;
            return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
        }

        const dayNames = ['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'];

        /* ════════════════════════════════════════════
           MOCK DATA — Weeks, Faculties, Cohorts, Events
           ════════════════════════════════════════════ */

        const weekData = (function() {
            const start = new Date(2026, 5, 15); // 15-Jun-2026
            const arr = [];
            for (let w = 1; w <= 14; w++) {
                const ms = start.getTime() + (w - 1) * 7 * 86400000;
                const mon = new Date(ms);
                const sun = new Date(ms + 6 * 86400000);
                const fmt = d => `${String(d.getDate()).padStart(2,'0')} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][d.getMonth()]} ${d.getFullYear()}`;
                const days = [];
                for (let d = 0; d < 7; d++) {
                    const dt = new Date(ms + d * 86400000);
                    days.push({
                        abbr: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][d],
                        date: fmt(dt),
                        sunday: d === 6,
                        today: d === 0 && w === 11,
                        holiday: d === 3 && w === 11,
                    });
                }
                arr.push({ label: `Week ${w + 8}`, range: `${fmt(mon)} ~ ${fmt(sun)}`, days });
            }
            return arr;
        })();

        /* ───── Faculty & Cohort Hierarchy ───── */
        const facultyData = [
            {
                id: 'focs',
                name: 'Faculty of Computing and Information Technology (FOCS)',
                cohorts: [
                    { id: 'rsd2s1', name: 'RSD2 (S1) — Bachelor of Computer Science (Soft. Eng.)' },
                    { id: 'rsd3s1g1', name: 'RSD3 (S1) G1 — Bachelor of Computer Science (Soft. Eng.)' },
                    { id: 'rsd3s1g2', name: 'RSD3 (S1) G2 — Bachelor of Computer Science (Soft. Eng.)' },
                    { id: 'dsf2s1', name: 'DSF2 (S1) — Diploma in Computer Science' },
                    { id: 'dft2s1', name: 'DFT2 (S1) — Diploma in Information Technology' },
                ]
            },
            {
                id: 'fol',
                name: 'Faculty of Law (FOL)',
                cohorts: [
                    { id: 'rsdl2s1g1', name: 'RSD2 (S1) G1 — Bachelor of Law' },
                    { id: 'rsdl2s1g2', name: 'RSD2 (S1) G2 — Bachelor of Law' },
                    { id: 'dlf2s1', name: 'DLF2 (S1) — Diploma in Law' },
                    { id: 'dlm2s1', name: 'DLM2 (S1) — Diploma in Legal Management' },
                ]
            },
            {
                id: 'fod',
                name: 'Faculty of Design (FOD)',
                cohorts: [
                    { id: 'rsdds1', name: 'RSD2 (S1) — Bachelor of Design (Multimedia)' },
                    { id: 'rsdd3s1', name: 'RSD3 (S1) — Bachelor of Design (Graphic)' },
                    { id: 'ddm2s1', name: 'DDM2 (S1) — Diploma in Digital Media' },
                    { id: 'dfm2s1', name: 'DFM2 (S1) — Diploma in Fashion Merchandising' },
                ]
            },
            {
                id: 'fcci',
                name: 'Faculty of Creative Industries (FCCI)',
                cohorts: [
                    { id: 'rbu2s1', name: 'RBU2 (S1) — Bachelor of Business (Marketing)' },
                    { id: 'dmc2s1', name: 'DMC2 (S1) — Diploma in Mass Communication' },
                    { id: 'dit2s1', name: 'DIT2 (S1) — Diploma in Interior Design' },
                    { id: 'dcm2s1', name: 'DCM2 (S1) — Diploma in Creative Multimedia' },
                ]
            }
        ];

        /* ───── Mock Events (indexed by week, keyed by cohort id) ───── */
        const allEvents = {};
        const cohorts = facultyData.flatMap(f => f.cohorts);
        cohorts.forEach(c => { allEvents[c.id] = {}; });

        function addEvent(cohortId, weekIdx, event) {
            if (!allEvents[cohortId][weekIdx]) allEvents[cohortId][weekIdx] = [];
            allEvents[cohortId][weekIdx].push(event);
        }

        // ── FOCS / RSD2 (S1) ──
        addEvent('rsd2s1', 0, { di:0, start:4, end:7, code:'BMIT6767', type:'L', venue:'B103', lecturer:'Dr. Christopher Lazarus', status:'normal', name:'Object-Oriented Programming', remarks:'' });
        addEvent('rsd2s1', 0, { di:1, start:0, end:3, code:'BMIT5678', type:'L', venue:'B105', lecturer:'En. Lim Jia Zheng', status:'normal', name:'Database Systems', remarks:'' });
        addEvent('rsd2s1', 0, { di:3, start:6, end:9, code:'BMIT9012', type:'L', venue:'B106', lecturer:'Pn. Surayaini Basri', status:'normal', name:'Computer Networks', remarks:'' });
        addEvent('rsd2s1', 0, { di:4, start:10, end:13, code:'BMIT3456', type:'T', venue:'B103', lecturer:'Dr. Chang Foo Chung', status:'normal', name:'Artificial Intelligence', remarks:'' });

        addEvent('rsd2s1', 1, { di:0, start:4, end:7, code:'BMIT6767', type:'L', venue:'B103', lecturer:'Dr. Christopher Lazarus', status:'normal', name:'Object-Oriented Programming', remarks:'' });
        addEvent('rsd2s1', 1, { di:2, start:0, end:3, code:'BMIT5678', type:'L', venue:'B105', lecturer:'En. Lim Jia Zheng', status:'replacement', name:'Database Systems', remarks:'24-Aug-2026' });
        addEvent('rsd2s1', 1, { di:3, start:6, end:9, code:'BMIT9012', type:'L', venue:'B106', lecturer:'Pn. Surayaini Basri', status:'normal', name:'Computer Networks', remarks:'' });
        addEvent('rsd2s1', 1, { di:5, start:0, end:3, code:'BMIT3456', type:'T', venue:'B103', lecturer:'Dr. Chang Foo Chung', status:'normal', name:'Artificial Intelligence', remarks:'' });

        addEvent('rsd2s1', 2, { di:0, start:4, end:7, code:'BMIT6767', type:'L', venue:'B103', lecturer:'Dr. Christopher Lazarus', status:'normal', name:'Object-Oriented Programming', remarks:'' });
        addEvent('rsd2s1', 2, { di:1, start:0, end:3, code:'BMIT5678', type:'L', venue:'B105', lecturer:'En. Lim Jia Zheng', status:'normal', name:'Database Systems', remarks:'' });
        addEvent('rsd2s1', 2, { di:2, start:10, end:13, code:'BMIT9999', type:'T', venue:'B202', lecturer:'Dr. Tan Ah Meng', status:'pending', name:'Machine Learning', remarks:'', requestedAt:'03 Sep 2026, 10:30 AM', requestedBy:'Dr. Tan Ah Meng' });
        addEvent('rsd2s1', 2, { di:3, start:6, end:9, code:'BMIT9012', type:'L', venue:'B106', lecturer:'Pn. Surayaini Basri', status:'replacement', name:'Computer Networks', remarks:'26-Aug-2026' });
        addEvent('rsd2s1', 2, { di:4, start:10, end:13, code:'BMIT3456', type:'T', venue:'B103', lecturer:'Dr. Chang Foo Chung', status:'normal', name:'Artificial Intelligence', remarks:'' });

        // ── FOCS / DSF2 (S1) ──
        addEvent('dsf2s1', 0, { di:0, start:0, end:3, code:'BMIT1010', type:'L', venue:'B201', lecturer:'Ms. Nurul Aini', status:'normal', name:'Introduction to Computing', remarks:'' });
        addEvent('dsf2s1', 0, { di:2, start:4, end:7, code:'BMIT2020', type:'L', venue:'B202', lecturer:'Mr. Ravi Kumar', status:'normal', name:'Programming Fundamentals', remarks:'' });
        addEvent('dsf2s1', 0, { di:4, start:8, end:11, code:'BMIT3030', type:'T', venue:'B203', lecturer:'Ms. Siti Aminah', status:'normal', name:'Data Structures', remarks:'' });

        addEvent('dsf2s1', 1, { di:0, start:0, end:3, code:'BMIT1010', type:'L', venue:'B201', lecturer:'Ms. Nurul Aini', status:'normal', name:'Introduction to Computing', remarks:'' });
        addEvent('dsf2s1', 1, { di:2, start:4, end:7, code:'BMIT2020', type:'L', venue:'B202', lecturer:'Mr. Ravi Kumar', status:'replacement', name:'Programming Fundamentals', remarks:'26-Aug-2026' });
        addEvent('dsf2s1', 1, { di:4, start:8, end:11, code:'BMIT3030', type:'T', venue:'B203', lecturer:'Ms. Siti Aminah', status:'normal', name:'Data Structures', remarks:'' });

        // ── FOCS / DFT2 (S1) ──
        addEvent('dft2s1', 0, { di:1, start:2, end:5, code:'BMIT4040', type:'L', venue:'B301', lecturer:'En. Ahmad Faiz', status:'normal', name:'Web Development', remarks:'' });
        addEvent('dft2s1', 0, { di:3, start:6, end:9, code:'BMIT5050', type:'L', venue:'B302', lecturer:'Pn. Farah Hanum', status:'normal', name:'Database Design', remarks:'' });

        addEvent('dft2s1', 1, { di:1, start:2, end:5, code:'BMIT4040', type:'L', venue:'B301', lecturer:'En. Ahmad Faiz', status:'normal', name:'Web Development', remarks:'' });
        addEvent('dft2s1', 1, { di:3, start:6, end:9, code:'BMIT5050', type:'L', venue:'B302', lecturer:'Pn. Farah Hanum', status:'normal', name:'Database Design', remarks:'' });
        addEvent('dft2s1', 1, { di:4, start:0, end:3, code:'BMIT6060', type:'L', venue:'B303', lecturer:'Dr. Lim Wei Ming', status:'pending', name:'Cybersecurity Fundamentals', remarks:'', requestedAt:'01 Sep 2026, 09:15 AM', requestedBy:'Dr. Lim Wei Ming' });

        // ── FOCS / RSD3 (S1) G1 ──
        addEvent('rsd3s1g1', 0, { di:0, start:8, end:11, code:'BMIT7070', type:'L', venue:'A101', lecturer:'Prof. Dr. Khoo Teik Huat', status:'normal', name:'Advanced Software Engineering', remarks:'' });
        addEvent('rsd3s1g1', 0, { di:2, start:0, end:3, code:'BMIT8080', type:'L', venue:'A102', lecturer:'Dr. Patricia Gomez', status:'normal', name:'Cloud Architecture', remarks:'' });

        // ── FOCS / RSD3 (S1) G2 ──
        addEvent('rsd3s1g2', 0, { di:0, start:8, end:11, code:'BMIT7070', type:'L', venue:'A101', lecturer:'Prof. Dr. Khoo Teik Huat', status:'normal', name:'Advanced Software Engineering', remarks:'' });
        addEvent('rsd3s1g2', 0, { di:3, start:0, end:3, code:'BMIT8080', type:'L', venue:'A102', lecturer:'Dr. Patricia Gomez', status:'normal', name:'Cloud Architecture', remarks:'' });

        // ── FOL / RSDL2 (S1) G1 ──
        addEvent('rsdl2s1g1', 0, { di:0, start:2, end:5, code:'LAW1001', type:'L', venue:'C101', lecturer:'Prof. Dato\' Siva', status:'normal', name:'Contract Law', remarks:'' });
        addEvent('rsdl2s1g1', 0, { di:2, start:6, end:9, code:'LAW2002', type:'L', venue:'C102', lecturer:'Ms. Rachel Wong', status:'normal', name:'Criminal Law', remarks:'' });
        addEvent('rsdl2s1g1', 0, { di:4, start:0, end:3, code:'LAW3003', type:'T', venue:'C103', lecturer:'En. Haris Iskandar', status:'normal', name:'Tort Law', remarks:'' });

        addEvent('rsdl2s1g1', 1, { di:0, start:2, end:5, code:'LAW1001', type:'L', venue:'C101', lecturer:'Prof. Dato\' Siva', status:'normal', name:'Contract Law', remarks:'' });
        addEvent('rsdl2s1g1', 1, { di:2, start:6, end:9, code:'LAW2002', type:'L', venue:'C102', lecturer:'Ms. Rachel Wong', status:'replacement', name:'Criminal Law', remarks:'26-Aug-2026' });

        // ── FOD / RSD2 (S1) (Design) ──
        addEvent('rsdds1', 0, { di:1, start:4, end:7, code:'DESN101', type:'L', venue:'D101', lecturer:'Ms. Amanda Teo', status:'normal', name:'Visual Communication', remarks:'' });
        addEvent('rsdds1', 0, { di:3, start:0, end:3, code:'DESN202', type:'L', venue:'D102', lecturer:'Mr. Ben Lee', status:'normal', name:'Digital Illustration', remarks:'' });

        // ── FCCI / DMC2 (S1) ──
        addEvent('dmc2s1', 0, { di:0, start:6, end:9, code:'COM1001', type:'L', venue:'E101', lecturer:'Ms. Elaine Chen', status:'normal', name:'Introduction to Mass Comm', remarks:'' });
        addEvent('dmc2s1', 0, { di:2, start:2, end:5, code:'COM2002', type:'L', venue:'E102', lecturer:'Mr. Jason Tan', status:'normal', name:'Journalism', remarks:'' });
        addEvent('dmc2s1', 0, { di:4, start:4, end:7, code:'COM3003', type:'T', venue:'E103', lecturer:'Ms. Karen Lim', status:'pending', name:'Public Relations', remarks:'' });

        /* ───── State ───── */
        let currentWeek = 0;
        let selectedCohortId = null;

        /* ════════════════════════════════════════════
           DROPDOWN POPULATION
           ════════════════════════════════════════════ */

        function populateWeeks() {
            const sel = document.getElementById('weekSelect');
            sel.innerHTML = weekData.map((w, i) =>
                `<option value="${i}">${w.label} · ${w.range}</option>`
            ).join('');
            sel.selectedIndex = currentWeek;
        }

        function populateFaculties() {
            const sel = document.getElementById('facultySelect');
            sel.innerHTML = '<option value="">Select Faculty</option>' +
                facultyData.map(f => `<option value="${f.id}">${f.name}</option>`).join('');
        }

        function onFacultyChange() {
            const fid = document.getElementById('facultySelect').value;
            const cohortSel = document.getElementById('cohortSelect');
            if (!fid) {
                cohortSel.innerHTML = '<option value="">Select Cohort</option>';
                cohortSel.disabled = true;
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'Select a cohort';
                document.getElementById('emptyText').textContent = 'Choose a faculty and cohort to view timetable.';
                document.getElementById('timetable').querySelector('thead').innerHTML = '';
                document.getElementById('timetable').querySelector('tbody').innerHTML = '';
                document.getElementById('sumTotal').textContent = '0';
                document.getElementById('sumReplacement').textContent = '0';
                document.getElementById('sumPending').textContent = '0';
                document.getElementById('sumConflict').textContent = '0';
                document.getElementById('resultCount').textContent = 'Showing 0 of 0 events';
                updateWeekArrows(true, true);
                selectedCohortId = null;
                return;
            }
            const faculty = facultyData.find(f => f.id === fid);
            cohortSel.disabled = false;
            cohortSel.innerHTML = '<option value="">Select Cohort</option>' +
                faculty.cohorts.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
        }

        function onCohortChange() {
            const cid = document.getElementById('cohortSelect').value;
            if (!cid) {
                selectedCohortId = null;
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'Select a cohort';
                document.getElementById('emptyText').textContent = 'Choose a cohort to view timetable.';
                document.getElementById('timetable').querySelector('thead').innerHTML = '';
                document.getElementById('timetable').querySelector('tbody').innerHTML = '';
                document.getElementById('sumTotal').textContent = '0';
                document.getElementById('sumReplacement').textContent = '0';
                document.getElementById('sumPending').textContent = '0';
                document.getElementById('sumConflict').textContent = '0';
                document.getElementById('resultCount').textContent = 'Showing 0 of 0 events';
                updateWeekArrows(true, true);
                return;
            }
            selectedCohortId = cid;
            currentWeek = 0;
            document.getElementById('weekSelect').selectedIndex = 0;
            buildTimetable();
        }

        /* ════════════════════════════════════════════
           WEEK NAVIGATION
           ════════════════════════════════════════════ */

        function prevWeek() {
            if (currentWeek > 0) {
                currentWeek--;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
            }
        }

        function nextWeek() {
            if (currentWeek < weekData.length - 1) {
                currentWeek++;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
            }
        }

        function selectWeek(index) {
            currentWeek = parseInt(index);
            buildTimetable();
        }

        /* ════════════════════════════════════════════
           TIMETABLE GRID BUILDER
           ════════════════════════════════════════════ */

        function buildTimetable() {
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            if (!selectedCohortId) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'Select a cohort';
                document.getElementById('emptyText').textContent = 'Choose a faculty and cohort to view timetable.';
                updateWeekArrows(true, true);
                return;
            }

            const data = weekData[currentWeek];
            const days = data.days;
            const weekEvents = allEvents[selectedCohortId]?.[currentWeek] || [];

            // ── Check if week has any events ──
            if (weekEvents.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'No classes scheduled';
                document.getElementById('emptyText').textContent = 'No classes scheduled for this cohort in the selected week.';
                document.getElementById('sumTotal').textContent = '0';
                document.getElementById('sumReplacement').textContent = '0';
                document.getElementById('sumPending').textContent = '0';
                document.getElementById('sumConflict').textContent = '0';
                document.getElementById('resultCount').textContent = 'Showing 0 of 0 events';
                updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
                return;
            }

            document.getElementById('emptyState').style.display = 'none';

            // ── Time header row ──
            const timeHeaderRow = document.createElement('tr');
            const cornerTh = document.createElement('th');
            cornerTh.className = 'time-header-col';
            cornerTh.style.cssText = 'position: sticky; left: 0; z-index: 40;';
            cornerTh.innerHTML = '<span style="font-size:13px;font-weight:600;">Day / Time</span>';
            timeHeaderRow.appendChild(cornerTh);

            hours.forEach(h => {
                const th = document.createElement('th');
                th.className = 'hour-header';
                const end = add30min(h);
                th.innerHTML = `<span class="hour-top">${h}</span><span class="hour-bottom">${end}</span>`;
                timeHeaderRow.appendChild(th);
            });
            head.appendChild(timeHeaderRow);

            // ── Day rows ──
            days.forEach((day, di) => {
                const tr = document.createElement('tr');

                const dayTd = document.createElement('td');
                let dayColClass = 'time-col';
                if (day.today) dayColClass += ' today';
                if (day.holiday) dayColClass += ' holiday-col';
                if (day.sunday) dayColClass += ' sunday-col';
                dayTd.className = dayColClass;
                let dayHtml = `<span class="day-label">${day.abbr}</span><span class="date-label">${day.date}</span>`;
                if (day.holiday) {
                    dayHtml += `<span class="holiday-label">Public Holiday</span>`;
                }
                dayTd.innerHTML = dayHtml;
                tr.appendChild(dayTd);

                const dayEvents = weekEvents.filter(e => e.di === di);

                const slotMap = {};
                hours.forEach((_, hi) => { slotMap[hi] = null; });

                dayEvents.forEach(e => {
                    for (let hi = e.start; hi <= e.end; hi++) {
                        if (hi === e.start) {
                            slotMap[hi] = { event: e, span: e.end - e.start + 1 };
                        } else {
                            slotMap[hi] = { event: null, span: 0, occupied: true };
                        }
                    }
                });

                hours.forEach((h, hi) => {
                    const td = document.createElement('td');
                    let cellClass = 'hour-cell';
                    if (day.sunday) cellClass += ' sunday-slot';
                    if (day.holiday) cellClass += ' holiday-slot';
                    td.className = cellClass;
                    td.dataset.day = di;
                    td.dataset.hour = hi;

                    const info = slotMap[hi];

                    if (info && info.event) {
                        const e = info.event;
                        const isConflict = day.holiday;
                        const div = document.createElement('div');
                        div.className = 'event-block span-' + info.span;
                        if (isConflict) {
                            div.classList.add('event-public-holiday');
                        } else if (e.status === 'normal') {
                            div.classList.add('event-normal');
                        } else if (e.status === 'replacement') {
                            div.classList.add('event-replacement');
                        } else if (e.status === 'pending') {
                            div.classList.add('event-pending');
                        }

                        const startTime = (typeof to12h === 'function' ? to12h(hours[e.start]) : hours[e.start]);
                        const endTime = (typeof to12h === 'function' ? to12h(hours[e.end + 1] || add30min(hours[e.end])) : hours[e.end + 1] || add30min(hours[e.end]));

                        let extraHtml = '';
                        if (e.status === 'replacement' && e.remarks) {
                            extraHtml = `<span class="ev-note">(Replaced for ${e.remarks})</span>`;
                        } else if (e.status === 'pending') {
                            extraHtml = `<span class="ev-note">(Pending Approval)</span>`;
                        }

                        div.innerHTML = `
                            <span class="ev-code">${e.code}(${e.type})</span>
                            <span class="ev-venue">${e.venue}</span>
                            <span class="ev-time">${startTime} - ${endTime}</span>
                            ${extraHtml}
                        `;

                        div.addEventListener('click', function() { openModal(e, di); });
                        td.appendChild(div);

                        if (info.span > 1) {
                            td.colSpan = info.span;
                        }
                    } else if (info && info.occupied) {
                        td.style.display = 'none';
                    } else {
                        const div = document.createElement('div');
                        div.className = 'cell-empty';
                        td.appendChild(div);
                    }

                    tr.appendChild(td);
                });

                body.appendChild(tr);
            });

            document.getElementById('resultCount').textContent = 'Showing ' + weekEvents.length + ' of ' + weekEvents.length + ' events';
            updateSummaries(weekEvents);
        }

        /* ════════════════════════════════════════════
           SEARCH & FILTER
           ════════════════════════════════════════════ */

        function applyFilters() {
            if (!selectedCohortId) return;
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const statusFilter = document.getElementById('statusFilter').value;
            const weekEvents = allEvents[selectedCohortId]?.[currentWeek] || [];
            const data = weekData[currentWeek];
            const days = data ? data.days : [];

            const filtered = weekEvents.filter(e => {
                const matchesStatus = statusFilter === 'all' || e.status === statusFilter || (statusFilter === 'conflict' && days[e.di] && days[e.di].holiday);
                if (statusFilter === 'conflict' && e.status !== 'conflict') {
                    if (!(days[e.di] && days[e.di].holiday)) return false;
                }
                const matchesSearch = !query ||
                    (e.code && e.code.toLowerCase().includes(query)) ||
                    (e.name && e.name.toLowerCase().includes(query)) ||
                    (e.lecturer && e.lecturer.toLowerCase().includes(query)) ||
                    (e.venue && e.venue.toLowerCase().includes(query));
                return matchesSearch && matchesStatus;
            });

            document.getElementById('resultCount').textContent = `Showing ${filtered.length} of ${weekEvents.length} events`;

            // Rebuild grid with filtered events
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            if (filtered.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'No matching events';
                document.getElementById('emptyText').textContent = 'Try adjusting your search or filter.';
                updateSummaries(weekEvents);
                updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
                return;
            }

            document.getElementById('emptyState').style.display = 'none';

            // Build grid using filtered events
            const timeHeaderRow = document.createElement('tr');
            const cornerTh = document.createElement('th');
            cornerTh.className = 'time-header-col';
            cornerTh.style.cssText = 'position: sticky; left: 0; z-index: 40;';
            cornerTh.innerHTML = '<span style="font-size:13px;font-weight:600;">Day / Time</span>';
            timeHeaderRow.appendChild(cornerTh);

            hours.forEach(h => {
                const th = document.createElement('th');
                th.className = 'hour-header';
                const end = add30min(h);
                th.innerHTML = `<span class="hour-top">${h}</span><span class="hour-bottom">${end}</span>`;
                timeHeaderRow.appendChild(th);
            });
            head.appendChild(timeHeaderRow);

            days.forEach((day, di) => {
                const tr = document.createElement('tr');

                const dayTd = document.createElement('td');
                let dayColClass = 'time-col';
                if (day.today) dayColClass += ' today';
                if (day.holiday) dayColClass += ' holiday-col';
                if (day.sunday) dayColClass += ' sunday-col';
                dayTd.className = dayColClass;
                let dayHtml = `<span class="day-label">${day.abbr}</span><span class="date-label">${day.date}</span>`;
                if (day.holiday) dayHtml += `<span class="holiday-label">Public Holiday</span>`;
                dayTd.innerHTML = dayHtml;
                tr.appendChild(dayTd);

                const dayEvents = filtered.filter(e => e.di === di);

                const slotMap = {};
                hours.forEach((_, hi) => { slotMap[hi] = null; });

                dayEvents.forEach(e => {
                    for (let hi = e.start; hi <= e.end; hi++) {
                        if (hi === e.start) {
                            slotMap[hi] = { event: e, span: e.end - e.start + 1 };
                        } else {
                            slotMap[hi] = { event: null, span: 0, occupied: true };
                        }
                    }
                });

                hours.forEach((h, hi) => {
                    const td = document.createElement('td');
                    let cellClass = 'hour-cell';
                    if (day.sunday) cellClass += ' sunday-slot';
                    if (day.holiday) cellClass += ' holiday-slot';
                    td.className = cellClass;

                    const info = slotMap[hi];

                    if (info && info.event) {
                        const e = info.event;
                        const isConflict = day.holiday;
                        const div = document.createElement('div');
                        div.className = 'event-block span-' + info.span;
                        if (isConflict) div.classList.add('event-public-holiday');
                        else if (e.status === 'normal') div.classList.add('event-normal');
                        else if (e.status === 'replacement') div.classList.add('event-replacement');
                        else if (e.status === 'pending') div.classList.add('event-pending');

                        const startTime = (typeof to12h === 'function' ? to12h(hours[e.start]) : hours[e.start]);
                        const endTime = (typeof to12h === 'function' ? to12h(hours[e.end + 1] || add30min(hours[e.end])) : hours[e.end + 1] || add30min(hours[e.end]));

                        let extraHtml = '';
                        if (e.status === 'replacement' && e.remarks) {
                            extraHtml = `<span class="ev-note">(Replaced for ${e.remarks})</span>`;
                        } else if (e.status === 'pending') {
                            extraHtml = `<span class="ev-note">(Pending Approval)</span>`;
                        }

                        div.innerHTML = `
                            <span class="ev-code">${e.code}(${e.type})</span>
                            <span class="ev-venue">${e.venue}</span>
                            <span class="ev-time">${startTime} - ${endTime}</span>
                            ${extraHtml}
                        `;

                        div.addEventListener('click', function() { openModal(e, di); });
                        td.appendChild(div);

                        if (info.span > 1) td.colSpan = info.span;
                    } else if (info && info.occupied) {
                        td.style.display = 'none';
                    } else {
                        const div = document.createElement('div');
                        div.className = 'cell-empty';
                        td.appendChild(div);
                    }

                    tr.appendChild(td);
                });

                body.appendChild(tr);
            });

            updateSummaries(weekEvents);
        }

        /* ════════════════════════════════════════════
           SUMMARY
           ════════════════════════════════════════════ */

        function updateSummaries(events) {
            const data = weekData[currentWeek];
            const days = data ? data.days : [];
            let total = events.length;
            let replacement = 0, pending = 0, conflict = 0;

            events.forEach(e => {
                if (e.status === 'replacement') replacement++;
                if (e.status === 'pending') pending++;
                if (days[e.di] && days[e.di].holiday) conflict++;
            });

            document.getElementById('sumTotal').textContent = total;
            document.getElementById('sumReplacement').textContent = replacement;
            document.getElementById('sumPending').textContent = pending;
            document.getElementById('sumConflict').textContent = conflict;
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
        }

        /* ════════════════════════════════════════════
           MODAL
           ════════════════════════════════════════════ */

        function openModal(event, di) {
            document.getElementById('modalTitle').textContent = event.code + ' — ' + event.name;

            const data = weekData[currentWeek];
            const days = data ? data.days : [];
            const isConflict = days[di] && days[di].holiday;
            const displayStatus = isConflict ? 'conflict' : event.status;

            const badge = document.getElementById('modalStatusBadge');
            badge.textContent = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1);
            badge.className = 'modal-status-badge ' + displayStatus;

            const startTime = (typeof to12h === 'function' ? to12h(hours[event.start]) : hours[event.start]);
            const endTime = (typeof to12h === 'function' ? to12h(hours[event.end + 1] || add30min(hours[event.end])) : hours[event.end + 1] || add30min(hours[event.end]));

            document.getElementById('mdlCourse').textContent = event.code || '—';
            document.getElementById('mdlName').textContent = event.name || '—';
            document.getElementById('mdlLecturer').textContent = event.lecturer || '—';
            document.getElementById('mdlVenue').textContent = event.venue || '—';
            document.getElementById('mdlCohort').textContent = event.cohort || selectedCohortId || '—';

            const dayName = days[di] ? days[di].abbr : '—';
            const dateStr = days[di] ? days[di].date : '—';
            document.getElementById('mdlTime').textContent = `${dayName}, ${dateStr} · ${startTime} – ${endTime}`;

            const statusBadge = document.getElementById('mdlStatusBadge');
            const statusText = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1);
            statusBadge.textContent = statusText;
            statusBadge.className = 'badge ' + (displayStatus === 'normal' ? 'badge-normal' : displayStatus === 'replacement' ? 'badge-replacement' : displayStatus === 'pending' ? 'badge-pending' : 'badge-conflict');

            document.getElementById('mdlRemarks').textContent = event.remarks || '—';

            document.getElementById('eventModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        /* ════════════════════════════════════════════
           INIT
           ════════════════════════════════════════════ */

        document.addEventListener('DOMContentLoaded', function() {
            populateWeeks();
            populateFaculties();

            // Show initial empty state
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('emptyTitle').textContent = 'Select a cohort';
            document.getElementById('emptyText').textContent = 'Choose a faculty and cohort to view timetable.';
            updateWeekArrows(true, true);
        });
@endsection
