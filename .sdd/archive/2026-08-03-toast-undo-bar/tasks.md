# Tasks: Toast/Undo Bar for Critical Actions

## Task 1: Add Toast CSS to theme.css
- **File:** `public/css/theme.css`
- **Action:** Append 45 lines of toast CSS at end of file (after line 1604)
- **CSS classes:** `.toast-bar`, `.toast-bar.visible`, `.toast-message`, `.toast-undo`, `.toast-undo:hover`, `.toast-close`, `.toast-close:hover`, `@keyframes toastSlideUp`
- [x] Complete

## Task 2: Add Toast JS to ui-common.js
- **File:** `public/js/ui-common.js`
- **Action:** Append 35 lines of toast JS at end of file (after line 320)
- **Functions:** `showToast(message, undoCallback, duration)`, `dismissToast()`
- [x] Complete

## Task 3: Add Toast HTML to layout
- **File:** `resources/views/layouts/ui-template.blade.php`
- **Action:** Add 5 lines before `</body>` (after `<script>` block)
- **HTML:** `<div class="toast-bar" id="toastBar">` with message, undo, close elements
- [x] Complete

## Task 4: Refactor my-request-history single cancel
- **File:** `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`
- **Action:** Edit `confirmCancelAction` click handler (line 1163-1172)
- **Change:** Save removed item before splice, call `showToast()` with undo callback
- [x] Complete

## Task 5: Refactor my-request-history batch cancel
- **File:** `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`
- **Action:** Edit `confirmBatchCancelAction` click handler (line 1203-1208)
- **Change:** Save removed items before filter, call `showToast()` with undo callback
- [x] Complete

## Task 6: Refactor MyTimetable cancel class
- **File:** `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`
- **Action:** Edit `closeCancelConfirm()` function (line 415-420)
- **Change:** Call `showToast()` when confirmed (undo is no-op since no data removed)
- [x] Complete

## Task 7: Refactor replacement-arrangement submit
- **File:** `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`
- **Action:** Edit `proceed()` confirm callback (line 1325-1328)
- **Change:** Add `showToast()` call after `showConfirmModal()` (no undo — final)
- [x] Complete

## Task 8: Refactor replacement-arrangement clear all
- **File:** `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`
- **Action:** Edit `clearAll()` confirm callback (line 1336-1346)
- **Change:** Save cells/slots before clear, call `showToast()` with undo callback
- [x] Complete

## Task 9: Refactor replacement-arrangement navigate away
- **File:** `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`
- **Action:** Edit `navigateTo()` confirm callback (line 1354-1356)
- **Change:** Save cells/slots before clear, call `showToast()` with undo callback
- [x] Complete

## Task 10: Refactor replacement-arrangement goBack
- **File:** `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`
- **Action:** Edit `goBack()` function (line 1398-1408)
- **Change:** Save cells/slots before clear, call `showToast()` with undo callback, delay navigation 5s via setTimeout (undo clears timer + restores state)
- [x] Complete

## Task 11: Update changelogs
- **Files:** `page-changelogs/my-request-history-changelog.md`, `page-changelogs/replacement-arrangement-changelog.md`
- **Action:** Add toast entries for theme.css, ui-common.js, layouts/ui-template.blade.php, and template changes
- [x] Complete
