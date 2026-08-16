# Todo List

> General task tracker for the TARUMT Class Replacement System UI.
> Each item tracks: description, status, priority, affected files, and notes.

---

## Status Key

| Status | Meaning |
|--------|---------|
| `pending` | Not started |
| `in_progress` | Actively working on it |
| `blocked` | Waiting on dependency or decision |
| `completed` | Done and verified |
| `cancelled` | No longer needed |

---

## Priority Key

| Priority | Meaning |
|----------|---------|
| `high` | Blocking other work or user-facing bug |
| `medium` | Important but not urgent |
| `low` | Nice to have, cosmetic, or future improvement |

---

## Tasks

### [TASK-001] Request Details Modal (PENDING) — Readjust for Own Pending Events

- **Status:** `pending`
- **Priority:** `high`
- **Affected files:**
  - `public/js/ui-common.js` — `openClassModal()` (lines 564-640)
  - `resources/views/ui-design-templates/CohortTimetable-UI-design-template.blade.php` — `openModal()` (line 387)
  - `resources/views/ui-design-templates/MyTimetable-UI-design-template.blade.php` — `openModal()` (line 268)

**Description:**
When a user clicks their **own pending** event on CohortTimetable or MyTimetable, the Class Details modal should use a **2-tab layout** (mimicking the request-approval/request-history modal design). Non-own-pending events keep the current flat layout.

**Changes:**

1. **`openClassModal()` in `ui-common.js`:**
   - Accept new `cfg.isOwnPending` flag
   - When `isOwnPending === true`, render with tabs instead of flat rows:
     - **Tab 1: "Class Details"** — Subject Code, Subject Name, Class Type, Lecturer, Venue, Day, Date, Start Time, End Time, Duration (new), Cohort(s), Total Students
     - **Tab 2: "Request Status"** — Status badge, Status Description, Requested At, Replacement Date (cross-ref `MockData.requests`), Replacement Time (cross-ref), Replacement Venue (cross-ref)
   - Remove: Timeline, Remarks, Requested By
   - Footer: Close (left) | View Full Request (right)

2. **`CohortTimetable-UI-design-template.blade.php`:**
   - Detect own pending: `event.status === 'pending' && event.lecturer === MockData.currentUser.name && event.requestId`
   - Pass `isOwnPending` flag to `openClassModal`
   - Add `studentCount` to `extraFields` when available

3. **`MyTimetable-UI-design-template.blade.php`:**
   - Detect own pending: `event.status === 'pending' && event.requestId`
   - Pass `isOwnPending` flag to `openClassModal`

**OOP compliance:** All rendering logic stays in `openClassModal()` (shared). Pages only pass config flags. No duplication.

---

### [TASK-002] Copy Icon on Subject Code in All Modals

- **Status:** `pending`
- **Priority:** `medium`
- **Affected files:**
  - `public/js/ui-common.js` — `DetailModal.row()` (line 753)
  - `public/css/theme.css` — new `.copy-code-btn` styles

**Description:**
Add a copy icon with hover tooltip to the "Subject Code" row in all 7 modals that display course codes. One change in the shared `DetailModal.row()` method covers all modals. The code value in modals is already clean (e.g., `BMIT6767` without type suffix), so copying the value directly gives the clean code.

**Affected modals:**

| # | Page | Modal ID | Code var |
|---|------|----------|----------|
| 1 | My Timetable | `classModal` | `event.code` |
| 2 | Student My Timetable | `classModal` | `event.code` |
| 3 | Cohort Timetable | `eventModal` | `event.code` |
| 4 | Venue Timetable | `eventModal` | `e.code` |
| 5 | Replacement Home | `quickViewModal` | `c.code` |
| 6 | My Request History | `modalOverlay` | `r.courseCode` |
| 7 | Request Approval | `modalOverlay` | `r.courseCode` |

**Changes:**

1. **`public/js/ui-common.js` — `DetailModal.row()` (line 753):**
   - Detect when `label === 'Subject Code'`
   - Wrap value in a clickable span with a copy icon (`&#x2398;`)
   - `onclick` copies clean code (strips trailing `(T)`/`(L)`/`(P)` if present) to clipboard via `navigator.clipboard.writeText()`
   - Shows toast feedback via existing `copyToast` element
   - `data-tip="Click to copy"` — existing `initDataTipTooltips()` handles hover tooltip above the icon
   - `event.stopPropagation()` prevents modal interactions

2. **`public/css/theme.css` — new `.copy-code-btn` styles:**
   ```css
   .copy-code-btn {
       display: inline-flex;
       align-items: center;
       justify-content: center;
       width: 18px;
       height: 18px;
       margin-left: 6px;
       border-radius: var(--radius-xs);
       cursor: pointer;
       opacity: 0.4;
       font-size: 13px;
       vertical-align: middle;
       transition: opacity 0.15s, background 0.15s;
   }
   .copy-code-btn:hover {
       opacity: 1;
       background: var(--color-primary-container);
   }
   ```

3. **Changelogs** — Update all 7 page changelogs:
   - `my-timetable-changelog.md`
   - `student-my-timetable-ui-changelog.md`
   - `cohort-timetable-ui-changelog.md`
   - `venue-timetable-ui-changelog.md`
   - `replacement-home-changelog.md`
   - `my-request-history-changelog.md`
   - `request-approval-changelog.md`

**Verification:**
- Open each of the 7 modals — confirm copy icon appears next to subject code
- Click icon → code copied to clipboard (no type suffix), toast shows "Copied BMIT6767"
- Hover icon → "Click to copy" tooltip appears above
- Other `DetailModal.row()` calls (non-Subject-Code) unaffected

---

### [TASK-003] Title & Subtitle Utility Classes for theme.css

- **Status:** `pending`
- **Priority:** `low`
- **Affected files:**
  - `public/css/theme.css` — new utility classes

**Description:**
Add 5 useful and distinct title/subtitle CSS utility classes to `theme.css`. Each serves a different heading scenario and is visually distinct from existing classes.

**Classes to add:**

1. **`.section-heading` + `.section-heading-sub`** — Section heading with optional description beneath (16px/600 + 13px/variant)
2. **`.card-title`** — Compact card/panel heading (13px/600)
3. **`.title-row` + `.title-row-title` + `.title-row-sub`** — Flex row layout, title + subtitle side-by-side baseline-aligned (15px/600 + 13px/variant)
4. **`.overline`** — Tiny uppercase eyebrow label above a title (10px/700/primary/uppercase/letter-spacing)
5. **`.kicker` + `.kicker-badge`** — Title with inline badge/tag (15px/600 + 11px badge with primary-container bg)

**Visual distinction from existing classes:**

| Class | Size | Key trait |
|---|---|---|
| `.page-title` (existing) | 22px | Large bold page h1 |
| `.section-heading` | 16px | Medium section title + sub |
| `.card-title` | 13px | Compact card/panel heading |
| `.title-row` | 15px + 13px | Flex row — title + sub side-by-side |
| `.overline` | 10px | Uppercase eyebrow above a title |
| `.kicker` | 15px | Title + inline badge |
| `.title-subtitle` (existing) | 13px | Small bold label |

All use existing `theme.css` tokens. No new variables needed.

---

## Template: New Task

```markdown
### [TASK-XXX] Title

- **Status:** `pending`
- **Priority:** `medium`
- **Affected files:**
  - `path/to/file.ext` — description

**Description:**
What needs to be done and why.

**Changes:**
1. Step-by-step changes
2. ...

**Notes:**
Any additional context, decisions, or blockers.
```
