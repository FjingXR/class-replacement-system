# Spec: /sdd-propose for a new UI frontend-only page

> **This file is auto-referenced.** `AGENTS.md` instructs any agent to read this spec
> BEFORE generating `/sdd-propose` output for a new **frontend-only UI page**.
> Fill the `<...>` placeholders in the "Page to create" section, then proceed to generate
> the SDD proposal/design/tasks following the rules below.

Create a new **frontend-only UI page** for the TARUMT Class Replacement System.

## Context (read first, non-negotiable)
1. Read `CodingMAIN.md` in full BEFORE writing anything — it is the single source of truth for this project. Pay special attention to §10.0 (UI Design Rules) and §9 (Page Inventory).
2. Read `../final/FR&NFR.md` for any FR/NFR this page satisfies.
3. Read the existing page closest in pattern under `resources/views/ui-design-templates/` and the matching `page-changelogs/*.md` to learn house style before designing.

## Discuss with the user BEFORE generating (do not skip)
After reading the above, talk to the user first — do NOT jump straight to writing the proposal:
1. **FR/NFR logic check:** list every FR/NFR that touches this page and say if each is logical for the mock phase. Flag anything that needs backend (defer it), and flag any contradiction between FR/NFR and CodingMAIN.md.
2. **UI design details:** confirm with the user the legend items + colors, summary cards, modal pattern, week picker, what to copy from the closest page, and what to remove (action buttons, role-only logic, renamed labels).
3. Only after the user says OK on (1) and (2), proceed to the "Page to create" and deliverables.

## Page to create
- **Name:** `<e.g. Request Approval>`
- **Template file:** `resources/views/ui-design-templates/<kebab-case>-UI-design-template.blade.php`
- **Route:** `/<kebab-route>-ui` in `routes/web.php`, passing `$activeNav`
- **activeNav:** `<one of: dashboard | my-timetable | cohort-timetables | replacement-arrangement | replacement-history | request-approval>`
- **Primary user / role:** `<Student | Lecturer | Programme Leader>`
- **Purpose (1–2 sentences):** `<what the page does>`
- **Mock-data scope:** `<list the mock JS objects / columns / cards this page needs>`

## Mandated design rules (from CodingMAIN.md §10.0) — every rule must be followed
1. **Color consistency** — consume ONLY `public/css/theme.css` CSS custom-property tokens (`--color-*`). NEVER hardcode hex/rgb/rgba in `@section('page-styles')`. If a new color is needed, add ONE token to `theme.css` once.
2. **Same name + same color = same meaning** — use the canonical legend/status→color map in §10.0 exactly. No synonyms, no recoloring. A status shown on another page reuses the same label + token here.
3. **OOP concepts + DRY** — `@extends('layouts.ui-template')`; reuse `@include('partials.ui-nav-bar'...)` and `@include('partials.ui-summary-bar'...)`; shared JS helpers in `public/js/ui-common.js`; shared CSS in `theme.css`; **shared mock data in `public/js/mock-data.js` (`window.MockData`)**. NEVER copy-paste nav bar / table / helpers / mock data into the new page.
4. **Mock data: one source of truth (`public/js/mock-data.js`)** — the page MUST read mock data from `window.MockData.*`, NOT re-declare cohorts/lecturers/venues/semester or duplicate page datasets inline. `MockData` is READ-ONLY: derive a local copy (`slice()`/spread) before mutating per-week/per-session state. If the page needs NEW mock data, add ONE new section to `mock-data.js` (e.g. `MockData.studentTimetable = {...}`) and reference it — do NOT inline it in the page's `<script>`. (Throwaway-by-design: deleted in Sprint 3 when real Livewire/DB data is wired.)
5. **Minimise plain text, maximise icon buttons** — actions are icon buttons (✎ ✕ ✓ 👁 ‹ › ▲▼) with `title`/`aria-label`, not verbose text labels. Visible text limited to titles, headers, badges, key data. Reuse SVGs from `resources/views/flux/icon/`.
6. **Don't overwhelm — secondary/detail info in modals** — the page surface shows only scannable essentials (grid/table + filters + summary cards + primary actions). Full request details, audit history, rejection-reason form, validation breakdown etc. open in a **modal** on click.
7. **Promote-on-third-duplication (DRY / OOP)** — while building the new page, if any UI element / markup block / CSS class / JS helper is **now duplicated across 3+ pages** (e.g. the `.summary-bar` + `.summary-card` pattern, the `.legend-bar`, the week picker, the detail modal shell), STOP and **promote it into a shared file**:
   - shared markup → a new Blade partial in `resources/views/partials/` (then `@include` it);
   - shared CSS → a class in `public/css/theme.css`;
   - shared JS → a helper in `public/js/ui-common.js`;
   - shared mock data → a section in `public/js/mock-data.js`.
   Then replace the inline copies in the **new page AND the existing pages** with `@include`/`var(...)`/`helper()`/`MockData.*`. Do NOT leave 3 copies of the same thing — that breaks the OOP/DRY concept the FYP rubric scores. List every promotion you do in the SDD `design.md` under a "Promoted to shared" section.
