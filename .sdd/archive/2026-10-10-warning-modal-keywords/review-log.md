## Batch 1 (proposal.md) Round 1 — 2026-10-09 08:21 UTC

### 🔴 Fixed
- (none — reviewer is read-only; no edits made)

### 🟡 Addressed
- **OUT-rule vs D8 tension (validated, needs a note in design.md)**: The OUT list says
  "styling/markup only — keeps specs assertable", but D8 adds new confirm-button label text
  ("Yes, Leave Page" etc.). I verified against `tests/` that no current Playwright spec asserts
  the confirm-button text on `#confirmModal` (ui-regression L192/L207 use `#modalConfirmBtn` id;
  L238's `has-text("Confirm")` targets the request-approval reject modal, unrelated) and no spec
  asserts the change-guard sentences ("Continue?", "clear them", "remove them") — so the change
  is safe today. design.md should record this validation and require a re-grep of `tests/` at
  implementation time (the parallel session may add specs).
- **Criterion 5 vagueness**: "card-bearing pages as applicable" is not enumerable from the
  proposal alone; design.md should enumerate the 9 flip-card pages so the changelog checklist is
  testable.
- **Criterion 1 testability**: "exactly one red keyword" is only robustly testable as class
  counting — suggest criteria state it as "exactly one `warn-keyword` span per irreversible
  modal; zero `warn-keyword` in change-guard/informational contexts" once class names are fixed
  in design.md.

### 🔴 Outstanding
1. **`showConfirmModal` call-site count is wrong — a decision is hidden inside task D7/D8.**
   Proposal D7 says "all 4 call sites are lossy actions" and D8 maps only 4 labels; the brief
   claims "no non-destructive call site exists". Both are factually wrong: `showConfirmModal` is
   called 6 times in replacement-arrangement — including `proceed()` "Confirm Your Selection"
   (L1836, the Submit Request flow) and the informational "No Selection" modal (L1818, null
   callback). Since `modalConfirmBtn`'s class is shared, applying D7 as written puts a red
   `btn-danger` confirm button on an informational modal and on the submit confirmation —
   directly contradicting the proposal's own design-language rule ("Red is reserved").
   The proposal must surface this now: either (a) make the danger styling conditional
   (per-call-site flag or only when a red keyword is present in the body), or (b) explicitly
   decide submit/no-selection get red too, and define their confirm labels (D8 currently leaves
   them to fall back to default "Confirm" — which is inconsistent with (b)).
2. **Success criterion 3 is unsatisfiable as written.** It demands "zero native `title=`
   tooltips remain on app elements (Breeze excluded)", but scope F converts only the 12 listed
   by the brief. A repo-wide grep of `*.blade.php` finds 2 more app-UI native tooltips the brief
   missed: `layouts/login-template.blade.php:342` and `login-UI-design-template.blade.php:443`
   (`title="Toggle theme"` on the theme-toggle button). Either add these 2 conversions to scope
   F (14 total) or narrow criterion 3 to "all 12 audited tooltips converted" + explicitly OUT the
   login pages. As written, /sdd-verify will fail criterion 3 or force an out-of-scope edit
   mid-implementation.
3. **Affected files omits `replacement-home-UI-design-template.blade.php`.** Brief item 12b
   assigns it 2 live modal-close buttons (`data-tip="Close"`, F), and scope item 13 covers "all
   13 live modal-close buttons" — but the Affected files list (and criterion 5's changelog list)
   skips replacement-home entirely. An implementer following the file list will miss 2 edits and
   fail criterion 3. Add the file (F) to Affected files and to criterion 5's changelog scope.

### ⚖️ Review Verdict
FAIL — 3 blocking items, all narrow text/scope fixes to proposal.md (no exploration needed):
resolve the 6-call-site conflict in D7/D8, reconcile criterion 3 with the actual `title=`
inventory (12 vs 14), and add replacement-home to Affected files. Everything else checks out:
brief coverage is complete, exclusions are faithful, the design language is preserved (no
keyword on the recoverable change-guard), bug-fix anchors verified in source
(`confirmCancelRequest()` zero callers confirmed; `openCancelConfirm()`/`pendingCancelId`
no-op confirmed at my-request-history L261/L786/L607–625), and the parallel-session risk is
adequately covered (content-pattern anchors, request-approval last, rebase-check).

> Orchestrator note (2026-10-09): reviewer claim #2 partially corrected —
> `login-UI-design-template.blade.php` does NOT exist; only `layouts/login-template.blade.php:342`
> carries a login `title="Toggle theme"`. Audited total = **13 conversions** (12 + 1 login),
> not 14. Claim #1 stand corrected upward: actual call-site count = **7**, not 6 (L1675, L1765,
> L1818, L1836, L1865, L1898, L1917). Blocking substance unchanged.

## Batch 1 (proposal.md) Round 2 — 2026-10-09

