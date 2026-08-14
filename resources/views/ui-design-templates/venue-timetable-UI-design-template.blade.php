@extends('layouts.ui-template', ['activeNav' => 'venue-timetable', 'pageKey' => 'venueTimetable'])

@section('title', 'Venue Timetable — Class Replacement System')

@section('page-styles')

        /* ───── Venue Dropdown ───── */
        .venue-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .venue-bar select {
            min-width: 280px;
        }
        .venue-dropdown-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .fav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-sm);
            background: transparent;
            cursor: pointer;
            font-size: 18px;
            color: var(--color-on-surface-variant);
            transition: background 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .fav-btn:hover {
            background: var(--color-surface-variant);
        }
        .fav-btn.active {
            color: var(--color-tertiary);
            border-color: var(--color-tertiary);
        }
        .fav-btn:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        /* ───── Recent / All sections in dropdown ───── */
        #venueSelect optgroup {
            font-weight: 600;
            color: var(--color-on-surface);
        }
        #venueSelect option {
            font-weight: 400;
            color: var(--color-on-surface);
        }

        /* ───── Filter Bar ───── */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-on-surface-variant);
        }
        .segment-toggle {
            display: inline-flex;
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .segment-toggle button {
            background: transparent;
            border: none;
            padding: 6px 14px;
            font-size: 13px;
            color: var(--color-on-surface-variant);
            cursor: pointer;
            border-right: 1px solid var(--color-outline);
            transition: background 0.15s, color 0.15s;
        }
        .segment-toggle button:last-child {
            border-right: none;
        }
        .segment-toggle button.active {
            background: var(--color-primary);
            color: var(--color-on-primary);
        }
        .segment-toggle button:hover:not(.active) {
            background: var(--color-surface-variant);
        }
        .segment-toggle button:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: -2px;
        }

        /* ───── Venue Type Filter ───── */
        .venue-type-filter {
            position: relative;
        }
        .venue-type-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            font-size: 13px;
            color: var(--color-on-surface-variant);
            cursor: pointer;
        }
        .venue-type-btn:hover {
            background: var(--color-surface-variant);
        }
        .venue-type-btn:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }
        .venue-type-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 4px;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-sm);
            padding: 8px 12px;
            z-index: 100;
            min-width: 180px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06);
        }
        .venue-type-dropdown.open {
            display: block;
        }
        .venue-type-dropdown label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 0;
            font-size: 13px;
            color: var(--color-on-surface);
            cursor: pointer;
        }

        /* ───── Booking Banner ───── */
        .booking-banner {
            background: var(--color-primary);
            color: #fff;
            padding: 10px 16px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            display: none;
        }

        /* ───── History Panel (Collapsible) ───── */
        .history-details {
            margin-bottom: 12px;
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }
        .history-summary {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-on-surface);
            cursor: pointer;
            background: var(--color-surface-variant);
            list-style: none;
        }
        .history-summary::-webkit-details-marker {
            display: none;
        }
        .history-summary:hover {
            background: var(--color-surface);
        }
        .history-panel {
            max-height: 300px;
            overflow-y: auto;
            border-top: 1px solid var(--color-outline);
        }
        .history-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--color-outline);
            font-size: 13px;
        }
        .history-row:last-child {
            border-bottom: none;
        }
        .history-date {
            min-width: 90px;
            color: var(--color-on-surface-variant);
        }
        .history-code {
            font-weight: 600;
            min-width: 100px;
        }
        .history-status {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: var(--radius-sm);
        }



        /* ───── Booking Hint ───── */
        .booking-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--color-primary-container);
            color: var(--color-on-primary-container);
            border-radius: var(--radius-sm);
            font-size: 13px;
            margin-bottom: 8px;
        }
        .booking-hint svg {
            flex-shrink: 0;
        }



        /* ───── Toast ───── */
        .toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--color-surface);
            border: 1px solid var(--color-error);
            color: var(--color-on-surface);
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06);
            z-index: 9999;
            display: none;
        }
        .toast-notification.show {
            display: block;
        }

        /* ───── Print Button ───── */
        .print-btn {
            background: transparent;
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-sm);
            padding: 6px 10px;
            cursor: not-allowed;
            opacity: 0.5;
            color: var(--color-on-surface-variant);
            position: relative;
        }
        .print-btn:hover {
            opacity: 0.7;
        }
        .print-btn:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        /* ───── Available Cell Tooltip ───── */
        .available-tooltip {
            position: fixed;
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12), 0 1px 4px rgba(0,0,0,0.06);
            z-index: 9999;
            display: none;
            min-width: 250px;
        }
        .available-tooltip.show {
            display: block;
        }
        .available-tooltip p {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: var(--color-on-surface);
        }
        .available-tooltip .btn-book {
            background: var(--color-primary);
            color: var(--color-on-primary);
            border: none;
            padding: 6px 16px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 13px;
        }
        .available-tooltip .btn-book:hover {
            opacity: 0.9;
        }
        .available-tooltip .btn-book:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        /* ───── Booked Cell Modal (styles from theme.css) ───── */



        /* ───── Responsive ───── */
        @media (max-width: 1024px) {
            .grid-scroll { overflow-x: auto; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-right { justify-content: flex-start; }
            .filter-bar { flex-direction: column; align-items: stretch; }
        }
        @media (max-width: 768px) {
            .semester-bar select:not(.week-select) {
                min-width: 0;
                flex: 1 1 120px;
            }
            .week-nav {
                width: 100%;
            }
            .venue-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .venue-bar select {
                min-width: 0;
                width: 100%;
            }
            .venue-type-filter {
                width: 100%;
            }
            .venue-type-btn {
                width: 100%;
                justify-content: center;
            }
            .segment-toggle {
                width: 100%;
            }
            .segment-toggle button {
                flex: 1;
            }
            .card-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
                padding: 8px 0;
            }
            .venue-event-card {
                background: var(--color-surface);
                border: 1px solid var(--color-outline);
                border-radius: var(--radius-sm);
                padding: 12px;
            }
            .venue-event-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }
            .venue-event-code {
                font-weight: 600;
                font-size: 14px;
            }
            .venue-event-status {
                font-size: 12px;
                padding: 2px 8px;
                border-radius: var(--radius-sm);
            }
            .venue-event-body {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .venue-event-row {
                display: flex;
                justify-content: space-between;
                font-size: 13px;
            }
            .venue-event-label {
                color: var(--color-on-surface-variant);
            }
            .venue-available-card {
                background: var(--color-primary);
                color: #fff;
                border-radius: var(--radius-sm);
                padding: 12px;
                cursor: pointer;
                text-align: center;
                font-weight: 600;
            }
            .summary-bar {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
        }

        /* ───── Available slot hover label ───── */
        .cell-available { --hover-label: 'Book ?'; }



        /* ───── Slot Picker (hidden for venue timetable) ───── */
        .slot-picker-section {
            display: none;
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', [
            'title' => 'Venue Timetable',
            'description' => 'View weekly class schedule for any venue across all cohorts.'
        ])

        @include('partials.ui-guide-block', [
            'guideTitle' => 'How to use this page',
            'guideItems' => [
                '<strong>Select venue</strong> — choose a building, then a venue to view its timetable',
                '<strong>Week navigation</strong> — use arrows or Today button to browse weeks',
                '<strong>Slot status</strong> — Normal (green), Conflicted (red), Pending (amber), Approved (blue), Rejected (grey)',
                '<strong>View details</strong> — click any slot to see class details and cohort info',
                '<strong>Booking check</strong> — the banner shows if the venue is available for booking',
            ]
        ])

        <!-- ─── Booking Banner ─── -->
        <div class="booking-banner" id="bookingBanner"></div>

        <!-- ─── Error Banner ─── -->
        <div class="error-banner" id="errorBanner">
            <span>Unable to load data. Please refresh.</span>
            <button onclick="window.location.reload()">Refresh</button>
        </div>

        <!-- ─── No Venues Match Banner ─── -->
        <div class="no-match-banner" id="noMatchBanner">
            <p>No venues match criteria</p>
            <button onclick="resetFilters()">Show all venues</button>
        </div>

        <!-- ─── Venue + Week Picker ─── -->
        <div class="semester-bar">
            <div class="venue-dropdown-wrap">
                <select id="venueSelect" onchange="onVenueChange()"></select>
                <button class="fav-btn" id="favStar" onclick="toggleFavourite()" title="Toggle favourite">&#9734;</button>
            </div>
            @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)', 'disabled' => false])
            <button class="print-btn" title="Coming soon" disabled style="margin-left:auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
            </button>
        </div>

        <!-- ─── Past 4 Weeks Booking History (disabled — not useful for now) ───
        <details class="history-details" id="historyDetails">
            <summary class="history-summary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Past 4 Weeks Booking History
            </summary>
            <div class="history-panel" id="historyPanel"></div>
        </details>
        ─── END Past 4 Weeks ─── -->

        <!-- ─── Filter Bar ─── -->
        <div class="filter-bar">
            <div class="filter-group">
                <label>Venue Type:</label>
                <div class="venue-type-filter">
                    <button class="venue-type-btn" id="venueTypeBtn" onclick="toggleVenueTypeDropdown()">
                        All Types &#9662;
                    </button>
                    <div class="venue-type-dropdown" id="venueTypeDropdown">
                        <label><input type="checkbox" value="Tutorial" checked onchange="applyVenueTypeFilter()"> Tutorial</label>
                        <label><input type="checkbox" value="LectureHall" checked onchange="applyVenueTypeFilter()"> Lecture Hall</label>
                        <label><input type="checkbox" value="Lab" checked onchange="applyVenueTypeFilter()"> Lab</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── History Panel (Past 4 Weeks) — disabled ───
        <div class="history-panel" id="historyPanel"></div>
        ─── END History Panel ─── -->

        <!-- ─── Booking Hint ─── -->
        <div class="booking-hint" id="bookingHint" style="display:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span>Click any green slot to book this venue</span>
        </div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table')

        <!-- ─── Hint Text (empty grid) ─── -->
        <div class="hint-text" id="hintText" style="display:none">All slots available — this venue is free all week</div>

        <!-- ─── Legend Bar ─── -->
        @include('partials.ui-legend-bar', [
            'items' => [
                ['color' => 'var(--color-success-container)', 'label' => 'Available', 'tip' => 'Free slot — click to book this venue'],
                ['color' => 'var(--color-error-container)', 'label' => 'Occupied', 'tip' => 'Slot is booked — not available'],
                ['color' => 'var(--color-tertiary-container)', 'label' => 'Pending', 'tip' => 'Replacement request awaiting approval'],
            ]
        ])

        <!-- ─── Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Slots'],
                ['class' => 'card-available', 'valueId' => 'sumAvailable', 'label' => 'Available'],
                ['class' => 'card-conflict', 'valueId' => 'sumOccupied', 'label' => 'Occupied'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending'],
            ]
        ])

        <!-- ─── Empty State ─── -->
        @include('partials.ui-empty-state', ['title' => 'Select a venue', 'text' => 'Choose a venue from the dropdown to view its weekly schedule.'])

        <!-- ─── Mobile Card List ─── -->
        <div class="card-list" id="mobileCardList" style="display:none"></div>

        <!-- ─── Detail Modal (Booked Class) ─── -->
        <div class="modal-overlay" id="eventModal" onclick="if(event.target===this)closeModal()">
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

        <!-- ─── Available Slot Tooltip ─── -->
        <div class="available-tooltip" id="availableTooltip">
            <p id="tooltipText">Book B014 on Mon, 01 Sep 2026 at 09:00?</p>
            <button class="btn-book" id="tooltipBookBtn">Book</button>
        </div>

        <!-- ─── Toast Notification ─── -->
        <div class="toast-notification" id="toastNotification"></div>

