# Design: Mobile Responsive + Nav Drawer

## Critical Constraint: Desktop Preservation

**ALL mobile + tablet changes must be additive only. DO NOT disturb existing desktop code/design.**

Rules:
1. **All new CSS MUST be inside `@media` blocks** — never modify existing CSS rules
2. **All new HTML elements MUST be hidden on desktop** via `display: none` in default state, shown only in media queries
3. **All new JS functions MUST be new additions** — never modify existing functions
4. **All new CSS classes MUST use unique names** — never reuse existing class names
5. **Desktop layout (≥1025px) must remain pixel-perfect** — any deviation = rollback

**Breakpoints:**
- Tablet: `@media (max-width: 1024px)`
- Mobile: `@media (max-width: 768px)`

**Verification:** After implementation, compare desktop view before/after. Must be identical.

---

## 0. Tablet View (769px–1024px)

### 0.1 Navigation (769px–1024px)

```css
@media (max-width: 1024px) {
    .nav-item { padding: 0 10px; font-size: 13px; }
    .user-info { display: none; }  /* hide name, show avatar only */
    .top-logo { margin-right: 12px; }
}
```

### 0.2 Data Tables (769px–1024px)

Already handled by existing rule in theme.css:
```css
@media (max-width: 1024px) {
    .grid-scroll { overflow-x: auto; }
}
```

No additional changes needed — tables allow horizontal scroll on tablet.

### 0.3 Summary Cards (769px–1024px)

```css
@media (max-width: 1024px) {
    .summary-bar {
        grid-template-columns: repeat(3, 1fr);
    }
    .summary-card:nth-child(4),
    .summary-card:nth-child(5) {
        grid-column: span 1;
    }
    .summary-value { font-size: 22px; }
    .summary-label { font-size: 12px; }
}
```

### 0.4 Modals (769px–1024px)

```css
@media (max-width: 1024px) {
    .modal {
        max-width: 560px;
    }
}
```

### 0.5 Toolbar (769px–1024px)

Already handled by existing rule in theme.css:
```css
@media (max-width: 1024px) {
    .toolbar { flex-direction: column; align-items: stretch; }
    .toolbar-left { justify-content: flex-start; }
    .search-input { width: 100%; }
    .filter-select { width: auto; flex: 0 0 auto; }
}
```

No additional changes needed.

### 0.6 Touch Targets (769px–1024px)

```css
@media (max-width: 1024px) {
    .nav-item,
    .legend-item,
    .week-arrow,
    .today-btn,
    .modal-close,
    .btn-close-modal,
    .page-btn,
    .badge {
        min-height: 40px;
        min-width: 40px;
    }
}
```

---

## 1. Nav Drawer (≤768px)

### 1.1 Markup Changes — `ui-nav-bar.blade.php`

Add hamburger button before `.nav-items`, wrap nav-items in drawer container:

```html
<div class="top-bar">
    <div class="top-logo" onclick="navigateHome()">
        <img src="/images/logo_banner.png" alt="TAR UMT">
    </div>

    <!-- Hamburger (mobile only) -->
    <button class="nav-hamburger" id="navHamburger" aria-label="Open navigation menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <!-- Desktop nav (hidden on mobile) -->
    <div class="nav-items">
        @foreach ($items as $it)
        <a class="nav-item {{ $activeNav === $it['key'] ? 'active' : '' }}" href="{{ $it['href'] }}">{{ $it['label'] }}</a>
        @endforeach
    </div>

    <div class="top-right">
        <!-- ... existing theme toggle, notif, user panel ... -->
    </div>
</div>

<!-- Mobile drawer overlay (hidden by default) -->
<div class="nav-drawer-overlay" id="navDrawerOverlay"></div>

<!-- Mobile drawer (hidden by default) -->
<div class="nav-drawer" id="navDrawer">
    <div class="nav-drawer-header">
        <span class="nav-drawer-title">Navigation</span>
        <button class="nav-drawer-close" id="navDrawerClose" aria-label="Close navigation">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>
    <div class="nav-drawer-items">
        @foreach ($items as $it)
        <a class="nav-drawer-item {{ $activeNav === $it['key'] ? 'active' : '' }}" href="{{ $it['href'] }}">{{ $it['label'] }}</a>
        @endforeach
    </div>
</div>
```

