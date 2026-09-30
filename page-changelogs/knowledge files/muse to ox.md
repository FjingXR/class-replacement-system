# New Session Bootstrap

You are continuing work from a previous OpenCode TUI session.

## Context

* Treat this as a continuation, not a completely fresh project.
* Preserve the user's existing intent, architecture, conventions, decisions, and constraints when they are provided below.
* Do not blindly change working code or established decisions without a reason.
* Before making significant changes, inspect the relevant existing code/files and understand the current implementation.

## Working Rules

* Be concise, practical, and implementation-focused.
* Prefer the smallest correct change over unnecessary refactoring.
* Reuse existing patterns, utilities, types, and architecture where possible.
* Do not invent APIs, files, dependencies, or project conventions.
* Verify assumptions from the codebase before acting.
* Keep changes scoped to the requested task.
* Preserve backward compatibility unless the task explicitly requires breaking changes.
* Handle errors and edge cases appropriately.
* After changes, check the affected code for obvious errors, regressions, and consistency.
* If something is unclear or potentially destructive, explain the uncertainty before proceeding.

## Coding Style

* Follow the project's existing style over personal preferences.
* Prefer readable, maintainable code.
* Avoid unnecessary comments; comment only when the reasoning is non-obvious.
* Avoid premature abstractions and over-engineering.
* Keep functions/components focused.
* Do not add dependencies unless genuinely necessary.

## Response Style

* State what you found/understood briefly.
* State what you changed and why.
* Mention important assumptions or remaining issues.
* Do not give long explanations unless requested.
* If the task is straightforward, just execute it.

## Previous Session Context

Paste the relevant project/task context below:

[PROJECT / GOAL]
TARUMT Class Replacement System — replace manual Google-Sheets replacement workflow via deterministic 4-vector intersection (lecturer × cohort(s) × room × capacity → color grid) + OCC (version on time_slots) + FCFS Approval Dashboard + RBAC + notifications. BMCS3404 Project I (FYP1 14w design + FYP2 7w). SDGs 4 & 8. Prototype: 14 cohorts, 14 staff, 23 Block B rooms (Tutorial B002,B014-B018,B100-B109 cap35; LectureHall B110,B111 cap80; Lab B005,B009-B011 cap28; CiscoLab B006 cap32). Current phase: frontend mock only — `public/js/mock-data.js` single source (throwaway Sprint 3), `public/css/theme.css` tokens only, `public/js/ui-common.js` shared, Blade `@extends('layouts.ui-template')` + `@include` partials. Stack: Laravel 13 (PHP 8.3, livewire-starter-kit, Livewire 4+Flux 2+Blaze), Tailwind, PostgreSQL `class_replacement` user `philler`, Fortify+WebAuthn, Pint/PHPStan, Vite. Routes: `/`, `/login/student`, `/login/staff`, `/dashboard`, `/my-timetable-ui`, `/cohort-timetable-ui`, `/replacement-home-ui`, `/replacement-arrangement`, `/my-request-history-ui`, `/request-approval-ui`.

[IMPORTANT DECISIONS]
- Baseline canonical: `83fd365` `fix: timetable grid now loads on initial page load` (init order `populateWeekSelect → applyUrlParams → buildTimetable()`). Rejected block model `f3d2554` — do not re-apply without explicit request. `CodingMAIN.md` is single source of truth.
- Frontend mock only — no migrations/models/backend unless explicitly requested. Service classes `MatrixIntersectionEngine` + `OCCValidator` deferred to Sprint 1-3.
- Colors: only `theme.css` custom-property tokens (`--color-primary`, `--color-secondary` Available green, `--color-error` Occupied red, `--color-tertiary` Pending yellow, etc.). Same label → same color. Promote-on-3rd duplication to shared (partial/theme/ui-common/mock-data), record in SDD `design.md`.
- Mock data: `window.MockData` read-only (`slice()`/spread before mutate); new data → add ONE section to `mock-data.js`. `MockData.semester=202605` 2026-07-27→2026-10-26, `currentUser` En. Lim Jia Zheng 5770, PW `Tarumt@2026` all seeded.
- Confirm destructive actions with popup + toast undo bottom-left 5s; ≤3 clicks flows; maximize `resources/views/flux/icon/` icons; modals for detail; responsive ≤768px mandatory.
- Git: branch `fjing`, conventional commits `ui:` `fix:` `refactor:` `oop:` `docs:`, never push unless asked, `git reset --hard <hash>` preferred. After Blade change: `pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &` (view:clear broken). Checks: `composer run lint:check` + `types:check` before finishing PHP.

