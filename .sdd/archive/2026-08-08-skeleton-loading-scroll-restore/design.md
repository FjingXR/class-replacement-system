# Design: Skeleton Loading + Scroll Restoration

## Technical Approach

### Feature 1: Skeleton Loading

**Architecture:**
- CSS in `theme.css` — existing `.skeleton*` classes + new `prefers-reduced-motion` media query
- JS in `ui-common.js` — existing `showSkeleton()`/`hideSkeleton()` + new `withSkeleton()` wrapper
- Pages call shared functions; no copy-paste

**Existing Code (no changes):**
```css
/* theme.css:1683-1709 */
.skeleton {
    background: linear-gradient(90deg, var(--color-surface-variant) 25%, var(--color-outline) 50%, var(--color-surface-variant) 75%);
    background-size: 200% 100%;
    animation: skeleton-shimmer 1.5s infinite;
    border-radius: var(--radius-sm);
}
@keyframes skeleton-shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.skeleton-row { height: 60px; margin-bottom: 8px; }
.skeleton-card { height: 80px; margin-bottom: 12px; }
.skeleton-text { height: 14px; margin-bottom: 8px; width: 80%; }
```

```js
// ui-common.js:410-421
function showSkeleton(container, type = 'rows', count = 5) {
    container.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const el = document.createElement('div');
        el.className = `skeleton skeleton-${type === 'rows' ? 'row' : 'card'}`;
        container.appendChild(el);
    }
}
function hideSkeleton(container) { container.innerHTML = ''; }
```

**New Code:**

1. **`prefers-reduced-motion` media query** (theme.css, append after line 1709):
```css
@media (prefers-reduced-motion: reduce) {
    .skeleton { animation: none; }
}
```

2. **`withSkeleton(callback, container, count=10, delay=400)`** (ui-common.js):
```js
// Imperative — executes after 50ms delay (not a factory)
// 50ms delay allows browser to paint skeleton before callback replaces it
// Shows skeleton, awaits callback (sync or async), hides skeleton after delay
// try/finally ensures hideSkeleton runs even if callback throws
function withSkeleton(callback, container, count = 10, delay = 400) {
    showSkeleton(container, 'rows', count);
    const hide = () => setTimeout(() => hideSkeleton(container), delay);
    setTimeout(() => {
        try {
            const result = callback();
            if (result && typeof result.then === 'function') {
                return result.then(hide, (err) => { hide(); throw err; });
            }
            hide();
        } catch (err) { hide(); throw err; }
    }, 50);
}
```

3. **`showSummarySkeleton()`** (ui-common.js):
```js
// Skeleton-izes summary card values (shared across all pages)
// Stores original HTML in dataset for restoration
function showSummarySkeleton() {
    document.querySelectorAll('.summary-card .summary-value').forEach(el => {
        el.dataset.original = el.innerHTML;
        el.innerHTML = '<div class="skeleton" style="height:24px;width:40px;display:inline-block"></div>';
    });
}

function hideSummarySkeleton() {
    document.querySelectorAll('.summary-card .summary-value').forEach(el => {
        if (el.dataset.original !== undefined) {
            // Don't restore from dataset.original — it captured the default "0" before real data
            // Just clean up the marker; the page's updateSummary() will set real values
            delete el.dataset.original;
        }
    });
}
```

**Note:** `hideSummarySkeleton()` does NOT restore from `dataset.original` — that value was captured before `renderTable()`/`updateSummary()` ran, so it would overwrite real data back to "0". Instead, it only cleans up the marker. The page's `updateSummary()` function (called inside `renderTable()`) sets the actual values.

**Page Wiring Pattern:**
```js
// Initial load
const tableBody = document.getElementById('tableBody');
showSummarySkeleton();
withSkeleton(() => renderTable(data), tableBody, 10, 400);

// On filter/search change
window.scrollTo(0, 0);  // Reset scroll to top
showSummarySkeleton();
withSkeleton(() => renderTable(filteredData), tableBody, 10, 400);
```

### Feature 2: Scroll Restoration

**Architecture:**
- JS in `ui-common.js` — existing `saveScrollPosition()`/`restoreScrollPosition()` + new `clearScrollPosition()`/`initScrollRestore()`
- Global wiring in `ui-template.blade.php` — one `initScrollRestore()` call per page load

