# Design — notifications-panel

> **Baseline:** `explore-brief.md` (grilled D1–D13 + user-requested Mark-all-as-read text
> button) + **frozen `proposal.md`** (§3a resolutions + round-2 pageKey mechanism adopted
> verbatim). Round-1 review fixes (4 🔴) are folded in here: render rule pinned, badge
> flash-window defined, storage key re-aligned to D5, desktop panel made `fixed`.

## 1. Technical approach

Frontend mock only. The bell in the shared `ui-nav-bar` stops calling `alert(...)` and calls
`toggleNotifPanel()` from `ui-common.js`. Panel markup ships in a new partial
`ui-notifications-panel.blade.php` included by `ui-nav-bar.blade.php` (so 8 of the 9 templates
get it for free; `replacement-arrangement` hides the nav and never sees it). Rows render from
`MockData.notifications` filtered by the active role (from the single pageKey→role map in
`ui-common.js`, fed by `body[data-page]`). Read state lives in `localStorage` **per role** —
`MockData` itself is never mutated. The list is a **to-do tray, not a history**: rows that get
read (clicked or mark-all) are removed from the list; unread = rows shown; an all-caught-up
compact message replaces the list at 0 unread (cuts the 🔴1 contradiction: no muted-history
CSS, `#notifEmpty` reachable, D6 exactly as grilled).

## 2. Architecture decisions

