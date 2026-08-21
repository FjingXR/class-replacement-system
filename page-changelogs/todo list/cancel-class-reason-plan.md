# Cancel Class — Add Mandatory Reason Field Plan

## Goal
Update the Cancel Class confirmation modal on the My Timetable page to require a mandatory reason before cancelling, matching FR 2.16 in the FYP report (Ch3 §3.4). Also add a cancellation notification to affected students (FR 1.5).

---

## Why This Change

- FR 2.16 states: "Lecturers shall be able to cancel their own scheduled class by providing a mandatory reason."
- The current cancel modal (`MyTimetable-UI-design-template.blade.php`) only has "Are you sure?" → Yes/No — no reason input.
- The modal text says "A cancellation notice will be sent to all affected parties" but no notification is implemented.
- Report-vs-code mismatch flagged during §3.4 cross-check.

---

## Design Decisions

| Aspect | Decision |
|--------|----------|
| **Reason field** | Required `<textarea>` in the cancel confirmation modal |
| **Min length** | 10 characters (prevents empty or single-word reasons) |
| **Validation** | Disable "Yes, Cancel Class" button until reason meets min length |
| **Reason storage** | Passed to backend (future Sprint 3) — for now, captured in JS state |
| **Notification** | Email to affected students when class is cancelled (FR 1.5) |
| **Notification content** | Subject code, original day/time/venue, cancellation reason |
| **Notification delivery** | Database-backed queue (FR 4.15/4.16 pattern) |

---

## Files to Create/Modify

| # | File | Action |
|---|------|--------|
| 1 | `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php` | **Modify** — add reason textarea to cancel modal, update JS |
| 2 | `public/css/theme.css` | **Add** — `.cancel-reason-input` styles (if not reusing existing textarea styles) |
| 3 | `page-changelogs/my-timetable-changelog.md` | **Update** — record this change |

---

## Step 1: Add Reason Textarea to Cancel Modal

Current modal (lines 218-234):

```html
<div class="cancel-modal-body">
    <p>Are you sure you want to cancel this class?</p>
    <p class="section-heading-sub" style="margin-top:6px;">
        This action cannot be undone. A cancellation notice will be sent to all affected parties.
    </p>
</div>
```

Add after the existing body text:

```html
<div class="cancel-modal-body">
    <p>Are you sure you want to cancel this class?</p>
    <p class="section-heading-sub" style="margin-top:6px;">
        This action cannot be undone. A cancellation notice will be sent to all affected parties.
    </p>
    <label for="cancelReason" style="display:block; margin-top:16px; font-size:13px; font-weight:600; color:var(--color-on-surface-variant);">
        Reason for cancellation (required)
    </label>
    <textarea
        id="cancelReason"
        class="cancel-reason-input"
        placeholder="e.g. Public holiday, lecturer medical leave, venue unavailable..."
        rows="3"
        maxlength="500"
        oninput="validateCancelReason()"
    ></textarea>
    <div id="cancelReasonHint" class="format-hint" style="margin-top:4px;">
        Minimum 10 characters. <span id="cancelCharCount">0/500</span>
    </div>
</div>
```

---

## Step 2: Update JS — Validate Reason + Pass to Backend

```js
function validateCancelReason() {
    const reason = document.getElementById('cancelReason').value.trim();
    const btn = document.querySelector('.btn-cancel-danger');
    const hint = document.getElementById('cancelReasonHint');
    const count = document.getElementById('cancelCharCount');

    count.textContent = reason.length + '/500';

    if (reason.length >= 10) {
        btn.disabled = false;
        hint.style.color = 'var(--color-on-surface-variant)';
    } else {
        btn.disabled = true;
        hint.style.color = 'var(--color-error)';
    }
}

function closeCancelConfirm(confirmed) {
    document.getElementById('cancelConfirmOverlay').style.display = 'none';
    if (confirmed) {
        const reason = document.getElementById('cancelReason').value.trim();
        // TODO: Send reason to backend (Sprint 3)
        // POST /api/cancel-class { event_id, reason }
        closeModal();
        toast.show('Class cancelled. Reason: ' + reason.substring(0, 50) + (reason.length > 50 ? '...' : ''), null);
        // Reset textarea
        document.getElementById('cancelReason').value = '';
        validateCancelReason();
    }
}
```

Also update `cancelClass()` to reset the textarea and disable the button when opening:

```js
function cancelClass() {
    const overlay = document.getElementById('cancelConfirmOverlay');
    document.getElementById('cancelReason').value = '';
    validateCancelReason(); // disables button, resets counter
    overlay.style.display = 'flex';
    document.getElementById('cancelReason').focus();
}
```

---

## Step 3: CSS for Reason Textarea

Add to `theme.css` if existing textarea styles don't cover it:

```css
.cancel-reason-input {
    width: 100%;
    margin-top: 8px;
    padding: 10px 12px;
    border: 1px solid var(--color-outline);
    border-radius: var(--radius-md);
    background: var(--color-surface-variant);
    color: var(--color-on-surface);
    font-family: inherit;
    font-size: 14px;
    resize: vertical;
    min-height: 72px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.cancel-reason-input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(0, 77, 152, 0.1);
    outline: none;
}
.cancel-reason-input::placeholder {
    color: var(--color-on-surface-variant);
    opacity: 0.5;
}
```

---

## Step 4: Cancellation Notification (FR 1.5 / FR 4.15 pattern)

When a class is cancelled, the system should send an email notification to all students in the affected cohort(s):

- **Trigger:** Lecturer confirms cancellation with reason
- **Recipients:** All students in the affected cohort(s)
- **Content:** Subject code, original day/time/venue, cancellation reason, lecturer name
- **Delivery:** Database-backed queue (same pattern as FR 4.15/4.16)
- **Implementation:** Sprint 3 (backend + mail + queue worker)

For now (mock phase), the toast message confirms the action. The email implementation is deferred to Sprint 3.

---

## Step 5: Verification Checklist

- [ ] Cancel Class button appears for normal (non-conflicted, non-pending) classes
- [ ] Cancel modal shows reason textarea with placeholder
- [ ] "Yes, Cancel Class" button is disabled until reason ≥ 10 characters
- [ ] Character counter updates as user types (0/500 → 15/500)
- [ ] Submitting with valid reason → toast shows "Class cancelled. Reason: ..."
- [ ] Reason textarea resets when modal reopens
- [ ] Cancel button stays hidden for conflicted and pending classes (existing logic unchanged)
- [ ] Mobile: textarea is full-width, keyboard doesn't cover the button

---

## Order of Implementation

1. Add reason textarea HTML to cancel modal
2. Add `validateCancelReason()` JS function
3. Update `cancelClass()` to reset textarea + focus
4. Update `closeCancelConfirm()` to capture reason
5. Add `.cancel-reason-input` CSS to `theme.css`
6. Test: valid reason, too short reason, empty, max length
7. Update `my-timetable-changelog.md`

---

## Notes

- The "Yes, Cancel Class" button should start **disabled** (same pattern as login button in `login-template.blade.php`)
- The reason is captured in JS for now — backend storage + email notification deferred to Sprint 3
- The existing "No, Keep It" button is unaffected
- The cancel button visibility logic (`isConflict || status === 'pending' ? 'none' : 'flex'`) is unchanged
