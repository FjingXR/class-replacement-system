# Changelog — Notifications Panel (shared nav bell)

## [2026-10-02] Notifications panel behind the nav bell (frontend mock) — SDD change `notifications-panel`

Kills the `alert('Notifications panel')` stub and the hardcoded "3" badge: the
top-bar bell now opens a real panel — an **unread-only todo tray** with per-role
rows and localStorage read-state (FR-4.15 / 2.13 / 1.9 mock surface; FR 4.16
backend = Sprint 3).

### Files Created

#### `resources/views/partials/ui-notifications-panel.blade.php`
- Overlay + dialog shell: scrim, `role="dialog"` panel, header with unread pill
  and **"Mark all as read"** text button (user-granted rule-5 exception; hidden
  at 0 unread), ✕ close, JS-rendered `#notifList` body.
- Inline caught-up empty state ("You're all caught up").
- Wired by the ui-common `initNotifPanel` module — no page-side JS.

### Files Modified

| File | Change |
|---|---|
| `resources/views/partials/ui-nav-bar.blade.php` | Bell → `toggleNotifPanel()` + `aria-haspopup` / `data-tip`; badge span emptied + `hidden` (AD-8 no-flash); panel partial included after the drawer |
| `resources/views/layouts/ui-template.blade.php` | Dropped the `notifCount` forwarder arg (last server-side "3" echo) |
| `public/css/theme.css` | New tokens `--top-bar-height: 56px` (`.top-bar` refactored to consume it; visuals byte-identical, screenshot-verified) + `--color-scrim` light/dark pair (the one approved rgba); `.notif-*` family — desktop 360px fixed popover @ z 999 / ≤768px full-width top sheet @ z 998 overlay, 44px rows, 5 canonical container tiles |
| `public/js/ui-common.js` | Hoisted `openNavDrawer()`/`closeNavDrawer()` out of `initMobileNav` (behavior-identical, smoke-tested open/close/Esc/swipe/scroll-lock). Panel module: `NOTIF_ROLE_BY_PAGE` (/studentMyTimetable + upcomingReplacements → student, requestApproval → pl, else lecturer); per-role localStorage read state (AD-4, seed-once first paint from `read: true` flags, `MockData` never mutated); `relTime` + absolute `data-tip` times; `renderNotifList` (AD-5 unread-only + caught-up swap + AD-15 scroll restore); `markNotifRead`/`markAllNotifsRead`; `open/read/close/toggleNotifPanel` (aria-expanded, body overflow inline lock, AD-12 drawer↔panel mutual exclusion BOTH directions); `refreshNotifBadge` on DOMContentLoaded |
| `public/js/mock-data.js` | New §2.13 `MockData.notifications` — 12 rows (4/role: pl submitted/awaiting; lecturer approved/rejected; student update), `minutesAgo` ages, `read: true` on 6 seed-read rows; DELETED dead `studentTimetable.notificationCount` + stale comment (§2.5 single-source) |
| `ui-design-templates/request-approval-UI-design-template.blade.php` | `@extends` += `pageKey => 'requestApproval'` (role-map lever; routes untouched) |
| `ui-design-templates/student-my-timetable-UI-design-template.blade.php` + `upcoming-replacements-UI-design-template.blade.php` | Deleted per-page `'notifCount' => 3` args + inline `notifBadge.textContent` feed blocks (badge now computed by `refreshNotifBadge`) |

### Role → seed state
- Fresh localStorage = **badge 2 on every page** (2 unread rows per role; 6
  pre-read rows seeded at first paint).
- Behavior: row click = mark read + navigate (D4 deep links); list shows unread
  only (trims on read); 0 unread → "You're all caught up"; panel does **not**
  auto-clear on open.
- localStorage reset instructions: `localStorage.removeItem('notifications-read-student')`
  (also `-pl`, `-lecturer`) in DevTools → back to badge 2.

### Deviation notes (honest)
- Design's `.notif-head` border-bottom called for `--color-outline-variant` —
  token never existed in theme.css; used `--color-outline`.
- Data-tip on rows sits on the row anchor (whole-row hover) rather than only the
  time span; shown elements keep their `hidden` attribute with inline display
  overriding (cosmetic consistency note).
- Font sizes normalized to px per house convention (design's 0.75rem = 12px).

### Verification
T9–T14 all pass:
- [x] Fresh badges 2/2/2 on student / PL / lecturer pages
- [x] Row click trims + navigates; mark-all → caught-up
- [x] Persistence both ways + key-removal restore
- [x] 1280↔375: popover ↔ top-sheet
- [x] Drawer mutual exclusion both directions; overlay/Esc close
- [x] Scroll-glued fixed panel
- [x] Dark + light token render; console 0 errors
- [x] Hardcoded-color scan clean
- [x] Lint/types = pre-existing app/-only set
