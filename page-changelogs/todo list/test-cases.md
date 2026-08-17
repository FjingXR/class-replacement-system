# Test Cases — Class Replacement System

> Manual + automated test cases for AI agents (DeepSeek, Playwright) to validate the UI.
> Each test has: ID, preconditions, steps, expected result, and priority.

---

## Status Key

| Status | Meaning |
|--------|---------|
| `pending` | Not yet executed |
| `pass` | Passed |
| `fail` | Failed — bug found |
| `skip` | Skipped (not applicable) |

---

## Section 1: Page Load & Initialization

### TC-1.1 Replacement Arrangement — Grid loads on initial visit

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** Fresh session, no localStorage state
- **Steps:**
  1. Navigate to the URL above
  2. Wait for page to fully load (networkidle)
  3. Check if timetable grid has cells rendered
- **Expected:** Grid has >0 `.cell-content` elements, `#tableHead` and `#tableBody` have content
- **Automated check:** `document.querySelectorAll('.timetable .cell-content').length > 0`

### TC-1.2 Replacement Arrangement — Grid loads without URL params

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-arrangement`
- **Preconditions:** Fresh session
- **Steps:**
  1. Navigate to `/replacement-arrangement` (no params)
  2. Wait for page load
  3. Check grid has cells
- **Expected:** Grid renders with default venue (B103) and current week

### TC-1.3 All pages — No JS errors on load

- **Status:** `pending`
- **Priority:** `high`
- **URLs:** All 8 pages
- **Preconditions:** Fresh session
- **Steps:**
  1. Navigate to each page
  2. Listen for `pageerror` events
  3. Collect all errors
- **Expected:** No `pageerror` events (except the pre-existing `Cannot set properties of null` on replacement-arrangement which is known)
- **Automated check:** `page.on("pageerror", ...)` should not capture new errors

### TC-1.4 Replacement Arrangement — Duration param caps selection

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=3&from=replacement-home`
- **Preconditions:** None
- **Steps:**
  1. Navigate to URL with `duration=3`
  2. Check progress text shows "Selected 0 of 6 slots"
  3. Click 6 available cells
  4. Try to click a 7th cell
- **Expected:** Progress caps at "Selected 6 of 6 slots", 7th click is blocked

---

## Section 2: Week Navigation

### TC-2.1 My Timetable — Today button persists

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/my-timetable-ui`
- **Preconditions:** Fresh session
- **Steps:**
  1. Navigate to page
  2. Click next arrow to go to week 3
  3. Click "Today" button
  4. Reload page
  5. Check `weekNav.currentWeek`
- **Expected:** After Today click, `weekNav.currentWeek` equals today's week index. After reload, same value.

### TC-2.2 Cohort Timetable — Today button persists

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/cohort-timetable-ui`
- **Preconditions:** Fresh session
- **Steps:**
  1. Navigate to page
  2. Select a faculty and cohort
  3. Click next arrow to week 5
  4. Click "Today" button
  5. Check `localStorage.getItem('cohortTimetableState')` — should have `week` = today's index
  6. Reload page
  7. Verify week is restored to today
- **Expected:** `cohortTimetableState.week` matches today's index after reload

### TC-2.3 Venue Timetable — Today button persists

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/venue-timetable-ui`
- **Preconditions:** Fresh session
- **Steps:**
  1. Navigate to page
  2. Click next arrow to week 5
  3. Click "Today" button
  4. Check `localStorage.getItem('currentWeek')`
  5. Reload page
  6. Verify week is restored
- **Expected:** `currentWeek` in localStorage matches today's index

### TC-2.4 Student My Timetable — Today button persists

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/student-my-timetable-ui`
- **Preconditions:** Fresh session
- **Steps:**
  1. Navigate to page
  2. Click next arrow to week 5
  3. Click "Today" button
  4. Check `localStorage.getItem('currentWeek')`
  5. Reload page
  6. Verify week is restored
- **Expected:** `currentWeek` in localStorage matches today's index

### TC-2.5 Replacement Arrangement — Week selector respects saved week

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/replacement-arrangement`
- **Preconditions:** Set `localStorage.setItem('currentWeek', '5')` before navigating
- **Steps:**
  1. Navigate to page
  2. Check `weekNav.currentWeek`
  3. Check `<select id="weekSelector">` value
- **Expected:** Both show week index 5

---

## Section 3: Modal & Detail Views

### TC-3.1 My Timetable — Class detail modal opens on event click

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/my-timetable-ui`
- **Preconditions:** Page loaded, week has events
- **Steps:**
  1. Click an event block on the timetable
  2. Check if `#classModal` is visible
  3. Check modal body has "Subject Code" row
- **Expected:** Modal opens, shows course code (e.g., "BMIT6767"), subject name, lecturer, etc.

