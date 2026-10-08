
### Postscript — own-records page + My summary cards (2026-10-08)

The page is now a true **own-records dashboard**: the table lists only
the logged-in lecturer's conflicted classes (owner scoping via the new
`lecturer` field on §2.10 rows; runtime-cancelled rows get theirs from
`ClassCancellation.rowFromEvent`, and `applyLedger` backfills lecturer
for ledgers persisted before this change). Scoping is a function
re-evaluated per rebuild so ledger-appended rows always appear.
Summary bar reduced to three mine-scoped cards — **My Conflicted
Classes (4) · My Hours to Cover (8) · My Courses (2)** — replacing the
old list-wide five (Venues Affected and Students Affected removed as
not user-relevant). Verified: 4 of the 14 seeded conflicts belong to
En. Lim Jia Zheng (ids 1, 2, 7, 13); others' rows no longer render.
Note: §2.10 id 8 (BMIT2073(T)) has no (T) slot in the timetable (only
L/P exist) — its lecturer was attributed by course code.
