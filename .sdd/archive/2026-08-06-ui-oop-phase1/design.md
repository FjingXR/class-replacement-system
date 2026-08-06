# Design — UI OOP Phase 1: Blade Partials + CSS Extraction

> Frozen proposal: `.sdd/changes/ui-oop-phase1/proposal.md`. This design operationalizes it.

**Status: FROZEN (re-frozen after modal-footer scope correction)**

## 1. Technical approach

Five new Blade partials replace duplicated HTML structures across 6 UI templates. Two CSS patterns move from inline `<style>` blocks into `theme.css` (badge variants only — modal-footer modifiers stay inline to avoid breaking RequestHistory). All partials follow the existing convention: `@php` defaults via `$var ?? default`, `@slot` for complex content, params for data.

**Convention reference:** Existing partials (`ui-nav-bar`, `ui-summary-bar`, `ui-today-btn`) use `$variable ?? default` for optional params, `@php` blocks for logic, and minimal HTML. New partials follow the same style.

## 2. Partial specifications

### 2.1 `partials/ui-page-header.blade.php`

**Replaces:** `<div class="page-header">…</div>` in all 6 templates.

**Params:**
| Param | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `$title` | string | no | `null` | Page title. If null, `<h1>` is omitted (Arrangement has no title). |
| `$chipText` | string | no | `null` | Semester chip text. If null, chip is omitted. |
| `$description` | string | no | `null` | Page description. If null, `<p>` is omitted. |
| `$chips` | array | no | `[]` | Extra chip pills `[['label' => 'RSD3(S1)G2', 'class' => 'semester-chip']]`. Rendered inside `.page-chips` wrapper alongside the semester chip. |

**Template:**
```blade
<div class="page-header">
    @if($title)
        <h1 class="page-title">{{ $title }}</h1>
    @endif
    @if(count($chips) > 0)
        <div class="page-chips">
            @if($chipText)
                <span class="semester-chip" id="semesterChip">{{ $chipText }}</span>
            @endif
            @foreach($chips as $chip)
                <span class="{{ $chip['class'] ?? 'semester-chip' }}">{{ $chip['label'] }}</span>
            @endforeach
        </div>
    @elseif($chipText)
        <span class="semester-chip" id="semesterChip">{{ $chipText }}</span>
    @endif
    @if($description)
        <p class="page-desc">{{ $description }}</p>
    @endif
</div>
```

**File mapping:**
| File | Call |
|------|------|
| MyTimetable | `@include('partials.ui-page-header', ['title' => 'My Timetable', 'chipText' => MockData::semester->chipText, 'description' => 'View your weekly class schedule...'])` |
| CohortTimetable | `@include('partials.ui-page-header', ['title' => 'Cohort Timetable', 'chipText' => MockData::semester->chipText, 'description' => 'View the weekly timetable for any cohort...'])` |
| StudentTimetable | `@include('partials.ui-page-header', ['title' => 'My Timetable', 'chipText' => MockData::semester->chipText, 'description' => 'View your weekly class schedule...', 'chips' => [['label' => 'RSD3(S1)G2']]])` |
| ReplacementHome | `@include('partials.ui-page-header', ['title' => 'Replacement Arrangement', 'chipText' => MockData::semester->chipText, 'description' => 'The following classes require replacement...'])` |
| ReplacementArrangement | `@include('partials.ui-page-header', ['chipText' => MockData::semester->chipText])` |
| RequestHistory | `@include('partials.ui-page-header', ['title' => 'My Request History', 'chipText' => MockData::semester->chipText, 'description' => 'View and monitor all replacement requests...'])` |

### 2.2 `partials/ui-week-nav.blade.php`

**Replaces:** `<div class="week-nav">…</div>` + optional `@include('partials.ui-today-btn')` in all 6 templates.

**Params:**
| Param | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `$prevOnclick` | string | yes | — | JS function name for prev (e.g., `'prevWeek()'`) |
| `$nextOnclick` | string | yes | — | JS function name for next (e.g., `'nextWeek()'`) |
| `$selectId` | string | yes | — | ID for the `<select>` element (e.g., `'weekSelect'`, `'weekFilter'`, `'weekSelector'`) |
| `$selectOnclick` | string | yes | — | JS onchange handler (e.g., `'selectWeek(this.value)'`) |
| `$selectClass` | string | no | `'week-select'` | CSS class for select (Arrangement uses `'selector-dropdown'`) |
| `$showTodayBtn` | bool | no | `true` | Whether to include `@include('partials.ui-today-btn')` |
| `$disabled` | bool | no | `false` | Whether arrows + select start disabled (CohortTimetable) |

