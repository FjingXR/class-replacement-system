# Tasks: UI JS OOP Refactor

## Task 1: Add DateHelper class
- [ ] Add `DateHelper` static class to `ui-common.js` (below existing functions)
- [ ] Move 8 date functions: `to12h`, `formatDate`, `formatDateTime`, `fmt`, `add30min`, `dayAbbr`, `isoDayName`, `getTodayMs`
- [ ] Keep old standalone functions temporarily (for backward compatibility)
- [ ] Verify: no console errors on any page

## Task 2: Migrate DateHelper call sites
- [ ] Update `formatClassBlock()` to use `DateHelper.to12h()`, `DateHelper.formatDate()`, `DateHelper.dayAbbr()`
- [ ] Update `formatReplacementBlock()` to use `DateHelper.dayAbbr()`, `DateHelper.formatDate()`, `DateHelper.isoDayName()`
- [ ] Update `updateWeekSubtitle()` to use `DateHelper.fmt()`
- [ ] Update template call sites (if any use these functions directly)
- [ ] Remove old standalone functions: `to12h`, `formatDate`, `formatDateTime`, `fmt`, `add30min`, `dayAbbr`, `isoDayName`, `getTodayMs`
- [ ] Verify: no console errors on any page

## Task 3: Add HtmlBuilder class
- [ ] Add `HtmlBuilder` static class to `ui-common.js`
- [ ] Move 3 HTML functions: `formatClassBlock` → `classBlock`, `formatReplacementBlock` → `replacementBlock`, `buildDayHtml` → `dayHeader`
- [ ] Update internal calls to use `DateHelper.*` methods
- [ ] Keep old standalone functions temporarily

## Task 4: Migrate HtmlBuilder call sites
- [ ] Update all templates that call `formatClassBlock()`, `formatReplacementBlock()`, `buildDayHtml()`
- [ ] Remove old standalone functions: `formatClassBlock`, `formatReplacementBlock`, `buildDayHtml`
- [ ] Verify: no console errors on any page

## Task 5: Add SkeletonLoader namespace
- [ ] Add `SkeletonLoader` namespace object to `ui-common.js`
- [ ] Move 5 functions: `showSkeleton` → `show`, `hideSkeleton` → `hide`, `withSkeleton` → `with`, `showSummarySkeleton` → `showSummary`, `hideSummarySkeleton` → `hideSummary`
- [ ] Keep old standalone functions temporarily

## Task 6: Migrate SkeletonLoader call sites
- [ ] Update all templates that call skeleton functions
- [ ] Remove old standalone functions
- [ ] Verify: no console errors on any page

## Task 7: Add ToastManager singleton
- [ ] Add `ToastManager` class to `ui-common.js`
- [ ] Move `showToast` → `toast.show()`, `dismissToast` → `toast.dismiss()`
- [ ] Move `_toastTimer` to instance state
- [ ] Keep old standalone functions temporarily

## Task 8: Migrate ToastManager call sites
- [ ] Update all templates that call `showToast()`, `dismissToast()`
- [ ] Remove old standalone functions and `_toastTimer` global
- [ ] Verify: no console errors on any page

## Task 9: Add ModalController class
- [ ] Add `ModalController` class to `ui-common.js`
- [ ] Implement: constructor, `open()`, `close()`, `isOpen()`
- [ ] Handle ESC and overlay click with proper listener cleanup
- [ ] Keep `closeOnEsc()` and `closeOnOverlayClick()` temporarily

## Task 10: Migrate ModalController call sites
- [ ] Update templates that use modal patterns to create `ModalController` instances
- [ ] Remove old `closeOnEsc()` and `closeOnOverlayClick()` functions
- [ ] Verify: no console errors, modals open/close correctly

## Task 11: Add TableController class
- [ ] Add `TableController` class to `ui-common.js`
- [ ] Implement: constructor, `sort()`, `compareBy()`, `makeHeader()`, `paginate()`, `updateResultCount()`, `initRpp()`
- [ ] Keep old standalone functions temporarily

