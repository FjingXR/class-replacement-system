# Run: Logout Modal

> Prompt to implement the `logout-modal` SDD change.

## What

Implement the logout confirmation modal for the TARUMT Class Replacement System.

**SDD:** `logout-modal` (3 artifacts frozen: proposal.md, design.md, tasks.md)
**Location:** `.sdd/changes/logout-modal/`

## Before you start

1. Read `.sdd/changes/logout-modal/proposal.md` — what and why
2. Read `.sdd/changes/logout-modal/design.md` — how (technical approach)
3. Read `.sdd/changes/logout-modal/tasks.md` — step-by-step implementation tasks
4. Read `CodingMAIN.md` — project conventions, especially §10 UI Design Rules

## Tasks (27 subtasks)

### Task 1: Logout form + nav bar wiring
- [ ] 1.1 Read `ui-nav-bar.blade.php` — identify both logout buttons (desktop line 49, mobile line 94)
- [ ] 1.2 Add hidden logout form at end of partial: `<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>`
- [ ] 1.3 Desktop: Replace `onclick="alert('Logout')"` on `.logout-btn` with `onclick="showLogoutModal()"`
- [ ] 1.4 Mobile: Replace `onclick="alert('Logout')"` on `.nav-drawer-logout` with `onclick="showLogoutModal()"`

### Task 2: Modal markup
- [ ] 2.1 Create `resources/views/partials/ui-logout-modal.blade.php`
- [ ] 2.2 Add modal overlay div with `id="logoutModal"`, `role="alertdialog"`, `aria-modal="true"`, `aria-labelledby="logoutModalTitle"` — uses existing `.modal-overlay` class (no inline display style)
- [ ] 2.3 Add modal content: heading "Confirm Logout", countdown text with `<span id="logoutCountdown">5</span>`, countdown bar div
- [ ] 2.4 Add `.modal-footer` with Cancel button (`.btn-outline`, `id="logoutCancel"`) and Logout now button (`.btn-danger`, `id="logoutNow"`)
- [ ] 2.5 Add "Don't ask me again" checkbox with `id="logoutDontAsk"`, class `.logout-dont-ask`

### Task 3: Modal styles (in partial)
- [ ] 3.1 Add `<style>` block inside `ui-logout-modal.blade.php`
- [ ] 3.2 Add `.logout-countdown-bar` — full width, 6px height, `var(--color-surface-variant)` background, 3px radius, overflow hidden, 0.75rem margin
- [ ] 3.3 Add `.logout-countdown-fill` — full height, `var(--color-primary)` background, 3px radius, 1s linear transition
- [ ] 3.4 Add `.logout-dont-ask` — flex, align center, 0.5rem gap, 0.75rem margin-top, 0.875rem font-size, `var(--color-on-surface-variant)` color, pointer cursor

### Task 4: Modal JS
- [ ] 4.1 Create `public/js/logout-modal.js`
- [ ] 4.2 Add `showLogoutModal()` function — check localStorage `logout_no_confirm` first; if '1', submit form directly
- [ ] 4.3 Close mobile nav drawer if open: remove `.open` from `#navDrawer` and `#navDrawerOverlay`, reset `document.body.style.overflow = ''`
- [ ] 4.4 Show modal: `modal.classList.add('show')`, start 5s countdown interval
- [ ] 4.5 Add countdown UI update: numeric text + fill bar width percentage
- [ ] 4.6 At 0s: clear interval, submit `#logout-form`
- [ ] 4.7 "Logout now" button: clear interval, submit form
- [ ] 4.8 "Cancel" button: clear interval, `modal.classList.remove('show')`
- [ ] 4.9 "Don't ask me again" checkbox: on change, set/remove `localStorage.logout_no_confirm`

### Task 5: Layout inclusion
- [ ] 5.1 In `resources/views/layouts/ui-template.blade.php`, add `@include('partials.ui-logout-modal')` inside the existing `@if(!isset($hideNav) || !$hideNav)` block, after the nav bar include, before `@yield('content')`
- [ ] 5.2 Add `<script src="/js/logout-modal.js"></script>` after the existing `<script src="/js/mock-data.js">` tag (after line 34), ensuring DOM is ready before handler binding

### Task 6: Lint + verify
- [ ] 6.1 Run `composer run lint:check`
- [ ] 6.2 Run `composer run types:check`
- [ ] 6.3 Confirm no new failures

## Verification

After implementation:
1. Click logout button (desktop) → modal appears with 5s countdown
2. Click "Cancel" → modal closes, stay on page
3. Click "Logout now" → immediate logout
4. Wait 5s → auto-logout
5. Check "Don't ask me again" → next logout bypasses modal
6. Test mobile nav drawer → same behavior
7. Run `composer run lint:check` → no new failures
