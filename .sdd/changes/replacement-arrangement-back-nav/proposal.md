# Proposal: Replacement Arrangement — Dynamic Back Navigation

## Why

The `/replacement-arrangement` page is reached from 3 different pages:
1. **replacement-home** — "Arrange Replacement" button
2. **my-request-history** — Empty state CTA "Submit a Replacement Request"
3. **venue-timetable** — Some action

Currently, the back button always goes to the same place (or doesn't work properly). Users expect to return to where they came from.

## Scope

### In scope (this change)

- Capture the referrer page when navigating to `/replacement-arrangement`
- Store referrer in URL parameter or session
- Back button navigates to the correct source page

### Not in scope

- Changes to the 3 source pages (replacement-home, my-request-history, venue-timetable)
- Backend logic or database changes
- New features on the replacement-arrangement page

## Impact

### Files to modify

1. `resources/views/ui-design-templates/replacement-arrangement-UIdesign-template.blade.php` — back button logic

### Shared files (read-only)

- `routes/web.php` — may need query parameter handling

### Risk

Low — small, isolated change to back button behavior.
