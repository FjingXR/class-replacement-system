---
name: sdd-propose
description: Generate SDD artifacts (proposal.md, design.md, tasks.md, sdd.yaml) for the TARUMT Class Replacement System. Use when the user wants to propose a new change, page, feature, or enhancement following the SDD workflow. Covers new UI pages, UI enhancements, backend features, and mobile-responsive work.
when_to_use: proposing a change, creating SDD artifacts, starting a new feature, new UI page, backend feature, enhancement
---

# SDD Propose

Generate the SDD proposal artifacts (`.sdd/changes/<change-name>/` with `sdd.yaml`, `proposal.md`, `design.md`, `tasks.md`) for the TARUMT Class Replacement System.

## Variants

Pick the variant that matches the request:

| Variant | Use when | Spec file |
|---------|----------|-----------|
| **new-ui-page** | Creating a brand-new frontend-only UI page | `prompts/sdd-propose-ui-page.md` |
| **ui-enhancement** | Adding features to an existing UI page | `prompts/run-*.md` (e.g. `run-my-request-history-ux.md`) + `prompts/sdd-propose-ui-page.md` adapted |
| **backend** | Backend feature (auth, migration, model, controller, API) | `prompts/sdd-propose-backend-template.md` |
| **mobile-responsive** | Mobile compliance for a single page | `prompts/sdd-propose-mobile-responsive.md` |

## Read first (non-negotiable, ALL variants)

1. `CodingMAIN.md` in full — single source of truth (§5 Domain Model, §6 RBAC, §7 FR/NFR, §9 Page Inventory, §10 Coding Conventions, §10.0 UI Design Rules).
2. `../final/FR&NFR.md` — find every FR/NFR the change touches.
3. The variant's spec file (table above) — read and follow it.
4. The closest existing page under `resources/views/ui-design-templates/` and its `page-changelogs/*.md` (for UI variants).
5. For backend: the existing code files to extend/reuse (models, migrations, controllers, actions, config).
6. `prompts/sdd.md` — the tracker: check Applied/In Progress/Queued for related work.

## Non-negotiable workflow step: DISCUSS BEFORE GENERATING

After reading the above, talk to the user first — do NOT jump straight to writing artifacts:

1. **FR/NFR logic check** — list every FR/NFR that touches this change; say whether each is logical for the mock phase; flag anything needing backend (defer it) and any contradiction between FR/NFR and CodingMAIN.md.
2. **Design details** — confirm with the user: legend items + colors, summary cards, modal pattern, week picker, what to copy from the closest page, what to remove (action buttons, role-only logic, renamed labels), implementation approach (which files to create vs extend).
3. **For backend:** confirm which files create vs extend, existing patterns/actions to reuse, middleware/session implications, auth/security concerns (CSRF, session invalidation, route protection).
4. Wait for the user's OK before writing the SDD artifacts.

## Deliverables (all variants)

`.sdd/changes/<change-name>/` containing:

1. **`sdd.yaml`** — model on `.sdd/changes/macos-ui-refactor/sdd.yaml`:
   ```yaml
   name: <change-name>
   created: <YYYY-MM-DD>
   status: active
   scope: <single-page | multi-page | backend | global>
   page: <blade template or "n/a">
   features:
     - <F1: short description>
     - <F2: short description>
   ```
2. **`proposal.md`** — what and why: problem, scope, out-of-scope, FR/NFR refs, features list, decisions.
3. **`design.md`** — how: technical approach, key data flows, CSS/JS/markup details. MUST include a **"Promoted to shared"** section listing any element promoted on 3rd duplication (DRY rule).
4. **`tasks.md`** — step-by-step implementation tasks with checkboxes, grouped into tasks (model on `prompts/run-logout-modal.md`), ending with lint + typecheck + changelog tasks.

Plus (per variant spec):
- `page-changelogs/<change-name>-changelog.md` — created BEFORE the proposal (even if only header + empty Files Changed), kept updated.
- Route in `routes/web.php` (new pages only).
- Mock data in `public/js/mock-data.js` — NEVER inline in the template.

## Mandated design rules (from CodingMAIN.md §10.0) — every rule must be followed

1. **Color consistency** — consume ONLY `public/css/theme.css` CSS custom-property tokens (`--color-*`). NEVER hardcode hex/rgb in `@section('page-styles')`. New color → add ONE token to `theme.css` once.
2. **Same name + same color = same meaning** — use the canonical legend/status→color map in §10.0 exactly. No synonyms, no recoloring.
3. **OOP + DRY** — `@extends('layouts.ui-template')`; `@include` partials; shared JS in `ui-common.js`; shared CSS in `theme.css`; shared mock data in `mock-data.js` (`window.MockData`). NEVER copy-paste nav bar / tables / helpers / mock data.
4. **Mock data single source** — page reads from `window.MockData.*`; `MockData` is READ-ONLY (slice/spread before mutating). New data → ONE new section in `mock-data.js`.
5. **Icon over text** — actions are icon buttons (✎ ✕ ✓ 👁 ‹ › ▲▼) with `title`/`aria-label`; reuse SVGs from `resources/views/flux/icon/`.
6. **Don't overwhelm** — secondary/detail info in modals, not the page surface.
7. **Promote-on-3rd-duplication** — if any element is now duplicated across 3+ pages, promote it to a shared file (partial / `theme.css` / `ui-common.js` / `mock-data.js`) and refactor the new AND existing pages to use it. Record in design.md under "Promoted to shared".
8. **Minimise steps** — fewest clicks possible; pre-select defaults; rethink any flow > ~3 clicks.
9. **Confirm critical actions** — delete/submit/cancel/approve/reject MUST show a confirmation popup ("Are you sure?").
10. **Toast/undo bar** — after critical actions, show toast at bottom-left with success message + Undo (5s auto-dismiss). CSS in `theme.css`, helper `showToast(message, undoCallback, duration)` in `ui-common.js`.
11. **Mobile responsive** — every page MUST include mobile layout (≤768px): nav drawer, card layout for tables, 2-col summary cards, flex-wrap legend, bottom-sheet modals, ≥44×44px touch targets, `clamp()` typography, safe-area insets, full-width inputs, skeleton loading, scroll restoration, bottom-center toasts on mobile. Document under "Mobile view" in design.md.

## Commit prefix

- New UI page: `ui:`
- Backend feature: `feat:` / `fix:` / `refactor:`
- Mobile work: `ui:`

## Constraints / out of scope

- Frontend mock phase: no migrations/models/backend logic unless explicitly requested (backend variant is the exception).
- No new dependencies; reuse Flux/Livewire/Tailwind + `theme.css` + `ui-common.js` + `mock-data.js`.
- Keep pages under ~1500 lines (split into partials if larger).