[CURRENT STATE]
- HEAD `a634be2` `docs: rename knowledge file to general_Session_2026-08-21.md` chain `a634be2←8dbef39←d2c494d←116cbee←83fd365`; `origin/fjing` at `f316278` diverged 4 vs 1; working tree has 5 modified + 1 untracked before commit (2026-08-21).
- Latest fix executed 2026-08-21: **[TASK-004] Staff ID Optional P Prefix — COMPLETED** `resources/views/auth/login-staff.blade.php:10-12` `idPlaceholder e.g. 6767→e.g. P5425 or 5425`, `idRegex ^\d+$→^P?\d{4}$`, `formatHint Numeric staff ID only→4 digits, optional "P" prefix`. `resources/views/layouts/login-template.blade.php:420` `new RegExp(@json($idRegex))` unchanged. `resources/views/auth/login-student.blade.php:11` `^\d{2}[A-Za-z]{3}\d{4}$` untouched. Slim plan replaces 200-line detailed plan: `page-changelogs/todo list/staff-id-p-prefix-frontend-fix-plan.md:1-95` (95 lines). Changelogs: `page-changelogs/login-oop-refactor-changelog.md:88-113` added Fix section, `page-changelogs/todo list/todo-list.md:173` TASK-004 `pending→completed`. Knowledge file header updated to `a634be2` chain.
- Verification: `python3 with_server.py --port 8000` + `npx playwright test tests/login-staff-prefix.spec.ts` 14/14 passed — `5425`/`P5425` enabled, `p5425`/bad lengths disabled, trim handled, student control `25RSD0001` enabled, `P5425` on student disabled, `idRegex.toString()= /^P?\d{4}$/`, placeholder/hint DOM correct. `composer run pint --test` exit 0. Cache cleared. Test file `tests/login-staff-prefix.spec.ts:10-63` created (currently untracked); `tests/ui-regression.spec.ts` existing (246 lines) covers grid/load/week-nav/modal.
- Pending commit: 5 modified files staged? Not yet pushed (see `git status` diverged). Lint ok.

[KNOWN ISSUES]
- Branch divergence: local `a634be2` (4 ahead) vs `origin/fjing` `f316278` (1 ahead) — need `git pull --rebase` or `push --force-with-lease` only on user request, do not auto-push.
- Stale Blade cache holds old regex — must `pkill -9 php && rm -f storage/framework/views/*.php` before verify (AGENTS.md:31).
- Untracked `tests/login-staff-prefix.spec.ts` — decide to keep as regression or delete before commit.
- Pre-existing dirty `page-changelogs/knowledge files/general_Session_2026-08-21.md:1-11` header diff (8dbef39 vs a634be2 chain) was present at session start — already included in working tree, treat as intentional.
- Backend P-strip deferred: DB stores `5425`; frontend allow alone insufficient for `P5425` login — needs `FortifyServiceProvider.php:28` `ltrim(P)` later (frontend-only scope per plan). HR 4-digit only; 5-digit would need FR amend.
- Remaining todos: TASK-001 (2-tab modal own pending, high), TASK-002 (copy icon Subject Code, medium), TASK-003 (title utility classes, low), TASK-005 (cancel reason ≥10 chars, high), TASK-006 (student history drop + upcoming, high).

[NEXT TASK]
Immediate next per `page-changelogs/todo list/todo-list.md`: TASK-001 / TASK-005 / TASK-002 (user previously listed 6 tasks, TASK-004 now completed). If user says `git commit`, stage `resources/views/auth/login-staff.blade.php:10-12`, `page-changelogs/login-oop-refactor-changelog.md`, `page-changelogs/todo list/todo-list.md`, `page-changelogs/todo list/staff-id-p-prefix-frontend-fix-plan.md`, `page-changelogs/knowledge files/general_Session_2026-08-21.md` (+ optionally `tests/login-staff-prefix.spec.ts`) with conventional message e.g. `fix: staff login accepts optional P prefix (frontend, TASK-004)` and do not push unless asked. If user asks next UI task, follow `prompts/sdd-propose-ui-page.md` + read `CodingMAIN.md` + closest template + its `page-changelogs/*.md` first.

## Priority

Follow this order:

1. User's current instruction
2. Existing project/codebase conventions
3. Constraints and decisions from the previous session
4. These general rules
5. Your own implementation preferences
