# Proposal: OOP Blade Template Refactor

## Why This Change Is Needed

The codebase has 4 UI design templates + 2 auth login pages, each containing a full HTML shell with duplicated `<head>`, inline `<style>`, and inline `<script>` blocks. The same top navigation bar HTML (~70 lines), nav-bar CSS (~400 lines), table/pagination/modal/summary CSS (~200 lines), and 5 JavaScript helper functions (`updateIcon`, `toggleTheme`, `navigateHome`, `to12h`, `formatDate`) are copy-pasted across every template. Any visual change (e.g., adding a nav link, updating a color token, fixing a theme toggle bug) requires editing all files identically, violating DRY and making maintenance error-prone.

Additionally, `auth/login-staff.blade.php` and `auth/login-student.blade.php` are ~80% identical (500 lines each) — they share the same glassmorphism card, input styling, theme toggle, password visibility toggle (`togglePassword`), and ripple animation (`ripple`). Only the background image, validation regex, button color (primary vs secondary), title text, and role-switch link differ.

A shared layout (`layouts/ui-template.blade.php`) and nav-bar partial (`partials/ui-nav-bar.blade.php`) already exist but are unused — the templates still hardcode everything. This change wires them up and extracts the remaining shared code.

## Scope

### In Scope

**1. Activate layout inheritance for all 4 UI design templates**

Refactor these 4 files to `@extends('layouts.ui-template')` and keep only their page-specific content in `@section('content')` / `@section('page-styles')` / `@section('page-scripts')`:
- `ui-design-templates/replacement-home-UI-design-template.blade.php`
- `ui-design-templates/MyTimetable-UI-design-template.blade.php`
- `ui-design-templates/my-request-history-UI-design-template.blade.php`
- `ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`

Each template passes `$activeNav` so the nav-bar partial can light up the correct active link. `replacement-home` passes `$activeNav = ''` (no nav item corresponds — the home view is reached via the logo click, not a nav link). The corresponding routes in `routes/web.php` are updated to pass this variable.

**2. Consolidate shared CSS into `theme.css`**

Move these duplicated CSS blocks from inline `<style>` into `public/css/theme.css`:
- Nav bar styles: `.top-bar`, `.top-logo`, `.nav-items`, `.nav-item`, `.top-right`, `.theme-toggle`, `.notif-btn`, `.notif-badge`, `.user-panel`, `.user-profile`, `.user-avatar`, `.user-info`, `.user-name`, `.user-role`, `.logout-btn`
- App container: `.app-container`
- Shared page elements: `.page-header`, `.page-title`, `.page-desc`
- Table styles: `.grid-wrapper`, `.grid-scroll`, `.timetable`, `.timetable th/td`, sortable headers, zebra striping, hover
- Sort arrow: `.sort-arrow`
- Sort hint: `.sort-hint`
- Toolbar: `.toolbar`, `.toolbar-left`, `.toolbar-right`, `.search-wrapper`, `.search-input`, `.search-icon`, `.filter-select`, `.result-count`
- Cell styles: `.cell-code`, `.cell-name`
- Badges: `.badge` base class
- Pagination: `.pagination-bar`, `.pagination-info`, `.pagination-controls`, `.page-btn`
- Summary cards: `.summary-bar`, `.summary-card`, `.summary-value`, `.summary-label`
- Empty state: `.empty-state`, `.empty-icon`, `.empty-title`, `.empty-text`
- Responsive breakpoints: `@media (max-width: 1024px)` and `@media (max-width: 768px)` for shared components
- Button: `.btn-action`, `.btn-outline`

Note: The login pages also load `theme.css` and use a `.theme-toggle` class for a different widget (40×40 fixed-position glassmorphism button vs the 32×32 nav-bar button). To avoid a class-name collision once the nav-bar `.theme-toggle` is in `theme.css`, the login-page version is renamed to `.login-theme-toggle` in the unified login view.

**3. Extract shared JavaScript into `public/js/ui-common.js`**

