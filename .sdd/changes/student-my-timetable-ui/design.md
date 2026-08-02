# Design: Student My Timetable UI

*frozen baseline: `proposal.md` (Batch 1).*

## 1. Technical Approach

The page is a **view-only weekly timetable** that mirrors the lecturer `MyTimetable` grid visually but reads its data from the canonical `MockData` source (introducing the first real consumption of `MockData.cohortTimetable`) and strips every lecturer write-action. It also acts as the trigger for the rule-#6 promotion of all duplicated timetable CSS into shared files, refactoring the two existing pages in the same pass.

### 1.1 Layout inheritance & partials (OOP — §10.1, §10.2)
```
@extends('layouts.ui-template', ['activeNav' => 'my-timetable', 'navItems' => [...3 items...], 'notifCount' => MockData.studentTimetable.notificationCount])
  @section('page-styles')   → page-local overrides ONLY (.modal-footer justify-content is NOT needed here — single Close button → uses shared flex-end)
  @section('content')
     .page-header           → title "My Timetable" + .semester-chip (semester) + .semester-chip (cohort "RSD3(S1)G2") + .page-desc
     .semester-progress     → progress bar under semester chip (§1.18)
     .semester-bar          → ‹ / week <select> / › / Today button (§1.9)  (NO faculty/cohort dropdowns — cohort is fixed)
     .week-subtitle         → "Week 3 of 14 · 15 Sep – 21 Sep" (§1.10)
     .heatmap-bar           → 14 clickable week squares (§1.12)
     .grid-wrapper > .grid-scroll > table.timetable  +  .empty-state (§1.13, hidden by default)
     @include('partials.ui-legend-bar')                                  ← NEW shared partial (replaces 3 inline copies)
     @include('partials.ui-summary-bar', [5 cards: card-total / card-hours("Class Hours") / card-replacement / card-pending / card-conflict])
     view-only modal (Close ✕ only) + status-timeline for pending (§1.15)
  @section('page-scripts')   → render logic ONLY; reads window.MockData.* (no data declared inline); keyboard nav (§1.14); dynamic notif (§1.16)
```
Shared modules consumed: `layouts/ui-template`, `partials/ui-nav-bar` (now parametrized), `partials/ui-summary-bar`, `partials/ui-legend-bar` (new), `public/css/theme.css` (all grid/legend/summary/modal CSS now lives here), `public/js/ui-common.js` (`to12h`, `add30min`, `hours`, `closeOnEsc`, `closeOnOverlayClick`, `updateWeekArrows`, `updateIcon`, `toggleTheme`), `public/js/mock-data.js`.

### 1.2 Data source — `MockData.cohortTimetable` (RSD3G2), not `MockData.myTimetable`
`MockData.myTimetable` is the lecturer's mixed teaching schedule (DFT2/DSF2/CSF2/RAF2/RBU2/DMF2 — irrelevant to one student). The RSD3(S1)G2 cohort schedule lives in:
- `MockData.cohortTimetable.rsd3g2Base` — 7 base weekly events (`BMIT7070/7071/7072/7073/7074/7075/8080`).
- `MockData.cohortTimetable.rsd3g2Flags` — per-week `{week: [[code, status, remarks], ...]}` overrides (status ∈ `replacement` | `pending`).

The page reconstructs the 14-week schedule exactly as `CohortTimetable` does today (the same reconstruction algorithm), but **reads from `MockData` instead of inline arrays** — making the student page the pilot consumer and establishing the pattern for the eventual migration of `CohortTimetable` itself.

### 1.3 Read-only / local-copy discipline (§10.3)
`MockData` is immutable shared state. The page builds a **local `eventsByWeek` object** once on load:
```
const eventsByWeek = {};                       // local, page-private
for (let w = 0; w < MockData.semester.weeks; w++) {
    eventsByWeek[w] = MockData.cohortTimetable.rsd3g2Base.map(c => ({ ...c, status: 'normal', remarks: '' }));  // spread = shallow copy per event
}
Object.entries(MockData.cohortTimetable.rsd3g2Flags).forEach(([w, list]) => {
    // w is string-coerced by Object.entries; eventsByWeek[w] coerces back — works either way
    list.forEach(([code, status, remarks]) => {
        const ev = eventsByWeek[w]?.find(e => e.code === code);
        if (ev) { ev.status = status; ev.remarks = remarks;
                  if (status === 'pending') { ev.requestedAt = '01 Sep 2026, 09:15 AM'; ev.requestedBy = ev.lecturer; } }
    });
});
```
Per-week reads then take a further `eventsByWeek[currentWeek].slice()` if any render needs a transient sort/filter copy (the cancelled filter is non-mutating — see 1.4 — so this is belt-and-braces).

