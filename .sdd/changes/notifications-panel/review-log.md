# Review Log — notifications-panel

## proposal Round 1 — 2026-10-02
### 🔴 Fixed
 - (none — verdict PASS, no 🔴)
### 🟡 Addressed
 - "All 9 pages render ui-nav-bar" corrected → 8 of 9 (replacement-arrangement hides nav via `hideNav => true`).
 - Active-role detection made concrete: routes get explicit `pageKey` values (layout already renders `data-page`), ui-common holds the single pageKey→role map with fallback 'lecturer'; "zero per-page work" claim scoped accordingly. `routes/web.php` added to impact table.
 - Student FR-1.9 deep-link fixed: student timetable-update rows → `/upcoming-replacements-ui` (never the lecturer `/my-timetable-ui` viewer).
 - "Superseded" clarified to **deleted**: inline `notifBadge.textContent` feeds (student-my-timetable:208–209, upcoming-replacements:338–339), per-page `'notifCount' => 3` args, and dead `MockData.studentTimetable.notificationCount` + stale comment all removed — single badge source.
 - Explore-brief open questions carried as §3a one-line resolutions: seed text → design.md; PL reminder glyph distinct; mark-read re-render keeps scroll position; Mark-all-as-read text button (user-requested exception to rule 5) hides at 0 unread.
 - Risk added: pre-existing lecturer-chrome ("LJZ"/"Lecturer") on student pages vs role-filtered rows — acknowledged, out of scope.
### Outstanding — none
### 🟢 Noted for design.md
 - Record this change under "Promoted to shared" (bell/panel/badge into shared partial + ui-common).
 - Re-record the D5 rationale for the text-button exception so later rounds don't icon-ify it.

## proposal Round 2 — 2026-10-02
### 🔴 Fixed
 - §3a mechanism premise corrected (verification round): `pageKey` is supplied by templates via `@extends` (8 of 9 already have it — wins over route data in this Laravel version), NOT by routes; only `request-approval-…blade.php` needs `pageKey => 'requestApproval'` added to its `@extends`. Impact-table "Routes" row replaced with the template row; routes/web.php change dropped.
### 🟡 Addressed
 - §1 residual "every page" → "8 of the 9 pages (all except replacement-arrangement)".
 - 🟢 carried into §2.5: double-fallback `?? 3` chain (layout + partial) documented — badge may flash stale "3" until `refreshNotifBadge()` computes; design must define that window's behavior.
### Outstanding — none

**Verdict: PASS** — proposal.md frozen after these edits.

## design Round 1 — 2026-10-02
### 🔴 Fixed
 - **Render rule pinned (AD-5):** tray shows unread rows only — read rows are removed (acted upon), not muted; caught-up message replaces the list at 0 unread. All "bold vs muted" claims removed; `#notifEmpty` now reachable; §11 rewritten to match.
 - **Badge flash-window defined (AD-8):** badge span rendered empty + `hidden` on server (no number echo); layout notifCount forwarder and per-page `'notifCount' => 3` args deleted — full template-touch list in §3. No stale "3" flash possible; 0-unread roles keep it hidden.
 - **Storage key re-aligned to frozen D5 (AD-4):** `notifications-read-<role>` (pageKey keying withdrawn — cleared re-inflation across same-role pages was wrong).
 - **Desktop panel `position: fixed` (AD-9):** `absolute` withdrawn (panel drifted with page scroll while bell is fixed); anchored via new `--top-bar-height` token + `top: calc(token + 8)`; page-scroll anchor test added to §11.
