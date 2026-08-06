# Tasks — UI OOP Phase 1: Blade Partials + CSS Extraction

> Frozen artifacts: `proposal.md`, `design.md`. Each task ≤ 2 hours.
> Refactor only — no new pages, no backend. Visual output must be identical before and after.

## Task 1 — Add badge variants to `theme.css`

- [x] **Convert `.badge-replacement` from hardcoded amber hex (`#d4a017`/`#b8860b`) to `var(--color-primary)` token** (design.md §3.1) — this is the ONLY visual change in the refactor (amber → blue)
- [x] Add `.badge-normal`, `.badge-replacement` (+ light mode), `.badge-pending`, `.badge-conflict` to `theme.css` using tokens from design.md §3.1
- [x] Remove the badge inline `<style>` definitions from `CohortTimetable-UI-design-template.blade.php`
- [x] **Do NOT extract `.modal-footer-left`/`.modal-footer-right` to theme.css** — RequestHistory uses these class names but has no CSS for them; making them global would change RequestHistory's layout. Keep them inline in MyTimetable.
- [x] Verify: CohortTimetable badge colors unchanged (except `.badge-replacement` amber → primary blue — confirm with user)

**Effort:** ~0.5h

## Task 2 — Create `partials/ui-page-header.blade.php`

- [x] Create `resources/views/partials/ui-page-header.blade.php` per design.md §2.1
- [x] Params: `$title`, `$chipText`, `$description`, `$chips` (array, default `[]`)
- [x] Logic: only wrap chips in `.page-chips` when `count($chips) > 0`; bare `<span>` for single chip
- [x] Replace page header block in all 6 templates with `@include('partials.ui-page-header', [...])`
- [x] Remove the `<span class="semester-chip" id="semesterChip"></span>` from each page's HTML (the partial renders it now)
- [x] Keep the `document.getElementById('semesterChip').textContent = MockData.semester.chipText;` JS line (it fills text into the partial's rendered span at runtime)

**Effort:** ~1h

## Task 3 — Create `partials/ui-week-nav.blade.php`

- [x] Create `resources/views/partials/ui-week-nav.blade.php` per design.md §2.2
- [x] Params: `$prevOnclick`, `$nextOnclick`, `$selectId`, `$selectOnclick`, `$selectClass` (default `'week-select'`), `$showTodayBtn` (default `true`), `$disabled` (default `false`)
- [x] Replace week-nav block in all 6 templates with `@include('partials.ui-week-nav', [...])`
- [x] Verify: each page's prev/next/select handlers still fire correctly

**Effort:** ~1h

## Task 4 — Create `partials/ui-empty-state.blade.php`

- [x] Create `resources/views/partials/ui-empty-state.blade.php` per design.md §2.3
- [x] Params: `$title`, `$text`, `$icon` (default calendar SVG), `$id` (default `'emptyState'`), `$ctaLabel`, `$ctaOnclick`, `$ctaStyle` (default `'display:none'`)
- [x] Replace empty-state block in 5 templates (all except Arrangement) with `@include('partials.ui-empty-state', [...])`
- [x] Verify: empty states display identically (hidden by default, shown by JS when needed)

**Effort:** ~0.75h

## Task 5 — Create `partials/ui-grid-table.blade.php`

- [x] Create `resources/views/partials/ui-grid-table.blade.php` per design.md §2.4
- [x] Params: `$scrollId` (default `'gridScroll'`), `$tableId` (default `'timetable'`), `$headId` (default `'tableHead'`), `$bodyId` (default `'tableBody'`)
- [x] Replace grid-wrapper + table shell in all 6 templates with `@include('partials.ui-grid-table')`
- [x] Verify: timetable grids render identically; JS selectors (by ID) still work

**Effort:** ~0.5h

## Task 6 — Create `partials/ui-class-detail-modal.blade.php`

- [x] Create `resources/views/partials/ui-class-detail-modal.blade.php` per design.md §2.5
- [x] Params: `$modalId` (default `'classModal'`), `$overlayOnclick` (default `'closeModalOutside(event)'`)
- [x] Uses `@hasSection`/`@yield('modal-footer')` with default Close button fallback
- [x] Replace modal block in 3 timetable templates (CohortTimetable, MyTimetable, StudentTimetable) with `@include` + `@section('modal-footer')`
- [x] **IMPORTANT:** `@section('modal-footer')` must appear BEFORE `@include('partials.ui-class-detail-modal')` in each template
- [x] Verify: modals open/close correctly; footer content matches original per page

**Effort:** ~1h

## Task 7 — Remove redundant inline CSS from templates

- [x] After all partials are wired, audit each template's `<style>` block
- [x] Remove badge classes from CohortTimetable (moved to theme.css in Task 1)
- [x] Keep `.modal-footer-left`/`.modal-footer-right` inline in MyTimetable (NOT extracted — would break RequestHistory)
- [x] Keep `.btn-outline`/`.btn-danger` inline in RequestHistory and Arrangement (not extracted per design.md §3.3)
- [x] Verify: no orphaned CSS classes remain; no visual changes

**Effort:** ~0.5h

## Task 8 — Documentation updates

- [x] Update `page-changelogs/*.md` for all 6 templates: note partial extraction
- [x] Update `CodingMAIN.md` §10: add the 5 new partials to the shared-modules list
- [x] Update `CodingMAIN.md` §13: add partials to key-files map

**Effort:** ~0.5h

## Task 9 — Final end-to-end verification

- [x] `php artisan serve`; load all 6 routes in sequence
- [x] For each: confirm layout/behavior identical to before refactor (the ONLY expected visual change is `.badge-replacement` amber → primary blue)
- [x] Confirm theme toggle, nav, week pickers, filters, modals all functional
- [x] Confirm `composer run lint:check` passes (no PHP changes, only Blade)
- [x] Commit: `refactor: extract shared UI patterns into Blade partials + theme.css (Phase 1)`

**Effort:** ~1h
