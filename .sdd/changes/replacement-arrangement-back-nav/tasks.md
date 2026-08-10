# Tasks: Replacement Arrangement — Dynamic Back Navigation (OOP)

## Task 1: Add BackNavigator class to ui-common.js
- [x] Add `BackNavigator` class with `getBackUrl()` and `navigate()` static methods
- [x] Verify existing tests still pass

## Task 2: Add `from` parameter to source pages
- [x] replacement-home: Add `&from=replacement-home` to navigation URL (L420)
- [x] my-request-history: Add `?from=my-request-history` to CTA URL (L436)
- [x] venue-timetable: Add `&from=venue-timetable` to navigation URL (L1413)
- [ ] Test each source page navigates correctly

## Task 3: Update replacement-arrangement back button
- [x] Replace hardcoded `/replacement-home-ui` with `BackNavigator.navigate()` in `goBack()` (L1499)
- [x] Test back button returns to correct source page

## Task 4: Final verification
- [x] Test all 3 source pages → replacement-arrangement → back button flow
- [x] Test fallback when no `from` parameter
