# Design: Timetable Print

## Technical Approach

Pure CSS + vanilla JS. No new dependencies.

1. **`theme.css`** — Add `@media print` block that hides non-essential elements, forces light theme, adjusts grid layout, and expands event blocks
2. **`ui-common.js`** — Add `printTimetable()` function that calls `window.print()`
3. **`ui-week-nav.blade.php`** — Add conditional printer icon button via `$showPrintBtn` parameter
4. **3 timetable templates** — Pass `showPrintBtn: true` to the week nav partial

## Architecture Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Print method | `window.print()` | Zero dependencies, native "Save as PDF", vector output |
| CSS approach | `@media print` | No runtime cost, declarative, browser-optimized |
| Button placement | Shared partial with param | DRY — one button definition, 3 pages |
| Color strategy | Override CSS custom properties | Ensures dark mode doesn't bleed into print |
| Layout | Landscape, multi-page allowed | Readability over density |
| Event block expansion | CSS `::before`/`::after` + data attributes | No JS DOM manipulation needed |

## Data Flow

```
User clicks print button
  → printTimetable() called (ui-common.js)
    → window.print() opens browser print dialog
      → @media print CSS applied (theme.css)
        → Non-essential elements hidden
        → Light theme forced via CSS variable overrides
        → Event blocks expanded via CSS content properties
        → Grid laid out in landscape, full width
```

## File Changes

### 1. `public/css/theme.css` — Add `.print-btn` screen styles + `@media print` block

**Screen styles** — Add after the existing `.today-btn` styles (line ~1199). Follows the same pattern as `.today-btn`:

```css
/* ── Print Button ── */
.print-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 8px;
    border: 1px solid var(--color-outline);
    background: var(--color-surface);
    color: var(--color-on-surface-variant);
    cursor: pointer;
    transition: background var(--transition), transform 0.15s;
}
.print-btn:hover {
    background: var(--color-surface-variant);
}
.print-btn:active {
    transform: scale(0.95);
}
.print-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
```

**Print styles** — Add after the existing `@media (prefers-reduced-motion: reduce)` block (line ~1713):

```css
/* ── Print Styles ── */
@media print {
    /* Force landscape */
    @page { size: landscape; margin: 10mm; }

    /* Force light theme */
    :root {
        --color-surface: #ffffff;
        --color-surface-variant: #f5f5f5;
        --color-on-surface: #1a1a1a;
        --color-on-surface-variant: #555555;
        --color-outline: #cccccc;
    }

    /* Hide non-essential elements */
    .top-bar,
    .nav-drawer,
    .nav-drawer-overlay,
    .semester-bar,
    .week-nav,
    .summary-bar,
    .semester-progress,
    .legend-bar + .summary-bar,
    .copy-toast,
    .modal-overlay,
    .modal,
    #toastBar,
    .btn-replace-now,
    .btn-cancel-class,
    .btn-close-modal,
    .nav-hamburger,
    .notif-btn,
    .theme-toggle,
    .user-panel,
    .week-chip,
    .toolbar,
    .toolbar-right,
    .print-btn { display: none !important; }

    /* Grid layout */
    .grid-wrapper { overflow: visible !important; }
    .grid-scroll { overflow: visible !important; }
    .timetable { width: 100% !important; min-width: 0 !important; }

    /* Remove sticky */
    .time-header-col,
    .time-col { position: static !important; }

    /* Remove hover/focus effects */
    .event-block:hover { filter: none !important; transform: none !important; }
    .event-block:focus { outline: none !important; }

    /* Remove today highlight */
    .today-cell,
    .today { background: transparent !important; }

    /* Expand event blocks for print */
    .event-block {
        font-size: 10px !important;
        padding: 4px !important;
        min-height: auto !important;
        height: auto !important;
        overflow: visible !important;
        white-space: normal !important;
    }

    /* Event block print details (via data attributes) */
    .event-block::after {
        content: attr(data-venue) " " attr(data-time);
        display: block;
        font-size: 9px;
        opacity: 0.7;
        margin-top: 2px;
    }
}
```

**Note:** The exact CSS variable names and selectors need verification against the actual `theme.css` structure. The event block expansion may require adding `data-venue` and `data-time` attributes to the JS-generated blocks.

### 2. `public/js/ui-common.js` — Add `printTimetable()`

Location: After `initScrollRestore()` function (line ~489).

```javascript
function printTimetable() {
    window.print();
}
```

### 3. `resources/views/partials/ui-week-nav.blade.php` — Add print button

Add `$showPrintBtn ?? false` parameter. Insert print button after the next arrow:

```php
@if($showPrintBtn ?? false)
<button class="print-btn" onclick="printTimetable()" @if($disabled ?? false) disabled @endif aria-label="Print timetable">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 6 2 18 2 18 9"/>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
        <rect x="6" y="14" width="12" height="8"/>
    </svg>
</button>
@endif
```

### 4. Timetable templates — Pass `showPrintBtn: true`

**MyTimetable-UI-design-template.blade.php** (line ~142):
```php
@include('partials.ui-week-nav', [..., 'showPrintBtn' => true])
```

**CohortTimetable-UI-design-template.blade.php** (line ~49):
```php
@include('partials.ui-week-nav', [..., 'showPrintBtn' => true])
```

**student-my-timetable-UI-design-template.blade.php** (line ~27):
```php
@include('partials.ui-week-nav', [..., 'showPrintBtn' => true])
```

## Event Block Data Attributes

The existing `buildTimetable()` JS in each template creates event blocks with `div.__eventData = e`. Two templates (MyTimetable, StudentMyTimetable) already set `div.dataset.name` and `div.dataset.venue`. CohortTimetable does **not** — this is an existing bug (hover tooltip `::after` in theme.css line 1226 uses `attr(data-name)` and `attr(data-venue)`).

**Fix (all 3 templates):** Add these lines to each template's `buildTimetable()` where event blocks are created:

```javascript
div.dataset.name = e.name || '';
div.dataset.venue = e.venue || '';
div.dataset.time = startStr + ' – ' + endStr;
```

This also fixes the existing CohortTimetable hover tooltip bug.

**Specific locations:**
- `MyTimetable-UI-design-template.blade.php` — line ~440 (already has `data.name`/`data.venue`, add `data.time`)
- `CohortTimetable-UI-design-template.blade.php` — line ~449 (add all three: `data.name`, `data.venue`, `data.time`)
- `student-my-timetable-UI-design-template.blade.php` — line ~313 (already has `data.name`/`data.venue`, add `data.time`)

## Dependencies

- None — pure CSS + vanilla JS
- `window.print()` supported in all modern browsers (Chrome, Firefox, Safari, Edge)

## Risks

| Risk | Mitigation |
|------|------------|
| CSS variable overrides may not覆盖 all dark mode colors | Test with actual dark theme, adjust variable list as needed |
| Event block expansion may break grid row height | Use `height: auto` and `min-height: 0` to allow natural flow |
| `@page { size: landscape }` not respected by all browsers | Fallback: user selects landscape manually in print dialog |
| Print button may be hidden by existing `display: none` rules | Verify no conflicting CSS selectors |
