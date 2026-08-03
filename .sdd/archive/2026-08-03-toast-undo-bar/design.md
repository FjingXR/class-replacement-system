# Design: Toast/Undo Bar for Critical Actions

## Technical Approach

Add a shared toast/undo bar component to the existing shared files (`theme.css`, `ui-common.js`, `layouts/ui-template.blade.php`) and refactor 6 critical actions across 3 UI templates to use the new `showToast()` helper.

### 1. Toast Component — CSS (`public/css/theme.css`)

Add at end of file (after line 1604):

```css
/* ── Toast/Undo Bar ─────────────────────────────────────────── */
.toast-bar {
    display: none;
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 200;
    background: var(--color-surface);
    border: 1px solid var(--color-outline);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    padding: 10px 16px;
    align-items: center;
    gap: 12px;
    animation: toastSlideUp 0.3s ease;
}
.toast-bar.visible { display: flex; }

.toast-bar .toast-message {
    font-size: 13px;
    font-weight: 500;
    color: var(--color-on-surface);
}

.toast-bar .toast-undo {
    padding: 4px 10px;
    border-radius: 4px;
    border: 1px solid var(--color-primary);
    background: transparent;
    color: var(--color-primary);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.toast-bar .toast-undo:hover {
    background: var(--color-primary);
    color: var(--color-on-primary);
}

.toast-bar .toast-close {
    padding: 4px 6px;
    border: none;
    background: transparent;
    color: var(--color-on-surface-variant);
    font-size: 14px;
    cursor: pointer;
    line-height: 1;
}
.toast-bar .toast-close:hover {
    color: var(--color-on-surface);
}

@keyframes toastSlideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
```

### 2. Toast Component — JS (`public/js/ui-common.js`)

Add at end of file (after line 320):

```javascript
// ───── Toast/Undo Bar ─────

let _toastTimer = null;

/**
 * Show a toast/undo bar at bottom-left.
 * @param {string} message - Success message to display
 * @param {function|null} undoCallback - Function to call when Undo is clicked (null = no Undo button)
 * @param {number} duration - Auto-dismiss time in ms (default 5000)
 */
function showToast(message, undoCallback, duration = 5000) {
    const bar = document.getElementById('toastBar');
    if (!bar) return;

    const msgEl = bar.querySelector('.toast-message');
    const undoBtn = bar.querySelector('.toast-undo');

    msgEl.textContent = message;

    if (undoCallback) {
        undoBtn.style.display = 'inline-block';
        undoBtn.onclick = function () {
            undoCallback();
            dismissToast();
        };
    } else {
        undoBtn.style.display = 'none';
    }

    bar.classList.add('visible');

    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(dismissToast, duration);
}

function dismissToast() {
    const bar = document.getElementById('toastBar');
    if (bar) bar.classList.remove('visible');
    clearTimeout(_toastTimer);
}
```

### 3. Toast Component — HTML (`resources/views/layouts/ui-template.blade.php`)

Add before `</body>` (after the `<script>` block, before line 42):

```html
<!-- Toast/Undo Bar (shared) -->
<div class="toast-bar" id="toastBar">
    <span class="toast-message"></span>
    <button class="toast-undo" style="display:none">Undo</button>
    <button class="toast-close" onclick="dismissToast()">✕</button>
</div>
```

### 4. Critical Actions — Refactor to Use `showToast()`

#### 4.1 my-request-history: Single Cancel (`quickCancel`)

**Before (line 1163-1172):**
```javascript
document.getElementById('confirmCancelAction').addEventListener('click', function() {
    if (pendingCancelId !== null) {
        var idx = mockRequests.findIndex(function(r) { return r.id === pendingCancelId; });
        if (idx !== -1) mockRequests.splice(idx, 1);
        selectedIds.delete(pendingCancelId);
        pendingCancelId = null;
        closeCancelConfirm();
        closeModal();
        renderTable();
    }
});
```

**After:**
```javascript
document.getElementById('confirmCancelAction').addEventListener('click', function() {
    if (pendingCancelId !== null) {
        var idx = mockRequests.findIndex(function(r) { return r.id === pendingCancelId; });
        if (idx !== -1) {
            var removed = mockRequests.splice(idx, 1)[0];
            selectedIds.delete(pendingCancelId);
            pendingCancelId = null;
            closeCancelConfirm();
            closeModal();
            renderTable();
            showToast('Request #' + removed.id + ' cancelled.', function() {
                mockRequests.splice(idx, 0, removed);
                renderTable();
            });
        }
    }
});
```

#### 4.2 my-request-history: Batch Cancel (`batchCancelSelected`)

**Before (line 1203-1208):**
```javascript
document.getElementById('confirmBatchCancelAction').addEventListener('click', function() {
    mockRequests = mockRequests.filter(function(r) { return !selectedIds.has(r.id); });
    selectedIds.clear();
    closeBatchCancelConfirm();
    renderTable();
});
```

**After:**
```javascript
document.getElementById('confirmBatchCancelAction').addEventListener('click', function() {
    var removed = mockRequests.filter(function(r) { return selectedIds.has(r.id); });
    mockRequests = mockRequests.filter(function(r) { return !selectedIds.has(r.id); });
    var count = removed.length;
    selectedIds.clear();
    closeBatchCancelConfirm();
    renderTable();
    showToast(count + ' request' + (count !== 1 ? 's' : '') + ' cancelled.', function() {
        mockRequests.push.apply(mockRequests, removed);
        renderTable();
    });
});
```

#### 4.3 MyTimetable: Cancel Class (`cancelClass`)

**Before (line 415-420):**
```javascript
function closeCancelConfirm(confirmed) {
    document.getElementById('cancelConfirmOverlay').style.display = 'none';
    if (confirmed) {
        closeModal();
    }
}
```

