# Design: UI JS OOP Refactor

## Technical Approach

Incremental class addition to `ui-common.js`. Each class is added below existing functions, templates are migrated one at a time, old functions are removed after full migration.

**No file splitting** — all 7 classes stay in `ui-common.js` to avoid extra HTTP requests (no bundler).

**Migration order** (simplest → most complex):
```
DateHelper → HtmlBuilder → SkeletonLoader → ToastManager → ModalController → TableController → WeekNavigator
```

Each step is independently testable — verify no regressions before moving to the next.

## Architecture

### Class Layout in ui-common.js

```
ui-common.js
├── [Existing standalone functions —逐步 removed as classes take over]
├── DateHelper (static class)
├── HtmlBuilder (static class)
├── SkeletonLoader (namespace object)
├── ToastManager (singleton class)
├── ModalController (class)
├── TableController (class)
├── WeekNavigator (class)
├── [Remaining standalone functions — out of scope]
```

### Class Specifications

#### 1. DateHelper (static class)

No state. Pure functions. No constructor needed.

```javascript
class DateHelper {
    static to12h(t) { /* existing to12h() */ }
    static formatDate(iso) { /* existing formatDate() */ }
    static formatDateTime(iso) { /* existing formatDateTime() */ }
    static fmt(d) { /* existing fmt() */ }
    static add30min(t) { /* existing add30min() */ }
    static dayAbbr(day) { /* existing dayAbbr() */ }
    static isoDayName(iso) { /* existing isoDayName() */ }
    static getTodayMs() { /* existing getTodayMs() */ }
}
```

**Migration:** Replace `to12h(...)` → `DateHelper.to12h(...)`, etc. All call sites are in `ui-common.js` itself (`formatClassBlock`, `formatReplacementBlock`) and in templates.

#### 2. HtmlBuilder (static class)

No state. Generates HTML strings. Depends on DateHelper.

```javascript
class HtmlBuilder {
    static classBlock(r) { /* existing formatClassBlock() */ }
    static replacementBlock(r) { /* existing formatReplacementBlock() */ }
    static dayHeader(day) { /* existing buildDayHtml() */ }
}
```

**Note:** `classBlock()` and `replacementBlock()` depend on standalone globals `getWeekNumber()` and `statusClass()` (which remain out of scope) and `DateHelper.isoDayName()` (which is in-scope). These must be available when HtmlBuilder methods are called.

**Migration:** Replace `formatClassBlock(r)` → `HtmlBuilder.classBlock(r)`, etc.

#### 3. SkeletonLoader (namespace object)

Stateless. Container-scoped operations.

```javascript
const SkeletonLoader = {
    show(container, type = 'rows', count = 5) { /* existing showSkeleton() */ },
    hide(container) { /* existing hideSkeleton() */ },
    with(callback, container, count = 10, delay = 400) { /* existing withSkeleton() */ },
    showSummary() { /* existing showSummarySkeleton() */ },
    hideSummary() { /* existing hideSummarySkeleton() */ }
};
```

**Migration:** Replace `showSkeleton(el)` → `SkeletonLoader.show(el)`, etc.

#### 4. ToastManager (singleton class)

Holds timer state. One global instance.

```javascript
class ToastManager {
    constructor() { this._timer = null; }
    show(message, undoCallback, duration = 5000, linkText = '', linkUrl = '', details = '') {
        /* existing showToast() logic */
    }
    dismiss() {
        /* existing dismissToast() logic */
    }
}
const toast = new ToastManager();
```

**Migration:** Replace `showToast(...)` → `toast.show(...)`, `dismissToast()` → `toast.dismiss()`.

#### 5. ModalController (class)

Generic modal with render callback. Manages ESC and overlay click listeners.

```javascript
class ModalController {
    constructor(modalId, renderFn) {
        this._modalId = modalId;
        this._renderFn = renderFn;
        this._onKeyDown = null;
        this._onOverlayClick = null;
    }
    open(data) {
        this.close(); // clean up any previous listeners
        this._renderFn(data);
        const modal = document.getElementById(this._modalId);
        modal.style.display = 'flex';
        this._onKeyDown = (e) => { if (e.key === 'Escape') this.close(); };
        this._onOverlayClick = (e) => { if (e.target === modal) this.close(); };
        document.addEventListener('keydown', this._onKeyDown);
        modal.addEventListener('click', this._onOverlayClick);
    }
    close() {
        const modal = document.getElementById(this._modalId);
        modal.style.display = 'none';
        if (this._onKeyDown) {
            document.removeEventListener('keydown', this._onKeyDown);
            this._onKeyDown = null;
        }
        if (this._onOverlayClick) {
            modal.removeEventListener('click', this._onOverlayClick);
            this._onOverlayClick = null;
        }
    }
    isOpen() {
        const modal = document.getElementById(this._modalId);
        return modal && modal.style.display !== 'none';
    }
}
```

