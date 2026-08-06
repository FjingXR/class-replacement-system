# Proposal: Replacement Arrangement — UX Enhancement Features

## Why This Change Is Needed

The existing Replacement Arrangement page is functional but lacks polish for power users. Lecturers selecting replacement slots need faster ways to navigate the grid, correct mistakes, and avoid conflicts. The current page has no keyboard support, no undo mechanism, no visual progress feedback, no capacity warnings, and no conflict detection. These 5 features address real usability gaps identified during testing.

## Scope

### In Scope

Enhance the **existing** `replacement-arrangement-UIdesign-template.blade.php` with 5 features:

**F1: Keyboard Shortcuts**
- Arrow keys (↑↓←→) navigate grid cells
- Enter/Space toggles cell selection
- Escape closes modal OR clears selection
- ? shows help overlay with shortcut list
- Focus indicator: `outline: 2px solid var(--color-primary)`

**F2: Selection Undo (Ctrl+Z)**
- Maintain `selectionHistory[]` stack
- Ctrl+Z reverses last selection/deselection
- Toast notification "Selection undone" (auto-dismiss 2s)

**F3: Progress Indicator**
- Visual bar above timetable: "Selected N of 4 slots"
- Color: green (complete), amber (in progress), grey (empty)

**F4: Venue Capacity Badge**
- Venue dropdown shows: `B103 — Tutorial Room (35 seats)`
- Warning icon ⚠ if venue capacity < cohort totalStudents

**F7: Conflict Warning Toast**
- On slot selection, check `MockData.myTimetable.eventsByWeek[currentWeek]` for overlap
- If conflict: yellow toast "This slot overlaps with your [courseCode] class"

### Out of Scope

- No new pages, routes, or backend logic
- No new mock data sections (read from existing `MockData.*`)
- No new dependencies (CSS + vanilla JS only)
- No changes to `public/js/mock-data.js`

## FR Traceability

| FR | Requirement | Feature |
|----|-------------|---------|
| FR 2.7 | Click green slot to select | F1 (keyboard alternative), F3 (progress) |
| FR 2.9 | Edit/cancel pending requests | F2 (undo extends this) |
| FR 4.3 | Intersection calculation | F4 (capacity feedback), F7 (conflict hint) |

## Impact Scope

| File | Action |
|------|--------|
| `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` | **Enhance** — add CSS + JS for 5 features |
| `page-changelogs/replacement-arrangement-changelog.md` | **Update** — log all changes |

No new files created. No existing files modified beyond these.
