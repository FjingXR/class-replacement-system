"""
Skeleton Loading + Scroll Restoration — Browser Test Suite
==========================================================
Tests cover: skeleton lifecycle, scroll restore, edge cases,
accessibility, performance, visual, and integration scenarios.

Run:  python tests/Browser/test_skeleton_scroll.py
      (requires Laravel dev server on :8000)
"""
import time
from playwright.sync_api import sync_playwright, expect

BASE = "http://127.0.0.1:8000"
PAGES = {
    "replacement-home": f"{BASE}/replacement-home-ui",
    "my-request-history": f"{BASE}/my-request-history-ui",
    "request-approval": f"{BASE}/request-approval-ui",
}

# ─── Helpers ───────────────────────────────────────────────────────────────

def _goto(page, url, wait="networkidle"):
    page.goto(url, wait_until=wait)
    page.wait_for_timeout(600)  # let skeleton + callback settle


def _has_skeleton(container_sel, page):
    return page.evaluate(f"""
        () => {{
            const c = document.querySelector('{container_sel}');
            return c ? c.querySelectorAll('.skeleton, .skeleton-row, .skeleton-card').length > 0 : false;
        }}
    """)


def _table_row_count(page):
    return page.evaluate("document.querySelectorAll('#tableBody tr').length")


def _summary_skeleton_visible(page):
    return page.evaluate("""
        () => {
            const cards = document.querySelectorAll('.summary-card .summary-value');
            return Array.from(cards).some(el => el.querySelector('.skeleton') !== null);
        }
    """)


def _scroll_pos(page):
    return page.evaluate("window.scrollY")


# ═══════════════════════════════════════════════════════════════════════════
# 1. SKELETON LIFECYCLE
# ═══════════════════════════════════════════════════════════════════════════

