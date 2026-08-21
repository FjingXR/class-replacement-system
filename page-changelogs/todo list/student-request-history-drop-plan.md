# Student Request History — Drop & Upcoming Replacements Plan

## Goal
Remove the Full Request History page from the student role, and prepare for an Upcoming Replacements feature (implementation approach TBD — separate page or section/card on student timetable).

---

## Why This Change

- Ch1 §1.1.4 states: "Student: view-only access for students" — status only, not a full history ledger.
- FR 1.3 (Ch3 §3.4) states: "Students shall be able to view the status of replacement requests for their cohort."
- FR 1.4 states: "Students shall be able to view upcoming replacement details for their cohort."
- The full Request History page is a PL/lecturer feature (audit trail, FR 3.7) — students have no decisions to make from past replacement history.
- The "Upcoming Replacements" feature directly addresses Ch1 §1.2 pain points: "told about the change too late", "hard to plan my day", "missing the class entirely because I wasn't informed".

---

## Decision Log

| Decision | Status |
|----------|--------|
| Drop Full Request History for students | ✅ Confirmed |
| Upcoming Replacements — separate page OR section/card on student timetable | ⏳ TBD (see below) |

### Upcoming Replacements — Two Options (to be decided)

**Option A: Section/card on Student My Timetable page**
- Zero extra clicks — visible on arrival
- Summary cards above/below the timetable grid
- Click card → detail modal (reuse `openClassModal`)
- Fits existing UI pattern: summary on page → modal for detail
- Matches UI rule #7 (fewest clicks) and #5 (modals for detail)
- Best for: 0-3 upcoming replacements per week (typical per cohort)

**Option B: Separate Upcoming Replacements page**
- Dedicated page with list/table of upcoming replacements
- More space for filters, sorting, week navigation
- +1 click through nav drawer
- Better if: many replacements per week, or if the timetable page gets too crowded

**Recommendation:** Option A (section/card) — replacements are rare per cohort per week, cards handle 0-3 easily, zero clicks beats a page. But defer final decision until Sprint 3 when real data is available.

---

## Files to Create/Modify

| # | File | Action |
|---|------|--------|
| 1 | `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php` | **Modify** — remove "Request History" nav item from `navItems` |
| 2 | `public/js/mock-data.js` | **Modify** — add `upcomingReplacements` dataset (when approach is decided) |
| 3 | `page-changelogs/student-my-timetable-ui-changelog.md` | **Update** — record nav item removal |

---

## Step 1: Remove "Request History" from Student Nav

Current `student-my-timetable-UI-design-template.blade.php` (lines 3-8):

```php
@extends('layouts.ui-template', [
    'activeNav' => 'my-timetable',
    'pageKey' => 'studentMyTimetable',
    'navItems' => [
        ['key'=>'my-timetable','label'=>'Student My Timetable','href'=>'/student-my-timetable-ui'],
        ['key'=>'replacement-history','label'=>'Request History','href'=>'/my-request-history-ui'],
    ],
    'notifCount' => 3,
])
```

Change to:

```php
@extends('layouts.ui-template', [
    'activeNav' => 'my-timetable',
    'pageKey' => 'studentMyTimetable',
    'navItems' => [
        ['key'=>'my-timetable','label'=>'Student My Timetable','href'=>'/student-my-timetable-ui'],
    ],
    'notifCount' => 3,
])
```

**Effect:** Students only see "Student My Timetable" in their nav. The "Request History" link (`/my-request-history-ui`) is no longer accessible from the student view.

**Note:** The `/my-request-history-ui` route itself stays — lecturers and PLs still use it. Only the student nav item is removed.

---

## Step 2: Upcoming Replacements (TBD — Sprint 3)

When the approach is decided (Option A or B), implement:

### If Option A (section/card on timetable page):

1. Add `upcomingReplacements` dataset to `public/js/mock-data.js`:
   ```js
   upcomingReplacements: [
       {
           code: 'BMIT2073',
           name: 'Mobile Application Development',
           type: 'L',
           cohort: 'RSD3(S1)G2',
           originalDay: 'Tuesday',
           originalTime: '14:00-16:00',
           originalVenue: 'B110',
           newDay: 'Thursday',
           newTime: '10:00-12:00',
           newVenue: 'B005',
           lecturer: 'En. Lim Jia Zheng',
           status: 'approved', // or 'pending'
           week: 7
       },
       // ... more entries
   ]
   ```

2. Add "Upcoming Replacements" card section to `student-my-timetable-UI-design-template.blade.php`:
   - Position: above the timetable grid (below semester bar)
   - Each card: subject code, new day/time/venue, status badge, lecturer name
   - Click card → open existing `openClassModal` with full primary info
   - Empty state: "No upcoming replacements this week" (reuse `ui-empty-state` partial)

3. Filter by current week — only show replacements for the week the student is viewing.

### If Option B (separate page):

1. Create `resources/views/ui-design-templates/upcoming-replacements-UI-design-template.blade.php`
2. Add route in `routes/web.php`
3. Add nav item to student nav
4. Build table/list with week filter, status badges, detail modal

---

## Step 3: Verification Checklist

- [ ] Student login → nav shows only "Student My Timetable" (no "Request History")
- [ ] Lecturer login → nav still shows "Request History" (unchanged)
- [ ] PL login → nav still shows "Request History" (unchanged)
- [ ] Student My Timetable page loads without errors
- [ ] (Sprint 3) Upcoming Replacements section/page shows replacement data for current week
- [ ] (Sprint 3) Clicking a replacement card/row opens detail modal with primary info
- [ ] (Sprint 3) Empty state shows when no upcoming replacements this week

---

## Order of Implementation

1. Remove "Request History" nav item from student template (immediate — 1 line change)
2. Update `student-my-timetable-ui-changelog.md`
3. (Sprint 3) Decide: Option A (card) or Option B (page)
4. (Sprint 3) Add `upcomingReplacements` mock data
5. (Sprint 3) Implement chosen approach
6. (Sprint 3) Wire detail modal (reuse `openClassModal`)
7. (Sprint 3) Test all roles — student sees upcoming, lecturer/PL see history

---

## Notes

- The "Request History" page (`/my-request-history-ui`) is NOT deleted — it's still used by lecturers and PLs. Only the student nav link is removed.
- The notification badge (`notifCount: 3`) stays on the student nav — it's unrelated to request history.
- FR 1.3 (view status) is already covered by the timetable legend (red = conflict, blue = replacement, yellow = pending). No separate UI needed for status.
- FR 1.4 (upcoming replacement details) implementation is deferred to Sprint 3 — this plan only tracks the nav cleanup and records the two options.
