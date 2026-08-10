# Explore Brief: ui-js-oop-refactor

## Problem

`ui-common.js` is 767 lines of **80+ standalone functions** with no encapsulation. Functions rely on global variables (`currentWeek`, `weekData`, `pageState`), making the code hard to test, maintain, and extend. Timetable templates duplicate week navigation, modal, and table logic across 4+ pages.

## Scope

**In scope:**
- Refactor `ui-common.js` into 7 classes/namespaces
- Keep all classes in `ui-common.js` (no file splitting — no bundler, more HTTP requests not worth it)
- Page-specific functions stay in templates (only shared logic moves to classes)

**Out of scope:**
- Refactoring template-specific functions (buildTimetable, openModal, etc.)
- Creating a base TimetablePage class (too invasive, templates would need class instantiation)
- State Persistence (`createStatePersistence`) — already works as factory function
- Scroll Restoration, Navigation, Request/Status groups — already working as functions

## Classes

### 1. WeekNavigator (class)

Encapsulates week navigation state and logic. Replaces 10 global functions.

```javascript
class WeekNavigator {
    constructor(semesterData, weekData) { ... }
    jumpToToday() { ... }
    prev() { ... }
    next() { ... }
    select(index) { ... }
    save() { ... }      // localStorage
    load() { ... }      // localStorage
    updateSubtitle() { ... }
    updateProgress() { ... }
    updateArrows() { ... }
    get currentWeek() { ... }
    get weekData() { ... }
}
```

**Constructor params:**
- `semesterData` — `{ startDate, weeks }` from MockData.semester
- `weekData` — array built by each template (different per page)

**Current globals replaced:**
- `currentWeek` → `this._currentWeek`
- `weekData` → `this._weekData`
- `MockData.semester` → `this._semester`

**Pages using it:** MyTimetable, CohortTimetable, StudentMyTimetable, VenueTimetable

### 2. TableController (class)

Generic sort + pagination. Config-driven columns.

```javascript
class TableController {
    constructor(config) { ... }
    sort(field) { ... }
    paginate(data, page, pageSize) { ... }
    makeHeader(col) { ... }
    updateResultCount(data, total, label) { ... }
    initRpp(cfg) { ... }
}
```

**Constructor config:**
```javascript
{
    columns: [{ field, label, sortable, cls }],
    sortState: { field, dir },
    render: () => { ... }  // callback to re-render table
}
```

**Current functions replaced:**
- `makeSortableHeader()` → `this.makeHeader()`
- `compareBy()` → `this.sort()`
- `paginate()` → `this.paginate()`
- `initRpp()` → `this.initRpp()`
- `updateResultCount()` → `this.updateResultCount()`

**Pages using it:** replacement-home, request-approval, my-request-history

### 3. SkeletonLoader (namespace object)

Stateless skeleton loading. Organized as namespace, not class.

```javascript
const SkeletonLoader = {
    show(container, type, count) { ... },
    hide(container) { ... },
    with(callback, container, count, delay) { ... },
    showSummary() { ... },
    hideSummary() { ... }
};
```

**Current functions replaced:**
- `showSkeleton()` → `SkeletonLoader.show()`
- `hideSkeleton()` → `SkeletonLoader.hide()`
- `withSkeleton()` → `SkeletonLoader.with()`
- `showSummarySkeleton()` → `SkeletonLoader.showSummary()`
- `hideSummarySkeleton()` → `SkeletonLoader.hideSummary()`

### 4. ToastManager (singleton class)

One global instance. Manages the toast bar.

```javascript
const toast = new ToastManager();
toast.show(message, undoCallback, duration, linkText, linkUrl, details);
toast.dismiss();
```

**Current functions replaced:**
- `showToast()` → `toast.show()`
- `dismissToast()` → `toast.dismiss()`
- `_toastTimer` → `this._timer`

### 5. ModalController (class)

Generic modal with render callback.

```javascript
class ModalController {
    constructor(modalId, renderFn) { ... }
    open(data) { ... }
    close() { ... }
    isOpen() { ... }
}
```

**Constructor params:**
- `modalId` — DOM id of the modal element
- `renderFn(data)` — callback that populates modal content

**Behavior:**
- `open(data)` → calls `renderFn(data)`, shows modal, binds ESC + overlay click
- `close()` → hides modal, unbinds listeners
- ESC key and overlay click auto-close

**Current functions replaced:**
- `closeOnEsc()` → handled internally
- `closeOnOverlayClick()` → handled internally

### 6. DateHelper (static class)

Pure date/time formatting utilities.

```javascript
class DateHelper {
    static to12h(t) { ... }
    static formatDate(iso) { ... }
    static formatDateTime(iso) { ... }
    static fmt(d) { ... }
    static add30min(t) { ... }
    static dayAbbr(day) { ... }
    static isoDayName(iso) { ... }
    static getTodayMs() { ... }
}
```

### 7. HtmlBuilder (static class)

HTML-generating functions.

```javascript
class HtmlBuilder {
    static classBlock(r) { ... }
    static replacementBlock(r) { ... }
    static dayHeader(day) { ... }
}
```

**Current functions replaced:**
- `formatClassBlock()` → `HtmlBuilder.classBlock()`
- `formatReplacementBlock()` → `HtmlBuilder.replacementBlock()`
- `buildDayHtml()` → `HtmlBuilder.dayHeader()`

## Migration Strategy

Each class is added **incrementally** — no big-bang rewrite:

1. Add class to `ui-common.js` (below existing functions)
2. Update one template to use the new class
3. Verify no regressions
4. Remove old standalone function (after all pages migrated)
5. Repeat for next class

**Order:**
```
DateHelper → HtmlBuilder → SkeletonLoader → ToastManager → ModalController → TableController → WeekNavigator
```

(Simplest → most complex, each step is independently testable)

## Cross-Module Data Flows

```
Template (page-specific)
  ├── builds weekData → passes to WeekNavigator constructor
  ├── builds column config → passes to TableController constructor
  ├── provides renderFn → passes to ModalController constructor
  └── calls class methods for navigation, sort, pagination

ui-common.js (shared)
  ├── WeekNavigator — holds currentWeek, weekData, semester
  ├── TableController — holds sortState, columns, render callback
  ├── SkeletonLoader — stateless, takes container param
  ├── ToastManager — singleton, holds _timer
  ├── ModalController — holds modalId, renderFn
  ├── DateHelper — static, no state
  └── HtmlBuilder — static, no state
```

## Rejected Approaches

| Approach | Reason Rejected |
|----------|----------------|
| Split into separate JS files | No bundler = more HTTP requests, worse performance |
| Create base TimetablePage class | Too invasive — templates would need class instantiation, not just function calls |
| Refactor template-specific functions | Page-specific logic (buildTimetable, openModal) differs too much between pages |
| Refactor Scroll Restoration | Already works well as functions, minimal encapsulation benefit |
| Refactor State Persistence | Already returns object with .save()/.restore() — already OOP-ish |

## Open Questions

- Should the WeekNavigator auto-save week to localStorage on every navigation, or only on explicit save() call?
- Should the TableController emit events when sort/page changes, or just call the render callback?
