@extends('layouts.ui-template', ['activeNav' => 'my-timetable', 'pageKey' => 'myTimetable'])

@section('title', 'My Timetable')

@section('page-styles')

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
        .btn-replace-now {
            padding: 10px 20px;
            border-radius: var(--radius-md);
            border: none;
            background: var(--color-error-container);
            color: var(--color-on-error-container);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
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
        .btn-replace-now:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        .btn-cancel-class {
            padding: 10px 20px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-error);
            background: transparent;
            color: var(--color-error);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background var(--transition), transform 0.15s;
        }
        .btn-cancel-class:hover {
            background: var(--color-error-container);
        }
        .btn-cancel-class:active {
            transform: scale(0.97);
        }
        .btn-cancel-class:focus-visible {
            outline: 2px solid var(--color-error);
            outline-offset: 2px;
        }

        /* ───── Cancel Confirmation Modal ───── */
        .cancel-overlay {
            position: fixed; inset: 0; z-index: 1000;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .cancel-modal {
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-xl);
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
            border-radius: var(--radius-md);
            border: 1px solid var(--color-outline-strong);
            background: var(--color-surface);
            color: var(--color-on-surface-variant);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background var(--transition), transform 0.15s;
        }
        .btn-cancel-secondary:hover {
            background: var(--color-surface-variant);
        }
        .btn-cancel-secondary:active {
            transform: scale(0.97);
        }
        .btn-cancel-secondary:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }
        .btn-cancel-danger {
            padding: 10px 20px;
            border-radius: var(--radius-md);
            border: none;
            background: var(--color-error);
            color: var(--color-on-error);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background var(--transition), transform 0.15s;
        }
        .btn-cancel-danger:hover {
            filter: brightness(1.1);
        }
        .btn-cancel-danger:active {
            transform: scale(0.97);
        }
        .btn-cancel-danger:focus-visible {
            outline: 2px solid var(--color-error);
            outline-offset: 2px;
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'My Timetable', 'description' => 'View your weekly class schedule and manage replacement requests across all cohorts.'])

        @include('partials.ui-guide-block', [
            'guideTitle' => 'How to use this page',
            'guideItems' => [
                '<strong>Week navigation</strong> — use arrows or Today button to browse weeks',
                '<strong>Slot status</strong> — Normal (green), Conflicted (red), Pending (amber), Approved (blue), Rejected (grey)',
                '<strong>Request replacement</strong> — click any conflicted slot to open the request form',
                '<strong>View details</strong> — click a normal/approved slot to see class details',
            ]
        ])

        <!-- ─── Semester Progress ─── -->
        <div class="semester-progress" id="semesterProgress">
            <div class="progress-label" id="progressLabel"></div>
            <div class="progress-track"><div class="progress-fill" id="progressFill"></div></div>
        </div>

        <!-- ─── Semester Bar ─── -->
        <div class="semester-bar">
            @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)'])
        </div>

        <!-- ─── Week Subtitle ─── -->
        <div class="week-subtitle" id="weekSubtitle"></div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table')
            @include('partials.ui-empty-state', ['title' => 'No classes this week', 'text' => 'All classes for this week have been cancelled.'])

        <!-- ─── Legend Bar ─── -->
        @include('partials.ui-legend-bar')

        <!-- ─── Weekly Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes'],
                ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Teaching Hours'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts'],
            ]
        ])

    <!-- ═══ Class Detail Modal ═══ -->
    @section('modal-footer')
        <div class="modal-footer-left">
            <button class="btn-replace-now" id="btnReplaceNow" style="display:none" onclick="goToReplacement(currentModalEvent?.code, currentModalEvent?.cohort, { day: currentModalEvent?.di, start: currentModalEvent?.start, end: currentModalEvent?.end, venue: currentModalEvent?.venue }, 'my-timetable')">Replace Now</button>
            <button class="btn-cancel-class" id="btnCancelClass" style="display:none" onclick="cancelClass()"></button>
        </div>
        <div class="modal-footer-right">
            <button class="btn-close-modal" onclick="closeModal()">Close</button>
        </div>
    @endsection
    @include('partials.ui-class-detail-modal')

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

    <!-- ═══ Copy Toast ═══ -->
    <div class="copy-toast" id="copyToast"></div>

@endsection