### 1.2 CSS — `theme.css`

```css
/* ── Nav Hamburger (mobile only) ── */
.nav-hamburger {
    display: none;
    width: 44px;
    height: 44px;
    border: none;
    background: transparent;
    color: var(--color-on-surface);
    cursor: pointer;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-sm);
    transition: background var(--transition);
}
.nav-hamburger:hover { background: var(--color-surface-variant); }

/* ── Nav Drawer ── */
.nav-drawer-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 998;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s;
}
.nav-drawer-overlay.open { display: block; opacity: 1; }

.nav-drawer {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    height: 100dvh;
    z-index: 999;
    background: var(--color-surface);
    border-right: 1px solid var(--color-outline);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    padding-top: env(safe-area-inset-top);
    padding-bottom: env(safe-area-inset-bottom);
}
.nav-drawer.open { transform: translateX(0); }

.nav-drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 16px 12px;
    border-bottom: 1px solid var(--color-outline);
}
.nav-drawer-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-on-surface);
}
.nav-drawer-close {
    width: 44px;
    height: 44px;
    border: none;
    background: transparent;
    color: var(--color-on-surface-variant);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-sm);
}
.nav-drawer-close:hover { background: var(--color-surface-variant); }

.nav-drawer-items {
    display: flex;
    flex-direction: column;
    padding: 8px 0;
}
.nav-drawer-item {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    color: var(--color-on-surface-variant);
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: background var(--transition), color var(--transition);
    min-height: 48px;
}
.nav-drawer-item:hover { background: var(--color-surface-variant); color: var(--color-on-surface); }
.nav-drawer-item.active {
    background: var(--color-primary-container);
    color: var(--color-on-primary-container);
    font-weight: 600;
    border-left: 3px solid var(--color-primary);
}
```

### 1.3 JS — `ui-common.js`

```javascript
function initMobileNav() {
    const hamburger = document.getElementById('navHamburger');
    const drawer = document.getElementById('navDrawer');
    const overlay = document.getElementById('navDrawerOverlay');
    const closeBtn = document.getElementById('navDrawerClose');

    if (!hamburger || !drawer || !overlay) return;

    function openDrawer() {
        drawer.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', openDrawer);
    closeBtn.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);

    // Swipe left to close
    let touchStartX = 0;
    drawer.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    drawer.addEventListener('touchend', (e) => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (diff > 50) closeDrawer();
    }, { passive: true });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('open')) closeDrawer();
    });
}
```

### 1.4 Mobile CSS (≤768px)

```css
@media (max-width: 768px) {
    .nav-hamburger { display: flex; }
    .nav-items { display: none; }
    .user-info { display: none; }
}
```

## 2. Timetable Grid → Card Layout (≤768px)

### 2.1 CSS — `theme.css`

```css
/* ── Mobile card layout for timetable grid ── */
@media (max-width: 768px) {
    .grid-scroll { overflow-x: hidden; }

    .timetable,
    .timetable thead,
    .timetable tbody,
    .timetable tr,
    .timetable td,
    .timetable th {
        display: block;
    }

    .timetable thead { display: none; }

    .timetable tbody tr {
        background: var(--color-surface);
        border: 1px solid var(--color-outline);
        border-radius: var(--radius-md);
        margin-bottom: 12px;
        padding: 12px;
        box-shadow: var(--shadow-sm);
    }

    .timetable td.hour-cell {
        height: auto;
        min-width: 0;
        padding: 8px 0;
        border-bottom: 1px solid var(--color-outline);
    }
    .timetable td.hour-cell:last-child { border-bottom: none; }

    .timetable td.time-col {
        position: static;
        width: 100%;
        min-width: 0;
        background: var(--color-primary-container);
        border-radius: var(--radius-sm);
        margin-bottom: 8px;
        padding: 8px;
    }

    .event-block {
        width: 100%;
        height: auto;
        min-height: 60px;
        padding: 10px;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .event-block .ev-code { font-size: 14px; text-align: left; }
    .event-block .ev-venue { font-size: 12px; text-align: right; }
    .event-block .ev-time { font-size: 11px; text-align: right; }
}
```