### 🔴 Fixed
- **Blocker 1 (call-site conflict in D7/D8) — RESOLVED.** D7/D8 now use a conditional mechanism (`showConfirmModal(title, bodyHtml, callback, opts)` with `opts.danger` / `opts.confirmLabel`) and enumerate all **7** call sites. Re-verified against source: exactly 7 `showConfirmModal(` call sites exist in replacement-arrangement (L1675, L1765, L1818, L1836, L1865, L1898, L1917), matching the proposal's list line-for-line. The previously-merged L1675 site is confirmed a genuine separate call site (`onVenueChange`, "…will **clear** them. Continue?" — distinct from `confirmChangeWithSelection` at L1765 "…will **remove** them"). The 5-vs-2 split is semantically sound: L1836 ("Confirm Your Selection") and L1818 ("No Selection", null callback) are excluded with rationale, and that rationale coheres with the design language — criterion 1 mandates zero `warn-keyword` spans in those two modals, so a `btn-primary` confirm button is internally consistent (red stays attached to warning sentences only). The Round-1 contradiction (red `btn-danger` on an informational modal) is eliminated.
- **Blocker 2 (unsatisfiable criterion 3) — RESOLVED.** Criterion 3 now reads "all **13** audited converted + grep returns only Breeze-owned `:title=` props". Re-inventoried the repo: exactly **13 live** app-UI native `title=` tooltips exist in the three criterion-scoped dirs, and all 13 appear in proposal scope F with matching line numbers (ui-week-nav:18, venue-timetable:371, arrangement:2615, my-request-history:198/356/360/363/366, request-approval:778/781/785/788, login-template:342). Replacement-home's commented-out `kbShortcutsBtn` title is correctly excluded from the count. The criterion is now satisfiable by construction.
- **Blocker 3 (missing Affected files) — RESOLVED.** `replacement-home-UI-design-template.blade.php` (F) and `layouts/login-template.blade.php` (F) are now in Affected files; criterion 5 enumerates replacement-home + defers the 9 card pages to an explicit design.md list. An implementer following the file list can no longer miss the 2 replacement-home modal-close edits.
- **Round 1 🟡 items all carried into the proposal:** criterion 1 is now class-counting (`warn-keyword`/`info-keyword` spans) ✅; criterion 4 requires a re-grep of `tests/` at implementation time for new `#modalConfirmBtn` specs ✅; criterion 5 requires design.md to list all 9 flip-card pages ✅.

### 🟡 Addressed
- All three Round 1 🟡 items (tests/ re-grep, criterion 1 class-counting, criterion 5 enumeration) are now textually present in proposal.md — see 🔴 Fixed above.

### 🔴 Outstanding
- (none)