@section('page-scripts')

        const weekData = generateWeekData();

        const seedEvents = MockData.myTimetable.eventsByWeek[MockData.myTimetable.seedWeek];
        const weeklyTemplate = seedEvents.filter(e => e.status === 'normal');
        const eventsByWeek = {};
        for (let i = 0; i < MockData.semester.weeks; i++) {
            const explicit = MockData.myTimetable.eventsByWeek[i];
            if (explicit !== undefined) {
                eventsByWeek[i] = explicit;
            } else {
                eventsByWeek[i] = weeklyTemplate.slice();
            }
        }
        const eventsData = eventsByWeek;

        let currentWeek = currentWeekIndex();
        let currentModalEvent = null;

        const weekNav = new WeekNavigator(MockData.semester, weekData);
        weekNav._currentWeek = currentWeek;

        /* ───── Week persistence: keep the user's chosen week across refresh ───── */
        function loadSavedWeek() { weekNav.load(); currentWeek = weekNav.currentWeek; }
        function saveWeek() { weekNav.save(); }

        function openModal(event) {
            currentModalEvent = event;
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

            let timelineHtml = '';
            if (event.status === 'pending') {
                timelineHtml = '<div class="status-timeline">' +
                    '<div class="step completed">Submitted \u2713</div>' +
                    '<div class="step active">Under Review</div>' +
                    '<div class="step">Awaiting Replacement</div>' +
                '</div>';
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

            document.getElementById('modalBody').innerHTML = timelineHtml + fields.map(f =>
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
                toast.show('Class cancelled.', null);
            }
        }

        function closeModalOutside(e) {
            closeOnOverlayClick(e, closeModal);
        }

        closeOnEsc(closeModal);

        function buildTimetable() {
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            const tableEl = document.getElementById('timetable');
            const emptyEl = document.getElementById('emptyState');
            head.innerHTML = '';
            body.innerHTML = '';

            const data = weekData[currentWeek];
            const days = data.days;
            const events = eventsData[currentWeek] || [];

            if (events.length === 0) {
                tableEl.style.display = 'none';
                emptyEl.style.display = 'flex';
                updateSummary();
                return;
            }

            tableEl.style.display = '';
            emptyEl.style.display = 'none';

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

            days.forEach((day, di) => {
                const tr = document.createElement('tr');

                const dayTd = document.createElement('td');
                let dayColClass = 'time-col';
                if (day.today) dayColClass += ' today';
                if (day.holiday || day.sunday) dayColClass += ' offday';
                dayTd.className = dayColClass;
                dayTd.innerHTML = HtmlBuilder.dayHeader(day);
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
                    if (day.today) cellClass += ' today-cell';
                    if (day.sunday || day.holiday) cellClass += ' offday-slot';
                    td.className = cellClass;
                    td.dataset.day = di;
                    td.dataset.hour = hi;

                    const info = slotMap[hi];

                    if (info && info.event) {
                        const e = info.event;
                        const isConflict = day.holiday;
                        const div = document.createElement('div');
                        div.className = 'event-block span-' + info.span;
                        div.setAttribute('tabindex', '0');
                        div.__eventData = e;
                        div.dataset.name = e.name || '';
                        div.dataset.venue = e.venue || '';
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
            let replacement = 0, pending = 0, conflict = 0, hours = 0;

            events.forEach(e => {
                if (e.status === 'replacement') replacement++;
                if (e.status === 'pending') pending++;
                if (days[e.di] && days[e.di].holiday) conflict++;
                hours += (e.end - e.start + 1) * 0.5;
            });

            document.getElementById('sumTotal').textContent = total;
            document.getElementById('sumHours').textContent = (hours % 1 === 0 ? hours : hours.toFixed(1));
            document.getElementById('sumReplacement').textContent = replacement;
            document.getElementById('sumPending').textContent = pending;
            document.getElementById('sumConflict').textContent = conflict;
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
        }

        function prevWeek() {
            if (currentWeek > 0) {
                currentWeek--;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                updateWeekSubtitle();
                updateProgress();
                weekNav._currentWeek = currentWeek;
                weekNav.save();
            }
        }

        function nextWeek() {
            if (currentWeek < weekData.length - 1) {
                currentWeek++;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                updateWeekSubtitle();
                updateProgress();
                weekNav._currentWeek = currentWeek;
                weekNav.save();
            }
        }

        function selectWeek(index) {
            currentWeek = parseInt(index);
            buildTimetable();
            updateWeekSubtitle();
            updateProgress();
            weekNav._currentWeek = currentWeek;
            weekNav.save();
        }

        document.addEventListener('DOMContentLoaded', function() {
            weekNav.load();
            currentWeek = weekNav.currentWeek;
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            const sel = document.getElementById('weekSelect');
            const isMobile = window.innerWidth <= 768;
            sel.innerHTML = weekData.map((w, i) => {
                const label = isMobile
                    ? `${w.label} · ${w.rangeShort}`
                    : `${w.label} · ${w.range}`;
                return `<option value="${i}">${label}</option>`;
            }).join('');
            sel.selectedIndex = currentWeek;

            buildTimetable();
            updateWeekSubtitle();
            updateProgress();
        });

        initTodayBtn();
        initWeekKeyboardShortcuts();

        /* Override TODAY to use weekNav + persist */
        document.getElementById('todayBtn')?.addEventListener('click', function() {
            weekNav.jumpToToday();
            currentWeek = weekNav.currentWeek;
            weekNav.save();
        });

        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'SELECT' || document.getElementById('classModal').style.display === 'flex') return;
            if (e.key === 'ArrowLeft') { prevWeek(); }
            if (e.key === 'ArrowRight') { nextWeek(); }
            if (e.key === 'Enter' && e.target.classList.contains('event-block')) {
                const eventData = e.target.__eventData;
                if (eventData) openModal(eventData);
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('ev-code')) {
                navigator.clipboard.writeText(e.target.textContent).then(function() {
                    const toast = document.getElementById('copyToast');
                    toast.textContent = 'Copied ' + e.target.textContent;
                    toast.classList.add('show');
                    setTimeout(function() { toast.classList.remove('show'); }, 1500);
                });
            }
        });

        // Mobile swipe gestures for week navigation
        if (window.innerWidth <= 768) {
            const gridScroll = document.querySelector('.grid-scroll');
            if (gridScroll) {
                initSwipeGesture({
                    element: gridScroll,
                    onSwipeLeft: () => {
                        const nextBtn = document.querySelector('[onclick*="nextWeek"], [data-action="next"]');
                        if (nextBtn) nextBtn.click();
                    },
                    onSwipeRight: () => {
                        const prevBtn = document.querySelector('[onclick*="prevWeek"], [data-action="prev"]');
                        if (prevBtn) prevBtn.click();
                    }
                });
            }
        }
@endsection
