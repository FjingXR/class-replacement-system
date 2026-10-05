---
name: notifications-panel
created: 2026-10-02
status: applied
---
# Proposal — notifications-panel

> **Change:** Build the real notifications panel behind the nav-bell stub (frontend-only UI design)
> **Roles:** all three — Lecturer / Programme Leader / Student (role-filtered mock rows)
> **Explore baseline:** `.sdd/changes/notifications-panel/explore-brief.md` (grilled + user-approved 2026-10-02)
> **Standing directive:** everything follows the plan by default; deviations only where grilled with the user

---

## 1. Why this change is needed

The bell button in the shared top nav — rendered on **8 of the 9 pages** (all except
`replacement-arrangement`, which extends with `hideNav => true`) via
`partials/ui-nav-bar.blade.php` — is a dead stub: `onclick="alert('Notifications panel')"`
(ui-nav-bar.blade.php:30). A **hardcoded badge** ("3") dangles on it with no backing surface,
so those pages show a notification count that cannot be acted on.

CodingMAIN.md §77 scopes in-app notifications to exactly three FR-derived triggers —
FR 4.15 (PL notified on submission), FR 2.13 (lecturer notified on outcome), FR 1.9 (students
notified of timetable updates) — delivered via a DB-backed queue (FR 4.16). The queue and email
transport are **Sprint-3 backend work**; but the *user-facing surface* where those
notifications surface later has no design at all. Building the panel now (a) kills the fake
badge/user lie on every page, (b) gives Sprint 3 a ready ingestion point, and (c) demonstrates
the panel on both desktop and mobile as required.

## 2. In scope (mock phase, frontend only)

1. **Panel markup** — new partial `resources/views/partials/ui-notifications-panel.blade.php`,
   included once from `ui-nav-bar.blade.php` right after the `nav-drawer` markup. The bell flips
   from `alert(...)` to a `toggleNotifPanel()` shared helper.
2. **Two layouts, one data layer** (user requirement: desktop **and** mobile design):
   - **Desktop (≥768px):** ~360px right-aligned popover anchored under the bell; pinned header
     (title + unread pill + **"Mark all as read" text button** (user-requested) + ✕ close) +
     scrollable list capped ~420px; closes on outside click + Esc.
   - **Mobile (≤768px):** full-width top sheet dropping under the top bar with dimmed overlay,
     mirroring the existing `nav-drawer`/`nav-drawer-overlay` interaction (tap overlay / ✕ to
     close); same header + taller touch rows, scrollable.
3. **Mock data** — ONE new section `MockData.notifications` in `public/js/mock-data.js`:
   12 rows (4 per role), each `{ id, role, type, title, desc, minutesAgo, link }`, ages spanning
   minutes→days, stored as `minutesAgo` offsets (relative times never rot; absolute time
   computed at render for the tooltip). `MockData` stays read-only (renderer filters by role,
   never mutates). Four FR-derived types (grilled decision D3):
   - PL: "Request submitted" (`--color-primary-container` tile) + "Awaiting approval" reminder
     (`--color-tertiary-container`)
   - Lecturer: "Request approved" (`--color-secondary-container`) / "Request rejected"
     (`--color-error-container`) outcomes
   - Student: "Timetable update / replacement class scheduled" (`--color-surface-variant`)
4. **Row behavior** — row = one click target: **mark read + deep-link** to the relevant page
   (submission → `/request-approval-ui`, outcome → `/my-request-history-ui`, student timetable
   update → `/upcoming-replacements-ui` — FR 1.9 recipients are students only, so the lecturer
   `/my-timetable-ui` viewer is never an FR-1.9 target (review 🟡3)). Relative
   timestamp + `data-tip` absolute (grilled D9). No per-row delete (D13) — mark-read only. Row icons use
   canonical §10.0 container colors — zero new color concepts (D10).
5. **Read state + badge** — explicit read (opening does NOT auto-clear, D5); unread ids
   persisted in `localStorage` keyed per role; **new shared ui-common helpers**:
   `openNotifPanel/closeNotifPanel/toggleNotifPanel`, `markNotifRead(id)`, `markAllNotifsRead()`,
   `refreshNotifBadge()` (grilled D11), `relTime(minutesAgo)`. Badge = computed unread count for
   the active role (replaces the hardcoded 3). Dead data is **deleted, not ignored** (review
   🟡4): the inline `notifBadge.textContent` JS blocks (student-my-timetable:208–209,
   upcoming-replacements:338–339), the per-page `'notifCount' => 3` Blade args, and the now-dead
   `MockData.studentTimetable.notificationCount` field + its stale "drives the nav-badge dot"
   comment (mock-data.js:489/498) are all removed — one badge source remains (single-source
   rule). Note the **double-fallback chain** (`layouts/ui-template.blade.php:35` and
   `ui-nav-bar.blade.php:35` both say `?? 3`): until `refreshNotifBadge()` runs the badge can
   flash the stale "3" — design must define badge behavior for that window (and while browsing
   pages whose role legitimately has 0 unread, the badge correctly disappears only after the
   helper computes).
