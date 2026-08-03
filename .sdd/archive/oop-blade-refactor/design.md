# Design: OOP Blade Template Refactor

## Technical Approach

This change refactors Blade templates from "copy-paste standalone files" to "layout-extendible pages" using Laravel's Blade inheritance system (`@extends`, `@section`, `@yield`, `@include`). No PHP classes, no Livewire, no Vite build — pure Blade templating and static asset files.

## Architecture Decisions

### 1. Layout Structure: `@extends` + `@section` Pattern

**Decision:** Use `layouts/ui-template.blade.php` as the base layout with named `@yield` sections. Child pages fill them with `@section`.

The layout exposes these yield sections (verified against actual file):
- `@yield('title')` (line 6) → `<title>` tag
- `@yield('page-styles')` (line 236) → inside the layout's `<style>` wrapper — child pages emit **raw CSS rules only** (no `<style>` tags)
- `@yield('content')` (line 244) → page HTML body
- `@yield('page-scripts')` (line 273) → inside the layout's `<script>` wrapper — child pages emit **raw JS only** (no `<script>` tags)

**Template structure after refactor:**

```blade
@extends('layouts.ui-template', ['activeNav' => 'replacement-history'])

@section('title', 'My Request History — Class Replacement System')

@section('page-styles')
    /* Page-specific CSS only: column widths, badge variants, modal fields */
    .col-no { width: 50px; }
    .col-requested-at { width: 145px; }
    /* ... etc — raw CSS, NO <style> wrapper */
@endsection

@section('content')
    <!-- Page header -->
    <!-- Toolbar -->
    <!-- Table -->
    <!-- Pagination -->
    <!-- Summary cards -->
    <!-- Empty state -->
@endsection

@section('page-scripts')
    // Mock data, renderTable(), updatePagination(), sorting, modal logic
    // Raw JS, NO <script> wrapper — functions from ui-common.js already available
@endsection
```

### 2. CSS Consolidation Strategy

**Decision:** Move all shared component CSS into `theme.css`. Keep only page-specific CSS in each template's `@section('page-styles')`.

**What moves to `theme.css` (shared by 3+ templates):**
- Body base styles (`font-family`, `background`, `color`, `min-height`, `overflow-x`, `transition`)
- Nav bar + top-bar + user panel + theme toggle + notif button (all 6 files including layout)
- App container (`.app-container`)
- Page header (`.page-header`, `.page-title`, `.page-desc`) — 3 templates
- Grid wrapper + timetable table base (`.grid-wrapper`, `.grid-scroll`, `.timetable`, `.timetable th/td`, sortable headers, zebra, hover) — 3 templates
- Sort arrow + sort hint (`.sort-arrow`, `.sort-hint`) — 3 templates
- Toolbar + search + filter select + result count — 3 templates
- Cell styles (`.cell-code`, `.cell-name`) — 3 templates
- `.badge` base class — 3 templates
- Pagination bar + page buttons (`.pagination-bar`, `.pagination-info`, `.pagination-controls`, `.page-btn`) — 2 templates, but CSS is small and tightly coupled
- Summary bar + cards (`.summary-bar`, `.summary-card`, `.summary-value`, `.summary-label`) — 3 templates
- Empty state (`.empty-state`, `.empty-icon`, `.empty-title`, `.empty-text`) — 2 templates, small CSS
- Buttons (`.btn-action`, `.btn-outline`) — 2 templates
- Responsive breakpoints for above (`@media (max-width: 1024px)` and `@media (max-width: 768px)`) — all templates

**What stays inline in each template's `@section('page-styles')`:**
- Column width classes (`col-no`, `col-code`, etc.) — each page has different columns
- Badge color variants (`badge-holiday`, `status-pending`, etc.) — each page has different badge types
- Modal CSS — present in 3 templates but with different field layouts; stays inline
- Login card CSS (glassmorphism, input styling, ripple animation) — unique to login page
- Any page-specific utility CSS