### TC-3.2 Cohort Timetable — Modal shows cohort info

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/cohort-timetable-ui`
- **Preconditions:** Faculty and cohort selected, events visible
- **Steps:**
  1. Click an event block
  2. Check modal has "Cohort" row
- **Expected:** Modal shows cohort name in the detail rows

### TC-3.3 Venue Timetable — Modal shows venue details

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/venue-timetable-ui`
- **Preconditions:** Venue selected, events visible
- **Steps:**
  1. Click an event block
  2. Check modal has "Venue" row with venue info
- **Expected:** Modal shows venue code, type, and capacity

### TC-3.4 Replacement Home — Quick view modal opens

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-home-ui`
- **Preconditions:** Conflict cards visible
- **Steps:**
  1. Click a replacement card
  2. Check if `#quickViewModal` is visible
  3. Check modal has "Subject Code" row
- **Expected:** Modal opens with course code, status, conflict reason, venue, time

### TC-3.5 Request Approval — Detail modal opens

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/request-approval-ui`
- **Preconditions:** Pending requests visible
- **Steps:**
  1. Click a request row or "View" button
  2. Check `#modalOverlay` is visible
  3. Check modal has "Subject Code" row
- **Expected:** Modal opens with full request details including tabs

### TC-3.6 My Request History — Detail modal opens

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/my-request-history-ui`
- **Preconditions:** Request history exists
- **Steps:**
  1. Click a request row
  2. Check `#modalOverlay` is visible
  3. Check modal has "Subject Code" row
- **Expected:** Modal opens with request details, timeline, tabs

---

## Section 4: Replacement Flow (End-to-End)

### TC-4.1 Select venue slot and verify summary updates

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** Grid loaded
- **Steps:**
  1. Click an available (green) cell
  2. Check progress text updates to "Selected 1 of 4 slots"
  3. Check summary grid shows a card with venue and time info
  4. Click the same cell again to deselect
  5. Check progress returns to "Selected 0 of 4 slots"
- **Expected:** Selection toggles correctly, summary updates in real-time

### TC-4.2 Maximum selection enforcement

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** Grid loaded
- **Steps:**
  1. Click 4 different available cells
  2. Check progress shows "Selected 4 of 4 slots"
  3. Try clicking a 5th available cell
  4. Check it does not get selected
- **Expected:** Selection capped at MAX_SELECTION (4 for 2hr class)

### TC-4.3 Clear All resets selection

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** Some cells selected
- **Steps:**
  1. Select 2 available cells
  2. Click "Clear ALL" button
  3. Check progress shows "Selected 0 of 4 slots"
  4. Check all previously selected cells are back to available (green)
- **Expected:** All selections cleared, cells return to available state

### TC-4.4 Submit request — confirmation modal appears

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** At least 1 slot selected
- **Steps:**
  1. Select 1 available cell
  2. Click "Submit Request" button
  3. Check confirmation modal appears
  4. Check modal shows the selected venue, time, and slot count
- **Expected:** Confirmation modal with correct details, Confirm/Cancel buttons

### TC-4.5 Submit request — confirm sends request

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** Confirmation modal open
- **Steps:**
  1. Click "Confirm" in the confirmation modal
  2. Check success toast/message appears
  3. Check the selected cell changes to "pending" state (yellow)
- **Expected:** Request submitted, cell state updated to pending

---

## Section 5: Request Approval Flow

### TC-5.1 Approve request — confirmation popup

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/request-approval-ui`
- **Preconditions:** Pending requests visible
- **Steps:**
  1. Click "Approve" button on a pending request
  2. Check confirmation popup appears
  3. Check popup shows request details
- **Expected:** Confirmation popup with "Are you sure?" message

### TC-5.2 Reject request — requires reason

- **Status:** `pending`
- **Priority:** `high`
- **URL:** `/request-approval-ui`
- **Preconditions:** Pending requests visible
- **Steps:**
  1. Click "Reject" button on a pending request
  2. Check reject modal opens
  3. Check textarea for rejection reason is present
  4. Try to confirm without entering a reason
  5. Check validation error or button is disabled
- **Expected:** Reject modal requires a reason before confirming

### TC-5.3 Bulk select — select multiple requests

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/request-approval-ui`
- **Preconditions:** Multiple pending requests visible
- **Steps:**
  1. Click checkboxes on 3 different requests
  2. Check bulk action bar appears
  3. Check count shows "3 selected"
- **Expected:** Bulk selection works, action bar visible

---

## Section 6: Request History

### TC-6.1 My Request History — Status badges correct

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/my-request-history-ui`
- **Preconditions:** Requests with different statuses exist
- **Steps:**
  1. Check that "Approved" requests have green badge
  2. Check that "Rejected" requests have red badge
  3. Check that "Pending" requests have yellow badge
- **Expected:** Status badges use correct colors per status

### TC-6.2 Batch cancel — confirmation shows correct count

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/my-request-history-ui`
- **Preconditions:** Multiple pending requests
- **Steps:**
  1. Select 3 pending requests
  2. Click "Cancel Selected"
  3. Check confirmation modal shows "Cancel 3 selected request(s)?"
