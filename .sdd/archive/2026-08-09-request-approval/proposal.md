# Proposal: Request Approval Page (PL Side)

## Why This Change Is Needed

The current system allows lecturers to submit and track replacement requests via the "My Request History" page, but Program Leaders (PL) have no dedicated interface to review, approve, or reject them. The PL must currently rely on ad-hoc communication (email, WhatsApp) to handle approval workflows, leading to delays, lost requests, and no audit trail. This change adds a Request Approval page that displays all replacement requests with a default "Pending" status filter, urgency indicators (requests within 3 days of the class date flagged as "Urgent"), quick Approve/Reject action buttons inline in the table, and a full detail modal with remarks support. Beyond the core review workflow, the page includes 17 PL-efficiency features: bulk approve/reject with per-row checkboxes and a batch action bar, enhanced approve/reject confirm dialogs with request summaries, reject reason presets (clickable chips), an urgency filter, request age indicators ("X days ago"), approval notes, a pending count badge on the nav bar, a "viewed" indicator for rows already opened in the modal, keyboard shortcuts for rapid navigation (Arrow/Enter/A/R/Escape), review-next auto-advance after approve/reject, slot validity preview icons (✓/⚠/?) in the Proposed Replacement column, toast notifications replacing browser alerts, undo stack (3-5 sec toast with Undo button), animated transitions for state changes, smart grouping by course or lecturer, mini request lifecycle timeline in the detail modal, and skeleton loading placeholders. The page is built on the codebase's OOP architecture — **inheritance** (Blade layout via `@extends`), **composition** (Blade partials via `@include`), and **shared modules** (`theme.css`, `ui-common.js`) — and promotes duplicated helpers into the shared JS module so the codebase has a single source of truth instead of copy-pasted page-local copies.

## FR Traceability

This page implements the frontend of the Programme Leader approval workflow (Chapter 3 FR 3.x). FR→page mapping (design-phase scope: frontend only; backend integration deferred):

| FR | Requirement (abbrev.) | Page feature |
|----|-----------------------|--------------|
| 3.2 | Time-based queue of pending requests | Default "Pending" filter + default sort by `requestedAt` asc (FIFO), week filter, pagination |
| 3.3 | Queue shows proposer, subject, cohort(s), proposed time/venue | Lecturer column, Course Code & Name column, Original/Replacement class blocks (cohorts + venue in detail modal) |
| 3.4 | Pre-computed slot validity | Slot Validity field in modal Section 3 (mock `slotValidity`, later from FR 4.3 engine) |
| 3.5 | Approve with one click | Inline Approve button + modal footer Approve (confirm → alert, no state change in design phase) |
| 3.6 | Reject with mandatory reason | Rejection Reason modal (textarea, Confirm disabled until non-empty) |
| 3.7 | Audit trail (identity, timestamp, action, reason) | Reviewed By / Reviewed At in modal (backend audit log deferred) |
| 2.8 | Lecturer submits request for PL approval | Receiving end of the submission workflow (data shown in this page) |
| 4.15 | Email PL when request submitted | Feeds this queue (backend, deferred) |

Non-functional: NFR 1.3 (load < 2s — client-side mock data), NFR 3.1 (responsive via theme.css breakpoints), NFR 3.3 (simple English messages in empty states/alerts). Backend NFRs (5.x, 7.x) apply to later phases.

## Scope

### In Scope

**1. Create a new Blade template: `request-approval-UI-design-template.blade.php`**

Extends `layouts/ui-template` with `$activeNav = 'request-approval'`. The page includes:

- **Page Header**: Title "Request Approval" + description "Review and manage replacement requests from lecturers in your program."
- **Toolbar**: Search input (by course code, course name, or lecturer name), status filter dropdown (All, Pending, Approved, Rejected, Cancelled, Completed — defaulting to "Pending"), week filter dropdown (dynamic from `weekRanges`), and a "Reset Filters" button. Result count on the right.
- **Sort Hint**: "Click column headers to sort (Requested Timestamp, Original Class, Proposed Replacement, Urgency, Status)" — lists sortable columns by name, matching my-request-history pattern.
- **Data Table**: 10 columns (see Section 2 below) with client-side sorting, pagination (10 per page), row hover, and zebra striping
- **Pagination Bar**: Standard prev/next + numbered pages
- **Summary Cards**: 4 cards via `@include('partials.ui-summary-bar', ['cards' => [...]])` — Pending (amber, `card-pending`/`summaryPending`), Approved (green, `card-approved`/`summaryApproved`), Rejected (red, `card-rejected`/`summaryRejected`), Total Reviewed (blue, `card-total`/`summaryReviewed`) — counts from all mock data (unfiltered). "Total Reviewed" = count of entries where status is Approved, Rejected, or Completed (i.e. all requests that have been acted on by the PL, excluding Pending and Cancelled).
- **Empty State**: Two variants — fully empty (no requests at all) and filtered empty (no results match filters)
- **Detail Modal**: Full request details with 3 sections + Approve/Reject buttons in footer for Pending requests
- **Approve/Reject Confirmation**: Browser `confirm()` dialog on approve/reject click; no in-memory state update (frontend design phase only)

**2. Table Columns (10 columns)**

