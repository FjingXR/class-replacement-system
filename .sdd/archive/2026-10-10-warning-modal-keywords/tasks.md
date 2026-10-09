# Tasks — warning-modal-keywords

Frozen baselines: proposal.md (Batch 1), design.md (Batch 2), specs/warning-language/spec.md (Batch 3).
Every task ≤ 2h. ALL edits use content-pattern anchors (re-grep immediately before each edit) —
line numbers in artifacts are approximate and DRIFT (parallel session active; request-approval
edits applied LAST). Changelog postscript required per task where marked (R10).

## T0 — Safety rails (before any edit)

- [ ] `git status` snapshot: record the parallel session's dirty files
      (`request-approval-UI-design-template.blade.php` expected dirty — do NOT stage it into
      unrelated commits; rebase-check before staging).
- [ ] Re-grep `tests/` for: `#modalConfirmBtn` text assertions, `has-text("Confirm")`,
      `Continue?`, `will be lost`, `cannot be undone` (R9.2). Record hits; if any assert the
      affected modals' button text, STOP and surface before T2 (where the D8 labels land).
- [ ] Regression baseline: run the 4 spec files (`confirm-guards.spec.ts`,
      `cancel-class.spec.ts`, `venue-timetable.spec.ts`, `ui-regression.spec.ts` —
      request-history coverage rides inside ui-regression; R9.1) and record pass counts.

## T1 — theme.css keyword classes (R8)

- [ ] Add `/* ── Warning keyword (modals & card backs) ── */` block near the event-hover-
      tooltip section: `.warn-keyword` (`--color-error`, 700, uppercase) + `.info-keyword`
      (700, uppercase, inherited color). Zero hardcoded colors.
- [ ] Changelog: none (global css — covered by page postscripts).

## T2 — Arrangement confirm modal: mechanism + copy (R1.1–R1.3, R2.1–R2.3, R3)

- [ ] `showConfirmModal` gains `opts` (`danger`, `confirmLabel`); `classList.toggle` for
      `btn-danger`/`btn-primary`; `textContent = opts.confirmLabel || 'Confirm'` (R3.1/R3.2).
- [ ] 5 warning call sites updated with `danger:true` + labels: leave "Yes, Leave Page",
      back "Yes, Go Back", Clear All "Yes, Clear All", both change-guards "Yes, Change"
      (R3.3). "Confirm Your Selection" + "No Selection" untouched.
- [ ] Leave/back body: wrap "lost" in `warn-keyword`; `<br>` before "Are you sure…" (R1.1,
      R1.2, R2.1).
- [ ] Clear All body: `<strong>ALL</strong>` → `warn-keyword`; `<br>` after "?" (R1.3, R2.2).
- [ ] Both change-guard bodies: `<br>` before "Continue?" — NO keyword (R1.5, R2.3).
- [ ] Verify cancel `.btn-outline` reset still works (R3.4) — open a change-guard, cancel,
      reopen: no stale danger, cancel closes.
- [ ] Changelog postscript: `page-changelogs/replacement-arrangement-changelog.md` (R10).

## T3 — my-request-history: keywords, dead code, bug fix (R1.4, R4, R5)

- [ ] 3 cancel-request modal bodies (static L~275, quick-cancel JS L~609, batch JS L~656):
      wrap "undone" in `warn-keyword` (R1.4). Wording unchanged.
- [ ] Delete `confirmCancelRequest()` (R5) — re-grep callers first (zero expected).
- [ ] Bug fix per design §4: page-global `var currentRequestId = null;`; store in
      `openModalById` after lookup guard; static button →
      `onclick="openCancelConfirm(currentRequestId)"`; `openCancelConfirm(requestId)` sets
      `pendingCancelId = requestId` (R4.1–R4.3).
- [ ] Chip ✕ tooltips ×4 (L~356/360/363/366) + "Reset all filters" (L~198): `title=` →
      `data-tip`, same text (R7.1).
