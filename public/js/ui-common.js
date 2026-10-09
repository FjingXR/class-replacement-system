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
    /* R-3 (round-3): logo goes to the page's role home (staff → My Timetable,
       student → Student My Timetable); pages without a homeUrl land on the
       welcome view. The layout emits window.PAGE_HOME. */
    window.location.href = window.PAGE_HOME || '/';
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
    const today = new Date(getTodayMs());
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
    /* mark the semester's earliest bookable day (the lead-time boundary) —
       grid builders render the "Bookings open" chip in its time-col when the
       page opts in (cfg.bookableBadge) */
    const fb = firstBookableDay(arr);
    if (fb) arr[fb.week].days[fb.day].firstBookable = true;
    return arr;
}

// ───── Lead-Time Rule (3 working days) ─────
/**
 * The first selectable date under the lead-time rule: the 3rd working day
 * from the MockData.mockNow anchor (Mon-Fri, skipping the page's holiday
 * flags). A slot on this date IS selectable (>= 3, per the agreed boundary).
 * @param {Array} weekData — page week data (days carry .date 'DD Mon YYYY' + .holiday)
 * @returns {Date}
 */
function leadTimeCutoff(weekData) {
    const today = new Date(getTodayMs());
    const holidays = new Set();
    (weekData || []).forEach(function(w) {
        (w.days || []).forEach(function(d) { if (d.holiday) holidays.add(d.date); });
    });
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const wd = new Date(today);
    let count = 0;
    while (count < 3) {
        wd.setDate(wd.getDate() + 1);
        const dow = wd.getDay();
        if (dow === 0 || dow === 6) continue; /* weekend */
        if (holidays.has(String(wd.getDate()).padStart(2, '0') + ' ' + months[wd.getMonth()] + ' ' + wd.getFullYear())) continue;
        count++;
    }
    return wd;
}

/**
 * True when a slot may NOT be selected: it lies in the past or fewer than
 * 3 working days from the MockData.mockNow anchor.
 * @param {Array}  weekData — page week data (days carry .date 'DD Mon YYYY' + .holiday)
 * @param {number} weekIdx
 * @param {number} dayIdx
 * @returns {boolean} true = the cell renders read-only
 */
function isSlotTooSoon(weekData, weekIdx, dayIdx) {
    const day = weekData && weekData[weekIdx] && weekData[weekIdx].days && weekData[weekIdx].days[dayIdx];
    if (!day) return false;
    const slot = new Date(day.date);
    slot.setHours(0, 0, 0, 0);
    const today = new Date(getTodayMs());
    if (slot <= today) return true; /* past, or today itself */
    return slot < leadTimeCutoff(weekData);
}

/**
 * A week is BOOKABLE when at least one day can host a replacement request:
 * not Sunday, not a holiday, and not too-soon (past / inside the
 * 3-working-day window). Drives the arrangement page's hidden-unbookable
 * weeks rule and its "Earliest bookable" destination — one source of truth
 * with isSlotTooSoon so they can never drift apart.
 * @param {Array}  weekData
 * @param {number} weekIdx
 * @returns {boolean} true = at least one selectable slot exists that week
 */
function weekHasBookableSlot(weekData, weekIdx) {
    const week = weekData && weekData[weekIdx];
    if (!week) return false;
    for (var d = 0; d < week.days.length; d++) {
        const day = week.days[d];
        if (day.sunday || day.abbr === 'Sun' || day.holiday) continue;
        if (isSlotTooSoon(weekData, weekIdx, d)) continue;
        return true;
    }
    return false;
}

/**
 * The earliest bookable day in a given week (first day that can host a
 * request) — i.e. the lead-time boundary day for that week.
 * @param {Array}  weekData
 * @param {number} weekIdx
 * @returns {number} day index, or -1 when the week has no bookable day
 */
function firstBookableDayIn(weekData, weekIdx) {
    const week = weekData && weekData[weekIdx];
    if (!week) return -1;
    for (var d = 0; d < week.days.length; d++) {
        const day = week.days[d];
        if (day.sunday || day.abbr === 'Sun' || day.holiday) continue;
        if (isSlotTooSoon(weekData, weekIdx, d)) continue;
        return d;
    }
    return -1;
}

/**
 * The earliest bookable day across the whole semester — the "Earliest
 * bookable" destination: { week, day } indexes, or null when no week is
 * bookable at all (end-of-semester edge; pages must guard).
 * @param {Array} weekData
 * @returns {{week:number, day:number}|null}
 */
function firstBookableDay(weekData) {
    if (!weekData) return null;
    for (var w = 0; w < weekData.length; w++) {
        const d = firstBookableDayIn(weekData, w);
        if (d >= 0) return { week: w, day: d };
    }
    return null;
}

/**
 * Pulses the earliest bookable day row so the arrangement page's
 * "Earliest bookable" action lands on the exact origin (the lead-time
 * boundary day), not just somewhere in the week. Day rows carry
 * tr[data-day-index] (builder sets tr.dataset.dayIndex); the pulse uses the
 * shared success token.
 * @param {Array}  weekData
 * @param {number} weekIdx — the week on screen (must hold a bookable day)
 */
function flashEarliestBookableDay(weekData, weekIdx) {
    const body = document.getElementById('tableBody');
    const dayIdx = firstBookableDayIn(weekData, weekIdx);
    if (!body || dayIdx < 0) return;
    const rows = body.querySelectorAll('tr[data-day-index="' + dayIdx + '"]');
    rows.forEach(function(r) {
        r.classList.remove('bookable-flash');
        void r.offsetWidth; /* restart the animation on repeat clicks */
        r.classList.add('bookable-flash');
    });
    setTimeout(function() {
        rows.forEach(function(r) { r.classList.remove('bookable-flash'); });
    }, 1300);
}

/**
 * Renders the contextual lead-time banner: visible only while the viewed
 * week contains blocked days (past / inside the 3-working-day window).
 * Same banner family as the booking-intent notice, neutral info tone.
 * @param {string} elId
 * @param {Array}  weekData
 * @param {number} weekIdx — the week currently on screen
 */
