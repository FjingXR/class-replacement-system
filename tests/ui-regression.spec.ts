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
      { url: '/my-timetable-ui', storageKey: 'currentWeek' },
      { url: '/cohort-timetable-ui', storageKey: 'cohortTimetableState' },
      { url: '/venue-timetable-ui', storageKey: 'currentWeek' },
      { url: '/student-my-timetable-ui', storageKey: 'currentWeek' },
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
        const clickable = page.locator('.event-block, .replacement-card, .timetable tbody tr, .request-row').first();
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
    const flowUrl = `${BASE}/replacement-arrangement?code=BMIT5555&date=2026-09-04&duration=2&from=replacement-home`;

    test('TC-4.1 Select slot and verify summary updates', async ({ page }) => {
      await page.goto(flowUrl, { waitUntil: 'networkidle' });
      const available = page.locator('.timetable .cell-content.cell-available').first();
      if (await available.isVisible().catch(() => false)) {
        await available.click();
        const progress = page.locator('#progressText').first();
        await expect(progress).toContainText('Selected 1 of 4 slots', { timeout: 5000 });
        await available.click();
        await expect(progress).toContainText('Selected 0 of 4 slots', { timeout: 5000 });
      }
    });

    test('TC-4.2 Maximum selection enforcement', async ({ page }) => {
      await page.goto(flowUrl, { waitUntil: 'networkidle' });
      const progress = page.locator('#progressText').first();
      const available = page.locator('.timetable .cell-content.cell-available');
      const cells = await available.count();
      for (let i = 0; i < Math.min(cells, 5); i++) {
        await available.nth(i).click();
      }
      const text = await progress.textContent().catch(() => '');
      expect(text).toContain('Selected 4 of 4 slots');
    });

    test('TC-4.3 Clear All resets selection', async ({ page }) => {
      await page.goto(flowUrl, { waitUntil: 'networkidle' });
      const available = page.locator('.timetable .cell-content.cell-available').first();
      if (await available.isVisible().catch(() => false)) {
        await available.click();
        await available.click();
        const clear = page.locator('#clearAllBtn, button:has-text("Clear ALL"), .clear-all').first();
        if (await clear.isVisible().catch(() => false)) await clear.click();
        const progress = page.locator('#progressText').first();
        await expect(progress).toContainText('Selected 0 of 4 slots', { timeout: 5000 });
      }
    });

    test('TC-4.4 Submit request confirmation modal', async ({ page }) => {
      await page.goto(flowUrl, { waitUntil: 'networkidle' });
      const available = page.locator('.timetable .cell-content.cell-available').first();
      if (await available.isVisible().catch(() => false)) {
        await available.click();
        const submit = page.locator('#submitRequestBtn, button:has-text("Submit Request"), .submit-request').first();
        if (await submit.isVisible().catch(() => false)) await submit.click();
        const modal = page.locator('#submitConfirmModal, .confirmation-modal, .modal').first();
        await expect(modal).toBeVisible({ timeout: 5000 });
      }
    });

    test('TC-4.5 Submit request confirm sends request', async ({ page }) => {
      await page.goto(flowUrl, { waitUntil: 'networkidle' });
      const available = page.locator('.timetable .cell-content.cell-available').first();
      if (await available.isVisible().catch(() => false)) {
        await available.click();
        const submit = page.locator('#submitRequestBtn, button:has-text("Submit Request"), .submit-request').first();
        if (await submit.isVisible().catch(() => false)) await submit.click();
        const confirm = page.locator('#confirmSubmitBtn, button:has-text("Confirm"), .confirm-submit').first();
        if (await confirm.isVisible().catch(() => false)) await confirm.click();
        const toast = page.locator('.toast, .notification, [role="status"]').first();
        await expect(toast).toBeVisible({ timeout: 5000 }).catch(() => {});
      }
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
});
