# Design — warning-modal-keywords

Created: 2026-10-09 · Status: draft (Batch 2, unfrozen)
Frozen baseline: [proposal.md](./proposal.md) · [explore-brief.md](./explore-brief.md)

## 1. The two keyword classes (theme.css)

Defined ONCE in `public/css/theme.css`, placed near the existing event-hover-tooltip section
(~L2473) under a new comment block `/* ── Warning keyword (modals & card backs) ── */`:

```css
.warn-keyword {
    color: var(--color-error);
    font-weight: 700;
    text-transform: uppercase;
}
.info-keyword {
    font-weight: 700;
    text-transform: uppercase;
}
```

- Tokens only (`--color-error` exists; default color inherited = default-color rule for
  info-keyword). No hardcoded hex/rgb. No font-size change (inherits context).
- Uppercase via CSS `text-transform` — sentence source stays natural-case, greppable, and
  specs asserting sentence text remain valid.
- Both classes are context-free inline spans (`<span class="warn-keyword">lost</span>`);
  safe inside `innerHTML` bodies and `{!! $desc !!}` flip-backs.
- Convention note (AGENTS.md §10.7): these two classes are BORN-SHARED in `theme.css` (not
  promoted from a page) — recorded here to keep the shared-CSS audit trail uniform.

## 2. `showConfirmModal` extension (replacement-arrangement)

```js
function showConfirmModal(title, bodyHtml, callback, opts) {
    opts = opts || {};
    ...
    confirmBtn.classList.toggle('btn-danger', !!opts.danger);
    confirmBtn.classList.toggle('btn-primary', !opts.danger);
    confirmBtn.textContent = opts.confirmLabel || 'Confirm';
    ...
}
```

- `hideConfirmModal` cancel-button reset (`.btn-outline` onclick) untouched.
- Class toggle (not className overwrite) so any other classes on `modalConfirmBtn` survive.

### Call-site matrix (all 7, verified)

| Call site | Line | danger | confirmLabel |
|---|---|---|---|
| leave guard (`navigateTo`) | ~1898 | ✅ | "Yes, Leave Page" |
| back guard (`goBack`) | ~1917 | ✅ | "Yes, Go Back" |
| Clear All | ~1865 | ✅ | "Yes, Clear All" |
| change-guard #2 (`onVenueChange` inline) | ~1675 | ✅ | "Yes, Change" |
| change-guard #1 (`confirmChangeWithSelection`) | ~1765 | ✅ | "Yes, Change" |
| "Confirm Your Selection" (`proceed`) | ~1836 | ❌ | (default "Confirm") |
| "No Selection" (informational) | ~1818 | ❌ | (default "Confirm") |

## 3. Modal copy — exact rendered results (markup pattern)

Keyword span wraps ONE word (or the minimal consequence phrase); the rest of the sentence is
untouched. `<br>` inserted exactly where the line break goes. Anchors are content patterns —
re-grep each sentence at edit time (parallel session active).

| Modal | Body becomes (HTML) |
|---|---|
| leave / back | `You have selections that will be <span class="warn-keyword">lost</span> if you leave this page.<br>Are you sure you want to leave?` / `…go back?` |
| Clear All | `Are you sure you want to clear all selections across <span class="warn-keyword">all</span> weeks?<br>You can undo this from the toast that appears.` (replaces `<strong>ALL</strong>`) |
| change-guard ×2 | `…Changing the <strong>{actionLabel/venue}</strong> will remove/clear them.<br>Continue?` — NO keyword. Sentence wording unification ("clear" vs "remove") deliberately NOT done in this change: two call sites build the sentence differently (one interpolates `actionLabel`); unifying adds coupling for zero user value. |
| cancel single (static L275 + quick-cancel JS L609) | `Are you sure you want to cancel this replacement request? This action cannot be <span class="warn-keyword">undone</span>.` |
| cancel batch (JS L656) | `Cancel {count} selected request(s)? This action cannot be <span class="warn-keyword">undone</span>.` |

