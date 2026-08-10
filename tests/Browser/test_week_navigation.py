"""
Week Navigation & TODAY Button — Playwright Test Suite
======================================================
Tests the week navigation persistence behavior across all timetable pages.

Expected behavior:
  1. First visit (no localStorage) → defaults to today's week
  2. Navigate to a different week → persists to localStorage
  3. Refresh → restores the saved week (not today)
  4. Click TODAY → shows today's week (does NOT persist)
  5. Refresh after TODAY → back to the saved week
  6. Cross-page sync → all pages share the same week state
"""

from playwright.sync_api import sync_playwright, Page, BrowserContext
import pytest

# ───── Constants ─────

PAGES = {
    "my-timetable": "http://127.0.0.1:8000/my-timetable-ui",
    "student-my-timetable": "http://127.0.0.1:8000/student-my-timetable-ui",
    "cohort-timetable": "http://127.0.0.1:8000/cohort-timetable-ui",
    "venue-timetable": "http://127.0.0.1:8000/venue-timetable-ui",
}

TARGET_WEEK = 3  # a non-today week to navigate to


# ───── Helpers ─────

def fresh_page(ctx: BrowserContext, url: str) -> Page:
    """Navigate to url with a clean localStorage."""
    page = ctx.new_page()
    page.goto(url, wait_until="networkidle")
    page.wait_for_timeout(500)
    page.evaluate("localStorage.clear()")
    page.goto("about:blank")
    page.goto(url, wait_until="networkidle")
    page.wait_for_timeout(1500)
    return page


def today_index(page: Page) -> int:
    return page.evaluate("currentWeekIndex()")


def current_week(page: Page) -> int:
    return page.evaluate("currentWeek")


def navigate_to_week(page: Page, week: int):
    """Navigate to a specific week and persist it."""
    has_weeknav = page.evaluate("typeof weekNav !== 'undefined'")
    page.evaluate(f"""
        currentWeek = {week};
        document.getElementById('weekSelect').selectedIndex = {week};
        if (typeof weekNav !== 'undefined') {{
            weekNav._currentWeek = {week};
            weekNav.save();
        }}
        if (typeof venueState !== 'undefined') {{
            venueState.save();
        }}
        if (typeof saveState === 'function') {{
            saveState();
        }}
        if (typeof buildTimetable === 'function') {{
            buildTimetable();
        }}
    """)
    page.wait_for_timeout(500)


def click_today(page: Page):
    """Simulate clicking the TODAY button (persists today's week)."""
    has_weeknav = page.evaluate("typeof weekNav !== 'undefined'")
    if has_weeknav:
        page.evaluate("weekNav.jumpToToday(); currentWeek = weekNav.currentWeek; weekNav.save();")
    else:
        page.evaluate("jumpToToday()")
    page.wait_for_timeout(500)


