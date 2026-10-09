# Spec — warning-language

Capability: the app's warning design language (red-keyword rule, line-break rhythm, danger
buttons, tooltip parity, card-back keywords) as defined in frozen proposal.md A–G and
design.md §1–§8. Frozen baselines: proposal.md, design.md, explore-brief.md.

## R1 — Red keyword is reserved for irreversible consequences

The system SHALL render exactly one `warn-keyword` span (red + bold + uppercase) in each
irreversible-action warning, and zero `warn-keyword` spans in any other MODAL BODY or TOOLTIP
(attention-card flip-backs carry `warn-keyword` by R6.1 and are not violations of R1).

- **R1.1** Leave-guard modal: "…will be LOST…" — LOST is `warn-keyword`.
- **R1.2** Go-back-guard modal: same as R1.1.
- **R1.3** Clear-All modal: "…across ALL weeks?" — ALL is `warn-keyword`.
- **R1.4** Cancel-single / quick-cancel / batch-cancel modals: "…cannot be UNDONE." — UNDONE
  is `warn-keyword`.
- **R1.5** Change-guard modals (both copies), "Confirm Your Selection", "No Selection":
  zero `warn-keyword` spans.
- **R1.6** `data-tip` tooltips: plain text only; no keyword markup anywhere.
- **Scenario (spec test a)**: open leave-guard → modal body contains ≥1 `.warn-keyword`;
  trigger change-guard → modal body contains 0 `.warn-keyword`.

## R2 — Line-break rhythm in confirm modal bodies

- **R2.1** Leave/go-back: `<br>` between the consequence sentence and the "Are you sure…"
  question (question on its own line).
- **R2.2** Clear All: `<br>` between the "?" and the undo reassurance sentence.
- **R2.3** Both change-guard copies: `<br>` before "Continue?".
- Warning sentence wording SHALL NOT change (styling/markup only).

## R3 — Confirm-modal button styling is conditional

- **R3.1** `showConfirmModal(title, bodyHtml, callback, opts)` — when `opts.danger` is set,
  `#modalConfirmBtn` renders `btn-danger`; otherwise `btn-primary`. Class state is re-derived
  on every open (no stale danger leak between opens).
- **R3.2** When `opts.confirmLabel` is set, the button shows that text; default "Confirm".
- **R3.3** Exactly the 5 warning call sites pass danger+label ("Yes, Leave Page", "Yes, Go
  Back", "Yes, Clear All", "Yes, Change" ×2); "Confirm Your Selection" and "No Selection"
  keep default styling and label.
- **R3.4** The cancel-side `.btn-outline` reset in `hideConfirmModal` is untouched and keeps
  working.

## R4 — Detail-modal Cancel Request works (bug fix)

- **R4.1** `openModalById(id)` stores the viewed request id in a page-global.
- **R4.2** The static "Cancel Request" button opens the cancel-confirm overlay bound to THAT
  request (via `openCancelConfirm(currentRequestId)` → `pendingCancelId`).
- **R4.3** Confirming cancels exactly that request with the same behavior as quick-cancel
  (row removed, undo toast).
- **Scenario (spec test b)**: open a pending request's detail modal → Cancel Request →
  overlay visible → Yes → request cancelled + undo toast visible. (Pre-fix: timeout.)

## R5 — Dead code removal

`confirmCancelRequest()` (native `confirm()` + `alert()`) SHALL NOT exist in
my-request-history after this change (zero callers today).

## R6 — Card flip-back keywords are class-keyed

- **R6.1** Cards with class `card-conflict` / `card-pending` / `card-rejected`: their
  flip-back description contains exactly one `warn-keyword` span — regardless of whether the
  description comes from the shared map or an inline override (42 card overrides in scope,
  design.md §5 table).
- **R6.2** All other cards: exactly one `info-keyword` span (bold + uppercase, default color).
- **R6.3** One keyword span per description; secondary `<strong>` emphasis (e.g. "each slot =
  30 minutes") stays plain `<strong>`.
- **R6.4** Card FRONTs (value + label) are untouched. Page-header `description` props (7) are
  untouched.
- **R6.5** The "Cannot select"/"Cannot book" cards carry `card-conflict` → red applies
  (deliberate decision, design.md §5).

## R7 — Tooltip parity

- **R7.1** All 13 audited native `title=` tooltips (proposal F12 list) become `data-tip` with
  identical text.
- **R7.2** All 13 live `modal-close` buttons carry `data-tip="Close"`.
- **R7.3** After the change, a grep of `resources/views/{ui-design-templates,partials,layouts}/*.blade.php`
  for app-UI `title="` returns only Breeze-owned `:title=` component props;
  `resources/views/login-UI-design-template.blade.php` (route-less mockup) is OUT.

## R8 — Styling tokens

`.warn-keyword` / `.info-keyword` are defined once in `theme.css`, use `--color-error` /
inherited color tokens only (zero hardcoded hex/rgb), and uppercase via `text-transform`.

## R9 — No regression

- **R9.1** Existing Playwright specs (`confirm-guards`, `cancel-class`, request-history,
  venue-timetable, ui-regression) SHALL pass unchanged.
- **R9.2** `tests/` SHALL be re-grepped for `#modalConfirmBtn` text assertions before the D8
  labels are applied (parallel session may add specs).

## R10 — Changelog postscripts

Changelog postscripts SHALL be written per design.md §8: replacement-arrangement,
my-request-history, request-approval, venue-timetable, replacement-home, plus the card-
keyword pages (CohortTimetable, MyTimetable, replacement-history, student-my-timetable).
The login layout has no changelog file — its tooltip swap is noted in the commit message.
