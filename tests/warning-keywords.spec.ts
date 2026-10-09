import { test, expect } from '@playwright/test';

// ── warning-modal-keywords (SDD 2026-10-09) — R4: the detail modal's
// "Cancel Request" button was a silent no-op (overlay opened without
// binding pendingCancelId). This test fails by timeout on pre-fix code.

const RH = 'http://localhost:8000/my-request-history-ui';

test('detail-modal Cancel Request actually cancels the viewed request', async ({ page }) => {
  await page.goto(RH, { waitUntil: 'networkidle' });
  // Open a PENDING request's detail modal via its status badge (cancel button
  // is only rendered for pending requests)
  const badge = page.locator('.badge:has-text("Pending")').first();
  await badge.click();
  const cancelBtn = page.locator('#cancelRequestBtn');
  await expect(cancelBtn).toBeVisible({ timeout: 5000 });
  await cancelBtn.click();
  // Confirm overlay bound to the viewed request (pre-fix: Yes did nothing)
  await expect(page.locator('#cancelConfirmOverlay')).toBeVisible({ timeout: 5000 });
  await page.locator('#confirmCancelAction').click();
  // Same behavior as quick-cancel: removal feedback + undo toast
  await expect(page.locator('#toastBar')).toContainText('cancelled', { timeout: 5000 });
  // Overlay closed after the action
  await expect(page.locator('#cancelConfirmOverlay')).toBeHidden({ timeout: 5000 });
});