Move 5 duplicated functions into a shared file included by `layouts/ui-template.blade.php` via `<script src="/js/ui-common.js"></script>` (loaded normally, not deferred — the layout's inline IIFE and DOMContentLoaded handler depend on these functions being available):
- `updateIcon(isDark)` — moon/sun SVG paths
- `toggleTheme()` — DOM class toggle + localStorage persistence
- `navigateHome()` — redirect to `/` (this is the default for all pages; `replacement-home` previously redirected to `/my-timetable-ui` but will now use the shared `/` redirect — this is an intentional behavior change)
- `to12h(t)` — 24h → 12h time conversion
- `formatDate(iso)` — ISO date → "DD Mon YYYY"

**4. OOP-improve existing auth login pages (keep separate)**

Update `auth/login-staff.blade.php` and `auth/login-student.blade.php` independently — no file merging. Changes per file:
- Add `<script src="/js/ui-common.js"></script>` in `<head>` (provides `updateIcon`/`toggleTheme`)
- Remove inline `updateIcon` and `toggleTheme` functions from `<script>` (now in `ui-common.js`)
- Drop the vestigial Tailwind CDN (`cdn.tailwindcss.com` + `tailwind.config` block) — no utility classes are used
- Keep all login-specific CSS, HTML, and remaining JS (`togglePassword`, `ripple`, `validateLogin`) inline

No route changes needed — both routes already point to their respective views.

**5. Extract reusable Blade partial for summary bar (3+ templates)**

Create `partials/ui-summary-bar.blade.php` — `.summary-bar` container with card slots. The summary bar structure (`.summary-bar` → `.summary-card` × N → `.summary-value` + `.summary-label`) is duplicated across `replacement-home`, `MyTimetable`, and `my-request-history` (3 templates, meets the threshold). The partial accepts an array of cards: `[['class' => 'card-total', 'valueId' => 'summaryTotal', 'label' => 'Total Requests'], ...]`.

Note: `pagination-bar` and `empty-state` are only duplicated across 2 templates each (below the 3+ threshold) — these stay inline in each template's `@section('content')`.

### Explicitly Out of Scope

- `login-UI-design-template.blade.php` (at views root) — this is a standalone mockup with no route; it is not the real login page and is not touched
- Login page files are kept separate — no consolidation into a single view
- No back-end PHP controllers, services, or middleware changes
- No Livewire component creation
- No database schema or migration changes
- No new routes (existing routes stay, only the view/closure they use changes)
- No Vite/build pipeline changes — `ui-common.js` is served as a static file from `public/js/`
- No refactoring of page-specific JS logic (sorting, filtering, mock data, modal opening) — these stay inline in each template's `@section('page-scripts')`
- No refactoring of page-specific CSS (column widths unique per page, unique badge color classes, unique modal field layouts)
- The Flux-based layouts (`layouts/app.blade.php`, `layouts/app/sidebar.blade.php`, `layouts/app/header.blade.php`, `layouts/auth/`) and their pages are not touched
- The `dashboard.blade.php` and `welcome.blade.php` are not touched

## Impact Scope

| File | Action |
|------|--------|
| `layouts/ui-template.blade.php` | **Edit** — add `<script src="/js/ui-common.js">`; remove duplicated nav-bar/app-container CSS (now in `theme.css`); remove inline `updateIcon`/`toggleTheme`/`navigateHome` (now in `ui-common.js`); the `@include('partials.ui-nav-bar')` and `@yield` sections already exist and remain |
| `partials/ui-nav-bar.blade.php` | **No change** — already exists and correct |
| `public/css/theme.css` | **Edit** — append ~600 lines of shared component CSS (nav, table, toolbar, pagination, summary, empty state, badges, buttons, responsive) |
| `public/js/ui-common.js` | **Create** — 5 shared helper functions |
| `partials/ui-summary-bar.blade.php` | **Create** — reusable summary card row |
| `ui-design-templates/replacement-home-UI-design-template.blade.php` | **Edit** — `@extends` layout, remove duplicated CSS/HTML/JS, keep only page-specific content |
| `ui-design-templates/MyTimetable-UI-design-template.blade.php` | **Edit** — same refactor |
| `ui-design-templates/my-request-history-UI-design-template.blade.php` | **Edit** — same refactor |
| `ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | **Edit** — same refactor |
| `auth/login-staff.blade.php` | **Edit** — add `ui-common.js` script tag, remove inline `updateIcon`/`toggleTheme`, drop Tailwind CDN |
| `auth/login-student.blade.php` | **Edit** — same changes as staff login |
| `routes/web.php` | **Edit** — update `/login/student` and `/login/staff` closures to pass config variables; update 4 UI template routes to pass `$activeNav` |

No existing files are modified beyond the above. No backend, no models, no migrations.
