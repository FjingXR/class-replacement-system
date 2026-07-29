# Changelog — OOP Blade Template Refactor

## Files Changed

### `public/js/ui-common.js` (new)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | — | Created file | 5 shared JS functions: `updateIcon(isDark)`, `toggleTheme()`, `navigateHome()` (→ `/`), `to12h(t)`, `formatDate(iso)` — extracted from 6 templates into a single file, loaded by layout + login pages |

### `public/css/theme.css`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | — | Appended shared CSS | ~544 lines of shared component CSS appended — body base, nav bar (`.top-bar`→`.logout-btn`), `.app-container`, page header, grid+timetable table, sort arrows, toolbar+search+filter, cell styles, `.badge` base, pagination, summary bar+cards, empty state, buttons, responsive `@media` breakpoints. Extracted from inline `<style>` in layout + 4 UI templates |

### `resources/views/partials/ui-summary-bar.blade.php` (new)

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | — | Created partial | Reusable Blade partial accepting `$cards` array (`class`, `valueId`, `label`) — renders `.summary-bar` with `.summary-card` divs via `@foreach`. Used by 3 templates (replacement-home, my-timetable, my-request-history) |

### `resources/views/layouts/ui-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | Lines 18–20 | CSS extraction | Removed ~220 lines of shared CSS from inline `<style>` (body, nav-bar, app-container, responsive) — now in `theme.css`. Left `<style>` as wrapper for `@yield('page-styles')` |
| 2026-07-29 | Lines 34, 36–38 | JS extraction | Removed inline `updateIcon`/`toggleTheme`/`navigateHome` — now in `ui-common.js`. Added `<script src="/js/ui-common.js">` before inline `<script>`. Kept `DOMContentLoaded` handler inline |
| 2026-07-29 | Lines 24–26 | Conditional nav | Wrapped `@include('partials.ui-nav-bar')` in `@if(!isset($hideNav) \|\| !$hideNav)` — replacement-arrangement page passes `hideNav: true` to avoid double top-bar (page has its own custom top bar) |
| 2026-07-29 | — | Layout shrink | Layout reduced from 276 to 43 lines — removed all embedded CSS/JS, now pure structural shell with `@yield` sections |

### `resources/views/partials/ui-nav-bar.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | Line 10 | Nav link fix | Changed `Replacement Arrangement` nav item href from `/replacement-arrangement` → `/replacement-home-ui` (links to the section home page, not the detail page) |

### `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | Lines 1–2 | Refactored | Replaced standalone `<html>` shell with `@extends('layouts.ui-template', ['activeNav' => 'replacement-arrangement'])`. Extracted shared CSS/JS to theme.css/ui-common.js. Removed ~300 lines of duplicated code. Page now fills `@section('title')`, `@section('page-styles')` (column widths, badge variants, modal CSS), `@section('content')` (page-header, toolbar, sort-hint, grid-wrapper, summary-bar partial, empty-state), `@section('page-scripts')` (mock data, render functions, event listeners) |

### `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | Lines 1–2 | Refactored | Same pattern, `$activeNav` = `'my-timetable'`. Summary cards derived from existing `.summary-bar` HTML. Removed duplicated CSS/JS, reduced file size significantly |

### `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | Lines 1–2 | Refactored | Same pattern, `$activeNav` = `'replacement-history'`. Summary cards: Total, Approved, Pending, Rejected |

### `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | Lines 1 | Refactored | Same pattern, `$activeNav` = `'replacement-arrangement'`, `'hideNav' => true`. No summary bar on this page. Shared nav bar suppressed because page has its own custom top bar (back button + centered logo) |

### `resources/views/auth/login-staff.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | `<head>` | Added ui-common.js | Added `<script src="/js/ui-common.js">` after pre-paint IIFE |
| 2026-07-29 | `<script>` | Removed duplicates | Removed inline `updateIcon(isDark)` and `toggleTheme()` — now resolved from `ui-common.js` |
| 2026-07-29 | `<head>` | Dropped Tailwind | Removed `<script src="https://cdn.tailwindcss.com">` and `tailwind.config` block (vestigial, no utility classes used) |

### `resources/views/auth/login-student.blade.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | `<head>` | Added ui-common.js | Same 3 changes as login-staff |
| 2026-07-29 | `<script>` | Removed duplicates | Same as staff |
| 2026-07-29 | `<head>` | Dropped Tailwind | Same as staff |

### `routes/web.php`

| Timestamp | Location | Change | Detail |
|-----------|----------|--------|--------|
| 2026-07-29 | Lines 15–29 | Updated routes | Added `$activeNav` to all 4 UI routes: `/replacement-home-ui` → `'replacement-arrangement'`, `/my-timetable-ui` → `'my-timetable'`, `/my-request-history-ui` → `'replacement-history'`, `/replacement-arrangement` → `'replacement-arrangement'`. No `->name()` added (routes unreferenced by `route()` helpers) |