Renders (via text-transform): "…will be **LOST**…", "…across **ALL** weeks?", "…cannot be
**UNDONE**."

## 4. Dead code + bug fix (my-request-history)

- **Delete** `confirmCancelRequest()` (L779-784) entirely — zero callers (verified Round 1).
- **Bug fix — affirmative mechanism** (the button is STATIC markup; `openModalById` renders via
  shared `DetailModal.render(...)` and only toggles button visibility — no id variable exists
  today, and inline `onclick` resolves in GLOBAL scope):
  1. Add a page-global `var currentRequestId = null;` near the other modal state.
  2. In `openModalById(id)` (L707), first line after the lookup guard: `currentRequestId = id;`
  3. Button (L261, static markup): `onclick="openCancelConfirm(currentRequestId)"`
     — inline onclick evaluates `currentRequestId` in global scope at CLICK time, which is
     correct (top-level `var` in the inline script is a window global).
  4. `openCancelConfirm(requestId)` stores it: `pendingCancelId = requestId;`
- Behavior after fix: identical to quick-cancel path (same overlay, same
  `confirmCancelAction` listener, same undo toast). Re-grep `currentRequestId` before naming —
  it does not exist today (verified), so no collision.

## 5. Summary-card flip-backs (9 pages, enumerated)

**Keyword attaches to the CARD's class, not the prose**: a card whose class is
`card-conflict` / `card-pending` / `card-rejected` gets a `warn-keyword` span (red+bold+caps);
every other card gets an `info-keyword` span (bold+caps, default color). This holds for BOTH
description sources: the `$descriptions` map in `partials/ui-summary-bar.blade.php` AND inline
`'description'` overrides at include sites.

**Deliberate semantic call (user rule, stated explicitly)**: the `card-conflict`-classed
"Cannot select"/"Cannot book" cards (arrangement, venue) describe a *recoverable* state but
carry an attention CLASS — red applies because the rule keys to card class. Recorded here so
it is a decision, not an accident.

Descriptions live in TWO places — both must be edited. TRUE inline-override inventory
(re-grepped 2026-10-09 after review Round 2: **42 card overrides** across the 9 pages;
**7 `ui-page-header` description props excluded — none are cards, none touched**; line
numbers WILL drift — content-anchor every edit):

| Page | Card overrides | warn (attention) | info |
|---|---|---|---|
| CohortTimetable | ×5 (L87-95) | L95 card-conflict | L87 total, L89 hours, L91 taught-by-you, L93 your-classes |
| MyTimetable | ×5 (L119-127) | L125 card-pending, L127 card-conflict | L119 total, L121 hours, L123 replacement |
| replacement-history | ×5 (L132-136) | L134 card-pending | L132 total, L133 replacement, L135 past, L136 hours |
| my-request-history | ×5 (L236-244) | L242 card-pending, L244 card-rejected | L236 total, L238 hours, L240 approved |
| replacement-arrangement | ×4 (L1031-1037) | L1035 card-pending, L1037 card-conflict ("Cannot select") | L1031 total, L1033 available |
| replacement-home | ×3 (L227-231) | L227 card-conflict | L229 duration, L231 courses |
| venue-timetable | ×5 (L425-433) | L433 card-conflict ("Cannot book") | L425 total, L427 available, L429 you-teach, L431 your-classes |
| request-approval | ×5 (L312-320) | L314 card-pending, L318 card-rejected | L312 total, L316 approved, L320 decided |
| student-my-timetable | ×5 (L55-63) | L61 card-pending, L63 card-conflict | L55 total, L57 hours, L59 replacement |

(venue L335 — and the other 6 page-header description props, one per page — are NOT cards;
do not touch.)

### Keyword choice per description (rule: the noun/phrase the card is ABOUT, one per sentence)

- Attention → `warn-keyword`: **clashes** → `CLASHES` (card-conflict), **waiting** /
  **awaiting** / **being processed** → the waiting phrase (card-pending), **declined** /
  **cannot** → the declined/unavailable word (card-rejected / the Cannot-cards).
