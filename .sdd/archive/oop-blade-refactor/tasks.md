# Tasks: OOP Blade Template Refactor

## Task 1 — Create `public/js/ui-common.js` and append shared CSS to `theme.css`

- [x] Create `public/js/ui-common.js` with 5 functions:
  - `updateIcon(isDark)` — moon/sun SVG paths (copy from `replacement-home-UI-design-template.blade.php`)
  - `toggleTheme()` — DOM class toggle + localStorage persistence (copy from same file)
  - `navigateHome()` — set to `window.location.href = '/'` (intentional change from source's `/my-timetable-ui`, see design Section 8)
  - `to12h(t)` — 24h → 12h time conversion (copy from same file)
  - `formatDate(iso)` — ISO date → "DD Mon YYYY" (copy from same file)
- [x] Append shared component CSS to `public/css/theme.css` — body base styles, nav bar (`.top-bar` → `.logout-btn`), `.app-container`, page header, grid wrapper + timetable, sort arrow + hint, toolbar + search + filter, cell styles, `.badge` base, pagination, summary bar + cards, empty state, buttons (`.btn-action`, `.btn-outline`), responsive breakpoints (`@media 1024px`, `@media 768px`)
- [x] Verify `theme.css` is valid CSS (no syntax errors)
- [x] Verify `ui-common.js` has no syntax errors

**Effort:** 1.5 hours

## Task 2 — Create `partials/ui-summary-bar.blade.php`

- [x] Create `partials/ui-summary-bar.blade.php` — accepts `$cards` array, loops with `@foreach`, outputs `.summary-card` div with `$card['class']`, `$card['valueId']`, `$card['label']`
```blade
<div class="summary-bar" id="summaryBar">
    @foreach ($cards as $card)
        <div class="summary-card {{ $card['class'] }}">
            <span class="summary-value" id="{{ $card['valueId'] }}">0</span>
            <span class="summary-label">{{ $card['label'] }}</span>
        </div>
    @endforeach
</div>
```
- [x] Verify partial file exists and is valid Blade syntax

**Effort:** 30 minutes

## Task 3 — Refactor `layouts/ui-template.blade.php`

- [x] Remove all shared CSS from the layout's inline `<style>` (body, nav-bar, app-container, responsive `@media`) — leave only the `<style>` wrapper containing `@yield('page-styles')`
- [x] Remove `updateIcon`, `toggleTheme`, `navigateHome` from inline `<script>` — they move to `ui-common.js`
- [x] Keep the `DOMContentLoaded` handler inline (calls `updateIcon()` which is now in `ui-common.js`)
- [x] Add `<script src="/js/ui-common.js"></script>` immediately before the inline `<script>` block (before the DOMContentLoaded handler + `@yield('page-scripts')`)
- [x] Verify the `@include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? ''])` and all `@yield` sections remain unchanged
- [x] Verify `layouts/ui-template.blade.php` parses without Blade errors (lint the file) — full E2E testing deferred to Task 4

**Effort:** 1 hour

## Task 4 — Refactor 4 UI design templates to `@extends` + update routes

- [x] Refactor `replacement-home-UI-design-template.blade.php`:
  - Replace `<!DOCTYPE html>` + `<html>` + `<head>` + `<body>` shell with `@extends('layouts.ui-template', ['activeNav' => ''])`
  - Convert `<title>…</title>` to `@section('title', 'Replacement Arrangement — Class Replacement System')`
  - Move page-specific CSS (column widths, badge variants, urgency classes, filter-input/filter-label, **modal CSS with modal field-layout styles**) into `@section('page-styles')` as raw CSS (no `<style>` tags)
  - Move page HTML (`page-header`, `toolbar`, `sort-hint`, `grid-wrapper`, `pagination-bar`, `@include('partials.ui-summary-bar', ['cards' => [5 cards: card-conflicted, card-venues, card-students, card-duration, card-courses]])`, `empty-state`) into `@section('content')`
  - Move page-specific JS (`conflictedClasses` mock data, `buildTable()`, `updatePagination()`, `updateResultCount()`, `updateSummary()`, `badgeClass()`, `daysLeft()`, `urgencyClass()`, `computeWeek()`, `populateWeekDropdown()`, `goToReplacementWith()`, DOMContentLoaded handler, event listeners) into `@section('page-scripts')` as raw JS (no `<script>` tags)
  - Remove inline `updateIcon`, `toggleTheme`, `navigateHome`, `to12h`, `formatDate` — now in `ui-common.js`
  - Remove the hardcoded nav bar HTML (`.top-bar` block) — now via `@include` in layout
  - Remove shared CSS (nav, body, app-container, table base, pagination, summary, empty state, etc.) — now in `theme.css`
- [x] Refactor `MyTimetable-UI-design-template.blade.php` — same pattern, `$activeNav` = `'my-timetable'`, `@include('partials.ui-summary-bar', ['cards' => [inspect existing `.summary-bar` HTML to derive card array]])`
- [x] Refactor `my-request-history-UI-design-template.blade.php` — same pattern, `$activeNav` = `'replacement-history'`, `@include('partials.ui-summary-bar', ['cards' => [4 cards: card-total, card-approved, card-pending, card-rejected]])`
- [x] Refactor `replacement-arrangement-UIdesign-template.blade.php` — same pattern, `$activeNav` = `'replacement-arrangement'`, `'hideNav' => true` (page has its own custom top bar with back button + centered logo; shared nav bar would produce a double top-bar) (no summary bar on this page)
- [x] Update `routes/web.php` — add `$activeNav` to all 4 UI template routes (do not add `->name()` — these routes are unreferenced by `route()` helpers; only the `view()` call changes):
  - `/replacement-home-ui` → `['activeNav' => '']`
  - `/my-timetable-ui` → `['activeNav' => 'my-timetable']`
  - `/my-request-history-ui` → `['activeNav' => 'replacement-history']`
  - `/replacement-arrangement` → `['activeNav' => 'replacement-arrangement']` — no `hideNav` needed here; the flag is set in the template's `@extends` call
- [x] Test each page: verify nav bar renders (with correct active link), theme toggle works, logo click goes to `/`, table renders, sorting/pagination/filtering all work, summary cards show correct counts

**Effort:** 2 hours

## Task 5 — OOP-improve existing auth login pages

- [x] Edit `auth/login-student.blade.php`:
  - Add `<script src="/js/ui-common.js"></script>` in `<head>` (after the pre-paint IIFE)
  - Remove the inline `updateIcon(isDark)` function from `<script>`
  - Remove the inline `toggleTheme()` function from `<script>`
  - Drop the Tailwind CDN script tag (`<script src="https://cdn.tailwindcss.com"></script>`)
  - Drop the `tailwind.config = { ... }` block
  - Verify `DOMContentLoaded` handler still works (calls `updateIcon()` which is now in `ui-common.js`)
- [x] Edit `auth/login-staff.blade.php`:
  - Same 5 changes as student login
- [x] Test: visit `/login/student` — verify theme toggle works (sun/moon icon changes, persists across reload)
- [x] Test: visit `/login/staff` — verify theme toggle works
- [x] Test: verify no console errors on either page (no missing function errors)

**Effort:** 30 minutes

## Task 6 — Final smoke test across all pages

- [x] Visit all 5 routes — 4 UI templates (`/replacement-home-ui`, `/my-timetable-ui`, `/my-request-history-ui`, `/replacement-arrangement`) + login pages (`/login/student`, `/login/staff`)
- [x] Verify no Blade compile errors on any page
- [x] Verify nav bar renders on all 4 UI templates with correct active link highlighted
- [x] Verify theme persistence works: toggle theme on one page, navigate to another, verify theme persists
- [x] Verify logo click goes to `/` on all pages
- [x] Verify login pages have working theme toggle and no console errors

**Effort:** 30 minutes