**Migration:** Each template that uses modal patterns creates its own instance:
```javascript
const classModal = new ModalController('classModal', (data) => { /* render */ });
```

#### 6. TableController (class)

Config-driven sort + pagination. Holds sort state and column config.

```javascript
class TableController {
    constructor(config) {
        this._columns = config.columns;
        this._sortState = config.sortState || { field: null, dir: 'asc' };
        this._render = config.render;
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
    makeHeader(col) { /* existing makeSortableHeader() logic, uses this._sortState */ }
    paginate(data, page, pageSize) { /* existing paginate() logic, adapted */ }
    updateResultCount(data, total, label) { /* existing updateResultCount() logic */ }
    initRpp(cfg) { /* existing initRpp() logic */ }
    get sortState() { return this._sortState; }
}
```

**Migration:** Each table page creates its own instance with column config.

#### 7. WeekNavigator (class)

Encapsulates week navigation state. Auto-saves to localStorage.

```javascript
class WeekNavigator {
    constructor(semesterData, weekData) {
        this._semester = semesterData;
        this._weekData = weekData;
        this._currentWeek = 0;
    }
    get currentWeek() { return this._currentWeek; }
    get weekData() { return this._weekData; }
    get semester() { return this._semester; }

    jumpToToday() {
        this._currentWeek = this._currentWeekIndex();
        this._buildTimetable();
        this._updateSelect();
        this._updateSubtitle();
        this._updateProgress();
        this._updateArrows();
        this.save();
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
        if (this._currentWeek < this._weekData.length - 1) {
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
        this._updateSubtitle();
        this._updateProgress();
        this._updateArrows();
        this.save();
    }
    save() {
        try { localStorage.setItem('currentWeek', this._currentWeek); } catch (e) {}
    }
    load() {
        try {
            const saved = parseInt(localStorage.getItem('currentWeek'));
            if (!isNaN(saved) && saved >= 0 && saved < this._weekData.length) {
                this._currentWeek = saved;
            }
        } catch (e) {}
    }
    initKeyboard() {
        if (this._keyboardBound) return;
        this._keyboardBound = true;
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;
            if (e.key === '[') { e.preventDefault(); this.prevWeek(); }
            if (e.key === ']') { e.preventDefault(); this.nextWeek(); }
        });
    }
    initTodayBtn() {
        var btn = document.getElementById('todayBtn');
        if (btn) btn.addEventListener('click', () => this.jumpToToday());
    }
    // Public aliases matching proposal naming (for direct callers)
    saveWeek() { this.save(); }
    loadSavedWeek() { this.load(); }
    initWeekKeyboardShortcuts() { this.initKeyboard(); }
    updateWeekSubtitle() { this._updateSubtitle(); }
    updateWeekProgress() { this._updateProgress(); }
    // Internal helpers
    _currentWeekIndex() { /* existing currentWeekIndex() */ }
    _buildTimetable() {
        if (typeof window.buildTimetable === 'function') window.buildTimetable();
    }
    _updateSelect() {
        var sel = document.getElementById('weekSelect');
        if (sel) sel.selectedIndex = this._currentWeek;
    }
    _updateSubtitle() {
        const el = document.getElementById('weekSubtitle');
        if (el) {
            el.textContent = 'Week ' + (this._currentWeek + 1) + ' of ' + this._semester.weeks + ' \u00B7 ' + DateHelper.fmt(this._weekData[this._currentWeek].start) + ' \u00B7 ' + DateHelper.fmt(this._weekData[this._currentWeek].end);
        }
    }
    _updateProgress() {
        const pct = ((this._currentWeek + 1) / this._semester.weeks) * 100;
        const fill = document.getElementById('progressFill');
        const label = document.getElementById('progressLabel');
        if (fill) fill.style.width = pct + '%';
        if (label) label.textContent = 'Week ' + (this._currentWeek + 1) + ' of ' + this._semester.weeks;
    }
    _updateArrows() {
        const prevDisabled = this._currentWeek <= 0;
        const nextDisabled = this._currentWeek >= this._weekData.length - 1;
        updateWeekArrows(prevDisabled, nextDisabled);
    }
    _scrollToGrid() {
        var grid = document.querySelector('.grid-wrapper');
        if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
```

