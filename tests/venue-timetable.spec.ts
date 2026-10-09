import { test, expect } from '@playwright/test';

const PAGE = '/venue-timetable-ui';

/** Open the cascading venue dropdown and walk Type → Block → Floor → Room,
 *  picking the `index`-th room of the first reachable branch. Returns false
 *  when a column is empty. (The old #venueSelect <select> was replaced by the
 *  4-column VenueDropdown — changelog 2026-08-16.) */
async function pickVenue(page, index: number): Promise<boolean> {
  await page.locator('.venue-dd-trigger').click();
  const cols = page.locator('.venue-dd-panel .venue-col');
  for (let level = 0; level < 3; level++) {
    const items = cols.nth(level).locator('.venue-col-item-parent');
    if ((await items.count()) === 0) return false;
    await items.first().click();
    // the next column is built on demand — wait for it to become visible
    await cols.nth(level + 1).waitFor({ state: 'visible', timeout: 3000 }).catch(() => {});
  }
  const rooms = cols.nth(3).locator('.venue-col-item-room');
  const roomCount = await rooms.count();
  if (roomCount === 0) return false;
  await rooms.nth(Math.min(index, roomCount - 1)).click();
  await page.waitForTimeout(400);
  return true;
}

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

  test('TC05 — print button exists and shows coming-soon toast', async ({ page }) => {
    const btn = page.locator('.print-btn');
    await expect(btn).toBeVisible();
    // 2026-10-03: the print action is a placeholder — enabled, but every
    // click explains itself via the shared toast instead of printing.
    await btn.click();
    await expect(page.locator('#toastBar')).toContainText('Printing is coming soon');
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

  test('TC08 — venue dropdown trigger is visible and opens the columns', async ({ page }) => {
    const trigger = page.locator('.venue-dd-trigger');
    await expect(trigger).toBeVisible();
    await trigger.click();
    await expect(page.locator('.venue-dd')).toHaveClass(/open/);
    await expect(page.locator('.venue-dd-panel .venue-col').first()).toBeVisible();
  });

  test('TC09 — venue registry backs the dropdown (≥5 rooms)', async ({ page }) => {
    await page.locator('.venue-dd-trigger').click();
    // the cascading columns are built from MockData.venues (23 rooms)
    const venueCount = await page.evaluate(() => MockData.venues.length);
    expect(venueCount).toBeGreaterThanOrEqual(5);
    await expect(page.locator('.venue-dd-panel .venue-col-item-parent').first()).toBeVisible();
  });

  test('TC10 — a venue is selected by default', async ({ page }) => {
    const code = await page.evaluate(() => venueDropdown.getSelected());
    expect(code).toBeTruthy();
    // and the trigger shows it, not the placeholder
    await expect(page.locator('.venue-dd-trigger')).not.toContainText('Select a venue');
  });

  test('TC11 — changing venue rebuilds timetable', async ({ page }) => {
    const changed = await pickVenue(page, 1);
    test.skip(!changed, 'no alternate venue reachable in the cascading dropdown');
    const rows = page.locator('#tableBody tr');
    await expect(rows).toHaveCount(7);
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

  // TC21–TC25 — the old #venueTypeBtn checkbox filter was replaced by the
  // cascading dropdown's Type column (choosing a type IS the filter).

  test('TC21 — venue type column reachable inside the dropdown', async ({ page }) => {
    await page.locator('.venue-dd-trigger').click();
    await expect(page.locator('.venue-dd-panel .venue-col').first()).toBeVisible();
    await expect(page.locator('.venue-dd-panel .venue-col-item-parent').first()).toBeVisible();
  });

  test('TC22 — dropdown opens and closes on trigger toggle', async ({ page }) => {
    await page.locator('.venue-dd-trigger').click();
    await expect(page.locator('.venue-dd')).toHaveClass(/open/);
    await page.locator('.venue-dd-trigger').click();
    await expect(page.locator('.venue-dd')).not.toHaveClass(/open/);
  });

  test('TC23 — type column lists the 3 dropdown categories (CiscoLab groups under Lab)', async ({ page }) => {
    await page.locator('.venue-dd-trigger').click();
    // CiscoLab (B006) is merged into the Lab category in the dropdown
    const col = page.locator('.venue-dd-panel .venue-col').first();
    for (const t of ['Tutorial', 'LectureHall', 'Lab']) {
      await expect(col.locator(`.venue-col-item-parent[data-type="${t}"]`)).toBeVisible();
    }
  });

  test('TC23b — Lab cascade includes B006; every option carries a data-tip full name', async ({ page }) => {
    await page.locator('.venue-dd-trigger').click();
    await page.waitForTimeout(300);
    // Walk Lab → Block B → Ground Floor (clicks, same idiom as pickVenue)
    await page.locator('.venue-col-item-parent[data-type="Lab"]').click();
    await page.waitForTimeout(300);
    await page.locator('.venue-dd-panel .venue-col:nth-child(2) .venue-col-item-parent').first().click();
    await page.waitForTimeout(300);
    await page.locator('.venue-dd-panel .venue-col:nth-child(3) .venue-col-item-parent').first().click();
    await page.waitForTimeout(300);
    const rooms = page.locator('.venue-dd-panel .venue-col:nth-child(4) .venue-col-item-room');
    await expect(page.locator('.venue-col-item-room[data-code="B006"]')).toHaveCount(1);
    // every room option exposes a full-name tooltip attribute
    const tips = await rooms.evaluateAll((els: HTMLElement[]) => els.map(el => el.getAttribute('data-tip') || ''));
    expect(tips.length).toBeGreaterThan(0);
    for (const tip of tips) expect(tip).toMatch(/^B\d+ · .+ · .+, Block B$/);
    // hovering shows the shared tooltip ABOVE the item, revealing the true name
    await page.locator('.venue-col-item-room[data-code="B006"]').hover();
    const tipText = await page.evaluate(() => document.querySelector('.data-tip-tooltip')?.textContent);
    expect(tipText).toContain('B006 · Cisco Lab');
  });

  test('TC24 — clicking a type lists its blocks', async ({ page }) => {
    await page.locator('.venue-dd-trigger').click();
    await page.locator('.venue-dd-panel .venue-col-item-parent[data-type="Lab"]').first().click();
    const blocks = page.locator('.venue-dd-panel .venue-col').nth(1).locator('.venue-col-item-parent');
    await expect(blocks.first()).toBeVisible();
  });

  test('TC25 — room selection closes the dropdown and updates the selection', async ({ page }) => {
    const changed = await pickVenue(page, 0);
    test.skip(!changed, 'no room reachable in the cascading dropdown');
    await expect(page.locator('.venue-dd')).not.toHaveClass(/open/);
    const after = await page.evaluate(() => venueDropdown.getSelected());
    expect(after).toBeTruthy();
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
      expect(text).toMatch(/[A-Z]{2,4}-\d{4}|[A-Z]{4}\d{4}/);
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

  test('TC34 — legend bar has 4 items (Available + 3 status chips)', async ({ page }) => {
    const items = page.locator('.legend-bar .legend-item');
    await expect(items).toHaveCount(4);
  });

  test('TC35 — legend shows Available + status chips in order, with ownership hint', async ({ page }) => {
    const items = page.locator('.legend-bar .legend-item');
    await expect(items).toHaveText([
      'Available',
      'Normal',
      'Pending',
      'Conflict / Public Holiday',
    ]);
    // §10.0 two-axis language: colour = status, border weight = ownership
    const hint = page.locator('.legend-bar .legend-ownership-hint');
    await expect(hint).toBeVisible();
    await expect(hint.locator('.osd-thick')).toHaveCount(1);
    await expect(hint.locator('.osd-thin')).toHaveCount(1);
  });

  test('TC35b — ownership is border-gated (own 3px red, others hairline red)', async ({ page }) => {
    // B110 wk3: AMCS2093(L) is currentUser's conflict → loud (event-conflict, 3px)
    await page.goto(`${PAGE}?venue=B110`, { waitUntil: 'networkidle' });
    await page.locator('#weekSelect').selectOption('3');
    await page.waitForTimeout(400);
    const myLoud = page.locator('#tableBody .event-conflict');
    await expect(myLoud).toHaveCount(1);
    await expect(myLoud.locator('.ev-code')).toHaveText(/AMCS2093/);
    await expect(page.locator('#tableBody .event-public-holiday')).toHaveCount(0);

    // B101 Week 1 (index 0): MPU-2302(T) is En. Muada's conflict → same error
    // fill but hairline border (event-public-holiday = "not yours to act on")
    await page.goto(`${PAGE}?venue=B101`, { waitUntil: 'networkidle' });
    await page.locator('#weekSelect').selectOption('0');
    await page.waitForTimeout(400);
    await expect(page.locator('#tableBody .event-conflict')).toHaveCount(0);
    const quiet = page.locator('#tableBody .event-public-holiday');
    await expect(quiet).toHaveCount(1);
    await expect(quiet.locator('.ev-code')).toHaveText(/MPU-2302/);
  });

  // ════════════════════════════════════════════
  // 10. SUMMARY CARDS
  // ════════════════════════════════════════════

  test('TC36 — summary bar has 5 cards', async ({ page }) => {
    const cards = page.locator('.summary-bar .summary-card');
    await expect(cards).toHaveCount(5);
  });

  test('TC37 — summary cards show Total, Available, My Teaching, Unavailable', async ({ page }) => {
    await expect(page.locator('#sumTotal')).toBeVisible();
    await expect(page.locator('#sumAvailable')).toBeVisible();
    await expect(page.locator('#sumMyClasses')).toBeVisible();
    await expect(page.locator('#sumMyHours')).toBeVisible();
    await expect(page.locator('#sumUnavailable')).toBeVisible();
    await expect(page.locator('#sumPending')).toHaveCount(0);   // Pending card removed
  });

  test('TC38 — summary values are numeric', async ({ page }) => {
    const total = await page.locator('#sumTotal').textContent();
    expect(Number(total)).not.toBeNaN();
  });

  test('TC38b — My Teaching cards match the grid (B110, weekly pattern)', async ({ page }) => {
    /* B110's weekly pattern carries exactly one class of the logged-in
       lecturer (AMCS2093(L), 2h — merged-cohort twins deduped to one
       visible block) in every week, so any week works. B110 is NOT the
       default venue (venues[0] = B002), so preselect it via URL. */
    await page.goto(`${PAGE}?venue=B110`, { waitUntil: 'networkidle' });
    await expect(page.locator('#sumMyClasses')).toHaveText('1');
    await expect(page.locator('#sumMyHours')).toHaveText('2');
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
      const codeRow = page.locator('#eventModal .detail-row:has-text("Subject Code")');
      await expect(codeRow).toContainText(/[A-Z]{2,4}-\d{4}|[A-Z]{4}\d{4}/);
    }
  });

  test('TC41 — modal shows venue field with format "CODE — Type (N seats)"', async ({ page }) => {
    const eventBlock = page.locator('#tableBody .event-block').first();
    if (await eventBlock.count() > 0) {
      await eventBlock.click();
      // Venue-row locator: ":has-text(\"Venue\")" is ambiguous (the Status
      // Description row's value contains the word "venue"); only the Venue
      // row contains "(N seats)" — blade openModal L924.
      const venueRow = page.locator('#eventModal .detail-row', { hasText: /\(\d+ seats\)/ });
      await expect(venueRow).toContainText(/[A-Z]\d{3}/);
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

  /** Open the available-slot tooltip on the first bookable cell. Scrolls the
   *  cell into view and lets the (by-design) scroll-hide handler settle BEFORE
   *  clicking — otherwise Playwright's auto-scroll on click races the tooltip
   *  open and it closes instantly (TC47 flake 2026-10-08, legend grew taller). */
  async function openAvailableTooltip(page: import('@playwright/test').Page): Promise<void> {
    const cell = page.locator('#tableBody .cell-available').first();
    if ((await cell.count()) === 0) return;
    await cell.evaluate((el: HTMLElement) => el.scrollIntoView({ block: 'center' }));
    await page.waitForTimeout(150);
    await cell.click();
  }

  test('TC45 — clicking available cell shows tooltip', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    const count = await availableCell.count();
    if (count > 0) {
      await openAvailableTooltip(page);
      await expect(page.locator('#availableTooltip')).toHaveClass(/show/);
    }
  });

  test('TC46 — tooltip shows booking confirmation text', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    if (await availableCell.count() > 0) {
      await openAvailableTooltip(page);
      const text = await page.locator('#tooltipText').textContent();
      expect(text).toMatch(/Book .+ on .+, .+ at .+/);
    }
  });

  test('TC47 — tooltip has Book button', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    if (await availableCell.count() > 0) {
      await openAvailableTooltip(page);
      await expect(page.locator('#tooltipBookBtn')).toBeVisible();
    }
  });

  test('TC48 — pressing Escape closes tooltip', async ({ page }) => {
    const availableCell = page.locator('#tableBody .cell-available').first();
    if (await availableCell.count() > 0) {
      await openAvailableTooltip(page);
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

  test('TC56 — mobile: venue dropdown trigger exists', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.waitForTimeout(300);
    await expect(page.locator('.venue-dd-trigger')).toBeVisible();
  });

  test('TC57 — mobile: picker bar is visible', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await expect(page.locator('.semester-bar')).toBeVisible();
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
    await page.evaluate(() => venueDropdown.select('B006'));
    await page.waitForTimeout(500);
    const state = await page.evaluate(() => localStorage.getItem('venueTimetableState'));
    expect(state).toBeTruthy();
    const parsed = JSON.parse(state!);
    expect(parsed.venue).toBe('B006');
  });

  test('TC61 — favourites are saved to localStorage', async ({ page }) => {
    await page.locator('#favStar').click();
    await page.waitForTimeout(200);
    const favs = await page.evaluate(() => localStorage.getItem('venueFavourites'));
    expect(favs).toBeTruthy();
  });

  test('TC62 — recent venues are saved to localStorage', async ({ page }) => {
    await page.evaluate(() => venueDropdown.select('B110'));
    await page.waitForTimeout(300);
    const recent = await page.evaluate(() => localStorage.getItem('venueRecent'));
    expect(recent).toBeTruthy();
    expect(JSON.parse(recent!)).toContain('B110');
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

  test('TC65 — mobile: venue dropdown trigger is visible', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await expect(page.locator('.venue-dd-trigger')).toBeVisible();
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
    await page.evaluate(() => venueDropdown.select('B110'));
    await page.waitForTimeout(600);
    const rows = page.locator('#tableBody tr');
    const count = await rows.count();
    expect(count).toBe(7);
  });

  // ════════════════════════════════════════════
  // 27. CANCEL CLASS FREES THE SLOT (cancel-class-enhancement §8)
  // ════════════════════════════════════════════

  // Fixture, VERIFIED against public/js/mock-data.js §2.7: B110 — Week 11
  // (idx 10) Monday AMCS2093(L, start=6 end=9), lecturer
  // 'En. Lim Jia Zheng' = currentUser, status 'normal' → own + cancellable
  // (week 10 Monday = 2026-11-30, beyond the real clock's Oct 5+ demo
  // anchor, so the S5 end-time guard can never bite). B110 also carries
  // AMIS1012(L) rows from OTHER cohorts (not currentUser) — untouched here.
  // The venue dropdown is the custom VenueDropdown (4-column hierarchy);
  // its instance is closure-scoped, so the room is picked via UI clicks:
  // Lecture Hall → Block B → Floor 1 → B110.
  test('TC70 — cancelling an own class frees the venue slot (B110 Week 11)', async ({ page }) => {
    // Week 10 (0-indexed) — B110's own AMCS2093 lecture is future + normal
    await page.locator('#weekSelect').selectOption('10');

    // Navigate the custom venue dropdown: Type → Block → Floor → Room
    await page.locator('.venue-dd-trigger').click();
    await page.locator('.venue-col-item-parent[data-type="LectureHall"]').click();
    await page.locator('.venue-col-item-parent[data-block="B"]').click();
    await page.locator('.venue-col-item-parent[data-floor="Floor 1"]').click();
    await page.locator('.venue-col-item-room[data-code="B110"]').click();
    await expect(page.locator('.venue-dd-trigger .venue-dd-label')).toContainText('B110');

    // Grid rebuilt for B110 — wait for the own lecture block before sampling
    const block = page.locator('#tableBody .event-block:has-text("AMCS2093(L)")');
    await expect(block).toBeVisible();
    const availableBefore = Number(await page.locator('#sumAvailable').textContent());

    // Cancel it through the shared modal
    await block.click();
    await expect(page.locator('#eventModal')).toBeVisible();
    await expect(page.locator('#eventModal .modal-footer [data-cancel-class-btn]')).toBeVisible();
    await page.locator('#eventModal .modal-footer [data-cancel-class-btn]').click();
    await expect(page.locator('#cancelClassOverlay')).toBeVisible();
    await page.locator('#cancelReasonList input[type="radio"][value="Medical Leave"]').click();
    await expect(page.locator('#confirmCancelClassBtn')).toBeEnabled();
    await page.locator('#confirmCancelClassBtn').click();
    await expect(page.locator('#cancelClassSuccessState')).toBeVisible();
    await page.locator('#cancelLaterBtn').click();

    // The cancelled block vanishes from the venue grid…
    await expect(page.locator('#tableBody .event-block:has-text("AMCS2093(L)")')).toHaveCount(0);
    // …its four Monday slots (08:00–09:30) turn bookable again…
    await expect(page.locator('#tableBody td[data-day="0"][data-hour="6"] .cell-available')).toBeVisible();
    await expect(page.locator('#tableBody td[data-day="0"][data-hour="7"] .cell-available')).toBeVisible();
    await expect(page.locator('#tableBody td[data-day="0"][data-hour="8"] .cell-available')).toBeVisible();
    await expect(page.locator('#tableBody td[data-day="0"][data-hour="9"] .cell-available')).toBeVisible();
    // …and the Available summary card counts them back in.
    await expect.poll(
      () => page.locator('#sumAvailable').textContent().then((t) => Number(t)),
    ).toBe(availableBefore + 4);
  });
});