### 2.2 JS — `ui-common.js`

No JS changes needed for card layout — pure CSS conversion.

## 3. Summary Cards (≤768px)

```css
@media (max-width: 768px) {
    .summary-bar {
        grid-template-columns: repeat(2, 1fr);
    }
    .summary-card:last-child {
        grid-column: span 2;
    }
    .summary-value { font-size: 20px; }
    .summary-label { font-size: 11px; }
}

@media (max-width: 480px) {
    .summary-bar {
        grid-template-columns: 1fr;
    }
    .summary-card:last-child {
        grid-column: span 1;
    }
}
```

## 4. Bottom Sheet Modals (≤768px)

```css
@media (max-width: 768px) {
    .modal-overlay {
        align-items: flex-end;
        padding: 0;
    }

    .modal {
        max-width: 100%;
        width: 100%;
        max-height: 80vh;
        border-radius: 16px 16px 0 0;
        animation: modalSlideUp 0.3s ease;
        overflow-y: auto;
    }

    @keyframes modalSlideUp {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }

    .modal-header {
        padding: 12px 16px 0;
        position: sticky;
        top: 0;
        background: var(--color-surface);
        z-index: 1;
    }

    .modal-header::before {
        content: '';
        display: block;
        width: 40px;
        height: 4px;
        background: var(--color-outline-strong);
        border-radius: 2px;
        margin: 0 auto 12px;
    }

    .modal-body { padding: 16px; }
    .modal-footer { padding: 0 16px 16px; }
}
```

## 5. Touch Targets (WCAG 2.5.5)

```css
@media (max-width: 768px) {
    .nav-item,
    .legend-item,
    .week-arrow,
    .today-btn,
    .modal-close,
    .btn-close-modal,
    .page-btn,
    .badge {
        min-height: 44px;
        min-width: 44px;
    }

    .week-arrow {
        width: 44px;
        height: 44px;
    }

    .modal-close {
        width: 44px;
        height: 44px;
    }
}
```

## 6. Swipe Gestures (≤768px)

### 6.1 JS — `ui-common.js`

```javascript
function initSwipeGesture(config) {
    const { element, onSwipeLeft, onSwipeRight, threshold = 50 } = config;
    let touchStartX = 0;
    let touchStartY = 0;
    let lastSwipeTime = 0;

    element.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    element.addEventListener('touchend', (e) => {
        const now = Date.now();
        if (now - lastSwipeTime < 300) return; // debounce

        const diffX = touchStartX - e.changedTouches[0].clientX;
        const diffY = Math.abs(touchStartY - e.changedTouches[0].clientY);

        if (Math.abs(diffX) > threshold && diffY < 100) {
            lastSwipeTime = now;
            if (diffX > 0) onSwipeLeft();
            else onSwipeRight();
        }
    }, { passive: true });
}
```

### 6.2 Usage in Timetable Pages

```javascript
// In student-my-timetable or my-timetable page scripts
if (window.innerWidth <= 768) {
    initSwipeGesture({
        element: document.querySelector('.grid-scroll'),
        onSwipeLeft: () => { /* next week */ },
        onSwipeRight: () => { /* prev week */ }
    });
}
```

## 7. Collapsible Day Cards (≤768px)

### 7.1 CSS — `theme.css`