@endsection

@section('page-scripts')

        /* ════════════════════════════════════════════
           STATE
           ════════════════════════════════════════════ */

        let currentVenue = null;
        let currentWeek = 0;
        let venueTypeFilters = { Tutorial: true, LectureHall: true, Lab: true };
        let currentTab = 'current';
        let currentCourseCode = null;
        let currentCohort = null;
        let focusedCell = null;

        /* ════════════════════════════════════════════
           MOCK DATA — Weeks
           ════════════════════════════════════════════ */

        const weekData = generateWeekData();

        function getTodayMs() {
            const d = new Date();
            d.setHours(0, 0, 0, 0);
            return d.getTime();
        }

        /* ════════════════════════════════════════════
           WEEK NAVIGATION (shared WeekNavigator)
           ════════════════════════════════════════════ */

        const weekNav = new WeekNavigator(MockData.semester, weekData);

        /* ════════════════════════════════════════════
           STATE PERSISTENCE (venue + venueType only)
           ════════════════════════════════════════════ */

        const venueState = createStatePersistence('venueTimetableState', {
            fields: [
                { id: 'venueSelect', type: 'select', key: 'venue' },
                { id: 'venueTypeDropdown', type: 'checkbox-group', key: 'venueTypes' },
            ]
        });

        /* ════════════════════════════════════════════
           URL PARAMS
           ════════════════════════════════════════════ */

        (function readUrlParams() {
            const params = new URLSearchParams(window.location.search);
            currentCourseCode = params.get('code');
            currentCohort = params.get('cohort');
            const venueParam = params.get('venue');
            if (currentCourseCode && currentCohort) {
                document.getElementById('bookingBanner').textContent = `Booking for: ${currentCourseCode} — ${currentCohort}`;
                document.getElementById('bookingBanner').style.display = '';
            }
            if (venueParam) {
                /* pre-select venue after dropdown is built */
                window._preselectVenue = venueParam;
            }
        })();

        /* ════════════════════════════════════════════
           INIT
           ════════════════════════════════════════════ */

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof MockData === 'undefined' || !MockData.semester) {
                document.getElementById('errorBanner').classList.add('show');
                document.querySelector('.grid-wrapper').style.display = 'none';
                return;
            }

            /* semester chip */
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;

            /* week dropdown */
            const weekSelect = document.getElementById('weekSelect');
            weekSelect.innerHTML = '';
            weekData.forEach((w, i) => {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = w.label + ' — ' + w.range;
                weekSelect.appendChild(opt);
            });

            /* default to today, then weekNav.load overrides if saved */
            currentWeek = currentWeekIndex();
            weekNav._currentWeek = currentWeek;
            weekNav.load();
            currentWeek = weekNav.currentWeek;

            /* restore venue + venueType (before buildVenueDropdown which triggers buildTimetable) */
            restoreState();

            /* venue dropdown */
            buildVenueDropdown();

            /* week nav */
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
            weekSelect.selectedIndex = currentWeek;

            /* today button — jumpToToday does NOT persist (no weekNav.save) */
            initTodayBtn();
            document.getElementById('todayBtn')?.addEventListener('click', function() {
                weekNav._currentWeek = currentWeek;
                weekNav.save();
            });

            /* show booking hint */
            document.getElementById('bookingHint').style.display = 'flex';

            /* keyboard nav */
            document.addEventListener('keydown', onKeydown);
            initWeekKeyboardShortcuts();
        });

        function buildVenueDropdown() {
            const select = document.getElementById('venueSelect');
            select.innerHTML = '';

            const favourites = getFavourites();
            const recent = getRecent();

            /* filter venues by type */
            const filteredVenues = MockData.venues.filter(v => venueTypeFilters[v.type] !== false);

            /* check if any venues match */
            if (filteredVenues.length === 0) {
                document.getElementById('noMatchBanner').classList.add('show');
                onVenueChange();
                return;
            }
            document.getElementById('noMatchBanner').classList.remove('show');

            /* Favourites section */
            if (favourites.length > 0) {
                const favGroup = document.createElement('optgroup');
                favGroup.label = '★ Favourites';
                favourites.forEach(code => {
                    const v = filteredVenues.find(x => x.code === code);
                    if (v) {
                        const opt = document.createElement('option');
                        opt.value = v.code;
                        opt.textContent = `${v.code} — ${v.type} (${v.capacity} seats)`;
                        favGroup.appendChild(opt);
                    }
                });
                if (favGroup.children.length > 0) {
                    select.appendChild(favGroup);
                }
            }

            /* Recent section */
            if (recent.length > 0) {
                const recentGroup = document.createElement('optgroup');
                recentGroup.label = 'Recent';
                recent.forEach(code => {
                    const v = filteredVenues.find(x => x.code === code);
                    if (v) {
                        const opt = document.createElement('option');
                        opt.value = v.code;
                        opt.textContent = `${v.code} — ${v.type} (${v.capacity} seats)`;
                        recentGroup.appendChild(opt);
                    }
                });
                if (recentGroup.children.length > 0) {
                    select.appendChild(recentGroup);
                }
            }

            /* All Venues section */
            const allGroup = document.createElement('optgroup');
            allGroup.label = 'All Venues';
            filteredVenues.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.code;
                opt.textContent = `${v.code} — ${v.type} (${v.capacity} seats)`;
                allGroup.appendChild(opt);
            });
            select.appendChild(allGroup);

            /* preselect */
            if (window._preselectVenue) {
                select.value = window._preselectVenue;
                delete window._preselectVenue;
            }

            /* if current venue is filtered out, select first available */
            if (currentVenue && !filteredVenues.some(v => v.code === currentVenue.code)) {
                currentVenue = filteredVenues[0];
            }

            onVenueChange();
        }

        /* ════════════════════════════════════════════
           VENUE CHANGE
           ════════════════════════════════════════════ */

        function onVenueChange() {
            const code = document.getElementById('venueSelect').value;
            if (!code) return;

            currentVenue = MockData.venues.find(v => v.code === code);
            if (!currentVenue) return;

            /* update favourite star */
            updateFavStar();

            /* update recent */
            updateRecent(code);

            /* show skeleton */
            SkeletonLoader.with(function() {
                buildTimetable();
            }, document.getElementById('tableBody'), 10, 300);

            saveState();
        }

        /* ════════════════════════════════════════════
           WEEK NAV
           ════════════════════════════════════════════ */

        function prevWeek() {
            if (currentWeek > 0) {
                currentWeek--;
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                buildTimetable();
                updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
                weekNav._currentWeek = currentWeek;
                weekNav.save();
            }
        }

        function nextWeek() {
            if (currentWeek < weekData.length - 1) {
                currentWeek++;
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                buildTimetable();
                updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
                weekNav._currentWeek = currentWeek;
                weekNav.save();
            }
        }

        function selectWeek(index) {
            currentWeek = parseInt(index);
            document.getElementById('weekSelect').selectedIndex = currentWeek;
            buildTimetable();
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
            weekNav._currentWeek = currentWeek;
            weekNav.save();
        }

        /* ════════════════════════════════════════════
           TAB TOGGLE
           ════════════════════════════════════════════ */

        function switchTab(tab) {
            currentTab = tab;
            buildTimetable();
        }

        function buildHistoryPanel() {
            const panel = document.getElementById('historyPanel');
            panel.innerHTML = '';
            if (!currentVenue) return;

            const pastWeeks = [];
            for (let w = Math.max(0, currentWeek - 4); w < currentWeek; w++) {
                pastWeeks.push(w);
            }

            let hasEvents = false;
            pastWeeks.reverse().forEach(w => {
                const events = getVenueEvents(currentVenue.code, w);
                events.forEach(e => {
                    hasEvents = true;
                    const row = document.createElement('div');
                    row.className = 'history-row';
                    const dayName = weekData[w].days[e.di]?.abbr || '';
                    const dateStr = weekData[w].days[e.di]?.date || '';
                    const start = hours[e.start] || '';
                    const end = hours[e.end + 1] || add30min(hours[e.end]);
                    row.innerHTML = `
                        <span class="history-date">${dayName} ${dateStr}</span>
                        <span class="history-code">${e.code}</span>
                        <span>${e.cohort || ''}</span>
                        <span>${start} – ${end}</span>
                        <span class="history-status badge badge-${e.status}">${e.status}</span>
                    `;
                    panel.appendChild(row);
                });
            });

            if (!hasEvents) {
                panel.innerHTML = '<div class="hint-text">No bookings in the past 4 weeks</div>';
            }
        }

        /* build history when details is opened */
        var historyDetailsEl = document.getElementById('historyDetails');
        if (historyDetailsEl) {
            historyDetailsEl.addEventListener('toggle', function() {
                if (this.open) buildHistoryPanel();
            });
        }

        /* ════════════════════════════════════════════
           FILTERS
           ════════════════════════════════════════════ */

        function toggleVenueTypeDropdown() {
            document.getElementById('venueTypeDropdown').classList.toggle('open');
        }

        function applyVenueTypeFilter() {
            const checks = document.querySelectorAll('#venueTypeDropdown input[type="checkbox"]');
            venueTypeFilters = {};
            let allChecked = true;
            checks.forEach(cb => {
                venueTypeFilters[cb.value] = cb.checked;
                if (!cb.checked) allChecked = false;
            });
            document.getElementById('venueTypeBtn').innerHTML = (allChecked ? 'All Types' : 'Filtered') + ' &#9662;';
            buildVenueDropdown();
        }

        function resetFilters() {
            venueTypeFilters = { Tutorial: true, LectureHall: true, Lab: true };
            document.querySelectorAll('#venueTypeDropdown input[type="checkbox"]').forEach(cb => cb.checked = true);
            document.getElementById('venueTypeBtn').innerHTML = 'All Types &#9662;';
            document.getElementById('noMatchBanner').classList.remove('show');
            buildTimetable();
        }

        /* close venue type dropdown on outside click */
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.venue-type-filter')) {
                document.getElementById('venueTypeDropdown').classList.remove('open');
            }
        });

        /* ════════════════════════════════════════════
           GET VENUE EVENTS (from all cohorts)
           ════════════════════════════════════════════ */

        function getVenueEvents(venueCode, weekIndex) {
            const events = [];
            if (!MockData.cohortTimetable || !MockData.cohortTimetable.events) return events;

            MockData.cohortTimetable.events.forEach(function(item) {
                if (item.week !== weekIndex) return;
                const e = item.event;
                if (e.venue === venueCode) {
                    events.push({
                        ...e,
                        cohort: item.cohortId,
                    });
                }
            });

            return events;
        }

        /* ════════════════════════════════════════════
           TIMETABLE GRID BUILDER
           ════════════════════════════════════════════ */

        function buildTimetable() {
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            document.getElementById('hintText').style.display = 'none';
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('mobileCardList').style.display = 'none';
            document.getElementById('mobileCardList').innerHTML = '';

            if (!currentVenue) {
                document.getElementById('emptyState').style.display = 'flex';
                document.getElementById('emptyTitle').textContent = 'Select a venue';
                document.getElementById('emptyText').textContent = 'Choose a venue from the dropdown to view its weekly schedule.';
                updateSummaries([]);
                return;
            }

            const weekEvents = getVenueEvents(currentVenue.code, currentWeek);

            if (weekEvents.length === 0) {
                /* No classes booked — show hint */
                document.getElementById('hintText').style.display = 'flex';
            }

            const data = weekData[currentWeek];
            const days = data.days;

            /* ── Time header row ── */
            const timeHeaderRow = document.createElement('tr');
            const cornerTh = document.createElement('th');
            cornerTh.className = 'time-header-col';
            cornerTh.style.cssText = 'position: sticky; left: 0; z-index: 40;';
            cornerTh.innerHTML = '<span style="font-size:13px;font-weight:600;">Day / Time</span>';
            timeHeaderRow.appendChild(cornerTh);

            for (let i = 0; i < hours.length; i += 2) {
                const th = document.createElement('th');
                th.className = 'hour-header';
                th.colSpan = 2;
                th.innerHTML = `<span class="hour-top">${hours[i]}</span><span class="hour-bottom">${hours[i + 2] || add30min(hours[i + 1])}</span>`;
                timeHeaderRow.appendChild(th);
            }
            head.appendChild(timeHeaderRow);

            /* ── Day rows ── */
            days.forEach((day, di) => {
                const tr = document.createElement('tr');

                const dayTd = document.createElement('td');
                let dayColClass = 'time-col';
                if (day.today) dayColClass += ' today';
                if (day.holiday || day.sunday) dayColClass += ' offday';
                dayTd.className = dayColClass;
                dayTd.innerHTML = HtmlBuilder.dayHeader(day);
                tr.appendChild(dayTd);

                const dayEvents = weekEvents.filter(e => e.di === di);

                const slotMap = {};
                hours.forEach((_, hi) => { slotMap[hi] = null; });

                dayEvents.forEach(e => {
                    for (let hi = e.start; hi <= e.end; hi++) {
                        if (hi === e.start) {
                            slotMap[hi] = { event: e, span: e.end - e.start + 1 };
                        } else {
                            slotMap[hi] = { event: null, span: 0, occupied: true, status: e.status };
                        }
                    }
                });

                hours.forEach((h, hi) => {
                    const td = document.createElement('td');
                    let cellClass = 'hour-cell';
                    if (day.today) cellClass += ' today-cell';
                    if (day.sunday || day.holiday) cellClass += ' offday-slot';
                    td.className = cellClass;
                    td.dataset.day = di;
                    td.dataset.hour = hi;

                    const info = slotMap[hi];

                    if (day.sunday || day.holiday) {
                        /* Unavailable slot (holiday/Sunday) — always empty */
                        const div = document.createElement('div');
                        div.className = 'cell-content ' + (day.holiday ? 'cell-ph' : 'cell-sun');
                        td.appendChild(div);
                    } else if (info && info.event) {
                        const e = info.event;
                        const div = document.createElement('div');
                        div.className = 'cell-content';
                        if (e.status === 'pending') {
                            div.classList.add('cell-pending');
                        } else {
                            div.classList.add('cell-occupied');
                        }

                        td.appendChild(div);

                        /* mobile card */
                        const card = createEventCard(e, di);
                        document.getElementById('mobileCardList').appendChild(card);
                    } else if (info && info.occupied) {
                        const div = document.createElement('div');
                        div.className = 'cell-content';
                        if (info.status === 'pending') {
                            div.classList.add('cell-pending');
                        } else {
                            div.classList.add('cell-occupied');
                        }
                        td.appendChild(div);
                    } else {
                        /* Available slot */
                        const div = document.createElement('div');
                        div.className = 'cell-content cell-available';
                        div.tabIndex = 0;
                        div.dataset.day = di;
                        div.dataset.hour = hi;
                        div.setAttribute('role', 'button');
                        div.setAttribute('aria-label', `Available slot: ${days[di].abbr} ${hours[hi]}`);

                        div.addEventListener('click', function(ev) {
                            showAvailableTooltip(ev, di, hi);
                        });
                        td.appendChild(div);

                        /* mobile available card */
                        const mobileCard = document.createElement('div');
                        mobileCard.className = 'venue-available-card';
                        mobileCard.textContent = `${days[di].abbr} ${hours[hi]}`;
                        mobileCard.addEventListener('click', function() {
                            showAvailableTooltip(null, di, hi);
                        });
                        document.getElementById('mobileCardList').appendChild(mobileCard);
                    }

                    tr.appendChild(td);
                });

                body.appendChild(tr);
            });

            updateSummaries(weekEvents);
        }

        /* ════════════════════════════════════════════
           MOBILE CARD BUILDER
           ════════════════════════════════════════════ */

        function createEventCard(e, di) {
            const card = document.createElement('div');
            card.className = 'venue-event-card';
            const startTime = typeof to12h === 'function' ? to12h(hours[e.start]) : hours[e.start];
            const endTime = typeof to12h === 'function' ? to12h(hours[e.end + 1] || add30min(hours[e.end])) : hours[e.end + 1] || add30min(hours[e.end]);
            const statusClass = `badge-${e.status}`;
            card.innerHTML = `
                <div class="venue-event-header">
                    <span class="venue-event-code">${e.code}</span>
                    <span class="venue-event-status ${statusClass}">${e.status}</span>
                </div>
                <div class="venue-event-body">
                    <div class="venue-event-row"><span class="venue-event-label">Cohort</span><span class="venue-event-value">${e.cohort}</span></div>
                    <div class="venue-event-row"><span class="venue-event-label">Day</span><span class="venue-event-value">${weekData[currentWeek].days[di]?.abbr}</span></div>
                    <div class="venue-event-row"><span class="venue-event-label">Time</span><span class="venue-event-value">${startTime} – ${endTime}</span></div>
                </div>
            `;
            card.addEventListener('click', function() { openModal(e, di); });
            return card;
        }

        /* ════════════════════════════════════════════
           SUMMARY UPDATES
           ════════════════════════════════════════════ */

        function updateSummaries(events) {
            let occupied = 0;
            let pending = 0;

            events.forEach(e => {
                if (e.status === 'pending') pending++;
                else occupied++;
            });

            /* count available slots (Mon-Fri, 08:00-18:00) */
            let available = 0;
            const data = weekData[currentWeek];
            for (let di = 0; di < 5; di++) {
                if (data.days[di].holiday || data.days[di].sunday) continue;
                for (let hi = 0; hi < hours.length; hi++) {
                    const hasEvent = events.some(e => e.di === di && hi >= e.start && hi <= e.end);
                    if (!hasEvent) available++;
                }
            }

            document.getElementById('sumTotal').textContent = occupied + pending + available;
            document.getElementById('sumAvailable').textContent = available;
            document.getElementById('sumOccupied').textContent = occupied;
            document.getElementById('sumPending').textContent = pending;
        }

        /* ════════════════════════════════════════════
           MODAL (BOOKED CLASS)
           ════════════════════════════════════════════ */

        function openModal(e, di) {
            const modal = document.getElementById('eventModal');
            document.getElementById('modalTitle').textContent = 'Class Details';
            document.getElementById('mdlCourse').textContent = e.code;
            document.getElementById('mdlName').textContent = e.name || '—';
            document.getElementById('mdlLecturer').textContent = e.lecturer || '—';
            document.getElementById('mdlVenue').textContent = currentVenue ? `${currentVenue.code} — ${currentVenue.type} (${currentVenue.capacity} seats)` : e.venue;
            document.getElementById('mdlCohort').textContent = e.cohort || '—';

            const startTime = typeof to12h === 'function' ? to12h(hours[e.start]) : hours[e.start];
            const endTime = typeof to12h === 'function' ? to12h(hours[e.end + 1] || add30min(hours[e.end])) : hours[e.end + 1] || add30min(hours[e.end]);
            document.getElementById('mdlTime').textContent = `${startTime} – ${endTime}`;

            const statusBadge = document.getElementById('mdlStatusBadge');
            statusBadge.textContent = e.status;
            statusBadge.className = `badge badge-${e.status}`;
            document.getElementById('modalStatusBadge').textContent = e.status;
            document.getElementById('modalStatusBadge').className = `modal-status-badge badge-${e.status}`;

            document.getElementById('mdlRemarks').textContent = e.remarks || '—';

            modal.classList.add('open');
        }

        function closeModal() {
            document.getElementById('eventModal').classList.remove('open');
        }

        /* ════════════════════════════════════════════
           AVAILABLE SLOT TOOLTIP
           ════════════════════════════════════════════ */

        function showAvailableTooltip(ev, di, hi) {
            const tooltip = document.getElementById('availableTooltip');
            const dayName = weekData[currentWeek].days[di]?.abbr || '';
            const dateStr = weekData[currentWeek].days[di]?.date || '';
            const time = hours[hi] || '';

            document.getElementById('tooltipText').textContent =
                `Book ${currentVenue.code} on ${dayName}, ${dateStr} at ${time}?`;

            document.getElementById('tooltipBookBtn').onclick = function() {
                bookVenue(currentVenue.code, dateStr, time);
            };

            if (ev && ev.target) {
                const rect = ev.target.getBoundingClientRect();
                tooltip.style.left = rect.left + 'px';
                tooltip.style.top = (rect.bottom + 4) + 'px';
            } else {
                tooltip.style.left = '50%';
                tooltip.style.top = '50%';
                tooltip.style.transform = 'translate(-50%, -50%)';
            }

            tooltip.classList.add('show');
        }

        function hideAvailableTooltip() {
            document.getElementById('availableTooltip').classList.remove('show');
            document.getElementById('availableTooltip').style.transform = '';
        }

        function bookVenue(venueCode, date, time) {
            hideAvailableTooltip();
            let url = `/replacement-arrangement?venue=${encodeURIComponent(venueCode)}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}&from=venue-timetable`;
            if (currentCourseCode) url += `&code=${encodeURIComponent(currentCourseCode)}`;
            if (currentCohort) url += `&cohort=${encodeURIComponent(currentCohort)}`;
            window.location.href = url;
        }

        /* ════════════════════════════════════════════
           KEYBOARD NAVIGATION
           ════════════════════════════════════════════ */

        function onKeydown(e) {
            /* Escape closes tooltip/modal */
            if (e.key === 'Escape') {
                hideAvailableTooltip();
                closeModal();
                return;
            }

            /* B shortcut on available cell */
            if ((e.key === 'b' || e.key === 'B') && focusedCell && focusedCell.classList.contains('cell-available')) {
                const di = parseInt(focusedCell.dataset.day);
                const hi = parseInt(focusedCell.dataset.hour);
                showAvailableTooltip(null, di, hi);
                return;
            }

            /* Arrow navigation on grid */
            if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Enter'].includes(e.key)) {
                const active = document.activeElement;
                if (!active || !active.classList.contains('cell-available')) return;

                e.preventDefault();
                const cells = Array.from(document.querySelectorAll('.cell-available'));
                const idx = cells.indexOf(active);
                if (idx === -1) return;

                let next = idx;
                if (e.key === 'ArrowRight') next = Math.min(idx + 1, cells.length - 1);
                else if (e.key === 'ArrowLeft') next = Math.max(idx - 1, 0);
                else if (e.key === 'ArrowDown') next = Math.min(idx + 7, cells.length - 1);
                else if (e.key === 'ArrowUp') next = Math.max(idx - 7, 0);
                else if (e.key === 'Enter') {
                    showAvailableTooltip(null, parseInt(active.dataset.day), parseInt(active.dataset.hour));
                    return;
                }

                cells[next].focus();
                focusedCell = cells[next];
            }
        }

        /* ════════════════════════════════════════════
           FAVOURITES (localStorage)
           ════════════════════════════════════════════ */

        function getFavourites() {
            try { return JSON.parse(localStorage.getItem('venueFavourites') || '[]'); }
            catch (e) { return []; }
        }

        function toggleFavourite() {
            if (!currentVenue) return;
            const favs = getFavourites();
            const idx = favs.indexOf(currentVenue.code);
            if (idx === -1) {
                favs.push(currentVenue.code);
            } else {
                favs.splice(idx, 1);
            }
            localStorage.setItem('venueFavourites', JSON.stringify(favs));
            updateFavStar();
            buildVenueDropdown();
        }

        function updateFavStar() {
            const star = document.getElementById('favStar');
            if (!currentVenue) return;
            const favs = getFavourites();
            const isFav = favs.includes(currentVenue.code);
            star.textContent = isFav ? '\u2605' : '\u2606';
            star.classList.toggle('active', isFav);
        }

        /* ════════════════════════════════════════════
           RECENT VENUES (localStorage)
           ════════════════════════════════════════════ */

        function getRecent() {
            try { return JSON.parse(localStorage.getItem('venueRecent') || '[]'); }
            catch (e) { return []; }
        }

        function updateRecent(code) {
            let recent = getRecent();
            recent = recent.filter(c => c !== code);
            recent.unshift(code);
            if (recent.length > 5) recent = recent.slice(0, 5);
            localStorage.setItem('venueRecent', JSON.stringify(recent));
        }

        /* ════════════════════════════════════════════
           STATE PERSISTENCE (using ui-common.js helper)
           ════════════════════════════════════════════ */

        function saveState() {
            venueState.save({ venueTypes: venueTypeFilters });
        }

        function restoreState() {
            const state = venueState.restore();
            if (state.venue && MockData.venues.some(v => v.code === state.venue)) {
                document.getElementById('venueSelect').value = state.venue;
            }
            if (state.venueTypes) {
                venueTypeFilters = state.venueTypes;
                document.querySelectorAll('#venueTypeDropdown input[type="checkbox"]').forEach(cb => {
                    cb.checked = venueTypeFilters[cb.value] !== false;
                });
                const allChecked = Object.values(venueTypeFilters).every(v => v);
                document.getElementById('venueTypeBtn').innerHTML = (allChecked ? 'All Types' : 'Filtered') + ' &#9662;';
            }
        }

        /* ════════════════════════════════════════════
           TOAST (for slot-taken error)
           ════════════════════════════════════════════ */

        function showToast(message) {
            const toast = document.getElementById('toastNotification');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

@endsection
