@extends('layouts.ui-template', [
    'activeNav' => 'my-timetable',
    'pageKey' => 'studentMyTimetable',
    'navItems' => [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard'],
        ['key'=>'my-timetable','label'=>'My Timetable','href'=>'/student-my-timetable-ui'],
        ['key'=>'replacement-history','label'=>'Replacement History','href'=>'/my-request-history-ui'],
    ],
    'notifCount' => 3,
])

@section('title', 'My Timetable')

@section('content')

        <!-- ─── Page Header ─── -->
        <div class="page-header">
            <h1 class="page-title">My Timetable</h1>
            <div class="page-chips">
                <span class="semester-chip" id="semesterChip"></span>
                <span class="semester-chip">RSD3(S1)G2</span>
            </div>
            <p class="page-desc">View your weekly class schedule across all sessions.</p>
        </div>

        <!-- ─── Semester Progress ─── -->
        <div class="semester-progress" id="semesterProgress">
            <div class="progress-label" id="progressLabel"></div>
            <div class="progress-track"><div class="progress-fill" id="progressFill"></div></div>
        </div>

        <!-- ─── Semester Bar ─── -->
        <div class="semester-bar">
            <button class="week-arrow" onclick="prevWeek()" aria-label="Previous week">&#8249;</button>
            <select class="week-select" id="weekSelect" onchange="selectWeek(this.value)"></select>
            <button class="week-arrow" onclick="nextWeek()" aria-label="Next week">&#8250;</button>
            <button class="today-btn" id="todayBtn" title="Jump to current week" aria-label="Jump to current week">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <circle cx="12" cy="12" r="6"/>
                    <circle cx="12" cy="12" r="2"/>
                </svg>
                Today
            </button>
        </div>

        <!-- ─── Week Subtitle ─── -->
        <div class="week-subtitle" id="weekSubtitle"></div>

        <!-- ─── Grid Wrapper ─── -->
        <div class="grid-wrapper">
            <div class="grid-scroll" id="gridScroll">
                <table class="timetable" id="timetable">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
            <div class="empty-state" id="emptyState" style="display:none;">
                <div class="empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                        <line x1="10" y1="14" x2="14" y2="18"/>
                        <line x1="14" y1="14" x2="10" y2="18"/>
                    </svg>
                </div>
                <div class="empty-title">No classes this week<br>All classes for this week have been cancelled.</div>
            </div>
        </div>

        <!-- ─── Legend Bar ─── -->
        @include('partials.ui-legend-bar')

        <!-- ─── Weekly Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes'],
                ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Class Hours'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts'],
            ]
        ])

    <!-- ═══ View-Only Modal ═══ -->
    <div class="modal-overlay" id="classModal" style="display:none" onclick="closeModalOutside(event)">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle">Class Details</span>
                <span class="modal-status-badge" id="modalStatusBadge">Normal</span>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer" style="justify-content:flex-end;">
                <button class="btn-close-modal" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- ═══ Copy Toast ═══ -->
    <div class="copy-toast" id="copyToast"></div>

@endsection

