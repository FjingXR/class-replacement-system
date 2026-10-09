@extends('layouts.ui-template', [
        'homeUrl' => '/student-my-timetable-ui',
    'activeNav' => 'replacement-history',
    'pageKey' => 'replacementHistory',
    'navItems' => [
        ['key'=>'my-timetable','label'=>'Student My Timetable','href'=>'/student-my-timetable-ui'],
        ['key'=>'replacement-history','label'=>'Replacement History','href'=>'/replacement-history-ui'],
    ],
])

@section('title', 'Replacement History')

@section('page-styles')

        /* ─── Replacement history list: TABLE on desktop (shared .timetable/.data-table styles),
               CARDS on mobile only (user revision 2026-10-01) ─── */
        .upcoming-list {
            display: none; /* cards are mobile-only */
        }
        #upcomingBody tr {
            cursor: pointer;
        }
        #upcomingBody tr:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: -2px;
        }
        .col-status { white-space: nowrap; }
        .upcoming-card-wk {
            font-size: 11px;
            font-weight: 600;
            color: var(--color-on-surface-variant);
            background: var(--color-surface-variant);
            border-radius: var(--radius-sm);
            padding: 2px 8px;
            white-space: nowrap;
        }

        /* ─── Toolbar: stick Today to the right of the week nav
               (page-scoped override of the shared space-between distribution) ─── */
        .toolbar { justify-content: flex-start; }

        .upcoming-card {
            background: var(--color-surface);
            border: 1px solid var(--color-outline);
            border-radius: var(--radius-lg);
            padding: 14px 16px;
            cursor: pointer;
            transition: background 0.15s, box-shadow 0.15s;
        }
        /* hover elevation mirrors theme.css .request-card / cell-hover pattern */
        .upcoming-card:hover {
            background: var(--color-surface-variant);
            box-shadow: var(--shadow-sm);
        }
        .upcoming-card:active {
            transform: scale(0.99);
        }
        .upcoming-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }
        .upcoming-card-code {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-on-surface);
        }
        .upcoming-card-slots {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
            font-size: 12px;
            color: var(--color-on-surface);
        }
        .upcoming-card-lect {
            font-size: 12px;
            color: var(--color-on-surface-variant);
        }
        .slot-arrow {
            color: var(--color-primary);
        }
        .upcoming-card.past {
            border: 1px solid var(--color-outline);
            color: var(--color-on-surface-variant);
        }

        /* ─── Mobile (≤768px): cards replace the table; toolbar stacks via theme.css shared rules ─── */
        @media (max-width: 768px) {
            #upcomingGridWrapper {
                display: none !important;
            }
            .upcoming-list {
                display: grid;
                grid-template-columns: 1fr;
                gap: 8px;
                margin-top: 14px;
            }
            /* whole card is the touch target — keep it ≥44px */
            .upcoming-card {
                min-height: 44px;
            }
        }
@endsection

@section('content')

        <!-- ─── Page Header ─── -->
        @include('partials.ui-page-header', ['title' => 'Replacement History', 'description' => 'Replacement classes confirmed or pending for your cohort.', 'chips' => [['label' => 'RSD3(S1)G2']]])

        <!-- ─── Toolbar: week navigation + show-past toggle ─── -->
        <div class="toolbar">
            @include('partials.ui-week-nav', ['prevOnclick' => 'prevWeekFilter()', 'nextOnclick' => 'nextWeekFilter()', 'selectId' => 'weekFilter', 'selectOnclick' => "weekFilterChanged({onRebuild: renderUpcoming})", 'showTodayBtn' => true])
            <label class="toggle-wrapper" data-tip="Show replacement classes from weeks that have already passed">
                <input type="checkbox" id="showPast">
                <span class="toggle-track"><span class="toggle-thumb"></span></span>
                <span class="toggle-label">Show Past</span>
            </label>
        </div>

        <!-- ─── Replacement list — desktop: shared table (JS renders rows); mobile: cards ─── -->
        @include('partials.ui-grid-table', ['wrapperId' => 'upcomingGridWrapper', 'tableId' => 'upcomingTable', 'headId' => 'upcomingHead', 'bodyId' => 'upcomingBody', 'tableClass' => 'timetable data-table'])
        <div id="upcomingList" class="upcoming-list"></div>

        <!-- ─── Empty state (shown by JS when the filtered list is empty; above the summary) ─── -->
        @include('partials.ui-empty-state', ['title' => 'No replacements this week', 'text' => 'No upcoming replacement classes for RSD3(S1)G2 in the selected week.'])

        <!-- ─── Summary strip (5 cards: Total / Upcoming / Pending / Past / Hours) — hidden when the view is empty ─── -->
        @include('partials.ui-summary-bar', [ 'cards' => [
            ['class' => 'card-total', 'valueId' => 'sumTotal', 'label' => 'Total', 'description' => 'Replacement classes affecting <span class="info-keyword">your cohort</span> in the selected view.'],
            ['class' => 'card-replacement', 'valueId' => 'sumUpcoming', 'label' => 'Upcoming', 'description' => 'Confirmed replacement classes <span class="info-keyword">still ahead</span> of you — check the New Slot column for when and where.'],
            ['class' => 'card-pending', 'valueId' => 'sumPending', 'label' => 'Pending', 'description' => 'Replacement requests still <span class="warn-keyword">waiting for PL approval</span> for your cohort.'],
            ['class' => 'card-hours', 'valueId' => 'sumPast', 'label' => 'Past', 'description' => 'Confirmed replacements that have <span class="info-keyword">already taken place</span> — enable Show Past to see them.'],
            ['class' => 'card-hours', 'valueId' => 'sumHours', 'label' => 'Hours', 'description' => 'Total <span class="info-keyword">hours</span> of replaced classes in the selected view (each slot = <strong>30 minutes</strong>).'],
        ] ])

        <!-- ─── Class detail modal (shared shell for openClassModal) ─── -->
        @include('partials.ui-class-detail-modal')

