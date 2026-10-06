# Tasks — sweep-fixes-round-1

- [x] T1 (D-1) ui-common: promote days-left label contract (`daysLeftLabel`) + urgency note
- [x] T2 (D-1) replacement-home: anchor `daysLeft()` on `DateHelper.getTodayMs()`; table cell + modal Status/Status Description use shared contract; description negative copy; modal urgency vocab unified onto urgency-high/mid/low (dead `.urgency-urgent/warning/normal` classes removed from usage)
- [x] T3 (D-1) ui-common `replacementHomeRow` + `replacementHomeCard`: shared label
- [x] T4 (D-2) ui-common: `StatusText.label()` helper (map + capitalise fallback)
- [x] T5 (D-2) venue: `StatusText.label(e.status)` at history card (:660), event card (:846), modal (:914)
- [x] T6 (D-3) ui-common: `populateWeekSelect` registry + debounced breakpoint-crossing resize re-populate (selection preserved)
- [x] T7 (D-4) mock-data: `requester` derived in one place + contract comment; my-request-history owner filter line
- [x] T8 (D-5) WeekNavigator default key namespaced (`weekNav-<selectId>`) + legacy `currentWeek` migration in `load()`; explicit keys: myTimetableWeek / cohortTimetableWeek / studentTimetableWeek
- [x] T9 Verified live (Playwright): Overdue in table/card/modal (screenshot-verified), anchor clock consistent, venue badges Title case ×3, 768→1440 re-label without reload (value preserved), legacy key migrated (`myTimetableWeek=5`, `currentWeek` gone), history counts unchanged (17 of 20), 0 console errors on all 6 affected pages; composer lint:check + types:check run — only pre-existing backend debt, no new findings
- [x] T10 Changelog postscripts (replacement-home, venue, my-request-history, my-timetable, student-my-timetable, cohort-timetable, oop-js-consolidation) + sweep report F-7/F-10 reclassified, F-1/F-4/F-11 marked fixed in the summary table
- [x] T11 (F-2, user decision 2026-10-06: pre-wire per-user) mock-data: `recipientId` derivation block; ui-common: user-keyed read store + first-paint union migration from the retired per-role keys; badge/pill/mark-all = mailbox unread total; mark-all = whole mailbox; panel open resyncs the header; list stays category-scoped (AD-2 frozen); TEMP console helpers updated. Verified live: badge 11 identical on lecturer + student pages, mark-one-read drains to 10 with pill synced, fresh-store reseed deterministic, 0 console errors
- [x] T12 (F-12, user decision 2026-10-06: option a) CodingMAIN.md §10.0 rule 1 documents the layout's pre-theme paint literals as the sole sanctioned FOUC exception (update-both note; literals confirmed at `layouts/ui-template.blade.php:23`)
- [x] T13 Sweep report F-2/F-12 closed; notifications-panel + oop-js-consolidation changelog postscripts appended

**Completion notes (deviations from design):** none material. F-10 shipped as "derived in one place" rather than 20 inline literals (cleaner, same contract). CohortTimetable gained an explicit key too (same collision family as F-11). F-2/F-12 arrived post-design as user decisions and shipped as T11–T13 above. Badge demo number is now **11** (21 mailbox rows − 10 data `read:true`) — previously 3/5 per page context; the archived AD-4 per-role key shape is superseded by the user-keyed store (F-2 decision). Residual: phpstan/pint baseline debt is pre-existing and untouched.