@section('page-scripts')
        const dayNames = ['MONDAY', 'TUESDAY', 'WEDNESAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
        const dayNamesCorrect = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];

        function fmt(d) {
            return String(d.getDate()).padStart(2, '0') + ' ' + d.toLocaleString('en', { month: 'short' }) + ' ' + d.getFullYear();
        }

        function fmtShort(d) {
            return String(d.getDate()).padStart(2, '0') + ' ' + d.toLocaleString('en', { month: 'short' });
        }

        const weekData = (function() {
            const start = new Date(MockData.semester.startDate);
            start.setHours(0, 0, 0, 0);
            const arr = [];
            for (let w = 0; w < MockData.semester.weeks; w++) {
                const ms = start.getTime() + w * 7 * 86400000;
                const startDate = new Date(ms);
                const endDate = new Date(ms + 6 * 86400000);
                const days = [];
                for (let d = 0; d < 7; d++) {
                    const dt = new Date(ms + d * 86400000);
                    let holiday = false;
                    let holidayLabel = '';
                    MockData.holidays.forEach(function(h) {
                        if (h.week === w + 1 && h.dayIndex === d) {
                            holiday = true;
                            holidayLabel = h.label;
                        }
                    });
                    days.push({
                        abbr: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][d],
                        date: fmt(dt),
                        sunday: d === 6,
                        today: (function() { var t = new Date('2026-09-17'); t.setHours(0,0,0,0); return dt.getTime() === t.getTime(); })(),
                        holiday: holiday,
                        holidayLabel: holidayLabel,
                    });
                }
                arr.push({
                    label: 'Week ' + (w + 1),
                    start: startDate,
                    end: endDate,
                    range: fmt(startDate) + ' ~ ' + fmt(endDate),
                    rangeShort: fmtShort(startDate) + ' ~ ' + fmtShort(endDate),
                    days: days,
                });
            }
            return arr;
        })();

        const eventsByWeek = {};
        for (let w = 0; w < MockData.semester.weeks; w++) {
            eventsByWeek[w] = MockData.cohortTimetable.rsd3g2Base.map(function(c) {
                return Object.assign({}, c, { status: 'normal', remarks: '', meta: {} });
            });
        }
        Object.entries(MockData.cohortTimetable.rsd3g2Flags).forEach(function(entry) {
            const w = entry[0];
            const list = entry[1];
            list.forEach(function(flag) {
                const code = flag[0];
                const status = flag[1];
                const remarks = flag[2];
                const evArr = eventsByWeek[w];
                if (evArr) {
                    const ev = evArr.find(function(e) { return e.code === code; });
                    if (ev) {
                        ev.status = status;
                        ev.remarks = remarks;
                        if (status === 'pending') {
                            ev.requestedAt = '01 Sep 2026, 09:15 AM';
                            ev.requestedBy = ev.lecturer;
                        }
                    }
                }
            });
        });

        function getVisibleEvents(weekIdx) {
            const weekEvents = eventsByWeek[weekIdx] || [];
            return weekEvents.filter(function(e) {
                if (e.status === 'cancelled') return false;
                const cancelled = MockData.studentTimetable.cancelledFlags[weekIdx] || [];
                return cancelled.indexOf(e.code) === -1;
            });
        }

        function currentWeekIndex() {
            const semesterStart = new Date(MockData.semester.startDate);
            const today = new Date('2026-09-17');
            today.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);
            const idx = Math.floor((today - semesterStart) / 86400000 / 7);
            return Math.max(0, Math.min(weekData.length - 1, idx));
        }
        let currentWeek = currentWeekIndex();

        const WEEK_KEY = 'studentMyTimetableWeek';
        function loadSavedWeek() {
            const saved = parseInt(localStorage.getItem(WEEK_KEY));
            if (!isNaN(saved) && saved >= 0 && saved < weekData.length) {
                currentWeek = saved;
            }
        }
        function saveWeek() {
            try { localStorage.setItem(WEEK_KEY, String(currentWeek)); } catch (e) {}
        }

        function updateWeekSubtitle() {
            const el = document.getElementById('weekSubtitle');
            if (el) {
                el.textContent = 'Week ' + (currentWeek + 1) + ' of ' + MockData.semester.weeks + ' \u00B7 ' + fmt(weekData[currentWeek].start) + ' \u00B7 ' + fmt(weekData[currentWeek].end);
            }
        }

        function updateProgress() {
            const pct = ((currentWeek + 1) / MockData.semester.weeks) * 100;
            const fill = document.getElementById('progressFill');
            const label = document.getElementById('progressLabel');
            if (fill) fill.style.width = pct + '%';
            if (label) label.textContent = 'Week ' + (currentWeek + 1) + ' of ' + MockData.semester.weeks;
        }

        function buildWeekOptions() {
            const sel = document.getElementById('weekSelect');
            var isMobile = window.innerWidth <= 768;
            sel.innerHTML = weekData.map(function(w, i) {
                var label = isMobile
                    ? 'Week ' + (i + 1) + ' \u00B7 ' + w.rangeShort
                    : 'Week ' + (i + 1) + ' \u00B7 ' + fmt(w.start) + ' ~ ' + fmt(w.end);
                return '<option value="' + i + '">' + label + '</option>';
            }).join('');
            sel.selectedIndex = currentWeek;
        }

        function openModal(event) {
            document.getElementById('modalTitle').textContent = event.code || 'Class Details';

            const days = weekData[currentWeek].days;
            const isConflict = days[event.di] && days[event.di].holiday;
            const displayStatus = isConflict ? 'conflict' : event.status;

            const badge = document.getElementById('modalStatusBadge');
            badge.textContent = displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1);
            badge.className = 'modal-status-badge ' + displayStatus;

            const startStr = to12h(hours[event.start]);
            const endStr = to12h(hours[event.end + 1] || add30min(hours[event.end]));

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
                { label: 'Venue', value: event.venue || '\u2014' },
                { label: 'Day', value: dayNamesCorrect[event.di] },
                { label: 'Date', value: weekData[currentWeek].days[event.di].date },
                { label: 'Time', value: startStr + ' \u2013 ' + endStr },
                { label: 'Status', value: displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1) },
                { label: 'Remarks', value: event.remarks || '\u2014' },
            ];

            if (event.status === 'pending') {
                fields.splice(fields.length - 1, 0,
                    { label: 'Requested At', value: event.requestedAt || '\u2014' },
                    { label: 'Requested By', value: event.requestedBy || '\u2014' }
                );
            }

            const fieldsHtml = fields.map(function(f) {
                return '<div class="modal-field">' +
                    '<span class="field-label">' + f.label + '</span>' +
                    '<span class="field-value">' + f.value + '</span>' +
                '</div>';
            }).join('');

            document.getElementById('modalBody').innerHTML = timelineHtml + fieldsHtml;
            document.getElementById('classModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('classModal').style.display = 'none';
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
            const visibleEvents = getVisibleEvents(currentWeek);

            if (visibleEvents.length === 0) {
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
                th.innerHTML = '<span class="hour-top">' + hours[i] + '</span><span class="hour-bottom">' + (hours[i + 2] || add30min(hours[i + 1])) + '</span>';
                timeHeaderRow.appendChild(th);
            }
            head.appendChild(timeHeaderRow);

            days.forEach(function(day, di) {
                const tr = document.createElement('tr');

                const dayTd = document.createElement('td');
                let dayColClass = 'time-col';
                if (day.today) dayColClass += ' today';
                if (day.holiday || day.sunday) dayColClass += ' offday';
                dayTd.className = dayColClass;
                let dayHtml = '<span class="day-label">' + day.abbr + '</span><span class="date-label">' + day.date + '</span>';
                if (day.holiday) {
                    dayHtml += '<span class="holiday-label">' + (day.holidayLabel || 'Public Holiday') + '</span>';
                }
                dayTd.innerHTML = dayHtml;
                tr.appendChild(dayTd);

                const dayEvents = visibleEvents.filter(function(e) { return e.di === di; });

                const slotMap = {};
                hours.forEach(function(_, hi) { slotMap[hi] = null; });

                dayEvents.forEach(function(e) {
                    for (let hi = e.start; hi <= e.end; hi++) {
                        if (hi === e.start) {
                            slotMap[hi] = { event: e, span: e.end - e.start + 1 };
                        } else {
                            slotMap[hi] = { event: null, span: 0, occupied: true };
                        }
                    }
                });

                hours.forEach(function(h, hi) {
                    const td = document.createElement('td');
                    let cellClass = 'hour-cell';
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
                            extraHtml = '<span class="ev-note">(Replaced for ' + e.remarks + ')</span>';
                        }

                        div.innerHTML =
                            '<span class="ev-code">' + e.code + '(' + e.type + ')</span>' +
                            '<span class="ev-venue">' + e.venue + '</span>' +
                            '<span class="ev-time">' + startTime + ' - ' + endTime + '</span>' +
                            extraHtml;

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
            const visibleEvents = getVisibleEvents(currentWeek);
            const days = weekData[currentWeek].days;
            let total = visibleEvents.length;
            let replacement = 0, pending = 0, conflict = 0, classHours = 0;

            visibleEvents.forEach(function(e) {
                if (e.status === 'replacement') replacement++;
                if (e.status === 'pending') pending++;
                if (days[e.di] && days[e.di].holiday) conflict++;
                classHours += (e.end - e.start + 1) * 0.5;
            });

            document.getElementById('sumTotal').textContent = total;
            document.getElementById('sumHours').textContent = (classHours % 1 === 0 ? classHours : classHours.toFixed(1));
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
                saveWeek();
            }
        }

        function nextWeek() {
            if (currentWeek < weekData.length - 1) {
                currentWeek++;
                buildTimetable();
    
    document.getElementById('weekSelect').selectedIndex = currentWeek;
                updateWeekSubtitle();
                updateProgress();
                saveWeek();
            }
        }

        function selectWeek(index) {
            currentWeek = parseInt(index);
            buildTimetable();

updateWeekSubtitle();
            updateProgress();
            saveWeek();
        }

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

        document.getElementById('todayBtn').addEventListener('click', function() {
            currentWeek = currentWeekIndex();
            buildTimetable();

document.getElementById('weekSelect').selectedIndex = currentWeek;
            updateWeekSubtitle();
            updateProgress();
            saveWeek();
            document.querySelector('.grid-wrapper').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        document.addEventListener('DOMContentLoaded', function() {
            loadSavedWeek();

            const chipEl = document.getElementById('semesterChip');
            if (chipEl) chipEl.textContent = MockData.semester.chipText;

            const notifBadge = document.getElementById('notifBadge');
            if (notifBadge) notifBadge.textContent = MockData.studentTimetable.notificationCount;

            buildWeekOptions();
            buildTimetable();

updateWeekSubtitle();
            updateProgress();
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