### 🟡 Addressed
 - `--color-scrim` token added (AD-10) — no new rgba in touched code; pre-existing overlay rgba left untouched.
 - Drawer hook mechanism specified (AD-12): hoist drawer open/close to shared `openNavDrawer/closeNavDrawer` in ui-common; panel open ↔ drawer close via those; body scroll lock = inline overflow (NO `.no-scroll` class — doesn't exist).
 - Seed rows rebuilt from §2.12 reality — BMIT1234/BMIT3114/invented lecturer names removed; real codes/lecturers/venues (BMIT5678→Mon B110, BMIT2233→Thu B103, BMIT7074→Thu B005, BMIT7070→Wed B102, BMIT7071/7073 rejection narratives).
 - Helper renamed back to `refreshNotifBadge()` per frozen proposal (design drift withdrawn).
 - Glyphs declared inline SVG in the partial (AD-14) — flux/icon/ lacks them.
 - House rule-10 checklist disposition added to §9 (viewport meta pre-existing; others N/A with reasons).
 - 🟢 notes: modal-overlay z note (AD-18); relTime "Yesterday" window defined (AD-16); §5 garbled sentence cleaned.
### Outstanding — none

**Verdict: re-review pending (round 2)** — fixes applied.

## design Round 2 — 2026-10-02
### 🔴 Fixed
 - (none from this round's findings — the 🔴 count was zero; this round was strictly scoped 🟡 text edits)
### 🟡 Addressed
 - Seed-slot fabrication residue (reviewer claimed n-lec-2/n-stu-2, n-stu-4, n-pl-4 cite slots that don't exist in §2.12). **Verified against mock-data.js before editing — the reviewer's 3 sub-claims are factually wrong:**
   - n-lec-2/n-stu-2 "BMIT2233 · Thu 12:00–14:00 @ B103 approved" = §2.12 **id 15** (status 'replacement', newDi 3 / Thursday, B103) — real.
   - n-stu-4 "BMIT7070 · Wed 12:00–14:00 @ B102" = §2.12 **id 14** (status 'replacement', newDi 2 / Wednesday, B102) — real (reviewer's "BMIT7070 doesn't appear in §2.12" is incorrect).
   - n-pl-4 "BMIT2233, Week 10" = §2.12 **id 9** (pending, week 9; 0-indexed week 9 renders as "Week 10" — established SDD convention from the upcoming-replacements change) — real.
   - Seeds therefore stand unchanged.
 - n-pl-3 count corrected: "3 requests awaiting your decision this week" → **"2 requests"** — §2.12 has exactly 2 pending rows in the current week (ids 8, 9). (The one genuine defect the round-2 audit surfaced.)
 - AD-16 nit fixed: max seed 4320m = **3d**, not "4.5d".
### 🟡 Carried
 - 380px list cap vs proposal "~420px": deviation note recorded in AD-9 (within "~" tolerance). No geometry change.
### Insufficient-evidence claims (not acted on)
 - Reviewer's claim that §2.12 BMIT2233 rows are "ids 2, 9, 11, all pending with newDay null" overlooked id 15; claim that BMIT7070 "lives only in the rsd2s1 timetable with Friday @ B103 slots" overlooked id 14. Both refuted above with row ids.
### Outstanding — none

**Verdict: re-review pending (round 3)** — scoped fixes applied; seeds re-anchored where genuinely wrong, refutations logged where the finding itself didn't hold.

## tasks Round 1 — 2026-10-02
### 🔴 Fixed
 - **AD-12 two-direction wiring missing from tasks** → T5 now explicitly amends the hoisted `openNavDrawer()` body to call `closeNotifPanel()` (with `typeof` guard) in addition to panel→drawer; T13's both-directions test now has an executing task.
 - **Frozen §7 promotion with no task: dead `MockData.studentTimetable.notificationCount` + stale comment** → deletion added to T8 explicitly (was only in its commit message's purview).
### 🟡 Addressed
 - §11 check 2 (bell opens panel, 2 rows for role) → assertion added to T10.
 - §11 check 6 (rows ≥44px mobile) → verification added to T13.
 - §11 check 12 second half (no hardcoded colors in touched files) → scan added to T14.
 - 🟢 adopted: T11 pinned lecturer deep-link check; T9 marked ops task.
### Outstanding — none

**Verdict: PASS → tasks.md FROZEN** (round 2 freeze-confirmation: all 5 round-1 fixes verified present with accurate line anchors; no regressions; ordering/granularity/commit conventions intact).


## design Round 3 — 2026-10-02
### 🔴 Fixed
 - (none — zero 🔴 this round)
### 🟡 Addressed (freeze-batch)
 - AD-18 z-fact corrected: modal-overlay is 999 desktop / 1000 mobile (desktop tiebreak by DOM order) — same conclusion, accurate numbers.
 - AD-9/§6: typographic minus (U+2212) in `calc()` excerpt replaced with ASCII `-` (copy-paste hazard).
 - AD-16 boundary made deterministic: "Yesterday" = 24h ≤ age < 48h; "Xd ago" ≥ 48h.
### Outstanding — none

**Verdict: PASS → design.md FROZEN** (3 review rounds: 🔴4→fixed → narrow 🟡 → PASS; reviewer's round-2 seed-fabrication claims refuted with §2.12 row ids 8/9/14/15 and logged above; the one genuine defect, n-pl-3 count, fixed).

## specs/ — 2026-10-02
Skipped by design: single-surface shared-component change (one partial + shared files); no cross-module capability split. All spec-level detail (token names, z-tiers, seed rows, localStorage schema, verification hooks) already lives in frozen proposal/design per the upcoming-replacements-ui precedent (specs/ also skipped there).





## Post-apply user revision — 2026-10-02 (unread-only toggle)

User requested (in-session, explicit) a permanent "Show unread only" toggle for the panel —
reversing the round-1 rejection of muted history (AD-5's implicit tray). Options presented
(keep-as-is / permanent / TEMP); user chose **permanent**. Artifacts amended declaratively:
design.md §12 adds AD-19 (header `.notif-filters` row + house switch `#notifUnreadOnly`, ON =
frozen AD-5 byte-for-byte, OFF = all rows newest-first with `.notif-row--read` muted history)
and AD-20 (caught-up swap only in ON mode; header visibility stays unread-count-driven; toggle
not persisted). tasks.md gains T17/T18 (§6). No reviewer round — user-instructed post-apply
revision, per house precedent (see upcoming-replacements review-log "Post-apply user revision").
