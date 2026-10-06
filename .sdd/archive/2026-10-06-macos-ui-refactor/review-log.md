# Review Log: macOS-Inspired UI Refactor

## Round 1: Initial Proposal

**Date:** 2026-08-12
**Reviewer:** User
**Decision:** Approved

### Notes
- User wants macOS as default theme
- Dark/light toggle preserved as secondary options
- Start with login pages first
- FYP deadline in 3-4 weeks

### Action Items
- [x] Create SDD proposal
- [x] Create design.md
- [x] Create tasks.md
- [ ] Start Phase 1 (login pages)

## Archive Note — 2026-10-06

### ✅ Applied — organic implementation, RE-SCOPED (tasks.md never ticked)
Spot-check against HEAD, 2026-10-06:

| Phase | Verdict | Evidence |
|---|---|---|
| 1 Login | ✅ (except 1.4) | token radius/shadows (`--shadow-card`), global `-apple-system` font (`theme.css:152`) |
| 1.4 / 2.1–2.3 | ⚠️ re-scoped | NO `.macos` class exists; `toggleTheme()` (`ui-common.js:11`) cycles **dark ↔ light only**. macOS became the DEFAULT base look — exactly the proposal headline ("macOS becomes default theme with dark/light toggle preserved as secondary options"), so the tasks' "3rd toggleable theme" wording was superseded by the final design |
| 3 Partials | ✅ | `--radius-pill` nav items, `.summary-card` flip cards, `.modal` `--radius-2xl` + layered `0 4px 12px` macOS shadow, inputs `--radius-md` |
| 4 Pages | ✅ | all templates consume shared theme tokens; canonical `.badge-*` map (`theme.css:887`) |
| 5 Responsive | ✅ | bottom sheet (`theme.css:2743`), 48px targets (`hover: none`), tablet `repeat(3, 1fr)` (`:3076`), `skeleton-shimmer` (`:2937`), `.toast-bar` (`:3203`) |

### 🟡 Deviations from tasks.md (documented, both intentional)
- Literal px values in tasks were replaced by the radius **token ladder** (`--radius-xs..pill`) — the later tokens-only rule (§10.0) made hardcoding illegal; same visual intent.
- "macOS as third theme option" → macOS as default base theme; toggle stays 2-state.

### 🔴 Outstanding
- (none)