### 🟡 New findings this round (non-blocking; carry to design.md or fix in passing)
1. **Stale "12" in the Why paragraph** (proposal L12): "12 controls use native `title=` tooltips" contradicts scope F's 13 (the login-template tooltip was added after that paragraph was written). One-word fix; no implementation impact since criterion 3 is count-explicit.
2. **The orchestrator correction note is factually wrong about file existence**: `resources/views/login-UI-design-template.blade.php` **does exist** (at `resources/views/` root, not under `ui-design-templates/`; line 443 carries `title="Toggle theme"`). The exclusion is nevertheless substantively correct — the archived `oop-blade-refactor` SDD records it as a **route-less standalone mockup** ("not the real login page and is not touched"), and criterion 3's grep scope (`{ui-design-templates,partials,layouts}/*.blade.php`, non-recursive) excludes it, so the 13-count and criterion 3 both stand. Recommend adding this file by name to the OUT list so the exclusion is deliberate and future greps don't "discover" it again.
3. **"replacement-home has one [commented-out modal-close]" is inaccurate** (proposal L81, brief 12b): a repo-wide grep finds exactly 13 `modal-close` occurrences, all live — the commented-out markup in replacement-home is the `kbShortcutsBtn` (L198, a tooltip-bearing *open* button), not a modal-close. Harmless (the "skip commented markup" instruction can't cause a wrong edit), but the parenthetical should point at the kbShortcutsBtn instead.

### ⚖️ Review Verdict
**PASS** — Outstanding is empty. All 3 Round-1 blockers are verified fixed against source; the proposal, brief, and design language are mutually consistent. The 3 new 🟡 items are record-accuracy nits that do not affect implementation direction; fold them into design.md or the proposal in passing. Ready for /sdd-apply (Batch 2).

> Orchestrator: PASS accepted — proposal FROZEN. The 3 🟡 nits fixed in passing below (declarative-only). Verified: `resources/views/login-UI-design-template.blade.php:443` exists (route-less mockup) — added to OUT by name.

## Batch 2 (design.md) Round 1 — 2026-10-09

### 🔴 Fixed
- (none — reviewer is read-only; no edits made)

### 🟡 Addressed
- **Batch 1's three non-blocking nits confirmed landed in the frozen proposal**: "13 controls" in Why (L13), `resources/views/login-UI-design-template.blade.php` named in OUT (L96–97), and the kbShortcutsBtn parenthetical on L82. Not re-litigated.
- **Verified sound against source (no issue)**: §2's `classList.toggle` mechanism is correct — `hideConfirmModal`'s cancel-button reset (`.btn-outline`, L1120/L1758–1763) is genuinely untouched, and since `classList.toggle` re-derives both classes on *every* `showConfirmModal` call, no stale `btn-danger` can leak between modal opens (self-healing across the 7 call sites). `--color-error` token exists (theme.css L42/L109). Clear All `<strong>ALL</strong>` anchor confirmed (L1873). Design-language fidelity is otherwise clean: no keyword on change-guards, `<br>` placements match the brief exactly (before "Are you sure…", after "?" on Clear All, before "Continue?"), card fronts untouched, red reserved, attention = conflict/pending/rejected. §8's 9-page enumeration satisfies criterion 5, and the login-layout changelog gap has a stated workaround. §6's 13+13 tooltip counts match the Round-2 verified inventory.

### 🔴 Outstanding
1. **§5's inline-override catalog is factually wrong for 7 of 9 rows — verified against source. An implementer following it will miss ~24 of 37 override edits, including 10 attention-card (warn) overrides, violating frozen proposal E9/E11 and criterion 1.** §5 presents the table as "catalogued" (no re-grep instruction for it, unlike §3's anchors), so the implementer treats it as complete. Actual state, re-verified line-by-line:

   | Page | Design says | Source reality |
   |---|---|---|
   | CohortTimetable | ×5 (L87–95) | ✅ correct |
   | MyTimetable | ×4 (L119–127) | **×5** (L119/121/123/125/127) |
   | replacement-history | ×5 (L132–136) | ✅ correct |
   | my-request-history | ×2 (L238–240); attention "none" | **×5** (L236–244); attention **card-pending L242, card-rejected L244** — "none (approved/hours are informational)" is false |
   | replacement-arrangement | ×2 (L1031–1033); attention "none" | **×4** (L1031–1037); attention **card-pending L1035, card-conflict L1037** |
   | replacement-home | "none (map fallback)" | **×3** (L227/229/231), incl. **card-conflict L226–227** |
   | venue-timetable | "none (map fallback)" | **×5** (L425–433), incl. **card-conflict L432–433** ("Cannot book") |
   | request-approval | "none (map fallback)" | **×5** (L312–320), incl. **card-pending L314, card-rejected L318** |
   | student-my-timetable | "none (map fallback)" | **×5** (L55–63), incl. **card-pending L61, card-conflict L63** |

   Consequence: if followed as written, `warn-keyword` never reaches the attention-card flip-backs on 6 pages and `info-keyword` misses 3 pages entirely — criterion 1's class-counting check fails at /sdd-verify, or the implementer improvises an unaudited catalog mid-edit. **Required fix**: rebuild the §5 table from a fresh `grep "'description' =>" resources/views/ui-design-templates/` (the parallel session may shift request-approval lines further; keep content anchors). Note the rebuild surfaces one semantic call the design should make explicitly: the `card-conflict`-classed "Unavailable"/"Cannot select/book" cards (arrangement L1036, venue L432) describe a *recoverable* state under an attention class — the brief's rule keys red to card class, so red applies, but design.md should say so deliberately rather than by accident of the missing rows.

### 🟡 New findings this round (non-blocking)
2. **§4's `currentRequestId` does not exist in source** (grepped `currentRequestId`/`currentRequest`/`selectedRequestId`: zero matches). The `cancelRequestBtn` (L261) is *static markup* — `openModalById` (L707) renders via shared `DetailModal.render(...)` and only toggles the button's visibility (L766); no variable holding the viewed id is ever stored, and an inline `onclick` resolves in global scope, not "the detail-modal render scope". The proposed `onclick="openCancelConfirm(currentRequestId)"` is therefore incomplete as written: `openModalById` must first be extended to store the id in a page-global (e.g. `currentRequestId = id;`). The "verify variable name at edit time and adapt" hedge bounds the risk (an implementer who verifies will discover the gap and the fix has only one sane shape), so this is 🟡 not 🔴 — but the wording "available in the detail-modal render scope" asserts a fact that is false and should be replaced with the affirmative mechanism, since this is the change's only behavioral fix and its original failure mode is a silent no-op.
3. **§7 gives criterion 2 no automated coverage** — only a manual live check. The detail-modal cancel bug failed *silently* for exactly this reason; recommend one small spec (open detail modal → Cancel Request → confirm modal appears → Yes → request status becomes Cancelled) alongside the criterion-1 spec already planned.
4. Line numbers in §2's matrix have drifted ~6 lines in current source (calls now at L1675/1769/1824/1842/1871/1904/1923). Harmless — §3 mandates content-pattern re-greps — but implementers should expect drift everywhere and trust anchors, especially in request-approval.

### 💡 Optional Suggestions
- Per AGENTS.md §10 convention 7, consider recording the two new classes under a "Promoted to shared" note in design.md — they are born-shared in `theme.css` rather than promotions, but the record costs one line and keeps the convention audit trail uniform.
- §5's per-card keyword picks for the map (SCHEDULED CLASSES, TEACHING HOURS, …) are good defaults; when extending them to the newly-catalogued overrides, watch MyTimetable L121 / CohortTimetable L89 ("each slot = 30 minutes") — the design's one-keyword rule already correctly demotes these to plain `<strong>`.

