@extends('layouts.ui-template', [
        'homeUrl' => '/my-timetable-ui','activeNav' => 'cohort-timetables', 'pageKey' => 'cohortTimetable'])

@section('title', 'Cohort Timetable — Class Replacement System')

@section('page-styles')

        /* ───── Empty State (shared from theme.css) ───── */

        /* ───── Disabled Selects ───── */
        .semester-bar select:disabled,
        .semester-bar select:disabled:hover {
            opacity: 0.5;
            cursor: var(--cursor-cancel);
            background: var(--color-surface-variant);
            color: var(--color-on-surface-variant);
        }

        /* ───── Responsive ───── */
        @media (max-width: 1024px) {
            .grid-scroll { overflow-x: auto; }
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
        .timetable td.hour-cell.offday-slot {
            background: transparent;
        }
        .timetable td.today-cell.offday-slot {
            background: transparent;
        }
        .timetable td.hour-cell.offday-slot .cell-empty {
            background: transparent;
        }

@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'Cohort Timetable', 'description' => 'View the weekly timetable for any cohort across all faculties.'])

        @include('partials.ui-guide-block', [
            'guideTitle' => 'How to use this page',
            'guideItems' => [
                '<strong>Select cohort</strong> — choose a faculty, then a cohort to view its timetable',
                '<strong>Week navigation</strong> — use arrows or Today button to browse weeks',
                '<strong>Slot status</strong> — Blue: Your classes, Green: Others\' classes, Grey: Others\' pending, Amber: Your pending, Red: Conflict',
                '<strong>View details</strong> — click any slot to see class details and venue info',
            ]
        ])

        <!-- ─── Semester Bar ─── -->
        <div class="semester-bar">
            <select id="facultySelect" onchange="onFacultyChange()">
                <option value="">Select Faculty</option>
            </select>
            <select id="cohortSelect" onchange="onCohortChange()" disabled>
                <option value="">Select Cohort</option>
            </select>
            @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)', 'disabled' => true, 'showPrint' => true])
        </div>

        <!-- ─── Grid Wrapper ─── -->
        @include('partials.ui-grid-table')

        <!-- ─── Legend Bar ─── -->
        @include('partials.ui-legend-bar', [
            'items' => [
                ['color' => 'var(--color-primary-container)', 'label' => 'Your Classes', 'tip' => 'Normal or replacement sessions assigned to you'],
                ['color' => 'var(--color-success-container)', 'label' => 'Others\' Classes', 'tip' => 'Normal or replacement sessions by other lecturers'],
                ['color' => 'var(--color-surface-variant)', 'label' => 'Others\' Pending', 'tip' => 'Replacement request by other lecturers, awaiting PL approval'],
                ['color' => 'var(--color-tertiary-container)', 'label' => 'Your Pending', 'tip' => 'Your replacement request, awaiting PL approval'],
                ['color' => 'var(--color-error-container)', 'label' => 'Conflict / Public Holiday', 'tip' => 'Scheduling conflict or public holiday'],
            ]
        ])

        <!-- ─── Summary Bar ─── -->
        @include('partials.ui-summary-bar', [
            'cards' => [
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes',
                    'description' => 'Total classes scheduled for <strong>this cohort</strong> in the selected week.'],
                ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Teaching Hours',
                    'description' => 'Total <strong>teaching hours</strong> for this cohort in the selected week (each slot = <strong>30 minutes</strong>).'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements',
                    'description' => 'Classes with a <strong>replacement lecturer</strong> assigned this week.'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending',
                    'description' => 'Replacement requests for this cohort still <strong>waiting for approval</strong> or a volunteer.'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts',
                    'description' => '<strong>Scheduling clashes</strong> or classes on <strong>public holidays</strong> for this cohort that need attention.'],
            ]
        ])

        <!-- ─── Empty State ─── -->
        @include('partials.ui-empty-state', ['title' => 'Select a faculty first', 'text' => 'Choose a faculty, then pick a cohort to view its weekly timetable.'])

        <!-- ─── Event Modal ─── -->
        @include('partials.ui-class-detail-modal', ['modalId' => 'eventModal'])

        <!-- ═══ Cancel Class Confirm Modal (shared partial — cancel-class-enhancement) ═══ -->
        @include('partials.ui-cancel-class-modal')

