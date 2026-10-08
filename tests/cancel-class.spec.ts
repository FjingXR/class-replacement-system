import { test, expect } from '@playwright/test';

// ═══════════════════════════════════════════════════════════════════════
// cancel-class-enhancement (design §8) — Cancel Class flow coverage.
//
// Fixture keys, VERIFIED against public/js/mock-data.js (§2.6 myTimetable,
// §2.7 cohortTimetable):
//   - Own future cancellable (S1/S7–S11):
//       eventsByWeek[10] di=1 start=12 end=15 BMIT2013 L B009 lecturer
//       'En. Lim Jia Zheng' status 'normal' (all week-10 rows are normal —
//       week 10 Monday = 2026-11-30, safely beyond the real clock, whose
//       date is later than the mockNow Oct-5 demo anchor).
//   - Own pending (S3): eventsByWeek[9] di=2 start=12 end=13 AMCS2093 T
//       B106 status 'pending'.
//   - Own ended (S5): eventsByWeek[0] di=0 AMCS2093 L B110 — Week 1
//       Monday 2026-09-21, long past the real clock.
//   - Others' class (S2): cohort dft2s1 base di=0 start=2 AMIT2014 L B016,
//       lecturer 'Cik Ellis Chieng' ≠ currentUser.
//   - Seeded ledger entry (S12/S13b/S14): eventsByWeek[0] di=3 start=13
//       AMCS2093 T B107 (own, plain 'normal') → twin cohort dft2s1 di=3
//       start=13 AMCS2093 T B107 resolves too.
//   - Venue own class (tests/venue-timetable.spec.ts TC70): B110 week 10
//       Monday AMCS2093 L start=6 end=9 (lecturer own, 'normal').
//
// Real-clock note (S5 vs S1+): the mock phase pins mockNow to Mon 5 Oct
// 2026, but the browser clock is later — every cancellable fixture uses
// week indices ≥ 9 so the S5 end-time check can never pass by accident.
// ═══════════════════════════════════════════════════════════════════════

const MY = '/my-timetable-ui';
const COHORT = '/cohort-timetable-ui';
const HOME = '/replacement-home-ui';
const ARRANGE = '/replacement-arrangement';
const LEDGER_KEY = 'classCancellationLedger';
const ME = 'En. Lim Jia Zheng';

// The seeded representation of an AMCS2093 T · B107 cancellation (Thurs
// of week 1 = 2026-09-24 via ClassCancellation.isoDate(0, 3)). row.date is
// deliberately '2026-09-14' so replacement-home's date-ascending sort
// (10 rows/page) keeps the row — and its chip — on pagination page 1.
function seedEntry() {
  return {
    id: 'cxl-seed-1',
    matchKey: { week: 0, code: 'AMCS2093', di: 3, start: 13, lecturer: ME },
    reason: 'Medical Leave',
    detail: '',
    cancelledAt: '2026-10-05T09:00:00.000Z',
    row: {
      id: 99,
      code: 'AMCS2093',
      name: 'Operating Systems',
      type: 'T',
      date: '2026-09-14',
      day: 'Monday',
      timeStart: '13:00',
      timeEnd: '14:00',
      duration: 1,
      venue: 'B107',
      totalStudents: 28,
      cohorts: ['DFT2 (S1)'],
      conflictReason: 'Medical Leave',
    },
    chip: true,
  };
}