8. **Minimise steps — fewest clicks possible** — every common task reaches its outcome in the minimum number of clicks/screens. Prefer one inline action over multi-step forms, pre-select sensible defaults, don't hop between pages for a single task. If a flow needs more than ~3 clicks, rethink it.
9. **Confirm critical actions** — any destructive or irreversible action (delete, submit, cancel, approve, reject) MUST show a confirmation popup ("Are you sure?") before executing. This makes the system forgiving so users dare to try unfamiliar features, knowing a critical move can always be backed out.
10. **Toast/undo bar after critical actions** — after any critical action (delete, submit, cancel, approve, reject), show a temporary toast bar at **bottom-left** (same position as `.bulk-action-bar`) with a success message and an **Undo** button. The toast auto-dismisses after **5 seconds** unless manually closed. The undo callback restores the previous state. CSS in `theme.css`, JS helper `showToast(message, undoCallback, duration)` in `ui-common.js`. Document every toast usage in `design.md` under "Toast/undo bar" section.
10. **Mobile responsive design** — every page MUST include mobile layout (≤768px breakpoint). The following enhancements are MANDATORY:
    - **Nav drawer:** Hamburger icon (☰) replaces desktop links; slide-in drawer from left with overlay, close on tap/ESC/swipe; body scroll locked when open.
    - **Card layout:** Convert data tables to card layout on mobile (each row = a card with essential columns only). No horizontal scroll.
    - **Summary cards:** 2-column grid on mobile (5 cards → 3+2 layout).
    - **Legend bar:** Flex-wrap, items flow naturally into 2 rows.
    - **Semester bar:** Reduce select width, stack elements if needed, full-width.
    - **Page header:** Chips stack vertically, title reduces font size.
    - **Bottom sheet modals:** Modals slide up from bottom (not centered), 80vh max, drag handle, full-screen backdrop.
    - **Touch targets:** All buttons/links ≥ 44×44px (WCAG 2.5.5).
    - **Swipe gestures:** Swipe left/right to navigate weeks on timetable.
    - **Collapsible cards:** Day cards collapse/expand on tap (chevron indicator).
    - **Responsive typography:** Use `clamp()` for fluid font scaling (title 24→20px, day 14→13px, event 12→11px).
    - **Safe area insets:** Respect iPhone notch/home indicator via `env(safe-area-inset-*)`.
    - **Full-width inputs:** Selects, text inputs, buttons span full width on mobile.
    - **Skeleton loading:** Grey placeholder shapes with shimmer animation while data loads.
    - **Scroll restoration:** Remember scroll position on browser back/forward via `sessionStorage`.
    - **Toast position:** Toasts at bottom-center on mobile (thumb-reachable).
    - **Viewport meta:** `<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">` in `<head>`.
    Add CSS media queries in `theme.css` (shared). JS helpers in `ui-common.js`. Document mobile layout in `design.md` under "Mobile view" section.

## Deliverables (SDD: proposal → design → tasks)
1. `.sdd/changes/<change-name>/` with `sdd.yaml`, `proposal.md`, `design.md`, `tasks.md`.
2. The Blade template (render logic in `@section('page-scripts')`; NO inline mock data — read from `window.MockData.*`). If new mock data is needed, add it to `public/js/mock-data.js`.
3. The route added to `routes/web.php`.
4. **A changelog file `page-changelogs/<change-name>-changelog.md` created BEFORE the proposal** (even if it only has the header + an empty `## Files Changed` section), then kept updated as you build. Follow the exact format of an existing file in `page-changelogs/` (e.g. `my-timetable-changelog.md`): top `# Changelog — <Page Name>`, a `## Files Changed` section, one `### \`<file path>\`` heading per changed file, and per-file a table `| Timestamp | Location | Change | Detail |`. Log every touched file (template, mock-data.js, routes/web.php, shared partials/theme.css refactors). Use server-local ISO-ish timestamps.
5. After apply, run `composer run lint:check` + `composer run types:check` and confirm no new failures.

## Constraints / out of scope
- No new migrations, models, or backend logic (frontend mock phase).
- No new dependencies; reuse existing Flux/Livewire/Tailwind + `theme.css` + `ui-common.js` + `mock-data.js`.
- Keep the page under ~1500 lines (split into partials if larger).
