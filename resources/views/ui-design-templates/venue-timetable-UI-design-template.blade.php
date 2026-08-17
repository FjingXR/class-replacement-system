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
        .venue-dropdown-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
            cursor: var(--cursor-cancel);
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
            .venue-bar .venue-dd-trigger {
                min-width: 0;
                width: 100%;
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
                @include('partials.ui-venue-dropdown', ['selectId' => 'venueSelect'])
                <button class="fav-btn" id="favStar" data-tip="Add to Favourites">&#9734;</button>
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
                ['color' => 'var(--color-tertiary-container)', 'label' => 'Pending', 'tip' => 'Replacement request awaiting approval'],
                ['color' => 'var(--color-error-container)', 'label' => 'Unavailable', 'tip' => 'Cannot book — slot is booked, Sunday, or public holiday'],
            ]
        ])

        <!-- ─── Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Slots',
                    'description' => 'All time slots shown for <strong>this venue</strong> in the selected week.'],
                ['class' => 'card-available', 'valueId' => 'sumAvailable', 'label' => 'Available',
                    'description' => '<strong>Free time slots</strong> that can be booked for this venue.'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending',
                    'description' => 'Slots held by <strong>replacement requests</strong> awaiting approval.'],
                ['class' => 'card-conflict', 'valueId' => 'sumUnavailable', 'label' => 'Unavailable',
                    'description' => '<strong>Cannot book</strong> — booked class, Sunday, or public holiday.'],
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
                    <button class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body" id="modalBody"></div>
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
        let venueDropdown = null;
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
           STATE PERSISTENCE (venue only)
           ════════════════════════════════════════════ */

        function saveState() {
            if (venueDropdown) localStorage.setItem('venueTimetableState', JSON.stringify({ venue: venueDropdown.getSelected() }));
        }
        function restoreState() {
            try {
                const state = JSON.parse(localStorage.getItem('venueTimetableState') || '{}');
                return state.venue || null;
            } catch (e) { return null; }
        }

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
            populateWeekSelect(weekSelect.id || 'weekSelect', {
                ranges: false,
                labelFn: function(w) { return w.label + ' — ' + w.range; }
            });

            /* default to today, then weekNav.load overrides if saved */
            currentWeek = currentWeekIndex();
            weekNav._currentWeek = currentWeek;
            weekNav.load();
            currentWeek = weekNav.currentWeek;

            /* restore venue from state or URL param */
            const savedVenue = restoreState();
            const preselect = window._preselectVenue || savedVenue;

            /* init venue dropdown */
            venueDropdown = new VenueDropdown(
                document.getElementById('venueSelectDropdown'),
                {
                    venues: MockData.venues,
                    initialCode: preselect || MockData.venues[0]?.code,
                    onSelect: function(code) { onVenueChange(); }
                }
            );

            /* trigger initial venue change to load timetable */
            onVenueChange();

            /* init favourite button */
            updateFavStar();
            document.getElementById('favStar').addEventListener('click', toggleFavourite);

            /* week nav */
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
            weekSelect.selectedIndex = currentWeek;

            /* today button — delegated to WeekNavigator.jumpToToday() which now saves internally */
            weekNav.initTodayBtn();

            /* show booking hint */
            document.getElementById('bookingHint').style.display = 'flex';

            /* keyboard nav */
            document.addEventListener('keydown', onKeydown);
            initWeekKeyboardShortcuts();
        });

        /* ════════════════════════════════════════════
           VENUE CHANGE
           ════════════════════════════════════════════ */

        function onVenueChange() {
            const code = venueDropdown ? venueDropdown.getSelected() : null;
            if (!code) return;

            currentVenue = MockData.venues.find(v => v.code === code);
            if (!currentVenue) return;

            /* update favourite star */
            updateFavStar();

            /* update recent */
            if (venueDropdown) venueDropdown.updateRecent(code);

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
            weekNav.prevWeek();
            currentWeek = weekNav.currentWeek;
        }

        function nextWeek() {
            weekNav.nextWeek();
            currentWeek = weekNav.currentWeek;
        }

        function selectWeek(index) {
            weekNav.selectWeek(parseInt(index, 10));
            currentWeek = weekNav.currentWeek;
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
            currentWeek = weekNav.currentWeek;
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

            buildTimetableGrid({
                events: weekEvents,
                days: days,
                cellRender: function(td, di, hi, day, info) {
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
                }
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
            /* Count exactly what the grid renders (same cells), so the stats
               always match the timetable — including overlapping bookings that
               share the same hour slot.
               Unavailable = Occupied + Sunday + Public Holiday (all 'cannot book').
               Falls back to 0 when no venue selected. */
            let occupied = 0;
            let pending = 0;
            let available = 0;
            if (currentVenue) {
                occupied = document.querySelectorAll('.timetable .cell-content.cell-occupied').length;
                pending = document.querySelectorAll('.timetable .cell-content.cell-pending').length;
                available = document.querySelectorAll('.timetable .cell-content.cell-available').length;
            }
            const sunday = document.querySelectorAll('.timetable .cell-content.cell-sun').length;
            const ph = document.querySelectorAll('.timetable .cell-content.cell-ph').length;
            const unavailable = occupied + sunday + ph;

            document.getElementById('sumTotal').textContent = available + pending + unavailable;
            document.getElementById('sumAvailable').textContent = available;
            document.getElementById('sumPending').textContent = pending;
            document.getElementById('sumUnavailable').textContent = unavailable;
        }

        /* ════════════════════════════════════════════
           MODAL (BOOKED CLASS)
           ════════════════════════════════════════════ */

        function openModal(e, di) {
            const startTime = typeof to12h === 'function' ? to12h(hours[e.start]) : hours[e.start];
            const endTime = typeof to12h === 'function' ? to12h(hours[e.end + 1] || add30min(hours[e.end])) : hours[e.end + 1] || add30min(hours[e.end]);
            const venueStr = currentVenue ? `${currentVenue.code} — ${currentVenue.type} (${currentVenue.capacity} seats)` : (e.venue || '—');

            DetailModal.render({
                modalId: 'eventModal',
                title: 'Class Details',
                subtitle: e.code + ' · ' + (e.name || ''),
                body: DetailModal.section('Class Information',
                    DetailModal.row('Subject Code', e.code, { strong: true }) +
                    DetailModal.row('Subject Name', e.name || '—') +
                    DetailModal.row('Lecturer', e.lecturer || '—') +
                    DetailModal.row('Venue', venueStr) +
                    DetailModal.row('Cohort', e.cohort || '—') +
                    DetailModal.row('Start Time', startTime, { strong: true }) +
                    DetailModal.row('End Time', endTime) +
                    DetailModal.row('Status', '<span class="badge badge-' + e.status + '">' + e.status + '</span>') +
                    DetailModal.row('Status Description', e.status === 'pending' ? 'Replacement request awaiting approval' : 'Class booked for this venue') +
                    DetailModal.row('Remarks', e.remarks || '—')
                )
            });
        }

        function closeModal() {
            DetailModal.close();
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

        /* close tooltip on scroll (cell position is no longer relevant) */
        window.addEventListener('scroll', hideAvailableTooltip, { passive: true });
        document.querySelector('.grid-scroll')?.addEventListener('scroll', hideAvailableTooltip, { passive: true });

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
           FAVOURITES (via VenueDropdown)
           ════════════════════════════════════════════ */

        // TODO: persist to DB instead of localStorage
        function toggleFavourite() {
            if (!currentVenue || !venueDropdown) return;
            venueDropdown.toggleFavourite(currentVenue.code, document.getElementById('favStar'));
        }

        function updateFavStar() {
            venueDropdown && venueDropdown.updateFavStar(document.getElementById('favStar'), currentVenue.code);
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