**Template:**
```blade
<div class="week-nav">
    <button class="week-arrow" onclick="{{ $prevOnclick }}" aria-label="Previous week" {{ $disabled ? 'disabled' : '' }}>&#8249;</button>
    <select class="{{ $selectClass }}" id="{{ $selectId }}" onchange="{{ $selectOnclick }}" {{ $disabled ? 'disabled' : '' }}></select>
    <button class="week-arrow" onclick="{{ $nextOnclick }}" aria-label="Next week" {{ $disabled ? 'disabled' : '' }}>&#8250;</button>
</div>
@if($showTodayBtn)
    @include('partials.ui-today-btn')
@endif
```

**File mapping:**
| File | Call |
|------|------|
| MyTimetable | `@include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)'])` |
| CohortTimetable | `@include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)', 'disabled' => true])` |
| StudentTimetable | `@include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelect', 'selectOnclick' => 'selectWeek(this.value)'])` |
| ReplacementHome | `@include('partials.ui-week-nav', ['prevOnclick' => 'prevWeekFilter()', 'nextOnclick' => 'nextWeekFilter()', 'selectId' => 'weekFilter', 'selectOnclick' => 'weekFilterChanged(this.value)', 'showTodayBtn' => false])` |
| ReplacementArrangement | `@include('partials.ui-week-nav', ['prevOnclick' => 'prevWeek()', 'nextOnclick' => 'nextWeek()', 'selectId' => 'weekSelector', 'selectOnclick' => 'onWeekChange()', 'selectClass' => 'selector-dropdown', 'showTodayBtn' => false])` |
| RequestHistory | `@include('partials.ui-week-nav', ['prevOnclick' => 'prevWeekFilter()', 'nextOnclick' => 'nextWeekFilter()', 'selectId' => 'weekFilter', 'selectOnclick' => 'weekFilterChanged(this.value)', 'showTodayBtn' => false])` |

### 2.3 `partials/ui-empty-state.blade.php`

**Replaces:** `<div class="empty-state">…</div>` in 5 templates (all except Arrangement).

**Params:**
| Param | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `$title` | string | yes | — | Empty state title text |
| `$text` | string | no | `''` | Description text |
| `$icon` | string | no | calendar SVG | SVG string. Defaults to standard calendar icon. |
| `$id` | string | no | `'emptyState'` | Wrapper div ID |
| `$ctaLabel` | string | no | `null` | If provided, renders a CTA button |
| `$ctaOnclick` | string | no | `null` | CTA button onclick handler |
| `$ctaStyle` | string | no | `'display:none'` | Inline style for CTA button (hidden by default, toggled via JS) |

**Default icon SVG:**
```html
<svg class="empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
    <line x1="16" y1="2" x2="16" y2="6"/>
    <line x1="8" y1="2" x2="8" y2="6"/>
    <line x1="3" y1="10" x2="21" y2="10"/>
</svg>
```

**Template:**
```blade
<div class="empty-state" id="{{ $id }}" style="display:none">
    {!! $icon !!}
    <h3 class="empty-title">{{ $title }}</h3>
    @if($text)
        <p class="empty-text">{{ $text }}</p>
    @endif
    @if($ctaLabel)
        <button class="empty-cta" onclick="{{ $ctaOnclick }}" style="{{ $ctaStyle }}">{{ $ctaLabel }}</button>
    @endif
</div>
```

**File mapping:**
| File | Call |
|------|------|
| MyTimetable | `@include('partials.ui-empty-state', ['title' => 'No classes this week', 'text' => 'All classes for this week have been cancelled.'])` |
| CohortTimetable | `@include('partials.ui-empty-state', ['title' => 'Select a faculty first', 'text' => 'Choose a faculty, then pick a cohort to view its weekly timetable.'])` |
| StudentTimetable | `@include('partials.ui-empty-state', ['title' => 'No classes this week', 'text' => 'All classes for this week have been cancelled.'])` |
| ReplacementHome | `@include('partials.ui-empty-state', ['title' => 'No classes currently require replacement arrangements.', 'text' => 'Try adjusting your search or filter criteria.'])` |
| RequestHistory | `@include('partials.ui-empty-state', ['title' => "You haven't submitted any replacement requests for this semester.", 'text' => 'Submit a replacement request for any conflicted class.', 'ctaLabel' => 'Submit a Replacement Request', 'ctaOnclick' => "window.location.href='/replacement-arrangement'"])` |

### 2.4 `partials/ui-grid-table.blade.php`

**Replaces:** `<div class="grid-wrapper"><div class="grid-scroll">…<table>…</table></div></div>` in all 6 templates.

**Params:**
| Param | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `$scrollId` | string | no | `'gridScroll'` | ID for the scroll wrapper div |
| `$tableId` | string | no | `'timetable'` | ID for the `<table>` element |
| `$headId` | string | no | `'tableHead'` | ID for `<thead>` |
| `$bodyId` | string | no | `'tableBody'` | ID for `<tbody>` |

**Template:**
```blade
<div class="grid-wrapper">
    <div class="grid-scroll" id="{{ $scrollId }}">
        <table class="timetable" id="{{ $tableId }}">
            <thead id="{{ $headId }}"></thead>
            <tbody id="{{ $bodyId }}"></tbody>
        </table>
    </div>
</div>
```

