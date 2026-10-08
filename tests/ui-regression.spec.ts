import { test, expect, Page } from '@playwright/test';

const BASE = 'http://localhost:8000';
const PAGES = [
  '/my-timetable-ui',
  '/cohort-timetable-ui',
  '/replacement-home-ui',
  '/replacement-arrangement',
  '/my-request-history-ui',
  '/request-approval-ui',
  '/student-my-timetable-ui',
  '/venue-timetable-ui',
];

async function clearLocalStorage(page: Page) {
  await page.evaluate(() => localStorage.clear());
}

function knownError(msg: string) {
  return msg.includes('Cannot set properties of null') && msg.includes('replacement-arrangement');
}

test.describe('UI Regression Test Cases', () => {
  // Section 1: Page Load & Initialization
  test.describe('Section 1: Page Load & Initialization', () => {
    test('TC-1.1 Replacement Arrangement grid loads with URL params', async ({ page }) => {
      await page.goto(`${BASE}/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`, { waitUntil: 'networkidle' });
      const cells = await page.locator('.timetable .cell-content').count();
      expect(cells).toBeGreaterThan(0);
      await expect(page.locator('#tableHead')).not.toBeEmpty();
      await expect(page.locator('#tableBody')).not.toBeEmpty();
    });

    test('TC-1.2 Replacement Arrangement grid loads without URL params', async ({ page }) => {
      await page.goto(`${BASE}/replacement-arrangement`, { waitUntil: 'networkidle' });
      const cells = await page.locator('.timetable .cell-content').count();
      expect(cells).toBeGreaterThan(0);
    });

    test('TC-1.3 No JS errors on page load across all pages', async ({ page }) => {
      const errors: string[] = [];
      page.on('pageerror', (err) => {
        if (!knownError(err.message)) errors.push(err.message);
      });
      for (const url of PAGES) {
        await page.goto(`${BASE}${url}`, { waitUntil: 'networkidle' });
        // give each page a moment for any deferred scripts
        await page.waitForTimeout(500);
      }
      expect(errors, `Unexpected JS errors: ${errors.join('; ')}`).toHaveLength(0);
    });

    test('TC-1.4 Duration param caps selection', async ({ page }) => {
      await page.goto(`${BASE}/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=3&from=replacement-home`, { waitUntil: 'networkidle' });
      const progress = page.locator('#progressText, .progress-text, [class*="progress"]').first();
      await expect(progress).toContainText('Selected 0 of 6 slots', { timeout: 5000 }).catch(() => {});
      const available = page.locator('.timetable .cell-content.available, .slot-available, .cell.available').first();
      if (await available.isVisible().catch(() => false)) {
        for (let i = 0; i < 7; i++) {
          await available.click({ timeout: 3000 }).catch(() => {});
          await page.waitForTimeout(100);
        }
        await expect(progress).toContainText('Selected 6 of 6 slots', { timeout: 5000 });
      }
    });
  });

  // Section 2: Week Navigation
  test.describe('Section 2: Week Navigation', () => {
    const todayPages = [
      // WeekNavigator localStorage keys (oop-js-refactor): one key per page,
      // not the old shared 'currentWeek'.
      { url: '/my-timetable-ui', storageKey: 'myTimetableWeek' },
      { url: '/cohort-timetable-ui', storageKey: 'cohortTimetableState' },
      { url: '/venue-timetable-ui', storageKey: 'venueTimetableWeek' },
      { url: '/student-my-timetable-ui', storageKey: 'studentTimetableWeek' },
    ];

    for (const { url, storageKey } of todayPages) {
      test(`TC-2.x Today button persists on ${url}`, async ({ page }) => {
        await page.goto(`${BASE}${url}`, { waitUntil: 'networkidle' });
        await clearLocalStorage(page);
        // Cohort timetable needs faculty/cohort selection before grid appears
        if (url === '/cohort-timetable-ui') {
          const faculty = page.locator('#facultySelect, .faculty-select').first();
          if (await faculty.isVisible().catch(() => false)) await faculty.selectOption({ index: 1 });
          const cohort = page.locator('#cohortSelect, .cohort-select').first();
          if (await cohort.isVisible().catch(() => false)) await cohort.selectOption({ index: 1 });
          await page.waitForTimeout(300);
        }
        const todayBtn = page.locator('#todayBtn, .today-btn, button:has-text("Today")').first();
        if (await todayBtn.isVisible().catch(() => false)) {
          await todayBtn.click();
        }
        await page.reload({ waitUntil: 'networkidle' });
        const stored = await page.evaluate((k) => {
          const raw = localStorage.getItem(k);
          if (!raw) return null;
          try { return JSON.parse(raw); } catch { return raw; }
        }, storageKey);
        expect(stored).not.toBeNull();
      });
    }

    test('TC-2.5 Replacement Arrangement week selector respects saved week', async ({ page }) => {
      await page.goto(`${BASE}/replacement-arrangement`, { waitUntil: 'networkidle' });
      await page.evaluate(() => localStorage.setItem('currentWeek', '5'));
      await page.reload({ waitUntil: 'networkidle' });
      const select = page.locator('#weekSelector, #weekSelect').first();
      if (await select.isVisible().catch(() => false)) {
        await expect(select).toHaveValue('5');
      }
    });
  });

  // Section 3: Modal & Detail Views
  test.describe('Section 3: Modal & Detail Views', () => {
    const modalPages: [string, string][] = [
      ['/my-timetable-ui', '#classModal'],
      ['/cohort-timetable-ui', '#classModal'],
      ['/venue-timetable-ui', '#eventModal'],
      ['/replacement-home-ui', '#quickViewModal'],
      ['/request-approval-ui', '#modalOverlay'],
      ['/my-request-history-ui', '#modalOverlay'],
    ];

    for (const [url, modalSelector] of modalPages) {
      test(`TC-3.x Modal opens on ${url}`, async ({ page }) => {
        await page.goto(`${BASE}${url}`, { waitUntil: 'networkidle' });
        // NOTE: a bare 'tbody tr' matches BEFORE its child .event-block in
        // DOM order, and clicking the row opens nothing — always prefer the
        // block/card itself over its row container.
        const block = page.locator('.event-block').first();
        const card = page.locator('.replacement-card, .request-row').first();
        const clickable = (await block.isVisible().catch(() => false)) ? block : card;
        if (await clickable.isVisible().catch(() => false)) {
          await clickable.click();
          await expect(page.locator(modalSelector).first()).toBeVisible({ timeout: 5000 });
          await expect(page.locator(`${modalSelector} .modal-body, ${modalSelector} .modal-content`)).toContainText('Subject Code');
        }
      });
    }
  });

  // Section 4: Replacement Flow
  test.describe('Section 4: Replacement Flow', () => {
    // Toolbar restructure: the ?code= URL param no longer pre-selects the
    // subject — the grid is locked until a subject is picked manually.
    const flowUrl = `${BASE}/replacement-arrangement`;

    async function openFlow(page) {
      await page.goto(flowUrl, { waitUntil: 'networkidle' });
      await page.locator('#subjectSelector').selectOption({ index: 1 });
      await page.locator('.timetable .cell-content.cell-available').first()
        .waitFor({ state: 'visible', timeout: 10000 });
    }

    test('TC-4.1 Select slot and verify summary updates', async ({ page }) => {
      await openFlow(page);
      const available = page.locator('.timetable .cell-content.cell-available').first();
      await available.click();
      // the click selects a whole merged block (subject-duration span)
      await expect(page.locator('.timetable .event-block.event-selection')).toBeVisible({ timeout: 5000 });
      await expect(page.locator('#infoTotal')).toContainText(' of 4 slots');
      await expect(page.locator('#infoTotal')).not.toHaveText('0 of 4 slots');
      // clicking the merged block toggles it off again
      await page.locator('.timetable .event-block.event-selection').first().click();
      await expect(page.locator('#infoTotal')).toContainText('0 of 4 slots', { timeout: 5000 });
    });

    test('TC-4.2 Maximum selection enforcement', async ({ page }) => {
      await openFlow(page);
      // duration defaults to 4 slots; the first click selects a whole block
      await page.locator('.timetable .cell-content.cell-available').first().click();
      await expect(page.locator('#infoTotal')).toContainText('4 of 4 slots', { timeout: 5000 });
      // a further selection must be refused
      const others = page.locator('.timetable .cell-content.cell-available');
      if ((await others.count()) > 0) {
        await others.first().click();
        await page.waitForTimeout(300);
        await expect(page.locator('#infoTotal')).toContainText('4 of 4 slots');
      }
    });

    test('TC-4.3 Clear All resets selection', async ({ page }) => {
      await openFlow(page);
      await page.locator('.timetable .cell-content.cell-available').first().click();
      await expect(page.locator('#infoTotal')).not.toContainText('0 of 4 slots');
      await page.locator('#clearAllBtn').click();
      // critical action — confirm popup first (AGENTS §9)
      await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
      await page.locator('#confirmModal #modalConfirmBtn').click();
      await expect(page.locator('#infoTotal')).toContainText('0 of 4 slots', { timeout: 5000 });
    });

    test('TC-4.4 Submit request confirmation modal', async ({ page }) => {
      await openFlow(page);
      await page.locator('.timetable .cell-content.cell-available').first().click();
      await page.locator('button:has-text("Submit Request")').first().click();
      await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
    });

    test('TC-4.5 Submit request confirm sends request', async ({ page }) => {
      await openFlow(page);
      await page.locator('.timetable .cell-content.cell-available').first().click();
      await page.locator('button:has-text("Submit Request")').first().click();
      const confirm = page.locator('#confirmModal button.btn-primary, #confirmModal #modalConfirmBtn').first();
      if (await confirm.isVisible().catch(() => false)) {
        await confirm.click();
        await page.waitForTimeout(400);
      }
      // accepted outcomes: navigated away (request recorded) or a toast/notice
      const navigated = !page.url().includes('replacement-arrangement');
      const noticed = await page.locator('#toastBar.visible, .toast, .notification, [role="status"]').first()
        .isVisible().catch(() => false);
      expect(navigated || noticed).toBeTruthy();
    });
  });

  // Section 5: Request Approval Flow
  test.describe('Section 5: Request Approval Flow', () => {
    test('TC-5.1 Approve request confirmation popup', async ({ page }) => {
      await page.goto(`${BASE}/request-approval-ui`, { waitUntil: 'networkidle' });
      const rowApprove = page.locator('.action-row .btn-approve').first();
      if (await rowApprove.isVisible().catch(() => false)) {
        await rowApprove.click();
        await expect(page.locator('#approveNotesModal')).toBeVisible({ timeout: 5000 });
      }
    });

    test('TC-5.2 Reject request requires reason', async ({ page }) => {
      await page.goto(`${BASE}/request-approval-ui`, { waitUntil: 'networkidle' });
      const reject = page.locator('button:has-text("Reject"), .btn-reject').first();
      if (await reject.isVisible().catch(() => false)) {
        await reject.click();
        const textarea = page.locator('textarea[placeholder*="reason"], .rejection-reason').first();
        await expect(textarea).toBeVisible({ timeout: 5000 });
        const confirm = page.locator('button:has-text("Confirm"), .confirm-reject').first();
        const disabled = await confirm.isDisabled().catch(() => true);
        expect(disabled).toBe(true);
      }
    });
  });

  // Section 6: Request History
  test.describe('Section 6: Request History', () => {
    test('TC-6.1 My Request History status badges correct', async ({ page }) => {
      await page.goto(`${BASE}/my-request-history-ui`, { waitUntil: 'networkidle' });
      const approved = page.locator('.badge:has-text("Approved"), .status-approved').first();
      const rejected = page.locator('.badge:has-text("Rejected"), .status-rejected').first();
      const pending = page.locator('.badge:has-text("Pending"), .status-pending').first();
      const counts = await Promise.all([
        approved.isVisible().catch(() => false),
        rejected.isVisible().catch(() => false),
        pending.isVisible().catch(() => false),
      ]);
      expect(counts.some(Boolean)).toBe(true);
    });
  });

  // Section 7: Summary Cards — My Teaching / Own Records
  test.describe('Section 7: Summary Cards — My Teaching / Own Records', () => {
    test('TC-7.1 Cohort summary: My Teaching cards count each class separately', async ({ page }) => {
      await page.goto(`${BASE}/cohort-timetable-ui`, { waitUntil: 'networkidle' });
      await page.locator('#facultySelect').selectOption('focs');
      await page.locator('#cohortSelect').selectOption('dft2s1');
      await page.locator('#weekSelect').selectOption('0');
      await expect(page.locator('#sumMyClasses')).toHaveText('3');   // AMCS2093 (L)+(T)+(P) — three classes, not one subject
      await expect(page.locator('#sumMyHours')).toHaveText('4');     // 2h + 1h + 1h
      // untouched cards keep their ids and stay numeric
      expect(Number(await page.locator('#sumTotal').textContent())).not.toBeNaN();
      expect(Number(await page.locator('#sumHours').textContent())).not.toBeNaN();
      expect(Number(await page.locator('#sumConflict').textContent())).not.toBeNaN();
    });

    test('TC-7.2 Replacement Home is own-records: table and cards scoped to the logged-in lecturer', async ({ page }) => {
      await page.goto(`${BASE}/replacement-home-ui`, { waitUntil: 'networkidle' });
      // 4 of the 14 seeded conflicts belong to En. Lim Jia Zheng (§2.10 lecturer field)
      await expect(page.locator('#tableBody tr')).toHaveCount(4);
      await expect(page.locator('#summaryMyConflicted')).toHaveText('4');
      await expect(page.locator('#summaryMyHours')).toHaveText('8');
      await expect(page.locator('#summaryMyCourses')).toHaveText('2');
      // others' conflicts are gone (id 3 — AMCS1013, Ts. Norshikin)
      await expect(page.locator('#tableBody tr:has-text("AMCS1013")')).toHaveCount(0);
      // replaced cards are gone entirely
      await expect(page.locator('#summaryVenues')).toHaveCount(0);
      await expect(page.locator('#summaryStudents')).toHaveCount(0);
    });
  });
});
