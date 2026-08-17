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
                ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total Classes',
                    'description' => 'Total classes in <strong>your weekly timetable</strong> for the selected week.'],
                ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Teaching Hours',
                    'description' => 'Total <strong>teaching hours</strong> in your timetable for the selected week (each slot = <strong>30 minutes</strong>).'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements',
                    'description' => 'Classes where a <strong>replacement lecturer</strong> is covering you this week.'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending',
                    'description' => 'Replacement requests of yours still <strong>waiting for approval</strong> or a volunteer.'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts',
                    'description' => '<strong>Scheduling clashes</strong> in your timetable or classes on <strong>public holidays</strong> that need attention.'],
            ]
        ])

    <!-- ═══ Class Detail Modal ═══ -->
    @section('modal-footer')
        <div class="modal-footer-left">
            <button class="btn-close-modal" onclick="closeModal()">Close</button>
        </div>
        <div class="modal-footer-right">
            <button class="btn-replace-now" id="btnReplaceNow" style="display:none" onclick="goToReplacement(currentModalEvent?.code, currentModalEvent?.cohort, { day: currentModalEvent?.di, start: currentModalEvent?.start, end: currentModalEvent?.end, venue: currentModalEvent?.venue, duration: (currentModalEvent && currentModalEvent.start !== undefined && currentModalEvent.end !== undefined) ? ((currentModalEvent.end - currentModalEvent.start + 1) / 2) : undefined }, 'my-timetable')">Replace Now</button>
            <button class="btn-cancel-class" id="btnCancelClass" style="display:none" onclick="cancelClass()"></button>
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
                <p class="section-heading-sub" style="margin-top:6px;">This action cannot be undone. A cancellation notice will be sent to all affected parties.</p>
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

            const days = weekData[currentWeek].days;
            const isConflict = days[event.di] && days[event.di].holiday;

            const replaceBtn = document.getElementById('btnReplaceNow');
            replaceBtn.style.display = isConflict ? 'flex' : 'none';

            const cancelBtn = document.getElementById('btnCancelClass');
            cancelBtn.style.display = (isConflict || event.status === 'pending') ? 'none' : 'flex';
            cancelBtn.textContent = 'Cancel Class?';

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
            currentWeek = weekNav.currentWeek;
            buildTimetableGrid({
                events: eventsData[currentWeek] || [],
                days: weekData[currentWeek].days,
                onEventClick: function(e) { openModal(e); },
                tooltipExtra: function(e) {
                    // Tooltip shows the cohort(s) — the lecturer is the viewer, so venue/lecturer are redundant.
                    if (e.cohorts && e.cohorts.length) return e.cohorts.join(' + ');
                    return e.cohort || e.venue || '—';
                },
                replacementNoteFn: function(e) {
                    return buildReplacementNote(e);
                }
            });
            updateSummary();
        }

        function updateSummary() {
            const events = eventsData[currentWeek] || [];
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