**CSS load order:**
```
theme.css (shared component CSS, loaded first via <link>)
  → layout inline <style> wrapping @yield('page-styles') (page-specific CSS, loaded second, can override)
```

### 3. JavaScript Extraction (`ui-common.js`)

**Decision:** Create `public/js/ui-common.js` with 5 shared functions. Loaded by the layout via `<script src="/js/ui-common.js"></script>` placed immediately before the layout's inline `<script>` block.

**File structure:**
```javascript
// ui-common.js
function updateIcon(isDark) { ... }
function toggleTheme() { ... }
function navigateHome() { window.location.href = '/'; }
function to12h(t) { ... }
function formatDate(iso) { ... }
```

**Layout script order:**
```
1. <script> IIFE (pre-paint theme detection) — inline in <head>, runs immediately (layout L9-16)
2. <link rel="stylesheet" href="/css/theme.css"> — in <head> (layout L17)
3. <script src="/js/ui-common.js"></script> — NEW: in <body>, before inline <script> (see below)
4. Layout inline <script> — contains DOMContentLoaded handler that calls updateIcon() (kept inline; updateIcon is now external but available because script #3 loaded first)
5. @yield('page-scripts') — inside the same inline <script> wrapper (layout L273)
```

The `<script src="/js/ui-common.js"></script>` is placed immediately before the inline `<script>` block containing the DOMContentLoaded handler and `@yield('page-scripts')`. This guarantees functions are defined before any code runs.

**Remove inline copies of `to12h` and `formatDate` from all 4 UI templates** — they now resolve to the global functions in `ui-common.js`.

The layout's inline `<script>` retains:
- The `DOMContentLoaded` handler (calls the now-external `updateIcon()`)
- `@yield('page-scripts')` (page-specific JS)

### 4. Auth Login Page OOP Updates

**Decision:** Keep `login-staff.blade.php` and `login-student.blade.php` as separate files. Apply OOP improvements: load shared JS, remove duplicated functions, drop unused Tailwind CDN.

**Changes per file:**
1. Add `<script src="/js/ui-common.js"></script>` in `<head>` (after the pre-paint IIFE, before `theme.css` or alongside it)
2. Remove inline `updateIcon(isDark)` and `toggleTheme()` functions from `<script>` — they resolve to the global functions in `ui-common.js`
3. Drop the Tailwind CDN script + `tailwind.config` block (vestigial — no utility classes used)
4. Keep all login-specific CSS, HTML, and remaining JS (`togglePassword`, `ripple`, `validateLogin`, DOMContentLoaded handler) inline

**No route changes** — `/login/student` and `/login/staff` already point to their respective views.

**File changes:**
| File | Change |
|------|--------|
| `auth/login-staff.blade.php` | Add `ui-common.js` script tag, remove inline `updateIcon`/`toggleTheme`, drop Tailwind CDN |
| `auth/login-student.blade.php` | Same changes |

### 5. Summary Bar Partial

**Decision:** `partials/ui-summary-bar.blade.php` accepts an array of card configs.

```blade
{{-- partials/ui-summary-bar.blade.php --}}
<div class="summary-bar" id="summaryBar">
    @foreach ($cards as $card)
        <div class="summary-card {{ $card['class'] }}">
            <span class="summary-value" id="{{ $card['valueId'] }}">0</span>
            <span class="summary-label">{{ $card['label'] }}</span>
        </div>
    @endforeach
</div>
```

**Usage in a template:**
```blade
@include('partials.ui-summary-bar', [
    'cards' => [
        ['class' => 'card-total', 'valueId' => 'summaryTotal', 'label' => 'Total Requests'],
        ['class' => 'card-approved', 'valueId' => 'summaryApproved', 'label' => 'Approved'],
    ]
])
```

### 6. Layout File Changes

The layout file (`layouts/ui-template.blade.php`) currently contains shared CSS in its inline `<style>` (lines 18-234+) and shared JS in its inline `<script>` (lines 247-274).

