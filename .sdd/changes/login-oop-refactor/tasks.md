# Tasks: Login Pages OOP Refactor

## Task 1: Create shared login layout

- [ ] Create `resources/views/layouts/login-template.blade.php`
- [ ] Add HTML shell (`<!DOCTYPE html>`, `<head>`, `<body>`)
- [ ] Add pre-paint theme IIFE
- [ ] Add `ui-common.js` and `theme.css` includes
- [ ] Add `@yield('page-styles')` section
- [ ] Add background layer with `$bgImage` parameter
- [ ] Add `@yield('content')` section
- [ ] Add shared JS with `validateLogin()` function using `$idRegex`
- [ ] Add `@yield('page-scripts')` section

## Task 2: Move shared CSS to layout

- [ ] Copy all CSS from `login-staff.blade.php` to `layouts/login-template.blade.php`
- [ ] Parameterize button colors: replace hardcoded `var(--color-secondary)` with `var(--login-btn-bg)`
- [ ] Parameterize button hover: replace hardcoded `#88dbb3` with `var(--login-btn-hover-dark)`
- [ ] Parameterize spinner color: replace hardcoded `var(--color-on-secondary)` with `var(--login-spinner-color)`
- [ ] Add CSS custom properties in `:root` using Blade variables
- [ ] Verify CSS syntax is valid

## Task 3: Refactor login-staff.blade.php

- [ ] Replace entire file with `@extends('layouts.login-template', [...])` structure
- [ ] Pass all 12 parameters with staff-specific values
- [ ] Move form HTML to `@section('content')`
- [ ] Remove all duplicated CSS (now in layout)
- [ ] Remove all duplicated JS (now in layout)
- [ ] Remove `validateStaff()` function (now `validateLogin()` in layout)
- [ ] Update `oninput="validateStaff()"` to `oninput="validateLogin()"`
- [ ] Verify file is ~80 lines (down from 442)

## Task 4: Refactor login-student.blade.php

- [ ] Replace entire file with `@extends('layouts.login-template', [...])` structure
- [ ] Pass all 12 parameters with student-specific values
- [ ] Move form HTML to `@section('content')`
- [ ] Remove all duplicated CSS (now in layout)
- [ ] Remove all duplicated JS (now in layout)
- [ ] Remove `validateStudent()` function (now `validateLogin()` in layout)
- [ ] Update `oninput="validateStudent()"` to `oninput="validateLogin()"`
- [ ] Verify file is ~80 lines (down from 442)

## Task 5: Verify staff login

- [ ] Visit `http://localhost:8000/login/staff` — verify no Blade compile errors
- [ ] Verify page renders identically to previous behavior
- [ ] Verify background image loads (`staff-login-bg.jpg`)
- [ ] Verify green button color (secondary)
- [ ] Test ID validation: enter non-numeric ID → button stays disabled
- [ ] Test ID validation: enter numeric ID (e.g., "6767") + password → button enables
- [ ] Click "Log in" → form submits
- [ ] Click "Student? Student Login" → navigates to `/login/student`
- [ ] Toggle theme → dark/light mode works
- [ ] Verify no console errors

## Task 6: Verify student login

- [ ] Visit `http://localhost:8000/login/student` — verify no Blade compile errors
- [ ] Verify page renders identically to previous behavior
- [ ] Verify background image loads (`student-login-bg.jpg`)
- [ ] Verify blue button color (primary)
- [ ] Test ID validation: enter invalid format (e.g., "12345") → button stays disabled
- [ ] Test ID validation: enter valid format (e.g., "25RSD0001") + password → button enables
- [ ] Click "Log in" → form submits
- [ ] Click "Staff? Staff Login" → navigates to `/login/staff`
- [ ] Toggle theme → dark/light mode works
- [ ] Verify no console errors

## Task 7: Verify no regression

- [ ] Visit `http://localhost:8000/my-timetable-ui` — verify UI pages still work
- [ ] Visit `http://localhost:8000/replacement-home-ui` — verify UI pages still work
- [ ] Verify no console errors on any page

## Task 8: Update changelog

- [ ] Create `page-changelogs/login-oop-refactor-changelog.md`
- [ ] Document files changed: layout created, staff refactored, student refactored
- [ ] Document key design decisions: 12 Blade parameters, CSS custom properties, single validateLogin()
