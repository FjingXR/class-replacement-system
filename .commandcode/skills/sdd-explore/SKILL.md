---
name: sdd-explore
description: Investigate a change idea before proposing an SDD for the TARUMT Class Replacement System. Use when the user says "explore", "investigate", "research", or wants to understand scope/impact of a potential feature or change before committing to an SDD proposal. Produces an explore-brief.md.
when_to_use: exploring a feature idea, checking what files a change touches, scoping before /sdd-propose, understanding impact before proposing
---

# SDD Explore

Produce an exploration brief for a potential SDD change in the TARUMT Class Replacement System, BEFORE any proposal/design/tasks are written.

## When to use

- User wants to understand the scope/impact of a change before committing to it
- User says "explore", "investigate", "research", "scope this out", "what would it take"
- A change spans multiple files/pages and needs impact analysis first

## Read first (non-negotiable)

1. `CodingMAIN.md` in full — the single source of truth (§9 Page Inventory, §10 UI Design Rules, §10.0 color legend).
2. `../final/FR&NFR.md` — find every FR/NFR the change touches.
3. The actual files the change would affect (Blade templates, `theme.css`, `ui-common.js`, `mock-data.js`, `routes/web.php`, partials in `resources/views/partials/`).
4. The matching `page-changelogs/*.md` for any page touched, to learn house style and prior changes.
5. `prompts/sdd.md` — the tracker, to see what's applied/in progress/queued.
6. Check `.sdd/archive/` for similar past changes you can learn from.

## Process

1. Read everything above.
2. Investigate the actual code: which files/partials/functions/classes the change touches, and what already exists that can be reused.
3. Think through the design decisions, alternatives, and rejected approaches.
4. Identify cross-module data flows (which partials, CSS classes, JS helpers, mock-data sections are involved).
5. Check for 3rd-duplication opportunities (DRY promote-on-3rd rule from §10.0).
6. Write the explore brief to `.sdd/changes/<change-name>/explore-brief.md`.

## Explore brief format (model on `.sdd/changes/timetable-print/explore-brief.md`)

```markdown
# Explore Brief: <change-name>

## Problem
<What problem is being solved? 1-3 sentences.>

## Scope
**In scope:**
- <item>

**Out of scope:**
- <item>

## Decisions
<Key design decisions with rationale. For each: the decision, why, and (if relevant) alternatives.>

## Cross-Module Data Flows
<ASCII diagram or list showing which partials / CSS classes / JS helpers / mock-data sections are involved and how they connect.>

## Implementation Approach
<High-level: which files change and what changes in each.>

## Open Questions
<Questions for the user that block final design.>

## Rejected Approaches
| Approach | Reason Rejected |
|----------|----------------|

## Estimated Effort
| Task | Estimate |
|------|----------|
```

## Deliverables

- `.sdd/changes/<change-name>/explore-brief.md` (create the folder if it doesn't exist; also drop a minimal `sdd.yaml` with `name`, `created`, `status: exploring`).

## Do NOT

- Do NOT write `proposal.md` / `design.md` / `tasks.md` — that's `/sdd-propose`'s job.
- Do NOT implement anything.
- Do NOT modify shared files (`theme.css`, `ui-common.js`, `mock-data.js`) — this is read-only investigation.
