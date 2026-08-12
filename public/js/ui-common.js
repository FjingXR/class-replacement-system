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
    constructor(semesterData, weekData) {
        this._semester = semesterData;
        this._weekData = weekData;
        this._currentWeek = 0;
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
    }

    prevWeek() {
        if (this._currentWeek > 0) {
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
        this._currentWeek = index;
        this._buildTimetable();
        this._updateSelect();
        this._updateSubtitle();
        this._updateProgress();
        this._updateArrows();
        this.save();
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
        var sel = document.getElementById('weekSelect');
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

    static replacementBlock(r) {
        if (!r.replacementDate) return '<span style="color:var(--color-on-surface-variant);opacity:0.5">&mdash;</span>';
        var d = DateHelper.dayAbbr(DateHelper.isoDayName(r.replacementDate));
        var dateStr = DateHelper.formatDate(r.replacementDate);
        var wn = getWeekNumber(r.replacementDate);
        var weekTag = wn ? ' (Week ' + wn + ')' : '';
        var statusCls = statusClass(r.status);
        var venue = r.replacementVenue || r.venue || '—';
        return '<div class="cell-class-block">'
            + '<span class="class-day-date">' + d + ', ' + dateStr + weekTag + '</span><br>'
            + '<span class="class-time ' + statusCls + '">' + r.replacementTime + '</span><br>'
            + '<span class="class-venue">' + venue + '</span>'
            + '</div>';
    }

    static dayHeader(day) {
        let html = '<span class="day-label">' + day.abbr + '</span><span class="date-label">' + day.date + '</span>';
        if (day.today) {
            html += '<span class="today-badge">Today</span>';
        } else if (day.holiday) {
            html += '<span class="holiday-label">' + (day.holidayLabel || 'Public Holiday') + '</span>';
        } else if (day.sunday) {
            html += '<span class="date-label off-label">OFF</span>';
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
        if (opts.urgencyLevel && opts.urgencyClass && opts.urgencyLabel) {
            var level = opts.urgencyLevel(r.classDate);
            urgencyHtml = '<span class="urgency-badge ' + opts.urgencyClass(level) + '">' + opts.urgencyLabel(level) + '</span>';
        }
        var ageHtml = opts.requestAgeHtml ? opts.requestAgeHtml(r.requestedAt) : '';
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
            { html: '<span class="cell-code">' + c.code + '</span><span class="cell-name">' + c.name + ' <span style="font-weight:400;font-size:12px;color:var(--color-on-surface-variant)">(' + typeLabel + ')</span></span>', cls: 'col-code' },
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
            '<span class="rc-code">' + c.code + ' <span style="font-weight:400;font-size:12px;color:var(--color-on-surface-variant)">(' + typeLabel + ')</span></span>' +
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
            if (el.dataset.original !== undefined) {
                el.innerHTML = el.dataset.original;
                delete el.dataset.original;
            }
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

function weekFilterChanged(opts) {
    pageState.currentPage = 1;
    if (opts && typeof opts.onBeforeRebuild === 'function') opts.onBeforeRebuild();
    buildTable();
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
        const p = s.trim().split('-');
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
