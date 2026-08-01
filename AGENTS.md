# AGENTS.md — TARUMT Class Replacement System

This file is auto-loaded every session. It tells any agent (including forked sessions)
how to work in this repo and what to read before generating output.

## Read first (every session)
- **`CodingMAIN.md`** — the single source of truth. Read it in full before any coding/design work.
  It contains: project overview, problem domain, architecture (5 objectives + slot state machine),
  tech stack & commands, domain model, RBAC matrix, **FR & NFR (§7)**, dataset, page inventory,
  and **Coding Conventions §10** (incl. UI Design Rules §10.0).

## Running /sdd-propose (or /sdd-apply) for a NEW UI frontend-only page
**MANDATORY:** before generating the proposal/design/tasks, the agent MUST read and follow:
[`prompts/sdd-propose-ui-page.md`](prompts/sdd-propose-ui-page.md)

That spec enforces, for every new UI page:
1. Color consistency — only `public/css/theme.css` CSS custom-property tokens; never hardcode hex/rgb.
2. Same name + same color for the same meaning — follow the canonical legend/status→color map in §10.0.
3. OOP concepts — `@extends('layouts.ui-template')`, `@include` partials, shared `theme.css` + `ui-common.js` + `mock-data.js`; no copy-paste.
4. **Mock data: read from `window.MockData` in `public/js/mock-data.js` (single source).** Never re-declare cohorts/lecturers/venues/semester or duplicate page datasets inline. New data → add ONE section to `mock-data.js`. Treat `MockData` as read-only; `slice()`/spread before mutating.
5. Minimise plain text, maximise icon buttons (reuse `resources/views/flux/icon/`).
6. Don't overwhelm — detail/冷门 info goes in modals, not the page surface.
7. **Promote-on-3rd-duplication (DRY/OOP)** — if a markup block / CSS class / JS helper / mock-data slice ends up the SAME across 3+ pages, promote it to a shared file (Blade partial `resources/views/partials/`, `theme.css`, `ui-common.js`, or `mock-data.js`) and refactor the new page AND existing pages to use it. Record every promotion in the SDD `design.md` under "Promoted to shared".

The agent must also read the closest existing template under `resources/views/ui-design-templates/`
and the matching `page-changelogs/*.md` to learn house style before designing.

## Standing rules (all tasks)
- Frontend mock phase: no migrations / models / backend logic unless explicitly requested.
- Reuse Flux/Livewire/Tailwind + `theme.css` + `ui-common.js`; no new dependencies.
- Conventional commits: `ui:`, `feat:`, `fix:`, `refactor:`, `oop:`, `style:`, `docs:`.
- Working branch: `fjing`. Never commit secrets. Never push unless asked.
- Before finishing a task that touches PHP: run `composer run lint:check` + `composer run types:check`.
- After any UI page change: update the matching `page-changelogs/*.md`.
- DB is PostgreSQL (`class_replacement`, user `philler`). Reset demo data: `php artisan migrate:fresh --seed`.