| # | Decision | Basis |
|---|----------|-------|
| AD-1 | Panel markup in new partial, included by `ui-nav-bar` — not per-page | D1; the bell partial already sits on every consumer page |
| AD-2 | Role detection = `body[data-page]` → `NOTIF_ROLE_BY_PAGE` map in ui-common keyed on the **exact template pageKeys** (`studentMyTimetable|upcomingReplacements → student`, `requestApproval → pl`, else `lecturer`); fallback `'lecturer'`. Only `request-approval-…blade.php` gains `pageKey => 'requestApproval'` in its own `@extends`; routes NOT edited | frozen proposal §3a (round-2 correction) |
| AD-3 | `MockData.notifications` = 12 rows `{ id, role, type, title, desc, minutesAgo, link }`; type ∈ `submitted / awaiting / approved / rejected / update`; renderer filters by role, never mutates | D2/D3/D12 |
| AD-4 | Read state = localStorage key **`notifications-read-<role>`** (not per pageKey) holding JSON array of ids. Keyed by role per frozen D5 — a row read on `/upcoming-replacements-ui` stays read on `/student-my-timetable-ui` (same role) | D5 (round-1 fix: pageKey keying withdrawn) |
| AD-5 | **Render rule (pinned, round-1 fix):** `#notifList` shows **unread rows only**. Clicking a row or "Mark all as read" removes read rows from the list (they do not mute); at 0 unread the list area shows `#notifEmpty`. Panel opening does NOT auto-clear (D5/AD-17) | 🔴1 fix; honors D6 as grilled ("no notifications, or everything already read") |
| AD-6 | "Mark all as read" = text button in panel header, hidden at 0 unread | D5 + user exception to §10.0 rule 5 (do NOT icon-ify later) |
| AD-7 | `refreshNotifBadge()` (proposal-frozen name) runs on `DOMContentLoaded`: badge = role's unread count; un/hides `#notifBadge`, un/hides `#notifUnreadPill`, un/hides `#notifMarkAll` | D11 |
| AD-8 | **Badge starts empty, never flashes (round-1 fix):** the server badge echo is rendered with NO fallback number — `ui-nav-bar` line 35 becomes `<span class="notif-badge" id="notifBadge" hidden></span>`; the layout stops forwarding `notifCount` (`ui-template.blade.php:35` — the `?? 3` fallback site); per-page `'notifCount' => 3` args at `student-my-timetable:8` and `upcoming-replacements:8` are deleted (completes frozen proposal §2.5). Until `refreshNotifBadge()` computes (first paint, ms), the badge is **hidden** — dark "3" flash is impossible; a role with 0 unread stays hidden | 🔴2 fix; kills fake badge on every page |
| AD-9 | Desktop ≥768px: `position: fixed` popover (round-1 fix — never `absolute`, it must not scroll with the page since the bell is in the fixed top bar): `top: calc(var(--top-bar-height) + 8px); right: 12px; width: min(360px, calc(100vw - 24px))`; list `max-height: 380px`, scrolls (CSS plan uses ASCII `-`) | D7 (proposal's "~420px": 380px chosen — deviation note, within the "~" tolerance) |
| AD-10 | New spacing token `--top-bar-height: 56px` added to `theme.css` once; `.top-bar { height }` refactored to consume it (.notif-panel/.notif-sheet share it — no height hardcode drift). Token-pair `--color-scrim` (light+dark) added for the panel overlay (round-1 fix — `--color-scrim` did not exist and rule 1 forbids new rgba in touched code); pre-existing overlay rgba usages are NOT refactored here | 🔴4/🟡5 fix |
| AD-11 | Mobile ≤768px: panel becomes fixed full-width top sheet (`top: var(--top-bar-height)`), own `.notif-overlay` dimmer; z-tier 998/999 mirroring `nav-drawer-overlay`/`nav-drawer`; body scroll locked with the drawer's own mechanism (`document.body.style.overflow = 'hidden'`) — no `.no-scroll` class (round-1 fix: that class doesn't exist in house CSS) | D8 |
| AD-12 | Drawer↔panel mutual exclusion mechanism: hoist the drawer's closure-local `openDrawer()/closeDrawer()` up to module-level shared functions `openNavDrawer()/closeNavDrawer()` in ui-common (markers: same behavior, no DOM/behavior change); panel open calls `closeNavDrawer()`, drawer open calls `closeNotifPanel()`; no coupling of internals beyond those two shared calls | 🔴6 fix; recorded under Promoted to shared |
| AD-13 | Close affordances: outside tap, Esc (panel listens only while open), ✕ in header; `aria-haspopup`/`aria-expanded` on bell; natural tab order; no full focus trap (mock-phase scope) | explore §open questions |
| AD-14 | Glyphs ship as **inline `<svg>` inside the partial** (the established `ui-nav-bar` pattern — `resources/views/flux/icon/` has only 4 unrelated glyphs, none of these five) | 🟡9 fix |
| AD-15 | Shell markup is static (header + list container); only `#notifList` innerHTML re-renders. On mark-read re-render while open: capture the list's `scrollTop`, re-render, restore it (cheap, no jump-to-top; row-click navigation happens after render) | proposal §3a + round-1 note |
| AD-16 | Times: `relTime(minutesAgo)` — "just now" <1m; "Xm ago" <60m; "Xh ago" <24h; "Yesterday" 24h≤age<48h; "Xd ago" ≥48h (max seed 3d); absolute (`D MMM, h:mm A`) as `data-tip` | D9/D12 |
| AD-17 | Read = actioned: clicked/mark-all rows leave the tray (AD-5 trim); no per-row delete, no toast/undo (non-destructive — rule 9 never triggered); panel opening never auto-clears | D5/D13 |
| AD-18 | Existing modal-overlay z (999 desktop / 1000 mobile; desktop tiebreak by DOM order — modals render at the end of page templates, panel lives in the nav partial near body-top) sits above the panel by design — the panel never hosts modals; no z conflict | reviewer 🟢→🟡 corrected (round 3) |

## 3. Component map (Blade)

```
layouts/ui-template.blade.php
└─ include(partial : ui-nav-bar, [activeNav, navItems])            (notifCount forwarder REMOVED)
   ├─ .notif-btn → onclick="toggleNotifPanel()"  aria-haspopup="true" aria-expanded ↔ state
   ├─ .notif-badge #notifBadge <span hidden></span> — no server number echo (AD-8)
   ├─ INLINE: @include('partials.ui-notifications-panel')           (NEW — after #navDrawer)
   │  ├─ .notif-overlay #notifOverlay (click → closeNotifPanel())
   │  └─ .notif-panel #notifPanel  role="dialog" aria-label="Notifications"
   │     ├─ .notif-head
   │     │  ├─ "Notifications"  +  .notif-unread-pill #notifUnreadPill [hidden at 0]
   │     │  ├─ button.notif-markall #notifMarkAll → markAllNotifsRead() [hidden at 0]
   │     │  └─ button.notif-close (✕) → closeNotifPanel()
   │     ├─ .notif-list #notifList                (UNREAD rows only — AD-5)
   │     │  └─ a.notif-row[data-id][href=row.link]
   │     │     ├─ .notif-row-icon  (inline svg glyph; tile class per type)
   │     │     ├─ .notif-row-main  → .notif-row-title / .notif-row-desc
   │     │     └─ .notif-row-time  [data-tip = absolute]
   │     └─ .notif-empty #notifEmpty ("You're all caught up" — shown at 0 unread)
```

