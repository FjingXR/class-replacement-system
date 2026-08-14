@extends('layouts.ui-template', [
    'activeNav' => 'my-timetable',
    'pageKey' => 'studentMyTimetable',
    'navItems' => [
        ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard'],
        ['key'=>'my-timetable','label'=>'Student My Timetable','href'=>'/student-my-timetable-ui'],
        ['key'=>'replacement-history','label'=>'Replacement History','href'=>'/my-request-history-ui'],
    ],
    'notifCount' => 3,
])

@section('title', 'Student My Timetable')

@section('page-styles')
<style>
    .timetable td.hour-cell.offday-slot {
        background: transparent;
    }
</style>
@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'Student My Timetable', 'description' => 'View your weekly class schedule across all sessions.', 'chips' => [['label' => 'RSD3(S1)G2']]])

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
                    'description' => 'Total classes in <strong>your timetable</strong> this week.'],
                ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Class Hours',
                    'description' => 'Total <strong>class hours</strong> you have this week (each slot = <strong>30 minutes</strong>).'],
                ['class' => 'card-replacement', 'valueId' => 'sumReplacement', 'label' => 'Replacements',
                    'description' => 'Classes where a <strong>different lecturer</strong> is covering this week.'],
                ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending',
                    'description' => 'Replacement requests still <strong>being processed</strong> for your classes.'],
                ['class' => 'card-conflict', 'valueId' => 'sumConflict', 'label' => 'Conflicts',
                    'description' => '<strong>Scheduling overlaps</strong> in your timetable that need attention.'],
            ]
        ])

    <!-- ═══ View-Only Modal ═══ -->
    @include('partials.ui-class-detail-modal')

    <!-- ═══ Copy Toast ═══ -->
    <div class="copy-toast" id="copyToast"></div>

@endsection

@section('page-scripts')

        const weekData = generateWeekData();

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

        let currentWeek = currentWeekIndex();

        const weekNav = new WeekNavigator(MockData.semester, weekData);
        weekNav._currentWeek = currentWeek;

        const WEEK_KEY = 'studentMyTimetableWeek';
        function loadSavedWeek() { weekNav.load(); currentWeek = weekNav.currentWeek; }
        function saveWeek() { weekNav.save(); }

        function buildWeekOptions() {
            const sel = document.getElementById('weekSelect');
            var isMobile = window.innerWidth <= 768;
            sel.innerHTML = weekData.map(function(w, i) {
                var label = isMobile
                    ? 'Week ' + (i + 1) + ' \u00B7 ' + w.rangeShort
                    : 'Week ' + (i + 1) + ' \u00B7 ' + w.range;
                return '<option value="' + i + '">' + label + '</option>';
            }).join('');
            sel.selectedIndex = currentWeek;
        }

        function openModal(event) {
            openClassModal({
                event: event,
                dayIndex: event.di,
                days: weekData[currentWeek].days,
                title: event.code || 'Class Details'
            });
        }

        function closeModal() {
            document.getElementById('classModal').style.display = 'none';
        }

        function closeModalOutside(e) {
            closeOnOverlayClick(e, closeModal);
        }

        closeOnEsc(closeModal);

        function buildTimetable() {
            buildTimetableGrid({
                events: getVisibleEvents(currentWeek),
                days: weekData[currentWeek].days,
                onEventClick: function(e) { openModal(e); }
            });
            updateSummary();
        }

        function updateSummary() {
            computeSummary(getVisibleEvents(currentWeek), weekData[currentWeek].days);
            updateWeekArrows(currentWeek <= 0, currentWeek >= weekData.length - 1);
        }

        function prevWeek() {
            if (currentWeek > 0) {
                currentWeek--;
                weekNav._currentWeek = currentWeek;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                updateWeekSubtitle();
                updateProgress();
                weekNav.save();
            }
        }

        function nextWeek() {
            if (currentWeek < weekData.length - 1) {
                currentWeek++;
                weekNav._currentWeek = currentWeek;
                buildTimetable();
                document.getElementById('weekSelect').selectedIndex = currentWeek;
                updateWeekSubtitle();
                updateProgress();
                weekNav.save();
            }
        }

        function selectWeek(index) {
            currentWeek = parseInt(index);
            weekNav._currentWeek = currentWeek;
            buildTimetable();
            updateWeekSubtitle();
            updateProgress();
            weekNav.save();
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

        weekNav.initTodayBtn();

        document.addEventListener('DOMContentLoaded', function() {
            weekNav.load();
            currentWeek = weekNav.currentWeek;

            const chipEl = document.getElementById('semesterChip');
            if (chipEl) chipEl.textContent = MockData.semester.chipText;

            const notifBadge = document.getElementById('notifBadge');
            if (notifBadge) notifBadge.textContent = MockData.studentTimetable.notificationCount;

            buildWeekOptions();
            buildTimetable();
            initWeekKeyboardShortcuts();

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
