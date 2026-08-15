# Replacement Arrangement — Toolbar Restructure Plan

**Date:** 2026-08-15
**Status:** Pending implementation
**Page:** `replacement-arrangement-UIdesign-template.blade.php`

---

## Problem

The current toolbar mixes 3 concerns in one row (week nav, subject selector, venue selector) with a separate slot picker list below. The layout feels overwhelmed and unstructured.

## Goal

Split into two clear zones on the same row:
- **PRIMARY (left):** What am I replacing? (subject + which slot)
- **FILTERS / ACTIONS (right):** Where/when to look? (week + venue)

---

## Current Layout (before)

```
┌─────────────────────────────────────────────────────────────────┐
│  [< Week 1 >] [Today]  │  [Select a subject ▾]     │ [Venue ▾] │
│                         │  DFT1234 — OOP (Lecture)  │           │
│                         │  Cohort: DFT2(S1) · 35    │           │
├─────────────────────────────────────────────────────────────────┤
│  Slot Picker (list):                                            │
│  ○ W5 · Mon 10:00–12:00 @ B103    [Conflict]                   │
│  ○ W5 · Wed 14:00–16:00 @ B103    [Cancelled]                  │
├─────────────────────────────────────────────────────────────────┤
│  Hint: Select an available (green) time slot                    │
├─────────────────────────────────────────────────────────────────┤
│  [Timetable Grid]                                               │
├─────────────────────────────────────────────────────────────────┤
│  Legend: Available | Selection | Pending | PH/Sun | Reserved    │
├─────────────────────────────────────────────────────────────────┤
│  Summary: 154 Total | 94 Available | 4 Pending | 56 Unavail    │
├─────────────────────────────────────────────────────────────────┤
│  Selection Summary (card)                                       │
├─────────────────────────────────────────────────────────────────┤
│  Cohort: DFT2(S1)/DSF2(S1)        [Clear ALL] [Submit Request] │
└─────────────────────────────────────────────────────────────────┘
```

## Target Layout (after)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  [← Back]              Replacement Arrangement              [🌙]            │
├─────────────────────────────────────────────────────────────────────────────┤
│  202505 Semester · 27-Jul-2026 – 26-Oct-2026                              │
├─────────────────────────────────────────────────────────────────────────────┤
│  ▸ How to use this page                                                    │
│    • Select subject — choose the course to arrange a replacement for       │
│    • Slot status — Available (green), Unavailable (booked / PH / Sunday)   │
│    • Submit — confirm selection to send request for approval                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  PRIMARY (left)                          │  FILTERS / ACTIONS (right)       │
│  ┌──────────────────────────────┐        │  ┌─────────────────────────────┐  │
│  │ [Select a subject        ▾] │        │  │ [<  Week 1 · 27 Jul ~ 02 Aug >] [Today] │  │
│  └──────────────────────────────┘        │  └─────────────────────────────┘  │
│  DFT1234 — Object-Oriented Programming (L)  │  ┌─────────────────────────┐  │
│  35 Students                             │  │ [B002 — Tutorial (35) ▾] │  │
│  ┌──────────────────────────────────────┐│  └─────────────────────────┘  │
│  │ [W5 · Mon 10:00–12:00 @ B103    ▾] ││                                   │
│  └──────────────────────────────────────┘│                                   │
│                          ▲               │                                   │
│  hover tooltip (above):  │               │                                   │
│  ┌─────────────────────────────────────┐ │                                   │
│  │ Week 5 · Monday, 28 Jul 2026       │ │                                   │
│  └─────────────────────────────────────┘ │                                   │
│                                                                             │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ DAY / TIME  │ 08:00 │ 08:30 │ 09:00 │ ... │ 17:00 │ 17:30        │   │
│  │─────────────┼───────┼───────┼───────┼─────┼───────┼───────        │   │
│  │ Mon 27 Jul  │  ██   │  ██   │  ██   │     │  ██   │  ██          │   │
│  │ PUBLIC      │       │       │       │     │       │               │   │
│  │ HOLIDAY     │       │       │       │     │       │               │   │
│  │─────────────┼───────┼───────┼───────┼─────┼───────┼───────        │   │
│  │ Tue 28 Jul  │  🟩   │  🟩   │  🟩   │     │  🟩   │  🟩          │   │
│  │─────────────┼───────┼───────┼───────┼─────┼───────┼───────        │   │
│  │ ...         │       │       │       │     │       │               │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
├─────────────────────────────────────────────────────────────────────────────┤
│  Legend:                                                                     │
│  🟩 Available  │  🔵 Your Current Selection  │  🟡 Pending (You)           │
│  🔴 Classes on Public Holiday / Sunday  │  ⬜ Reserved by Others           │
├─────────────────────────────────────────────────────────────────────────────┤
│  Selection Summary                                                          │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │  📅 No time slots selected.                                        │   │
│  │     Click an available (green) time slot to begin.                 │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────────────────────┤
│                                         [Clear ALL 🗑]  [Submit Request →]  │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## Changes Summary

