# Spec: /sdd-propose for a new UI frontend-only page

> **This file is auto-referenced.** `AGENTS.md` instructs any agent to read this spec
> BEFORE generating `/sdd-propose` output for a new **frontend-only UI page**.
> Fill the `<...>` placeholders in the "Page to create" section, then proceed to generate
> the SDD proposal/design/tasks following the rules below.

Create a new **frontend-only UI page** for the TARUMT Class Replacement System.

## Context (read first, non-negotiable)
1. Read `CodingMAIN.md` in full BEFORE writing anything — it is the single source of truth for this project. Pay special attention to §10.0 (UI Design Rules) and §9 (Page Inventory).
2. Read `../final/FR&NFR.md` for any FR/NFR this page satisfies.
3. Read the existing page closest in pattern under `resources/views/ui-design-templates/` and the matching `page-changelogs/*.md` to learn house style before designing.

## Page to create
- **Name:** `<e.g. Request Approval>`
- **Template file:** `resources/views/ui-design-templates/<kebab-case>-UI-design-template.blade.php`
- **Route:** `/<kebab-route>-ui` in `routes/web.php`, passing `$activeNav`
- **activeNav:** `<one of: dashboard | my-timetable | cohort-timetables | replacement-arrangement | replacement-history | request-approval>`
- **Primary user / role:** `<Student | Lecturer | Programme Leader>`
- **Purpose (1–2 sentences):** `<what the page does>`
- **Mock-data scope:** `<list the mock JS objects / columns / cards this page needs>`

## Mandated design rules (from CodingMAIN.md §10.0) — every rule must be followed
1. **Color consistency** — consume ONLY `public/css/theme.css` CSS custom-property tokens (`--color-*`). NEVER hardcode hex/rgb/rgba in `@section('page-styles')`. If a new color is needed, add ONE token to `theme.css` once.
2. **Same name + same color = same meaning** — use the canonical legend/status→color map in §10.0 exactly. No synonyms, no recoloring. A status shown on another page reuses the same label + token here.
3. **OOP concepts** — `@extends('layouts.ui-template')`; reuse `@include('partials.ui-nav-bar'...)` and `@include('partials.ui-summary-bar'...)`; shared JS in `public/js/ui-common.js`; shared CSS in `theme.css`. NEVER copy-paste nav bar / table / helpers into the new page.
4. **Minimise plain text, maximise icon buttons** — actions are icon buttons (✎ ✕ ✓ 👁 ‹ › ▲▼) with `title`/`aria-label`, not verbose text labels. Visible text limited to titles, headers, badges, key data. Reuse SVGs from `resources/views/flux/icon/`.
5. **Don't overwhelm — 冷门/detail info in modals** — the page surface shows only scannable essentials (grid/table + filters + summary cards + primary actions). Full request details, audit history, rejection-reason form, validation breakdown etc. open in a **modal** on click.

## Deliverables (SDD: proposal → design → tasks)
1. `.sdd/changes/<change-name>/` with `sdd.yaml`, `proposal.md`, `design.md`, `tasks.md`.
2. The Blade template (mock data in inline `<script>`, no backend wiring — frontend-only phase).
3. The route added to `routes/web.php`.
4. A matching `page-changelogs/<change-name>-changelog.md`.
5. After apply, run `composer run lint:check` + `composer run types:check` and confirm no new failures.

## Constraints / out of scope
- No new migrations, models, or backend logic (frontend mock phase).
- No new dependencies; reuse existing Flux/Livewire/Tailwind + `theme.css` + `ui-common.js`.
- Keep the page under ~1500 lines (split into partials if larger).
