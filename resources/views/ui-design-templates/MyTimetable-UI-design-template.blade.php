@extends('layouts.ui-template', [
        'homeUrl' => '/my-timetable-ui','activeNav' => 'my-timetable', 'pageKey' => 'myTimetable'])

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
        @include('partials.ui-page-header', ['title' => 'My Timetable', 'description' => 'View your weekly class schedule and manage replacement requests across all cohorts.'])

        @include('partials.ui-guide-block', [
            'guideTitle' => 'How to use this page',
            'guideItems' => [
                '<strong>Week navigation</strong> — use arrows or Today button to browse weeks',
                '<strong>Slot status</strong> — Normal (green), Conflicted (red), Pending (amber)',
                '<strong>Request replacement</strong> — click any conflicted slot to open the request form',
                '<strong>View details</strong> — click any slot to see class details',
            ]
        ])

        <!-- ─── Semester Progress ─── -->
        <div class="semester-progress" id="semesterProgress">
            <div class="progress-label" id="progressLabel"></div>
            <div class="progress-track"><div class="progress-fill" id="progressFill"></div></div>
        </div>

        <!-- ─── Semester Bar ─── -->
        <div class="semester-bar">
            @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)', 'showPrint' => true])
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
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes',
                    'description' => 'Total classes in <span class="info-keyword">your weekly timetable</span> for the selected week.'],
                ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Teaching Hours',
                    'description' => 'Total <span class="info-keyword">teaching hours</span> in your timetable for the selected week (each slot = <strong>30 minutes</strong>).'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements',
                    'description' => 'Classes where a <span class="info-keyword">replacement lecturer</span> is covering you this week.'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending',
                    'description' => 'Replacement requests of yours still <span class="warn-keyword">waiting for approval</span> or a volunteer.'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts',
                    'description' => '<span class="warn-keyword">Scheduling clashes</span> in your timetable or classes on <strong>public holidays</strong> that need attention.'],
            ]
        ])

    <!-- ═══ Class Detail Modal ═══ -->
    @section('modal-footer')
        <div class="modal-footer-left">
            <button class="btn-close-modal" onclick="closeModal()">Close</button>
        </div>
        <div class="modal-footer-right">
            <button class="btn-replace-now" id="btnReplaceNow" style="display:none" onclick="goToReplacement(currentModalEvent?.code, currentModalEvent?.cohort, { day: currentModalEvent?.di, start: currentModalEvent?.start, end: currentModalEvent?.end, venue: currentModalEvent?.venue, duration: (currentModalEvent && currentModalEvent.start !== undefined && currentModalEvent.end !== undefined) ? ((currentModalEvent.end - currentModalEvent.start + 1) / 2) : undefined }, 'my-timetable')">Replace Now</button>
        </div>
    @endsection
    @include('partials.ui-class-detail-modal')

    <!-- ═══ Cancel Class Confirm Modal (shared partial — cancel-class-enhancement) ═══ -->
    @include('partials.ui-cancel-class-modal')

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

        const weekNav = new WeekNavigator(MockData.semester, weekData, null, 'myTimetableWeek');
        weekNav._currentWeek = currentWeek;

        function openModal(event) {
            currentModalEvent = event;

            const days = weekData[currentWeek].days;
            // Replace Now applies to any class that needs a replacement:
            // a scheduling conflict OR a class falling on a public holiday
            // (Decision A 2026-10-07 — holiday blocks get the button too).
            const isConflict = (days[event.di] && days[event.di].holiday) || event.status === 'conflict';

            const replaceBtn = document.getElementById('btnReplaceNow');
            replaceBtn.style.display = isConflict ? 'flex' : 'none';

            let cohortValue = event.cohort;
            let studentValue = event.studentCount ? String(event.studentCount) : '—';
            if (event.cohorts && event.studentCounts) {
                cohortValue = event.cohorts.join(' + ');
                studentValue = event.studentCounts.join('+') + ' = ' + event.studentCounts.reduce((a, b) => a + b, 0);
            }

            openClassModal({
                event: event,
                dayIndex: event.di,
                days: days,
                modalId: 'classModal',
                title: 'Class Details',
                extraFields: [
                    { label: 'Cohort', value: cohortValue },
                    { label: 'Total Students', value: studentValue }
                ]
            });

            // Cancel Class? button (shared modal — cancel-class-enhancement §5):
            // self-hides via ClassCancellation.isCancellable (S1–S5); the Later
            // path closes the class modal and rebuilds the grid in place.
            CancelClass.renderButton(
                document.querySelector('#classModal .modal-footer'),
                event, days, currentWeek,
                function() {
                    closeModal();
                    buildTimetable();
                }
            );
        }

        function closeModal() {
            document.getElementById('classModal').style.display = 'none';
        }

        function closeModalOutside(e) {
            closeOnOverlayClick(e, closeModal);
        }

        closeOnEsc(closeModal);

        function buildTimetable() {
            currentWeek = weekNav.currentWeek;
            buildTimetableGrid({
                events: eventsData[currentWeek] || [],
                days: weekData[currentWeek].days,
                onEventClick: function(e) { openModal(e); },
                tooltipExtra: function(e) {
                    // Page context: cohort(s). The shared builder appends the
                    // lecturer + run-status ("· En. Lim Jia Zheng · Normal").
                    if (e.cohorts && e.cohorts.length) return e.cohorts.join(' + ');
                    return e.cohort || '';
                },
                replacementNoteFn: function(e) {
                    return buildReplacementNote(e);
                }
            });
            updateSummary();
        }

        function updateSummary() {
            const events = (eventsData[currentWeek] || []).filter(e => e.status !== 'cancelled');
            computeSummary(events, weekData[currentWeek].days);
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
        }

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

        document.addEventListener('DOMContentLoaded', function() {
            weekNav.load();
            currentWeek = weekNav.currentWeek;
            /* ?week=N deep link WINS over any saved week position — the
               cross-page undo lands here at the cancelled class's session
               week (clamped to the demo range; absent/invalid → saved or
               mock-now week, unchanged behavior). */
            const urlWeek = parseInt(new URLSearchParams(location.search).get('week'), 10);
            if (!isNaN(urlWeek) && urlWeek >= 0 && urlWeek < weekData.length) {
                currentWeek = urlWeek;
                weekNav._currentWeek = urlWeek;
            }
            document.getElementById('semesterChip').textContent = MockData.semester.chipText;
            populateWeekSelect('weekSelect', { ranges: false, selected: currentWeek });

            buildTimetable();
            updateWeekSubtitle();
            updateProgress();
        });

        weekNav.initTodayBtn();
        initWeekKeyboardShortcuts();

        initTimetableKeyboardHandlers({
            prevWeek: prevWeek,
            nextWeek: nextWeek,
            openModal: openModal,
            modalId: 'classModal'
        });

        initEvCodeCopy('copyToast');

        initGridSwipeGestures(prevWeek, nextWeek);
@endsection
