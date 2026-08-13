# Tasks: Logout Confirmation Modal

> **Frontend note:** All placeholder UI must reuse existing patterns — CSS tokens from `theme.css`, JS helpers from `ui-common.js`, Blade patterns from existing partials. Match the look of existing UI elements. The frontend SDD later will polish further (responsive, animation, accessibility).

## Task 1: Logout form + nav bar wiring
- [x] 1.1 Read `ui-nav-bar.blade.php` — identify both logout buttons (desktop line 49, mobile line 94)
- [x] 1.2 Add hidden logout form at end of partial: `<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>`
- [x] 1.3 Desktop: Replace `onclick="alert('Logout')"` on `.logout-btn` with `onclick="showLogoutModal()"`
- [x] 1.4 Mobile: Replace `onclick="alert('Logout')"` on `.nav-drawer-logout` with `onclick="showLogoutModal()"`

## Task 2: Modal markup
- [x] 2.1 Create `resources/views/partials/ui-logout-modal.blade.php`
- [x] 2.2 Add modal overlay div with `id="logoutModal"`, `role="alertdialog"`, `aria-modal="true"`, `aria-labelledby="logoutModalTitle"` — uses existing `.modal-overlay` class (no inline display style)
- [x] 2.3 Add modal content: heading "Confirm Logout", countdown text with `<span id="logoutCountdown">5</span>`, countdown bar div
- [x] 2.4 Add `.modal-footer` with Cancel button (`.btn-outline`, `id="logoutCancel"`) and Logout now button (`.btn-danger`, `id="logoutNow"`)
- [x] 2.5 Add "Don't ask me again" checkbox with `id="logoutDontAsk"`, class `.logout-dont-ask`

## Task 3: Modal styles (in partial)
- [x] 3.1 Add `<style>` block inside `ui-logout-modal.blade.php`
- [x] 3.2 Add `.logout-countdown-bar` — full width, 6px height, `var(--color-surface-variant)` background, 3px radius, overflow hidden, 0.75rem margin
- [x] 3.3 Add `.logout-countdown-fill` — full height, `var(--color-primary)` background, 3px radius, 1s linear transition
- [x] 3.4 Add `.logout-dont-ask` — flex, align center, 0.5rem gap, 0.75rem margin-top, 0.875rem font-size, `var(--color-on-surface-variant)` color, pointer cursor

## Task 4: Modal JS
- [x] 4.1 Create `public/js/logout-modal.js`
- [x] 4.2 Add `showLogoutModal()` function — check localStorage `logout_no_confirm` first; if '1', submit form directly
- [x] 4.3 Close mobile nav drawer if open: remove `.open` from `#navDrawer` and `#navDrawerOverlay`, reset `document.body.style.overflow = ''`
- [x] 4.4 Show modal: `modal.classList.add('show')`, start 5s countdown interval
- [x] 4.5 Add countdown UI update: numeric text + fill bar width percentage
- [x] 4.6 At 0s: clear interval, submit `#logout-form`
- [x] 4.7 "Logout now" button: clear interval, submit form
- [x] 4.8 "Cancel" button: clear interval, `modal.classList.remove('show')`
- [x] 4.9 "Don't ask me again" checkbox: on change, set/remove `localStorage.logout_no_confirm`

## Task 5: Layout inclusion
- [x] 5.1 In `resources/views/layouts/ui-template.blade.php`, add `@include('partials.ui-logout-modal')` inside the existing `@if(!isset($hideNav) || !$hideNav)` block, after the nav bar include, before `@yield('content')`
- [x] 5.2 Add `<script src="/js/logout-modal.js"></script>` after the existing `<script src="/js/mock-data.js">` tag (after line 34), ensuring DOM is ready before handler binding

## Task 6: Lint + verify
- [x] 6.1 Run `composer run lint:check`
- [x] 6.2 Run `composer run types:check`
- [x] 6.3 Confirm no new failures