**After:**
```javascript
function closeCancelConfirm(confirmed) {
    document.getElementById('cancelConfirmOverlay').style.display = 'none';
    if (confirmed) {
        closeModal();
        showToast('Class cancelled.', null);
    }
}
```

Note: MyTimetable's cancelClass() currently only closes the modal without removing the event from the data. The toast is added for UX consistency, but `null` is passed for undoCallback since no data was actually modified — there is nothing to undo. **Backend phase:** `cancelClass()` will call API to mark class as cancelled; undo callback will call API to restore the class.

#### 4.4 replacement-arrangement: Submit Request (`proceed`)

**Before (line 1325-1328):**
```javascript
function() {
    hideConfirmModal();
    showConfirmModal('Submitted', 'Your replacement request has been submitted for approval.', null);
}
```

**After:**
```javascript
function() {
    hideConfirmModal();
    showConfirmModal('Submitted', 'Your replacement request has been submitted for approval.', null);
    showToast('Replacement request submitted.', null);
}
```

Note: No undo — submission is final.

#### 4.5 replacement-arrangement: Clear ALL (`clearAll`)

**Before (line 1336-1346):**
```javascript
function() {
    hideConfirmModal();
    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
    selectedCells.forEach(c => {
        c.el.classList.remove('cell-selected');
        c.el.classList.add('cell-available');
        c.el.innerHTML = '';
    });
    selectedCells = [];
    updateCounter();
}
```

**After:**
```javascript
function() {
    hideConfirmModal();
    var savedCells = selectedCells.slice();
    var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
    selectedCells.forEach(c => {
        c.el.classList.remove('cell-selected');
        c.el.classList.add('cell-available');
        c.el.innerHTML = '';
    });
    selectedCells = [];
    updateCounter();
    showToast('All selections cleared.', function() {
        Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
        savedCells.forEach(c => {
            c.el.classList.remove('cell-available');
            c.el.classList.add('cell-selected');
            c.el.innerHTML = '✓';
        });
        selectedCells = savedCells;
        updateCounter();
    });
}
```

#### 4.6 replacement-arrangement: Navigate Away (`navigateTo`)

**Before (line 1354-1356):**
```javascript
function() { hideConfirmModal(); window.location.href = url; }
```

**After:**
```javascript
function() {
    var savedCells = selectedCells.slice();
    var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
    hideConfirmModal();
    selectedCells = [];
    Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
    showToast('Selections cleared.', function() {
        Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
        selectedCells = savedCells;
    });
    window.location.href = url;
}
```

Note: The undo restores state in memory, but the navigation has already occurred. **Backend phase:** `navigateTo()` will delay navigation 5s (match `goBack()` pattern); undo callback will call API to restore server-side selections.

#### 4.7 replacement-arrangement: Go Back (`goBack`)

**Before (line 1398-1408):**
```javascript
function goBack() {
    if (selectedCells.length > 0) {
        showConfirmModal(
            'Unsaved Changes',
            'You have selected time slots that will be lost if you leave this page. Are you sure you want to go back?',
            function() { hideConfirmModal(); window.location.href = '/replacement-home-ui'; }
        );
    } else {
        window.location.href = '/replacement-home-ui';
    }
}
```

**After:**
```javascript
function goBack() {
    if (selectedCells.length > 0) {
        showConfirmModal(
            'Unsaved Changes',
            'You have selected time slots that will be lost if you leave this page. Are you sure you want to go back?',
            function() {
                hideConfirmModal();
                var savedCells = selectedCells.slice();
                var savedSlots = JSON.parse(JSON.stringify(selectedSlotsByVenue));
                Object.keys(selectedSlotsByVenue).forEach(k => { selectedSlotsByVenue[k] = {}; });
                selectedCells.forEach(c => {
                    c.el.classList.remove('cell-selected');
                    c.el.classList.add('cell-available');
                    c.el.innerHTML = timeLabelHtml(c.hour);
                });
                selectedCells = [];
                updateCounter();
                var navTimer = setTimeout(function() { window.location.href = '/replacement-home-ui'; }, 5000);
                showToast('Selections cleared.', function() {
                    clearTimeout(navTimer);
                    Object.keys(savedSlots).forEach(k => { selectedSlotsByVenue[k] = savedSlots[k]; });
                    savedCells.forEach(c => {
                        c.el.classList.remove('cell-available');
                        c.el.classList.add('cell-selected');
                        c.el.innerHTML = '<span class="sel-text"></span>' + timeLabelHtml(c.hour);
                    });
                    selectedCells = savedCells;
                    updateCounter();
                });
            }
        );
    } else {
        window.location.href = '/replacement-home-ui';
    }
}
```

Note: Unlike `navigateTo()`, `goBack()` delays navigation by 5 seconds via `setTimeout`, giving the user time to click Undo. If Undo is clicked, the timer is cleared and selections are restored. If the timer expires, navigation proceeds. This is the correct pattern for undoable navigate-away actions.

## File Changes

| File | Change |
|------|--------|
| `public/css/theme.css` | +45 lines (toast CSS) |
| `public/js/ui-common.js` | +35 lines (showToast + dismissToast) |
| `resources/views/layouts/ui-template.blade.php` | +5 lines (toast HTML) |
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | Edit 2 cancel handlers to save removed items + call showToast() |
| `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php` | Edit closeCancelConfirm() to call showToast() |
| `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | Edit 4 action handlers to save state + call showToast() (proceed, clearAll, navigateTo, goBack) |
| `page-changelogs/my-request-history-changelog.md` | Add toast entries |
| `page-changelogs/replacement-arrangement-changelog.md` | Add toast entries |
