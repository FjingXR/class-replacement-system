# Proposal: Logout Confirmation Modal

## Why

The current logout button (`onclick="alert('Logout')"`) is a dummy placeholder with no confirmation. Users could accidentally click logout and lose their session. A confirmation modal with a 5-second countdown provides a safety net while keeping the flow fast for power users. The "Don't ask me again" option respects user preference for repeated actions.

## Scope

### In scope

1. **Logout form** — Replace both `onclick="alert('Logout')"` buttons with a real `<form method="POST" action="{{ route('logout') }}">` + `@csrf` + submit button. The form is the foundation; the modal enhances it. Fortify provides the `/logout` POST route (registered automatically by `FortifyServiceProvider`).
2. **Logout confirmation modal** — When a user clicks the logout button (desktop or mobile), show a modal with:
   - 5-second countdown timer
   - "Logout now" button (skip countdown, submit the logout form immediately)
   - "Cancel" button (close modal, stay on page)
   - "Don't ask me again" checkbox
   - At 0s countdown → auto-submit the logout form
   - Reuses existing `.modal-overlay` + `.modal` classes from `theme.css`
3. **localStorage preference** — "Don't ask me again" stores `logout_no_confirm = '1'` in `localStorage`. Next logout bypasses the modal and submits the form directly. Key is `logout_no_confirm`, blanket per-browser (not per-role). No in-app reset path during mock phase (permanent browser-level setting).
4. **Two logout buttons** — Desktop user panel (line 49) and mobile nav drawer (line 94) both trigger the same modal. `showLogoutModal()` must close the mobile nav drawer (if open) before displaying the modal, to avoid layered UI states.
5. **Coexistence with C4** — This is click-triggered (user explicitly clicks logout). C4 auto-logout is idle-triggered (25min inactivity). They don't conflict.

### Out of scope

- C4 auto-logout idle modal (stays in auth-wiring SDD)
- Server-side "don't ask me again" (localStorage only — browser-specific)
- Logout audit trail or analytics
- New migrations, models, or composer dependencies

## FR/NFR traceability

| Ref | Feature | This SDD |
|-----|---------|----------|
| FR 1.1 (implicit) | Logout must work | Logout form + route wired via Fortify |
| NFR 2.5 | CSRF on POST | Logout form uses POST + @csrf |
| NFR 3.3 | Simple English messages | Modal text uses clear, simple language |
| NFR 3.1 | Responsive interface | Modal works on desktop + mobile (nav drawer) |

## Impact scope

| File | Change type |
|------|------------|
| `resources/views/partials/ui-nav-bar.blade.php` | Modify — replace both `onclick="alert('Logout')"` with `onclick="showLogoutModal()"` + add hidden logout form |
| `resources/views/partials/ui-logout-modal.blade.php` | **NEW** — modal markup (reuse `.modal-overlay` + `.modal` from theme.css) |
| `public/js/logout-modal.js` | **NEW** — countdown timer, skip/undo, localStorage logic, close nav drawer |
| `public/css/theme.css` | Modify — add countdown timer + checkbox styles |
| `resources/views/layouts/ui-template.blade.php` | Modify — include `logout-modal.js` script tag |
