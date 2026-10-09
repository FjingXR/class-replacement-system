@extends('layouts.ui-template', [
        'homeUrl' => '/my-timetable-ui','activeNav' => 'venue-timetable', 'pageKey' => 'venueTimetable'])

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
            color: var(--color-on-primary);
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
            /* the card list is the mobile booking surface — the desktop grid
               cannot fit a phone viewport */
            body[data-page='venueTimetable'] #timetable { display: none; }
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
                color: var(--color-on-primary);
                border-radius: var(--radius-sm);
                padding: 12px;
                cursor: pointer;
                text-align: center;
                font-weight: 600;
            }
            /* F-6 (round-2): day separator above each day's run of slot cards */
            .m-slot-day {
                font-size: 11px;
                font-style: italic;
                color: var(--color-on-surface-variant);
                opacity: 0.7;
                margin: 10px 2px 2px;
                padding-left: 2px;
            }
            .m-slot-day:first-child { margin-top: 0; }
            .summary-bar {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
        }

        /* ───── Available slot hover label ───── */
        .cell-available { --hover-label: 'Book ?'; }
        /* too late in the day for a full booking — hover says so instead */
        .cell-available.cell-no-fit { --hover-label: 'Not bookable'; cursor: not-allowed; }
        /* booked classes open their details on click */
        .cell-content.cell-has-details { cursor: pointer; }



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
                '<strong>Slot status</strong> — Normal (green), Conflicted (red), Pending (amber)',
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
            <button class="print-btn" title="Coming soon" onclick="toast.show('Printing is coming soon')" style="margin-left:auto;">
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
            <span>Click any green empty slot to book this venue</span>
        </div>

        <!-- ─── Lead-Time Notice (contextual: shown while the viewed week is blocked) ─── -->
        <div class="lead-time-note" id="leadTimeNote" style="display:none"></div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table')

        <!-- ─── Hint Text (empty grid) ─── -->
        <div class="hint-text" id="hintText" style="display:none">All slots available — this venue is free all week</div>

        <!-- ─── Legend Bar ─── -->
        @include('partials.ui-legend-bar', [
            'ownershipHint' => true,
            'items' => [
                ['color' => 'var(--color-success-container)', 'label' => 'Available', 'tip' => 'Free slot — click to book this venue (Sunday, holiday and lead-time slots can\'t be booked)'],
                ['class' => 'event-normal', 'label' => 'Normal', 'tip' => 'Scheduled class with no issues (replacement sessions fold in here on this page)'],
                ['class' => 'event-pending', 'label' => 'Pending', 'tip' => 'Replacement request awaiting PL approval'],
                ['class' => 'event-conflict', 'label' => 'Conflict / Public Holiday', 'tip' => 'This class will not run as scheduled — scheduling conflict or public holiday (remaining holiday slots show as empty \'PH\' cells)'],
            ]
        ])

        {{-- ─── Summary Bar — restored (venue-event-blocks §5). Counts stay
             grid-equivalent via the span-weighted updateSummaries() below. --}}
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Slots',
                    'description' => 'All time slots shown for <strong>this venue</strong> in the selected week.'],
                ['class' => 'card-available', 'valueId' => 'sumAvailable', 'label' => 'Available',
                    'description' => '<strong>Free time slots</strong> that can be booked for this venue.'],
                ['class' => 'card-replacement', 'valueId' => 'sumMyClasses', 'label' => 'My Teaching Classes',
                    'description' => 'Sessions <strong>you teach</strong> in this venue this week — <strong>each class counts separately</strong>.'],
                ['class' => 'card-hours', 'valueId' => 'sumMyHours', 'label' => 'My Teaching Hours',
                    'description' => 'Total hours of <strong>your classes</strong> in this venue this week (each slot = <strong>30 minutes</strong>).'],
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

        <!-- ─── Cancel Class Confirm Modal (shared partial — cancel-class-enhancement) ─── -->
        @include('partials.ui-cancel-class-modal')

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

        /* ════════════════════════════════════════════
           WEEK NAVIGATION (shared WeekNavigator)
           ════════════════════════════════════════════ */

        const weekNav = new WeekNavigator(MockData.semester, weekData, null, 'venueTimetableWeek');

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
                ranges: false
                /* labels = shared default ("Week N · DD Mon YYYY ~ DD Mon YYYY"),
                   matching the arrangement page's week select */
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

            /* resolve first — an unknown/stale code must not clobber the
               currently selected venue */
            const venueObj = MockData.venues.find(v => v.code === code);
            if (!venueObj) return;
            currentVenue = venueObj;

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
                        <span class="history-status badge badge-${e.status}">${StatusText.label(e.status)}</span>
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
            /* Combined-lecture twins (same venue, same di+start, one row per
               cohort) used to be LAST-WIN in the grid slotMap (ui-common.js),
               so a normal twin could overwrite a conflict twin and the slot
               rendered green. Merge them here: status by severity
               (conflict > pending > replacement > normal), cohorts joined,
               students summed. */
            const SEVERITY = { conflict: 3, pending: 2, replacement: 1, normal: 0 };
            const byKey = {};
            const order = [];
            if (!MockData.cohortTimetable || !MockData.cohortTimetable.events) return order;

            MockData.cohortTimetable.events.forEach(function(item) {
                if (item.week !== weekIndex) return;
                const e = item.event;
                if (e.venue !== venueCode || e.status === 'cancelled') return;
                const ev = Object.assign({}, e, {
                    cohort: e.cohort || item.cohortId,
                    cohorts: [e.cohort || item.cohortId],   // per-cohort list → Total Students lookup
                });
                const key = ev.di + ':' + ev.start;
                const cur = byKey[key];
                if (!cur) {
                    byKey[key] = ev;
                    order.push(key);
                    return;
                }
                if ((SEVERITY[ev.status] || 0) > (SEVERITY[cur.status] || 0)) cur.status = ev.status;
                if (cur.cohort.indexOf(ev.cohort) === -1) cur.cohort += ' + ' + ev.cohort;
                if (cur.cohorts.indexOf(ev.cohorts[0]) === -1) cur.cohorts.push(ev.cohorts[0]);
                if (!cur.requestedAt && ev.requestedAt) cur.requestedAt = ev.requestedAt;
                if (!cur.requestedBy && ev.requestedBy) cur.requestedBy = ev.requestedBy;
                if (!cur.remarks && ev.remarks) cur.remarks = ev.remarks;
            });

            return order.map(function(k) { return byKey[k]; });
        }

        /* cohortTimetable.events carry no studentCount — resolve the total from
           the course registry (sum of each merged cohort's count; course total
           as fallback). */
        function venueTotalStudents(e) {
            if (e.studentCount) return e.studentCount;
            const course = (MockData.courses || []).find(function(c) { return c.code === e.code; });
            if (!course) return '—';
            let sum = 0, matched = false;
            (e.cohorts || []).forEach(function(cn) {
                const i = course.cohorts.indexOf(cn);
                if (i !== -1) { sum += course.cohortCounts[i] || 0; matched = true; }
            });
            return matched ? sum : (course.studentCount != null ? course.studentCount : '—');
        }

        /* ════════════════════════════════════════════
           TIMETABLE GRID BUILDER
           ════════════════════════════════════════════ */

        function buildTimetable() {
            /* the grid is about to be replaced — a live Book tooltip would go
               stale, and so would the tracked keyboard cell */
            hideAvailableTooltip();
            focusedCell = null;
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

            /* F-6 (round-2): the mobile slot list is long (every 30-min
               bookable start = one card) — group the cards under a small day
               header, re-emitted with each builder pass so week/venue swaps
               stay consistent (list itself is cleared above). */
            let lastSlotDay = null;
            function mobileSlotDayHeader(di) {
                if (di === lastSlotDay) return;
                lastSlotDay = di;
                const hd = document.createElement('div');
                hd.className = 'm-slot-day';
                hd.textContent = days[di].abbr + ' · ' + days[di].date;
                document.getElementById('mobileCardList').appendChild(hd);
            }

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
                    /* PH-day exception: the logged-in lecturer's OWN classes
                       still render as loud red blocks on public-holiday days
                       (parity with my-timetable — "your class won't run");
                       everyone else's stay empty 'PH' cells. */
                    const offMine = info && info.event && day.holiday &&
                                    info.event.lecturer === MockData.currentUser.name;
                    if ((day.sunday || day.holiday) && !offMine) {
                        /* Unavailable slot (holiday/Sunday) — always empty */
                        const div = document.createElement('div');
                        div.className = 'cell-content ' + (day.holiday ? 'cell-ph' : 'cell-sun');
                        td.appendChild(div);
                    } else if (info && info.event) {
                        const e = info.event;
                        const div = document.createElement('div');
                        div.className = 'event-block span-' + info.span;   // span = e.end - e.start + 1 (half-hours)
                        div.setAttribute('tabindex', '0');
                        div.setAttribute('role', 'button');
                        div.setAttribute('aria-label', `View class details: ${e.code} ${hours[hi]}`);
                        div._evt = e; div._di = di; div.__eventData = e;   // parity with default builder
                        div.dataset.name  = e.name  || '';
                        div.dataset.venue = e.venue || '';
                        /* `.event-block::after` tooltip: "name · lecturer · status"
                           — parity with the shared builder's uniform format. */
                        div.dataset.tip2 = (e.lecturer || '—') + ' · ' + eventStatusLabel(e, day.holiday);
                        var isMine = e.lecturer === MockData.currentUser.name;
                        if (day.holiday) {
                            /* own class on a public holiday — loud red, it won't run */
                            div.classList.add('event-conflict');
                        } else if (e.status === 'pending') {
                            div.classList.add(isMine ? 'event-mine-pending' : 'event-others-pending');
                        } else if (e.status === 'conflict') {
                            // Own conflicts get the loud 3px red border; others' the same red with a hairline (owner-gated, parity with cohort)
                            div.classList.add(isMine ? 'event-conflict' : 'event-public-holiday');
                        } else {
                            div.classList.add(isMine ? 'event-mine' : 'event-others');
                        }
                        const startTime = to12h(hours[e.start]);
                        const endTime   = to12h(hours[e.end + 1] || add30min(hours[e.end]));
                        div.innerHTML =
                            '<span class="ev-code">' + e.code + '(' + e.type + ')</span>' +
                            '<span class="ev-venue">' + e.venue + '</span>' +
                            '<span class="ev-time">' + startTime + ' - ' + endTime + '</span>' +
                            buildReplacementNote(e, {
                                checkOwnership: function (ev) { return ev.lecturer === MockData.currentUser.name; }
                            });
                        div.addEventListener('click', function () { openModal(e, di); });
                        div.addEventListener('focus', function () { focusedCell = div; });
                        td.appendChild(div);
                        if (info.span > 1) td.colSpan = info.span;   // parity: builder sets it only when span > 1
                        // mobile booked card — UNCHANGED
                        mobileSlotDayHeader(di);
                        document.getElementById('mobileCardList').appendChild(createEventCard(e, di));
                    } else if (info && info.occupied) {
                        td.style.display = 'none';                          // continuation consumed by head's colSpan
                    } else if (isSlotTooSoon(weekData, currentWeek, di)) {
                        /* lead-time rule: inside the 3-working-day window —
                           rendered read-only (no Book tooltip, no keyboard) */
                        const div = document.createElement('div');
                        div.className = 'cell-content cell-too-soon';
                        td.appendChild(div);
                    } else {
                        /* Available slot */
                        const div = document.createElement('div');
                        div.className = 'cell-content cell-available';
                        /* a booking can't fit if it would run past the day end */
                        if (hi + BOOK_SPAN > hours.length) div.classList.add('cell-no-fit');
                        div.tabIndex = 0;
                        div.dataset.day = di;
                        div.dataset.hour = hi;
                        div.setAttribute('role', 'button');
                        div.setAttribute('aria-label', `Available slot: ${days[di].abbr} ${hours[hi]}`);

                        div.addEventListener('click', function(ev) {
                            focusedCell = div;
                            showAvailableTooltip(ev, di, hi);
                        });
                        div.addEventListener('focus', function() { focusedCell = div; });
                        td.appendChild(div);

                        /* mobile available card */
                        mobileSlotDayHeader(di);
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

            /* mobile: the card list IS the booking surface — reveal it after a
               rebuild (the builder hides it first to clear stale cards) */
            const mcl = document.getElementById('mobileCardList');
            if (mcl) mcl.style.display = (mcl.children.length && window.matchMedia('(max-width: 768px)').matches) ? '' : 'none';
            /* keep it honest across viewport resizes (no rebuild needed).
               De-duped: buildTimetable runs on every venue/week change, and
               re-adding an anonymous listener here used to stack one copy per
               rebuild (resize ran N redraws after N venue visits). */
            if (window.__venueResizeHandler) window.removeEventListener('resize', window.__venueResizeHandler);
            window.__venueResizeHandler = function() {
                const mcl = document.getElementById('mobileCardList');
                if (mcl) mcl.style.display = (mcl.children.length && window.matchMedia('(max-width: 768px)').matches) ? '' : 'none';
            };
            window.addEventListener('resize', window.__venueResizeHandler);

            /* lead-time banner (contextual: shown while the viewed week is blocked) */
            renderLeadTimeNote('leadTimeNote', weekData, currentWeek);

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
                    <span class="venue-event-status ${statusClass}">${StatusText.label(e.status)}</span>
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
            /* The four summary cards are restored on this page (design §5).
               Null-guard kept so the rebuild never touches a missing node. */
            if (!document.getElementById('sumTotal')) return;
            let occupied = 0, pending = 0, available = 0, tooSoon = 0;
            if (currentVenue && events) {
                available = document.querySelectorAll('.timetable .cell-content.cell-available').length;
                tooSoon   = document.querySelectorAll('.timetable .cell-content.cell-too-soon').length;
                const days = weekData[currentWeek].days;
                const heads = {};                              // last-write-wins = grid slotMap semantics
                events.forEach(function (e) {
                    if (days[e.di] && (days[e.di].sunday || days[e.di].holiday)) return;  // non-offday only
                    heads[e.di + ':' + e.start] = e;           // iterate weekEvents order — LAST wins
                });
                Object.keys(heads).forEach(function (k) {
                    const e = heads[k], span = e.end - e.start + 1;
                    if (e.status === 'pending') pending += span; else occupied += span;
                });
            }
            const sunday = document.querySelectorAll('.timetable .cell-content.cell-sun').length;
            const ph     = document.querySelectorAll('.timetable .cell-content.cell-ph').length;
            const unavailable = occupied + sunday + ph + tooSoon;
            document.getElementById('sumTotal').textContent = available + pending + unavailable;
            document.getElementById('sumAvailable').textContent   = available;
            document.getElementById('sumUnavailable').textContent = unavailable;

            /* My Teaching cards — offday events are skipped because this
               grid renders them as PH cells, never as blocks (the same
               grid-equivalence rule the slot counters above follow). */
            const onGrid = (events || []).filter(function (e) {
                const d = weekData[currentWeek].days[e.di];
                return d && !d.sunday && !d.holiday;
            });
            const my = myTeachingStats(onGrid);
            document.getElementById('sumMyClasses').textContent = my.classes;
            document.getElementById('sumMyHours').textContent   = (my.hours % 1 === 0 ? my.hours : my.hours.toFixed(1));
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
                tabs: [
                    { key: 'session', label: 'Session', html: DetailModal.section('Session',
                        DetailModal.row('Subject Code', e.code, { strong: true }) +
                        DetailModal.row('Subject Name', e.name || '—') +
                        DetailModal.row('Lecturer', e.lecturer || '—') +
                        DetailModal.row('Cohort', e.cohort || '—') +
                        DetailModal.row('Total Students', venueTotalStudents(e)) +
                        DetailModal.row('Start Time', startTime, { strong: true }) +
                        DetailModal.row('End Time', endTime)
                    ) },
                    { key: 'venue-status', label: 'Venue & Status', html: DetailModal.section('Venue & Status',
                        DetailModal.row('Venue', venueStr) +
                        DetailModal.row('Status', '<span class="badge badge-' + e.status + '">' + StatusText.label(e.status) + '</span>') +
                        DetailModal.row('Status Description', e.status === 'conflict' ? 'Scheduling conflict — needs attention' : e.status === 'pending' ? 'Replacement request awaiting approval' : 'Class booked for this venue') +
                        DetailModal.row('Remarks', e.remarks || '—')
                    ) },
                ]
            });

            // Cancel Class? button (shared modal — cancel-class-enhancement §5):
            // self-hides via ClassCancellation.isCancellable (S1–S5); the Later
            // path closes the event modal and rebuilds the grid, so the
            // cancelled block vanishes and the slot turns available again.
            CancelClass.renderButton(
                document.querySelector('#eventModal .modal-footer'),
                e, weekData[currentWeek].days, currentWeek,
                function() {
                    closeModal();
                    buildTimetable();
                }
            );
        }

        function closeModal() {
            DetailModal.close();
        }

        /* ════════════════════════════════════════════
           AVAILABLE SLOT TOOLTIP
           ════════════════════════════════════════════ */

        /* Slots one booking spans on the arrangement page (its default
           MAX_SELECTION: 4 half-hour slots = 2h). A booking starting too late in
           the day can't fit — don't offer Book on those cells. */
        const BOOK_SPAN = 4;

        function showAvailableTooltip(ev, di, hi) {
            if (hi + BOOK_SPAN > hours.length) {
                toast.show('A booking needs ' + (BOOK_SPAN * 30) + ' minutes — not enough time left in the day.');
                return;
            }
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
            /* a fresh Book click is a new intent: if THIS slot was previously
               cancelled (auto-select discarded) or its banner dismissed, start clean */
            const bookingKey = venueCode + '|' + date + '|' + time;
            try {
                if (sessionStorage.getItem('bookingIntentCancelled') === bookingKey) sessionStorage.removeItem('bookingIntentCancelled');
                if (sessionStorage.getItem('bookingIntentDismissed') === bookingKey) sessionStorage.removeItem('bookingIntentDismissed');
            } catch (e) {}
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

            /* Arrow navigation on grid (available AND booked cells are focusable) */
            if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Enter'].includes(e.key)) {
                const active = document.activeElement;
                if (!active || !(active.classList.contains('cell-content') || active.classList.contains('event-block')) || active.tabIndex !== 0) return;

                e.preventDefault();
                const cells = Array.from(document.querySelectorAll('.cell-content[tabindex="0"], .event-block[tabindex="0"]'));
                const idx = cells.indexOf(active);
                if (idx === -1) return;

                let next = idx;
                if (e.key === 'ArrowRight') next = Math.min(idx + 1, cells.length - 1);
                else if (e.key === 'ArrowLeft') next = Math.max(idx - 1, 0);
                else if (e.key === 'ArrowDown') next = Math.min(idx + 7, cells.length - 1);
                else if (e.key === 'ArrowUp') next = Math.max(idx - 7, 0);
                else if (e.key === 'Enter') {
                    /* booked cells open their details; available cells the Book tooltip */
                    if (active._evt) openModal(active._evt, active._di);
                    else if (active.classList.contains('cell-available')) {
                        showAvailableTooltip(null, parseInt(active.dataset.day), parseInt(active.dataset.hour));
                    }
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