function renderLeadTimeNote(elId, weekData, weekIdx) {
    const el = document.getElementById(elId);
    if (!el) return;
    const week = weekData && weekData[weekIdx];
    const blocked = [];
    let fullyPast = true;
    if (week) {
        const today = new Date(getTodayMs());
        week.days.forEach(function(d, dIdx) {
            if (isSlotTooSoon(weekData, weekIdx, dIdx)) blocked.push(dIdx);
            const dt = new Date(d.date);
            dt.setHours(0, 0, 0, 0);
            if (dt >= today) fullyPast = false;
        });
    }
    if (!blocked.length) { el.innerHTML = ''; el.style.display = 'none'; return; }
    const cutoff = leadTimeCutoff(weekData);
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const dateStr = days[cutoff.getDay()] + ', ' + String(cutoff.getDate()).padStart(2, '0') + ' ' + months[cutoff.getMonth()] + ' ' + cutoff.getFullYear();
    /* copy: a fully-past week reads differently from the current one */
    const lead = fullyPast
        ? 'This week has already passed'
        : 'This week is within 3 working days of today';
    const copy = fullyPast || blocked.length >= 5
        ? lead + ' \u2014 bookable from <strong>' + dateStr + '</strong> onward.'
        : 'Some slots this week are too soon \u2014 bookable from <strong>' + dateStr + '</strong> onward.';
    el.className = 'lead-time-note';
    el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'
        + '<span>' + copy + '</span>';
    el.style.display = '';
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
    constructor(semesterData, weekData, selectId, storageKey, weekFilter) {
        this._semester = semesterData;
        this._weekData = weekData;
        this._currentWeek = 0;
        this._selectId = selectId || 'weekSelect';
        /* namespaced by default (sweep-fixes-round-1, F-11): pages that don't
           pass a key get one derived from their select id, so sibling pages
           can't overwrite each other's saved position anymore (the old
           shared-collision note below). Explicit keys win when passed. */
        this._storageKey = storageKey || ('weekNav-' + (this._selectId || 'weekSelect'));
        /* optional visibility filter (page-supplied): when present, weeks
           failing it (e.g. no bookable slot) are skipped in navigation and
           resolved away on load/select; null = every week is navigable
           (venue-timetable keeps its full list — it's a browsing/history page) */
        this._weekFilter = weekFilter || null;
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
        this._updateSelect();
        this._buildTimetable();
        this._updateSubtitle();
        this._updateProgress();
        this._scrollToGrid();
        this.save();
    }

    prevWeek() {
        const target = this._resolveStep(this._currentWeek - 1, -1);
        if (target >= 0 && target < this._currentWeek) {
            this._beforeNavigate();
            this._currentWeek = target;
            this._updateSelect();
            this._buildTimetable();
            this._updateSubtitle();
            this._updateProgress();
            this._updateArrows();
            this.save();
        }
    }

    nextWeek() {
        const target = this._resolveStep(this._currentWeek + 1, 1);
        if (target >= 0 && target > this._currentWeek) {
            this._beforeNavigate();
            this._currentWeek = target;
            this._updateSelect();
            this._buildTimetable();
            this._updateSubtitle();
            this._updateProgress();
            this._updateArrows();
            this.save();
        }
    }

    selectWeek(index) {
        if (this._weekFilter && !this._weekFilter(index)) {
            const resolved = this._resolveStep(index, 1);
            if (resolved < 0) return; /* nothing visible — stay put */
            index = resolved;
        }
        this._beforeNavigate();
        this._currentWeek = index;
        this._updateSelect();
        this._buildTimetable();
        this._updateSubtitle();
        this._updateProgress();
        this._updateArrows();
        this.save();
    }

    /**
     * Steps from `from` in direction `dir` (-1 back / +1 forward) until a
     * week passes the visibility filter; -1 when none does. Without a
     * filter this is the identity (`from` itself), so unfiltered pages
     * behave exactly as before.
     */
    _resolveStep(from, dir) {
        var i = from;
        var max = this._semester.weeks - 1;
        while (i >= 0 && i <= max) {
            if (!this._weekFilter || this._weekFilter(i)) return i;
            i += dir;
        }
        return -1;
    }

    /** First visible (weekFilter-passing) week index, or -1 when none. */
    firstVisibleWeek() {
        return this._resolveStep(0, 1);
    }

    /**
     * "Earliest bookable" destination: jump to the first week that can host
     * a request and flash its boundary day. Falls back to real "today" when
     * no week is bookable at all (end-of-semester edge — grid renders
     * read-only rather than the dropdown going empty).
     */
    jumpToEarliestBookable() {
        var target = this.firstVisibleWeek();
        if (target < 0) return this.jumpToToday();
        if (target !== this._currentWeek) {
            this._beforeNavigate();
            this._currentWeek = target;
            this._updateSelect();
            this._buildTimetable();
            this._updateSubtitle();
            this._updateProgress();
            this._updateArrows();
            this._scrollToGrid();
            this.save();
        }
        if (typeof this.flashEarliestBookable === 'function') this.flashEarliestBookable();
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
            localStorage.setItem(this._storageKey, this._currentWeek);
        } catch (e) { /* ignore */ }
    }

    load() {
        try {
            /* one-time legacy migration (F-11): pages that used to share the
               generic 'currentWeek' key adopt it once, then it is retired —
               first visited page inherits the old position, later pages fall
               through to today. Harmless because demo week picks don't persist. */
            var saved = localStorage.getItem(this._storageKey);
            if (saved === null) {
                var legacy = localStorage.getItem('currentWeek');
                if (legacy !== null) {
                    localStorage.setItem(this._storageKey, legacy);
                    localStorage.removeItem('currentWeek');
                    saved = legacy;
                }
            }
            if (saved !== null) {
                var idx = parseInt(saved, 10);
                if (!isNaN(idx)) {
                    this._currentWeek = Math.max(0, Math.min(this._semester.weeks - 1, idx));
                }
            }
        } catch (e) { /* ignore */ }
        /* snap to a visible week when the saved one is hidden now (e.g. a
           stored week that no longer holds a bookable slot) */
        if (this._weekFilter && !this._weekFilter(this._currentWeek)) {
            var fv = this.firstVisibleWeek();
            if (fv >= 0) this._currentWeek = fv;
        }
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
        var today = new Date(getTodayMs());
        today.setHours(0, 0, 0, 0);
        var idx = Math.floor((today - semesterStart) / 86400000 / 7);
        return Math.max(0, Math.min(this._semester.weeks - 1, idx));
    }

    _buildTimetable() {
        if (typeof window.buildTimetable === 'function') window.buildTimetable();
    }

    _updateSelect() {
        var sel = document.getElementById(this._selectId);
        if (!sel) return;
        /* with a filter the options are a subset — match by value, not position */
        if (this._weekFilter) {
            var v = String(this._currentWeek);
            for (var o = 0; o < sel.options.length; o++) {
                if (sel.options[o].value === v) { sel.selectedIndex = o; return; }
            }
            return;
        }
        sel.selectedIndex = this._currentWeek;
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
        /* with a filter, arrow ends = the visible set's edges */
        if (this._weekFilter) {
            if (prev) prev.disabled = this._resolveStep(this._currentWeek - 1, -1) < 0;
            if (next) next.disabled = this._resolveStep(this._currentWeek + 1, 1) < 0;
            return;
        }
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
        /* "Bookings open" chip: the semester's earliest bookable day, when the
           page opts in (cfg.bookableBadge) — data-driven via generateWeekData's
           firstBookable flag, so it tracks the anchor and never hardcodes */
        if (cfg.bookableBadge && day.firstBookable) {
            dayTd.insertAdjacentHTML('beforeend', '<span class="bookable-badge">Bookings open</span>');
        }
        tr.appendChild(dayTd);

        if (cfg.cellRender) {
            const dayEvents = cfg.events.filter(e => e.di === di && e.status !== 'cancelled');
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

        const dayEvents = cfg.events.filter(e => e.di === di && e.status !== 'cancelled');

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
                } else if (isConflict || e.status === 'conflict') {
                    // §10.0: conflict & PH are the same "this class won't run as
                    // scheduled" story — one loud red on personal timetables
                    // (everything here is the viewer's own; owner-gating with a
                    // quiet tint only applies to the cohort/venue browse pages)
                    div.classList.add('event-conflict');
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
/* ───── My-teaching stats (shared: cohort + venue summary bars) ───── */

/**
 * Count + hours of the classes taught by the logged-in lecturer within a
 * page's (already week/filter-scoped) events array. Dedupes via the grid's
 * own slotMap semantics (last-write-wins per day:start) so merged-cohort
 * duplicate rows count once — the number always matches visible blocks.
 * Offday events are skipped by callers whose grid renders them as PH cells.
 */
function myTeachingStats(events) {
    const heads = {};
    (events || []).forEach(function (e) {
        if (!e || e.lecturer !== MockData.currentUser.name) return;
        heads[e.di + ':' + e.start] = e;               // last-write-wins = grid slotMap semantics
    });
    const mine = Object.keys(heads).map(function (k) { return heads[k]; });
    const hours = mine.reduce(function (s, e) { return s + (e.end - e.start + 1) * 0.5; }, 0);
    return { classes: mine.length, hours: hours };
}

function computeSummary(events, days) {
    let total = events.length;
    let replacement = 0, pending = 0, conflict = 0, hrs = 0;

    events.forEach(e => {
        if (e.status === 'replacement') replacement++;
        if (e.status === 'pending') pending++;
        if (e.status === 'conflict' || (days[e.di] && days[e.di].holiday)) conflict++;
        hrs += (e.end - e.start + 1) * 0.5;
    });

    /* Null-guarded: pages whose summary bar drops a card (e.g. cohort's
       Replacements/Pending → My Teaching cards) keep the shared builder
       crash-free; existing ids keep their exact previous behavior. */
    const set = function (id, v) {
        const el = document.getElementById(id);
        if (el) el.textContent = v;
    };
    set('sumTotal', total);
    set('sumHours', (hrs % 1 === 0 ? hrs : hrs.toFixed(1)));
    set('sumReplacement', replacement);
    set('sumPending', pending);
    set('sumConflict', conflict);

    const my = myTeachingStats(events);
    set('sumMyClasses', my.classes);
    set('sumMyHours', (my.hours % 1 === 0 ? my.hours : my.hours.toFixed(1)));
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
 * @param {Array} [cfg.groups] - Grouped layout: [{ heading, rows: [{ label, value, strong? }] }].
 *   When present, renders one category per group; 2+ groups become a tab bar
 *   (same tabbed taxonomy as my-request-history's Request Details). Additive —
 *   omit for the flat event layout.
 */
/**
 * Render grouped rows inside a detail modal: 2+ groups → a modal tab bar with
 * one tab per category (like my-request-history's Request Details); a single
 * group → plain stacked body. Shared by openClassModal and page modals.
 * @param {object} opts - { modalId, title, subtitle?, timeline? }
 * @param {Array} groups - [{ heading, rows: [{ label, value, strong? }] }]
 * @returns {number} category count rendered (1 = plain body, 2+ = tabs)
 */
function renderModalGroups(opts, groups) {
    const tabs = (groups || []).filter(g => g.rows && g.rows.length).map(function (g) {
        return {
            key: String(g.heading).toLowerCase().replace(/[^a-z0-9]+/g, '-'),
            label: g.heading,
            html: DetailModal.section(g.heading, g.rows.map(function (r) {
                return DetailModal.row(r.label, r.value, { strong: r.strong });
            }).join(''))
        };
    });
    if (tabs.length > 1) {
        DetailModal.render(Object.assign({}, opts, { tabs: tabs }));
    } else {
        DetailModal.render(Object.assign({}, opts, { body: tabs.length ? tabs[0].html : '' }));
    }
    return tabs.length;
}

function openClassModal(cfg) {
    // ── Grouped layout (additive, §10.0 rules 5/6 — grouped categories as tabs) ──
    if (cfg.groups) {
        renderModalGroups({
            modalId: cfg.modalId || 'classModal',
            title: cfg.title || 'Class Details',
            subtitle: cfg.subtitle || '',
            timeline: cfg.timeline || null
        }, cfg.groups);
        return;
    }

    const event = cfg.event;
    const di = cfg.dayIndex;
    const days = cfg.days;

    const isConflict = days[di] && days[di].holiday;
    // §10.0 rule 2 — one source: badge class AND badge text from the same `st`.
    // A class falling on a public holiday is "Public Holiday" (red), NOT
    // "Conflict" — holiday ≠ clash, two different §10.0 meanings. Grid block
    // already styles it event-public-holiday; the badge now matches.
    const st = isConflict ? 'public-holiday' : (event.status || 'normal');
    const displayStatus = st === 'public-holiday'
        ? 'Public Holiday'
        : st.charAt(0).toUpperCase() + st.slice(1);

    const startStr = to12h(hours[event.start]);
    const endStr = to12h(hours[event.end + 1] || add30min(hours[event.end]));

    const statusDesc = isConflict ? 'Class falls on a public holiday — no class runs' : event.status === 'pending' ? 'Replacement request awaiting approval' : event.status === 'conflict' ? 'Scheduling conflict — needs attention' : 'Scheduled class with no issues';

    const rows = [
        { label: 'Subject Code', value: event.code },
        { label: 'Subject Name', value: event.name },
        { label: 'Class Type', value: event.type === 'L' ? 'Lecture (L)' : event.type === 'P' ? 'Practical (P)' : 'Tutorial (T)' },
        { label: 'Lecturer', value: event.lecturer },
        { label: 'Venue', value: event.venue || '\u2014' },
        { label: 'Day', value: dayNames[di] || days[di].abbr },
        { label: 'Date', value: days[di].date },
        { label: 'Start Time', value: startStr, strong: true },
        { label: 'End Time', value: endStr },
        { label: 'Status', value: '<span class="badge badge-' + st + '">' + displayStatus + '</span>' },
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

    // ── Confirmed replacement: show the replaced original conflicted class ──
    // `replacedFor` (dd-Mon-yyyy, seeded) or a date-shaped remarks string at
    // cohort level. The original kept the weekly slot, so its time/venue are
    // this block's own schedule.
    if (String(event.status) === 'replacement') {
        const rf = event.replacedFor ||
            ((event.remarks || '').match(/^\d{2}-[A-Z][a-z]{2}-\d{4}$/) || [])[0];
        if (rf) {
            const m = rf.match(/^(\d{2})-([A-Za-z]{3})-(\d{4})$/);
            const MONTHS = { jan: 0, feb: 1, mar: 2, apr: 3, may: 4, jun: 5,
                jul: 6, aug: 7, sep: 8, oct: 9, nov: 10, dec: 11 };
            const dayName = m && MONTHS[m[2].toLowerCase()] !== undefined
                ? dayNames[(new Date(+m[3], MONTHS[m[2].toLowerCase()], +m[1]).getDay() + 6) % 7] || ''
                : '';
            const stIdx = rows.findIndex(f => f.label === 'Status');
            rows.splice(stIdx, 0,
                { label: 'Original Date', value: (dayName ? dayName + ', ' : '') + rf, strong: true },
                { label: 'Original Time', value: startStr + ' \u2013 ' + endStr },
                { label: 'Original Venue', value: event.venue || '\u2014' });
            if (event.replacedReason) {
                rows.splice(stIdx + 3, 0, { label: 'Original Conflict', value: event.replacedReason });
            }
            // the raw date no longer belongs under Remarks — it lives in Original Date
            for (let i = rows.length - 1; i >= 0; i--) {
                if (rows[i].label === 'Remarks' && rows[i].value === rf) { rows.splice(i, 1); }
            }
        }
    }

    // ── Auto-grouped layout (§10.0 rules 5/6 — detail grouped, not a wall) ──
    // Same three-category taxonomy the request-history modal uses. Rows keep
    // their order inside each bucket; unknown labels default to Class Information.
    const STATUS_LABELS = ['Status', 'Status Description', 'Requested At', 'Requested By', 'Rejection Reason', 'Remarks'];
    const SCHEDULE_LABELS = ['Day', 'Date', 'Start Time', 'End Time', 'Duration', 'Venue'];
    function bucketOf(label) {
        if (label.indexOf('Original ') === 0) return 'Original Class';
        if (STATUS_LABELS.indexOf(label) >= 0) return 'Status';
        if (SCHEDULE_LABELS.indexOf(label) >= 0) return 'Schedule';
        return 'Class Information';
    }
    const buckets = rows.reduce(function (acc, r) {
        const k = bucketOf(r.label);
        (acc[k] = acc[k] || []).push(r);
        return acc;
    }, {});
    const groups = ['Class Information', 'Schedule', 'Original Class', 'Status']
        .filter(k => buckets[k] && buckets[k].length)
        .map(k => ({ heading: k, rows: buckets[k] }));

    renderModalGroups({
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
    }, groups);

    // Footer cleanup runs on EVERY open: the footer is static markup, so an
    // anchor appended for a previous pending class would otherwise linger on
    // a normal class's modal (reported 2026-10-08 — "why View Full Request?").
    var _mOverlay = document.getElementById(cfg.modalId || 'classModal');
    var _mFooter = _mOverlay ? _mOverlay.querySelector('.modal-footer') : null;
    if (_mFooter) {
        _mFooter.querySelectorAll('a.btn-action').forEach(function(b) { b.remove(); });
    }

    // Add "View Full Request" button to footer right side for OWN pending
    // requests only: requests[] ids resolve on my-request-history, so another
    // lecturer's pending (e.g. cohort page) must not link into this user's
    // request history (reported 2026-10-08 — id-collision leak).
    if (event.status === 'pending' && event.requestId && event.requestedBy === MockData.currentUser.name && _mFooter) {
        var viewBtn = document.createElement('a');
        viewBtn.href = '/my-request-history-ui?id=' + event.requestId;
        viewBtn.className = 'btn-action';
        viewBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg> View Full Request';
        viewBtn.style.textDecoration = 'none';
        var rightGroup = _mFooter.querySelector('.modal-footer-right');
        if (rightGroup) {
            rightGroup.appendChild(viewBtn);
        } else {
            _mFooter.appendChild(viewBtn);
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

// Shared detail-modal close (promoted from per-page copies, §10.0 rule 7 —
// the ui-class-detail-modal partial hardcodes onclick="closeModal()").
// Pages may still define their own closeModal (their later script blocks
// override this) — new pages only need a copy if they use a different modal id.
function closeModal() {
    var m = document.getElementById('classModal');
    if (m) m.style.display = 'none';
}

function closeOnEsc(closeFn) {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeFn();
    });
}

function closeOnOverlayClick(e, closeFn) {
    if (e.target === e.currentTarget) closeFn();
}

// ───── Summary visibility helper ─────

// Auto-hide the summary section (cards + hint) when a filtered view is empty —
// there is nothing to summarize (§10.0 rule 7, promoted from
// replacement-history / my-request-history / replacement-home /
// request-approval which all follow the same pattern).
function syncSummarySection(visible) {
    var s = document.getElementById('summarySection');
    if (s) s.style.display = visible ? '' : 'none';
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

// ───── Mobile Navigation Drawer ─────

function openNavDrawer() {
    // AD-12 direction 2: opening the drawer closes the notifications panel.
    // typeof guard — hoisted drawer code may run before panel definitions
    // in some load orders (ui-template inline script vs ui-common.js).
    if (typeof closeNotifPanel === 'function') closeNotifPanel();
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navDrawerOverlay');
    drawer.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeNavDrawer() {
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navDrawerOverlay');
    drawer.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
}

function initMobileNav() {
    const hamburger = document.getElementById('navHamburger');
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navDrawerOverlay');
    const closeBtn = document.getElementById('navDrawerClose');

    if (!hamburger || !drawer || !overlay) return;

    function toggleDrawer() {
        if (drawer.classList.contains('open')) closeNavDrawer();
        else openNavDrawer();
    }

    hamburger.addEventListener('click', toggleDrawer);
    closeBtn.addEventListener('click', closeNavDrawer);
    overlay.addEventListener('click', closeNavDrawer);

    // Swipe left to close
    let touchStartX = 0;
    drawer.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    drawer.addEventListener('touchend', (e) => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (diff > 50) closeNavDrawer();
    }, { passive: true });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('open')) closeNavDrawer();
    });
}

// ───── Notifications Panel (shared, read-state via localStorage) ─────
// Consumes window.MockData.notifications (mock-data.js §2.13, read-only) —
// never mutated at runtime. UNREAD STATE IS PER USER (F-2 pre-wire): one
// mailbox per logged-in user under 'notifications-read-user-<staffId>' — the
// backend day this store moves to the server (user_id natural key) and rows
// arrive already recipient-scoped. The row's `role` is only the CATEGORY the
// panel views by page context (AD-2); the badge/pill always count the whole
// mailbox. All element lookups are guarded: the panel partial (T6) may be absent.

const NOTIF_ROLE_BY_PAGE = { // AD-2 — body[data-page] → panel role
    studentMyTimetable: 'student',
    replacementHistory: 'student',
    requestApproval: 'pl',
};

const NOTIF_GLYPHS = { // 16px lucide-style stroke glyphs (design §5)
    submitted: '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
    awaiting:  '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
    approved:  '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
    rejected:  '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>',
    update:    '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
};

function notifUserId() { // F-2 pre-wire — mailbox owner (backend: auth user id)
    return (window.MockData && MockData.currentUser && MockData.currentUser.staffId) || 'demo';
}

function notifReadKey() { // per-USER mailbox (was per-role: 'notifications-read-<role>')
    return 'notifications-read-user-' + notifUserId();
}

/** The user's mailbox: rows addressed to the current user. Mock phase: every
 *  row carries recipientId = the persona (derived in mock-data.js §2.13); the
 *  backend day the API returns only the user's own rows, so this becomes a
 *  no-op guard. */
function notifMailboxRows() {
    var uid = notifUserId();
    return window.MockData.notifications.filter(function(n) {
        return !n.recipientId || n.recipientId === uid;
    });
}

function currentNotifRole() { // AD-2 — fallback 'lecturer' for anything else/missing
    const pageKey = document.body ? document.body.dataset.page : null;
    return (pageKey && NOTIF_ROLE_BY_PAGE[pageKey]) || 'lecturer';
}

/** AD-16 — <1 "just now", <60 "Xm", <1440 "Xh", <2880 "Yesterday", else "Xd". */
function relTime(minutesAgo) {
    var m = Number(minutesAgo) || 0;
    if (m < 1) return 'just now';
    if (m < 60) return m + 'm ago';
    if (m < 1440) return Math.floor(m / 60) + 'h ago';
    if (m < 2880) return 'Yesterday';
    return Math.floor(m / 1440) + 'd ago';
}

/** Absolute time for the row tooltip, e.g. "2 Oct, 9:41 AM". */
function notifAbsTime(minutesAgo) {
    var d = new Date(Date.now() - minutesAgo * 60000);
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var hh = d.getHours();
    const ampm = hh >= 12 ? 'PM' : 'AM';
    hh = hh === 0 ? 12 : hh > 12 ? hh - 12 : hh;
    return d.getDate() + ' ' + months[d.getMonth()] + ', ' + hh + ':' + String(d.getMinutes()).padStart(2, '0') + ' ' + ampm;
}

function notifGlyph(type) {
    return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        + (NOTIF_GLYPHS[type] || NOTIF_GLYPHS.update) + '</svg>';
}

/** Read id set for the user's mailbox. Missing key / invalid JSON → empty set (key never reseeded once present). */
function getNotifReads() {
    try {
        var raw = localStorage.getItem(notifReadKey());
        if (raw === null) return new Set();
        var arr = JSON.parse(raw);
        return Array.isArray(arr) ? new Set(arr) : new Set();
    } catch (e) {
        return new Set();
    }
}

function persistNotifReads(reads) {
    try {
        localStorage.setItem(notifReadKey(), JSON.stringify(Array.from(reads)));
    } catch (e) { /* storage unavailable — badge just stays volatile */ }
}

/**
 * Bell badge refresh (AD-7). Safe on ANY page — on pages without the bell
 * (e.g. replacement-arrangement) every element lookup is guarded.
 * Seeds the localStorage key at FIRST PAINT ONLY (AD-8): if the key is absent
 * it is written once with every pre-seen (`read: true`) row of the WHOLE
 * mailbox plus anything already marked read under the retired per-role store
 * (F-2 legacy migration — old keys are consumed and removed); present keys
 * (even `[]`) are never reseeded. Badge/pill count = mailbox unread TOTAL,
 * regardless of which category the page views.
 */
function refreshNotifBadge() {
    if (!window.MockData || !window.MockData.notifications) return;
    const key = notifReadKey();

    if (localStorage.getItem(key) === null) {
        const seed = new Set(window.MockData.notifications
            .filter(function (n) { return n.read === true; })
            .map(function (n) { return n.id; }));
        try {
            ['student', 'pl', 'lecturer'].forEach(function (r) {
                var raw = localStorage.getItem('notifications-read-' + r);
                if (raw) JSON.parse(raw).forEach(function (id) { seed.add(id); });
                localStorage.removeItem('notifications-read-' + r);
            });
        } catch (e) { /* ignore malformed legacy stores */ }
        persistNotifReads(seed);
    }

    const reads = getNotifReads();
    updateNotifHeaderState(notifMailboxRows()
        .filter(function (n) { return !reads.has(n.id); }).length);
}

/** Header state shared by refreshNotifBadge + panel opens (AD-5):
 *  ONE count — the user's mailbox unread TOTAL (badge + pill + mark-all all
 *  follow it; the panel's per-category list no longer drives the header). */
function updateNotifHeaderState(count) {
    var badge = document.getElementById('notifBadge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
    var pill = document.getElementById('notifUnreadPill');
    if (pill) {
        pill.textContent = count;
        pill.style.display = count > 0 ? 'inline-flex' : 'none';
    }
    var markAll = document.getElementById('notifMarkAll');
    if (markAll) markAll.style.display = count > 0 ? 'inline-flex' : 'none';
}

/** AD-5 — render unread rows (anchor links, D4) + caught-up/empty swap.
 *  T17 — "Unread only" filter (AD-19/AD-20): missing #notifUnreadOnly or
 *  checked → exactly the old unread-only behavior (unsorted, frozen order).
 *  Unchecked → all of the role's rows, newest first (minutesAgo ascending,
 *  sorted on a copy — MockData is read-only). Read rows get .notif-row--read
 *  (muted); badge/pill/mark-all always follow the unread count. */
function renderNotifList() {
    const list = document.getElementById('notifList');
    if (!list) return;
    if (!window.MockData || !window.MockData.notifications) return;

    const role = currentNotifRole();             /* AD-2 — view category only */
    const reads = getNotifReads();               /* per-user mailbox store */
    const unread = notifMailboxRows().filter(function (n) {
        return n.role === role && !reads.has(n.id);
    });

    const filterEl = document.getElementById('notifUnreadOnly');
    const unreadOnly = !filterEl || filterEl.checked;
    const all = !!filterEl && !filterEl.checked;
    let rows = unread;
    if (all) {
        rows = notifMailboxRows()
            .filter(function (n) { return n.role === role; })
            .slice() // MockData read-only (AGENTS.md §4)
            .sort(function (a, b) { return a.minutesAgo - b.minutesAgo; });
    }

    list.innerHTML = rows.map(function (n) {
        return '<a class="notif-row' + (reads.has(n.id) ? ' notif-row--read' : '') + '" data-id="' + escHtml(n.id) + '" href="' + escHtml(n.link) + '"'
            + ' data-tip="' + escHtml(n.title + ' — ' + n.desc) + '" aria-label="' + escHtml(n.title + ' — ' + n.desc) + '">'
            + '<span class="notif-row-icon notif-tile-' + escHtml(n.type) + '">' + notifGlyph(n.type) + '</span>'
            + '<span class="notif-row-body">'
            + '<span class="notif-row-title">' + escHtml(n.title) + '</span>'
            + '<span class="notif-row-desc">' + escHtml(n.desc) + '</span>'
            + '</span>'
            + '<span class="notif-row-time" data-tip="' + escHtml(notifAbsTime(n.minutesAgo)) + '" data-tip-pos="left">' + escHtml(relTime(n.minutesAgo)) + '</span>'
            + '</a>';
    }).join('');

    // Caught-up swap (AD-5): only in unread-only mode at 0 unread. "All" mode
    // never shows caught-up — it always has rows unless the category has none.
    // NOTE: the header (badge/pill/mark-all) is NOT resynced here — it counts
    // the whole mailbox (F-2); the list is only the viewed category.
    const empty = document.getElementById('notifEmpty');
    const showEmpty = (unreadOnly && unread.length === 0) || rows.length === 0;
    if (empty) empty.style.display = showEmpty ? 'flex' : 'none';
    list.style.display = showEmpty ? 'none' : '';
}

/** Add one id to the user's mailbox read set, persist, refresh the badge. */
function markNotifRead(id) {
    if (!window.MockData || !window.MockData.notifications) return;
    const reads = getNotifReads();
    reads.add(id);
    persistNotifReads(reads);
    refreshNotifBadge();
}

/** Mark the WHOLE mailbox read (backend semantics: every unread row of this
 *  user, all categories) + refresh badge and list. */
function markAllNotifsRead() {
    if (!window.MockData || !window.MockData.notifications) return;
    const reads = getNotifReads();
    window.MockData.notifications.forEach(function (n) {
        reads.add(n.id);
    });
    persistNotifReads(reads);
    refreshNotifBadge();
    renderNotifList();
}

function openNotifPanel() { // AD-12 direction 1 — drawer never stacks with panel
    closeNavDrawer();    const panel = document.getElementById('notifPanel');
    const overlay = document.getElementById('notifOverlay');
    const bell = document.querySelector('.notif-btn');
    if (panel) panel.classList.add('open');
    if (overlay) overlay.classList.add('open');
    if (bell) bell.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden'; // AD-11 — inline lock, both breakpoints
    refreshNotifBadge(); // header state follows the mailbox (F-2 pre-wire)
    renderNotifList();
}

function closeNotifPanel() {
    const panel = document.getElementById('notifPanel');
    const overlay = document.getElementById('notifOverlay');
    const bell = document.querySelector('.notif-btn');
    if (panel) panel.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
    if (bell) bell.setAttribute('aria-expanded', 'false');
    // Release the body lock only if the nav drawer isn't open too (AD-11) —
    // releasing while the drawer holds its own lock would let the body scroll.
    const drawer = document.getElementById('navDrawer');
    if (!drawer || !drawer.classList.contains('open')) {
        document.body.style.overflow = '';
    }
}

function toggleNotifPanel() {
    const panel = document.getElementById('notifPanel');
    if (!panel) return;
    if (panel.classList.contains('open')) closeNotifPanel();
    else openNotifPanel();
}

// ───── TEMP — UI testing only, DELETE BEFORE SUBMISSION ─────

/** Console helper: clears the user read key (incl. legacy per-role stores) + reloads. */
function resetNotifDemo() {
    try {
        localStorage.removeItem(notifReadKey());
        ['student', 'pl', 'lecturer'].forEach(function (r) {
            localStorage.removeItem('notifications-read-' + r);
        });
    } catch (e) { /* ignore */ }
    window.location.reload();
}

/** One-shot for demo shots: re-mark (or un-mark) a single row, no reload. */
function notifDevToggle(id) {
    const reads = getNotifReads();
    if (reads.has(id)) reads.delete(id); else reads.add(id);
    persistNotifReads(reads);
    refreshNotifBadge();
    if (document.getElementById('notifPanel').classList.contains('open')) renderNotifList();
}

/** Quick reference of all 12 ids — paste in console: notifIds() */
function notifIds() {
    return MockData.notifications.map(function (n) { return n.id + ' (' + n.role + (n.read ? ', seed-read' : '') + ')'; });
}


/**
 * Wire the panel (idempotent). Self-contained DOMContentLoaded hook (AD-7) —
 * the layout's boot chain lives in ui-template.blade.php and may also call
 * refreshNotifBadge(); both paths are cheap + guarded for missing markup.
 */
function initNotifPanel() {
    if (window.__notifPanelInitialized) return;
    window.__notifPanelInitialized = true;

    const panel = document.getElementById('notifPanel');
    const overlay = document.getElementById('notifOverlay');

    // Bell / ✕ / Mark-all: only wire elements the partial does NOT own via
    // inline onclick (T6) so handlers never double-fire.
    const bell = document.querySelector('.notif-btn');
    if (bell && !bell.hasAttribute('onclick')) {
        bell.addEventListener('click', toggleNotifPanel);
    }
    if (overlay) overlay.addEventListener('click', closeNotifPanel); // outside click (AD-13)

    const closeBtn = panel ? panel.querySelector('.notif-close') : null;
    if (closeBtn && !closeBtn.hasAttribute('onclick')) {
        closeBtn.addEventListener('click', closeNotifPanel);
    }
    const markAll = document.getElementById('notifMarkAll');
    if (markAll && !markAll.hasAttribute('onclick')) {
        markAll.addEventListener('click', markAllNotifsRead);
    }

    // Row click = mark read + re-render + restore scroll, BEFORE the anchor's
    // native navigation (D4). Capture phase so it runs ahead of default nav;
    // preventDefault is deliberately omitted (AD-15).
    const list = document.getElementById('notifList');
    if (list) {
        list.addEventListener('click', function (e) {
            const row = e.target.closest ? e.target.closest('.notif-row') : null;
            if (!row || !row.getAttribute('data-id')) return;
            const scrollTop = list.scrollTop;
            markNotifRead(row.getAttribute('data-id'));
            renderNotifList();
            list.scrollTop = scrollTop;
        }, true);
    }

    // T17 — "Unread only" toggle: re-render with scroll preserved (AD-15) and
    // header state untouched (badge always follows the unread count).
    const unreadOnlyToggle = document.getElementById('notifUnreadOnly');
    if (unreadOnlyToggle) {
        unreadOnlyToggle.addEventListener('change', function () {
            const scrollTop = list ? list.scrollTop : 0;
            renderNotifList();
            if (list) list.scrollTop = scrollTop;
        });
    }

    // Esc closes only while the panel is open (AD-13).
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        const p = document.getElementById('notifPanel');
        if (p && p.classList.contains('open')) closeNotifPanel();
    });

    refreshNotifBadge();
}

document.addEventListener('DOMContentLoaded', function () {
    initNotifPanel();
});

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
        /* anchored to MockData.mockNow (the fixed demo "today") when present,
           falling back to the real clock once that anchor is deleted —
           TODO(backend) note sits in mock-data.js mockNow; both stay in sync. */
        var src = (window.MockData && MockData.mockNow) ? new Date(MockData.mockNow) : new Date();
        src.setHours(0, 0, 0, 0);
        return src.getTime();
    }
}

// ───── Days-left display contract (replacement-home surfaces: table cell,
// mobile card footer, quick-view modal Status row — 3rd-duplication promo) ─────

/* daysLeft → label: <0 "Overdue" · 0 "Today" · 1 "1 day left" · else "N days left".
   Urgency classes reuse the table vocabulary (urgency-high/mid/low — the only
   defined CSS); overdue renders in the same red as imminent (error semantics). */
function daysLeftLabel(days) {
    /* overdue carries the day count (user ask, 2026-10-06): the diff is
       against the MockData.mockNow anchor, same as every other lead-time
       computation — flips to real "today" at the go-live switch */
    if (days < 0) {
        const n = Math.abs(days);
        return 'Overdue (' + n + (n === 1 ? ' day' : ' days') + ' ago)';
    }
    if (days === 0) return 'Today';
    if (days === 1) return '1 day left';
    return days + ' days left';
}

/* Status enum → display label (§10.0: same name for the same meaning).
   Some datasets store lowercase enums ('normal') vs Title-case ('Pending') —
   this renders ONE Title-case vocabulary everywhere. CSS badge classes stay
   keyed by the raw enum (badge-normal etc.), so visuals are untouched. */
const StatusText = {
    map: { normal: 'Normal', replacement: 'Replacement', pending: 'Pending',
           approved: 'Approved', rejected: 'Rejected', conflict: 'Conflict' },
    label(status) {
        var s = String(status);
        var hit = this.map[s.toLowerCase()];
        if (hit) return hit;
        return s.charAt(0).toUpperCase() + s.slice(1);
    }
};

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
            /* generic badge text (displays PUBLIC HOLIDAY via the existing
               uppercase transform); the specific holiday name, when known,
               rides the shared data-tip hover tooltip */
            const tipAttr = day.holidayLabel ? ' data-tip="' + day.holidayLabel + '"' : '';
            html += '<span class="holiday-badge"' + tipAttr + '>Public Holiday</span>';
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
            { html: '<span class="' + urgencyCls + '">' + daysLeftLabel(days) + '</span>', cls: 'col-urgency' },
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
            '<span class="' + urgencyCls + '">' + daysLeftLabel(days) + '</span>' +
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
        this._current = null;
    }

    show(message, undoCallback, duration = 5000, linkText = '', linkUrl = '', details = '', onManualDismiss = null, onAutoDismiss = null) {
        const bar = document.getElementById('toastBar');
        if (!bar) return;

        this._current = { message: message, onManualDismiss: onManualDismiss, onAutoDismiss: onAutoDismiss };

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
                /* Dismiss FIRST (U1, 2026-10-08): callbacks commonly show
                   their own confirmation toast — dismissing AFTER the callback
                   wiped the callback's bar ~0 ms after it appeared, so the
                   user saw the toast vanish with no feedback. */
                this.dismiss();
                undoCallback();
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
        this._timer = setTimeout(() => {
            this.dismiss();
            /* Full display = seen (2026-10-08 decision): a toast that survived
               its whole duration retires itself via onAutoDismiss (e.g. the
               cancellation undo toast snoozes). Manual ✕ goes through close()
               instead; navigating away early kills the timer — no hook, and
               the toast legitimately re-shows on the next load. */
            const hook = this._current && this._current.onAutoDismiss;
            if (hook) hook();
        }, duration);
    }

    dismiss() {
        const bar = document.getElementById('toastBar');
        if (bar) bar.classList.remove('visible');
        clearTimeout(this._timer);
    }

    /* Manual close (the ✕ in the layout toast bar). Unlike the auto-timeout
       dismiss(), this fires the current toast's onManualDismiss hook so a
       dismissal can have consequences (e.g. snoozing the cancellation toast). */
    close() {
        this.dismiss();
        const hook = this._current && this._current.onManualDismiss;
        if (hook) hook();
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
    if (!sel) return; /* page has no week filter — its own keyboard nav applies */
    if (sel.selectedIndex > 0) {
        sel.selectedIndex--;
        sel.dispatchEvent(new Event('change'));
    }
}