- **Expected:** Correct count in confirmation message

---

## Section 7: Keyboard Navigation

### TC-7.1 Replacement Arrangement — Arrow keys navigate time slots

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** Grid loaded, no input focused
- **Steps:**
  1. Press arrow keys (up/down/left/right)
  2. Check cell highlight moves
  3. Press Enter on an available cell
  4. Check cell gets selected
- **Expected:** Keyboard navigation moves focus, Enter selects

### TC-7.2 Replacement Arrangement — ? opens shortcuts modal

- **Status:** `pending`
- **Priority:** `low`
- **URL:** `/replacement-arrangement`
- **Preconditions:** No input focused
- **Steps:**
  1. Press `?` key
  2. Check keyboard shortcuts modal appears
  3. Press Escape
  4. Check modal closes
- **Expected:** Shortcuts modal toggles with `?` and closes with Escape

---

## Section 8: Edge Cases & Error Handling

### TC-8.1 Replacement Arrangement — No matching venue for student count

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`
- **Preconditions:** Course has 30 students
- **Steps:**
  1. Check venue dropdown only shows venues with capacity >= 30
  2. Check "Showing venues that fit 30 students" hint is visible
- **Expected:** Venues filtered by student count

### TC-8.2 Replacement Arrangement — Public holiday slots blocked

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/replacement-arrangement`
- **Preconditions:** Week contains a public holiday
- **Steps:**
  1. Check that cells on public holiday have red/error styling
  2. Click a public holiday cell
  3. Check it does not get selected
- **Expected:** Public holiday cells are visually distinct and not selectable

### TC-8.3 Replacement Arrangement — Sunday slots blocked

- **Status:** `pending`
- **Priority:** `medium`
- **URL:** `/replacement-arrangement`
- **Preconditions:** Week contains a Sunday column
- **Steps:**
  1. Check Sunday column cells have distinct styling
  2. Click a Sunday cell
  3. Check it does not get selected
- **Expected:** Sunday cells are not selectable

### TC-8.4 Modal closes on overlay click

- **Status:** `pending`
- **Priority:** `medium`
- **URLs:** Any page with a modal
- **Preconditions:** Modal is open
- **Steps:**
  1. Click the dark overlay area outside the modal
  2. Check modal closes
- **Expected:** Modal closes on overlay click (outside-in dismiss)

### TC-8.5 Modal closes on Escape key

- **Status:** `pending`
- **Priority:** `medium`
- **URLs:** Any page with a modal
- **Preconditions:** Modal is open
- **Steps:**
  1. Press Escape key
  2. Check modal closes
- **Expected:** Modal closes on Escape

---

## Section 9: Responsive / Dark Mode

### TC-9.1 Dark mode toggle works

- **Status:** `pending`
- **Priority:** `low`
- **URLs:** Any page
- **Preconditions:** None
- **Steps:**
  1. Click theme toggle button
  2. Check `data-theme` attribute changes on `<html>`
  3. Check colors update (background becomes dark, text becomes light)
- **Expected:** Theme switches between light and dark

### TC-9.2 Mobile viewport — timetable scrolls horizontally

- **Status:** `pending`
- **Priority:** `low`
- **URLs:** Any timetable page
- **Preconditions:** Set viewport to 375px width
- **Steps:**
  1. Navigate to timetable page
  2. Check timetable container is scrollable
  3. Scroll horizontally to see all days
- **Expected:** Timetable is scrollable on mobile viewport

---

## Section 10: Data Integrity

### TC-10.1 Mock data consistency — all courses have required fields

- **Status:** `pending`
- **Priority:** `medium`
- **URLs:** Any page
- **Steps:**
  1. In browser console, check `MockData.courses` array
  2. Each course should have: `code`, `name`, `type`, `cohorts`, `studentCount`
  3. Check `MockData.lecturers` array — each should have: `name`, `staffId`, `email`
  4. Check `MockData.venues` array — each should have: `code`, `type`, `capacity`
- **Expected:** All required fields present and non-empty

### TC-10.2 Week data consistency — all weeks have 7 days

- **Status:** `pending`
- **Priority:** `medium`
- **Steps:**
  1. Check `window.MockData.semester.weeks` array
  2. Each week should have exactly 7 days
  3. Each day should have: `date`, `label`, `abbr`
- **Expected:** All weeks have 7 complete day objects

---

## How to Use

### For Playwright automation:
```python
# Example: Run TC-1.1
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page()
    page.goto("http://localhost:8000/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home", wait_until="networkidle")
    cells = page.evaluate("() => document.querySelectorAll('.timetable .cell-content').length")
    assert cells > 0, f"TC-1.1 FAIL: Expected >0 cells, got {cells}"
    print("TC-1.1 PASS")
    browser.close()
```

### For DeepSeek / AI review:
Each test case can be given to an AI agent as a prompt:
> "Review the code for [TC-X.X description]. Check if [expected behavior] is correctly implemented. Look for edge cases, missing error handling, or logic bugs."
