import { test, expect } from '@playwright/test';

const BASE = 'http://localhost:8000';

test.describe('Staff ID Optional P Prefix — Slim UI Fix', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(`${BASE}/login/staff`, { waitUntil: 'networkidle' });
  });

  test('placeholder and hint', async ({ page }) => {
    await expect(page.locator('#login_id')).toHaveAttribute('placeholder', 'e.g. P5425 or 5425');
    await expect(page.locator('.format-hint')).toHaveText('4 digits, optional "P" prefix');
    const content = await page.content();
    // HTML contains JSON-escaped \\d, so match with one or more backslashes
    expect(content).toMatch(/\^P\?\\+d\{4\}\$/);
    // also check JS RegExp object exists (const at top level, not window.idRegex)
    const hasRegExp = await page.evaluate(() => typeof idRegex !== 'undefined' && idRegex instanceof RegExp);
    expect(hasRegExp).toBe(true);
    const reStr = await page.evaluate(() => idRegex.toString());
    expect(reStr).toBe('/^P?\\d{4}$/');
  });

  // Helper to fill and check button disabled state
  async function check(page: any, id: string, pw: string, shouldBeEnabled: boolean) {
    await page.fill('#login_id', '');
    await page.fill('#password', '');
    await page.fill('#login_id', id);
    await page.fill('#password', pw);
    // trigger validateLogin via input event (fill does) + also call directly
    await page.evaluate(() => (window as any).validateLogin?.());
    const disabled = await page.locator('#loginBtn').isDisabled();
    expect(disabled).toBe(!shouldBeEnabled);
  }

  test('5425 + pw => enabled', async ({ page }) => { await check(page, '5425', 'Tarumt@2026', true); });
  test('P5425 + pw => enabled', async ({ page }) => { await check(page, 'P5425', 'Tarumt@2026', true); });
  test('p5425 lower => disabled', async ({ page }) => { await check(page, 'p5425', 'Tarumt@2026', false); });
  test('542 3digits => disabled', async ({ page }) => { await check(page, '542', 'Tarumt@2026', false); });
  test('54255 5digits => disabled', async ({ page }) => { await check(page, '54255', 'Tarumt@2026', false); });
  test('P542 3digits with P => disabled', async ({ page }) => { await check(page, 'P542', 'Tarumt@2026', false); });
  test('PP5425 => disabled', async ({ page }) => { await check(page, 'PP5425', 'Tarumt@2026', false); });
  test(' spaces trim => enabled', async ({ page }) => { await check(page, ' 5425 ', 'Tarumt@2026', true); });
  test('empty id => disabled', async ({ page }) => { await check(page, '', 'Tarumt@2026', false); });
  test('valid id empty pw => disabled', async ({ page }) => { await check(page, '5425', '', false); });
  test('P5425 trim with spaces => enabled', async ({ page }) => { await check(page, ' P5425 ', 'Tarumt@2026', true); });

  test('student control remains', async ({ page }) => {
    await page.goto(`${BASE}/login/student`, { waitUntil: 'networkidle' });
    const content = await page.content();
    expect(content).toMatch(/\^\\+d\{2\}\[A-Za-z\]\{3\}\\+d\{4\}\$/);
    expect(content).toContain('25RSD0001');
    await expect(page.locator('#login_id')).toHaveAttribute('placeholder', 'e.g. 25RSD0001');
    // 25RSD0001 => enabled, P5425 => disabled on student page
    await page.fill('#login_id', '25RSD0001');
    await page.fill('#password', 'Tarumt@2026');
    await page.evaluate(() => (window as any).validateLogin?.());
    expect(await page.locator('#loginBtn').isDisabled()).toBe(false);
    await page.fill('#login_id', 'P5425');
    await page.evaluate(() => (window as any).validateLogin?.());
    expect(await page.locator('#loginBtn').isDisabled()).toBe(true);
  });

  test('JS console test matrix', async ({ page }) => {
    const results = await page.evaluate(() => {
      const re = new RegExp("^P?\\d{4}$");
      return {
        a: re.test("P5425"),
        b: re.test("5425"),
        c: re.test("p5425"),
        d: re.test("542"),
        e: re.test("54255"),
      };
    });
    expect(results).toEqual({ a: true, b: true, c: false, d: false, e: false });
  });
});
