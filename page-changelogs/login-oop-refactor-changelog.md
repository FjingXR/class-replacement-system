# Login Pages OOP Refactor Changelog

## Date: 2026-08-09
## Type: refactor

## Summary
Refactored login pages (staff and student) to use shared layout via OOP inheritance pattern.

## Changes Made

### Created
- `resources/views/layouts/login-template.blade.php` — Shared login layout (~280 lines)
  - HTML shell, pre-paint theme IIFE, ui-common.js + theme.css includes
  - All shared CSS (~280 lines) with parameterized colors via CSS custom properties
  - Shared JS with `validateLogin()` function using `$idRegex` parameter
  - 12 Blade parameters for full customization
  - Background layer, theme toggle, login card, form, role switch link

### Modified
- `resources/views/auth/login-staff.blade.php` — Refactored from 442 lines to 21 lines
  - Now extends `layouts.login-template` with staff-specific parameters
  - Removed all duplicated CSS and JS
  - Removed `validateStaff()` function (now `validateLogin()` in layout)

- `resources/views/auth/login-student.blade.php` — Refactored from 442 lines to 21 lines
  - Now extends `layouts.login-template` with student-specific parameters
  - Removed all duplicated CSS and JS
  - Removed `validateStudent()` function (now `validateLogin()` in layout)

## Key Design Decisions

1. **12 Blade Parameters:** title, bgImage, btnColorToken, btnColorOnToken, btnHoverDark, btnHoverLight, spinnerColorToken, idLabel, idPlaceholder, idRegex, formatHint, loginType, roleSwitchHtml

2. **CSS Custom Properties:** Parameterized button colors and spinner color via CSS custom properties set in `:root`

3. **Single Validation Function:** `validateLogin()` uses `$idRegex` parameter for ID validation

4. **Button Disabled Style:** Normalized to student style (`background: var(--color-outline); opacity: 0.4`) for both pages

5. **Form Action:** Kept as-is for mock phase, ready for future backend wiring

## Verification

### Staff Login (`/login/staff`)
- [x] No Blade compile errors
- [x] Page renders identically to previous behavior
- [x] Background image loads (`staff-login-bg.jpg`)
- [x] Green button color (secondary)
- [x] ID validation: non-numeric ID → button stays disabled
- [x] ID validation: numeric ID (e.g., "6767") + password → button enables
- [x] Role switch link navigates to `/login/student`
- [x] Theme toggle works

### Student Login (`/login/student`)
- [x] No Blade compile errors
- [x] Page renders identically to previous behavior
- [x] Background image loads (`student-login-bg.jpg`)
- [x] Blue button color (primary)
- [x] ID validation: invalid format (e.g., "12345") → button stays disabled
- [x] ID validation: valid format (e.g., "25RSD0001") + password → button enables
- [x] Role switch link navigates to `/login/staff`
- [x] Theme toggle works

## Line Count Reduction
- **Before:** 884 lines total (442 + 442)
- **After:** 322 lines total (280 + 21 + 21)
- **Reduction:** 562 lines (63.6% reduction)

## Files Changed
1. `resources/views/layouts/login-template.blade.php` — Created
2. `resources/views/auth/login-staff.blade.php` — Modified (442 → 21 lines)
3. `resources/views/auth/login-student.blade.php` — Modified (442 → 21 lines)
