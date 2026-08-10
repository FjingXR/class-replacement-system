# Explore Brief: Login Pages OOP Refactor

## Problem Statement

Both `login-staff.blade.php` and `login-student.blade.php` are **standalone HTML files** (442 lines each) that don't use Blade layout inheritance (`@extends`). They are **96% identical** (only 19 lines differ out of 442). This creates a DRY violation — ~350 lines of duplicated CSS + ~30 lines of duplicated JS.

## Root Cause

The login pages were created **before** the OOP refactor (`oop-blade-refactor` SDD). They predate the `layouts/ui-template.blade.php` pattern used by all UI pages.

## Solution: Shared Login Layout

Create `layouts/login-template.blade.php` as the shared base layout for both login pages. Both pages will `@extends('layouts.login-template')` and only provide their unique content via `@section`.

## Differences to Parameterize (12 Blade variables)

| Variable | Staff Value | Student Value | CSS/HTML Usage |
|----------|-------------|---------------|----------------|
| `$title` | "Staff" | "Student" | `<title>` tag, page title |
| `$bgImage` | `/images/staff-login-bg.jpg` | `/images/student-login-bg.jpg` | `.bg-layer` background |
| `$btnColorToken` | `--color-secondary` | `--color-primary` | `.login-btn` background |
| `$btnColorOnToken` | `--color-on-secondary` | `--color-on-primary` | `.login-btn` color |
| `$btnHoverDark` | `#88dbb3` | `#154d94` | `.login-btn:hover` dark mode |
| `$btnHoverLight` | `#2db876` | `#154d94` | `.login-btn:hover` light mode |
| `$spinnerColorToken` | `--color-on-secondary` | `--color-on-primary` | `.spinner` border-top-color |
| `$idLabel` | "Staff ID" | "Student ID" | Label text |
| `$idPlaceholder` | "e.g. 6767" | "e.g. 25RSD0001" | Input placeholder |
| `$idRegex` | `^\d+$` | `^\d{2}[A-Za-z]{3}\d{4}$` | JS validation |
| `$formatHint` | "Numeric staff ID only" | "Format: 2 digits + 3 letters + 4 digits" | Hint text |
| `$loginType` | "staff" | "student" | Hidden input value |
| `$roleSwitchHtml` | `Student? <a href="...">Student Login</a>` | `Staff? <a href="...">Staff Login</a>` | Role switch link |

## Shared Layout Structure

```
layouts/login-template.blade.php  ← NEW (shared HTML shell, CSS, JS)
  ├── auth/login-staff.blade.php  ← @extends (only form + unique CSS)
  └── auth/login-student.blade.php ← @extends (only form + unique CSS)
```

## Shared CSS (~350 lines, parameterized)

All CSS moves to `layouts/login-template.blade.php`'s `@section('page-styles')`:
- Body base styles
- Background layer (uses `$bgImage`)
- Login card (glassmorphism)
- Input styling
- Button styling (uses `$btnColorToken`, `$btnColorOnToken`, `$btnHoverDark`, `$btnHoverLight`)
- Spinner (uses `$spinnerColorToken`)
- Format hint
- Role switch link
- Responsive breakpoints
- Dark/light mode overrides

## Shared JS (~30 lines, parameterized)

Single `validateLogin(idRegex)` function replaces `validateStaff()` and `validateStudent()`:
```javascript
function validateLogin() {
    const idRegex = new RegExp('{{ $idRegex }}');
    // ... validation logic using idRegex
}
```

## Rejected Approaches

| Approach | Why Rejected |
|----------|--------------|
| Extract CSS to `theme.css` | Login CSS (glassmorphism, background images) is very different from UI page CSS — would pollute theme.css |
| Extract CSS to new `login.css` | Extra HTTP request, login CSS is small enough to keep in layout |
| Keep as-is | DRY violation persists, any login change requires editing both files |
| Partial parameterization (keep button CSS in each page) | ~20 lines of CSS still duplicated, defeats the purpose |

## Acceptance Criteria

1. Both login pages render identically to current behavior
2. No duplicated CSS/JS between the two pages
3. Shared login layout exists at `layouts/login-template.blade.php`
4. Both pages use `@extends('layouts.login-template')`
5. Route `/login/staff` and `/login/student` still work
6. No console errors
7. Theme toggle works on both pages
8. Role switch links work ("Staff? Staff Login" ↔ "Student? Student Login")
9. Validation works (staff: numeric only, student: 25RSD0001 pattern)
10. All CSS uses `var()` tokens from `theme.css` (no hardcoded hex except hover states)

## Scope

**In scope:**
- Create `layouts/login-template.blade.php`
- Refactor `login-staff.blade.php` to `@extends`
- Refactor `login-student.blade.php` to `@extends`

**Out of scope:**
- Login backend logic (auth-wiring SDD)
- Login UX enhancements (auth-wiring-enhancements SDD)
- New login features
- Mobile responsive design for login pages

## Files Changed

| File | Action |
|------|--------|
| `resources/views/layouts/login-template.blade.php` | **Create** — shared login layout |
| `resources/views/auth/login-staff.blade.php` | **Modify** — refactor to `@extends` |
| `resources/views/auth/login-student.blade.php` | **Modify** — refactor to `@extends` |