## Task 12: Migrate replacement-home to TableController
- [ ] Create `TableController` instance with column config
- [ ] Replace `makeSortableHeader()`, `compareBy()`, `paginate()`, `initRpp()`, `updateResultCount()` calls
- [ ] Verify: sort, pagination, result count work

## Task 13: Migrate request-approval to TableController
- [ ] Create `TableController` instance with column config
- [ ] Replace standalone function calls
- [ ] Verify: sort, pagination, result count work

## Task 14: Migrate my-request-history to TableController
- [ ] Create `TableController` instance with column config
- [ ] Replace standalone function calls
- [ ] Verify: sort, pagination, result count work

## Task 15: Remove old table standalone functions
- [ ] Remove: `makeSortableHeader`, `compareBy`, `paginate`, `initRpp`, `updateResultCount`
- [ ] Verify: no console errors on table pages

## Task 16: Add WeekNavigator core
- [ ] Add `WeekNavigator` class to `ui-common.js`
- [ ] Implement: constructor, `prevWeek()`, `nextWeek()`, `selectWeek()`, `jumpToToday()`, `save()`, `load()`
- [ ] Implement internal helpers: `_currentWeekIndex()`, `_buildTimetable()`, `_updateSelect()`
- [ ] Keep old standalone functions temporarily

## Task 17: Add WeekNavigator UI helpers
- [ ] Implement: `initKeyboard()`, `initTodayBtn()`, `_updateSubtitle()`, `_updateProgress()`, `_updateArrows()`, `_scrollToGrid()`
- [ ] Add public aliases: `saveWeek()`, `loadSavedWeek()`, `initWeekKeyboardShortcuts()`, `updateWeekSubtitle()`, `updateWeekProgress()`
- [ ] Add getters: `currentWeek`, `weekData`, `semester`

## Task 18: Migrate MyTimetable to WeekNavigator
- [ ] Create `WeekNavigator` instance as `window.weekNav`
- [ ] Define wrapper functions: `prevWeek()`, `nextWeek()`, `selectWeek()`, `saveWeek()`
- [ ] Replace `initTodayBtn()` → `window.weekNav.initTodayBtn()`
- [ ] Replace `initWeekKeyboardShortcuts()` → `window.weekNav.initKeyboard()`
- [ ] Update `buildTimetable()` to use `window.weekNav.currentWeek` and `window.weekNav.weekData`
- [ ] Verify: week navigation, jump to today, keyboard shortcuts, localStorage persistence

## Task 19: Migrate CohortTimetable to WeekNavigator
- [ ] Same steps as Task 18
- [ ] Verify: week navigation works correctly

## Task 20: Migrate StudentMyTimetable to WeekNavigator
- [ ] Same steps as Task 18
- [ ] Verify: week navigation works correctly

## Task 21: Migrate VenueTimetable to WeekNavigator
- [ ] Same steps as Task 18
- [ ] Verify: week navigation works correctly

## Task 22: Remove old standalone functions
- [ ] Remove: `jumpToToday`, `initTodayBtn`, `currentWeekIndex`, `updateWeekSubtitle`, `updateWeekProgress`, `updateWeekArrows` (if all pages migrated)
- [ ] Remove global variables: `currentWeek`, `weekData` (if no longer referenced)
- [ ] Verify: no console errors on any page

## Task 23: Final verification
- [ ] Test all timetable pages: MyTimetable, CohortTimetable, StudentMyTimetable, VenueTimetable
- [ ] Test all table pages: replacement-home, request-approval, my-request-history
- [ ] Test modal open/close on all pages with modals
- [ ] Test toast notifications
- [ ] Test skeleton loading
- [ ] Test keyboard shortcuts ([ and ])
- [ ] Test localStorage persistence (refresh page, week should persist)
- [ ] No console errors on any page
- [ ] All existing functionality preserved
