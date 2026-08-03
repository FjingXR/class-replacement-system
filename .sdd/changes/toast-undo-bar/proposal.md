# Proposal: Toast/Undo Bar for Critical Actions

## Why This Change Is Needed

The current UI provides confirmation dialogs before critical actions (delete, submit, cancel), but after the action is executed, the only feedback is a browser `alert()` or no feedback at all. This breaks the user flow and provides no opportunity to undo accidental actions.

A **toast/undo bar** at bottom-left (matching the existing `.bulk-action-bar` position) gives users immediate feedback after critical actions with a 5-second window to undo. This follows modern UX patterns ( Gmail, GitHub, Material Design) and makes the system more forgiving — users can explore features without fear, knowing mistakes can be reversed.

This implements **CodingMAIN.md §10.0 rule #10** (toast/undo bar for critical actions).

## Scope

### In Scope

**1. Shared toast component (CSS + JS + HTML)**

- **CSS** in `public/css/theme.css`: `.toast-bar` styles matching `.bulk-action-bar` (surface bg, outline border, shadow, radius-md, padding 10px 16px, fixed bottom-left z-index 200)
- **JS** in `public/js/ui-common.js`: `showToast(message, undoCallback, duration=5000)` + `dismissToast()` helpers
- **HTML** in `resources/views/layouts/ui-template.blade.php`: `<div class="toast-bar" id="toastBar">` before `</body>`

**2. Critical actions to add toast/undo (7 actions across 3 pages)**

| # | Page | Action | Toast Message | Undo? |
|---|------|--------|---------------|-------|
| 1 | my-request-history | `quickCancel(id)` | "Request #{id} cancelled." | ✅ Re-insert at index |
| 2 | my-request-history | `batchCancelSelected()` | "{N} requests cancelled." | ✅ Re-insert all |
| 3 | MyTimetable | `cancelClass()` | "Class cancelled." | ❌ `null` — no undo in mock phase; backend will call API to cancel class, undo will call API to restore |
| 4 | replacement-arrangement | `proceed()` | "Replacement request submitted." | ❌ No undo (final — submission is irreversible) |
| 5 | replacement-arrangement | `clearAll()` | "All selections cleared." | ✅ Restore selections (client-side mock) |
| 6 | replacement-arrangement | `navigateTo()` | "Selections cleared." | ✅ Restore in-memory state (immediate nav — will delay when backend wired) |
| 7 | replacement-arrangement | `goBack()` | "Selections cleared." | ✅ Restore selections + cancel 5s navigation timer |

### Backend Integration Notes (Future Phase)

This SDD is **frontend mock only**. When backend is wired, the following changes apply:

| Action | Mock Phase (current) | Backend Phase (future) |
|--------|---------------------|----------------------|
| `cancelClass()` | `showToast('Class cancelled.', null)` — no undo | `showToast('Class cancelled.', () => undoCancelClass(id))` — undo calls API to restore class |
| `navigateTo()` | Immediate navigation — undo restores in-memory state only | Delay navigation 5s (match `goBack()` pattern) — undo calls API to restore server-side state |
| `proceed()` | `showToast('...submitted.', null)` — no undo | Same — submission is final |
| `quickCancel()` / `batchCancelSelected()` | Undo re-inserts into `mockRequests[]` array | Undo calls API to restore cancelled requests |
| `clearAll()` / `goBack()` | Undo restores in-memory `selectedCells` + `selectedSlotsByVenue` | Undo calls API to restore server-side selections |

### Out of Scope

- Settings pages (delete user, delete passkey, disable 2FA, regenerate recovery codes)
- Approval/rejection toast (PL request-approval page — future SDD change)
- Toast for non-critical actions (navigation, filter changes, etc.)

## Impact Scope

| File | Action |
|------|--------|
| `public/css/theme.css` | **Edit** — add `.toast-bar` CSS styles at end of file |
| `public/js/ui-common.js` | **Edit** — add `showToast()` + `dismissToast()` helpers at end of file |
| `resources/views/layouts/ui-template.blade.php` | **Edit** — add toast HTML before `</body>` |
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | **Edit** — refactor 2 cancel actions to use `showToast()` |
| `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php` | **Edit** — refactor cancelClass() to use `showToast()` |
| `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | **Edit** — refactor 4 actions to use `showToast()` (proceed, clearAll, navigateTo, goBack) |
| `page-changelogs/my-request-history-changelog.md` | **Edit** — add toast entries |
| `page-changelogs/replacement-arrangement-changelog.md` | **Edit** — add toast entries |

No new files are created. The toast component is shared via existing files.