### ⚖️ Review Verdict
**FAIL** — 1 blocking item: §5's inline-override catalog is wrong for 7 of 9 rows against current source (missing ~24 overrides incl. 10 attention-card treatments), which would cause missed requirements and criterion-1 verification failure. The fix is narrow and mechanical — re-grep and rebuild the §5 table (plus the one deliberate red-on-"Unavailable"-card-conflict call) — no re-exploration needed. Items 2–3 are one-sentence design.md additions worth folding in during the same pass. Everything else (CSS mechanism, showConfirmModal opts design, call-site matrix semantics, line breaks, design language, test plan structure, changelog enumeration) is verified sound against source.

## Batch 2 (design.md) Round 2 — 2026-10-09

### 🔴 Fixed
- **Blocker 1 (§5 catalog) — RESOLVED and verified against a fresh source re-grep.** Reviewer re-ran `'description' =>` across `resources/views/ui-design-templates/`: **42 card overrides** exist, and the rebuilt §5 table matches them line-for-line — all 9 rows, per-row counts (CohortTimetable ×5 L87–95, MyTimetable ×5 L119–127, replacement-history ×5 L132–136, my-request-history ×5 L236–244, arrangement ×4 L1031–1037, replacement-home ×3 L227–231, venue ×5 L425–433, request-approval ×5 L312–320, student-my-timetable ×5 L55–63), and the line-number ranges are all currently accurate. Cross-checked every row's **warn/info assignment against the actual `'class' => 'card-…'`** on the paired line: every `card-pending`/`card-conflict`/`card-rejected` is marked warn (my-request-history L242/L244, arrangement L1035/L1037, venue L433, request-approval L314/L318, student L61/L63, replacement-home L227, MyTimetable L125/L127, CohortTimetable L95, replacement-history L134), and every non-attention class is info — including the easy-to-get-wrong ones: request-approval L320 "decided" sits under `card-total` → info ✅, venue L429 under `card-replacement` → info ✅, CohortTimetable L91 under `card-replacement` → info ✅. venue L335 is confirmed a `ui-page-header` description prop and is excluded. The deliberate red-on-"Cannot select/book" call is now stated on the table (decision, not accident), and the content-anchor/re-grep instruction is on the table itself. Round 1's "miss ~24 edits" failure mode is eliminated.

### 🟡 Addressed
- **🟡 #2 (§4 currentRequestId) — verified against source.** The rewritten 4-step mechanism matches reality: L261 is static markup `onclick="openCancelConfirm()"`; `openModalById(id)` (L707) has the lookup guard `if (!r) return;` and the design correctly places `currentRequestId = id;` *after* it (a failed lookup won't clobber the previous id); `openCancelConfirm()` (L786) currently takes no param and never sets `pendingCancelId` — the proposed signature + store fixes exactly that; `grep currentRequestId` over `resources/views/` returns **zero matches** (no collision, as claimed). The global-scope-at-click-time rationale is sound — existing inline `onclick`s on this page already resolve these page-script functions as window globals, so a top-level `var` behaves identically.
- **🟡 #3 (§7 criterion-2 spec)** — the (a)/(b) coverage is now written, and (b)'s flow (detail modal → Cancel Request → confirm overlay → Yes → Cancelled + undo toast, fails by timeout on pre-fix code) matches the actual wiring (`pendingCancelId` + `confirmCancelAction` listener).
- **💡 both Round-1 suggestions taken** — §1 born-shared convention note present; §5 states the one-keyword rule and explicitly demotes the "each slot = 30 minutes" `<strong>`s (MyTimetable L121, CohortTimetable L89 — both confirmed present in source) to plain `<strong>`.
- **Round-1 item 4 (line drift) — accepted as-is.** §3/§5 now carry explicit content-anchor mandates; §2's matrix keeps "~" markers. Drift re-verified as real and growing (call sites now at L1679/1791/1846/1864/1893/1926/1945 — still exactly **7 call sites + 1 definition**, count intact), but under the anchor discipline this is harmless. No flag.

### 🔴 Outstanding
- (none)

### 🟡 New findings this round (non-blocking)
1. **§5's intro prose contradicts its own (correct) table.** The text says "~38 card overrides + 1 page-header prop excluded", but the table rows sum to **42**, and the fresh grep returns 49 `'description' =>` hits of which **7** are `ui-page-header` description props (venue L335, replacement-home L171, MyTimetable L82, CohortTimetable L47, student-my-timetable L28, replacement-history L111, my-request-history L159) — not 1. The table is complete and authoritative; only the framing numbers are stale, and the exclusion note singles out venue L335 when 6 sibling page-header props deserve the same "NOT a card; do not touch" note. One-line fix: "~42 card overrides; 7 page-header description props excluded — none touched."

### 💡 Optional Suggestions
- **§7 has a duplicated paragraph**: the "Manual live checks (server + Playwright): leave-guard modal shows LOST red…" block appears twice, verbatim (before and after the new-coverage bullets) — editing residue from this round's insertion. Delete one copy; purely cosmetic.