@endsection

@section('page-scripts')

        /* ════════════════════════════════════════════
           MOCK DATA — Weeks, Faculties, Cohorts, Events
           ════════════════════════════════════════════ */

        const weekData = generateWeekData();

        /* ───── Read from centralized MockData ───── */
        const facultyData = MockData.cohortTimetable.faculties;

        // Rebuild allEvents from centralized data — re-callable because the
        // copies (Object.assign) snapshot the status at build time: a same-load
        // cancel mutates the canonical MockData stores, so the assembly MUST be
        // re-run before every rebuild or the cancelled block stays visible
        // (design §4 same-load snapshot fix).
        let allEvents = {};
        function buildAllEvents() {
            allEvents = {};
            MockData.cohortTimetable.events.forEach(function(entry) {
                if (!allEvents[entry.cohortId]) allEvents[entry.cohortId] = {};
                if (!allEvents[entry.cohortId][entry.week]) allEvents[entry.cohortId][entry.week] = [];
                allEvents[entry.cohortId][entry.week].push(Object.assign({}, entry.event));
            });
            // RSD3 G2 renders straight from cohortTimetable.events: the seeder already
            // applies rsd3g2Flags slot-keyed (snap-to-first-block) in §2.7, so a
            // local rebuild from rsd3g2Base + code-keyed rsd3g2Flags would re-flag
            // every block of a code (phantom multi-reds / double pendings).
        }
        buildAllEvents();

        /* ───── State ───── */
        let currentWeek = currentWeekIndex();
        let selectedCohortId = null;

        const weekNav = new WeekNavigator(MockData.semester, weekData, null, 'cohortTimetableWeek');
        weekNav._currentWeek = currentWeek;

        /* ════════════════════════════════════════════
           DROPDOWN POPULATION
           ════════════════════════════════════════════ */

        function populateWeeks() {
            populateWeekSelect('weekSelect', { ranges: false, selected: currentWeek });
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
                document.getElementById('emptyState').style.display = 'flex';
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
            document.getElementById('emptyState').style.display = 'flex';
            document.getElementById('emptyTitle').textContent = 'Select a cohort';
            document.getElementById('emptyText').textContent = 'Pick a cohort from ' + faculty.name + ' to view its weekly timetable.';
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
        }

        function onCohortChange() {
            const cid = document.getElementById('cohortSelect').value;
            const weekSel = document.getElementById('weekSelect');
            if (!cid) {
                selectedCohortId = null;
                weekSel.disabled = true;
                document.getElementById('emptyState').style.display = 'flex';
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
            weekNav._currentWeek = currentWeek;
            document.getElementById('weekSelect').selectedIndex = currentWeek;
            buildTimetable();
            saveState();
        }

        /* ════════════════════════════════════════════
           WEEK NAVIGATION
           ════════════════════════════════════════════ */

        function prevWeek() {
            weekNav.prevWeek();
            currentWeek = weekNav.currentWeek;
            saveState();
        }

        function nextWeek() {
            weekNav.nextWeek();
            currentWeek = weekNav.currentWeek;
            saveState();
        }

        function selectWeek(index) {
            weekNav.selectWeek(parseInt(index, 10));
            currentWeek = weekNav.currentWeek;
            saveState();
        }

        function goToday() {
            weekNav.jumpToToday();
            currentWeek = weekNav.currentWeek;
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
            if (!state || !state.faculty) {
                weekNav.load();
                currentWeek = weekNav.currentWeek;
                return;
            }
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
                    weekNav._currentWeek = w;
                    document.getElementById('weekSelect').selectedIndex = w;
                    buildTimetable();
                    weekNav.save();
                    saveState();
                }
            }
        }

        /* ════════════════════════════════════════════
           TIMETABLE GRID BUILDER
           ════════════════════════════════════════════ */

        function buildTimetable() {
            currentWeek = weekNav.currentWeek;
            if (!selectedCohortId) {
                document.getElementById('weekSelect').disabled = true;
                document.getElementById('emptyState').style.display = 'flex';
                document.getElementById('emptyTitle').textContent = 'Select a faculty first';
                document.getElementById('emptyText').textContent = 'Choose a faculty, then pick a cohort to view its weekly timetable.';
                updateWeekArrows(true, true);
                return;
            }

            const weekEvents = allEvents[selectedCohortId]?.[currentWeek] || [];

            if (weekEvents.length === 0) {
                document.getElementById('emptyState').style.display = 'flex';
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

            buildTimetableGrid({
                events: weekEvents,
                days: weekData[currentWeek].days,
                onEventClick: function(e, di) { openModal(e, di); },
                tooltipExtra: function(e) {
                    return e.lecturer || '—';
                },
                statusClassFn: function(div, e, isConflict) {
                    if (isConflict) {
                        div.classList.add('event-public-holiday');
                        return;
                    }
                    var isMine = e.lecturer === MockData.currentUser.name;
                    if (e.status === 'conflict') {
                        // §10.0 legend A: Conflict = red, same colour on every page
                        div.classList.add('event-conflict');
                    } else if (e.status === 'pending') {
                        div.classList.add(isMine ? 'event-mine-pending' : 'event-others-pending');
                    } else {
                        div.classList.add(isMine ? 'event-mine' : 'event-others');
                    }
                },
                replacementNoteFn: function(e) {
                    return buildReplacementNote(e, {
                        checkOwnership: function(ev) { return ev.lecturer === MockData.currentUser.name; }
                    });
                }
            });
            updateSummaries(weekEvents.filter(e => e.status !== 'cancelled'));
        }

        function updateSummaries(events) {
            computeSummary(events, weekData[currentWeek].days);
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
        }

        /* ════════════════════════════════════════════
           MODAL
           ════════════════════════════════════════════ */

        function openModal(event, di) {
            openClassModal({
                event: event,
                dayIndex: (di !== undefined ? di : event.di),
                days: weekData[currentWeek].days,
                modalId: 'eventModal',
                extraFields: [
                    { label: 'Cohort', value: event.cohort || selectedCohortId || '—' }
                ]
            });

            // Cancel Class? button (shared modal — cancel-class-enhancement §5):
            // self-hides via ClassCancellation.isCancellable (S1–S5); the Later
            // path re-runs the allEvents assembly (same-load snapshot fix) then
            // rebuilds the grid + summary in place.
            CancelClass.renderButton(
                document.querySelector('#eventModal .modal-footer'),
                event, weekData[currentWeek].days, currentWeek,
                function() {
                    closeModal();
                    buildAllEvents();
                    buildTimetable();
                }
            );
        }

        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        function closeModalOutside(e) {
            closeOnOverlayClick(e, closeModal);
        }

        closeOnEsc(closeModal);

        /* ════════════════════════════════════════════
           INIT
           ════════════════════════════════════════════ */

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            populateWeeks();
            populateFaculties();

            // Show initial empty state with guidance
            document.getElementById('emptyState').style.display = 'flex';
            document.getElementById('emptyTitle').textContent = 'Select a faculty first';
            document.getElementById('emptyText').textContent = 'Choose a faculty, then pick a cohort to view its weekly timetable.';
            document.getElementById('cohortSelect').disabled = true;
            document.getElementById('weekSelect').disabled = true;
            updateWeekArrows(true, true);

            // Restore last selection (faculty / cohort / week) — overrides the empty state above if present
            restoreState();
        });

        document.getElementById('todayBtn')?.addEventListener('click', goToday);
        initWeekKeyboardShortcuts();

        initTimetableKeyboardHandlers({
            prevWeek: prevWeek,
            nextWeek: nextWeek,
            openModal: openModal,
            modalId: 'eventModal'
        });

        initGridSwipeGestures(prevWeek, nextWeek);
@endsection
