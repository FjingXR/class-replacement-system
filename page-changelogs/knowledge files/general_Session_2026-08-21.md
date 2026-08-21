# Knowledge Transfer — New AI Session Handover

> **Source template:** `/Prompt — Transfer Important Context to a New AI Session.md`
> **Created:** 2026-08-21 | **Updated:** 2026-08-21 (rename + commit)
> **Baseline wanted:** `83fd365` — `fix: timetable grid now loads on initial page load` — reverted via `git reset --hard 83fd365` per user `83fd365e4152669b...`
> **Previous reverted:** `f3d2554` (block model) + `f316278` (rename) — discarded
> **Current HEAD (committed, clean):** `8dbef39` — `docs: add knowledge transfer 2026-08-21, FYP briefing, todo plans + regression tests` (parent `d2c494d` ← `116cbee` ← `83fd365`; `origin/fjing` at `f316278`, diverged 3 vs 1)
> **Branch:** `fjing` — working tree clean
> **Project root:** `/home/philler/Desktop/tarumt/degree/y3s1/BMCS3404 PROJECT I (4)/class-replacement-system`

---

## 1. User Profile & Working Preferences

- **Who:** Poong Foo Jing (25SMR10186), Bachelor in IT (Hons) Software Systems Development, RSD3G2, TAR UMT Sabah, FOCS
- **Supervisor:** Mr. Lim Jia Zheng | **Moderator:** Ms. Teng Nga Sing | **Client:** FOCS Sabah
- **Communication:** Extremely concise. Replies are often single-word (`yes`). Expects agent to execute, not ask follow-ups. When design is wrong, immediately asks to `git back to exact commit` (provide hash, e.g. `83fd365e415...`).
- **Session hygiene:** Frequently creates new sessions; wants a **compact, information-dense handover doc** to transfer context (this file). Wants source template reused, not chronologically summarized.
- **Workflow mode:** Uses Opencode plan/build modes. In `plan` mode = read-only + plan; `build` mode = allowed to edit/run commands. Respects `<system-reminder>` mode switches.
- **Verification expectation:** For coding tasks — agent must verify via execution (run server, grep, playwright `with_server.py`, `composer run lint:check/types:check`), not just claim success. Evidence before synthesis.
- **Git expectations:** Working branch `fjing`. Conventional commits (`ui:`, `feat:`, `fix:`, `refactor:`, `oop:`, `style:`, `docs:`). Never push unless asked. Uses `git reset --hard <hash>` when rejecting work — prefers discarding over `revert` commits.
- **Response style expected from AI:** Short, concise, factual, no superlatives/praise/emojis, include `file_path:line_number` when referencing code.

---

## 2. Academic Context — BMCS3404 Project I (FYP)

- **Course:** BMCS3404 Project I (FYP1: 14 weeks design + FYP2: 7 weeks in 3 sprints)
- **SDG:** SDG 4 (Quality Education), SDG 8 (Decent Work)
- **Proposal/docs:** `25SMR10186_Form 2_Proposal`, `Ch1.pdf`, `FR&NFR.md` (Ch3 §3.4), `Full Functional Specifications V2.pdf` — `CodingMAIN.md` is single source of truth.
- **5 Pain Points:** (1) fragmented spreadsheets, (2) multi-cohort blind spots (e.g. RSD3G1+RSD2G2+RSD2G3 shared), (3) venue PDF separate, (4) Sheets no OCC → double-booking, (5) PL burden + no audit trail.
- **5 Objectives:**
  1. Multi-Entity Matrix Intersection Engine (lecturer × cohort(s) × room × capacity → color grid)
  2. OCC layer (integer `version` on `time_slots`, millisecond validation, rollback)
  3. FCFS Approval Dashboard (queue, 1-click approve, mandatory reject reason, audit trail)
  4. RBAC + notifications (student view-only, lecturer CRUD own, PL hybrid + emails via DB queue)
  5. Prototype Deployment (14 cohorts, 14 staff, 23 Block B rooms)
- **FRs:** 1.1–1.5 (student), 2.1–2.12 (lecturer), 3.1–3.7 (PL), 4.1–4.17 (system) — verbatim in `CodingMAIN.md:188-236`
- **NFRs:** Performance <500ms intersection, <2s page load; Security (Bcrypt, Eloquent, CSRF, 30-min session); Usability (responsive); Reliability (99%, no double-booking); Maintainability (PSR-12/Pint, migrations, service classes); Scalability (50 concurrent, queue worker); Legal (PDPA, localhost only).
- **Current academic phase:** Frontend mock phase — UI design templates only. No migrations/models/backend unless explicitly requested. `mock-data.js` is throwaway-by-design, deleted in Sprint 3.

---

## 3. Projects & Assignments — Class Replacement System