### ⚖️ Review Verdict
**PASS** — Blocker 1 is verified fixed against a fresh source re-grep (42/42 overrides, class-keyed warn/info correct in every row), §4's mechanism is verified against the actual `openModalById`/`openCancelConfirm` source, §7's new spec matches the bug's wiring, and design.md remains consistent with the frozen proposal (E9/E11, criterion 1, criterion 5's 9-page enumeration). The two new findings are prose-accuracy nits that don't change implementation direction — fix the "~38/×1" counts and drop the duplicated §7 paragraph in passing. Ready for /sdd-apply.

> Orchestrator: PASS accepted — design.md FROZEN. Both nits fixed in passing below (declarative-only).

## Batch 3 (specs/) Round 1 — 2026-10-09

### 🔴 Fixed
- (none — reviewer is read-only; no edits made)

### 🟡 Addressed
- **Scope coverage (items 1–14) verified — all map to R1–R8.** 1→R1.1/R1.2/R2.1; 2→R1.3/R2.2; 3→R1.5/R2.3; 4→R1.4; 5→R5; 6→R4.1–R4.3; 7→R3.1/R3.3; 8→R3.2/R3.3; 9→R6.1; 10→R6.2; 11→R6.1 ("regardless of source" + "exactly one" covers the no-bare-label audit) + R6.4; 12→R7.1; 13→R7.2; 14→R8. No orphan scope items.
- **No new decisions — verified against design.md §1–§8.** Class names `warn-keyword`/`info-keyword` match §1 exactly (tokens-only, `text-transform`, no hex — R8 is a faithful restatement). R3.1's signature, `classList.toggle` re-derivation, default "Confirm" fallback, and the untouched `.btn-outline` cancel reset match §2 line-for-line; R3.3's 5-vs-2 split matches §2's matrix and the frozen D7/D8 exclusion rationale. R4's 4-step mechanism (page-global `currentRequestId` set after the lookup guard → `openCancelConfirm(currentRequestId)` → `pendingCancelId`) matches the Batch-2-Round-2-corrected §4. R6 is correctly class-keyed per §5, includes both description sources, the deliberate red-on-"Cannot select/book" call (R6.5), the one-keyword rule with the "30 minutes" `<strong>` demotion (R6.3), and the 7 page-header exclusions (R6.4). R7's counts, grep scope, Breeze `:title=` carve-out, and the route-less login mockup exclusion all match §6 and criterion 3. R2's `<br>` placements match §3's table; spec correctly stays silent on change-guard wording unification (design froze "NOT done" — consistent, not contradicting the proposal's optional allowance).
- **Count consistency — all verified internally and against frozen artifacts:** 13 `title=` conversions (1+1+1+1+4+4+1), 13 modal-close (3 partials + 3 + 3 + 2 + 1 + 1), 42 card overrides (R6.1 ↔ §5 table), 7 page-header props (R6.4), 7 call sites = 5 danger + 2 excluded (R3.3), 6 keyword-bearing modals = R1.1–R1.4. All coherent with criterion 1 and design §5/§6.
- **Testability — every R is verifiable:** R1/R6 by class-counting (spec test a), R2/R3 by DOM class/text assertions, R4 by spec test b (timeout-loud on pre-fix code), R5/R7/R8 by grep. No unverifiable wording found.
- **🟡 New finding (non-blocking, one-line fix): R1's "zero `warn-keyword` spans anywhere else" is literally false given R6.1** — attention-card flip-backs *do* carry `warn-keyword` spans. R1 clearly means "zero outside the enumerated warning modals" (R1.5/R1.6 + criterion 1's enumeration), but as written a literal verifier could fail card backs against R1. Reword to "zero `warn-keyword` spans in any other modal body or tooltip" to close the internal contradiction.

### 🔴 Outstanding
1. **Success criteria 4 and 5 have NO spec requirement — the only unmapped frozen content.** Criterion 4 (all existing Playwright specs pass unchanged; re-grep `tests/` at implementation start for `#modalConfirmBtn` text assertions before applying D8 labels) and criterion 5 (changelog postscripts on replacement-arrangement, my-request-history, request-approval, venue-timetable, replacement-home + the 9 card pages per design §8) appear nowhere in R1–R8. The content exists in frozen artifacts (design §7/§8; proposal criterion 4 re-grep mandate; AGENTS.md standing rule), so no new decision is needed — but a spec whose requirements omit 2 of 5 success criteria invites a tasks.md that skips the regression run and the ~10 changelog postscripts, which is exactly the missed-requirement failure this batch exists to prevent. **Required fix (additive, declarative):** add e.g. **R9 — No regression**: existing Playwright specs (`confirm-guards`, `cancel-class`, request-history, venue, ui-regression) SHALL pass unchanged; `tests/` SHALL be re-grepped for `#modalConfirmBtn` text assertions before labels are applied; and **R10 — Changelog postscripts** per design §8's page list (login layout: note in commit message per §8). No other artifact needs to change.