Template-touch list (complete):
1. `request-approval-…blade.php` `@extends` gets `pageKey => 'requestApproval'` (only template missing it).
2. `student-my-timetable-…blade.php:8` — delete `'notifCount' => 3`.
3. `upcoming-replacements-…blade.php:8` — delete `'notifCount' => 3`.
4. `student-my-timetable-…blade.php:208–209` + `upcoming-replacements-…blade.php:338–339` — delete inline `notifBadge.textContent` JS blocks.
5. `layouts/ui-template.blade.php:35` — remove `notifCount` forwarder (`?? 3` fallback site).
6. `ui-nav-bar.blade.php:30` — bell alert → `toggleNotifPanel()`; line 35 — badge span emptied/hidden (AD-8).

## 4. Data flow

```
templates (@extends pageKey) → layout <body data-page="…">
  → ui-common DOMContentLoaded: refreshNotifBadge()
      role  = NOTIF_ROLE_BY_PAGE[pageKey] || 'lecturer'
      reads = Set(JSON.parse(localStorage['notifications-read-' + role] || '[]'))
      rows  = MockData.notifications.filter(n => n.role === role)     // read-only usage
      unread = rows.filter(n => !reads.has(n.id))
      badge.textContent = unread.length (hidden if 0); pill/markall same visibility
open panel → renderNotifList(unread) → rows or #notifEmpty (AD-5)
row click  → markNotifRead(id) → reads += id → save → re-render + badge → navigate
mark all   → markAllNotifsRead() → reads += all role row ids → save → list → caught-up + badge hidden
```

localStorage schema (per role — AD-4): `notifications-read-lecturer`,
`notifications-read-pl`, `notifications-read-student` = JSON id arrays,
e.g. `["n-pl-1","n-pl-3"]`. Demo reset: DevTools →
`localStorage.removeItem('notifications-read-<role>')`; documented in changelog.

## 5. Mock data spec — `MockData.notifications` (12 rows, 4 per role)

Types map to canonical tiles (§10.0(C) containers — **zero new color concepts**):

| type | glyph (inline svg) | tile tokens | deep link |
|---|---|---|---|
| submitted | inbox/arrow | `--color-primary-container / -on-primary-container` | `/request-approval-ui` |
| awaiting (PL reminder) | clock | `--color-tertiary-container / -on-tertiary-container` | `/request-approval-ui` |
| approved | check | `--color-success-container / -on-success-container` **(post-apply revision 2026-10-02: was `--color-secondary-container` per the then-canonical §10.0 row — user corrected to success green, matching every live surface (`.badge-normal`, approved summary card); the §10.0 "Approved" legend row itself still says secondary-container and is known-drift backlog)** | `/my-request-history-ui` |
| rejected | x | `--color-error-container / -on-error-container` | `/my-request-history-ui` |
| update | calendar | `--color-surface-variant / -on-surface-variant` | `/upcoming-replacements-ui` |

Seed rows rebuilt from §2.12 `upcomingReplacements` reality (round-1 🟡7 — no BMIT1234/BMIT3114,
no invented lecturer names):

**PL**
1. `n-pl-1` submitted · "New replacement request" / "BMIT5678 Database Systems · submitted by En. Lim Jia Zheng" · 25m
2. `n-pl-2` submitted (read) · "BMIT7075 Mobile App Development · submitted by Ms. Lim Pei Shan" · 90m
3. `n-pl-3` awaiting · "Approval still pending" / "2 requests awaiting your decision this week" · 240m
4. `n-pl-4` awaiting (read) · "1 request awaiting approval — BMIT2233, Week 10" · 420m