- **Purpose:** Replace manual Google-Sheets replacement workflow at TAR UMT Sabah via deterministic 4-vector intersection + OCC + FCFS dashboard.
- **Current status at `83fd365`:** 5 UI templates built + request-approval planned; seeders for 14 cohorts + 14 lecturers done; 23 venues NOT yet seeded (need migration); Matrix Engine / OCC / wiring still `🔲 Not built` (Sprint 1-2).
- **Routes (web.php):** `/`, `/login/student`, `/login/staff`, `/dashboard`, `/my-timetable-ui`, `/cohort-timetable-ui`, `/replacement-home-ui`, `/replacement-arrangement`, `/my-request-history-ui`, `/request-approval-ui`
- **Nav (partials/ui-nav-bar.blade.php):** Dashboard → My Timetable → Cohort Timetables → Replacement Arrangement → Replacement History (5 items)
- **Recent session narrative (critical):**
  - Agent attempted to restore "block model" (commit `f3d2554`: `selectBlock`/`deselectBlock`/`renderMergedBlock` etc., 4-cell block selection) — user said `wow, this aint the design what i wanted. can u check and git back to exact commit ?`
  - User specified hash `83fd365e4152669bdef05e91b5ce263374261a3d`
  - Agent reset with `git reset --hard 83fd365` — confirmed correct state.
  - **Lesson:** Do NOT re-introduce block model without explicit request. `83fd365` is the canonical wanted design.

---

## 4. Technical / System Context

- **Stack:** Laravel 13 (PHP 8.3, livewire-starter-kit), Livewire 4 + Flux 2 + Blaze, Blade + TailwindCSS + vanilla JS, PostgreSQL `class_replacement` user `philler`, Fortify + WebAuthn passkeys, Pint/PHPStan/PHPUnit, Vite (light use).
- **Key files:**
  ```
  CodingMAIN.md                          ← read FIRST every session (architecture, FR/NFR, conventions §10)
  AGENTS.md                              ← auto-loaded, points to CodingMAIN + prompts/sdd-propose-ui-page.md
  prompts/sdd-propose-ui-page.md         ← mandatory for new UI pages (9 UI rules + DRY promote-on-3rd)
  public/css/theme.css                   ← ALL shared CSS + tokens (--color-bg, --color-primary, etc.)
  public/js/ui-common.js                 ← ALL shared JS (WeekNavigator, DetailModal, to12h, etc.)
  public/js/mock-data.js                 ← SINGLE source of truth for mock data (window.MockData)
  resources/views/layouts/ui-template.blade.php
  resources/views/partials/* (ui-nav-bar, ui-summary-bar, ui-page-header, ui-week-nav, ui-empty-state, ui-grid-table, ui-class-detail-modal)
  resources/views/ui-design-templates/* (5 templates)
  page-changelogs/*.md + todo list/todo-list.md
  .sdd/changes/<change-name>/ (SDD workflow)
  ```
- **Theme tokens (CodingMAIN §10.0):** Never hardcode hex/rgb. Slot-grid mapping: Green=`--color-secondary` (Available), Red=`--color-error` (Occupied/Conflict), Yellow=`--color-tertiary` (Pending), Grey=`--color-outline-strong`/`--color-surface-variant` (Reserved), Blue=`--color-primary` (Selection/Replacement). Status badges use container variants.
- **Venue matrix (CodingMAIN §3):** Tutorial B002,B014–B018,B100–B109 (cap 35 L/T); LectureHall B110,B111 (80 L); Lab B005,B009–B011 (28 P); CiscoLab B006 (32 P). Block C excluded. MPU-3133 rule: tutorial + lecture halls OK, labs excluded (user decision 2026-08-01).
- **Slot State Machine:** `available` → `pending` (self) → `reserved` (other) → `occupied` (approved/locked). Visual colors per §3.
- **Dataset (seeded):** 14 cohorts (FOCS 11 + FAFB 3) + 11 staff DCIT + 2 DACB + 1 DSSH + student counts fixed (e.g. RSD3G3=13, RAF2G4=10). Semester: `202605` 2026-07-27 to 2026-10-26 (14 weeks), `MockData.semester` canonical, `holidays` array 5 entries, `currentUser` = En. Lim Jia Zheng 5770.
- **Mock-data rule:** Pages read `window.MockData.*` only. Treat as read-only — `slice()/spread` before mutating. New data → add ONE section to `mock-data.js`.
- **Commands:**
  ```bash
  composer run dev / php artisan serve --host=127.0.0.1 --port=8000
  php artisan migrate:fresh --seed
  composer run lint:check + composer run types:check (before finishing PHP tasks)
  pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve --port=8000 &  # fix stale Blade (view:clear broken)
  lsof -i :8000 ; pg_isready
  python3 ~/.config/opencode/skills/webapp-testing/scripts/with_server.py --server "php artisan serve ..." --port 8000 --timeout 15 -- <cmd>
  ```