- [ ] 3 modal-close buttons → `data-tip="Close"` (R7.2).
- [ ] Changelog postscript: `page-changelogs/my-request-history-changelog.md` (R10).

## T4 — Card flip-backs: shared map + 42 inline overrides (R6)

*(split guidance: if this task threatens the 2h ceiling, split T4a = map + warn/attention
overrides, T4b = info overrides — no reordering)*

- [ ] `partials/ui-summary-bar.blade.php` `$descriptions` map: one keyword span per entry —
      warn for card-conflict/card-pending/card-rejected, info for the rest (R6.1/R6.2).
- [ ] All 42 inline overrides per design §5 table (9 pages): same rule; keep secondary
      `<strong>`s plain; one keyword per description (R6.3). Content-anchor every edit.
      *(T4a: attention/warn overrides first; T4b: the info overrides.)*
- [ ] Page-header `description` props (7) untouched (R6.4). Card fronts untouched.
- [ ] Visual check: hover an attention card + an info card on 2 different pages.
- [ ] Changelog postscripts (R10): cohort-timetable, my-timetable, replacement-history,
      student-my-timetable changelogs (arrangement + my-request-history + home + approval +
      venue already get postscripts in their tasks).

## T5 — Tooltip parity: remaining files (R7)

- [ ] ui-week-nav print-btn + venue print-btn: `title=` → `data-tip` (R7.1).
- [ ] Arrangement ★ dropdown star: `title=` → `data-tip` (standalone element — no
      updateFavStar conflict).
- [ ] modal-close `data-tip="Close"`: ui-class-detail-modal, ui-cancel-class-modal,
      ui-logout-modal, replacement-home ×2, arrangement ×1, venue ×1 (R7.2).
- [ ] `layouts/login-template.blade.php` theme-toggle: `title=` → `data-tip` (login MOCKUP
      file stays OUT). Note the swap in the commit message — no changelog file exists for the
      login layout (design §8).
- [ ] **Request-approval (apply LAST — parallel session is reshaping this file; re-grep every
      anchor immediately before each edit):**
      - 4 chip `title="Remove"` → `data-tip="Remove"` — content-anchor each
        `filter-chip-remove` template string (Status / Urgency / Week / Search chips;
        L~778/781/785/788 will drift) (R7.1).
      - 3 modal-close buttons → `data-tip="Close"` (Detail close, Reject, Approve-notes)
        (R7.2).
- [ ] If any converted tooltip clips at the viewport edge, add `data-tip-pos="left"`
      (expected: none).
- [ ] Changelog postscripts: venue-timetable, request-approval, replacement-home (R10).

## T6 — New spec coverage (R1 scenario, R4 scenario)

- [ ] Spec test (a): leave-guard body has ≥1 `.warn-keyword`; change-guard body has 0.
- [ ] Spec test (b): pending request detail modal → Cancel Request → overlay → Yes →
      status Cancelled + undo toast (fails by timeout on pre-fix code).
- [ ] Both live in `tests/` (confirm-guards.spec.ts or a new focused spec file — implementer's
      choice, follow existing file organization).

## T7 — Verify (closes criteria 1–5)

- [ ] Criterion 1: class-count audit per R1 across the 6 warning modals + change-guards +
      submit + no-selection + tooltips.
- [ ] Criterion 3: repo grep — `title="` in the 3 scoped dirs returns only Breeze-owned
      `:title=` props; 13 `data-tip="Close"` present.
- [ ] Criterion 4: re-run the 4 spec files — ALL pass unchanged, plus the 2 new tests.
- [ ] Criterion 5: postscripts present on all 9 pages (grep `page-changelogs/` for this
      change's postscript marker).
- [ ] Request-approval edits re-verified against the then-current working tree; rebase-check
      before staging (parallel session).
- [ ] `node --check` on any edited JS-bearing files; Blade view cache clear + server restart
      if blades changed.
