# Tasks: Phase 2 — Template Migration to Shared OOP Classes

## Task 1: Add new DateHelper methods to ui-common.js
- [x] Add `DateHelper.fmtShort(d)` static method
- [x] Add `DateHelper.weekRangeLabel(weekNum)` static method
- [x] Verify existing tests still pass

## Task 2: Migrate student-my-timetable (simplest template)
- [x] Replace `fmtShort(d)` call with `DateHelper.fmtShort(d)`
- [x] Remove inline `fmtShort()` function definition (L61-64)
- [x] Test page loads without console errors

## Task 3: Migrate request-approval
- [x] Replace `formatShortDate(iso)` calls with `DateHelper.formatDate(iso)`
- [x] Remove inline `formatShortDate()` function (L553-558)
- [x] Refactor `renderTable()` (L882-893) to use `HtmlBuilder` for row construction
- [x] Refactor `renderCards()` (L894-950) to use `HtmlBuilder` for card construction
- [x] Test page loads without console errors

## Task 4: Migrate my-request-history
- [x] Refactor `renderTable()` (L560-745) to use `HtmlBuilder` for row construction
- [x] Refactor `renderCards()` (L747-810) to use `HtmlBuilder` for card construction
- [x] Test page loads without console errors

## Task 5a: Migrate replacement-home — week helpers
- [x] Replace `computeWeek(isoDate)` calls with `getWeekNumber(iso)`
- [x] Remove inline `computeWeek()` function (L270-277)
- [x] Replace `weekRangeLabel(weekNum)` calls with `DateHelper.weekRangeLabel(weekNum)`
- [x] Remove inline `weekRangeLabel()` function (L279-292)
- [x] Test page loads without console errors

## Task 5b: Migrate replacement-home — buildTable
- [x] Refactor `buildTable()` (L299-392) to use `HtmlBuilder` for row construction
- [x] Test page loads without console errors

## Task 5c: Migrate replacement-home — renderCards
- [x] Refactor `renderCards()` (L394-450) to use `HtmlBuilder` for card construction
- [x] Test page loads without console errors

## Task 6: Final verification
- [x] Run full test suite (`python -m pytest tests/Browser/test_ui_oop_refactor.py -v`)
- [x] Verify all 4 migrated pages load with zero console errors
- [x] Update page-changelogs/my-request-history.md
- [x] Update page-changelogs/request-approval.md
- [x] Update page-changelogs/replacement-home.md
- [x] Update page-changelogs/student-my-timetable.md
