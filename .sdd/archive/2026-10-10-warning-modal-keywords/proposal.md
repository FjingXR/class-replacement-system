# Proposal — warning-modal-keywords

Created: 2026-10-09 · Status: draft (Batch 1, unfrozen)
Explore baseline: [explore-brief.md](./explore-brief.md)

## Why

The app warns users about destructive/irreversible actions in modals whose key words carry no
visual weight — the sentence "This action cannot be undone." renders identically to small talk.
Meanwhile the same concepts are styled inconsistently across the app: two different sentences
describe the same selection-clearing guard, 13 modal-close buttons have no tooltip while one
identical button does, 13 controls use native `title=` tooltips while the rest of the app uses
the `data-tip` system, and one cancel path is silently broken (Yes button no-ops) with dead
code sitting next to it.

Users must be able to see, at a glance, WHICH words in a warning carry consequences. This
change establishes one design language for that and applies it everywhere warnings appear.

## Design language (the one rule)

> **Red + bold + UPPERCASE keyword = irreversible or attention-needing consequence.**
> **Bold + UPPERCASE keyword in default color = informational emphasis.**
> Red is reserved. If an action is undoable or recoverable, it gets no red keyword.

## Scope — IN

### A. Warning-modal keywords + line breaks (replacement-arrangement)

1. **Leave / go-back guard** (`navigateTo`, `goBack`): "will be lost" → keyword **LOST**
   (red+bold+caps); `<br>` before "Are you sure you want to leave/go back?".
2. **Clear All guard**: `<strong>ALL</strong>` → red keyword **ALL**; `<br>` after the "?"
   (before "You can undo this from the toast that appears.").
3. **Change guard ×2** (`confirmChangeWithSelection` + the second inline copy): `<br>` before
   "Continue?"; **no keyword** (selection is re-selectable → recoverable → no red).

### B. Cancel-request modals (my-request-history)

4. Single (static markup), quick-cancel (JS-built), batch (JS-built): "cannot be undone" →
   keyword **UNDONE** (red+bold+caps). Sentence wording unchanged.

### C. Dead code + bug fix (my-request-history)

5. **Delete `confirmCancelRequest()`** — native `confirm()`+`alert()`, zero callers.
6. **Fix detail-modal Cancel Request**: `openCancelConfirm()` never sets `pendingCancelId`, so
   the modal's "Yes, Cancel Request" silently does nothing. Fix: `openCancelConfirm(id)` stores
   the id; the detail-modal button passes the current request's id.

### D. Confirm-modal buttons (replacement-arrangement `showConfirmModal`)

