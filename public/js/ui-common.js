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

function to12h(t) {
    const [hStr, m] = t.split(':');
    const h = parseInt(hStr);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12 = h === 0 ? 12 : h > 12 ? h - 12 : h;
    return h12 + ':' + m + ' ' + ampm;
}

function formatDate(iso) {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const parts = iso.split('-');
    return parseInt(parts[2]) + ' ' + months[parseInt(parts[1]) - 1] + ' ' + parts[0];
}

function updateWeekArrows(prevDisabled, nextDisabled) {
    const prev = document.querySelector('.week-arrow[aria-label="Previous week"]');
    const next = document.querySelector('.week-arrow[aria-label="Next week"]');
    if (prev) prev.disabled = prevDisabled;
    if (next) next.disabled = nextDisabled;
}

// ───── Week helpers (shared by timetable pages) ─────

function currentWeekIndex() {
    const semesterStart = new Date(MockData.semester.startDate);
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

function fmt(d) {
    return String(d.getDate()).padStart(2, '0') + ' ' + d.toLocaleString('en', { month: 'short' }) + ' ' + d.getFullYear();
}

function updateWeekSubtitle() {
    const el = document.getElementById('weekSubtitle');
    if (el) {
        el.textContent = 'Week ' + (currentWeek + 1) + ' of ' + MockData.semester.weeks + ' \u00B7 ' + fmt(weekData[currentWeek].start) + ' \u00B7 ' + fmt(weekData[currentWeek].end);
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
    if (typeof saveWeek === 'function') saveWeek();
    var grid = document.querySelector('.grid-wrapper');
    if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function initTodayBtn() {
    var btn = document.getElementById('todayBtn');
    if (btn) btn.addEventListener('click', jumpToToday);
}

// ───── Day header builder (shared by all timetable pages) ─────

function buildDayHtml(day) {
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

// ───── Time slot helpers (shared by timetable pages) ─────

const hours = [
    '08:00', '08:30', '09:00', '09:30',
    '10:00', '10:30', '11:00', '11:30',
    '12:00', '12:30',
    '13:00', '13:30', '14:00', '14:30',
    '15:00', '15:30', '16:00', '16:30',
    '17:00', '17:30', '18:00', '18:30'
];

function add30min(t) {
    const [h, m] = t.split(':').map(Number);
    const total = h * 60 + m + 30;
    return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
}

// ───── Navigation ─────

function goToReplacement() {
    window.location.href = '/replacement-arrangement';
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

// ───── Modal helpers ─────

function closeOnEsc(closeFn) {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeFn();
    });
}

function closeOnOverlayClick(e, closeFn) {
    if (e.target === e.currentTarget) closeFn();
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

// ───── Skeleton Loading ─────

function showSkeleton(container, type = 'rows', count = 5) {
    container.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const el = document.createElement('div');
        el.className = `skeleton skeleton-${type === 'rows' ? 'row' : 'card'}`;
        container.appendChild(el);
    }
}

function hideSkeleton(container) {
    container.innerHTML = '';
}

// ───── Scroll Restoration ─────

function saveScrollPosition(key) {
    sessionStorage.setItem('scroll_' + key, window.scrollY);
}

function restoreScrollPosition(key) {
    const pos = sessionStorage.getItem('scroll_' + key);
    if (pos) window.scrollTo(0, parseInt(pos));
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

// ───── Toast/Undo Bar ─────

let _toastTimer = null;

/**
 * Show a toast/undo bar at bottom-left.
 * @param {string} message - Success message to display
 * @param {function|null} undoCallback - Function to call when Undo is clicked (null = no Undo button)
 * @param {number} duration - Auto-dismiss time in ms (default 5000)
 * @param {string} [linkText] - Optional text for a clickable link in the toast
 * @param {string} [linkUrl] - Optional URL for the clickable link
 * @param {string} [details] - Optional secondary detail text (shown below message)
 */
function showToast(message, undoCallback, duration = 5000, linkText = '', linkUrl = '', details = '') {
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
        undoBtn.onclick = function () {
            undoCallback();
            dismissToast();
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

    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(dismissToast, duration);
}

function dismissToast() {
    const bar = document.getElementById('toastBar');
    if (bar) bar.classList.remove('visible');
    clearTimeout(_toastTimer);
}

// ───── Day Helpers ─────

const dayNames = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];

function dayAbbr(day) {
    return day.substring(0, 3);
}

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