**Existing Code (no changes):**
```js
// ui-common.js:425-442
function saveScrollPosition(key) { sessionStorage.setItem('scroll_' + key, window.scrollY); }
function restoreScrollPosition(key) {
    const pos = sessionStorage.getItem('scroll_' + key);
    if (pos) window.scrollTo(0, parseInt(pos));
}
// Auto-save on scroll (debounced 200ms) — covers beforeunload case
window.addEventListener('scroll', () => {
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(() => {
        const pageKey = document.body.dataset.page;
        if (pageKey) saveScrollPosition(pageKey);
    }, 200);
}, { passive: true });
```

**Note:** The existing debounced scroll listener (ui-common.js:434-442) already saves scroll position continuously. This covers the `beforeunload` case — no explicit `beforeunload` handler is needed. The `initScrollRestore()` function adds the missing restore and clear behaviors.

**New Code:**

1. **`clearScrollPosition(key)`** (ui-common.js):
```js
function clearScrollPosition(key) { sessionStorage.removeItem('scroll_' + key); }
```

2. **`initScrollRestore(pageKey)`** (ui-common.js):
```js
function initScrollRestore(pageKey) {
    // Restore on pageshow (back/forward navigation)
    window.addEventListener('pageshow', (e) => {
        if (e.persisted) restoreScrollPosition(pageKey);
    });
    
    // Clear on deliberate navigation (nav link clicks within the app)
    document.querySelectorAll('.nav-item, .nav-drawer-item').forEach(link => {
        link.addEventListener('click', () => clearScrollPosition(pageKey));
    });
    
    // Expose scrollToTop for filter/search handlers
    window._scrollToTop = function() { window.scrollTo(0, 0); };
}
```

3. **Global wiring** (ui-template.blade.php, inside existing DOMContentLoaded):
```js
document.addEventListener('DOMContentLoaded', function() {
    var pageKey = document.body.dataset.page;
    if (pageKey) initScrollRestore(pageKey);
    // ... existing initMobileNav() call
});
```

**Data Flow:**
```
Page load → initScrollRestore('page-name')
  ↓
Scroll events → debounced save → sessionStorage['scroll_page-name'] (existing auto-save)
  ↓
User clicks nav link → clearScrollPosition() → sessionStorage.removeItem()
  ↓
User clicks Back → pageshow (e.persisted=true) → restoreScrollPosition() → window.scrollTo()
```

## Cross-Module Data Flows

| Flow | From | To | Data | Existing? |
|------|------|-----|------|-----------|
| Skeleton show (table) | Page JS | ui-common.js | container, type='rows', count=10 | ✅ Yes (count default changes 5→10 via call site) |
| Skeleton hide | ui-common.js | Page DOM | clears innerHTML | ✅ Yes |
| Skeleton with callback | Page JS | ui-common.js | callback, container, count, delay | ❌ New |
| Summary skeleton show | Page JS | ui-common.js | none (global DOM query) | ❌ New |
| Summary skeleton hide | Page JS | ui-common.js | none (restores from dataset) | ❌ New |
| Scroll save | scroll event (debounced) | sessionStorage | key + scrollY | ✅ Yes |
| Scroll restore | pageshow | window.scrollTo | saved Y | ✅ Yes |
| Scroll clear | nav click | sessionStorage | removes key | ❌ New |
| Init scroll restore | DOMContentLoaded | multiple events | pageKey | ❌ New |

## Dependencies

- **None** — all changes use existing CSS tokens and JS patterns
- No new npm packages, no new CDN imports
- Existing `showSkeleton()`/`hideSkeleton()`/`saveScrollPosition()`/`restoreScrollPosition()` signatures unchanged (backward compatible)

## Promoted to Shared

| Component | From | To | Pages Affected |
|-----------|------|-----|----------------|
| `prefers-reduced-motion` | (new) | theme.css | All pages with skeleton |
| `withSkeleton()` | (new) | ui-common.js | All pages with skeleton |
| `showSummarySkeleton()` | request-approval local code | ui-common.js | my-request-history, replacement-home, request-approval |
| `hideSummarySkeleton()` | (new) | ui-common.js | my-request-history, replacement-home, request-approval |
| `clearScrollPosition()` | (new) | ui-common.js | All pages |
| `initScrollRestore()` | (new) | ui-common.js | All pages |
| Global scroll wiring | (new) | ui-template.blade.php | All pages |
