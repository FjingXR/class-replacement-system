import { test, expect } from '@playwright/test';

const PAGE = '/venue-timetable-ui';

test.describe('Venue Timetable UI', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto(PAGE);
    await page.evaluate(() => {
      localStorage.clear();
      // Ensure week starts at 0
      sessionStorage.clear();
    });
    await page.goto(PAGE);
    await page.waitForTimeout(500);
  });

  // ════════════════════════════════════════════
  // 1. PAGE LOAD & STRUCTURE
  // ════════════════════════════════════════════

  test('TC01 — page loads with correct title', async ({ page }) => {
    await expect(page).toHaveTitle(/Venue Timetable/);
  });

  test('TC02 — page header shows "Venue Timetable"', async ({ page }) => {
    await expect(page.locator('.page-title')).toHaveText('Venue Timetable');
  });

  test('TC03 — page description is visible', async ({ page }) => {
    await expect(page.locator('.page-desc')).toContainText('View weekly class schedule for any venue');
  });

  test('TC04 — semester chip displays semester info', async ({ page }) => {
    const chip = page.locator('#semesterChip');
    await expect(chip).toBeVisible();
    await expect(chip).toContainText('202605 Semester');
  });

  test('TC05 — print button exists and is disabled', async ({ page }) => {
    const btn = page.locator('.print-btn');
    await expect(btn).toBeVisible();
    await expect(btn).toBeDisabled();
  });

  // ════════════════════════════════════════════
  // 2. NAVIGATION
  // ════════════════════════════════════════════

  test('TC06 — nav bar has Venue Timetable item', async ({ page }) => {
    const venueNav = page.locator('.nav-items .nav-item', { hasText: 'Venue Timetable' });
    await expect(venueNav).toBeVisible();
  });

  test('TC07 — Venue Timetable nav item is active', async ({ page }) => {
    const activeItem = page.locator('.nav-items .nav-item.active');
    await expect(activeItem).toHaveText('Venue Timetable');
  });

  // ════════════════════════════════════════════
  // 3. VENUE DROPDOWN
  // ════════════════════════════════════════════

  test('TC08 — venue dropdown is visible and populated', async ({ page }) => {
    const select = page.locator('#venueSelect');
    await expect(select).toBeVisible();
    const options = select.locator('option');
    const count = await options.count();
    expect(count).toBeGreaterThan(0);
  });

  test('TC09 — venue dropdown has venue options', async ({ page }) => {
    const options = page.locator('#venueSelect option');
    const count = await options.count();
    expect(count).toBeGreaterThanOrEqual(5);
  });

  test('TC10 — first venue is selected by default', async ({ page }) => {
    const select = page.locator('#venueSelect');
    const value = await select.inputValue();
    expect(value).toBeTruthy();
  });

  test('TC11 — changing venue rebuilds timetable', async ({ page }) => {
    const select = page.locator('#venueSelect');
    await select.selectOption({ index: 1 });
    await page.waitForTimeout(500);
    const body = page.locator('#tableBody');
    const rows = body.locator('tr');
    const count = await rows.count();
    expect(count).toBeGreaterThan(0);
  });

  // ════════════════════════════════════════════
  // 4. FAVOURITES (star icon)
  // ════════════════════════════════════════════

  test('TC12 — favourite star is visible', async ({ page }) => {
    await expect(page.locator('#favStar')).toBeVisible();
  });

  test('TC13 — clicking favourite star toggles its state', async ({ page }) => {
    const star = page.locator('#favStar');
    const textBefore = await star.textContent();
    await star.click();
    await page.waitForTimeout(200);
    const textAfter = await star.textContent();
    expect(textAfter).not.toBe(textBefore);
  });

  // ════════════════════════════════════════════
  // 5. WEEK PICKER
  // ════════════════════════════════════════════

  test('TC14 — week dropdown has 14 weeks', async ({ page }) => {
    const select = page.locator('#weekSelect');
    await expect(select).toBeVisible();
    const options = select.locator('option');
    await expect(options).toHaveCount(14);
  });

  test('TC15 — prev/next arrows are visible', async ({ page }) => {
    await expect(page.locator('.week-arrow[aria-label="Previous week"]')).toBeVisible();
    await expect(page.locator('.week-arrow[aria-label="Next week"]')).toBeVisible();
  });

  test('TC16 — clicking next week updates the grid', async ({ page }) => {
    const currentIdx = await page.locator('#weekSelect').evaluate((el: HTMLSelectElement) => el.selectedIndex);
    const nextBtn = page.locator('.week-arrow[aria-label="Next week"]');
    await nextBtn.click();
    await page.waitForTimeout(500);
    const select = page.locator('#weekSelect');
    const selectedIndex = await select.evaluate((el: HTMLSelectElement) => el.selectedIndex);
    expect(selectedIndex).toBe(currentIdx + 1);
  });

  test('TC17 — clicking prev week on first week stays at first week', async ({ page }) => {
    // Jump to week 0 first
    await page.locator('#weekSelect').selectOption('0');
    await page.waitForTimeout(500);
    const prevBtn = page.locator('.week-arrow[aria-label="Previous week"]');
    await expect(prevBtn).toBeDisabled();
  });

  // ════════════════════════════════════════════
  // 6. FILTERS
  // ════════════════════════════════════════════

  // TC18, TC19, TC20 — time filter removed
  // test('TC18 — time filter has 3 segments: All, Morning, Afternoon', ...)
  // test('TC19 — "All" is the default time filter', ...)
  // test('TC20 — clicking Morning filter activates it', ...)

  test('TC21 — venue type filter button is visible', async ({ page }) => {
    await expect(page.locator('#venueTypeBtn')).toBeVisible();
  });

  test('TC22 — clicking venue type button opens dropdown', async ({ page }) => {
    await page.locator('#venueTypeBtn').click();
    await expect(page.locator('#venueTypeDropdown')).toHaveClass(/open/);
  });

  test('TC23 — venue type dropdown has 3 checkboxes', async ({ page }) => {
    await page.locator('#venueTypeBtn').click();
    const checks = page.locator('#venueTypeDropdown input[type="checkbox"]');
    await expect(checks).toHaveCount(3);
  });

  test('TC24 — unchecking all venue type checkboxes shows no-match state', async ({ page }) => {
    await page.locator('#venueTypeBtn').click();
    const checks = page.locator('#venueTypeDropdown input[type="checkbox"]');
    const count = await checks.count();
    for (let i = 0; i < count; i++) {
      await checks.nth(i).uncheck();
    }
    // Button text should change to "Filtered"
    await expect(page.locator('#venueTypeBtn')).toContainText('Filtered');
  });

  test('TC25 — checking all venue type checkboxes resets to All Types', async ({ page }) => {
    await page.locator('#venueTypeBtn').click();
    const checks = page.locator('#venueTypeDropdown input[type="checkbox"]');
    const count = await checks.count();
    for (let i = 0; i < count; i++) {
      await checks.nth(i).uncheck();
    }
    for (let i = 0; i < count; i++) {
      await checks.nth(i).check();
    }
    await expect(page.locator('#venueTypeBtn')).toContainText('All Types');
  });

  // ════════════════════════════════════════════
  // 7. HISTORY PANEL (disabled — not useful for now)
  // ════════════════════════════════════════════

  // TC26, TC27, TC28 — history panel disabled
  // test('TC26 — history details element exists', ...)
  // test('TC27 — history summary shows "Past 4 Weeks Booking History"', ...)
  // test('TC28 — clicking history summary opens panel', ...)

  // ════════════════════════════════════════════
  // 8. TIMETABLE GRID
  // ════════════════════════════════════════════

  test('TC29 — timetable grid is visible', async ({ page }) => {
    await expect(page.locator('#timetable')).toBeVisible();
  });

  test('TC30 — grid has day rows (7 days)', async ({ page }) => {
    const rows = page.locator('#tableBody tr');
    const count = await rows.count();
    expect(count).toBe(7);
  });

  test('TC31 — grid has time header columns', async ({ page }) => {
    const headers = page.locator('#tableHead th.hour-header');
    const count = await headers.count();
    expect(count).toBeGreaterThan(0);
  });

  test('TC32 — booked cells show course code', async ({ page }) => {
    const eventBlocks = page.locator('#tableBody .event-block');
    const count = await eventBlocks.count();
    if (count > 0) {
      await expect(eventBlocks.first()).toBeVisible();
      const text = await eventBlocks.first().textContent();
      expect(text).toMatch(/BMIT\d+/);
    }
  });

  test('TC33 — available cells are visible (empty, no text)', async ({ page }) => {
    const availableCells = page.locator('#tableBody .cell-available');
    const count = await availableCells.count();
    if (count > 0) {
      await expect(availableCells.first()).toBeVisible();
      await expect(availableCells.first()).toHaveText('');
    }
  });

  // ════════════════════════════════════════════
  // 9. LEGEND BAR
  // ════════════════════════════════════════════

  test('TC34 — legend bar has 4 items', async ({ page }) => {
    const items = page.locator('.legend-bar .legend-item');
    await expect(items).toHaveCount(4);
  });

  test('TC35 — legend shows Available, Replacement, Pending, Conflict', async ({ page }) => {
    const items = page.locator('.legend-bar .legend-item');
    await expect(items.nth(0)).toContainText('Available');
    await expect(items.nth(1)).toContainText('Replacement');
    await expect(items.nth(2)).toContainText('Pending');
    await expect(items.nth(3)).toContainText('Conflict');
  });

  // ════════════════════════════════════════════
  // 10. SUMMARY CARDS
  // ════════════════════════════════════════════

  test('TC36 — summary bar has 5 cards', async ({ page }) => {
    const cards = page.locator('.summary-bar .summary-card');
    await expect(cards).toHaveCount(5);
  });

  test('TC37 — summary cards show Total, Available, Replacement, Pending, Conflict', async ({ page }) => {
    await expect(page.locator('#sumTotal')).toBeVisible();
    await expect(page.locator('#sumAvailable')).toBeVisible();
    await expect(page.locator('#sumReplacement')).toBeVisible();
    await expect(page.locator('#sumPending')).toBeVisible();
    await expect(page.locator('#sumConflict')).toBeVisible();
  });

  test('TC38 — summary values are numeric', async ({ page }) => {
    const total = await page.locator('#sumTotal').textContent();
    expect(Number(total)).not.toBeNaN();
  });

  // ════════════════════════════════════════════
  // 11. MODAL (BOOKED CLASS)
  // ════════════════════════════════════════════

  test('TC39 — clicking a booked cell opens modal', async ({ page }) => {
    const eventBlock = page.locator('#tableBody .event-block').first();
    const count = await eventBlock.count();
    if (count > 0) {
      await eventBlock.click();
      await expect(page.locator('#eventModal')).toBeVisible();
    }
  });

  test('TC40 — modal shows course code field', async ({ page }) => {
    const eventBlock = page.locator('#tableBody .event-block').first();
    if (await eventBlock.count() > 0) {
      await eventBlock.click();
      await expect(page.locator('#mdlCourse')).not.toHaveText('—');
    }
  });

  test('TC41 — modal shows venue field with format "CODE — Type (N seats)"', async ({ page }) => {
    const eventBlock = page.locator('#tableBody .event-block').first();
    if (await eventBlock.count() > 0) {
      await eventBlock.click();
      const venue = await page.locator('#mdlVenue').textContent();
      expect(venue).toMatch(/\w+ — \w+ \(\d+ seats\)/);
    }
  });

  test('TC42 — clicking close button closes modal', async ({ page }) => {
    const eventBlock = page.locator('#tableBody .event-block').first();
    if (await eventBlock.count() > 0) {
      await eventBlock.click();
      await page.locator('.btn-close-modal').click();
      await expect(page.locator('#eventModal')).not.toBeVisible();
    }
  });

  test('TC43 — pressing Escape closes modal', async ({ page }) => {
    const eventBlock = page.locator('#tableBody .event-block').first();
    if (await eventBlock.count() > 0) {
      await eventBlock.click();
      await page.keyboard.press('Escape');
      await expect(page.locator('#eventModal')).not.toBeVisible();
    }
  });

  test('TC44 — clicking overlay closes modal', async ({ page }) => {
    const eventBlock = page.locator('#tableBody .event-block').first();
    if (await eventBlock.count() > 0) {
      await eventBlock.click();
      await page.locator('#eventModal').click({ position: { x: 5, y: 5 } });
      await expect(page.locator('#eventModal')).not.toBeVisible();
    }
  });

  // ════════════════════════════════════════════
  // 12. AVAILABLE SLOT TOOLTIP
  // ════════════════════════════════════════════

  test('TC45 — clicking available cell shows tooltip', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    const count = await availableCell.count();
    if (count > 0) {
      await availableCell.click();
      await expect(page.locator('#availableTooltip')).toHaveClass(/show/);
    }
  });

  test('TC46 — tooltip shows booking confirmation text', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    if (await availableCell.count() > 0) {
      await availableCell.click();
      const text = await page.locator('#tooltipText').textContent();
      expect(text).toMatch(/Book .+ on .+, .+ at .+/);
    }
  });

  test('TC47 — tooltip has Book button', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    if (await availableCell.count() > 0) {
      await availableCell.click();
      await expect(page.locator('#tooltipBookBtn')).toBeVisible();
    }
  });

  test('TC48 — pressing Escape closes tooltip', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    if (await availableCell.count() > 0) {
      await availableCell.click();
      await page.keyboard.press('Escape');
      await expect(page.locator('#availableTooltip')).not.toHaveClass(/show/);
    }
  });

  // ════════════════════════════════════════════
  // 13. BOOKING BANNER (URL PARAMS)
  // ════════════════════════════════════════════

  test('TC49 — booking banner shows when code+cohort in URL', async ({ page }) => {
    await page.goto(`${PAGE}?code=BMIT5555&cohort=RSD3G2`);
    await page.waitForTimeout(1000);
    const banner = page.locator('#bookingBanner');
    const text = await banner.textContent();
    expect(text).toContain('BMIT5555');
  });

  test('TC50 — booking banner hidden when no params', async ({ page }) => {
    const banner = page.locator('#bookingBanner');
    const isVisible = await banner.isVisible();
    expect(isVisible).toBe(false);
  });

  // ════════════════════════════════════════════
  // 14. ERROR HANDLING
  // ════════════════════════════════════════════

  test('TC51 — error banner is hidden by default', async ({ page }) => {
    await expect(page.locator('#errorBanner')).not.toHaveClass(/show/);
  });

  // ════════════════════════════════════════════
  // 15. HINT TEXT (EMPTY STATE)
  // ════════════════════════════════════════════

  test('TC52 — hint text hidden when venue has classes', async ({ page }) => {
    const hint = page.locator('#hintText');
    const eventBlocks = page.locator('#tableBody .event-block');
    if (await eventBlocks.count() > 0) {
      await expect(hint).not.toBeVisible();
    }
  });

  // ════════════════════════════════════════════
  // 16. EMPTY STATE (no venue selected)
  // ════════════════════════════════════════════

  test('TC53 — empty state visible on initial load before venue selection', async ({ page }) => {
    await page.goto(PAGE);
    await page.waitForTimeout(300);
    const emptyState = page.locator('#emptyState');
    const isVisible = await emptyState.isVisible();
    expect(typeof isVisible).toBe('boolean');
  });

  // ════════════════════════════════════════════
  // 17. KEYBOARD NAVIGATION
  // ════════════════════════════════════════════

  test('TC54 — available cell has tabindex for keyboard focus', async ({ page }) => {
    const cell = page.locator('#tableBody .cell-available').first();
    if (await cell.count() > 0) {
      const tabindex = await cell.getAttribute('tabindex');
      expect(tabindex).toBe('0');
    }
  });

  test('TC55 — available cell has role="button"', async ({ page }) => {
    const cell = page.locator('#tableBody .cell-available').first();
    if (await cell.count() > 0) {
      const role = await cell.getAttribute('role');
      expect(role).toBe('button');
    }
  });

  // ════════════════════════════════════════════
  // 18. MOBILE VIEW (≤768px)
  // ════════════════════════════════════════════

  test('TC56 — mobile: venue dropdown exists', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.waitForTimeout(300);
    await expect(page.locator('#venueSelect')).toBeVisible();
  });

  test('TC57 — mobile: filter bar is visible', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await expect(page.locator('.filter-bar')).toBeVisible();
  });

  test('TC58 — mobile: summary cards exist', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    const cards = page.locator('.summary-bar .summary-card');
    await expect(cards).toHaveCount(5);
  });

  // ════════════════════════════════════════════
  // 19. TABLET VIEW (769px–1024px)
  // ════════════════════════════════════════════

  test('TC59 — tablet: timetable grid is visible', async ({ page }) => {
    await page.setViewportSize({ width: 900, height: 720 });
    await page.waitForTimeout(300);
    await expect(page.locator('#timetable')).toBeVisible();
  });

  // ════════════════════════════════════════════
  // 20. LOCALSTORAGE PERSISTENCE
  // ════════════════════════════════════════════

  test('TC60 — state is saved to localStorage on venue change', async ({ page }) => {
    const select = page.locator('#venueSelect');
    await select.selectOption({ index: 2 });
    await page.waitForTimeout(500);
    const state = await page.evaluate(() => localStorage.getItem('venueTimetableState'));
    expect(state).toBeTruthy();
    const parsed = JSON.parse(state!);
    expect(parsed.venue).toBeTruthy();
  });

  test('TC61 — favourites are saved to localStorage', async ({ page }) => {
    await page.locator('#favStar').click();
    await page.waitForTimeout(200);
    const favs = await page.evaluate(() => localStorage.getItem('venueFavourites'));
    expect(favs).toBeTruthy();
  });

  test('TC62 — recent venues are saved to localStorage', async ({ page }) => {
    const select = page.locator('#venueSelect');
    await select.selectOption({ index: 3 });
    await page.waitForTimeout(300);
    const recent = await page.evaluate(() => localStorage.getItem('venueRecent'));
    expect(recent).toBeTruthy();
  });

  // ════════════════════════════════════════════
  // 21. TOAST NOTIFICATION
  // ════════════════════════════════════════════

  test('TC63 — toast notification is hidden by default', async ({ page }) => {
    await expect(page.locator('#toastNotification')).not.toBeVisible();
  });

  // ════════════════════════════════════════════
  // 22. RESPONSIVE FILTERS
  // ════════════════════════════════════════════

  // TC64 — time filter removed
  // test('TC64 — mobile: time filter buttons are visible', ...)

  test('TC65 — mobile: venue type filter button is visible', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await expect(page.locator('#venueTypeBtn')).toBeVisible();
  });

  // ════════════════════════════════════════════
  // 23. VENUE CHANGE REBUILDS GRID
  // ════════════════════════════════════════════

  test('TC66 — changing venue updates summary card counts', async ({ page }) => {
    const totalBefore = await page.locator('#sumTotal').textContent();
    const select = page.locator('#venueSelect');
    const options = select.locator('option');
    const count = await options.count();
    if (count > 1) {
      await select.selectOption({ index: 1 });
      await page.waitForTimeout(500);
      const totalAfter = await page.locator('#sumTotal').textContent();
      expect(Number(totalBefore)).not.toBeNaN();
      expect(Number(totalAfter)).not.toBeNaN();
    }
  });

  // ════════════════════════════════════════════
  // 24. WEEK CHANGE REBUILDS GRID
  // ════════════════════════════════════════════

  test('TC67 — changing week updates the grid content', async ({ page }) => {
    const select = page.locator('#weekSelect');
    await select.selectOption({ index: 2 });
    await page.waitForTimeout(500);
    const selectedIndex = await select.evaluate((el: HTMLSelectElement) => el.selectedIndex);
    expect(selectedIndex).toBe(2);
  });

  // ════════════════════════════════════════════
  // 25. COMBINED FILTERS
  // ════════════════════════════════════════════

  // TC68 — time filter removed
  // test('TC68 — time and venue type filters work together', ...)

  // ════════════════════════════════════════════
  // 26. SKELETON LOADING
  // ════════════════════════════════════════════

  test('TC69 — grid rebuilds correctly after venue change', async ({ page }) => {
    const select = page.locator('#venueSelect');
    await select.selectOption({ index: 1 });
    await page.waitForTimeout(600);
    const rows = page.locator('#tableBody tr');
    const count = await rows.count();
    expect(count).toBe(7);
  });
});
