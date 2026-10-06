# Explore Brief — sweep-fixes-round-1

**Date:** 2026-10-06 · **Branch:** `fjing` · **Source:** `playwright snapshot/pre-submission-bug-sweep.md` (full-audit sweep, report-only, 2026-10-05/06 — zero 🔴)

## What was explored
A page-by-page audit of all 9 UI templates + shared shell (dark/light × 1440/768, console, tokens, interactions, cross-page consistency). The sweep is complete and user-approved as the input for this change.

## Findings in scope (user picked "Fix batch" = option 1)
| ID | Sev | Page | One-liner |
|----|-----|------|-----------|
| F-1 | 🟡 | replacement-home | Negative days-left rendered as "Urgent"; past classes stay actionable (dead end after lead-time rule) |
| F-4 | 🟡 | venue-timetable | Mobile card badges leak raw lowercase enum (`replacement`, `normal`) |
| F-7 | 🟡 | arrangement (+student-tt) | Week-select label compact vs full at same viewport across pages |
| F-10 | 🟡 | my-request-history | No owner scoping: all 20 mock rows render as "mine" while approval page attributes them to 5 lecturers |
| F-11 | 💡 | my-timetable (+student-tt) | Generic `currentWeek` localStorage key vs namespaced siblings |

Findings explicitly OUT of scope (need/await separate user decisions):
- F-2 (role-scoped bell badge inconsistency + unreproduced transient) — user decision pending
- F-12 (html.dark/light background hex in layout, FOUC guard) — document-as-exception vs tokenize: user decision pending
- F-5, F-6 (legend hover wording; 63 stacked cards) — polish, defer
- F-8, F-9 (selection span quantum; a11y attrs on arrangement cells) — defer to a later round unless user pulls them in

## Constraints honoured
- Theme.css tokens only; no new dependencies; mock data single-source (`window.MockData`); promote-on-3rd-duplication
- Every touched page gets its `page-changelogs/*.md` postscript
- Sub-fixes must not change frozen SDD artifacts of prior changes (approvalRequests dataset is frozen — F-10 touches `MockData.requests`, not `approvalRequests`)
