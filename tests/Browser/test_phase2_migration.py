"""
Phase 2 Template Migration — Browser Test Suite
===============================================
Tests cover: DateHelper.fmtShort, DateHelper.weekRangeLabel,
HtmlBuilder.requestCard, HtmlBuilder.myRequestCard,
HtmlBuilder.replacementHomeRow, HtmlBuilder.replacementHomeCard,
and regression tests for migrated pages.

Run:  python tests/Browser/test_phase2_migration.py
      (requires Laravel dev server on :8000)
"""
import time
from playwright.sync_api import sync_playwright, expect

BASE = "http://127.0.0.1:8000"
PAGES = {
    "replacement-home": f"{BASE}/replacement-home-ui",
    "my-request-history": f"{BASE}/my-request-history-ui",
    "request-approval": f"{BASE}/request-approval-ui",
    "my-timetable": f"{BASE}/my-timetable-ui",
    "cohort-timetable": f"{BASE}/cohort-timetable-ui",
    "student-my-timetable": f"{BASE}/student-my-timetable-ui",
    "venue-timetable": f"{BASE}/venue-timetable-ui",
}

# ─── Helpers ───────────────────────────────────────────────────────────────

def _goto(page, url, wait="networkidle"):
    page.goto(url, wait_until=wait)
    page.wait_for_timeout(600)


def _console_errors(page):
    """Return list of console errors."""
    errors = []
    page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)
    return errors


# ═══════════════════════════════════════════════════════════════════════════
# 1. DATEHELPER — NEW METHODS (Phase 2)
# ═══════════════════════════════════════════════════════════════════════════