```css
@media (max-width: 768px) {
    .day-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
        background: var(--color-surface-variant);
        border-radius: var(--radius-sm);
        cursor: pointer;
        min-height: 48px;
    }

    .day-card-header .chevron {
        transition: transform 0.2s;
    }
    .day-card-header.collapsed .chevron {
        transform: rotate(-90deg);
    }

    .day-card-content {
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.2s;
        max-height: 500px;
        opacity: 1;
    }
    .day-card-content.collapsed {
        max-height: 0;
        opacity: 0;
    }
}
```

### 7.2 JS — `ui-common.js`

```javascript
function initCollapsibleCards() {
    document.querySelectorAll('.day-card-header').forEach(header => {
        header.addEventListener('click', () => {
            header.classList.toggle('collapsed');
            const content = header.nextElementSibling;
            content.classList.toggle('collapsed');
        });
    });
}
```

## 8. Responsive Typography (≤768px)

```css
@media (max-width: 768px) {
    .page-title { font-size: clamp(18px, 4vw, 20px); }
    .time-col .day-label { font-size: 13px; }
    .event-block .ev-code { font-size: 12px; }
    .event-block .ev-venue { font-size: 11px; }
    .event-block .ev-time { font-size: 10px; }
    .legend-item { font-size: 0.8rem; }
    .modal-title { font-size: 16px; }
}
```

## 9. Safe Area Insets

**Exception: Not wrapped in @media — applies to all viewports (iPhone notch support).**

Already handled in nav drawer CSS (§1.2) and bottom sheet modal CSS (§4).

Additional global safe-area CSS:

```css
body {
    padding-top: env(safe-area-inset-top);
    padding-bottom: env(safe-area-inset-bottom);
    padding-left: env(safe-area-inset-left);
    padding-right: env(safe-area-inset-right);
}
```

**Desktop impact:** None — `env(safe-area-inset-*)` returns 0 on non-notched devices.

## 10. Full-width Form Inputs (≤768px)

```css
@media (max-width: 768px) {
    .filter-select,
    .search-input,
    .week-select {
        width: 100% !important;
    }

    .toolbar-left,
    .toolbar-right {
        width: 100%;
    }

    .toolbar {
        flex-direction: column;
        gap: 8px;
    }
}
```

## 11. Skeleton Loading

**Exception: Not wrapped in @media — applies to all viewports (loading states are cross-cutting).**

### 11.1 CSS — `theme.css`

```css
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

.skeleton-row {
    height: 60px;
    margin-bottom: 8px;
}

.skeleton-card {
    height: 80px;
    margin-bottom: 12px;
}

.skeleton-text {
    height: 14px;
    margin-bottom: 8px;
    width: 80%;
}
```

### 11.2 JS — `ui-common.js`

```javascript
function showSkeleton(container, type = 'rows', count = 5) {
    container.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const el = document.createElement('div');
        el.className = `skeleton skeleton-${type === 'rows' ? 'row' : 'card'}`;
        container.appendChild(el);
    }
}

function hideSkeleton(container) {
    container.innerHTML = '';
}
```

## 12. Scroll Restoration

**Exception: Not wrapped in @media — applies to all viewports (scroll memory is cross-cutting).**

### 12.1 JS — `ui-common.js`

```javascript
function saveScrollPosition(key) {
    sessionStorage.setItem('scroll_' + key, window.scrollY);
}

function restoreScrollPosition(key) {
    const pos = sessionStorage.getItem('scroll_' + key);
    if (pos) window.scrollTo(0, parseInt(pos));
}

// Auto-save on scroll (debounced)
let scrollTimer;
window.addEventListener('scroll', () => {
    clearTimeout(scrollTimer);
    scrollTimer = setTimeout(() => {
        const pageKey = document.body.dataset.page;
        if (pageKey) saveScrollPosition(pageKey);
    }, 200);
}, { passive: true });
```

### 12.2 Usage in Pages

