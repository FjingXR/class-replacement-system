# Review Log: logout-modal

## proposal.md Round 1 — 2026-08-03

### 🔴 Fixed
- Added logout form to scope (feature #1) — form is now part of this change, not a prerequisite
- Confirmed Fortify provides the `/logout` POST route
- Fixed NFR traceability: FR 1.1 (implicit: logout must work) instead of misleading "Login" trace
- Added z-index strategy: `showLogoutModal()` closes mobile nav drawer before showing modal

### 🟡 Addressed
- Added note: "Don't ask me again" is permanent browser-level setting, no in-app reset in mock phase
- Added CSS scope: reuse `.modal-overlay` + `.modal` from theme.css, add countdown/checkbox styles
- Added post-logout behavior: Fortify redirects to login page (default)

### 🔴 Outstanding
- (none)

## proposal.md Round 2 — 2026-08-03

### 🔴 Fixed
- (none)

### 🟡 Addressed
- Post-logout redirect clarified: Fortify redirects to `/login` by default

### 🔴 Outstanding
- (none) — **proposal.md FROZEN**

## design.md Round 1 — 2026-08-03

### 🔴 Fixed
- Replaced `--color-muted` with `--color-surface-variant` (existing token)
- Replaced `.btn-primary`/`.btn-secondary` with `.btn-outline`/`.btn-danger` (existing classes)
- Changed `.modal-actions` to `.modal-footer` (existing class with flex layout)
- Changed overlay class removal from `'active'` to `'open'` (matching ui-common.js)
- Added `document.body.style.overflow = ''` to prevent scroll-lock leak
- Removed false `.remember-me` claim, added `.logout-dont-ask` style rule

### 🟡 Addressed
- Changed `role="dialog"` to `role="alertdialog"` for time-sensitive modal
- Clarified form placement: "at the end of the partial, after the `</div>` closing `.nav-drawer`"

### 🔴 Outstanding
- (none)
