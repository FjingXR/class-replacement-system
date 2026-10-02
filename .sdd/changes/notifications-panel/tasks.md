# Tasks — notifications-panel

> Every task ≤2h. Order respects dependencies: mock data → partial/CSS → JS → wiring →
> cleanup → verify → docs. Convention: `ui:`/`feat:`/`fix:`/`oop:`/`style:`/`docs:` commits on
> branch `fjing`; never push unless asked.

## 1. Shared files first

- [ ] **T1 mock-data** — add §2.13 section `MockData.notifications`: the 12 seed rows exactly per frozen design §5 (roles lecturer/pl/student; types submitted/awaiting approved/rejected/update; `minutesAgo` ages; `link` per design §5 table; `read: true` flags on n-pl-2/n-pl-4/n-lec-2/n-lec-4/n-stu-3/n-stu-4). Commit: `feat: mock notifications dataset (12 rows, 4 per role)`.
- [ ] **T2 theme.css tokens** — add `--top-bar-height: 56px` + `--color-scrim` (light/dark pair); refactor `.top-bar { height: 56px }` to consume the token; verify visually the top bar looks identical (screenshot). Commit with T3.
- [ ] **T3 theme.css panel family** — `.notif-overlay/.notif-panel/.notif-head/.notif-unread-pill/.notif-markall/.notif-row/.notif-row-icon/.notif-row-time/.notif-empty` per design §6, desktop popover geometry; then the ≤768px media query block (top sheet + overlay + ≥44px rows). Tokens only — zero hex/rgb in this design beyond the new scrim token. Commit: `feat: notifications panel CSS (popover + mobile top sheet)`.

## 2. JS module

- [ ] **T4 ui-common: drawer hoist** — hoist `openDrawer()/closeDrawer()` closures out of `initMobileNav` (ui-common.js:1272–1315) to behavior-identical module functions `openNavDrawer()/closeNavDrawer()`; hamburger/overlay/close/swipe/Esc handlers now call them. SMOKE-TEST drawer (open/close/swipe/Esc/scroll-lock) before proceeding — this is a refactor touching existing behavior. Commit: `oop: hoist nav-drawer open/close to shared helpers`.
- [ ] **T5 ui-common: panel module** — `NOTIF_ROLE_BY_PAGE` (AD-2 exact keys), `notifReadKey`, `relTime` (AD-16 thresholds), `openNotifPanel/closeNotifPanel/toggleNotifPanel` (fixed positioning; outside-click + Esc while open; `aria-expanded`; body overflow inline lock on open, released on close; **mutual exclusion BOTH directions (AD-12): panel open calls `closeNavDrawer()`, AND the hoisted `openNavDrawer()` body is amended to call `closeNotifPanel()` when that function exists (`typeof` guard — hoisted drawer code predates the panel)**), `renderNotifList` (AD-5: unread rows only; caught-up swap; scrollTop preservation AD-15), `markNotifRead`, `markAllNotifsRead`, `refreshNotifBadge` (AD-7/AD-8; `read:true` seed-flag → first-paint key write) + DOMContentLoaded hook. Commit: `feat: notifications panel helpers (open/close/render/read-state/badge)`.

## 3. Blade wiring

- [ ] **T6 panel partial** — create `resources/views/partials/ui-notifications-panel.blade.php` per design §3 (overlay + panel shell, header with unread pill / Mark-all text button / ✕, `#notifList`, `#notifEmpty`, 5 inline SVG glyphs per AD-14, `data-tip` absolute time); include it in `ui-nav-bar` right after `#navDrawer` markup. Commit: `ui: notifications panel partial wired into nav bar`.
- [ ] **T7 bell + badge span** — `ui-nav-bar.blade.php:30` alert → `toggleNotifPanel()` (+`aria-haspopup="true"`); line 35 badge span → empty + `hidden` (AD-8). Also remove the `notifCount` forwarder from `layouts/ui-template.blade.php:35` include args. Commit with T8.
- [ ] **T8 template cleanup** — `request-approval-…blade.php` `@extends` += `pageKey => 'requestApproval'`; delete `'notifCount' => 3` args (student-my-timetable:8, upcoming-replacements:8); delete the inline `notifBadge.textContent` JS blocks (student-my-timetable:208–209, upcoming-replacements:338–339); **delete dead `MockData.studentTimetable.notificationCount` + its stale "drives the nav-badge dot" comment (mock-data.js:489/498) — frozen proposal §2.5 / design §7 promotion requirement**. Commit: `feat: wire notifications panel + remove hardcoded badge stubs`.
- [ ] **T9** — clear stale Blade cache (`pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &`) and smoke-render `/upcoming-replacements-ui` + `/request-approval-ui` — no PHP errors, panel present, `data-page` correct on request-approval.

## 4. Verification (design §11 all 12 checks)

- [ ] **T10** — fresh state (3 `notifications-read-*` keys cleared): badge hidden→2 on student page, PL page and lecturer page (`/my-request-history-ui`); unread pill + Mark-all visible; **bell opens the panel with exactly 2 unread rows rendered for that role (§11 check 2)**; no stale "3" flash.
- [ ] **T11** — row click: row leaves list (trim), badge decrements, navigation lands on the right page per type map (**incl. lecturer rows on `/my-request-history-ui`** §11 check 9); Mark-all → caught-up message + badge/pill/mark-all hidden.
- [ ] **T12** — persistence: reload keeps state both directions; deleting `notifications-read-<role>` restores badge 2; caught-up-by-data check (all ids written to key).
- [ ] **T13** — breakpoints 1280↔375: popover ↔ top sheet + overlay; drawer↔panel mutual exclusion both directions; overlay tap + Esc close; **touch rows ≥44px on mobile (§11 check 6)**.
- [ ] **T14** — scroll-anchor test (wheel-scroll with panel open — panel glued to bell, AD-9); theme toggle dark-mode render; console errors = 0 throughout; **hardcoded-color scan of touched files (theme.css new rules, ui-nav-bar, panel partial, ui-common.js panel code — §11 check 12)**.
- [ ] **T15** — run `composer run lint:check` + `composer run types:check`; confirm failures remain the pre-existing app/-only set (Blade touched = PHP check mandatory).

## 5. Documentation

- [ ] **T16 changelog** — fill `page-changelogs/notifications-panel-changelog.md` (per-file tables: partial, ui-nav-bar, ui-template, theme.css, ui-common.js, mock-data.js, 3 templates; localStorage reset instructions); add cross-reference notes to `upcoming-replacements-ui` and `student-my-timetable` changelogs for the badge-feed deletions. Commit: `docs: notifications-panel changelog`.