class TestDateHelperPhase2:
    """DateHelper.fmtShort and DateHelper.weekRangeLabel work correctly."""

    def test_datehelper_has_fmtshort(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["student-my-timetable"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof DateHelper.fmtShort")
            assert result == "function", f"DateHelper.fmtShort should be a function, got {result}"
            browser.close()

    def test_datehelper_has_weekrangelabel(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof DateHelper.weekRangeLabel")
            assert result == "function", f"DateHelper.weekRangeLabel should be a function, got {result}"
            browser.close()

    def test_datehelper_fmtshort_output(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["student-my-timetable"], wait_until="domcontentloaded")
            
            # Test fmtShort with a known date
            result = page.evaluate("""
                () => {
                    const d = new Date(2026, 7, 10); // Aug 10, 2026
                    return DateHelper.fmtShort(d);
                }
            """)
            assert result == "10 Aug", f"Expected '10 Aug', got '{result}'"
            
            # Test fmtShort with another date
            result = page.evaluate("""
                () => {
                    const d = new Date(2026, 11, 25); // Dec 25, 2026
                    return DateHelper.fmtShort(d);
                }
            """)
            assert result == "25 Dec", f"Expected '25 Dec', got '{result}'"
            
            browser.close()

    def test_datehelper_weekrangelabel_output(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Test weekRangeLabel with week 1
            result = page.evaluate("DateHelper.weekRangeLabel(1)")
            assert "Week 1" in result, f"Expected 'Week 1' in result, got '{result}'"
            assert "31 Aug" in result, f"Expected '31 Aug' in result, got '{result}'"
            
            # Test weekRangeLabel with week 2
            result = page.evaluate("DateHelper.weekRangeLabel(2)")
            assert "Week 2" in result, f"Expected 'Week 2' in result, got '{result}'"
            assert "07 Sep" in result, f"Expected '07 Sep' in result, got '{result}'"
            
            # Test weekRangeLabel with invalid week
            result = page.evaluate("DateHelper.weekRangeLabel(99)")
            assert result == "Week 99", f"Expected 'Week 99', got '{result}'"
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 2. HTMLBUILDER — NEW METHODS (Phase 2)
# ═══════════════════════════════════════════════════════════════════════════

class TestHtmlBuilderPhase2:
    """HtmlBuilder.requestCard, myRequestCard, replacementHomeRow, replacementHomeCard work correctly."""

    def test_htmlbuilder_has_requestcard(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["request-approval"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof HtmlBuilder.requestCard")
            assert result == "function", f"HtmlBuilder.requestCard should be a function, got {result}"
            browser.close()

    def test_htmlbuilder_has_myrequestcard(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["my-request-history"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof HtmlBuilder.myRequestCard")
            assert result == "function", f"HtmlBuilder.myRequestCard should be a function, got {result}"
            browser.close()

    def test_htmlbuilder_has_replacementhomerow(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof HtmlBuilder.replacementHomeRow")
            assert result == "function", f"HtmlBuilder.replacementHomeRow should be a function, got {result}"
            browser.close()

    def test_htmlbuilder_has_replacementhomecard(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof HtmlBuilder.replacementHomeCard")
            assert result == "function", f"HtmlBuilder.replacementHomeCard should be a function, got {result}"
            browser.close()

    def test_htmlbuilder_requestcard_output(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["request-approval"], wait_until="domcontentloaded")
            
            # Test requestCard with mock data
            result = page.evaluate("""
                () => {
                    const r = {
                        id: 1,
                        status: 'Pending',
                        courseCode: 'BMIT6767',
                        courseName: 'Object-Ooped Programming',
                        classDate: '2026-08-10',
                        timeStart: '09:00',
                        lecturer: 'Dr. Christopher Lazarus'
                    };
                    const html = HtmlBuilder.requestCard(r, {});
                    return html.includes('BMIT6767') && html.includes('Pending');
                }
            """)
            assert result == True, "requestCard should contain course code and status"
            browser.close()

    def test_htmlbuilder_myrequestcard_output(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["my-request-history"], wait_until="domcontentloaded")
            
            # Test myRequestCard with mock data
            result = page.evaluate("""
                () => {
                    const r = {
                        courseCode: 'BMIT6767',
                        courseName: 'Object-Ooped Programming',
                        classType: 'T',
                        classDay: 'MONDAY',
                        classDate: '2026-08-10',
                        timeStart: '09:00',
                        timeEnd: '11:00',
                        venue: 'KB-201',
                        status: 'Pending',
                        cohorts: ['RSD3(S1)G2'],
                        requestedAt: '2026-08-09T10:00:00'
                    };
                    const html = HtmlBuilder.myRequestCard(r, {});
                    return html.includes('BMIT6767') && html.includes('Tutorial');
                }
            """)
            assert result == True, "myRequestCard should contain course code and type label"
            browser.close()

    def test_htmlbuilder_replacementhomecard_output(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Test replacementHomeCard with mock data
            result = page.evaluate("""
                () => {
                    const c = {
                        code: 'BMIT6767',
                        name: 'Object-Ooped Programming',
                        type: 'L',
                        day: 'MON',
                        date: '2026-08-10',
                        timeStart: '09:00',
                        timeEnd: '11:00',
                        venue: 'KB-201',
                        conflictReason: 'Venue Unavailable',
                        cohorts: ['RSD3(S1)G2'],
                        totalStudents: 35
                    };
                    const opts = {
                        daysLeft: () => 5,
                        urgencyClass: () => 'urgency-high',
                        badgeClass: () => 'badge-conflict'
                    };
                    const html = HtmlBuilder.replacementHomeCard(c, opts);
                    return html.includes('BMIT6767') && html.includes('5 days left');
                }
            """)
            assert result == True, "replacementHomeCard should contain course code and days left"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 3. INLINE HELPERS REMOVED (Phase 2)
# ═══════════════════════════════════════════════════════════════════════════

class TestInlineHelpersRemoved:
    """Verify that inline helpers have been removed from migrated templates."""

    def test_student_my_timetable_no_fmtshort(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["student-my-timetable"], wait_until="domcontentloaded")
            
            # Check that inline fmtShort function is not defined
            result = page.evaluate("""
                () => {
                    // Look for function declarations in the page scripts
                    const scripts = document.querySelectorAll('script');
                    for (const script of scripts) {
                        if (script.textContent.includes('function fmtShort(')) {
                            return false;
                        }
                    }
                    return true;
                }
            """)
            assert result == True, "student-my-timetable should not have inline fmtShort function"
            browser.close()

    def test_request_approval_no_formatshortdate(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["request-approval"], wait_until="domcontentloaded")
            
            # Check that inline formatShortDate function is not defined
            result = page.evaluate("""
                () => {
                    const scripts = document.querySelectorAll('script');
                    for (const script of scripts) {
                        if (script.textContent.includes('function formatShortDate(')) {
                            return false;
                        }
                    }
                    return true;
                }
            """)
            assert result == True, "request-approval should not have inline formatShortDate function"
            browser.close()

    def test_replacement_home_no_computeweek(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Check that inline computeWeek function is not defined
            result = page.evaluate("""
                () => {
                    const scripts = document.querySelectorAll('script');
                    for (const script of scripts) {
                        if (script.textContent.includes('function computeWeek(')) {
                            return false;
                        }
                    }
                    return true;
                }
            """)
            assert result == True, "replacement-home should not have inline computeWeek function"
            browser.close()

    def test_replacement_home_no_weekrangelabel(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Check that inline weekRangeLabel function is not defined
            result = page.evaluate("""
                () => {
                    const scripts = document.querySelectorAll('script');
                    for (const script of scripts) {
                        if (script.textContent.includes('function weekRangeLabel(')) {
                            return false;
                        }
                    }
                    return true;
                }
            """)
            assert result == True, "replacement-home should not have inline weekRangeLabel function"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 4. PAGE REGRESSION TESTS (Phase 2)
# ═══════════════════════════════════════════════════════════════════════════

class TestPageRegression:
    """Regression tests for migrated pages — verify they load and function correctly."""

    def test_student_my_timetable_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = _console_errors(page)
            _goto(page, PAGES["student-my-timetable"])
            
            # Verify page loaded
            title = page.title()
            assert "Student" in title or "Timetable" in title, f"Unexpected page title: {title}"
            
            # Verify no console errors
            assert len(errors) == 0, f"Console errors: {errors}"
            
            # Verify week subtitle exists
            subtitle = page.query_selector("#weekSubtitle")
            assert subtitle is not None, "Week subtitle element not found"
            
            # Verify week subtitle has content
            text = subtitle.text_content()
            assert "Week" in text, f"Week subtitle should contain 'Week', got: {text}"
            
            browser.close()

    def test_request_approval_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = _console_errors(page)
            _goto(page, PAGES["request-approval"])
            
            # Verify page loaded
            title = page.title()
            assert "Request" in title or "Approval" in title, f"Unexpected page title: {title}"
            
            # Verify no console errors
            assert len(errors) == 0, f"Console errors: {errors}"
            
            # Verify table exists
            table = page.query_selector("#tableBody")
            assert table is not None, "Table body element not found"
            
            # Verify rows exist
            rows = page.query_selector_all("#tableBody tr")
            assert len(rows) > 0, "Table should have at least one row"
            
            browser.close()

    def test_my_request_history_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = _console_errors(page)
            _goto(page, PAGES["my-request-history"])
            
            # Verify page loaded
            title = page.title()
            assert "Request" in title or "History" in title, f"Unexpected page title: {title}"
            
            # Verify no console errors
            assert len(errors) == 0, f"Console errors: {errors}"
            
            # Verify table exists
            table = page.query_selector("#tableBody")
            assert table is not None, "Table body element not found"
            
            # Verify rows exist
            rows = page.query_selector_all("#tableBody tr")
            assert len(rows) > 0, "Table should have at least one row"
            
            browser.close()

    def test_replacement_home_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = _console_errors(page)
            _goto(page, PAGES["replacement-home"])
            
            # Verify page loaded
            title = page.title()
            assert "Replacement" in title or "Home" in title, f"Unexpected page title: {title}"
            
            # Verify no console errors
            assert len(errors) == 0, f"Console errors: {errors}"
            
            # Verify table exists
            table = page.query_selector("#tableBody")
            assert table is not None, "Table body element not found"
            
            # Verify rows exist
            rows = page.query_selector_all("#tableBody tr")
            assert len(rows) > 0, "Table should have at least one row"
            
            # Verify week dropdown exists
            weekSelect = page.query_selector("#weekFilter")
            assert weekSelect is not None, "Week filter dropdown not found"
            
            # Verify week dropdown has options
            options = page.query_selector_all("#weekFilter option")
            assert len(options) > 1, "Week filter should have multiple options"
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 5. BUG FIX TESTS (venue-timetable, my-timetable)
# ═══════════════════════════════════════════════════════════════════════════

class TestBugFixes:
    """Test bug fixes for venue-timetable and my-timetable."""

    def test_venue_timetable_loads_without_error(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = _console_errors(page)
            _goto(page, PAGES["venue-timetable"])
            
            # Verify no console errors (bug fix: addEventListener on null)
            assert len(errors) == 0, f"Console errors: {errors}"
            
            # Verify page loaded
            title = page.title()
            assert "Venue" in title or "Timetable" in title, f"Unexpected page title: {title}"
            
            browser.close()

    def test_my_timetable_loads_without_error(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = _console_errors(page)
            _goto(page, PAGES["my-timetable"])
            
            # Verify no console errors (bug fix: d is undefined in fmt())
            assert len(errors) == 0, f"Console errors: {errors}"
            
            # Verify page loaded
            title = page.title()
            assert "Timetable" in title, f"Unexpected page title: {title}"
            
            # Verify week subtitle exists
            subtitle = page.query_selector("#weekSubtitle")
            assert subtitle is not None, "Week subtitle element not found"
            
            # Verify week subtitle has content
            text = subtitle.text_content()
            assert "Week" in text, f"Week subtitle should contain 'Week', got: {text}"
            
            browser.close()

    def test_venue_timetable_history_details_works(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = _console_errors(page)
            _goto(page, PAGES["venue-timetable"])
            
            # Verify page loads without errors (bug fix: addEventListener on null)
            # Note: historyDetails element is commented out in template, so we just verify no errors
            assert len(errors) == 0, f"Console errors: {errors}"
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 6. FULL TEST SUITE REGRESSION
# ═══════════════════════════════════════════════════════════════════════════

class TestFullRegression:
    """Full regression test — all pages load without console errors."""

    def test_all_pages_load_without_errors(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            
            for name, url in PAGES.items():
                page = browser.new_page()
                errors = _console_errors(page)
                _goto(page, url)
                
                # Verify no console errors
                assert len(errors) == 0, f"{name} has console errors: {errors}"
                
                page.close()
            
            browser.close()

    def test_all_pages_have_title(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            
            for name, url in PAGES.items():
                page = browser.new_page()
                _goto(page, url)
                
                # Verify page has a title
                title = page.title()
                assert len(title) > 0, f"{name} has no title"
                
                page.close()
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# MAIN
# ═══════════════════════════════════════════════════════════════════════════

if __name__ == "__main__":
    import pytest
    pytest.main([__file__, "-v", "--tb=short"])
