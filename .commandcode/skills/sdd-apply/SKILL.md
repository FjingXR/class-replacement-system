---
name: sdd-apply
description: Implement a frozen SDD change for the TARUMT Class Replacement System by executing its proposal.md, design.md, and tasks.md. Use when the user says "apply", "implement", or "build" a change that already has SDD artifacts in .sdd/changes/.
when_to_use: applying an SDD, implementing frozen artifacts, building a proposed change, executing tasks.md
---

# SDD Apply

Implement a frozen SDD change by executing its frozen artifacts (`.sdd/changes/<change-name>/proposal.md`, `design.md`, `tasks.md`).

## When to use

- User says "apply the SDD", "implement X", "build this change"
- An SDD folder exists in `.sdd/changes/` (or `.sdd/archive/`) with frozen artifacts
- A `run-*.md` prompt exists in `prompts/` for the change — read and follow it

## Read first (before touching code)

1. `.sdd/changes/<change-name>/proposal.md` — what and why.
2. `.sdd/changes/<change-name>/design.md` — how (technical approach, "Promoted to shared" section).
3. `.sdd/changes/<change-name>/tasks.md` — step-by-step implementation tasks.
4. `CodingMAIN.md` — project conventions, especially §10 UI Design Rules.
5. If a `prompts/run-<change-name>.md` exists, follow it — it encodes the full task list and verification steps.
6. The matching `page-changelogs/<change-name>-changelog.md` — keep it updated as you build.

## Process

1. Read the artifacts above.
2. Execute `tasks.md` step by step, checking off boxes as you complete them.
3. Follow the design rules in the artifacts exactly (color tokens only, mock data from `window.MockData.*`, `@include` partials, no copy-paste).
4. **Changelog discipline:** update `page-changelogs/<change-name>-changelog.md` as you go — every touched file gets a row `| Timestamp | Location | Change | Detail |` with server-local ISO-ish timestamps.
5. **Promote-on-3rd-duplication:** if implementation reveals an element duplicated across 3+ pages, promote it to a shared file and refactor existing pages too; record in design.md under "Promoted to shared".
6. When done:
   - Run `composer run lint:check` + `composer run types:check` — confirm no new failures.
   - Clear stale Blade cache before verifying UI: `pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &` (kill old server first).
   - Update `prompts/sdd.md` tracker: move the change to Applied (or In Progress → Applied), with date + one-line summary.
   - Commit with the change's commit prefix (`ui:`, `feat:`, `fix:`, `refactor:`).

## Verification

- Follow the verification steps in the `run-*.md` prompt if present, or in `tasks.md`.
- For UI changes: verify in the browser at the page's `-ui` route.
- For backend changes: run the relevant tests / manual flows.
- Confirm lint + typecheck pass with no NEW failures (existing failures are acceptable if pre-existing — note them).

## Do NOT

- Do NOT deviate from the frozen artifacts without asking the user first.
- Do NOT add features not in scope.
- Do NOT skip the changelog or tracker updates.