**Lecturer** (outcomes)
5. `n-lec-1` approved · "Request approved" / "BMIT5678 · replacement to Mon 14:00 – 16:00 @ B110 approved" · 12m
6. `n-lec-2` approved (read) · "BMIT2233 · replacement to Thu 12:00 – 14:00 @ B103 approved" · 150m
7. `n-lec-3` rejected · "Request rejected" / "BMIT7071 Research Methods · PL asked for an alternative slot" · 1440m ⇒ "Yesterday"
8. `n-lec-4` rejected (read) · "BMIT7073 IT Ethics · PL asked for an alternative slot" · 2880m

**Student** (timetable updates)
9. `n-stu-1` update · "BMIT5678 · replacement class Mon 14:00 – 16:00 @ B110" · 30m
10. `n-stu-2` update · "BMIT2233 · replacement class Thu 12:00 – 14:00 @ B103" · 120m
11. `n-stu-3` update (read) · "BMIT7074 · replacement moved to Thu 10:00 – 12:00 @ B005" · 1560m
12. `n-stu-4` update (read) · "BMIT7070 · replacement class Wed 12:00 – 14:00 @ B102" · 4320m

Fresh state = **6 unread / 6 read overall; each role's pages open with badge 2**
(2 unread rows each). Pre-seeded read ids are baked as a `read: true` flag in the mock rows
consumed ONLY at first paint when the localStorage key is absent — they never mutate
`MockData` at runtime (first paint writes the key). With AD-5 trim semantics this demos the
full path: bold unread rows → click/mark-all → list trims → pill shrinks → caught-up message.

## 6. CSS plan (`theme.css`, tokens only)

```
--top-bar-height   : 56px;                  /* NEW spacing token (AD-10), consumed by .top-bar too */
--color-scrim      : rgba(0,0,0,.5) light / rgba(0,0,0,.6) dark;   /* NEW color token (AD-10) */
.notif-overlay     fixed inset 0; bg var(--color-scrim); z 998; display none; open class 'open'
.notif-panel       BOTH modes position fixed (AD-9/10).
                   ≥768px: top calc(var(--top-bar-height) + 8px); right 12px;
                   width min(360px, calc(100vw - 24px)); bg var(--color-surface);
                   border var(--color-outline); radius lg; shadow-lg; z 999
.notif-head        flex; border-bottom outline-variant; padding token
.notif-unread-pill bg var(--color-primary); color var(--color-on-primary); radius-pill
#notifMarkAll      text-style button (theme token set); [hidden] at 0 unread
.notif-row         grid tile|text|time; gap/padding tokens; hover surface-variant;
                   title 600wt on-surface; desc + time 0.75rem on-surface-variant; ≥44px touch
.notif-row-icon    36px tile radius-sm; per-type container tokens (§5) — dark mode free (tokens)
.notif-row-time    0.75rem muted; absolute date via data-tip (house tooltip already global)
.notif-empty       centered; 32px inline icon + "You're all caught up" compact scale
@media ≤768px      panel: top var(--top-bar-height); left 0; right 0; width 100%; radius 0;
                   border-inline 0; max-height calc(100dvh - var(--top-bar-height)); scroll;
                   overlay .open visible; body overflow hidden inline (AD-11 — NO .no-scroll class)
```

Note: `.notif-badge` styles remain as-is; the badge element is empty+`hidden` until helper
paints (AD-8).

## 7. Promoted to shared

- `partials/ui-notifications-panel.blade.php` — panel markup via `ui-nav-bar` (all nav-bearing pages).
- `ui-common.js`: `NOTIF_ROLE_BY_PAGE`, `notifReadKey`, `openNotifPanel/closeNotifPanel/toggleNotifPanel`, `renderNotifList`, `markNotifRead`, `markAllNotifsRead`, `refreshNotifBadge` (proposal-frozen name), `relTime`, **`openNavDrawer/closeNavDrawer`** (hoisted from `initMobileNav` closures, behavior-identical — AD-12).

- `theme.css`: `.notif-panel` family + tokens `--top-bar-height`, `--color-scrim` (existing `.notif-btn/.notif-badge` kept).
- `mock-data.js`: `MockData.notifications` — dead `MockData.studentTimetable.notificationCount` + stale comment deleted (§2.5).

## 8. Toast/undo bar

Not used — mark-read/mark-all are non-destructive (AD-17); rule 9 confirm-popups not triggered.

## 9. Mobile view (≤768px)

