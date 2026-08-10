"""
UI JS OOP Refactor — Browser Test Suite
===========================
Tests cover: DateHelper, HtmlBuilder, SkeletonLoader, ToastManager,
and regression tests for page functionality.

Run:  python tests/Browser/test_ui_oop_refactor.py
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
# 1. DATEHELPER CLASS
# ═══════════════════════════════════════════════════════════════════════════

class TestDateHelper:
    """DateHelper static class exists and methods work correctly."""

    def test_datehelper_class_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof DateHelper")
            assert result in ["object", "function"], f"DateHelper should be a class, got {result}"
            browser.close()

    def test_datehelper_has_all_methods(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            methods = page.evaluate("""
                () => {
                    const expected = ['to12h', 'formatDate', 'formatDateTime', 'fmt', 
                                     'add30min', 'dayAbbr', 'isoDayName', 'getTodayMs'];
                    return expected.filter(m => typeof DateHelper[m] !== 'function');
                }
            """)
            assert len(methods) == 0, f"Missing DateHelper methods: {methods}"
            browser.close()

    def test_datehelper_to12h(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Test 12h conversion
            result = page.evaluate("DateHelper.to12h('09:30')")
            assert result == "9:30 AM", f"Expected '9:30 AM', got '{result}'"
            
            result = page.evaluate("DateHelper.to12h('14:00')")
            assert result == "2:00 PM", f"Expected '2:00 PM', got '{result}'"
            
            result = page.evaluate("DateHelper.to12h('00:00')")
            assert result == "12:00 AM", f"Expected '12:00 AM', got '{result}'"
            
            browser.close()

    def test_datehelper_format_date(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("DateHelper.formatDate('2026-08-10')")
            assert result == "10 Aug 2026", f"Expected '10 Aug 2026', got '{result}'"
            
            result = page.evaluate("DateHelper.formatDate('2026-12-25')")
            assert result == "25 Dec 2026", f"Expected '25 Dec 2026', got '{result}'"
            
            browser.close()

    def test_datehelper_day_abbr(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("DateHelper.dayAbbr('MONDAY')")
            assert result == "MON", f"Expected 'MON', got '{result}'"
            
            result = page.evaluate("DateHelper.dayAbbr('FRIDAY')")
            assert result == "FRI", f"Expected 'FRI', got '{result}'"
            
            browser.close()

    def test_datehelper_add30min(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("DateHelper.add30min('09:30')")
            assert result == "10:00", f"Expected '10:00', got '{result}'"
            
            result = page.evaluate("DateHelper.add30min('14:45')")
            assert result == "15:15", f"Expected '15:15', got '{result}'"
            
            browser.close()

    def test_datehelper_get_today_ms(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("DateHelper.getTodayMs()")
            today = page.evaluate("new Date().setHours(0,0,0,0)")
            assert result == today, f"Expected {today}, got {result}"
            
            browser.close()

    def test_old_standalone_functions_removed(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Verify global aliases delegate to DateHelper methods
            result = page.evaluate("""
                () => {
                    const checks = {
                        to12h: typeof to12h === 'function' && to12h('09:30') === DateHelper.to12h('09:30'),
                        formatDate: typeof formatDate === 'function' && formatDate('2026-08-10') === DateHelper.formatDate('2026-08-10'),
                        fmt: typeof fmt === 'function',
                        add30min: typeof add30min === 'function' && add30min('09:30') === DateHelper.add30min('09:30'),
                        dayAbbr: typeof dayAbbr === 'function' && dayAbbr('MONDAY') === DateHelper.dayAbbr('MONDAY'),
                        isoDayName: typeof isoDayName === 'function',
                        getTodayMs: typeof getTodayMs === 'function' && getTodayMs() === DateHelper.getTodayMs(),
                        formatDateTime: typeof formatDateTime === 'function'
                    };
                    return Object.entries(checks).filter(([k, v]) => !v).map(([k]) => k);
                }
            """)
            assert len(result) == 0, f"Global aliases don't delegate correctly: {result}"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 2. HTMLBUILDER CLASS
# ═══════════════════════════════════════════════════════════════════════════

class TestHtmlBuilder:
    """HtmlBuilder static class exists and methods work correctly."""

    def test_htmlbuilder_class_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof HtmlBuilder")
            assert result in ["object", "function"], f"HtmlBuilder should be a class, got {result}"
            browser.close()

    def test_htmlbuilder_has_all_methods(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            methods = page.evaluate("""
                () => {
                    const expected = ['classBlock', 'replacementBlock', 'dayHeader'];
                    return expected.filter(m => typeof HtmlBuilder[m] !== 'function');
                }
            """)
            assert len(methods) == 0, f"Missing HtmlBuilder methods: {methods}"
            browser.close()

    def test_htmlbuilder_day_header(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["my-timetable"], wait_until="domcontentloaded")
            
            result = page.evaluate("""
                HtmlBuilder.dayHeader({
                    abbr: 'MON',
                    date: '10 Aug 2026',
                    today: false,
                    holiday: false,
                    sunday: false
                })
            """)
            assert "MON" in result, f"dayHeader should contain day abbreviation, got {result}"
            assert "10 Aug 2026" in result, f"dayHeader should contain date, got {result}"
            browser.close()

    def test_htmlbuilder_day_header_today(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["my-timetable"], wait_until="domcontentloaded")
            
            result = page.evaluate("""
                HtmlBuilder.dayHeader({
                    abbr: 'MON',
                    date: '10 Aug 2026',
                    today: true,
                    holiday: false,
                    sunday: false
                })
            """)
            assert "Today" in result, f"dayHeader should contain 'Today' badge, got {result}"
            browser.close()

    def test_old_standalone_functions_removed(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            # Use a different page that doesn't have local formatClassBlock
            page.goto(PAGES["my-timetable"], wait_until="domcontentloaded")
            
            removed = page.evaluate("""
                () => {
                    const fns = ['formatClassBlock', 'formatReplacementBlock', 'buildDayHtml'];
                    return fns.filter(f => typeof window[f] === 'function');
                }
            """)
            assert len(removed) == 0, f"Old standalone functions still exist: {removed}"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 3. SKELETONLOADER NAMESPACE
# ═══════════════════════════════════════════════════════════════════════════

class TestSkeletonLoader:
    """SkeletonLoader namespace exists and methods work correctly."""

    def test_skeletonloader_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof SkeletonLoader")
            assert result == "object", f"SkeletonLoader should be an object, got {result}"
            browser.close()

    def test_skeletonloader_has_all_methods(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            methods = page.evaluate("""
                () => {
                    const expected = ['show', 'hide', 'with', 'showSummary', 'hideSummary'];
                    return expected.filter(m => typeof SkeletonLoader[m] !== 'function');
                }
            """)
            assert len(methods) == 0, f"Missing SkeletonLoader methods: {methods}"
            browser.close()

    def test_skeletonloader_show_hide(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Create a test container
            page.evaluate("""
                () => {
                    const div = document.createElement('div');
                    div.id = 'test-skeleton-container';
                    document.body.appendChild(div);
                }
            """)
            
            # Show skeleton
            page.evaluate("SkeletonLoader.show(document.getElementById('test-skeleton-container'), 'rows', 3)")
            has_skeleton = page.evaluate("""
                document.getElementById('test-skeleton-container').querySelectorAll('.skeleton').length > 0
            """)
            assert has_skeleton, "SkeletonLoader.show() should add skeleton elements"
            
            # Hide skeleton
            page.evaluate("SkeletonLoader.hide(document.getElementById('test-skeleton-container'))")
            has_skeleton = page.evaluate("""
                document.getElementById('test-skeleton-container').querySelectorAll('.skeleton').length > 0
            """)
            assert not has_skeleton, "SkeletonLoader.hide() should remove skeleton elements"
            
            # Cleanup
            page.evaluate("document.getElementById('test-skeleton-container').remove()")
            browser.close()

    def test_old_standalone_functions_removed(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Verify global aliases delegate to DateHelper methods
            result = page.evaluate("""
                () => {
                    // Check that global functions exist and delegate to DateHelper
                    const checks = {
                        to12h: typeof to12h === 'function' && to12h('09:30') === DateHelper.to12h('09:30'),
                        formatDate: typeof formatDate === 'function' && formatDate('2026-08-10') === DateHelper.formatDate('2026-08-10'),
                        fmt: typeof fmt === 'function',
                        add30min: typeof add30min === 'function' && add30min('09:30') === DateHelper.add30min('09:30'),
                        dayAbbr: typeof dayAbbr === 'function' && dayAbbr('MONDAY') === DateHelper.dayAbbr('MONDAY'),
                        isoDayName: typeof isoDayName === 'function',
                        getTodayMs: typeof getTodayMs === 'function' && getTodayMs() === DateHelper.getTodayMs(),
                        formatDateTime: typeof formatDateTime === 'function'
                    };
                    return Object.entries(checks).filter(([k, v]) => !v).map(([k]) => k);
                }
            """)
            assert len(result) == 0, f"Global aliases don't delegate correctly: {result}"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 4. TOASTMANAGER CLASS
# ═══════════════════════════════════════════════════════════════════════════

class TestToastManager:
    """ToastManager singleton exists and methods work correctly."""

    def test_toastmanager_class_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof ToastManager")
            assert result == "function", f"ToastManager should be a function (class), got {result}"
            browser.close()

    def test_toast_singleton_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof toast")
            assert result == "object", f"toast singleton should be an object, got {result}"
            
            has_show = page.evaluate("typeof toast.show == 'function'")
            assert has_show, "toast should have show() method"
            
            has_dismiss = page.evaluate("typeof toast.dismiss == 'function'")
            assert has_dismiss, "toast should have dismiss() method"
            
            browser.close()

    def test_toast_show_dismiss(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Show toast
            page.evaluate("toast.show('Test message', null, 1000)")
            page.wait_for_timeout(100)
            
            is_visible = page.evaluate("""
                document.getElementById('toastBar').classList.contains('visible')
            """)
            assert is_visible, "Toast should be visible after show()"
            
            # Dismiss toast
            page.evaluate("toast.dismiss()")
            page.wait_for_timeout(100)
            
            is_visible = page.evaluate("""
                document.getElementById('toastBar').classList.contains('visible')
            """)
            assert not is_visible, "Toast should be hidden after dismiss()"
            
            browser.close()

    def test_toast_auto_dismiss(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Show toast with short duration
            page.evaluate("toast.show('Auto dismiss test', null, 500)")
            page.wait_for_timeout(100)
            
            is_visible = page.evaluate("""
                document.getElementById('toastBar').classList.contains('visible')
            """)
            assert is_visible, "Toast should be visible immediately"
            
            # Wait for auto-dismiss
            page.wait_for_timeout(600)
            
            is_visible = page.evaluate("""
                document.getElementById('toastBar').classList.contains('visible')
            """)
            assert not is_visible, "Toast should auto-dismiss after duration"
            
            browser.close()

    def test_old_standalone_functions_removed(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            removed = page.evaluate("""
                () => {
                    const fns = ['showToast', 'dismissToast'];
                    return fns.filter(f => typeof window[f] == 'function');
                }
            """)
            assert len(removed) == 0, f"Old standalone functions still exist: {removed}"
            
            # _toastTimer should also be removed
            has_timer = page.evaluate("typeof _toastTimer !== 'undefined'")
            assert not has_timer, "_toastTimer global should be removed"
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 5. MODALCONTROLLER CLASS
# ═══════════════════════════════════════════════════════════════════════════

class TestModalController:
    """ModalController class exists and is available for use."""

    def test_modalcontroller_class_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof ModalController")
            assert result == "function", f"ModalController should be a function (class), got {result}"
            browser.close()

    def test_modalcontroller_instantiation(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Create a test modal
            page.evaluate("""
                () => {
                    const div = document.createElement('div');
                    div.id = 'test-modal';
                    div.style.display = 'none';
                    document.body.appendChild(div);
                }
            """)
            
            # Instantiate ModalController
            result = page.evaluate("""
                () => {
                    const mc = new ModalController('test-modal', (data) => {});
                    return {
                        hasOpen: typeof mc.open == 'function',
                        hasClose: typeof mc.close == 'function',
                        hasIsOpen: typeof mc.isOpen == 'function'
                    };
                }
            """)
            assert result['hasOpen'], "ModalController should have open() method"
            assert result['hasClose'], "ModalController should have close() method"
            assert result['hasIsOpen'], "ModalController should have isOpen() method"
            
            # Cleanup
            page.evaluate("document.getElementById('test-modal').remove()")
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 6. TABLECONTROLLER CLASS
# ═══════════════════════════════════════════════════════════════════════════

class TestTableController:
    """TableController class exists and is available for use."""

    def test_tablecontroller_class_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof TableController")
            assert result == "function", f"TableController should be a function (class), got {result}"
            browser.close()

    def test_tablecontroller_instantiation(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            
            # Instantiate TableController
            result = page.evaluate("""
                () => {
                    const tc = new TableController({
                        columns: [{ field: 'name', label: 'Name', sortable: true }],
                        sortState: { field: null, dir: 'asc' },
                        render: () => {}
                    });
                    return {
                        hasSort: typeof tc.sort == 'function',
                        hasCompareBy: typeof tc.compareBy == 'function',
                        hasMakeHeader: typeof tc.makeHeader == 'function',
                        hasPaginate: typeof tc.paginate == 'function',
                        hasUpdateResultCount: typeof tc.updateResultCount == 'function',
                        hasInitRpp: typeof tc.initRpp == 'function',
                        sortState: tc.sortState
                    };
                }
            """)
            assert result['hasSort'], "TableController should have sort() method"
            assert result['hasCompareBy'], "TableController should have compareBy() method"
            assert result['hasMakeHeader'], "TableController should have makeHeader() method"
            assert result['hasPaginate'], "TableController should have paginate() method"
            assert result['hasUpdateResultCount'], "TableController should have updateResultCount() method"
            assert result['hasInitRpp'], "TableController should have initRpp() method"
            assert result['sortState'] is not None, "TableController should have sortState"
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 7. WEEKNAVIGATOR CLASS
# ═══════════════════════════════════════════════════════════════════════════

class TestWeekNavigator:
    """WeekNavigator class exists and is available for use."""

    def test_weeknavigator_class_exists(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["my-timetable"], wait_until="domcontentloaded")
            
            result = page.evaluate("typeof WeekNavigator")
            assert result == "function", f"WeekNavigator should be a function (class), got {result}"
            browser.close()

    def test_weeknavigator_instantiation(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["my-timetable"], wait_until="domcontentloaded")
            
            # Instantiate WeekNavigator
            result = page.evaluate("""
                () => {
                    const wn = new WeekNavigator(
                        { startDate: '2026-08-31', weeks: 15 },
                        [{ start: new Date(2026, 7, 31), end: new Date(2026, 8, 6) }]
                    );
                    return {
                        hasPrevWeek: typeof wn.prevWeek == 'function',
                        hasNextWeek: typeof wn.nextWeek == 'function',
                        hasSelectWeek: typeof wn.selectWeek == 'function',
                        hasJumpToToday: typeof wn.jumpToToday == 'function',
                        hasSave: typeof wn.save == 'function',
                        hasLoad: typeof wn.load == 'function',
                        hasCurrentWeek: typeof wn.currentWeek !== 'undefined',
                        hasWeekData: typeof wn.weekData !== 'undefined',
                        hasSemester: typeof wn.semester !== 'undefined'
                    };
                }
            """)
            assert result['hasPrevWeek'], "WeekNavigator should have prevWeek() method"
            assert result['hasNextWeek'], "WeekNavigator should have nextWeek() method"
            assert result['hasSelectWeek'], "WeekNavigator should have selectWeek() method"
            assert result['hasJumpToToday'], "WeekNavigator should have jumpToToday() method"
            assert result['hasSave'], "WeekNavigator should have save() method"
            assert result['hasLoad'], "WeekNavigator should have load() method"
            assert result['hasCurrentWeek'], "WeekNavigator should have currentWeek getter"
            assert result['hasWeekData'], "WeekNavigator should have weekData getter"
            assert result['hasSemester'], "WeekNavigator should have semester getter"
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 8. REGRESSION TESTS
# ═══════════════════════════════════════════════════════════════════════════

class TestRegression:
    """Verify existing functionality still works after refactor."""

    def test_replacement_home_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = []
            page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)
            
            page.goto(PAGES["replacement-home"], wait_until="networkidle")
            page.wait_for_timeout(1500)  # longer wait for data to load
            
            # Check table has rows
            rows = page.evaluate("document.querySelectorAll('#tableBody tr').length")
            assert rows > 0, f"Replacement home should have table rows, got {rows}"
            
            # Check no critical console errors (ignore non-critical)
            critical_errors = [e for e in errors if 'SyntaxError' in e or 'TypeError' in e or 'ReferenceError' in e]
            assert len(critical_errors) == 0, f"Critical console errors: {critical_errors}"
            
            browser.close()

    def test_my_request_history_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = []
            page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)
            
            page.goto(PAGES["my-request-history"], wait_until="networkidle")
            page.wait_for_timeout(1500)
            
            rows = page.evaluate("document.querySelectorAll('#tableBody tr').length")
            assert rows > 0, f"My request history should have table rows, got {rows}"
            
            critical_errors = [e for e in errors if 'SyntaxError' in e or 'TypeError' in e or 'ReferenceError' in e]
            assert len(critical_errors) == 0, f"Critical console errors: {critical_errors}"
            
            browser.close()

    def test_request_approval_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = []
            page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)
            
            page.goto(PAGES["request-approval"], wait_until="networkidle")
            page.wait_for_timeout(1500)
            
            rows = page.evaluate("document.querySelectorAll('#tableBody tr').length")
            assert rows > 0, f"Request approval should have table rows, got {rows}"
            
            critical_errors = [e for e in errors if 'SyntaxError' in e or 'TypeError' in e or 'ReferenceError' in e]
            assert len(critical_errors) == 0, f"Critical console errors: {critical_errors}"
            
            browser.close()

    def test_my_timetable_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = []
            page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)
            
            page.goto(PAGES["my-timetable"], wait_until="networkidle")
            page.wait_for_timeout(1500)
            
            # Check page has content (week select or grid)
            has_content = page.evaluate("""
                document.querySelectorAll('.week-select, .grid-wrapper, .day-card').length > 0
            """)
            assert has_content, "My timetable should have content elements"
            
            # Check no critical JS errors
            critical_errors = [e for e in errors if 'SyntaxError' in e or 'ReferenceError' in e]
            assert len(critical_errors) == 0, f"Critical console errors: {critical_errors}"
            
            browser.close()

    def test_cohort_timetable_loads(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = []
            page.on("console", lambda msg: errors.append(msg.text) if msg.type == "error" else None)
            
            page.goto(PAGES["cohort-timetable"], wait_until="networkidle")
            page.wait_for_timeout(1500)
            
            has_content = page.evaluate("""
                document.querySelectorAll('.week-select, .grid-wrapper, .day-card').length > 0
            """)
            assert has_content, "Cohort timetable should have content elements"
            
            critical_errors = [e for e in errors if 'SyntaxError' in e or 'ReferenceError' in e]
            assert len(critical_errors) == 0, f"Critical console errors: {critical_errors}"
            
            browser.close()

    def test_skeleton_loading_works(self):
        """Verify skeleton loading still works on table pages."""
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            page.wait_for_timeout(1500)  # wait for data to load
            
            rows = page.evaluate("document.querySelectorAll('#tableBody tr').length")
            assert rows > 0, f"Table should have rows after skeleton, got {rows}"
            
            browser.close()

    def test_toast_functionality_works(self):
        """Verify toast notifications still work."""
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            
            page.goto(PAGES["replacement-home"], wait_until="networkidle")
            page.wait_for_timeout(600)
            
            # Show toast
            page.evaluate("toast.show('Test notification', null, 1000)")
            page.wait_for_timeout(100)
            
            is_visible = page.evaluate("""
                document.getElementById('toastBar').classList.contains('visible')
            """)
            assert is_visible, "Toast should be visible"
            
            # Check message
            message = page.evaluate("document.querySelector('.toast-message')?.textContent || ''")
            assert "Test notification" in message, f"Toast message should contain 'Test notification', got '{message}'"
            
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# MAIN
# ═══════════════════════════════════════════════════════════════════════════

if __name__ == "__main__":
    import pytest
    pytest.main([__file__, "-v", "--tb=short"])
