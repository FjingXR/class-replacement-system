# Tasks: Login Pages OOP Refactor

## Grill Session Completed

**Decisions:**
- Button disabled style normalized to student style (`background: var(--color-outline); opacity: 0.4`) for both pages
- Form action `/login` kept as-is for mock phase, ready for future backend wiring
- `togglePassword()`, `ripple()`, `updateIcon()` confirmed as shared functions in `ui-common.js` — no changes needed

## Task 1: Create shared login layout

- [x] Create `resources/views/layouts/login-template.blade.php`
- [x] Add HTML shell (`<!DOCTYPE html>`, `<head>`, `<body>`)
- [x] Add pre-paint theme IIFE
- [x] Add `ui-common.js` and `theme.css` includes
- [x] Add `@yield('page-styles')` section
- [x] Add background layer with `$bgImage` parameter
- [x] Add `@yield('content')` section
- [x] Add shared JS with `validateLogin()` function using `$idRegex`
- [x] Add `@yield('page-scripts')` section

## Task 2: Move shared CSS to layout

- [x] Copy all CSS from `login-staff.blade.php` to `layouts/login-template.blade.php`
- [x] Parameterize button colors: replace hardcoded `var(--color-secondary)` with `var(--login-btn-bg)`
- [x] Parameterize button hover: replace hardcoded `#88dbb3` with `var(--login-btn-hover-dark)`
- [x] Parameterize spinner color: replace hardcoded `var(--color-on-secondary)` with `var(--login-spinner-color)`
- [x] Add CSS custom properties in `:root` using Blade variables
- [x] Verify CSS syntax is valid

## Task 3: Refactor login-staff.blade.php

- [x] Replace entire file with `@extends('layouts.login-template', [...])` structure
- [x] Pass all 12 parameters with staff-specific values
- [x] Move form HTML to `@section('content')`
- [x] Remove all duplicated CSS (now in layout)
- [x] Remove all duplicated JS (now in layout)
- [x] Remove `validateStaff()` function (now `validateLogin()` in layout)
- [x] Update `oninput="validateStaff()"` to `oninput="validateLogin()"`
- [x] Verify file is ~80 lines (down from 442)

## Task 4: Refactor login-student.blade.php

- [x] Replace entire file with `@extends('layouts.login-template', [...])` structure
- [x] Pass all 12 parameters with student-specific values
- [x] Move form HTML to `@section('content')`
- [x] Remove all duplicated CSS (now in layout)
- [x] Remove all duplicated JS (now in layout)
- [x] Remove `validateStudent()` function (now `validateLogin()` in layout)
- [x] Update `oninput="validateStudent()"` to `oninput="validateLogin()"`
- [x] Verify file is ~80 lines (down from 442)

## Task 5: Verify staff login

- [x] Visit `http://localhost:8000/login/staff` — verify no Blade compile errors
- [x] Verify page renders identically to previous behavior
- [x] Verify background image loads (`staff-login-bg.jpg`)
- [x] Verify green button color (secondary)
- [x] Test ID validation: enter non-numeric ID → button stays disabled
- [x] Test ID validation: enter numeric ID (e.g., "6767") + password → button enables
- [x] Click "Log in" → form submits
- [x] Click "Student? Student Login" → navigates to `/login/student`
- [x] Toggle theme → dark/light mode works
- [x] Verify no console errors

## Task 6: Verify student login

- [x] Visit `http://localhost:8000/login/student` — verify no Blade compile errors
- [x] Verify page renders identically to previous behavior
- [x] Verify background image loads (`student-login-bg.jpg`)
- [x] Verify blue button color (primary)
- [x] Test ID validation: enter invalid format (e.g., "12345") → button stays disabled
- [x] Test ID validation: enter valid format (e.g., "25RSD0001") + password → button enables
- [x] Click "Log in" → form submits
- [x] Click "Staff? Staff Login" → navigates to `/login/staff`
- [x] Toggle theme → dark/light mode works
- [x] Verify no console errors

## Task 7: Verify no regression

- [x] Visit `http://localhost:8000/my-timetable-ui` — verify UI pages still work
- [x] Visit `http://localhost:8000/replacement-home-ui` — verify UI pages still work
- [x] Verify no console errors on any page

## Task 8: Update changelog

- [x] Create `page-changelogs/login-oop-refactor-changelog.md`
- [x] Document files changed: layout created, staff refactored, student refactored
- [x] Document key design decisions: 12 Blade parameters, CSS custom properties, single validateLogin()
