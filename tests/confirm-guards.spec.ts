import { test, expect } from '@playwright/test';

const BASE = 'http://localhost:8000/replacement-arrangement';

async function openPage(page) {
  await page.goto(BASE, { waitUntil: 'networkidle' });
  await page.locator('.timetable .cell-content').first().waitFor({ state: 'visible', timeout: 15000 });
}

async function selectGridSlot(page) {
  const cell = page.locator('.timetable .cell-content.cell-available').first();
  await cell.waitFor({ state: 'visible', timeout: 10000 });
  await cell.click();
  await page.locator('.timetable .cell-content.cell-selected').first().waitFor({ state: 'visible', timeout: 5000 });
}

async function pickOtherVenue(page): Promise<boolean> {
  // VenueDropdown: 4-column hierarchy (Type → Block → Floor → Room)
  await page.locator('.venue-dd-trigger').click();
  // Pick the first type
  const typeCol = page.locator('.venue-col').nth(0);
  const typeItems = typeCol.locator('.venue-col-item-parent');
  const typeCount = await typeItems.count();
  if (typeCount === 0) return false;
  await typeItems.nth(0).click();
  // Block
  const blockCol = page.locator('.venue-col').nth(1);
  const blockItems = blockCol.locator('.venue-col-item-parent');
  const blockCount = await blockItems.count();
  if (blockCount === 0) return false;
  await blockItems.nth(0).click();
  // Floor
  const floorCol = page.locator('.venue-col').nth(2);
  const floorItems = floorCol.locator('.venue-col-item-parent');
  const floorCount = await floorItems.count();
  if (floorCount === 0) return false;
  await floorItems.nth(0).click();
  // Room (different from current if possible)
  const roomCol = page.locator('.venue-col').nth(3);
  const rooms = roomCol.locator('.venue-col-item-room:not(.selected)');
  const roomCount = await rooms.count();
  if (roomCount === 0) return false;
  await rooms.first().click();
  return true;
}

test('venue change with selection pops confirmation; cancel restores', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  const picked = await pickOtherVenue(page);
  if (!picked) { test.skip(true, 'no alternate venue reachable'); return; }
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#confirmModal .btn-outline').click();
  await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 5000 });
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeVisible({ timeout: 5000 });
});

test('venue change confirm clears selection and applies', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  const picked = await pickOtherVenue(page);
  if (!picked) { test.skip(true, 'no alternate venue reachable'); return; }
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#modalConfirmBtn').click();
  await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 5000 });
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeHidden({ timeout: 5000 });
});

test('subject change with selection pops confirmation; cancel restores', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  const subjectSel = page.locator('#subjectSelector');
  const before = await subjectSel.inputValue();
  const options = subjectSel.locator('option');
  const optCount = await options.count();
  if (optCount < 2) { test.skip(true, 'no alternate subject'); return; }
  // Pick an option different from current
  let targetVal = null;
  for (let i = 1; i < optCount; i++) {
    const val = await options.nth(i).getAttribute('value');
    if (val && val !== before) { targetVal = val; break; }
  }
  if (!targetVal) { test.skip(true, 'no alternate subject value'); return; }
  await subjectSel.selectOption(targetVal);
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#confirmModal .btn-outline').click();
  await expect(subjectSel).toHaveValue(before);
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeVisible({ timeout: 5000 });
});

test('subject change confirm clears selection and applies', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  const subjectSel = page.locator('#subjectSelector');
  const before = await subjectSel.inputValue();
  const options = subjectSel.locator('option');
  const optCount = await options.count();
  let targetVal = null;
  for (let i = 1; i < optCount; i++) {
    const val = await options.nth(i).getAttribute('value');
    if (val && val !== before) { targetVal = val; break; }
  }
  if (!targetVal) { test.skip(true, 'no alternate subject value'); return; }
  await subjectSel.selectOption(targetVal);
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#modalConfirmBtn').click();
  await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 5000 });
  await expect(subjectSel).toHaveValue(targetVal);
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeHidden({ timeout: 5000 });
});

