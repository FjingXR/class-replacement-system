---
name: sdd-status
description: Update the SDD tracker (prompts/sdd.md) for the TARUMT Class Replacement System. Use when an SDD is created, applied, deferred, or its status changes.
when_to_use: updating the SDD tracker, marking an SDD applied, queueing an SDD, changing SDD status
---

# SDD Status

Keep `prompts/sdd.md` — the living index of all SDD changes — accurate.

## When to use

- After an SDD is proposed → move/queue it appropriately
- After an SDD is applied → move it to Applied
- When an SDD is deferred → move it to Deferred / Future
- User says "update the tracker", "mark X applied", "queue Y"

## Read first

1. `prompts/sdd.md` — the tracker itself (the only file this skill edits).
2. `.sdd/changes/` and `.sdd/archive/` — to verify the actual state of changes on disk.

## Tracker structure (keep it intact)

The tracker has four sections:

1. **Applied (already built)** — table: `| SDD | Date | What it did |`. One row per applied change.
2. **In Progress (proposed, not yet applied)** — table: `| SDD | Date | What it does |`.
3. **Queued (not yet proposed)** — table: `| # | SDD name | What it does | Depends on | Prompt file |`.
4. **Deferred / Future** — table: `| # | Feature | Why deferred | Target sprint |`.

## Rules

- Move a change from one section to another — never duplicate rows.
- One-line summary of what the change did, matching the tone of existing rows.
- Preserve any additional decision tables or notes already in the file (e.g. "Advanced auth features — final decisions").
- Use the real date (server-local) for the Date column.
- If the change name in `.sdd/changes/` differs from the tracker name, use the folder name and note the discrepancy.

## Do NOT

- Do NOT edit `CodingMAIN.md`, SDD artifacts, or any code — tracker only.
- Do NOT invent changes that don't exist on disk.