- **Test logins:** PW `Tarumt@2026` for all seeded users. Lecturers via `/login/staff` email, Students via `{yy}{PROG}{seq}@student.tarc.edu.my`, PLs 5425 Surayaini / 5516 Rahmat.

---

## 5. Important Decisions (Confirmed)

- **Architecture:** Frontend mock phase now; real DB wiring Sprint 1-3. Service classes `MatrixIntersectionEngine` + `OCCValidator` per NFR 5.3.
- **Color/OOP:** All colors via `theme.css` tokens; same label = same color everywhere; promote-on-3rd duplication to shared; `@extends` + `@include` partials; no copy-paste.
- **Mock data single source:** `public/js/mock-data.js` is only source; read-only; throwaway in Sprint 3.
- **Edge decisions (user-confirmed 2026-08-01):** Student counts fixed; timetable PDFs + self-modifications; original block becomes bookable after approval; PL Master Config Panel DROPPED; notifications FR 1.5/4.15/4.16 exact; MPU-3133 = exclude labs only; duration-aware windows (engine returns windows ≥ duration, grid 1h cells, click selects whole contiguous block).
- **Recent confirmed revert:** `83fd365` is correct baseline. Block model (`f3d2554`) was rejected — do not re-apply without request.
- **Week nav fix at `83fd365`:** `fix: timetable grid now loads on initial page load` — init order `populateWeekSelect → applyUrlParams → buildTimetable()`.
- **Prior related fixes (context for 83fd365):** `75eb947` utility classes refactor, `1919214` init order, `da3d534` venue URL param, `56e9c2c` fav star fix, `63b3e09` fav OOP, `efbf6be` Today persists, etc. (see `git log --oneline -20`).

---

## 6. Known Problems & Solutions

- **Stale Blade cache:** `php artisan view:clear` broken → use `pkill -9 php && rm -f storage/framework/views/*.php && php artisan serve ... &` (AGENTS.md + CodingMAIN §10).
- **Week nav / venue init race:** Fixed at `83fd365` by reordering init. Symptom: grid empty after arriving from venue timetable.
- **Favouriting star collision:** Fixed `56e9c2c` — name collision + null check `sumTotal`.
- **Branch divergence:** After `reset --hard 83fd365`, local `fjing` diverged from `origin/fjing` (local has `d2c494d,116cbee` not on remote; remote has `f316278` not local). **Solution:** `git pull --rebase` or `git push --force-with-lease` only if user asks — do not auto-push.
- **Untracked files (post-reset):** `FYP-BRIEFING.md`, `tests/ui-regression.spec.ts`, `page-changelogs/todo list/*plan.md` (4 plans), `CodingMAIN.md` + `todo-list.md` modified but not staged — need decision to stage/commit or discard.
- **Block model bug (resolved by revert):** `f3d2554` merged block painted over via `<td>` positioning, required search in `#timetable` not `tableBody` — moot after revert, but if block model ever retried, refer to `f3d2554` diff.

---

## 7. Current / Pending Tasks

> Tracker: `page-changelogs/todo list/todo-list.md` (6 tasks, all `pending`)

- **[TASK-001] high — Request Details Modal 2-tab for own pending** (`ui-common.js:openClassModal` + `CohortTimetable:387` + `MyTimetable:268`) — Tabs: Class Details vs Request Status; pass `isOwnPending` flag.
- **[TASK-002] medium — Copy icon on Subject Code** (`ui-common.js:DetailModal.row:753` + `theme.css:.copy-code-btn`) — covers 7 modals, clipboard + toast + `data-tip` tooltip.
- **[TASK-003] low — Title & subtitle utility classes** (`theme.css`) — 5 combos: `.section-heading`, `.card-title`, `.title-row`, `.overline`, `.kicker` (distinct from `.page-title` 22px).
- **[TASK-004] high — Staff ID optional P prefix** (`login-staff.blade.php` regex `^P?\d{4}$` + backend strip P) — see `staff-id-p-prefix-plan.md`
- **[TASK-005] high — Cancel Class mandatory reason** (`MyTimetable` modal + `theme.css:.cancel-reason-input`, ≥10 chars, counter) — see `cancel-class-reason-plan.md`
- **[TASK-006] high — Student Request History drop + Upcoming** (remove nav item from student template + Sprint 3 `upcomingReplacements` mock data) — see `student-request-history-drop-plan.md`

- **Immediate repo tasks:** Decide fate of `FYP-BRIEFING.md`, `tests/ui-regression.spec.ts`, 4 `*plan.md` files + modified `CodingMAIN.md`/`todo-list.md`; resolve `git status` divergence (2 vs 1 commits).