test.describe('Cancel Class Flow', () => {
  test.beforeEach(async ({ page }) => {
    // Fresh storage for every test (serial file, one fresh context each) —
    // cancels mutate the in-memory MockData per page load anyway, but the
    // sessionStorage ledger + localStorage week persistence must not bleed.
    // ONE-TIME per test (marker key): a per-navigation .clear() would wipe a
    // ledger the test itself seeded, then the seed-if-absent script would
    // resurrect it (S13b/S14 would replay the un-mutated entry forever).
    await page.addInitScript(() => {
      try {
        if (!sessionStorage.getItem('__pwTestInitDone')) {
          localStorage.clear();
          sessionStorage.clear();
          sessionStorage.setItem('__pwTestInitDone', '1');
        }
      } catch (e) { /* §10 degrade */ }
    });
  });

  // ── S1 — guard: own class shows the Cancel Class? button ──
  test('S1 — own normal future class shows the Cancel Class? button (my-timetable)', async ({ page }) => {
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('10');
    // Week 10 students' Web-Based Integrated Systems — own + normal + future
    const block = page.locator('#tableBody .event-block:has-text("BMIT2013(L)")');
    await expect(block).toBeVisible();
    await block.click();
    await expect(page.locator('#classModal')).toBeVisible();
    await expect(page.locator('#classModal .modal-footer [data-cancel-class-btn]')).toBeVisible();
  });

  // ── S2 — guard: others' class has no cancel button ──
  test("S2 — another lecturer's class shows no cancel button (cohort)", async ({ page }) => {
    await page.goto(COHORT);
    await page.locator('#facultySelect').selectOption('focs');
    await page.locator('#cohortSelect').selectOption('dft2s1');
    // AMIT2014 L (B016, Mon) belongs to Cik Ellis Chieng — not currentUser.
    // The cohort-change lands on the mockNow week (idx 2) where the base
    // block exists unmodified.
    const block = page.locator('#tableBody .event-block:has-text("AMIT2014(L)")');
    await expect(block).toBeVisible();
    await block.click();
    await expect(page.locator('#eventModal')).toBeVisible();
    await expect(page.locator('#eventModal .modal-footer [data-cancel-class-btn]')).toHaveCount(0);
  });

  // ── S3 — guard: own pending block has no cancel button ──
  test('S3 — own pending class has no cancel button (my-timetable)', async ({ page }) => {
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('9');
    // Week 9 Wed 13:30 OS tutorial B106 — status 'pending', lecturer is the
    // viewer (exercises the S3 status guard, not the S2 ownership one).
    const block = page.locator('#tableBody .event-block:has-text("AMCS2093(T)")')
      .filter({ hasText: 'B106' });
    await expect(block).toBeVisible();
    await block.click();
    await expect(page.locator('#classModal')).toBeVisible();
    await expect(page.locator('#classModal .modal-footer [data-cancel-class-btn]')).toHaveCount(0);
  });

  // ── S5 — guard: own class whose end datetime has passed ──
  test('S5 — own class whose end time has passed shows no cancel button (my-timetable, week 1)', async ({ page }) => {
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('0');
    // Week 1 Monday 21 Sep 2026 — the real browser clock (Oct 7+) is long past.
    const block = page.locator('#tableBody .event-block:has-text("AMCS2093(L)")');
    await expect(block).toBeVisible();
    await block.click();
    await expect(page.locator('#classModal')).toBeVisible();
    await expect(page.locator('#classModal .modal-footer [data-cancel-class-btn]')).toHaveCount(0);
  });

  // ── S7/S8 — Yes-button gating in the shared cancel modal ──
  test('S7/S8 — Yes gated until a reason is picked; "Other" requires detail (my-timetable)', async ({ page }) => {
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('10');
    const block = page.locator('#tableBody .event-block:has-text("BMIT2013(L)")');
    await expect(block).toBeVisible();
    await block.click();
    const btn = page.locator('#classModal .modal-footer [data-cancel-class-btn]');
    await expect(btn).toBeVisible();
    await btn.click();

    const overlay = page.locator('#cancelClassOverlay');
    await expect(overlay).toBeVisible();
    const yes = page.locator('#confirmCancelClassBtn');
    const hint = page.locator('#cancelReasonHint');

    // S7 — no reason yet: Yes disabled + inline hint
    await expect(yes).toBeDisabled();
    await expect(hint).toHaveText('Please select a cancellation reason.');

    // Pick a real reason → enabled
    await page.locator('#cancelReasonList input[type="radio"][value="Medical Leave"]').click();
    await expect(yes).toBeEnabled();
    await expect(hint).toHaveText('');

    // S8 — switch to "Other": textarea appears; empty detail re-disables
    await page.locator('#cancelReasonList input[type="radio"][value="Other"]').click();
    const detail = page.locator('#cancelOtherDetail');
    await expect(detail).toBeVisible();
    await expect(yes).toBeDisabled();
    await detail.fill('Guest lecture clash — faculty seminar');
    await expect(yes).toBeEnabled();
  });

  // ── S9 (+S11) — happy path: block vanishes, ledger written, Later path ──
  test('S9/S11 — confirm cancels the class: block gone, ledger written, Later keeps page + undo toast', async ({ page }) => {
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('10');
    const block = page.locator('#tableBody .event-block:has-text("BMIT2013(L)")');
    await expect(block).toBeVisible();
    await block.click();
    await page.locator('#classModal .modal-footer [data-cancel-class-btn]').click();
    await expect(page.locator('#cancelClassOverlay')).toBeVisible();

    await page.locator('#cancelReasonList input[type="radio"][value="Medical Leave"]').click();
    await page.locator('#confirmCancelClassBtn').click();

    // Success state carries the message (the confirm path never double-toasts)
    await expect(page.locator('#cancelClassSuccessState')).toBeVisible();
    await expect(page.locator('#cancelClassSuccessState')).toContainText('✓ Class cancelled');
    await expect(page.locator('#cancelClassSummary')).toContainText('BMIT2013');

    // S11 — "I'll Do It Later": no navigation; grid rebuilds; undo toast
    await page.locator('#cancelLaterBtn').click();
    await expect(page).toHaveURL(/\/my-timetable-ui/);  // stayed on the page
    await expect(page.locator('#cancelClassOverlay')).toBeHidden();
    await expect(page.locator('#tableBody .event-block:has-text("BMIT2013(L)")')).toHaveCount(0);
    // Summary cards treat the slot as free too (design §4)
    await expect(page.locator('#sumTotal')).toHaveText('6');
    // S11 undo toast with visible Undo button
    await expect(page.locator('#toastBar .toast-undo')).toBeVisible();

    // S9 — the ledger entry is written to sessionStorage
    const ledger = await page.evaluate(
      (key) => JSON.parse(sessionStorage.getItem(key) || '[]'), LEDGER_KEY,
    );
    expect(ledger).toHaveLength(1);
    expect(ledger[0].matchKey).toEqual({
      week: 10, code: 'BMIT2013', di: 1, start: 12, lecturer: ME,
    });
    expect(ledger[0].reason).toBe('Medical Leave');
    expect(ledger[0].row.id).toBeGreaterThan(14);  // conflictedClasses max seed id
  });

  // ── S10 — success → "Arrange Replacement Now" ──
  test('S10 — Arrange Replacement Now navigates to the arrangement page with the cancelled class params', async ({ page }) => {
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('10');
    const block = page.locator('#tableBody .event-block:has-text("BMIT2013(L)")');
    await expect(block).toBeVisible();
    await block.click();
    await page.locator('#classModal .modal-footer [data-cancel-class-btn]').click();
    await page.locator('#cancelReasonList input[type="radio"][value="Medical Leave"]').click();
    await page.locator('#confirmCancelClassBtn').click();
    await expect(page.locator('#cancelClassSuccessState')).toBeVisible();

    await page.locator('#cancelArrangeNowBtn').click();
    // date = ClassCancellation.isoDate(10, 1) = semester start 2026-09-21
    // + 70 + 1 days = 2026-12-01; duration = (15-12+1)/2 = 2 half-hours → 2 h
    await expect(page).toHaveURL(/\/replacement-arrangement\?code=BMIT2013&date=2026-12-01&duration=2/);
  });

  // ── S12 — pre-seeded ledger replay: load toast + Just cancelled chip ──
  test('S12 — pre-seeded ledger replays: load undo toast + Just cancelled chip on replacement-home', async ({ page }) => {
    // The ledger value is a JSON ARRAY of entries.
    const entry = JSON.stringify([seedEntry()]);
    await page.addInitScript(
      ({ key, value }: { key: string; value: string }) => {
        // Seed on the FIRST document load only (the before-each clear runs
        // first); later navigations see the key present and never clobber a
        // ledger that the test itself mutated (S13b consumed / S14 undo).
        try {
          if (!sessionStorage.getItem(key)) {
            sessionStorage.setItem(key, value);
          }
        } catch (e) { /* §10 degrade */ }
      },
      { key: LEDGER_KEY, value: entry },
    );

    await page.goto(HOME);

    // S12 load toast (12 s) with Undo
    const bar = page.locator('#toastBar');
    await expect(bar).toBeVisible();    await expect(bar).toContainText('Class cancelled — arrange replacement when ready');
    await expect(page.locator('#toastBar .toast-undo')).toBeVisible();

    // S13 — home row listed with the Just cancelled chip (seed date chosen
    // to sort onto pagination page 1)
    const row = page.locator('#tableBody tr', { hasText: 'AMCS2093' });
    await expect(row).toBeVisible();
    await expect(row.locator('.just-cancelled-chip')).toBeVisible();

    // The replay also hit the grid twin store: the seeded week-0 B107 block
    // is already cancelled on my-timetable without any user action.
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('0');
    await expect(
      page.locator('#tableBody .event-block:has-text("AMCS2093(T)")')
        .filter({ hasText: 'B107' }),
    ).toHaveCount(0);
  });

  // ── S13b — arrangement submit marks the entry consumed (retained) ──
  test('S13b — consumed entry keeps the cancelled state replay but drops chip + load toast', async ({ page }) => {
    const entry = JSON.stringify([seedEntry()]);
    await page.addInitScript(
      ({ key, value }: { key: string; value: string }) => {
        // Same first-load-only seeding as S12 above.
        try {
          if (!sessionStorage.getItem(key)) {
            sessionStorage.setItem(key, value);
          }
        } catch (e) { /* §10 degrade */ }
      },
      { key: LEDGER_KEY, value: entry },
    );

    // The arrangement page is the submit consumer — load it carrying the
    // cancelled class's params (the same URL scheme the success state uses).
    await page.goto(`${ARRANGE}?code=AMCS2093&date=2026-09-24&duration=1`, { waitUntil: 'networkidle' });
    // Minimal stable submit-free variant: run the page's own submit hook
    // (window.ClassCancellation is a global-lexical bound; pages share it).
    const changed = await page.evaluate(() => ClassCancellation.markConsumedByCode('AMCS2093'));
    expect(changed).toBe(1);
    const stored = await page.evaluate(
      (key) => {
        const led = JSON.parse(sessionStorage.getItem(key) || '[]');
        return led[0] ? led[0].consumed : undefined;
      },
      LEDGER_KEY,
    );
    expect(stored).toBe('arranged');  // RETAINED, not removed (§6 lifecycle)

    await page.goto(HOME);
    // Chip gone (un-consumed chip rendering is entry-scoped)…
    await expect(page.locator('.just-cancelled-chip')).toHaveCount(0);
    // …the row is still listed (state kept replaying)…
    await expect(page.locator('#tableBody tr', { hasText: 'AMCS2093' })).toBeVisible();
    // …and the load toast no longer offers undo.
    await expect(page.locator('#toastBar .toast-undo')).toBeHidden();
  });

  // ── S14 — undo from the load toast restores everything ──
  test('S14 — undo removes the entry, restores the block on my-timetable', async ({ page }) => {
    const entry = JSON.stringify([seedEntry()]);
    await page.addInitScript(
      ({ key, value }: { key: string; value: string }) => {
        // Same first-load-only seeding as S12 above.
        try {
          if (!sessionStorage.getItem(key)) {
            sessionStorage.setItem(key, value);
          }
        } catch (e) { /* §10 degrade */ }
      },
      { key: LEDGER_KEY, value: entry },
    );

    await page.goto(HOME);
    await expect(page.locator('#toastBar .toast-undo')).toBeVisible();
    await page.locator('.toast-undo').click();

    // Newest entry removed from the ledger
    const count = await page.evaluate(
      (key) => JSON.parse(sessionStorage.getItem(key) || '[]').length, LEDGER_KEY,
    );
    expect(count).toBe(0);
    // Confirmation toast
    await expect(page.locator('#toastBar')).toContainText('Class restored.');

    // Twin restored: navigating to my-timetable shows the block again
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('0');
    await expect(
      page.locator('#tableBody .event-block:has-text("AMCS2093(T)")')
        .filter({ hasText: 'B107' }),
    ).toBeVisible();
  });

  // ── S15 — stale/unresolvable ledger entry is dropped silently ──
  test('S15 — unresolvable ledger entry: no toast, no crash, entry dropped', async ({ page }) => {
    const pageErrors: string[] = [];
    page.on('pageerror', (err) => pageErrors.push(err.message));
    const garbage = JSON.stringify([{
      id: 'cxl-x',
      matchKey: { week: 0, code: 'NOPE', di: 0, start: 0, lecturer: 'X' },
    }]);

    await page.goto('/');
    await page.evaluate(([key, value]) => {
      sessionStorage.setItem(key, value);
    }, [LEDGER_KEY, garbage]);

    await page.goto(HOME);
    // No toast was offered …
    await expect(page.locator('#toastBar')).toBeHidden();
    await expect(page.locator('#toastBar .toast-undo')).toBeHidden();
    // … the page is healthy …
    await expect(page.locator('#tableBody tr').first()).toBeVisible();
    // … the entry was dropped from storage (S15 — ledgerRemove writes back an
    // empty array rather than deleting the key) …
    const left = await page.evaluate(
      (key) => JSON.parse(sessionStorage.getItem(key) || '[]'), LEDGER_KEY,
    );
    expect(left).toEqual([]);
    // … and nothing blew up.
    expect(pageErrors).toEqual([]);
  });

  // ── S4 — guard: own class on a public-holiday day has no cancel button ──
  test('S4 — own class on a holiday day shows no cancel button (my-timetable, Deepavali in-lieu Monday)', async ({ page }) => {
    await page.goto(MY);
    // Week idx 7 Monday = 09 Nov 2026 — MockData.holidays[1] 'Deepavali
    // Holiday (In Lieu)' (1-based week 8, dayIndex 0). Own + normal block
    // sits there: eventsByWeek[7] di=0 start=6 end=9 AMCS2093 L B110 —
    // its end datetime (13:00 that day) is still future vs the real clock,
    // so it is the S4 day-holiday guard (not the S5 end-time one) that
    // suppresses the button. The fixture is reachable straight through the
    // UI — no synthetic-event page.evaluate fallback needed.
    await page.locator('#weekSelect').selectOption('7');
    const block = page.locator('#tableBody .event-block:has-text("AMCS2093(L)")');
    await expect(block).toBeVisible();
    await block.click();
    await expect(page.locator('#classModal')).toBeVisible();
    await expect(page.locator('#classModal .modal-footer [data-cancel-class-btn]')).toHaveCount(0);
  });

  // ── S6 — confirm-time clock recheck: class ends while the modal is open ──
  test('S6 — class ending between modal open and confirm aborts with the "already ended" hint', async ({ page }) => {
    await page.goto(MY);
    await page.locator('#weekSelect').selectOption('10');
    // Same S9 fixture: eventsByWeek[10] di=1 (Tue 01 Dec 2026) BMIT2013 L
    // B009, start idx 12 / end idx 15 → 2:00–4:00 PM (hours[16] = '16:00').
    const block = page.locator('#tableBody .event-block:has-text("BMIT2013(L)")');
    await expect(block).toBeVisible();
    await block.click();
    await page.locator('#classModal .modal-footer [data-cancel-class-btn]').click();
    await expect(page.locator('#cancelClassOverlay')).toBeVisible();

    await page.locator('#cancelReasonList input[type="radio"][value="Medical Leave"]').click();

    // Clock mechanism (named per design §8): page.clock.setFixedTime ALONE
    // — no clock.install(). Date is faked from this point but timers stay
    // real, so the app's setTimeout/toast code is untouched. Freeze 30 s
    // past the class end (16:00:30 local on 01 Dec 2026); the modal was
    // opened under the real clock BEFORE the end, so only the confirm-time
    // re-check (ClassCancellation.cancel → isCancellable) can catch it.
    await page.clock.setFixedTime(new Date(2026, 11, 1, 16, 0, 30));

    await page.locator('#confirmCancelClassBtn').click();

    // Abort: inline hint, overlay stays open, no success state
    await expect(page.locator('#cancelReasonHint')).toHaveText('This class has already ended.');
    await expect(page.locator('#cancelClassOverlay')).toBeVisible();
    await expect(page.locator('#cancelClassSuccessState')).toBeHidden();
    // Nothing was written: ledger untouched …
    const ledger = await page.evaluate(
      (key) => JSON.parse(sessionStorage.getItem(key) || '[]'), LEDGER_KEY,
    );
    expect(ledger).toHaveLength(0);
    // … and the block is still on the grid.
    await expect(page.locator('#tableBody .event-block:has-text("BMIT2013(L)")')).toHaveCount(1);
  });
});
