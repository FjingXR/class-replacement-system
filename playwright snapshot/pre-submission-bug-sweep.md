# Pre-Submission Bug Sweep — Report Only

> **Scope:** all 9 UI pages + shared shell (nav-bar, notifications panel, login/logout)
> **Method:** live Playwright pass (dark 1440 / light 1440 / dark+light 768 mobile, console log, interaction click-through) + code pass (token violations, FR deviations vs CodingMAIN.md, cross-page consistency, stale leftovers)
> **Rule:** 🔴 report only — **no fixes during the sweep.** Fixes become SDD changes afterwards, batched by severity.
> **Started:** 2026-10-05 · branch `fjing` @ `5151424` · demo anchor Oct 5 2026 active

## Severity legend
- 🔴 **Blocking** — broken behaviour, console errors, spec violation
- 🟡 **Should-fix** — inconsistency, visual defect, misleading state
- 💡 **Nice-to-have** — polish, wording, minor code hygiene

---

## 1. replacement-home (`/replacement-home-ui`)

### 🟡 F-1 — Negative "days left" treated as "Urgent", past classes stay actionable
- Table "DAYS LEFT" column renders **−31 / −25 / −24 … days** (red) for classes from Sep weeks (anchor = Oct 5). Same on mobile cards (−31 days left) and in the quick-view modal's **Status** row.
- Quick-view modal **Status Description: "Urgent — arrange a replacement soon"** for a class 31 days **in the past** — the urgency mapper treats any small/negative `daysLeft` as urgent; negatives should read e.g. "Overdue / past date".
- Those rows still offer a fully-enabled **Arrange Replacement** button; the lead-time rule then hides every week on the landing arrangement page → dead-end flow for past classes.
- Location: `daysLeft()`/`urgencyClass()` + modal status mapping in `replacement-home-UI-design-template.blade.php`; dataset rows predate the Oct-5 anchor.
- **Decision needed later:** hide/deprioritise past classes vs label them "Overdue" (report-only now).

### Verified-good (no action)
- Console: 0 errors/warnings across dark / light / mobile-load.
- Light + dark themes correct; no horizontal overflow at 768; badges dynamic (Request Approval 8 · bell 3).
- Row click → quick-view modal opens/closes cleanly; modal is design-consistent (Close + primary action).
- Mobile: `.replacement-card` = proper accessible button (role=button, tabindex, Enter/Space → navigates).
- "Arrange Replacement" navigates to `/replacement-arrangement` pre-filled; week-select uses the unified chevron family.

---

## 2. upcoming-replacements (`/upcoming-replacements-ui`)

### ✅ F-2 — ~~Bell badge shows different unread counts per page role-context~~ → **RESOLVED 2026-10-06: pre-wired to per-user mailbox (sweep-fixes-round-1)**
- User decision: badge must give **one number per logged-in user** (backend semantics — `WHERE recipient_id = auth:id`), pre-wired now so the swap is a data change later.
- Shipped: notifications rows gain `recipientId` (derived once in mock-data.js §2.13 block, rows copied per read-only convention); read store retired per-role keys → **one user store** `notifications-read-user-<staffId>` (legacy 3 role keys migrated by union at first paint, then removed); badge/pill/mark-all count the **whole mailbox**; the panel list/view stays category-scoped by page context (AD-2 frozen design, kept); mark-all = "my whole mailbox" (backend semantics).
- Verified live: badge 11 on lecturer AND student pages (was 3 vs 5); inter-page drift gone; mark-one-read → 10 with pill synced; deterministic reseed from the 10 `read:true` data rows on first visit; fresh-user number = 11; 0 console errors.
- Backend TODO: replace the localStorage store with the server-persisted read flag (or a read_at column) — the key/user derivation + mailbox filter already match that shape. (TEMP console helpers + `n-tmp-*` rows still pending the pre-submission purge.)