**Changes:**
1. **Remove ALL shared CSS** from the layout's inline `<style>` — body base styles, nav-bar (`.top-bar`→`.logout-btn`), `.app-container`, and the responsive `@media (max-width: 768px)` block — moving them to `theme.css`. The layout's `<style>` tag is left as a wrapper containing only `@yield('page-styles')` so child-page CSS lands inside it.
2. **Remove `updateIcon`, `toggleTheme`, `navigateHome`** from the layout's inline `<script>` — they move to `ui-common.js`.
3. **Keep the `DOMContentLoaded` handler** inline (it calls the now-external `updateIcon()` — the function is available because `ui-common.js` loads first).
4. **Add `<script src="/js/ui-common.js"></script>`** immediately before the inline `<script>` block containing the DOMContentLoaded handler and `@yield('page-scripts')`.
5. The `@include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? ''])` (L241) and `@yield` sections (L236, L244, L273) already exist and remain unchanged.

### 7. UI Template Route Changes

Update `routes/web.php` to pass `$activeNav` for each UI template route (do **not** add `->name()` — these routes are unreferenced by `route()` helpers):

```php
Route::get('/replacement-home-ui', function () {
    return view('ui-design-templates.replacement-home-UI-design-template', ['activeNav' => '']);
});

Route::get('/my-timetable-ui', function () {
    return view('ui-design-templates.MyTimetable-UI-design-template', ['activeNav' => 'my-timetable']);
});

Route::get('/my-request-history-ui', function () {
    return view('ui-design-templates.my-request-history-UI-design-template', ['activeNav' => 'replacement-history']);
});

Route::get('/replacement-arrangement', function () {
    return view('ui-design-templates.replacement-arrangement-UIdesign-template', ['activeNav' => 'replacement-arrangement']);
});
```

### 8. `navigateHome()` Behavior Change

**Before:** `replacement-home-UI-design-template.blade.php` defined `navigateHome()` → `window.location.href = '/my-timetable-ui'`. All other templates used `/`.

**After:** All templates inherit `navigateHome()` from `ui-common.js` → `window.location.href = '/'`. The logo click on the Replacement Home page now goes to `/` (welcome page) instead of `/my-timetable-ui`.

This is intentional — the logo should consistently go to the home page, not a random sub-page.

## Dependencies

- `theme.css` — must be loaded before inline `<style>` in all pages
- `ui-common.js` — must be loaded before `@yield('page-scripts')` in the layout; loaded directly in login page
- `partials/ui-nav-bar.blade.php` — already exists, no changes needed
- `partials/ui-summary-bar.blade.php` — new file, created in this change
- Blade `@extends`, `@section`, `@yield` — built-in Laravel Blade features, no packages needed

## File Changes

| File | Change |
|------|--------|
| `layouts/ui-template.blade.php` | Remove all shared CSS from `<style>` (body, nav, app-container, responsive) → move to `theme.css`. Remove `updateIcon`/`toggleTheme`/`navigateHome` from `<script>` → move to `ui-common.js`. Add `<script src="/js/ui-common.js">` before inline `<script>`. Keep DOMContentLoaded handler. |
| `public/css/theme.css` | Append shared component CSS (~600 lines) |
| `public/js/ui-common.js` | Create — 5 shared functions |
| `partials/ui-summary-bar.blade.php` | Create — reusable summary bar partial |
| `ui-design-templates/replacement-home-UI-design-template.blade.php` | Refactor to `@extends`, remove duplicated CSS/HTML/JS, keep only page-specific content |
| `ui-design-templates/MyTimetable-UI-design-template.blade.php` | Same refactor |
| `ui-design-templates/my-request-history-UI-design-template.blade.php` | Same refactor |
| `ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | Same refactor |
| `auth/login-staff.blade.php` | Add `ui-common.js` script tag, remove inline `updateIcon`/`toggleTheme`, drop Tailwind CDN |
| `auth/login-student.blade.php` | Same changes |
| `routes/web.php` | Update 4 UI routes (pass `$activeNav`) + 2 login routes (pass config array) |
