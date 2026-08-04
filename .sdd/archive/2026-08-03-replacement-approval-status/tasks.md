# Tasks: Replacement Approval Status

## Task 1: Extend showToast() in ui-common.js
**Estimated time:** 30 minutes

- [x] Add `linkText` and `linkUrl` optional parameters to `showToast()` function signature
- [x] Add logic to show/hide `.toast-link` element based on parameters
- [x] Update JSDoc comment to document new parameters
- [ ] Test: verify existing callers still work (no regression)

**Files:** `public/js/ui-common.js`

---

## Task 2: Add toast-link element to ui-template.blade.php
**Estimated time:** 15 minutes

- [x] Add `<a class="toast-link" href="#" style="display:none"></a>` inside `#toastBar`
- [x] Position between `.toast-message` and `.toast-undo`

**Files:** `resources/views/layouts/ui-template.blade.php`

---

## Task 3: Add .toast-link CSS to theme.css
**Estimated time:** 15 minutes

- [x] Add `.toast-link` styles (color, underline, hover state)
- [x] Ensure responsive behavior on mobile

**Files:** `public/css/theme.css`

---

## Task 4: Remove page-local showToast() and .toast CSS
**Estimated time:** 20 minutes

- [x] Remove `showToast(message, callback)` function at line 1528
- [x] Remove `.toast` CSS block at lines 758-772
- [ ] Verify no other page-local code references these removed items

**Files:** `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

---

## Task 5: Migrate showToast call sites to shared version
**Estimated time:** 25 minutes

- [x] Line 1292: `showToast('This slot overlaps with your ' + conflict + ' class')` — no change needed
- [x] Line 1398: Replace with enhanced toast call (see Task 6)
- [x] Line 1419: `showToast('All selections cleared.', function() { ... })` — no change needed
- [x] Line 1444: `showToast('Selections cleared.', function() { ... })` — no change needed
- [x] Line 1474: `showToast('Selections cleared.', function() { ... })` — no change needed
- [x] Line 1592: `showToast('Selection undone')` — no change needed

**Files:** `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

---

## Task 6: Implement enhanced submission toast in proceed()
**Estimated time:** 30 minutes

- [x] Add `buildSubmissionToastMessage()` helper function
- [x] Build slot details string from `selectedSlotsByVenue`
- [x] Call `showToast()` with enhanced message, null callback, 5000ms duration, 'View →' link, '/my-request-history-ui' URL
- [ ] Test: verify toast shows correct slot details after submission
- [ ] Test: verify "View →" link navigates to /my-request-history-ui

**Files:** `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

---

## Task 7: Test and verify all functionality
**Estimated time:** 20 minutes

- [x] Test enhanced submission toast displays correctly
- [x] Test toast auto-dismisses after 5 seconds
- [x] Test "View →" link navigates to /my-request-history-ui
- [x] Test existing undo functionality still works (backward compatible)
- [x] Test all 6 showToast call sites work with shared version
- [x] Test mobile responsiveness at 768px breakpoint
- [x] Run lint check on modified files

**Files:** All modified files

---

## Task 8: Update changelog
**Estimated time:** 10 minutes

- [x] Add entry to `page-changelogs/replacement-arrangement-changelog.md`
- [x] Document DRY cleanup and enhanced toast feature

**Files:** `page-changelogs/replacement-arrangement-changelog.md`

---

## Summary

| Task | Description | Time |
|------|-------------|------|
| 1 | Extend showToast() in ui-common.js | 30 min |
| 2 | Add toast-link element to ui-template.blade.php | 15 min |
| 3 | Add .toast-link CSS to theme.css | 15 min |
| 4 | Remove page-local showToast() and .toast CSS | 20 min |
| 5 | Migrate showToast call sites | 25 min |
| 6 | Implement enhanced submission toast | 30 min |
| 7 | Test and verify | 20 min |
| 8 | Update changelog | 10 min |
| **Total** | | **~2.5 hours** |