---

## 8. Writing Style Profile

> No long-form writing sample provided in this session; infer from code/docs style.

- **Code/comment style:** Concise, token-aware, OOP-minded, prefers `file:line` references, minimal prose.
- **Commit messages:** Conventional, imperative, short scope (`fix:`, `ui:`, `refactor:`), one line + optional body.
- **Docs (changelogs):** Structured, per-page, records promotions under "Promoted to shared".
- **User English:** Short fragments, lower-case casual when chatting (`wow , this aint...`), but formal when providing hash/paths. Wants AI output short/concise, not verbose.
- **For new AI:** Do NOT upgrade to native/professional English in academic docs unless explicitly asked — reproduce simple, clear B1-B2 with short sentences, bullet points, and canonical terms from CodingMAIN.

---

## 9. Important Constraints

- **Phase:** Frontend mock ONLY — no migrations/models/backend unless explicitly requested.
- **Reuse:** Flux/Livewire/Tailwind + `theme.css` + `ui-common.js` + `mock-data.js`; no new deps.
- **Colors:** Only `theme.css` custom-property tokens; never hardcode hex/rgb.
- **DRY:** Promote-on-3rd duplication; record in SDD `design.md`.
- **Icons:** Maximize icon buttons (`resources/views/flux/icon/`), minimize plain text; `title`/`aria-label` for a11y.
- **Modals:** Detail/secondary info in modals, not page surface.
- **Steps:** Fewest clicks; ~3 clicks max; pre-select defaults.
- **Confirm:** Destructive actions must have confirm popup; toast + undo at bottom-left 5s.
- **Responsive:** Mandatory mobile (≤768px) per CodingMAIN §10.0 rule 9 (drawer, cards, bottom sheet, 44px touch, clamp, safe-area-insets, etc.).
- **Git:** Branch `fjing`; never commit secrets; never push unless asked; conventional commits; update `page-changelogs/*.md` per UI change.
- **DB:** PostgreSQL `class_replacement` user `philler`; `migrate:fresh --seed` to reset; never deploy beyond localhost (PDPA, NFR 7.2).
- **Checks:** `composer run lint:check` + `types:check` before finishing PHP tasks; clear Blade cache after Blade changes.

---

## 10. Instructions for the New AI

1. **Read FIRST every session:** `CodingMAIN.md` in full, then `AGENTS.md` + `prompts/sdd-propose-ui-page.md` if doing UI work, then closest template + its `page-changelogs/*.md`, then `git status`/`git log --oneline -10`.
2. **Respect current baseline:** HEAD is `83fd365` per user confirmation. Do not re-apply `f3d2554` block model or `f316278` rename without explicit request.
3. **Preserve decisions:** Use `window.MockData` as single source, `theme.css` tokens, OOP partials; do not re-declare cohorts/lecturers/venues inline.
4. **Verify via execution:** Run commands, grep, start server with `pkill`+clear+serve, use `with_server.py` for Playwright; include `file:line` citations; base output on verified evidence, not assumptions.
5. **Be concise & factual:** Short answers, no fluff/praise/emojis, professional objectivity; disagree when technically needed.
6. **Confirm critical actions:** Show popup before delete/submit/cancel/approve/reject; offer toast + undo; keep flows ≤3 clicks.
7. **Handle divergence:** Do not auto `push --force` after reset — ask user. Use `git status` to check untracked/modified files before editing.
8. **Commit hygiene:** Conventional commits; update matching `page-changelogs/*.md` for UI changes; run lint/types checks for PHP.
9. **Follow SDD workflow:** For non-trivial features, use `.sdd/changes/<name>/` proposal→design→tasks→apply→verify→archive; record promotions.
10. **Ask, don't guess:** When hash/path/decision is ambiguous (e.g. which block model design), stop and ask for clarification rather than applying.

---

## 11. Unresolved / Needs Confirmation (updated 2026-08-21 — committed)

- **Resolved — committed:** `8dbef39` committed everything (was untracked/modified now clean). Includes `FYP-BRIEFING.md`, `tests/ui-regression.spec.ts`, 4 `todo list/*plan.md`, `CodingMAIN.md` + `todo-list.md` updates, and this knowledge file (renamed to `general_Session_2026-08-21.md` per request).
- **Branch divergence (still open):** Local `8dbef39` (3 ahead of `83fd365`) vs `origin/fjing` `f316278` (1 ahead of `83fd365`) — diverged 3 vs 1. Push only if user asks (`git push --force-with-lease` or `pull --rebase`).
- **Block model future:** User rejected `f3d2554` design — desired slot-selection UX still pending spec.

---

> **Handover ready.** New AI: read this + `CodingMAIN.md` + `AGENTS.md` and resume from §7 pending tasks.
