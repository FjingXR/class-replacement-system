# Explore Brief: Replacement Approval Status

**Date:** 2026-08-03
**Status:** Scope clarified, ready for proposal

---

## Problem Statement

After submitting replacement slots, lecturers see a brief "Submitted" toast that disappears. They have no persistent visual feedback that their request was submitted, and no easy way to check its approval status without manually navigating to my-request-history.

**Root cause:** The current flow ends abruptly at submission — the toast is ephemeral and provides no actionable follow-up.

---

## Scope

### What We're Building

**Two enhancements to the replacement-arrangement page:**

1. **Submission status toast (extended)** — After clicking "Proceed" and confirming, show a toast with:
   - Status text: "✓ Submitted — Pending Approval"
   - Submitted slot details: venue, day, date, time range
   - "View →" button that navigates to `/my-request-history-ui`
   - Auto-dismiss after 5 seconds (reuses existing toast system from `ui-common.js`)

2. **No persistent status bar** — The toast is ephemeral. After dismiss, the only way to check status is via `/my-request-history-ui`.

### What We're NOT Building

- No persistent status bar on the arrangement page
- No approval/rejection status tracking (no backend yet)
- No resubmit rejected requests flow
- No real-time status updates (no WebSocket/Livewire)
- No changes to the my-request-history page (assumed to exist)

---

## Key Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Toast vs persistent bar | Toast (ephemeral) | Less page clutter; user chose Option B |
| Toast reuses ui-common.js | Yes | Already has message + undo + auto-dismiss |
| "View →" button | Navigate to `/my-request-history-ui` | User confirmed |
| Show slot details in toast | Yes | User confirmed — venue, day, time |
| Auto-dismiss duration | 5 seconds | Matches existing toast behavior |
| After dismiss | Navigate to `/my-request-history-ui` manually | User confirmed |

---

## Technical Design

### Files to Modify

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | Replace current `showToast()` in `proceed()` with extended toast call |
| `public/js/ui-common.js` | Extend `showToast()` to accept optional `linkText` + `linkUrl` params |

### Current Flow (after Proceed confirmation)

```
User clicks "Proceed" → Modal shows slot list → User clicks "Confirm"
→ Toast: "Submitted" (2s auto-dismiss) → Page stays on timetable
```

### New Flow

```
User clicks "Proceed" → Modal shows slot list → User clicks "Confirm"
→ Toast: "✓ Submitted — Pending Approval · B103 · Mon, 4 Aug · 10:00-12:00 [View →]"
→ Toast auto-dismisses after 5s
→ User can navigate to /my-request-history-ui anytime
```

### Toast Extension (ui-common.js)

```javascript
// Current signature:
showToast(message, undoCallback, duration)

// New signature (backward compatible):
showToast(message, undoCallback, duration, linkText, linkUrl)
```

When `linkText` and `linkUrl` are provided, render a second button/link in the toast bar.

### Toast HTML (in ui-template.blade.php)

Add a link element inside `#toastBar`:

```html
<div id="toastBar" class="toast-bar">
    <span class="toast-message"></span>
    <a class="toast-link" href="#" style="display:none"></a>
    <button class="toast-undo" style="display:none">Undo</button>
</div>
```

---

## Mock Data

No new mock data needed. The submission is simulated — we show the slot details that the user just selected (from `selectedSlots[]` state).

---

## Cross-Module Data Flows

```
replacement-arrangement page
    ├── reads: selectedSlots[] (current selections)
    ├── reads: MockData.venueSlots (for display)
    ├── calls: showToast() from ui-common.js
    │       └── renders: #toastBar in ui-template.blade.php
    └── navigates: /my-request-history-ui (via toast link)

my-request-history page (not modified)
    └── displays: approval status (assumed to exist)
```

---

## Rejected Approaches

| Approach | Why Rejected |
|----------|--------------|
| Persistent status bar on arrangement page | Adds clutter; user chose ephemeral toast |
| Custom toast implementation | Already have `showToast()` in ui-common.js; DRY principle (Rule 7) |
| New modal for submission confirmation | Would add another step; current modal flow is sufficient |
| Real-time status updates | No backend/Livewire yet; out of scope for mock phase |

---

## Open Questions / Future Work

1. **Resubmit rejected requests** — Not in this scope, but the status toast establishes the pattern for future enhancement
2. **Backend integration** — When Livewire is added, the toast could show real approval status from the database
3. **Notification badge** — Could show count of pending/approved requests in the nav bar (separate feature)

---

## Acceptance Criteria

- [ ] After confirming submission, toast shows "✓ Submitted — Pending Approval"
- [ ] Toast includes slot details: venue code, day, date, time range
- [ ] Toast has "View →" link that navigates to `/my-request-history-ui`
- [ ] Toast auto-dismisses after 5 seconds
- [ ] Existing undo functionality still works (backward compatible)
- [ ] Mobile: toast displays correctly at 768px breakpoint
- [ ] No lint errors in modified files