7. **Conditional danger styling** — `showConfirmModal(title, bodyHtml, callback, opts)` gains
   an options param (`opts.danger`, `opts.confirmLabel`). Of the **7 call sites** (verified:
   L1675 change-guard, L1765 change-guard, L1818 "No Selection", L1836 "Confirm Your
   Selection", L1865 Clear All, L1898 leave, L1917 back), exactly the **5 warning call sites**
   (leave, back, Clear All, both change-guards) pass `danger: true`; `modalConfirmBtn` becomes
   `btn-danger` only when `opts.danger` is set, else stays `btn-primary`.
8. **Per-action confirm labels** for the same 5 warning sites: "Yes, Leave Page" / "Yes, Go
   Back" / "Yes, Clear All" / "Yes, Change" (both change-guards). The 2 non-warning call sites
   are explicitly EXCLUDED: "Confirm Your Selection" (submit is a positive go-ahead, not a
   warning) and "No Selection" (informational) keep `btn-primary` + default "Confirm" label.
   Omitted `opts.confirmLabel` falls back to "Confirm".

### E. Summary-card flip-back keywords (shared partial + inline overrides, 9 pages)

9. Attention cards (`card-conflict`, `card-pending`, `card-rejected`): one keyword per
   description, **red+bold+caps**.
10. All other card descriptions: one keyword per description, **bold+caps, default color**.
11. Inline `'description'` overrides on including pages get the same treatment; audit so no
    card falls back to a bare label. Card FRONTs untouched.

### F. System-wide tooltip parity

12. Convert **13** native `title=` tooltips to `data-tip` (audited 2026-10-09): 2× print
    "Coming soon" (ui-week-nav, venue page), ★ "Toggle favourite" (arrangement dropdown —
    standalone element, NOT the shared `updateFavStar` star), "Reset all filters", 8×
    filter-chip ✕ "Remove" (my-request-history ×4 incl. the Exclude-Completed chip;
    request-approval ×4 incl. the Urgency chip), and 1× "Toggle theme" on the login page's
    standalone theme-toggle (`layouts/login-template.blade.php:342` — separate from the
    nav-bar toggle, which already uses `data-tip`). Text unchanged.
13. Add `data-tip="Close"` to all 13 live `modal-close` buttons (partials: class-detail,
    cancel-class, logout; pages: my-request-history ×3, request-approval ×3, replacement-home
    ×2, arrangement ×1, venue ×1 — all 13 occurrences are live; the commented-out markup in
    replacement-home is the `kbShortcutsBtn` open-button, not a modal-close).

### G. Styling mechanism

14. Two classes in `theme.css` (tokens only, no hardcoded hex/rgb): `warn-keyword`
    (red+bold+caps) and `info-keyword` (default color+bold+caps via `text-transform`). Defined
    once; consumed by A/B/E. Exact names finalized in design.md.

## Scope — OUT (explicit)

- Logout modal copy; Breeze settings pages (`pages/settings/*`, auth pages); browser
  `beforeunload` dialog (unstyleable); card fronts; keywords inside `data-tip` (plain text by
  design); touch/tap-to-flip for cards; any backend/PHP behavior.
- `resources/views/login-UI-design-template.blade.php` (repo root) — route-less standalone
  mockup, deliberately untouched per the archived `oop-blade-refactor` decision (its L443
  `title="Toggle theme"` is therefore NOT part of the 13-conversion count).
- Changing warning sentence WORDING (styling/markup only — keeps specs assertable). Exception:
  the two change-guard sentences may optionally be unified to one wording (declarative, same
  meaning).

## Affected files

- `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` (A, D, F)
- `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` (B, C, F)
- `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php` (F only)
- `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php` (F only)
- `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` (F only)
- `resources/views/layouts/login-template.blade.php` (F only — theme-toggle title)
- `resources/views/partials/ui-week-nav.blade.php`, `ui-class-detail-modal.blade.php`,
  `ui-cancel-class-modal.blade.php`, `ui-logout-modal.blade.php` (F only)
- `resources/views/partials/ui-summary-bar.blade.php` + pages with inline descriptions (E)
- `public/css/theme.css` (G)

## Success criteria

1. Exactly one `warn-keyword` span per irreversible modal (leave, back, Clear All, the three
   cancel-request modals); **zero** `warn-keyword` spans in the change-guard modals, the submit
   confirm, the "No Selection" modal, tooltips, and informational card descriptions;
   `info-keyword` spans (bold+caps, default color) present in non-attention card descriptions.
2. Cancel-request from the detail modal actually cancels (bug fix) — same modal/undo behavior
   as quick-cancel.
3. All **13** audited native `title=` tooltips converted to `data-tip`; a repo-wide grep of
   `resources/views/{ui-design-templates,partials,layouts}/*.blade.php` for app-UI `title=`
   tooltips returns only Breeze-owned `:title=` component props; every live modal-close has
   `data-tip="Close"`.
4. All existing Playwright specs pass (`confirm-guards`, `cancel-class`, request-history,
   venue, ui-regression); text assertions unaffected because wording is unchanged. Re-grep
   `tests/` at implementation time for new specs asserting `#modalConfirmBtn` text (parallel
   session may add them) before applying D8 labels.
5. Changelog postscripts on affected pages: replacement-arrangement, my-request-history,
   request-approval, venue-timetable, replacement-home (F), and card-bearing pages per the
   enumeration design.md must provide (all 9 flip-card pages, listed explicitly).

## Risks

- **Parallel session** is actively editing request-approval (dirty tree). Mitigation: all
  anchors are content patterns, re-grepped at edit time; request-approval edits applied last
  and re-verified; rebase-check before staging. Full note in explore-brief.
- `showConfirmModal` label param must keep the cancel-button reset logic (`hideConfirmModal`
  resets `.btn-outline` onclick) working — no regression to confirm/cancel wiring.
- Flip-card back uses `{!! $desc !!}` — keyword spans are safe HTML; no escaping change needed.
