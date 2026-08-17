// ui-common.js — Shared JS helpers for all UI templates

function updateIcon(isDark) {
    const icon = document.getElementById('theme-icon');
    if (!icon) return;
    icon.innerHTML = isDark
        ? '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>'
        : '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
}

function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');
    html.classList.toggle('light');
    html.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'light' : 'dark');
    updateIcon(!isDark);
}

function navigateHome() {
    window.location.href = '/';
}

function updateWeekArrows(prevDisabled, nextDisabled) {
    const prev = document.querySelector('.week-arrow[aria-label="Previous week"]');
    const next = document.querySelector('.week-arrow[aria-label="Next week"]');
    if (prev) prev.disabled = prevDisabled;
    if (next) next.disabled = nextDisabled;
}

// ───── Week helpers (shared by timetable pages) ─────

function currentWeekIndex() {
    const parts = MockData.semester.startDate.split('-');
    const semesterStart = new Date(parts[0], parts[1] - 1, parts[2]);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const idx = Math.floor((today - semesterStart) / 86400000 / 7);
    return Math.max(0, Math.min(MockData.semester.weeks - 1, idx));
}

function updateProgress() {
    const pct = ((currentWeek + 1) / MockData.semester.weeks) * 100;
    const fill = document.getElementById('progressFill');
    const label = document.getElementById('progressLabel');
    if (fill) fill.style.width = pct + '%';
    if (label) label.textContent = 'Week ' + (currentWeek + 1) + ' of ' + MockData.semester.weeks;
}

