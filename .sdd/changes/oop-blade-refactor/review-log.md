## proposal.md Round 1 — 2026-07-29

### 🔴 Fixed
 - Template-count contradiction: "6 templates" → "4 templates (4 in `ui-design-templates/`); the 5th file `login-UI-design-template.blade.php` at views root explicitly placed in out-of-scope as a route-less mockup"

### 🟡 Addressed
 - `navigateHome()` target conflict: stated that `/` is the default for all pages; `replacement-home` previously redirected to `/my-timetable-ui` — this behavior change is now documented as intentional
 - `.theme-toggle` class-name collision: login-page version renamed to `.login-theme-toggle` in the unified login view
 - Login button/hover/disabled divergence: enumerated exact differences (staff: opacity 0.3 disabled, `#88dbb3`/`#2db876` hover; student: `var(--color-outline)` disabled bg + opacity 0.4, `#154d94` hover); specified role-scoped modifier classes `.login-btn--secondary` / `.login-btn--primary` in inline login CSS, not theme.css
 - Layout file edit description: corrected to "remove duplicated CSS/JS now in theme.css + ui-common.js; add script include" (the `@include` and `@yield` already exist)
 - All 4 UI routes need `$activeNav`: added to impact scope — routes updated to pass `$activeNav` variable
 - `togglePassword()` and `ripple()`: mentioned as consolidated into unified login view
 - `validationFunction` implementation detail: changed to abstract "per-role front-end ID validation (numeric for staff, `NNLLLNNNN` for student) wired via single `validateLogin()` keyed off role variable"
 - `ui-pagination` and `ui-empty-state` partials: dropped — only 2 templates each (below 3+ threshold); kept only `ui-summary-bar` (3 templates)
 - `formatHint` student string: corrected to include example: `"Format: 2 digits + 3 letters + 4 digits (e.g. 25RSD0001)"`

### 🔴 Outstanding
 - (none — awaiting re-review)

## proposal.md Round 2 — 2026-07-29

### ✅ Round 1 Fixes Verified
 - All 10 Round 1 issues confirmed resolved against actual codebase

### 🟡 Addressed (declarative additions before freeze)
 - Login page theme-toggle function access: stated that unified login loads `/js/ui-common.js` for `updateIcon`/`toggleTheme` + keeps pre-paint IIFE inline — no duplication
 - `$activeNav` value for `replacement-home`: stated it passes `''` (no nav item corresponds — home is reached via logo click)
 - Tailwind CDN disposition: stated it is dropped entirely (vestigial, no utility classes used)

### 🔴 Outstanding
 - (none)

### ✅ PASS — proposal.md is frozen.

## design.md Round 1 — 2026-07-29

### 🔴 Fixed
 - `@push` → `@section` in code examples: rewrote Section 1 cleanly as a single decision — all four sections use `@section('…') … @endsection` because the layout uses `@yield`; removed the stream-of-consciousness reasoning
 - Frozen proposal.md soft-freeze: changed `@push('page-styles')`/`@push('page-scripts')` → `@section` in proposal L17 and L87 (declarative correction matching the real layout's `@yield`)
 - Login view `ui-common.js` include: uncommented `<script src="/js/ui-common.js"></script>` in the login view code example (was commented out, would break theme toggle)

### 🟡 Addressed
 - `validateLogin` mechanism divergence from proposal: added note explaining the design uses a route-injected `$validationRegex` rather than a role switch inside the function (outcome identical, approach cleaner)
 - Regex injection fragility: added note about `{{ }}` HTML-escaping constraint — no `<`, `>`, `&`, `"`, `'`; switch to `{!! !!}` if needed
 - Layout CSS removal completeness: Section 6 now says "remove ALL shared CSS — body, nav-bar, app-container, responsive @media" not just "nav-bar CSS"
 - DOMContentLoaded handler disposition: Section 6 point 3 now states to keep the handler inline and place `ui-common.js` before the inline `<script>` block
 - Pre-paint IIFE in login: stated "placed verbatim from layouts/ui-template.blade.php lines 9-16"
 - Line count: proposal soft-freeze ~400 → ~600 (corrected to match design.md)
 - `@section('page-styles')` output boundary: code example now shows raw CSS (no `<style>` wrapper) and explicitly notes child pages should NOT include `<style>` tags
 - Section 1 heading: renamed from "@push Pattern" to "@section Pattern"
 - Explicit note to remove inline `to12h`/`formatDate` from all 4 templates

### 🔴 Outstanding
 - (none)

### ✅ PASS — design.md is frozen.

## proposal.md soft-freeze correction — 2026-07-29
 - proposal.md L98: `~400 lines` → `~600 lines` (declarative — matches design.md File Changes table and actual CSS volume estimate)

## design.md Round 2 — 2026-07-29

### ✅ Round 1 Fixes Verified
 - All 12 Round 1 issues confirmed resolved against actual codebase

### 🟡 Addressed (declarative)
 - proposal.md L98 line count soft-freeze applied: ~400 → ~600
 
 ### 🔴 Outstanding
  - (none)
 
 ### ✅ PASS — design.md is frozen.

## tasks.md Round 1 — 2026-07-29

### 🔴 Fixed
 - Task 3/4 summary-bar partial ordering: split into 6 tasks — Task 2 creates `partials/ui-summary-bar.blade.php` BEFORE Task 4 refactors templates; Task 4 now uses `@include` correctly; no build-break gap

### 🟡 Addressed
 - Task 3 test step: reworded to "lint the file; full E2E deferred to Task 4" (layout was previously unused, no page extends it until Task 4)
 - Task 4: added `@section('title', '...')` conversion step for `<title>` element
 - Task 4: added modal CSS explicitly to the page-specific retention list
 - Task 1: reworded `navigateHome()` — explicitly states the `→ /` behavior change with reference to design Section 8
 - Task 5: explicitly states "preserve `->name('login.student')` / `->name('login.staff')`"
 - Task 5: inlined full 10-variable config arrays for both student and staff routes
 - Task 4: MyTimetable card set says "inspect existing `.summary-bar` HTML to derive card array"
 - Moved 4 UI template route updates from Task 5 into Task 4 (routes supply `$activeNav`)
 - Added Task 6: final smoke test across all 6 routes
 
### 🔴 Outstanding
 - (none)

## tasks.md Round 2 — 2026-07-29

### ✅ Round 1 Fixes Verified
 - All 10 Round 1 issues confirmed resolved against actual codebase

### 🟡 Addressed (declarative)
 - `->name()` ambiguity on 4 UI routes: added "do not add `->name()` — routes are unreferenced; only `view()` call changes" clarification to Task 4

### 🔴 Outstanding
 - (none)

### ✅ PASS — tasks.md is frozen.

## Implementation findings — 2026-07-29

### 🟡 Deviation from design: replacement-arrangement nav bar

**Issue:** The replacement-arrangement page has its own custom top bar (back button + centered logo + title). `@extends` + layout `@include('partials.ui-nav-bar')` produced a double top-bar.

**Fix:** Made nav bar conditional in `layouts/ui-template.blade.php`:
```blade
@if(!($hideNav ?? false))
    @include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? ''])
@endif
```

Replacement-arrangement template passes `'hideNav' => true` via `@extends`. The other 3 templates omit the flag (default `false`), so nav bar renders normally.

**Verification:** Playwright snapshot confirmed:
- Replacement-arrangement page: no "Dashboard" / "Notifications" in DOM — only the custom back button + centered logo
- Replacement-home page: nav bar renders with "Dashboard" link present
