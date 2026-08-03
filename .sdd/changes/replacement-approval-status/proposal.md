# Proposal: Replacement Approval Status

## Why

After submitting replacement slots, lecturers see a brief "Submitted" toast that disappears within 2 seconds. There is no persistent visual feedback confirming the submission was received, and no easy way to navigate to the request history page to track approval status.

**Current pain points:**
- Toast disappears too quickly — lecturer may not notice it
- No slot details shown — lecturer can't verify what was submitted
- No shortcut to `/my-request-history-ui` — must manually navigate via menu

## What

### In Scope

1. **Extend the submission toast** in `ui-common.js` to support an optional link button
2. **Update the toast HTML** in `ui-template.blade.php` to include a link element
3. **Modify the `proceed()` flow** in `replacement-arrangement-UIdesign-template.blade.php` to show an enhanced toast with:
   - Status text: "✓ Submitted — Pending Approval"
   - Slot details: venue code, day, date, time range
   - "View →" link button navigating to `/my-request-history-ui`
   - Auto-dismiss after 5 seconds (extended from current 2 seconds)
4. **DRY cleanup** — Remove the page-local `showToast()` function (line 1528) and its associated `.toast` CSS (lines 758-772), then migrate all 6 call sites to use the shared version from `ui-common.js`

### Out of Scope

- No persistent status bar on the arrangement page
- No approval/rejection status tracking (no backend yet)
- No resubmit rejected requests flow
- No real-time status updates (no WebSocket/Livewire)
- No changes to the my-request-history page itself
- No new mock data additions

## Impact

### Files Modified

| File | Change Type | Description |
|------|-------------|-------------|
| `public/js/ui-common.js` | Extend | Add `linkText` and `linkUrl` optional parameters to `showToast()` |
| `resources/views/layouts/ui-template.blade.php` | Extend | Add `<a class="toast-link">` element inside `#toastBar` |
| `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | Modify | Remove page-local `showToast()` (line 1528) and `.toast` CSS (lines 758-772), migrate 6 call sites to shared version, update `proceed()` to show enhanced toast |

### Dependencies

- Existing `showToast()` function in `ui-common.js` (lines 387-410)
- Existing `#toastBar` element in `ui-template.blade.php` (lines 43-48)
- Existing toast CSS styles in `theme.css`
- Current `proceed()` function in replacement-arrangement template (line 1378)

### Risk

- **Low risk** — Extends existing toast system with backward-compatible parameters
- DRY cleanup removes duplication without changing behavior
- No breaking changes to other pages using `showToast()`
- No new dependencies or libraries

## FR Traceability

| FR | Description | This Change |
|----|-------------|-------------|
| FR-UI-001 | Lecturer can arrange replacement slots | Enhances post-submission feedback |
| Rule 7 | Promote-on-3rd-duplication (DRY/OOP) | Removes page-local showToast() duplicate |

## Acceptance Criteria

- [ ] After confirming submission, toast shows "✓ Submitted — Pending Approval"
- [ ] Toast includes slot details: venue code, day, date, time range
- [ ] Toast has "View →" link that navigates to `/my-request-history-ui`
- [ ] Toast auto-dismisses after 5 seconds
- [ ] Existing undo functionality still works (backward compatible)
- [ ] Mobile: toast displays correctly at 768px breakpoint
