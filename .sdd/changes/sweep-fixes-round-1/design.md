# Design — sweep-fixes-round-1

**Decisions frozen 2026-10-06:** F-1 = label "Overdue", keep row + button enabled (arrangement's lead-time banner explains the blocked state). F-7 = ship the shared resize re-populate fix. F-10 re-diagnosed during design: the two 20-row datasets share **zero rows** (harness join, courseCode+classDate) — `MockData.requests` is implicitly persona-scoped by construction, so no re-attribution; make the contract explicit instead.

## D-1 — F-1 (replacement-home, negative days-left)

Shared contract, promoted to `ui-common.js` (table cell + mobile card + modal = 3rd duplication):

```js
/* daysLeft → display contract. Anchor = DateHelper.getTodayMs() (demo/at-anchor,
   TODO(backend): real clock at go-live). label: <0 "Overdue", 0 "Today",
   1 "1 day left", else "N days left". urgency: <0 urgency-high (red), ≤3 urgent,
   ≤7 warning-approaching, else normal — single vocabulary, mapped per surface. */
```

- `daysLeft(iso)` in the page → anchored on `DateHelper.getTodayMs()` (was `new Date()`).
- `HtmlBuilder.replacementHomeCard` (ui-common:2169) → shared label fn; "Overdue" still renders in the same red slot (urgency-high) — visual family unchanged.
- Quick-view modal (:467 Status row, :468 Status Description): Status = shared label; Description gains the negative branch **"Class date has passed — a replacement can no longer be arranged automatically."** (≤3 → Urgent…, ≤7 → Approaching…, else Within normal lead time — unchanged).
- `urgencyClass(days)`: add `<0 → 'urgency-high'` (no numeric change).
- Table DAYS LEFT cell: same shared label.

## D-2 — F-4 (venue raw status enums)

Promote `StatusText` to `ui-common.js`:

```js
const StatusText = {
    map: { normal: 'Normal', replacement: 'Replacement', pending: 'Pending',
           approved: 'Approved', rejected: 'Rejected', conflict: 'Conflict' },
    label(status) { return this.map[String(status).toLowerCase()]
                    || (String(status).charAt(0).toUpperCase() + String(status).slice(1)); }
};
```

Use at the 3 venue sites (:660 history card, :846 event card badge, :914 modal Status row). `badge-${e.status}` CSS class keys stay raw-enum — zero visual change, text only. Largely-safe for future pages; do NOT retrofit Title-case-native pages (their values already equal the label).

## D-3 — F-7 reclassified → shared resize re-populate

`populateWeekSelect` (`ui-common.js:2549`) gains a registry + breakpoint-crossing refresh:

- `populateWeekSelect._registry[selectId] = cfg` (set on every call).
- ONE debounced resize listener (ui-common scope, not per page): on crossing `<= 768` (per-select `lastIsMobile`), re-run `populateWeekSelect(id, cfg)` preserving the current DOM value across the `innerHTML` rebuild.
- Mirrors the venue `mobileCardList` resize precedent; all WeekNavigator pages get it for free (no page-side edits).

## D-4 — F-10 downgraded → explicit mock contract

- `MockData.requests` section: comment declaring rows are **the current persona's own submissions** (single-user mock; API day = `WHERE requester_id`), plus `requester` field set to `MockData.c currentUser.name`-literal `'En. Lim Jia Zheng'` on all 20 rows.
- Page (`my-request-history`): one-line filter `requester === MockData.currentUser.name` with the same backend comment (no-op today — all rows match; protects the page the moment extra requesters appear).
- Sweep report F-10 updated to 💡 (downgraded from 🟡): no contradiction existed (distinct datasets).

## D-5 — F-11 (localStorage key)

- `WeekNavigator` default storage key `'currentWeek'` → `'myTimetableWeek'` (ui-common:307).
- Migration in `load()`: if new key absent and legacy `currentWeek` present → read + migrate, remove legacy key (both my-timetable and student-tt benefit; venue/arrangement keys untouched).

## Promoted to shared
- `ui-common.js`: days-left label contract (D-1), `StatusText` (D-2), `populateWeekSelect` registry+resize (D-3, extends existing shared fn).

## Risks / guardrails
- `ui-common.js` edits are cross-page — re-verify console on the pages that use the touched fns (replacement-home, venue, arrangement, my-timetable, student-tt, upcoming).
- No visual/color/geometry changes anywhere: text + key names + resize hook only.
- No edits to frozen sections (`approvalRequests` untouched).