**Exposure:** Each template creates instance as `window.weekNav` so Blade partial onclick handlers (`prevWeek()`, `nextWeek()`, `selectWeek()`) delegate:
```javascript
window.weekNav = new WeekNavigator(MockData.semester, weekData);
function prevWeek() { window.weekNav.prevWeek(); }
function nextWeek() { window.weekNav.nextWeek(); }
function selectWeek(i) { window.weekNav.selectWeek(parseInt(i)); }
function saveWeek() { window.weekNav.save(); }
```

## Data Flow

```
Template (page-specific)
  │
  ├─ defines weekData array (page-specific)
  ├─ creates WeekNavigator instance → window.weekNav
  ├─ defines thin wrapper functions (prevWeek, nextWeek, etc.)
  │    → delegates to window.weekNav methods
  │
  ├─ creates TableController instance (if table page)
  │    → passes column config + render callback
  │
  ├─ creates ModalController instance (if modal page)
  │    → passes modal ID + render function
  │
  └─ calls SkeletonLoader, toast.show(), DateHelper, HtmlBuilder directly

ui-common.js (shared)
  │
  ├─ DateHelper — static, no state
  ├─ HtmlBuilder — static, depends on DateHelper
  ├─ SkeletonLoader — namespace, stateless, container-scoped
  ├─ ToastManager — singleton, holds _timer
  ├─ ModalController — per-instance, holds modalId + renderFn
  ├─ TableController — per-instance, holds sortState + columns + render callback
  └─ WeekNavigator — per-instance, holds currentWeek + weekData + semester
```

## Blade Partial Compatibility

`resources/views/partials/ui-week-nav.blade.php` uses inline onclick handlers:
- `prevWeek()` → `onclick="{{ $prevOnclick }}"`
- `nextWeek()` → `onclick="{{ $nextOnclick }}"`
- `selectWeek(this.value)` → `onclick="{{ $selectOnclick }}"`

After refactor, templates pass wrapper function names that delegate to `window.weekNav`:
```php
@include('partials.ui-week-nav', [
    'prevOnclick' => 'prevWeek()',
    'nextOnclick' => 'nextWeek()',
    'selectOnclick' => 'selectWeek(this.value)',
])
```

The wrapper functions (`prevWeek`, `nextWeek`, `selectWeek`) are defined in each template's `<script>` block and delegate to `window.weekNav`.

## Template Migration Steps

Each timetable template must:

1. **Create WeekNavigator instance:**
```javascript
window.weekNav = new WeekNavigator(MockData.semester, weekData);
```

2. **Define wrapper functions:**
```javascript
function prevWeek() { window.weekNav.prevWeek(); }
function nextWeek() { window.weekNav.nextWeek(); }
function selectWeek(i) { window.weekNav.selectWeek(parseInt(i)); }
function saveWeek() { window.weekNav.save(); }
```

3. **Replace direct calls:**
- `initTodayBtn()` → `window.weekNav.initTodayBtn()`
- `initWeekKeyboardShortcuts()` → `window.weekNav.initKeyboard()`

4. **Replace global variable references in template code:**
- `currentWeek` → `window.weekNav.currentWeek`
- `weekData` → `window.weekNav.weekData`
- `MockData.semester` → `window.weekNav.semester` (if accessed directly)

5. **Update `buildTimetable()` to use navigator state:**
```javascript
function buildTimetable() {
    var week = window.weekNav.currentWeek;
    var data = window.weekNav.weekData;
    // ... existing logic using week and data
}
```

## Remaining Standalone Functions (out of scope)

These stay as global functions in `ui-common.js`:
- `updateIcon`, `toggleTheme`, `navigateHome`
- `ripple`, `togglePassword`
- `initMobileNav`, `initSwipeGesture`, `initCollapsibleCards`
- `initScrollRestore`, `saveScrollPosition`, `restoreScrollPosition`, `clearScrollPosition`
- `updateNavBadge`, `statusClass`, `getWeekRange`, `isInWeek`, `getWeekNumber`
- `weekFilterChanged`, `prevWeekFilter`, `nextWeekFilter`
- `goToReplacement`
- `createStatePersistence`
- Constants: `hours`, `dayNames`, `weekRanges`
