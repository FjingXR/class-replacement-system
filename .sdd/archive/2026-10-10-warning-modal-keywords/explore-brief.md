# Explore Brief — warning-modal-keywords

Date: 2026-10-09 · Explored via interactive grilling session (all decisions user-confirmed).

## Rejected approaches and WHY

- **`data-tip` popups on summary cards (replace flip)** — rejected: `data-tip` is a plain-text
  attribute; red+bold+CAPS keywords cannot render inside it. Flip-back uses `{!! $desc !!}` HTML.
- **Upgrade `data-tip` to support HTML app-wide** — rejected: shared component used by legend,
  nav, today-btn, table headers, event blocks, notifications — blast radius too large for a
  styling change.
- **Native `title=` tooltips** — rejected: breaks the single-tooltip idiom (`data-tip` is the
  app's system, theme.css L3528); native styling is impossible (browser chrome).
- **Red value on card fronts when > 0** — user rejected: fronts stay untouched.
- **Keywords on ALL card descriptions in red** — user rejected: informational descriptions get
  bold+CAPS in DEFAULT color; only attention cards get red.
- **`\n` for line breaks in modal bodies** — rejected (fact): `showConfirmModal` injects via
  `innerHTML`, `\n` collapses to a space. Must use `<br>`.
- **Keywords inside tooltips** — rejected: tooltips stay plain text (see data-tip rejection).

## Design language (user-confirmed rule)

**Red + bold + CAPS keyword = irreversible / needs-attention consequence ONLY.
Bold + CAPS (default color) keyword = informational emphasis.**

Applied to:
- Modals: LOST (leave/go-back), UNDONE (cancel request ×3), ALL (Clear All, red).
- Card flip-backs: attention cards (card-conflict, card-pending, card-rejected) keyword = red+
  bold+CAPS; all other descriptions keyword = bold+CAPS default color.
- `confirmChangeWithSelection`: NO keyword (selection is re-selectable = recoverable), only line break.

## Final solution — complete mapping table

| # | Element | File:Line | Decision |
|---|---|---|---|
| 1 | Leave modal (`navigateTo`) | replacement-arrangement ~L1895 | `LOST` red-caps; `<br>` before "Are you sure you want to leave?" |
| 1b | Go-back modal (`goBack`) | same file ~L1913 | `LOST` red-caps; `<br>` before "Are you sure you want to go back?" |
| 2 | Clear All modal | same file ~L1867 | `ALL` red-caps (replaces existing `<strong>`); `<br>` after "?" before "You can undo…" |
| 7 | Change-guard modal (`confirmChangeWithSelection`) | same file ~L1763 | `<br>` before "Continue?"; no keyword. SECOND copy at ~L1677 ("…will clear them. Continue?") — same treatment both; wording unification optional, never add a keyword here |
| 8 | `showConfirmModal` buttons | same file L1742/L1121 | `modalConfirmBtn` → `btn-danger`; optional confirm-label param; labels "Yes, Leave Page" / "Yes, Go Back" / "Yes, Clear All" / "Yes, Change" |
| 3/3b/4 | Cancel-request modals (static single L275, quick-cancel JS L609, batch L656) | my-request-history | `UNDONE` red-caps in "cannot be undone" |
| 5 | `confirmCancelRequest()` native confirm+alert | my-request-history L779 | DEAD CODE (zero callers) → delete |
| 6 | Detail-modal Cancel Request bug | my-request-history L261/L786/L620 | `openCancelConfirm(id)` sets `pendingCancelId`; button passes id (currently Yes = silent no-op) |
| 9 | Card flip-backs, 9 pages | `partials/ui-summary-bar.blade.php` + inline `'description'` overrides (e.g. CohortTimetable L87-93) | keyword rule per design language; audit inline overrides too |
| 12a | Native `title=` → `data-tip` conversions (13) | ui-week-nav:18 + venue-timetable:371 (print "Coming soon"); arrangement:2615 (★ "Toggle favourite" — standalone dropdown star, NOT the shared `updateFavStar` element); my-request-history:198 ("Reset all filters") + :356/:360/:363/:366 (chip ✕ "Remove"); request-approval:778/:781/:785/:788 (chip ✕ "Remove"); layouts/login-template.blade.php:342 ("Toggle theme" — standalone login toggle; nav-bar toggle already uses data-tip) | `data-tip` with identical text |
| 12b | `data-tip="Close"` on modal-close buttons (13 live) | partials: ui-class-detail-modal, ui-cancel-class-modal, ui-logout-modal (1 each); pages: my-request-history 3, request-approval 3, replacement-home 2, arrangement 1, venue-timetable 1 | matches notif-close precedent; all 13 occurrences live (replacement-home's commented-out markup is the kbShortcutsBtn, not a modal-close) |

## Excluded (explicit)

- Logout modal copy (countdown, not lossy).
- Breeze settings pages (delete account, passkey, recovery codes) — framework-owned.
- Browser `beforeunload` dialog — unstyleable by nature.
- Card fronts (values/labels) — untouched.
- Keywords inside `data-tip` tooltips — plain text by design.
- Touch/tap-to-flip for cards — desktop demo only.

## Key cross-module facts

- `showConfirmModal(title, bodyHtml, callback)`: L1742; `modalBody.innerHTML = bodyHtml` (HTML ok);
  `modalTitle.textContent = title`; cancel button reset targets `.btn-outline`. **7 call sites**
  (re-audited 2026-10-09 after reviewer Round 1): L1675 change-guard #2, L1765 change-guard #1,
  L1818 "No Selection" (informational, null callback), L1836 "Confirm Your Selection" (submit
  flow — consumes the ledger entry per S13b), L1865 Clear All, L1898 leave, L1917 back. The 5
  warning sites get danger+label; submit + No Selection keep default styling (red is reserved).
- `modalConfirmBtn` currently `btn-primary` "Confirm" (L1121) — serves all 4 lossy call sites
  (leave, back, clear-all, change-guard); no non-destructive call site exists.
- Card partial: `partials/ui-summary-bar.blade.php`, 12-class `$descriptions` map, flip-card markup,
  "Hover a card to learn more" hint; included by 9 pages; some pages pass inline `description`.
- `pendingCancelId` set ONLY by `quickCancel()` (L607); listener L620 no-ops when null.
- Specs touching this area: `tests/confirm-guards.spec.ts` (back-button confirmation),
  `tests/cancel-class.spec.ts` (14 tests), request-history specs. Copy strings must stay
  assertable (styling only, no wording change except adding markup).

## Known open questions

- None blocking. Keyword word-choice per card description drafted at implementation time,
  follows the design language rule (reviewer checks against rule, not fixed strings).

## Coordination warning (added 2026-10-09 after user flag)

A parallel session is ACTIVELY editing `request-approval-UI-design-template.blade.php`
(working tree dirty; recent commits `3e6e905`, `afaacc2`, `635bf01` reshaped it — new Urgency
filter, chip rows, data-tip usage). Consequences for this change:

1. **NEVER trust line numbers from this brief at edit time** — all anchors are content patterns
   (grep for the sentence text / function name / class name) and MUST be re-located immediately
   before each edit.
2. Request-approval file edits (3 modal-close tips, 4 chip title conversions) should be applied
   LAST and re-verified against the then-current working tree; if the parallel session commits
   meanwhile, rebase-check before staging.
3. The two change-guard sentences in arrangement (L1677 "clear them" / L1767 "remove them")
   were discovered during re-audit — both are in scope for the `<br>` treatment.

## Prospective keyword styling mechanism (implementation note)

Keyword markup = `<span class="warn-keyword">LOST</span>` style class defined ONCE in
`theme.css` (tokens only: `--color-error`, bold weight) + a `info-keyword` variant (default
color, bold, uppercase via CSS `text-transform`) — so copy stays readable in source and the
styling rule lives in one place. Exact class names settled in design.md.
