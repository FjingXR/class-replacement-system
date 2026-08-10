# Tasks: Phase 2 — Template Migration to Shared OOP Classes

## Task 1: Add new DateHelper methods to ui-common.js
- [ ] Add `DateHelper.fmtShort(d)` static method
- [ ] Add `DateHelper.weekRangeLabel(weekNum)` static method
- [ ] Verify existing tests still pass

## Task 2: Migrate student-my-timetable (simplest template)
- [ ] Replace `fmtShort(d)` call with `DateHelper.fmtShort(d)`
- [ ] Remove inline `fmtShort()` function definition (L61-64)
- [ ] Test page loads without console errors

## Task 3: Migrate request-approval
- [ ] Replace `formatShortDate(iso)` calls with `DateHelper.formatDate(iso)`
- [ ] Remove inline `formatShortDate()` function (L553-558)
- [ ] Refactor `renderTable()` (L882-893) to use `HtmlBuilder` for row construction
- [ ] Refactor `renderCards()` (L894-950) to use `HtmlBuilder` for card construction
- [ ] Test page loads without console errors

## Task 4: Migrate my-request-history
- [ ] Refactor `renderTable()` (L560-745) to use `HtmlBuilder` for row construction
- [ ] Refactor `renderCards()` (L747-810) to use `HtmlBuilder` for card construction
- [ ] Test page loads without console errors

## Task 5a: Migrate replacement-home — week helpers
- [ ] Replace `computeWeek(isoDate)` calls with `getWeekNumber(iso)`
- [ ] Remove inline `computeWeek()` function (L270-277)
- [ ] Replace `weekRangeLabel(weekNum)` calls with `DateHelper.weekRangeLabel(weekNum)`
- [ ] Remove inline `weekRangeLabel()` function (L279-292)
- [ ] Test page loads without console errors

## Task 5b: Migrate replacement-home — buildTable
- [ ] Refactor `buildTable()` (L299-392) to use `HtmlBuilder` for row construction
- [ ] Test page loads without console errors

## Task 5c: Migrate replacement-home — renderCards
- [ ] Refactor `renderCards()` (L394-450) to use `HtmlBuilder` for card construction
- [ ] Test page loads without console errors

## Task 6: Final verification
- [ ] Run full test suite (`python -m pytest tests/Browser/test_ui_oop_refactor.py -v`)
- [ ] Verify all 4 migrated pages load with zero console errors
- [ ] Update page-changelogs/my-request-history.md
- [ ] Update page-changelogs/request-approval.md
- [ ] Update page-changelogs/replacement-home.md
- [ ] Update page-changelogs/student-my-timetable.md