**File mapping:** All 6 files call `@include('partials.ui-grid-table')` with default IDs. No params needed for most pages.

### 2.5 `partials/ui-class-detail-modal.blade.php`

**Replaces:** `<div class="modal-overlay" id="…Modal">…</div>` in 3 timetable templates.

**Params:**
| Param | Type | Required | Default | Notes |
|-------|------|----------|---------|-------|
| `$modalId` | string | no | `'classModal'` | ID for the modal overlay div |
| `$overlayOnclick` | string | no | `'closeModalOutside(event)'` | onclick for overlay backdrop |

**Uses `@hasSection`/`@yield('modal-footer')`** for footer content. If no section provided, renders a simple Close button.

**Template:**
```blade
<div class="modal-overlay" id="{{ $modalId }}" style="display:none" onclick="{{ $overlayOnclick }}">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modalTitle">Class Details</span>
            <span class="modal-status-badge" id="modalStatusBadge">Normal</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            @hasSection('modal-footer')
                @yield('modal-footer')
            @else
                <button class="btn-close-modal" onclick="closeModal()">Close</button>
            @endif
        </div>
    </div>
</div>
```

**File mapping:**
| File | Call |
|------|------|
| MyTimetable | `@include('partials.ui-class-detail-modal')` + `@section('modal-footer')` with Replace Now + Cancel + Close buttons |
| StudentTimetable | `@include('partials.ui-class-detail-modal')` + `@section('modal-footer')` with Close button only |
| CohortTimetable | Uses `id="eventModal"` and static fields in body — `@include('partials.ui-class-detail-modal', ['modalId' => 'eventModal', 'overlayOnclick' => 'if(event.target===this)closeModal()'])` |

## 3. CSS extractions → `theme.css`

### 3.1 Badge variants (from CohortTimetable)

**Token mapping (using existing theme.css tokens):**

| Class | Background | Text | Source |
|-------|-----------|------|--------|
| `.badge-normal` | `var(--color-secondary)` | `var(--color-on-secondary)` | Already uses tokens (no change) |
| `.badge-replacement` | `var(--color-primary)` | `var(--color-on-primary)` | Was hardcoded `#d4a017`/`#b8860b` → converted to primary |
| `.badge-pending` | `var(--color-tertiary)` | `var(--color-on-tertiary)` | Already uses tokens (no change) |
| `.badge-conflict` | `var(--color-error)` | `var(--color-on-error)` | Already uses tokens (no change) |

**CSS to extract:**
```css
.badge-normal { background: var(--color-secondary); color: var(--color-on-secondary); }
.badge-replacement { background: var(--color-primary); color: var(--color-on-primary); }
html.light .badge-replacement { background: var(--color-primary-dark); color: var(--color-on-primary); }
.badge-pending { background: var(--color-tertiary); color: var(--color-on-tertiary); }
.badge-conflict { background: var(--color-error); color: var(--color-on-error); }
```

### 3.2 Modal footer modifiers — NOT extracted (kept inline in MyTimetable)

**Decision:** `.modal-footer-left` / `.modal-footer-right` are defined inline in MyTimetable's `<style>` block. They are **NOT** extracted to `theme.css` because RequestHistory uses these same class names in its HTML but has no CSS definitions for them — making the rules global would change RequestHistory's layout (add flex + gap where none existed). Kept inline to preserve visual identity of both pages.

### 3.3 Button variants — NOT extracted (kept inline)

**Decision:** `.btn-outline` and `.btn-danger` have **incompatible definitions** across RequestHistory and Arrangement:
- RequestHistory: standalone classes with full padding/border/radius
- Arrangement: modifiers of `.btn` base class, inheriting padding/radius

Merging would break Arrangement's button styling. These remain as inline `<style>` in each template. Extraction deferred to Phase 2 or a future change when button styles are unified.

## 4. Sequencing

1. Add CSS to `theme.css` first (badge variants; btn variants stay inline, modal-footer modifiers stay inline)
2. Create 5 partial files
3. Refactor templates one at a time (start with simplest: ReplacementArrangement → RequestHistory → ReplacementHome → MyTimetable → CohortTimetable → StudentTimetable)
4. Remove inline CSS from each template after partial is wired (except btn-outline/btn-danger which stay inline)
5. Verify each route after refactoring

## 5. Risks

| Risk | Mitigation |
|------|-----------|
| Badge hex-to-token changes visual appearance | `.badge-replacement` switches from amber to primary blue — verify with user before applying |
| Week-nav partial breaks onclick handlers | Each page passes its own handler names — no shared JS logic |
| Modal @slot doesn't work as expected | Use `@hasSection`/`@yield` pattern (standard Laravel Blade) |
| CSS extraction conflicts with existing theme.css rules | Check theme.css for existing `.badge`, `.modal-footer` before adding |
| Button variants incompatible across pages | Kept inline — extraction deferred to Phase 2 |