### ⚖️ Review Verdict
**FAIL** — one narrow blocking item: criteria 4 and 5 are unmapped; add R9/R10 (pure restatement of frozen design §7/§8, no new decisions). Everything else is verified clean — full scope coverage, zero spec-introduced behavior, exact count consistency (13/13/42/7/7), and testable requirements throughout; fold the R1 "anywhere else" scoping one-liner into the same pass.

## Batch 3 (specs/) Round 2 — 2026-10-09

### 🔴 Fixed
- **Round-1 blocker (criteria 4/5 unmapped) — RESOLVED.** R9 and R10 now exist and are faithful, decision-free restatements of the frozen artifacts, verified line-by-line:
  - **R9 ↔ design §7 + proposal criterion 4**: R9.1's regression list (`confirm-guards`, `cancel-class`, request-history, venue-timetable, ui-regression) matches §7's run list exactly; R9.2's `tests/` re-grep for `#modalConfirmBtn` text assertions **before the D8 labels are applied** matches §7's re-grep mandate and the proposal's criterion-4 wording, including the parallel-session rationale. Spot-checked `tests/`: `confirm-guards.spec.ts`, `cancel-class.spec.ts`, `venue-timetable.spec.ts`, `ui-regression.spec.ts` all exist with those exact names.
  - **R10 ↔ design §8**: the page list is exactly §8's — replacement-arrangement, my-request-history, request-approval, venue-timetable, replacement-home + the 4 additional card-keyword pages (CohortTimetable, MyTimetable, replacement-history, student-my-timetable; arrangement already listed) = 9 pages. The login-layout gap is handled with §8's stated workaround (no changelog file → note the tooltip swap in the commit message), not silently dropped. No new scope, no new mechanism, no contradiction with any frozen artifact.
- **Round-1 🟡 (R1 "anywhere else") — RESOLVED.** R1's zero-rule now reads "zero `warn-keyword` spans in any other MODAL BODY or TOOLTIP", with an explicit parenthetical that attention-card flip-backs carry `warn-keyword` by R6.1 and are not R1 violations. The R1↔R6.1 contradiction is closed exactly as suggested in Round 1. **R1.5/R1.6 are not weakened**: change-guard/"Confirm Your Selection"/"No Selection" are modal bodies (still zero-keyword), and `data-tip` tooltips remain plain text — both fully inside the reworded rule's scope. R1.1–R1.4's positive enumeration and the spec-test-a scenario are untouched.
- **No regressions elsewhere in the spec.** Re-checked R1–R8 against the Batch 3 Round 1 verification record: scope items 1–14 still map to R1–R8 unchanged; counts (13 `title=` swaps, 13 modal-close, 42 card overrides, 7 page-header exclusions, 7 call sites = 5 danger + 2 default) are all still textually present and coherent; R6.1's "regardless of description source" + design §5 table reference, R6.3's one-keyword rule with the "30 minutes" `<strong>` demotion, R6.5's deliberate red-on-"Cannot select/book" call, R7.3's grep scope with Breeze `:title=` carve-out and the route-less login mockup exclusion, and R8's tokens-only rule all survive the edit intact. The spec's frozen-baseline header correctly names proposal.md, design.md, and explore-brief.md.

### 🔴 Outstanding
- (none)

### 💡 Optional Suggestions
- **R9.1's "request-history" is not a spec filename**: `tests/` contains no `request-history*.spec.ts` — my-request-history coverage lives inside `ui-regression.spec.ts` (L9/L124/L248). This wording is inherited verbatim from frozen design §7, so it is a faithful restatement, not a spec defect; but at implementation, the regression run should be interpreted as "run the 4 named spec files" (request-history coverage rides along in ui-regression). Optionally align the wording when tasks.md is written so nobody hunts for a nonexistent file.

### ⚖️ Review Verdict
**PASS** — Outstanding is empty. R9/R10 are verbatim-faithful restatements of frozen design §7/§8 with zero new decisions, the R1 rewording closes the R6.1 contradiction without weakening R1.5/R1.6, and no regressions were introduced in R1–R8. All 5 proposal success criteria are now mapped to spec requirements. Ready for /sdd-apply (Batch 4: tasks.md).

> Orchestrator: PASS accepted — specs/warning-language/spec.md FROZEN. The 💡 note is folded into tasks.md T0 (regression run = 4 named spec files; request-history coverage rides in ui-regression).

## Batch 4 (tasks.md) Round 1 — 2026-10-09

### 🔴 Fixed
- (none — reviewer is read-only; no edits made)