Top sheet per AD-11: full width under top bar, overlay, body scroll locked (inline overflow,
same mechanism as drawer). Same data layer and markup as desktop; only the media query changes
geometry; drawer↔panel mutual exclusion (AD-12); touch rows ≥44px. `data-tip` tooltips degrade
cleanly (no hover on touch — absolute date aid is desktop-only; accepted in D9).

House rule-10 checklist disposition (component, not a page): viewport meta — pre-existing in
layout (ui-template:5); safe-area insets — N/A for a sheet below the fixed top-bar (drawer's
`env()` usage pattern noted; no overlapping edge elements here); skeleton loading — N/A,
synchronous in-memory render; responsive `clamp()` typography — N/A, house type scale already
applies; toasts — N/A (§8).

## 10. Dependencies

Existing: theme tokens, `.notif-btn/.notif-badge`, nav-drawer overlay z-tier, `body[data-page]`
pipeline (scroll-restore precedent), global tooltip init, MockData-before-scripts load order.
Glyphs: inline SVG (AD-14 — flux/icon/ lacks these five). New npm/cdn deps: **none**.

## 11. Verification hooks (→ tasks)

Playwright (server per house command) against `/upcoming-replacements-ui` (student)
and `/request-approval-ui` (pl role):
1. Fresh state (localStorage keys cleared): badge hidden → **2** after first paint; no "3" flash ever.
2. Bell opens panel; unread rows = that role's 2; unread pill and mark-all visible.
3. Row click → row leaves the list (AD-5 trim), badge decrements, then browser lands on row.link.
4. Mark-all → list → caught-up message; badge, pill and mark-all hidden.
5. Reload → state persists (role-keyed, AD-4); removing `notifications-read-<role>` restores 2.
6. Breakpoints 1280 ↔ 375 resize: popover ↔ top sheet + overlay; rows ≥44px on mobile.
7. Drawer mutual exclusion both directions (AD-12); overlay tap + Esc close.
8. **Scroll-anchored test (AD-9):** open the panel, scroll the page with the wheel — panel stays
   glued under the fixed bell (would catch the round-1 `absolute` regression).
9. Lecturer page check (`/my-request-history-ui`): badge = 2, deep-link lands correctly.
10. Theme toggle → panel dark mode re-verified (tokens-only).
11. Caught-up via data: write all role ids into `notifications-read-<role>` → caught-up on open.
12. Console errors 0 throughout; no hardcoded colors in touched files.

---

## 12. Post-apply user revision (2026-10-02) — "Unread only" filter toggle

User request (permanent, after apply): give the tray an explicit **filter control** instead of the
implicit unread-only render. This *reverses the enforcement scope* of AD-5 (it now describes the
toggle-ON state only) and un-deletes the round-1 muted-history idea, deliberately, at the user's
instruction. Recorded here rather than via a new SDD change (single-surface, ≤2h).

| # | Decision | Basis |
|---|----------|-------|
| AD-19 | `.notif-head` gains a second row `.notif-filters` hosting one house switch: `.toggle-wrapper` + input `#notifUnreadOnly` (CHECKED by default on every load — tray behavior unchanged for fresh/first paint) + `.toggle-label` "Unread only". **ON = frozen AD-5 behavior byte-for-byte** (unread rows only; caught-up swap at 0). **OFF = all of the role's rows render newest-first (`minutesAgo` ascending — for all 21 seed rows this equals the existing order), read rows muted via `.notif-row--read`**: title weight 400 + `--color-on-surface-variant`, desc/time unchanged; row stays a live deep-link anchor; `markNotifRead` stays idempotent (clicking a muted row navigates, no list change) | user request 2026-10-02 (supersedes round-1 rejection of muted history) |
| AD-20 | Caught-up swap fires only when filter is **ON** and unread = 0; **OFF mode always shows rows** (muted read data instead). Badge / unread-pill / mark-all visibility keep following the **unread count** (AD-7/AD-8) regardless of view mode — nothing moves in the header when the switch flips. Toggle state is NOT persisted (mock; default ON each load). `#notifUnreadOnly` `change` → re-render with AD-15 scroll-preservation; switch id is null-guarded so pre-partial pages stay safe | pairing AD-19 with D6/AD-7 |