| # | Column Header | CSS Class | Width | Sortable | Sort Field | Notes |
|---|---------------|-----------|-------|----------|------------|-------|
| 1 | `#` | `.col-no` | 50px | No | — | Row number (page-relative) |
| 2 | Requested Timestamp | `.col-requested-at` | 145px | Yes | `requestedAt` | Exact submission time formatted as "30 Aug 2026, 10:30 AM" |
| 3 | Lecturer | `.col-lecturer` | 130px | No | — | Lecturer name (new field) |
| 4 | Course Code & Name | `.col-code` | 200px | No | — | Two-line: code (bold) + name |
| 5 | Original Class | `.col-original` | 170px | Yes | `classDate` | Multi-line: day + date + time + duration |
| 6 | Proposed Replacement | `.col-replacement` | 170px | Yes | `replacementDate` | Multi-line: date + time, color-coded by status |
| 7 | Students | `.col-students` | 70px | No | — | Total affected students |
| 8 | Urgency | `.col-urgency` | 90px | Yes | `urgencyDays` | Badge: "Urgent" (≤3 days, red) or "Normal" (>3 days, green). Note: urgency sort produces similar ordering to Original Class sort by `classDate` (both are monotonic functions of class date), but urgency sort groups Urgent entries first regardless of exact date — useful for PL to see urgent items at a glance. |
| 9 | Status | `.col-status` | 130px | Yes | `status` | Status badge (clickable → opens modal). PL benefits from status sorting to group Pending requests together. |
| 10 | Actions | `.col-actions` | 140px | No | — | Approve/Reject buttons for Pending; "View" button for others |

**Sort profile rationale**: Course Code & Name and Lecturer are un-sortable (per user requirement — the PL typically searches by these rather than sorting). Proposed Replacement is sortable (per user requirement — PL may want to see replacement dates in order). Status is sortable (new — PL may want to group Pending requests together). These deviate from my-request-history's sort profile to suit the PL's review workflow.

**3. Urgency Logic**

Urgency is computed relative to a **fixed demo reference date** of `2026-08-29` (the Friday before Week 1 starts on 2026-08-31). This ensures urgency badges are deterministic and visible in the mock data — if urgency used real `new Date()`, every mock entry (dated Aug/Sep) would compute as "Normal" when viewed in July, defeating the feature.

```javascript
const URGENCY_REFERENCE_DATE = new Date('2026-08-29T00:00:00');

function urgencyLevel(classDate) {
    const target = new Date(classDate + 'T00:00:00');
    const diffDays = Math.ceil((target - URGENCY_REFERENCE_DATE) / (1000 * 60 * 60 * 24));
    return diffDays <= 3 ? 'urgent' : 'normal';
}
```

`urgencyDays` (used as the sort field for column 8) is a **derived/computed value**, not a stored mock-data field. It is computed at sort time as `Math.ceil((target - URGENCY_REFERENCE_DATE) / (1000 * 60 * 60 * 24))`.

- Urgent badge: `--color-error-container` background, `--color-on-error-container` text
- Normal badge: `--color-secondary-container` background, `--color-on-secondary-container` text

**4. In-Table Actions**

For **Pending** rows, two buttons side-by-side:
- **Approve** (`✓ Approve`): Green outline button using `--color-secondary` border; hover fills green. Calls `approveRequest(id)` → browser `confirm()` → `alert()` on confirm.
- **Reject** (`✕ Reject`): Red outline button using `--color-error` border; hover fills red. Calls `openRejectModal(id)` → opens the Rejection Reason modal (FR 3.6) → user enters the mandatory reason → Confirm Reject calls `rejectRequest()` → `confirm()` → `alert()`.

For **non-Pending** rows:
- **View**: Outline button. Opens the detail modal via `openModal(index)`.

Note: For Pending rows, only the Approve/Reject buttons are shown (no "View" button). For non-Pending rows, only the "View" button is shown. The Status badge in column 9 is clickable for ALL rows (opens modal). This gives two entry points to the modal for non-Pending rows (badge + View button) and one entry point for Pending rows (badge only, plus Approve/Reject for quick action).

**4.1 Rejection Reason Modal (FR 3.6 — mandatory reason)**

Satisfies FR 3.6: "Programme Leaders shall be able to reject a request by providing a mandatory reason."

Flow: PL clicks Reject → a small modal opens with a `#rejectReasonInput` textarea (placeholder "Please provide a reason for rejection..."). The "Confirm Reject" button is **disabled until the textarea has non-whitespace content** (mandatory reason). On valid input: `confirm()` dialog → `alert()` on confirm (no state change — frontend design phase). The reason typed is not persisted (backend phase later), but the modal's Rejection Reason field demonstrates where it will appear.

**5. Detail Modal**

Same 3-section structure as my-request-history:

- **Section 1 — "Request Information"**: Request No., Requested At, Status (badge + description), Rejection Reason (conditionally shown for `status === 'Rejected'`), Lecturer, Course Code, Course Name, Class Type. (Renamed from my-request-history's "General Info" to reflect the PL's review context. Rejection Reason field preserved from my-request-history lines 788-790.)
- **Section 2 — "Original Class Detail"**: Affected Cohort(s), Total Students (with breakdown if multi-cohort), Original Date, Original Day, Original Time, Duration, Original Venue
- **Section 3 — "Requested Replacement Class"**: Replacement Date, Replacement Time, Replacement Venue, **Slot Validity (FR 3.4)**, Reviewed By, Reviewed At

**5.1 Slot Validity (FR 3.4)**

