# Explore Brief — UI OOP Phase 1: Blade Partials + CSS Extraction

## Problem

6 UI design templates contain ~18 duplicated patterns (HTML structures, CSS classes, JS functions) that violate the project's DRY/OOP convention. Phase 1 targets the **5 Blade partials + 3 CSS extractions** — the simpler, structural half. Phase 2 (JS extractions) is deferred.

## Scope

### In Scope — Blade Partials (5 new partials)

| # | Partial | Replaces | Files affected | Params |
|---|---------|----------|---------------|--------|
| 1 | `partials/ui-page-header.blade.php` | `<div class="page-header">…` block | All 6 files | `$title`, `$chipText`, `$description`, optional `$chips` array (for status badges) |
| 2 | `partials/ui-week-nav.blade.php` | `<div class="week-nav">…` + `@include('ui-today-btn')` | 4 files (Cohort, MyTimetable, StudentTimetable, Arrangement) | `$weeks` array, optional `$reversed` (for Arrangement's inverted prev/next) |
| 3 | `partials/ui-empty-state.blade.php` | `<div class="empty-state">…` block | 5 files (all except Arrangement) | `$title`, `$text`, optional `$icon` (SVG string, defaults to calendar) |
| 4 | `partials/ui-grid-table.blade.php` | `<div class="grid-wrapper">…<table>…` wrapper | All 6 files | optional `$scrollId` (default `gridScroll`), optional `$tableId` (default `timetable`) |
| 5 | `partials/ui-class-detail-modal.blade.php` | `<div class="modal-overlay" id="…Modal">…` | 3 files (Cohort, MyTimetable, StudentTimetable) | `$modalId`, uses `@slot('modal-footer')` for footer content |

### In Scope — CSS Extraction (→ `theme.css`)

| # | CSS Pattern | Currently in | Files affected |
|---|-------------|-------------|---------------|
| 6 | Badge classes (`.badge-normal`, `.badge-replacement`, `.badge-pending`, `.badge-conflict`, `.status-*`) | Inline `<style>` in Cohort + RequestHistory | 3+ files |
| 7 | Modal footer layout (`.modal-footer`, `.modal-footer-left`, `.modal-footer-right`) | Inline `<style>` in MyTimetable + RequestHistory + Arrangement + Home | 3+ files |
| 8 | Button variants (`.btn-outline`, `.btn-danger`) | Inline `<style>` in RequestHistory + Arrangement | 3 files |

### Out of Scope (Phase 2)

All JS extractions: `weekData` generation, `currentWeekIndex()`, `prevWeek/nextWeek/selectWeek`, `buildTimetable()`, `updateSummary()`, `openModal/closeModal`, event block HTML, `dayNames` array, mobile swipe init.

## Key Design Decisions

1. **Parameters, not global reads** — Partials accept params. Pages own data; partials just render. Keeps partials testable and reusable.

2. **Page header supports two variants** — Single semester chip (5 files) OR chip + status badges via `$chips` array (student-my-timetable). One partial handles both.

3. **Empty state defaults to calendar SVG** — `$icon` param is optional. If not passed, renders the standard calendar SVG. Pages needing a different icon pass their own SVG string.

4. **Grid table uses default IDs** — `$scrollId` defaults to `gridScroll`, `$tableId` defaults to `timetable`. Most pages already use these IDs, minimizing JS changes.

5. **Modal footer uses Blade `@slot`** — More natural for HTML content than a string param. Standard Laravel pattern.

6. **CSS goes into `theme.css` directly** — No new CSS file. These are simple utility classes that belong in the shared stylesheet.

7. **Week nav supports reversed direction** — Arrangement's prev/next is inverted (prev=week++, next=week--). The partial accepts optional `$reversed` flag.

## Files to Modify

### New files (5 partials)
- `resources/views/partials/ui-page-header.blade.php`
- `resources/views/partials/ui-week-nav.blade.php`
- `resources/views/partials/ui-empty-state.blade.php`
- `resources/views/partials/ui-grid-table.blade.php`
- `resources/views/partials/ui-class-detail-modal.blade.php`

### Modified files (6 templates + 1 CSS)
- `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php`
- `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php`
- `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`
- `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`
- `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php`
- `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`
- `public/css/theme.css` (add badge, modal-footer, btn classes)

## Verification

- All 6 routes render identically (visual comparison against existing screenshots)
- No new CSS classes leak (existing pages unaffected)
- Partial params are self-documenting (readable at call site)
- Inline `<style>` blocks reduced by ~150 lines total