### 1.4 Cancelled-filter pipeline
```
const weekEvents = (eventsByWeek[currentWeek] || []);
const visibleEvents = weekEvents.filter(e =>
    e.status !== 'cancelled' &&
    !(MockData.studentTimetable.cancelledFlags[currentWeek] || []).includes(e.code)
);
```
`visibleEvents` (not `weekEvents`) feeds **both** the grid `buildTimetable()` and `updateSummary()`. This guarantees cancelled classes never render and never count, with no legend swatch or summary card for them.

`cancelledFlags` keys are 0-indexed (same as `rsd3g2Flags`), aligned with `currentWeek`. JS object property access (`obj[key]`) coerces numeric keys to strings automatically, so `cancelledFlags[currentWeek]` works regardless of key representation.

### 1.5 Conflict derivation (new `MockData.holidays` consumer)
`MockData.holidays` = `[{ week: 3, dayIndex: 3, label: 'Public Holiday' }]`. The day-builder tags `days[d].holiday = true` when `loopW+1 === h.week && d === h.dayIndex` — where `loopW` is the 0-indexed week loop counter inside the `weekData` construction pass (runs at build time, not at user-week-selection time). Note: `rsd3g2Flags` keys are 0-indexed; `MockData.holidays.week` is 1-indexed to match the `Week N` label — reconcile by `+1`. An event whose `days[event.di].holiday === true` renders with the `event-public-holiday` class + opens the modal with status badge `conflict`. This mirrors `CohortTimetable`'s render path but sources the rule from `MockData.holidays` (declarative) rather than a hardcoded `d===3 && w===3` inline.