Satisfies FR 3.4: "Each request shall display pre-computed slot validity." In the design phase the validity is a mock data field (`slotValidity: 'valid' | 'conflict'`); in the backend phase it will come from the matrix intersection computation (FR 4.3).

Rendered as a line in modal Section 3 (replacing nothing — added as new field after Replacement Venue):
- `Valid` → green text with ✓ prefix (`.slot-valid`)
- `Conflict` → red text with ⚠ prefix + conflict hint (`.slot-conflict`)

Mock data: ~4-5 of the 20 entries have `slotValidity: 'conflict'` (e.g. "Room already occupied"), the rest `'valid'` — so the feature is visible without overwhelming the page.

**Modal Footer**:
- Pending: Reject button (left, red) + Approve button (right, green). Approve triggers `confirm()` → `alert()`; Reject opens the Rejection Reason modal (4.1).
- Non-Pending: Close button (right, outline).

**6. Mock Data (20 entries)**

The mock data lives in a **new shared module `public/js/mock-data.js`** (data separated from logic — encapsulation). It defines two globals consumed by the page:

- `MockData.approvalRequests` — the 20 request entries
- `URGENCY_REFERENCE_DATE` — the fixed demo reference date (`2026-08-29T00:00:00`)

The layout loads `mock-data.js` via `<script src="/js/mock-data.js"></script>` right after `ui-common.js` (both before the page's inline script). The page script references the globals directly — it does NOT redeclare them. No other page's inline mock data is touched (my-request-history keeps its own distinct inline dataset; migrating other pages' data into `mock-data.js` is future work).

Entry structure: same as my-request-history mock data, but:
- Add `lecturer` field (string, e.g. "Kylian Mbappe")
- Add `slotValidity` field (string: `'valid'` or `'conflict'`; ~4-5 entries 'conflict' with a `conflictReason` string, e.g. "Room C202 already occupied") — consumed by Slot Validity (FR 3.4)
- Data must be **distinct** from my-request-history — different lecturers (5+), different course codes (8+), different cohorts (6+), different venues (6+), varied durations (1–3 hours), varied class dates spanning Weeks 1–4 (31 Aug – 27 Sep 2026). Include a mix of urgency levels: some entries with `classDate` within 3 days of the reference date `2026-08-29` — use class dates **2026-08-31 and 2026-09-01** (within 3 days of 08-29 → "Urgent", AND inside Week 1 so they don't vanish under the week filter; 08-29/08-30 fall outside all weekRanges and must NOT be used for urgent entries) — and the rest with class dates later in the semester (these will show as "Normal").

Status distribution: 8 Pending, 5 Approved, 4 Rejected, 2 Completed, 1 Cancelled.

**6.1 Default Sort (FR 3.2 — time-based queue)**

Satisfies FR 3.2: "Programme Leaders shall be able to view a time-based queue of all pending replacement requests." Default `sortState = { field: 'requestedAt', dir: 'asc' }` — pending requests appear oldest-first (FIFO review order), matching the queue concept. Users can still re-sort via column headers.

**7. PL Efficiency Features (8 additions)**

These features improve the PL's review speed and accuracy. All are frontend-only in the mock phase (confirm/alert, no state change). Each is independently testable.

**7.1 Bulk Approve/Reject (Medium effort)**

Adds a checkbox column as column 1 (shifting the original `#` column — see §2 table update below). Per-row checkboxes appear on **Pending rows only**; non-Pending rows have no checkbox. A "Select All" checkbox in the `<thead>` toggles all visible Pending row checkboxes. When ≥1 checkbox is checked, a **batch action bar** appears fixed above the table: "3 selected" + "Approve Selected" (green) + "Reject Selected" (red). Clicking "Approve Selected" shows `confirm()` listing the selected IDs + a one-line summary per request, then `alert()` on confirm. "Reject Selected" opens the Rejection Reason modal (§4.1) — the reason applies to all selected items. No in-memory state update (design phase).

**7.2 Enhanced Approve/Reject Confirm Summary (Small effort)**

The existing bare `confirm("Approve this request?")` is enhanced to include a multi-line summary: request ID, course code + name, lecturer, original class date/time, replacement date/time, venue. Example:
```
Approve request #1?
BMIT2201 — Data Structures & Algorithms
Lecturer: Kylian Mbappe
Original: Mon 31 Aug 09:00–11:00
Replacement: Wed 02 Sep 09:00–11:00
Venue: C201
```
Prevents accidental approve/reject of the wrong row. Applied to both single Approve (§4) and bulk Approve Selected (§7.1).

**7.3 Reject Reason Presets (Small effort)**

Above the existing `#rejectReasonInput` textarea in `#rejectReasonModal` (§4.1), add 5 clickable chips: "Venue unavailable", "Insufficient notice", "Slot conflict", "Lecturer unavailable", "Other". Clicking a chip fills the textarea with that text and enables the Confirm Reject button. "Other" clears the textarea for free-text entry. Chips are styled as small outline buttons (`.reject-preset-chip`); the active chip gets a filled background. This standardises rejection reasons for the audit trail (FR 3.7) and reduces typing.

**7.4 Urgency Filter (Small effort)**

Add a chip/button group in the toolbar between the status filter and the week filter: three buttons — "All" (default), "Urgent", "Normal". Clicking "Urgent" shows only rows where `urgencyLevel(r.classDate) === 'urgent'`; "Normal" shows only normal; "All" shows both. The filter combines with the existing status filter (AND logic). Resets to "All" when "Reset Filters" is clicked. Styled as `.urgency-filter-chip` buttons matching the toolbar's visual language.

**7.5 Request Age Sub-label (Small effort)**

Under the Requested Timestamp in column 2, render a small sub-label: "X days ago" (computed from `requestedAt` vs current date). Colour-coded: green dot (≤1 day), amber dot (2–3 days), red dot (>3 days since submission). Helps PL spot stale requests at a glance. Purely computed from `requestedAt` — no mock data changes needed. CSS: `.request-age` with `.age-fresh` (green), `.age-waiting` (amber), `.age-stale` (red).

**7.6 Approval Notes (Medium effort)**

A small modal `#approveNotesModal` with an optional "Notes" textarea and an Approve button. When PL clicks the Approve button (single or bulk), instead of a bare `confirm()`, this modal opens showing a summary of the request(s) + the textarea. PL can optionally type a note (e.g., "Use Room C201 instead"). Clicking Approve in this modal shows `confirm()` including the notes text, then `alert()`. If the notes textarea is left empty, proceeds without notes. Notes are not persisted (design phase) but the modal demonstrates where backend audit notes will appear.

**7.7 Pending Count Badge on Nav Bar (Small effort)**

The "Request Approval" nav link in `ui-nav-bar.blade.php` shows a small red badge with the pending count (e.g. `8`). In the mock phase, the badge is rendered by the page script after counting `MockData.approvalRequests.filter(r => r.status === 'Pending').length`. Backend phase: dynamic from `PendingRequest::count()`. Styled as `.nav-badge` — small red circle with white text, positioned after the nav link text.

**7.8 Viewed Indicator (Small effort)**

A JS `Set` (`viewedIds`) tracks which request IDs have been opened in the detail modal. When `openModal(index)` is called, the request's ID is added to `viewedIds` and the row gets a subtle CSS class (`.row-viewed` — faint left-border accent or background tint). The indicator persists for the session (resets on page reload). In the backend phase, this maps to a `viewed_at` timestamp per PL per request. Helps PL avoid reviewing the same request twice.

**7a. Table Column Update (for §2)**

Adding checkbox column 1 shifts the numbering. Updated column spec:

| # | Column Header | CSS Class | Width | Sortable | Sort Field | Notes |
|---|---------------|-----------|-------|----------|------------|-------|
| 0 | ☐ (Select) | `.col-checkbox` | 35px | No | — | "Select All" checkbox in `<thead>`; per-row checkboxes on Pending rows only; non-Pending rows render empty cell |
| 1 | `#` | `.col-no` | 50px | No | — | Row number (page-relative) |
| 2 | Requested Timestamp | `.col-requested-at` | 145px | Yes | `requestedAt` | + request age sub-label (§7.5) |
| 3 | Lecturer | `.col-lecturer` | 130px | No | — | Lecturer name |
| 4 | Course Code & Name | `.col-code` | 200px | No | — | Two-line: code (bold) + name |
| 5 | Original Class | `.col-original` | 170px | Yes | `classDate` | Multi-line: day + date + time + duration |
| 6 | Proposed Replacement | `.col-replacement` | 170px | Yes | `replacementDate` | Multi-line: date + time, color-coded by status |
| 7 | Students | `.col-students` | 70px | No | — | Total affected students |
| 8 | Urgency | `.col-urgency` | 90px | Yes | `urgencyDays` | Badge: "Urgent" (≤3 days, red) or "Normal" (>3 days, green) |
| 9 | Status | `.col-status` | 130px | Yes | `status` | Status badge (clickable → opens modal) |
| 10 | Actions | `.col-actions` | 140px | No | — | Approve/Reject buttons for Pending; "View" button for others |

Table `min-width` updates to **1335px** (was 1300px; +35px for checkbox column).

**7b. Batch Action Bar HTML**

Appears fixed above the table when ≥1 checkbox is checked. Hidden by default (`display:none`). Contains: selected count text + "Approve Selected" button (green outline, same style as `.btn-approve`) + "Reject Selected" button (red outline, same style as `.btn-reject`). "Approve Selected" calls `bulkApprove()` → confirm summary (§7.2) → alert. "Reject Selected" calls `openRejectModal()` in bulk mode (the reason applies to all selected items). Both clear checkboxes after action.

**7c. Approval Notes Modal HTML**

```html
<div class="modal-overlay" id="approveNotesModal">
    <div class="modal">
        <div class="modal-header"><h3 class="modal-title">Approve Request</h3>
            <button class="modal-close" onclick="closeApproveNotesModal()">&times;</button></div>
        <div class="modal-body">
            <div id="approveNotesSummary"></div>
            <div class="modal-field">
                <span class="modal-field-label">Notes (optional)</span>
                <textarea id="approveNotesInput" rows="2" placeholder="Optional note for the audit trail..."></textarea>
            </div>
        </div>
        <div class="modal-footer"><div class="modal-footer-right">
            <button class="btn-outline" onclick="closeApproveNotesModal()">Cancel</button>
            <button class="btn-approve" id="confirmApproveBtn">Approve</button>
        </div></div>
    </div>
</div>
```

Placed AFTER `#rejectReasonModal` in the DOM (same z-index stacking rules). The Escape handler is extended: `if (approveNotesModal.classList.contains('show')) closeApproveNotesModal(); else if (rejectReasonModal.classList.contains('show')) closeRejectModal(); else closeModal();` — still ONE handler, three layers.

**7d. Viewed Indicator CSS**

```css
.row-viewed td:first-child { border-left: 3px solid var(--color-primary); }
```

Subtle left-border accent on viewed rows. Session-only (JS Set, resets on reload).

**7e. Request Age CSS**

```css
.request-age { font-size: 11px; color: var(--color-on-surface-variant); margin-top: 2px; }
.request-age::before { content: '● '; font-size: 8px; }
.age-fresh .request-age::before { color: var(--color-primary); }
.age-waiting .request-age::before { color: var(--color-tertiary); }
.age-stale .request-age::before { color: var(--color-error); }
```

**7f. Reject Preset Chips HTML (inside §4.1 modal)**

```html
<div class="reject-presets">
    <button class="reject-preset-chip" onclick="applyRejectPreset('Venue unavailable')">Venue unavailable</button>
    <button class="reject-preset-chip" onclick="applyRejectPreset('Insufficient notice')">Insufficient notice</button>
    <button class="reject-preset-chip" onclick="applyRejectPreset('Slot conflict')">Slot conflict</button>
    <button class="reject-preset-chip" onclick="applyRejectPreset('Lecturer unavailable')">Lecturer unavailable</button>
    <button class="reject-preset-chip" onclick="applyRejectPreset('')">Other</button>
</div>
```

Placed inside `#rejectReasonModal .modal-body` ABOVE the textarea. "Other" passes empty string → clears textarea.

**7g. Nav Badge HTML**

```html
<a class="nav-item {{ $activeNav === 'request-approval' ? 'active' : '' }}" href="/request-approval-ui">
    Request Approval <span class="nav-badge" id="navPendingBadge"></span>
</a>
```

Badge populated by page script: `document.getElementById('navPendingBadge').textContent = pendingCount;` — hidden when count is 0.

```css
.nav-badge { display: inline-block; min-width: 18px; height: 18px; line-height: 18px; border-radius: 9px; background: var(--color-error); color: var(--color-on-error); font-size: 11px; font-weight: 600; text-align: center; margin-left: 6px; padding: 0 5px; }
```

**7h. Urgency Filter HTML (in toolbar)**

```html
<div class="urgency-filter" id="urgencyFilter">
    <button class="urgency-filter-chip active" data-urgency="all">All</button>
    <button class="urgency-filter-chip" data-urgency="urgent">Urgent</button>
    <button class="urgency-filter-chip" data-urgency="normal">Normal</button>
</div>
```

Placed in `.toolbar-left` after the status `<select>` and before the week `<select>`. JS toggles `.active` class and calls `renderTable()` with the urgency filter applied.

```css
.urgency-filter { display: inline-flex; gap: 4px; margin-left: 8px; }
.urgency-filter-chip { padding: 4px 10px; border-radius: 12px; border: 1px solid var(--color-outline); background: transparent; color: var(--color-on-surface-variant); font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s, color 0.15s; }
.urgency-filter-chip.active { background: var(--color-primary); color: var(--color-on-primary); border-color: var(--color-primary); }
.urgency-filter-chip:hover:not(.active) { background: var(--color-surface-variant); }
```

**i. Keyboard Shortcuts** (effort: Small)

Keyboard navigation for power reviewers. Active when the table is visible (not when a modal is open):

| Key | Action |
|-----|--------|
| `↑` / `↓` | Move highlight between visible rows |
| `Enter` | Open detail modal for highlighted row |
| `A` | Approve highlighted row (if Pending) — opens approval notes modal |
| `R` | Reject highlighted row (if Pending) — opens rejection reason modal |
| `Escape` | Move highlight off all rows (deselect) |

**Visual:** Highlighted row gets `.row-active` class — `background: var(--color-primary-container)` + left accent border, distinct from `.row-viewed`.

**State:** `activeRowIndex = -1` — index into `currentFiltered`. When a modal opens, keyboard nav pauses (all shortcuts ignored while any `.modal.show` is visible).

**Scope:** Only navigates rows visible on the current page (within the current pagination page). Does not auto-advance pages.

```css
.row-active { background: var(--color-primary-container) !important; border-left: 3px solid var(--color-primary); }
```

**j. Review Next Auto-Advance** (effort: Small)

After completing any approve/reject action (including from the approval notes modal or rejection reason modal), automatically open the next Pending request in the current filtered list:

1. Find next Pending request after the just-acted-on request's index in `currentFiltered`
2. If found → open its detail modal (or approval notes modal for approve, rejection reason modal for reject)
3. If no more Pending → close modal, clear `activeRowIndex`
4. Update `activeRowIndex` to match the auto-advanced row

This pairs with keyboard shortcuts: a PL can press `A` → confirm approve → next Pending opens → `A` again → rapid sequential review.

**Scope:** Only auto-advances when the user explicitly approves/rejects (not when they close the modal with Escape or overlay click). Does not auto-advance pages (if the last Pending on the current page is acted on, modal closes).

**k. Slot Validity Preview Icon** (effort: Tiny)

Show a tiny validity indicator in the **Proposed Replacement** column so the PL can spot conflicts at a glance without opening the modal:

| `slotValidity` value | Icon | CSS class | Tooltip |
|----------------------|------|-----------|---------|
| `"Valid"` | `✓` | `.slot-valid` | "Slot available — no conflict" |
| `"Conflict"` | `⚠` | `.slot-conflict` | "Conflict — another class scheduled" |
| `"Tentative"` | `?` | `.slot-tentative` | "Tentative — pending venue confirmation" |

```css
.slot-icon { font-size: 11px; margin-left: 4px; font-weight: 600; }
.slot-valid { color: var(--color-approved, #2e7d32); }
.slot-conflict { color: var(--color-rejected, #c62828); }
.slot-tentative { color: var(--color-amber, #f59e0b); }
```

Placed inside the Proposed Replacement cell, after the time line: `3:00 PM – 5:00 PM · <span class="slot-icon slot-valid">✓</span>`.

**l. Toast Notifications** (effort: Small)

Replace all browser `alert()` calls with the existing `showToast(message, undoCallback, duration)` function from `ui-common.js` (line 387). The toast bar HTML already exists in `ui-template.blade.php` (line 44) with CSS in `theme.css` (line 1653).

| Action | Toast message | Undo? | Duration |
|--------|--------------|-------|----------|
| Single approve | "Request #X approved." | Yes (§7m) | 5000ms |
| Single reject | "Request #X rejected." | Yes (§7m) | 5000ms |
| Bulk approve | "N request(s) approved." | Yes (§7m) | 5000ms |
| Bulk reject | "N request(s) rejected." | Yes (§7m) | 5000ms |
| Validation error | "Please provide a rejection reason." | No | 3000ms |

No new HTML or CSS needed — reuses the existing shared toast bar.

**m. Undo Stack** (effort: Small)

When a toast with Undo is shown, the undo callback reverts the action by restoring the previous status in the `MockData.approvalRequests` array and re-rendering.

```javascript
// Example undo flow for single approve:
function approveRequest(id) {
    const r = MockData.approvalRequests.find(x => x.id === id);
    const prevStatus = r.status; // 'Pending'
    if (confirm('Approve Request #' + id + '?')) {
        r.status = 'Approved'; // design-phase simulation
        renderTable();
        updateNavBadge();
        showToast('Request #' + id + ' approved.', function() {
            r.status = prevStatus; // undo: restore Pending
            renderTable();
            updateNavBadge();
        });
    }
}
```

**Scope:** Undo restores the in-memory status only (design phase). The undo callback is cleared when the toast auto-dismisses or is manually closed. Only the **last action** is undoable (no multi-level undo stack).

**n. Animated Transitions** (effort: Small)

Add subtle CSS transitions to make state changes feel smooth:

| Element | Animation | CSS |
|---------|-----------|-----|
| Row status change | Flash green/red background on approve/reject | `.row-flash-approved` / `.row-flash-rejected` — 0.6s fade-out |
| Filter change | Fade table content opacity 0→1 | `.grid-scroll` already has `transition: opacity 0.1s` — add class toggle |
| Group expand/collapse | `max-height` transition on group body | `.group-body { transition: max-height 0.3s ease, opacity 0.2s; overflow: hidden; }` |
| Summary card count | Number count-up on filter change | Optional — CSS `counter()` or JS `requestAnimationFrame` |

```css
.row-flash-approved { animation: flashGreen 0.6s ease; }
.row-flash-rejected { animation: flashRed 0.6s ease; }
@keyframes flashGreen { 0% { background: var(--color-secondary-container); } 100% { background: transparent; } }
@keyframes flashRed { 0% { background: var(--color-error-container); } 100% { background: transparent; } }
```

**o. Smart Grouping** (effort: Medium)

Add a "Group by" dropdown in `.toolbar-left` (after the urgency filter chips, before the week filter):

```html
<select id="groupFilter" onchange="setGroupFilter(this.value)">
    <option value="none">No grouping</option>
    <option value="course">Group by Course</option>
    <option value="lecturer">Group by Lecturer</option>
</select>
```

When a group is selected:
1. After filtering/sorting, group `currentFiltered` by the selected field
2. Insert `<tr class="group-header">` rows before each group — clickable to collapse/expand
3. Group header shows: expand/collapse arrow + group name + count badge (e.g. "BCS1234 — Object-Oriented Programming (3)")
4. Collapsed groups hide their body rows via `max-height: 0; opacity: 0`

**State:** `groupField = 'none'` — toggled by `setGroupFilter(value)`, resets page to 1, calls `renderTable()`.

**Interaction with other features:**
- Sorting applies **within** each group (groups maintain their order: alphabetical by group key)
- Pagination applies **after** grouping (group headers count as rows for pagination)
- Keyboard navigation skips group headers (only navigates data rows)
- Select All only selects data rows within the current page (not across groups)

**p. Mini Timeline** (effort: Small)

Add a request lifecycle timeline at the top of the detail modal (`.modal-body`), before Section 1:

```html
<div class="request-timeline">
    <div class="timeline-step completed">
        <div class="timeline-dot"></div>
        <div class="timeline-label">Submitted</div>
        <div class="timeline-time">30 Aug, 10:30 AM</div>
    </div>
    <div class="timeline-connector completed"></div>
    <div class="timeline-step completed">
        <div class="timeline-dot"></div>
        <div class="timeline-label">Viewed</div>
        <div class="timeline-time">30 Aug, 2:15 PM</div>
    </div>
    <div class="timeline-connector active"></div>
    <div class="timeline-step active">
        <div class="timeline-dot"></div>
        <div class="timeline-label">Reviewed</div>
        <div class="timeline-time">—</div>
    </div>
</div>
```

**States:** `.timeline-step.completed` (green dot + filled), `.timeline-step.active` (amber dot + pulsing), `.timeline-step.pending` (grey dot + outline).

**Data:** Uses existing mock fields: `requestedAt` (Submitted), `viewedAt` (Viewed — new mock field, nullable), `reviewedAt` (Reviewed — new mock field, nullable). The timeline is purely visual — it reads from the request object and does not track real view events.

```css
.request-timeline { display: flex; align-items: center; gap: 0; padding: 12px 0 16px; border-bottom: 1px solid var(--color-outline-variant); margin-bottom: 16px; }
.timeline-step { display: flex; flex-direction: column; align-items: center; gap: 4px; position: relative; z-index: 1; }
.timeline-dot { width: 12px; height: 12px; border-radius: 50%; border: 2px solid var(--color-outline); background: var(--color-surface); transition: all 0.3s; }
.timeline-step.completed .timeline-dot { background: var(--color-secondary); border-color: var(--color-secondary); }
.timeline-step.active .timeline-dot { background: var(--color-tertiary); border-color: var(--color-tertiary); animation: pulse 1.5s infinite; }
.timeline-label { font-size: 11px; font-weight: 500; color: var(--color-on-surface-variant); }
.timeline-time { font-size: 10px; color: var(--color-on-surface-variant); opacity: 0.7; }
.timeline-connector { flex: 1; height: 2px; background: var(--color-outline-variant); min-width: 40px; }
.timeline-connector.completed { background: var(--color-secondary); }
.timeline-connector.active { background: linear-gradient(90deg, var(--color-secondary), var(--color-tertiary)); }
@keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(var(--color-tertiary-rgb, 156, 39, 176), 0.4); } 50% { box-shadow: 0 0 0 6px rgba(var(--color-tertiary-rgb, 156, 39, 176), 0); } }
```

**q. Skeleton Loading** (effort: Small)

Show skeleton placeholder rows when the page first loads or when filters change, then fade in the real content after a simulated delay.

**Existing CSS:** `.skeleton`, `.skeleton-row` (60px height), `.skeleton-card`, `.skeleton-text` with `skeleton-shimmer` animation in `theme.css:1570`.

**Behavior:**
1. On `DOMContentLoaded`: show 10 skeleton rows + 4 skeleton cards for 300ms, then render real content
2. On filter/sort change: briefly show skeleton rows (150ms) then re-render — gives a "loading" feel
3. Skeleton rows match the table column layout (11 columns)

```javascript
function showSkeleton() {
    const tbody = document.querySelector('#dataTable tbody');
    tbody.innerHTML = Array(10).fill('').map(() =>
        '<tr>' + Array(11).fill('<td><div class="skeleton" style="height:16px"></div></td>').join('') + '</tr>'
    ).join('');
    document.querySelectorAll('.summary-card .summary-value').forEach(el => {
        el.innerHTML = '<div class="skeleton" style="height:24px;width:40px"></div>';
    });
}
```

**8. Nav Bar Update**

Add a "Request Approval" nav link to `partials/ui-nav-bar.blade.php`, positioned after "Replacement Arrangement" and before "Replacement History":
```html
<a class="nav-item {{ $activeNav === 'request-approval' ? 'active' : '' }}" href="/request-approval-ui">Request Approval</a>
```

**9. Route**

Add to `routes/web.php`:
```php
Route::get('/request-approval-ui', function () {
    return view('ui-design-templates.request-approval-UI-design-template', ['activeNav' => 'request-approval']);
});
```
No `->name()` — route is unreferenced by `route()` helpers.

**10. OOP / Reuse**

The page is built on OOP concepts: **inheritance** (Blade layout), **composition** (Blade partials), **encapsulation** (shared JS module + shared CSS module), and **reuse**. It reuses every existing shared asset, and — where a helper is needed by more than one page — promotes it into the shared module instead of duplicating it page-locally.

**10.1 Inheritance — Blade layout**

- `@extends('layouts.ui-template', ['activeNav' => 'request-approval'])` — the layout provides the HTML shell, theme pre-paint script, `<link>` to `theme.css`, nav bar include, `ui-common.js` + `mock-data.js` includes, and DOMContentLoaded handler
- `@section`/`@yield` — the page fills 4 sections: `title`, `page-styles`, `content`, `page-scripts`

**10.2 Composition — Blade partials**

- `@include('partials.ui-nav-bar', ['activeNav' => 'request-approval'])` — auto-included by the layout (no explicit include needed)
- `@include('partials.ui-summary-bar', ['cards' => [...]])` — summary cards, same partial reused by replacement-home, my-timetable, my-request-history

**10.3 Encapsulation — shared CSS module (`theme.css`)**

Reuse (no copy): `.toolbar`, `.grid-wrapper`, `.grid-scroll`, `.timetable`, `.timetable th/td`, `.pagination-bar`, `.pagination-info`, `.pagination-controls`, `.page-btn`, `.summary-bar`, `.summary-card`, `.summary-value`, `.summary-label`, `.empty-state`, `.empty-icon`, `.empty-title`, `.empty-text`, `.cell-code`, `.cell-name`, `.badge` base, sort arrow + sort hint, `.app-container`, nav styles, responsive breakpoints.

**10.4 Encapsulation — shared JS module (`ui-common.js`)**

Reuse (no copy) — already used by my-request-history:
- `to12h(t)`, `formatDate(iso)` — time/date formatting
- `compareBy(sortState, va, vb)` — generic sort comparator
- `makeSortableHeader(col, sortState, render)` — sortable header factory
- `paginate(cfg)`, `updateResultCount(cfg)` — pagination + result count
- `closeOnEsc(closeFn)`, `closeOnOverlayClick(e, closeFn)` — modal close helpers

**10.5 NEW — promote duplicated helpers into `ui-common.js`**

The following 10 helpers currently exist page-locally in `my-request-history-UI-design-template.blade.php` (lines 465–546). Since this page needs the identical logic, they are **promoted (moved) into `ui-common.js`** — the single source of truth — rather than copy-pasted:

| Helper | Current location (my-request-history) | Promoted to |
|--------|----------------------------------------|-------------|
| `weekRanges` const | line 465 | `ui-common.js` |
| `formatDateTime(iso)` | line 472 | `ui-common.js` |
| `statusClass(status)` | line 488 | `ui-common.js` |
| `dayAbbr(day)` | line 499 | `ui-common.js` |
| `isoDayName(iso)` | line 503 | `ui-common.js` |
| `formatClassBlock(r)` | line 509 | `ui-common.js` |
| `formatReplacementBlock(r)` | line 519 | `ui-common.js` |
| `getWeekRange(weekVal)` | line 529 | `ui-common.js` |
| `isInWeek(classDate, weekVal)` | line 534 | `ui-common.js` |
| `getWeekNumber(iso)` | line 541 | `ui-common.js` |

These helpers are generic (they depend only on `weekRanges`, `to12h`, `formatDate` — all shared) and are identical across pages. `my-request-history-UI-design-template.blade.php` is **refactored** to call the shared versions instead of its page-local copies (delete local copies, rely on `ui-common.js` loaded by the layout). This removes ~80 lines of duplicated JS.

**10.6 NEW — shared data module (`mock-data.js`)**

The page's mock data is **not** embedded in the page script. The shared module `public/js/mock-data.js` defines `window.MockData.approvalRequests` (20 request entries, see §6) and `MockData.urgencyReferenceDate` (fixed demo reference date `new Date('2026-08-29T00:00:00')`).

The layout loads it via `<script src="/js/mock-data.js"></script>` immediately after `ui-common.js` (both before the page's inline script). The page's `@section('page-scripts')` reads from `MockData.*` and does NOT redeclare them (redeclaring `const approvalRequests` / `const URGENCY_REFERENCE_DATE` would throw "Identifier already declared" and kill the page script). Data is separated from logic (encapsulation): `mock-data.js` holds only data, `ui-common.js` holds only generic functions, the page holds page-specific logic. Other pages keep their own inline datasets for now — migrating them into `mock-data.js` is future work.

**Page-local (NOT shared — page-specific):** urgency helpers (`urgencyLevel`, `urgencyClass`, `urgencyLabel`, `urgencyDays` — the functions; the `MockData.urgencyReferenceDate` value lives in `mock-data.js`), `approveRequest`, `rejectRequest`, `slotValidityHtml`, render/filter/sort glue, modal open/close, event listeners. Page-specific CSS (urgency badges, action buttons, column widths, modal styles) stays in the page's `@section('page-styles')` — copied from my-request-history per the existing per-page duplication convention for page-specific CSS.

### Out of Scope

- Backend logic (no database, no API, no auth role check)
- In-memory state updates on approve/reject (frontend design phase only — clicking Approve/Reject shows a confirm dialog + alert, does NOT change the row status or update summary cards)
- PL-specific nav bar filtering (the "Request Approval" nav link is visible to all users in this design phase)
- Email notifications
- Pagination beyond client-side

## Files Changed

| File | Change |
|------|--------|
| `resources/views/ui-design-templates/request-approval-UI-design-template.blade.php` | **Create** — full page with CSS, HTML, JS (reads `MockData.approvalRequests` + `MockData.urgencyReferenceDate` from mock-data.js, does not embed them) |
| `public/js/mock-data.js` | **Create** — shared data module: `MockData.approvalRequests` (20 entries) + `MockData.urgencyReferenceDate` |
| `resources/views/layouts/ui-template.blade.php` | **Modify** — add `<script src="/js/mock-data.js"></script>` after ui-common.js |
| `resources/views/partials/ui-nav-bar.blade.php` | **Modify** — add "Request Approval" nav link |
| `routes/web.php` | **Modify** — add `/request-approval-ui` route |
| `public/js/ui-common.js` | **Modify** — promote 10 shared helpers from my-request-history (weekRanges, formatDateTime, statusClass, dayAbbr, isoDayName, formatClassBlock, formatReplacementBlock, getWeekRange, isInWeek, getWeekNumber) |
| `resources/views/ui-design-templates/my-request-history-UI-design-template.blade.php` | **Modify** — remove the 10 promoted page-local helpers; use shared ui-common.js versions instead (no behavior change) |
| `page-changelogs/request-approval-changelog.md` | **Update/replace** — file already exists (dated 2026-08-01); rewrite it to document the page implementation, OOP helper promotion, mock-data.js module, and my-request-history refactor |
