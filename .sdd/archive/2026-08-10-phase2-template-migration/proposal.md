# Proposal: Phase 2 — Template Migration to Shared OOP Classes

## Why

Phase 1 established the shared OOP infrastructure (`DateHelper`, `TableController`, `HtmlBuilder`, `WeekNavigator`) in `ui-common.js`. However, most templates still contain **inline implementations** of date formatting, table building, and other helpers that duplicate what the shared classes provide.

This creates:
- **Maintenance drift** — bug fixes in shared classes don't reach inline copies
- **Inconsistency** — different templates format dates/tables differently
- **Code bloat** — each template carries ~50-200 lines of redundant helpers

## Scope

### In scope (this change)

Migrate the following 4 templates to use shared OOP classes:

| Template | Inline helpers to migrate | Notes |
|----------|--------------------------|-------|
| `my-request-history-UI-design-template.blade.php` | `renderTable()` (L560), `renderCards()` (L747) | Highest inline usage |
| `request-approval-UI-design-template.blade.php` | `formatShortDate()` (L553), `renderTable()` (L882), `renderCards()` (L894) | Moderate |
| `replacement-home-UI-design-template.blade.php` | `computeWeek()` (L270), `weekRangeLabel()` (L279), `buildTable()` (L299), `renderCards()` (L394) | Most duplication |
| `student-my-timetable-UI-design-template.blade.php` | `fmtShort()` (L61) | Minimal |

### Migration targets

| Inline helper | Migrate to |
|---------------|------------|
| `formatDate(iso)` / `formatShortDate(iso)` | `DateHelper.formatDate()` or `DateHelper.fmt()` |
| `fmtShort(d)` | `DateHelper.fmtShort()` (new static method) |
| `computeWeek(isoDate)` | `getWeekNumber()` from `ui-common.js` |
| `weekRangeLabel(weekNum)` | `weekRanges` lookup from `ui-common.js` |
| `buildTable()` / `renderTable()` / `renderCards()` | `TableController.build()` or `HtmlBuilder` methods |

### Already shared (no migration needed)

- `formatDateTime()` calls — these use the global alias from `ui-common.js`, already shared
- `dayAbbr()`, `HtmlBuilder.classBlock()`, `HtmlBuilder.replacementBlock()` — already using shared classes

**Not in scope:**
- `replacement-arrangement-UIdesign-template.blade.php` — complex grid logic, separate migration
- `CohortTimetable`, `MyTimetable`, `venue-timetable` — already migrated in Phase 1
- Backend logic, migrations, models
- New features or UI changes

## Impact

### Files to modify

1. `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php`
2. `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php`
3. `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php`
4. `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php`

### Shared files

- `public/js/ui-common.js` — `DateHelper`, `TableController`, `HtmlBuilder` classes (will be extended with new static methods)
- `public/js/mock-data.js` — mock data source (read-only, no changes)

### Risk

Low — pure refactoring, no behavioral changes. Each template can be migrated and tested independently.
