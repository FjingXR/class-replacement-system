@extends('layouts.ui-template', ['activeNav' => 'cohort-timetables', 'pageKey' => 'cohortTimetable'])

@section('title', 'Cohort Timetable — Class Replacement System')

@section('page-styles')

        /* ───── Empty State (shared from theme.css) ───── */

        /* ───── Disabled Selects ───── */
        .semester-bar select:disabled,
        .semester-bar select:disabled:hover {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
        }

        /* ───── Responsive ───── */
        @media (max-width: 1024px) {
            .grid-scroll { overflow-x: auto; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-right { justify-content: flex-start; }
        }
        @media (max-width: 768px) {
            .semester-bar select:not(.week-select) {
                min-width: 0;
                flex: 1 1 120px;
            }
            .week-nav {
                width: 100%;
            }
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'Cohort Timetable', 'description' => 'View the weekly timetable for any cohort across all faculties.'])

        <!-- ─── Semester Bar ─── -->
        <div class="semester-bar">
            <select id="facultySelect" onchange="onFacultyChange()">
                <option value="">Select Faculty</option>
            </select>
            <select id="cohortSelect" onchange="onCohortChange()" disabled>
                <option value="">Select Cohort</option>
            </select>
            @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)', 'disabled' => true])
        </div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table')

        <!-- ─── Legend Bar ─── -->
        @include('partials.ui-legend-bar')

        <!-- ─── Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes'],
                ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Teaching Hours'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts'],
            ]
        ])

        <!-- ─── Empty State ─── -->
        @include('partials.ui-empty-state', ['title' => 'Select a faculty first', 'text' => 'Choose a faculty, then pick a cohort to view its weekly timetable.'])

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

        /* ════════════════════════════════════════════
           MOCK DATA — Weeks, Faculties, Cohorts, Events
           ════════════════════════════════════════════ */

        const weekData = (function() {
            const start = new Date(MockData.semester.startDate);
            start.setHours(0, 0, 0, 0);
            const arr = [];
            const todayMs = (function() { const t = new Date('2026-08-02'); t.setHours(0, 0, 0, 0); return t.getTime(); })();
            for (let w = 1; w <= 14; w++) {
                const ms = start.getTime() + (w - 1) * 7 * 86400000;
                const mon = new Date(ms);
                const sun = new Date(ms + 6 * 86400000);
                const fmt = d => `${String(d.getDate()).padStart(2,'0')} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][d.getMonth()]} ${d.getFullYear()}`;
                const fmtShort = d => `${String(d.getDate()).padStart(2,'0')} ${['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][d.getMonth()]}`;
                const days = [];
                for (let d = 0; d < 7; d++) {
                    const dt = new Date(ms + d * 86400000);
                    days.push({
                        abbr: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][d],
                        date: fmt(dt),
                        sunday: d === 6,
                        today: dt.getTime() === todayMs,
                        holiday: MockData.holidays.some(function(h) { return h.week === w && h.dayIndex === d; }),
                    });
                }
                arr.push({ label: `Week ${w}`, range: `${fmt(mon)} ~ ${fmt(sun)}`, rangeShort: `${fmtShort(mon)} ~ ${fmtShort(sun)}`, days });
            }
            return arr;
        })();

        /* ───── Read from centralized MockData ───── */
        const facultyData = MockData.cohortTimetable.faculties;

        // Rebuild allEvents from centralized data
        const allEvents = {};
        MockData.cohortTimetable.events.forEach(function(entry) {
            if (!allEvents[entry.cohortId]) allEvents[entry.cohortId] = {};
            if (!allEvents[entry.cohortId][entry.week]) allEvents[entry.cohortId][entry.week] = [];
            allEvents[entry.cohortId][entry.week].push(Object.assign({}, entry.event));
        });

        // Reconstruct RSD3 G2: populate ALL 14 weeks with base events, then apply flag overrides.
        var rsd3g2Cohort = 'rsd3s1g2';
        if (!allEvents[rsd3g2Cohort]) allEvents[rsd3g2Cohort] = {};
        for (var w = 0; w < 14; w++) {
            allEvents[rsd3g2Cohort][w] = [];
            MockData.cohortTimetable.rsd3g2Base.forEach(function(evt) {
                var copy = Object.assign({}, evt);
                if (!copy.status) copy.status = 'normal';
                allEvents[rsd3g2Cohort][w].push(copy);
            });
        }
        MockData.cohortTimetable.rsd3g2Flags && Object.keys(MockData.cohortTimetable.rsd3g2Flags).forEach(function(w) {
            var weekIdx = parseInt(w);
            MockData.cohortTimetable.rsd3g2Flags[w].forEach(function(entry) {
                var flagCode = entry[0], flagStatus = entry[1], flagDate = entry[2] || '';
                var weekEvents = allEvents[rsd3g2Cohort][weekIdx];
                weekEvents.forEach(function(evt) {
                    if (evt.code === flagCode) {
                        evt.status = flagStatus;
                        if (flagDate) evt.remarks = flagDate;
                    }
                });
            });
        });

        /* ───── State ───── */
        let currentWeek = currentWeekIndex();
        let selectedCohortId = null;

        /* ════════════════════════════════════════════
           DROPDOWN POPULATION
           ════════════════════════════════════════════ */

        function populateWeeks() {
            const sel = document.getElementById('weekSelect');
            const isMobile = window.innerWidth <= 768;
            sel.innerHTML = weekData.map((w, i) => {
                const label = isMobile
                    ? `${w.label} · ${w.rangeShort}`
                    : `${w.label} · ${w.range}`;
                return `<option value="${i}">${label}</option>`;
            }).join('');
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
            const weekSel = document.getElementById('weekSelect');
            if (!fid) {
                cohortSel.innerHTML = '<option value="">Select Cohort</option>';
                cohortSel.disabled = true;
                weekSel.disabled = true;
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'Select a faculty first';
                document.getElementById('emptyText').textContent = 'Choose a faculty, then pick a cohort to view its weekly timetable.';
                document.getElementById('timetable').querySelector('thead').innerHTML = '';
                document.getElementById('timetable').querySelector('tbody').innerHTML = '';
                document.getElementById('sumTotal').textContent = '0';
                document.getElementById('sumHours').textContent = '0';
                document.getElementById('sumReplacement').textContent = '0';
                document.getElementById('sumPending').textContent = '0';
                document.getElementById('sumConflict').textContent = '0';
                updateWeekArrows(true, true);
                selectedCohortId = null;
                saveState();
                return;
            }
            const faculty = facultyData.find(f => f.id === fid);
            cohortSel.disabled = false;
            cohortSel.innerHTML = '<option value="">Select Cohort</option>' +
                faculty.cohorts.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            weekSel.disabled = true;
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('emptyTitle').textContent = 'Select a cohort';
            document.getElementById('emptyText').textContent = 'Pick a cohort from ' + faculty.name + ' to view its weekly timetable.';
            document.getElementById('timetable').querySelector('thead').innerHTML = '';
            document.getElementById('timetable').querySelector('tbody').innerHTML = '';
            document.getElementById('sumTotal').textContent = '0';
            document.getElementById('sumReplacement').textContent = '0';
            document.getElementById('sumPending').textContent = '0';
            document.getElementById('sumConflict').textContent = '0';
            updateWeekArrows(true, true);
            selectedCohortId = null;
            saveState();
        }

        function onCohortChange() {
            const cid = document.getElementById('cohortSelect').value;
            const weekSel = document.getElementById('weekSelect');
            if (!cid) {
                selectedCohortId = null;
                weekSel.disabled = true;
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'Select a cohort';
                document.getElementById('emptyText').textContent = 'Choose a faculty, then pick a cohort to view its weekly timetable.';
                document.getElementById('timetable').querySelector('thead').innerHTML = '';
                document.getElementById('timetable').querySelector('tbody').innerHTML = '';
                document.getElementById('sumTotal').textContent = '0';
                document.getElementById('sumHours').textContent = '0';
                document.getElementById('sumReplacement').textContent = '0';
                document.getElementById('sumPending').textContent = '0';
                document.getElementById('sumConflict').textContent = '0';
                updateWeekArrows(true, true);
                saveState();
                return;
            }
            selectedCohortId = cid;
            weekSel.disabled = false;
            currentWeek = currentWeekIndex();
            document.getElementById('weekSelect').selectedIndex = currentWeek;
            buildTimetable();
            saveState();
        }

        /* ════════════════════════════════════════════
           WEEK NAVIGATION
           ════════════════════════════════════════════ */

        function prevWeek() {
            if (currentWeek > 0) {
                currentWeek--;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                saveState();
            }
        }

        function nextWeek() {
            if (currentWeek < weekData.length - 1) {
                currentWeek++;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                saveState();
            }
        }

        function selectWeek(index) {
            currentWeek = parseInt(index);
            buildTimetable();
            saveState();
        }

        /* ════════════════════════════════════════════
           STATE PERSISTENCE (localStorage)
           ════════════════════════════════════════════ */

        const STATE_KEY = 'cohortTimetableState';

        function saveState() {
            try {
                localStorage.setItem(STATE_KEY, JSON.stringify({
                    faculty: document.getElementById('facultySelect').value,
                    cohort: document.getElementById('cohortSelect').value,
                    week: currentWeek
                }));
            } catch (e) { /* storage unavailable — ignore */ }
        }

        function restoreState() {
            let state = null;
            try { state = JSON.parse(localStorage.getItem(STATE_KEY) || 'null'); } catch (e) { state = null; }
            if (!state || !state.faculty) return;
            const faculty = facultyData.find(f => f.id === state.faculty);
            if (!faculty) return;
            document.getElementById('facultySelect').value = faculty.id;
            onFacultyChange();
            if (state.cohort && faculty.cohorts.some(c => c.id === state.cohort)) {
                document.getElementById('cohortSelect').value = state.cohort;
                onCohortChange();
                const w = parseInt(state.week);
                if (!isNaN(w) && w >= 0 && w < weekData.length) {
                    currentWeek = w;
                    document.getElementById('weekSelect').selectedIndex = w;
                    buildTimetable();
                    saveState();
                }
            }
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
                document.getElementById('weekSelect').disabled = true;
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('emptyTitle').textContent = 'Select a faculty first';
                document.getElementById('emptyText').textContent = 'Choose a faculty, then pick a cohort to view its weekly timetable.';
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
                document.getElementById('sumHours').textContent = '0';
                document.getElementById('sumReplacement').textContent = '0';
                document.getElementById('sumPending').textContent = '0';
                document.getElementById('sumConflict').textContent = '0';
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

            for (let i = 0; i < hours.length; i += 2) {
                const th = document.createElement('th');
                th.className = 'hour-header';
                th.colSpan = 2;
                th.innerHTML = `<span class="hour-top">${hours[i]}</span><span class="hour-bottom">${hours[i + 2] || add30min(hours[i + 1])}</span>`;
                timeHeaderRow.appendChild(th);
            }
            head.appendChild(timeHeaderRow);

            // ── Day rows ──
            days.forEach((day, di) => {
                const tr = document.createElement('tr');

                const dayTd = document.createElement('td');
                let dayColClass = 'time-col';
                if (day.today) dayColClass += ' today';
                if (day.holiday || day.sunday) dayColClass += ' offday';
                dayTd.className = dayColClass;
                dayTd.innerHTML = buildDayHtml(day);
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

            updateSummaries(weekEvents);
        }

        /* ════════════════════════════════════════════
           SUMMARY
           ════════════════════════════════════════════ */

        function updateSummaries(events) {
            const data = weekData[currentWeek];
            const days = data ? data.days : [];
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
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            populateWeeks();
            populateFaculties();

            // Show initial empty state with guidance
            document.getElementById('emptyState').style.display = 'block';
            document.getElementById('emptyTitle').textContent = 'Select a faculty first';
            document.getElementById('emptyText').textContent = 'Choose a faculty, then pick a cohort to view its weekly timetable.';
            document.getElementById('cohortSelect').disabled = true;
            document.getElementById('weekSelect').disabled = true;
            updateWeekArrows(true, true);

            // Restore last selection (faculty / cohort / week) — overrides the empty state above if present
            restoreState();
        });

        initTodayBtn();
        initWeekKeyboardShortcuts();

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