- Informational → `info-keyword`, replacing the existing single most-defining `<strong>`:
  e.g. `SCHEDULED CLASSES` (total), `TEACHING HOURS` / `HOURS` (hours), `REPLACEMENT LECTURER`
  (replacement), `FREE SLOTS` (available), `APPROVED` (approved), `YOU SUBMITTED` /
  `YOUR COHORT` / `YOUR CLASSES` (per-context totals), `STILL AHEAD` (upcoming),
  `ALREADY TAKEN PLACE` (past), `RESCHEDULED` (duration), `COURSES` (courses),
  `DECIDED` (decided), `YOU TEACH` (you-teach), `NEED A REPLACEMENT` (home conflict is warn —
  keyword = `NEED A REPLACEMENT`).
- Existing secondary `<strong>`s (e.g. "each slot = 30 minutes" — MyTimetable L121,
  CohortTimetable L89) stay plain `<strong>` — only ONE keyword span per description; the rest
  of the emphasis hierarchy unchanged.
- Same one-keyword rule applied to every inline override.

## 6. Tooltip parity (F)

- 13 `title=` → `data-tip` swaps: identical attribute text, no `data-tip-pos` unless the
  element sits within ~40px of the right viewport edge on default desktop width (none of the
  13 do; the chip ✕ buttons are mid-page). Verify at implementation: if a chip tooltip clips,
  add `data-tip-pos="left"`.
- 13 modal-close buttons get `data-tip="Close"` (list in proposal F13; all live).
- The login page's standalone theme-toggle (`layouts/login-template.blade.php:342`) gets the
  same swap; `resources/views/login-UI-design-template.blade.php` (route-less mockup) stays OUT.
- Note: the ★ favourite star (arrangement dropdown) does NOT conflict with ui-common's
  `updateFavStar` (different element — dropdown star is static markup inside a template
  literal; the shared one sets `dataset.tip` on the venue-panel star).

## 7. Tests & verification plan

- Re-grep `tests/` at implementation start for: `#modalConfirmBtn` text assertions,
  "Continue?", "clear them", "remove them", "will be lost", "cannot be undone" (parallel
  session may have added specs). None exist as of Round 2 (reviewer-verified).
- Run: `confirm-guards.spec.ts`, `cancel-class.spec.ts` (14), request-history specs,
  venue-timetable.spec.ts, ui-regression specs. All must pass unchanged.
- Manual live checks (server + Playwright): leave-guard modal shows LOST red + "Yes, Leave
  Page" danger button; change-guard shows NO red + "Yes, Change"; chip ✕ hover shows bubble;
  card back hover shows keyword styling on an attention card and an info card.
- New coverage (small, in tasks): **two specs** —
  (a) criterion-1: `warn-keyword` present in leave-guard modal body, absent in change-guard
  body (class-counting);
  (b) criterion-2 (the only behavioral fix — the original bug failed silently, so it gets
  automated coverage): open a pending request's detail modal → click "Cancel Request" →
  confirm overlay appears → "Yes, Cancel Request" → request status becomes Cancelled + undo
  toast appears (fails on pre-fix code with a timeout, making the silent no-op loud).

## 8. Changelog postscripts (criterion 5)

Pages: replacement-arrangement (keywords, buttons, tooltips), my-request-history (keywords,
bug fix, dead code removal, tooltips), request-approval (tooltips), venue-timetable
(tooltips), replacement-home (tooltips), plus card-keyword pages: CohortTimetable,
MyTimetable, replacement-history, student-my-timetable, replacement-arrangement (already
listed). Login layout has no changelog file — note the tooltip swap in the change summary
commit message instead.

## 9. Out of scope / unchanged (restated)

Breeze pages, route-less login mockup, logout modal copy, beforeunload, card fronts,
data-tip internals, wording of warning sentences (except the D8 button labels, which are new
UI text on a control no spec asserts).
