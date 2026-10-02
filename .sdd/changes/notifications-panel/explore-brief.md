# Explore Brief — Notifications Panel (frontend-only UI design)

## Problem
The bell button in the shared top nav (`partials/ui-nav-bar.blade.php:30`) is a dead stub —
`onclick="alert('Notifications panel')"`. Every page shows a badge count that has no backing
surface. Root cause: the notifications feature was scoped in CodingMAIN §77 (FR 1.9 / 4.15 / 2.13,
queued via FR 4.16) but the UI phase never built the user-facing surface. This change designs and
builds **only the frontend mock phase** — no backend, no queue, no migrations (standing rule:
frontend mock phase).

## Scope
**In scope**
- Notifications **panel** (popover) opened from the bell, added to the **shared `ui-nav-bar`
  partial** so every page that renders the partial gets it (user decision; matches the stub).
- Mock data: ONE new section `MockData.notifications` in `public/js/mock-data.js`.
- Desktop (popover) **and** mobile (≤768px top sheet) designs — explicitly required.
- Read/unread state, badge wiring, empty state, nav-loop integration (developer loop per house
  SDD workflow).
- Changelog + SDD artifacts (proposal/design/tasks via `/sdd-propose`).

**Out of scope**
- Backend / DB-backed queue / email (FR 4.16) — mock phase only.
- Any new standalone page; no routing changes.
- Timetable-cell notifications or toast messages (`ToastManager` in ui-common is transient
  feedback, a different concept — untouched).
- `replacement-arrangement` page-internal selection summary.
- Notification "digest"/settings screens.