### 1. Toolbar Restructure

**Before:** 3-column toolbar (left: week, center: subject, right: venue)
**After:** 2-zone layout (left: primary info, right: filters)

| Zone | Elements | Position |
|------|----------|----------|
| PRIMARY (left) | Subject dropdown + subject info line + slot dropdown | Top-left |
| FILTERS (right) | Week nav + Today btn + Venue dropdown | Top-right |

### 2. Subject Dropdown

- Stays as a dropdown (user can change subject anytime)
- After selection, shows info line below: `CODE — Name (Type)` + student count
- Type badge inline as (L), (T), or (P)
- **Remove:** cohort info from this line (was: `Cohort: DFT2(S1) / DSF2(S1)`)

**Format:**
```
DFT1234 — Object-Oriented Programming (L)
35 Students
```

### 3. Slot Picker → Dropdown

**Before:** List of radio buttons in a card
**After:** Dropdown select

**Options format:** `W5 · Mon 10:00–12:00 @ B103`
**Hover tooltip (above):** `Week 5 · Monday, 28 Jul 2026`

**Behavior:**
- Hidden until subject is selected
- Options: conflict + cancelled slots from `extractSlotsForSubject()`
- Tooltip shows full date above the dropdown element

### 4. Removed/Commented Out

| Element | Status | Reason |
|---------|--------|--------|
| Summary bar cards (Total/Available/Pending/Unavailable) | Commented out | Reduces visual clutter |
| Progress bar | Commented out | Reduces visual clutter |
| `sel-summary-count` ("0 / 4 Selected") | Commented out | Reduces visual clutter |
| Hint text div | Removed | Covered by guide block + legend tips + empty state |
| Cohort in footer | Removed | Redundant with subject info |
| Cohort in subject meta | Removed | Redundant |

### 5. Mobile Responsive (≤768px)

- Stack vertically: primary on top, filters below
- Full-width dropdowns
- Tooltip stays above

---

## Files to Modify

| File | Changes |
|------|---------|
| `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | Restructure toolbar HTML + update `renderSlotPicker()` to render dropdown + add tooltip CSS + remove hint text div + update footer |
| `public/css/theme.css` | Add tooltip-above CSS if not exists |

## Implementation Steps

1. **Restructure toolbar HTML** — replace 3-column layout with 2-zone layout
2. **Update subject info line** — format as `CODE — Name (Type)` + student count, remove cohort
3. **Convert slot picker to dropdown** — replace radio list with `<select>`, update `renderSlotPicker()`
4. **Add hover tooltip** — above the slot dropdown, show full date on hover
5. **Remove hint text div** — no longer needed
6. **Update footer** — remove cohort info
7. **Mobile responsive** — stack zones vertically on ≤768px
8. **Test** — verify all interactions (subject change, slot select, week nav, venue change)

---

## Questions to Resolve

- [ ] Should the slot dropdown default to the first option or show "Select a slot to replace" placeholder?
- [ ] Should changing subject reset the slot selection?
- [ ] Should changing venue affect the slot dropdown options? (Currently slots are subject-based, not venue-based)