@endsection

@section('page-scripts')

        // ─── Top-level functions (inline handler strings run in global scope) ───

        // ─── Persisted view state (house pattern, cf. my-request-history saveFilters/restoreFilters) ───
        const VIEW_KEY = 'replacement-history-view';
            /* legacy key from the pre-rename page (2026-10-06) */
            const LEGACY_VIEW_KEY = 'upcoming-replacements-view';
        function saveView() {
            try {
                localStorage.setItem(VIEW_KEY, JSON.stringify({
                    week: document.getElementById('weekFilter').value,
                    showPast: document.getElementById('showPast').checked,
                }));
            } catch (e) { /* storage unavailable — non-fatal */ }
        }

        // ─── Shared slot formatter (list + modal):
        // ─── Week 14 · Fri, 30 Oct 2026, 12:00 PM to 1:30 PM (1.5 hrs) @ B103
        function fmtSlot(weekDays, week, di, start, end, time, venue, withWeek) {
            const d = weekDays[week].days[di];
            const h = (end - start + 1) * 0.5; // 30-min index space
            const t12 = time.split('–').map(s => to12h(s.trim())).join(' to ');
            return (withWeek ? 'Week ' + (week + 1) + ' · ' : '') +
                d.abbr + ', ' + d.date + ', ' + t12 +
                ' (' + (h === 1 ? '1 hr' : h + ' hrs') + ') @ ' + venue;
        }

        /* ── R-2 (round-3): sortable columns — shared makeSortableHeader house
           pattern (cf. request-approval / my-request-history). Null field =
           the natural chronological order below; sort state is session-only
           (the VIEW_KEY persists week/past filters, not the sort). ── */
        const columns = [
            { label: '#', sortable: false, tip: 'Row number' },
            { label: 'Subject', sortable: true, field: 'subject', tip: 'Course code and name' },
            { label: 'Original Slot', sortable: true, field: 'originalSlot', tip: 'Week, day and time of the class being replaced' },
            { label: 'New Slot', sortable: true, field: 'newSlot', tip: 'Proposed replacement slot — rows awaiting PL approval sort last' },
            { label: 'Lecturer', sortable: true, field: 'lecturer', tip: 'Lecturer handling the replacement' },
            { label: 'Status', sortable: true, field: 'status', tip: 'Pending first, then confirmed — nearest slot breaks ties' },
        ];
        let sortState = { field: null, dir: 'asc' };

        const sortKeys = {
            subject: r => r.code + ' ' + r.name,
            originalSlot: r => [r.week, r.di, r.start],
            /* awaiting-PL rows have no replacement yet — they sort last (both directions) */
            newSlot: r => (r.newDay ? [r.week, r.newDi, r.newStart] : null),
            lecturer: r => r.lecturer || '',
            status: r => (r.status === 'pending' ? 0 : 1),
        };

        function applySort(visible, all) {
            if (!sortState.field || !sortKeys[sortState.field]) {
                visible.sort(all
                    ? (a, b) => (a.week - b.week) || (a.di - b.di) || (a.start - b.start)
                    : (a, b) => (a.di - b.di) || (a.start - b.start));
                return;
            }
            const dir = sortState.dir === 'asc' ? 1 : -1;
            const key = sortKeys[sortState.field];
            visible.sort((a, b) => {
                const ka = key(a), kb = key(b);
                if (ka === null && kb === null) return 0;
                if (ka === null) return 1;   /* nulls stay last regardless of direction */
                if (kb === null) return -1;
                let c;
                if (Array.isArray(ka)) {
                    c = 0;
                    for (let i = 0; i < ka.length && c === 0; i++) {
                        c = (ka[i] < kb[i] ? -1 : ka[i] > kb[i] ? 1 : 0);
                    }
                } else {
                    c = (ka < kb ? -1 : ka > kb ? 1 : 0);
                }
                if (c !== 0) return c * dir;
                /* tiebreak: the natural chronological order */
                return (a.week - b.week) || (a.di - b.di) || (a.start - b.start);
            });
        }

        function renderUpcoming() {
            // AD-2: select values are 1-based strings; dataset weeks are 0-based.
            // 'all' = All Weeks option (page-side addition; shared helpers are
            // selectedIndex-based so they stay NaN-safe).
            const all = document.getElementById('weekFilter').value === 'all';
            const w0 = all ? null : parseInt(document.getElementById('weekFilter').value, 10) - 1;
            const cur = currentWeekIndex();
            const showPast = document.getElementById('showPast').checked;

            // AD-5: MockData is read-only — slice() before filtering
            const visible = MockData.replacementHistory.slice()
                .filter(r => (all || r.week === w0) && (r.week >= cur || showPast));

            // AD-11: summary counts from the SAME visible predicate
            // Upcoming = confirmed (replacement) & week >= current; Past = confirmed & already taken
            document.getElementById('sumTotal').textContent    = visible.length;
            document.getElementById('sumUpcoming').textContent = visible.filter(r => r.status === 'replacement' && r.week >= cur).length;
            document.getElementById('sumPending').textContent  = visible.filter(r => r.status === 'pending').length;
            document.getElementById('sumPast').textContent     = visible.filter(r => r.status === 'replacement' && r.week < cur).length;
            document.getElementById('sumHours').textContent    = visible.reduce((s, r) => s + (r.end - r.start + 1) * 0.5, 0);

            applySort(visible, all);

            // ── Desktop table (shared .timetable/.data-table house pattern, cf. my-request-history) ──
            const weekDays = generateWeekData(); // one build per render; rows index into it
            const head = document.getElementById('upcomingHead');
            const body = document.getElementById('upcomingBody');
            head.innerHTML = '';
            const htr = document.createElement('tr');
            columns.forEach(function(col) {
                htr.appendChild(makeSortableHeader(col, sortState, function() {
                    renderUpcoming();
                }));
            });
            head.appendChild(htr);
            body.innerHTML = visible.map((r, i) => {
                const past = r.week < cur;
                const tip = r.status === 'replacement'
                    ? 'Confirmed — attend the new slot shown'
                    : 'Awaiting PL approval — the new slot is not confirmed yet';
                const origSlot = fmtSlot(weekDays, r.week, r.di, r.start, r.end, r.originalTime, r.originalVenue, true);
                const newSlot = r.newDay ? fmtSlot(weekDays, r.week, r.newDi, r.newStart, r.newEnd, r.newTime, r.newVenue, true) : 'Awaiting PL approval';
                return '<tr role="button" tabindex="0" data-id="' + r.id + '">' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td class="col-code">' + r.code + ' · ' + r.name + ' (' + r.type + ')</td>' +
                    '<td>' + origSlot + '</td>' +
                    '<td>' + newSlot + '</td>' +
                    '<td>' + r.lecturer + '</td>' +
                    '<td class="col-status">' +
                        '<span class="badge badge-' + r.status + '" data-tip="' + tip + '">' + (r.status === 'replacement' ? 'Replacement' : 'Pending') + '</span>' +
                        (past ? ' <span class="badge badge-past" data-tip="This replacement has already taken place">Past</span>' : '') +
                    '</td>' +
                '</tr>';
            }).join('');
            document.getElementById('upcomingGridWrapper').style.display = visible.length === 0 ? 'none' : '';

            // ── Mobile cards (same rows, card anatomy; week chip carries the week digit) ──
            const list = document.getElementById('upcomingList');
            list.innerHTML = visible.map(r => {
                const past = r.week < cur;
                const tip = r.status === 'replacement'
                    ? 'Confirmed — attend the new slot shown'
                    : 'Awaiting PL approval — the new slot is not confirmed yet';
                const origSlot = fmtSlot(weekDays, r.week, r.di, r.start, r.end, r.originalTime, r.originalVenue, false);
                const newSlot = r.newDay ? fmtSlot(weekDays, r.week, r.newDi, r.newStart, r.newEnd, r.newTime, r.newVenue, false) : 'Awaiting PL approval';
                return '<div class="upcoming-card' + (past ? ' past' : '') + '" role="button" tabindex="0" data-id="' + r.id + '">' +
                    '<div class="upcoming-card-head">' +
                        '<span class="upcoming-card-wk">' + (r.week + 1) + '</span>' +
                        '<span class="upcoming-card-code">' + r.code + ' · ' + r.name + ' (' + r.type + ')</span>' +
                        '<span class="badge badge-' + r.status + '" data-tip="' + tip + '">' + (r.status === 'replacement' ? 'Replacement' : 'Pending') + '</span>' +
                        (past ? '<span class="badge badge-past" data-tip="This replacement has already taken place">Past</span>' : '') +
                    '</div>' +
                    '<div class="upcoming-card-slots">' +
                        origSlot +
                        '<span class="slot-arrow">→</span>' +
                        newSlot +
                    '</div>' +
                    '<div class="upcoming-card-lect">' + r.lecturer + '</div>' +
                '</div>';
            }).join('');

            const emptyState = document.getElementById('emptyState');
            if (emptyState) emptyState.style.display = visible.length === 0 ? 'flex' : 'none';
            // Nothing to summarize when the view is empty — cf. my-request-history
            syncSummarySection(visible.length > 0);
        }

        function openReplacementModal(id) {
            const r = MockData.replacementHistory.find(x => x.id === id);
            if (!r) return;
            const pending = r.status === 'pending';
            const flagsTuple = (MockData.cohortTimetable.rsd3g2Flags[r.week] || [])
                .find(f => f[0] === r.code && f[1] === r.status);
            const weekDays = generateWeekData();
            const past = r.week < currentWeekIndex();

            // AD-6: same default the grid fabricates
            const requestedAt = pending ? ((flagsTuple && flagsTuple[3]) || '01 Sep 2026, 09:15 AM') : undefined;

            // Holiday remap — parity with the grid (shared openClassModal behavior):
            // pending rows whose ORIGINAL day is a holiday show a red Conflict badge.
            const day = weekDays[r.week].days[pending ? r.di : r.newDi];
            const conflict = day && day.holiday;
            const statusBadge = '<span class="badge badge-' + (conflict ? 'conflict' : r.status) + '">' +
                (conflict ? 'Conflict' : (r.status === 'replacement' ? 'Replacement' : 'Pending')) + '</span>';

            // Slot facts as ONE ROW EACH (data-label / data-value list) — modal-style:
            //   Week 10 · Day Friday · Date 02 Oct 2026 · Time … · Duration … · Venue …
            const slotRows = (week, di, start, end, time, venue, fullDay) => {
                const d = weekDays[week].days[di];
                // Duration as "x hours y minutes" (30-min grid → minutes are 0 or 30; zero parts omitted)
                const mins = (end - start + 1) * 30;
                const hh = Math.floor(mins / 60);
                const mm = mins % 60;
                const dur = (hh ? hh + ' hour' + (hh === 1 ? '' : 's') + (mm ? ' ' : '') : '') +
                            (mm ? mm + ' minutes' : '');
                return [
                    { label: 'Week', value: String(week + 1) },
                    { label: 'Day', value: fullDay },
                    { label: 'Date', value: d.date },
                    { label: 'Time', value: time.split('–').map(s => to12h(s.trim())).join(' to ') },
                    { label: 'Duration', value: dur },
                    { label: 'Venue', value: venue },
                ];
            };

            openClassModal({
                title: r.code + ' — Replacement',
                subtitle: r.name,
                modalId: 'classModal',
                // Grouped layout (§10.0 rule 6 — tidy categories instead of a flat wall)
                groups: [
                    { heading: 'Class', rows: [
                        { label: 'Subject Code', value: r.code },
                        { label: 'Subject Name', value: r.name },
                        { label: 'Class Type', value: r.type === 'L' ? 'Lecture (L)' : 'Tutorial (T)' },
                        { label: 'Lecturer', value: r.lecturer },
                    ]},
                    { heading: 'Original Slot', rows: slotRows(r.week, r.di, r.start, r.end, r.originalTime, r.originalVenue, r.originalDay) },
                    { heading: 'New Slot', rows: r.newDay
                        ? slotRows(r.week, r.newDi, r.newStart, r.newEnd, r.newTime, r.newVenue, r.newDay)
                        : [{ label: 'Status', value: 'Awaiting PL approval <span class="detail-value--muted">— the Programme Leader (PL) has not confirmed this replacement; the new slot is not decided yet</span>' }] },
                    { heading: 'Status', rows: [
                        { label: 'Status', value: statusBadge },
                        ...(past ? [{ label: 'Status Note', value: 'This replacement has already taken place.' }] : []),
                        ...(pending ? [{ label: 'Requested At', value: requestedAt }] : []),
                        ...(r.requestedAt ? [{ label: 'Remarks', value: 'Requested ' + r.requestedAt }] : []),
                    ]},
                ],
                timeline: pending ? [
                    { label: 'Submitted', time: requestedAt || 'Done', state: 'completed' },
                    { label: 'Under Review', time: 'In progress', state: 'active', dot: 'dot-warning' },
                    { label: 'Awaiting Replacement', time: 'Next', state: 'pending' },
                ] : null,
                // NOTE: no requestId — AD-12 (no View Full Request on list modals)
            });
        }

        // ─── Modal close wiring — closeModal() is now the shared global in
        // ─── ui-common.js (§10.0 rule 7 promote); page keeps only the
        // ─── overlay/Escape wiring (cf. student-my-timetable).
        function closeModalOutside(e) {
            closeOnOverlayClick(e, closeModal);
        }

        closeOnEsc(closeModal);

        document.addEventListener('DOMContentLoaded', function() {
            const chipEl = document.getElementById('semesterChip');
            if (chipEl) chipEl.textContent = MockData.semester.chipText;

            // BINDING: page must load on 0-based week 9 ("Week 10"), never "Week 1"
            /* includeAll: the All Weeks option renderUpcoming() filters on.
               Single writer = the shared helper (a page-side IIFE prepend used
               to double it up after includeAll landed 2026-10-06, and the old
               hand-inserted option also vanished on breakpoint re-populate). */
            populateWeekSelect('weekFilter', { selected: currentWeekIndex() + 1, includeAll: true });

            // Restore persisted view (defaults to the current week on first visit)
            (function() {
                let saved = {};
                try {
                    saved = JSON.parse(localStorage.getItem(VIEW_KEY) || '{}');
                } catch (e) { saved = {}; }
                /* legacy key from the pre-rename page (2026-10-06) — migrate once */
                if (Object.keys(saved).length === 0) {
                    try {
                        const legacy = JSON.parse(localStorage.getItem(LEGACY_VIEW_KEY) || 'null');
                        if (legacy) { saved = legacy; localStorage.setItem(VIEW_KEY, JSON.stringify(legacy)); }
                        localStorage.removeItem(LEGACY_VIEW_KEY);
                    } catch (e2) { /* ignore */ }
                }
                const sel = document.getElementById('weekFilter');
                if (saved.week && (saved.week === 'all' || sel.querySelector('option[value="' + saved.week + '"]'))) {
                    sel.value = saved.week;
                }
                if (saved.showPast) document.getElementById('showPast').checked = true;
            })();
            updateWeekArrowState();

            document.getElementById('showPast').addEventListener('change', function() {
                saveView();
                renderUpcoming();
            });
            document.getElementById('weekFilter').addEventListener('change', saveView);

            // AD-10: pages with #weekFilter wire their own today handler
            // (shared jumpToToday() targets #weekSelect)
            document.getElementById('todayBtn')?.addEventListener('click', function() {
                const sel = document.getElementById('weekFilter');
                sel.value = String(currentWeekIndex() + 1);
                sel.dispatchEvent(new Event('change'));
            });

            // Card/row events — delegated so listeners survive re-renders
            // (cards = mobile container, #upcomingBody rows = desktop table)
            const openFromEl = function(e, selector) {
                const el = e.target.closest(selector);
                if (el) openReplacementModal(Number(el.dataset.id));
            };
            ['#upcomingList', '#upcomingBody'].forEach(function(sel) {
                const root = document.querySelector(sel);
                root.addEventListener('click', function(e) { openFromEl(e, sel + ' [data-id]'); });
                root.addEventListener('keydown', function(e) {
                    if (e.key !== 'Enter' && e.key !== ' ') return;
                    const el = e.target.closest(sel + ' [data-id]');
                    if (!el) return;
                    if (e.key === ' ') e.preventDefault(); // avoid page scroll
                    openReplacementModal(Number(el.dataset.id));
                });
            });

            renderUpcoming();
        });

@endsection
