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

## Bug Fix: Login button stayed disabled with valid input

**Date:** 2026-08-12

**Cause:** `const idRegex = @json($idRegex);` in the layout produced a JSON **string** (e.g. `"^\\d+$"`), not a RegExp object. Calling `.test()` on a string threw `TypeError: idRegex.test is not a function`, which crashed `validateLogin()` and left the button disabled forever.

**Fix:** Wrap in a RegExp constructor: `const idRegex = new RegExp(@json($idRegex));`

**Verified:**
- `/login/staff` — entering `6767` + password enables the Log in button
- `/login/student` — entering `25RSD0001` + password enables the Log in button
- No console errors on either page

**Note:** Requires clearing view cache (or waiting for OPCache `revalidate_freq`) after editing. Run `rm -rf storage/framework/views/*.php` if changes don't appear.

## Fix: Staff ID Optional "P" Prefix (FR 2.1)

**Date:** 2026-08-21 | **FR:** FR 2.1 (Ch3 §3.4 — "four digits with an optional 'P' prefix, e.g., P5425 or 5425")

**File:** `resources/views/auth/login-staff.blade.php:10-12` — 3 tokens changed, no layout JS change (`layouts/login-template.blade.php:420` `new RegExp(@json($idRegex))` consumes directly)

| Token | Before | After |
|-------|--------|-------|
| `idPlaceholder` | `e.g. 6767` | `e.g. P5425 or 5425` |
| `idRegex` | `^\d+$` (any digit length, no P) | `^P?\d{4}$` (uppercase P optional + exactly 4 digits) |
| `formatHint` | `Numeric staff ID only` | `4 digits, optional "P" prefix` |

**Rationale:** Before, `P5425` → `idRegex.test()` false → button stayed `disabled` and never submitted, contradicting FR 2.1. Student regex `^\d{2}[A-Za-z]{3}\d{4}$` untouched.

**Verified:**
- View source: `idRegex` = `^P?\d{4}$`, placeholder `e.g. P5425 or 5425`, hint `4 digits, optional "P" prefix`
- `new RegExp("^P?\\d{4}$").test("P5425")==true`, `"5425"==true`, `"p5425"==false`, `"542"==false`, `"54255"==false`, `"P542"==false`, `"PP5425"==false`, `" 5425 ".trim()==true`
- `/login/staff`: `5425`+pw → enabled, `P5425`+pw → enabled, `p5425`/3-/5-digits → disabled, empty pw → disabled
- `/login/student` control: `25RSD0001` → enabled, `P5425` on student page → disabled (student regex unchanged)
- No console errors on `DOMContentLoaded`; `validateLogin()` called on `oninput` + `DOMContentLoaded` (`login-template.blade.php:382,396,436`)

**Backend note (deferred):** DB stores `5425` (`dataset/lecturers.md`). Frontend allow alone insufficient for `P5425` login — backend must strip `P` before `Lecturer::where('staff_id')` (see `staff-id-p-prefix-frontend-fix-plan.md:§7`). Not implemented in this frontend-only change.

**Cache:** `pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &` required per `AGENTS.md:31`.