test('slot trigger change with selection pops confirmation; cancel restores', async ({ page }) => {
  await openPage(page);
  // Find a subject with 2+ slot options
  const subjectSel = page.locator('#subjectSelector');
  const options = subjectSel.locator('option');
  const optCount = await options.count();
  let targetVal = null;
  for (let i = 1; i < optCount; i++) {
    const val = await options.nth(i).getAttribute('value');
    await subjectSel.selectOption(val);
    await page.waitForTimeout(400);
    if (await page.locator('.slot-dd-item').count() >= 2) { targetVal = val; break; }
  }
  if (!targetVal) { test.skip(true, 'no subject with 2+ slots'); return; }
  await subjectSel.selectOption(targetVal);
  await page.waitForTimeout(400);
  // Select a grid cell
  await selectGridSlot(page);
  const items = page.locator('.slot-dd-item');
  const beforeText = await page.locator('#slotTriggerText').textContent();
  await page.locator('#slotTrigger').click();
  await items.nth(1).click();
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#confirmModal .btn-outline').click();
  await expect(page.locator('#slotTriggerText')).toHaveText(beforeText);
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeVisible({ timeout: 5000 });
});

test('clear all with selection pops confirmation; cancel keeps selection', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  await page.locator('#clearAllBtn').click();
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#confirmModal .btn-outline').click();
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeVisible({ timeout: 5000 });
});

test('no confirmation popup when changing dropdowns without selection', async ({ page }) => {
  await openPage(page);
  const subjectSel = page.locator('#subjectSelector');
  const options = subjectSel.locator('option');
  const optCount = await options.count();
  if (optCount >= 2) {
    const targetVal = await options.nth(1).getAttribute('value');
    if (targetVal) {
      await subjectSel.selectOption(targetVal);
      await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 3000 });
    }
  }
});

test('week next arrow with selection pops confirmation; confirm saves and navigates', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  const nextBtn = page.locator('.week-arrow[aria-label="Next week"]');
  await nextBtn.click();
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await expect(page.locator('#confirmModal .modal-body')).toContainText('saved');
  await page.locator('#modalConfirmBtn').click();
  await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 5000 });
  // Selection should be cleared visually after navigation
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeHidden({ timeout: 5000 });
});

test('week prev arrow with selection pops confirmation; cancel keeps selection', async ({ page }) => {
  await openPage(page);
  // Navigate to week 2 first so prev is available
  const nextBtn = page.locator('.week-arrow[aria-label="Next week"]');
  await nextBtn.click();
  await page.waitForTimeout(300);
  await selectGridSlot(page);
  const prevBtn = page.locator('.week-arrow[aria-label="Previous week"]');
  await prevBtn.click();
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#confirmModal .btn-outline').click();
  await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 5000 });
  await expect(page.locator('.timetable .cell-content.cell-selected').first()).toBeVisible({ timeout: 5000 });
});

test('week dropdown change with selection pops confirmation; cancel restores dropdown value', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  const weekSel = page.locator('#weekSelector');
  const beforeVal = await weekSel.inputValue();
  const options = weekSel.locator('option');
  const optCount = await options.count();
  let targetVal: string | null = null;
  for (let i = 0; i < optCount; i++) {
    const val = await options.nth(i).getAttribute('value');
    if (val && val !== beforeVal) { targetVal = val; break; }
  }
  if (!targetVal) { test.skip(true, 'no alternate week'); return; }
  await weekSel.selectOption(targetVal);
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#confirmModal .btn-outline').click();
  await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 5000 });
  await expect(weekSel).toHaveValue(beforeVal);
});

test('back button with saved selection (after week change) shows confirmation', async ({ page }) => {
  await openPage(page);
  await selectGridSlot(page);
  // Navigate away (confirm the week change)
  const nextBtn = page.locator('.week-arrow[aria-label="Next week"]');
  await nextBtn.click();
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
  await page.locator('#modalConfirmBtn').click();
  await expect(page.locator('#confirmModal')).toBeHidden({ timeout: 5000 });
  // Now click the page back button — should show confirmation because saved selections exist
  const backBtn = page.locator('.back-btn');
  await backBtn.click();
  await expect(page.locator('#confirmModal')).toBeVisible({ timeout: 5000 });
});