function nextWeekFilter() {
    const sel = document.getElementById('weekFilter');
    if (!sel) return;
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

    /* remember this select's cfg so a runtime breakpoint-crossing (768) can
       re-format labels (full ↔ compact) without a reload — same spirit as the
       venue mobileCardList resize hook. Re-runs preserve the selection. */
    populateWeekSelect._registry = populateWeekSelect._registry || {};
    populateWeekSelect._registry[selectId] = cfg;
    populateWeekSelect._mobileState = populateWeekSelect._mobileState || {};
    populateWeekSelect._mobileState[selectId] = isMobile;
    if (!populateWeekSelect._resizeWired) {
        populateWeekSelect._resizeWired = true;
        var _pwTimer;
        window.addEventListener('resize', function() {
            clearTimeout(_pwTimer);
            _pwTimer = setTimeout(function() {
                var mobileNow = window.innerWidth <= 768;
                Object.keys(populateWeekSelect._registry).forEach(function(id) {
                    var el = document.getElementById(id);
                    if (!el || populateWeekSelect._mobileState[id] === mobileNow) return;
                    var preserve = el.value;
                    populateWeekSelect(id, populateWeekSelect._registry[id]);
                    el.value = preserve; /* cfg.selected may be stale after re-populate */
                });
            }, 200);
        });
    }

    let html = '';
    if (cfg.includeAll) html += '<option value="all">All Weeks</option>';

    /* optional visibility filter (weekData path only): only weeks passing it
       become options, keeping ABSOLUTE week numbers as values so saved
       positions, URL params and cross-page references stay meaningful */
    const filter = (cfg.weekFilter && !useRanges) ? cfg.weekFilter : null;
    const list = filter
        ? source.map(function(w, i) { return { w: w, i: i }; }).filter(function(x) { return filter(x.i); })
        : source;

    list.forEach(function(x, i) {
        const w = list === source ? x : x.w;
        const value = useRanges ? w.value : (list === source ? i : x.i);
        let label;
        if (cfg.labelFn) {
            label = cfg.labelFn(w, value, isMobile, useRanges);
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
        var url = BackNavigator.#routes[from] || BackNavigator.getDefault();
        /* keep the booking context alive across the round-trip: the venue page's
           "Booking for:" banner runs on these params */
        if (from === 'venue-timetable') {
            var keep = [];
            if (params.get('code')) keep.push('code=' + encodeURIComponent(params.get('code')));
            if (params.get('cohort')) keep.push('cohort=' + encodeURIComponent(params.get('cohort')));
            if (keep.length) url += '?' + keep.join('&');
        }
        return url;
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
        /* unknown/stale code (e.g. a favourite left over from older mock data):
           ignore instead of selecting into a phantom venue */
        if (!this.venues.some(v => v.code === code)) return;
        this.selectedCode = code;
        this._updateTrigger();
        this._updateActive();
        this._close();
        this.onSelect(code);
    }

    /**
     * Venues that survive the active filter (or all venues when unfiltered) —
     * used by Ctrl+1/2/3 venue switching and the "fits N students" note.
     */
    getFiltered() {
        return this.filter ? this.venues.filter(this.filter) : this.venues.slice();
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
        /* the chevron is a separate svg (same shared arrow as the week-select);
           only the label span receives text */
        const label = this.trigger.querySelector('.venue-dd-label');
        if (!this.selectedCode) {
            if (label) label.textContent = 'Select a venue';
            else this.trigger.textContent = 'Select a venue';
            return;
        }
        const v = this.venues.find(x => x.code === this.selectedCode);
        if (v) {
            const typeLabel = v.type === 'LectureHall' ? 'Lecture Hall' : v.type;
            const text = v.code + ' \u2014 ' + typeLabel + ' (' + v.capacity + ' seats)';
            if (label) label.textContent = text;
            else this.trigger.textContent = text; /* legacy markup fallback */
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

// ───── ClassCancellation (cancel-class-enhancement — design §1) ─────

/**
 * Owns the whole cancel-class lifecycle: isCancellable guards (S1–S5),
 * reason validation (S7/S8), twin mutation across myTimetable +
 * cohortTimetable (§3), the sessionStorage ledger (§2) and the undo toast
 * (§6). Storage touches are ALL guarded (§10): a throwing sessionStorage
 * degrades to the in-memory mirror (same-session undo only, documented
 * trade-off — file:// / privacy modes).
 */
const ClassCancellation = {
    /* §1 reason vocabulary — deliberately reuses the conflictedClasses
       conflictReason enum ('Medical Leave', 'Annual Leave', 'Official Event'
       already exist there) + cancellation-specific entries + catch-all
       'Other'. */
    REASONS: ['Medical Leave', 'Annual Leave', 'Official Event',
              'Family Emergency', 'Venue/Facility Issue', 'Other'],
    LEDGER_KEY: 'classCancellationLedger',  // sessionStorage — tab-scope demo reset
    UNDO_TOAST_MS: 5000,
    _seq: 0,               // entry-id suffix — no same-millisecond collision
    _MONTHS: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    _memLedger: [],        // §10 fallback mirror when storage throws

    /* §3 matching key: 0-indexed week + code + di (0 = Mon) + start
       (half-hour index) + lecturer. A merged-cohort myTimetable block
       intentionally matches SEVERAL cohortTimetable twins (S16). */
    matchKeyOf(event, weekIndex) {
        return { week: weekIndex, code: event.code, di: event.di,
                 start: event.start, lecturer: event.lecturer };
    },

    /* S7/S8 — Yes-button gating: reason required + in the vocabulary;
       'Other' demands a non-empty trimmed detail. */
    validate(reason, detail) {
        if (!reason || this.REASONS.indexOf(reason) < 0) {
            return { ok: false, hint: 'Please select a cancellation reason.' };
        }
        if (reason === 'Other' && !String(detail || '').trim()) {
            return { ok: false, hint: 'Please describe the reason for "Other".' };
        }
        return { ok: true, hint: '' };
    },

    /* S1–S5 guards, recomputed per call (cheap; the confirm path re-checks
       at S6): own class + status 'normal' OR 'replacement' (a confirmed
       replacement slot is still the lecturer's own scheduled class — FR
       2.16; 2026-10-08 SDD-waived extension, priorStatus preserved for
       undo) + non-holiday, non-Sunday day + END datetime strictly in the
       future (real clock per design §1). */
    isCancellable(event, days, weekIndex) {
        if (!event) return false;
        if (event.lecturer !== MockData.currentUser.name) return false;  // S2
        if (event.status !== 'normal' && event.status !== 'replacement') return false; // S3
        const day = days && days[event.di];
        if (!day || day.holiday || day.sunday || day.abbr === 'Sun') return false; // S4
        const end = this.endDateTime(event, days, weekIndex);
        return !!end && end.getTime() > Date.now();                      // S5
    },

    /**
     * The class's END datetime as a real Date (the S5/S6 boundary).
     *
     * Parse (deterministic — design §10 pin):
     * - DATE: `days[event.di].date` in the pinned 'DD Mon YYYY' format
     *   produced by generateWeekData's `fmt` ('21 Sep 2026'). Split on
     *   whitespace → [dd, Mon, yyyy]; the month index comes from the same
     *   English short-month vocabulary (_MONTHS) — the string is NEVER
     *   handed to the Date constructor, so no locale/parse anxiety.
     * - TIME: derived EXACTLY like the grid renders it (§1): the end time
     *   string is `hours[event.end + 1] || add30min(hours[event.end])`
     *   over the shared `hours` half-hour array. Those values are 'HH:MM'
     *   24h strings in this dataset ('13:00'); a 12h companion ('1:00 PM')
     *   is tolerated by _parseTime for robustness.
     * Both halves assemble in LOCAL time via new Date(y, mo, dd, hh, mm);
     * callers compare against the real clock.
     * Returns null for a malformed/unmatched day (guards treat null > now
     * as false → not cancellable, never a throw).
     */
    endDateTime(event, days, weekIndex) {
        const day = days && days[event.di];
        if (!day) return null;
        const p = String(day.date).trim().split(/\s+/);
        const mo = this._MONTHS.indexOf(p[1]);
        if (p.length !== 3 || mo < 0) return null;
        const minutes = this._parseTime(hours[event.end + 1] || DateHelper.add30min(hours[event.end]));
        return new Date(parseInt(p[2], 10), mo, parseInt(p[0], 10),
                        Math.floor(minutes / 60), minutes % 60);
    },

    /* 'HH:MM' 24h or 'H:MM AM/PM' 12h → minutes since midnight (0 fallback). */
    _parseTime(t) {
        const m = String(t).trim().match(/^(\d{1,2}):(\d{2})\s*([AaPp][Mm])?$/);
        if (!m) return 0;
        let h = parseInt(m[1], 10);
        const mi = parseInt(m[2], 10);
        if (m[3]) {
            if (/[pP]/.test(m[3]) && h < 12) h += 12;
            if (/[aA]/.test(m[3]) && h === 12) h = 0;
        }
        return h * 60 + mi;
    },

    /* Any accepted time vocabulary → 'HH:MM' 24h (row timeStart/timeEnd). */
    _to24h(t) {
        const minutes = this._parseTime(t);
        return String(Math.floor(minutes / 60)).padStart(2, '0') + ':' +
               String(minutes % 60).padStart(2, '0');
    },

    /* Row-label formatter: 'DFT2(S1)' → 'DFT2 (S1)' and 'RSD2(S1)G2' →
       'RSD2 (S1) G2' — the spacing of the existing conflictedClasses rows
       + faculties cohort labels. Unrecognized strings pass through
       verbatim (also keeps already-formatted row labels intact). */
    formatCohortLabel(c) {
        const m = String(c).trim().match(/^([A-Z]{3}\d)\(S(\d)\)(?:G(\d))?$/);
        if (!m) return String(c).trim();
        return m[1] + ' (S' + m[2] + ')' + (m[3] ? ' G' + m[3] : '');
    },

    /* Row cohort labels: prefer event.cohorts, else split the merged
       'A + B' cohort string some events carry (§2.6 single blocks store
       one cohort in event.cohort only). */
    cohortLabels(event) {
        const raw = Array.isArray(event.cohorts) ? event.cohorts :
            (event.cohort ? String(event.cohort).split(' + ') : []);
        return raw.map(this.formatCohortLabel, this);
    },

    _eventMatches(ev, mk) {
        return !!ev && ev.code === mk.code && ev.di === mk.di &&
               ev.start === mk.start && ev.lecturer === mk.lecturer;
    },

    /* §3 twin matching across BOTH stores (0-indexed weeks on both sides
       — myTimetable.eventsByWeek keys, cohortTimetable item.week). The
       cohort store nests each block in item.event, so resolve/unions take
       {store:'my'|'cohort', eventRef} pairs; multi-match is expected for
       merged-cohort blocks (S16), zero is valid for cohortTimetable-only
       blocks (row totalStudents = 0 per §3). */
    resolveMatches(mk) {
        const out = [];
        if (!mk) return out;
        const week = (MockData.myTimetable && MockData.myTimetable.eventsByWeek)[mk.week];
        if (Array.isArray(week)) {
            week.forEach(function(ev) {
                if (this._eventMatches(ev, mk)) out.push({ store: 'my', eventRef: ev });
            }, this);
        }
        (MockData.cohortTimetable.events || []).forEach(function(item) {
            if (item.week === mk.week && this._eventMatches(item.event, mk)) {
                out.push({ store: 'cohort', eventRef: item.event });
            }
        }, this);
        return out;
    },

    /* ISO 'YYYY-MM-DD' from 0-indexed weekIndex + di via the §2 pin:
       semester.startDate + 7*weekIndex + di days — the SAME math
       generateWeekData uses (index 0 is the Week-1 Monday), NOT the
       'DD Mon YYYY' display string that replacement-home's
       daysLeft()/getWeekNumber() parsers reject. Shared so the cancel
       modal's Arrange-Now URL reuses the one date pin. */
    isoDate(weekIndex, di) {
        const parts = String(MockData.semester.startDate).split('-');
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        d.setDate(d.getDate() + weekIndex * 7 + di);
        return d.getFullYear() + '-' +
               String(d.getMonth() + 1).padStart(2, '0') + '-' +
               String(d.getDate()).padStart(2, '0');
    },

    _nextRowId() {
        let maxId = 0;
        (MockData.conflictedClasses || []).forEach(function(r) {
            if (r && r.id > maxId) maxId = r.id;
        });
        return maxId + 1;
    },

    /**
     * §2 row build — a conflictedClasses-shaped row (so replacement-home's
     * existing table/card renderers consume it verbatim):
     * - date: ISO 'YYYY-MM-DD' from the semester start-date math (§2 pin);
     *   day: full name derived from that ISO date (DateHelper.isoDayName);
     * - timeStart/timeEnd: 24h 'HH:MM' from the half-hour indices, using
     *   the same grid vocabulary (hours[i+1] || add30min(hours[end]));
     * - duration: half-hour span → hours ((end - start + 1) / 2);
     * - totalStudents: the twin myTimetable match(es) studentCounts sum
     *   when resolvable, else 0 (§3 — cohort-only blocks stay valid);
     * - cohorts: formatted labels from the event's cohorts; the
     *   row-reason IS the chosen cancellation reason (S13 chip etc.);
     * - id: next free integer (max existing row id + 1).
     */
    rowFromEvent(event, days, weekIndex, reason) {
        let total = 0;
        this.resolveMatches(this.matchKeyOf(event, weekIndex)).forEach(function(m) {
            if (m.store !== 'my') return;
            const ev = m.eventRef;
            if (Array.isArray(ev.studentCounts)) {
                ev.studentCounts.forEach(function(c) { total += (c || 0); });
            } else if (typeof ev.studentCount === 'number') {
                total += ev.studentCount;
            }
        });
        const iso = this.isoDate(weekIndex, event.di);
        return {
            id: this._nextRowId(),
            code: event.code,
            name: event.name,
            type: event.type,
            lecturer: event.lecturer,   // cancelled classes are own by construction — keeps them visible under replacement-home's owner scoping
            date: iso,
            day: DateHelper.isoDayName(iso),
            timeStart: this._to24h(hours[event.start]),
            timeEnd: this._to24h(hours[event.end + 1] || DateHelper.add30min(hours[event.end])),
            duration: (event.end - event.start + 1) / 2,
            venue: event.venue,
            totalStudents: total,
            cohorts: this.cohortLabels(event),
            conflictReason: reason,
        };
    },

    /* RAW ledger helpers — every touch try/catch-guarded (§10). */

    ledgerRead() {
        try {
            const raw = sessionStorage.getItem(this.LEDGER_KEY);
            if (raw) {
                const arr = JSON.parse(raw);
                if (Array.isArray(arr)) return arr;
            }
        } catch (e) { /* storage unavailable / malformed — fall through */ }
        return this._memLedger;
    },

    ledgerAppend(entry) {
        this._memLedger.push(entry);
        try {
            const raw = sessionStorage.getItem(this.LEDGER_KEY);
            const arr = raw ? JSON.parse(raw) : [];
            if (!Array.isArray(arr)) arr = [];
            arr.push(entry);
            sessionStorage.setItem(this.LEDGER_KEY, JSON.stringify(arr));
        } catch (e) { /* §10 degrade — the mirror already holds the entry */ }
    },

    ledgerRemove(entry) {
        const id = entry && entry.id;
        this._memLedger = this._memLedger.filter(function(e) { return e.id !== id; });
        try {
            const raw = sessionStorage.getItem(this.LEDGER_KEY);
            const arr = raw ? JSON.parse(raw) : [];
            if (Array.isArray(arr)) {
                const next = arr.filter(function(e) { return e.id !== id; });
                if (next.length !== arr.length) {
                    sessionStorage.setItem(this.LEDGER_KEY, JSON.stringify(next));
                }
            }
        } catch (e) { /* §10 degrade */ }
    },

    /* Replace-by-id with a shallow copy — the caller's object wins, so a
       later consumed:'arranged' (S13b) or chip-flag clear persists
       through replay. */
    ledgerUpdate(entry) {
        const id = entry && entry.id;
        if (!id) return;
        const i = this._memLedger.findIndex(function(e) { return e.id === id; });
        if (i >= 0) this._memLedger[i] = Object.assign({}, entry);
        try {
            const raw = sessionStorage.getItem(this.LEDGER_KEY);
            const arr = raw ? JSON.parse(raw) : [];
            if (Array.isArray(arr)) {
                for (let k = 0; k < arr.length; k++) {
                    if (arr[k].id === id) { arr[k] = Object.assign({}, entry); break; }
                }
                sessionStorage.setItem(this.LEDGER_KEY, JSON.stringify(arr));
            }
        } catch (e) { /* §10 degrade — mirror already updated */ }
    },

    /**
     * S9 — re-check guards at CONFIRM time (S6) + reason validation
     * (S7/S8), cancel EVERY resolved twin in place (status 'cancelled' +
     * cancelledReason/cancelledDetail — merged-cohort multi-match gets all
     * of them, S16), insert the row when its id is not already present,
     * write the ledger entry, return {ok:true, entry} or {ok:false, hint}.
     */
    cancel(event, days, weekIndex, reason, detail) {
        if (!this.isCancellable(event, days, weekIndex)) {
            const end = this.endDateTime(event, days, weekIndex);
            return { ok: false,
                     hint: (end && end.getTime() <= Date.now())
                           ? 'This class has already ended.'
                           : 'This class can no longer be cancelled.' };
        }
        const check = this.validate(reason, detail);
        if (!check.ok) return check;

        const mk = this.matchKeyOf(event, weekIndex);
        const matches = this.resolveMatches(mk);
        if (!matches.length) {
            return { ok: false, hint: 'This class is no longer in the timetable.' };
        }
        matches.forEach(function(m) {
            m.eventRef.status = 'cancelled';
            m.eventRef.cancelledReason = reason;
            m.eventRef.cancelledDetail = detail ? String(detail).trim() : '';
        });

        const row = this.rowFromEvent(event, days, weekIndex, reason);
        const rows = MockData.conflictedClasses;
        if (!rows.some(function(r) { return r.id === row.id; })) rows.push(row);

        const entry = {
            id: 'cxl-' + Date.now() + '-' + (++this._seq),
            matchKey: mk,
            reason: reason,
            detail: detail ? String(detail).trim() : '',
            cancelledAt: new Date().toISOString(),
            row: row,
            chip: true,
            /* Status to restore on undo — a cancelled 'replacement' block
               must come back AS a replacement (Original-Date trail intact),
               not as 'normal' (2026-10-08 SDD-waived extension). */
            priorStatus: event.status === 'replacement' ? 'replacement' : 'normal',
        };
        this.ledgerAppend(entry);
        return { ok: true, entry: entry };
    },

    /* S14 — restore EVERY resolved twin to its PRIOR status ('normal' or
       'replacement' — see cancel()'s priorStatus), strip the cancellation
       fields, remove the row by id, remove the entry. */
    undo(entry) {
        if (!entry) return;
        const restore = entry.priorStatus || 'normal';
        this.resolveMatches(entry.matchKey).forEach(function(m) {
            m.eventRef.status = restore;
            delete m.eventRef.cancelledReason;
            delete m.eventRef.cancelledDetail;
        });
        const rows = MockData.conflictedClasses || [];
        const idx = rows.findIndex(function(r) { return r.id === (entry.row && entry.row.id); });
        if (idx >= 0) rows.splice(idx, 1);
        this.ledgerRemove(entry);
    },

    /* S12/S13b — newest entry that is neither removed nor consumed, or
       null. Consumed entries (consumed:'arranged') keep replaying the
       cancelled state but stop the toast + chip (§6 lifecycle). */
    activeEntry() {
        const entries = this.ledgerRead();
        for (let i = entries.length - 1; i >= 0; i--) {
            const e = entries[i];
            if (e && !e.removed && !e.consumed) return e;
        }
        return null;
    },

    /**
     * S12/S13/S15 replay — once per page load: re-apply every entry's
     * cancelled state idempotently (status already 'cancelled' → plain
     * overwrite; row pushed only when its id is absent). Entries whose
     * match keys resolve to NOTHING are stale → DROPPED from the ledger
     * (S15) so the toast never nags on dead entries.
     */
    applyLedger() {
        const self = this;
        this.ledgerRead().slice().forEach(function(entry) {
            const matches = self.resolveMatches(entry.matchKey);
            if (!matches.length) { self.ledgerRemove(entry); return; }
            matches.forEach(function(m) {
                m.eventRef.status = 'cancelled';
                m.eventRef.cancelledReason = entry.reason;
                m.eventRef.cancelledDetail = entry.detail;
            });
            const rows = MockData.conflictedClasses || [];
            if (entry.row && !rows.some(function(r) { return r.id === entry.row.id; })) {
                /* Own-records backfill: ledgers persisted before replacement-home
                   became owner-scoped store rows without `lecturer`. Cancellations
                   are own-only by construction, so the twin event's lecturer is
                   the owner — without this the replayed row would be filtered out
                   of replacement-home (S12/S13b). */
                if (!entry.row.lecturer) {
                    entry.row.lecturer = (matches[0] && matches[0].eventRef.lecturer) ||
                                         MockData.currentUser.name;
                }
                rows.push(entry.row);
            }
        });
    },

    /* Public read accessor for per-entry chip rendering (S13): a COPY of
       the raw ledger (pages must not mutate the raw array). Newest last,
       per-entry chips — NOT just activeEntry() (§6 stack semantics). */
    allEntries() {
        return this.ledgerRead().slice();
    },

    /* S13b — mark every entry whose matchKey.code equals `code` as
       consumed:'arranged'. The entry is RETAINED (applyLedger keeps
       replaying the cancelled state) but the chip + load toast stop and
       undo is no longer offered (§6 lifecycle). Returns how many entries
       changed; a submit for a different subject touches nothing. */
    markConsumedByCode(code) {
        let changed = 0;
        this.allEntries().forEach(function(entry) {
            if (entry && !entry.consumed && entry.matchKey &&
                entry.matchKey.code === code) {
                entry.consumed = 'arranged';
                this.ledgerUpdate(entry);
                changed++;
            }
        }, this);
        return changed;
    },

    /* §6 — the ONE undo-toast helper: the load bootstrap AND the "I'll Do
       It Later" path both call it; the confirm path must NOT double-toast
       (the modal's success state carries the message instead). Snooze rule
       (2026-10-08 decision): the toast shows for 5 s — surviving the full
       display OR clicking ✕ both count as "seen" and snooze it for the
       session; only a quick navigate-away (timer killed) re-toasts on the
       next load. Undo stays reachable via the home chip's Undo affordance
       (replacement-home page). */
    showUndoToast() {
        toast.show('Class cancelled — arrange replacement when ready', function() {
            const entry = ClassCancellation.activeEntry();
            if (!entry) return;
            ClassCancellation.undo(entry);
            /* On my-timetable the in-place grid rebuild IS the feedback
               (U2, 2026-10-08). Anywhere else the undo deep-links to
               my-timetable at the cancelled class's session week
               (?week=<matchKey.week>) — the restored=1 param makes the
               arrival page confirm with the same 'Class restored.' toast
               (2026-10-08 user request: UNDO must land on the exact
               class session week, not stay on the arrangement page). */
            if (location.pathname === '/my-timetable-ui') {
                if (typeof buildTimetable === 'function') buildTimetable();
                toast.show('Class restored.', null);
            } else {
                const wk = (entry.matchKey && Number.isInteger(entry.matchKey.week))
                    ? entry.matchKey.week : null;
                window.location.href = '/my-timetable-ui' +
                    (wk !== null ? '?week=' + wk + '&restored=1' : '?restored=1');
            }
        }, ClassCancellation.UNDO_TOAST_MS, '', '', '', function() {
            ClassCancellation.snoozeToast();
        }, function() {
            ClassCancellation.snoozeToast();
        });
    },

    /* Toast snooze — the user explicitly closed the cancellation toast, so
       stop re-showing it on every page load (2026-10-08 feedback: the toast
       kept returning after every refresh). The LEDGER ENTRY IS KEPT: the
       cancelled state + chip still replay; only the toast is silenced. */
    snoozeToast() {
        const entry = this.activeEntry();
        if (entry && !entry.toastSnoozed) {
            entry.toastSnoozed = true;
            this.ledgerUpdate(entry);
        }
    },

    /* Does ANY live entry still deserve a load toast? A snoozed entry stays
       silent; a NEW cancellation (fresh entry, unsnoozed) toasts again. */
    needsLoadToast() {
        return this.allEntries().some(function(e) {
            return e && !e.removed && !e.consumed && !e.toastSnoozed;
        });
    },
};

/* S12 bootstrap — registered HERE, not in the layout (design §2): a
   separate DOMContentLoaded listener owned by ui-common.js so it fires on
   EVERY page while the layout's own init hook stays untouched. Replay AND
   the load toast both wait for DOMContentLoaded: ui-common.js loads in the
   body BEFORE the #toastBar markup, and ToastManager.show() silently
   no-ops without it (design §2 🔴1). */
document.addEventListener('DOMContentLoaded', function() {
    try {
        ClassCancellation.applyLedger();
        if (ClassCancellation.needsLoadToast()) ClassCancellation.showUndoToast();
        else if (new URLSearchParams(location.search).get('restored') === '1')
            toast.show('Class restored.', null);  // arrival feedback after a cross-page undo
    } catch (e) { /* degrade — page init must never break over the ledger */ }
});

/* bfcache guard — Arrange-Now (or any future leave-with-modal-open path)
   can be revisited via the browser BACK button; the restored snapshot
   replays with the cancel modal still shown. Close it here — open() fully
   resets the confirm/success state on the next open (2026-10-08 user
   report: modal still open after BACK from replacement-arrangement). */
window.addEventListener('pageshow', function(e) {
    if (e.persisted) {
        try { CancelClassModal.close(); } catch (err) { /* degrade */ }
    }
});

// ───── CancelClassModal + CancelClass.renderButton (cancel-class-enhancement — design §5) ─────

/**
 * Controller for the promoted partial `partials/ui-cancel-class-modal.blade.php`
 * (included by my-timetable, cohort-timetable and venue-timetable). Owns the
 * confirm state (impact preview, reason radios, S7/S8 Yes-gating, S6
 * confirm-time re-check) and the success state (S10 Arrange-Now navigation,
 * S11 Later → rebuild + undo toast). The cancel ITSELF stays in
 * ClassCancellation.cancel — this object is pure UI orchestration.
 */
const CancelClassModal = {
    /* { event, days, weekIndex, onCancelled, entry } for the current open */
    _ctx: null,
    _bound: false,

    /* Bind the partial's dynamic widgets ONCE, lazily at first open —
       ui-common.js loads before the partial markup in the body. */
    _bind() {
        if (this._bound) return;
        const list = document.getElementById('cancelReasonList');
        const detail = document.getElementById('cancelOtherDetail');
        if (list) {
            list.addEventListener('change', (function(e) {
                if (e.target && e.target.name === 'cancelReason') this._refreshGate();
            }).bind(this));
        }
        if (detail) detail.addEventListener('input', this._refreshGate.bind(this));
        /* Esc closes WITHOUT cancelling (confirm state) — one shared
           document listener, active only while the overlay is shown. */
        document.addEventListener('keydown', (function(e) {
            const overlay = document.getElementById('cancelClassOverlay');
            if (e.key === 'Escape' && overlay && overlay.classList.contains('show')) this.dismiss();
        }).bind(this));
        this._bound = true;
    },

    /* S1–S5 guard + confirm-state render. `context.onCancelled` (optional)
       is the page's grid-rebuild callback used by the Later path. */
    open(event, days, weekIndex, context) {
        if (!ClassCancellation.isCancellable(event, days, weekIndex)) return;  // S1–S5
        this._ctx = {
            event: event,
            days: days,
            weekIndex: weekIndex,
            onCancelled: (context && typeof context.onCancelled === 'function') ? context.onCancelled : null,
            entry: null,
        };
        this._bind();
        this._renderReasons();
        this._renderImpact();
        const detail = document.getElementById('cancelOtherDetail');
        if (detail) { detail.value = ''; detail.style.display = 'none'; }
        this._swapState('confirm');
        this._refreshGate();  // S7 initial hint + disabled Yes
        const overlay = document.getElementById('cancelClassOverlay');
        if (overlay) overlay.classList.add('show');
    },

    /* Raw close — no cancel performed, no rebuild (confirm state only). */
    close() {
        const overlay = document.getElementById('cancelClassOverlay');
        if (overlay) overlay.classList.remove('show');
    },

    /* Esc / overlay-click / header × / Keep Class. Once a cancel has been
       performed (success state), closing MUST still rebuild the grid +
       toast — the cancel already happened — so it routes to the Later path. */
    dismiss() {
        if (this._ctx && this._ctx.entry) { this.later(); return; }
        this.close();
    },

    /* Render the reason radios from the ClassCancellation enum (the markup
       keeps only the #cancelReasonList container — design §5). */
    _renderReasons() {
        const list = document.getElementById('cancelReasonList');
        if (!list) return;
        list.innerHTML = '<div class="detail-group"><div class="detail-group-title">Cancellation Reason</div></div>';
        const group = list.firstChild;
        ClassCancellation.REASONS.forEach(function(reason) {
            const label = document.createElement('label');
            label.style.cssText = 'display:flex;align-items:center;gap:8px;padding:6px 0;cursor:pointer;font-size:13px;';
            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'cancelReason';
            input.value = reason;
            input.style.accentColor = 'var(--color-primary)';
            label.appendChild(input);
            label.appendChild(document.createTextNode(reason));
            group.appendChild(label);
        });
    },

    /* Impact preview (design §5): subject, day+date, 12h time range, venue,
       cohort(s) — students row only when a myTimetable twin resolves (§3). */
    _renderImpact() {
        const el = document.getElementById('cancelClassImpact');
        const ctx = this._ctx;
        if (!el || !ctx) return;
        const ev = ctx.event;
        const day = ctx.days && ctx.days[ev.di];
        const startStr = to12h(hours[ev.start]);
        const endStr = to12h(hours[ev.end + 1] || add30min(hours[ev.end]));
        const students = this.studentsFor(ev, ctx.weekIndex);
        el.innerHTML = DetailModal.section('This Class',
            DetailModal.row('Subject', escHtml(ev.code) + ' (' + escHtml(ev.type || '') + ') · ' + escHtml(ev.name || ''), { strong: true }) +
            DetailModal.row('Day & Date', escHtml((day && day.abbr) || '') + ' · ' + escHtml((day && day.date) || '')) +
            DetailModal.row('Time', escHtml(startStr) + ' \u2013 ' + escHtml(endStr)) +
            DetailModal.row('Venue', escHtml(ev.venue || '\u2014')) +
            DetailModal.row('Cohort(s)', escHtml(ClassCancellation.cohortLabels(ev).join(', ') || '\u2014')) +
            (students === null ? '' : DetailModal.row('Students', String(students)))
        );
    },

    /* §3 students helper — myTimetable twin studentCounts sum when the
       match resolves, else null (preview shows cohorts only; row keeps 0). */
    studentsFor(event, weekIndex) {
        let total = 0;
        let found = false;
        ClassCancellation.resolveMatches(ClassCancellation.matchKeyOf(event, weekIndex))
            .forEach(function(m) {
                if (m.store !== 'my') return;
                const ev = m.eventRef;
                if (Array.isArray(ev.studentCounts)) {
                    found = true;
                    ev.studentCounts.forEach(function(c) { total += (c || 0); });
                } else if (typeof ev.studentCount === 'number') {
                    found = true;
                    total += ev.studentCount;
                }
            });
        return found ? total : null;
    },

    _selectedReason() {
        const picked = document.querySelector('#cancelReasonList input[name="cancelReason"]:checked');
        return picked ? picked.value : '';
    },

    /* S7/S8 — reason + Other-detail gate the Yes button and the inline hint. */
    _refreshGate() {
        const reason = this._selectedReason();
        const detail = document.getElementById('cancelOtherDetail');
        const hint = document.getElementById('cancelReasonHint');
        const yes = document.getElementById('confirmCancelClassBtn');
        if (detail) detail.style.display = reason === 'Other' ? '' : 'none';
        const check = ClassCancellation.validate(reason, detail ? detail.value : '');
        if (hint) hint.textContent = check.hint;
        if (yes) yes.disabled = !check.ok;
    },

    /* S6 + S9 — confirm-time re-check, then the actual cancel. On failure
       stay open and surface the hint ('This class has already ended.'). */
    confirm() {
        const ctx = this._ctx;
        if (!ctx || ctx.entry) return;
        const detail = document.getElementById('cancelOtherDetail');
        const res = ClassCancellation.cancel(ctx.event, ctx.days, ctx.weekIndex,
            this._selectedReason(), detail ? detail.value : '');
        if (!res.ok) {
            const hint = document.getElementById('cancelReasonHint');
            if (hint) hint.textContent = res.hint;
            return;
        }
        ctx.entry = res.entry;
        const summary = document.getElementById('cancelClassSummary');
        if (summary) {
            const ev = ctx.event;
            const day = ctx.days && ctx.days[ev.di];
            summary.textContent = ev.code + ' (' + (ev.type || '') + ') · ' +
                ((day && day.abbr) || '') + ' · ' + ((day && day.date) || '') + ' · ' +
                to12h(hours[ev.start]) + ' \u2013 ' + to12h(hours[ev.end + 1] || add30min(hours[ev.end]));
        }
        this._swapState('success');
    },

    /* S10 — navigate to the arrangement page carrying the cancelled class's
       values (goToReplacementWith param scheme, design §2 ISO date pin). */
    arrangeNow() {
        const ctx = this._ctx;
        if (!ctx || !ctx.entry) return;
        /* Close BEFORE navigating: the browser's BACK button restores this
           page from bfcache, and an open success modal would replay with it
           (2026-10-08 user report). open() resets state on next open. */
        this.close();
        const row = ctx.entry.row;
        window.location.href = '/replacement-arrangement?code=' + encodeURIComponent(row.code) +
            '&date=' + encodeURIComponent(row.date) +
            '&duration=' + row.duration;
        /* navigation unloads the page — the bootstrap toast fires there (§6) */
    },

    /* S11 — close, let the page rebuild its grid via onCancelled, then the
       shared undo toast (the confirm path never double-toasts, §6). */
    later() {
        const ctx = this._ctx;
        this.close();
        if (ctx && typeof ctx.onCancelled === 'function') ctx.onCancelled();
        ClassCancellation.showUndoToast();
    },

    /* Swap confirm ↔ success state in-place, footer buttons included. */
    _swapState(state) {
        const confirmState = document.getElementById('cancelClassConfirmState');
        const successState = document.getElementById('cancelClassSuccessState');
        const keep = document.getElementById('cancelKeepClassBtn');
        const yes = document.getElementById('confirmCancelClassBtn');
        const arrange = document.getElementById('cancelArrangeNowBtn');
        const laterBtn = document.getElementById('cancelLaterBtn');
        const success = state === 'success';
        if (confirmState) confirmState.style.display = success ? 'none' : '';
        if (successState) successState.style.display = success ? '' : 'none';
        if (keep) keep.style.display = success ? 'none' : '';
        if (yes) yes.style.display = success ? 'none' : '';
        if (arrange) arrange.style.display = success ? '' : 'none';
        if (laterBtn) laterBtn.style.display = success ? '' : 'none';
    },
};

/**
 * Cancel Class? button for class-detail modal footers (design §5) — works
 * for ALL THREE modal producers: my-timetable / cohort (the shared
 * ui-class-detail-modal partial's .modal-footer) and venue (its own
 * eventModal .modal-footer). Pages call it right after their modal render:
 *
 *   CancelClass.renderButton(overlayEl.querySelector('.modal-footer'),
 *                            event, days, weekIndex, onCancelled);
 *
 * Self-hiding (S1–S5): appends ONLY when ClassCancellation.isCancellable —
 * a previously appended button is always removed first, so re-renders never
 * stack duplicates and a now-non-cancellable class loses its button. When
 * the page defines .btn-cancel-class styling (my-timetable page-styles) it
 * is reused; otherwise the theme's ghost .btn-danger token style applies.
 */
const CancelClass = {
    _styleChecked: null,

    _hasBtnCancelClassStyle() {
        if (this._styleChecked !== null) return this._styleChecked;
        let found = false;
        try {
            const sheets = document.styleSheets;
            for (let i = 0; i < sheets.length && !found; i++) {
                let rules = null;
                try { rules = sheets[i].cssRules; } catch (e) { continue; }  // cross-origin
                if (!rules) continue;
                for (let r = 0; r < rules.length; r++) {
                    if (rules[r].selectorText &&
                        rules[r].selectorText.indexOf('.btn-cancel-class') >= 0) {
                        found = true;
                        break;
                    }
                }
            }
        } catch (e) { /* fall through — not found → ghost btn-danger */ }
        this._styleChecked = found;
        return found;
    },

    renderButton(container, event, days, weekIndex, onCancelled) {
        if (!container) return;
        // Remove any previously appended cancel button BEFORE the guard, so a
        // class that turned non-cancellable loses its stale button too.
        container.querySelectorAll('[data-cancel-class-btn]').forEach(function(b) { b.remove(); });
        if (!ClassCancellation.isCancellable(event, days, weekIndex)) return;  // S1–S5
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = this._hasBtnCancelClassStyle() ? 'btn-cancel-class' : 'btn-danger';
        btn.setAttribute('data-cancel-class-btn', '1');
        btn.textContent = 'Cancel Class?';
        btn.addEventListener('click', function() {
            CancelClassModal.open(event, days, weekIndex, { onCancelled: onCancelled });
        });
        // Respect the footer's left/right grouping when the producer uses it
        // (same convention as openClassModal's "View Full Request" append).
        const rightGroup = container.querySelector('.modal-footer-right');
        (rightGroup || container).appendChild(btn);
    },
};