6. **Empty state (D6)** — compact inline "You're all caught up" message inside the panel
   (panel-scale; the page-level `ui-empty-state` partial is deliberately NOT reused there).
7. **CSS** — `.notif-panel` family (panel, header, rows, tiles, empty) in `theme.css`, tokens
   only; z-index tier above nav-drawer; overlay reuses the nav-drawer-overlay pattern.

## 3. Explicitly out of scope

- **No backend** — no migrations, models, queue/worker, email, or Livewire wiring (FR 4.16 is
  Sprint-3 backend; the panel is its future ingestion surface, not its implementation).
- No new page, no route changes — the panel lives in an existing shared partial.
- No notification **settings/digest** screens; no cross-lecturer alerts (§77 forbids unless
  requested later).
- No changes to `ToastManager` (transient action feedback — a different concept).
- No per-row dismiss/delete, no overdue/urgency scoring.
- Full keyboard focus-trap (Esc-to-close and natural tab order are in scope; focus trap is not).

## 4. Impact scope

| Area | File | Change |
|---|---|---|
| New partial | `resources/views/partials/ui-notifications-panel.blade.php` | panel + overlay markup; header with Mark-all button |
| Shared nav | `resources/views/partials/ui-nav-bar.blade.php` | bell `onclick` → `toggleNotifPanel()`; include panel partial; keep fallback badge |
| Shared CSS | `public/css/theme.css` | `.notif-panel` family + media query (desktop popover ↔ mobile top sheet) |
| Shared JS | `public/js/ui-common.js` | panel open/close, render/filter, read-state, mark-all, badge, relTime |
| Mock data | `public/js/mock-data.js` | ONE new section `notifications` (12 rows); delete dead `notificationCount` field (§2.5) |
| Templates | `request-approval-UI-design-template.blade.php` (`@extends`) + badge-feed deletions in `student-my-timetable` / `upcoming-replacements` | add `pageKey → 'requestApproval'` (the only template without one); delete stale badge JS (§2.5) |
| Page cleanup | `student-my-timetable-…blade.php:208`, `upcoming-replacements-…blade.php:338`, per-page `notifCount` args | drop hardcoded badge feeds; helper computes instead |
| Changelogs | `page-changelogs/` | new changelog entry documenting the cross-cutting change |

**Consumers (unchanged surface):** 8 of the 9 `ui-design-templates` pages render `ui-nav-bar`
(every page except `replacement-arrangement`, which extends with `hideNav => true`), so the
panel appears there automatically with **zero per-page work** beyond badge-stub cleanup — that's
the point of hosting it in the shared partial (§10.0 rule 3 OOP/reuse).

## 3a. Mock-phase resolutions carried from explore-brief open questions

- **Active-role detection (D2/D11 mechanism):** 8 of the 9 templates **already** pass
  `'pageKey' => …` via `@extends('layouts.ui-template', [...])` (cohortTimetable, myRequestHistory,
  myTimetable, replacementArrangement, replacementHome, studentMyTimetable, upcomingReplacements,
  venueTimetable) and the layout renders it into `body[data-page]`; only **`request-approval`
  lacks one** and gets `'pageKey' => 'requestApproval'` added to its own `@extends` (the
  `@extends` array wins over route data in this Laravel version, so routes are NOT the lever —
  review-2 correction). `ui-common.js` holds the single **pageKey→role map keyed on those exact
  template strings** (`studentMyTimetable|upcomingReplacements → student`,
  `requestApproval → pl`, everything else → lecturer) with fallback `'lecturer'`. No routes/web.php
  change and no per-page JS edits.
- **Seed text:** exact per-role row wording for the 12 rows is drafted in `design.md`
  (row anatomy needs the exact strings before tasks can split them).
- **PL reminder glyph:** design.md picks a distinct glyph (e.g. clock/hourglass SVG) from the
  submission row's glyph — never two types sharing one tile icon.
- **Mark-read re-render:** clicking a row keeps the panel's scroll position (re-render preserves
  anchor, no jump-to-top); page navigation is immediate anyway.
- **Mark-all UI (D5, user-requested):** a `Mark all as read` **text button** in the panel header
  (not icon-only — explicit user-requested exception to §10.0 rule 5, rationale recorded in
  D5); it hides when 0 unread.

## 5. Risks / notes

- Theme-toggle correctness: tokens-only styling makes dark mode automatic, but the panel must be
  visually verified in both modes.
- Id collision hazard: `#notifBadge` (bell) vs `#navPendingBadge` (approval nav badge) are
  distinct — design must not conflate them.
- Z-order: panel/overlay must sit above page content but coordinate with the mobile nav-drawer
  (only one may be open at a time).
- localStorage keys (`notifications-read-<role>`) are demo state — documented in changelog for
  reset instructions; clearing them restores the unread demo view.
- **Pre-existing mock chrome mismatch (out of scope, review 🟡6):** `ui-nav-bar` hardcodes the
  Lecturer identity ("LJZ", "En. Lim Jia Zheng") on every page including student-facing ones.
  The panel's role comes from the pageKey→role map, so a student page can show student rows
  under lecturer chrome. This predates the change and is deliberately accepted for the mock
  phase; not touched here.