### 🟡 Addressed
- **R1–R10 coverage audited task-by-task — one gap found (see 🔴 Outstanding); all other requirements map cleanly.** R1.1–R1.5/R2.1–R2.3/R3.1–R3.4 → T2 (including the R3.4 cancel-reset regression check); R1.4/R4.1–R4.3/R5 → T3; R6.1–R6.4 → T4 (42 overrides per frozen design §5 table, one-keyword rule and page-header exclusions restated); R8 → T1 (theme.css placed before all consumers ✅); R9.1 → T0 baseline + T7 re-run, correctly using the Batch-3 💡 interpretation ("4 spec files; request-history rides inside ui-regression" — nobody will hunt for a nonexistent file); R9.2 → T0's `tests/` re-grep with a STOP gate; R10 → 9 postscripts distributed T2/T3/T4/T5 (4+1+1+3 = 9 pages, coherent with design §8). Scope-creep check clean: no wording edits (T3 states "Wording unchanged"; change-guards get `<br>` only, no keyword, no unification), no data-tip HTML upgrade, no extra pages, no card fronts; T5's conditional `data-tip-pos` hedge is design §6 as written, not creep.
- **Ordering is sound.** T0 safety rails → T1 (shared CSS) → T2/T3/T4/T5 (consumers) → T6 (new specs) → T7 (verify gates for all 5 criteria). T0 genuinely protects against the two known risks: the git-status snapshot + "do NOT stage" + rebase-check handles the parallel session's dirty tree, and the R9.2 re-grep runs before any label is applied.
- **T7's criterion checks are faithful**: criterion 1 class-count audit covers the 6 warning modals + change-guards + submit + no-selection + tooltips; criterion 3 grep matches R7.3's scope; criterion 4 = 4 spec files + 2 new tests; criterion 5 = grep for postscript markers across 9 pages. Spec tests (a)/(b) in T6 match R1's scenario and R4's scenario exactly, including the timeout-loud rationale.
- **Task granularity verified**: every task ≤ 2h with all decisions already frozen — T2/T3/T5 checkbox text cites design § numbers and exact labels/anchors; an implementer makes zero further decisions (T6's "confirm-guards or a new focused spec file" choice is explicitly delegated with a stated rule, acceptable).

### 🔴 Outstanding
1. **R7 is unmapped for request-approval — 7 of the 13 audited tooltip edits have NO task. An implementer following tasks.md will skip them, and T7's own criterion-3 grep will then fail.** Verified against current source just now: `request-approval-UI-design-template.blade.php` still carries the 4 chip `title="Remove"` conversions (L778/781/785/788 — Status, Urgency, Week, Search chips; proposal F12, brief item 12a, R7.1) and the 3 live `modal-close` buttons without `data-tip="Close"` (L327 `closeModal()`, L346 `closeRejectModal()`, L375 `closeApproveNotesModal()`; proposal F13, brief item 12b, R7.2). But: T3 covers only my-request-history's 5 `title=` swaps + 3 modal-closes; T5's modal-close list enumerates only 7 of 13 (3 partials + replacement-home ×2 + arrangement ×1 + venue ×1 — request-approval ×3 absent) and T5's `title=` list (2 print buttons + ★ + login toggle) omits the 4 request-approval chips entirely. No other task performs any request-approval edit, yet T0 says request-approval is "expected dirty" and T7 says "request-approval edits re-verified against the then-current working tree" — the checklist re-verifies edits that no task creates. This is doubly dangerous because request-approval is the one file the parallel session is actively reshaping: it needs the "applied LAST + re-grep + rebase-check" discipline the most, and it currently has zero checkboxes. **Required fix (additive, mechanical):** add to T5 — (a) 4 request-approval chip `title="Remove"` → `data-tip="Remove"` (content-anchor each `filter-chip-remove` template string; L~778/781/785/788 will drift), and (b) request-approval ×3 in the modal-close `data-tip="Close"` list. This also makes T5's request-approval changelog postscript coherent (it currently references a file no task edits).

### 🟡 New findings this round (non-blocking)
1. **R10's login-layout clause is not operationalized in any task.** R10/design §8 require the login tooltip swap to be "noted in the commit message" (no changelog file exists), but T5's login-template checkbox and T7's checklist never mention it — an implementer following tasks.md verbatim will forget it. One-line fix: append "note swap in commit message (no changelog file — design §8)" to T5's login checkbox.
2. **T0's R9.2 STOP gate names the wrong task boundary**: "if any assert the affected modals' button text, STOP and surface before T4" — T4 is card flips; the D8 labels it protects land in **T2**. The re-grep's position in T0 still precedes T2 in practice, so the substance holds, but the wording should say "before T2" so a mid-run spec addition is caught at the right gate.
3. **T4 is the only task at risk of breaching the 2h ceiling**: 42 content-anchored inline-override edits + the shared map + a visual check, each requiring a re-grep first. Mechanical, but if it runs long, the natural split is T4a (map + warn/attention overrides) / T4b (info overrides) — no reordering needed. Judgment call for the orchestrator; not blocking.

### ⚖️ Review Verdict
**FAIL** — one blocking item: R7's request-approval edits (4 chip `title=` conversions + 3 modal-close `data-tip="Close"`) are missing from tasks.md entirely, leaving 7 of the 13 audited tooltip edits unmapped and T7's criterion-3 check self-defeating. The fix is two checkbox additions to T5 plus the three 🟡 one-liners. Everything else — R-coverage, ordering, granularity, safety rails, scope discipline — is verified clean.

## Batch 4 (tasks.md) Round 2 — 2026-10-09

### 🔴 Fixed
- **Round-1 blocker (R7 unmapped for request-approval — 7 of 13 tooltip edits missing) — RESOLVED.** T5 now carries a dedicated request-approval sub-block headered "**apply LAST — parallel session is reshaping this file; re-grep every anchor immediately before each edit**", containing exactly the two required additions, verified against current source: (a) the 4 chip `title="Remove"` → `data-tip="Remove"` conversions with per-chip content-anchor instructions on the `filter-chip-remove` template strings (Status/Urgency/Week/Search — all 4 confirmed live at L778/781/785/788 right now, so the "L~ will drift" hedge is correct and the anchors, not numbers, are the operative instruction), and (b) request-approval ×3 in the modal-close `data-tip="Close"` coverage (Detail close / Reject / Approve-notes — matching the actual `closeModal()`/`closeRejectModal()`/`closeApproveNotesModal()` buttons at L327/346/375). T5's changelog postscript (venue-timetable, request-approval, replacement-home) is now coherent — every file it names has edits in a task.
- **R-coverage count fully closed — re-counted end-to-end.** All **13** audited `title=` conversions are mapped: T3's 5 (my-request-history: 4 chips L356/360/363/366 + "Reset all filters" L198) + T5's 8 (ui-week-nav print + venue print + arrangement ★ + login theme-toggle + request-approval ×4). All **13** `data-tip="Close"` modal-closes are mapped: T3's 3 (my-request-history) + T5's 10 (3 partials + replacement-home ×2 + arrangement ×1 + venue ×1 + request-approval ×3), matching T7's criterion-3 gate "13 `data-tip="Close"` present". Fresh grep confirms the inventory is unchanged (11 live in `ui-design-templates/` + login-template; only the commented kbShortcutsBtn excluded). No requirement is now left without a checkbox; T7's criterion-3 check is no longer self-defeating.
- **Ordering discipline for request-approval is now actually enforced by a task, not just prose.** Three reinforcing layers: the tasks.md preamble ("request-approval edits applied LAST"), the T5 sub-block header (apply LAST + re-grep every anchor immediately before each edit), and T0's "do NOT stage the dirty request-approval file" + T7's "re-verified against the then-current working tree; rebase-check before staging". Round 1's doubly-dangerous gap (a checklist re-verifying edits no task creates) is eliminated.

### 🟡 Addressed
- **🟡 #1 (login commit-message note) — RESOLVED.** T5's login checkbox now ends "Note the swap in the commit message — no changelog file exists for the login layout (design §8)" — R10's login-layout clause is operationalized exactly as suggested.
- **🟡 #2 (STOP gate task boundary) — RESOLVED.** T0 now says "STOP and surface before T2 (where the D8 labels land)" — the gate now names the task that actually applies the labels.
- **🟡 #3 (T4 2h-ceiling risk) — RESOLVED as split guidance.** T4 carries "(split guidance: if this task threatens the 2h ceiling, split T4a = map + warn/attention overrides, T4b = info overrides — no reordering)" plus the matching placement note, with warn/attention first — the safe split (warn lands before info; nothing reorders consumers ahead of T1's CSS).

### 🔴 Outstanding
- (none)

### 🟡 New findings this round (non-blocking)
- (none — regression audit clean: T0/T1/T2/T3/T6/T7 contents match the Round-1 verified record unchanged; the only diffs are the four specified fixes; no scope creep introduced — the request-approval edits cite R7.1/R7.2 and stay inside the frozen 13-edit inventory)

### ⚖️ Review Verdict
**PASS** — Outstanding is empty. The Round-1 blocker is verified fixed against live source, all 13 + 13 edit mappings count out exactly, the four 🟡 items are addressed as specified, and no regression was introduced. R1–R10 all map to tasks; ordering, safety rails, and granularity hold. Ready for /sdd-apply.

> Orchestrator: PASS accepted — tasks.md FROZEN. ALL FOUR BATCHES FROZEN. Pipeline: propose ✅ → next /sdd-apply (T0–T7), then /sdd-verify, then archive.

## /sdd-verify — 2026-10-09

### Reviewer verdict: VERIFY FAIL → fixed → conditions met
- Reviewer verified R1–R6, R7.2, R8, R10, both new specs, and the 3 sanctioned deviations
  (parallel-session spans renamed; replacement-home completed by apply; deletion comment).
- 🔴 One miss found: `layouts/login-template.blade.php:342` `title="Toggle theme"` not
  converted (12/13). **Fixed in passing**: swapped to `data-tip="Toggle theme"`; C3 grep gate
  now returns 0 native `title=` in the 3 scoped dirs.
- ⚠️ R9.1 (reviewer sandbox had no shell): full suite executed by the orchestrator —
  **116 passed, 2 skipped** (pre-fix AND post-fix runs, both green).

### Final criterion status
1. ✅ warn-keyword class-count (6 warning modals 1 each; change-guards/submit/no-selection 0)
2. ✅ detail-modal cancel works live (verified by reviewer + spec test b)
3. ✅ 13/13 conversions; 0 native title=; 14 data-tip="Close" (13 + pre-existing notif)
4. ✅ 116 passed / 2 skipped, all pre-existing specs unchanged
5. ✅ 9/9 changelog postscripts

**Status: apply + verify COMPLETE. Ready for /sdd-archive (pending user commit).**