## Key decisions (grilled, one at a time)
| # | Decision | Choice | Rejected alternatives & why |
|---|----------|--------|------------------------------|
| D1 | Shape | Panel in the shared `ui-nav-bar` partial, all pages | Standalone page (route hop, violates §10.0 rule 8); demo-page-only (wasteful duplicate later) |
| D2 | Roles | Rows tagged per `role` ('lecturer'/'pl'/'student'); panel shows active role's rows only | Single generic list (a student seeing PL outcomes is incoherent) |
| D3 | Types | FR-derived 4 types: PL "new request submitted" (FR 4.15); Lecturer "request approved"/"rejected" outcome (FR 2.13); Student "timetable update / replacement class scheduled" (FR 1.9) + PL "awaiting approval" reminder variant | Broader fictional set (out-of-FR noise) |
| D4 | Row click | Mark read + navigate to relevant page (submission→/request-approval-ui, outcome→/my-request-history-ui, timetable update→/upcoming-replacements-ui or /my-timetable-ui per role) | Detail-modal-first (2 clicks; target pages ARE the detail views); inline expand |
| D5 | Read state | Explicit read: opening panel does NOT auto-clear; click rows one by one; **"Mark all as read" text button in the panel header** (user explicitly requested; hidden/disabled when 0 unread); unread ids persisted in `localStorage` per role | Open=mark-all (loses review-later signal); session-only (badge sticks exactly like the stub it replaces); icon-only mark-all (✓✓ meaning less obvious) |
| D6 | Empty panel | Compact inline "You're all caught up" message, small icon | Reuse `ui-empty-state` partial (page-scale 48px icon + CTA fights a ~360px popover) |
| D7 | Desktop | ~360px right-aligned popover under bell; header (title + mark-all + close) + scrollable list capped ~420px; closes on outside click + Esc | Grow-to-fit (viewport collisions, wall of text vs §10.0 rule 6); wide mega-dropdown |
| D8 | Mobile (≤768px) | Full-width top sheet dropping under top bar + dimmed overlay, pattern of existing mobile `nav-drawer` (tap overlay/✕ closes); same header + scroll rows; media query drives layout | Side drawer (feels like navigation, not a check); bottom sheet (no house precedent, bell sits top) |
| D9 | Timestamps | Relative ("2h ago") with absolute in `data-tip` | Absolute-only (stale-feeling in inbox); hybrid (mixed look) |
| D10 | Icon color | Type icon tiles use canonical container colors (§10.0 C badges): submission→`--color-primary-container`, awaiting approval→`--color-tertiary-container`, approved→`--color-secondary-container`, rejected→`--color-error-container`, timetable update→`--color-surface-variant` | Neutral-only (loses at-a-glance type); new per-type colors (violates rule 2) |
| D11 | Badge source | New ui-common helper computes unread count = role-filtered `MockData.notifications` − locally-stored read ids; pages' hardcoded `notifCount => 3` stubs removed/ignored | Keep static stub (badge vs panel visibly inconsistent) |
| D12 | Dataset | 12 rows (4 per role), mixed unread/read, ages minutes→days; stored as `minutesAgo` offsets, absolute time computed at render (never "3 months ago" rot) | Absolute dates (rots); small ~6-row set (can't demo scroll/hover) |
| D13 | Deletion | None — mark-read-only; rows are read + navigate | Per-row dismiss ✕ (no FR basis, adds confirm/undo machinery) |

## Component architecture / data flows
- **Markup:** new partial `resources/views/partials/ui-notifications-panel.blade.php`, included
  once from `ui-nav-bar.blade.php` (panel + overlay siblings, after `nav-drawer` markup).
  Nav button flips from `alert(...)` to `toggleNotifPanel()`.
- **CSS:** `.notif-panel`, `.notif-overlay`, row/header/empty classes → `theme.css`, tokens only.
  Layering: panel ≥ nav-drawer z-index tier (nav-drawer uses ~z-index 999 overlay per §1057–1079
  of theme.css — verify exact tiers in design phase); overlay pattern mirrors `.nav-drawer-overlay`.
- **JS (`ui-common.js`, additive, §10.0 rule 7):**
  - `openNotifPanel()` / `closeNotifPanel()` / `toggleNotifPanel()` — render role rows from
    `MockData.notifications`, resolve read state from localStorage, bind Esc + outside click.
  - `markNotifRead(id)` — write to localStorage, re-render rows + badge.
  - `markAllNotifsRead()` — bulk write + re-render.
  - `refreshNotifBadge()` — badge = unread count for active role; called by any page via DOMContentLoaded hook.
  - `relTime(minutesAgo)` — "2h ago / Yesterday / 3d ago" formatter.
  - Deep-link map per type (D4) as a config object (openClassModal-style additive cfg, no page-local copies).
- **Mock data (`mock-data.js` §new):** `MockData.notifications` — array of
  `{ id, role, type, title, desc, minutesAgo, link }`; read-only; role filter happens in the
  renderer, no mutation.
- **Pages:** remove per-page `notifCount` Blade values and the inline `notifBadge.textContent`
  JS (student-my-timetable:208, upcoming-replacements:338); nav-bar badge becomes
  helper-driven with a static fallback until the helper runs.

## Cross-module edge cases to nail in design
- Theme toggle: panel must re-token correctly in dark mode (tokens-only makes this automatic —
  verify visually).
- `notifBadge` id collision with existing `.nav-badge#navPendingBadge` (approval nav badge) —
  separate IDs, no coupling.
- Z-index vs nav-drawer drawn simultaneously (mobile) — panel closes when drawer opens and vice
  versa.
- localStorage keys namespaced per role, e.g. `notifications-read-<role>`; clearing keys resets
  demo state (documented in changelog).
- When a page passes a role with **zero** rows possible (e.g. student role rows exist etc.) —
  D6 empty message must render inside panel bounds.
- Repaint of panel while open when rows change (mark-one-read re-render keeps scroll position).

## Acceptance criteria (mock-phase)
1. Bell opens the panel on **every** page that renders `ui-nav-bar`; stub `alert` is gone.
2. Desktop ≥768px: anchored ~360px popover as D7; ≤768px: top sheet + overlay as D8 — verified in
   both widths via Playwright resize.
3. Badge shows real unread remainder for active role; fixing pages no longer show hardcoded 3.
4. Click row → read + navigates as D4; "Mark all as read" text button in header clears the rest
   (disabled/hidden at 0 unread); state persists across reload (localStorage).
5. Empty panel shows D6 compact message; cleared-read demo path reproducible.
6. Light **and** dark mode; zero hardcoded colors; 0 console errors; §10.0 lint-able (no hex/rgb
   in touched files).
7. Changelogs + this SDD pipeline artifacts complete.

## Open questions for /sdd-propose
- Exact per-role row content wording (seed text for 12 rows) — draft in proposal.
- Whether the PL "awaiting approval reminder" rows count toward a dedicated reminder visual
  distinct from "submitted" (D3 says reminder variant exists; icon set must pick distinct glyph).
- Panel scroll-position behavior on mark-read re-render (keep position vs top) — trivial; propose
  keep-position.
- Keyboard navigation (Tab focus trap?) — propose simple: Esc close + rows are buttons/links in
  tab order; full focus[val] trap not required for mock phase.
