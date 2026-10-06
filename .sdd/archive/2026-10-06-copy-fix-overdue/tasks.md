# Tasks — copy-fix-overdue

- [x] T1 fix the overdue Status Description string (replacement-home:480)
- [x] T2 verify live: overdue row → detail modal shows corrected copy; 0 console errors
- [x] T3 changelog postscript (replacement-home); note: design = the wording itself (no architectural decisions — single-string change)
- [x] T4 (user ask, option A) shared `daysLeftLabel()` (ui-common:2083): overdue branch renders "Overdue (N days ago)" w/ singular handling — one edit covers the table cell (ui-common:2220), card view (:2243) and home modal (home:479); `.col-urgency` widened 100→150px (no wrap, verified via scrollHeight check)
- [x] T5 verified live: "Overdue (31/25 days ago)" in cells + modal, 0 console errors
