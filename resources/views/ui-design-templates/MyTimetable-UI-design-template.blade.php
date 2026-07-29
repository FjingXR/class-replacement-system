@extends('layouts.ui-template', ['activeNav' => 'my-timetable'])

@section('title', 'My Timetable')

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
            min-width: 140px;
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

        /* ───── Time Column ───── */
        .time-col {
            width: 130px;
            min-width: 130px;
            left: 0;
            position: sticky;
            z-index: 15;
            background: var(--color-surface);
            font-weight: 600;
        }
        .time-col .day-label {
            display: block;
            font-size: 14px;
        }
        .time-col .date-label {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: var(--color-on-surface-variant);
            margin-top: 2px;
        }
        .time-col .holiday-label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: var(--color-error);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
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
            display: flex; justify-content: space-between; align-items: center;
            padding: 0 24px 20px;
        }
        .modal-footer-left {
            display: flex; align-items: center;
        }
        .modal-footer-right {
            display: flex; align-items: center; gap: 10px;
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
        .btn-replace-now {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background: var(--color-error-container);
            color: var(--color-on-error-container);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background var(--transition), transform 0.15s;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-replace-now:hover {
            filter: brightness(1.1);
        }
        .btn-replace-now:active {
            transform: scale(0.97);
        }

        .btn-cancel-class {
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid var(--color-error);
            background: transparent;
            color: var(--color-error);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background var(--transition), transform 0.15s;
        }
        .btn-cancel-class:hover {
            background: var(--color-error-container);
        }
        .btn-cancel-class:active {
            transform: scale(0.97);
        }

        /* ───── Cancel Confirmation Modal ───── */
        .cancel-overlay {
            position: fixed; inset: 0; z-index: 1000;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .cancel-modal {
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            max-width: 420px; width: 100%;
            animation: modalIn 0.2s ease;
        }
        .cancel-modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px 0;
        }
        .cancel-modal-title {
            font-size: 18px; font-weight: 700; color: var(--color-on-surface);
        }
        .cancel-modal-body {
            padding: 20px 24px;
            font-size: 14px; color: var(--color-on-surface);
            line-height: 1.5;
        }
        .cancel-modal-footer {
            display: flex; justify-content: flex-end; gap: 10px;
            padding: 0 24px 20px;
        }
        .btn-cancel-secondary {
            padding: 10px 20px;
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
        .btn-cancel-secondary:hover {
            background: var(--color-surface-variant);
        }
        .btn-cancel-secondary:active {
            transform: scale(0.97);
        }
        .btn-cancel-danger {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background: var(--color-error);
            color: var(--color-on-error);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background var(--transition), transform 0.15s;
        }
        .btn-cancel-danger:hover {
            filter: brightness(1.1);
        }
        .btn-cancel-danger:active {
            transform: scale(0.97);
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        <div class="page-header">
            <h1 class="page-title">My Timetable</h1>
            <p class="page-desc">View your weekly class schedule and manage replacement requests across all cohorts.</p>
        </div>

        <!-- ─── Semester Bar ─── -->
        <div class="semester-bar">
            <button class="week-arrow" onclick="prevWeek()" aria-label="Previous week">&#8249;</button>
            <select class="week-select" id="weekSelect" onchange="selectWeek(this.value)"></select>
            <button class="week-arrow" onclick="nextWeek()" aria-label="Next week">&#8250;</button>
            <span class="session-text">202605 Semester (Monday, 15-Jun-2026 ~ Sunday, 20-Sep-2026)</span>
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

        <!-- ─── Legend Bar ─── -->
        <div class="legend-bar">
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-secondary);"></div>
                Normal Class
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-primary);"></div>
                Confirmed Replacement
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-tertiary);"></div>
                Pending Approval
            </div>
            <div class="legend-item">
                <div class="legend-swatch" style="background: var(--color-error);"></div>
                Public Holiday / Conflict
            </div>
        </div>

        <!-- ─── Weekly Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Confirmed Replacement'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending Approval'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts / Public Holiday'],
            ]
        ])

    <!-- ═══ Class Detail Modal ═══ -->
    <div class="modal-overlay" id="classModal" style="display:none" onclick="closeModalOutside(event)">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle">Class Details</span>
                <span class="modal-status-badge" id="modalStatusBadge">Normal</span>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <div class="modal-footer-left">
                    <button class="btn-replace-now" id="btnReplaceNow" style="display:none" onclick="goToReplacement()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                        Replace Now
                    </button>
                    <button class="btn-cancel-class" id="btnCancelClass" style="display:none" onclick="cancelClass()"></button>
                </div>
                <div class="modal-footer-right">
                    <button class="btn-close-modal" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Cancel Confirmation Modal ═══ -->
    <div class="cancel-overlay" id="cancelConfirmOverlay" style="display:none" onclick="if(event.target===this)closeCancelConfirm(false)">
        <div class="cancel-modal">
            <div class="cancel-modal-header">
                <span class="cancel-modal-title">Cancel Class</span>
                <button class="modal-close" onclick="closeCancelConfirm(false)">&times;</button>
            </div>
            <div class="cancel-modal-body">
                <p>Are you sure you want to cancel this class?</p>
                <p style="font-size:13px;opacity:0.7;margin-top:6px;">This action cannot be undone. A cancellation notice will be sent to all affected parties.</p>
            </div>
            <div class="cancel-modal-footer">
                <button class="btn-cancel-secondary" onclick="closeCancelConfirm(false)">No, Keep It</button>
                <button class="btn-cancel-danger" onclick="closeCancelConfirm(true)">Yes, Cancel Class</button>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
        const hours = [
            '08:00', '08:30', '09:00', '09:30',
            '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30',
            '13:00', '13:30', '14:00', '14:30',
            '15:00', '15:30', '16:00', '16:30',
            '17:00', '17:30', '18:00', '18:30'
        ];

        function add30min(t) {
            const [h, m] = t.split(':').map(Number);
            const total = h * 60 + m + 30;
            return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
        }

        function to12h(t) {
            const [h, m] = t.split(':').map(Number);
            const ampm = h >= 12 ? 'PM' : 'AM';
            const h12 = h === 0 ? 12 : h > 12 ? h - 12 : h;
            return `${h12}:${String(m).padStart(2, '0')} ${ampm}`;
        }

        const dayNames = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];

        const weekData = [
            {
                label: 'Week 9',
                range: '18 Aug – 24 Aug 2026',
                days: [
                    { abbr: 'Mon', date: '18 Aug 2026' },
                    { abbr: 'Tue', date: '19 Aug 2026' },
                    { abbr: 'Wed', date: '20 Aug 2026' },
                    { abbr: 'Thu', date: '21 Aug 2026' },
                    { abbr: 'Fri', date: '22 Aug 2026' },
                    { abbr: 'Sat', date: '23 Aug 2026' },
                    { abbr: 'Sun', date: '24 Aug 2026', sunday: true },
                ]
            },
            {
                label: 'Week 10',
                range: '25 Aug – 31 Aug 2026',
                days: [
                    { abbr: 'Mon', date: '25 Aug 2026' },
                    { abbr: 'Tue', date: '26 Aug 2026' },
                    { abbr: 'Wed', date: '27 Aug 2026' },
                    { abbr: 'Thu', date: '28 Aug 2026' },
                    { abbr: 'Fri', date: '29 Aug 2026' },
                    { abbr: 'Sat', date: '30 Aug 2026' },
                    { abbr: 'Sun', date: '31 Aug 2026', sunday: true },
                ]
            },
            {
                label: 'Week 11',
                range: '01 Sep – 07 Sep 2026',
                days: [
                    { abbr: 'Mon', date: '01 Sep 2026', today: true },
                    { abbr: 'Tue', date: '02 Sep 2026' },
                    { abbr: 'Wed', date: '03 Sep 2026' },
                    { abbr: 'Thu', date: '04 Sep 2026', holiday: true },
                    { abbr: 'Fri', date: '05 Sep 2026' },
                    { abbr: 'Sat', date: '06 Sep 2026' },
                    { abbr: 'Sun', date: '07 Sep 2026', sunday: true },
                ]
            }
        ];

        const eventsData = {
            0: [
                { di: 0, start: 4, end: 7, code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', status: 'normal', name: 'Object-Oriented Programming', remarks: '' },
                { di: 1, start: 0, end: 3, code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', status: 'normal', name: 'Database Systems', remarks: '' },
            ],
            1: [
                { di: 0, start: 4, end: 7, code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', status: 'normal', name: 'Object-Oriented Programming', remarks: '' },
                { di: 2, start: 2, end: 5, code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD2 (S1)', status: 'normal', name: 'Computer Networks', remarks: '' },
                { di: 4, start: 0, end: 3, code: 'BMIT3456', type: 'L', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
            ],
            2: [
                { di: 0, start: 4, end: 7, code: 'BMIT6767', type: 'L', venue: 'B103', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 24, status: 'normal', name: 'Object-Oriented Programming', remarks: '' },
                { di: 0, start: 12, end: 14, code: 'BMIT1234', type: 'T', venue: 'B104', lecturer: 'Dr. Christopher Lazarus', cohort: 'DFT2 (S1)', studentCount: 22, status: 'normal', name: 'Data Structures', remarks: '' },
                { di: 1, start: 0, end: 3, code: 'BMIT5678', type: 'L', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', studentCount: 28, status: 'normal', name: 'Database Systems', remarks: '' },
                { di: 1, start: 14, end: 16, code: 'BMIT5678', type: 'T', venue: 'B105', lecturer: 'En. Lim Jia Zheng', cohort: 'DSF2 (S1)', status: 'replacement', name: 'Database Systems', remarks: '31-Aug-2026' },
                { di: 2, start: 2, end: 5, code: 'BMIT9012', type: 'L', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD3(S1)G1 + RSD3(S1)G2', cohorts: ['RSD3(S1)G1', 'RSD3(S1)G2'], studentCounts: [16, 9], status: 'normal', name: 'Computer Networks', remarks: '' },
                { di: 2, start: 12, end: 15, code: 'BMIT9012', type: 'T', venue: 'B106', lecturer: 'Pn. Surayaini Basri', cohort: 'RSD2 (S1)', status: 'replacement', name: 'Computer Networks', remarks: '26-Aug-2026' },
                { di: 3, start: 4, end: 7, code: 'BMIT5555', type: 'L', venue: 'B110', lecturer: 'Dr. Lim Wei Ming', cohort: 'CSF2 (S1)', status: 'normal', name: 'Software Engineering', remarks: '' },
                { di: 3, start: 10, end: 12, code: 'BMIT6666', type: 'T', venue: 'B111', lecturer: 'Pn. Sarah Tan', cohort: 'CSF2 (S1)', status: 'normal', name: 'Mobile App Development', remarks: '' },
                { di: 4, start: 0, end: 3, code: 'BMIT3456', type: 'L', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 4, start: 5, end: 7, code: 'BMIT3456', type: 'T', venue: 'B103', lecturer: 'Dr. Chang Foo Chung', cohort: 'RAF2 (S1)', status: 'normal', name: 'Artificial Intelligence', remarks: '' },
                { di: 5, start: 2, end: 5, code: 'BMIT7890', type: 'L', venue: 'B201', lecturer: 'En. Jefther Edward', cohort: 'RBU2 (S1)', status: 'normal', name: 'Project Management', remarks: '' },
                { di: 5, start: 12, end: 14, code: 'BMIT9999', type: 'T', venue: 'B202', lecturer: 'Dr. Tan Ah Meng', cohort: 'DMF2 (S1)', status: 'pending', name: 'Machine Learning', remarks: '', requestedAt: '03 Sep 2026, 10:30 AM', requestedBy: 'Dr. Tan Ah Meng' },
            ]
        };

        let currentWeek = 2;

        function openModal(event) {
            document.getElementById('modalTitle').textContent = event.code || 'Class Details';

            const days = weekData[currentWeek].days;
            const isConflict = days[event.di] && days[event.di].holiday;
            const displayStatus = isConflict ? 'conflict' : event.status;

            const badge = document.getElementById('modalStatusBadge');
            badge.textContent = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1);
            badge.className = 'modal-status-badge ' + displayStatus;

            const replaceBtn = document.getElementById('btnReplaceNow');
            replaceBtn.style.display = isConflict ? 'flex' : 'none';

            const cancelBtn = document.getElementById('btnCancelClass');
            cancelBtn.style.display = isConflict ? 'none' : 'flex';
            cancelBtn.textContent = event.status === 'pending' ? 'Cancel Request?' : 'Cancel Class?';

            const startStr = to12h(hours[event.start]);
            const endStr = to12h(hours[event.end + 1] || add30min(hours[event.end]));

            let cohortValue = event.cohort;
            let studentValue = event.studentCount ? String(event.studentCount) : '—';
            if (event.cohorts && event.studentCounts) {
                cohortValue = event.cohorts.join(' + ');
                studentValue = event.studentCounts.join('+') + ' = ' + event.studentCounts.reduce((a, b) => a + b, 0);
            }

            const fields = [
                { label: 'Subject Code', value: event.code },
                { label: 'Subject Name', value: event.name },
                { label: 'Class Type', value: event.type === 'L' ? 'Lecture (L)' : 'Tutorial (T)' },
                { label: 'Lecturer', value: event.lecturer },
                { label: 'Cohort', value: cohortValue },
                { label: 'Total Students', value: studentValue },
                { label: 'Venue', value: event.venue || '—' },
                { label: 'Day', value: dayNames[event.di] },
                { label: 'Date', value: weekData[currentWeek].days[event.di].date },
                { label: 'Time', value: startStr + ' – ' + endStr },
                { label: 'Status', value: displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1) },
                { label: 'Remarks', value: event.remarks || '—' },
            ];

            if (event.status === 'pending') {
                fields.splice(fields.length - 1, 0,
                    { label: 'Requested At', value: event.requestedAt || '—' },
                    { label: 'Requested By', value: event.requestedBy || '—' }
                );
            }

            document.getElementById('modalBody').innerHTML = fields.map(f =>
                `<div class="modal-field">
                    <span class="field-label">${f.label}</span>
                    <span class="field-value">${f.value}</span>
                </div>`
            ).join('');

            document.getElementById('classModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('classModal').style.display = 'none';
        }

        function cancelClass() {
            const overlay = document.getElementById('cancelConfirmOverlay');
            overlay.style.display = 'flex';
        }

        function closeCancelConfirm(confirmed) {
            document.getElementById('cancelConfirmOverlay').style.display = 'none';
            if (confirmed) {
                closeModal();
            }
        }

        function closeModalOutside(e) {
            if (e.target === e.currentTarget) closeModal();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        function buildTimetable() {
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            const data = weekData[currentWeek];
            const days = data.days;
            const events = eventsData[currentWeek] || [];

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
                if (day.holiday) {
                    dayHtml += `<span class="holiday-label">Public Holiday</span>`;
                }
                dayTd.innerHTML = dayHtml;
                tr.appendChild(dayTd);

                const dayEvents = events.filter(e => e.di === di);

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

                        const startTime = to12h(hours[e.start]);
                        const endTime = to12h(hours[e.end + 1] || add30min(hours[e.end]));

                        let extraHtml = '';
                        if (e.status === 'replacement') {
                            extraHtml = `<span class="ev-note">(Replaced for ${e.remarks})</span>`;
                        }

                        div.innerHTML = `
                            <span class="ev-code">${e.code}(${e.type})</span>
                            <span class="ev-venue">${e.venue}</span>
                            <span class="ev-time">${startTime} - ${endTime}</span>
                            ${extraHtml}
                        `;

                        div.addEventListener('click', function() { openModal(e); });
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
            updateSummary();
        }

        function updateSummary() {
            const events = eventsData[currentWeek] || [];
            const days = weekData[currentWeek].days;
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
        }

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

        function goToReplacement() {
            window.location.href = '/replacement-arrangement';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sel = document.getElementById('weekSelect');
            sel.innerHTML = weekData.map((w, i) =>
                `<option value="${i}">${w.label} · ${w.range}</option>`
            ).join('');
            sel.selectedIndex = currentWeek;

            buildTimetable();
        });
@endsection
