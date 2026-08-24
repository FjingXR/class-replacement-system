# Upcoming Replacements UI — Implementation Details

> **For:** Student-facing Upcoming Replacements page design work (Sprint 3)
> **Status:** Stub shipped — nav link live, data live, UI pending
> **Read first:** `CodingMAIN.md` §10.0 UI rules + `prompts/sdd-propose-ui-page.md`
> **Related:** TASK-006 in `todo-list.md`, `student-request-history-drop-plan.md`

---

## 1. What Already Exists (done in TASK-006 Phase 1)

| Piece | Location |
|---|---|
| Nav item "Upcoming Replacements" | `student-my-timetable-UI-design-template.blade.php:4-7` |
| Route `GET /upcoming-replacements-ui` | `routes/web.php:39-41` |
| Stub template | `resources/views/ui-design-templates/upcoming-replacements-UI-design-template.blade.php` |
| Dataset `MockData.upcomingReplacements` (3 rows) | `public/js/mock-data.js` §2.12 |

The stub already wires: student `navItems` (copy this exact array into the real page),
`pageKey='upcomingReplacements'`, semester chip + notif badge init.

---

## 2. Data Contract (`MockData.upcomingReplacements`, read-only)

```js
{
    id: Number,
    code: 'BMIT7074', name: 'Software Testing', type: 'L'|'T',
    lecturer: 'Dr. Koh Li May', cohort: 'RSD3(S1)G2',
    week: 1,                                  // 0-indexed; matches rsd3g2Flags keys
    status: 'replacement' | 'pending',        // approved-upcoming vs awaiting PL
    requestedAt: '25-Aug-2026',               // '' when pending
    di, start, end,                           // ORIGINAL slot — same 30-min index
                                              // space as rsd3g2Base (hours map
                                              // ui-common.js:316)
    originalDay, originalTime, originalVenue,
    newDi, newStart, newEnd,                  // null when still pending
    newDay, newTime, newVenue,                // null when still pending
}
```

Rules:
- Treat as read-only — `slice()`/spread before any mutation.
- New rows MUST reference real `rsd3g2Base` courses and weeks that exist in
  `rsd3g2Flags` so grid ↔ list never contradict.
- If you need more rows, add them to the ONE §2.12 section — never inline in the page.

---

## 3. Status → Color Map (canonical, no exceptions)

| status | Badge class | Token bg |
|---|---|---|
| `replacement` (approved & upcoming) | `.badge-replacement` | `var(--color-primary)` blue — theme.css:664 |
| `pending` (awaiting PL approval) | `.badge-pending` | `var(--color-warning)` yellow — theme.css:665 |

Same label = same color everywhere (CodingMAIN §10.0). Never invent a third status color;
never hardcode hex/rgb.

---

## 4. OOP Reuse Inventory (build ONLY from these)

| Need | Use | Where |
|---|---|---|
| Page frame | `@extends('layouts.ui-template')` | keep stub's params |
| Header | `@include('partials.ui-page-header')` | title/desc/chips props |
| Week filter | `@include('partials.ui-week-nav')` + `WeekNavigator` / `populateWeekSelect` | ui-common.js |
| Empty state (no replacements this week) | `@include('partials.ui-empty-state')` | unhide via JS like stub does |
| Detail view (card click) | `openClassModal({ event, dayIndex, days, title })` | ui-common.js:564 |
| Copy toast | `<div class="copy-toast" id="copyToast">` + `initEvCodeCopy('copyToast')` | stub already has it |
| Summary strip (optional) | `@include('partials.ui-summary-bar')` | same pattern as student timetable |
| Icons | reuse existing SVGs under `resources/views/flux/icon/` patterns | no plain-text buttons |
| New CSS classes | append to `theme.css` using tokens only | promote-on-3rd applies |

### openClassModal wiring (detail modal, ≤1 click from card)
Cards are not grid cells, so build a synthetic event for the shared modal:

```js
function openReplacementModal(r) {
    const days = weekDays(r.week);            // derive from MockData.semester
    openClassModal({
        event: {
            code: r.code, name: r.name, type: r.type,
            lecturer: r.lecturer, venue: r.newVenue || r.originalVenue,
            status: r.status === 'pending' ? 'pending' : 'replacement',
            remarks: r.requestedAt ? 'Requested ' + r.requestedAt : '',
        },
        dayIndex: r.status === 'pending' ? r.di : r.newDi,
        days: days,
        title: r.code + ' \u2014 Replacement',
        extraFields: [
            { label: 'Original Slot', value: r.originalDay + ', ' + r.originalTime + ' \u00b7 ' + r.originalVenue },
            { label: 'New Slot', value: r.newDay ? (r.newDay + ', ' + r.newTime + ' \u00b7 ' + r.newVenue) : 'Awaiting PL approval' },
        ],
    });
}
```

If `openClassModal` needs a small extension (e.g. footer button variant), extend the
shared function with an optional cfg flag — do NOT copy it into the page.

---

## 5. Suggested Layout (design freedom within these rules)

Option A shape chosen earlier (cards, zero extra clicks):

```
PageHeader (chips: cohort)
WeekNav (week filter — defaults to current week)
─────────────────────────────────────────────
[replacement cards for selected week]   ← .upcoming-card row/grid
  ┌ code · name · type ── badge ─┐
  │ old slot → new slot (arrow)  │
  │ lecturer                     │
  └ click = detail modal ────────┘
EmptyState when 0 cards ("No upcoming replacements this week")
```

- Filter: show only rows where `r.week === currentWeek`; changing weeks re-renders.
- Card anatomy must stay scannable: subject identity, old→new transition, status badge.
- Pending row: new-slot cell shows "Awaiting PL approval" instead of fake details.

## 6. Hard Rules (from CodingMAIN §10.0 + AGENTS.md)

1. Colors only via `theme.css` custom properties.
2. Same meaning = same name = same color (§3 map above).
3. No copy-paste: reuse partials/shared JS; promote-on-3rd anything repeated.
4. Mock data from `window.MockData` only — single source.
5. Icon buttons over text; `title`/`aria-label`.
6. Detail in modals, not page surface; ≤3 clicks per task.
7. Responsive ≤768px mandatory (drawer/cards/44px touch targets).
8. After Blade changes: clear cache — `pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &`.
9. Update `upcoming-replacements-ui-changelog.md` + this file's status when built.

---

## 7. Verification Checklist (when UI is built)

- [ ] Student nav: both items render; active state follows `activeNav`
- [ ] Cards show only the selected week's rows (weeks 1/2/7 have demo data)
- [ ] Approved row → blue badge, full new-slot info; click opens modal with Original/New Slot fields
- [ ] Pending row → yellow badge, "Awaiting PL approval", modal shows no invented venue
- [ ] Empty weeks → empty state visible
- [ ] Playwright via `with_server.py`: nav click-through, card→modal screenshot
- [ ] Staff pages untouched; `/my-request-history-ui` still loads directly for staff