### Verified-good (no action)
- Console 0 errors/warnings across all passes.
- Empty state for Week 11 is **correct** — investigated as suspected data/indexing bug, cleared: display Week N ↔ 0-based `r.week`/`rsd3g2Flags` keys are consistent (page filter `value − 1`, card label `r.week + 1`, modal dates cross-checked vs calendar).
- "Show Past" semantics correct (relaxes past-boundary within selected week only; All Weeks + Show Past → 18 rows, summaries 2 upcoming / 9 pending / 7 past — all correct).
- Row/card click → `modal--detail` with Class / Original Slot / New Slot / Status sections; status badge colors match §10.0 (Replacement = primary, Pending = yellow, Past = outline).
- Unified toolbar (chevrons, week-select, Today primary); mobile stacks toolbar + cards cleanly; no overflow; no hardcoded tokens beyond the shared-shell html.bg exception (see §10 shell notes).

---

## 3. venue-timetable (`/venue-timetable-ui`)

### 🟡 F-4 — Mobile class-card status badges leak raw lowercase enum values
- Mobile card list (`.card-list`) shows badges **"replacement"** (primary) and **"normal"** (success) — lowercase internal status strings. Everywhere else status chips are Title case with the §10.0 vocabulary ("Pending", "Replacement", "Approved"…). Breaks the same-name-same-meaning rule; should be "Replacement"/"Normal" (or the legend's terms).
- Location: mobile class-card builder in `venue-timetable-UI-design-template.blade.php`.

### 💡 F-5 — Legend wording assumes hover on a touch surface
- Legend reads "Hover a colour to learn more" and is also rendered on mobile (768) where the grid is replaced by the card list and there is no hover. Suggest "Hover / tap" or hide the legend on mobile.

### 💡 F-6 — 63 stacked bookable-slot cards on mobile
- Every 30-min bookable slot renders as a full-width primary button (63 for B103 week 11). Long scroll; consider grouping by day or a time-range picker. Not blocking — list starts correctly at Thu 08:00 (earliest bookable ✓ lead-time respected).

### Verified-good (no action)
- Console 0 errors/warnings in all passes; both themes correct; no overflow.
- Lead-time contextual banner renders with green `<strong>` date ("bookable from **Thursday, 08 Oct 2026** onward") ✓ matches anchor math; hidden logic intact.
- Commented-out summary cards leave no gap; toolbar flows banner → grid.
- Full week label "Week 11 · 05 Oct 2026 ~ 11 Oct 2026" ✓; venue dropdown + unified chevron ✓; Today primary ✓; TODAY badge primary on Mon ✓.
- Mobile booking flow end-to-end: card click → styled fixed tooltip ("Book B103 on Thu, 08 Oct 2026 at 09:00?" + Book button) → Book → navigates to `/replacement-arrangement` with context banner "Booking B103 · Thu, 08 Oct 2026 · 09:00 — pick a subject to pre-fill the slots." ✓ (Initial "native confirm" suspicion withdrawn — it was the app's styled tooltip; booking did not mutate anything before subject selection ✓.)
- Arrival view of arrangement (mobile) healthy: compact week label, green **Earliest bookable** button, Conflict-Schedule stepper, Back button, both banners stacked.

---

## 4. replacement-arrangement (`/replacement-arrangement`)

### 🟡 F-7 — ~~Week-select label format differs from venue at the same viewport~~ → **RECLASSIFIED 2026-10-06: test artifact + small real sliver (fix shipped in sweep-fixes-round-1)**
- ~~At 1440: arrangement shows the compact label…~~ Cleared on a fresh 1440 load: arrangement renders the **full** label (shared default ✓). The compact appearance came from options baked while the viewport was 768 during sequential testing — options are populated once at load, not on viewport change.
- Real (much smaller) gap found: week-select labels don't re-format when a live resize crosses the ≤768 breakpoint. **Fixed** in `sweep-fixes-round-1`: populateWeekSelect registry + debounced breakpoint-crossing re-populate, selection preserved (verify: venue 768→1440 re-labelled live without reload; value kept).

### 💡 F-8 — Selection span vs class duration
- Class conflict is 10:00–11:30 (**1.5 h**) but clicking a cell previews a **2 h** block ("8:00 AM – 10:00 AM · 4 slots", confirm modal says "4 slots"). If the booking quantum is intentionally duration-rounded-up-to-2h, fine — otherwise the span should mirror the class duration (3 × 30 min). Confirm intended behaviour.

### 💡 F-9 — Available cells lack the venue grid's a11y attributes
- Venue available cells: `role=button`, `tabIndex`, `aria-label` ("Available slot: Thu 08:00"). Arrangement `.cell-available` cells: no `dataset.day/hour`, **no aria-label**, no role — keyboard/AT users can't tell what they're selecting. Feature-parity gap between the two grids.

### Verified-good (no action)
- Console 0 errors/warnings across all passes; dark + light correct; page rgba() usages are shadows/scrim only (no colour-token violations); `rgba(var(--color-primary-rgb…))` used properly.
- Unbookable weeks hidden: week select starts at Week 11 (= current week) ✓ decision A live.
- Lead-time banner + BOOKINGS OPEN (Thu, success) + TODAY (Mon, primary) + green Earliest-bookable button all correct.
- Subject selection flow: conflict strip populates (course · Week 3 slot · cohort · 24 students), venue auto-picks B103, "Showing venues that fit 24 students" helper appears, conflict-slot select appears.
- Cell click → 4-cell primary selection; Submit Request → styled confirm modal ("Confirm Your Selection", correct block summary, Cancel/Confirm/✕) ✓ rule 9; Cancel leaves state intact.
- Cross-page arrival from venue booking shows context banner + Back button ✓.

---

## 5. request-approval (`/request-approval-ui`)

### Verified-good (no action)
- Console 0 errors/warnings across dark / light / mobile.
- **Badge ↔ queue consistency**: 8 results = nav badge 8; after approving one request both drain to 7 live ✓ (`updateNavBadge` wired to the same source).
- FCFS ordering correct (2 Oct → 3 Oct → 4 Oct → 5 Oct, relative stamps "3 days ago / 2 days ago / Yesterday / Today" all anchored to Oct 5 ✓).
- Proposed-replacement venue availability pre-checked per row (⚠ E201 conflict vs ✓ D103/C201/D104) ✓ OCC preview.
- Own pending requests visible in queue (spec FR 3.x — PL sees all) ✓.
- Approve flow: styled "Approve Request" modal with full row summary + optional audit-trail note + Cancel/✕ ✓ rule 9.
- Filters: segmented urgency (All/Urgent/Normal), status select, Hide Completed toggle, Reset Filters, filter chip row with ✕ ✓; mobile collapses to stacked filters + cards (#1/#2 renumbered post-approve ✓); no overflow; "Pending" badge Title case ✓.
- Leaving the page with an in-progress arrangement selection triggers a `beforeunload` guard (intentional protection; noted, not a bug).

---

## 6. my-request-history (`/my-request-history-ui`)

### 💡 F-10 — ~~"My" history has no owner scoping in the mock data~~ → **DOWNGRADED 2026-10-06: no contradiction existed (distinct datasets); contract made explicit in sweep-fixes-round-1**
- ~~Contradiction claim~~ cleared: a courseCode+classDate join of `MockData.requests` vs `approvalRequests` returned **zero overlapping rows** — the history set is a distinct, implicitly persona-owned dataset, not a copy of the approval set. No examiner-facing contradiction.
- Hardening shipped in `sweep-fixes-round-1`: rows carry an explicit `requester` field (derived in one place in mock-data.js; read-only convention respected) + a one-line page filter `requester === MockData.currentUser.name` (no-op until the API introduces other requesters; backend = `WHERE requester_id = auth:id`, FR 4.13).

### Verified-good (no action)
- Console 0 errors/warnings; no overflow; light theme correct.
- "Showing 17 of 20 results" + Exclude Completed toggle + active-filter chip ✓; sort hint ✓.
- Status badges §10.0-correct (Approved = success, Pending = yellow, Rejected = error); QUICK CANCEL enabled **only** on Pending rows ("-" otherwise) ✓ FR-consistent.
- Cancel flow: styled "Confirm Cancellation" modal (row summary, "No, Keep It" / destructive red "Yes, Cancel Request", ✕) ✓ rule 9 — dismissed without mutating.
- Aging indicator on requested-at stamps (30+ days red) — consistent treatment.

---

## 7. my-timetable (`/my-timetable-ui`)

### 💡 F-11 — Generic localStorage key `currentWeek`
- Week persistence works (designed: "keep the user's chosen week across refresh" — a stale Week-6 default traced to an old saved value, cleared ✓ default lands on current week 11). But my-timetable persists under the generic key **`currentWeek`** while venue/arrangement use namespaced keys (`venueTimetableWeek`, `arrangementWeek`). Collision risk + naming inconsistency; rename to `myTimetableWeek`.

### Verified-good (no action)
- Console 0 errors/warnings; no overflow; light theme correct (dark verified in prior rounds).
- Progress bar "Week N of 14" + date range under toolbar tracks the selected week.
- Replacement overlays: "(Replaced for 07-Sep-2026)" blue chip ✓ primary-family; "(Pending since 05 Sep 2026)" yellow ✓ — §10.0 semantics.
- Class Details modal: full info block, **"Normal" badge Title case** (reinforces F-4: venue mobile cards' lowercase "normal"/"replacement" are the outliers), destructive "Cancel Class?" in error red ✓.

---

## 8. cohort-timetable (`/cohort-timetable-ui`)

### Verified-good (no action)
- Console 0 errors/warnings; no overflow; light theme correct.
- Empty state: "Select a faculty first", cohort select properly disabled until faculty chosen, week select disabled until cohort, summary cards zeroed ✓.
- Progressive cascade works: Faculty (FOCS) → 5 cohort options → grid + summaries (9 classes / 17.5 h / 0 / 0 / 0 — matches the rendered grid cell count) ✓.
- Role-aware legend (Your Classes = blue, Others' = green, Others'/Your Pending = yellow, Conflict = red) holds: persona's own classes render blue ✓; TODAY badge primary on Mon ✓; full week label ✓; Today button ✓.
- 💡 minor (fold into F-5): legend "Hover a colour to learn more" again assumes hover.

---

## 9. student-my-timetable (`/student-my-timetable-ui`)

### Verified-good (no action)
- Console 0 errors/warnings; no overflow; desktop + mobile day-grouped cards clean.
- Student RBAC nav (only Student My Timetable + Upcoming Replacements) ✓; cohort chip RSD3(S1)G2 ✓; replaced class overlay blue + "(Replaced for 07-Sep-2026)" ✓.
- Shares the generic `currentWeek` persistence key with my-timetable (see F-11) and uses the compact week label (see F-7).

---

## Shared shell (nav-bar · notifications panel · login/logout)

### ✅ F-12 — html background colours hardcoded in the layout `<style>` → **RESOLVED 2026-10-06: documented as the sanctioned exception (user decision F-12=a)**
- `CodingMAIN.md` §10.0 rule 1 now carries the explicit sanction: the layout's pre-theme paint (`html.dark { background: #0D1B2A }` / `html.light { background: #F0F3F7 }` in `layouts/ui-template.blade.php`) exists because those declarations run BEFORE theme.css is parsed — `var(--color-...)` cannot apply that early. Update-both instruction added (grep `html.dark`). Sanctions end with this one exception; zero code change.

### Verified-good (no action)
- Nav badges dynamic on every page (`updateNavBadge` at layout init; role-scoped bell per F-2 mechanics); Request Approval badge always equals the approval queue's Pending count (8 → 7 after approve ✓).
- Theme toggle works on all 9 pages; `.light` class flips root correctly; token scan found no hardcoded colours in any page's own styles beyond shadow/scrim rgba() and F-12.
- No horizontal overflow at 768 on any page; all modals/tooltips use the styled family (no native confirm/alert anywhere — the venue booking "dialog" suspicion was withdrawn).
- `beforeunload` guard on arrangement (in-progress selection) — intentional.

---

## Summary

| ID | Sev | Page | Finding |
|----|-----|------|---------|
| F-1 | ✅ | replacement-home | Fixed: anchored clock + Overdue/Today/singular labels at all 3 surfaces (decision: keep rows + button) |
| F-2 | ✅ | shell/notifications | Pre-wired: per-user mailbox read store + recipientId; one badge number everywhere (11) |
| F-4 | ✅ | venue-timetable | Fixed: shared StatusText.label() at 3 sites; CSS keys unchanged |
| F-7 | ✅ | arrangement | Reclassified: test artifact (fresh load = full label ✓); resize-crossing re-label fix shipped |
| F-10 | ✅ | my-request-history | Downgraded: datasets share zero rows (no contradiction); explicit requester contract shipped |
| F-11 | ✅ | my-timetable (+cohort+student) | Fixed: per-page keys + legacy migration + keyless default namespacing in WeekNavigator |
| F-12 | ✅ | shell | Documented as the §10.0 sanctioned FOUC exception (zero code change) |
| F-5 | 💡 | venue + cohort | ~~"Hover a colour…" legend on touch surfaces~~ ✅ fixed (round 2): dual copy in the shared partial + `(hover: none)` CSS swap |
| F-6 | 💡 | venue-timetable | ~~63 stacked bookable-slot cards on mobile~~ ✅ fixed (round 2): day-grouped `.m-slot-day` headers |
| F-8 | 💡 | arrangement | ~~2 h selection span vs 1.5 h class duration~~ ✅ fixed (round 2): BLOCK_SPAN re-derives from the picked conflict slot (inclusive-end +1); URL branch keeps initial authority |
| F-9 | 💡 | arrangement | ~~Available cells lack role/tabIndex/aria-label~~ ✅ fixed (round 2): `setupAvailableCell()` = venue parity + Enter/Space + focus preview |

**Bonus (round 2):** static token scan across all 9 templates (sweep's live pass skipped venue) → venue `color:#fff` ×2 fixed to `var(--color-on-primary)`.

**Zero 🔴 blocking findings.** Sweep covered: 9 pages × (dark 1440 + light + 768 mobile), console logs, token audit, interaction click-throughs (approve, cancel, submit, book, filters, cascades), and cross-page consistency. No fixes applied — report only.



## Round 3 addendum (2026-10-06 — user-directed, not sweep findings)

Round 3 (`.sdd/changes/sweep-fixes-round-3`, verified PASS) was feature work rather
than defect fixes, but it closed the sweep's one blind spot: **replacement-home's
data table** (JS-built, missed by the original `<table` inventory grep) is now fully
covered by the sortable-table consolidation, alongside Replacement History (renamed
from Upcoming Replacements), My Request History and Request Approval. Also shipped:
role-home logo rule (all 9 pages), `All Weeks` dropdown de-duplication on the student
table, and the lecturer-side display-only columns made sortable where reasonable
(Cohort(s) intentionally excluded — multi-value).

**Sortable-table ledger (final):**
| Page | Sortable | Non-sortable (by design) |
|---|---|---|
| Replacement History | Subject, Original Slot, New Slot, Lecturer, Status | # |
| My Request History | Requested At, Course, Original Class, Requested Replacement, Venue, Students, Status | Cohort(s), Quick Cancel |
| Request Approval | Requested At, Lecturer (by name), Course, Original Class, Proposed Replacement, Students, Urgency, Status | #, Actions |
| Replacement Home | Course, Original Class, Days Left, Venue | #, Students, Cohort(s), Conflict Reason, Action |