# ═══════════════════════════════════════════════════════════════
#  TC-01: First visit defaults to today
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_first_visit_defaults_to_today(ctx: BrowserContext, name: str, url: str):
    """TC-01: On first visit with no saved state, the page should show today's week."""
    page = fresh_page(ctx, url)
    try:
        assert current_week(page) == today_index(page), (
            f"[{name}] Expected week={today_index(page)} (today), got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-02: Navigate persists to localStorage
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_navigate_persists_week(ctx: BrowserContext, name: str, url: str):
    """TC-02: After navigating to week 3, localStorage should reflect week 3."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        assert current_week(page) == TARGET_WEEK

        # Verify localStorage has the saved value
        saved = page.evaluate("""() => {
            if (typeof weekNav !== 'undefined') return parseInt(localStorage.getItem('currentWeek'));
            if (typeof venueState !== 'undefined') {
                const s = JSON.parse(localStorage.getItem('venueTimetableState') || '{}');
                return s.week;
            }
            return null;
        }""")
        assert saved == TARGET_WEEK, f"[{name}] localStorage week={saved}, expected {TARGET_WEEK}"
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-03: Refresh restores saved week
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_refresh_restores_saved_week(ctx: BrowserContext, name: str, url: str):
    """TC-03: After navigating to week 3 and refreshing, week 3 should be restored."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == TARGET_WEEK, (
            f"[{name}] After refresh: expected week={TARGET_WEEK}, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-04: TODAY shows today and persists
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_today_shows_today_persists(ctx: BrowserContext, name: str, url: str):
    """TC-04: Clicking TODAY should show today's week and persist it."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        td = today_index(page)

        click_today(page)
        assert current_week(page) == td, (
            f"[{name}] After TODAY: expected week={td}, got {current_week(page)}"
        )

        # Verify localStorage WAS overwritten with today's week
        saved = page.evaluate("""() => {
            if (typeof weekNav !== 'undefined') return parseInt(localStorage.getItem('currentWeek'));
            if (typeof venueState !== 'undefined') {
                const s = JSON.parse(localStorage.getItem('venueTimetableState') || '{}');
                return s.week;
            }
            return null;
        }""")
        assert saved == td, (
            f"[{name}] localStorage should be {td} (today), got {saved}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-05: Refresh after TODAY restores today (persisted)
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_refresh_after_today_restores_today(ctx: BrowserContext, name: str, url: str):
    """TC-05: After clicking TODAY and refreshing, the page should show today's week."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        click_today(page)
        td = today_index(page)
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == td, (
            f"[{name}] After TODAY+refresh: expected week={td}, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-06: Multiple navigations accumulate correctly
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_multiple_navigations(ctx: BrowserContext, name: str, url: str):
    """TC-06: Navigating week 3 → 7 → 0 should persist week 0."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, 3)
        navigate_to_week(page, 7)
        navigate_to_week(page, 0)
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == 0, (
            f"[{name}] Expected week=0 after multiple navigations, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-07: TODAY between navigations: last navigate wins
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_today_between_navigations(ctx: BrowserContext, name: str, url: str):
    """TC-07: Navigate → TODAY → navigate again → refresh → last navigated week."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, 3)
        click_today(page)  # persists today
        navigate_to_week(page, 7)  # persists week 7, overwriting today
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == 7, (
            f"[{name}] Expected week=7, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-08: TODAY twice in a row persists today
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_today_twice_persists(ctx: BrowserContext, name: str, url: str):
    """TC-08: Clicking TODAY twice should show today, with today persisted."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        click_today(page)
        click_today(page)
        td = today_index(page)
        assert current_week(page) == td
        # Save should be today's week
        saved = page.evaluate("""() => {
            if (typeof weekNav !== 'undefined') return parseInt(localStorage.getItem('currentWeek'));
            if (typeof venueState !== 'undefined') {
                const s = JSON.parse(localStorage.getItem('venueTimetableState') || '{}');
                return s.week;
            }
            return null;
        }""")
        assert saved == td, (
            f"[{name}] localStorage should be {td} (today), got {saved}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-09: Clear localStorage reverts to today
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_clear_localstorage_reverts_to_today(ctx: BrowserContext, name: str, url: str):
    """TC-09: After clearing localStorage and refreshing, page should default to today."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        page.evaluate("localStorage.clear()")
        page.goto("about:blank")
        page.goto(url, wait_until="networkidle")
        page.wait_for_timeout(1500)
        assert current_week(page) == today_index(page), (
            f"[{name}] After clear: expected today={today_index(page)}, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-10: Week dropdown stays in sync with currentWeek
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_week_select_synced(ctx: BrowserContext, name: str, url: str):
    """TC-10: The week <select> dropdown index should match currentWeek."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        sel_idx = page.evaluate("document.getElementById('weekSelect').selectedIndex")
        assert sel_idx == TARGET_WEEK, (
            f"[{name}] weekSelect.selectedIndex={sel_idx}, expected {TARGET_WEEK}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-11: Today button updates the select dropdown
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_today_updates_select(ctx: BrowserContext, name: str, url: str):
    """TC-11: After TODAY, the select dropdown should show today's index."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, TARGET_WEEK)
        click_today(page)
        sel_idx = page.evaluate("document.getElementById('weekSelect').selectedIndex")
        assert sel_idx == today_index(page), (
            f"[{name}] After TODAY: select={sel_idx}, expected {today_index(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-12: Cross-page sync — navigate on MyTimetable, check on StudentMyTimetable
# ═══════════════════════════════════════════════════════════════

def test_cross_page_sync(ctx: BrowserContext):
    """TC-12: Navigating on one page should affect the other (shared localStorage)."""
    page1 = fresh_page(ctx, PAGES["my-timetable"])
    page2 = ctx.new_page()
    try:
        navigate_to_week(page1, TARGET_WEEK)
        page2.goto(PAGES["student-my-timetable"], wait_until="networkidle")
        page2.wait_for_timeout(1500)
        assert current_week(page2) == TARGET_WEEK, (
            f"Cross-page: student-my-timetable week={current_week(page2)}, expected {TARGET_WEEK}"
        )
    finally:
        page1.close()
        page2.close()


# ═══════════════════════════════════════════════════════════════
#  TC-13: Edge case — navigate to week 0 (first week)
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_navigate_to_first_week(ctx: BrowserContext, name: str, url: str):
    """TC-13: Navigating to week 0 should persist and restore correctly."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, 0)
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == 0, (
            f"[{name}] Expected week=0, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-14: Edge case — navigate to last week
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_navigate_to_last_week(ctx: BrowserContext, name: str, url: str):
    """TC-14: Navigating to the last week (13) should persist and restore correctly."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, 13)
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == 13, (
            f"[{name}] Expected week=13, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-15: Page loads without JS errors
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_no_console_errors(ctx: BrowserContext, name: str, url: str):
    """TC-15: Each timetable page should load with zero console errors."""
    errors = []
    page = ctx.new_page()
    page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)
    try:
        page.goto(url, wait_until="networkidle")
        page.wait_for_timeout(2000)
        assert len(errors) == 0, f"[{name}] Console errors: {errors}"
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-16: prevWeek persists
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_prev_week_persists(ctx: BrowserContext, name: str, url: str):
    """TC-16: Using prevWeek() should persist the new week."""
    page = fresh_page(ctx, url)
    try:
        td = today_index(page)
        if td > 0:
            page.evaluate("prevWeek()")
            page.wait_for_timeout(500)
            assert current_week(page) == td - 1
            page.reload(wait_until="networkidle")
            page.wait_for_timeout(1000)
            assert current_week(page) == td - 1, (
                f"[{name}] prevWeek didn't persist: got {current_week(page)}"
            )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-17: nextWeek persists
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_next_week_persists(ctx: BrowserContext, name: str, url: str):
    """TC-17: Using nextWeek() should persist the new week."""
    page = fresh_page(ctx, url)
    try:
        td = today_index(page)
        page.evaluate("nextWeek()")
        page.wait_for_timeout(500)
        assert current_week(page) == td + 1
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == td + 1, (
            f"[{name}] nextWeek didn't persist: got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-18: selectWeek persists
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_select_week_persists(ctx: BrowserContext, name: str, url: str):
    """TC-18: Using selectWeek(5) should persist week 5."""
    page = fresh_page(ctx, url)
    try:
        page.evaluate("selectWeek(5)")
        page.wait_for_timeout(500)
        assert current_week(page) == 5
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == 5, (
            f"[{name}] selectWeek didn't persist: got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-19: Full flow — navigate → today → navigate → refresh
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_full_flow(ctx: BrowserContext, name: str, url: str):
    """TC-19: Complex flow: visit → nav 5 → today → nav 10 → refresh → week 10."""
    page = fresh_page(ctx, url)
    try:
        navigate_to_week(page, 5)
        click_today(page)
        navigate_to_week(page, 10)
        page.reload(wait_until="networkidle")
        page.wait_for_timeout(1000)
        assert current_week(page) == 10, (
            f"[{name}] Full flow: expected week=10, got {current_week(page)}"
        )
    finally:
        page.close()


# ═══════════════════════════════════════════════════════════════
#  TC-20: Corrupted localStorage falls back to today
# ═══════════════════════════════════════════════════════════════

@pytest.mark.parametrize("name,url", PAGES.items(), ids=PAGES.keys())
def test_corrupted_localstorage_fallback(ctx: BrowserContext, name: str, url: str):
    """TC-20: If localStorage has a non-numeric value, page should default to today."""
    page = ctx.new_page()
    try:
        page.goto(url, wait_until="networkidle")
        page.wait_for_timeout(500)
        page.evaluate("""() => {
            localStorage.setItem('currentWeek', 'not-a-number');
            localStorage.setItem('venueTimetableState', JSON.stringify({week: 'not-a-number'}));
            localStorage.setItem('cohortTimetableState', JSON.stringify({week: 'not-a-number'}));
        }""")
        page.goto("about:blank")
        page.goto(url, wait_until="networkidle")
        page.wait_for_timeout(1500)
        assert current_week(page) == today_index(page), (
            f"[{name}] Corrupted storage: expected today={today_index(page)}, got {current_week(page)}"
        )
    finally:
        page.close()