```javascript
// In page scripts
document.body.dataset.page = 'studentMyTimetable';
restoreScrollPosition('studentMyTimetable');
```

## 13. Toast Position (≤768px)

```css
@media (max-width: 768px) {
    .copy-toast {
        bottom: calc(24px + env(safe-area-inset-bottom));
        left: 50%;
        transform: translateX(-50%);
        max-width: calc(100vw - 32px);
        text-align: center;
    }
}
```

## 14. Viewport Meta

Already exists in `ui-template.blade.php` line 5:
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

Update to include `viewport-fit=cover` for safe area support:
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
```

## 15. Promoted to Shared

| # | What | From | To | Notes |
|---|------|------|-----|-------|
| 15.1 | Nav drawer CSS | New | `theme.css` | Hamburger, drawer, overlay styles |
| 15.2 | Nav drawer JS | New | `ui-common.js` | `initMobileNav()` |
| 15.3 | Card layout CSS | New | `theme.css` | Timetable grid → card conversion |
| 15.4 | Bottom sheet modal CSS | New | `theme.css` | Mobile modal slide-up |
| 15.5 | Touch target CSS | New | `theme.css` | WCAG 2.5.5 compliance (mobile + tablet) |
| 15.6 | Swipe gesture JS | New | `ui-common.js` | `initSwipeGesture()` |
| 15.7 | Collapsible card CSS + JS | New | `theme.css` + `ui-common.js` | Day card expand/collapse |
| 15.8 | Responsive typography CSS | New | `theme.css` | Font size reductions |
| 15.9 | Safe area CSS | New | `theme.css` | iPhone notch/home indicator |
| 15.10 | Full-width input CSS | New | `theme.css` | Mobile form inputs |
| 15.11 | Skeleton loading CSS + JS | New | `theme.css` + `ui-common.js` | Loading placeholders |
| 15.12 | Scroll restoration JS | New | `ui-common.js` | `saveScrollPosition()`/`restoreScrollPosition()` |
| 15.13 | Toast position CSS | New | `theme.css` | Mobile toast positioning |
| 15.14 | Tablet navigation CSS | New | `theme.css` | Nav links, user info, logo |
| 15.15 | Tablet summary cards CSS | New | `theme.css` | 3-column grid |
| 15.16 | Tablet modal CSS | New | `theme.css` | Max-width 560px |
| 15.17 | Tablet touch target CSS | New | `theme.css` | Minimum 40×40px |

**Stays page-specific:**
- Page-specific modal fields, action buttons, summary card counts — not promoted
- Page-specific table column definitions — not promoted

## 16. File Changes

**Rule: All changes are ADDITIVE ONLY. No existing code is modified or removed.**

| File | Change | Desktop Impact |
|------|--------|----------------|
| `resources/views/partials/ui-nav-bar.blade.php` | **Extend** — add hamburger + drawer markup (hidden on desktop) | None — new elements hidden via `display: none` |
| `resources/views/layouts/ui-template.blade.php` | **Extend** — add viewport-fit=cover + initMobileNav() call | None — viewport-fit only affects safe area, no visual change |
| `public/css/theme.css` | **Extend** — add all mobile + tablet CSS inside `@media` queries | None — all new rules inside media queries |
| `public/js/ui-common.js` | **Extend** — add new mobile JS functions | None — new functions only, no existing code modified |
| All 6 page templates | **Extend** — add `data-page` attribute for scroll restoration | None — attribute only, no visual change |
| `page-changelogs/mobile-responsive-changelog.md` | **Create** — new changelog | None — new file only |

## 17. Known Limitations (Mock Phase)

- Skeleton loading is CSS-only (no real async data fetching in mock phase)
- Scroll restoration uses sessionStorage (clears on tab close)
- Swipe gestures only work on touch devices (no mouse simulation in mock phase)
- Card layout for timetable grid is simplified (no drag-and-drop reordering)