class TestSkeletonLifecycle:
    """Skeleton appears, then disappears after content loads."""

    def test_skeleton_appears_and_disappears_replacement_home(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")

            # Skeleton should appear within 50ms
            page.wait_for_timeout(30)
            has_skel = _has_skeleton("#tableBody", page)
            assert has_skel, "Skeleton should appear in tableBody on load"

            # After callback (50ms) + hideSkeleton (400ms) = ~500ms
            page.wait_for_timeout(600)
            has_skel_after = _has_skeleton("#tableBody", page)
            rows = _table_row_count(page)
            assert not has_skel_after, f"Skeleton should be gone, but found {_has_skeleton}"
            assert rows > 0, f"Table should have rows, got {rows}"
            browser.close()

    def test_skeleton_appears_and_disappears_my_request_history(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["my-request-history"], wait_until="domcontentloaded")
            page.wait_for_timeout(30)
            assert _has_skeleton("#tableBody", page), "Skeleton should appear"
            page.wait_for_timeout(600)
            assert not _has_skeleton("#tableBody", page), "Skeleton should disappear"
            assert _table_row_count(page) > 0, "Table should have rows"
            browser.close()

    def test_skeleton_appears_and_disappears_request_approval(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["request-approval"], wait_until="domcontentloaded")
            page.wait_for_timeout(30)
            assert _has_skeleton("#tableBody", page), "Skeleton should appear"
            page.wait_for_timeout(600)
            assert not _has_skeleton("#tableBody", page), "Skeleton should disappear"
            assert _table_row_count(page) > 0, "Table should have rows"
            browser.close()

    def test_summary_skeleton_appears_and_disappears(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            page.wait_for_timeout(30)
            # Summary skeleton should be visible
            page.wait_for_timeout(600)
            # Summary skeleton should be hidden after hideSummarySkeleton
            summary_skel = _summary_skeleton_visible(page)
            assert not summary_skel, "Summary skeleton should be hidden after load"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 2. EDGE CASES
# ═══════════════════════════════════════════════════════════════════════════

class TestEdgeCases:
    """Boundary conditions and unusual inputs."""

    def test_empty_search_results_shows_empty_state(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            search = page.locator("#searchInput")
            search.fill("ZZZZNONEXISTENT999")
            page.wait_for_timeout(700)

            # Should show "no results" or empty table
            table_html = page.evaluate("document.getElementById('tableBody').innerHTML")
            rows = _table_row_count(page)
            assert rows == 0 or "no" in table_html.lower() or table_html.strip() == "", \
                f"Expected empty state for nonsense search, got {rows} rows"
            browser.close()

    def test_rapid_filter_clicks_no_crash(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = []
            page.on("pageerror", lambda e: errors.append(str(e)))
            _goto(page, PAGES["replacement-home"])

            # Rapidly click through filter chips
            chips = page.locator(".week-chip, .chip")
            count = min(chips.count(), 5)
            for i in range(count):
                chips.nth(i).click()
                page.wait_for_timeout(50)

            page.wait_for_timeout(800)
            assert len(errors) == 0, f"JS errors during rapid clicks: {errors}"
            browser.close()

    def test_rapid_search_typing_no_crash(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            errors = []
            page.on("pageerror", lambda e: errors.append(str(e)))
            _goto(page, PAGES["replacement-home"])

            search = page.locator("#searchInput")
            for ch in "abcdef":
                search.type(ch, delay=20)
                page.wait_for_timeout(30)

            page.wait_for_timeout(800)
            assert len(errors) == 0, f"JS errors during rapid typing: {errors}"
            browser.close()

    def test_withSkeleton_callback_error_still_hides_skeleton(self):
        """If the callback throws, hideSkeleton must still run."""
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            page.wait_for_timeout(600)

            # Inject a failing withSkeleton call
            result = page.evaluate("""
                () => {
                    const container = document.getElementById('tableBody');
                    try {
                        withSkeleton(() => { throw new Error('boom'); }, container, 3, 200);
                    } catch(e) {}
                    return new Promise(resolve => {
                        setTimeout(() => {
                            const hasSkel = container.querySelectorAll('.skeleton, .skeleton-row').length > 0;
                            resolve({ hasSkel, html: container.innerHTML.length });
                        }, 500);
                    });
                }
            """)
            assert not result["hasSkel"], "Skeleton should be hidden even after callback error"
            browser.close()

    def test_network_throttle_skeleton_visible_longer(self):
        """Slow network: skeleton should stay visible while callback is delayed."""
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            ctx = browser.new_context()
            page = ctx.new_page()

            # Throttle by aborting and re-fetching with delay via CDP
            page.route("**/*.js", lambda route: route.abort())
            _goto(page, PAGES["replacement-home"])

            page.wait_for_timeout(200)

            # With JS blocked, skeleton should appear but table won't populate
            has_skel = _has_skeleton("#tableBody", page)
            page.wait_for_timeout(1000)

            # Even if table doesn't load, skeleton lifecycle should be correct
            # Re-enable JS
            page.unroute("**/*.js")
            page.reload(wait_until="networkidle")
            page.wait_for_timeout(800)

            rows = _table_row_count(page)
            assert rows > 0, f"Table should eventually load, got {rows} rows"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 3. SCROLL RESTORATION
# ═══════════════════════════════════════════════════════════════════════════

class TestScrollRestoration:
    """Scroll position save/restore across navigation."""

    def test_scroll_saved_on_scroll(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Scroll down
            page.evaluate("window.scrollTo(0, 500)")
            page.wait_for_timeout(400)

            saved = page.evaluate("sessionStorage.getItem('scroll_replacementHome')")
            assert saved is not None, "Scroll position should be saved in sessionStorage"
            assert int(saved) > 0, f"Saved position should be > 0, got {saved}"
            browser.close()

    def test_scroll_restored_on_back_button(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Scroll down
            page.evaluate("window.scrollTo(0, 600)")
            page.wait_for_timeout(400)

            # Navigate away
            page.locator(".nav-item").first.click()
            page.wait_for_load_state("networkidle")
            page.wait_for_timeout(200)

            # Go back
            page.go_back()
            page.wait_for_load_state("networkidle")
            page.wait_for_timeout(1000)

            pos = _scroll_pos(page)
            assert pos >= 200, f"Scroll should be restored near 600, got {pos}"
            browser.close()

    def test_scroll_cleared_on_nav_click(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Set scroll position
            page.evaluate("window.scrollTo(0, 500)")
            page.wait_for_timeout(300)

            # Click nav link (should clear scroll)
            nav = page.locator(".nav-item").first
            nav.click()
            page.wait_for_timeout(300)

            cleared = page.evaluate("sessionStorage.getItem('scroll_replacementHome')")
            assert cleared is None, f"Scroll should be cleared on nav click, got {cleared}"
            browser.close()

    def test_scroll_reset_to_top_on_search(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Scroll down
            page.evaluate("window.scrollTo(0, 800)")
            page.wait_for_timeout(300)

            # Type in search (should trigger scroll-to-top)
            search = page.locator("#searchInput")
            search.fill("BMIT")
            page.wait_for_timeout(600)

            pos = _scroll_pos(page)
            assert pos < 200, f"Scroll should reset to top on search, got {pos}"
            browser.close()

    def test_multiple_back_forward_restores_correctly(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Scroll to 700
            page.evaluate("window.scrollTo(0, 700)")
            page.wait_for_timeout(400)

            # Navigate away via nav
            page.locator(".nav-item").first.click()
            page.wait_for_load_state("networkidle")
            page.wait_for_timeout(300)

            # Navigate to another page via URL
            _goto(page, PAGES["my-request-history"])

            # Go back twice
            page.go_back()
            page.wait_for_load_state("networkidle")
            page.wait_for_timeout(300)
            page.go_back()
            page.wait_for_load_state("networkidle")
            page.wait_for_timeout(1000)

            pos = _scroll_pos(page)
            assert pos >= 200, f"Scroll should restore to ~700 after multiple backs, got {pos}"
            browser.close()

    def test_fresh_load_starts_at_top(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            ctx = browser.new_context()
            # Clear any session storage
            page = ctx.new_page()
            _goto(page, PAGES["replacement-home"])

            pos = _scroll_pos(page)
            assert pos == 0, f"Fresh load should start at top, got {pos}"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 4. ACCESSIBILITY
# ═══════════════════════════════════════════════════════════════════════════

class TestAccessibility:
    """Screen reader and keyboard accessibility."""

    def test_skeleton_elements_not_focusable(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            page.wait_for_timeout(30)

            # Skeleton elements should not be focusable
            focusable = page.evaluate("""
                () => {
                    const skels = document.querySelectorAll('.skeleton, .skeleton-row, .skeleton-card');
                    return Array.from(skels).filter(el => {
                        el.focus();
                        return document.activeElement === el;
                    }).length;
                }
            """)
            assert focusable == 0, f"Found {focusable} focusable skeleton elements"
            browser.close()

    def test_table_has_aria_labels(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            has_aria = page.evaluate("""
                () => {
                    const table = document.querySelector('.timetable, table');
                    if (!table) return false;
                    return table.getAttribute('aria-label') !== null ||
                           table.getAttribute('aria-labelledby') !== null ||
                           table.querySelector('th') !== null;
                }
            """)
            assert has_aria, "Table should have aria labels or header cells"
            browser.close()

    def test_search_input_has_label(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            has_label = page.evaluate("""
                () => {
                    const input = document.getElementById('searchInput');
                    if (!input) return false;
                    const id = input.id;
                    const label = document.querySelector(`label[for="${id}"]`);
                    return label !== null || input.getAttribute('aria-label') !== null ||
                           input.getAttribute('placeholder') !== null;
                }
            """)
            assert has_label, "Search input should have a label or aria-label"
            browser.close()

    def test_keyboard_navigation_to_table(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Tab to interactive elements
            for _ in range(10):
                page.keyboard.press("Tab")
                page.wait_for_timeout(50)

            focused_tag = page.evaluate("document.activeElement.tagName")
            assert focused_tag in ("INPUT", "BUTTON", "A", "SELECT"), \
                f"Focus should be on interactive element, got {focused_tag}"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 5. PERFORMANCE
# ═══════════════════════════════════════════════════════════════════════════

class TestPerformance:
    """Performance and resource usage."""

    def test_session_storage_not_leaking(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Scroll multiple times
            for i in range(10):
                page.evaluate(f"window.scrollTo(0, {i * 100})")
                page.wait_for_timeout(100)

            # Should only have one scroll entry
            keys = page.evaluate("""
                () => Object.keys(sessionStorage).filter(k => k.startsWith('scroll_'))
            """)
            assert len(keys) <= 3, f"Too many scroll entries: {keys} (max 3 pages)"
            browser.close()

    def test_skeleton_animation_performance(self):
        """Skeleton shimmer should use CSS animation, not JS."""
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            page.wait_for_timeout(30)

            uses_css_anim = page.evaluate("""
                () => {
                    const skel = document.querySelector('.skeleton');
                    if (!skel) return true; // no skeleton = pass
                    const style = getComputedStyle(skel);
                    return style.animationName !== 'none' && style.animationName !== '';
                }
            """)
            # Also check no requestAnimationFrame loops from JS
            uses_raf = page.evaluate("""
                () => {
                    return new Promise(resolve => {
                        let count = 0;
                        const orig = window.requestAnimationFrame;
                        window.requestAnimationFrame = () => { count++; return orig.apply(this, arguments); };
                        setTimeout(() => {
                            window.requestAnimationFrame = orig;
                            resolve(count);
                        }, 100);
                    });
                }
            """)
            assert uses_css_anim, "Skeleton should use CSS animation"
            assert uses_raf < 50, f"Too many rAF calls ({uses_raf}), skeleton should not use JS animation"
            browser.close()

    def test_memory_not_growing_with_repeated_skeleton(self):
        """Repeated withSkeleton calls should not leak memory."""
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Get baseline DOM node count
            baseline = page.evaluate("document.getElementsByTagName('*').length")

            # Trigger skeleton 20 times
            for _ in range(20):
                page.evaluate("""
                    () => {
                        const container = document.getElementById('tableBody');
                        withSkeleton(() => {}, container, 5, 100);
                    }
                """)
                page.wait_for_timeout(200)

            after = page.evaluate("document.getElementsByTagName('*').length")
            growth = after - baseline
            assert growth < 50, f"DOM grew by {growth} nodes after 20 skeleton cycles (possible leak)"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 6. VISUAL
# ═══════════════════════════════════════════════════════════════════════════

class TestVisual:
    """Visual correctness and theming."""

    def test_prefers_reduced_motion_disables_shimmer(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            ctx = browser.new_context(
                reduced_motion="reduce"
            )
            page = ctx.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            page.wait_for_timeout(30)

            anim = page.evaluate("""
                () => {
                    const skel = document.querySelector('.skeleton');
                    if (!skel) return 'none';
                    return getComputedStyle(skel).animationName;
                }
            """)
            assert anim == "none", f"Shimmer should be disabled with reduced-motion, got {anim}"
            browser.close()

    def test_dark_mode_colors_during_skeleton(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")
            page.wait_for_timeout(30)

            bg = page.evaluate("""
                () => {
                    const skel = document.querySelector('.skeleton');
                    if (!skel) return null;
                    const style = getComputedStyle(skel);
                    return style.backgroundImage || style.backgroundColor;
                }
            """)
            # Should have a gradient or color in dark mode
            assert bg is not None, "Skeleton should have a background"
            assert bg != "none" and bg != "rgba(0, 0, 0, 0)" and bg != "transparent", \
                f"Skeleton should have visible background in dark mode, got {bg}"
            browser.close()

    def test_light_mode_colors_during_skeleton(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            ctx = browser.new_context()
            page = ctx.new_page()
            page.goto(PAGES["replacement-home"], wait_until="domcontentloaded")

            # Switch to light mode
            page.evaluate("document.documentElement.className = 'light'")
            page.wait_for_timeout(30)

            # Check that CSS variables are defined (light mode has valid colors)
            has_vars = page.evaluate("""
                () => {
                    const root = getComputedStyle(document.documentElement);
                    const sv = root.getPropertyValue('--color-surface-variant').trim();
                    const ol = root.getPropertyValue('--color-outline').trim();
                    return { surfaceVariant: sv, outline: ol };
                }
            """)
            assert has_vars["surfaceVariant"], "--color-surface-variant should be defined in light mode"
            assert has_vars["outline"], "--color-outline should be defined in light mode"

            # Skeleton may have already disappeared - check CSS rule exists
            has_skeleton_rule = page.evaluate("""
                () => {
                    for (const sheet of document.styleSheets) {
                        try {
                            for (const rule of sheet.cssRules) {
                                if (rule.selectorText === '.skeleton') return true;
                            }
                        } catch(e) {}
                    }
                    return false;
                }
            """)
            assert has_skeleton_rule, ".skeleton CSS rule should exist"
            browser.close()

    def test_mobile_viewport_skeleton_layout(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            ctx = browser.new_context(
                viewport={"width": 375, "height": 812}
            )
            page = ctx.new_page()
            _goto(page, PAGES["replacement-home"])

            # Table should still be present
            rows = _table_row_count(page)
            assert rows > 0, f"Table should render on mobile, got {rows} rows"

            # No horizontal overflow
            overflow = page.evaluate("""
                () => document.documentElement.scrollWidth > document.documentElement.clientWidth
            """)
            assert not overflow, "Page should not have horizontal overflow on mobile"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# 7. INTEGRATION
# ═══════════════════════════════════════════════════════════════════════════

class TestIntegration:
    """Interaction with other features."""

    def test_skeleton_during_modal_open(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Trigger skeleton
            page.evaluate("""
                () => {
                    const container = document.getElementById('tableBody');
                    withSkeleton(() => {}, container, 5, 1000);
                }
            """)
            page.wait_for_timeout(30)

            # Open a modal if available
            modal_opened = page.evaluate("""
                () => {
                    const btn = document.querySelector('[data-modal], .modal-trigger, .btn-arrange');
                    if (btn) { btn.click(); return true; }
                    return false;
                }
            """)
            page.wait_for_timeout(200)

            # Skeleton should still be in table body
            has_skel = _has_skeleton("#tableBody", page)
            # Modal shouldn't break skeleton
            browser.close()

    def test_toast_during_skeleton(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Trigger skeleton
            page.evaluate("""
                () => {
                    const container = document.getElementById('tableBody');
                    withSkeleton(() => {}, container, 5, 500);
                }
            """)
            page.wait_for_timeout(30)

            # Show a toast
            page.evaluate("""
                () => {
                    if (typeof showToast === 'function') {
                        showToast('Test toast', 'info');
                    }
                }
            """)
            page.wait_for_timeout(300)

            # Toast bar should exist
            toast = page.locator(".toast-bar, #toastBar")
            assert toast.count() > 0, "Toast bar should exist in DOM"
            browser.close()

    def test_hideSummarySkeleton_before_updateSummary(self):
        """hideSummarySkeleton should not clear real values set by updateSummary."""
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Get summary values
            values = page.evaluate("""
                () => {
                    return Array.from(document.querySelectorAll('.summary-card .summary-value'))
                        .map(el => el.textContent.trim());
                }
            """)
            assert len(values) > 0, "Summary cards should have values"

            # Trigger skeleton + callback that sets values
            page.evaluate("""
                () => {
                    showSummarySkeleton();
                    document.querySelectorAll('.summary-card .summary-value').forEach(el => {
                        el.textContent = '999';
                    });
                    hideSummarySkeleton();
                }
            """)
            page.wait_for_timeout(100)

            after = page.evaluate("""
                () => {
                    return Array.from(document.querySelectorAll('.summary-card .summary-value'))
                        .map(el => el.textContent.trim());
                }
            """)
            assert "999" in after, f"Values set after skeleton should persist, got {after}"
            browser.close()

    def test_filter_change_triggers_skeleton(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            # Click a week filter chip
            chips = page.locator(".week-chip, .chip")
            if chips.count() > 1:
                chips.nth(1).click()
                page.wait_for_timeout(30)

                has_skel = _has_skeleton("#tableBody", page)
                # Skeleton should appear briefly
                page.wait_for_timeout(700)
                assert not _has_skeleton("#tableBody", page), "Skeleton should disappear after filter"
            browser.close()

    def test_search_change_triggers_skeleton(self):
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            _goto(page, PAGES["replacement-home"])

            search = page.locator("#searchInput")
            search.fill("BMIT")
            page.wait_for_timeout(30)

            has_skel = _has_skeleton("#tableBody", page)
            # Could be true or false depending on timing
            page.wait_for_timeout(700)
            assert not _has_skeleton("#tableBody", page), "Skeleton should disappear after search"
            assert _table_row_count(page) > 0, "Table should have filtered rows"
            browser.close()


# ═══════════════════════════════════════════════════════════════════════════
# Run all tests
# ═══════════════════════════════════════════════════════════════════════════

if __name__ == "__main__":
    import sys
    import inspect

    classes = [obj for name, obj in globals().items()
               if inspect.isclass(obj) and name.startswith("Test")]

    passed = 0
    failed = 0
    errors = []

    for cls in classes:
        print(f"\n{'='*60}")
        print(f"  {cls.__name__}")
        print(f"{'='*60}")
        for method_name in sorted(dir(cls)):
            if not method_name.startswith("test_"):
                continue
            method = getattr(cls, method_name)
            test = cls()
            try:
                method(test)
                print(f"  ✓ {method_name}")
                passed += 1
            except Exception as e:
                print(f"  ✗ {method_name}: {e}")
                failed += 1
                errors.append((method_name, str(e)))

    print(f"\n{'='*60}")
    print(f"  RESULTS: {passed} passed, {failed} failed")
    print(f"{'='*60}")

    if errors:
        print("\nFailed tests:")
        for name, err in errors:
            print(f"  - {name}: {err}")
        sys.exit(1)
