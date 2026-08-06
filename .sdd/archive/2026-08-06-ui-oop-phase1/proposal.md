# Proposal: UI OOP Phase 1 — Blade Partials + CSS Extraction

## Why This Change Is Needed

The 6 UI design templates (`CohortTimetable`, `MyTimetable`, `StudentMyTimetable`, `ReplacementHome`, `ReplacementArrangement`, `MyRequestHistory`) contain **18 duplicated patterns** — identical HTML structures, CSS classes, and layout blocks copy-pasted across files. This violates the project's established DRY/OOP convention (`theme.css`, `ui-common.js`, existing partials like `ui-nav-bar`, `ui-summary-bar`).

Every new page requires re-creating the same page header, empty state, grid wrapper, modal, and badge styles from scratch. Any design change (e.g., updating the page header layout) must be applied to 6 files by hand. This is the same problem that `mock-data.js` solved for data — now we solve it for UI structure.

This is **Phase 1** of a 2-phase OOP extraction. Phase 1 targets the **structural** half (5 Blade partials + 3 CSS extractions). Phase 2 (JS function extraction) is deferred to a separate SDD change.

## Scope

### In Scope

**1. Five new Blade partials** under `resources/views/partials/`:

| # | Partial | What it replaces | Files | Key params |
|---|---------|-----------------|-------|------------|
| 1 | `ui-page-header.blade.php` | `<div class="page-header">…</div>` block (title + semester chip + description) | All 6 | `$title`, `$chipText`, `$description`, optional `$chips` array |
| 2 | `ui-week-nav.blade.php` | `<div class="week-nav">…</div>` + optional `@include('ui-today-btn')` (week arrows + select + today button) | All 6 | `$weeks` array, optional `$reversed`, optional `$showTodayBtn` (default true) |
| 3 | `ui-empty-state.blade.php` | `<div class="empty-state">…</div>` (icon + title + text) | 5 (all except Arrangement) | `$title`, `$text`, optional `$icon` SVG |
| 4 | `ui-grid-table.blade.php` | `<div class="grid-wrapper"><div class="grid-scroll">…<table>…</table></div></div>` | All 6 | optional `$scrollId`, `$tableId` |
| 5 | `ui-class-detail-modal.blade.php` | `<div class="modal-overlay" id="…Modal">…</div>` (class detail modal) | 3 (Cohort, MyTimetable, StudentTimetable) | `$modalId`, `@slot('modal-footer')` |

**2. Three CSS extractions** into `public/css/theme.css`:

| # | CSS Pattern | Currently in | What moves | Notes |
|---|-------------|-------------|-----------|-------|
| 6 | Badge variant classes (`.badge-normal`, `.badge-replacement`, `.badge-pending`, `.badge-conflict`) | Inline `<style>` in CohortTimetable (lines 8–27) | ~20 lines | **Must convert hardcoded hex to `var(--color-*)` tokens first** (CodingMAIN §10.0.1). `.badge` base already exists in `theme.css` line 514 — only variants move. |
| 7 | Modal footer modifiers (`.modal-footer-left`, `.modal-footer-right`) | Inline `<style>` in MyTimetable (lines 11–14) | ~8 lines | Base `.modal-footer` already in `theme.css` line 812 — only the left/right sub-containers move. Only truly duplicated in MyTimetable; other files use the base. |
| 8 | Button variants (`.btn-outline`, `.btn-danger`) | Inline `<style>` in RequestHistory + Arrangement | ~20 lines | **Different definitions per page** — must merge into single shared definition using theme tokens, or skip extraction. See design.md for resolution. |

**3. Refactor all 6 templates** to use the new partials and remove inline duplicates.

### Not In Scope

- **Phase 2 (JS extractions):** `weekData` generation, `currentWeekIndex()`, `prevWeek/nextWeek/selectWeek`, `buildTimetable()`, `updateSummary()`, `openModal/closeModal`, event block HTML, `dayNames` array, mobile swipe init — all deferred to a separate SDD change.
- **New pages:** No new UI pages are created.
- **Backend/logic changes:** Pure frontend structural refactor. No migrations, models, or Livewire components.
- **`ui-common.js` edits:** Phase 2's territory. This change does not touch `ui-common.js`.
- **`mock-data.js` edits:** Already centralized. This change does not touch mock data.
- **Visual/UX changes:** All pages render identically before and after. Same layout, same colors, same behavior.

## Impact Scope

**Files touched:**
- **New (5):** `resources/views/partials/ui-page-header.blade.php`, `ui-week-nav.blade.php`, `ui-empty-state.blade.php`, `ui-grid-table.blade.php`, `ui-class-detail-modal.blade.php`
- **Modified (7):** 6 templates under `resources/views/ui-design-templates/` + `public/css/theme.css`
- **Reduced:** ~150 lines of duplicated HTML/CSS eliminated across templates

**Net effect:** Each template shrinks by ~25 lines (inline HTML replaced by 1-line `@include`). New pages only need to call the partials with params — no more copy-pasting structural blocks.

## Impact Diagram

```
theme.css  ←──── badge variants (hex→token conversion) + modal-footer modifiers + btn variants
    │
    ├── ui-page-header.blade.php     ← new partial (title + chip + description + optional chips)
    ├── ui-week-nav.blade.php        ← new partial (week arrows + select + optional today-btn)
    ├── ui-empty-state.blade.php     ← new partial (icon + title + text)
    ├── ui-grid-table.blade.php      ← new partial (grid-wrapper + scroll + table shell)
    └── ui-class-detail-modal.blade.php ← new partial (modal overlay + header + body + slot footer)
          │
          ├── CohortTimetable template
          ├── MyTimetable template
          ├── StudentMyTimetable template
          ├── ReplacementHome template
          ├── ReplacementArrangement template
          └── MyRequestHistory template
```

## Design Decisions (from explore-brief)

1. **Parameters, not global reads** — Partials accept params. Pages own data; partials just render.
2. **Page header supports two variants** — Single chip OR chip + status badges via `$chips` array.
3. **Empty state defaults to calendar SVG** — `$icon` optional; default is the standard calendar SVG.
4. **Grid table uses default IDs** — `gridScroll` / `timetable` defaults; optional override.
5. **Modal footer uses Blade `@slot`** — Standard Laravel pattern for HTML content.
6. **CSS goes into `theme.css` directly** — No new CSS file.
7. **Week nav supports reversed direction** — `$reversed` flag for Arrangement's inverted prev/next.

## Verification

- All 6 routes render identically (visual comparison against existing screenshots)
- No new CSS classes leak (existing pages unaffected)
- Partial params are self-documenting (readable at call site)
- Inline `<style>` blocks reduced by ~150 lines total
- `composer run lint:check` passes (no PHP changes, only Blade)
