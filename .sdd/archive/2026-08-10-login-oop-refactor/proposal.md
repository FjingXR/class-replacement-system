# Proposal: Login Pages OOP Refactor

## Why This Change Is Needed

Both `login-staff.blade.php` and `login-student.blade.php` are standalone HTML files (442 lines each) that don't use Blade layout inheritance (`@extends`). They are **96% identical** (only 19 lines differ out of 442). This creates a DRY violation — ~350 lines of duplicated CSS + ~30 lines of duplicated JS are copy-pasted between the two files.

Any login styling change (e.g., updating the glassmorphism card, input fields, or button styles) requires editing both files identically. This is error-prone and violates the project's OOP architecture established by the `oop-blade-refactor` SDD.

## Scope

### In Scope

1. **Create `layouts/login-template.blade.php`** — shared login layout with:
   - HTML shell (`<!DOCTYPE html>`, `<head>`, `<body>`)
   - Pre-paint theme IIFE
   - `ui-common.js` and `theme.css` includes
   - All shared login CSS (~350 lines, parameterized via Blade variables)
   - Background layer (uses `$bgImage`)
   - `@yield('content')` for page-specific form
   - Shared login JS (`validateLogin()`)
   - `@yield('page-scripts')` for page-specific JS

2. **Refactor `auth/login-staff.blade.php`** to `@extends('layouts.login-template')`:
   - Pass 12 parameters (title, bgImage, btnColor, regex, etc.)
   - Keep only the form HTML in `@section('content')`
   - Keep only staff-specific CSS overrides in `@section('page-styles')` (if any)
   - Keep only staff-specific JS in `@section('page-scripts')` (if any)

3. **Refactor `auth/login-student.blade.php`** to `@extends('layouts.login-template')`:
   - Pass 12 parameters (title, bgImage, btnColor, regex, etc.)
   - Keep only the form HTML in `@section('content')`
   - Keep only student-specific CSS overrides in `@section('page-styles')` (if any)
   - Keep only student-specific JS in `@section('page-scripts')` (if any)

### Out of Scope

- Login backend logic (auth-wiring SDD)
- Login UX enhancements (auth-wiring-enhancements SDD)
- New login features
- Mobile responsive design for login pages

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/layouts/login-template.blade.php` | **Create** — shared login layout |
| `resources/views/auth/login-staff.blade.php` | **Modify** — refactor to `@extends` |
| `resources/views/auth/login-student.blade.php` | **Modify** — refactor to `@extends` |

No route changes needed — `/login/staff` and `/login/student` already point to the correct views.

## FR Traceability

This change is a refactoring (OOP/DRY improvement) and does not implement new FRs. It preserves existing login behavior:

- Login page rendering (existing)
- Theme toggle (existing)
- Role switch links (existing)
- ID validation (existing)
- Form submission (existing)

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
