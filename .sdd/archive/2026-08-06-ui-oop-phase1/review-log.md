# Review Log — ui-oop-phase1

## proposal.md Round 1 — 2026-08-02

### 🔴 Fixed
- Week-nav file count: 4 → 6 (all templates). Added `$showTodayBtn` param (default true) to handle files without `@include('ui-today-btn')`.
- CSS extraction scope narrowed: badge variants only (not `.status-*`), modal footer modifiers only (not base), btn variants flagged as conflicting definitions needing design.md resolution.
- Added hex-to-token conversion mandate for badge CSS (CodingMAIN §10.0.1).

### 🟡 Addressed
- `.badge` base already in `theme.css` line 514 — clarified extraction is variants only.
- `.modal-footer-left`/`.modal-footer-right` only in MyTimetable — narrowed scope.
- `.btn-outline`/`.btn-danger` have different definitions per page — flagged for design.md resolution.

### 🔴 Outstanding
- (none)

## proposal.md Round 2 — 2026-08-02

### 🔴 Fixed
- (none)

### 🟡 Addressed
- "~150 lines" metric clarified as all inline HTML/CSS duplication, not just `<style>` blocks.

### 🔴 Outstanding
- (none)

**→ proposal.md FROZEN**

## design.md Round 1 — 2026-08-02

### 🔴 Fixed
- Page header: `.page-chips` wrapper only rendered when `$chips` is non-empty; bare `<span>` for single chip (preserves 5/6 pages' layout).
- Empty state CTA: added `$ctaStyle` param (default `'display:none'`) to preserve JS toggle behavior.
- Badge tokens: mapped to existing theme.css tokens (`--color-secondary`, `--color-primary`, `--color-tertiary`, `--color-error`). Dropped non-existent `--color-warning` references.
- Badge `.badge-normal`: kept `var(--color-secondary)` (matches original).
- Week-nav RequestHistory: fixed `selectOnclick` to `'weekFilterChanged(this.value)'`.
- Button variants: dropped extraction (incompatible definitions). Kept inline.
- Modal-footer: fixed `.modal-footer-left` (no gap), `.modal-footer-right` (gap: 10px).

### 🟡 Addressed
- `$reversed` param: Arrangement's prev/next inversion is handled at the call site (JS functions already swap semantics), not in the partial. Documented.
- Grid-table: ReplacementHome gets `id="gridScroll"` by default; verified no ID-based JS selectors exist.

### 🔴 Outstanding
- (none)

## design.md Round 2 — 2026-08-02

### 🔴 Fixed
- (none)

### 🟡 Addressed
- Updated §2.5 description from `@slot` to `@hasSection`/`@yield` (matches actual template).
- Noted `onWeekChange()` reads select from DOM directly (no `this.value` needed).

### 🔴 Outstanding
- (none)

**→ design.md FROZEN (re-frozen after modal-footer scope correction)**

## tasks.md Round 1 — 2026-08-02

### 🔴 Fixed
- Modal-footer extraction: removed from Task 1 and Task 7; kept inline in MyTimetable (RequestHistory uses same class names without CSS — extracting would break its layout). design.md §3.2/§4 updated to match.

### 🟡 Addressed
- Task 2 bullet 4 split into two clear bullets (remove HTML span vs keep JS textContent).
- Task 9 added badge color change as the sole expected visual diff.

### 🔴 Outstanding
- (none)

## tasks.md Round 2 — 2026-08-02

### 🔴 Fixed
- (none)

### 🟡 Addressed
- (none — all Round 1 items resolved)

### 🔴 Outstanding
- (none)

**→ tasks.md FROZEN**