### 1.6 Week calendar + chip from `MockData.semester`
```
const start = new Date(MockData.semester.startDate);   // 2026-08-31
// build weekData[0..13] from start (same algorithm as today, just sourced start)
// chip: <span class="semester-chip" id="semesterChip"></span>  → textContent = MockData.semester.chipText
currentWeekIndex() → Math.floor((today - start) / 7d) clamped 0..(MockData.semester.weeks - 1)
```
Week persistence key: `studentMyTimetableWeek` (own localStorage key; does not collide with MyTimetable's `myTimetableWeek`).

### 1.7 Modal — view-only
Static Blade shell mirroring `MyTimetable`'s modal markup (`#classModal` overlay + `.modal` + `.modal-header[title+statusBadge+close]` + `.modal-body` + `.modal-footer[right: Close only]`). `openModal(event)` builds the field list dynamically (same field array as MyTimetable minus Cohort/Total Students; inserts Requested At/By when `status==='pending'`). Does **not** read `event.meta.*` (§1.7: empty pocket, backend-ready). No `btnReplaceNow`, no `btnCancelClass`, no `#cancelConfirmOverlay`. Close via shared `closeOnEsc(closeModal)` + an inline `closeModalOutside(e)` wrapper on the overlay's `onclick` — same pattern as MyTimetable (`closeModalOutside(e) { closeOnOverlayClick(e, closeModal); }`).

### 1.8 Nav parametrization
`partials/ui-nav-bar.blade.php` gains a `$navItems` default at top:
```php
@php
  $items = $navItems ?? [
    ['key'=>'dashboard','label'=>'Dashboard','href'=>'/dashboard'],
    ['key'=>'my-timetable','label'=>'My Timetable','href'=>'/my-timetable-ui'],
    ['key'=>'cohort-timetables','label'=>'Cohort Timetables','href'=>'/cohort-timetable-ui'],
    ['key'=>'replacement-arrangement','label'=>'Replacement Arrangement','href'=>'/replacement-home-ui'],
    ['key'=>'replacement-history','label'=>'Replacement History','href'=>'/my-request-history-ui'],
  ];
@endphp
@foreach ($items as $it)
  <a class="nav-item {{ $activeNav === $it['key'] ? 'active' : '' }}" href="{{ $it['href'] }}">{{ $it['label'] }}</a>
@endforeach
```
Existing pages pass only `['activeNav' => ...]` → unchanged behavior. Student route passes `['activeNav'=>'my-timetable', 'navItems'=>[3 items with student hrefs]]`.

### 1.9 Today button
Icon button (target/bullseye SVG) inside `.semester-bar`, placed after the `›` arrow:
```html
<button class="today-btn" id="todayBtn" title="Jump to current week" aria-label="Jump to current week">
    <!-- inline SVG bullseye icon -->
</button>
```
`onclick` handler: sets `currentWeek = currentWeekIndex()`, rebuilds grid, saves to `studentMyTimetableWeek` localStorage, updates select + arrows. CSS in `theme.css` — styled like `.week-arrow` (same size/shape, different icon).

### 1.10 Week subtitle
```html
<div class="week-subtitle" id="weekSubtitle"></div>
```
Between semester bar and grid. Updated on week change:
```
weekSubtitle.textContent = `Week ${currentWeek+1} of ${MockData.semester.weeks} · ${fmt(weekData[currentWeek].start)} – ${fmt(weekData[currentWeek].end)}`;
```
`fmt(d)` is a page-local helper: `function fmt(d) { return d.getDate() + ' ' + d.toLocaleString('en', { month: 'short' }); }` — formats as `d-MMM` (e.g., "1 Sep"), no year, no leading zero. CSS: `.week-subtitle { font-size: 0.85rem; color: var(--color-on-surface-variant); margin: 4px 0 8px; }`.

### 1.11 Week select date ranges
Modify the `<option>` building loop to set:
```
option.textContent = `Week ${i+1} (${fmt(weekData[i].start)} – ${fmt(weekData[i].end)})`;
```
Uses same `fmt(d)` helper as §1.10. The `weekData` rebuild (proposal item 10) produces per-week start/end Date objects — this surfaces them in the dropdown.

### 1.12 Mini heatmap bar
```html
<div class="heatmap-bar" id="heatmapBar" role="tablist" aria-label="Week overview"></div>
```
Above the grid, below the week subtitle. Builds 14 `<button class="heatmap-cell">` elements:
```js
for (let w = 0; w < MockData.semester.weeks; w++) {
    const weekEvents = eventsByWeek[w] || [];
    let color = 'var(--color-surface-variant)'; // grey = no events
    if (weekEvents.length > 0) {
        const statuses = new Set(weekEvents.map(e => e.status));
        if (statuses.has('conflict')) color = 'var(--color-error)';
        else if (statuses.has('pending')) color = 'var(--color-tertiary)';
        else if (statuses.has('replacement')) color = 'var(--color-primary)';
        else color = 'var(--color-secondary)';
    }
    // active cell gets border/outline
    cell.classList.toggle('active', w === currentWeek);
}
```
**Priority**: red (conflict) > yellow (pending) > blue (replacement) > green (all normal) > grey (no events). Click handler = same as week select change (`currentWeek = w; rebuild();`). CSS: `.heatmap-bar { display: flex; gap: 4px; margin-bottom: 8px; }` `.heatmap-cell { width: 100%; aspect-ratio: 1; border-radius: 4px; border: 2px solid transparent; cursor: pointer; }` `.heatmap-cell.active { border-color: var(--color-on-surface); }`. The `active` cell toggle runs unconditionally (outside the color conditional) — an empty week can still be "active" so the user sees which week they're on. No responsive breakpoint needed for mock phase (14 cells flex at ~30px each ≈ 420px, fits tablet+).

### 1.13 Empty state
The existing `.empty-state` in `theme.css` (with `.empty-icon`, `.empty-title`, `.empty-text` subclasses) is reused — no new CSS class needed. §4.13 is updated to reflect this.
```html
<div class="empty-state" id="emptyState" style="display:none;">
    <div class="empty-icon"><!-- inline SVG calendar-x icon --></div>
    <div class="empty-title">No classes this week</div>
    <div class="empty-text">All classes for this week have been cancelled.</div>
</div>
```
Inside `.grid-wrapper`, hidden by default. After computing `visibleEvents`: if `visibleEvents.length === 0`, hide `table.timetable` and show `#emptyState`; otherwise show table and hide empty state.

### 1.14 Keyboard navigation
```js
document.addEventListener('keydown', (e) => {
    // Don't intercept when select or modal is focused
    if (e.target.tagName === 'SELECT' || document.getElementById('classModal')?.classList.contains('open')) return;
    if (e.key === 'ArrowLeft') { /* prev week */ }
    if (e.key === 'ArrowRight') { /* next week */ }
    if (e.key === 'Enter' && e.target.classList.contains('event-block')) {
        const event = e.target.__eventData; // set via element.__eventData = event in buildTimetable
        if (event) openModal(event);
    }
});
```
Arrow listeners on `document`; when `<select>` is focused, browser consumes arrows natively — no conflict. Each `.event-block` gets `tabindex="0"` and `__eventData = event` during `buildTimetable()`. CSS: `.event-block:focus-visible { outline: 2px solid var(--color-primary); outline-offset: 2px; }`.

### 1.15 Modal request-status timeline
Inside `openModal(event)`, when `status === 'pending'`, prepend to `.modal-body`:
```html
<div class="status-timeline">
    <div class="step completed">Submitted ✓</div>
    <div class="step active">Under Review</div>
    <div class="step">Awaiting Replacement</div>
</div>
```
CSS: `.status-timeline { display: flex; gap: 8px; margin-bottom: 16px; }` `.step { flex: 1; text-align: center; padding: 8px 4px; border-radius: 6px; background: var(--color-surface-variant); font-size: 0.8rem; }` `.step.completed { background: var(--color-secondary-container); }` `.step.active { background: var(--color-tertiary-container); font-weight: 600; }`. This is a visual-only approximation — steps will be refined when the approval pipeline lands in Sprint 3.

### 1.16 Dynamic notification count
**Required layout change** in `layouts/ui-template.blade.php` — change the `@include` call from:
```php
@include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? ''])
```
to:
```php
@include('partials.ui-nav-bar', ['activeNav' => $activeNav ?? '', 'notifCount' => $notifCount ?? 3])
```
This forwards `$notifCount` (passed via `@extends`) to the partial. The nav partial uses `$notifCount` for the badge:
```html
<span class="notif-badge" id="notifBadge">{{ $notifCount ?? 3 }}</span>
```
The student page passes `['notifCount' => MockData.studentTimetable.notificationCount]` via `@extends`. Existing pages pass no `$notifCount` → default `3` kicks in. Guard: if the `#notifBadge` element doesn't exist (e.g., on pages that hide nav), skip silently.

### 1.17 Event `.meta` pocket
In the reconstruction loop (§1.3), add `meta: {}` to each spread copy:
```js
eventsByWeek[w] = MockData.cohortTimetable.rsd3g2Base.map(c => ({
    ...c, status: 'normal', remarks: '', meta: {}
}));
```
Currently empty — the modal does not read `event.meta.*`. When the backend later adds fields, the modal can read them without a data-structure refactor.

### 1.18 Semester progress bar
```html
<div class="semester-progress" id="semesterProgress">
    <div class="progress-label" id="progressLabel"></div>
    <div class="progress-track"><div class="progress-fill" id="progressFill"></div></div>
</div>
```
Under the semester chip in page header. Updated on week change:
```js
const pct = ((currentWeek + 1) / MockData.semester.weeks) * 100;
progressFill.style.width = pct + '%';
progressLabel.textContent = `Week ${currentWeek+1} of ${MockData.semester.weeks}`;
```
CSS: `.semester-progress { margin: 4px 0; }` `.progress-track { height: 4px; background: var(--color-surface-variant); border-radius: 2px; overflow: hidden; }` `.progress-fill { height: 100%; background: var(--color-primary); border-radius: 2px; transition: width 0.3s; }` `.progress-label { font-size: 0.75rem; color: var(--color-on-surface-variant); margin-bottom: 2px; }`.

### 1.19 Week-change transition
CSS-only animation on the grid container. On week change, a `.grid-transitioning` class is toggled on `.grid-scroll`:
```css
.grid-scroll { transition: opacity 0.1s ease; }
.grid-scroll.grid-transitioning { opacity: 0; }
```
In JS, during week change: add class → `requestAnimationFrame` → remove class (triggers fade-in). Total visual duration ~300ms. No layout shift; purely opacity.

### 1.20 Event hover tooltip
CSS `::after` pseudo-element on `.event-block` using `data-name` and `data-venue` attributes:
```html
<div class="event-block" data-name="Object-Oriented Programming" data-venue="Lab 3.01" ...>
    <span class="ev-code">BMIT7070</span> ...
</div>
```
```css
.event-block { position: relative; }
.event-block::after {
    content: attr(data-name) ' · ' attr(data-venue);
    position: absolute; bottom: 100%; left: 50%; transform: translateX(-50%);
    background: var(--color-inverse-surface); color: var(--color-on-inverse-surface);
    padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; white-space: nowrap;
    opacity: 0; pointer-events: none; transition: opacity 0.15s; z-index: 10;
}
.event-block:hover::after { opacity: 1; }
```
In `buildTimetable()`, set `data-name` and `data-venue` on each `.event-block` element from `event.name` and `event.venue`.

### 1.21 Copy course code
Click handler on `.ev-code` elements inside event blocks:
```js
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('ev-code')) {
        navigator.clipboard.writeText(e.target.textContent).then(() => {
            const toast = document.getElementById('copyToast');
            toast.textContent = `Copied ${e.target.textContent}`;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 1500);
        });
    }
});
```
Toast element in Blade: `<div class="copy-toast" id="copyToast"></div>` at bottom of page.
CSS: `.copy-toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: var(--color-inverse-surface); color: var(--color-on-inverse-surface); padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; opacity: 0; transition: opacity 0.2s; pointer-events: none; z-index: 100; }` `.copy-toast.show { opacity: 1; }`.
`.ev-code` gets `cursor: pointer` to indicate clickability. Tooltip is irrelevant on mobile — `.event-block` is hidden per responsive card layout (mock phase: no mobile breakpoint needed; desktop-only).

### 1.22 Smooth scroll to grid
In the Today button handler (§1.9) and heatmap click handler (§1.12), after setting `currentWeek` and rebuilding the grid, add:
```js
document.querySelector('.grid-wrapper').scrollIntoView({ behavior: 'smooth', block: 'start' });
```
One line, no new CSS. Ensures the timetable is visible after a jump, especially on screens where the grid is below the fold.

## 2. Data Flow
```
public/js/mock-data.js (window.MockData — READ ONLY)
   ├─ MockData.semester            → chip text + weekData calendar (start, weeks)
   ├─ MockData.holidays            → day.holiday flag → conflict derivation
   ├─ MockData.cohortTimetable.rsd3g2Base   ┐
   └─ MockData.cohortTimetable.rsd3g2Flags  ┴→ local eventsByWeek[14]
   └─ MockData.studentTimetable.cancelledFlags → cancelled-filter → visibleEvents
                                                       ├→ buildTimetable()  (table.timetable)
                                                       └→ updateSummary()   (5 cards)
onclick event → openModal(event) → field array → #classModal
prev/next/select week → currentWeek → rebuild + saveStud​entMyTimetableWeek
```

## 3. Dependencies
- `mock-data.js` — +`MockData.studentTimetable` (with `notificationCount`); §2.2 comment updated.
- `ui-common.js` — read-only (`hours`, `to12h`, `add30min`, `closeOnEsc`, `closeOnOverlayClick`, `updateWeekArrows`); no changes.
- `theme.css` — +promoted CSS blocks (see §4) + new enhancement CSS (heatmap, progress-bar, empty-state, status-timeline, keyboard-focus, today-btn, week-subtitle, grid-transition, event-tooltip, copy-toast).
- `partials/ui-nav-bar.blade.php` — +`$navItems` param + `$notifCount` param + `id="notifBadge"` on badge span.
- `layouts/ui-template.blade.php` — forward `$notifCount ?? 3` to nav partial `@include`.
- `partials/ui-summary-bar.blade.php` — unchanged (consumed as-is).
- `partials/ui-legend-bar.blade.php` — NEW.
- No new PHP/migrations/Livewire components (frontend mock phase).

## 4. Promoted to shared (rule-#6 record — mandated by proposal)

| # | What | From | To | Notes |
|---|------|------|-----|-------|
| 4.1 | Legend bar **markup** (4 items, canonical token swatches) | MyT + Cohort inline | NEW `partials/ui-legend-bar.blade.php`; `@include` on MyT, Cohort, Student | swatch colors via `var(--color-secondary/primary/tertiary/error)` |
| 4.2 | Legend **CSS** (`.legend-bar/.legend-item/.legend-swatch`) | MyT + Cohort inline | `theme.css` | — |
| 4.3 | Summary-card color CSS (`.card-total/.card-replacement/.card-pending/.card-conflict/.card-hours`) | MyT + Cohort inline | `theme.css` | — |
| 4.4 | Modal-shell CSS (`.modal-overlay/.modal/.modal-header/.modal-title/.modal-status-badge+variants/.modal-close/.modal-body/.modal-field/.field-label/.field-value/.modal-footer/.btn-close-modal/@keyframes modalIn`) | MyT + Cohort inline | `theme.css` | `.modal-footer` standardized `flex-end`; **MyT keeps 1-line `space-between` override** (footer has left+right children). Student + Cohort use `flex-end` (single Close) |
| 4.5 | Semester-bar / week-picker CSS (`.semester-bar/.week-arrow+states/.week-select+option+html.dark/.semester-bar select:not(.week-select)`) | MyT + Cohort inline | `theme.css` | `.week-select` min-width standardized **160** (was 140 in MyT, +20px doesn't break layout — verified: only `‹` / select / `›` in bar). `.semester-bar select:not(.week-select)` styles faculty/cohort dropdowns; student page does not render this element — promotion consolidates the 2 existing copies + future-proofs |
| 4.6 | Time-column CSS (`.time-col/.day-label/.date-label/.holiday-label/.today/.holiday-col/.sunday-col` + nested) | MyT + Cohort inline | `theme.css` | — |
| 4.7 | Hour-header CSS (`.hour-header/.hour-top/.hour-bottom`) | MyT + Cohort inline | `theme.css` | — |
| 4.8 | Hour-cell + event-block CSS (`.hour-cell/.sunday-slot/.holiday-slot/.event-public-holiday/.event-block+hover/active/.event-normal/.event-replacement/.event-pending/sunday+holiday overrides/.ev-code/.ev-venue/.ev-time/.ev-note/.cell-empty/.timetable tr:last-child`) | MyT + Cohort inline | `theme.css` | — |
| 4.9 | **DELETE** dead `.today-highlight` (`rgba(26,95,180,0.04)!important`) | MyT + Cohort inline | removed entirely | rule-#1 hardcoded-rgba violation; unused by either JS; NOT recreated on Student |
| 4.10 | **Today button** CSS (`.today-btn` + states) | Student only | `theme.css` | Styled like `.week-arrow`; new for enhancement item 13 |
| 4.11 | **Week subtitle** CSS (`.week-subtitle`) | Student only | `theme.css` | New for enhancement item 14 |
| 4.12 | **Heatmap bar** CSS (`.heatmap-bar/.heatmap-cell/.heatmap-cell.active`) | Student only | `theme.css` | New for enhancement item 16; colors via §10.0 tokens |
| 4.13 | **Empty state** — reuse existing `.empty-state` in `theme.css` (with `.empty-icon`, `.empty-title`, `.empty-text` subclasses). Student page renders the existing structure with a calendar-x icon, "No classes this week" title, and "All classes for this week have been cancelled." text. No new CSS needed. | existing `theme.css` | reused | New for enhancement item 17 |
| 4.14 | **Keyboard focus** CSS (`.event-block:focus-visible`) | Student only | `theme.css` | New for enhancement item 18 |
| 4.15 | **Status timeline** CSS (`.status-timeline/.step/.step.completed/.step.active`) | Student only | `theme.css` | New for enhancement item 19 |
| 4.16 | **Semester progress bar** CSS (`.semester-progress/.progress-track/.progress-fill/.progress-label`) | Student only | `theme.css` | New for enhancement item 22 |
| 4.17 | **Week-change transition** CSS (`.grid-scroll` transition declaration, `.grid-scroll.grid-transitioning`) | Student only | `theme.css` | New for enhancement item 23 |
| 4.18 | **Event hover tooltip** CSS (`.event-block::after`, `.ev-code` cursor) | Student only | `theme.css` | New for enhancement item 24 |
| 4.19 | **Copy toast** CSS (`.copy-toast/.copy-toast.show`) | Student only | `theme.css` | New for enhancement item 25 |

**Stays page-specific (intentional, not duplicated):**
- MyT: `.btn-replace-now`, `.btn-cancel-class`, `.cancel-overlay` (+ sub-classes) — lecturer action modal CSS, not used elsewhere.
- MyT: `.modal-footer-left`, `.modal-footer-right` — two-button footer wrappers (Replace-Now / Cancel-Class left, Close right); CohortTimetable and Student use single Close only, so these have no shared equivalent.
- MyT: 1-line `.modal-footer { justify-content: space-between; }` override.
- Cohort: `.badge-normal/.badge-replacement/.badge-pending/.badge-conflict` — used only in its modal body `mdlStatusBadge`. (Pre-existing `.badge-replacement` gold `#d4a017` rule-#1 violation noted for a future dedicated CohortTimetable cleanup; out of scope here.)

## 5. File-by-file change map

| File | Change |
|------|--------|
| `routes/web.php` | +1 route `GET /student-my-timetable-ui` |
| `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php` | NEW (view-only; reads `MockData.*`; render logic in `@section('page-scripts')`; includes enhancements §1.9–§1.18) |
| `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php` | legend → `@include('partials.ui-legend-bar')`; remove inline CSS blocks 4.2–4.8; delete `.today-highlight`; keep `.btn-replace-now/.btn-cancel-class/.cancel-overlay` + `.modal-footer-left/.modal-footer-right` + `.modal-footer{space-between}` override; `weekData` builder + `currentWeekIndex()` + chip → `MockData.semester` |
| `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` | legend → `@include('partials.ui-legend-bar')`; remove inline CSS blocks 4.2–4.8; delete `.today-highlight`; keep `.badge-*` page-specific; `weekData` builder + `currentWeekIndex()` + chip → `MockData.semester`. **Holiday inline rule `d===3&&w===3` NOT touched** (out of scope) |
| `resources/views/partials/ui-legend-bar.blade.php` | NEW |
| `resources/views/partials/ui-nav-bar.blade.php` | + `$navItems` param (default = current 5 + hrefs) + `$notifCount` param (default = 3) + `id="notifBadge"` on badge span |
| `resources/views/layouts/ui-template.blade.php` | forward `$notifCount ?? 3` to `@include('partials.ui-nav-bar', ...)` |
| `public/css/theme.css` | + blocks 4.2–4.8 (promoted) + blocks 4.10–4.19 (new enhancement CSS) |
| `public/js/mock-data.js` | + `MockData.studentTimetable` (with `activeCohort`, `cancelledFlags`, `notificationCount`); §2.2 holidays comment updated |
| `page-changelogs/student-my-timetable-ui-changelog.md` | populated during apply |

## 6. Mobile & Tablet View Design (rule #9 — mandatory)

### 6.1 Breakpoints
- **Tablet:** `@media (max-width: 1024px)` — stack summary cards to 2 columns, reduce grid padding
- **Mobile:** `@media (max-width: 768px)` — full card layout, stacked elements

### 6.2 Mobile Layout (≤768px)

**Page header:**
- Stack title, semester-chip, cohort-chip, and description vertically
- Reduce font sizes: `.page-title { font-size: 1.25rem; }`, `.page-desc { font-size: 0.8rem; }`

**Semester progress bar:**
- Full-width, hide `.progress-label` text (show only percentage via `::after` pseudo-element)

**Week picker (`.semester-bar`):**
- Hide prev/next arrows (`.week-arrow { display: none; }`)
- Full-width week select dropdown (`.week-select { width: 100%; min-width: 0; }`)
- Hide Today button (`.today-btn { display: none; }`)

**Week subtitle:**
- Full-width, centered text, smaller font (`.week-subtitle { font-size: 0.75rem; text-align: center; }`)

**Heatmap bar:**
- Scrollable horizontally (`.heatmap-bar { overflow-x: auto; flex-wrap: nowrap; }`)
- Smaller cells (`.heatmap-cell { min-width: 28px; height: 28px; }`)

**Timetable grid → Card layout:**
- Hide `<table.timetable>` on mobile
- Show `.card-list` container (new element, hidden on desktop)
- Each event renders as a card:
  ```html
  <div class="event-card">
      <div class="event-card-header">
          <span class="event-card-code">BMIT7070</span>
          <span class="event-card-status status-normal">Normal</span>
      </div>
      <div class="event-card-body">
          <div class="event-card-row"><span class="event-card-label">Day</span><span class="event-card-value">Monday</span></div>
          <div class="event-card-row"><span class="event-card-label">Time</span><span class="event-card-value">09:00 – 11:00</span></div>
          <div class="event-card-row"><span class="event-card-label">Venue</span><span class="event-card-value">B104</span></div>
          <div class="event-card-row"><span class="event-card-label">Lecturer</span><span class="event-card-value">Dr. Ahmad</span></div>
      </div>
  </div>
  ```
- Card CSS:
  ```css
  .card-list { display: none; }
  .event-card {
      background: var(--color-surface);
      border: 1px solid var(--color-outline);
      border-radius: var(--radius-md);
      padding: 12px;
      margin-bottom: 8px;
  }
  .event-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
  }
  .event-card-code { font-weight: 600; color: var(--color-on-surface); }
  .event-card-body { display: flex; flex-direction: column; gap: 4px; }
  .event-card-row { display: flex; justify-content: space-between; font-size: 0.85rem; }
  .event-card-label { color: var(--color-on-surface-variant); }
  .event-card-value { color: var(--color-on-surface); font-weight: 500; }
  @media (max-width: 768px) {
      .grid-scroll { display: none; }
      .card-list { display: block; }
  }
  ```

**Legend bar:**
- Wrap to 2 rows if needed (`.legend-bar { flex-wrap: wrap; gap: 8px; }`)

**Summary cards (via `@include('partials.ui-summary-bar')`):**
- Stack to 1 column on mobile (`.summary-bar { grid-template-columns: 1fr; }`)

**Empty state:**
- Full-width, centered, reduced padding (`.empty-state { padding: 24px 16px; }`)

**Modal:**
- Full-screen on mobile (`.modal { width: 100vw; height: 100vh; border-radius: 0; max-height: none; }`)
- Hide `.modal-header` close button (use footer Close only)
- Stack modal fields vertically with full width

### 6.3 Tablet Layout (769px–1024px)

**Summary cards:**
- 2-column grid (`.summary-bar { grid-template-columns: repeat(2, 1fr); }`)
- 5th card spans full width (`.summary-card:last-child { grid-column: span 2; }`)

**Timetable grid:**
- Keep table layout but reduce cell padding (`.hour-cell { padding: 2px; }`)
- Smaller font for event blocks (`.ev-code { font-size: 10px; }`, `.ev-time { font-size: 9px; }`)

**Week picker:**
- Keep arrows + dropdown but reduce dropdown width (`.week-select { min-width: 140px; }`)

### 6.4 CSS Media Queries Location
Add all mobile/tablet CSS in `@section('page-styles')` of the Blade template (page-specific, not shared to `theme.css` — the timetable card layout is unique to this page).

## 7. Known residuals (post-apply, tracked for later changes)
- MyT + Cohort inline *event* data (`eventsData` / `facultyData` + `allEvents`) still inline — full migration to `MockData` is a separate change.
- Cohort inline holiday rule (`d===3&&w===3`) still hardcoded — refactored when Cohort migrates its event data.
- Cohort `.badge-replacement` gold `#d4a017` — pre-existing rule-#1 violation, deferred.
- The 3 other pages' stale 15-Jun chip (replacement-arrangement/home, my-request-history) — deferred.
