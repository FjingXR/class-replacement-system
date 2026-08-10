# Proposal: UI JS OOP Refactor

## Why

`ui-common.js` is 767 lines of 80+ standalone functions with no encapsulation. Functions rely on global variables (`currentWeek`, `weekData`, `pageState`), making the code hard to test, maintain, and extend. Timetable templates duplicate week navigation, modal, and table logic across 4+ pages.

This refactor restructures the shared JS into 7 classes/namespaces while keeping all code in `ui-common.js` (no file splitting).

## What

### In Scope
| Item | Detail | Functions Replaced |
|------|--------|--------------------|
| `DateHelper` | Static class — date/time formatting | `to12h`, `formatDate`, `formatDateTime`, `fmt`, `add30min`, `dayAbbr`, `isoDayName`, `getTodayMs` |
| `HtmlBuilder` | Static class — HTML generation | `formatClassBlock`, `formatReplacementBlock`, `buildDayHtml` |
| `SkeletonLoader` | Namespace object — stateless skeleton | `showSkeleton`, `hideSkeleton`, `withSkeleton`, `showSummarySkeleton`, `hideSummarySkeleton` |
| `ToastManager` | Singleton class — toast lifecycle | `showToast`, `dismissToast`, `_toastTimer` |
| `ModalController` | Class — generic modal | `closeOnEsc`, `closeOnOverlayClick`, ESC/overlay patterns |
| `TableController` | Class — config-driven sort + pagination | `makeSortableHeader`, `compareBy`, `paginate`, `initRpp`, `updateResultCount` |
| `WeekNavigator` | Class — week navigation + localStorage | `jumpToToday`, `prevWeek`, `nextWeek`, `selectWeek`, `saveWeek`, `loadSavedWeek`, `updateWeekSubtitle`, `updateWeekProgress`, `initWeekKeyboardShortcuts`, `initTodayBtn`, `currentWeekIndex`, globals `currentWeek`/`weekData` |

### Out of Scope
| Item | Reason |
|------|--------|
| Template-specific functions | `buildTimetable`, `openModal` differ per page |
| Base TimetablePage class | Too invasive — templates would need class instantiation |
| State Persistence | Already works as factory function |
| Scroll Restoration | Already works as functions |
| Navigation helpers | One-time init, no state to manage |
| File splitting | No bundler = more HTTP requests |
| Utility functions | `ripple`, `togglePassword`, `initMobileNav`, `initSwipeGesture`, `initCollapsibleCards`, `updateNavBadge`, `statusClass`, `getWeekRange`, `isInWeek`, `getWeekNumber`, `updateIcon`, `toggleTheme`, `navigateHome`, `updateWeekArrows` — remain as standalone globals |
| Week filter helpers | `weekFilterChanged`, `prevWeekFilter`, `nextWeekFilter` — remain as standalone globals (table pages) |

### Design Decisions (resolved)
| Question | Decision |
|----------|----------|
| Should WeekNavigator auto-save on every navigation? | Yes — save to localStorage on every `prevWeek()`/`nextWeek()`/`selectWeek()`/`jumpToToday()` call |
| Should TableController emit events or call render callback? | Call render callback — simpler, no event bus needed |

## Impact

### Files Modified
| File | Change |
|------|--------|
| `public/js/ui-common.js` | Add 7 classes, remove old standalone functions |
| `resources/views/partials/ui-week-nav.blade.php` | Update onclick handlers to use `window.weekNav` instance |
| `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php` | Instantiate WeekNavigator as `window.weekNav`, use class methods |
| `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` | Instantiate WeekNavigator as `window.weekNav`, use class methods |
| `resources/views/ui-design-templates/student-my-timetable-UI-design-template.blade.php` | Instantiate WeekNavigator as `window.weekNav`, use class methods |
| `resources/views/ui-design-templates/venue-timetable-UI-design-template.blade.php` | Instantiate WeekNavigator as `window.weekNav`, use class methods |
| `resources/views/ui-design-templates/replacement-home-UI-design-template.blade.php` | Use TableController |
| `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php` | Use TableController |
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | Use TableController |

### Dependencies
- None — pure vanilla JS, no new libraries

### Risk
- **Medium** — refactoring shared code affects all pages
- Mitigated by incremental migration (one class at a time, verify after each)

## Acceptance Criteria

1. All 7 classes/namespaces exist in `ui-common.js`
2. Old standalone functions are removed after migration
3. All timetable pages (MyTimetable, CohortTimetable, StudentMyTimetable, VenueTimetable) use `WeekNavigator`
4. All table pages (replacement-home, request-approval, my-request-history) use `TableController`
5. All pages that currently use skeleton/toast/modal patterns use the new classes
6. No console errors on any page
7. All existing functionality preserved (navigation, sort, pagination, modals, toasts)
8. No global variables for week state (`currentWeek`, `weekData`)
