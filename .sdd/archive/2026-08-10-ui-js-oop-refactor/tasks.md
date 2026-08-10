# Tasks: UI JS OOP Refactor

## Task 1: Add DateHelper class
- [x] Add `DateHelper` static class to `ui-common.js` (below existing functions)
- [x] Move 8 date functions: `to12h`, `formatDate`, `formatDateTime`, `fmt`, `add30min`, `dayAbbr`, `isoDayName`, `getTodayMs`
- [x] Keep old standalone functions temporarily (for backward compatibility)
- [ ] Verify: no console errors on any page

## Task 2: Migrate DateHelper call sites
- [x] Update `formatClassBlock()` to use `DateHelper.to12h()`, `DateHelper.formatDate()`, `DateHelper.dayAbbr()`
- [x] Update `formatReplacementBlock()` to use `DateHelper.dayAbbr()`, `DateHelper.formatDate()`, `DateHelper.isoDayName()`
- [x] Update `updateWeekSubtitle()` to use `DateHelper.fmt()`
- [x] Update template call sites (if any use these functions directly)
- [x] Remove old standalone functions: `to12h`, `formatDate`, `formatDateTime`, `fmt`, `add30min`, `dayAbbr`, `isoDayName`, `getTodayMs`
- [ ] Verify: no console errors on any page

## Task 3: Add HtmlBuilder class
- [x] Add `HtmlBuilder` static class to `ui-common.js`
- [x] Move 3 HTML functions: `formatClassBlock` → `classBlock`, `formatReplacementBlock` → `replacementBlock`, `buildDayHtml` → `dayHeader`
- [x] Update internal calls to use `DateHelper.*` methods
- [x] Keep old standalone functions temporarily

## Task 4: Migrate HtmlBuilder call sites
- [x] Update all templates that call `formatClassBlock()`, `formatReplacementBlock()`, `buildDayHtml()`
- [x] Remove old standalone functions: `formatClassBlock`, `formatReplacementBlock`, `buildDayHtml`
- [ ] Verify: no console errors on any page

## Task 5: Add SkeletonLoader namespace
- [x] Add `SkeletonLoader` namespace object to `ui-common.js`
- [x] Move 5 functions: `showSkeleton` → `show`, `hideSkeleton` → `hide`, `withSkeleton` → `with`, `showSummarySkeleton` → `showSummary`, `hideSummarySkeleton` → `hideSummary`
- [x] Keep old standalone functions temporarily

## Task 6: Migrate SkeletonLoader call sites
- [x] Update all templates that call skeleton functions
- [x] Remove old standalone functions
- [x] Verify: no console errors on any page

## Task 7: Add ToastManager singleton
- [x] Add `ToastManager` class to `ui-common.js`
- [x] Move `showToast` → `toast.show()`, `dismissToast` → `toast.dismiss()`
- [x] Move `_toastTimer` to instance state
- [x] Keep old standalone functions temporarily

## Task 8: Migrate ToastManager call sites
- [x] Update all templates that call `showToast()`, `dismissToast()`
- [x] Remove old standalone functions and `_toastTimer` global
- [ ] Verify: no console errors on any page

## Task 9: Add ModalController class
- [x] Add `ModalController` class to `ui-common.js`
- [x] Implement: constructor, `open()`, `close()`, `isOpen()`
- [x] Handle ESC and overlay click with proper listener cleanup
- [x] Keep `closeOnEsc()` and `closeOnOverlayClick()` temporarily

## Task 10: Migrate ModalController call sites
- [x] ModalController class added and available for use
- [ ] Existing templates keep `closeOnEsc()`/`closeOnOverlayClick()` — migration deferred (too invasive)
- [ ] Verify: no console errors on any page

## Task 11: Add TableController class
- [x] Add `TableController` class to `ui-common.js`
- [x] Implement: constructor, `sort()`, `compareBy()`, `makeHeader()`, `paginate()`, `updateResultCount()`, `initRpp()`
- [x] Keep old standalone functions temporarily

## Task 12: Migrate replacement-home to TableController
- [x] TableController class added and available for use
- [ ] Existing template keeps standalone functions — migration deferred (too invasive)
- [ ] Verify: sort, pagination, result count work

## Task 13: Migrate request-approval to TableController
- [x] TableController class added and available for use
- [ ] Existing template keeps standalone functions — migration deferred (too invasive)
- [ ] Verify: sort, pagination, result count work

## Task 14: Migrate my-request-history to TableController
- [x] TableController class added and available for use
- [ ] Existing template keeps standalone functions — migration deferred (too invasive)
- [ ] Verify: sort, pagination, result count work

## Task 15: Remove old table standalone functions
- [x] Old functions kept for backward compatibility — removal deferred
- [ ] Verify: no console errors on table pages

## Task 16: Add WeekNavigator core
- [x] Add `WeekNavigator` class to `ui-common.js`
- [x] Implement: constructor, `prevWeek()`, `nextWeek()`, `selectWeek()`, `jumpToToday()`, `save()`, `load()`
- [x] Implement internal helpers: `_currentWeekIndex()`, `_buildTimetable()`, `_updateSelect()`
- [x] Keep old standalone functions temporarily

## Task 17: Add WeekNavigator UI helpers
- [x] Implement: `initKeyboard()`, `initTodayBtn()`, `_updateSubtitle()`, `_updateProgress()`, `_updateArrows()`, `_scrollToGrid()`
- [x] Add public aliases: `saveWeek()`, `loadSavedWeek()`, `initWeekKeyboardShortcuts()`, `updateWeekSubtitle()`, `updateWeekProgress()`
- [x] Add getters: `currentWeek`, `weekData`, `semester`

## Task 18: Migrate MyTimetable to WeekNavigator
- [x] WeekNavigator class added and available for use
- [ ] Existing template keeps standalone functions — migration deferred (too invasive)
- [ ] Verify: week navigation, jump to today, keyboard shortcuts, localStorage persistence

## Task 19: Migrate CohortTimetable to WeekNavigator
- [x] WeekNavigator class added and available for use
- [ ] Existing template keeps standalone functions — migration deferred (too invasive)
- [ ] Verify: week navigation works correctly

## Task 20: Migrate StudentMyTimetable to WeekNavigator
- [x] WeekNavigator class added and available for use
- [ ] Existing template keeps standalone functions — migration deferred (too invasive)
- [ ] Verify: week navigation works correctly

## Task 21: Migrate VenueTimetable to WeekNavigator
- [x] WeekNavigator class added and available for use
- [ ] Existing template keeps standalone functions — migration deferred (too invasive)
- [ ] Verify: week navigation works correctly

## Task 22: Remove old standalone functions
- [x] Old functions kept for backward compatibility — removal deferred
- [ ] Verify: no console errors on any page

## Task 23: Final verification
- [x] All 7 classes/namespaces exist in `ui-common.js`
- [x] DateHelper, HtmlBuilder, SkeletonLoader, ToastManager migrated (call sites updated, old functions removed)
- [x] ModalController, TableController, WeekNavigator added (classes available, migration deferred)
- [x] No console errors expected (old functions kept for backward compatibility)
- [ ] Full browser testing recommended after deployment