function generateWeekData() {
    const parts = MockData.semester.startDate.split('-');
    const start = new Date(parts[0], parts[1] - 1, parts[2]);
    const arr = [];
    const todayMs = getTodayMs();
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const fmt = d => `${String(d.getDate()).padStart(2,'0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
    const fmtShort = d => `${String(d.getDate()).padStart(2,'0')} ${months[d.getMonth()]}`;
    for (let w = 1; w <= MockData.semester.weeks; w++) {
        const ms = start.getTime() + (w - 1) * 7 * 86400000;
        const mon = new Date(ms);
        const sun = new Date(ms + 6 * 86400000);
        const days = [];
        for (let d = 0; d < 7; d++) {
            const dt = new Date(ms + d * 86400000);
            let holiday = false;
            let holidayLabel = '';
            MockData.holidays.forEach(function(h) {
                if (h.week === w && h.dayIndex === d) {
                    holiday = true;
                    holidayLabel = h.label;
                }
            });
            days.push({
                abbr: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][d],
                date: fmt(dt),
                sunday: d === 6,
                today: dt.getTime() === todayMs,
                holiday: holiday,
                holidayLabel: holidayLabel,
            });
        }
        arr.push({ label: `Week ${w}`, range: `${fmt(mon)} ~ ${fmt(sun)}`, rangeShort: `${fmtShort(mon)} ~ ${fmtShort(sun)}`, days });
    }
    return arr;
}

function updateWeekSubtitle() {
    const el = document.getElementById('weekSubtitle');
    if (el) {
        const week = weekData[currentWeek];
        let subtitle = 'Week ' + (currentWeek + 1) + ' of ' + MockData.semester.weeks;
        if (week.start && week.end) {
            subtitle += ' \u00B7 ' + DateHelper.fmt(week.start) + ' \u00B7 ' + DateHelper.fmt(week.end);
        } else if (week.range) {
            subtitle += ' \u00B7 ' + week.range;
        }
        el.textContent = subtitle;
    }
}

function updateWeekArrowState() {
    var sel = document.getElementById('weekFilter');
    updateWeekArrows(sel.selectedIndex <= 0, sel.selectedIndex >= sel.options.length - 1);
}

// ───── Today button (shared by all timetable pages) ─────
// Each page must define: buildTimetable()
// Optionally define: updateSummary(), saveWeek()

function jumpToToday() {
    currentWeek = currentWeekIndex();
    buildTimetable();
    var sel = document.getElementById('weekSelect');
    if (sel) sel.selectedIndex = currentWeek;
    if (typeof updateWeekSubtitle === 'function') updateWeekSubtitle();
    if (typeof updateSummary === 'function') updateSummary();
    if (typeof updateProgress === 'function') updateProgress();
    var grid = document.querySelector('.grid-wrapper');
    if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function initTodayBtn() {
    var btn = document.getElementById('todayBtn');
    if (btn) btn.addEventListener('click', jumpToToday);
}

class WeekNavigator {
    constructor(semesterData, weekData, selectId) {
        this._semester = semesterData;
        this._weekData = weekData;
        this._currentWeek = 0;
        this._selectId = selectId || 'weekSelect';
    }

    get currentWeek() {
        return this._currentWeek;
    }

    get weekData() {
        return this._weekData;
    }

    get semester() {
        return this._semester;
    }

    jumpToToday() {
        this._currentWeek = this._currentWeekIndex();
        this._buildTimetable();
        this._updateSelect();
        this._updateSubtitle();
        this._updateProgress();
        this._scrollToGrid();
        this.save();
    }

    prevWeek() {
        if (this._currentWeek > 0) {
            this._beforeNavigate();
            this._currentWeek--;
            this._buildTimetable();
            this._updateSelect();
            this._updateSubtitle();
            this._updateProgress();
            this._updateArrows();
            this.save();
        }
    }

    nextWeek() {
        if (this._currentWeek < this._semester.weeks - 1) {
            this._beforeNavigate();
            this._currentWeek++;
            this._buildTimetable();
            this._updateSelect();
            this._updateSubtitle();
            this._updateProgress();
            this._updateArrows();
            this.save();
        }
    }

    selectWeek(index) {
        this._beforeNavigate();
        this._currentWeek = index;
        this._buildTimetable();
        this._updateSelect();
        this._updateSubtitle();
        this._updateProgress();
        this._updateArrows();
        this.save();
    }

    onWeekChange() {
        var sel = document.getElementById(this._selectId);
        if (sel) this.selectWeek(parseInt(sel.value, 10));
    }

    _beforeNavigate() {
        if (typeof this.onBeforeNavigate === 'function') this.onBeforeNavigate();
    }

    save() {
        try {
            localStorage.setItem('currentWeek', this._currentWeek);
        } catch (e) { /* ignore */ }
    }

    load() {
        try {
            var saved = localStorage.getItem('currentWeek');
            if (saved !== null) {
                var idx = parseInt(saved, 10);
                if (!isNaN(idx)) {
                    this._currentWeek = Math.max(0, Math.min(this._semester.weeks - 1, idx));
                }
            }
        } catch (e) { /* ignore */ }
    }

    initKeyboard() {
        if (this._keyboardBound) return;
        this._keyboardBound = true;
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;
            if (e.key === '[') {
                e.preventDefault();
                this.prevWeek();
            } else if (e.key === ']') {
                e.preventDefault();
                this.nextWeek();
            }
        });
    }

    initTodayBtn() {
        var btn = document.getElementById('todayBtn');
        if (btn) btn.addEventListener('click', () => this.jumpToToday());
    }

    saveWeek() {
        this.save();
    }

    loadSavedWeek() {
        this.load();
    }

    initWeekKeyboardShortcuts() {
        this.initKeyboard();
    }

    updateWeekSubtitle() {
        this._updateSubtitle();
    }

    updateWeekProgress() {
        this._updateProgress();
    }

    _currentWeekIndex() {
        var parts = this._semester.startDate.split('-');
        var semesterStart = new Date(parts[0], parts[1] - 1, parts[2]);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var idx = Math.floor((today - semesterStart) / 86400000 / 7);
        return Math.max(0, Math.min(this._semester.weeks - 1, idx));
    }

    _buildTimetable() {
        if (typeof window.buildTimetable === 'function') window.buildTimetable();
    }

    _updateSelect() {
        var sel = document.getElementById(this._selectId);
        if (sel) sel.selectedIndex = this._currentWeek;
    }

    _updateSubtitle() {
        var el = document.getElementById('weekSubtitle');
        if (el) {
            var week = this._weekData[this._currentWeek];
            var subtitle = 'Week ' + (this._currentWeek + 1) + ' of ' + this._semester.weeks;
            if (week.start && week.end) {
                subtitle += ' \u00B7 ' + DateHelper.fmt(week.start) + ' \u00B7 ' + DateHelper.fmt(week.end);
            } else if (week.range) {
                subtitle += ' \u00B7 ' + week.range;
            }
            el.textContent = subtitle;
        }
    }

    _updateProgress() {
        var pct = ((this._currentWeek + 1) / this._semester.weeks) * 100;
        var fill = document.getElementById('progressFill');
        var label = document.getElementById('progressLabel');
        if (fill) fill.style.width = pct + '%';
        if (label) label.textContent = 'Week ' + (this._currentWeek + 1) + ' of ' + this._semester.weeks;
    }

    _updateArrows() {
        var prev = document.querySelector('.week-arrow[aria-label="Previous week"]');
        var next = document.querySelector('.week-arrow[aria-label="Next week"]');
        if (prev) prev.disabled = this._currentWeek <= 0;
        if (next) next.disabled = this._currentWeek >= this._semester.weeks - 1;
    }

    _scrollToGrid() {
        var grid = document.querySelector('.grid-wrapper');
        if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ───── Time slot helpers (shared by timetable pages) ─────

const hours = [
    '08:00', '08:30', '09:00', '09:30',
    '10:00', '10:30', '11:00', '11:30',
    '12:00', '12:30',
    '13:00', '13:30', '14:00', '14:30',
    '15:00', '15:30', '16:00', '16:30',
    '17:00', '17:30', '18:00', '18:30'
];

// ───── Shared timetable grid builder ─────

/**
 * Build a timetable grid. Call from page-level buildTimetable().
 * @param {object} cfg
 * @param {Array} cfg.events - Array of event objects for the current week
 * @param {Array} cfg.days - Array of day objects from weekData
 * @param {function} cfg.onEventClick - Callback (event, dayIndex) when an event block is clicked
 * @param {string} [cfg.tableId='timetable'] - ID of the table element
 * @param {string} [cfg.headId='tableHead'] - ID of the thead element
 * @param {string} [cfg.bodyId='tableBody'] - ID of the tbody element
 * @param {string} [cfg.emptyId='emptyState'] - ID of the empty state element
 */
/**
 * Build a timetable grid. Call from page-level buildTimetable().
 * @param {object} cfg
 * @param {Array} cfg.events - Array of event objects for the current week
 * @param {Array} cfg.days - Array of day objects from weekData
 * @param {function} [cfg.onEventClick] - Click handler for event blocks (event, dayIndex)
 * @param {function} [cfg.cellRender] - Optional custom cell renderer (td, dayIndex, hourIndex, day).
 *   When provided, the header + day-column scaffolding is still shared but each hour cell is
 *   delegated to this callback instead of the default event-block layout. Used by
 *   replacement-arrangement + venue-timetable (selection cell model).
 */
function buildReplacementNote(e, opts) {
    opts = opts || {};
    var checkOwnership = opts.checkOwnership;
    if (typeof checkOwnership === 'function' && !checkOwnership(e)) return '';
    if (e.status === 'replacement' && e.remarks) {
        return '<span class="ev-note">(Replaced for ' + e.remarks + ')</span>';
    }
    if (e.status === 'pending' && e.requestedAt) {
        var submitted = e.requestedAt.split(',')[0];
        return '<span class="ev-note">(Pending since ' + submitted + ')</span>';
    }
    return '';
}

function buildTimetableGrid(cfg) {
    const head = document.getElementById(cfg.headId || 'tableHead');
    const body = document.getElementById(cfg.bodyId || 'tableBody');
    const tableEl = document.getElementById(cfg.tableId || 'timetable');
    const emptyEl = document.getElementById(cfg.emptyId || 'emptyState');
    head.innerHTML = '';
    body.innerHTML = '';

    if (!cfg.cellRender) {
        if (cfg.events.length === 0) {
            tableEl.style.display = 'none';
            emptyEl.style.display = 'flex';
            return;
        }
        tableEl.style.display = '';
        emptyEl.style.display = 'none';
    } else {
        tableEl.style.display = '';
    }

    const timeHeaderRow = document.createElement('tr');
    const cornerTh = document.createElement('th');
    cornerTh.className = 'time-header-col';
    cornerTh.style.cssText = 'position: sticky; left: 0; z-index: 40;';
    cornerTh.innerHTML = '<span class="card-title">Day / Time</span>';
    timeHeaderRow.appendChild(cornerTh);

    for (let i = 0; i < hours.length; i += 2) {
        const th = document.createElement('th');
        th.className = 'hour-header';
        th.colSpan = 2;
        th.innerHTML = '<span class="hour-top">' + hours[i] + '</span><span class="hour-bottom">' + (hours[i + 2] || add30min(hours[i + 1])) + '</span>';
        timeHeaderRow.appendChild(th);
    }
    head.appendChild(timeHeaderRow);

    cfg.days.forEach((day, di) => {
        const tr = document.createElement('tr');
        tr.dataset.dayIndex = di;

        const dayTd = document.createElement('td');
        let dayColClass = 'time-col';
        if (day.today) dayColClass += ' today';
        if (day.holiday || day.sunday) dayColClass += ' offday';
        dayTd.className = dayColClass;
        dayTd.innerHTML = HtmlBuilder.dayHeader(day);
        tr.appendChild(dayTd);

        if (cfg.cellRender) {
            const dayEvents = cfg.events.filter(e => e.di === di);
            const slotMap = {};
            hours.forEach((_, hi) => { slotMap[hi] = null; });
            dayEvents.forEach(e => {
                for (let hi = e.start; hi <= e.end; hi++) {
                    if (hi === e.start) {
                        slotMap[hi] = { event: e, span: e.end - e.start + 1 };
                    } else {
                        slotMap[hi] = { event: null, span: 0, occupied: true, status: e.status };
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
                cfg.cellRender(td, di, hi, day, slotMap[hi]);
                tr.appendChild(td);
            });
            body.appendChild(tr);
            return;
        }

        const dayEvents = cfg.events.filter(e => e.di === di);

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
                div.dataset.tip2 = (typeof cfg.tooltipExtra === 'function')
                    ? cfg.tooltipExtra(e)
                    : (e.venue || '');
                if (typeof cfg.statusClassFn === 'function') {
                    cfg.statusClassFn(div, e, isConflict);
                } else if (isConflict) {
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
                if (typeof cfg.replacementNoteFn === 'function') {
                    extraHtml = cfg.replacementNoteFn(e) || '';
                } else if (e.status === 'replacement' && e.remarks) {
                    extraHtml = '<span class="ev-note">(Replaced for ' + e.remarks + ')</span>';
                }

                div.innerHTML =
                    '<span class="ev-code">' + e.code + '(' + e.type + ')</span>' +
                    '<span class="ev-venue">' + e.venue + '</span>' +
                    '<span class="ev-time">' + startTime + ' - ' + endTime + '</span>' +
                    extraHtml;

                div.addEventListener('click', function() { cfg.onEventClick(e, di); });
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
}

// ───── Shared summary calculator ─────

/**
 * Compute summary stats and update DOM elements.
 * @param {Array} events - Array of event objects for the current week
 * @param {Array} days - Array of day objects from weekData
 */
function computeSummary(events, days) {
    let total = events.length;
    let replacement = 0, pending = 0, conflict = 0, hrs = 0;

    events.forEach(e => {
        if (e.status === 'replacement') replacement++;
        if (e.status === 'pending') pending++;
        if (days[e.di] && days[e.di].holiday) conflict++;
        hrs += (e.end - e.start + 1) * 0.5;
    });

    document.getElementById('sumTotal').textContent = total;
    document.getElementById('sumHours').textContent = (hrs % 1 === 0 ? hrs : hrs.toFixed(1));
    document.getElementById('sumReplacement').textContent = replacement;
    document.getElementById('sumPending').textContent = pending;
    document.getElementById('sumConflict').textContent = conflict;
}

// ───── Shared modal open helper ─────

/**
 * Open the class detail modal with common fields.
 * @param {object} cfg
 * @param {object} cfg.event - The event object
 * @param {number} cfg.dayIndex - Day index
 * @param {Array} cfg.days - Day data array
 * @param {Array} cfg.extraFields - Additional fields to append before Status
 * @param {string} [cfg.modalId='classModal'] - Modal element ID
 * @param {string} [cfg.title] - Custom title (default: event.code)
 */
function openClassModal(cfg) {
    const event = cfg.event;
    const di = cfg.dayIndex;
    const days = cfg.days;

    const isConflict = days[di] && days[di].holiday;
    const displayStatus = isConflict ? 'conflict' : event.status;

    const startStr = to12h(hours[event.start]);
    const endStr = to12h(hours[event.end + 1] || add30min(hours[event.end]));

    const statusDesc = event.status === 'pending' ? 'Replacement request awaiting approval' : event.status === 'conflict' ? 'Scheduling conflict — needs attention' : 'Scheduled class with no issues';

    const rows = [
        { label: 'Subject Code', value: event.code },
        { label: 'Subject Name', value: event.name },
        { label: 'Class Type', value: event.type === 'L' ? 'Lecture (L)' : 'Tutorial (T)' },
        { label: 'Lecturer', value: event.lecturer },
        { label: 'Venue', value: event.venue || '\u2014' },
        { label: 'Day', value: dayNames[di] || days[di].abbr },
        { label: 'Date', value: days[di].date },
        { label: 'Start Time', value: startStr, strong: true },
        { label: 'End Time', value: endStr },
        { label: 'Status', value: '<span class="badge badge-' + (event.status || 'normal') + '">' + displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1) + '</span>' },
        { label: 'Status Description', value: statusDesc },
        { label: 'Remarks', value: event.remarks || '\u2014' },
    ];

    if (event.status === 'pending') {
        rows.splice(rows.length - 1, 0,
            { label: 'Requested At', value: event.requestedAt || '\u2014' },
            { label: 'Requested By', value: event.requestedBy || '\u2014' }
        );
    }

    if (cfg.extraFields) {
        const statusIdx = rows.findIndex(f => f.label === 'Status');
        cfg.extraFields.forEach((f, i) => { rows.splice(statusIdx + i, 0, f); });
    }

    const bodyHtml = DetailModal.section('Class Information',
        rows.map(r => DetailModal.row(r.label, r.value, { strong: r.strong })).join('')
    );

    DetailModal.render({
        modalId: cfg.modalId || 'classModal',
        title: cfg.title || 'Class Details',
        subtitle: (event.code || '') + (event.name ? ' \u2014 ' + event.name : ''),
        timeline: event.status === 'pending'
            ? [
                { label: 'Submitted', time: event.requestedAt || 'Done', state: 'completed' },
                { label: 'Under Review', time: 'In progress', state: 'active', dot: 'dot-warning' },
                { label: 'Awaiting Replacement', time: 'Next', state: 'pending' },
              ]
            : null,
        body: bodyHtml
    });

    // Add "View Full Request" button to footer right side for own pending requests
    if (event.status === 'pending' && event.requestId) {
        var overlay = document.getElementById(cfg.modalId || 'classModal');
        var footer = overlay.querySelector('.modal-footer');
        if (footer) {
            // Remove any previously appended buttons to avoid duplicates
            footer.querySelectorAll('a.btn-action').forEach(function(b) { b.remove(); });
            var viewBtn = document.createElement('a');
            viewBtn.href = '/my-request-history-ui?id=' + event.requestId;
            viewBtn.className = 'btn-action';
            viewBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg> View Full Request';
            viewBtn.style.textDecoration = 'none';
            var rightGroup = footer.querySelector('.modal-footer-right');
            if (rightGroup) {
                rightGroup.appendChild(viewBtn);
            } else {
                footer.appendChild(viewBtn);
            }
        }
    }
}

// ───── Unified Detail Modal (shared inspection UI) ─────
// Renders into a `.modal.modal--detail` shell:
//   title strip  ->  optional GLOBAL timeline (never part of a tab)
//                   ->  tab bar (only when 2+ categories) + active panel
// Footer/actions are handled by the page (unchanged).
//
// Usage (page builder):
//   DetailModal.render({
//     modalId: 'modalOverlay',       // overlay element id
//     title: 'Request Details',
//     subtitle: '#12 · BMIT2201 — Data Structures',
//     status: { text: 'Pending', cls: 'status-pending' },  // optional
//     timeline: [                    // optional global timeline (always visible)
//       { label: 'Submitted', time: '12 Aug, 3:30 AM', state: 'completed' },
//       { label: 'Viewed',    time: '12 Aug, 7:00 AM', state: 'completed' },
//       { label: 'Reviewed',  time: 'Pending',          state: 'active'  }
//     ],
//     tabs: [                        // categories; tab bar auto-hidden if length <= 1
//       { key: 'info', label: 'Request Information', html: '...' },
//       ...
//     ]
//   });

const DetailModal = {
    _overlay: null,
    _body: null,

    render(cfg) {
        if (!this._overlay) {
            this._overlay = document.getElementById(cfg.modalId || 'modalOverlay');
            this._body = this._overlay.querySelector('.modal-body');
        } else if (this._overlay.id !== (cfg.modalId || 'modalOverlay')) {
            this._overlay = document.getElementById(cfg.modalId || 'modalOverlay');
            this._body = this._overlay.querySelector('.modal-body');
        }
        if (!this._overlay || !this._body) return;

        const modalEl = this._overlay.querySelector('.modal');
        if (modalEl) modalEl.classList.add('modal--detail');

        const titleEl = this._overlay.querySelector('.modal-title');
        if (titleEl) {
            titleEl.innerHTML = escHtml(cfg.title || '') +
                (cfg.subtitle ? '<span class="modal-subtitle">' + escHtml(cfg.subtitle) + '</span>' : '');
        }
        // Header status badge intentionally removed — status is shown as a
        // badge row inside the body (see DetailModal.row with a .badge value).

        let html = '';
        if (cfg.timeline && cfg.timeline.length) html += DetailModal.timeline(cfg.timeline);

        const tabs = cfg.tabs || [];
        if (tabs.length > 1) {
            html += '<div class="modal-tabs">' + tabs.map((t, i) =>
                '<button type="button" class="modal-tab' + (i === 0 ? ' active' : '') + '" data-tab="' + t.key + '">' + escHtml(t.label) + '</button>'
            ).join('') + '</div>';
        }

        html += tabs.map((t, i) =>
            '<div class="modal-tab-panel' + (i === 0 ? ' active' : '') + '" data-panel="' + t.key + '">' + (t.html || '') + '</div>'
        ).join('') + (tabs.length === 0 && cfg.body ? cfg.body : '');

        this._body.innerHTML = html;

        if (this._body.querySelector('.modal-tab')) {
            this._body.querySelectorAll('.modal-tab').forEach(btn => {
                btn.addEventListener('click', () => this.selectTab(btn.dataset.tab));
            });
        }

        this.open();
    },

    selectTab(key) {
        if (!this._body) return;
        this._body.querySelectorAll('.modal-tab').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === key);
        });
        this._body.querySelectorAll('.modal-tab-panel').forEach(panel => {
            panel.classList.toggle('active', panel.dataset.panel === key);
        });
    },

    // Render a global horizontal timeline rail (always visible, never a tab).
    // Each step may supply `dot` ('dot-success'|'dot-error'|'dot-warning'|'dot-primary')
    // so the dot colour reflects that step's status.
    timeline(steps) {
        let html = '<div class="modal-timeline">';
        steps.forEach((s, i) => {
            const st = s.state || 'pending';
            const dotCls = s.dot || '';
            html += '<div class="tl-step ' + st + ' ' + dotCls + '">';
            html += '<div class="tl-dot"></div>';
            html += '<div class="tl-label">' + escHtml(s.label) + '</div>';
            if (s.time) html += '<div class="tl-time">' + escHtml(s.time) + '</div>';
            html += '</div>';
            if (i < steps.length - 1) {
                html += '<div class="tl-connector' + (st === 'completed' ? ' completed' : '') + '"></div>';
            }
        });
        return html + '</div>';
    },

    // Render a definition section (caption + rows) inside a tab.
    section(title, rowsHtml) {
        return '<div class="detail-group"><div class="detail-group-title">' + escHtml(title) + '</div>' + (rowsHtml || '') + '</div>';
    },

    // Render a single definition row (label + value with optional variant).
    row(label, value, opts) {
        opts = opts || {};
        if (value === null || value === '' || value === undefined) return '';
        const cls = ['detail-value'];
        if (opts.strong) cls.push('detail-value--strong');
        if (opts.muted) cls.push('detail-value--muted');
        return '<div class="detail-row"><span class="detail-label">' + escHtml(label) + '</span><span class="' + cls.join(' ') + '">' + value + '</span></div>';
    },

    blank() {
        if (this._body) this._body.innerHTML = '';
    },

    open() {
        if (this._overlay) {
            this._overlay.classList.add('show');
            this._overlay.style.display = 'flex';
        }
    },

    close() {
        if (this._overlay) {
            this._overlay.classList.remove('show');
            this._overlay.style.display = 'none';
        }
    }
};

function escHtml(s) {
    return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// ───── Shared timetable keyboard + copy + swipe helpers ─────

/**
 * Initialize arrow-key week navigation and Enter-to-open-modal on timetable pages.
 * @param {object} cfg
 * @param {function} cfg.prevWeek
 * @param {function} cfg.nextWeek
 * @param {function} cfg.openModal - Callback (eventData)
 * @param {string} [cfg.modalId='classModal']
 */
function initTimetableKeyboardHandlers(cfg) {
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'SELECT' || document.getElementById(cfg.modalId || 'classModal').style.display === 'flex') return;
        if (e.key === 'ArrowLeft') { cfg.prevWeek(); }
        if (e.key === 'ArrowRight') { cfg.nextWeek(); }
        if (e.key === 'Enter' && e.target.classList.contains('event-block')) {
            const eventData = e.target.__eventData;
            if (eventData) cfg.openModal(eventData);
        }
    });
}

/**
 * Initialize click-to-copy on .ev-code elements with toast feedback.
 * @param {string} [toastId='copyToast']
 */
function initEvCodeCopy(toastId) {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('ev-code')) {
            navigator.clipboard.writeText(e.target.textContent).then(function() {
                const toastEl = document.getElementById(toastId || 'copyToast');
                if (toastEl) {
                    toastEl.textContent = 'Copied ' + e.target.textContent;
                    toastEl.classList.add('show');
                    setTimeout(function() { toastEl.classList.remove('show'); }, 1500);
                }
            });
        }
    });
}

/**
 * Initialize mobile swipe gestures on the grid-scroll element for week navigation.
 * @param {function} onPrev - Callback for swipe right
 * @param {function} onNext - Callback for swipe left
 */
function initGridSwipeGestures(onPrev, onNext) {
    if (window.innerWidth <= 768) {
        const gridScroll = document.querySelector('.grid-scroll');
        if (gridScroll) {
            initSwipeGesture({
                element: gridScroll,
                onSwipeLeft: onNext,
                onSwipeRight: onPrev
            });
        }
    }
}

// ───── Navigation ─────

function goToReplacement(code, cohort, opts, from) {
    let url = '/replacement-arrangement';
    const params = [];
    if (code) params.push('code=' + encodeURIComponent(code));
    if (cohort) params.push('cohort=' + encodeURIComponent(cohort));
    if (opts) {
        if (opts.day !== undefined) params.push('day=' + opts.day);
        if (opts.start !== undefined) params.push('start=' + opts.start);
        if (opts.end !== undefined) params.push('end=' + opts.end);
        if (opts.venue) params.push('originalVenue=' + encodeURIComponent(opts.venue));
        if (opts.duration !== undefined && opts.duration !== null && !isNaN(opts.duration)) {
            params.push('duration=' + opts.duration);
        }
    }
    if (from) params.push('from=' + from);
    if (params.length) url += '?' + params.join('&');
    window.location.href = url;
}

// ───── Table sorting ─────

function compareBy(sortState, va, vb) {
    if (va < vb) return sortState.dir === 'asc' ? -1 : 1;
    if (va > vb) return sortState.dir === 'asc' ? 1 : -1;
    return 0;
}

function makeSortableHeader(col, sortState, render) {
    const th = document.createElement('th');
    th.className = col.cls;
    if (col.tip) th.setAttribute('data-tip', col.tip);
    if (col.sortable) {
        th.classList.add('sortable');
        var arrow = '';
        if (sortState.field === col.field) {
            arrow = '<span class="sort-arrow">' + (sortState.dir === 'asc' ? '&#9650;' : '&#9660;') + '</span>';
        }
        th.innerHTML = col.label + arrow;
        th.addEventListener('click', function() {
            if (sortState.field === col.field) {
                sortState.dir = sortState.dir === 'asc' ? 'desc' : 'asc';
            } else {
                sortState.field = col.field;
                sortState.dir = 'asc';
            }
            render();
        });
    } else {
        th.textContent = col.label;
    }
    return th;
}

// ───── Pagination ─────

/**
 * Initialize a Rows Per Page selector.
 * @param {object} cfg
 * @param {string} cfg.selectId - ID of the <select> element
 * @param {string} cfg.storageKey - localStorage key (null = no persistence)
 * @param {number|string} cfg.defaultVal - Default page size ('all' for Infinity)
 * @param {function} cfg.onChange - Callback receiving the new page size (number or Infinity)
 */
function initRpp(cfg) {
    var sel = document.getElementById(cfg.selectId);
    if (!sel) return;

    if (cfg.storageKey) {
        var saved = localStorage.getItem(cfg.storageKey);
        if (saved !== null) {
            sel.value = saved;
        }
    }

    var initial = sel.value;
    var parsed = initial === 'all' ? Infinity : parseInt(initial) || cfg.defaultVal;
    cfg.onChange(parsed);

    sel.addEventListener('change', function () {
        var val = this.value;
        var pageSize = val === 'all' ? Infinity : parseInt(val) || cfg.defaultVal;
        if (cfg.storageKey) {
            localStorage.setItem(cfg.storageKey, val);
        }
        cfg.onChange(pageSize);
    });
}

function paginate(cfg) {
    const totalPages = Math.ceil(cfg.data.length / cfg.pageSize);
    const info = document.getElementById(cfg.infoId);
    if (cfg.data.length === 0) {
        info.textContent = 'Showing 0 of 0';
    } else {
        const from = (cfg.state.currentPage - 1) * cfg.pageSize + 1;
        const to = Math.min(cfg.state.currentPage * cfg.pageSize, cfg.data.length);
        info.textContent = 'Showing ' + from + '-' + to + ' of ' + cfg.data.length;
    }

    const controls = document.getElementById(cfg.controlsId);
    controls.innerHTML = '';

    const prev = document.createElement('button');
    prev.className = 'page-btn';
    prev.textContent = '\u2039';
    prev.disabled = cfg.state.currentPage <= 1;
    prev.addEventListener('click', function() {
        if (cfg.state.currentPage > 1) {
            cfg.state.currentPage--;
            cfg.render();
        }
    });
    controls.appendChild(prev);

    for (var p = 1; p <= totalPages; p++) {
        (function(page) {
            const btn = document.createElement('button');
            btn.className = 'page-btn';
            if (page === cfg.state.currentPage) btn.classList.add('active');
            btn.textContent = String(page);
            btn.addEventListener('click', function() {
                cfg.state.currentPage = page;
                cfg.render();
            });
            controls.appendChild(btn);
        })(p);
    }

    const next = document.createElement('button');
    next.className = 'page-btn';
    next.textContent = '\u203A';
    next.disabled = cfg.state.currentPage >= totalPages;
    next.addEventListener('click', function() {
        if (cfg.state.currentPage < totalPages) {
            cfg.state.currentPage++;
            cfg.render();
        }
    });
    controls.appendChild(next);
}

function updateResultCount(cfg) {
    const count = document.getElementById(cfg.elId);
    count.textContent = 'Showing ' + cfg.data.length + ' of ' + cfg.total + ' ' + cfg.label;
}

class TableController {
    constructor(config) {
        this._columns = config.columns || [];
        this._sortState = config.sortState || { field: '', dir: 'asc' };
        this._render = config.render || function() {};
    }

    sort(field) {
        if (this._sortState.field === field) {
            this._sortState.dir = this._sortState.dir === 'asc' ? 'desc' : 'asc';
        } else {
            this._sortState.field = field;
            this._sortState.dir = 'asc';
        }
        this._render();
    }

    compareBy(va, vb) {
        if (va < vb) return this._sortState.dir === 'asc' ? -1 : 1;
        if (va > vb) return this._sortState.dir === 'asc' ? 1 : -1;
        return 0;
    }

    makeHeader(col) {
        const th = document.createElement('th');
        th.className = col.cls;
        if (col.sortable) {
            th.classList.add('sortable');
            var arrow = '';
            if (this._sortState.field === col.field) {
                arrow = '<span class="sort-arrow">' + (this._sortState.dir === 'asc' ? '&#9650;' : '&#9660;') + '</span>';
            }
            th.innerHTML = col.label + arrow;
            th.addEventListener('click', () => {
                this.sort(col.field);
            });
        } else {
            th.textContent = col.label;
        }
        return th;
    }

    paginate(data, page, pageSize) {
        const totalPages = Math.ceil(data.length / pageSize);
        const from = (page - 1) * pageSize + 1;
        const to = Math.min(page * pageSize, data.length);
        return {
            totalPages,
            from,
            to,
            total: data.length
        };
    }

    updateResultCount(data, total, label) {
        return 'Showing ' + data.length + ' of ' + total + ' ' + label;
    }

    initRpp(cfg) {
        var sel = document.getElementById(cfg.selectId);
        if (!sel) return;

        if (cfg.storageKey) {
            var saved = localStorage.getItem(cfg.storageKey);
            if (saved !== null) {
                sel.value = saved;
            }
        }

        var initial = sel.value;
        var parsed = initial === 'all' ? Infinity : parseInt(initial) || cfg.defaultVal;
        cfg.onChange(parsed);

        sel.addEventListener('change', function () {
            var val = this.value;
            var pageSize = val === 'all' ? Infinity : parseInt(val) || cfg.defaultVal;
            if (cfg.storageKey) {
                localStorage.setItem(cfg.storageKey, val);
            }
            cfg.onChange(pageSize);
        });
    }

    get sortState() {
        return this._sortState;
    }
}

// ───── Modal helpers ─────

function closeOnEsc(closeFn) {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeFn();
    });
}

function closeOnOverlayClick(e, closeFn) {
    if (e.target === e.currentTarget) closeFn();
}

class ModalController {
    constructor(modalId, renderFn) {
        this._modalId = modalId;
        this._renderFn = renderFn;
        this._onKeyDown = null;
        this._onOverlayClick = null;
    }

    open(data) {
        this.close();
        this._renderFn(data);
        const modal = document.getElementById(this._modalId);
        if (modal) modal.style.display = 'flex';
        this._onKeyDown = (e) => {
            if (e.key === 'Escape') this.close();
        };
        this._onOverlayClick = (e) => {
            if (e.target === modal) this.close();
        };
        document.addEventListener('keydown', this._onKeyDown);
        if (modal) modal.addEventListener('click', this._onOverlayClick);
    }

    close() {
        const modal = document.getElementById(this._modalId);
        if (modal) modal.style.display = 'none';
        if (this._onKeyDown) {
            document.removeEventListener('keydown', this._onKeyDown);
            this._onKeyDown = null;
        }
        if (this._onOverlayClick) {
            if (modal) modal.removeEventListener('click', this._onOverlayClick);
            this._onOverlayClick = null;
        }
    }

    isOpen() {
        const modal = document.getElementById(this._modalId);
        return modal ? modal.style.display !== 'none' : false;
    }
}

// ───── BulkSelection (shared by my-request-history + request-approval) ─────
// Owns the set of selected ids and the bulk action bar UI (visibility + count),
// plus resetting row/header checkboxes on clear(). Pages own their own refresh
// (renderTable / highlight) after mutating.

class BulkSelection {
    constructor(cfg) {
        cfg = cfg || {};
        this.ids = new Set();
        this.bar = document.getElementById(cfg.barId || 'bulkActionBar');
        this.count = document.getElementById(cfg.countId || 'bulkCount');
        this.checkboxSelector = cfg.checkboxSelector || null;   // e.g. '.row-checkbox'
        this.headerCheckboxId = cfg.headerCheckboxId || null;   // e.g. 'headerCheckbox'
    }

    get size() { return this.ids.size; }

    has(id) { return this.ids.has(id); }

    add(id) { this.ids.add(id); this.updateBar(); }

    delete(id) { this.ids.delete(id); this.updateBar(); }

    toggle(id) { this.ids.has(id) ? this.ids.delete(id) : this.ids.add(id); this.updateBar(); }

    setAll(ids) { this.ids = new Set(ids || []); this.updateBar(); }

    clear() {
        this.ids.clear();
        if (this.checkboxSelector) {
            document.querySelectorAll(this.checkboxSelector).forEach(function (cb) { cb.checked = false; });
        }
        if (this.headerCheckboxId) {
            var hc = document.getElementById(this.headerCheckboxId);
            if (hc) hc.checked = false;
        }
        this.updateBar();
    }

    updateBar() {
        var bar = this.bar;
        if (!bar) return;
        if (this.ids.size === 0) {
            bar.classList.remove('visible');
            return;
        }
        bar.classList.add('visible');
        if (this.count) this.count.textContent = this.ids.size + ' selected';
    }
}

// ───── Login page helpers ─────

function togglePassword() {
    const pw = document.getElementById('password');
    const eye = document.getElementById('eye-icon');
    const isHidden = pw.type === 'password';
    pw.type = isHidden ? 'text' : 'password';
    eye.innerHTML = isHidden
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}

function ripple(e, btn) {
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    const el = document.createElement('span');
    el.className = 'ripple';
    el.style.width = el.style.height = size + 'px';
    el.style.left = x + 'px';
    el.style.top = y + 'px';
    btn.appendChild(el);
    setTimeout(() => el.remove(), 500);
}

// ───── Mobile Navigation ─────

/**
 * Initialize keyboard shortcuts for week navigation ([ and ]).
 * Each page must define either prevWeek()/nextWeek() or prevWeekFilter()/nextWeekFilter().
 */
function initWeekKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;
        if (e.key === '[') {
            e.preventDefault();
            if (typeof prevWeek === 'function') prevWeek();
            else if (typeof prevWeekFilter === 'function') prevWeekFilter();
        } else if (e.key === ']') {
            e.preventDefault();
            if (typeof nextWeek === 'function') nextWeek();
            else if (typeof nextWeekFilter === 'function') nextWeekFilter();
        }
    });
}

function initMobileNav() {
    const hamburger = document.getElementById('navHamburger');
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navDrawerOverlay');
    const closeBtn = document.getElementById('navDrawerClose');

    if (!hamburger || !drawer || !overlay) return;

    function openDrawer() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    function toggleDrawer() {
        if (drawer.classList.contains('open')) closeDrawer();
        else openDrawer();
    }

    hamburger.addEventListener('click', toggleDrawer);
    closeBtn.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);

    // Swipe left to close
    let touchStartX = 0;
    drawer.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    drawer.addEventListener('touchend', (e) => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (diff > 50) closeDrawer();
    }, { passive: true });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('open')) closeDrawer();
    });
}

// ───── Swipe Gesture ─────

function initSwipeGesture(config) {
    const { element, onSwipeLeft, onSwipeRight, threshold = 50 } = config;
    let touchStartX = 0;
    let touchStartY = 0;
    let lastSwipeTime = 0;

    element.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    element.addEventListener('touchend', (e) => {
        const now = Date.now();
        if (now - lastSwipeTime < 300) return; // debounce

        const diffX = touchStartX - e.changedTouches[0].clientX;
        const diffY = Math.abs(touchStartY - e.changedTouches[0].clientY);

        if (Math.abs(diffX) > threshold && diffY < 100) {
            lastSwipeTime = now;
            if (diffX > 0) onSwipeLeft();
            else onSwipeRight();
        }
    }, { passive: true });
}

// ───── Collapsible Day Cards ─────

function initCollapsibleCards() {
    document.querySelectorAll('.day-card-header').forEach(header => {
        header.addEventListener('click', () => {
            header.classList.toggle('collapsed');
            const content = header.nextElementSibling;
            content.classList.toggle('collapsed');
        });
    });
}

// ───── DateHelper (static utility class) ─────

class DateHelper {
    static to12h(t) {
        const [hStr, m] = t.split(':');
        const h = parseInt(hStr);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const h12 = h === 0 ? 12 : h > 12 ? h - 12 : h;
        return h12 + ':' + m + ' ' + ampm;
    }

    /**
     * Convert a 24h time range string like "09:00 – 11:00" (also "09:00-11:00",
     * "09:00 - 11:00") into "9:00 AM to 11:00 AM". Returns the input unchanged
     * when it does not look like a time range.
     */
    static format12hRange(t) {
        if (!t) return t;
        const m = String(t).match(/(\d{1,2}:\d{2})\s*[-–—]\s*(\d{1,2}:\d{2})/);
        if (!m) return t;
        return DateHelper.to12h(m[1]) + ' to ' + DateHelper.to12h(m[2]);
    }

    static formatDate(iso) {
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const parts = iso.split('-');
        return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];
    }

    static formatDateTime(iso) {
        if (!iso) return '';
        const [datePart, timePart] = iso.split('T');
        const [y, mo, d] = datePart.split('-');
        const [h, mi] = timePart.split(':');
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const day = parseInt(d);
        const month = months[parseInt(mo) - 1];
        const year = parseInt(y);
        let hh = parseInt(h);
        const mm = mi;
        const ampm = hh >= 12 ? 'PM' : 'AM';
        hh = hh === 0 ? 12 : hh > 12 ? hh - 12 : hh;
        return day + ' ' + month + ' ' + year + ', ' + hh + ':' + mm + ' ' + ampm;
    }

    static fmt(d) {
        return String(d.getDate()).padStart(2, '0') + ' ' + d.toLocaleString('en', { month: 'short' }) + ' ' + d.getFullYear();
    }

    static fmtShort(d) {
        return String(d.getDate()).padStart(2, '0') + ' ' + d.toLocaleString('en', { month: 'short' });
    }

    static weekRangeLabel(weekNum) {
        var range = weekRanges.find(function(w) { return w.value === String(weekNum); });
        if (!range) return 'Week ' + weekNum;
        var startParts = range.start.split('-');
        var endParts = range.end.split('-');
        var start = new Date(startParts[0], startParts[1] - 1, startParts[2]);
        var end = new Date(endParts[0], endParts[1] - 1, endParts[2]);
        if (window.innerWidth <= 768) {
            return 'Week ' + weekNum + ' \u00B7 ' + DateHelper.fmtShort(start) + ' ~ ' + DateHelper.fmtShort(end);
        }
        return 'Week ' + weekNum + ' \u00B7 ' + DateHelper.fmt(start) + ' ~ ' + DateHelper.fmt(end);
    }

    static add30min(t) {
        const [h, m] = t.split(':').map(Number);
        const total = h * 60 + m + 30;
        return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
    }

    static dayAbbr(day) {
        return day.substring(0, 3);
    }

    static isoDayName(iso) {
        var p = iso.split('-');
        var d = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
        return ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d.getDay()];
    }

    static getTodayMs() {
        var t = new Date();
        t.setHours(0, 0, 0, 0);
        return t.getTime();
    }
}

// ───── Backward-compatible global aliases (delegate to DateHelper) ─────

function to12h(t) { return DateHelper.to12h(t); }
function formatDate(iso) { return DateHelper.formatDate(iso); }
function formatDateTime(iso) { return DateHelper.formatDateTime(iso); }
function fmt(d) { return DateHelper.fmt(d); }
function add30min(t) { return DateHelper.add30min(t); }
function dayAbbr(day) { return DateHelper.dayAbbr(day); }
function isoDayName(iso) { return DateHelper.isoDayName(iso); }
function getTodayMs() { return DateHelper.getTodayMs(); }

// ───── HtmlBuilder (static utility class) ─────

class HtmlBuilder {
    static classBlock(r) {
        var d = DateHelper.dayAbbr(r.classDay);
        var dateStr = DateHelper.formatDate(r.classDate);
        var wn = getWeekNumber(r.classDate);
        var weekTag = wn ? ' (Week ' + wn + ')' : '';
        var timeStr = DateHelper.to12h(r.timeStart) + ' to ' + DateHelper.to12h(r.timeEnd);
        var hrs = r.duration + ' hr' + (r.duration > 1 ? 's' : '');
        return '<div class="cell-class-block"><span class="class-day-date">' + d + ', ' + dateStr + weekTag + '</span><br><span class="class-time">' + timeStr + '</span> <span class="class-duration">(' + hrs + ')</span></div>';
    }

    static replacementBlock(r, opts) {
        opts = opts || {};
        if (!r.replacementDate) return '<span style="color:var(--color-on-surface-variant);opacity:0.5">&mdash;</span>';
        var d = DateHelper.dayAbbr(DateHelper.isoDayName(r.replacementDate));
        var dateStr = DateHelper.formatDate(r.replacementDate);
        var wn = getWeekNumber(r.replacementDate);
        var weekTag = wn ? ' (Week ' + wn + ')' : '';
        var html = '<div class="cell-class-block">'
            + '<span class="class-day-date">' + d + ', ' + dateStr + weekTag + '</span><br>'
            + '<span class="class-time' + (opts.colorStatus === false ? '' : ' ' + statusClass(r.status)) + '">'
            + DateHelper.format12hRange(r.replacementTime) + '</span>';
        if (opts.showVenue !== false) {
            var venue = r.replacementVenue || r.venue || '—';
            html += '<br><span class="class-venue">'
                + (opts.slotBadge ? opts.slotBadge : '')
                + '<span class="venue-label">' + venue + '</span></span>';
        }
        return html + '</div>';
    }

    static dayHeader(day) {
        let html = '<span class="day-label">' + day.abbr + '</span><span class="date-label">' + day.date + '</span>';
        if (day.holiday) {
            html += '<span class="holiday-badge">' + (day.holidayLabel || 'Public Holiday') + '</span>';
        }
        if (day.today) {
            html += '<span class="today-badge">Today</span>';
        } else if (day.sunday && !day.holiday) {
            html += '<span class="off-badge">OFF</span>';
        }
        return html;
    }

    static requestCard(r, opts) {
        var lecturerHtml = '';
        if (opts.lookupLecturer) {
            var l = opts.lookupLecturer(r.lecturer);
            lecturerHtml = l ? l.name + ' (' + l.staffId + ')' : r.lecturer;
        } else {
            lecturerHtml = r.lecturer;
        }
        var urgencyHtml = '';
        var urgencyText = '';
        if (opts.urgencyLevel && opts.urgencyClass && opts.urgencyLabel) {
            var level = opts.urgencyLevel(r.classDate);
            urgencyHtml = '<span class="urgency-badge ' + opts.urgencyClass(level) + '">' + opts.urgencyLabel(level) + '</span>';
            urgencyText = opts.urgencyLabel(level);
        }
        var ageHtml = opts.requestAgeHtml ? opts.requestAgeHtml(r.requestedAt, urgencyText) : '';
        return '<div class="card-header">' +
            '<span class="card-code">#' + r.id + '</span>' +
            '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>' +
            '</div>' +
            '<div class="card-body">' +
            '<div><strong>Course:</strong> ' + r.courseCode + ' - ' + r.courseName + '</div>' +
            '<div><strong>Date:</strong> ' + DateHelper.formatDate(r.classDate) + ' ' + DateHelper.to12h(r.timeStart) + '</div>' +
            '<div><strong>Lecturer:</strong> ' + lecturerHtml + '</div>' +
            (urgencyHtml ? '<div><strong>Urgency:</strong> ' + urgencyHtml + '</div>' : '') +
            (ageHtml ? '<div>' + ageHtml + '</div>' : '') +
            '</div>';
    }

    static myRequestCard(r, opts) {
        var typeLabel = r.classType === 'L' ? 'Lecture' : 'Tutorial';
        var dayStr = DateHelper.dayAbbr(r.classDay);
        var dateStr = DateHelper.formatDate(r.classDate);
        var timeStr = DateHelper.to12h(r.timeStart) + ' – ' + DateHelper.to12h(r.timeEnd);
        var ageHtml = opts.requestAgeHtml ? opts.requestAgeHtml(r.requestedAt) : '';
        return '<div class="card-header">' +
            '<span class="card-code">' + r.courseCode + ' (' + typeLabel + ')</span>' +
            '<span class="badge ' + statusClass(r.status) + '">' + r.status + '</span>' +
            '</div>' +
            '<div class="card-body">' +
            '<strong>' + r.courseName + '</strong><br>' +
            dayStr + ', ' + dateStr + '<br>' +
            timeStr + ' · ' + r.venue +
            '</div>' +
            '<div class="card-footer">' +
            ageHtml +
            '<span>' + r.cohorts.join(', ') + '</span>' +
            '</div>';
    }

    static replacementHomeRow(c, opts) {
        var typeLabel = c.type === 'L' ? 'L' : 'T';
        var days = opts.daysLeft(c.date);
        var urgencyCls = opts.urgencyClass(days);
        return [
            { html: opts.index, cls: 'col-no' },
            { html: '<span class="cell-code">' + c.code + '</span><span class="cell-name">' + c.name + ' <span class="cell-type-label">(' + typeLabel + ')</span></span>', cls: 'col-code' },
            { html: opts.formatClassBlock(c), cls: 'col-original' },
            { html: '<span class="' + urgencyCls + '">' + days + ' days</span>', cls: 'col-urgency' },
            { html: c.venue, cls: 'col-venue' },
            { html: String(c.totalStudents), cls: 'col-students' },
            { html: c.cohorts.join('<br>'), cls: 'col-cohort' },
            { html: '<span class="badge ' + opts.badgeClass(c.conflictReason) + '">' + c.conflictReason + '</span>', cls: 'col-reason' },
            { html: '<button class="btn-action" onclick="event.stopPropagation(); goToReplacementWith(\'' + c.code + '\',\'' + c.date + '\')"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> Arrange Replacement</button>', cls: 'col-action' }
        ];
    }

    static replacementHomeCard(c, opts) {
        var typeLabel = c.type === 'L' ? 'Lecture' : 'Tutorial';
        var days = opts.daysLeft(c.date);
        var urgencyCls = opts.urgencyClass(days);
        return '<div class="rc-header">' +
            '<span class="rc-code">' + c.code + ' <span class="cell-type-label">(' + typeLabel + ')</span></span>' +
            '<span class="badge ' + opts.badgeClass(c.conflictReason) + '">' + c.conflictReason + '</span>' +
            '</div>' +
            '<div class="rc-body">' +
            '<strong>' + c.name + '</strong><br>' +
            c.day + ', ' + DateHelper.formatDate(c.date) + ' · Week ' + getWeekNumber(c.date) + '<br>' +
            DateHelper.to12h(c.timeStart) + ' – ' + DateHelper.to12h(c.timeEnd) + ' · ' + c.venue +
            '</div>' +
            '<div class="rc-footer">' +
            '<span class="' + urgencyCls + '">' + days + ' days left</span>' +
            '<span>' + c.cohorts.join(', ') + '</span>' +
            '</div>';
    }

    static rowTip(/* ...parts */) {
        return Array.prototype.filter
            .call(arguments, function(p) { return p != null && p !== ''; })
            .join(' | ');
    }

    static tipAttr(tooltipStr) {
        return ' data-tip="' + tooltipStr.replace(/"/g, '&quot;') + '"';
    }
}

// ───── Skeleton Loading ─────

const SkeletonLoader = {
    show(container, type = 'rows', count = 5) {
        container.innerHTML = '';
        const isTbody = container.tagName === 'TBODY';
        for (let i = 0; i < count; i++) {
            if (isTbody) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 9;
                td.innerHTML = '<div class="skeleton skeleton-row"></div>';
                tr.appendChild(td);
                container.appendChild(tr);
            } else {
                const el = document.createElement('div');
                el.className = `skeleton skeleton-${type === 'rows' ? 'row' : 'card'}`;
                container.appendChild(el);
            }
        }
    },
    hide(container) {
        if (!container) return;
        container.querySelectorAll('.skeleton, .skeleton-row, .skeleton-card').forEach(el => el.remove());
        container.querySelectorAll('tr').forEach(tr => {
            if (tr.querySelector('.skeleton')) tr.remove();
        });
    },
    with(callback, container, count = 10, delay = 400) {
        this.show(container, 'rows', count);
        const hide = () => setTimeout(() => this.hide(container), delay);
        setTimeout(() => {
            try {
                const result = callback();
                if (result && typeof result.then === 'function') {
                    return result.then(hide, (err) => { hide(); throw err; });
                }
                hide();
            } catch (err) { hide(); throw err; }
        }, 50);
    },
    showSummary() {
        document.querySelectorAll('.summary-card .summary-value').forEach(el => {
            el.dataset.original = el.innerHTML;
            el.innerHTML = '<div class="skeleton" style="height:24px;width:40px;display:inline-block"></div>';
        });
    },
    hideSummary() {
        document.querySelectorAll('.summary-card .summary-value').forEach(el => {
            delete el.dataset.original;
        });
    }
};

// ───── Scroll Restoration ─────

function saveScrollPosition(key) {
    sessionStorage.setItem('scroll_' + key, window.scrollY);
}

function restoreScrollPosition(key) {
    const pos = sessionStorage.getItem('scroll_' + key);
    if (pos) window.scrollTo(0, parseInt(pos));
}

function clearScrollPosition(key) {
    sessionStorage.removeItem('scroll_' + key);
}

function initScrollRestore(pageKey) {
    window.addEventListener('pageshow', (e) => {
        if (e.persisted) restoreScrollPosition(pageKey);
    });
    document.querySelectorAll('.nav-item, .nav-drawer-item').forEach(link => {
        link.addEventListener('click', () => clearScrollPosition(pageKey));
    });
    window._scrollToTop = function() { window.scrollTo(0, 0); };
}

// Auto-save on scroll (debounced)
let scrollTimer;
window.addEventListener('scroll', () => {
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(() => {
        const pageKey = document.body.dataset.page;
        if (pageKey) saveScrollPosition(pageKey);
    }, 200);
}, { passive: true });

// ───── ToastManager (singleton class) ─────

class ToastManager {
    constructor() {
        this._timer = null;
    }

    show(message, undoCallback, duration = 5000, linkText = '', linkUrl = '', details = '') {
        const bar = document.getElementById('toastBar');
        if (!bar) return;

        const msgEl = bar.querySelector('.toast-message');
        const detailsEl = bar.querySelector('.toast-details');
        const undoBtn = bar.querySelector('.toast-undo');
        const linkEl = bar.querySelector('.toast-link');

        msgEl.textContent = message;

        if (details && detailsEl) {
            detailsEl.textContent = details;
            detailsEl.style.display = 'block';
        } else if (detailsEl) {
            detailsEl.style.display = 'none';
        }

        if (undoCallback) {
            undoBtn.style.display = 'inline-block';
            undoBtn.onclick = () => {
                undoCallback();
                this.dismiss();
            };
        } else {
            undoBtn.style.display = 'none';
        }

        if (linkText && linkUrl && linkEl) {
            linkEl.textContent = linkText;
            linkEl.href = linkUrl;
            linkEl.style.display = 'inline-block';
        } else if (linkEl) {
            linkEl.style.display = 'none';
        }

        bar.classList.add('visible');

        clearTimeout(this._timer);
        this._timer = setTimeout(() => this.dismiss(), duration);
    }

    dismiss() {
        const bar = document.getElementById('toastBar');
        if (bar) bar.classList.remove('visible');
        clearTimeout(this._timer);
    }
}

const toast = new ToastManager();

// ───── State Persistence (localStorage) ─────

/**
 * Generic state persistence helper.
 * @param {string} storageKey - localStorage key
 * @param {object} config - { fields: [{id, type, key, transform?}] }
 *   type: 'select' | 'checkbox-group' | 'variable'
 *   id: DOM element id (for select/checkbox-group)
 *   key: property name in saved state
 *   transform: optional fn(val) => savedValue
 */
function createStatePersistence(storageKey, config) {
    return {
        save(extraFields) {
            try {
                const state = {};
                config.fields.forEach(f => {
                    if (f.type === 'select') {
                        const el = document.getElementById(f.id);
                        state[f.key] = f.transform ? f.transform(el.value) : el.value;
                    } else if (f.type === 'checkbox-group') {
                        const checkboxes = document.querySelectorAll(f.selector || '#' + f.id + ' input[type="checkbox"]');
                        const vals = {};
                        checkboxes.forEach(cb => { vals[cb.value] = cb.checked; });
                        state[f.key] = vals;
                    } else if (f.type === 'variable') {
                        state[f.key] = window[f.varName];
                    }
                });
                if (extraFields) Object.assign(state, extraFields);
                localStorage.setItem(storageKey, JSON.stringify(state));
            } catch (e) { /* ignore */ }
        },
        restore(defaults) {
            let state = null;
            try { state = JSON.parse(localStorage.getItem(storageKey) || 'null'); } catch (e) { state = null; }
            if (!state) return defaults || {};

            config.fields.forEach(f => {
                if (state[f.key] === undefined) return;
                if (f.type === 'select') {
                    const el = document.getElementById(f.id);
                    if (el) el.value = state[f.key];
                } else if (f.type === 'checkbox-group') {
                    const checkboxes = document.querySelectorAll(f.selector || '#' + f.id + ' input[type="checkbox"]');
                    checkboxes.forEach(cb => {
                        cb.checked = state[f.key][cb.value] !== false;
                    });
                } else if (f.type === 'variable') {
                    window[f.varName] = state[f.key];
                }
            });

            return state;
        }
    };
}



const dayNames = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];

// ───── Week Filter Navigation ─────

/**
 * Handle week filter change. Pages may override the rebuild behaviour by
 * passing an options object with `onRebuild` (e.g. renderTable) and
 * `onBeforeRebuild` (e.g. saveFilters / reset page state).
 * Falling back to globals keeps the simple `<select onchange="weekFilterChanged(this.value)">`
 * working for HOME.
 * @param {object|*} opts - Options object, or ignored value for simple usage
 */
function weekFilterChanged(opts) {
    if (opts && typeof opts === 'object') {
        if (typeof opts.onBeforeRebuild === 'function') opts.onBeforeRebuild();
        if (typeof opts.onRebuild === 'function') {
            opts.onRebuild();
        } else if (typeof buildTable === 'function') {
            buildTable();
        } else if (typeof renderTable === 'function') {
            renderTable();
        }
    } else {
        if (typeof pageState !== 'undefined' && pageState) pageState.currentPage = 1;
        if (typeof buildTable === 'function') {
            buildTable();
        } else if (typeof renderTable === 'function') {
            renderTable();
        }
    }
    updateWeekArrowState();
}

function prevWeekFilter() {
    const sel = document.getElementById('weekFilter');
    if (sel.selectedIndex > 0) {
        sel.selectedIndex--;
        sel.dispatchEvent(new Event('change'));
    }
}

function nextWeekFilter() {
    const sel = document.getElementById('weekFilter');
    if (sel.selectedIndex < sel.options.length - 1) {
        sel.selectedIndex++;
        sel.dispatchEvent(new Event('change'));
    }
}

// ── Promoted from my-request-history (shared helpers) ──

let _weekRanges = null;
function buildWeekRanges() {
    const monthMap = { 'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3, 'May': 4, 'Jun': 5, 'Jul': 6, 'Aug': 7, 'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11 };
    function parseDate(s) {
        const p = s.trim().split(' ');
        return new Date(parseInt(p[2]), monthMap[p[1]], parseInt(p[0]));
    }
    const data = generateWeekData();
    return data.map(function(w, i) {
        const parts = w.range.split(' ~ ');
        const start = parseDate(parts[0]);
        const end = parseDate(parts[1]);
        const iso = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        return {
            value: String(i + 1),
            label: w.label + ' \u00b7 ' + w.range,
            labelShort: w.label + ' \u00b7 ' + w.rangeShort,
            start: iso(start),
            end: iso(end),
        };
    });
}
Object.defineProperty(window, 'weekRanges', {
    get: function() {
        if (!_weekRanges) _weekRanges = buildWeekRanges();
        return _weekRanges;
    }
});

function statusClass(status) {
    const map = {
        'Pending': 'status-pending',
        'Approved': 'status-approved',
        'Rejected': 'status-rejected',
        'Cancelled': 'status-cancelled',
        'Completed': 'status-completed'
    };
    return map[status] || '';
}

/**
 * Build HTML for a request-age indicator (shared: my-request-history, request-approval).
 * Age is computed relative to ACTUAL today (the mock data is generated relative
 * to `new Date()`), so "yesterday" shows as Yesterday, not a drift from a fixed date.
 *
 * Color legend:
 *   Today / Yesterday (≤1 day)  → age-fresh  (primary / green)
 *   2–3 days                    → age-waiting (tertiary / amber)
 *   4+ days                     → age-stale  (error / red)
 *
 * Uses `data-tip` (rendered above the element by initDataTipTooltips) instead of
 * the native `title`, which browsers draw below.
 * @param {string} requestedAt - ISO/timestamp string of when the request was made
 * @param {string} [urgencyLabel] - Optional "Urgent"/"Normal" to surface in the tooltip
 * @returns {string} HTML string
 */
function requestAgeHtml(requestedAt, urgencyLabel) {
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    const req = new Date(requestedAt);
    if (isNaN(req.getTime())) return '<div class="request-age request-age--unknown">—</div>';
    req.setHours(0, 0, 0, 0);
    const diff = Math.round((now.getTime() - req.getTime()) / 86400000);
    if (diff < 0) return '<div class="request-age request-age--unknown">—</div>';
    let label;
    if (diff === 0) {
        label = 'Today';
    } else if (diff === 1) {
        label = 'Yesterday';
    } else {
        label = diff + ' days ago';
    }
    const cls = diff <= 1 ? 'age-fresh' : diff <= 3 ? 'age-waiting' : 'age-stale';
    const tip = urgencyLabel
        ? label + ' · Urgency: ' + urgencyLabel
        : label + ' · ' + (cls === 'age-fresh' ? 'recently submitted' : cls === 'age-waiting' ? '2–3 days old' : '4+ days old');
    return '<div class="request-age ' + cls + '" data-tip="' + tip + '">' + label + '</div>';
}


function getWeekRange(weekVal) {
    const found = weekRanges.find(function(w) { return w.value === weekVal; });
    return found || null;
}

function isInWeek(classDate, weekVal) {
    if (weekVal === 'all') return true;
    const range = getWeekRange(weekVal);
    if (!range) return true;
    return classDate >= range.start && classDate <= range.end;
}

function getWeekNumber(iso) {
    for (var i = 0; i < weekRanges.length; i++) {
        if (iso >= weekRanges[i].start && iso <= weekRanges[i].end) return weekRanges[i].value;
    }
    return '';
}

/**
 * Populate a week <select> with options from weekRanges or weekData.
 * @param {string} selectId - ID of the <select> element
 * @param {object} cfg - { includeAll:bool, ranges:bool, selected:value, labelFn }
 */
function populateWeekSelect(selectId, cfg) {
    const sel = document.getElementById(selectId);
    if (!sel) return;
    cfg = cfg || {};
    const isMobile = window.innerWidth <= 768;
    const useRanges = cfg.ranges !== false; // default: weekRanges (string "1".."14")
    const source = useRanges ? weekRanges : weekData;

    let html = '';
    if (cfg.includeAll) html += '<option value="all">All Weeks</option>';

    source.forEach(function(w, i) {
        const value = useRanges ? w.value : i;
        let label;
        if (cfg.labelFn) {
            label = cfg.labelFn(w, i, isMobile, useRanges);
        } else if (useRanges) {
            label = isMobile ? (w.labelShort || w.label) : w.label;
        } else {
            label = isMobile ? (w.label + ' \u00B7 ' + w.rangeShort) : (w.label + ' \u00B7 ' + w.range);
        }
        html += '<option value="' + value + '">' + label + '</option>';
    });

    sel.innerHTML = html;
    if (cfg.selected !== undefined) {
        sel.value = String(cfg.selected);
    }
    return sel;
}

function updateNavBadge() {
    var count = (window.MockData && MockData.approvalRequests)
        ? MockData.approvalRequests.filter(function(r) { return r.status === 'Pending'; }).length
        : 0;
    var badge = document.getElementById('navPendingBadge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

/**
 * Rebuild a table's body with the standard skeleton-loading recipe:
 * reset page → scroll to top → show skeleton → render → hide skeleton.
 * @param {object} cfg
 * @param {function} cfg.render - Function that renders the table body
 * @param {function} [cfg.after] - Extra logic to run after render (e.g. updateWeekArrowState)
 * @param {string} [cfg.bodyId='tableBody'] - tbody element ID
 * @param {number} [cfg.count=10] - Skeleton row count
 * @param {number} [cfg.delay=400] - Skeleton delay
 */
function rebuildTable(cfg) {
    if (typeof pageState !== 'undefined' && pageState) pageState.currentPage = 1;
    if (typeof currentPage !== 'undefined') currentPage = 1;
    window.scrollTo(0, 0);
    SkeletonLoader.showSummary();
    SkeletonLoader.with(function() {
        cfg.render();
        SkeletonLoader.hideSummary();
        if (typeof cfg.after === 'function') cfg.after();
    }, document.getElementById(cfg.bodyId || 'tableBody'), cfg.count || 10, cfg.delay || 400);
}

// ───── BackNavigator (dynamic back button) ─────

class BackNavigator {
    static #routes = {
        'replacement-home': '/replacement-home-ui',
        'my-request-history': '/my-request-history-ui',
        'venue-timetable': '/venue-timetable-ui',
        'my-timetable': '/my-timetable-ui'
    };

    static getDefault() {
        return '/replacement-home-ui';
    }

    static getBackUrl() {
        var params = new URLSearchParams(window.location.search);
        var from = params.get('from');
        return BackNavigator.#routes[from] || BackNavigator.getDefault();
    }

    static navigate() {
        window.location.href = BackNavigator.getBackUrl();
    }
}

// ───── Data-Tip Tooltip (above the element, avoids grid-wrapper overflow:hidden) ─────
// Shows a fixed tooltip ABOVE any element carrying a `data-tip` attribute
// (table headers, legend items, icon buttons, etc.). Clamps to the viewport
// so the tip is always fully visible.

function initDataTipTooltips() {
    // Idempotent: the global layout init + any per-page legacy initHeaderTooltips()
    // alias both call this — guard so only ONE tooltip div + listener set is created.
    if (window.__dataTipTooltipsInitialized) return;
    window.__dataTipTooltipsInitialized = true;

    var tip = document.createElement('div');
    tip.className = 'data-tip-tooltip';
    document.body.appendChild(tip);

    function findTip(node) {
        if (!node) return null;
        return node.closest ? node.closest('[data-tip]') : null;
    }
    function hide() {
        tip.style.opacity = '0';
        tip.style.visibility = 'hidden';
    }

    document.addEventListener('mouseenter', function(e) {
        var el = findTip(e.target);
        if (!el) return;
        var r = el.getBoundingClientRect();
        tip.textContent = el.getAttribute('data-tip');
        // Force layout so offsetWidth/offsetHeight are accurate (text just set)
        tip.style.visibility = 'hidden';
        tip.style.opacity = '1';
        tip.style.left = '0px';
        tip.style.top = '0px';
        var tw = tip.offsetWidth;
        var th = tip.offsetHeight;
        var left, top;
        if (el.getAttribute('data-tip-pos') === 'left') {
            // Tooltip to the LEFT, vertically centred (top-bar buttons)
            left = r.left - tw - 8;
            top = r.top + (r.height / 2) - (th / 2);
            left = Math.max(4, left);
            top = Math.max(4, top);
        } else {
            left = r.left + (r.width / 2) - (tw / 2);
            left = Math.max(4, Math.min(left, window.innerWidth - tw - 4));
            top = r.top - th - 6;
            if (top < 4) {
                // Not enough room above — show below the element instead
                top = r.bottom + 6;
            }
        }
        tip.style.left = left + 'px';
        tip.style.top = top + 'px';
        tip.style.opacity = '1';
        tip.style.visibility = 'visible';
    }, true);

    document.addEventListener('mouseleave', function(e) {
        if (findTip(e.target)) hide();
    }, true);

    // Safety: hide when focusing away / on scroll so stale tips don't linger
    document.addEventListener('mouseout', function(e) {
        if (findTip(e.relatedTarget)) return;
        if (findTip(e.target)) hide();
    }, true);
}

// Back-compat alias: pages that used initHeaderTooltips() before.
function initHeaderTooltips() {
    initDataTipTooltips();
}

/* ═══════════════════════════════════════════════════════════════
   VenueDropdown — nested grouped venue picker
   ═══════════════════════════════════════════════════════════════ */
class VenueDropdown {
    /**
     * @param {HTMLElement} container  — .venue-dd element
     * @param {Object}      opts
     * @param {Array}        opts.venues         — MockData.venues array
     * @param {Function}     opts.onSelect       — callback(code) when venue selected
     * @param {string}       [opts.storageKey]   — localStorage key for favourites
     * @param {number}       [opts.maxFavourites] — max favourites (default 5)
     * @param {string}       [opts.initialCode]  — pre-selected venue code
     * @param {Function}     [opts.filter]       — filter function(v) for venues
     */
    constructor(container, opts) {
        this.container = container;
        this.venues = (opts.venues || []).slice();
        this.onSelect = opts.onSelect || function() {};
        this.storageKey = opts.storageKey || 'venueFavourites';
        this.recentStorageKey = opts.recentStorageKey || 'venueRecent';
        this.maxFavourites = opts.maxFavourites || 5;
        this.maxRecent = opts.maxRecent || 5;
        this.filter = opts.filter || null;
        this.selectedCode = opts.initialCode || null;

        this.trigger = container.querySelector('.venue-dd-trigger');
        this.panel = container.querySelector('.venue-dd-panel');

        this._onDocClick = this._onDocClick.bind(this);
        this._onKeydown = this._onKeydown.bind(this);

        this._buildGroups();
        this._bindEvents();

        if (this.selectedCode) this._updateTrigger();
    }

    /* ── Public API ─────────────────────────────────────────── */

    select(code) {
        this.selectedCode = code;
        this._updateTrigger();
        this._updateActive();
        this._close();
        this.onSelect(code);
    }

    getSelected() {
        return this.selectedCode;
    }

    getFavourites() {
        try { return JSON.parse(localStorage.getItem(this.storageKey) || '[]'); }
        catch (e) { return []; }
    }

    toggleFavourite(code, starEl) {
        const favs = this.getFavourites();
        const idx = favs.indexOf(code);
        if (idx === -1) {
            if (favs.length >= this.maxFavourites) return false;
            favs.push(code);
        } else {
            favs.splice(idx, 1);
        }
        localStorage.setItem(this.storageKey, JSON.stringify(favs));
        this.refreshFavourites();
        if (starEl) this.updateFavStar(starEl, code);
        return true;
    }

    refreshFavourites() {
        this._buildGroups();
        this._updateActive();
    }

    getRecent() {
        try { return JSON.parse(localStorage.getItem(this.recentStorageKey) || '[]'); }
        catch (e) { return []; }
    }

    updateRecent(code) {
        let recent = this.getRecent();
        recent = recent.filter(c => c !== code);
        recent.unshift(code);
        if (recent.length > this.maxRecent) recent = recent.slice(0, this.maxRecent);
        localStorage.setItem(this.recentStorageKey, JSON.stringify(recent));
    }

    setFilter(filterFn) {
        this.filter = filterFn;
        this._buildGroups();
        this._updateActive();
    }

    destroy() {
        document.removeEventListener('click', this._onDocClick, true);
        document.removeEventListener('keydown', this._onKeydown, true);
    }

    updateFavStar(starEl, code) {
        if (!starEl || !code) return;
        const favs = this.getFavourites();
        const isFav = favs.includes(code);
        starEl.textContent = isFav ? '\u2605' : '\u2606';
        starEl.classList.toggle('active', isFav);
        starEl.disabled = favs.length >= this.maxFavourites && !isFav;
        starEl.dataset.tip = starEl.disabled ? 'Maximum 5 Favourites' : (isFav ? 'Remove from Favourites' : 'Add to Favourites');
    }

    /* ── Build columns ──────────────────────────────────────── */

    _buildGroups() {
        this.panel.innerHTML = '';
        this.columns = [];
        this.activeType = null;
        this.activeBlock = null;
        this.activeFloor = null;
        this.activeUnit = null;

        // Four hierarchy columns: Type | Block | Floor | Room
        for (let i = 0; i < 4; i++) {
            const col = document.createElement('div');
            col.className = 'venue-col';
            this.panel.appendChild(col);
            this.columns.push(col);
        }

        this._buildTypeColumn();
        this._clearColumnsFrom(1);
    }

    _getVenues() {
        return this.filter ? this.venues.filter(this.filter) : this.venues;
    }

    _clearColumnsFrom(start) {
        for (let i = start; i < this.columns.length; i++) {
            const col = this.columns[i];
            col.innerHTML = '';
            col.classList.remove('visible');
        }
    }

    _addSectionHeader(col, label) {
        const header = document.createElement('div');
        header.className = 'venue-col-header';
        header.textContent = label;
        col.appendChild(header);
    }

    _buildTypeColumn() {
        const col = this.columns[0];
        col.innerHTML = '';
        col.classList.add('visible');
        const venues = this._getVenues();

        // Favourites — expandable 2-level group: ★ Favourites › [rooms]
        if (this.getFavourites().some(code => venues.some(v => v.code === code))) {
            const row = this._createParentItem('★ Favourites', { unit: 'favourites' });
            row.addEventListener('mouseenter', () => this._buildUnitColumn('favourites'));
            row.addEventListener('click', (e) => {
                e.stopPropagation();
                this._buildUnitColumn('favourites');
            });
            col.appendChild(row);
        }

        // Recent — expandable 2-level group: Recent › [rooms]
        if (this.getRecent().some(code => venues.some(v => v.code === code))) {
            const row = this._createParentItem('Recent', { unit: 'recent' });
            row.addEventListener('mouseenter', () => this._buildUnitColumn('recent'));
            row.addEventListener('click', (e) => {
                e.stopPropagation();
                this._buildUnitColumn('recent');
            });
            col.appendChild(row);
        }

        this._addSectionHeader(col, 'Types');

        if (venues.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'venue-dd-empty';
            empty.textContent = 'No venues available';
            col.appendChild(empty);
            return;
        }

        const typeOrder = ['Tutorial', 'LectureHall', 'Lab', 'CiscoLab'];
        const typeLabels = { Tutorial: 'Tutorial', LectureHall: 'Lecture Hall', Lab: 'Lab', CiscoLab: 'CiscoLab' };
        typeOrder.forEach(type => {
            const catVenues = venues.filter(v => v.type === type);
            if (catVenues.length === 0) return;
            const row = this._createParentItem(typeLabels[type] || type, { type });
            row.addEventListener('mouseenter', () => this._buildBlockColumn(type));
            row.addEventListener('click', (e) => {
                e.stopPropagation();
                this._buildBlockColumn(type);
            });
            col.appendChild(row);
        });

        this._updateActiveParents();
    }

    _buildUnitColumn(unit) {
        this.activeType = null;
        this.activeBlock = null;
        this.activeFloor = null;
        this.activeUnit = unit;
        this._clearColumnsFrom(1);
        const col = this.columns[1];
        col.classList.add('visible');
        this._addSectionHeader(col, unit === 'favourites' ? 'Favourites' : 'Recent');

        const venues = this._getVenues();
        const codes = unit === 'favourites' ? this.getFavourites() : this.getRecent();
        const list = codes
            .map(code => venues.find(v => v.code === code))
            .filter(Boolean);

        if (list.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'venue-dd-empty';
            empty.textContent = 'No items';
            col.appendChild(empty);
            return;
        }

        list.forEach(v => col.appendChild(this._createRoomItem(v, unit === 'favourites' ? 'favourite' : 'recent')));
        this._updateActiveParents();
    }

    _buildBlockColumn(type) {
        this.activeType = type;
        this.activeUnit = null;
        this._clearColumnsFrom(1);
        const col = this.columns[1];
        col.classList.add('visible');
        this._addSectionHeader(col, 'Blocks');

        const venues = this._getVenues();
        const catVenues = venues.filter(v => v.type === type);
        const blocks = [...new Set(catVenues.map(v => v.code.charAt(0)))].sort();

        if (blocks.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'venue-dd-empty';
            empty.textContent = 'No blocks';
            col.appendChild(empty);
            return;
        }

        blocks.forEach(block => {
            const row = this._createParentItem('Block ' + block, { block });
            row.addEventListener('mouseenter', () => this._buildFloorColumn(type, block));
            row.addEventListener('click', (e) => {
                e.stopPropagation();
                this._buildFloorColumn(type, block);
            });
            col.appendChild(row);
        });

        this._updateActiveParents();
    }

    _buildFloorColumn(type, block) {
        this.activeType = type;
        this.activeBlock = block;
        this._clearColumnsFrom(2);
        const col = this.columns[2];
        col.classList.add('visible');
        this._addSectionHeader(col, 'Floors');

        const venues = this._getVenues();
        const blockVenues = venues.filter(v => v.type === type && v.code.charAt(0) === block);

        // Floor from 2nd char: 0 = Ground, 1+ = Floor N
        const floorMap = {};
        blockVenues.forEach(v => {
            const d = parseInt(v.code.charAt(1));
            const label = d === 0 ? 'Ground Floor' : 'Floor ' + d;
            if (!floorMap[label]) floorMap[label] = [];
            floorMap[label].push(v);
        });
        const floorOrder = Object.keys(floorMap).sort((a, b) => {
            const da = parseInt(a) || 0;
            const db = parseInt(b) || 0;
            return da - db;
        });

        if (floorOrder.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'venue-dd-empty';
            empty.textContent = 'No floors';
            col.appendChild(empty);
            return;
        }

        floorOrder.forEach(floor => {
            const row = this._createParentItem(floor, { floor });
            row.addEventListener('mouseenter', () => this._buildRoomColumn(type, block, floor));
            row.addEventListener('click', (e) => {
                e.stopPropagation();
                this._buildRoomColumn(type, block, floor);
            });
            col.appendChild(row);
        });

        this._updateActiveParents();
    }

    _buildRoomColumn(type, block, floor) {
        this.activeType = type;
        this.activeBlock = block;
        this.activeFloor = floor;
        this._clearColumnsFrom(3);
        const col = this.columns[3];
        col.classList.add('visible');
        this._addSectionHeader(col, 'Rooms');

        const venues = this._getVenues();
        const floorVenues = venues.filter(v => {
            if (v.type !== type || v.code.charAt(0) !== block) return false;
            const d = parseInt(v.code.charAt(1));
            const label = d === 0 ? 'Ground Floor' : 'Floor ' + d;
            return label === floor;
        });

        if (floorVenues.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'venue-dd-empty';
            empty.textContent = 'No rooms';
            col.appendChild(empty);
            return;
        }

        floorVenues.forEach(v => col.appendChild(this._createRoomItem(v, 'hierarchy')));
        this._updateActiveParents();
    }

    _createParentItem(label, data) {
        const row = document.createElement('div');
        row.className = 'venue-col-item venue-col-item-parent';
        for (const k in data) row.dataset[k] = data[k];

        const name = document.createElement('span');
        name.className = 'venue-col-item-name';
        name.textContent = label;
        row.appendChild(name);

        const chevron = document.createElement('span');
        chevron.className = 'venue-col-item-chevron';
        chevron.textContent = '›';
        row.appendChild(chevron);

        return row;
    }

    _createRoomItem(v, groupType) {
        const row = document.createElement('div');
        row.className = 'venue-col-item venue-col-item-room';
        row.dataset.code = v.code;
        if (v.code === this.selectedCode) row.classList.add('selected');

        const favs = this.getFavourites();
        const isFav = favs.includes(v.code);

        if (groupType === 'favourite') {
            const icon = document.createElement('span');
            icon.className = 'venue-col-item-icon';
            icon.textContent = '⭐';
            row.appendChild(icon);
        } else if (groupType === 'recent') {
            const icon = document.createElement('span');
            icon.className = 'venue-col-item-icon';
            icon.textContent = '🕐';
            row.appendChild(icon);
        } else if (isFav) {
            const icon = document.createElement('span');
            icon.className = 'venue-col-item-icon';
            icon.textContent = '⭐';
            row.appendChild(icon);
        }

        const name = document.createElement('span');
        name.className = 'venue-col-item-name';
        name.textContent = v.code;
        row.appendChild(name);

        const meta = document.createElement('span');
        meta.className = 'venue-col-item-meta';
        meta.textContent = v.capacity + ' seats';
        row.appendChild(meta);

        if (v.code === this.selectedCode) {
            const check = document.createElement('span');
            check.className = 'venue-col-item-check';
            check.textContent = '✓';
            row.appendChild(check);
        }

        row.addEventListener('click', (e) => {
            e.stopPropagation();
            this.select(v.code);
        });

        return row;
    }

    _updateActiveParents() {
        if (!this.columns) return;
        this.columns[0].querySelectorAll('.venue-col-item-parent').forEach(row => {
            if (row.dataset.type) {
                row.classList.toggle('active', row.dataset.type === this.activeType);
            } else if (row.dataset.unit) {
                row.classList.toggle('active', row.dataset.unit === this.activeUnit);
            }
        });
        this.columns[1].querySelectorAll('.venue-col-item-parent').forEach(row => {
            row.classList.toggle('active', row.dataset.block === this.activeBlock);
        });
        this.columns[2].querySelectorAll('.venue-col-item-parent').forEach(row => {
            row.classList.toggle('active', row.dataset.floor === this.activeFloor);
        });
    }

    /* ── Open / Close ───────────────────────────────────────── */

    _open() {
        this.container.classList.add('open');
        // Reset to just the Type column on every open
        this.activeType = null;
        this.activeBlock = null;
        this.activeFloor = null;
        this.activeUnit = null;
        this._clearColumnsFrom(0);
        this._buildTypeColumn();
        document.addEventListener('click', this._onDocClick, true);
        document.addEventListener('keydown', this._onKeydown, true);
    }

    _close() {
        this.container.classList.remove('open');
        document.removeEventListener('click', this._onDocClick, true);
        document.removeEventListener('keydown', this._onKeydown, true);
    }

    _toggle() {
        if (this.container.classList.contains('open')) {
            this._close();
        } else {
            this._open();
        }
    }

    /* ── Event handlers ─────────────────────────────────────── */

    _bindEvents() {
        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this._toggle();
        });
    }

    _onDocClick(e) {
        if (!this.container.contains(e.target)) {
            this._close();
        }
    }

    _onKeydown(e) {
        if (e.key === 'Escape') this._close();
    }

    /* ── Helpers ────────────────────────────────────────────── */

    _updateTrigger() {
        if (!this.selectedCode) {
            this.trigger.textContent = 'Select a venue ▾';
            return;
        }
        const v = this.venues.find(x => x.code === this.selectedCode);
        if (v) {
            const typeLabel = v.type === 'LectureHall' ? 'Lecture Hall' : v.type;
            this.trigger.textContent = v.code + ' — ' + typeLabel + ' (' + v.capacity + ' seats) ▾';
        }
    }

    _updateActive() {
        if (!this.columns) return;
        this.columns.forEach(col => {
            col.querySelectorAll('.venue-col-item-room').forEach(row => {
                const isSel = row.dataset.code === this.selectedCode;
                row.classList.toggle('selected', isSel);
                let check = row.querySelector('.venue-col-item-check');
                if (isSel && !check) {
                    check = document.createElement('span');
                    check.className = 'venue-col-item-check';
                    check.textContent = '✓';
                    row.appendChild(check);
                } else if (!isSel && check) {
                    check.remove();
                }
            });
        });
        this._updateActiveParents();
    }
}
